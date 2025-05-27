<?php if (!is_single() && !is_page()) : // 只在非文章和非页面页面关闭site-content div，因为这些页面在各自的模板中有自己的div ?>
    </div><!-- .site-content -->
<?php endif; ?>

    <div class="copyright-info">
        <div class="container">
            <p>
                <?php 
                $cc_settings = blog_get_cc_settings();
                if ($cc_settings['enabled'] && $cc_settings['type'] === 'cc0') :
                    // CC0协议特殊处理 - 不显示版权符号
                ?>
                <?php echo blog_get_copyright_years(); ?> <?php bloginfo('name'); ?> 本站内容根据<a href="<?php echo esc_url($cc_settings['info']['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($cc_settings['info']['name']); ?></a>许可协议授权<?php if ($cc_settings['show_icon']) : ?> <img src="<?php echo esc_url($cc_settings['info']['icon_url']); ?>" alt="<?php echo esc_attr($cc_settings['info']['name']); ?>" style="vertical-align: middle; margin-left: 5px;"><?php endif; ?>
                <?php elseif ($cc_settings['enabled']) : ?>
                &copy; <?php echo blog_get_copyright_years(); ?> <?php bloginfo('name'); ?> 本站内容根据<a href="<?php echo esc_url($cc_settings['info']['url']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($cc_settings['info']['name']); ?></a>许可协议授权<?php if ($cc_settings['show_icon']) : ?> <img src="<?php echo esc_url($cc_settings['info']['icon_url']); ?>" alt="<?php echo esc_attr($cc_settings['info']['name']); ?>" style="vertical-align: middle; margin-left: 5px;"><?php endif; ?>
                <?php else : ?>
                &copy; <?php echo blog_get_copyright_years(); ?> <?php bloginfo('name'); ?>
                <?php endif; ?>
            </p>
            <?php 
            $establishment_timestamp = blog_get_establishment_timestamp();
            $show_runtime = blog_should_show_runtime();
            if (!empty($establishment_timestamp) && $show_runtime) :
            ?>
            <p class="site-runtime">
                本站已稳定运行 <span id="site-runtime-display">计算中...</span>
            </p>
            <script>
            // 建站时间戳（秒）
            var establishmentTimestamp = <?php echo $establishment_timestamp; ?>;
            
            function updateRuntime() {
                var now = Math.floor(Date.now() / 1000); // 当前时间戳（秒）
                var runningSeconds = now - establishmentTimestamp;
                
                if (runningSeconds < 0) {
                    document.getElementById('site-runtime-display').textContent = '时间计算错误';
                    return;
                }
                
                var days = Math.floor(runningSeconds / 86400);
                var hours = Math.floor((runningSeconds % 86400) / 3600);
                var minutes = Math.floor((runningSeconds % 3600) / 60);
                var seconds = runningSeconds % 60;
                
                var runtimeText = days + '天' + hours + '小时' + minutes + '分' + seconds + '秒';
                document.getElementById('site-runtime-display').textContent = runtimeText;
            }
            
            // 立即更新一次
            updateRuntime();
            
            // 每秒更新
            setInterval(updateRuntime, 1000);
            </script>
            <?php endif; ?>
            
            <?php
            // 移动端声明显示在运行时间下方
            $statement_settings = blog_get_statement_settings();
            if ($statement_settings['show_wordpress'] || $statement_settings['show_theme']) :
            ?>
            <div class="mobile-statements" style="text-align: center; margin-top: 8px;">
                <?php
                // 显示 WordPress 声明
                if ($statement_settings['show_wordpress']) :
                    $wp_position_class = $statement_settings['wordpress_position'] === 'bottom-left' ? 'statement-left' : 'statement-right';
                ?>
                <span class="site-statement site-statement-wordpress <?php echo esc_attr($wp_position_class); ?>">
                    <a href="https://wordpress.org/" target="_blank" rel="noopener noreferrer">基于 WordPress</a>
                </span>
                <?php endif; ?>

                <?php
                // 显示主题声明
                if ($statement_settings['show_theme']) :
                    $theme_position_class = $statement_settings['theme_position'] === 'bottom-left' ? 'statement-left' : 'statement-right';
                ?>
                <span class="site-statement site-statement-theme <?php echo esc_attr($theme_position_class); ?>">
                    <a href="https://github.com/cottboy/PureAura" target="_blank" rel="noopener noreferrer">使用 PureAura 主题</a>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // 桌面端声明信息（保持原有的绝对定位）
    $statement_settings = blog_get_statement_settings();
    
    // 显示 WordPress 声明
    if ($statement_settings['show_wordpress']) :
        $wp_position_class = $statement_settings['wordpress_position'] === 'bottom-left' ? 'statement-left' : 'statement-right';
    ?>
    <div class="site-statement site-statement-wordpress <?php echo esc_attr($wp_position_class); ?> desktop-only">
        <a href="https://wordpress.org/" target="_blank" rel="noopener noreferrer">基于 WordPress</a>
    </div>
    <?php endif; ?>

    <?php
    // 显示主题声明
    if ($statement_settings['show_theme']) :
        $theme_position_class = $statement_settings['theme_position'] === 'bottom-left' ? 'statement-left' : 'statement-right';
    ?>
    <div class="site-statement site-statement-theme <?php echo esc_attr($theme_position_class); ?> desktop-only">
        <a href="https://github.com/cottboy/PureAura" target="_blank" rel="noopener noreferrer">使用 PureAura 主题</a>
    </div>
    <?php endif; ?>

    <?php wp_footer(); ?>
</body>
</html> 