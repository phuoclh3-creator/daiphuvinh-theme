<?php
/**
 * Header: thanh trên (logo, tìm kiếm, liên hệ) + thanh menu đỏ.
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone     = dpv_opt( 'dpv_phone' );
$email     = dpv_opt( 'dpv_email' );
$has_woo   = class_exists( 'WooCommerce' );
$login_url = $has_woo ? wc_get_page_permalink( 'myaccount' ) : wp_login_url();
$cart_url  = $has_woo ? wc_get_cart_url() : '';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="<?php echo esc_attr( get_theme_mod( 'dpv_primary', '#2272ad' ) ); ?>">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="top">
	<div class="container header-top">
		<div class="brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="brand__mark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
					<svg viewBox="0 0 48 48" width="52" height="52" aria-hidden="true" focusable="false"><circle cx="24" cy="24" r="24" fill="currentColor"/><path d="M13 35V15h14v6M13 35h20V22l-6-5" fill="none" stroke="#fff" stroke-width="2.6" stroke-linejoin="round"/><path d="M24 35v-7h5v7" fill="none" stroke="#fff" stroke-width="2.6"/></svg>
				</a>
			<?php endif; ?>
			<div class="brand__text<?php echo has_custom_logo() ? ' screen-reader-text' : ''; ?>"><?php /* Có logo ảnh: ẩn chữ bằng CSS nhưng vẫn giữ cho trình đọc/SEO. */ ?>
				<a class="brand__name" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<span class="brand__tag"><?php bloginfo( 'description' ); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<?php get_search_form(); ?>

		<div class="header-contact">
			<?php if ( $phone ) : ?>
				<a class="hc-line" href="tel:<?php echo esc_attr( dpv_tel( $phone ) ); ?>">
					<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
					<span><?php echo esc_html( $phone ); ?></span>
				</a>
			<?php endif; ?>
			<?php if ( $email ) : ?>
				<a class="hc-line hc-line--mail" href="mailto:<?php echo esc_attr( antispambot( $email ) ); ?>">
					<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm9 7 8-5H4zm0 2L4 9v9h16V9z"/></svg>
					<span><?php echo esc_html( antispambot( $email ) ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<div class="header-actions">
			<?php if ( ! is_front_page() ) : // Ẩn nút Đăng nhập ở trang chủ. ?>
				<a class="btn btn--outline" href="<?php echo esc_url( $login_url ); ?>" rel="nofollow"><?php esc_html_e( 'Đăng nhập', 'dpv-theme' ); ?></a>
			<?php endif; ?>
			<?php if ( $cart_url ) : ?>
				<a class="cart-link" href="<?php echo esc_url( $cart_url ); ?>" rel="nofollow" aria-label="<?php esc_attr_e( 'Giỏ hàng', 'dpv-theme' ); ?>">
					<svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" d="M2 3h3l2.4 11.2a1 1 0 0 0 1 .8h9.2a1 1 0 0 0 1-.76L20 7H6"/><circle cx="9" cy="19.5" r="1.4" fill="currentColor"/><circle cx="17" cy="19.5" r="1.4" fill="currentColor"/></svg>
					<?php $n = dpv_cart_count(); if ( $n ) : ?><span class="cart-link__n"><?php echo (int) $n; ?></span><?php endif; ?>
				</a>
			<?php endif; ?>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">
				<span class="screen-reader-text"><?php esc_html_e( 'Mở menu', 'dpv-theme' ); ?></span>
				<span class="nav-toggle__bars" aria-hidden="true"></span>
			</button>
		</div>
	</div>

	<div class="navbar">
		<div class="container navbar__in">
			<div class="catbtn-wrap">
				<button class="catbtn" type="button" aria-expanded="false" aria-controls="cat-panel">
					<span class="catbtn__bars" aria-hidden="true"></span>
					<?php esc_html_e( 'Danh mục sản phẩm', 'dpv-theme' ); ?>
				</button>
				<?php if ( ! is_front_page() ) : ?>
					<div class="cat-panel cat-panel--drop" id="cat-panel" hidden>
						<?php dpv_product_menu(); ?>
					</div>
				<?php else : // Trang chủ: khung rỗng, JS nhân bản danh mục từ khối bên trái banner khi người dùng cuộn xuống rồi bấm nút. ?>
					<div class="cat-panel cat-panel--drop" id="cat-panel" hidden></div>
				<?php endif; ?>
			</div>

			<nav id="primary-nav" class="primary-nav" aria-label="<?php esc_attr_e( 'Menu chính', 'dpv-theme' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'depth'          => 2,
					'fallback_cb'    => 'dpv_primary_fallback',
				) );
				?>
			</nav>
		</div>
	</div>
</header>
