<?php
/**
 * Bình luận.
 *
 * @package dpv-theme
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="section-title section-title--left"><?php echo esc_html( get_comments_number() ); ?> <?php esc_html_e( 'bình luận', 'dpv-theme' ); ?></h2>
		<ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) ); ?></ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>
	<?php
	$commenter = wp_get_current_commenter();
	comment_form( array(
		'title_reply'          => __( 'Để lại bình luận', 'dpv-theme' ),
		'title_reply_to'       => __( 'Trả lời %s', 'dpv-theme' ),
		'cancel_reply_link'    => __( 'Hủy trả lời', 'dpv-theme' ),
		'label_submit'         => __( 'Gửi bình luận', 'dpv-theme' ),
		'comment_notes_before' => '<p class="comment-notes">' . esc_html__( 'Email của bạn sẽ không được hiển thị công khai. Các trường bắt buộc được đánh dấu *', 'dpv-theme' ) . '</p>',
		'comment_field'        => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Bình luận', 'dpv-theme' ) . ' *</label><textarea id="comment" name="comment" cols="45" rows="6" required></textarea></p>',
		'fields'               => array(
			'author' => '<p class="comment-form-author"><label for="author">' . esc_html__( 'Họ tên', 'dpv-theme' ) . ' *</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" required></p>',
			'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'dpv-theme' ) . ' *</label><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" required></p>',
			'url'    => '<p class="comment-form-url"><label for="url">' . esc_html__( 'Website', 'dpv-theme' ) . '</label><input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '"></p>',
			'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"> <label for="wp-comment-cookies-consent">' . esc_html__( 'Lưu tên, email và website của tôi cho lần bình luận sau.', 'dpv-theme' ) . '</label></p>',
		),
	) );
	?>
</section>
