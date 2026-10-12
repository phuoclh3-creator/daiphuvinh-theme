<?php
/**
 * Tích hợp WooCommerce (chỉ nạp khi WooCommerce hoạt động).
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Khung bao quanh nội dung Woo.
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

add_action( 'woocommerce_before_main_content', function () {
	echo '<div class="container page-wrap"><main id="content" class="site-main woo-main">';
	dpv_breadcrumb();
}, 10 );
add_action( 'woocommerce_after_main_content', function () {
	echo '</main></div>';
}, 10 );

// Số sản phẩm mỗi hàng / mỗi trang.
add_filter( 'loop_shop_columns', function () {
	return 4;
} );
add_filter( 'loop_shop_per_page', function () {
	return 16;
} );

// Dùng H2 cho tên sản phẩm trong danh sách (H1 chỉ dành cho tiêu đề trang).
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
add_action( 'woocommerce_shop_loop_item_title', function () {
	echo '<h2 class="woocommerce-loop-product__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2>';
}, 10 );

// Sản phẩm không có giá (báo giá theo liên hệ): hiện "Liên hệ".
add_filter( 'woocommerce_get_price_html', function ( $price, $product ) {
	if ( '' === $product->get_price() ) {
		return '<span class="price price--contact">' . esc_html__( 'Liên hệ', 'dpv-theme' ) . '</span>';
	}
	return $price;
}, 10, 2 );

// Bỏ breadcrumb mặc định của Woo vì đã có breadcrumb của theme.
add_filter( 'woocommerce_breadcrumb_defaults', '__return_empty_array' );

// Giảm tải: bỏ CSS/JS Woo ở trang không liên quan.
add_action( 'wp_enqueue_scripts', function () {
	if ( function_exists( 'is_woocommerce' ) && ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() && ! is_front_page() ) {
		wp_dequeue_style( 'woocommerce-general' );
		wp_dequeue_style( 'woocommerce-layout' );
		wp_dequeue_style( 'woocommerce-smallscreen' );
		wp_dequeue_style( 'wc-blocks-style' );
		wp_dequeue_script( 'wc-add-to-cart' );
		wp_dequeue_script( 'woocommerce' );
		wp_dequeue_script( 'wc-cart-fragments' );
	}
}, 99 );

/**
 * Số sản phẩm trong giỏ (cho icon giỏ hàng ở header).
 */
function dpv_cart_count() {
	return ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
}
