<?php
/**
 * Blog 主题功能
 */

if (!function_exists('blog_setup')) :
    function blog_setup() {
        // 添加标题标签支持
        add_theme_support('title-tag');
        
        // 添加特色图像支持
        add_theme_support('post-thumbnails');
        
        // 添加自定义logo支持
        add_theme_support('custom-logo', array(
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        ));
        
        // 添加自定义背景支持
        add_theme_support('custom-background', array(
            'default-color' => 'f9f9f9',
        ));
        
        // 添加自定义头部图像支持
        add_theme_support('custom-header', array(
            'default-image'      => get_template_directory_uri() . '/assets/images/website background.jpg',
            'width'              => 1920,
            'height'             => 1080,
            'flex-height'        => true,
            'flex-width'         => true,
            'uploads'            => true,
            'random-default'     => false,
            'header-text'        => true,
            'default-text-color' => 'fff',
        ));
        
        // 注册导航菜单
        register_nav_menus(array(
            'primary' => __('主导航', 'blog'),
        ));
        
        // 添加HTML5支持
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));
        
        // 添加文章形式支持
        add_theme_support('post-formats', array(
            'aside',        // 日志
            'gallery',      // 图片
            'link',         // 链接
            'image',        // 图片
            'quote',        // 引用
            'status',       // 状态
            'video',        // 视频
            'audio',        // 音频
            'chat'          // 聊天
        ));
        
        // 设置评论相关配置
        // 设置评论嵌套深度为较大值，允许无限回复
        update_option('thread_comments_depth', 999);
        
        // 启用嵌套评论
        if (!get_option('thread_comments')) {
            update_option('thread_comments', 1);
        }
    }
endif;
add_action('after_setup_theme', 'blog_setup');

// 注册侧边栏
function blog_widgets_init() {
    register_sidebar(array(
        'name'          => __('主侧边栏', 'blog'),
        'id'            => 'sidebar-1',
        'description'   => __('添加小工具到侧边栏.', 'blog'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'blog_widgets_init');

// 加载脚本和样式
function blog_scripts() {
    // 主样式表
    wp_enqueue_style('blog-style', get_stylesheet_uri(), array(), '1.0.0');
    
    // 添加评论回复脚本
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    
    // 搜索安全脚本
    wp_enqueue_script('blog-search-security', get_template_directory_uri() . '/js/search-security.js', array(), '1.0.0', true);
    
    wp_enqueue_script('blog-scripts', get_template_directory_uri() . '/js/scripts.js', array('jquery'), '1.0', true);
    
    // 加载海报轮播脚本
    if (is_front_page()) {
        wp_enqueue_script('poster-slider', get_template_directory_uri() . '/js/poster-slider.js', array(), '1.0', true);
    }
    
    // 加载图片灯箱脚本 - 仅在单篇文章页面加载
    if (is_singular('post')) {
        wp_enqueue_script('image-lightbox', get_template_directory_uri() . '/js/image-lightbox.js', array(), '1.0', true);
    }
    
    // 禁用WordPress内置的lightbox功能
    wp_dequeue_script('wp-block-image-view');
    wp_dequeue_style('wp-block-image');
    
    // 如果是文章或页面且评论开启，加载评论回复脚本
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
        
        // 传递评论参数到前端JavaScript
        wp_localize_script('comment-reply', 'commentReplyParams', array(
            'max_depth' => 999 // 设置最大深度为很大的值，允许无限回复
        ));
        
        // 加载评论修复脚本 - 暂时禁用以修复嵌套回复问题
        // wp_enqueue_script('comment-fix', get_template_directory_uri() . '/js/comment-fix.js', array('jquery', 'comment-reply'), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'blog_scripts');

// 自定义摘要长度 - 移除字数限制，让CSS的line-clamp控制显示
function blog_excerpt_length($length) {
    return 999; // 设置一个很大的数值，让CSS的line-clamp来控制显示行数
}
add_filter('excerpt_length', 'blog_excerpt_length');

// 自定义摘要末尾符号
function blog_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'blog_excerpt_more');

// 添加编辑器样式支持
add_editor_style();

// 自定义评论回调函数
function blog_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    
    // 恢复获取被回复者信息的逻辑
    $comment_parent = $comment->comment_parent;
    $is_reply = $comment_parent > 0;
    $parent_author = '';
    
    if ($is_reply) {
        $parent_comment = get_comment($comment_parent);
        if ($parent_comment) {
            $parent_author = $parent_comment->comment_author;
        }
    }
    
    // $is_reply 的判断已经包含在上面，无需重复
    // $is_reply = $comment->comment_parent > 0;
    
    ?>
    <li <?php comment_class($is_reply ? 'comment-reply-item' : ''); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment">
            <?php // 新增: 包裹头像、用户名和元数据的 div ?>
            <div class="comment-header"> 
                <?php 
                // 头像现在是 .comment-header 的直接子元素，尺寸调整为53px以匹配CSS
                echo get_avatar($comment, 53, '', '', array('class' => 'comment-avatar'));
                ?>
                <?php // 新增: 包裹用户名和元数据的 div ?>
                <div class="comment-author-meta-details">
            <div class="comment-author">
                        <h4>
                            <?php 
                            // 自定义评论作者链接输出，支持新标签页打开和nofollow
                            $author_name = get_comment_author();
                            $author_url = get_comment_author_url();
                            
                            if (!empty($author_url) && $author_url !== 'http://') {
                                // 有网站URL时，输出带链接的作者名（新标签页打开，nofollow）
                                echo '<a href="' . esc_url($author_url) . '" target="_blank" rel="nofollow noopener">' . esc_html($author_name) . '</a>';
                            } else {
                                // 没有网站URL时，只输出作者名
                                echo esc_html($author_name);
                            }
                            
                            // 检测并显示用户角色标志（仅限登录用户）
                            if ($comment->user_id > 0) {
                                // 只有当评论者是登录的WordPress用户时才显示角色标志
                                $user = get_userdata($comment->user_id);
                                if ($user) {
                                    $user_roles = $user->roles;
                                    if (!empty($user_roles)) {
                                        $role = $user_roles[0]; // 获取主要角色
                                        $role_badge = '';
                                        
                                        switch ($role) {
                                            case 'administrator':
                                                $role_badge = '<span class="user-role-badge admin-badge">管理员</span>';
                                                break;
                                            case 'editor':
                                                $role_badge = '<span class="user-role-badge editor-badge">编辑</span>';
                                                break;
                                            case 'author':
                                                $role_badge = '<span class="user-role-badge author-badge">作者</span>';
                                                break;
                                            case 'contributor':
                                                $role_badge = '<span class="user-role-badge contributor-badge">贡献者</span>';
                                                break;
                                        }
                                        
                                        echo $role_badge;
                                    }
                                }
                            }
                            ?>
                            <?php if ($is_reply && $parent_author) : // 如果是回复，在作者名后显示回复对象 ?>
                                <span class="reply-to-inline">
                                    回复 <span class="replied-author-name"><?php echo esc_html($parent_author); ?></span>
                                </span>
            <?php endif; ?>
                        </h4>
            </div>
            
            <div class="comment-metadata">
                <time datetime="<?php comment_time('c'); ?>">
                            <?php echo get_comment_date(get_option('date_format')); /* 只显示日期，不显示时间 */ ?>
                </time>
                <?php edit_comment_link(__('编辑', 'blog'), ' <span class="edit-link">', '</span>'); ?>
            </div>
                </div> <?php // 结束 .comment-author-meta-details ?>
            </div> <?php // 结束 .comment-header ?>

            <div class="comment-content-wrapper">
                <div class="comment-content">
                    <?php if ($comment->comment_approved == '0') : ?>
                        <p><em><?php _e('您的评论正在等待审核。', 'blog'); ?></em></p>
                    <?php endif; ?>
                    <?php comment_text(); ?>
                </div>
                
                <div class="reply">
                    <?php 
                    // 为所有评论设置相同的回复参数，确保都可以被回复
                    // 不管在显示中是第几级，逻辑上都视为可以回复
                    $reply_args = array(
                        'depth' => 1, // 始终设置为1，这样所有评论都可以被回复
                        'max_depth' => 999, // 设置一个很大的数值，确保不会因为深度限制而无法回复
                        'reply_text' => __('回复', 'blog')
                    );
                    
                    comment_reply_link($reply_args); 
                    ?>
                </div>
            </div>
        </article>
    <?php
}

// 自定义分页函数
function blog_pagination() {
    global $wp_query;
    
    // 确保有多个页面才显示分页
    if ($wp_query->max_num_pages <= 1) {
        return;
    }
    
    $current = max(1, get_query_var('paged'));
    $total = intval($wp_query->max_num_pages);
    
    $links = array();
    
    // 上一页按钮
    if ($current > 1) {
        $links[] = '<a class="page-numbers prev" href="' . get_pagenum_link($current - 1) . '">' . __('上一页', 'blog') . '</a>';
    } else {
        $links[] = '<span class="page-numbers prev disabled">' . __('上一页', 'blog') . '</span>';
    }
    
    // 确定起始和结束页
    if ($total <= 7) {
        // 如果总页数少于或等于7，显示所有页码
        $start = 1;
        $end = $total;
    } else {
        // 复杂的情况，需要显示省略号
        $start = max(1, $current - 2);
        $end = min($total, $current + 2);
        
        // 调整以确保我们至少显示7个页码项（包括首尾和省略号）
        if ($start == 1) {
            $end = 5;
        } elseif ($end == $total) {
            $start = $total - 4;
        }
    }
    
    // 确保总是显示第一页
    if ($start > 1) {
        $links[] = '<a class="page-numbers" href="' . get_pagenum_link(1) . '">1</a>';
        // 如果不是紧跟第一页，加入省略号
        if ($start > 2) {
            $links[] = '<span class="page-numbers dots">...</span>';
        }
    }
    
    // 添加中间页码
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current) {
            $links[] = '<span class="page-numbers current">' . $i . '</span>';
        } else {
            $links[] = '<a class="page-numbers" href="' . get_pagenum_link($i) . '">' . $i . '</a>';
        }
    }
    
    // 确保总是显示最后一页
    if ($end < $total) {
        // 如果不是紧跟最后一页，加入省略号
        if ($end < $total - 1) {
            $links[] = '<span class="page-numbers dots">...</span>';
        }
        $links[] = '<a class="page-numbers" href="' . get_pagenum_link($total) . '">' . $total . '</a>';
    }
    
    // 下一页按钮
    if ($current < $total) {
        $links[] = '<a class="page-numbers next" href="' . get_pagenum_link($current + 1) . '">' . __('下一页', 'blog') . '</a>';
    } else {
        $links[] = '<span class="page-numbers next disabled">' . __('下一页', 'blog') . '</span>';
    }
    
    // 输出分页HTML
    echo '<nav class="pagination">' . implode('', $links) . '</nav>';
}

