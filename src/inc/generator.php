<?php
/**
 * Construct shortcodes.
 *
 * @category Core
 * @package  AblePlayer
 * @author   Joe Dolson
 * @license  GPLv3
 * @link     https://www.joedolson.com/ableplayer/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a shortcode for Able Player.
 *
 * @param string $format Output type. Default is 'shortcode', can output values in array.
 */
function ableplayer_generate( $format = 'shortcode' ) {
	if ( isset( $_POST['generator'] ) ) {
		$nonce = sanitize_text_field( $_POST['_wpnonce'] );
		if ( ! wp_verify_nonce( $nonce, 'ableplayer-nonce' ) ) {
			wp_die( 'Invalid nonce' );
		}
		$string    = '';
		$array     = array();
		$shortcode = 'ableplayer';
		$keys      = array( 'youtube-id', 'vimeo-id', 'media-id', 'youtube-desc-id', 'youtube-sign-src', 'vimeo-desc-id', 'media-desc-id', 'media-asl-id', 'poster', 'captions', 'subtitles', 'descriptions', 'chapters', 'autoplay', 'loop', 'playsinline', 'hidecontrols', 'heading', 'speed', 'start', 'volume', 'seekinterval' );
		$post      = map_deep( $_POST, 'sanitize_text_field' );

		if ( empty( $post['youtube-id'] ) && empty( $post['vimeo-id'] ) && empty( $post['media-id'] ) ) {
			return array(
				'message' => __( 'You must specify at least one media source', 'ableplayer' ),
				'type'    => 'error',
			);
		}
		foreach ( $post as $key => $v ) {
			if ( in_array( $key, $keys, true ) ) {
				if ( 'speed' === $key && ableplayer_get_settings( 'default_speed' ) === $v ) {
					continue;
				}
				if ( 'heading' === $key && ableplayer_get_settings( 'default_heading' ) === $v ) {
					continue;
				}
				if ( '' !== $v ) {
					if ( in_array( $key, array( 'captions', 'subtitles', 'descriptions', 'chapters' ), true ) ) {
						$v .= ableplayer_shortcode_track( $key, $post );
					}
					$array[ $key ] = $v;
					$string       .= " $key=&quot;$v&quot;";
				}
			}
		}
		$output = esc_html( $shortcode . $string );
		ableplayer_update_setting( 'last_shortcode', $output );

		if ( 'shortcode' === $format && ! is_array( $output ) ) {
			$return = "<div class='notice notice-info'><p><textarea readonly='readonly' class='large-text readonly'>[$output]</textarea></p></div>";
			echo wp_kses( $return, ableplayer_kses_elements() );
		} else {
			if ( is_array( $output ) ) {
				return $output;
			}
			$array['shortcode'] = "[$output]";
			$array['message']   = __( 'New shortcode created.', 'ableplayer' );
			$array['type']      = 'success';

			return $array;
		}
	}
}

/**
 * Get the srclang and label for a shortcode track.
 *
 * @param string $kind Type of track.
 * @param array  $post Array of data to test.
 *
 * @return string
 */
function ableplayer_shortcode_track( $kind, $post ) {
	// Handle track srclang and label.
	$default_lang = str_replace( '_', '-', get_locale() );
	$kinds        = array(
		'captions'     => __( 'Captions', 'ableplayer' ),
		'subtitles'    => __( 'Subtitles', 'ableplayer' ),
		'descriptions' => __( 'Audio Description', 'ableplayer' ),
		'chapters'     => __( 'Chapters', 'ableplayer' ),
	);

	$v       = '';
	$srclang = isset( $post[ $kind . '-srclang' ] ) ? $post[ $kind . '-srclang' ] : '';
	$label   = isset( $post[ $kind . '-label' ] ) ? $post[ $kind . '-label' ] : '';
	if ( $srclang && $srclang !== $default_lang ) {
		$v .= '|' . $srclang;
	}
	if ( $label && $label !== $kinds['captions'] ) {
		$v .= '|' . $label;
	}

	return $v;
}

/**
 * Form to create a shortcode
 *
 * @param array $data Data submitted from shortcode generator.
 */
function ableplayer_generator( $data = array() ) {
	?>
	<form action="<?php echo esc_url( admin_url( 'options-general.php?page=ableplayer' ) ) . '#ableplayer-shortcode'; ?>" method="POST" id="ableplayer-generate">
		<?php ableplayer_generator_fields( $data ); ?>
		<p>
			<input type="submit" class="button-primary" name="generator" value="<?php esc_html_e( 'Generate Shortcode', 'ableplayer' ); ?>"/>
		</p>
	</form>
	<?php
}

