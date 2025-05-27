<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>><?php wp_body_open(); ?>
<header class="site-header">
    <div class="container">
        <div class="site-branding">
            <?php
            // 优先检查自定义主题设置的Logo
            if (blog_has_custom_logo()) :
                $custom_logo = blog_get_custom_logo();
                ?>
                <div class="site-logo">
                    <a href="<?php echo esc_url(home_url('/')); ?>">
                        <img src="<?php echo esc_url($custom_logo['url']); ?>" alt="<?php echo esc_attr($custom_logo['alt']); ?>" class="custom-logo">
                    </a>
                </div>
            <?php
            elseif (has_custom_logo()) :
                the_custom_logo();
            else :
                // 如果没有设置任何logo，显示网站标题
            ?>
                <div class="site-title">
                    <a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
                </div>
            <?php 
            endif; // 结束 if (has_custom_logo())
            ?>
        </div>

        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
            <span class="menu-toggle-icon"></span>
        </button>

        <nav class="main-navigation">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => false,
            ));
            ?>
        </nav>
    </div>
</header>

<?php if (!is_single() && !is_page()) : // 仅在非文章和非页面显示site-content包装器 ?>
<div class="site-content">
    <?php if (is_front_page() && !is_paged()) : ?>
        <div class="main-content-container">
            <div class="poster-wrapper">
                <div class="poster-container">
                    <?php
                    // 使用新的主题设置获取海报图片和链接数据
                    $poster_data = blog_get_poster_data();
                    
                    if (!empty($poster_data)) {
                        foreach ($poster_data as $item) {
                            $image_url = $item['image_url'];
                            $link_url = $item['link_url'];
                            $open_new_tab = isset($item['open_new_tab']) ? (int)$item['open_new_tab'] : 0; // 默认在当前页面打开
                            $add_nofollow = isset($item['add_nofollow']) ? (int)$item['add_nofollow'] : 0; // 默认不添加nofollow
                            
                            echo '<div class="poster-slide">';
                            
                            if (!empty($link_url)) {
                                // 如果有链接，包装在a标签中
                                $link_attributes = 'href="' . esc_url($link_url) . '" class="poster-link"';
                                
                                // 构建rel属性
                                $rel_values = array();
                                if ($open_new_tab) {
                                    $link_attributes .= ' target="_blank"';
                                    $rel_values[] = 'noopener';
                                }
                                if ($add_nofollow) {
                                    $rel_values[] = 'nofollow';
                                }
                                
                                if (!empty($rel_values)) {
                                    $link_attributes .= ' rel="' . implode(' ', $rel_values) . '"';
                                }
                                
                                echo '<a ' . $link_attributes . '>';
                                echo '<img src="' . esc_url($image_url) . '" alt="海报图片">';
                                echo '</a>';
                            } else {
                                // 如果没有链接，直接显示图片
                                echo '<img src="' . esc_url($image_url) . '" alt="海报图片">';
                            }
                            
                            echo '</div>';
                        }
                    } else {
                        // 如果没有设置任何图片，显示默认提示
                        echo '<div class="poster-slide">';
                        echo '<div style="background: #f0f0f0; height: 100%; display: flex; align-items: center; justify-content: center; color: #666; font-size: 1.2rem;">';
                        echo __('请在主题设置中添加海报图片', 'blog');
                        echo '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="poster-dots"></div>
            </div>
        </div>
    <?php endif; ?>
<?php else : ?>
<div class="site-content">
    <?php endif; ?> 