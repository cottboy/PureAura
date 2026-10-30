<?php
/**
 * 左侧导航条：站点标识、导航、目录、工具。
 *
 * @package PureAura
 */

$pureaura_toc = isset( $args['toc'] ) ? $args['toc'] : array();
?>
<aside class="rail" aria-label="<?php esc_attr_e( '站点导航与控制', 'pureaura' ); ?>">
	<div class="rail__head">
		<?php pureaura_brand(); ?>
	</div>

	<nav class="rail__nav" aria-label="<?php esc_attr_e( '主导航', 'pureaura' ); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'nav-list',
				'depth'          => 1,
				'fallback_cb'    => 'pureaura_nav_fallback',
			)
		);
		?>
	</nav>

	<button class="rail__search" type="button" data-open="search">
		<?php pureaura_the_icon( 'search', 17 ); ?>
		<span><?php esc_html_e( '搜索', 'pureaura' ); ?></span>
		<kbd class="rail__kbd">/</kbd>
	</button>

	<?php if ( ! empty( $pureaura_toc ) ) : ?>
		<div class="rail__toc" data-toc>
			<p class="rail__label"><?php esc_html_e( '目录', 'pureaura' ); ?></p>
			<nav aria-label="<?php esc_attr_e( '文章目录', 'pureaura' ); ?>">
				<ol class="toc">
					<?php foreach ( $pureaura_toc as $pureaura_item ) : ?>
						<li class="toc__item toc__item--h<?php echo (int) $pureaura_item['level']; ?>">
							<a class="toc__link" href="#<?php echo esc_attr( $pureaura_item['id'] ); ?>" data-toc-link="<?php echo esc_attr( $pureaura_item['id'] ); ?>">
								<?php echo esc_html( $pureaura_item['text'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</nav>
		</div>
	<?php endif; ?>

	<div class="rail__foot">
		<div class="rail__appearance">
			<span class="rail__label"><?php esc_html_e( '外观', 'pureaura' ); ?></span>
			<div class="seg" data-seg role="group" aria-label="<?php esc_attr_e( '外观模式', 'pureaura' ); ?>">
				<span class="seg__thumb" aria-hidden="true"></span>
				<button class="seg__btn" type="button" aria-pressed="false" data-theme-set="light" aria-label="<?php esc_attr_e( '浅色', 'pureaura' ); ?>" title="<?php esc_attr_e( '浅色', 'pureaura' ); ?>">
					<?php pureaura_the_icon( 'sun', 16 ); ?>
				</button>
				<button class="seg__btn" type="button" aria-pressed="false" data-theme-set="auto" aria-label="<?php esc_attr_e( '跟随系统', 'pureaura' ); ?>" title="<?php esc_attr_e( '跟随系统', 'pureaura' ); ?>">
					<?php pureaura_the_icon( 'auto', 16 ); ?>
				</button>
				<button class="seg__btn" type="button" aria-pressed="false" data-theme-set="dark" aria-label="<?php esc_attr_e( '深色', 'pureaura' ); ?>" title="<?php esc_attr_e( '深色', 'pureaura' ); ?>">
					<?php pureaura_the_icon( 'moon', 16 ); ?>
				</button>
			</div>
		</div>

		<div class="rail__meta">
			<a href="<?php echo esc_url( get_feed_link() ); ?>" class="rail__link">RSS</a>
			<span class="rail__dot" aria-hidden="true"></span>
			<span class="rail__year"><?php echo esc_html( gmdate( 'Y' ) ); ?></span>
		</div>
	</div>
</aside>
