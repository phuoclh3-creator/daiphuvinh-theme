<?php
/**
 * Trang tĩnh.
 *
 * @package dpv-theme
 */

get_header();
?>
<div class="container page-wrap">
	<main id="content" class="site-main">
		<?php dpv_breadcrumb(); ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
				<header class="entry-header"><h1 class="entry-title"><?php the_title(); ?></h1></header>
				<div class="entry-content"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	</main>
</div>
<?php
get_footer();
