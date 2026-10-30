<?php
/**
 * 模板工具函数：图标、摘要、阅读时长、目录、文章元信息等。
 *
 * @package PureAura
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 内联图标。全部为同一套 1.5px 描边线性图标，避免图标风格混杂。
 *
 * @param string $name  图标名。
 * @param int    $size  尺寸（像素）。
 * @param string $class 额外 class。
 * @return string
 */
function pureaura_icon( $name, $size = 20, $class = '' ) {
	$icons = array(
		'home'    => '<path d="M4 10.6 12 4l8 6.6V20a1 1 0 0 1-1 1h-4.6v-6.2H9.6V21H5a1 1 0 0 1-1-1Z"/>',
		'search'  => '<circle cx="11" cy="11" r="6.2"/><path d="m15.6 15.6 4.4 4.4"/>',
		'grid'    => '<rect x="4" y="4" width="6.4" height="6.4" rx="1.8"/><rect x="13.6" y="4" width="6.4" height="6.4" rx="1.8"/><rect x="4" y="13.6" width="6.4" height="6.4" rx="1.8"/><rect x="13.6" y="13.6" width="6.4" height="6.4" rx="1.8"/>',
		'sun'     => '<circle cx="12" cy="12" r="4"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6 7 7M17 17l1.4 1.4M18.4 5.6 17 7M7 17l-1.4 1.4"/>',
		'moon'    => '<path d="M20 13.4A8.2 8.2 0 0 1 10.6 4a8.2 8.2 0 1 0 9.4 9.4Z"/>',
		'auto'    => '<circle cx="12" cy="12" r="8"/><path d="M12 4a8 8 0 0 1 0 16Z" fill="currentColor" stroke="none"/>',
		'close'   => '<path d="m6 6 12 12M18 6 6 18"/>',
		'up'      => '<path d="M12 19V5M6 11l6-6 6 6"/>',
		'right'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'left'    => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
		'copy'    => '<rect x="9" y="9" width="11" height="11" rx="2.4"/><path d="M15 9V6.4A2.4 2.4 0 0 0 12.6 4H6.4A2.4 2.4 0 0 0 4 6.4v6.2A2.4 2.4 0 0 0 6.4 15H9"/>',
		'check'   => '<path d="m5 12.6 4.6 4.6L19 7"/>',
		'link'    => '<path d="M10 13a4 4 0 0 0 5.7 0l2.6-2.6a4 4 0 0 0-5.7-5.7L11.4 6"/><path d="M14 11a4 4 0 0 0-5.7 0L5.7 13.6a4 4 0 0 0 5.7 5.7L12.6 18"/>',
		'clock'   => '<circle cx="12" cy="12" r="8"/><path d="M12 8v4.4l2.8 1.8"/>',
		'eye'     => '<path d="M2.6 12S6 6.4 12 6.4 21.4 12 21.4 12 18 17.6 12 17.6 2.6 12 2.6 12Z"/><circle cx="12" cy="12" r="2.6"/>',
		'tag'     => '<path d="M12.6 3.6H20v7.4l-8.6 8.6a1.6 1.6 0 0 1-2.3 0L3.4 13.9a1.6 1.6 0 0 1 0-2.3Z"/><circle cx="16.4" cy="7.6" r="1.2"/>',
		'menu'    => '<path d="M4 8h16M4 16h11"/>',
		'list'    => '<path d="M9 6h11M9 12h11M9 18h11"/><circle cx="4.6" cy="6" r="1.2" fill="currentColor" stroke="none"/><circle cx="4.6" cy="12" r="1.2" fill="currentColor" stroke="none"/><circle cx="4.6" cy="18" r="1.2" fill="currentColor" stroke="none"/>',
		'rss'     => '<path d="M5 19h.01"/><path d="M4 11a9 9 0 0 1 9 9"/><path d="M4 5a15 15 0 0 1 15 15"/>',
		'quote'   => '<path d="M9 6C6.2 7.4 4.8 9.8 4.8 13v5h5v-5H7.4c0-2.2.7-3.8 2.3-4.8Zm10 0c-2.8 1.4-4.2 3.8-4.2 7v5h5v-5h-2.4c0-2.2.7-3.8 2.3-4.8Z"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$size = (int) $size;

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( trim( 'icon ' . $class ) ),
		$size,
		$icons[ $name ]
	);
}

