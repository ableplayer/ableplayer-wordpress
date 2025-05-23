<?php
/**
 * Able Player for WordPress Settings page
 *
 * @category Settings
 * @package  AblePlayer
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/ableplayer-wordpress/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Able Player settings.
 *
 * @param string $setting A specific setting key. Default empty string.
 *
 * @return array|mixed The full settings array or a specific setting value.
 */
function ableplayer_get_settings( $setting = '' ) {
	$settings = get_option( 'ableplayer_settings', ableplayer_default_settings() );
	$settings = array_merge( ableplayer_default_settings(), $settings );
	if ( $setting ) {
		return ( isset( $settings[ $setting ] ) ) ? $settings[ $setting ] : '';
	}

	return $settings;
}

/**
 * Save an AblePlayer setting.
 *
 * @param string $key Setting key.
 * @param mixed  $value Setting value.
 *
 * @return bool
 */
function ableplayer_update_setting( $key, $value = '' ) {
	$settings = get_option( 'ableplayer_settings', ableplayer_default_settings() );

	$settings[ $key ] = $value;
	$return           = update_option( 'ableplayer_settings', $settings );

	return $return;
}

/**
 * Generate input & field for an AblePlayer setting.
 *
 * @param array $args {
 *     Array of settings arguments.
 *
 *     @type string       $name Name of the option used in name attribute. Required.
 *     @type string|array $label Input label or array of labels (for radio or checkbox groups).
 *     @type string|array $default Default value or values when option not set.
 *     @type string       $note Note associated using aria-describedby.
 *     @type array        $atts Array of attributes to use on the input.
 *     @type string       $type Type of input field.
 *     @type boolean      $echo True to echo, false to return.
 *     @type array        $wrap Array of wrapper details (class, element, id).
 *     @type string       $id Override the default ID, which is derived from the name.
 * }
 * @param string $context Default 'settings'. Set to 'generator' for rendering non-settings.
 *
 * @return string|void
 */