// 获取文章的第一张图片作为缩略图
function blog_get_first_image() {
    global $post;
    $first_img = '';
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    
    if (isset($matches[1][0])) {
        $first_img = $matches[1][0];
    }
    
    if (empty($first_img)) {
        // 如果没有找到图片，返回默认图片
        $first_img = get_template_directory_uri() . '/assets/images/default-thumbnail.jpg';
    }
    
    return $first_img;
}

// 添加自定义类到菜单项
function blog_menu_item_classes($classes, $item, $args) {
    if (in_array('menu-item-has-children', $classes)) {
        $classes[] = 'dropdown';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'blog_menu_item_classes', 10, 3);

/**
 * 搜索处理 - 防止XSS和SQL注入
 */

// 1. 搜索输入过滤和验证
function blog_sanitize_search_input($search_term) {
    // 移除HTML标签
    $search_term = strip_tags($search_term);
    
    // 移除多余的空白字符
    $search_term = trim($search_term);
    
    // 限制搜索词长度，防止过长的搜索词（按实际字符数计算，支持中英文）
    $max_length = 200; // 200个字符（无论中文还是英文都按字符数计算）
    if (mb_strlen($search_term, 'UTF-8') > $max_length) {
        $search_term = mb_substr($search_term, 0, $max_length, 'UTF-8');
    }
    
    // 移除潜在的恶意字符
    $search_term = preg_replace('/[<>"\']/', '', $search_term);
    
    // 移除SQL注入相关的关键词
    $dangerous_patterns = array(
        '/\bunion\b/i',
        '/\bselect\b/i',
        '/\binsert\b/i',
        '/\bupdate\b/i',
        '/\bdelete\b/i',
        '/\bdrop\b/i',
        '/\balter\b/i',
        '/\bexec\b/i',
        '/\bscript\b/i',
        '/\balert\b/i',
        '/javascript:/i',
        '/vbscript:/i',
        '/data:/i'
    );
    
    foreach ($dangerous_patterns as $pattern) {
        $search_term = preg_replace($pattern, '', $search_term);
        }
    
    return $search_term;
}

// 2. 搜索频率限制（防止暴力搜索）
function blog_check_search_rate_limit() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $current_time = time();
    
    // 从后台设置获取搜索限制
    $search_limit_minute = get_option('blog_search_limit_minute', 10); // 默认每分钟10次
    $search_limit_daily = get_option('blog_search_limit_daily', 300);   // 默认每天300次
    $time_window_minute = 60; // 时间窗口60秒
    $time_window_daily = 24 * 60 * 60; // 时间窗口24小时
    
    // 获取IP的每分钟搜索记录
    $search_log_minute = get_transient('search_log_minute_' . md5($ip));
    if (!$search_log_minute) {
        $search_log_minute = array();
    }

    // 获取IP的每日搜索记录
    $search_log_daily = get_transient('search_log_daily_' . md5($ip));
    if (!$search_log_daily) {
        $search_log_daily = array();
    }
    
    // 清理过期的每分钟搜索记录
    $search_log_minute = array_filter($search_log_minute, function($timestamp) use ($current_time, $time_window_minute) {
        return ($current_time - $timestamp) < $time_window_minute;
    });
    
    // 清理过期的每日搜索记录
    $search_log_daily = array_filter($search_log_daily, function($timestamp) use ($current_time, $time_window_daily) {
        return ($current_time - $timestamp) < $time_window_daily;
    });
    
    // 检查是否超过每分钟限制
    if (count($search_log_minute) >= $search_limit_minute) {
        return false; // 超过每分钟限制
    }
    
    // 检查是否超过每日限制
    if (count($search_log_daily) >= $search_limit_daily) {
        return false; // 超过每日限制
    }
    
    // 记录当前搜索
    $search_log_minute[] = $current_time;
    $search_log_daily[] = $current_time;

    // 更新缓存
    set_transient('search_log_minute_' . md5($ip), $search_log_minute, $time_window_minute);
    set_transient('search_log_daily_' . md5($ip), $search_log_daily, $time_window_daily);
    
    return true; // 允许搜索
}

// 3. 处理搜索请求
function blog_secure_search_handler($wp_query) {
    // 只在前端搜索时执行
    if (is_admin() || !$wp_query->is_main_query() || !is_search()) {
        return;
    }

    // 检查是否有搜索词
    if (!isset($_GET['s']) || empty($_GET['s'])) {
        return;
    }
    

    
    // 验证搜索频率限制
    if (!blog_check_search_rate_limit()) {
        wp_die(__('搜索过于频繁，请稍后再试。', 'blog'), __('搜索限制', 'blog'), array('response' => 429));
        return;
    }

    // 清理和验证搜索词
    $search_term = blog_sanitize_search_input($_GET['s']);
    
    // 如果搜索词为空，不进行任何处理（应该不会发生，因为前端已阻止）
    if (empty($search_term)) {
        return;
    }
    
    // 更新查询变量
    $wp_query->set('s', $search_term);
}
add_action('pre_get_posts', 'blog_secure_search_handler', 5);

// 4. 安全输出搜索词（防止XSS）
function blog_safe_search_query() {
    $search_query = get_search_query();
    
    // 双重转义确保安全
    $search_query = esc_html($search_query);
    $search_query = esc_attr($search_query);
    
    return $search_query;
}

// 5. 搜索结果标题安全输出
function blog_safe_search_title() {
    $search_query = blog_safe_search_query();
    
    if (!empty($search_query)) {
        return sprintf(__('搜索 %s', 'blog'), '<span class="search-term">' . $search_query . '</span>');
    }
    
    return __('搜索结果', 'blog');
}

/**
 * 修改搜索查询，只搜索文章标题（增强安全版本）
 */
function blog_search_by_title_only($search, $wp_query) {
    if (empty($search)) {
        return $search; // 如果搜索为空，返回原始搜索
    }
    
    global $wpdb;
    
    // 确保这是一个搜索查询
    if (!isset($wp_query->query_vars['s'])) {
        return $search;
    }
    
    // 获取搜索词并进行安全处理
    $search_term = $wp_query->query_vars['s'];
    
    // 使用新的安全处理函数
    $search_term = blog_sanitize_search_input($search_term);
    
    // 如果搜索词为空，返回原始搜索让WordPress自然处理
    if (empty($search_term)) {
        return $search;
    }
    
    // 使用WordPress的安全函数进行SQL转义
    $search_term = $wpdb->esc_like($search_term);
    $search_term = '%' . $search_term . '%';
    
    // 移除原始的搜索条件
    $search = '';
    
    // 添加只搜索标题的条件，使用预处理语句防止SQL注入
    $search .= $wpdb->prepare(" AND (({$wpdb->posts}.post_title LIKE %s))", $search_term);
    
    return $search;
}
add_filter('posts_search', 'blog_search_by_title_only', 10, 2); 

/**
 * 组织评论为两级嵌套结构
 * 将所有深层嵌套的回复都归类到顶级评论下
 */
function blog_organize_comments_two_level($comments) {
    $organized = array();
    $top_level = array();
    $replies = array();
    
    // 先分离顶级评论和回复
    foreach ($comments as $comment) {
        if ($comment->comment_parent == 0) {
            $top_level[] = $comment;
        } else {
            // 找到这个回复的最顶级父评论
            $root_parent = blog_find_root_parent($comment, $comments);
            if (!isset($replies[$root_parent])) {
                $replies[$root_parent] = array();
            }
            $replies[$root_parent][] = $comment;
        }
    }
    
    // 组织最终结构
    foreach ($top_level as $parent_comment) {
        $parent_id = $parent_comment->comment_ID;
        $organized[] = array(
            'comment' => $parent_comment,
            'replies' => isset($replies[$parent_id]) ? $replies[$parent_id] : array()
        );
    }
    
    return $organized;
}

/**
 * 找到评论的根父评论ID
 */
function blog_find_root_parent($comment, $all_comments) {
    $parent_id = $comment->comment_parent;
    
    // 如果没有父评论，返回自己的ID
    if ($parent_id == 0) {
        return $comment->comment_ID;
    }
    
    // 查找父评论
    foreach ($all_comments as $potential_parent) {
        if ($potential_parent->comment_ID == $parent_id) {
            // 如果父评论还有父评论，继续向上查找
            if ($potential_parent->comment_parent != 0) {
                return blog_find_root_parent($potential_parent, $all_comments);
            } else {
                // 找到根父评论
                return $potential_parent->comment_ID;
            }
        }
    }
    
    return $parent_id;
}

/**
 * 显示两级嵌套的评论结构
 */
function blog_display_two_level_comments($comment_structure) {
    foreach ($comment_structure as $comment_group) {
        $parent_comment = $comment_group['comment'];
        $replies = $comment_group['replies'];
        
        // 显示父评论
        blog_display_single_comment($parent_comment, false);
        
        // 如果有回复，显示回复列表
        if (!empty($replies)) {
            echo '<ul class="children">';
            foreach ($replies as $reply) {
                blog_display_single_comment($reply, true);
            }
            echo '</ul>';
        }
        
        echo '</li>'; // 关闭父评论的 li 标签
    }
}

/**
 * 显示单个评论
 */
