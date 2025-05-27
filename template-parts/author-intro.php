<?php
/**
 * 作者/站点介绍模块
 */

// 判断当前页面类型
$is_single_post = is_single();
$is_author_page = is_author();
$show_author_info = $is_single_post || $is_author_page;

if ($show_author_info) {
    // 获取作者信息
    if ($is_single_post) {
        $author_id = get_the_author_meta('ID');
    } else {
        $author_id = get_queried_object_id();
    }
    
    $author_name = get_the_author_meta('display_name', $author_id);
    $author_description = get_the_author_meta('description', $author_id);
    $author_avatar = get_avatar($author_id, 100);
    $author_posts_url = get_author_posts_url($author_id);
} else {
    // 获取站点信息
    
    // 使用主题设置中的自定义介绍数据
    $intro_data = blog_get_intro_data();
    $display_name = !empty($intro_data['name']) ? $intro_data['name'] : get_bloginfo('name');
    $display_description = !empty($intro_data['description']) ? $intro_data['description'] : get_bloginfo('description');
    
    // 设置站点图标
    if (!empty($intro_data['image_url'])) {
        $site_icon = '<img src="' . esc_url($intro_data['image_url']) . '" alt="' . esc_attr($display_name) . '" class="site-icon">';
    } else {
        $site_icon = '<div class="default-site-icon"><i class="fas fa-globe"></i></div>';
    }
}
?>

<div class="intro-module">
    <div class="intro-card">
        <?php if ($show_author_info) : ?>
            <!-- 作者信息 -->
            <div class="intro-avatar-section">
                <div class="intro-avatar">
                    <?php echo $author_avatar; ?>
                </div>
            </div>
            <div class="intro-details">
                <h3 class="intro-name">
                    <a href="<?php echo esc_url($author_posts_url); ?>"><?php echo esc_html($author_name); ?></a>
                </h3>
            </div>
            <?php if ($author_description) : ?>
                <div class="intro-description">
                    <p><?php echo nl2br(esc_html($author_description)); ?></p>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <!-- 站点信息 -->
            <div class="intro-avatar-section">
                <div class="intro-avatar">
                    <?php echo $site_icon; ?>
                </div>
            </div>
            <div class="intro-details">
                <h3 class="intro-name">
                    <a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($display_name); ?></a>
                </h3>
            </div>
            <?php if ($display_description) : ?>
                <div class="intro-description">
                    <p><?php echo nl2br(esc_html($display_description)); ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div> 