<?php
/**
 * Bài viết đơn: H1, ngày cập nhật, mục lục tự động, bài liên quan.
 *
 * @package dpv-theme
 */

get_header();
?>
<div class="container page-wrap has-sidebar">
	<main id="content" class="site-main">
		<?php dpv_breadcrumb(); ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<p class="entry-meta">
						<?php esc_html_e( 'Cập nhật:', 'dpv-theme' ); ?>
						<time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'd/m/Y' ) ); ?></time>
						<?php $pcat = dpv_primary_category( get_the_ID() ); if ( $pcat ) : ?>
							· <a href="<?php echo esc_url( get_category_link( $pcat ) ); ?>"><?php echo esc_html( $pcat->name ); ?></a>
						<?php endif; ?>
					</p>
				</header>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="entry-thumb"><?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<?php wp_link_pages(); ?>
				<?php the_tags( '<p class="entry-tags">' . esc_html__( 'Thẻ: ', 'dpv-theme' ), ', ', '</p>' ); ?>
			</article>

			<?php
			$related = new WP_Query( array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'post__not_in'        => array( get_the_ID() ),
				'category__in'        => wp_get_post_categories( get_the_ID() ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			) );
			if ( $related->have_posts() ) : ?>
				<section class="related" aria-labelledby="related-title">
					<h2 class="section-title section-title--left" id="related-title"><?php esc_html_e( 'Bài viết liên quan', 'dpv-theme' ); ?></h2>
					<div class="grid grid--3">
						<?php while ( $related->have_posts() ) : $related->the_post(); dpv_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( comments_open() || get_comments_number() ) { comments_template(); } ?>
		<?php endwhile; ?>
	</main>
	<?php get_sidebar(); ?>
</div>
<?php
get_footer();