/**
 * Settings to configure Able Player shortcode.
 *
 * @param array|string $data Data posted to shortcode builder.
 */
function ableplayer_generator_fields( $data ) {
	$params = array();
	if ( $data && is_array( $data ) ) {
		$params = $data;
	}
	$message        = isset( $params['message'] ) ? $params['message'] : __( 'Generate an <code>[ableplayer]</code> shortcode.', 'ableplayer' );
	$message_type   = isset( $params['type'] ) ? $params['type'] : 'info';
	$shortcode      = isset( $params['shortcode'] ) ? $params['shortcode'] : '[ableplayer]';
	$last_shortcode = ableplayer_get_settings( 'last_shortcode' );
	$shortcode      = ( ! isset( $params['shortcode'] ) && $last_shortcode ) ? "[$last_shortcode]" : $shortcode;
	$default_lang   = str_replace( '_', '-', get_locale() );
	$kinds          = array(
		'captions'     => __( 'Captions', 'ableplayer' ),
		'subtitles'    => __( 'Subtitles', 'ableplayer' ),
		'descriptions' => __( 'Audio Description', 'ableplayer' ),
		'chapters'     => __( 'Chapters', 'ableplayer' ),
	);
	?>
	<div id="ableplayer-generator" class="generator">
		<div class="ableplayer-generator-data">
			<?php
			wp_admin_notice(
				$message,
				array(
					'type'               => $message_type,
					'additional_classes' => array( 'inline' ),
				)
			);
			?>
			<div><input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'ableplayer-nonce' ) ); ?>"/></div>
			<?php
			if ( $shortcode ) {
				echo wp_kses_post(
					'<div class="shortcode-preview"><p><label for="ableplayer_shortcode">Shortcode</label><textarea readonly class="large-text readonly ableplayer-shortcode-container" id="ableplayer_shortcode">' . $shortcode . '</textarea></p>
					<div class="ableplayer-copy-button"><button type="button" class="button-primary ableplayer-copy-to-clipboard" data-clipboard-target="#ableplayer_shortcode">' . __( 'Copy to clipboard', 'ableplayer' ) . '</button><span class="ableplayer-notice-copied">' . __( 'Shortcode Copied', 'ableplayer' ) . '</span></div>
					<p><button data-type="ableplayer" type="button" class="button button-secondary reset-ableplayer">' . __( 'Reset Shortcode', 'ableplayer' ) . '</button></p></div>'
				);
			}
			?>
		</div>
		<div class="ableplayer-generator-inputs">
			<fieldset>
				<legend><?php esc_html_e( 'Media Sources', 'ableplayer' ); ?></legend>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'    => 'source_type',
						'label'   => __( 'Video Source Type', 'ableplayer' ),
						'type'    => 'select',
						'default' => array(
							'youtube' => 'YouTube',
							'vimeo'   => 'Vimeo',
							'local'   => 'Local',
						),
					),
					'generator'
				);
				?>
				</p>
				<p class="media-sources youtube">
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'youtube-id',
						'label' => __( 'YouTube Source', 'ableplayer' ),
						'type'  => 'url',
						'atts'  => array(
							'placeholder' => 'https://youtube.com',
						),
					),
					'generator'
				);
				ableplayer_settings_field(
					array(
						'name'  => 'youtube-desc-id',
						'label' => __( 'YouTube Audio Described Source', 'ableplayer' ),
						'type'  => 'url',
						'atts'  => array(
							'placeholder' => 'https://youtube.com',
						),
					),
					'generator'
				);
				ableplayer_settings_field(
					array(
						'name'  => 'youtube-sign-src',
						'label' => __( 'YouTube Sign Language Source', 'ableplayer' ),
						'type'  => 'url',
						'atts'  => array(
							'placeholder' => 'https://youtube.com',
						),
					),
					'generator'
				);
				?>
				</p>
				<p class="media-sources vimeo">
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'vimeo-id',
						'label' => __( 'Vimeo Source', 'ableplayer' ),
						'type'  => 'url',
						'atts'  => array(
							'placeholder' => 'https://vimeo.com',
						),
					),
					'generator'
				);
				ableplayer_settings_field(
					array(
						'name'  => 'vimeo-desc-id',
						'label' => __( 'Vimeo Audio Described Source', 'ableplayer' ),
						'type'  => 'url',
						'atts'  => array(
							'placeholder' => 'https://vimeo.com',
						),
					),
					'generator'
				);
				?>
				</p>
				<div class="ableplayer-media-preview media-sources local">
					<div>
						<button type="button" class="button-primary upload-ableplayer-media upload-video" data-input="media-id"><?php esc_html_e( 'Select Media', 'ableplayer' ); ?></button>
						<button type="button" class="button-secondary ableplayer-remove-preview" data-input="media-id"><?php esc_html_e( 'Remove', 'ableplayer' ); ?></button>
					</div>
					<div class="preview-media-id"></div>
					<input type="hidden" name="media-id" value="">
				</div>
				<div class="ableplayer-media-preview media-sources local youtube">
					<div>
						<button type="button" class="button-primary upload-ableplayer-media upload-video" data-input="media-asl-id"><?php esc_html_e( 'Select Local Sign Language', 'ableplayer' ); ?></button>
						<button type="button" class="button-secondary ableplayer-remove-preview" data-input="media-asl-id"><?php esc_html_e( 'Remove', 'ableplayer' ); ?></button>
					</div>
					<div class="preview-media-asl-id"></div>
					<input type="hidden" name="media-asl-id" value="">
				</div>
				<div class="ableplayer-media-preview media-sources local">
					<div>
						<button type="button" class="button-primary upload-ableplayer-media upload-video" data-input="media-desc-id"><?php esc_html_e( 'Select Audio Described Media', 'ableplayer' ); ?></button>
						<button type="button" class="button-secondary ableplayer-remove-preview" data-input="media-desc-id"><?php esc_html_e( 'Remove', 'ableplayer' ); ?></button>
					</div>
					<div class="preview-media-desc-id"></div>
					<input type="hidden" name="media-desc-id" value="">
				</div>
				<div class="ableplayer-media-preview">
					<div>
						<button type="button" class="button-secondary upload-ableplayer-media upload-poster" data-input="poster"><?php esc_html_e( 'Select Poster', 'ableplayer' ); ?></button>
						<button type="button" class="button-secondary ableplayer-remove-preview" data-input="poster"><?php esc_html_e( 'Remove', 'ableplayer' ); ?></button>
					</div>
					<div class="preview-poster"></div>
					<input type="hidden" name="poster" value="">
				</div>
				<div class="ableplayer-tracks">
					<details>
						<summary><?php esc_html_e( 'Add Media Tracks', 'ableplayer' ); ?></summary>
						<div class="ableplayer-media-preview">
							<div>
								<button type="button" class="button-secondary upload-ableplayer-media upload-captions" data-input="captions"><?php esc_html_e( 'Add Captions', 'ableplayer' ); ?></button>
								<button type="button" class="button-secondary ableplayer-remove-preview" data-input="captions"><?php esc_html_e( 'Remove', 'ableplayer' ); ?></button>
							</div>
							<div>
								<div class="preview-captions"></div>
								<input type="hidden" name="captions" value="">
								<div class="ableplayer-track-details">
									<?php
									ableplayer_settings_field(
										array(
											'name'    => 'captions-srclang',
											'label'   => __( 'Language Code', 'ableplayer' ),
											'type'    => 'text',
											'default' => $default_lang,
										),
										'generator'
									);
									ableplayer_settings_field(
										array(
											'name'    => 'captions-label',
											'label'   => __( 'Captions Label', 'ableplayer' ),
											'type'    => 'text',
											'default' => $kinds['captions'],
										),
										'generator'
									);
									?>
								</div>
							</div>
						</div>
						<div class="ableplayer-media-preview">
							<div>
								<button type="button" class="button-secondary upload-ableplayer-media upload-subtitles" data-input="subtitles"><?php esc_html_e( 'Add Subtitles', 'ableplayer' ); ?></button>
								<button type="button" class="button-secondary ableplayer-remove-preview" data-input="subtitles"><?php esc_html_e( 'Remove', 'ableplayer' ); ?></button>
							</div>
							<div>
								<div class="preview-subtitles"></div>
								<input type="hidden" name="subtitles" value="">
								<div class="ableplayer-track-details">
									<?php
									ableplayer_settings_field(
										array(
											'name'    => 'subtitles-srclang',
											'label'   => __( 'Language Code', 'ableplayer' ),
											'type'    => 'text',
											'default' => $default_lang,
										),
										'generator'
									);
									ableplayer_settings_field(
										array(
											'name'    => 'subtitles-label',
											'label'   => __( 'Subtitles Label', 'ableplayer' ),
											'type'    => 'text',
											'default' => $kinds['subtitles'],
										),
										'generator'
									);
									?>
								</div>
							</div>
						</div>
						<div class="ableplayer-media-preview">
							<div>
								<button type="button" class="button-secondary upload-ableplayer-media upload-descriptions" data-input="descriptions"><?php esc_html_e( 'Add Audio Description', 'ableplayer' ); ?></button>
								<button type="button" class="button-secondary ableplayer-remove-preview" data-input="descriptions"><?php esc_html_e( 'Remove', 'ableplayer' ); ?></button>
							</div>
							<div>
								<div class="preview-descriptions"></div>
								<input type="hidden" name="descriptions" value="">
								<div class="ableplayer-track-details">
									<?php
									ableplayer_settings_field(
										array(
											'name'    => 'descriptions-srclang',
											'label'   => __( 'Language Code', 'ableplayer' ),
											'type'    => 'text',
											'default' => $default_lang,
										),
										'generator'
									);
									ableplayer_settings_field(
										array(
											'name'    => 'descriptions-label',
											'label'   => __( 'Descriptions Label', 'ableplayer' ),
											'type'    => 'text',
											'default' => $kinds['descriptions'],
										),
										'generator'
									);
									?>
								</div>
							</div>
						</div>
						<div class="ableplayer-media-preview">
							<div>
								<button type="button" class="button-secondary upload-ableplayer-media upload-chapters" data-input="chapters"><?php esc_html_e( 'Add Chapters', 'ableplayer' ); ?></button>
								<button type="button" class="button-secondary ableplayer-remove-preview" data-input="chapters"><?php esc_html_e( 'Remove', 'ableplayer' ); ?></button>
							</div>
							<div>
								<div class="preview-chapters"></div>
								<input type="hidden" name="chapters" value="">
								<div class="ableplayer-track-details">
									<?php
									ableplayer_settings_field(
										array(
											'name'    => 'chapter-srclang',
											'label'   => __( 'Language Code', 'ableplayer' ),
											'type'    => 'text',
											'default' => $default_lang,
										),
										'generator'
									);
									ableplayer_settings_field(
										array(
											'name'    => 'chapter-label',
											'label'   => __( 'Chapters Label', 'ableplayer' ),
											'type'    => 'text',
											'default' => $kinds['chapters'],
										),
										'generator'
									);
									?>
								</div>
							</div>
						</div>
					</details>
				</div>
			</fieldset>
			<fieldset>
				<legend><?php esc_html_e( 'Player Options', 'ableplayer' ); ?></legend>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'autoplay',
						'label' => __( 'Enable Autoplay', 'ableplayer' ),
						'type'  => 'checkbox-single',
					),
					'generator'
				);
				?>
				</p>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'loop',
						'label' => __( 'Enable Looping', 'ableplayer' ),
						'type'  => 'checkbox-single',
					),
					'generator'
				);
				?>
				</p>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'playsinline',
						'label' => __( 'Play inline on mobile devices', 'ableplayer' ),
						'type'  => 'checkbox-single',
					),
					'generator'
				);
				?>
				</p>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'hidecontrols',
						'label' => __( 'Hide controls while playing', 'ableplayer' ),
						'type'  => 'checkbox-single',
					),
					'generator'
				);
				?>
				</p>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'    => 'heading',
						'label'   => __( 'Hidden heading level', 'ableplayer' ),
						'type'    => 'select',
						'default' => array(
							'auto' => __( 'Automatically assigned', 'ableplayer' ),
							'0'    => __( 'No heading', 'ableplayer' ),
							'2'    => 'h2',
							'3'    => 'h3',
							'4'    => 'h4',
						),
					),
					'generator'
				);
				?>
				</p>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'    => 'speed',
						'label'   => __( 'Speed selection control', 'ableplayer' ),
						'type'    => 'select',
						'default' => array(
							'animals' => __( 'Animals: Tortoise and Hare', 'ableplayer' ),
							'arrows'  => __( 'Arrows', 'ableplayer' ),
						),
					),
					'generator'
				);
				?>
				</p>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'start',
						'label' => __( 'Start time in seconds', 'ableplayer' ),
						'type'  => 'number',
						'atts'  => array(
							'min'  => '0',
							'step' => '1',
						),
					),
					'generator'
				);
				?>
				</p>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'volume',
						'label' => __( 'Initial volume', 'ableplayer' ),
						'type'  => 'number',
						'atts'  => array(
							'min'  => '0',
							'max'  => '10',
							'step' => '1',
						),
					),
					'generator'
				);
				?>
				</p>
				<p>
				<?php
				ableplayer_settings_field(
					array(
						'name'  => 'seekinterval',
						'label' => __( 'Seek interval', 'ableplayer' ),
						'type'  => 'number',
						'note'  => __( 'A value of 0 lets Able Player calculate the seek interval based on video length.', 'ableplayer' ),
						'atts'  => array(
							'min'  => '0',
							'step' => '5',
						),
					),
					'generator'
				);
				?>
				</p>
			</fieldset>
		</div>
	</div>
		<?php
}
