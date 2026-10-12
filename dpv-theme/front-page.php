<?php
/**
 * Trang chủ: danh mục + banner + góc thiết kế, dự án, khối sản phẩm, giới thiệu (H1), cẩm nang.
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/* ---------- Banner ---------- */
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

/* ---------- Dữ liệu ---------- */
$quote_slug = dpv_opt( 'dpv_cat_quote', 'bang-bao-gia' );
$quote_term = get_term_by( 'slug', $quote_slug, 'category' );
$quote_q    = dpv_posts_by_cat( $quote_slug, 3 );
$project_q  = dpv_posts_by_cat( dpv_opt( 'dpv_cat_projects', 'du-an' ), 3 );
$news_q     = dpv_posts_by_cat( dpv_opt( 'dpv_cat_news', 'cam-nang' ), 4 );
$home_h1    = dpv_opt( 'dpv_home_h1' ) ?: get_bloginfo( 'name' );
$home_intro = dpv_opt( 'dpv_home_intro' );
// Ưu tiên nội dung soạn trong trang "Trang chủ" (trình soạn thảo); không có thì dùng đoạn giới thiệu trong Tùy biến.
$home_page_content = '';
if ( is_page() ) {
	$home_post = get_queried_object();
	if ( $home_post instanceof WP_Post && '' !== trim( wp_strip_all_tags( $home_post->post_content ) ) ) {
		$home_page_content = apply_filters( 'the_content', $home_post->post_content );
	}
}
$cat_slugs  = array_filter( array_map( 'sanitize_title', explode( ',', (string) dpv_opt( 'dpv_home_cats', 'chong-tham,bot-tret-tuong,keo-dan-gach,tam-cemboard' ) ) ) );
$defaults   = dpv_default_product_cats();
?>
<main id="content" class="site-main home">

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
					<!-- Banner mặc định (chưa tải ảnh): thay bằng ảnh thật trong Customizer > Trang chủ -->
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

	<?php if ( $project_q->have_posts() ) : ?>
	<section class="container block" aria-labelledby="projects-title">
		<h2 class="section-title" id="projects-title"><?php esc_html_e( 'Các dự án hoàn thiện', 'dpv-theme' ); ?></h2>
		<div class="grid grid--3">
			<?php while ( $project_q->have_posts() ) : $project_q->the_post(); ?>
				<a class="project" href="<?php the_permalink(); ?>">
					<?php echo dpv_thumb( get_the_ID(), 'dpv-card' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span class="project__title"><?php the_title(); ?></span>
				</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</section>
	<?php endif; ?>

	<?php
	/* ---------- Khối sản phẩm theo danh mục ---------- */
	foreach ( $cat_slugs as $slug ) :
		$label = isset( $defaults[ $slug ] ) ? $defaults[ $slug ] : ucwords( str_replace( '-', ' ', $slug ) );
		$term  = ( taxonomy_exists( 'product_cat' ) ) ? get_term_by( 'slug', $slug, 'product_cat' ) : false;
		if ( $term && ! is_wp_error( $term ) ) {
			$label = $term->name;
		}
		$products = array();
		$q        = null;
		if ( $term && function_exists( 'wc_get_products' ) ) {
			$products = wc_get_products( array(
				'status'   => 'publish',
				'limit'    => 8,
				'category' => array( $slug ),
				'return'   => 'ids',
			) );
		} else {
			$q = dpv_posts_by_cat( $slug, 4 );
			if ( ! term_exists( $slug, 'category' ) ) {
				$q = null;
			}
		}
		if ( ! $products && ( ! $q || ! $q->have_posts() ) ) {
			continue;
		}
		?>
		<section class="container block" aria-labelledby="cat-<?php echo esc_attr( $slug ); ?>">
			<div class="block__head">
				<h2 class="section-title section-title--left" id="cat-<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></h2>
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
	<?php endforeach; ?>

	<?php /* ---------- Giới thiệu + H1 ---------- */ ?>
	<section class="container block intro" aria-labelledby="home-h1">
		<h1 class="intro__h1" id="home-h1"><?php echo esc_html( $home_h1 ); ?></h1>
		<div class="intro__body entry-content">
			<?php
			if ( $home_page_content ) {
				echo $home_page_content; // phpcs:ignore WordPress.Security.EscapeOutput -- đã qua bộ lọc the_content.
			} elseif ( $home_intro ) {
				echo wp_kses_post( wpautop( $home_intro ) );
			} else {
				echo '<p>' . esc_html__( 'Nhập đoạn giới thiệu (150–300 từ, có từ khóa chính) trong Giao diện › Tùy biến › Đại Phú Vinh – Trang chủ.', 'dpv-theme' ) . '</p>';
			}
			?>
		</div>
	</section>

	<?php if ( $news_q->have_posts() ) : ?>
	<section class="container block" aria-labelledby="news-title">
		<div class="block__head">
			<h2 class="section-title section-title--left" id="news-title"><?php esc_html_e( 'Cẩm nang xây nhà', 'dpv-theme' ); ?></h2>
			<a class="more" href="<?php echo esc_url( dpv_cat_link( dpv_opt( 'dpv_cat_news', 'cam-nang' ) ) ); ?>"><?php esc_html_e( 'Xem thêm', 'dpv-theme' ); ?></a>
		</div>
		<div class="grid grid--4">
			<?php while ( $news_q->have_posts() ) : $news_q->the_post(); dpv_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
		</div>
	</section>
	<?php endif; ?>

</main>
<?php
get_footer();
