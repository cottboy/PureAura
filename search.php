<?php get_header(); ?>

<div class="site-content">
<div class="container">
    <div class="content-area">
        <main class="site-main">
            <?php if (have_posts()) : 
                global $wp_query;
                $result_count = $wp_query->found_posts;
                $search_term = esc_html(get_search_query());
            ?>
                <header class="page-header">
                    <h1 class="page-title"><?php 
                        if (!empty($search_term)) {
                            printf(__('搜索 %s', 'blog'), '<span class="search-term">' . $search_term . '</span>');
                        } else {
                            _e('搜索结果', 'blog');
                        }
                    ?></h1>
                    <p class="search-result-count">
                        <?php printf(__('为 "%s" 找到 %d 条搜索结果', 'blog'), esc_html($search_term), $result_count); ?>
                    </p>
                </header><!-- .page-header -->

                <div class="posts-grid">
                    <?php while (have_posts()) : the_post(); 
                        // 检查文章形式
                        $post_format = get_post_format();
                        
                        // 如果是状态形式，使用特殊布局
                        if ($post_format === 'status') :
                    ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card status-format fade-in'); ?>>
                            <div class="status-author">
                                <div class="status-avatar">
                                    <?php echo get_avatar(get_the_author_meta('ID'), 40); ?>
                                </div>
                                <div class="status-author-name">
                                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-link"><?php the_author(); ?></a>
                                </div>
                            </div>
                            
                            <div class="status-content">
                                <a href="<?php the_permalink(); ?>" class="status-content-link">
                                    <div class="status-text">
                                        <?php echo esc_html(blog_get_status_text_content()); ?>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="status-date">
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . '前'; ?>
                                </time>
                            </div>
                        </article>
                    <?php else : 
                        // 标准文章布局
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
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card fade-in'); ?>>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ($has_thumbnail) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php elseif ($first_image) : ?>
                                        <img src="<?php echo esc_url($first_image); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php else : ?>
                                        <div class="no-thumbnail">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                            
                            <div class="post-content">
                                <header class="entry-header">
                                    <h2 class="entry-title">
                                        <a href="<?php the_permalink(); ?>" style="display: block; cursor: pointer;"><?php the_title(); ?></a>
                                    </h2>
                                    <div class="entry-meta">
                                        <span class="posted-on">
                                            <i class="fa fa-calendar"></i>
                                            <?php echo get_the_date(); ?>
                                        </span>
                                            <span class="post-author">
                                                <i class="fa fa-user"></i>
                                                <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" class="author-link"><?php the_author(); ?></a>
                                        </span>
                                        <span class="categories">
                                            <i class="fa fa-folder"></i>
                                            <?php the_category(', '); ?>
                                        </span>
                                        <?php if (comments_open()) : ?>
                                            <span class="comments-link">
                                                <i class="fa fa-comment"></i>
                                                <?php comments_popup_link(__('0 评论', 'blog'), __('1 评论', 'blog'), __('% 评论', 'blog')); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </header>

                                <div class="entry-summary">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        </article>
                    <?php endif; ?>
                    <?php endwhile; ?>
                </div>

                <?php blog_pagination(); ?>

            <?php else : 
                $search_term = esc_html(get_search_query());
            ?>
                <header class="page-header">
                    <h1 class="page-title"><?php 
                        if (!empty($search_term)) {
                            printf(__('搜索 %s', 'blog'), '<span class="search-term">' . $search_term . '</span>');
                        } else {
                            _e('搜索结果', 'blog');
                        }
                    ?></h1>
                    <p class="search-result-count">搜索出0条结果</p>
                </header><!-- .page-header -->
                <p><?php _e('没有找到与您的搜索条件匹配的文章，请尝试其他关键词。', 'blog'); ?></p>
            <?php endif; ?>
        </main>
        
            <div class="sidebar-container">
                <?php get_template_part('template-parts/author-intro'); ?>
        <?php get_sidebar(); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?> 