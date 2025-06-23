<?php get_header(); ?>

<div class="site-content single-page-content">
<div class="container">
    <div class="content-area">
        <main class="site-main">
            <?php while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
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