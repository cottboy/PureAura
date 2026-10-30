<?php
/**
 * 404 页面。
 *
 * @package PureAura
 */

get_header();
?>

<section class="lost">
	<p class="lost__num" aria-hidden="true">404</p>
	<h1 class="lost__title"><?php esc_html_e( '这一页找不到了', 'pureaura' ); ?></h1>
	<p class="lost__text"><?php esc_html_e( '链接可能已经失效，或者地址被改动过。用搜索找找看，或者从最近的文章开始。', 'pureaura' ); ?></p>

	<?php get_search_form(); ?>

	<?php
	$pureaura_latest = new WP_Query(
		array(
			'posts_per_page'      => 4,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( $pureaura_latest->have_posts() ) :
		?>
		<h2 class="lost__label"><?php esc_html_e( '最近发布', 'pureaura' ); ?></h2>
		<div class="stream">
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

<?php
get_footer();
