<?php
/**
 * SEO on-page: meta description, canonical, robots, Open Graph, Twitter Card, robots.txt.
 * Tự tắt khi Yoast / Rank Math / AIOSEO / SEOPress đang chạy.
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tiêu đề: dùng dấu | và bỏ phần tagline ở trang con.
 */
add_filter( 'document_title_separator', function () {
	return '|';
} );

/**
 * Meta description theo từng loại trang.
 */
function dpv_meta_description() {
	$desc = '';

	if ( is_front_page() || is_home() ) {
		$desc = dpv_opt( 'dpv_home_meta_desc' );
		if ( '' === $desc ) {
			$desc = get_bloginfo( 'description' );
		}
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post ) {
			$desc = has_excerpt( $post ) ? $post->post_excerpt : wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$desc = term_description();
		if ( '' === trim( wp_strip_all_tags( $desc ) ) ) {
			$term = get_queried_object();
			if ( $term && ! is_wp_error( $term ) ) {
				/* translators: %s: tên danh mục */
				$desc = sprintf( __( 'Tổng hợp bài viết và sản phẩm về %s tại %s.', 'dpv-theme' ), $term->name, get_bloginfo( 'name' ) );
			}
		}
	} elseif ( is_post_type_archive() ) {
		$desc = get_the_archive_description();
	}

	$desc = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $desc ) ) );
	if ( mb_strlen( $desc ) > 160 ) {
		$desc = mb_substr( $desc, 0, 157 );
		$desc = preg_replace( '/\s+\S*$/u', '', $desc ) . '…';
	}
	return $desc;
}

/**
 * URL canonical cho các trang không phải bài viết đơn (WP chỉ tự in canonical cho singular).
 */
function dpv_canonical_url() {
	$url = '';
	if ( is_front_page() ) {
		$url = home_url( '/' );
	} elseif ( is_home() ) {
		$url = get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$t = get_queried_object();
		if ( $t && ! is_wp_error( $t ) ) {
			$l   = get_term_link( $t );
			$url = is_wp_error( $l ) ? '' : $l;
		}
	} elseif ( is_post_type_archive() ) {
		$url = get_post_type_archive_link( get_query_var( 'post_type' ) );
	} elseif ( is_author() ) {
		$url = get_author_posts_url( get_queried_object_id() );
	}
	if ( $url ) {
		$paged = (int) get_query_var( 'paged' );
		if ( $paged > 1 ) {
			$url = trailingslashit( $url ) . 'page/' . $paged . '/';
		}
	}
	return $url;
}

/**
 * Ảnh đại diện cho OG.
 */
function dpv_og_image() {
	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
		if ( $src ) {
			return array( $src[0], $src[1], $src[2] );
		}
	}
	$logo = get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$src = wp_get_attachment_image_src( $logo, 'full' );
		if ( $src ) {
			return array( $src[0], $src[1], $src[2] );
		}
	}
	$slide = absint( get_theme_mod( 'dpv_slide_1_img' ) );
	if ( $slide ) {
		$src = wp_get_attachment_image_src( $slide, 'large' );
		if ( $src ) {
			return array( $src[0], $src[1], $src[2] );
		}
	}
	return null;
}

/**
 * In meta vào <head>.
 */
function dpv_head_meta() {
	if ( dpv_has_seo_plugin() ) {
		return;
	}

	$desc = dpv_meta_description();
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}

	$canon = dpv_canonical_url();
	if ( $canon ) {
		echo '<link rel="canonical" href="' . esc_url( $canon ) . '">' . "\n";
	}

	// Open Graph + Twitter.
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : ( $canon ?: home_url( add_query_arg( array() ) ) );
	$type  = is_singular( 'post' ) ? 'article' : 'website';

	echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";

	$img = dpv_og_image();
	if ( $img ) {
		echo '<meta property="og:image" content="' . esc_url( $img[0] ) . '">' . "\n";
		echo '<meta property="og:image:width" content="' . (int) $img[1] . '">' . "\n";
		echo '<meta property="og:image:height" content="' . (int) $img[2] . '">' . "\n";
	}
	if ( is_singular( 'post' ) ) {
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="' . ( $img ? 'summary_large_image' : 'summary' ) . '">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	if ( $img ) {
		echo '<meta name="twitter:image" content="' . esc_url( $img[0] ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'dpv_head_meta', 2 );

// Ghi chú: với bài/trang đơn, WordPress đã tự in rel="canonical"; hàm trên chỉ in cho trang chủ, danh mục, thẻ, archive.

/**
 * Chặn index trang mỏng: kết quả tìm kiếm, 404, trang đăng nhập/giỏ hàng/tài khoản.
 */
add_filter( 'wp_robots', function ( $robots ) {
	if ( dpv_has_seo_plugin() ) {
		return $robots;
	}
	if ( is_search() || is_404() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['max-image-preview'] );
	}
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
} );

/**
 * Trang attachment (ảnh) → chuyển 301 về bài cha hoặc chính file ảnh.
 */
add_action( 'template_redirect', function () {
	if ( ! is_attachment() ) {
		return;
	}
	$post = get_queried_object();
	if ( $post && $post->post_parent ) {
		wp_safe_redirect( get_permalink( $post->post_parent ), 301 );
	} else {
		wp_safe_redirect( wp_get_attachment_url( $post->ID ) ?: home_url( '/' ), 301 );
	}
	exit;
} );

/**
 * robots.txt: khai báo sitemap có sẵn của WordPress, chặn tham số rác.
 */
add_filter( 'robots_txt', function ( $output, $public ) {
	if ( ! $public ) {
		return $output;
	}
	$output .= "\n# Đại Phú Vinh theme\n";
	$output .= "Disallow: /*?s=\n";
	$output .= "Disallow: /*?add-to-cart=\n";
	$output .= "Disallow: /*?add_to_wishlist=\n";
	$output .= "Disallow: /*?wc-ajax=\n";
	$output .= 'Sitemap: ' . home_url( '/wp-sitemap.xml' ) . "\n";
	return $output;
}, 20, 2 );

/**
 * Sitemap WP: bỏ trang tác giả (ít giá trị, dễ lộ tên đăng nhập).
 */
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return ( 'users' === $name ) ? false : $provider;
}, 10, 2 );

/**
 * Chặn liệt kê user qua REST/?author=1 (bảo mật + tránh URL rác).
 */
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );
add_action( 'template_redirect', function () {
	if ( is_author() && ! is_admin() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );

/**
 * Ảnh trong nội dung: bắt buộc có alt (lấy từ tên bài nếu trống) để không mất điểm SEO ảnh.
 */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment ) {
	if ( isset( $attr['alt'] ) && '' === trim( $attr['alt'] ) ) {
		$parent = $attachment instanceof WP_Post ? $attachment->post_parent : 0;
		$attr['alt'] = $parent ? get_the_title( $parent ) : get_the_title( $attachment );
	}
	return $attr;
}, 10, 2 );
