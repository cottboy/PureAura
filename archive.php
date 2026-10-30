<?php
/**
 * 分类、标签、作者、日期归档。
 *
 * @package PureAura
 */

get_header();
?>

<header class="page-head">
	<?php pureaura_breadcrumb(); ?>
	<h1 class="page-head__title"><?php the_archive_title(); ?></h1>
	<?php the_archive_description( '<div class="page-head__desc">', '</div>' ); ?>
	<p class="page-head__count">
		<?php
		printf(
			/* translators: %s: 文章数量 */
			esc_html__( '共 %s 篇', 'pureaura' ),
			esc_html( number_format_i18n( (int) $GLOBALS['wp_query']->found_posts ) )
		);
		?>
	</p>
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
			'title' => __( '这个分类还是空的', 'pureaura' ),
			'text'  => __( '等第一篇文章发布后，这里就会有内容了。', 'pureaura' ),
		)
	);
	?>
<?php endif; ?>

<?php
get_footer();