function ableplayer_settings_field( $args = array(), $context = 'settings' ) {
	$name     = ( isset( $args['name'] ) ) ? $args['name'] : '';
	$label    = ( isset( $args['label'] ) ) ? $args['label'] : '';
	$default  = ( isset( $args['default'] ) ) ? $args['default'] : '';
	$note     = ( isset( $args['note'] ) ) ? $args['note'] : '';
	$atts     = ( isset( $args['atts'] ) ) ? $args['atts'] : array();
	$type     = ( isset( $args['type'] ) ) ? $args['type'] : 'text';
	$echo     = ( isset( $args['echo'] ) ) ? $args['echo'] : true;
	$wrap     = ( isset( $args['wrap'] ) ) ? $args['wrap'] : array();
	$position = ( isset( $args['position'] ) ) ? $args['position'] : 'bottom';
	$id       = ( isset( $args['id'] ) ) ? $args['id'] : $name;
	$element  = '';
	$close    = '';
	if ( ! empty( $wrap ) ) {
		$el    = isset( $wrap['element'] ) ? $wrap['element'] : 'p';
		$class = '';
		$id    = '';
		if ( isset( $wrap['class'] ) && '' !== $wrap['class'] ) {
			$class = ' class="' . $wrap['class'] . '"';
		}
		if ( isset( $wrap['id'] ) && '' !== $wrap['id'] ) {
			$id = ' id="' . $wrap['id'] . '"';
		}
		$element = "<$el$class$id>";
		$close   = "</$el>";
	}

	$options    = '';
	$attributes = '';
	$return     = '';
	if ( 'text' === $type || 'url' === $type || 'email' === $type ) {
		$base_atts = array(
			'size' => '30',
		);
	} else {
		$base_atts = $atts;
	}
	$value = ableplayer_get_settings( $name );
	$value = ( 'generator' === $context && ! is_string( $value ) ) ? '' : $value;
	$atts  = array_merge( $base_atts, $atts );
	if ( is_array( $atts ) && ! empty( $atts ) ) {
		foreach ( $atts as $key => $val ) {
			$attributes .= " $key='" . esc_attr( $val ) . "'";
		}
	}
	if ( 'checkbox' !== $type ) {
		if ( is_array( $default ) ) {
			$hold = '';
		} else {
			$hold = $default;
		}
		$value = ( '' !== $value ) ? esc_attr( wp_unslash( $value ) ) : $hold;
	} else {
		$value = ( ! empty( $value ) ) ? (array) $value : $default;
	}
	switch ( $type ) {
		case 'text':
		case 'url':
		case 'email':
		case 'number':
			if ( $note ) {
				$note = sprintf( str_replace( '%', '', $note ), "<code>$value</code>" );
				$note = "<span id='$id-note' class='ableplayer-input-description'><i class='dashicons dashicons-editor-help' aria-hidden='true'></i>$note</span>";
				$aria = " aria-describedby='$id-note'";
			} else {
				$note = '';
				$aria = '';
			}
			$note_top    = ( 'top' === $position ) ? $note : '';
			$note_bottom = ( 'bottom' === $position ) ? $note : '';
			$return      = "$element<label class='label-$type' for='$id'>$label</label> $note_top <input type='$type' id='$id' name='$name' value='" . esc_attr( $value ) . "'$aria$attributes />$close $note_bottom";
			break;
		case 'hidden':
			$return = "<input type='hidden' id='$id' name='$name' value='" . esc_attr( $value ) . "' />";
			break;
		case 'textarea':
			if ( $note ) {
				$note = sprintf( $note, "<code>$value</code>" );
				$note = "<span id='$id-note' class='ableplayer-input-description'><i class='dashicons dashicons-editor-help' aria-hidden='true'></i>$note</span>";
				$aria = " aria-describedby='$id-note'";
			} else {
				$note = '';
				$aria = '';
			}
			$return = "$element<label class='label-textarea' for='$id'>$label</label><br /><textarea id='$id' name='$name'$aria$attributes>" . esc_attr( $value ) . "</textarea>$close$note";
			break;
		case 'checkbox-single':
			$checked = checked( 'true', ableplayer_get_settings( $name ), false );
			if ( $note ) {
				$note = "<div id='$id-note' class='ableplayer-input-description'><i class='dashicons dashicons-editor-help' aria-hidden='true'></i>" . sprintf( $note, "<code>$value</code>" ) . '</div>';
				$aria = " aria-describedby='$id-note'";
			} else {
				$note = '';
				$aria = '';
			}
			$return = "$element<input type='checkbox' id='$id' name='$name' value='on' $checked$attributes$aria /> <label for='$id' class='label-checkbox'>$label</label>$close$note";
			break;
		case 'checkbox':
		case 'radio':
			if ( $note ) {
				$note = sprintf( $note, "<code>$value</code>" );
				$note = "<span id='$id-note' class='ableplayer-input-description'><i class='dashicons dashicons-editor-help' aria-hidden='true'></i>$note</span>";
				$aria = " aria-describedby='$id-note'";
			} else {
				$note = '';
				$aria = '';
			}
			$att_name = $name;
			if ( 'checkbox' === $type ) {
				$att_name = $name . '[]';
			}
			if ( is_array( $label ) ) {
				foreach ( $label as $k => $v ) {
					if ( 'radio' === $type ) {
						$checked = ( $k === $value ) ? ' checked="checked"' : '';
					} else {
						$checked = ( in_array( $k, $value, true ) ) ? ' checked="checked"' : '';
					}
					$options .= "<li>$element<input type='$type' id='$id-$k' value='" . esc_attr( $k ) . "' name='$att_name'$aria$attributes$checked /> <label class='label-$type' for='$id-$k'>$v</label>$close</li>";
				}
			}
			$return = "$options $note";
			break;
		case 'select':
			if ( $note ) {
				$note = sprintf( $note, "<code>$value</code>" );
				$note = "<span id='$id-note' class='ableplayer-input-description'><i class='dashicons dashicons-editor-help' aria-hidden='true'></i>$note</span>";
				$aria = " aria-describedby='$id-note'";
			} else {
				$note = '';
				$aria = '';
			}
			if ( is_array( $default ) ) {
				foreach ( $default as $k => $v ) {
					$checked  = ( (string) $k === (string) $value ) ? ' selected="selected"' : '';
					$options .= "<option value='" . esc_attr( $k ) . "'$checked>$v</option>";
				}
			}
			$return = "
				<label class='label-select' for='$id'>$label</label>
				$element<select id='$id' name='$name'$aria$attributes />
					$options
				</select>$close
			$note";
			break;
	}

	if ( true === $echo ) {
		echo wp_kses( $return, ableplayer_kses_elements() );
	} else {
		return $return;
	}
}