function blog_display_single_comment($comment, $is_reply = false) {
    $GLOBALS['comment'] = $comment;
    
    // 获取直接父评论的作者信息（用于显示回复对象）
    $parent_author = '';
    if ($is_reply && $comment->comment_parent > 0) {
        $parent_comment = get_comment($comment->comment_parent);
        if ($parent_comment) {
            $parent_author = $parent_comment->comment_author;
        }
    }
    ?>
    <li <?php comment_class($is_reply ? 'comment-reply-item' : ''); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment">
            <div class="comment-header"> 
                <?php 
                echo get_avatar($comment, 53, '', '', array('class' => 'comment-avatar'));
                ?>
                <div class="comment-author-meta-details">
                    <div class="comment-author">
                        <h4>
                            <?php 
                            // 自定义评论作者链接输出，支持新标签页打开和nofollow
                            $author_name = get_comment_author();
                            $author_url = get_comment_author_url();
                            
                            if (!empty($author_url) && $author_url !== 'http://') {
                                // 有网站URL时，输出带链接的作者名（新标签页打开，nofollow）
                                echo '<a href="' . esc_url($author_url) . '" target="_blank" rel="nofollow noopener">' . esc_html($author_name) . '</a>';
                            } else {
                                // 没有网站URL时，只输出作者名
                                echo esc_html($author_name);
                            }
                            
                            // 检测并显示用户角色标志（仅限登录用户）
                            if ($comment->user_id > 0) {
                                // 只有当评论者是登录的WordPress用户时才显示角色标志
                                $user = get_userdata($comment->user_id);
                                if ($user) {
                                    $user_roles = $user->roles;
                                    if (!empty($user_roles)) {
                                        $role = $user_roles[0]; // 获取主要角色
                                        $role_badge = '';
                                        
                                        switch ($role) {
                                            case 'administrator':
                                                $role_badge = '<span class="user-role-badge admin-badge">管理员</span>';
                                                break;
                                            case 'editor':
                                                $role_badge = '<span class="user-role-badge editor-badge">编辑</span>';
                                                break;
                                            case 'author':
                                                $role_badge = '<span class="user-role-badge author-badge">作者</span>';
                                                break;
                                            case 'contributor':
                                                $role_badge = '<span class="user-role-badge contributor-badge">贡献者</span>';
                                                break;
                                        }
                                        
                                        echo $role_badge;
                                    }
                                }
                            }
                            ?>
                            <?php if ($is_reply && $parent_author) : // 如果是回复，在作者名后显示回复对象 ?>
                                <span class="reply-to-inline">
                                    回复 <span class="replied-author-name"><?php echo esc_html($parent_author); ?></span>
                                </span>
                            <?php endif; ?>
                        </h4>
                    </div>
                    
                    <div class="comment-metadata">
                        <time datetime="<?php comment_time('c'); ?>">
                            <?php echo get_comment_date(get_option('date_format')); ?>
                        </time>
                        <?php edit_comment_link(__('编辑', 'blog'), ' <span class="edit-link">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="comment-content-wrapper">
                <div class="comment-content">
                    <?php if ($comment->comment_approved == '0') : ?>
                        <p><em><?php _e('您的评论正在等待审核。', 'blog'); ?></em></p>
                    <?php endif; ?>
                    <?php comment_text(); ?>
                </div>
                
                <div class="reply">
                    <?php 
                    // 为所有评论设置相同的回复参数，确保都可以被回复
                    // 不管在显示中是第几级，逻辑上都视为可以回复
                    $reply_args = array(
                        'depth' => 1, // 始终设置为1，这样所有评论都可以被回复
                        'max_depth' => 999, // 设置一个很大的数值，确保不会因为深度限制而无法回复
                        'reply_text' => __('回复', 'blog')
                    );
                    
                    comment_reply_link($reply_args); 
                    ?>
                </div>
            </div>
        </article>
    <?php
    // 注意：如果不是回复（即顶级评论），不在这里关闭 li 标签
    // li 标签将在 blog_display_two_level_comments 函数中关闭
    if ($is_reply) {
        echo '</li>';
    }
}

/**
 * 确保新提交的评论遵循两级嵌套规则
 * 如果回复的是第二级评论，将其重新指向第一级父评论
 */
function blog_force_two_level_comments($commentdata) {
    if (isset($commentdata['comment_parent']) && $commentdata['comment_parent'] > 0) {
        $parent_comment = get_comment($commentdata['comment_parent']);
        
        if ($parent_comment && $parent_comment->comment_parent > 0) {
            // 如果父评论也有父评论，说明这是第三级或更深的回复
            // 将其重新指向最顶级的父评论
            $root_parent_id = $parent_comment->comment_parent;
            $root_parent = get_comment($root_parent_id);
            
            // 确保找到真正的根父评论
            while ($root_parent && $root_parent->comment_parent > 0) {
                $root_parent_id = $root_parent->comment_parent;
                $root_parent = get_comment($root_parent_id);
            }
            
            $commentdata['comment_parent'] = $root_parent_id;
        }
    }
    
    return $commentdata;
}
add_filter('preprocess_comment', 'blog_force_two_level_comments');

/**
 * 修改评论表单的默认参数，支持无限深度回复
 */
function blog_comment_form_defaults($defaults) {
    $defaults['max_depth'] = 999;
    return $defaults;
}
add_filter('comment_form_defaults', 'blog_comment_form_defaults');

/**
 * 禁用WordPress内置的lightbox功能
 */
function blog_disable_wp_lightbox() {
    // 移除lightbox功能相关的脚本和样式
    wp_deregister_script('wp-block-image-view');
    wp_dequeue_script('wp-block-image-view');
    
    // 禁用图片区块的lightbox功能
    add_filter('render_block_core/image', function($block_content) {
        // 移除WordPress添加的lightbox相关属性
        $block_content = preg_replace('/data-wp-[^=]*="[^"]*"/i', '', $block_content);
        $block_content = preg_replace('/class="[^"]*wp-lightbox[^"]*"/i', '', $block_content);
        return $block_content;
    });
}
add_action('wp_enqueue_scripts', 'blog_disable_wp_lightbox', 100);
add_action('wp_head', 'blog_disable_wp_lightbox', 100);

/**
 * 完全移除WordPress的lightbox功能
 */
function blog_remove_lightbox_support() {
    remove_theme_support('wp-block-styles');
    // 移除lightbox功能
    add_filter('wp_theme_json_data_default', function($theme_json) {
        $data = $theme_json->get_data();
        if (isset($data['settings']['blocks']['core/image']['lightbox'])) {
            unset($data['settings']['blocks']['core/image']['lightbox']);
        }
        return new WP_Theme_JSON_Data($data, 'default');
    });
}
add_action('after_setup_theme', 'blog_remove_lightbox_support'); 

/**
 * 主题设置 - 添加后台菜单
 */
function blog_theme_settings_menu() {
    // 添加主菜单（直接指向海报设置）
    add_menu_page(
        __('主题设置', 'blog'),       // 页面标题
        __('主题设置', 'blog'),       // 菜单标题
        'manage_options',             // 权限
        'blog-poster-settings',       // 菜单slug（直接指向海报设置）
        'blog_theme_settings_page',   // 回调函数（海报设置页面）
        'dashicons-admin-settings',   // 使用设置图标
        81                            // 菜单位置（在设置菜单之后）
    );
    
    // 添加海报子菜单
    add_submenu_page(
        'blog-poster-settings',       // 父菜单slug（改为海报设置）
        __('海报设置', 'blog'),       // 页面标题
        __('海报', 'blog'),           // 菜单标题
        'manage_options',             // 权限
        'blog-poster-settings',       // 菜单slug
        'blog_theme_settings_page'    // 回调函数（原来的设置页面）
    );
    
    // 添加Logo子菜单
    add_submenu_page(
        'blog-poster-settings',       // 父菜单slug（改为海报设置）
        __('Logo设置', 'blog'),       // 页面标题
        __('Logo', 'blog'),           // 菜单标题
        'manage_options',             // 权限
        'blog-logo-settings',         // 菜单slug
        'blog_logo_settings_page'     // 回调函数
    );
    
    // 添加介绍子菜单
    add_submenu_page(
        'blog-poster-settings',       // 父菜单slug
        __('站点介绍设置', 'blog'),   // 页面标题
        __('介绍模块', 'blog'),       // 菜单标题
        'manage_options',             // 权限
        'blog-intro-settings',        // 菜单slug
        'blog_intro_settings_page'    // 回调函数
    );
    
    // 添加建站时间子菜单
    add_submenu_page(
        'blog-poster-settings',       // 父菜单slug
        __('建站时间设置', 'blog'),   // 页面标题
        __('建站时间', 'blog'),       // 菜单标题
        'manage_options',             // 权限
        'blog-site-info-settings',    // 菜单slug
        'blog_site_info_settings_page' // 回调函数
    );
    
    // 添加CC协议子菜单
    add_submenu_page(
        'blog-poster-settings',       // 父菜单slug
        __('CC协议设置', 'blog'),     // 页面标题
        __('CC', 'blog'),             // 菜单标题
        'manage_options',             // 权限
        'blog-cc-settings',           // 菜单slug
        'blog_cc_settings_page'       // 回调函数
    );
    
    // 添加声明子菜单
    add_submenu_page(
        'blog-poster-settings',       // 父菜单slug
        __('声明设置', 'blog'),       // 页面标题
        __('声明', 'blog'),           // 菜单标题
        'manage_options',             // 权限
        'blog-statement-settings',    // 菜单slug
        'blog_statement_settings_page' // 回调函数
    );
    
    // 添加主题色子菜单
    add_submenu_page(
        'blog-poster-settings',       // 父菜单slug
        __('主题色设置', 'blog'),     // 页面标题
        __('主题色', 'blog'),         // 菜单标题
        'manage_options',             // 权限
        'blog-theme-color-settings',  // 菜单slug
        'blog_theme_color_settings_page' // 回调函数
    );
    
    // 添加搜索限制子菜单
    add_submenu_page(
        'blog-poster-settings',       // 父菜单slug
        __('搜索限制设置', 'blog'),   // 页面标题
        __('搜索限制', 'blog'),       // 菜单标题
        'manage_options',             // 权限
        'blog-search-limit-settings', // 菜单slug
        'blog_search_limit_settings_page' // 回调函数
    );
}
add_action('admin_menu', 'blog_theme_settings_menu');



/**
 * 主题设置页面
 */
function blog_theme_settings_page() {
    // 确保在当前页面加载媒体库脚本
    wp_enqueue_media();
    wp_enqueue_script('jquery');
    
    // 处理表单提交
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['blog_settings_nonce'], 'blog_settings_action')) {
        $poster_data = array();
        
        if (isset($_POST['blog_poster_images']) && isset($_POST['blog_poster_links'])) {
            $images = array_map('intval', $_POST['blog_poster_images']);
            $links = array_map('esc_url_raw', $_POST['blog_poster_links']);
            $new_tabs = isset($_POST['blog_poster_new_tabs']) ? $_POST['blog_poster_new_tabs'] : array();
            $nofollows = isset($_POST['blog_poster_nofollows']) ? $_POST['blog_poster_nofollows'] : array();
            
            $poster_index = 0; // 用于跟踪实际的图片索引
            for ($i = 0; $i < count($images); $i++) {
                if ($images[$i] > 0) { // 确保图片ID有效
                    $poster_data[] = array(
                        'image_id' => $images[$i],
                        'link_url' => isset($links[$i]) ? $links[$i] : '',
                        'open_new_tab' => isset($new_tabs[$poster_index]) ? 1 : 0,
                        'add_nofollow' => isset($nofollows[$poster_index]) ? 1 : 0
                    );
                    $poster_index++;
                }
            }
        }
        
        update_option('blog_poster_data', $poster_data);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('设置已保存！', 'blog') . '</p></div>';
    }
    
    // 获取当前设置
    $poster_data = get_option('blog_poster_data', array());
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blog_settings_action', 'blog_settings_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="blog_poster_images"><?php _e('首页海报图片', 'blog'); ?></label>
                    </th>
                    <td>
                        <div id="poster-images-container">
                            <?php if (!empty($poster_data)) : ?>
                                <?php foreach ($poster_data as $index => $item) : ?>
                                    <?php 
                                    $image_id = $item['image_id'];
                                    $link_url = $item['link_url'];
                                    $open_new_tab = isset($item['open_new_tab']) ? $item['open_new_tab'] : 0; // 默认为0（当前页面打开）
                                    $add_nofollow = isset($item['add_nofollow']) ? $item['add_nofollow'] : 0; // 默认为0（不添加nofollow）
                                    $image_url = wp_get_attachment_image_url($image_id, 'medium'); 
                                    ?>
                                    <?php if ($image_url) : ?>
                                        <div class="poster-image-item" data-index="<?php echo $index; ?>">
                                            <img src="<?php echo esc_url($image_url); ?>" alt="" style="max-width: 200px; height: auto; margin: 10px;">
                                            <input type="hidden" name="blog_poster_images[]" value="<?php echo $image_id; ?>">
                                            <br>
                                            <label style="display: block; margin: 10px 0 5px 0; font-weight: 500;">
                                                <?php _e('链接地址：', 'blog'); ?>
                                            </label>
                                            <input type="url" name="blog_poster_links[]" value="<?php echo esc_attr($link_url); ?>" 
                                                   placeholder="<?php _e('输入链接地址（可选）', 'blog'); ?>" 
                                                   style="width: 200px; padding: 5px; margin-bottom: 8px;">
                                            <br>
                                            <div style="margin: 5px 0;">
                                                <label style="display: block; margin-bottom: 3px;">
                                                    <input type="checkbox" name="blog_poster_new_tabs[<?php echo $index; ?>]" value="1" <?php checked($open_new_tab, 1); ?>>
                                                    <?php _e('在新标签页打开', 'blog'); ?>
                                                </label>
                                                <label style="display: block; margin-bottom: 3px;">
                                                    <input type="checkbox" name="blog_poster_nofollows[<?php echo $index; ?>]" value="1" <?php checked($add_nofollow, 1); ?>>
                                                    <?php _e('添加 nofollow 属性', 'blog'); ?>
                                                </label>
                                            </div>
                                            <br>
                                            <button type="button" class="button remove-poster-image"><?php _e('移除', 'blog'); ?></button>
                                            <button type="button" class="button change-poster-image"><?php _e('更换', 'blog'); ?></button>
                                            <span class="poster-image-order">
                                                <button type="button" class="button move-up" title="<?php _e('上移', 'blog'); ?>">↑</button>
                                                <button type="button" class="button move-down" title="<?php _e('下移', 'blog'); ?>">↓</button>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <p>
                            <button type="button" id="add-poster-image" class="button button-secondary">
                                <?php _e('添加海报图片', 'blog'); ?>
                            </button>
                        </p>
                        
                        <p class="description">
                            <?php _e('点击"添加海报图片"从媒体库选择图片。可以添加多张图片，它们将在首页轮播显示。每张图片都可以设置点击后跳转的链接地址（可选），并可单独设置是否在新标签页打开和是否添加nofollow属性。使用上移/下移按钮调整显示顺序。', 'blog'); ?>
                        </p>
                        <p class="description">
                            <strong><?php _e('关于 nofollow 属性：', 'blog'); ?></strong>
                            <?php _e('勾选此选项会告诉搜索引擎不要跟随该链接，通常用于广告链接、付费链接或不完全信任的外部链接。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <style>
        .poster-image-item {
            display: inline-block;
            vertical-align: top;
            margin: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
            text-align: center;
            position: relative;
        }
        
        .poster-image-item img {
            display: block;
            margin: 0 auto 10px;
            border-radius: 3px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .poster-image-item .button {
            margin: 2px;
            font-size: 12px;
        }
        
        .poster-image-order {
            display: block;
            margin-top: 5px;
        }
        
        .poster-image-order .button {
            padding: 2px 8px;
            min-height: auto;
            line-height: 1.2;
        }
        
        #add-poster-image {
            background: #0073aa;
            color: white;
            border-color: #006799;
        }
        
        #add-poster-image:hover {
            background: #005a87;
            border-color: #004f75;
        }
        </style>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // 媒体库选择器
        var mediaUploader;
        
        // 添加新图片
        $('#add-poster-image').click(function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: '<?php _e("选择海报图片", "blog"); ?>',
                button: {
                    text: '<?php _e("选择图片", "blog"); ?>'
                },
                multiple: true,
                library: {
                    type: 'image'
                }
            });
            
            mediaUploader.on('select', function() {
                var attachments = mediaUploader.state().get('selection').toJSON();
                
                attachments.forEach(function(attachment) {
                    var currentIndex = $('#poster-images-container .poster-image-item').length;
                    var itemHtml = '<div class="poster-image-item">' +
                        '<img src="' + attachment.sizes.medium.url + '" alt="" style="max-width: 200px; height: auto; margin: 10px;">' +
                        '<input type="hidden" name="blog_poster_images[]" value="' + attachment.id + '">' +
                        '<br>' +
                        '<label style="display: block; margin: 10px 0 5px 0; font-weight: 500;">' +
                        '<?php _e("链接地址：", "blog"); ?>' +
                        '</label>' +
                        '<input type="url" name="blog_poster_links[]" value="" placeholder="<?php _e("输入链接地址（可选）", "blog"); ?>" style="width: 200px; padding: 5px; margin-bottom: 8px;">' +
                        '<br>' +
                        '<div style="margin: 5px 0;">' +
                        '<label style="display: block; margin-bottom: 3px;">' +
                        '<input type="checkbox" name="blog_poster_new_tabs[' + currentIndex + ']" value="1">' +
                        '<?php _e("在新标签页打开", "blog"); ?>' +
                        '</label>' +
                        '<label style="display: block; margin-bottom: 3px;">' +
                        '<input type="checkbox" name="blog_poster_nofollows[' + currentIndex + ']" value="1">' +
                        '<?php _e("添加 nofollow 属性", "blog"); ?>' +
                        '</label>' +
                        '</div>' +
                        '<br>' +
                        '<button type="button" class="button remove-poster-image"><?php _e("移除", "blog"); ?></button>' +
                        '<button type="button" class="button change-poster-image"><?php _e("更换", "blog"); ?></button>' +
                        '<span class="poster-image-order">' +
                        '<button type="button" class="button move-up" title="<?php _e("上移", "blog"); ?>">↑</button>' +
                        '<button type="button" class="button move-down" title="<?php _e("下移", "blog"); ?>">↓</button>' +
                        '</span>' +
                        '</div>';
                    
                    $('#poster-images-container').append(itemHtml);
                });
            });
            
            mediaUploader.open();
        });
        
        // 移除图片
        $(document).on('click', '.remove-poster-image', function(e) {
            e.preventDefault();
            if (confirm('<?php _e("确定要移除这张图片吗？", "blog"); ?>')) {
                $(this).closest('.poster-image-item').remove();
            }
        });
        
        // 更换图片
        $(document).on('click', '.change-poster-image', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.poster-image-item');
            
            var changeUploader = wp.media({
                title: '<?php _e("更换海报图片", "blog"); ?>',
                button: {
                    text: '<?php _e("选择图片", "blog"); ?>'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            changeUploader.on('select', function() {
                var attachment = changeUploader.state().get('selection').first().toJSON();
                $item.find('img').attr('src', attachment.sizes.medium.url);
                $item.find('input[type="hidden"]').val(attachment.id);
            });
            
            changeUploader.open();
        });
        
        // 上移图片
        $(document).on('click', '.move-up', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.poster-image-item');
            var $prev = $item.prev('.poster-image-item');
            if ($prev.length) {
                $item.insertBefore($prev);
                updateCheckboxIndexes();
            }
        });
        
        // 下移图片
        $(document).on('click', '.move-down', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.poster-image-item');
            var $next = $item.next('.poster-image-item');
            if ($next.length) {
                $item.insertAfter($next);
                updateCheckboxIndexes();
            }
        });
        
        // 更新复选框的索引
        function updateCheckboxIndexes() {
            $('#poster-images-container .poster-image-item').each(function(index) {
                $(this).find('input[type="checkbox"][name^="blog_poster_new_tabs"]').attr('name', 'blog_poster_new_tabs[' + index + ']');
                $(this).find('input[type="checkbox"][name^="blog_poster_nofollows"]').attr('name', 'blog_poster_nofollows[' + index + ']');
            });
        }
    });
    </script>
    <?php
}

