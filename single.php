<?php get_header(); ?>

<div class="site-content single-page-content">
<div class="container">
    <div class="content-area">
        <main class="site-main">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
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
                            <?php if (has_tag()) : ?>
                                <span class="tags">
                                    <i class="fa fa-tags"></i>
                                    <?php the_tags('', ', '); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail() && !blog_should_hide_featured_image()) : ?>
                        <div class="post-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . __('页面:', 'blog'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <footer class="entry-footer">
                        <?php edit_post_link(__('编辑', 'blog'), '<span class="edit-link">', '</span>'); ?>
                    </footer>
                </article>

                <?php
                // 如果评论开启，显示评论模板
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

                // 显示上一篇/下一篇文章导航
                blog_custom_post_navigation();
                ?>

            <?php endwhile; ?>
        </main>

        <div class="sidebar-container">
            <?php get_template_part('template-parts/author-intro'); ?>
        <?php get_sidebar(); ?>
        </div>
        </div>
    </div>
</div>

<?php get_footer(); ?> 