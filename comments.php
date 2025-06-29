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
        // 1. 获取当前文章的所有已批准评论
        $all_comments = get_comments(array(
            'post_id' => get_the_ID(),
            'status'  => 'approve',
        ));

        // 2. 准备两个数组，一个用于顶级评论，一个用于子评论（以父ID为键）
        $top_level_comments = array();
        $child_comments = array();
        if ($all_comments) {
            foreach ($all_comments as $comment) {
                if ($comment->comment_parent == 0) {
                    $top_level_comments[] = $comment;
                } else {
                    $child_comments[$comment->comment_parent][] = $comment;
                }
            }
        }
        
        // 恢复主题自定义的排序逻辑
        $comment_order = blog_get_comment_order(); // 从 functions.php 获取排序设置
        usort($top_level_comments, function($a, $b) use ($comment_order) {
            $time_a = strtotime($a->comment_date);
            $time_b = strtotime($b->comment_date);
            
            if ($comment_order === 'asc') {
                return $time_a - $time_b; // 最早在上
            } else {
                return $time_b - $time_a; // 最新在上 (默认)
            }
        });

        // 3. 定义一个递归函数来获取一个评论下的所有后代评论
        if (!function_exists('blog_get_all_comment_descendants')) {
            function blog_get_all_comment_descendants($comment_id, &$children_map, &$descendants) {
                if (isset($children_map[$comment_id])) {
                    foreach ($children_map[$comment_id] as $child) {
                        $descendants[] = $child;
                        blog_get_all_comment_descendants($child->comment_ID, $children_map, $descendants);
                    }
                }
            }
        }

        // 4. 遍历并显示顶级评论及其扁平化的后代评论
        if ($top_level_comments) {
            foreach ($top_level_comments as $top_comment) {
                // 为即将调用的回调函数设置全局 $comment 对象
                $GLOBALS['comment'] = $top_comment;

                // 使用回调函数显示顶级评论
                // 因为 wp_list_comments 不再被使用，我们需要手动模拟参数
                $args = array(
                    'style'       => 'ul',
                    'short_ping'  => true,
                    'avatar_size' => 53,
                    'max_depth'   => get_option('thread_comments_depth'), // 遵循WP设置
                    'has_children' => isset($child_comments[$top_comment->comment_ID])
                );
                blog_display_standard_comment($top_comment, $args, 1); // 顶级评论深度为1

                // 5. 获取这个顶级评论的所有后代评论
                $descendants = array();
                if (isset($child_comments[$top_comment->comment_ID])) {
                     blog_get_all_comment_descendants($top_comment->comment_ID, $child_comments, $descendants);
                }

                // 6. 如果有后代评论，按时间排序并显示它们
                if (!empty($descendants)) {
                    // 按评论日期升序（旧到新）排序
                    usort($descendants, function($a, $b) {
                        return strtotime($a->comment_date) - strtotime($b->comment_date);
                    });

                    // 开始子评论列表
                    echo '<ul class="children">';

                    foreach ($descendants as $child_comment) {
                        // 为每条回复设置全局 $comment 对象
                        $GLOBALS['comment'] = $child_comment;

                        // 在扁平化列表中，回复不能有自己的子列表，所以 'has_children' 总是 false
                        $child_args = $args;
                        $child_args['has_children'] = false; 
                        blog_display_standard_comment($child_comment, $child_args, 2); // 所有回复深度都为2，以获得相同的缩进
                        echo '</li>'; // 手动闭合 'li' 标签
                    }

                    // 结束子评论列表
                    echo '</ul>';
                }

                // 手动闭合顶级评论的 'li' 标签
                echo '</li>';
            }
            // 循环结束后，清理全局变量
            unset($GLOBALS['comment']);
        }
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