/**
 * Logo设置页面
 */
function blog_logo_settings_page() {
    // 确保在当前页面加载媒体库脚本
    wp_enqueue_media();
    wp_enqueue_script('jquery');
    
    // 处理表单提交
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['blog_logo_nonce'], 'blog_logo_action')) {
        $logo_id = isset($_POST['blog_logo_id']) ? intval($_POST['blog_logo_id']) : 0;
        update_option('blog_custom_logo_id', $logo_id);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Logo设置已保存！', 'blog') . '</p></div>';
    }
    
    // 获取当前设置
    $logo_id = get_option('blog_custom_logo_id', 0);
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blog_logo_action', 'blog_logo_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="blog_logo_id"><?php _e('网站Logo', 'blog'); ?></label>
                    </th>
                    <td>
                        <div id="logo-preview-container">
                            <?php if ($logo_url) : ?>
                                <div class="logo-preview-item">
                                    <img src="<?php echo esc_url($logo_url); ?>" alt="Logo预览" style="max-width: 300px; height: auto; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;">
                                    <input type="hidden" id="blog_logo_id" name="blog_logo_id" value="<?php echo $logo_id; ?>">
                                    <br>
                                    <button type="button" id="change-logo" class="button"><?php _e('更换Logo', 'blog'); ?></button>
                                    <button type="button" id="remove-logo" class="button"><?php _e('移除Logo', 'blog'); ?></button>
                                </div>
                            <?php else : ?>
                                <div class="no-logo-item">
                                    <p><?php _e('当前没有设置Logo', 'blog'); ?></p>
                                    <input type="hidden" id="blog_logo_id" name="blog_logo_id" value="0">
                                    <button type="button" id="select-logo" class="button button-primary"><?php _e('选择Logo', 'blog'); ?></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <p class="description">
                            <?php _e('选择一张图片作为网站Logo，建议使用PNG格式的透明背景图片，推荐尺寸：高度60-100像素。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <style>
        .logo-preview-item img {
            display: block;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .no-logo-item {
            padding: 20px;
            border: 2px dashed #ddd;
            border-radius: 4px;
            text-align: center;
            background: #f9f9f9;
        }
        
        .no-logo-item p {
            margin: 0 0 15px 0;
            color: #666;
        }
        </style>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // 检查wp.media是否可用
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            console.error('WordPress媒体库未加载');
            alert('媒体库未正确加载，请刷新页面重试');
            return;
        }
        
        var mediaUploader;
        
        // 选择Logo
        $(document).on('click', '#select-logo, #change-logo', function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: '<?php _e("选择Logo图片", "blog"); ?>',
                button: {
                    text: '<?php _e("选择Logo", "blog"); ?>'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                var previewHtml = '<div class="logo-preview-item">' +
                    '<img src="' + (attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url) + '" alt="Logo预览" style="max-width: 300px; height: auto; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;">' +
                    '<input type="hidden" id="blog_logo_id" name="blog_logo_id" value="' + attachment.id + '">' +
                    '<br>' +
                    '<button type="button" id="change-logo" class="button"><?php _e("更换Logo", "blog"); ?></button>' +
                    '<button type="button" id="remove-logo" class="button"><?php _e("移除Logo", "blog"); ?></button>' +
                    '</div>';
                
                $('#logo-preview-container').html(previewHtml);
            });
            
            mediaUploader.open();
        });
        
        // 移除Logo
        $(document).on('click', '#remove-logo', function(e) {
            e.preventDefault();
            if (confirm('<?php _e("确定要移除Logo吗？", "blog"); ?>')) {
                var noLogoHtml = '<div class="no-logo-item">' +
                    '<p><?php _e("当前没有设置Logo", "blog"); ?></p>' +
                    '<input type="hidden" id="blog_logo_id" name="blog_logo_id" value="0">' +
                    '<button type="button" id="select-logo" class="button button-primary"><?php _e("选择Logo", "blog"); ?></button>' +
                    '</div>';
                
                $('#logo-preview-container').html(noLogoHtml);
            }
        });
    });
    </script>
    <?php
}

