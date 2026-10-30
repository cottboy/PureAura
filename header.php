<?php
/**
 * 页头：背景光场、导航条、移动顶栏、搜索层。
 *
 * @package PureAura
 */

$pureaura_toc = ( pureaura_mod( 'pureaura_show_toc' ) && is_singular() ) ? pureaura_get_toc() : array();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main"><?php esc_html_e( '跳到正文', 'pureaura' ); ?></a>

<div class="sky" aria-hidden="true">
	<span class="sky__field sky__field--1"></span>
	<span class="sky__field sky__field--2"></span>
	<span class="sky__field sky__field--3"></span>
	<span class="sky__field sky__field--4"></span>
	<svg class="sky__rings" viewBox="0 0 720 720" fill="none" aria-hidden="true">
		<g stroke="currentColor" stroke-width="1">
			<circle cx="220" cy="200" r="132"/>
			<circle cx="220" cy="200" r="214"/>
			<circle cx="220" cy="200" r="306"/>
			<circle cx="220" cy="200" r="410"/>
		</g>
	</svg>
</div>

<?php if ( pureaura_mod( 'pureaura_show_progress' ) && is_singular() ) : ?>
	<div class="progress" aria-hidden="true"><span class="progress__bar" data-progress></span></div>
<?php endif; ?>

<div class="shell">
	<?php get_template_part( 'template-parts/rail', null, array( 'toc' => $pureaura_toc ) ); ?>

	<div class="canvas">
		<header class="topbar">
			<div class="topbar__brand"><?php pureaura_brand(); ?></div>
			<div class="topbar__actions">
				<button class="icon-btn" type="button" data-open="search" aria-label="<?php esc_attr_e( '搜索', 'pureaura' ); ?>">
					<?php pureaura_the_icon( 'search', 20 ); ?>
				</button>
				<button class="icon-btn" type="button" data-open="menu" aria-label="<?php esc_attr_e( '菜单', 'pureaura' ); ?>">
					<?php pureaura_the_icon( 'menu', 20 ); ?>
				</button>
			</div>
		</header>

		<main class="main" id="main" tabindex="-1">
