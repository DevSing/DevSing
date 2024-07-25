<?php
/*
Plugin Name: All Page URL
Plugin URI: http://www.princekumar.in/all-page-url
Description: A plugin to show all page URLs, post URLs, and category URLs in a WordPress website with export options. <a href="http://wordpress.org/plugins/your-plugin-slug" target="_blank">View details</a>
Version: 1.1
Author: DevP
Author URI: http://www.princekumar.in
License: GPL2
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue plugin styles and scripts
function all_page_url_enqueue_assets($hook) {
    if ($hook !== 'toplevel_page_all-page-url') {
        return;
    }
    wp_enqueue_style('all-page-url-styles', plugin_dir_url(__FILE__) . 'style.css', array(), '1.1');
    wp_enqueue_script('all-page-url-scripts', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), '1.1', true);
    wp_localize_script('all-page-url-scripts', 'allPageUrlAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('all_page_url_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'all_page_url_enqueue_assets');

// Register shortcode to display all URLs
function all_page_url_display_all_urls() {
    ob_start();

    // Default to showing pages
    $paged = 1;
    $query_args = array('post_status' => 'publish', 'post_type' => 'page', 'paged' => $paged);
    $results = new WP_Query($query_args);

    ?>
    <h2>All URLs</h2>
    <div class="filter-options">
        <label for="filter-type">Show: </label>
        <select id="filter-type">
            <option value="pages">Pages</option>
            <option value="posts">Posts</option>
            <option value="categories">Categories</option>
        </select>

        <label for="filter-user">User: </label>
        <select id="filter-user">
            <option value="all">All Users</option>
            <?php
            $users = get_users();
            foreach ($users as $user) {
                echo '<option value="' . esc_attr($user->ID) . '">' . esc_html($user->display_name) . '</option>';
            }
            ?>
        </select>

        <div class="export-buttons">
            <button onclick="exportTableToCSV('data.csv')">Export to CSV</button>
            <button onclick="exportTableToExcel('data.xls')">Export to Excel</button>
        </div>
    </div>
    <div id="all-page-url-content">
        <?php include 'partials/posts-pages-table.php'; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('show_all_urls', 'all_page_url_display_all_urls');

// Add a menu item in the admin
function all_page_url_add_admin_menu() {
    add_menu_page(
        'Show All URLs',
        'Show All URLs',
        'manage_options',
        'all-page-url',
        'all_page_url_admin_page'
    );
}
add_action('admin_menu', 'all_page_url_add_admin_menu');

// Admin page content
function all_page_url_admin_page() {
    echo '<div class="wrap">';
    echo '<h1>All URLs</h1>';
    echo do_shortcode('[show_all_urls]');
    echo '</div>';
}

// AJAX handler for loading filtered data
function all_page_url_load_filtered_data() {
    check_ajax_referer('all_page_url_nonce', 'nonce');

    $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'pages';
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $user = isset($_POST['user']) ? intval($_POST['user']) : 0;

    switch ($type) {
        case 'posts':
            $query_args = array('post_status' => 'publish', 'post_type' => 'post', 'paged' => $paged);
            if ($user > 0) {
                $query_args['author'] = $user;
            }
            $results = new WP_Query($query_args);
            break;
        case 'categories':
            $query_args = array('taxonomy' => 'category', 'paged' => $paged);
            $results = get_categories($query_args);
            break;
        default:
            $query_args = array('post_status' => 'publish', 'post_type' => 'page', 'paged' => $paged);
            if ($user > 0) {
                $query_args['author'] = $user;
            }
            $results = new WP_Query($query_args);
            break;
    }

    if ($type === 'categories') {
        include 'partials/categories-table.php';
    } else {
        include 'partials/posts-pages-table.php';
    }

    wp_die();
}
add_action('wp_ajax_all_page_url_load_filtered_data', 'all_page_url_load_filtered_data');
add_action('wp_ajax_nopriv_all_page_url_load_filtered_data', 'all_page_url_load_filtered_data');
