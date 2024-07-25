<?php
$paged = isset($paged) ? $paged : 1;
?>
<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Category ID</th>
            <th>Title</th>
            <th>Category URL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1 + (($paged - 1) * get_option('posts_per_page'));
        if ($results) {
            foreach ($results as $category) {
                echo '<tr>';
                echo '<td>' . esc_html($i++) . '</td>';
                echo '<td>' . esc_html($category->term_id) . '</td>';
                echo '<td>' . esc_html($category->name) . '</td>';
                echo '<td>' . esc_url(get_category_link($category->term_id)) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4">No categories found.</td></tr>';
        }
        ?>
    </tbody>
</table>
<div class="pagination">
    <?php
    echo esc_html(paginate_links(array(
        'total' => ceil(count(get_categories()) / get_option('posts_per_page')),
        'current' => $paged,
        'format' => '?paged=%#%',
    )));
    ?>
</div>
