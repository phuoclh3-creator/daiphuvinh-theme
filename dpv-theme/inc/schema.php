<?php
/**
 * Dữ liệu có cấu trúc JSON-LD: Organization/LocalBusiness, WebSite (+SearchAction),
 * BreadcrumbList, BlogPosting. (Sản phẩm do WooCommerce tự xuất Product schema.)
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mảng breadcrumb dùng chung cho HTML và JSON-LD: [ [name, url], ... ].
 */
function dpv_breadcrumb_items() {
	$items   = array();
	$items[] = array( __( 'Trang chủ', 'dpv-theme' ), home_url( '/' ) );

	if ( is_front_page() ) {
		return array();
	}

	if ( is_singular() ) {
		$post = get_queried_object();

		if ( is_singular( 'product' ) && taxonomy_exists( 'product_cat' ) ) {
			$terms = get_the_terms( $post, 'product_cat' );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term = reset( $terms );
				foreach ( array_reverse( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) ) as $anc ) {
					$t = get_term( $anc, 'product_cat' );
					$items[] = array( $t->name, get_term_link( $t ) );
				}
				$items[] = array( $term->name, get_term_link( $term ) );
			}
		} elseif ( is_singular( 'post' ) ) {
			$cat = dpv_primary_category( $post->ID );
			if ( $cat ) {
				foreach ( array_reverse( get_ancestors( $cat->term_id, 'category' ) ) as $anc ) {
					$t = get_category( $anc );
					$items[] = array( $t->name, get_category_link( $t ) );
				}
				$items[] = array( $cat->name, get_category_link( $cat ) );
			}
		} elseif ( $post->post_parent ) {
			foreach ( array_reverse( get_post_ancestors( $post ) ) as $anc ) {
				$items[] = array( get_the_title( $anc ), get_permalink( $anc ) );
			}
		}
		$items[] = array( get_the_title( $post ), get_permalink( $post ) );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) ) as $anc ) {
				$t = get_term( $anc, $term->taxonomy );
				$items[] = array( $t->name, get_term_link( $t ) );
			}
			$items[] = array( $term->name, get_term_link( $term ) );
		}
	} elseif ( is_search() ) {
		/* translators: %s: từ khóa tìm kiếm */
		$items[] = array( sprintf( __( 'Tìm kiếm: %s', 'dpv-theme' ), get_search_query() ), '' );
	} elseif ( is_404() ) {
		$items[] = array( __( 'Không tìm thấy trang', 'dpv-theme' ), '' );
	} elseif ( is_post_type_archive() ) {
		$items[] = array( post_type_archive_title( '', false ), '' );
	} elseif ( is_home() ) {
		$items[] = array( __( 'Tin tức', 'dpv-theme' ), '' );
	}

	return $items;
}

/**
 * In JSON-LD.
 */
function dpv_print_schema() {
	if ( dpv_has_seo_plugin() ) {
		return;
	}

	$graph = array();
	$home  = home_url( '/' );
	$name  = dpv_opt( 'dpv_biz_name' ) ?: get_bloginfo( 'name' );

	$logo_id  = get_theme_mod( 'custom_logo' );
	$logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

	$same_as = array_values( array_filter( array(
		dpv_opt( 'dpv_facebook' ),
		dpv_opt( 'dpv_youtube' ),
		dpv_opt( 'dpv_tiktok' ),
	) ) );

	// Doanh nghiệp (xuất ở mọi trang để Google gắn thực thể).
	$org = array(
		'@type' => 'LocalBusiness',
		'@id'   => $home . '#organization',
		'name'  => $name,
		'url'   => $home,
	);
	if ( $logo_url ) {
		$org['logo']  = array( '@type' => 'ImageObject', 'url' => $logo_url );
		$org['image'] = $logo_url;
	}
	if ( dpv_opt( 'dpv_phone' ) ) {
		$org['telephone'] = dpv_opt( 'dpv_phone' );
	}
	if ( dpv_opt( 'dpv_email' ) ) {
		$org['email'] = dpv_opt( 'dpv_email' );
	}
	if ( dpv_opt( 'dpv_address' ) ) {
		$org['address'] = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => dpv_opt( 'dpv_address' ),
			'addressLocality' => dpv_opt( 'dpv_city' ),
			'addressCountry'  => 'VN',
		);
	}
	if ( dpv_opt( 'dpv_hours' ) ) {
		$org['openingHours'] = dpv_opt( 'dpv_hours' );
	}
	if ( $same_as ) {
		$org['sameAs'] = $same_as;
	}
	$graph[] = $org;

	// Website + ô tìm kiếm.
	$graph[] = array(
		'@type'           => 'WebSite',
		'@id'             => $home . '#website',
		'url'             => $home,
		'name'            => get_bloginfo( 'name' ),
		'inLanguage'      => get_bloginfo( 'language' ),
		'publisher'       => array( '@id' => $home . '#organization' ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => $home . '?s={search_term_string}',
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	// Breadcrumb.
	$crumbs = dpv_breadcrumb_items();
	if ( count( $crumbs ) > 1 ) {
		$list = array();
		foreach ( $crumbs as $i => $c ) {
			$li = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => wp_strip_all_tags( $c[0] ),
			);
			if ( ! empty( $c[1] ) && ! is_wp_error( $c[1] ) ) {
				$li['item'] = $c[1];
			}
			$list[] = $li;
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $list,
		);
	}

	// Bài viết.
	if ( is_singular( 'post' ) ) {
		$post    = get_queried_object();
		$article = array(
			'@type'            => 'BlogPosting',
			'@id'              => get_permalink( $post ) . '#article',
			'mainEntityOfPage' => get_permalink( $post ),
			'headline'         => mb_substr( wp_strip_all_tags( get_the_title( $post ) ), 0, 110 ),
			'datePublished'    => get_the_date( 'c', $post ),
			'dateModified'     => get_the_modified_date( 'c', $post ),
			'author'           => array( '@type' => 'Organization', 'name' => $name ),
			'publisher'        => array( '@id' => $home . '#organization' ),
			'inLanguage'       => get_bloginfo( 'language' ),
		);
		if ( has_post_thumbnail( $post ) ) {
			$article['image'] = get_the_post_thumbnail_url( $post, 'large' );
		}
		$d = dpv_meta_description();
		if ( $d ) {
			$article['description'] = $d;
		}
		$graph[] = $article;
	}

	$json = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'dpv_print_schema', 20 );
