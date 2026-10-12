<?php
/**
 * Kết quả tìm kiếm (noindex).
 *
 * @package dpv-theme
 */

get_header();
?>
<div class="container page-wrap">
	<main id="content" class="site-main">
		<?php dpv_breadcrumb(); ?>
		<header class="archive-head">
			<h1 class="archive-title">
				<?php
				/* translators: %s: từ khóa */
				printf( esc_html__( 'Kết quả cho: %s', 'dpv-theme' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
				?>
			</h1>
		</header>
		<?php if ( have_posts() ) : ?>
			<div class="grid grid--3">
				<?php while ( have_posts() ) : the_post(); dpv_card( get_the_ID() ); endwhile; ?>
			</div>
			<?php dpv_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Không tìm thấy kết quả. Hãy thử từ khóa khác.', 'dpv-theme' ); ?></p>
			<?php get_search_form(); ?>
		<?php endif; ?>
	</main>
</div>
<?php
get_footer();
