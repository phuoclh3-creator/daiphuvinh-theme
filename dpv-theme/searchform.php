<?php
/**
 * Form tìm kiếm (tìm cả sản phẩm nếu có WooCommerce).
 *
 * @package dpv-theme
 */

$dpv_sid = 's-' . wp_unique_id();
?>
<form role="search" method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $dpv_sid ); ?>"><?php esc_html_e( 'Tìm kiếm', 'dpv-theme' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $dpv_sid ); ?>" class="searchform__input" name="s" placeholder="<?php esc_attr_e( 'Tìm kiếm', 'dpv-theme' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
	<?php if ( class_exists( 'WooCommerce' ) ) : ?>
		<input type="hidden" name="post_type" value="product">
	<?php endif; ?>
	<button type="submit" class="searchform__btn" aria-label="<?php esc_attr_e( 'Tìm', 'dpv-theme' ); ?>">
		<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5" fill="none" stroke="currentColor" stroke-width="2.2"/><path d="m15.5 15.5 5 5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
	</button>
</form>
