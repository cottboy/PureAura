<?php
/**
 * 独立页面。
 *
 * @package PureAura
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'post post--page' ); ?>>
		<header class="post__head">
			<?php pureaura_breadcrumb(); ?>
			<h1 class="post__title"><?php the_title(); ?></h1>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="post__figure">
				<?php the_post_thumbnail( 'pureaura-cover', array( 'class' => 'post__img' ) ); ?>
			</figure>
		<?php endif; ?>

		<div class="prose" data-prose>
			<?php
			the_content();
			wp_link_pages(
				array(
					'before' => '<nav class="page-links">',
					'after'  => '</nav>',
				)
			);
			?>
		</div>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</article>
	<?php
endwhile;

get_footer();
