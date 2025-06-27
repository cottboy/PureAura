<?php get_header(); ?>

<div class="site-content">
    <div class="container">
        <div class="content-area">
            <main class="site-main">
                <section class="error-404 not-found">
                    <div class="error-404-content">
                        <!-- 404数字动画 -->
                        <div class="error-number">
                            <span class="digit-4">4</span>
                            <span class="digit-0">0</span>
                            <span class="digit-4-end">4</span>
                        </div>
                        
                        <!-- 错误信息 -->
                        <div class="error-message">
                            <h1 class="error-title">哎呀！页面走丢了</h1>
                            <p class="error-description">
                                <?php _e('您要找的页面可能已经搬家、改名或者正在度假中...', 'blog'); ?>
                            </p>
                        </div>
                        
                        <!-- 搜索区域 -->
                        <div class="error-search-section">
                            <h3><?php _e('试试搜索您想要的内容', 'blog'); ?></h3>
                            <div class="search-wrapper">
                                <?php get_search_form(); ?>
                            </div>
                        </div>
                        
                        <!-- 推荐内容 -->
                        <div class="error-recommendations">
                            <div class="recommendation-grid">
                                <!-- 看看这些 -->
                                <div class="rec-box random-posts-box">
                                    <h3>📝<?php _e('看看这些', 'blog'); ?></h3>
                                    <ul class="rec-list">
                                        <?php
                                        $args = array(
                                            'numberposts' => 5,
                                            'post_status' => 'publish',
                                            'orderby' => 'rand'
                                        );
                                        
                                        // 根据设置决定是否排除状态文章
                                        if (!blog_should_show_status_on_homepage()) {
                                            $args['tax_query'] = array(
                                                array(
                                                    'taxonomy' => 'post_format',
                                                    'field' => 'slug',
                                                    'terms' => array('post-format-status'),
                                                    'operator' => 'NOT IN'
                                                )
                                            );
                                        }
                                        
                                        $random_posts = get_posts($args);
                                        
                                        foreach ($random_posts as $post) :
                                        ?>
                                            <li>
                                                <a href="<?php echo get_permalink($post->ID); ?>">
                                                    <span class="rec-post-title"><?php echo $post->post_title; ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                

                            </div>
                        </div>
                        
                        <!-- 操作按钮 -->
                        <div class="error-actions">
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                                <span class="btn-icon">🏠</span>
                                <?php _e('回到首页', 'blog'); ?>
                            </a>
                        </div>
                        

                    </div>
                </section>
            </main>
        </div>
    </div>
</div>

<style>
/* 404页面专用样式 */
.error-404 {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem 0;
}

.error-404-content {
    max-width: 800px;
    position: relative;
}

/* 404数字动画 */
.error-number {
    font-size: 12rem;
    font-weight: bold;
    color: var(--accent-color);
    margin-bottom: 2rem;
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.error-number span {
    display: inline-block;
    animation: bounce 2s infinite;
    text-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.digit-4 { animation-delay: 0s; }
.digit-0 { 
    animation: bounce-wide 2s infinite !important;
    animation-delay: 0s !important; 
}
.digit-4-end { animation-delay: 0s; }

/* 强制样式 - 确保0字变宽 */
.error-number span.digit-0 {
    animation: bounce-wide 2s infinite !important;
    animation-delay: 0s !important;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-20px); }
    60% { transform: translateY(-10px); }
}

@keyframes bounce-wide {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0) scaleX(1.2); }
    40% { transform: translateY(-20px) scaleX(1.2); }
    60% { transform: translateY(-10px) scaleX(1.2); }
}

/* 错误信息 */
.error-message {
    margin-bottom: 3rem;
}

.error-title {
    font-size: 2.5rem;
    color: #333;
    margin-bottom: 1rem;
    font-weight: 600;
}

.error-description {
    font-size: 1.2rem;
    color: #666;
    line-height: 1.6;
}

/* 搜索区域 */
.error-search-section {
    margin-bottom: 3rem;
}

.error-search-section h3 {
    font-size: 1.3rem;
    margin-bottom: 1rem;
    color: #555;
}

.search-wrapper .search-form {
    max-width: 400px;
    margin: 0 auto;
    position: relative;
}

.search-wrapper .search-field {
    width: 100%;
    padding: 1rem 3rem 1rem 1.5rem;
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-wrapper .search-field:focus {
    outline: none;
}

.search-wrapper .search-submit {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none !important;
    border: none !important;
    color: #999 !important;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: auto !important;
    height: auto !important;
    font-size: 1rem;
}



.search-wrapper .search-submit i {
    font-size: 16px;
}

/* 推荐内容 */
.error-recommendations {
    margin-bottom: 3rem;
}

.recommendation-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.rec-box {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    text-align: left;
}

.rec-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.rec-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.rec-box h3 {
    font-size: 1.3rem;
    color: #333;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--accent-color);
    padding-bottom: 0.5rem;
}

.rec-list {
    list-style: none;
    padding: 0;
}

.rec-list li {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #f0f0f0;
}

.rec-list li:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.rec-list a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    color: #555;
    transition: color 0.3s ease;
}

.rec-list a:hover {
    color: var(--accent-color);
}

.rec-post-title, .rec-cat-name {
    font-weight: 500;
}

.rec-post-date, .rec-cat-count {
    font-size: 0.9rem;
    color: #999;
    background: #f5f5f5;
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
}

/* 操作按钮 */
.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 0;
    margin-top: 3rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.btn-primary {
    background: var(--accent-color);
    color: white;
}

.btn-primary:hover {
    background: #388E3C;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.btn-secondary {
    background: #f5f5f5;
    color: #666;
    border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
    background: #e9e9e9;
    color: #333;
    border-color: #ccc;
}

/* 装饰元素 */
.error-decoration {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
}

.floating-emoji {
    position: absolute;
    font-size: 2rem;
    animation: float 6s ease-in-out infinite;
    opacity: 0.3;
}

.floating-emoji:nth-child(1) {
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.floating-emoji:nth-child(2) {
    top: 20%;
    right: 15%;
    animation-delay: 2s;
}

.floating-emoji:nth-child(3) {
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

/* 响应式设计 */
@media (max-width: 768px) {
    .error-number {
        font-size: 5rem;
        gap: 0.5rem;
    }
    
    .error-title {
        font-size: 2rem;
    }
    
    .recommendation-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .rec-box {
        padding: 1.5rem;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 200px;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .error-number {
        font-size: 4rem;
    }
    
    .error-title {
        font-size: 1.8rem;
    }
}
</style>

<?php get_footer(); ?> 