<?php
/**
 * 后台"外观 → 自定义"面板。
 *
 * @package PureAura
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 复选框清洗。
 *
 * @param mixed $value 值。
 * @return bool
 */
function pureaura_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * 整数范围清洗（闭包工厂）。
 *
 * @param int $min 最小值。
 * @param int $max 最大值。
 * @return callable
 */
function pureaura_sanitize_range( $min, $max ) {
	return function ( $value ) use ( $min, $max ) {
		$value = (int) $value;
		return max( $min, min( $max, $value ) );
	};
}

/**
 * 白名单清洗（闭包工厂）。
 *
 * @param array $allowed 允许值。
 * @return callable
 */
function pureaura_sanitize_choice( $allowed ) {
	return function ( $value ) use ( $allowed ) {
		return in_array( $value, $allowed, true ) ? $value : $allowed[0];
	};
}

/**
 * 注册设置。
 *
 * @param WP_Customize_Manager $wp_customize 自定义器实例。
 */
function pureaura_customize_register( $wp_customize ) {
	/* ---------- 外观与材质 ---------- */
	$wp_customize->add_section(
		'pureaura_material',
		array(
			'title'       => __( '外观与材质', 'pureaura' ),
			'priority'    => 20,
			'description' => __( '决定整站的底色、玻璃质感与圆角。改动会即时反映到前台。', 'pureaura' ),
		)
	);

	$wp_customize->add_setting(
		'pureaura_theme_mode',
		array(
			'default'           => 'auto',
			'sanitize_callback' => pureaura_sanitize_choice( array( 'auto', 'light', 'dark' ) ),
		)
	);
	$wp_customize->add_control(
		'pureaura_theme_mode',
		array(
			'label'       => __( '默认外观', 'pureaura' ),
			'description' => __( '"跟随系统"会读取访客设备的深浅色设置。读者仍可在前台自行切换。', 'pureaura' ),
			'section'     => 'pureaura_material',
			'type'        => 'select',
			'choices'     => array(
				'auto'  => __( '跟随系统', 'pureaura' ),
				'light' => __( '浅色', 'pureaura' ),
				'dark'  => __( '深色', 'pureaura' ),
			),
		)
	);

	$wp_customize->add_setting(
		'pureaura_accent',
		array(
			'default'           => '#2d5bff',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'pureaura_accent',
			array(
				'label'       => __( '强调色', 'pureaura' ),
				'description' => __( '用于链接、按钮、当前导航项。建议选中等明度的颜色，太浅会看不清。', 'pureaura' ),
				'section'     => 'pureaura_material',
			)
		)
	);

	$wp_customize->add_setting(
		'pureaura_blur',
		array(
			'default'           => 26,
			'sanitize_callback' => pureaura_sanitize_range( 0, 48 ),
		)
	);
	$wp_customize->add_control(
		'pureaura_blur',
		array(
			'label'       => __( '玻璃模糊强度', 'pureaura' ),
			'description' => __( '数值越大，玻璃后的背景越模糊。0 为完全透明（性能最省）。', 'pureaura' ),
			'section'     => 'pureaura_material',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 48,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'pureaura_glass',
		array(
			'default'           => 46,
			'sanitize_callback' => pureaura_sanitize_range( 30, 95 ),
		)
	);
	$wp_customize->add_control(
		'pureaura_glass',
		array(
			'label'       => __( '玻璃不透明度', 'pureaura' ),
			'description' => __( '百分比越高，控件底色越实、文字越清晰；越低越通透。', 'pureaura' ),
			'section'     => 'pureaura_material',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 30,
				'max'  => 95,
				'step' => 1,
			),
		)
	);

	$wp_customize->add_setting(
		'pureaura_radius',
		array(
			'default'           => 24,
			'sanitize_callback' => pureaura_sanitize_range( 0, 36 ),
		)
	);
	$wp_customize->add_control(
		'pureaura_radius',
		array(
			'label'       => __( '控件圆角', 'pureaura' ),
			'section'     => 'pureaura_material',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 0,
				'max'  => 36,
				'step' => 1,
			),
		)
	);

	/* ---------- 布局 ---------- */
	$wp_customize->add_section(
		'pureaura_layout',
		array(
			'title'    => __( '布局', 'pureaura' ),
			'priority' => 22,
		)
	);

	$wp_customize->add_setting(
		'pureaura_layout',
		array(
			'default'           => 'editorial',
			'sanitize_callback' => pureaura_sanitize_choice( array( 'editorial', 'grid', 'minimal' ) ),
		)
	);
	$wp_customize->add_control(
		'pureaura_layout',
		array(
			'label'       => __( '文章列表样式', 'pureaura' ),
			'description' => __( '编辑式：大标题头条 + 分组列表。网格：两列排布。极简：只留标题。', 'pureaura' ),
			'section'     => 'pureaura_layout',
			'type'        => 'select',
			'choices'     => array(
				'editorial' => __( '编辑式（推荐）', 'pureaura' ),
				'grid'      => __( '两列网格', 'pureaura' ),
				'minimal'   => __( '极简列表', 'pureaura' ),
			),
		)
	);

	$wp_customize->add_setting(
		'pureaura_rail_side',
		array(
			'default'           => 'left',
			'sanitize_callback' => pureaura_sanitize_choice( array( 'left', 'right' ) ),
		)
	);
	$wp_customize->add_control(
		'pureaura_rail_side',
		array(
			'label'   => __( '导航条位置', 'pureaura' ),
			'section' => 'pureaura_layout',
			'type'    => 'select',
			'choices' => array(
				'left'  => __( '左侧', 'pureaura' ),
				'right' => __( '右侧', 'pureaura' ),
			),
		)
	);

	$wp_customize->add_setting(
		'pureaura_maxw',
		array(
			'default'           => 1180,
			'sanitize_callback' => pureaura_sanitize_range( 980, 1560 ),
		)
	);
	$wp_customize->add_control(
		'pureaura_maxw',
		array(
			'label'       => __( '内容宽度', 'pureaura' ),
			'description' => __( '单位像素。宽屏可适当加大，正文行宽会自动保持在舒适范围。', 'pureaura' ),
			'section'     => 'pureaura_layout',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 980,
				'max'  => 1560,
				'step' => 20,
			),
		)
	);

	$wp_customize->add_setting(
		'pureaura_bg_style',
		array(
			'default'           => 'aurora',
			'sanitize_callback' => pureaura_sanitize_choice( array( 'aurora', 'solid', 'image' ) ),
		)
	);
	$wp_customize->add_control(
		'pureaura_bg_style',
		array(
			'label'   => __( '背景', 'pureaura' ),
			'section' => 'pureaura_layout',
			'type'    => 'select',
			'choices' => array(
				'aurora' => __( '流光（默认）', 'pureaura' ),
				'solid'  => __( '纯色', 'pureaura' ),
				'image'  => __( '自定义图片', 'pureaura' ),
			),
		)
	);

	$wp_customize->add_setting(
		'pureaura_bg_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'pureaura_bg_image',
			array(
				'label'       => __( '背景图片', 'pureaura' ),
				'description' => __( '仅在"背景"选择自定义图片时生效。建议使用低对比、无明显主体的图片。', 'pureaura' ),
				'section'     => 'pureaura_layout',
			)
		)
	);

	$wp_customize->add_setting(
		'pureaura_bg_motion',
		array(
			'default'           => true,
			'sanitize_callback' => 'pureaura_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'pureaura_bg_motion',
		array(
			'label'       => __( '背景缓慢流动', 'pureaura' ),
			'description' => __( '关闭可进一步省电。系统开启"减少动态效果"时也会自动关闭。', 'pureaura' ),
			'section'     => 'pureaura_layout',
			'type'        => 'checkbox',
		)
	);

	/* ---------- 阅读体验 ---------- */
	$wp_customize->add_section(
		'pureaura_reading',
		array(
			'title'       => __( '阅读体验', 'pureaura' ),
			'priority'    => 24,
			'description' => __( '这些开关只影响文章页与列表的辅助信息。', 'pureaura' ),
		)
	);

	$reading_toggles = array(
		'pureaura_show_progress' => __( '顶部阅读进度条', 'pureaura' ),
		'pureaura_show_toc'      => __( '文章目录', 'pureaura' ),
		'pureaura_show_time'     => __( '预计阅读时长', 'pureaura' ),
		'pureaura_show_views'    => __( '浏览量', 'pureaura' ),
		'pureaura_show_thumb'    => __( '列表缩略图', 'pureaura' ),
	);

	foreach ( $reading_toggles as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => true,
				'sanitize_callback' => 'pureaura_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => 'pureaura_reading',
				'type'    => 'checkbox',
			)
		);
	}

	$wp_customize->add_setting(
		'pureaura_excerpt_len',
		array(
			'default'           => 72,
			'sanitize_callback' => pureaura_sanitize_range( 20, 200 ),
		)
	);
	$wp_customize->add_control(
		'pureaura_excerpt_len',
		array(
			'label'       => __( '摘要长度', 'pureaura' ),
			'description' => __( '按字符数计算，中文按字计。', 'pureaura' ),
			'section'     => 'pureaura_reading',
			'type'        => 'range',
			'input_attrs' => array(
				'min'  => 20,
				'max'  => 200,
				'step' => 4,
			),
		)
	);

	/* ---------- 文章页 ---------- */
	$wp_customize->add_section(
		'pureaura_single',
		array(
			'title'    => __( '文章页', 'pureaura' ),
			'priority' => 26,
		)
	);

	$single_toggles = array(
		'pureaura_show_author'  => __( '作者卡片', 'pureaura' ),
		'pureaura_show_related' => __( '相关文章', 'pureaura' ),
		'pureaura_show_share'   => __( '分享按钮', 'pureaura' ),
	);

	foreach ( $single_toggles as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => true,
				'sanitize_callback' => 'pureaura_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => 'pureaura_single',
				'type'    => 'checkbox',
			)
		);
	}

	/* ---------- 页脚 ---------- */
	$wp_customize->add_section(
		'pureaura_footer',
		array(
			'title'    => __( '页脚与备案', 'pureaura' ),
			'priority' => 28,
		)
	);

	$wp_customize->add_setting(
		'pureaura_footer_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'pureaura_footer_text',
		array(
			'label'       => __( '页脚简介', 'pureaura' ),
			'description' => __( '留空则显示站点副标题。', 'pureaura' ),
			'section'     => 'pureaura_footer',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_setting(
		'pureaura_icp',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'pureaura_icp',
		array(
			'label'       => __( 'ICP 备案号', 'pureaura' ),
			'description' => __( '例如：粤ICP备00000000号。留空则不显示。', 'pureaura' ),
			'section'     => 'pureaura_footer',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'pureaura_police',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'pureaura_police',
		array(
			'label'       => __( '公安备案号', 'pureaura' ),
			'description' => __( '例如：粤公网安备 00000000000000号。留空则不显示。', 'pureaura' ),
			'section'     => 'pureaura_footer',
			'type'        => 'text',
		)
	);

	$socials = array(
		'pureaura_social_weibo'  => __( '微博链接', 'pureaura' ),
		'pureaura_social_github' => __( 'GitHub 链接', 'pureaura' ),
		'pureaura_social_x'      => __( 'X 链接', 'pureaura' ),
	);

	foreach ( $socials as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $label,
				'section' => 'pureaura_footer',
				'type'    => 'url',
			)
		);
	}

	$wp_customize->add_setting(
		'pureaura_social_email',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_email',
		)
	);
	$wp_customize->add_control(
		'pureaura_social_email',
		array(
			'label'   => __( '联系邮箱', 'pureaura' ),
			'section' => 'pureaura_footer',
			'type'    => 'email',
		)
	);

	/* ---------- 高级 ---------- */
	$wp_customize->add_section(
		'pureaura_advanced',
		array(
			'title'       => __( '高级', 'pureaura' ),
			'priority'    => 30,
			'description' => __( '自定义 CSS 会原样输出到页面头部，写错可能影响显示，请先备份。', 'pureaura' ),
		)
	);

	$wp_customize->add_setting(
		'pureaura_custom_css',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_strip_all_tags',
		)
	);
	$wp_customize->add_control(
		'pureaura_custom_css',
		array(
			'label'   => __( '自定义 CSS', 'pureaura' ),
			'section' => 'pureaura_advanced',
			'type'    => 'textarea',
		)
	);
}
add_action( 'customize_register', 'pureaura_customize_register' );

/**
 * 自定义器预览脚本，让颜色改动免刷新预览。
 */
function pureaura_customize_preview_js() {
	wp_enqueue_script(
		'pureaura-customize-preview',
		get_template_directory_uri() . '/assets/js/customize-preview.js',
		array( 'customize-preview' ),
		PUREAURA_VERSION,
		array( 'in_footer' => true )
	);
}
add_action( 'customize_preview_init', 'pureaura_customize_preview_js' );