/**
 * 站点介绍设置页面
 */
function blog_intro_settings_page() {
    // 确保在当前页面加载媒体库脚本
    wp_enqueue_media();
    wp_enqueue_script('jquery');
    
    // 处理表单提交
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['blog_intro_nonce'], 'blog_intro_action')) {
        $intro_image_id = isset($_POST['blog_intro_image_id']) ? intval($_POST['blog_intro_image_id']) : 0;
        $intro_name = isset($_POST['blog_intro_name']) ? sanitize_text_field($_POST['blog_intro_name']) : '';
        $intro_description = isset($_POST['blog_intro_description']) ? sanitize_textarea_field($_POST['blog_intro_description']) : '';
        
        update_option('blog_intro_image_id', $intro_image_id);
        update_option('blog_intro_name', $intro_name);
        update_option('blog_intro_description', $intro_description);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('站点介绍设置已保存！', 'blog') . '</p></div>';
    }
    
    // 获取当前设置
    $intro_image_id = get_option('blog_intro_image_id', 0);
    $intro_name = get_option('blog_intro_name', '');
    $intro_description = get_option('blog_intro_description', '');
    $intro_image_url = $intro_image_id ? wp_get_attachment_image_url($intro_image_id, 'medium') : '';
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blog_intro_action', 'blog_intro_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="blog_intro_image_id"><?php _e('介绍图片', 'blog'); ?></label>
                    </th>
                    <td>
                        <div id="intro-image-preview-container">
                            <?php if ($intro_image_url) : ?>
                                <div class="intro-image-preview-item">
                                    <img src="<?php echo esc_url($intro_image_url); ?>" alt="介绍图片预览" style="max-width: 200px; height: auto; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;">
                                    <input type="hidden" id="blog_intro_image_id" name="blog_intro_image_id" value="<?php echo $intro_image_id; ?>">
                                    <br>
                                    <button type="button" id="change-intro-image" class="button"><?php _e('更换图片', 'blog'); ?></button>
                                    <button type="button" id="remove-intro-image" class="button"><?php _e('移除图片', 'blog'); ?></button>
                                </div>
                            <?php else : ?>
                                <div class="no-intro-image-item">
                                    <p><?php _e('当前没有设置介绍图片', 'blog'); ?></p>
                                    <input type="hidden" id="blog_intro_image_id" name="blog_intro_image_id" value="0">
                                    <button type="button" id="select-intro-image" class="button button-primary"><?php _e('选择图片', 'blog'); ?></button>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <p class="description">
                            <?php _e('选择一张图片作为站点介绍的头像或图标，建议使用正方形图片，推荐尺寸：200x200像素。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="blog_intro_name"><?php _e('站点名称', 'blog'); ?></label>
                    </th>
                    <td>
                        <input type="text" id="blog_intro_name" name="blog_intro_name" value="<?php echo esc_attr($intro_name); ?>" class="regular-text" placeholder="<?php _e('输入站点名称', 'blog'); ?>">
                        <p class="description">
                            <?php _e('在侧边栏介绍模块中显示的站点名称。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="blog_intro_description"><?php _e('站点描述', 'blog'); ?></label>
                    </th>
                    <td>
                        <textarea id="blog_intro_description" name="blog_intro_description" rows="5" cols="50" class="large-text" placeholder="<?php _e('输入站点描述...', 'blog'); ?>"><?php echo esc_textarea($intro_description); ?></textarea>
                        <p class="description">
                            <?php _e('在侧边栏介绍模块中显示的站点描述，支持换行。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
        
        <style>
        .intro-image-preview-item img {
            display: block;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .no-intro-image-item {
            padding: 20px;
            border: 2px dashed #ddd;
            border-radius: 4px;
            text-align: center;
            background: #f9f9f9;
        }
        
        .no-intro-image-item p {
            margin: 0 0 15px 0;
            color: #666;
        }
        </style>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // 检查wp.media是否可用
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            console.error('WordPress媒体库未加载');
            alert('媒体库未正确加载，请刷新页面重试');
            return;
        }
        
        var mediaUploader;
        
        // 选择介绍图片
        $(document).on('click', '#select-intro-image, #change-intro-image', function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: '<?php _e("选择介绍图片", "blog"); ?>',
                button: {
                    text: '<?php _e("选择图片", "blog"); ?>'
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                
                var previewHtml = '<div class="intro-image-preview-item">' +
                    '<img src="' + (attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url) + '" alt="介绍图片预览" style="max-width: 200px; height: auto; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px;">' +
                    '<input type="hidden" id="blog_intro_image_id" name="blog_intro_image_id" value="' + attachment.id + '">' +
                    '<br>' +
                    '<button type="button" id="change-intro-image" class="button"><?php _e("更换图片", "blog"); ?></button>' +
                    '<button type="button" id="remove-intro-image" class="button"><?php _e("移除图片", "blog"); ?></button>' +
                    '</div>';
                
                $('#intro-image-preview-container').html(previewHtml);
            });
            
            mediaUploader.open();
        });
        
        // 移除介绍图片
        $(document).on('click', '#remove-intro-image', function(e) {
            e.preventDefault();
            if (confirm('<?php _e("确定要移除介绍图片吗？", "blog"); ?>')) {
                var noImageHtml = '<div class="no-intro-image-item">' +
                    '<p><?php _e("当前没有设置介绍图片", "blog"); ?></p>' +
                    '<input type="hidden" id="blog_intro_image_id" name="blog_intro_image_id" value="0">' +
                    '<button type="button" id="select-intro-image" class="button button-primary"><?php _e("选择图片", "blog"); ?></button>' +
                    '</div>';
                
                $('#intro-image-preview-container').html(noImageHtml);
            }
        });
    });
    </script>
    <?php
}

/**
 * 在管理后台加载媒体库脚本
 */
function blog_admin_enqueue_scripts($hook) {
    // 支持海报设置、Logo设置、介绍设置、站点信息设置、CC设置、主题色设置和搜索限制设置页面
    // 检查当前页面是否为我们的设置页面
    if (strpos($hook, 'blog-poster-settings') === false && 
        strpos($hook, 'blog-logo-settings') === false &&
        strpos($hook, 'blog-intro-settings') === false &&
        strpos($hook, 'blog-site-info-settings') === false &&
        strpos($hook, 'blog-cc-settings') === false &&
        strpos($hook, 'blog-theme-color-settings') === false &&
        strpos($hook, 'blog-search-limit-settings') === false) {
        return;
    }
    
    wp_enqueue_media();
    wp_enqueue_script('jquery');
    
    // 调试：显示当前hook名称
    if (current_user_can('manage_options')) {
        echo '<!-- Hook名称: ' . $hook . ' -->';
    }
}
add_action('admin_enqueue_scripts', 'blog_admin_enqueue_scripts');

/**
 * 获取主题设置的海报图片和链接数据
 */
function blog_get_poster_data() {
    $poster_data = get_option('blog_poster_data', array());
    $result = array();
    
    if (!empty($poster_data)) {
        foreach ($poster_data as $item) {
            $image_id = $item['image_id'];
            $link_url = $item['link_url'];
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            
            if ($image_url) {
                $result[] = array(
                    'image_url' => $image_url,
                    'link_url' => $link_url,
                    'open_new_tab' => isset($item['open_new_tab']) ? (int)$item['open_new_tab'] : 0,
                    'add_nofollow' => isset($item['add_nofollow']) ? (int)$item['add_nofollow'] : 0
                );
            }
        }
    }
    
    return $result;
}

/**
 * 调试函数：显示当前保存的海报数据
 */
function blog_debug_poster_data() {
    if (current_user_can('manage_options')) {
        $poster_data = get_option('blog_poster_data', array());
        echo '<!-- 海报数据调试信息: ' . print_r($poster_data, true) . ' -->';
    }
}
add_action('wp_head', 'blog_debug_poster_data');

/**
 * 获取主题设置的海报图片（向后兼容）
 */
function blog_get_poster_images() {
    $poster_data = blog_get_poster_data();
    $image_urls = array();
    
    foreach ($poster_data as $item) {
        $image_urls[] = $item['image_url'];
    }
    
    return $image_urls;
}

/**
 * 获取自定义Logo信息
 */
function blog_get_custom_logo() {
    $logo_id = get_option('blog_custom_logo_id', 0);
    
    if ($logo_id) {
        $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        $logo_alt = get_post_meta($logo_id, '_wp_attachment_image_alt', true);
        
        if ($logo_url) {
            return array(
                'url' => $logo_url,
                'alt' => $logo_alt ? $logo_alt : get_bloginfo('name')
            );
        }
    }
    
    return false;
}

/**
 * 检查是否设置了自定义Logo
 */
function blog_has_custom_logo() {
    return blog_get_custom_logo() !== false;
}

/**
 * 备用的管理员脚本加载函数
 */
function blog_admin_init() {
    // 检查当前页面是否为我们的设置页面
    if (isset($_GET['page']) && 
        (strpos($_GET['page'], 'blog-poster-settings') !== false || 
         strpos($_GET['page'], 'blog-logo-settings') !== false ||
         strpos($_GET['page'], 'blog-intro-settings') !== false ||
         strpos($_GET['page'], 'blog-site-info-settings') !== false ||
         strpos($_GET['page'], 'blog-cc-settings') !== false ||
         strpos($_GET['page'], 'blog-theme-color-settings') !== false ||
         strpos($_GET['page'], 'blog-search-limit-settings') !== false)) {
        
        // 确保媒体库脚本已加载
        add_action('admin_footer', function() {
            wp_enqueue_media();
            wp_enqueue_script('jquery');
        });
    }
}
add_action('admin_init', 'blog_admin_init');

/**
 * 获取站点介绍数据
 */
