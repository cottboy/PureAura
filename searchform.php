<?php
/**
 * 自定义搜索表单模板
 */

// 获取当前的搜索词
$search_query = '';
if (is_search()) {
    $search_query = esc_attr(get_search_query());
} elseif (isset($_GET['s'])) {
    $search_query = esc_attr($_GET['s']);
}
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="search-field"><?php _e('搜索内容', 'blog'); ?></label>
    <input 
        type="search" 
        id="search-field"
        class="search-field" 
        placeholder="<?php echo esc_attr__('搜索内容', 'blog'); ?>" 
        value="<?php echo $search_query; ?>" 
        name="s" 
        autocomplete="off"
        required
    />
    <button type="submit" class="search-submit" aria-label="<?php echo esc_attr__('搜索', 'blog'); ?>">
        <i class="fas fa-search" aria-hidden="true"></i>
    </button>
</form> 