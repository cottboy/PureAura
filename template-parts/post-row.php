<?php
/**
 * 列表中的一行文章。
 *
 * @package PureAura
 */

$pureaura_thumb  = ( pureaura_mod( 'pureaura_show_thumb' ) && has_post_thumbnail() );
$pureaura_minute = pureaura_reading_time();
$pureaura_class  = 'row' . ( $pureaura_thumb ? ' row--thumb' : '' );
?>
<article <?php post_class( $pureaura_class ); ?>>
	<div class="row__side">
		<time class="row__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
			<?php echo esc_html( get_the_date( 'm.d' ) ); ?>
		</time>
		<?php if ( pureaura_mod( 'pureaura_show_time' ) ) : ?>
			<span class="row__minute"><?php echo esc_html( sprintf( __( '%d 分钟', 'pureaura' ), $pureaura_minute ) ); ?></span>
		<?php endif; ?>
	</div>

	<div class="row__body">
		<h2 class="row__title">
			<a class="row__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<?php
		$pureaura_dek = pureaura_excerpt();
		if ( $pureaura_dek ) :
			?>
			<p class="row__excerpt"><?php echo esc_html( $pureaura_dek ); ?></p>
		<?php endif; ?>
	</div>

	<?php
	$pureaura_cats = get_the_category();
	if ( $pureaura_cats ) :
		?>
		<a class="row__cat" href="<?php echo esc_url( get_category_link( $pureaura_cats[0]->term_id ) ); ?>"><?php echo esc_html( $pureaura_cats[0]->name ); ?></a>
	<?php endif; ?>

	<?php if ( $pureaura_thumb ) : ?>
		<a class="row__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			the_post_thumbnail(
				'pureaura-thumb',
				array(
					'loading'  => 'lazy',
					'decoding' => 'async',
					'alt'      => '',
				)
			);
			?>
		</a>
	<?php endif; ?>
</article>
