<?php
/**
 * Trang chủ dạng khối (block): mỗi phần là một khối riêng, có thể xóa / thêm / đổi chỗ
 * ngay trong trình soạn thảo của trang "Trang chủ".
 *
 * Các khối: dpv/hero, dpv/projects, dpv/products, dpv/posts. Phần giới thiệu (H1 + đoạn văn)
 * dùng khối có sẵn của WordPress (Tiêu đề, Đoạn văn…).
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------------
 * Đăng ký khối
 * ------------------------------------------------------------------------- */
function dpv_register_home_blocks() {
	wp_register_script(
		'dpv-home-blocks-editor',
		DPV_URI . '/assets/js/home-blocks-editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-core-data', 'wp-server-side-render', 'wp-i18n' ),
		DPV_VERSION . '.' . (int) @filemtime( DPV_DIR . '/assets/js/home-blocks-editor.js' ),
		true
	);

	$common = array(
		'api_version'   => 3,
		'editor_script' => 'dpv-home-blocks-editor',
	);

	register_block_type( 'dpv/hero', $common + array(
		'title'           => __( 'Đại Phú Vinh: Danh mục + Banner + Báo giá', 'dpv-theme' ),
		'attributes'      => array(
			'quoteCategory' => array( 'type' => 'string', 'default' => 'bang-bao-gia' ),
			'quoteCount'    => array( 'type' => 'number', 'default' => 3 ),
		),
		'render_callback' => 'dpv_render_block_hero',
	) );
	register_block_type( 'dpv/projects', $common + array(
		'title'           => __( 'Đại Phú Vinh: Dự án (3 ảnh lớn)', 'dpv-theme' ),
		'attributes'      => array(
			'title'    => array( 'type' => 'string', 'default' => 'Các dự án hoàn thiện' ),
			'category' => array( 'type' => 'string', 'default' => 'du-an' ),
			'count'    => array( 'type' => 'number', 'default' => 3 ),
		),
		'render_callback' => 'dpv_render_block_projects',
	) );
	register_block_type( 'dpv/products', $common + array(
		'title'           => __( 'Đại Phú Vinh: Sản phẩm theo danh mục', 'dpv-theme' ),
		'attributes'      => array(
			'title'    => array( 'type' => 'string', 'default' => '' ),
			'category' => array( 'type' => 'string', 'default' => '' ),
			'count'    => array( 'type' => 'number', 'default' => 8 ),
		),
		'render_callback' => 'dpv_render_block_products',
	) );
	register_block_type( 'dpv/posts', $common + array(
		'title'           => __( 'Đại Phú Vinh: Bài viết theo chuyên mục', 'dpv-theme' ),
		'attributes'      => array(
			'title'    => array( 'type' => 'string', 'default' => 'Cẩm nang xây nhà' ),
			'category' => array( 'type' => 'string', 'default' => 'cam-nang' ),
			'count'    => array( 'type' => 'number', 'default' => 4 ),
		),
		'render_callback' => 'dpv_render_block_posts',
	) );
}
add_action( 'init', 'dpv_register_home_blocks' );

/** Nhóm khối riêng trong trình chèn khối. */
function dpv_block_category( $categories ) {
	array_unshift( $categories, array( 'slug' => 'dpv', 'title' => __( 'Đại Phú Vinh', 'dpv-theme' ) ) );
	return $categories;
}
add_filter( 'block_categories_all', 'dpv_block_category' );

/** CSS cho khung soạn thảo (khối hiển thị đúng như ngoài web). */
function dpv_editor_assets() {
	if ( ! is_admin() ) {
		return;
	}
	wp_enqueue_style( 'dpv-style', DPV_URI . '/style.css', array(), DPV_VERSION . '.' . (int) @filemtime( DPV_DIR . '/style.css' ) );
	wp_add_inline_style( 'dpv-style', dpv_inline_css_vars() . '.editor-styles-wrapper .wp-block[data-type^="dpv/"]{max-width:none;margin:0}.editor-styles-wrapper{background:#fff}.editor-styles-wrapper .slider__dots{pointer-events:none}' );
}
add_action( 'enqueue_block_assets', 'dpv_editor_assets' );

