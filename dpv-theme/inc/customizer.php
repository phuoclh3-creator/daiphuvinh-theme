<?php
/**
 * Customizer: thông tin doanh nghiệp, trang chủ, màu sắc.
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function dpv_customize( WP_Customize_Manager $wp_customize ) {

	/* ---------- Thông tin doanh nghiệp ---------- */
	$wp_customize->add_section( 'dpv_business', array(
		'title'       => __( 'Đại Phú Vinh – Thông tin doanh nghiệp', 'dpv-theme' ),
		'priority'    => 30,
		'description' => __( 'Dùng cho header, footer và dữ liệu có cấu trúc (schema) giúp Google hiểu doanh nghiệp.', 'dpv-theme' ),
	) );

	$fields = array(
		'dpv_biz_name' => array( __( 'Tên doanh nghiệp', 'dpv-theme' ), 'sanitize_text_field', 'text' ),
		'dpv_phone'    => array( __( 'Hotline', 'dpv-theme' ), 'sanitize_text_field', 'text' ),
		'dpv_email'    => array( __( 'Email', 'dpv-theme' ), 'sanitize_email', 'text' ),
		'dpv_zalo'     => array( __( 'Zalo (số điện thoại hoặc link)', 'dpv-theme' ), 'sanitize_text_field', 'text' ),
		'dpv_address'  => array( __( 'Địa chỉ', 'dpv-theme' ), 'sanitize_text_field', 'text' ),
		'dpv_city'     => array( __( 'Tỉnh/Thành (schema)', 'dpv-theme' ), 'sanitize_text_field', 'text' ),
		'dpv_hours'    => array( __( 'Giờ làm việc (vd: Mo-Sa 07:30-17:30)', 'dpv-theme' ), 'sanitize_text_field', 'text' ),
		'dpv_facebook' => array( __( 'Facebook URL', 'dpv-theme' ), 'esc_url_raw', 'url' ),
		'dpv_youtube'  => array( __( 'YouTube URL', 'dpv-theme' ), 'esc_url_raw', 'url' ),
		'dpv_tiktok'   => array( __( 'TikTok URL', 'dpv-theme' ), 'esc_url_raw', 'url' ),
	);
	foreach ( $fields as $id => $f ) {
		$wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => $f[1] ) );
		$wp_customize->add_control( $id, array( 'label' => $f[0], 'section' => 'dpv_business', 'type' => $f[2] ) );
	}

	$wp_customize->add_setting( 'dpv_footer_about', array( 'default' => '', 'sanitize_callback' => 'sanitize_textarea_field' ) );
	$wp_customize->add_control( 'dpv_footer_about', array(
		'label'   => __( 'Giới thiệu ngắn ở chân trang', 'dpv-theme' ),
		'section' => 'dpv_business',
		'type'    => 'textarea',
	) );

	/* ---------- Trang chủ ---------- */
	$wp_customize->add_section( 'dpv_home', array(
		'title'    => __( 'Đại Phú Vinh – Trang chủ', 'dpv-theme' ),
		'priority' => 31,
	) );

	$wp_customize->add_setting( 'dpv_home_h1', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'dpv_home_h1', array(
		'label'       => __( 'Thẻ H1 trang chủ (chứa từ khóa chính)', 'dpv-theme' ),
		'description' => __( 'Mỗi trang chỉ có một H1. Để trống sẽ dùng tên website.', 'dpv-theme' ),
		'section'     => 'dpv_home',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'dpv_home_intro', array( 'default' => '', 'sanitize_callback' => 'wp_kses_post' ) );
	$wp_customize->add_control( 'dpv_home_intro', array(
		'label'   => __( 'Đoạn giới thiệu dưới H1 (HTML cơ bản)', 'dpv-theme' ),
		'section' => 'dpv_home',
		'type'    => 'textarea',
	) );

	$wp_customize->add_setting( 'dpv_home_meta_desc', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'dpv_home_meta_desc', array(
		'label'       => __( 'Meta description trang chủ (120–160 ký tự)', 'dpv-theme' ),
		'section'     => 'dpv_home',
		'type'        => 'textarea',
	) );

	for ( $i = 1; $i <= 4; $i++ ) {
		$wp_customize->add_setting( "dpv_slide_{$i}_img", array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, "dpv_slide_{$i}_img", array(
			/* translators: %d: số thứ tự slide */
			'label'     => sprintf( __( 'Banner %d – ảnh (1200×560)', 'dpv-theme' ), $i ),
			'section'   => 'dpv_home',
			'mime_type' => 'image',
		) ) );
		$wp_customize->add_setting( "dpv_slide_{$i}_alt", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( "dpv_slide_{$i}_alt", array(
			/* translators: %d: số thứ tự slide */
			'label'   => sprintf( __( 'Banner %d – mô tả ảnh (alt)', 'dpv-theme' ), $i ),
			'section' => 'dpv_home',
			'type'    => 'text',
		) );
		$wp_customize->add_setting( "dpv_slide_{$i}_url", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( "dpv_slide_{$i}_url", array(
			/* translators: %d: số thứ tự slide */
			'label'   => sprintf( __( 'Banner %d – liên kết', 'dpv-theme' ), $i ),
			'section' => 'dpv_home',
			'type'    => 'url',
		) );
	}

	$wp_customize->add_setting( 'dpv_cat_quote', array( 'default' => 'bang-bao-gia', 'sanitize_callback' => 'sanitize_title' ) );
	$wp_customize->add_control( 'dpv_cat_quote', array(
		'label'       => __( 'Cột phải: slug chuyên mục blog "Bảng Báo Giá"', 'dpv-theme' ),
		'description' => __( 'Hiện 3 bài mới nhất của chuyên mục này cạnh banner.', 'dpv-theme' ),
		'section'     => 'dpv_home',
		'type'        => 'text',
	) );
	$wp_customize->add_setting( 'dpv_cat_projects', array( 'default' => 'du-an', 'sanitize_callback' => 'sanitize_title' ) );
	$wp_customize->add_control( 'dpv_cat_projects', array(
		'label'   => __( 'Slug chuyên mục "Các dự án hoàn thiện"', 'dpv-theme' ),
		'section' => 'dpv_home',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'dpv_cat_news', array( 'default' => 'cam-nang', 'sanitize_callback' => 'sanitize_title' ) );
	$wp_customize->add_control( 'dpv_cat_news', array(
		'label'   => __( 'Slug chuyên mục "Cẩm nang xây nhà" (cuối trang chủ)', 'dpv-theme' ),
		'section' => 'dpv_home',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'dpv_home_cats', array( 'default' => 'chong-tham,bot-tret-tuong,keo-dan-gach,tam-cemboard', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'dpv_home_cats', array(
		'label'       => __( 'Các khối sản phẩm trên trang chủ (slug danh mục, cách nhau dấu phẩy)', 'dpv-theme' ),
		'description' => __( 'Lấy từ danh mục sản phẩm WooCommerce (product_cat). Nếu chưa có WooCommerce sẽ lấy bài viết cùng slug.', 'dpv-theme' ),
		'section'     => 'dpv_home',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'dpv_default_sidebar', array( 'default' => true, 'sanitize_callback' => 'rest_sanitize_boolean' ) );
	$wp_customize->add_control( 'dpv_default_sidebar', array(
		'label'       => __( 'Dùng sidebar tiếng Việt mặc định của theme', 'dpv-theme' ),
		'description' => __( 'Hiện "Bài viết mới" và "Chuyên mục". Bỏ chọn để dùng widget tự thêm trong Giao diện › Widget.', 'dpv-theme' ),
		'section'     => 'dpv_home',
		'type'        => 'checkbox',
	) );

	/* ---------- Màu sắc ---------- */
	$wp_customize->add_setting( 'dpv_primary', array( 'default' => '#2272ad', 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dpv_primary', array(
		'label'   => __( 'Màu chủ đạo', 'dpv-theme' ),
		'section' => 'colors',
	) ) );
	$wp_customize->add_setting( 'dpv_primary_dark', array( 'default' => '#17598a', 'sanitize_callback' => 'sanitize_hex_color' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dpv_primary_dark', array(
		'label'   => __( 'Màu chủ đạo (đậm, khi rê chuột)', 'dpv-theme' ),
		'section' => 'colors',
	) ) );
}
add_action( 'customize_register', 'dpv_customize' );