function blog_get_intro_data() {
    $intro_image_id = get_option('blog_intro_image_id', 0);
    $intro_name = get_option('blog_intro_name', '');
    $intro_description = get_option('blog_intro_description', '');
    
    $intro_image_url = '';
    if ($intro_image_id) {
        $intro_image_url = wp_get_attachment_image_url($intro_image_id, 'medium');
    }
    
    return array(
        'image_url' => $intro_image_url,
        'name' => $intro_name,
        'description' => $intro_description,
        'has_custom_data' => !empty($intro_image_url) || !empty($intro_name) || !empty($intro_description)
    );
}

/**
 * 检查是否设置了自定义站点介绍
 */
function blog_has_custom_intro() {
    $intro_data = blog_get_intro_data();
    return $intro_data['has_custom_data'];
}

/**
 * 站点信息设置页面
 */
function blog_site_info_settings_page() {
    // 处理表单提交
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['blog_site_info_nonce'], 'blog_site_info_action')) {
        $establishment_date = sanitize_text_field($_POST['blog_establishment_date']);
        $establishment_time = sanitize_text_field($_POST['blog_establishment_time']);
        $show_runtime = isset($_POST['blog_show_runtime']) ? 1 : 0;
        
        // 保存显示运行时间的设置
        update_option('blog_show_runtime', $show_runtime);
        
        if (empty($establishment_date) && empty($establishment_time)) {
            // 如果都为空，删除设置
            delete_option('blog_establishment_timestamp');
            delete_option('blog_establishment_year'); // 保持兼容性
            echo '<div class="notice notice-success is-dismissible"><p>' . __('设置已清空！', 'blog') . '</p></div>';
        } elseif (!empty($establishment_date)) {
            // 验证日期格式，使用WordPress时区
            $datetime_string = $establishment_date . ' ' . ($establishment_time ? $establishment_time : '00:00:00');
            
            // 使用WordPress时区创建DateTime对象
            $wp_timezone = wp_timezone();
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetime_string, $wp_timezone);
            
            if ($datetime && $datetime <= new DateTime('now', $wp_timezone)) {
                $timestamp = $datetime->getTimestamp();
                update_option('blog_establishment_timestamp', $timestamp);
                // 同时保存年份以保持版权信息的兼容性
                update_option('blog_establishment_year', wp_date('Y', $timestamp));
                echo '<div class="notice notice-success is-dismissible"><p>' . __('设置已保存！', 'blog') . '</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('请输入有效的日期时间（不能晚于当前时间）！', 'blog') . '</p></div>';
            }
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('请输入建站日期！', 'blog') . '</p></div>';
        }
    }
    
    // 获取当前设置
    $establishment_timestamp = get_option('blog_establishment_timestamp', '');
    $show_runtime = get_option('blog_show_runtime', 1); // 默认显示
    $establishment_date = '';
    $establishment_time = '';
    
    if ($establishment_timestamp) {
        $establishment_date = wp_date('Y-m-d', $establishment_timestamp);
        $establishment_time = wp_date('H:i:s', $establishment_timestamp);
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blog_site_info_action', 'blog_site_info_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="blog_show_runtime"><?php _e('显示运行时间', 'blog'); ?></label>
                    </th>
                    <td>
                        <label for="blog_show_runtime">
                            <input type="checkbox" 
                                   id="blog_show_runtime" 
                                   name="blog_show_runtime" 
                                   value="1" 
                                   <?php checked($show_runtime, 1); ?> />
                            <?php _e('在网站底部显示稳定运行时间', 'blog'); ?>
                        </label>
                        <p class="description">
                            <?php _e('勾选此选项将在网站底部显示"本站已稳定运行X天X小时X分X秒"的信息。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="blog_establishment_date"><?php _e('建站时间', 'blog'); ?></label>
                    </th>
                    <td>
                        <input type="date" 
                               id="blog_establishment_date" 
                               name="blog_establishment_date" 
                               value="<?php echo esc_attr($establishment_date); ?>" 
                               max="<?php echo wp_date('Y-m-d'); ?>"
                               style="width: 200px; margin-right: 10px;" />
                        
                        <input type="time" 
                               id="blog_establishment_time" 
                               name="blog_establishment_time" 
                               value="<?php echo esc_attr($establishment_time); ?>" 
                               step="1"
                               style="width: 150px;" />
                        
                        <p class="description">
                            <?php _e('请选择网站建立的具体日期和时间。建站年份将自动用于版权信息显示。', 'blog'); ?>
                            <br>
                            <?php _e('当前时间：', 'blog'); ?><?php echo wp_date('Y-m-d H:i:s'); ?>
                            <?php if (!empty($establishment_timestamp)) : ?>
                                <br>
                                <?php _e('建站时间：', 'blog'); ?>
                                <strong><?php echo wp_date('Y-m-d H:i:s', $establishment_timestamp); ?></strong>
                                <br>
                                <?php 
                                // 使用WordPress时区计算当前时间
                                $wp_timezone = wp_timezone();
                                $current_datetime = new DateTime('now', $wp_timezone);
                                $current_time = $current_datetime->getTimestamp();
                                $running_seconds = $current_time - $establishment_timestamp;
                                $running_days = floor($running_seconds / 86400);
                                $running_hours = floor(($running_seconds % 86400) / 3600);
                                $running_minutes = floor(($running_seconds % 3600) / 60);
                                $running_secs = $running_seconds % 60;
                                ?>
                                <?php _e('当前已运行：', 'blog'); ?>
                                <strong><?php echo $running_days; ?>天 <?php echo $running_hours; ?>小时 <?php echo $running_minutes; ?>分 <?php echo $running_secs; ?>秒</strong>
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>

    </div>
    <?php
}

/**
 * 获取建站年份
 */
function blog_get_establishment_year() {
    return get_option('blog_establishment_year', '');
}

/**
 * 获取建站时间戳
 */
function blog_get_establishment_timestamp() {
    return get_option('blog_establishment_timestamp', '');
}

/**
 * 检查是否显示运行时间
 */
function blog_should_show_runtime() {
    return get_option('blog_show_runtime', 1); // 默认显示
}

/**
 * CC协议设置页面
 */
function blog_cc_settings_page() {
    // 处理表单提交
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['blog_cc_nonce'], 'blog_cc_action')) {
        $enable_cc = isset($_POST['blog_enable_cc']) ? 1 : 0;
        $cc_type = sanitize_text_field($_POST['blog_cc_type']);
        $show_cc_icon = isset($_POST['blog_show_cc_icon']) ? 1 : 0;
        
        update_option('blog_enable_cc', $enable_cc);
        update_option('blog_cc_type', $cc_type);
        update_option('blog_show_cc_icon', $show_cc_icon);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('CC协议设置已保存！', 'blog') . '</p></div>';
    }
    
    // 获取当前设置
    $enable_cc = get_option('blog_enable_cc', 1); // 默认启用
    $cc_type = get_option('blog_cc_type', 'by-nc'); // 默认BY-NC
    $show_cc_icon = get_option('blog_show_cc_icon', 1); // 默认显示图标
    
    // CC协议选项
    $cc_options = array(
        'cc0' => array(
            'name' => 'CC0 1.0',
            'description' => '无权利保留 - 将作品贡献到公共领域，放弃所有版权，任何人都可以自由使用',
            'url' => 'https://creativecommons.org/publicdomain/zero/1.0/'
        ),
        'by' => array(
            'name' => 'CC BY 4.0',
            'description' => '署名 - 允许他人分发、重混、调整和基于您的作品进行创作，甚至是商业性使用',
            'url' => 'https://creativecommons.org/licenses/by/4.0/'
        ),
        'by-sa' => array(
            'name' => 'CC BY-SA 4.0',
            'description' => '署名-相同方式共享 - 允许重混、调整和基于您的作品进行创作，甚至是商业性使用',
            'url' => 'https://creativecommons.org/licenses/by-sa/4.0/'
        ),
        'by-nc' => array(
            'name' => 'CC BY-NC 4.0',
            'description' => '署名-非商业性使用 - 允许他人下载您的作品并与他人共享，但不能用于商业用途',
            'url' => 'https://creativecommons.org/licenses/by-nc/4.0/'
        ),
        'by-nc-sa' => array(
            'name' => 'CC BY-NC-SA 4.0',
            'description' => '署名-非商业性使用-相同方式共享 - 最严格的CC协议，仅允许他人下载您的作品并与他人共享',
            'url' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/'
        ),
        'by-nd' => array(
            'name' => 'CC BY-ND 4.0',
            'description' => '署名-禁止演绎 - 允许重新分发，商业性和非商业性使用都可以，但不能修改',
            'url' => 'https://creativecommons.org/licenses/by-nd/4.0/'
        ),
        'by-nc-nd' => array(
            'name' => 'CC BY-NC-ND 4.0',
            'description' => '署名-非商业性使用-禁止演绎 - 最严格的CC协议，仅允许他人下载您的作品并与他人共享',
            'url' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/'
        )
    );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blog_cc_action', 'blog_cc_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="blog_enable_cc"><?php _e('启用CC协议', 'blog'); ?></label>
                    </th>
                    <td>
                        <label for="blog_enable_cc">
                            <input type="checkbox" 
                                   id="blog_enable_cc" 
                                   name="blog_enable_cc" 
                                   value="1" 
                                   <?php checked($enable_cc, 1); ?> />
                            <?php _e('在版权信息中显示CC协议', 'blog'); ?>
                        </label>
                        <p class="description">
                            <?php _e('勾选此选项将在网站底部显示Creative Commons协议信息。取消勾选则只显示"© 年份 站点名称"。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
                
                                 <tr>
                     <th scope="row">
                         <label for="blog_show_cc_icon"><?php _e('显示CC图标', 'blog'); ?></label>
                     </th>
                     <td>
                         <label for="blog_show_cc_icon">
                             <input type="checkbox" 
                                    id="blog_show_cc_icon" 
                                    name="blog_show_cc_icon" 
                                    value="1" 
                                    <?php checked($show_cc_icon, 1); ?> />
                             <?php _e('在CC协议后显示对应的图标', 'blog'); ?>
                         </label>
                         <p class="description">
                             <?php _e('勾选此选项将在CC协议文字后显示相应的Creative Commons图标。', 'blog'); ?>
                         </p>
                     </td>
                 </tr>
                 
                 <tr>
                     <th scope="row">
                         <label for="blog_cc_type"><?php _e('CC协议类型', 'blog'); ?></label>
                     </th>
                    <td>
                        <?php foreach ($cc_options as $key => $option) : ?>
                            <label style="display: block; margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9;">
                                <input type="radio" 
                                       name="blog_cc_type" 
                                       value="<?php echo esc_attr($key); ?>" 
                                       <?php checked($cc_type, $key); ?> 
                                       style="margin-right: 8px;" />
                                <strong><?php echo esc_html($option['name']); ?></strong>
                                <br>
                                <span style="color: #666; font-size: 0.9rem; margin-left: 20px;">
                                    <?php echo esc_html($option['description']); ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                        
                        <p class="description">
                            <?php _e('选择适合您网站内容的Creative Commons协议类型。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>

    </div>
    <?php
}

/**
 * 获取CC协议设置
 */
function blog_get_cc_settings() {
    $enable_cc = get_option('blog_enable_cc', 1);
    $cc_type = get_option('blog_cc_type', 'by-nc');
    $show_cc_icon = get_option('blog_show_cc_icon', 1);
    
    $cc_options = array(
        'cc0' => array(
            'name' => 'CC0 1.0',
            'url' => 'https://creativecommons.org/publicdomain/zero/1.0/',
            'icon_url' => 'https://licensebuttons.net/p/zero/1.0/88x31.png'
        ),
        'by' => array(
            'name' => 'CC BY 4.0',
            'url' => 'https://creativecommons.org/licenses/by/4.0/',
            'icon_url' => 'https://licensebuttons.net/l/by/4.0/88x31.png'
        ),
        'by-sa' => array(
            'name' => 'CC BY-SA 4.0',
            'url' => 'https://creativecommons.org/licenses/by-sa/4.0/',
            'icon_url' => 'https://licensebuttons.net/l/by-sa/4.0/88x31.png'
        ),
        'by-nc' => array(
            'name' => 'CC BY-NC 4.0',
            'url' => 'https://creativecommons.org/licenses/by-nc/4.0/',
            'icon_url' => 'https://licensebuttons.net/l/by-nc/4.0/88x31.png'
        ),
        'by-nc-sa' => array(
            'name' => 'CC BY-NC-SA 4.0',
            'url' => 'https://creativecommons.org/licenses/by-nc-sa/4.0/',
            'icon_url' => 'https://licensebuttons.net/l/by-nc-sa/4.0/88x31.png'
        ),
        'by-nd' => array(
            'name' => 'CC BY-ND 4.0',
            'url' => 'https://creativecommons.org/licenses/by-nd/4.0/',
            'icon_url' => 'https://licensebuttons.net/l/by-nd/4.0/88x31.png'
        ),
        'by-nc-nd' => array(
            'name' => 'CC BY-NC-ND 4.0',
            'url' => 'https://creativecommons.org/licenses/by-nc-nd/4.0/',
            'icon_url' => 'https://licensebuttons.net/l/by-nc-nd/4.0/88x31.png'
        )
    );
    
    return array(
        'enabled' => $enable_cc,
        'type' => $cc_type,
        'show_icon' => $show_cc_icon,
        'info' => isset($cc_options[$cc_type]) ? $cc_options[$cc_type] : $cc_options['by-nc']
    );
}

/**
 * 生成版权年份字符串
 */
function blog_get_copyright_years() {
    $establishment_year = blog_get_establishment_year();
    $current_year = wp_date('Y');
    
    if (empty($establishment_year)) {
        return $current_year;
    }
    
    if ($establishment_year == $current_year) {
        return $establishment_year;
    } else {
        return $establishment_year . '-' . $current_year;
    }
}

/**
 * 声明设置页面
 */
function blog_statement_settings_page() {
    // 处理表单提交
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['blog_statement_nonce'], 'blog_statement_action')) {
        $show_wordpress = isset($_POST['blog_show_wordpress']) ? 1 : 0;
        $show_theme = isset($_POST['blog_show_theme']) ? 1 : 0;
        $wordpress_position = sanitize_text_field($_POST['blog_wordpress_position']);
        $theme_position = sanitize_text_field($_POST['blog_theme_position']);
        
        update_option('blog_show_wordpress', $show_wordpress);
        update_option('blog_show_theme', $show_theme);
        update_option('blog_wordpress_position', $wordpress_position);
        update_option('blog_theme_position', $theme_position);
        
        echo '<div class="notice notice-success is-dismissible"><p>' . __('设置已保存！', 'blog') . '</p></div>';
    }
    
    // 获取当前设置
    $show_wordpress = get_option('blog_show_wordpress', 0);
    $show_theme = get_option('blog_show_theme', 0);
    $wordpress_position = get_option('blog_wordpress_position', 'bottom-left');
    $theme_position = get_option('blog_theme_position', 'bottom-right');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blog_statement_action', 'blog_statement_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label><?php _e('WordPress 声明', 'blog'); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <label for="blog_show_wordpress">
                                <input type="checkbox" 
                                       id="blog_show_wordpress" 
                                       name="blog_show_wordpress" 
                                       value="1" 
                                       <?php checked($show_wordpress, 1); ?> />
                                <?php _e('显示"基于 WordPress"声明', 'blog'); ?>
                            </label>
                            <br><br>
                            <label for="blog_wordpress_position"><?php _e('显示位置：', 'blog'); ?></label>
                            <select id="blog_wordpress_position" name="blog_wordpress_position" style="margin-left: 10px;">
                                <option value="bottom-left" <?php selected($wordpress_position, 'bottom-left'); ?>>
                                    <?php _e('左下角', 'blog'); ?>
                                </option>
                                <option value="bottom-right" <?php selected($wordpress_position, 'bottom-right'); ?>>
                                    <?php _e('右下角', 'blog'); ?>
                                </option>
                            </select>
                        </fieldset>
                        <p class="description">
                            <?php _e('WordPress 声明将链接到官方网站 (https://wordpress.org/)。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label><?php _e('主题声明', 'blog'); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <label for="blog_show_theme">
                                <input type="checkbox" 
                                       id="blog_show_theme" 
                                       name="blog_show_theme" 
                                       value="1" 
                                       <?php checked($show_theme, 1); ?> />
                                <?php _e('显示"使用 PureAura 主题"声明', 'blog'); ?>
                            </label>
                            <br><br>
                            <label for="blog_theme_position"><?php _e('显示位置：', 'blog'); ?></label>
                            <select id="blog_theme_position" name="blog_theme_position" style="margin-left: 10px;">
                                <option value="bottom-left" <?php selected($theme_position, 'bottom-left'); ?>>
                                    <?php _e('左下角', 'blog'); ?>
                                </option>
                                <option value="bottom-right" <?php selected($theme_position, 'bottom-right'); ?>>
                                    <?php _e('右下角', 'blog'); ?>
                                </option>
                            </select>
                        </fieldset>
                        <p class="description">
                            <?php _e('PureAura 主题声明将链接到 GitHub 项目页面。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>

    </div>
    <?php
}

/**
 * 获取声明设置
 */
function blog_get_statement_settings() {
    return array(
        'show_wordpress' => get_option('blog_show_wordpress', 0),
        'show_theme' => get_option('blog_show_theme', 0),
        'wordpress_position' => get_option('blog_wordpress_position', 'bottom-left'),
        'theme_position' => get_option('blog_theme_position', 'bottom-right')
    );
}

/**
 * 主题色设置页面
 */
function blog_theme_color_settings_page() {
    // 处理表单提交
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['blog_theme_color_nonce'], 'blog_theme_color_action')) {
        $primary_color = sanitize_hex_color($_POST['blog_primary_color']);
        
        // 验证颜色格式
        if ($primary_color) {
            update_option('blog_primary_color', $primary_color);
            echo '<div class="notice notice-success is-dismissible"><p>' . __('主题色设置已保存！', 'blog') . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('请输入有效的颜色值！', 'blog') . '</p></div>';
        }
    }
    
    // 获取当前设置
    $primary_color = get_option('blog_primary_color', '#4CAF50'); // 默认绿色
    
    // 预设颜色选项
    $preset_colors = array(
        '#4CAF50' => '绿色',
        '#2196F3' => '蓝色',
        '#FF5722' => '橙红色',
        '#9C27B0' => '紫色',
        '#FF9800' => '橙色',
        '#607D8B' => '蓝灰色',
        '#795548' => '棕色',
        '#E91E63' => '粉红色',
        '#009688' => '青色',
        '#673AB7' => '深紫色',
        '#3F51B5' => '靛蓝色',
        '#FFC107' => '琥珀色'
    );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blog_theme_color_action', 'blog_theme_color_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="blog_primary_color"><?php _e('主题色', 'blog'); ?></label>
                    </th>
                    <td>
                        <input type="color" 
                               id="blog_primary_color" 
                               name="blog_primary_color" 
                               value="<?php echo esc_attr($primary_color); ?>" 
                               style="width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" />
                        
                        <input type="text" 
                               id="blog_primary_color_text" 
                               value="<?php echo esc_attr($primary_color); ?>" 
                               style="width: 100px; margin-left: 10px; font-family: monospace;" 
                               placeholder="#4CAF50" />
                               
                        <p class="description">
                            <?php _e('选择您喜欢的主题色，这将应用到整个网站的链接、按钮、强调色等元素。', 'blog'); ?>
                        </p>
                        
                        <div style="margin-top: 15px;">
                            <h4><?php _e('预设颜色：', 'blog'); ?></h4>
                            <div class="preset-colors" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                                <?php foreach ($preset_colors as $color => $name) : ?>
                                    <div class="preset-color-item" 
                                         style="display: flex; align-items: center; cursor: pointer; padding: 5px; border-radius: 4px; border: 1px solid #ddd; background: #f9f9f9;"
                                         data-color="<?php echo esc_attr($color); ?>"
                                         title="<?php echo esc_attr($name); ?>">
                                        <div style="width: 20px; height: 20px; background-color: <?php echo esc_attr($color); ?>; border-radius: 3px; margin-right: 8px; border: 1px solid #ccc;"></div>
                                        <span style="font-size: 12px; color: #666;"><?php echo esc_html($name); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                                                 </div>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>

    </div>
    
    <script>
    jQuery(document).ready(function($) {
                 // 颜色选择器改变事件
         $('#blog_primary_color').on('input change', function() {
             var color = $(this).val();
             $('#blog_primary_color_text').val(color);
         });
         
         // 文本输入框改变事件
         $('#blog_primary_color_text').on('input change', function() {
             var color = $(this).val();
             if (isValidColor(color)) {
                 $('#blog_primary_color').val(color);
             }
         });
         
         // 预设颜色点击事件
         $('.preset-color-item').on('click', function() {
             var color = $(this).data('color');
             $('#blog_primary_color').val(color);
             $('#blog_primary_color_text').val(color);
             
             // 高亮选中的预设颜色
             $('.preset-color-item').css('background', '#f9f9f9');
             $(this).css('background', '#e3f2fd');
         });
        
        // 验证颜色格式
        function isValidColor(color) {
            return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(color);
        }
        
        // 初始化：高亮当前选中的预设颜色
        var currentColor = $('#blog_primary_color').val();
        $('.preset-color-item').each(function() {
            if ($(this).data('color') === currentColor) {
                $(this).css('background', '#e3f2fd');
            }
        });
    });
    </script>
    
    <style>
    .preset-color-item:hover {
        background: #e3f2fd !important;
        border-color: #2196F3 !important;
    }
    
         #blog_primary_color_text {
         text-transform: uppercase;
     }
    </style>
    <?php
}