/** Đang render trong trình soạn thảo (ServerSideRender)? */
function dpv_is_editor_render() {
	return is_admin() || ( function_exists( 'wp_is_serving_rest_request' ) && wp_is_serving_rest_request() );
}

function dpv_empty_notice( $msg ) {
	if ( ! dpv_is_editor_render() ) {
		return '';
	}
	return '<section class="container block"><p class="muted" style="padding:18px;border:1px dashed #c3c8cf;border-radius:8px">' . esc_html( $msg ) . '</p></section>';
}

/* ---------------------------------------------------------------------------
 * Render: Hero (danh mục + banner + báo giá)
 * ------------------------------------------------------------------------- */
function dpv_render_block_hero( $atts ) {
	$quote_slug = isset( $atts['quoteCategory'] ) && '' !== $atts['quoteCategory'] ? sanitize_title( $atts['quoteCategory'] ) : 'bang-bao-gia';
	$quote_n    = max( 1, min( 8, (int) ( isset( $atts['quoteCount'] ) ? $atts['quoteCount'] : 3 ) ) );
	$quote_term = get_term_by( 'slug', $quote_slug, 'category' );
	$quote_q    = dpv_posts_by_cat( $quote_slug, $quote_n );

	$slides = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$id = absint( get_theme_mod( "dpv_slide_{$i}_img" ) );
		if ( $id ) {
			$slides[] = array(
				'id'  => $id,
				'alt' => dpv_opt( "dpv_slide_{$i}_alt" ),
				'url' => dpv_opt( "dpv_slide_{$i}_url" ),
			);
		}
	}

	ob_start();
	?>
	<section class="container hero" aria-label="<?php esc_attr_e( 'Danh mục và banner', 'dpv-theme' ); ?>">
		<aside class="hero__cats" aria-label="<?php esc_attr_e( 'Danh mục sản phẩm', 'dpv-theme' ); ?>">
			<div class="cat-panel" id="cat-panel-home">
				<?php dpv_product_menu(); ?>
			</div>
		</aside>

		<div class="hero__slider slider" data-slider data-interval="5500" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Banner nổi bật', 'dpv-theme' ); ?>">
			<div class="slider__track">
				<?php if ( $slides ) : ?>
					<?php foreach ( $slides as $n => $s ) : ?>
						<div class="slide<?php echo 0 === $n ? ' is-active' : ''; ?>" role="group" aria-label="<?php echo esc_attr( ( $n + 1 ) . ' / ' . count( $slides ) ); ?>">
							<?php
							$img = wp_get_attachment_image( $s['id'], 'dpv-hero', false, array(
								'class'         => 'slide__img',
								'alt'           => $s['alt'],
								'loading'       => 0 === $n ? 'eager' : 'lazy',
								'fetchpriority' => 0 === $n ? 'high' : 'auto',
								'sizes'         => '(min-width: 1100px) 760px, 100vw',
							) );
							if ( $s['url'] ) {
								echo '<a href="' . esc_url( $s['url'] ) . '">' . $img . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
							} else {
								echo $img; // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="slide slide--default is-active" role="group" aria-label="1 / 2">
						<div class="slide__copy">
							<p class="slide__kicker"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
							<p class="slide__title"><?php esc_html_e( 'Vật liệu xây dựng chính hãng – giá tốt tại TP.HCM', 'dpv-theme' ); ?></p>
							<ul class="slide__bullets">
								<li><?php esc_html_e( 'Chống thấm, bột trét, sơn, keo dán gạch', 'dpv-theme' ); ?></li>
								<li><?php esc_html_e( 'Tư vấn miễn phí – giao hàng nhanh', 'dpv-theme' ); ?></li>
							</ul>
						</div>
					</div>
					<div class="slide slide--default slide--alt" role="group" aria-label="2 / 2">
						<div class="slide__copy">
							<p class="slide__kicker"><?php esc_html_e( 'Giải pháp trọn gói', 'dpv-theme' ); ?></p>
							<p class="slide__title"><?php esc_html_e( 'Chống thấm toàn diện – bền cùng công trình', 'dpv-theme' ); ?></p>
							<ul class="slide__bullets">
								<li><?php esc_html_e( 'Báo giá nhanh theo diện tích', 'dpv-theme' ); ?></li>
								<li><?php esc_html_e( 'Hướng dẫn thi công chi tiết', 'dpv-theme' ); ?></li>
							</ul>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<div class="slider__dots" role="tablist"></div>
		</div>

		<aside class="hero__side" aria-labelledby="side-title">
			<h2 class="section-title section-title--left" id="side-title"><?php echo esc_html( $quote_term ? $quote_term->name : __( 'Bảng Báo Giá', 'dpv-theme' ) ); ?></h2>
			<?php if ( $quote_q->have_posts() ) : ?>
				<ul class="side-list">
					<?php while ( $quote_q->have_posts() ) : $quote_q->the_post(); ?>
						<li class="side-item">
							<a class="side-item__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo dpv_thumb( get_the_ID(), 'dpv-thumb' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<a class="side-item__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
				<?php if ( $quote_term ) : ?>
					<a class="more more--side" href="<?php echo esc_url( get_term_link( $quote_term ) ); ?>"><?php esc_html_e( 'Xem thêm', 'dpv-theme' ); ?></a>
				<?php endif; ?>
			<?php else : ?>
				<p class="muted"><?php esc_html_e( 'Chưa có bài trong chuyên mục Bảng Báo Giá.', 'dpv-theme' ); ?></p>
			<?php endif; ?>
		</aside>
	</section>
	<?php
	return ob_get_clean();
}

