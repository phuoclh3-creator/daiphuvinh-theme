<?php
/**
 * Footer + nút gọi/Zalo nổi.
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$phone = dpv_opt( 'dpv_phone' );
$zalo  = dpv_zalo_url();
$name  = dpv_opt( 'dpv_biz_name' ) ?: get_bloginfo( 'name' );
?>
<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-col">
			<h2 class="widget-title"><?php echo esc_html( $name ); ?></h2>
			<?php if ( dpv_opt( 'dpv_footer_about' ) ) : ?>
				<p><?php echo esc_html( dpv_opt( 'dpv_footer_about' ) ); ?></p>
			<?php endif; ?>
			<ul class="contact-list">
				<?php if ( dpv_opt( 'dpv_address' ) ) : ?><li><?php echo esc_html( dpv_opt( 'dpv_address' ) ); ?></li><?php endif; ?>
				<?php if ( $phone ) : ?><li><a href="tel:<?php echo esc_attr( dpv_tel( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li><?php endif; ?>
				<?php if ( dpv_opt( 'dpv_email' ) ) : ?><li><a href="mailto:<?php echo esc_attr( antispambot( dpv_opt( 'dpv_email' ) ) ); ?>"><?php echo esc_html( antispambot( dpv_opt( 'dpv_email' ) ) ); ?></a></li><?php endif; ?>
				<?php if ( dpv_opt( 'dpv_hours' ) ) : ?><li><?php echo esc_html( dpv_opt( 'dpv_hours' ) ); ?></li><?php endif; ?>
			</ul>
		</div>

		<div class="footer-col">
			<?php if ( is_active_sidebar( 'footer-1' ) ) : dynamic_sidebar( 'footer-1' ); else : ?>
				<h2 class="widget-title"><?php esc_html_e( 'Sản phẩm', 'dpv-theme' ); ?></h2>
				<ul class="footer-links">
					<?php foreach ( array_slice( dpv_default_product_cats(), 0, 6, true ) as $slug => $label ) : ?>
						<li><a href="<?php echo esc_url( dpv_cat_link( $slug ) ); ?>"><?php echo esc_html( $label ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="footer-col">
			<?php if ( is_active_sidebar( 'footer-3' ) ) : dynamic_sidebar( 'footer-3' ); else : ?>
				<h2 class="widget-title"><?php esc_html_e( 'Blog', 'dpv-theme' ); ?></h2>
				<ul class="footer-links">
					<?php
					foreach ( array(
						array( 'dpv_cat_quote', 'bang-bao-gia', __( 'Bảng báo giá', 'dpv-theme' ) ),
						array( 'dpv_cat_news', 'cam-nang', __( 'Cẩm nang vật liệu', 'dpv-theme' ) ),
						array( 'dpv_cat_projects', 'du-an', __( 'Dự án hoàn thiện', 'dpv-theme' ) ),
					) as $item ) {
						$slug = dpv_opt( $item[0], $item[1] );
						$term = get_term_by( 'slug', $slug, 'category' );
						$link = ( $term && ! is_wp_error( $term ) ) ? get_term_link( $term ) : home_url( '/' . $slug . '/' );
						if ( is_wp_error( $link ) ) {
							continue;
						}
						echo '<li><a href="' . esc_url( $link ) . '">' . esc_html( $item[2] ) . '</a></li>';
					}
					?>
				</ul>
			<?php endif; ?>
		</div>

		<div class="footer-col">
			<?php if ( is_active_sidebar( 'footer-2' ) ) : dynamic_sidebar( 'footer-2' ); else : ?>
				<h2 class="widget-title"><?php esc_html_e( 'Hỗ trợ khách hàng', 'dpv-theme' ); ?></h2>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-links',
					'depth'          => 1,
					'fallback_cb'    => function () {
						echo '<ul class="footer-links">';
						foreach ( array(
							'chinh-sach-doi-tra-san-pham'      => __( 'Chính sách đổi trả', 'dpv-theme' ),
							'chinh-sach-van-chuyen-giao-nhan'  => __( 'Vận chuyển & giao nhận', 'dpv-theme' ),
							'hinh-thuc-thanh-toan'             => __( 'Hình thức thanh toán', 'dpv-theme' ),
							've-chung-toi'                     => __( 'Về chúng tôi', 'dpv-theme' ),
							'lien-he'                          => __( 'Liên hệ', 'dpv-theme' ),
						) as $slug => $label ) {
							echo '<li><a href="' . esc_url( home_url( '/' . $slug . '/' ) ) . '">' . esc_html( $label ) . '</a></li>';
						}
						echo '</ul>';
					},
				) );
				?>
			<?php endif; ?>
		</div>
	</div>

	<div class="footer-bottom">
		<div class="container">
			<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( $name ); ?>. <?php esc_html_e( 'Bảo lưu mọi quyền.', 'dpv-theme' ); ?></p>
		</div>
	</div>
</footer>

<?php if ( $phone || $zalo ) : ?>
<div class="float-contact" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'dpv-theme' ); ?>">
	<?php if ( $zalo ) : ?>
		<a class="float-contact__btn float-contact__btn--zalo" href="<?php echo esc_url( $zalo ); ?>" target="_blank" rel="noopener nofollow"><span>Zalo</span></a>
	<?php endif; ?>
	<?php if ( $phone ) : ?>
		<a class="float-contact__btn float-contact__btn--tel" href="tel:<?php echo esc_attr( dpv_tel( $phone ) ); ?>"><span><?php esc_html_e( 'Gọi ngay', 'dpv-theme' ); ?></span></a>
	<?php endif; ?>
</div>
<?php endif; ?>

<?php
// Thanh tab cố định chân trang (chỉ hiện trên điện thoại): Cửa hàng · Tin tức · Hotline · Đường đi.
$dir_url = dpv_directions_url();
$tabs    = array(
	array(
		'url'   => dpv_shop_url(),
		'label' => __( 'Cửa hàng', 'dpv-theme' ),
		'ext'   => false,
		'icon'  => '<path d="M2.5 3.5h3l2.2 10.6a1 1 0 0 0 1 .8h8.6a1 1 0 0 0 1-.76L19.8 7H6"/><circle cx="9.5" cy="19" r="1.3"/><circle cx="17" cy="19" r="1.3"/>',
	),
	array(
		'url'   => dpv_news_url(),
		'label' => __( 'Tin tức', 'dpv-theme' ),
		'ext'   => false,
		'icon'  => '<path d="M6 3.5h11a1 1 0 0 1 1 1v14.5H7.2A2.2 2.2 0 0 0 5 21.2V4.5a1 1 0 0 1 1-1z"/><path d="M5 19.2A2.2 2.2 0 0 1 7.2 17H18"/>',
	),
);
if ( $phone ) {
	$tabs[] = array(
		'url'   => 'tel:' . dpv_tel( $phone ),
		'label' => __( 'Hotline', 'dpv-theme' ),
		'ext'   => false,
		'icon'  => '<path d="M6.6 10.8a15 15 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/>',
	);
}
if ( $dir_url ) {
	$tabs[] = array(
		'url'   => $dir_url,
		'label' => __( 'Đường đi', 'dpv-theme' ),
		'ext'   => true,
		'icon'  => '<path d="M12 21s6.5-5.6 6.5-11a6.5 6.5 0 0 0-13 0C5.5 15.4 12 21 12 21z"/><circle cx="12" cy="10" r="2.3"/>',
	);
}
?>
<nav class="tabbar" aria-label="<?php esc_attr_e( 'Điều hướng nhanh', 'dpv-theme' ); ?>">
	<?php foreach ( $tabs as $t ) : ?>
		<a class="tabbar__item" href="<?php echo esc_url( $t['url'], array( 'http', 'https', 'tel' ) ); ?>"<?php echo $t['ext'] ? ' target="_blank" rel="noopener"' : ''; ?>>
			<svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?php echo $t['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG tĩnh trong theme. ?></svg>
			<span><?php echo esc_html( $t['label'] ); ?></span>
		</a>
	<?php endforeach; ?>
</nav>

<?php wp_footer(); ?>
</body>
</html>
