<?php get_header(); ?>

<div class="site-content">
    <div class="container">
        <div class="content-area">
            <main class="site-main">
                <section class="error-404 not-found">
                    <header class="page-header">
                        <h1 class="page-title"><?php _e('页面未找到', 'blog'); ?></h1>
                    </header>

                    <div class="page-content">
                        <p><?php _e('很抱歉，您要查找的页面不存在。', 'blog'); ?></p>
                        
                        <div class="error-search">
                            <?php get_search_form(); ?>
                        </div>
                        
                        <div class="error-suggestions">
                            <h2><?php _e('您可能感兴趣的内容:', 'blog'); ?></h2>
                            
                            <div class="recent-posts">
                                <h3><?php _e('最新文章', 'blog'); ?></h3>
                                <ul>
                                    <?php
                                    $recent_posts = wp_get_recent_posts(array(
                                        'numberposts' => 5,
                                        'post_status' => 'publish'
                                    ));
                                    
                                    foreach ($recent_posts as $post) :
                                    ?>
                                        <li>
                                            <a href="<?php echo get_permalink($post['ID']); ?>">
                                                <?php echo $post['post_title']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="categories">
                                <h3><?php _e('分类目录', 'blog'); ?></h3>
                                <ul>
                                    <?php wp_list_categories(array(
                                        'title_li' => '',
                                        'number'   => 5,
                                    )); ?>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="back-home">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                                <?php _e('返回首页', 'blog'); ?>
                            </a>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>

<?php get_footer(); ?> 