/**
 * Save a group of AblePlayer settings.
 *
 * @param array $settings An array of settings.
 *
 * @return bool
 */
function ableplayer_update_options( $settings ) {
	if ( empty( $settings ) ) {
		return false;
	}
	$defaults = ableplayer_default_settings();
	$options  = get_option( 'ableplayer_settings' );
	if ( ! is_array( $options ) ) {
		$options = $defaults;
	}
	$settings = array_merge( $options, $settings );

	return update_option( 'ableplayer_settings', $settings );
}

/**
 * Update AblePlayer settings.
 *
 * @param array $post POST data.
 */
function ableplayer_update_settings( $post ) {
	$settings          = array();
	$replace_video     = ( ! empty( $post['replace_video'] ) && 'on' === $post['replace_video'] ) ? 'true' : 'false';
	$replace_audio     = ( ! empty( $post['replace_audio'] ) && 'on' === $post['replace_audio'] ) ? 'true' : 'false';
	$exclude_class     = ( ! empty( $post['exclude_class'] ) ) ? sanitize_text_field( $post['exclude_class'] ) : '';
	$replace_playlists = ( ! empty( $post['replace_playlists'] ) && 'on' === $post['replace_playlists'] ) ? 'true' : 'false';
	$disable_elements  = ( ! empty( $post['disable_elements'] ) && 'on' === $post['disable_elements'] ) ? 'true' : 'false';
	$youtube_nocookie  = ( ! empty( $post['youtube_nocookie'] ) && 'on' === $post['youtube_nocookie'] ) ? 'true' : 'false';
	$play_inline       = ( ! empty( $post['play_inline'] ) && 'on' === $post['play_inline'] ) ? 'true' : 'false';
	$render_transcript = ( ! empty( $post['render_transcript'] ) && 'on' === $post['render_transcript'] ) ? 'true' : 'false';
	$hide_controls     = ( ! empty( $post['hide_controls'] ) && 'on' === $post['hide_controls'] ) ? 'true' : 'false';
	$default_speed     = ( isset( $post['default_speed'] ) ) ? $post['default_speed'] : 'animals';
	$seek_interval     = ( isset( $post['seek_interval'] ) && $post['seek_interval'] > 5 ) ? absint( $post['seek_interval'] ) : '';
	$default_heading   = ( isset( $post['default_heading'] ) ) ? absint( $post['default_heading'] ) : 'auto';

	$settings['replace_video']     = $replace_video;
	$settings['replace_audio']     = $replace_audio;
	$settings['exclude_class']     = $exclude_class;
	$settings['replace_playlists'] = $replace_playlists;
	$settings['disable_elements']  = $disable_elements;
	$settings['youtube_nocookie']  = $youtube_nocookie;
	$settings['seek_interval']     = $seek_interval;
	$settings['play_inline']       = $play_inline;
	$settings['render_transcript'] = $render_transcript;
	$settings['hide_controls']     = $hide_controls;
	$settings['default_speed']     = $default_speed;
	$settings['default_heading']   = $default_heading;

	ableplayer_update_options( $settings );
}

