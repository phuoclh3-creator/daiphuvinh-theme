<?php
/**
 * Trang 404: gợi ý link nội bộ thay vì ngõ cụt.
 *
 * @package dpv-theme
 */

get_header();
?>
<div class="container page-wrap">
	<main id="content" class="site-main notfound">
		<h1 class="archive-title"><?php esc_html_e( 'Không tìm thấy trang', 'dpv-theme' ); ?></h1>
		<p><?php esc_html_e( 'Trang bạn tìm có thể đã được đổi địa chỉ hoặc không còn tồn tại. Hãy tìm lại hoặc xem các mục bên dưới.', 'dpv-theme' ); ?></p>
		<?php get_search_form(); ?>
		<h2 class="section-title section-title--left"><?php esc_html_e( 'Danh mục sản phẩm', 'dpv-theme' ); ?></h2>
		<div class="notfound__cats"><?php dpv_product_menu(); ?></div>
		<h2 class="section-title section-title--left"><?php esc_html_e( 'Bài viết mới', 'dpv-theme' ); ?></h2>
		<div class="grid grid--3">
			<?php
			$q = new WP_Query( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
			while ( $q->have_posts() ) : $q->the_post(); dpv_card( get_the_ID() ); endwhile; wp_reset_postdata();
			?>
		</div>
	</main>
</div>
<?php
get_footer();
