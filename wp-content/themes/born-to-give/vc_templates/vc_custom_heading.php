<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $source
 * @var $text
 * @var $link
 * @var $google_fonts
 * @var $font_container
 * @var $el_class
 * @var $css
 * @var $icon
 * @var $font_container_data - returned from $this->getAttributes
 * @var $google_fonts_data - returned from $this->getAttributes
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Custom_heading
 */
$source = $text = $link = $google_fonts = $font_container = $el_class = $css = $font_container_data = $google_fonts_data = $icon = '';
// This is needed to extract $font_container_data and $google_fonts_data

extract( shortcode_atts( array(
		'icon'  => '',
		'icon_size'  => '67',
		'icon_pos' => '',
		'subtitle'   => '',
		'stripe_pos' => '',
		'subtitle_color' => '',
		'stm_title_font_weight' => ''
), $atts ) );

extract( $this->getAttributes( $atts ) );

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

extract( $this->getStyles( $el_class, $css, $google_fonts_data, $font_container_data, $atts ) );

$settings = get_option( 'wpb_js_google_fonts_subsets' );
if ( is_array( $settings ) && ! empty( $settings ) ) {
	$subsets = '&subset=' . implode( ',', $settings );
} else {
	$subsets = '';
}

if ( isset( $google_fonts_data['values']['font_family'] ) ) {
	wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $google_fonts_data['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $google_fonts_data['values']['font_family'] . $subsets );
}

if( !empty( $stm_title_font_weight ) ) {
	$styles[] = 'font-weight:' . $stm_title_font_weight;
}

if ( ! empty( $styles ) ) {
	$style = 'style="' . esc_attr( implode( ';', $styles ) ) . '"';
} else {
	$style = '';
}

if ( 'post_title' === $source ) {
	$text = get_the_title( get_the_ID() );
}

if( ! empty( $font_container_data['values']['text_align'] ) ){
	$css_class .= ' text_align_' . $font_container_data['values']['text_align'];
}

if ( ! empty( $link ) ) {
	$link = vc_build_link( $link );
	$text = '<a href="' . esc_attr( $link['url'] ) . '"'
	        . ( $link['target'] ? ' target="' . esc_attr( $link['target'] ) . '"' : '' )
	        . ( $link['title'] ? ' title="' . esc_attr( $link['title'] ) . '"' : '' )
	        . '>' . $text . '</a>';
}

if( $icon ){
	$css_class .= ' has_icon';

	if( !empty( $icon_pos ) ) {
		$css_class .= ' icon_pos_' . $icon_pos;
	}
}

if( $subtitle ){
	$css_class .= ' has_subtitle';
}

if( $stripe_pos == 'hide' ) {
	$css_class .= ' title_no_stripe';
}

if( $stripe_pos == 'between' ) {
	$css_class .= ' stripe_' . esc_attr( $stripe_pos );
}

$subtitle_styles = array();
$subtitle_style = '';

if( !empty( $subtitle_color ) ) {
	$subtitle_styles[] = 'color:' . esc_attr( $subtitle_color );
}

if( !empty( $subtitle_styles ) && is_array( $subtitle_styles ) ) {
	$subtitle_style = ' style="'. implode( ';', $subtitle_styles ) .'"';
}

$output = '';
if ( apply_filters( 'vc_custom_heading_template_use_wrapper', true ) ) {
	$output .= '<div class="' . esc_attr( $css_class ) . '" >';
	if( $icon ){
		$output .= '<div class="icon" style="font-size: ' . esc_attr( $icon_size ) . 'px; line-height: ' . esc_attr( $icon_size ) . 'px;"><i class="' . $icon . '"></i></div>';
	}
	$output .= '<' . $font_container_data['values']['tag'] . ' ' . $style . ' >';
	$output .= $text;
	if( !empty( $subtitle ) && $stripe_pos != 'between' ){
		$output .= '<span class="subtitle"'. $subtitle_style .'>' . $subtitle . '</span>';
	}
	$output .= '</' . $font_container_data['values']['tag'] . '>';
	if( !empty( $subtitle ) && $stripe_pos == 'between' ){
		$output .= '<div class="subtitle"'. $subtitle_style .'>' . $subtitle . '</div>';
	}
	$output .= '</div>';
} else {
	$output .= '<' . $font_container_data['values']['tag'] . ' ' . $style . ' class="' . esc_attr( $css_class ) . '">';
	$output .= $text;
	$output .= '</' . $font_container_data['values']['tag'] . '>';
}

echo $output;