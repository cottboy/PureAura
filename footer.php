<?php
/**
 * 页脚：站点信息、导航、悬浮底栏与浮层。
 *
 * @package PureAura
 */

$pureaura_socials = pureaura_social_links();
$pureaura_desc    = pureaura_mod( 'pureaura_footer_text' );
if ( '' === trim( (string) $pureaura_desc ) ) {
	$pureaura_desc = get_bloginfo( 'description', 'display' );
}
?>
		</main>

		<footer class="foot">
			<div class="foot__grid">
				<div class="foot__col foot__col--brand">
					<p class="foot__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
					<?php if ( $pureaura_desc ) : ?>
						<p class="foot__desc"><?php echo wp_kses_post( $pureaura_desc ); ?></p>
					<?php endif; ?>
				</div>

				<div class="foot__col">
					<p class="foot__label"><?php esc_html_e( '浏览', 'pureaura' ); ?></p>
					<?php
					if ( has_nav_menu( 'footer' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => false,
								'menu_class'     => 'foot__list',
								'depth'          => 1,
							)
						);
					} else {
						$pureaura_cats = get_categories(
							array(
								'orderby'    => 'count',
								'order'      => 'DESC',
								'number'     => 4,
								'hide_empty' => true,
							)
						);
						echo '<ul class="foot__list">';
						echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( '首页', 'pureaura' ) . '</a></li>';
						foreach ( $pureaura_cats as $pureaura_cat ) {
							printf(
								'<li><a href="%s">%s</a></li>',
								esc_url( get_category_link( $pureaura_cat->term_id ) ),
								esc_html( $pureaura_cat->name )
							);
						}
						echo '</ul>';
					}
					?>
				</div>

				<div class="foot__col">
					<p class="foot__label"><?php esc_html_e( '联系', 'pureaura' ); ?></p>
					<ul class="foot__list">
						<?php foreach ( $pureaura_socials as $pureaura_key => $pureaura_link ) : ?>
							<li>
								<a href="<?php echo esc_url( $pureaura_link['url'] ); ?>"<?php echo 'email' === $pureaura_key ? '' : ' target="_blank" rel="noopener noreferrer"'; ?>>
									<?php echo esc_html( $pureaura_link['label'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
						<li><a href="<?php echo esc_url( get_feed_link() ); ?>">RSS</a></li>
						<li><a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"><?php esc_html_e( '搜索', 'pureaura' ); ?></a></li>
					</ul>
				</div>
			</div>

			<div class="foot__base">
				<p class="foot__copy">
					<?php
					printf(
						/* translators: 1: 年份，2: 站点名 */
						esc_html__( '© %1$s %2$s', 'pureaura' ),
						esc_html( gmdate( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
					?>
				</p>

				<?php if ( pureaura_mod( 'pureaura_icp' ) ) : ?>
					<a class="foot__legal" href="https://beian.miit.gov.cn/" target="_blank" rel="noopener noreferrer nofollow">
						<?php echo esc_html( pureaura_mod( 'pureaura_icp' ) ); ?>
					</a>
				<?php endif; ?>

				<?php if ( pureaura_mod( 'pureaura_police' ) ) : ?>
					<a class="foot__legal" href="https://beian.mps.gov.cn/" target="_blank" rel="noopener noreferrer nofollow">
						<?php echo esc_html( pureaura_mod( 'pureaura_police' ) ); ?>
					</a>
				<?php endif; ?>

				<p class="foot__made"><?php esc_html_e( '由 WordPress 驱动', 'pureaura' ); ?></p>
			</div>
		</footer>
	</div>
</div>

<nav class="dock" aria-label="<?php esc_attr_e( '快捷导航', 'pureaura' ); ?>">
	<a class="dock__item<?php echo is_front_page() ? ' is-current' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php pureaura_the_icon( 'home', 21 ); ?>
		<span><?php esc_html_e( '首页', 'pureaura' ); ?></span>
	</a>
	<button class="dock__item" type="button" data-open="menu">
		<?php pureaura_the_icon( 'grid', 21 ); ?>
		<span><?php esc_html_e( '分类', 'pureaura' ); ?></span>
	</button>
	<button class="dock__item" type="button" data-open="search">
		<?php pureaura_the_icon( 'search', 21 ); ?>
		<span><?php esc_html_e( '搜索', 'pureaura' ); ?></span>
	</button>
	<button class="dock__item" type="button" data-theme-toggle aria-label="<?php esc_attr_e( '切换深浅色', 'pureaura' ); ?>">
		<span data-theme-icon aria-hidden="true"><?php pureaura_the_icon( 'moon', 21 ); ?></span>
		<span data-theme-label><?php esc_html_e( '深色', 'pureaura' ); ?></span>
	</button>
</nav>

<button class="to-top" type="button" data-to-top aria-label="<?php esc_attr_e( '回到顶部', 'pureaura' ); ?>">
	<?php pureaura_the_icon( 'up', 18 ); ?>
</button>

<dialog class="sheet sheet--search" id="sheet-search" aria-label="<?php esc_attr_e( '搜索站点', 'pureaura' ); ?>">
	<div class="sheet__inner">
		<div class="sheet__top">
			<span class="sheet__hint"><?php esc_html_e( '全站搜索', 'pureaura' ); ?></span>
			<button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( '关闭搜索', 'pureaura' ); ?>">
				<?php pureaura_the_icon( 'close', 20 ); ?>
			</button>
		</div>

		<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="sheet-search-input"><?php esc_html_e( '搜索关键词', 'pureaura' ); ?></label>
			<input class="search-form__input" id="sheet-search-input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( '输入关键词，回车搜索', 'pureaura' ); ?>" autocomplete="off">
			<button class="btn btn--accent" type="submit"><?php esc_html_e( '搜索', 'pureaura' ); ?></button>
		</form>

		<div class="sheet__hint">
			<?php esc_html_e( '按 Esc 关闭', 'pureaura' ); ?>
		</div>

		<?php
		$pureaura_cats = get_categories(
			array(
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => 8,
				'hide_empty' => true,
			)
		);
		if ( $pureaura_cats ) :
			?>
			<p class="sheet__label sheet__label--sub"><?php esc_html_e( '按分类浏览', 'pureaura' ); ?></p>
			<ul class="chips">
				<?php foreach ( $pureaura_cats as $pureaura_cat ) : ?>
					<li>
						<a class="chip" href="<?php echo esc_url( get_category_link( $pureaura_cat->term_id ) ); ?>">
							<?php echo esc_html( $pureaura_cat->name ); ?>
							<span class="chip__num"><?php echo esc_html( number_format_i18n( $pureaura_cat->count ) ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<?php
		$pureaura_recent = new WP_Query(
			array(
				'posts_per_page'      => 3,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);

		if ( $pureaura_recent->have_posts() ) :
			?>
			<p class="sheet__label sheet__label--sub"><?php esc_html_e( '最近发布', 'pureaura' ); ?></p>
			<ul class="sheet__recent">
				<?php
				while ( $pureaura_recent->have_posts() ) :
					$pureaura_recent->the_post();
					?>
					<li>
						<a href="<?php the_permalink(); ?>">
							<span class="sheet__recent-date"><?php echo esc_html( get_the_date( 'm.d' ) ); ?></span>
							<span class="sheet__recent-title"><?php the_title(); ?></span>
						</a>
					</li>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</ul>
		<?php endif; ?>
	</div>
</dialog>

<dialog class="sheet sheet--menu" id="sheet-menu" aria-label="<?php esc_attr_e( '站点菜单', 'pureaura' ); ?>">
	<div class="sheet__inner">
		<div class="sheet__top">
			<?php pureaura_brand(); ?>
			<button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( '关闭菜单', 'pureaura' ); ?>">
				<?php pureaura_the_icon( 'close', 20 ); ?>
			</button>
		</div>

		<nav class="sheet__nav" aria-label="<?php esc_attr_e( '主导航', 'pureaura' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-list nav-list--sheet',
					'depth'          => 1,
					'fallback_cb'    => 'pureaura_nav_fallback',
				)
			);
			?>
		</nav>

		<div class="sheet__appearance">
			<span class="rail__label"><?php esc_html_e( '外观', 'pureaura' ); ?></span>
			<div class="seg seg--wide" data-seg role="group" aria-label="<?php esc_attr_e( '外观模式', 'pureaura' ); ?>">
				<span class="seg__thumb" aria-hidden="true"></span>
				<button class="seg__btn" type="button" aria-pressed="false" data-theme-set="light">
					<?php pureaura_the_icon( 'sun', 16 ); ?><span><?php esc_html_e( '浅色', 'pureaura' ); ?></span>
				</button>
				<button class="seg__btn" type="button" aria-pressed="false" data-theme-set="auto">
					<?php pureaura_the_icon( 'auto', 16 ); ?><span><?php esc_html_e( '系统', 'pureaura' ); ?></span>
				</button>
				<button class="seg__btn" type="button" aria-pressed="false" data-theme-set="dark">
					<?php pureaura_the_icon( 'moon', 16 ); ?><span><?php esc_html_e( '深色', 'pureaura' ); ?></span>
				</button>
			</div>
		</div>
	</div>
</dialog>

<?php wp_footer(); ?>
</body>
</html>
