<?php
/**
 * Mẫu mặc định: danh sách bài viết.
 *
 * @package dpv-theme
 */

get_header();
?>
<div class="container page-wrap">
	<main id="content" class="site-main">
		<?php dpv_breadcrumb(); ?>
		<header class="archive-head">
			<h1 class="archive-title"><?php echo is_home() && ! is_front_page() ? esc_html( single_post_title( '', false ) ) : esc_html__( 'Tin tức & cẩm nang', 'dpv-theme' ); ?></h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3">
				<?php while ( have_posts() ) : the_post(); dpv_card( get_the_ID() ); endwhile; ?>
			</div>
			<?php dpv_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Chưa có bài viết.', 'dpv-theme' ); ?></p>
		<?php endif; ?>
	</main>
</div>
<?php
get_footer();
