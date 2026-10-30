<?php
/**
 * 单篇文章。
 *
 * @package PureAura
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class( 'post' ); ?>>
		<header class="post__head">
			<?php pureaura_breadcrumb(); ?>

			<h1 class="post__title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p class="post__dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<?php pureaura_post_meta( array( 'class' => 'post__meta', 'cat' => false ) ); ?>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="post__figure">
				<?php
				the_post_thumbnail(
					'pureaura-cover',
					array(
						'class'         => 'post__img',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'decoding'      => 'async',
					)
				);
				?>
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

		<footer class="post__foot">
			<?php
			$pureaura_tags = get_the_tags();
			if ( $pureaura_tags ) :
				?>
				<ul class="chips chips--tags">
					<?php foreach ( $pureaura_tags as $pureaura_tag ) : ?>
						<li>
							<a class="chip chip--tag" href="<?php echo esc_url( get_tag_link( $pureaura_tag->term_id ) ); ?>" rel="tag">
								<?php echo esc_html( $pureaura_tag->name ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( pureaura_mod( 'pureaura_show_share' ) ) : ?>
				<div class="share">
					<span class="share__label"><?php esc_html_e( '分享这篇文章', 'pureaura' ); ?></span>
					<button class="chip chip--btn" type="button" data-copy-link data-copied="<?php esc_attr_e( '链接已复制', 'pureaura' ); ?>">
						<?php pureaura_the_icon( 'link', 16 ); ?>
						<span data-copy-label><?php esc_html_e( '复制链接', 'pureaura' ); ?></span>
					</button>
					<button class="chip chip--btn" type="button" data-native-share hidden>
						<?php pureaura_the_icon( 'right', 16 ); ?>
						<span><?php esc_html_e( '分享…', 'pureaura' ); ?></span>
					</button>
				</div>
			<?php endif; ?>

			<?php if ( pureaura_mod( 'pureaura_show_author' ) ) : ?>
				<section class="author">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 64, '', get_the_author(), array( 'class' => 'author__avatar' ) ); ?>
					<div class="author__body">
						<p class="author__name"><?php the_author(); ?></p>
						<?php if ( get_the_author_meta( 'description' ) ) : ?>
							<p class="author__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
						<?php endif; ?>
						<a class="author__link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>">
							<?php esc_html_e( '查看全部文章', 'pureaura' ); ?><?php pureaura_the_icon( 'right', 15 ); ?>
						</a>
					</div>
				</section>
			<?php endif; ?>

			<?php
			$pureaura_prev = get_previous_post();
			$pureaura_next = get_next_post();
			if ( $pureaura_prev || $pureaura_next ) :
				?>
				<nav class="pn" aria-label="<?php esc_attr_e( '上一篇与下一篇', 'pureaura' ); ?>">
					<?php if ( $pureaura_prev ) : ?>
						<a class="pn__item pn__item--prev" href="<?php echo esc_url( get_permalink( $pureaura_prev ) ); ?>">
							<span class="pn__label"><?php pureaura_the_icon( 'left', 15 ); ?><?php esc_html_e( '上一篇', 'pureaura' ); ?></span>
							<span class="pn__title"><?php echo esc_html( get_the_title( $pureaura_prev ) ); ?></span>
						</a>
					<?php endif; ?>

					<?php if ( $pureaura_next ) : ?>
						<a class="pn__item pn__item--next" href="<?php echo esc_url( get_permalink( $pureaura_next ) ); ?>">
							<span class="pn__label"><?php esc_html_e( '下一篇', 'pureaura' ); ?><?php pureaura_the_icon( 'right', 15 ); ?></span>
							<span class="pn__title"><?php echo esc_html( get_the_title( $pureaura_next ) ); ?></span>
						</a>
					<?php endif; ?>
				</nav>
			<?php endif; ?>

			<?php if ( pureaura_mod( 'pureaura_show_related' ) ) : ?>
				<?php
				$pureaura_related = pureaura_related_query( 3 );
				if ( $pureaura_related->have_posts() ) :
					?>
					<section class="related">
						<h2 class="related__title"><?php esc_html_e( '继续阅读', 'pureaura' ); ?></h2>
						<div class="stream stream--flat">
							<?php
							while ( $pureaura_related->have_posts() ) :
								$pureaura_related->the_post();
								get_template_part( 'template-parts/post-row' );
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</section>
				<?php endif; ?>
			<?php endif; ?>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</footer>
	</article>

	<?php
endwhile;

get_footer();
