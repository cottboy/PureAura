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
    
    // 移动端子菜单切换
    $('.nav-menu .menu-item-has-children > a').click(function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            $(this).parent().toggleClass('active');
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

/* 注释掉重复的原生 JS 菜单切换逻辑
document.addEventListener('DOMContentLoaded', function() {
    // 菜单切换功能
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-navigation');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            this.classList.toggle('active');
            mainNav.classList.toggle('toggled');
        });
        
        // 子菜单展开/折叠
        const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children > a');
        
        menuItemsWithChildren.forEach(function(item) {
            item.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    this.parentNode.classList.toggle('active');
                }
            });
        });
    }
}); 
*/ 