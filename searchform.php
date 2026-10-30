<?php
/**
 * 站内搜索表单。
 *
 * @package PureAura
 */

$pureaura_id = 'search-' . wp_unique_id();
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $pureaura_id ); ?>"><?php esc_html_e( '搜索关键词', 'pureaura' ); ?></label>
	<input
		class="search-form__input"
		id="<?php echo esc_attr( $pureaura_id ); ?>"
		type="search"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php esc_attr_e( '搜索文章…', 'pureaura' ); ?>"
		autocomplete="off"
	>
	<button class="btn btn--accent search-form__submit" type="submit">
		<?php pureaura_the_icon( 'search', 17 ); ?>
		<span><?php esc_html_e( '搜索', 'pureaura' ); ?></span>
	</button>
</form>
