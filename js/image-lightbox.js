document.addEventListener('DOMContentLoaded', function() {
    // 仅在文章内容页面执行
    if (!document.querySelector('.single-post .entry-content')) return;
    
    // 禁用WordPress内置的lightbox功能
    document.querySelectorAll('.wp-lightbox-container, .wp-lightbox-overlay').forEach(el => {
        el.remove();
    });
    
    // 移除WordPress添加的lightbox相关属性
    document.querySelectorAll('img[data-wp-on-async--click]').forEach(img => {
        img.removeAttribute('data-wp-on-async--click');
        img.removeAttribute('data-wp-class--hide');
        img.removeAttribute('data-wp-class--show');
    });
    
    // 移除WordPress添加的lightbox按钮
    document.querySelectorAll('button.lightbox-trigger').forEach(btn => {
        btn.remove();
    });
    
    // 创建灯箱元素
    const lightbox = document.createElement('div');
    lightbox.className = 'image-lightbox';
    lightbox.innerHTML = `
        <div class="lightbox-container">
            <img src="" alt="" class="lightbox-image">
        </div>
    `;
    document.body.appendChild(lightbox);
    
    // 获取灯箱内的元素
    const lightboxImage = lightbox.querySelector('.lightbox-image');
    
    // 为文章中的所有图片添加点击事件
    const contentImages = document.querySelectorAll('.single-post .entry-content img');
    contentImages.forEach(img => {
        // 确保图片可点击
        img.style.cursor = 'pointer';
        
        // 点击图片时打开灯箱
        img.addEventListener('click', function(e) {
            e.preventDefault();
            const imgSrc = this.getAttribute('src');
            
            // 设置灯箱图片源
            lightboxImage.setAttribute('src', imgSrc);
            
            // 打开灯箱
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden'; // 防止滚动
        });
    });
    
    // 关闭灯箱
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    // 按ESC键关闭灯箱
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) {
            closeLightbox();
        }
    });
    
    // 实现缩放功能
    let scale = 1;
    
    // 拖动功能变量
    let isDragging = false;
    let startX, startY;
    let translateX = 0, translateY = 0;
    
    // 滚轮缩放
    lightbox.addEventListener('wheel', function(e) {
        if (!lightbox.classList.contains('active')) return;
        
        e.preventDefault();
        
        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        scale = Math.min(Math.max(0.5, scale + delta), 3); // 限制缩放范围在0.5到3倍之间
        
        updateImageTransform();
    });
    
    // 双击重置
    lightboxImage.addEventListener('dblclick', function() {
        // 恢复过渡效果
        lightboxImage.style.transition = 'transform 0.2s ease';
        
        scale = 1;
        translateX = 0;
        translateY = 0;
        updateImageTransform();
    });
    
    // 鼠标按下事件 - 开始拖动
    lightboxImage.addEventListener('mousedown', function(e) {
        // 移除过渡效果，提高跟手性
        lightboxImage.style.transition = 'none';
        
        isDragging = true;
        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
        lightboxImage.style.cursor = 'grabbing';
        e.preventDefault(); // 防止图片被拖动而不是移动
    });
    
    // 鼠标移动事件 - 拖动过程
    window.addEventListener('mousemove', function(e) {
        if (!isDragging) return;
        
        translateX = e.clientX - startX;
        translateY = e.clientY - startY;
        updateImageTransform();
        e.preventDefault();
    });
    
    // 鼠标松开事件 - 结束拖动
    window.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            lightboxImage.style.cursor = 'grab';
            
            // 恢复过渡效果
            lightboxImage.style.transition = 'transform 0.2s ease';
        }
    });
    
    // 当鼠标离开浏览器窗口时也结束拖动
    window.addEventListener('mouseleave', function() {
        if (isDragging) {
            isDragging = false;
            lightboxImage.style.cursor = 'grab';
            
            // 恢复过渡效果
            lightboxImage.style.transition = 'transform 0.2s ease';
        }
    });
    
    // 更新图片变换
    function updateImageTransform() {
        lightboxImage.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
    }
    
    // 关闭灯箱函数
    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
        
        // 重置缩放和位置
        setTimeout(() => {
            scale = 1;
            translateX = 0;
            translateY = 0;
            updateImageTransform();
            
            // 恢复过渡效果
            lightboxImage.style.transition = 'transform 0.2s ease';
        }, 300);
    }
}); 