/**
 * 输出图标（已在上方转义）。
 *
 * @param string $name  图标名。
 * @param int    $size  尺寸。
 * @param string $class 额外 class。
 */
function pureaura_the_icon( $name, $size = 20, $class = '' ) {
	echo pureaura_icon( $name, $size, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- 图标为内置白名单 SVG。
}

/**
 * 纯文本摘要，按字符数截断，适配中文。
 *
 * @param int $length 字符数，0 表示使用主题选项。
 * @return string
 */
function pureaura_excerpt( $length = 0 ) {
	$length = $length ? (int) $length : (int) pureaura_mod( 'pureaura_excerpt_len' );
	$post   = get_post();

	if ( ! $post ) {
		return '';
	}

	$raw = (string) $post->post_excerpt;

	if ( '' === trim( $raw ) ) {
		$raw = (string) $post->post_content;
		$raw = preg_replace( '/<!--.*?-->/s', ' ', $raw );

		if ( preg_match( '/<p[^>]*>(.*?)<\/p>/is', $raw, $match ) ) {
			$raw = $match[1];
		}
	}

	$text = strip_shortcodes( $raw );
	$text = wp_strip_all_tags( $text );
	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = trim( (string) $text );

	if ( mb_strlen( $text, 'UTF-8' ) > $length ) {
		$text = mb_substr( $text, 0, $length, 'UTF-8' ) . '…';
	}

	return $text;
}

/**
 * 预计阅读时长（分钟）：中文按 400 字/分钟，西文按 220 词/分钟折算。
 *
 * @param int|null $post_id 文章 ID。
 * @return int
 */
function pureaura_reading_time( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : ( get_the_ID() ? get_the_ID() : get_queried_object_id() );
	$content = (string) get_post_field( 'post_content', $post_id );
	$content = strip_shortcodes( $content );
	$content = wp_strip_all_tags( $content );

	$cjk   = preg_match_all( '/[\x{4e00}-\x{9fff}\x{3040}-\x{30ff}]/u', $content );
	$words = str_word_count( preg_replace( '/[^\x00-\x7F]+/u', ' ', $content ) );

	$minutes = ( $cjk / 400 ) + ( $words / 220 );
	return max( 1, (int) ceil( $minutes ) );
}

/**
 * 记录并读取浏览量。
 *
 * @param int|null $post_id 文章 ID。
 * @return int
 */
function pureaura_get_views( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : ( get_the_ID() ? get_the_ID() : get_queried_object_id() );
	return (int) get_post_meta( $post_id, 'pureaura_views', true );
}

/**
 * 单篇文章访问时累加浏览量。
 */
function pureaura_count_views() {
	if ( is_admin() || is_preview() || is_feed() || ! is_singular( 'post' ) ) {
		return;
	}

	$post_id = get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, 'pureaura_views', pureaura_get_views( $post_id ) + 1 );
}
add_action( 'wp', 'pureaura_count_views' );

/**
 * 为正文标题补锚点，供目录跳转。
 *
 * @param string $content 正文。
 * @return string
 */
