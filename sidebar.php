<?php
/**
 * 侧边栏模板
 */
?>
<aside id="secondary" class="widget-area sidebar">
    <section class="widget widget_search">
        <h2 class="widget-title">搜索文章</h2>
        <?php get_search_form(); ?>
    </section>
    
    <section class="widget widget_popular_posts">
        <h2 class="widget-title">看看这些</h2>
        <div class="popular-posts-grid">
            <?php
            // 直接查询3篇随机文章
            $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => 3,
                'orderby' => 'rand', // 使用随机排序
            );
            
            $random_posts = new WP_Query($args);
            
            if ($random_posts->have_posts()) :
                while ($random_posts->have_posts()) : $random_posts->the_post();
                    // 尝试获取特色图片
                    $has_thumbnail = has_post_thumbnail();
                    $first_image = '';
                    
                    if (!$has_thumbnail) {
                        // 如果没有特色图片，尝试从文章内容中获取第一张图片
                        $content = get_the_content();
                        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
                        if (isset($matches[1][0])) {
                            // 安全处理图片URL
                            $first_image = esc_url($matches[1][0]);
                        }
                    }
            ?>
                <a href="<?php the_permalink(); ?>" class="mini-post-card-link">
                    <div class="mini-post-card">
                        <div class="mini-post-thumbnail">
                            <?php if ($has_thumbnail) : ?>
                                <?php the_post_thumbnail('thumbnail'); ?>
                            <?php elseif ($first_image) : ?>
                                <img src="<?php echo esc_url($first_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                            <?php else : ?>
                                <div class="no-thumbnail">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="mini-post-content">
                            <h3 class="mini-post-title">
                                <?php the_title(); ?>
                            </h3>
                            <div class="mini-post-meta">
                                <span class="mini-post-date"><?php echo get_the_date(); ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>暂无文章</p>';
            endif;
            ?>
        </div>
    </section>
</aside> 