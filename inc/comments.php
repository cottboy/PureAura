<?php
/**
 * 评论列表渲染。
 *
 * @package PureAura
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 单条评论。
 *
 * 说明：列表项的开标签由本函数输出，闭合标签交给 Walker_Comment 处理，
 * 这样嵌套回复才能正确落在父级 <li> 内。
 *
 * @param WP_Comment $comment 评论对象。
 * @param array      $args    参数。
 * @param int        $depth   层级。
 */
function pureaura_comment( $comment, $args, $depth ) {
	$pureaura_tag = 'div' === $args['style'] ? 'div' : 'li';
	?>
	<<?php echo esc_attr( $pureaura_tag ); ?> <?php comment_class( 'comment' ); ?> id="comment-<?php comment_ID(); ?>">
		<article class="comment__inner">
			<div class="comment__avatar">
				<?php echo get_avatar( $comment, 44, '', '', array( 'class' => 'comment__img' ) ); ?>
			</div>

			<div class="comment__main">
				<p class="comment__head">
					<span class="comment__name"><?php comment_author_link( $comment ); ?></span>
					<time class="comment__time" datetime="<?php comment_time( DATE_W3C ); ?>"><?php comment_time( 'Y年n月j日 H:i' ); ?></time>
					<?php if ( '0' === $comment->comment_approved ) : ?>
						<span class="comment__pending"><?php esc_html_e( '等待审核', 'pureaura' ); ?></span>
					<?php endif; ?>
				</p>

				<div class="comment__text">
					<?php
					if ( '0' === $comment->comment_approved ) {
						esc_html_e( '评论正在等待审核，通过后会显示在这里。', 'pureaura' );
					} else {
						comment_text( $comment );
					}
					?>
				</div>

				<div class="comment__foot">
					<?php
					comment_reply_link(
						array_merge(
							$args,
							array(
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'reply_text' => __( '回复', 'pureaura' ),
							)
						),
						$comment
					);
					?>
					<?php edit_comment_link( __( '编辑', 'pureaura' ), '<span class="comment__edit">', '</span>' ); ?>
				</div>
			</div>
		</article>
	<?php
}
