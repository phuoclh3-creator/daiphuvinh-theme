<?php
/**
 * Sidebar bài viết. Mặc định dùng sidebar tiếng Việt của theme (Bài viết mới + Chuyên mục);
 * bỏ chọn trong Tùy biến để dùng widget WordPress.
 *
 * @package dpv-theme
 */

$use_default = (bool) get_theme_mod( 'dpv_default_sidebar', true );

if ( ! $use_default && ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside class="sidebar" aria-label="<?php esc_attr_e( 'Thanh bên', 'dpv-theme' ); ?>">
	<?php if ( $use_default ) : ?>
		<section class="widget widget--recent">
			<h2 class="widget-title"><?php esc_html_e( 'Bài viết mới', 'dpv-theme' ); ?></h2>
			<ul class="widget-list">
				<?php
				$recent = new WP_Query( array(
					'post_type'           => 'post',
					'posts_per_page'      => 6,
					'post__not_in'        => is_singular() ? array( get_the_ID() ) : array(),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				) );
				while ( $recent->have_posts() ) :
					$recent->the_post();
					?>
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
		</section>
		<section class="widget widget--cats">
			<h2 class="widget-title"><?php esc_html_e( 'Chuyên mục', 'dpv-theme' ); ?></h2>
			<ul class="widget-list">
				<?php
				wp_list_categories( array(
					'title_li'   => '',
					'show_count' => true,
					'hide_empty' => true,
					'exclude'    => (int) get_option( 'default_category' ),
				) );
				?>
			</ul>
		</section>
	<?php else : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php endif; ?>
</aside>
