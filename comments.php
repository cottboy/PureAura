<?php
/**
 * 评论区。
 *
 * @package PureAura
 */

if ( post_password_required() ) {
	return;
}
?>

<section id="comments" class="comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments__title">
			<?php
			$pureaura_count = get_comments_number();
			printf(
				/* translators: %s: 评论数量 */
				esc_html( _n( '%s 条评论', '%s 条评论', $pureaura_count, 'pureaura' ) ),
				esc_html( number_format_i18n( $pureaura_count ) )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'callback'    => 'pureaura_comment',
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 44,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => pureaura_icon( 'left', 16 ) . '<span>' . esc_html__( '较早', 'pureaura' ) . '</span>',
				'next_text' => '<span>' . esc_html__( '较新', 'pureaura' ) . '</span>' . pureaura_icon( 'right', 16 ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="comments__closed"><?php esc_html_e( '评论已关闭。', 'pureaura' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'          => __( '说点什么', 'pureaura' ),
			'title_reply_to'       => __( '回复 %s', 'pureaura' ),
			'cancel_reply_link'    => __( '取消回复', 'pureaura' ),
			'label_submit'         => __( '发布评论', 'pureaura' ),
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
			'class_form'           => 'comment-form',
			'class_submit'         => 'btn btn--accent',
			'comment_field'        => sprintf(
				'<p class="comment-form__field"><label class="comment-form__label" for="comment">%s</label><textarea id="comment" name="comment" rows="5" required></textarea></p>',
				esc_html__( '内容', 'pureaura' )
			),
		)
	);
	?>
</section>
