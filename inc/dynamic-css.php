<?php
/**
 * 把后台选项转成 CSS 变量，实现"改设置即改外观"。
 *
 * @package PureAura
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 计算颜色亮度（0-1）。
 *
 * @param string $hex 十六进制颜色。
 * @return float
 */
function pureaura_luminance( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return 0;
	}

	$channels = array();
	foreach ( str_split( $hex, 2 ) as $pair ) {
		$value      = hexdec( $pair ) / 255;
		$channels[] = $value <= 0.03928 ? $value / 12.92 : pow( ( $value + 0.055 ) / 1.055, 2.4 );
	}

	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

/**
 * 按比例把颜色向白色或黑色混合。
 *
 * @param string $hex   基础颜色。
 * @param float  $ratio 混合比例 0-1。
 * @param string $with  目标颜色，白或黑。
 * @return string
 */
function pureaura_mix( $hex, $ratio, $with = 'ffffff' ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '#' . $with;
	}

	$ratio = max( 0, min( 1, (float) $ratio ) );
	$with  = ltrim( $with, '#' );

	$out = '#';
	for ( $i = 0; $i < 3; $i++ ) {
		$base   = hexdec( substr( $hex, $i * 2, 2 ) );
		$target = hexdec( substr( $with, $i * 2, 2 ) );
		$out   .= str_pad( dechex( (int) round( $base + ( $target - $base ) * $ratio ) ), 2, '0', STR_PAD_LEFT );
	}

	return $out;
}

/**
 * 生成主题变量与自定义样式。
 */
function pureaura_inline_css() {
	$accent      = sanitize_hex_color( pureaura_mod( 'pureaura_accent' ) );
	$accent      = $accent ? $accent : '#2d5bff';
	$accent_dark = pureaura_mix( $accent, 0.24 );
	$on_accent   = pureaura_luminance( $accent ) > 0.62 ? '#0a0c11' : '#ffffff';

	$blur   = max( 0, min( 48, (int) pureaura_mod( 'pureaura_blur' ) ) );
	$glass  = max( 30, min( 95, (int) pureaura_mod( 'pureaura_glass' ) ) ) / 100;
	$radius = max( 0, min( 36, (int) pureaura_mod( 'pureaura_radius' ) ) );
	$maxw   = max( 980, min( 1560, (int) pureaura_mod( 'pureaura_maxw' ) ) );

	$css  = ':root{';
	$css .= '--accent:' . esc_attr( $accent ) . ';';
	$css .= '--accent-dark:' . esc_attr( $accent_dark ) . ';';
	$css .= '--on-accent:' . esc_attr( $on_accent ) . ';';
	$css .= '--blur:' . $blur . 'px;';
	$css .= '--glass-a:' . round( $glass, 2 ) . ';';
	$css .= '--r:' . $radius . 'px;';
	$css .= '--maxw:' . $maxw . 'px;';
	$css .= '}';

	if ( 'image' === pureaura_mod( 'pureaura_bg_style' ) ) {
		$bg_image = pureaura_mod( 'pureaura_bg_image' );
		if ( $bg_image ) {
			$css .= '.sky{background-image:linear-gradient(180deg,rgba(255,255,255,.28),rgba(255,255,255,.08)),url("' . esc_url( $bg_image ) . '");background-size:cover;background-position:center;}';
			$css .= 'html[data-theme="dark"] .sky{background-image:linear-gradient(180deg,rgba(6,8,14,.62),rgba(6,8,14,.78)),url("' . esc_url( $bg_image ) . '");}';
			$css .= '.sky__field{display:none;}';
		}
	}

	$custom = (string) pureaura_mod( 'pureaura_custom_css' );
	if ( '' !== trim( $custom ) ) {
		$css .= wp_strip_all_tags( $custom );
	}

	wp_add_inline_style( 'pureaura', $css );
}
add_action( 'wp_enqueue_scripts', 'pureaura_inline_css', 20 );