/**
 * Build AblePlayer settings form.
 */
function ableplayer_settings_form() {
	if ( ! empty( $_POST ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'ableplayer-nonce' ) ) {
			wp_die( 'AblePlayer: Security check failed' );
		}
		$post = map_deep( $_POST, 'sanitize_textarea_field' );
		if ( isset( $post['ableplayer_settings'] ) ) {
			ableplayer_update_settings( $post );
			wp_admin_notice(
				__( 'AblePlayer Default Settings saved', 'ableplayer' ),
				array(
					'type' => 'success',
				)
			);
		}
	}
	?>
	<div class="wrap ableplayer-admin ableplayer-settings-page" id="ableplayer_settings">
		<h1><?php esc_html_e( 'Able Player Settings', 'ableplayer' ); ?></h1>
		<div class="ableplayer-tabs">
		<div class="tabs" role="tablist" data-default="ableplayer-settings">
			<button type="button" role="tab" aria-selected="false" id="tab_settings" aria-controls="ableplayer-settings"><?php esc_html_e( 'Settings', 'ableplayer' ); ?></button>
			<button type="button" role="tab" aria-selected="false" id="tab_shortcode" aria-controls="ableplayer-shortcode"><?php esc_html_e( 'Shortcodes', 'ableplayer' ); ?></button>
		</div>
		<div class="settings postbox-container ableplayer-wide">
			<div class="metabox-holder">
				<div class="ui-sortable meta-box-sortables">
					<div class="wptab postbox" aria-labelledby="tab_settings" role="tabpanel" id="ableplayer-settings">
						<h2><?php esc_html_e( 'AblePlayer Settings', 'ableplayer' ); ?></h2>

						<div class="inside">
							<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=ableplayer#ableplayer-settings' ) ); ?>">
								<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'ableplayer-nonce' ) ); ?>" />
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'replace_video',
										'label' => __( 'Use Able Player for all <code>video</code> elements.', 'ableplayer' ),
										'type'  => 'checkbox-single',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'replace_audio',
										'label' => __( 'Use Able Player for all <code>audio</code> elements.', 'ableplayer' ),
										'type'  => 'checkbox-single',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'exclude_class',
										'label' => __( 'Exclude class from Able Player parsing', 'ableplayer' ),
										'note'  => __( 'Disable Able Player on <code>video</code> or <code>audio</code> elements with this class or with a parent element with the class.', 'ableplayer' ),
										'type'  => 'text',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'replace_playlists',
										'label' => __( 'Use Able Player for WordPress media playlists.', 'ableplayer' ),
										'type'  => 'checkbox-single',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'disable_elements',
										'label' => __( 'Disable MediaElement JS.', 'ableplayer' ),
										'type'  => 'checkbox-single',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'youtube_nocookie',
										'label' => __( 'Set YouTube videos to use the nocookie parameter for increased privacy.', 'ableplayer' ),
										'type'  => 'checkbox-single',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'play_inline',
										'label' => __( 'Force mobile devices to play inline, instead of using their own media player.', 'ableplayer' ),
										'type'  => 'checkbox-single',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'render_transcript',
										'label' => __( 'Insert interactive transcript container.', 'ableplayer' ),
										'type'  => 'checkbox-single',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'    => 'default_speed',
										'label'   => __( 'Preferred speed control icon', 'ableplayer' ),
										'type'    => 'select',
										'default' => array(
											'animals' => __( 'Animals: Tortoise and Hare', 'ableplayer' ),
											'arrows'  => __( 'Arrows', 'ableplayer' ),
										),
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'seek_interval',
										'label' => __( 'Default seek interval in seconds.', 'ableplayer' ),
										'type'  => 'number',
										'atts'  => array(
											'min'  => 5,
											'step' => 5,
										),
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'  => 'hide_controls',
										'label' => __( 'Visually hide controls during playback', 'ableplayer' ),
										'type'  => 'checkbox-single',
									)
								);
								?>
								</p>
								<p>
								<?php
								ableplayer_settings_field(
									array(
										'name'    => 'default_heading',
										'label'   => __( 'Default hidden heading level', 'ableplayer' ),
										'type'    => 'select',
										'default' => array(
											'auto' => __( 'Automatically set', 'ableplayer' ),
											'0'    => __( 'No heading', 'ableplayer' ),
											'2'    => 'H2',
											'3'    => 'H3',
											'4'    => 'H4',
										),
									)
								);
								?>
								</p>
								<p>
									<input type="submit" name="ableplayer_settings" class="button-primary" value="<?php esc_html_e( 'Save Settings', 'ableplayer' ); ?>"/>
								</p>
							</form>
						</div>
					</div>

					<div class="wptab postbox initial-hidden" aria-labelledby="tab_shortcode" role="tabpanel" id="ableplayer-shortcode">
						<h2><?php esc_html_e( 'Create Shortcode', 'ableplayer' ); ?></h2>

						<div class="inside">
							<?php
							$data = ableplayer_generate( 'array' );
							ableplayer_generator( $data );
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
			<?php ableplayer_show_sidebar(); ?>
	</div>
	</div>
	<?php
}

