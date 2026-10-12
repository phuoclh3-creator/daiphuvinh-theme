<?php
/**
 * Nạp dữ liệu MẪU để xem giao diện (chỉ dùng cho bản xem trước, không đưa lên host thật).
 */
update_option( 'blogname', 'Đại Phú Vinh' );
update_option( 'blogdescription', 'Vật liệu xây dựng chính hãng – giá tốt tại TP.HCM' );
update_option( 'permalink_structure', '/%postname%/' );
update_option( 'WPLANG', 'vi' );
switch_theme( 'dpv-child' );

set_theme_mod( 'dpv_phone', '0900 000 000' );
set_theme_mod( 'dpv_email', 'lienhe@example.com' );
set_theme_mod( 'dpv_address', 'Địa chỉ mẫu – TP. Hồ Chí Minh' );
set_theme_mod( 'dpv_hours', 'Thứ 2 – Thứ 7: 7:30 – 17:30' );
set_theme_mod( 'dpv_footer_about', 'Chuyên cung cấp vật liệu xây dựng, chống thấm, bột trét, sơn, keo dán gạch và tấm Cemboard chính hãng.' );
set_theme_mod( 'dpv_home_h1', 'Đại Phú Vinh – Vật liệu xây dựng, chống thấm chính hãng giá tốt tại TP.HCM' );
set_theme_mod( 'dpv_home_intro', '<p>Đại Phú Vinh cung cấp vật liệu chống thấm, bột trét tường, sơn nước, keo dán gạch và tấm Cemboard chính hãng cho nhà thầu và chủ nhà. Đây là đoạn văn mẫu: hãy thay bằng nội dung giới thiệu thật, có từ khóa chính và liên kết nội bộ tới các danh mục quan trọng.</p>' );

$cats = array(
	'bang-bao-gia' => 'Bảng Báo Giá',
	'du-an'        => 'Dự án',
	'cam-nang'     => 'Cẩm nang xây nhà',
	'chong-tham'   => 'Chống thấm',
	'bot-tret-tuong' => 'Bột trét tường',
	'keo-dan-gach' => 'Keo dán gạch',
	'tam-cemboard' => 'Tấm Cemboard',
);
$ids = array();
foreach ( $cats as $slug => $name ) {
	$t = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
	$ids[ $slug ] = is_wp_error( $t ) ? 0 : $t['term_id'];
}
$mk = function ( $title, $cat, $body = '' ) use ( $ids ) {
	wp_insert_post( array(
		'post_title'    => $title,
		'post_status'   => 'publish',
		'post_content'  => $body ?: '<p>Nội dung mẫu cho bài “' . $title . '”. Bài viết thật nên có tiêu đề H2/H3 rõ ràng, ảnh có alt và liên kết nội bộ.</p><h2>Mục nhỏ thứ nhất</h2><p>Đoạn văn mẫu để xem kiểu chữ và khoảng cách.</p><h2>Mục nhỏ thứ hai</h2><ul><li>Ý một</li><li>Ý hai</li></ul>',
		'post_excerpt'  => 'Tóm tắt ngắn của bài “' . $title . '”, hiển thị dưới tiêu đề ở danh sách và dùng làm meta description.',
		'post_category' => array( $ids[ $cat ] ),
	) );
};
foreach ( array( 'Bảng báo giá chống thấm mới nhất', 'Bảng báo giá bột trét tường Jotun, Dulux', 'Bảng báo giá tấm Cemboard Thái Lan' ) as $t ) { $mk( $t, 'bang-bao-gia' ); }
foreach ( array( 'HOMESTAY TTN GARDEN TẠI HÒA THẮNG, TP. BUÔN MA THUỘT, TỈNH ĐẮK LẮK', 'NHÀ LƯU TRÚ TẠI KHU DU LỊCH KOTAM – TP. BUÔN MA THUỘT, TỈNH ĐẮK LẮK', 'NHÀ BA GIAN 550M2, SỰ KẾT HỢP GIỮA KIẾN TRÚC TRUYỀN THỐNG VÀ HIỆN ĐẠI' ) as $t ) { $mk( $t, 'du-an' ); }
foreach ( array( 'Cách chọn bột trét tường ngoài trời', 'Nguyên nhân nhà thấm nước và giải pháp', 'Kỹ thuật thi công tấm Cemboard làm vách ngăn' ) as $t ) { $mk( $t, 'cam-nang' ); }
foreach ( array( 'Màng khò nóng chống thấm Sika Bituseal', 'Sika Latex TH – phụ gia chống thấm', 'Màng tự dính chống thấm tầng hầm', 'Vữa chống thấm gốc xi măng' ) as $t ) { $mk( $t, 'chong-tham' ); }
foreach ( array( 'Bột trét tường ngoài Jotun', 'Bột trét tường trong nhà Việt Mỹ', 'Bột trét Dulux ngoài trời', 'Bột trét siêu mịn nội thất' ) as $t ) { $mk( $t, 'bot-tret-tuong' ); }
foreach ( array( 'Keo dán gạch Weber Tai Flex', 'Keo dán gạch Weber Tai Maxx', 'Keo chà ron Weber Color Plus', 'Keo chít mạch Saveto' ) as $t ) { $mk( $t, 'keo-dan-gach' ); }
foreach ( array( 'Tấm Cemboard Thái Lan 8mm', 'Tấm Cemboard lót sàn 16mm', 'Tấm Cemboard làm vách ngoại thất', 'Tấm Smartboard Prima' ) as $t ) { $mk( $t, 'tam-cemboard' ); }

wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'Liên hệ', 'post_name' => 'lien-he', 'post_status' => 'publish', 'post_content' => '<p>Trang liên hệ mẫu.</p>' ) );
wp_insert_post( array( 'post_type' => 'page', 'post_title' => 'Về chúng tôi', 'post_name' => 've-chung-toi', 'post_status' => 'publish', 'post_content' => '<p>Trang giới thiệu mẫu.</p>' ) );

/* ---------- Sản phẩm WooCommerce mẫu (nếu WooCommerce đã được cài) ---------- */
if ( class_exists( 'WooCommerce' ) && taxonomy_exists( 'product_cat' ) ) {
	update_option( 'woocommerce_currency', 'VND' );
	update_option( 'woocommerce_currency_pos', 'right_space' );
	update_option( 'woocommerce_price_thousand_sep', '.' );
	update_option( 'woocommerce_price_decimal_sep', ',' );
	update_option( 'woocommerce_price_num_decimals', '0' );
	$prod = array(
		'chong-tham'     => array( 'Chống thấm', array( array( 'Màng khò nóng Sika Bituseal T130 SG', 0 ), array( 'Sika Latex TH – phụ gia chống thấm 5L', 185000 ), array( 'Màng tự dính chống thấm tầng hầm', 0 ), array( 'Vữa chống thấm gốc xi măng 20kg', 320000 ) ) ),
		'bot-tret-tuong' => array( 'Bột trét tường', array( array( 'Bột trét tường ngoài trời Jotun 40kg', 465000 ), array( 'Bột trét Dulux ngoài trời 40kg', 489000 ), array( 'Bột trét tường trong nhà Việt Mỹ 40kg', 355000 ), array( 'Bột trét siêu mịn nội thất 40kg', 0 ) ) ),
		'keo-dan-gach'   => array( 'Keo dán gạch & chà ron', array( array( 'Keo dán gạch Weber Tai Flex 25kg', 215000 ), array( 'Keo dán gạch Weber Tai Maxx 25kg', 238000 ), array( 'Keo chà ron Weber Color Plus 2kg', 0 ), array( 'Keo chít mạch Saveto 1kg', 98000 ) ) ),
		'tam-cemboard'   => array( 'Tấm Cemboard', array( array( 'Tấm Cemboard Thái Lan 8mm', 0 ), array( 'Tấm Cemboard lót sàn 16mm', 0 ), array( 'Tấm Cemboard làm vách ngoại thất', 0 ), array( 'Tấm Smartboard Prima', 0 ) ) ),
	);
	foreach ( $prod as $slug => $info ) {
		$tt = wp_insert_term( $info[0], 'product_cat', array( 'slug' => $slug ) );
		$tid = is_wp_error( $tt ) ? 0 : (int) $tt['term_id'];
		foreach ( $info[1] as $p ) {
			$pr = new WC_Product_Simple();
			$pr->set_name( $p[0] );
			$pr->set_status( 'publish' );
			$pr->set_short_description( 'Mô tả ngắn mẫu của sản phẩm.' );
			$pr->set_description( 'Mô tả chi tiết mẫu: thông số, quy cách đóng gói, hướng dẫn sử dụng.' );
			if ( $p[1] ) {
				$pr->set_regular_price( (string) $p[1] );
			}
			if ( $tid ) {
				$pr->set_category_ids( array( $tid ) );
			}
			$pr->save();
		}
	}
}
flush_rewrite_rules();
