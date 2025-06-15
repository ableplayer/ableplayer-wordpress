<?php
/**
 * Custom KSES to allow some otherwise excluded attributes.
 *
 * @category Utilities
 * @package  AblePlayer
 * @author   Joe Dolson
 * @license  GPLv3
 * @link     https://www.joedolson.com/ableplayer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Array of allowed elements for using KSES on forms.
 *
 * @return array
 */
function ableplayer_kses_elements() {
	$elements = array(
		'svg'              => array(
			'class'           => array(),
			'style'           => array(),
			'focusable'       => array(),
			'role'            => array(),
			'aria-labelledby' => array(),
			'xmlns'           => array(),
			'viewbox'         => array(),
		),
		'g'                => array(
			'fill' => array(),
		),
		'title'            => array(
			'id'    => array(),
			'title' => array(),
		),
		'path'             => array(
			'd'    => array(),
			'fill' => array(),
		),
		'h2'               => array(
			'class' => array(),
			'id'    => array(),
		),
		'h3'               => array(
			'class' => array(),
			'id'    => array(),
		),
		'h4'               => array(
			'class' => array(),
			'id'    => array(),
		),
		'h5'               => array(
			'class' => array(),
			'id'    => array(),
		),
		'h6'               => array(
			'class' => array(),
			'id'    => array(),
		),
		'label'            => array(
			'for'             => array(),
			'class'           => array(),
			'id'              => array(),
			'aria-labelledby' => array(),
		),
		'option'           => array(
			'value'       => array(),
			'selected'    => array(),
			'data-period' => array(),
		),
		'select'           => array(
			'id'               => array(),
			'aria-describedby' => array(),
			'aria-labelledby'  => array(),
			'name'             => array(),
			'disabled'         => array(),
			'min'              => array(),
			'max'              => array(),
			'required'         => array(),
			'readonly'         => array(),
			'autocomplete'     => array(),
			'class'            => array(),
		),
		'input'            => array(
			'id'               => array(),
			'class'            => array(),
			'aria-describedby' => array(),
			'aria-labelledby'  => array(),
			'value'            => array(),
			'type'             => array(),
			'name'             => array(),
			'size'             => array(),
			'checked'          => array(),
			'disabled'         => array(),
			'min'              => array(),
			'max'              => array(),
			'required'         => array(),
			'readonly'         => array(),
			'autocomplete'     => array(),
			'data-href'        => array(),
			'placeholder'      => array(),
			'data-variable'    => array(),
			'maxlength'        => array(),
			'step'             => array(),
			'data-context'     => array(),
			'data-action'      => array(),
		),
		'textarea'         => array(
			'id'               => array(),
			'class'            => array(),
			'cols'             => array(),
			'rows'             => array(),
			'aria-describedby' => array(),
			'aria-labelledby'  => array(),
			'disabled'         => array(),
			'required'         => array(),
			'readonly'         => array(),
			'name'             => array(),
			'placeholder'      => array(),
		),
		'form'             => array(
			'id'     => array(),
			'name'   => array(),
			'action' => array(),
			'method' => array(),
			'class'  => array(),
			'role'   => array(),
		),
		'button'           => array(
			'name'                    => array(),
			'disabled'                => array(),
			'type'                    => array(),
			'class'                   => array(),
			'aria-expanded'           => array(),
			'aria-describedby'        => array(),
			'role'                    => array(),
			'aria-selected'           => array(),
			'aria-controls'           => array(),
			'data-href'               => array(),
			'data-type'               => array(),
			'aria-pressed'            => array(),
			'id'                      => array(),
			'data-context'            => array(),
			'data-model'              => array(),
			'data-event'              => array(),
			'data-modal-content-id'   => array(),
			'data-modal-prefix-class' => array(),
			'data-modal-close-text'   => array(),
			'data-modal-title'        => array(),
			'data-begin'              => array(),
			'data-end'                => array(),
			'data-value'              => array(),
			'value'                   => array(),
		),
		'ul'               => array(
			'class' => array(),
			'id'    => array(),
		),
		'fieldset'         => array(
			'class' => array(),
			'id'    => array(),
		),
		'legend'           => array(
			'class' => array(),
			'id'    => array(),
		),
		'li'               => array(
			'class' => array(),
		),
		'span'             => array(
			'id'          => array(),
			'class'       => array(),
			'aria-live'   => array(),
			'aria-hidden' => array(),
			'style'       => array(),
			'lang'        => array(),
			'title'       => array(),
		),
		'em'               => array(
			'id'          => array(),
			'class'       => array(),
			'aria-hidden' => array(),
		),
		'i'                => array(
			'id'          => array(),
			'class'       => array(),
			'aria-live'   => array(),
			'aria-hidden' => array(),
		),
		'strong'           => array(
			'id'          => array(),
			'class'       => array(),
			'aria-hidden' => array(),
		),
		'b'                => array(
			'id'    => array(),
			'class' => array(),
		),
		'abbr'             => array(
			'title'       => array(),
			'aria-hidden' => array(),
		),
		'hr'               => array(
			'class' => array(),
		),
		'p'                => array(
			'class' => array(),
		),
		'div'              => array(
			'class'           => array(),
			'aria-live'       => array(),
			'id'              => array(),
			'role'            => array(),
			'data-default'    => array(),
			'aria-labelledby' => array(),
			'style'           => array(),
			'lang'            => array(),
			'aria-label'      => array(),
			'tabindex'        => array(),
			'data-maptype'    => array(),
			'data-title'      => array(),
			'data-address'    => array(),
			'data-icon'       => array(),
			'data-lat'        => array(),
			'data-lng'        => array(),
		),
		'img'              => array(
			'class'    => true,
			'src'      => true,
			'alt'      => true,
			'width'    => true,
			'height'   => true,
			'id'       => true,
			'longdesc' => true,
			'tabindex' => true,
			'loading'  => true,
			'srcset'   => true,
		),
		'br'               => array(),
		'table'            => array(
			'class'           => array(),
			'id'              => array(),
			'aria-labelledby' => array(),
		),
		'caption'          => array(),
		'thead'            => array(),
		'tfoot'            => array(),
		'tbody'            => array(),
		'tr'               => array(
			'class' => array(),
			'id'    => array(),
		),
		'th'               => array(
			'scope'     => array(),
			'class'     => array(),
			'id'        => array(),
			'aria-sort' => array(),

		),
		'td'               => array(
			'class'        => array(),
			'id'           => array(),
			'aria-live'    => array(),
			'aria-current' => array(),
		),
		'a'                => array(
			'aria-label'       => array(),
			'aria-labelledby'  => array(),
			'aria-describedby' => array(),
			'href'             => array(),
			'class'            => array(),
			'aria-current'     => array(),
			'target'           => array(),
			'aria-pressed'     => array(),
			'data-title'       => array(),
			'id'               => array(),
			'rel'              => array(),
		),
		'section'          => array(
			'id'    => array(),
			'class' => array(),
		),
		'aside'            => array(
			'id'    => array(),
			'class' => array(),
		),
		'code'             => array(
			'class' => array(),
		),
		'pre'              => array(
			'class' => array(),
		),
		'duet-date-picker' => array(
			'identifier'        => array(),
			'first-day-of-week' => array(),
			'name'              => array(),
			'value'             => array(),
			'required'          => array(),
		),
		'time'             => array(
			'data-label' => array(),
			'class'      => array(),
			'datetime'   => array(),
		),
		'iframe'           => array(
			'width'           => array(),
			'height'          => array(),
			'src'             => array(),
			'title'           => array(),
			'frameborder'     => array(),
			'allow'           => array(),
			'allowfullscreen' => array(),
		),
		'article'          => array(
			'id'    => array(),
			'class' => array(),
		),
		'header'           => array(
			'id'    => array(),
			'class' => array(),
		),
		'nav'              => array(
			'id'         => array(),
			'class'      => array(),
			'aria-label' => array(),
		),
	);

	return $elements;
}
