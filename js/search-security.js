/**
 * 搜索表单安全增强脚本
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchForms = document.querySelectorAll('.search-form');
    
    searchForms.forEach(function(form) {
        const searchField = form.querySelector('.search-field');
        const submitButton = form.querySelector('.search-submit');
        
        if (searchField && submitButton) {
            // 实时验证搜索输入
            searchField.addEventListener('input', function(e) {
                const value = e.target.value;
                
                // 只检查长度和按钮状态，不进行字符替换
                const cleanLength = value.replace(/\s+/g, ' ').trim().length;
                
                // 更新提交按钮状态（不改变外观，只改变功能）
                const isValid = cleanLength >= 1 && cleanLength <= 200;
                submitButton.disabled = !isValid;
                
                // 只在检测到明显危险内容时才进行清理
                const hasCriticalDanger = /<script|<iframe|<object|<embed|javascript:|vbscript:|data:/i.test(value);
                
                if (hasCriticalDanger) {
                    // 移除危险字符
                    const cleanValue = value
                        .replace(/<script[^>]*>.*?<\/script>/gi, '') // 移除script标签
                        .replace(/<iframe[^>]*>.*?<\/iframe>/gi, '') // 移除iframe标签
                        .replace(/<object[^>]*>.*?<\/object>/gi, '') // 移除object标签
                        .replace(/<embed[^>]*>/gi, '') // 移除embed标签
                        .replace(/javascript:/gi, '') // 移除javascript:
                        .replace(/vbscript:/gi, '') // 移除vbscript:
                        .replace(/data:/gi, ''); // 移除data:
                    
                    if (cleanValue !== value) {
                        e.target.value = cleanValue;
                    }
                }
            });
            
            // 表单提交验证
            form.addEventListener('submit', function(e) {
                const searchValue = searchField.value.trim();
                
                // 严格检查：如果搜索值为空，直接阻止提交
                if (!searchValue || searchValue === '') {
                    e.preventDefault();
                    e.stopPropagation();
                    searchField.focus(); // 聚焦到搜索框
                    return false;
                }
                
                // 基本验证
                if (searchValue.length < 1) {
                    e.preventDefault();
                    alert('请输入搜索内容');
                    return false;
                }
                
                if (searchValue.length > 200) {
                    e.preventDefault();
                    alert('搜索词不能超过200个字符');
                    return false;
                }
                
                // 检查危险字符
                const dangerousPatterns = [
                    /<script/i,
                    /javascript:/i,
                    /vbscript:/i,
                    /onload=/i,
                    /onerror=/i,
                    /onclick=/i,
                    /<iframe/i,
                    /<object/i,
                    /<embed/i
                ];
                
                const hasDangerousContent = dangerousPatterns.some(pattern => 
                    pattern.test(searchValue)
                );
                
                if (hasDangerousContent) {
                    e.preventDefault();
                    alert('搜索词包含不允许的内容');
                    return false;
                }
                
                // 频率限制检查（客户端预检查）
                const currentTime = Date.now();
                
                // 检查每分钟限制（10次）
                let searchTimesMinute = JSON.parse(localStorage.getItem('searchTimesMinute') || '[]');
                const oneMinuteAgo = currentTime - 60 * 1000;
                searchTimesMinute = searchTimesMinute.filter(time => time > oneMinuteAgo);
                
                if (searchTimesMinute.length >= 10) {
                    e.preventDefault();
                    alert('搜索过于频繁，每分钟最多搜索10次，请稍后再试');
                    return false;
                }
                
                // 检查每日限制（300次）
                let searchTimesDaily = JSON.parse(localStorage.getItem('searchTimesDaily') || '[]');
                const oneDayAgo = currentTime - 24 * 60 * 60 * 1000;
                searchTimesDaily = searchTimesDaily.filter(time => time > oneDayAgo);
                
                if (searchTimesDaily.length >= 300) {
                    e.preventDefault();
                    alert('今日搜索次数已达上限（300次），请明天再试');
                    return false;
                }
                
                // 记录搜索时间
                searchTimesMinute.push(currentTime);
                searchTimesDaily.push(currentTime);
                localStorage.setItem('searchTimesMinute', JSON.stringify(searchTimesMinute));
                localStorage.setItem('searchTimesDaily', JSON.stringify(searchTimesDaily));
                
                // 清理搜索值
                searchField.value = searchValue
                    .replace(/<script[^>]*>.*?<\/script>/gi, '') // 移除script标签
                    .replace(/<iframe[^>]*>.*?<\/iframe>/gi, '') // 移除iframe标签
                    .replace(/<object[^>]*>.*?<\/object>/gi, '') // 移除object标签
                    .replace(/<embed[^>]*>/gi, '') // 移除embed标签
                    .replace(/javascript:/gi, '')
                    .replace(/vbscript:/gi, '')
                    .replace(/data:/gi, '')
                    .trim();
            });
            
            // 键盘事件监听，防止空搜索时按回车提交
            searchField.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.keyCode === 13) {
                    const searchValue = this.value.trim();
                    if (!searchValue || searchValue === '') {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                }
            });
            
            // 初始状态设置
            const initialValue = searchField.value.trim();
            const isInitiallyValid = initialValue.length >= 1 && initialValue.length <= 200;
            submitButton.disabled = !isInitiallyValid;
        }
    });
    
    // 防止复制粘贴恶意内容
    document.addEventListener('paste', function(e) {
        const target = e.target;
        if (target.classList.contains('search-field')) {
            setTimeout(function() {
                const value = target.value;
                
                // 只在检测到危险内容时才进行清理
                const hasDangerousContent = /<script|<iframe|<object|<embed|javascript:|vbscript:|data:/i.test(value);
                
                if (hasDangerousContent) {
                    const cleanValue = value
                        .replace(/<script[^>]*>.*?<\/script>/gi, '') // 移除script标签
                        .replace(/<iframe[^>]*>.*?<\/iframe>/gi, '') // 移除iframe标签
                        .replace(/<object[^>]*>.*?<\/object>/gi, '') // 移除object标签
                        .replace(/<embed[^>]*>/gi, '') // 移除embed标签
                        .replace(/javascript:/gi, '')
                        .replace(/vbscript:/gi, '')
                        .replace(/data:/gi, '');
                    
                    if (cleanValue !== value) {
                        target.value = cleanValue;
                    }
                }
            }, 10);
        }
    });
}); 