/**
 * Add AblePlayer menu items to main admin menu
 */
function ableplayer_menu() {
	add_options_page(
		__( 'Able Player', 'ableplayer' ),
		__( 'Able Player', 'ableplayer' ),
		'manage_options',
		'ableplayer',
		'ableplayer_settings_form'
	);
}
add_action( 'admin_menu', 'ableplayer_menu' );

/**
 * Produce AblePlayer admin sidebar
 */
function ableplayer_show_sidebar() {
	?>
<div class="postbox-container ableplayer-narrow">
	<div class="metabox-holder">
		<div class="ui-sortable meta-box-sortables">
			<div class="postbox">
				<h2><?php esc_html_e( 'AblePlayer Resources', 'ableplayer' ); ?></h2>

				<div class="inside">
					<p>
						<?php
						// Translators: URL for AblePlayer github docs.
						echo wp_kses_post( sprintf( __( 'Learn more about the <a href="%s">AblePlayer accessible media player</a>.', 'ableplayer' ), 'https://ableplayer.github.io/ableplayer/' ) );
						// Translators: URL for Joe Dolson donate page.
						echo ' ' . wp_kses_post( sprintf( __( 'Help support Able Player! <a href="%s">Sponsor Joe Dolson</a>, AblePlayer lead developer.', 'ableplayer' ), 'https://www.joedolson.com/donate/' ) );
						?>
					</p>
					<ul class="ableplayer-flex ableplayer-social">
						<li><a href="https://toot.io/@joedolson">
							<svg aria-hidden="true" width="24" height="24" viewBox="0 0 61 65" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M60.7539 14.3904C59.8143 7.40642 53.7273 1.90257 46.5117 0.836066C45.2943 0.655854 40.6819 0 29.9973 0H29.9175C19.2299 0 16.937 0.655854 15.7196 0.836066C8.70488 1.87302 2.29885 6.81852 0.744617 13.8852C-0.00294988 17.3654 -0.0827298 21.2237 0.0561464 24.7629C0.254119 29.8384 0.292531 34.905 0.753482 39.9598C1.07215 43.3175 1.62806 46.6484 2.41704 49.9276C3.89445 55.9839 9.87499 61.0239 15.7344 63.0801C22.0077 65.2244 28.7542 65.5804 35.2184 64.1082C35.9295 63.9428 36.6318 63.7508 37.3252 63.5321C38.8971 63.0329 40.738 62.4745 42.0913 61.4937C42.1099 61.4799 42.1251 61.4621 42.1358 61.4417C42.1466 61.4212 42.1526 61.3986 42.1534 61.3755V56.4773C42.153 56.4557 42.1479 56.4345 42.1383 56.4151C42.1287 56.3958 42.1149 56.3788 42.0979 56.3655C42.0809 56.3522 42.0611 56.3429 42.04 56.3382C42.019 56.3335 41.9971 56.3336 41.9761 56.3384C37.8345 57.3276 33.5905 57.8234 29.3324 57.8156C22.0045 57.8156 20.0336 54.3384 19.4693 52.8908C19.0156 51.6397 18.7275 50.3346 18.6124 49.0088C18.6112 48.9866 18.6153 48.9643 18.6243 48.9439C18.6333 48.9236 18.647 48.9056 18.6643 48.8915C18.6816 48.8774 18.7019 48.8675 18.7237 48.8628C18.7455 48.858 18.7681 48.8585 18.7897 48.8641C22.8622 49.8465 27.037 50.3423 31.2265 50.3412C32.234 50.3412 33.2387 50.3412 34.2463 50.3146C38.4598 50.1964 42.9009 49.9808 47.0465 49.1713C47.1499 49.1506 47.2534 49.1329 47.342 49.1063C53.881 47.8507 60.1038 43.9097 60.7362 33.9301C60.7598 33.5372 60.8189 29.8148 60.8189 29.4071C60.8218 28.0215 61.2651 19.5781 60.7539 14.3904Z" fill="url(#paint0_linear_89_8)"/><path d="M50.3943 22.237V39.5876H43.5185V22.7481C43.5185 19.2029 42.0411 17.3949 39.036 17.3949C35.7325 17.3949 34.0778 19.5338 34.0778 23.7585V32.9759H27.2434V23.7585C27.2434 19.5338 25.5857 17.3949 22.2822 17.3949C19.2949 17.3949 17.8027 19.2029 17.8027 22.7481V39.5876H10.9298V22.237C10.9298 18.6918 11.835 15.8754 13.6453 13.7877C15.5128 11.7049 17.9623 10.6355 21.0028 10.6355C24.522 10.6355 27.1813 11.9885 28.9542 14.6917L30.665 17.5633L32.3788 14.6917C34.1517 11.9885 36.811 10.6355 40.3243 10.6355C43.3619 10.6355 45.8114 11.7049 47.6847 13.7877C49.4931 15.8734 50.3963 18.6899 50.3943 22.237Z" fill="white"/><defs><linearGradient id="paint0_linear_89_8" x1="30.5" y1="0" x2="30.5" y2="65" gradientUnits="userSpaceOnUse"><stop stop-color="#6364FF"/><stop offset="1" stop-color="#563ACC"/></linearGradient></defs></svg>
							<span class="screen-reader-text">Mastodon</span></a>
						</li>
						<li><a href="https://bsky.app/profile/joedolson.bsky.social">
							<svg aria-hidden="true" width="24" height="24" viewBox="0 0 568 501" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M123.121 33.6637C188.241 82.5526 258.281 181.681 284 234.873C309.719 181.681 379.759 82.5526 444.879 33.6637C491.866 -1.61183 568 -28.9064 568 57.9464C568 75.2916 558.055 203.659 552.222 224.501C531.947 296.954 458.067 315.434 392.347 304.249C507.222 323.8 536.444 388.56 473.333 453.32C353.473 576.312 301.061 422.461 287.631 383.039C285.169 375.812 284.017 372.431 284 375.306C283.983 372.431 282.831 375.812 280.369 383.039C266.939 422.461 214.527 576.312 94.6667 453.32C31.5556 388.56 60.7778 323.8 175.653 304.249C109.933 315.434 36.0535 296.954 15.7778 224.501C9.94525 203.659 0 75.2916 0 57.9464C0 -28.9064 76.1345 -1.61183 123.121 33.6637Z" fill="#1185fe"/></svg>
							<span class="screen-reader-text">Bluesky</span></a>
						</li>
						<li><a href="https://linkedin.com/in/joedolson">
							<svg aria-hidden="true" height="24" viewBox="0 0 72 72" width="24" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path d="M8,72 L64,72 C68.418278,72 72,68.418278 72,64 L72,8 C72,3.581722 68.418278,-8.11624501e-16 64,0 L8,0 C3.581722,8.11624501e-16 -5.41083001e-16,3.581722 0,8 L0,64 C5.41083001e-16,68.418278 3.581722,72 8,72 Z" fill="#007EBB"/><path d="M62,62 L51.315625,62 L51.315625,43.8021149 C51.315625,38.8127542 49.4197917,36.0245323 45.4707031,36.0245323 C41.1746094,36.0245323 38.9300781,38.9261103 38.9300781,43.8021149 L38.9300781,62 L28.6333333,62 L28.6333333,27.3333333 L38.9300781,27.3333333 L38.9300781,32.0029283 C38.9300781,32.0029283 42.0260417,26.2742151 49.3825521,26.2742151 C56.7356771,26.2742151 62,30.7644705 62,40.051212 L62,62 Z M16.349349,22.7940133 C12.8420573,22.7940133 10,19.9296567 10,16.3970067 C10,12.8643566 12.8420573,10 16.349349,10 C19.8566406,10 22.6970052,12.8643566 22.6970052,16.3970067 C22.6970052,19.9296567 19.8566406,22.7940133 16.349349,22.7940133 Z M11.0325521,62 L21.769401,62 L21.769401,27.3333333 L11.0325521,27.3333333 L11.0325521,62 Z" fill="#FFF"/></g></svg>
							<span class="screen-reader-text">LinkedIn</span></a>
						</li>
						<li><a href="https://github.com/joedolson">
							<svg aria-hidden="true" width="24" height="24" viewBox="0 0 1024 1024" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8C0 11.54 2.29 14.53 5.47 15.59C5.87 15.66 6.02 15.42 6.02 15.21C6.02 15.02 6.01 14.39 6.01 13.72C4 14.09 3.48 13.23 3.32 12.78C3.23 12.55 2.84 11.84 2.5 11.65C2.22 11.5 1.82 11.13 2.49 11.12C3.12 11.11 3.57 11.7 3.72 11.94C4.44 13.15 5.59 12.81 6.05 12.6C6.12 12.08 6.33 11.73 6.56 11.53C4.78 11.33 2.92 10.64 2.92 7.58C2.92 6.71 3.23 5.99 3.74 5.43C3.66 5.23 3.38 4.41 3.82 3.31C3.82 3.31 4.49 3.1 6.02 4.13C6.66 3.95 7.34 3.86 8.02 3.86C8.7 3.86 9.38 3.95 10.02 4.13C11.55 3.09 12.22 3.31 12.22 3.31C12.66 4.41 12.38 5.23 12.3 5.43C12.81 5.99 13.12 6.7 13.12 7.58C13.12 10.65 11.25 11.33 9.47 11.53C9.76 11.78 10.01 12.26 10.01 13.01C10.01 14.08 10 14.94 10 15.21C10 15.42 10.15 15.67 10.55 15.59C13.71 14.53 16 11.53 16 8C16 3.58 12.42 0 8 0Z" transform="scale(64)" fill="#1B1F23"/></svg>
							<span class="screen-reader-text">GitHub</span></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
	<?php
}

/**
 * Get Able Player default settings.
 *
 * @return array
 */
function ableplayer_default_settings() {
	$settings = array(
		'replace_video'     => 'false',
		'replace_audio'     => 'false',
		'exclude_class'     => '',
		'replace_playlists' => 'false',
		'disable_elements'  => 'false',
		'youtube_nocookie'  => 'false',
		'play_inline'       => 'true',
		'render_transcript' => 'true',
		'default_speed'     => 'animals',
		'seek_interval'     => '30',
		'hide_controls'     => 'false',
		'default_heading'   => 'auto',
		'last_shortcode'    => '',
	);

	return $settings;
}
