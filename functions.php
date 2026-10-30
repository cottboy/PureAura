<?php
/**
 * PureAura 主题入口：环境准备、资源加载、通用工具函数。
 *
 * @package PureAura
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PUREAURA_VERSION', '1.0.0' );

/**
 * 主题基础能力。
 */
function pureaura_setup() {
	load_theme_textdomain( 'pureaura', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_editor_style( 'assets/css/editor.css' );

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( '主导航', 'pureaura' ),
			'footer'  => __( '页脚导航', 'pureaura' ),
		)
	);

	add_image_size( 'pureaura-cover', 1600, 900, true );
	add_image_size( 'pureaura-wide', 1200, 675, true );
	add_image_size( 'pureaura-thumb', 160, 160, true );
}
add_action( 'after_setup_theme', 'pureaura_setup' );

/**
 * 内容宽度。
 */
function pureaura_content_width() {
	$GLOBALS['content_width'] = 720;
}
add_action( 'after_setup_theme', 'pureaura_content_width', 0 );

/**
 * 载入样式与脚本。
 */
function pureaura_assets() {
	$uri = get_template_directory_uri();

	wp_enqueue_style( 'pureaura-base', get_stylesheet_uri(), array(), PUREAURA_VERSION );
	wp_enqueue_style( 'pureaura', $uri . '/assets/css/aura.css', array( 'pureaura-base' ), PUREAURA_VERSION );

	wp_enqueue_script(
		'pureaura',
		$uri . '/assets/js/aura.js',
		array(),
		PUREAURA_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'pureaura',
		'PureAuraData',
		array(
			'copied'  => __( '已复制', 'pureaura' ),
			'copy'    => __( '复制', 'pureaura' ),
			'copyFail' => __( '复制失败，请手动选择', 'pureaura' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'pureaura_assets' );

/**
 * 首屏前确定外观模式，避免深色模式闪白。
 */
function pureaura_color_mode_boot() {
	$mode = get_theme_mod( 'pureaura_theme_mode', 'auto' );
	?>
	<script>
	(function () {
		var fallback = <?php echo wp_json_encode( $mode ); ?>;
		var root = document.documentElement;
		root.classList.add('js');
		try {
			var saved = window.localStorage.getItem('pureaura-theme') || fallback;
			var dark = saved === 'dark' || (saved === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
			root.setAttribute('data-theme', dark ? 'dark' : 'light');
			root.setAttribute('data-theme-mode', saved);
		} catch (e) {
			root.setAttribute('data-theme', 'light');
			root.setAttribute('data-theme-mode', 'auto');
		}
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'pureaura_color_mode_boot', 1 );

/**
 * body class：布局、导航位置、字号等交给 CSS 分支。
 *
 * @param array $classes 现有 class。
 * @return array
 */
function pureaura_body_class( $classes ) {
	$classes[] = 'layout-' . pureaura_mod( 'pureaura_layout' );
	$classes[] = 'rail-' . pureaura_mod( 'pureaura_rail_side' );
	$classes[] = 'bg-' . pureaura_mod( 'pureaura_bg_style' );

	if ( ! pureaura_mod( 'pureaura_bg_motion' ) ) {
		$classes[] = 'still-sky';
	}
	if ( is_singular() ) {
		$classes[] = 'is-reading';
	}
	return $classes;
}
add_filter( 'body_class', 'pureaura_body_class' );

/**
 * 归档标题去掉"分类：""标签："这类前缀，标题自己说话。
 */
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

/**
 * 读取主题选项并带默认值。
 *
 * @param string $key 选项名。
 * @return mixed
 */
function pureaura_mod( $key ) {
	$defaults = array(
		'pureaura_accent'        => '#2d5bff',
		'pureaura_theme_mode'    => 'auto',
		'pureaura_blur'          => 26,
		'pureaura_glass'         => 46,
		'pureaura_radius'        => 24,
		'pureaura_layout'        => 'editorial',
		'pureaura_rail_side'     => 'left',
		'pureaura_maxw'          => 1180,
		'pureaura_bg_style'      => 'aurora',
		'pureaura_bg_image'      => '',
		'pureaura_bg_motion'     => true,
		'pureaura_show_progress' => true,
		'pureaura_show_toc'      => true,
		'pureaura_show_time'     => true,
		'pureaura_show_views'    => true,
		'pureaura_show_author'   => true,
		'pureaura_show_related'  => true,
		'pureaura_show_share'    => true,
		'pureaura_show_thumb'    => true,
		'pureaura_excerpt_len'   => 72,
		'pureaura_footer_text'   => '',
		'pureaura_icp'           => '',
		'pureaura_police'        => '',
		'pureaura_social_weibo'  => '',
		'pureaura_social_github' => '',
		'pureaura_social_x'      => '',
		'pureaura_social_email'  => '',
		'pureaura_custom_css'    => '',
	);

	$default = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return get_theme_mod( $key, $default );
}

/**
 * 载入功能模块。
 */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/comments.php';
require get_template_directory() . '/inc/dynamic-css.php';
require get_template_directory() . '/inc/customizer.php';
