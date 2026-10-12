<?php
/**
 * Hàm hỗ trợ.
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Một plugin SEO đầy đủ đang chạy? Khi đó theme không in meta/schema để tránh trùng.
 */
function dpv_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Lấy theme_mod kèm giá trị mặc định rỗng.
 */
function dpv_opt( $key, $default = '' ) {
	$v = get_theme_mod( $key, $default );
	return is_string( $v ) ? trim( $v ) : $v;
}

/**
 * Chuẩn hóa số điện thoại thành dạng tel:.
 */
function dpv_tel( $phone ) {
	return preg_replace( '/[^0-9+]/', '', (string) $phone );
}

/**
 * Link Zalo từ số điện thoại hoặc URL.
 */
function dpv_zalo_url() {
	$z = dpv_opt( 'dpv_zalo' );
	if ( '' === $z ) {
		$z = dpv_opt( 'dpv_phone' );
	}
	if ( '' === $z ) {
		return '';
	}
	if ( preg_match( '#^https?://#i', $z ) ) {
		return $z;
	}
	return 'https://zalo.me/' . dpv_tel( $z );
}

/**
 * Ảnh placeholder (SVG data URI) khi bài viết chưa có ảnh đại diện.
 */
function dpv_placeholder( $label = '' ) {
	$label = esc_html( mb_substr( (string) $label, 0, 28 ) );
	$svg   = '<svg xmlns="http://www.w3.org/2000/svg" width="480" height="360" viewBox="0 0 480 360"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f3ece6"/><stop offset="1" stop-color="#e3d6cb"/></linearGradient></defs><rect width="480" height="360" fill="url(#g)"/><g fill="none" stroke="#c9b8a8" stroke-width="6"><path d="M150 215l90-70 90 70v70H150z"/><path d="M215 285v-45h50v45"/></g></svg>';
	return 'data:image/svg+xml;utf8,' . rawurlencode( $svg );
}

/**
 * Trả về HTML ảnh đại diện của bài (hoặc placeholder).
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @param bool   $eager   true = không lazy (ảnh trên màn hình đầu).
 */
function dpv_thumb( $post_id, $size = 'dpv-card', $eager = false ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$attr = array(
			'class'   => 'dpv-img',
			'loading' => $eager ? 'eager' : 'lazy',
			'alt'     => '',
		);
		if ( $eager ) {
			$attr['fetchpriority'] = 'high';
		}
		$alt = trim( (string) get_post_meta( get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt', true ) );
		$attr['alt'] = '' !== $alt ? $alt : get_the_title( $post_id );
		return get_the_post_thumbnail( $post_id, $size, $attr );
	}
	return '<img class="dpv-img dpv-img--ph" src="' . esc_attr( dpv_placeholder() ) . '" width="480" height="360" alt="' . esc_attr( get_the_title( $post_id ) ) . '" loading="lazy" decoding="async">';
}

/**
 * Lấy bài viết theo slug chuyên mục; không có thì lấy bài mới nhất.
 */
function dpv_posts_by_cat( $slug, $number = 3 ) {
	$args = array(
		'post_type'           => 'post',
		'posts_per_page'      => (int) $number,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	$slug = trim( (string) $slug );
	if ( '' !== $slug && term_exists( $slug, 'category' ) ) {
		$args['category_name'] = $slug;
	}
	return new WP_Query( $args );
}

/**
 * Danh sách chuyên mục sản phẩm mặc định (dùng khi chưa tạo menu "Danh mục sản phẩm").
 * Lấy theo ngành hàng thực tế của Đại Phú Vinh.
 */
function dpv_default_product_cats() {
	return array(
		'chong-tham'       => __( 'Vật liệu chống thấm', 'dpv-theme' ),
		'bot-tret-tuong'   => __( 'Bột trét tường', 'dpv-theme' ),
		'son-nuoc'         => __( 'Sơn nước & sơn chống thấm', 'dpv-theme' ),
		'keo-dan-gach'     => __( 'Keo dán gạch & chà ron', 'dpv-theme' ),
		'tam-cemboard'     => __( 'Tấm Cemboard', 'dpv-theme' ),
		'phao-chi'         => __( 'Phào chỉ', 'dpv-theme' ),
		'nilon-lot-san'    => __( 'Nilon lót sàn', 'dpv-theme' ),
		'phu-kien-thi-cong' => __( 'Phụ kiện thi công', 'dpv-theme' ),
	);
}

/**
 * Link tới chuyên mục sản phẩm (WooCommerce) hoặc chuyên mục bài viết, nếu tồn tại.
 */
function dpv_cat_link( $slug ) {
	foreach ( array( 'product_cat', 'category' ) as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			$t = get_term_by( 'slug', $slug, $tax );
			if ( $t && ! is_wp_error( $t ) ) {
				$l = get_term_link( $t );
				if ( ! is_wp_error( $l ) ) {
					return $l;
				}
			}
		}
	}
	return home_url( '/' . $slug . '/' );
}

/**
 * Chuyên mục "chính" của bài: ưu tiên chuyên mục chính do Yoast / Rank Math chọn,
 * nếu không có thì lấy chuyên mục đầu tiên. Dùng cho breadcrumb và schema.
 * (Mỗi bài nên chỉ thuộc 1 chuyên mục để cấu trúc rõ ràng cho Google.)
 */
function dpv_primary_category( $post_id ) {
	$cats = get_the_category( $post_id );
	if ( ! $cats ) {
		return null;
	}
	foreach ( array( '_yoast_wpseo_primary_category', 'rank_math_primary_category' ) as $key ) {
		$pid = (int) get_post_meta( $post_id, $key, true );
		if ( $pid ) {
			foreach ( $cats as $c ) {
				if ( (int) $c->term_id === $pid ) {
					return $c;
				}
			}
		}
	}
	return $cats[0];
}

/**
 * Link "Đường đi": ưu tiên link Google Maps nhập tay, nếu không thì tạo từ địa chỉ.
 */
function dpv_directions_url() {
	$u = dpv_opt( 'dpv_map_url' );
	if ( '' !== $u ) {
		return $u;
	}
	$addr = dpv_opt( 'dpv_address' );
	if ( '' === $addr ) {
		return '';
	}
	return 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $addr );
}

/**
 * Link "Cửa hàng" (trang shop WooCommerce, nếu không có thì trang chủ).
 */
function dpv_shop_url() {
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$u = wc_get_page_permalink( 'shop' );
		if ( $u ) {
			return $u;
		}
	}
	return home_url( '/' );
}

/**
 * Link "Tin tức": trang bài viết nếu có, nếu không thì chuyên mục cẩm nang.
 */
function dpv_news_url() {
	$pid = (int) get_option( 'page_for_posts' );
	if ( $pid ) {
		return get_permalink( $pid );
	}
	return dpv_cat_link( dpv_opt( 'dpv_cat_news', 'cam-nang' ) );
}