/* ---------------------------------------------------------------------------
 * Render: Dự án
 * ------------------------------------------------------------------------- */
function dpv_render_block_projects( $atts ) {
	$title = isset( $atts['title'] ) && '' !== trim( $atts['title'] ) ? $atts['title'] : __( 'Các dự án hoàn thiện', 'dpv-theme' );
	$slug  = isset( $atts['category'] ) && '' !== $atts['category'] ? sanitize_title( $atts['category'] ) : 'du-an';
	$n     = max( 1, min( 12, (int) ( isset( $atts['count'] ) ? $atts['count'] : 3 ) ) );
	$q     = dpv_posts_by_cat( $slug, $n );
	if ( ! $q->have_posts() ) {
		return dpv_empty_notice( __( 'Khối "Dự án": chưa có bài trong chuyên mục đã chọn nên ngoài web sẽ không hiện.', 'dpv-theme' ) );
	}
	$id = 'projects-' . sanitize_html_class( $slug );
	ob_start();
	?>
	<section class="container block" aria-labelledby="<?php echo esc_attr( $id ); ?>">
		<h2 class="section-title" id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></h2>
		<div class="grid grid--3">
			<?php while ( $q->have_posts() ) : $q->the_post(); ?>
				<a class="project" href="<?php the_permalink(); ?>">
					<?php echo dpv_thumb( get_the_ID(), 'dpv-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="project__title"><?php the_title(); ?></span>
				</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/* ---------------------------------------------------------------------------
 * Render: Sản phẩm theo danh mục
 * ------------------------------------------------------------------------- */
function dpv_render_block_products( $atts ) {
	$slug = isset( $atts['category'] ) ? sanitize_title( $atts['category'] ) : '';
	$n    = max( 1, min( 24, (int) ( isset( $atts['count'] ) ? $atts['count'] : 8 ) ) );
	if ( '' === $slug ) {
		return dpv_empty_notice( __( 'Khối "Sản phẩm": hãy chọn danh mục ở thanh bên phải.', 'dpv-theme' ) );
	}
	$defaults = dpv_default_product_cats();
	$label    = isset( $defaults[ $slug ] ) ? $defaults[ $slug ] : ucwords( str_replace( '-', ' ', $slug ) );
	$term     = taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $slug, 'product_cat' ) : false;
	if ( $term && ! is_wp_error( $term ) ) {
		$label = $term->name;
	}
	if ( isset( $atts['title'] ) && '' !== trim( $atts['title'] ) ) {
		$label = $atts['title'];
	}

	$products = array();
	$q        = null;
	if ( $term && function_exists( 'wc_get_products' ) ) {
		$products = wc_get_products( array(
			'status'   => 'publish',
			'limit'    => $n,
			'category' => array( $slug ),
			'return'   => 'ids',
		) );
	} elseif ( term_exists( $slug, 'category' ) ) {
		$q = dpv_posts_by_cat( $slug, $n );
	}
	if ( ! $products && ( ! $q || ! $q->have_posts() ) ) {
		return dpv_empty_notice( sprintf( /* translators: %s: tên danh mục */ __( 'Khối "%s": danh mục chưa có sản phẩm nên ngoài web sẽ không hiện.', 'dpv-theme' ), $label ) );
	}
	$id = 'cat-' . sanitize_html_class( $slug );
	ob_start();
	?>
	<section class="container block" aria-labelledby="<?php echo esc_attr( $id ); ?>">
		<div class="block__head">
			<h2 class="section-title section-title--left" id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></h2>
			<a class="more" href="<?php echo esc_url( dpv_cat_link( $slug ) ); ?>"><?php esc_html_e( 'Xem thêm', 'dpv-theme' ); ?></a>
		</div>
		<div class="grid grid--4">
			<?php if ( $products ) : ?>
				<?php foreach ( $products as $pid ) :
					$p = wc_get_product( $pid );
					if ( ! $p ) { continue; } ?>
					<article class="card card--product">
						<a class="card__media" href="<?php echo esc_url( get_permalink( $pid ) ); ?>" tabindex="-1" aria-hidden="true"><?php echo dpv_thumb( $pid, 'dpv-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
						<div class="card__body">
							<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( $p->get_name() ); ?></a></h3>
							<p class="card__price"><?php echo wp_kses_post( $p->get_price_html() ?: esc_html__( 'Liên hệ', 'dpv-theme' ) ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			<?php else : ?>
				<?php while ( $q->have_posts() ) : $q->the_post(); dpv_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/* ---------------------------------------------------------------------------
 * Render: Bài viết theo chuyên mục (cẩm nang…)
 * ------------------------------------------------------------------------- */
function dpv_render_block_posts( $atts ) {
	$title = isset( $atts['title'] ) && '' !== trim( $atts['title'] ) ? $atts['title'] : __( 'Cẩm nang xây nhà', 'dpv-theme' );
	$slug  = isset( $atts['category'] ) && '' !== $atts['category'] ? sanitize_title( $atts['category'] ) : 'cam-nang';
	$n     = max( 1, min( 12, (int) ( isset( $atts['count'] ) ? $atts['count'] : 4 ) ) );
	$q     = dpv_posts_by_cat( $slug, $n );
	if ( ! $q->have_posts() ) {
		return dpv_empty_notice( __( 'Khối "Bài viết": chưa có bài trong chuyên mục đã chọn nên ngoài web sẽ không hiện.', 'dpv-theme' ) );
	}
	$id = 'news-' . sanitize_html_class( $slug );
	ob_start();
	?>
	<section class="container block" aria-labelledby="<?php echo esc_attr( $id ); ?>">
		<div class="block__head">
			<h2 class="section-title section-title--left" id="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $title ); ?></h2>
			<a class="more" href="<?php echo esc_url( dpv_cat_link( $slug ) ); ?>"><?php esc_html_e( 'Xem thêm', 'dpv-theme' ); ?></a>
		</div>
		<div class="grid grid--4">
			<?php while ( $q->have_posts() ) : $q->the_post(); dpv_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}

/* ---------------------------------------------------------------------------
 * Nội dung mặc định của trang chủ (dạng markup khối)
 * ------------------------------------------------------------------------- */
function dpv_block_comment( $name, $atts ) {
	return '<!-- wp:' . $name . ' ' . wp_json_encode( $atts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . ' /-->';
}

function dpv_default_home_content() {
	$defaults = dpv_default_product_cats();
	$slugs    = array_filter( array_map( 'sanitize_title', explode( ',', (string) dpv_opt( 'dpv_home_cats', 'chong-tham,bot-tret-tuong,keo-dan-gach,tam-cemboard' ) ) ) );

	$out   = array();
	$out[] = dpv_block_comment( 'dpv/hero', array( 'quoteCategory' => dpv_opt( 'dpv_cat_quote', 'bang-bao-gia' ), 'quoteCount' => 3 ) );
	$out[] = dpv_block_comment( 'dpv/projects', array( 'title' => 'Các dự án hoàn thiện', 'category' => dpv_opt( 'dpv_cat_projects', 'du-an' ), 'count' => 3 ) );
	foreach ( $slugs as $slug ) {
		$term  = taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $slug, 'product_cat' ) : false;
		$label = $term && ! is_wp_error( $term ) ? $term->name : ( isset( $defaults[ $slug ] ) ? $defaults[ $slug ] : ucwords( str_replace( '-', ' ', $slug ) ) );
		$out[] = dpv_block_comment( 'dpv/products', array( 'title' => html_entity_decode( $label, ENT_QUOTES, 'UTF-8' ), 'category' => $slug, 'count' => 8 ) );
	}

	// Giới thiệu: H1 + đoạn văn (khối có sẵn của WordPress).
	$h1    = dpv_opt( 'dpv_home_h1' ) ?: get_bloginfo( 'name' );
	$intro = dpv_opt( 'dpv_home_intro' );
	$paras = array();
	if ( $intro ) {
		foreach ( preg_split( '/\R{2,}/', trim( wp_strip_all_tags( $intro ) ) ) as $p ) {
			$p = trim( $p );
			if ( '' !== $p ) {
				$paras[] = $p;
			}
		}
	}
	if ( ! $paras ) {
		$paras[] = 'Nhập đoạn giới thiệu 150–300 từ, có từ khóa chính và liên kết nội bộ tới các danh mục quan trọng. Bạn có thể xóa hoặc sửa đoạn này ngay trong trình soạn thảo.';
	}
	$intro_html  = '<!-- wp:group {"className":"intro entry-content"} -->' . "\n" . '<div class="wp-block-group intro entry-content">';
	$intro_html .= "\n" . '<!-- wp:heading {"textAlign":"center","level":1} -->' . "\n" . '<h1 class="wp-block-heading has-text-align-center">' . esc_html( $h1 ) . '</h1>' . "\n" . '<!-- /wp:heading -->';
	foreach ( $paras as $p ) {
		$intro_html .= "\n\n" . '<!-- wp:paragraph -->' . "\n" . '<p>' . esc_html( $p ) . '</p>' . "\n" . '<!-- /wp:paragraph -->';
	}
	$intro_html .= "\n" . '</div>' . "\n" . '<!-- /wp:group -->';
	$out[]       = $intro_html;

	$out[] = dpv_block_comment( 'dpv/posts', array( 'title' => 'Cẩm nang xây nhà', 'category' => dpv_opt( 'dpv_cat_news', 'cam-nang' ), 'count' => 4 ) );

	return implode( "\n\n", $out );
}

/**
 * Lần đầu vào quản trị: nếu trang chủ tĩnh đang trống thì điền sẵn các khối để người dùng
 * thấy và sửa được ngay. Chỉ chạy một lần.
 */
function dpv_seed_home_page() {
	if ( get_option( 'dpv_home_seeded' ) || 'page' !== get_option( 'show_on_front' ) ) {
		return;
	}
	$pid  = (int) get_option( 'page_on_front' );
	$page = $pid ? get_post( $pid ) : null;
	if ( ! $page || 'page' !== $page->post_type ) {
		return;
	}
	if ( '' !== trim( wp_strip_all_tags( $page->post_content ) ) || has_blocks( $page->post_content ) ) {
		update_option( 'dpv_home_seeded', 1 );
		return;
	}
	wp_update_post( wp_slash( array( 'ID' => $pid, 'post_content' => dpv_default_home_content() ) ) );
	update_option( 'dpv_home_seeded', 1 );
}
add_action( 'admin_init', 'dpv_seed_home_page' );
