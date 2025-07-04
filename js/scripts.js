jQuery(document).ready(function($) {
    // 移动端菜单切换
    var $menuToggle = $('.menu-toggle');
    var $mainNavigation = $('.main-navigation');
    var $window = $(window);
    
    $menuToggle.click(function(e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $mainNavigation.toggleClass('toggled');
    });
    
    // 移动端子菜单切换，允许同时展开多个
    var menuClickTimeout;
    $(document).on('click', '.nav-menu .menu-item-has-children > a', function(e) {
        // 使用更可靠的移动端检测方法
        if (window.innerWidth <= 768 || $('.menu-toggle').is(':visible')) {
            e.preventDefault();
            e.stopPropagation();
            
            var $parentLi = $(this).parent('li');
            var $link = $(this);
            
            // 防抖处理：防止快速重复点击
            if (menuClickTimeout) {
                clearTimeout(menuClickTimeout);
            }
            
            menuClickTimeout = setTimeout(function() {
                // 强制切换active状态
                var wasActive = $parentLi.hasClass('active');
                
                if (wasActive) {
                    $parentLi.removeClass('active');
                } else {
                    $parentLi.addClass('active');
                }
                
                // 移动端强制移除hover状态，防止CSS冲突
                $parentLi.trigger('mouseleave');
                $parentLi.removeClass('hover');
                
                // 强制重绘以确保CSS变化生效
                $parentLi.get(0).offsetHeight;
                
            }, 50); // 50ms防抖延迟
        }
    });
    
    // 在窗口调整大小时重置菜单状态
    $window.resize(function() {
        if ($window.width() > 768) {
            $mainNavigation.removeClass('toggled');
            $menuToggle.removeClass('active');
            $('.nav-menu .menu-item-has-children').removeClass('active');
        }
    });
    
    // 图片和文章卡片的淡入效果
    $('.post-card').addClass('fade-in');
    
    // 添加页面加载动画
    $(window).on('load', function() {
        $('body').addClass('loaded');
    });
});