<?php
/**
 * Template tags: breadcrumb, phân trang, menu dự phòng, form tìm kiếm.
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Breadcrumb hiển thị (kèm microdata chuẩn schema).
 */
function dpv_breadcrumb() {
	$items = dpv_breadcrumb_items();
	if ( count( $items ) < 2 ) {
		return;
	}
	echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Đường dẫn', 'dpv-theme' ) . '"><ol>';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $it ) {
		$label = esc_html( wp_strip_all_tags( $it[0] ) );
		if ( $i === $last || empty( $it[1] ) || is_wp_error( $it[1] ) ) {
			echo '<li aria-current="page"><span>' . $label . '</span></li>';
		} else {
			echo '<li><a href="' . esc_url( $it[1] ) . '">' . $label . '</a></li>';
		}
	}
	echo '</ol></nav>';
}

/**
 * Phân trang dạng link thật (có thể crawl).
 */
function dpv_pagination() {
	$links = paginate_links( array(
		'type'      => 'list',
		'mid_size'  => 1,
		'prev_text' => '‹',
		'next_text' => '›',
	) );
	if ( $links ) {
		echo '<nav class="pagination" aria-label="' . esc_attr__( 'Phân trang', 'dpv-theme' ) . '">' . $links . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * Menu chính dự phòng khi chưa gán menu.
 */
function dpv_primary_fallback() {
	$about = get_page_by_path( 've-chung-toi' );
	$items = array(
		array( __( 'Trang chủ', 'dpv-theme' ), home_url( '/' ) ),
		array( __( 'Về chúng tôi', 'dpv-theme' ), $about ? get_permalink( $about ) : home_url( '/ve-chung-toi/' ) ),
		array( __( 'Báo giá', 'dpv-theme' ), home_url( '/bao-gia/' ) ),
		array( __( 'Cẩm nang xây nhà', 'dpv-theme' ), home_url( '/category/blog-seo/' ) ),
		array( __( 'Liên hệ', 'dpv-theme' ), home_url( '/lien-he/' ) ),
	);
	echo '<ul class="menu">';
	foreach ( $items as $it ) {
		echo '<li class="menu-item"><a href="' . esc_url( $it[1] ) . '">' . esc_html( $it[0] ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Cột "Danh mục sản phẩm": ưu tiên menu gán vị trí product_menu,
 * sau đó danh mục WooCommerce, cuối cùng danh sách mặc định.
 */
function dpv_product_menu() {
	if ( has_nav_menu( 'product_menu' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'product_menu',
			'container'      => false,
			'menu_class'     => 'cat-list',
			'depth'          => 2,
			'fallback_cb'    => false,
		) );
		return;
	}

	echo '<ul class="cat-list">';
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_terms( array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => false,
			'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			'number'     => 12,
		) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				echo '<li class="menu-item"><a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a></li>';
			}
			echo '</ul>';
			return;
		}
	}
	foreach ( dpv_default_product_cats() as $slug => $label ) {
		echo '<li class="menu-item"><a href="' . esc_url( dpv_cat_link( $slug ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Thẻ bài viết dạng card.
 */
function dpv_card( $post_id = null, $eager = false ) {
	$post_id = $post_id ?: get_the_ID();
	?>
	<article class="card">
		<a class="card__media" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo dpv_thumb( $post_id, 'dpv-card', $eager ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</a>
		<div class="card__body">
			<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a></h3>
			<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_id ), 22, '…' ) ); ?></p>
		</div>
	</article>
	<?php
}
