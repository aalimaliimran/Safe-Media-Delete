<?php
// Prevent Image Deletion for Media List View
function prevent_image_deletion($delete, $post) {
    $post_id = $post->ID;
  
    // Check if the attachment is used as a featured image
    $posts_with_image = new WP_Query(array(
        'meta_query' => array(
            array(
                'key'     => '_thumbnail_id',
                'value'   => $post_id,
                'compare' => '=',
            ),
        ),
        'post_type'      => 'any',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));
   
    if ($posts_with_image->have_posts()) {
        $associated_post_ids = $posts_with_image->posts;
        $post_links = array();
    
        foreach ($associated_post_ids as $associated_post_id) {
            $post_links[] = '<a href="' . get_edit_post_link($associated_post_id) . '">post ID: ' . $associated_post_id . '</a>';
        }
    
        $error_message = 'This media is used as a featured image or is attached in the content editor in the following posts: ' . implode(', ', $post_links) . '. Please remove the media from the posts before deleting.';
        wp_die($error_message);
    }

    // Check if the attachment is used in the content editor
    global $wpdb;
    $query = $wpdb->prepare("
        SELECT ID
        FROM {$wpdb->posts}
        WHERE post_content LIKE %s
            AND post_type IN ('post', 'page')
            AND post_status IN ('publish', 'draft', 'future', 'pending', 'private')
    ", '%wp-image-' . $post_id . '%');
    $posts = $wpdb->get_col($query);

    if (!empty($posts)) {
        $post_links = array();

        foreach ($posts as $associated_post_id) {
            $post_links[] = '<a href="' . get_edit_post_link($associated_post_id) . '">post ID: ' . $associated_post_id . '</a>';
        }

        $error_message = 'This media is attached in the content editor in the following posts: ' . implode(', ', $post_links) . '. Please remove the media from the posts before deleting.';
        wp_die($error_message);
    }

    // Check if the attachment is used in custom terms created by CMB2
    $associated_terms = get_attached_terms($post_id);
    if (!empty($associated_terms)) {
        $term_links = array();

        foreach ($associated_terms as $term) {
            $term_links[] = '<a href="' . get_edit_term_link($term) . '">Term ID: ' . $term. '</a>';
        }

        $error_message = 'This media attachment is associated with the following terms: ' . implode(', ', $term_links) . '. Please remove the media from the terms before deleting.';
        wp_die($error_message);
    }


    return $delete;
}

add_filter('pre_delete_attachment', 'prevent_image_deletion', 10, 2);
// Prevent Image Deletion for Media List View