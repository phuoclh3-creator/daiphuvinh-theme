<?php
/**
 * Trang chủ.
 *
 * Nội dung lấy từ trang tĩnh "Trang chủ" (soạn bằng khối trong trình soạn thảo: thêm / xóa / đổi chỗ).
 * Nếu trang đó trống thì dùng bố cục mặc định (cùng các khối).
 *
 * @package dpv-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$home_content = '';
if ( is_page() ) {
	$home_post = get_queried_object();
	if ( $home_post instanceof WP_Post && '' !== trim( wp_strip_all_tags( $home_post->post_content ) . ( has_blocks( $home_post->post_content ) ? 'x' : '' ) ) ) {
		$home_content = apply_filters( 'the_content', $home_post->post_content );
	}
}
if ( '' === trim( $home_content ) ) {
	$home_content = do_blocks( dpv_default_home_content() );
}
?>
<main id="content" class="site-main home">
	<div class="home-content">
		<?php echo $home_content; // phpcs:ignore WordPress.Security.EscapeOutput -- đã qua the_content / do_blocks. ?>
	</div>
</main>
<?php
get_footer();
