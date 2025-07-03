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
    $('.nav-menu .menu-item-has-children > a').on('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            // 只切换当前点击的父菜单的 'active' 状态，不影响其他菜单
            $(this).parent('li').toggleClass('active');
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