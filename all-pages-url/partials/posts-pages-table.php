<?php
$type = isset($type) ? $type : 'pages';
$paged = isset($paged) ? $paged : 1;
$query = $results;
$entity = $type === 'posts' ? 'Post' : 'Page';
?>
<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Publish Date</th>
            <th><?php echo esc_html($entity); ?> ID</th>
            <th>Title</th>
            <th><?php echo esc_html($entity); ?> URL</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1 + (($paged - 1) * $query->query_vars['posts_per_page']);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                echo '<tr>';
                echo '<td>' . esc_html($i++) . '</td>';
                echo '<td>' . esc_html(get_the_date()) . '</td>';
                echo '<td>' . esc_html(get_the_ID()) . '</td>';
                echo '<td>' . esc_html(get_the_title()) . '</td>';
                echo '<td>' . esc_url(get_permalink()) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="5">No ' . esc_html(strtolower($entity)) . 's found.</td></tr>';
        }
        wp_reset_postdata();
        ?>
    </tbody>
</table>
<div class="pagination">
    <?php
    echo esc_html(paginate_links(array(
        'total' => $query->max_num_pages,
        'current' => $paged,
        'format' => '?paged=%#%',
    )));
    ?>
</div>
