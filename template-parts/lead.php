<?php
/**
 * 首页头条：整站最新（或置顶）文章。
 *
 * @package PureAura
 */

$pureaura_has_thumb = has_post_thumbnail();
?>
<article <?php post_class( 'lead' . ( $pureaura_has_thumb ? ' lead--media' : '' ) ); ?>>
	<div class="lead__text">
		<h1 class="lead__title">
			<a class="lead__link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h1>

		<?php
		$pureaura_dek = pureaura_excerpt( 96 );
		if ( $pureaura_dek ) :
			?>
			<p class="lead__dek"><?php echo esc_html( $pureaura_dek ); ?></p>
		<?php endif; ?>

		<?php pureaura_post_meta( array( 'class' => 'lead__meta' ) ); ?>
	</div>

	<?php if ( $pureaura_has_thumb ) : ?>
		<a class="lead__figure" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			the_post_thumbnail(
				'pureaura-wide',
				array(
					'class'         => 'lead__img',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
				)
			);
			?>
		</a>
	<?php endif; ?>
</article>

<hr class="rule rule--lead">