function pureaura_heading_anchors( $content ) {
	if ( ! is_singular() || is_admin() || is_feed() ) {
		return $content;
	}

	$index = 0;

	return preg_replace_callback(
		'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
		function ( $matches ) use ( &$index ) {
			$index++;
			$attrs = $matches[2];

			if ( preg_match( '/\sid=("|\')([^"\']+)\1/i', $attrs ) ) {
				return $matches[0];
			}

			return '<h' . $matches[1] . $attrs . ' id="sec-' . $index . '">' . $matches[3] . '</h' . $matches[1] . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'pureaura_heading_anchors', 12 );

/**
 * 从原始正文解析目录。
 *
 * @return array
 */
function pureaura_get_toc() {
	if ( ! is_singular() ) {
		return array();
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		$post = get_post();
	}
	if ( ! $post ) {
		return array();
	}

	$content = (string) $post->post_content;
	if ( false === stripos( $content, '<h2' ) && false === stripos( $content, '<h3' ) ) {
		return array();
	}

	preg_match_all( '/<h([23])([^>]*)>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER );

	$toc   = array();
	$index = 0;

	foreach ( $matches as $match ) {
		$index++;
		$attrs = $match[2];
		$id    = 'sec-' . $index;

		if ( preg_match( '/\sid=("|\')([^"\']+)\1/i', $attrs, $id_match ) ) {
			$id = $id_match[2];
		}

		$text = trim( wp_strip_all_tags( $match[3] ) );
		if ( '' === $text ) {
			continue;
		}

		$toc[] = array(
			'level' => (int) $match[1],
			'id'    => $id,
			'text'  => $text,
		);
	}

	return $toc;
}

/**
 * 导航菜单为空时的兜底：首页 + 文章较多的分类。
 */
function pureaura_nav_fallback() {
	echo '<ul class="nav-list">';
	echo '<li class="nav-list__item"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( '首页', 'pureaura' ) . '</a></li>';

	$categories = get_categories(
		array(
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 5,
			'hide_empty' => true,
		)
	);

	foreach ( $categories as $category ) {
		printf(
			'<li class="nav-list__item"><a href="%s">%s</a></li>',
			esc_url( get_category_link( $category->term_id ) ),
			esc_html( $category->name )
		);
	}

	echo '</ul>';
}

/**
 * 紧凑日期文本：同年省略年份。
 *
 * @return string
 */
function pureaura_date_text() {
	return get_the_date( 'Y' ) === gmdate( 'Y' ) ? get_the_date( 'n月j日' ) : get_the_date( 'Y年n月j日' );
}

/**
 * 文章元信息：日期、分类、阅读时长、浏览量。
 *
 * @param array $args 显示项开关。
 */
function pureaura_post_meta( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'date'  => true,
			'cat'   => true,
			'time'  => (bool) pureaura_mod( 'pureaura_show_time' ),
			'views' => (bool) pureaura_mod( 'pureaura_show_views' ),
			'class' => '',
		)
	);

	echo '<div class="meta ' . esc_attr( $args['class'] ) . '">';

	if ( $args['date'] ) {
		printf(
			'<time class="meta__item" datetime="%s">%s</time>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( pureaura_date_text() )
		);
	}

	if ( $args['cat'] && 'post' === get_post_type() ) {
		$categories = get_the_category();
		if ( $categories ) {
			printf(
				'<a class="meta__item meta__item--link" href="%s">%s</a>',
				esc_url( get_category_link( $categories[0]->term_id ) ),
				esc_html( $categories[0]->name )
			);
		}
	}

	if ( $args['time'] ) {
		printf(
			'<span class="meta__item">%s%s</span>',
			pureaura_icon( 'clock', 15 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( sprintf( __( '约 %d 分钟', 'pureaura' ), pureaura_reading_time() ) )
		);
	}

	if ( $args['views'] ) {
		$views = pureaura_get_views();
		if ( $views > 0 ) {
			printf(
				'<span class="meta__item">%s%s</span>',
				pureaura_icon( 'eye', 15 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html( sprintf( __( '%s 次阅读', 'pureaura' ), number_format_i18n( $views ) ) )
			);
		}
	}

	echo '</div>';
}

/**
 * 面包屑。仅文章与页面使用。
 */
function pureaura_breadcrumb() {
	if ( is_front_page() ) {
		return;
	}

	echo '<nav class="crumb" aria-label="' . esc_attr__( '当前位置', 'pureaura' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( '首页', 'pureaura' ) . '</a>';

	if ( is_singular( 'post' ) ) {
		$categories = get_the_category();
		if ( $categories ) {
			echo '<span class="crumb__sep" aria-hidden="true">/</span>';
			printf(
				'<a href="%s">%s</a>',
				esc_url( get_category_link( $categories[0]->term_id ) ),
				esc_html( $categories[0]->name )
			);
		}
	} elseif ( is_page() ) {
		$parent = wp_get_post_parent_id( get_the_ID() );
		if ( $parent ) {
			echo '<span class="crumb__sep" aria-hidden="true">/</span>';
			printf(
				'<a href="%s">%s</a>',
				esc_url( get_permalink( $parent ) ),
				esc_html( get_the_title( $parent ) )
			);
		}
	} elseif ( is_search() ) {
		echo '<span class="crumb__sep" aria-hidden="true">/</span>';
		echo '<span>' . esc_html__( '搜索', 'pureaura' ) . '</span>';
	} elseif ( is_archive() ) {
		echo '<span class="crumb__sep" aria-hidden="true">/</span>';
		echo '<span>' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</span>';
	}

	echo '<span class="crumb__sep" aria-hidden="true">/</span>';
	$crumb_title = wp_strip_all_tags( get_the_title() );
	if ( mb_strlen( $crumb_title, 'UTF-8' ) > 18 ) {
		$crumb_title = mb_substr( $crumb_title, 0, 18, 'UTF-8' ) . '…';
	}
	echo '<span class="crumb__here" aria-current="page">' . esc_html( $crumb_title ) . '</span>';
	echo '</nav>';
}

/**
 * 分页。
 */
function pureaura_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => pureaura_icon( 'left', 18 ) . '<span>' . esc_html__( '上一页', 'pureaura' ) . '</span>',
			'next_text'          => '<span>' . esc_html__( '下一页', 'pureaura' ) . '</span>' . pureaura_icon( 'right', 18 ),
			'screen_reader_text' => __( '文章分页', 'pureaura' ),
			'class'              => 'pager',
		)
	);
}

/**
 * 相关文章：同分类优先，数量不足时用最新文章补齐。
 *
 * @param int $count 数量。
 * @return WP_Query
 */
function pureaura_related_query( $count = 3 ) {
	$post_id    = get_the_ID();
	$categories = wp_get_post_categories( $post_id );

	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => (int) $count,
		'post__not_in'        => array( $post_id ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	if ( $categories ) {
		$args['category__in'] = $categories;
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() && $categories ) {
		unset( $args['category__in'] );
		$query = new WP_Query( $args );
	}

	return $query;
}

/**
 * 社交链接配置。
 *
 * @return array
 */
function pureaura_social_links() {
	$links = array();

	$map = array(
		'weibo'  => array( 'label' => __( '微博', 'pureaura' ), 'url' => pureaura_mod( 'pureaura_social_weibo' ) ),
		'github' => array( 'label' => 'GitHub', 'url' => pureaura_mod( 'pureaura_social_github' ) ),
		'x'      => array( 'label' => 'X', 'url' => pureaura_mod( 'pureaura_social_x' ) ),
	);

	foreach ( $map as $key => $item ) {
		if ( $item['url'] ) {
			$links[ $key ] = array(
				'label' => $item['label'],
				'url'   => $item['url'],
			);
		}
	}

	$email = pureaura_mod( 'pureaura_social_email' );
	if ( $email ) {
		$links['email'] = array(
			'label' => __( '邮箱', 'pureaura' ),
			'url'   => 'mailto:' . $email,
		);
	}

	return $links;
}

/**
 * 站点标识：有自定义 Logo 用 Logo，否则用文字标识。
 */
function pureaura_brand() {	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	?>
	<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<span class="brand__mark" aria-hidden="true">
			<svg viewBox="0 0 32 32" width="26" height="26" fill="none">
				<circle cx="16" cy="16" r="12.5" stroke="currentColor" stroke-width="1" opacity=".35"/>
				<circle cx="16" cy="16" r="8" stroke="currentColor" stroke-width="1" opacity=".6"/>
				<circle cx="16" cy="16" r="3.2" fill="currentColor" opacity=".92"/>
				<path d="M16 1.6v4.2" stroke="currentColor" stroke-width="1" opacity=".8"/>
			</svg>
		</span>
		<span class="brand__text">
			<span class="brand__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
			<?php
			$tagline = get_bloginfo( 'description', 'display' );
			if ( $tagline ) :
				?>
				<span class="brand__tag"><?php echo esc_html( $tagline ); ?></span>
			<?php endif; ?>
		</span>
	</a>
	<?php
}
