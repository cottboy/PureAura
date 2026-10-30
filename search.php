<?php
/**
 * 搜索结果。
 *
 * @package PureAura
 */

get_header();

$pureaura_query = get_search_query();
?>

<header class="page-head">
	<?php pureaura_breadcrumb(); ?>
	<h1 class="page-head__title">
		<?php
		if ( $pureaura_query ) {
			printf(
				/* translators: %s: 搜索关键词 */
				esc_html__( '搜索“%s”', 'pureaura' ),
				esc_html( $pureaura_query )
			);
		} else {
			esc_html_e( '搜索', 'pureaura' );
		}
		?>
	</h1>

	<p class="page-head__count">
		<?php
		printf(
			/* translators: %s: 结果数量 */
			esc_html__( '找到 %s 条结果', 'pureaura' ),
			esc_html( number_format_i18n( (int) $GLOBALS['wp_query']->found_posts ) )
		);
		?>
	</p>

	<?php get_search_form(); ?>
</header>

<hr class="rule">

<?php if ( have_posts() ) : ?>
	<div class="stream">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/post-row' );
		endwhile;
		?>
	</div>
	<?php pureaura_pagination(); ?>
<?php else : ?>
	<?php
	get_template_part(
		'template-parts/content-none',
		null,
		array(
			'title' => __( '没有找到相关内容', 'pureaura' ),
			'text'  => __( '试着换一个更短的词，或者从最近的文章开始读。', 'pureaura' ),
		)
	);
	?>
<?php endif; ?>

<?php
get_footer();
