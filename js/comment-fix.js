/**
 * 禁用评论表单移动功能，保持评论表单在固定位置
 */
(function($) {
    $(document).ready(function() {
        // 保存原始的回复函数
        var originalAddComment = window.addComment;
        
        // 重写回复函数
        window.addComment = {
            // 重写移动表单函数，阻止表单移动
            moveForm: function(commId, parentId, respondId, postId) {
                // 不调用原始的moveForm函数，这样表单就不会移动
                
                // 获取评论表单
                var $form = $('#' + respondId);
                
                // 设置父评论ID
                $('#comment_parent').val(parentId);
                
                // 更新回复标题
                if (parentId != 0) {
                    // 获取父评论元素
                    var $parentComment = $('#comment-' + parentId);
                    var parentAuthor = '该评论';
                    
                    // 最简单的方法：直接从回复按钮的父元素获取作者名称
                    // 这是最可靠的，因为回复按钮是点击的元素，所以它一定存在
                    var $replyLink = $('#comment-' + parentId + ' .comment-reply-link');
                    if ($replyLink.length > 0) {
                        // 获取包含回复按钮的评论的作者
                        var $commentArticle = $replyLink.closest('article.comment');
                        var $authorElement = $commentArticle.find('.comment-author h4').first();
                        
                        if ($authorElement.length > 0) {
                            // 获取纯文本内容
                            parentAuthor = $authorElement.text().trim();
                            
                            // 如果作者名称包含多个空格，只取第一个单词
                            if (parentAuthor.indexOf(' ') > 0) {
                                var firstWord = parentAuthor.split(' ')[0];
                                if (firstWord.length >= 2) {  // 确保不是单个字符
                                    parentAuthor = firstWord;
                                }
                            }
                        }
                    }
                    
                    // 安全检查：确保作者名称不超过10个字符
                    if (parentAuthor.length > 10) {
                        parentAuthor = parentAuthor.substring(0, 10);
                    }
                    
                    // 更新标题 - 直接设置HTML内容
                    $('.comment-reply-title').html('回复 <span class="reply-author">' + parentAuthor + '</span> <small><a rel="nofollow" id="cancel-comment-reply-link" class="show" href="#respond">取消回复</a></small>');
                    
                    // 绑定取消回复事件
                    $('#cancel-comment-reply-link').off('click').on('click', function(e) {
                        e.preventDefault();
                        
                        // 重置父评论ID
                        $('#comment_parent').val('0');
                        
                        // 重置标题
                        $('.comment-reply-title').html('发表评论');
                        
                        // 隐藏取消回复链接
                        $(this).removeClass('show');
                        
                        // 滚动到评论表单
                        $('html, body').animate({
                            scrollTop: $form.offset().top - 100
                        }, 500);
                        
                        return false;
                    });
                    
                    // 滚动到评论表单
                    $('html, body').animate({
                        scrollTop: $form.offset().top - 100
                    }, 500);
                }
                
                return false; // 阻止默认行为
            }
        };
        
        // 复制原始函数的其他属性
        for (var prop in originalAddComment) {
            if (prop !== 'moveForm') {
                window.addComment[prop] = originalAddComment[prop];
            }
        }
        
        // 确保页面加载时评论表单处于正确状态
        $('#comment_parent').val('0');
        $('.comment-reply-title').html('发表评论');
        
        // 如果URL中包含replytocom参数，确保取消回复链接可见
        if (window.location.href.indexOf('replytocom') > -1) {
            $('#cancel-comment-reply-link').addClass('show');
        }
    });
})(jQuery); 