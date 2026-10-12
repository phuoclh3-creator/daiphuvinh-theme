<?php
/**
 * Child theme: Đại Phú Vinh.
 *
 * CSS của theme cha (style.css) đã được theme cha tự nạp; child chỉ nạp thêm style.css của mình
 * SAU theme cha để ghi đè.
 *
 * @package dpv-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'dpv-child-style',
		get_stylesheet_uri(),
		array( 'dpv-style' ),
		wp_get_theme()->get( 'Version' )
	);
}, 20 );

/*
 * Thêm hàm riêng bên dưới. Ví dụ:
 *
 * - Đổi số bài ở khối "Cẩm nang" trang chủ: copy front-page.php từ theme cha vào thư mục này rồi sửa.
 * - Ghi đè bất kỳ template nào: tạo file cùng tên (header.php, footer.php, single.php...) trong thư mục child.
 */
