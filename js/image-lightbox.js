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
        <span class="lightbox-close">&times;</span>
        <button class="lightbox-prev" aria-label="Previous image">&lsaquo;</button>
        <button class="lightbox-next" aria-label="Next image">&rsaquo;</button>
        <div class="lightbox-container">
            <img src="" alt="" class="lightbox-image">
        </div>
    `;
    document.body.appendChild(lightbox);
    
    // 获取灯箱内的元素
    const lightboxImage = lightbox.querySelector('.lightbox-image');
    const closeButton = lightbox.querySelector('.lightbox-close');
    const prevButton = lightbox.querySelector('.lightbox-prev');
    const nextButton = lightbox.querySelector('.lightbox-next');
    
    // 获取并存储所有图片源
    const contentImages = document.querySelectorAll('.single-post .entry-content img');
    const imageSources = Array.from(contentImages).map(img => img.src);
    let currentIndex = 0;

    // 显示指定索引的图片
    function showImage(index) {
        if (index < 0 || index >= imageSources.length) {
            return;
        }
        lightboxImage.src = imageSources[index];
        currentIndex = index;

        // 如果只有一张图片，则隐藏箭头
        const arrowDisplay = imageSources.length > 1 ? 'block' : 'none';
        prevButton.style.display = arrowDisplay;
        nextButton.style.display = arrowDisplay;
    }
    
    // 为文章中的所有图片添加点击事件
    contentImages.forEach((img, index) => {
        // 确保图片可点击
        img.style.cursor = 'pointer';
        
        // 点击图片时打开灯箱
        img.addEventListener('click', function(e) {
            e.preventDefault();
            // 打开灯箱并禁止页面滚动
            lightbox.classList.add('active');
            document.documentElement.style.overflow = 'hidden';
            showImage(index);
        });
    });
    
    // 关闭灯箱
    lightbox.addEventListener('click', function(e) {
        // 当点击的目标不是图片本身时，关闭灯箱
        if (e.target !== lightboxImage) {
            closeLightbox();
        }
    });
    
    // 阻止箭头点击关闭灯箱
    nextButton.addEventListener('click', function(e) {
        e.stopPropagation();
        const nextIndex = (currentIndex + 1) % imageSources.length;
        showImage(nextIndex);
    });

    prevButton.addEventListener('click', function(e) {
        e.stopPropagation();
        const prevIndex = (currentIndex - 1 + imageSources.length) % imageSources.length;
        showImage(prevIndex);
    });
    
    // 为关闭按钮添加点击事件
    closeButton.addEventListener('click', function(e) {
        e.stopPropagation(); // 同样阻止冒泡
        closeLightbox();
    });
    
    // 按键导航
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;

        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowRight' && imageSources.length > 1) {
            nextButton.click();
        } else if (e.key === 'ArrowLeft' && imageSources.length > 1) {
            prevButton.click();
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
        scale = Math.min(Math.max(0.1, scale + delta), 5); // 限制缩放范围在0.1到5倍之间
        
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
        
        // 恢复页面滚动
        document.documentElement.style.overflow = '';
        
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