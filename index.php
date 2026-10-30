<?php
/**
 * 首页与归档列表。
 *
 * @package PureAura
 */

get_header();

$pureaura_is_home = is_home() || is_front_page();
$pureaura_page_one = ! is_paged();
$pureaura_index    = 0;
?>

<?php if ( have_posts() ) : ?>

	<?php if ( $pureaura_is_home && $pureaura_page_one ) : ?>
		<div class="lead-wrap">
	<?php endif; ?>

	<?php
	while ( have_posts() ) :
		the_post();
		$pureaura_index++;

		if ( $pureaura_is_home && $pureaura_page_one && 1 === $pureaura_index ) :
			get_template_part( 'template-parts/lead' );
			?>
			</div><!-- /.lead-wrap -->
			<div class="section-head">
				<h2 class="section-head__title"><?php esc_html_e( '最近文章', 'pureaura' ); ?></h2>
				<a class="section-head__link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php esc_html_e( '全部', 'pureaura' ); ?><?php pureaura_the_icon( 'right', 15 ); ?>
				</a>
			</div>
			<div class="stream">
		<?php else : ?>
			<?php get_template_part( 'template-parts/post-row' ); ?>
		<?php endif; ?>
	<?php endwhile; ?>

	<?php if ( $pureaura_is_home && $pureaura_page_one ) : ?>
		</div><!-- /.stream -->
	<?php endif; ?>

	<?php pureaura_pagination(); ?>

<?php else : ?>
	<?php get_template_part( 'template-parts/content-none' ); ?>
<?php endif; ?>

<?php
get_footer();
