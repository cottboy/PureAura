<?php
/**
 * 评论模板
 */

// 如果帖子受密码保护且未输入密码，则不显示评论
if (post_password_required()) {
    return;
}
?>

<?php if (have_comments()) : ?>
<div id="comments" class="comments-area">
    <h2 class="comments-title">
        <?php
        $comment_count = get_comments_number();
        if ('1' === $comment_count) {
            echo esc_html__('1条评论', 'blog');
        } else {
            printf(
                /* translators: %1$s: comment count number. */
                esc_html(_nx('%1$s条评论', '%1$s条评论', $comment_count, 'comments title', 'blog')),
                number_format_i18n($comment_count)
            );
        }
        ?>
    </h2>

    <ul class="comment-list">
        <?php
        // 获取所有评论并重新组织结构为两级嵌套
        $comments = get_comments(array(
            'post_id' => get_the_ID(),
            'status' => 'approve',
            'order' => 'ASC'
        ));
        
        // 组织评论为两级结构
        $comment_structure = blog_organize_comments_two_level($comments);
        
        // 显示重新组织的评论
        blog_display_two_level_comments($comment_structure);
        ?>
    </ul>

    <?php
    the_comments_pagination(array(
        'prev_text' => '<span class="screen-reader-text">' . __('上一页', 'blog') . '</span>',
        'next_text' => '<span class="screen-reader-text">' . __('下一页', 'blog') . '</span>',
    ));
    ?>
</div><!-- #comments -->
<?php endif; ?>

<?php
// 如果评论已关闭且有评论，则显示提示
if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) :
?>
    <p class="no-comments"><?php _e('评论已关闭。', 'blog'); ?></p>
<?php elseif (comments_open()) : ?>
    <?php /* blog_comment_form(); // 暂时禁用自定义表单，使用默认表单测试 */ ?>
    <?php comment_form(); // 使用 WordPress 默认评论表单函数 ?>
<?php endif; ?> 