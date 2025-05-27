document.addEventListener('DOMContentLoaded', function() {
    const posterContainer = document.querySelector('.poster-container');
    const posterDots = document.querySelector('.poster-dots');
    
    if (!posterContainer) return;
    
    const slides = posterContainer.querySelectorAll('.poster-slide');
    if (slides.length === 0) return;
    
    // 预加载所有图片
    const images = [];
    slides.forEach((slide, index) => {
        const img = slide.querySelector('img');
        if (img) {
            const newImg = new Image();
            newImg.src = img.src;
            images[index] = newImg;
            
            // 当图片加载完成后，确保渲染优化
            newImg.onload = function() {
                img.style.opacity = '1';
                img.style.transform = 'translateZ(0)';
            };
        }
    });
    
    // 初始化轮播
    slides[0].classList.add('active');
    
    // 创建指示点
    slides.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('poster-dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        posterDots.appendChild(dot);
    });
    
    // 自动轮播
    let currentIndex = 0;
    let interval = setInterval(nextSlide, 10000);
    
    function nextSlide() {
        goToSlide((currentIndex + 1) % slides.length);
    }
    
    function goToSlide(index) {
        if (index === currentIndex) return; // 防止重复切换
        
        // 预标记下一张图片
        const nextIndex = (index + 1) % slides.length;
        slides.forEach(slide => slide.classList.remove('next'));
        slides[nextIndex].classList.add('next');
        
        // 移除当前激活的slide和dot
        slides[currentIndex].classList.remove('active');
        posterDots.children[currentIndex].classList.remove('active');
        
        // 激活新的slide和dot
        currentIndex = index;
        slides[currentIndex].classList.add('active');
        posterDots.children[currentIndex].classList.add('active');
        
        // 重置计时器
        clearInterval(interval);
        interval = setInterval(nextSlide, 10000);
    }
    
    // 当用户滚动页面或离开tab时暂停自动轮播
    window.addEventListener('blur', () => clearInterval(interval));
    window.addEventListener('focus', () => {
        clearInterval(interval);
        interval = setInterval(nextSlide, 10000);
    });
    
    // 鼠标悬停时暂停轮播
    posterContainer.addEventListener('mouseenter', () => clearInterval(interval));
    posterContainer.addEventListener('mouseleave', () => {
        clearInterval(interval);
        interval = setInterval(nextSlide, 10000);
    });
}); 