/**
 * 获取主题色设置
 */
function blog_get_primary_color() {
    return get_option('blog_primary_color', '#4CAF50');
}

/**
 * 在页面头部输出自定义主题色CSS
 */
function blog_output_custom_theme_color() {
    $primary_color = blog_get_primary_color();
    
    // 如果是默认颜色，不需要输出额外CSS
    if ($primary_color === '#4CAF50') {
        return;
    }
    
    // 计算深色版本（悬停效果用）
    $darker_color = blog_darken_color($primary_color, 20);
    
    ?>
    <style type="text/css" id="blog-custom-theme-color">
    :root {
        --accent-color: <?php echo esc_attr($primary_color); ?>;
    }
    
    /* 链接颜色 */
    a {
        color: <?php echo esc_attr($primary_color); ?>;
    }
    
    a:hover {
        color: <?php echo esc_attr($darker_color); ?>;
    }
    
    /* 导航菜单悬停 */
    .nav-menu a:hover {
        color: <?php echo esc_attr($primary_color); ?>;
    }
    
    /* 文章标题链接悬停 */
    .entry-title a:hover {
        color: <?php echo esc_attr($primary_color); ?>;
    }
    
    /* 搜索按钮 */
    .widget_search .search-submit,
    .search-form .search-submit {
        background: <?php echo esc_attr($primary_color); ?>;
    }
    
    .widget_search .search-submit:hover,
    .search-form .search-submit:hover {
        background: <?php echo esc_attr($darker_color); ?>;
    }
    
    /* 搜索框焦点 */
    .widget_search .search-field:focus {
        border-color: <?php echo esc_attr($primary_color); ?>;
        box-shadow: 0 0 0 2px rgba(<?php echo blog_hex_to_rgb($primary_color); ?>, 0.1);
    }
    
    /* 分页 */
    .pagination .page-numbers.current {
        background: <?php echo esc_attr($primary_color); ?>;
    }
    
    /* 标签云 */
    .wp-tag-cloud a:hover {
        background: <?php echo esc_attr($primary_color); ?>;
    }
    
    /* 评论表单按钮 */
    .comment-form input[type="submit"] {
        background: <?php echo esc_attr($primary_color); ?>;
    }
    
    .comment-form input[type="submit"]:hover {
        background: <?php echo esc_attr($darker_color); ?>;
    }
    
    /* 评论回复链接 */
    .comment-reply-link {
        color: <?php echo esc_attr($primary_color); ?>;
    }
    
    .comment-reply-link:hover {
        color: <?php echo esc_attr($darker_color); ?>;
    }
    
    /* 作者链接 */
    .author-link {
        color: <?php echo esc_attr($primary_color); ?>;
    }
    
    .author-link:hover {
        color: <?php echo esc_attr($darker_color); ?>;
    }
    
    /* 文章元信息标签 */
    .entry-meta .post-tags a {
        color: <?php echo esc_attr($primary_color); ?>;
    }
    
    .entry-meta .post-tags a:hover {
        color: <?php echo esc_attr($darker_color); ?>;
    }
    
    /* 声明链接悬停 */
    .site-statement a:hover {
        color: <?php echo esc_attr($primary_color); ?>;
    }
    
    /* 版权信息链接 */
    .copyright-info a {
        color: <?php echo esc_attr($primary_color); ?>;
    }
    
    .copyright-info a:hover {
        border-bottom: 1px dotted <?php echo esc_attr($primary_color); ?>;
    }
    
    /* 用户名链接（有网站URL时） */
    .comment-author h4 a {
        color: <?php echo esc_attr($primary_color); ?> !important;
    }
    
    .comment-author h4 a:hover {
        color: <?php echo esc_attr($darker_color); ?> !important;
    }
    
    /* 边框强调色 */
    .widget-title {
        border-bottom: 2px solid <?php echo esc_attr($primary_color); ?>;
    }
    
    .page-title {
        border-bottom: 2px solid <?php echo esc_attr($primary_color); ?>;
    }
    
    .author-description {
        border-left: 4px solid <?php echo esc_attr($primary_color); ?>;
    }
    
    .search .page-title {
        border-bottom: 2px solid <?php echo esc_attr($primary_color); ?>;
    }
    
    .single-post .entry-content blockquote {
        border-left: 4px solid <?php echo esc_attr($primary_color); ?>;
    }
    
    .comments-title, .comment-reply-title {
        border-bottom: 2px solid <?php echo esc_attr($primary_color); ?>;
    }
    
    .comment-content {
        border-left: 3px solid <?php echo esc_attr($primary_color); ?>;
    }
    
    /* 搜索词高亮 */
    .search-term {
        color: <?php echo esc_attr($primary_color); ?>;
        background: rgba(<?php echo blog_hex_to_rgb($primary_color); ?>, 0.1);
    }
    </style>
    <?php
}
add_action('wp_head', 'blog_output_custom_theme_color');

