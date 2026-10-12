<?php
/**
 * Danh mục, thẻ, lưu trữ.
 *
 * @package dpv-theme
 */

get_header();
$desc = get_the_archive_description();
?>
<div class="container page-wrap">
	<main id="content" class="site-main">
		<?php dpv_breadcrumb(); ?>
		<header class="archive-head">
			<h1 class="archive-title"><?php the_archive_title(); ?></h1>
			<?php if ( $desc && ! is_paged() ) : ?>
				<div class="archive-desc entry-content"><?php echo wp_kses_post( $desc ); ?></div>
			<?php endif; ?>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3">
				<?php $n = 0; while ( have_posts() ) : the_post(); dpv_card( get_the_ID(), $n++ < 1 ); endwhile; ?>
			</div>
			<?php dpv_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Chưa có bài viết trong mục này.', 'dpv-theme' ); ?></p>
		<?php endif; ?>
	</main>
</div>
<?php
get_footer();
