<?php
/**
 * Đại Phú Vinh – theme cha (nhẹ, chuẩn SEO).
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DPV_VERSION', '1.0.0' );
define( 'DPV_DIR', get_template_directory() );
define( 'DPV_URI', get_template_directory_uri() );

require_once DPV_DIR . '/inc/helpers.php';
require_once DPV_DIR . '/inc/customizer.php';
require_once DPV_DIR . '/inc/seo.php';
require_once DPV_DIR . '/inc/schema.php';
require_once DPV_DIR . '/inc/template-tags.php';
require_once DPV_DIR . '/inc/home-blocks.php';
if ( class_exists( 'WooCommerce' ) ) {
	require_once DPV_DIR . '/inc/woocommerce.php';
}

/**
 * Cấu hình theme.
 */
function dpv_setup() {
	load_theme_textdomain( 'dpv-theme', DPV_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 120,
		'width'       => 120,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'customize-selective-refresh-widgets' );

	// WooCommerce.
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Kích thước ảnh.
	set_post_thumbnail_size( 600, 450, true );
	add_image_size( 'dpv-card', 480, 360, true );
	add_image_size( 'dpv-hero', 1200, 560, true );
	add_image_size( 'dpv-thumb', 360, 220, true );

	register_nav_menus( array(
		'primary'      => __( 'Menu chính (thanh đỏ)', 'dpv-theme' ),
		'product_menu' => __( 'Danh mục sản phẩm (cột trái trang chủ)', 'dpv-theme' ),
		'footer'       => __( 'Menu chân trang (chính sách)', 'dpv-theme' ),
	) );
}
add_action( 'after_setup_theme', 'dpv_setup' );

/**
 * Chiều rộng nội dung.
 */
function dpv_content_width() {
	$GLOBALS['content_width'] = 820;
}
add_action( 'after_setup_theme', 'dpv_content_width', 0 );

/**
 * CSS + JS (không dùng thư viện ngoài, tải defer).
 */
function dpv_assets() {
	wp_enqueue_style( 'dpv-style', get_template_directory_uri() . '/style.css', array(), DPV_VERSION . '.' . (int) @filemtime( get_template_directory() . '/style.css' ) );
	wp_add_inline_style( 'dpv-style', dpv_inline_css_vars() );

	wp_enqueue_script( 'dpv-main', DPV_URI . '/assets/js/main.js', array(), DPV_VERSION . '.' . (int) @filemtime( get_template_directory() . '/assets/js/main.js' ), array(
		'in_footer' => true,
		'strategy'  => 'defer',
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'dpv_assets' );

/**
 * Biến CSS màu chủ đạo lấy từ Customizer.
 */
function dpv_inline_css_vars() {
	$primary = sanitize_hex_color( get_theme_mod( 'dpv_primary', '#e2231a' ) );
	$dark    = sanitize_hex_color( get_theme_mod( 'dpv_primary_dark', '#b81a13' ) );
	if ( ! $primary ) {
		$primary = '#e2231a';
	}
	if ( ! $dark ) {
		$dark = '#b81a13';
	}
	return ":root{--dpv-primary:{$primary};--dpv-primary-dark:{$dark};}";
}

/**
 * Widget areas.
 */
function dpv_widgets() {
	register_sidebar( array(
		'name'          => __( 'Sidebar bài viết', 'dpv-theme' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar( array(
			/* translators: %d: số cột */
			'name'          => sprintf( __( 'Chân trang – cột %d', 'dpv-theme' ), $i ),
			'id'            => 'footer-' . $i,
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
}
add_action( 'widgets_init', 'dpv_widgets' );

/**
 * Dọn dẹp <head> và các tính năng thừa để trang nhanh hơn.
 */
function dpv_cleanup() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
}
add_action( 'init', 'dpv_cleanup' );

// Bỏ CSS khối nếu không dùng Gutenberg trên front-end quá nhiều (giữ wp-block-styles).
add_filter( 'show_recent_comments_widget_style', '__return_false' );

/**
 * Độ dài và dấu cắt đoạn trích.
 */
add_filter( 'excerpt_length', function () {
	return 28;
}, 999 );
add_filter( 'excerpt_more', function () {
	return '…';
} );

/**
 * Thêm class cho <body>.
 */
add_filter( 'body_class', function ( $classes ) {
	if ( is_front_page() ) {
		$classes[] = 'is-home';
	}
	return $classes;
} );

/**
 * Skip link.
 */
add_action( 'wp_body_open', function () {
	echo '<a class="skip-link screen-reader-text" href="#content">' . esc_html__( 'Bỏ qua, tới nội dung', 'dpv-theme' ) . '</a>';
}, 1 );

/**
 * Tiêu đề trang lưu trữ: bỏ tiền tố tiếng Anh ("Category:", "Tag:"...), chỉ giữ tên mục.
 */
add_filter( 'get_the_archive_title', function ( $title ) {
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}
	if ( is_author() ) {
		return get_the_author();
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	if ( is_month() ) {
		/* translators: %s: tháng/năm */
		return sprintf( __( 'Tháng %s', 'dpv-theme' ), get_the_date( 'm/Y' ) );
	}
	if ( is_year() ) {
		/* translators: %s: năm */
		return sprintf( __( 'Năm %s', 'dpv-theme' ), get_the_date( 'Y' ) );
	}
	return $title;
} );
