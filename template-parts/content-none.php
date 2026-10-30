<?php
/**
 * 空状态：无文章、无搜索结果。
 *
 * @package PureAura
 */

$pureaura_title   = isset( $args['title'] ) ? $args['title'] : __( '这里还没有内容', 'pureaura' );
$pureaura_text    = isset( $args['text'] ) ? $args['text'] : __( '换一个关键词，或者从下面的文章开始。', 'pureaura' );
$pureaura_heading = isset( $args['heading'] ) && 'h1' === $args['heading'] ? 'h1' : 'h2';
?>
<section class="empty">
	<<?php echo esc_attr( $pureaura_heading ); ?> class="empty__title"><?php echo esc_html( $pureaura_title ); ?></<?php echo esc_attr( $pureaura_heading ); ?>>
	<p class="empty__text"><?php echo esc_html( $pureaura_text ); ?></p>

	<?php get_search_form(); ?>

	<?php
	$pureaura_latest = new WP_Query(
		array(
			'posts_per_page'      => 3,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( $pureaura_latest->have_posts() ) :
		?>
		<p class="empty__label"><?php esc_html_e( '最近发布', 'pureaura' ); ?></p>
		<div class="stream stream--flat">
			<?php
			while ( $pureaura_latest->have_posts() ) :
				$pureaura_latest->the_post();
				get_template_part( 'template-parts/post-row' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	<?php endif; ?>
</section>