/**
 * 搜索限制设置页面
 */
function blog_search_limit_settings_page() {
    // 处理表单提交
    if (isset($_POST['submit']) && wp_verify_nonce($_POST['blog_search_limit_nonce'], 'blog_search_limit_action')) {
        $search_limit_minute = intval($_POST['blog_search_limit_minute']);
        $search_limit_daily = intval($_POST['blog_search_limit_daily']);
        
        // 验证输入值
        if ($search_limit_minute < 1 || $search_limit_minute > 100) {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('每分钟搜索次数必须在1-100之间！', 'blog') . '</p></div>';
        } elseif ($search_limit_daily < 10 || $search_limit_daily > 10000) {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('每日搜索次数必须在10-10000之间！', 'blog') . '</p></div>';
        } else {
            update_option('blog_search_limit_minute', $search_limit_minute);
            update_option('blog_search_limit_daily', $search_limit_daily);
            echo '<div class="notice notice-success is-dismissible"><p>' . __('搜索限制设置已保存！', 'blog') . '</p></div>';
        }
    }
    
    // 获取当前设置
    $search_limit_minute = get_option('blog_search_limit_minute', 10); // 默认每分钟10次
    $search_limit_daily = get_option('blog_search_limit_daily', 300);   // 默认每天300次
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('blog_search_limit_action', 'blog_search_limit_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="blog_search_limit_minute"><?php _e('每分钟搜索限制', 'blog'); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="blog_search_limit_minute" 
                               name="blog_search_limit_minute" 
                               value="<?php echo esc_attr($search_limit_minute); ?>" 
                               min="1" 
                               max="100" 
                               class="small-text" />
                        <span><?php _e('次', 'blog'); ?></span>
                        <p class="description">
                            <?php _e('设置每个IP地址每分钟最多可以搜索的次数。建议值：5-20次。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="blog_search_limit_daily"><?php _e('每日搜索限制', 'blog'); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="blog_search_limit_daily" 
                               name="blog_search_limit_daily" 
                               value="<?php echo esc_attr($search_limit_daily); ?>" 
                               min="10" 
                               max="10000" 
                               class="small-text" />
                        <span><?php _e('次', 'blog'); ?></span>
                        <p class="description">
                            <?php _e('设置每个IP地址每天最多可以搜索的次数。建议值：100-1000次。', 'blog'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>

    </div>
    <?php
}

/**
 * 将十六进制颜色转换为RGB值
 */
function blog_hex_to_rgb($hex) {
    $hex = ltrim($hex, '#');
    
    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    return "$r, $g, $b";
}

/**
 * 加深颜色
 */
function blog_darken_color($hex, $percent) {
    $hex = ltrim($hex, '#');
    
    if (strlen($hex) == 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r - ($r * $percent / 100)));
    $g = max(0, min(255, $g - ($g * $percent / 100)));
    $b = max(0, min(255, $b - ($b * $percent / 100)));
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
} 

/**
 * 主题切换时清理数据
 * 当用户切换到其他主题时执行清理
 */
function blog_theme_cleanup() {
    // 清理主题设置选项
    $theme_options = array(
        // 海报设置
        'blog_poster_images',
        'blog_poster_links', 
        'blog_poster_new_tabs',
        'blog_poster_nofollows',
        
        // Logo设置
        'blog_custom_logo_url',
        'blog_custom_logo_alt',
        
        // 介绍模块设置
        'blog_intro_image_url',
        'blog_intro_name',
        'blog_intro_description',
        
        // 建站时间设置
        'blog_establishment_year',
        'blog_establishment_month',
        'blog_establishment_day',
        'blog_establishment_hour',
        'blog_establishment_minute',
        'blog_show_runtime',
        
        // CC协议设置
        'blog_cc_enabled',
        'blog_cc_type',
        'blog_cc_show_icon',
        
        // 声明设置
        'blog_show_wordpress_statement',
        'blog_wordpress_statement_position',
        'blog_show_theme_statement',
        'blog_theme_statement_position',
        
        // 主题色设置
        'blog_primary_color',
        
        // 搜索限制设置
        'blog_search_limit_per_minute',
        'blog_search_limit_per_day',
    );
    
    foreach ($theme_options as $option) {
        delete_option($option);
    }
    
    // 清理缓存
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }
    
    // 记录清理日志
    error_log('PureAura theme data cleaned up on theme switch.');
}

// 当切换到其他主题时执行清理
add_action('switch_theme', 'blog_theme_cleanup');

// 自定义评论表单字段顺序和布局
add_filter('comment_form_fields', function($fields) {
    $author = isset($fields['author']) ? $fields['author'] : '';
    $email  = isset($fields['email']) ? $fields['email'] : '';
    $url    = isset($fields['url']) ? $fields['url'] : '';
    $comment_field = isset($fields['comment']) ? $fields['comment'] : '';
    $cookies_field = isset($fields['cookies']) ? $fields['cookies'] : '';

    // 修改"显示名称"为"名称"
    if ($author) {
        $author = str_replace('显示名称', '名称', $author);
    }

    // 清空原有字段
    $fields = array();
    
    // 重新组织字段顺序：名称邮箱网站在一行，然后是评论框，最后是cookies复选框
    $fields['author_email_url'] = '<div class="comment-fields-row">' . $author . $email . $url . '</div>';
    
    if ($comment_field) {
        $fields['comment'] = $comment_field;
    }
    
    if ($cookies_field) {
        $fields['cookies'] = $cookies_field;
    }

    return $fields;
}, 50);

// 添加自定义CSS让输入框横向排列
add_action('wp_head', function() {
    echo '<style>
    .comment-fields-row {
        display: flex;
        gap: 10px;
    }
    .comment-fields-row p {
        flex: 1 1 0;
        margin-bottom: 0;
    }
    /* cookies复选框紧凑间距 */
    .comment-respond .comment-form-cookies-consent {
        margin-top: 0 !important;
        margin-bottom: 5px !important;
        padding: 0 !important;
    }
    /* 统一评论表单字体 */
    .comment-form-comment label {
        font-family: inherit !important;
        font-weight: inherit !important;
        font-size: inherit !important;
    }
    /* 禁用评论输入框的resize功能 */
    .comment-form textarea {
        resize: none !important;
    }
    </style>';
});