<?php
// Check if attachment is attached in editor
function is_attachment_used_in_classic_editor($attachment_id) {

    global $wpdb;

    $query = $wpdb->prepare("
        SELECT ID
        FROM {$wpdb->posts}
        WHERE post_content LIKE %s
            AND post_type IN ('post', 'page')
            AND post_status IN ('publish', 'draft', 'future', 'pending', 'private')
    ", '%wp-image-' . $attachment_id . '%');

    $posts = $wpdb->get_col($query);

    return $posts;
}

// Check Media is attached with Featured or Editor or CMB2 fields Terms
function get_attached_objects($attachment_id) {
    $attached_objects = array();

    // Check if the attachment is set as the featured image in a post
    $post_args = array(
        'post_type'      => 'any',
        'meta_query'     => array(
            array(
                'key'     => '_thumbnail_id',
                'value'   => $attachment_id,
                'compare' => '='
            )
        )
    );
    $post_query = new WP_Query($post_args);

    while ($post_query->have_posts()) {
        $post_query->the_post();
        $attached_objects[] = array(
            'id'       => get_the_ID(),
            'type'     => 'post',
            'taxonomy' => '',
        );
    }

    wp_reset_postdata();

    // Check if the attachment is used in the Classic Editor
    $classic_editor_posts = is_attachment_used_in_classic_editor($attachment_id);

    foreach ($classic_editor_posts as $classic_editor_post) {
        $attached_objects[] = array(
            'id'       => $classic_editor_post,
            'type'     => 'post',
            'taxonomy' => '',
        );
    }

    // Check if the attachment is used in custom taxonomy terms
    $cmb2_attached_objects = get_cmb2_attached_objects();

    foreach ($cmb2_attached_objects as $attachment_id => $term_ids) {
        $attached_terms = array();

        foreach ($term_ids as $term_id) {
            $term = get_term($term_id);
            if ($term) {
                $attached_terms[] = array(
                    'id'       => $term->term_id,
                    'type'     => 'term',
                    'taxonomy' => $term->taxonomy,
                );
            }
        }

        if (!empty($attached_terms)) {
            $attached_objects[] = array(
                'id'              => $attachment_id,
                'type'            => 'term',
                'taxonomy'        => $term->taxonomy,
                'attached_terms'  => $attached_terms,
            );
        }
    }

    return $attached_objects;
}



/**
 * Retrieve CMB2 attached objects.
 */

 function get_cmb2_attached_objects() {
    $cmb2_attached_objects = array();

    $terms = get_terms(array(
        'taxonomy'   => array('category', 'post_tag'),
        'hide_empty' => false,
    ));

    foreach ($terms as $term) {
        $attachment_ids = get_term_meta($term->term_id, 'custom_term_image_image', true);

        if (!empty($attachment_ids)) {
            if (!is_array($attachment_ids)) {
                $attachment_ids = array($attachment_ids);
            }
            foreach ($attachment_ids as $attachment_id) {
                $cmb2_attached_objects[$attachment_id][] = $term->term_id;
            }
        }
    }



    return $cmb2_attached_objects;
}

/**
 * Get the terms to which the attachment is attached
 *
 * @param int $attachment_id
 * @return array
 */
function get_attached_terms($attachment_id) {
    $attached_terms = array();

    // Retrieve the attachment IDs associated with the CMB2 field
    $cmb2_attached_objects = get_cmb2_attached_objects();

    // Find the attachment ID based on the attachment URL
    $attachment_url = wp_get_attachment_url($attachment_id);
   
    // Loop through the attached objects array
    foreach ($cmb2_attached_objects as $url => $ids) {
        if ($url === $attachment_url) {
       
            foreach ($ids as $id) {
                $terms = wp_get_post_terms($id, 'category');

              
              
                            $attached_terms[] = $id;
                
            }
           
        }
    }

    return $attached_terms;
}


/**
 * Add custom column to the Media Library table
 *
 * @param array $columns
 * @return array
 */
function add_attached_objects_column($columns)
{
    $columns['attached_objects'] = 'Attached Objects';
    return $columns;
}
add_filter('manage_media_columns', 'add_attached_objects_column');

 function populate_attached_objects_column($column_name, $attachment_id) {
    if ($column_name === 'attached_objects') {
        $attached_objects = get_attached_objects($attachment_id);
        $attached_terms = get_attached_terms($attachment_id);
        if (!empty($attached_objects)) {
            $post_links = array();
            $term_links = array();

            foreach ($attached_objects as $attached_object) {
                $object_id = $attached_object['id'];
                $object_type = $attached_object['type'];

                if ($object_type === 'post') {
                    $object_edit_link = get_edit_post_link($object_id);
                    $post_links[] = '<a href="' . esc_url($object_edit_link) . '">' . $object_id . '</a>';
                } 
            }

            if (!empty($post_links)) {
                echo '<strong>Posts: </strong>' . implode(', ', $post_links) . '<br>';
            }

            if ($object_type === 'term') {
                   
                   

                if (!empty($attached_terms) && !is_wp_error($attached_terms)) {
                    $term_links = array();
        
                    foreach ($attached_terms as $term) {
                        $term_edit_links = array();
                        
                        $term_edit_links['Category'] = get_edit_term_link($term, 'category');
                        $term_edit_links['Tag'] = get_edit_term_link($term, 'post_tag');
                        
                        foreach ($term_edit_links as $taxonomy => $term_edit_link) {
                            if ($term_edit_link) {
                                $term_links[] = '<a href="' . esc_url($term_edit_link) . '">' . $term . '</a>';
                            }
                        }
                    }
        
                   
                }
            }

             if (!empty($term_links)) {
                echo '<strong>Terms: </strong>' . implode(', ', $term_links);
            } 
        }
    }
}

add_action('manage_media_custom_column', 'populate_attached_objects_column', 10, 2); 


// Enqueue custom script for the media modal
 function my_plugin_add_attached_objects_field_to_media_modal($form_fields, $attachment)
{
    $attached_objects = get_attached_objects($attachment->ID);
    $attached_terms = get_attached_terms($attachment->ID);
    
    if (!empty($attached_objects)) {
        $html = '<div>';
       

        $post_links = array();
        $term_links = array();

        foreach ($attached_objects as $attached_object) {
            $object_id = $attached_object['id'];
            $object_type = $attached_object['type'];
            $object_edit_link = '';

            if ($object_type === 'post') {
                $object_edit_link = get_edit_post_link($object_id);
                $post_links[] = '<a href="' . esc_url($object_edit_link) . '">' . $object_id . '</a>';
            } 
        }

        if (!empty($post_links)) {
            $html .= '<strong>Posts: </strong>' . implode(', ', $post_links) . '<br>';
        }

        if ($object_type === 'term') {
            if (!empty($attached_terms) && !is_wp_error($attached_terms)) {
                $term_links = array();
    
                foreach ($attached_terms as $term) {
                    $term_edit_links = array();
                    
                    $term_edit_links['Category'] = get_edit_term_link($term, 'category');
                    $term_edit_links['Tag'] = get_edit_term_link($term, 'post_tag');
                    
                    foreach ($term_edit_links as $taxonomy => $term_edit_link) {
                        if ($term_edit_link) {
                            $term_links[] = '<a href="' . esc_url($term_edit_link) . '">' . $term . '</a>';
                        }
                    }
                }
    
               
            }
        }
    

        if (!empty($term_links)) {
            $html .= '<strong>Terms: </strong>' . implode(', ', $term_links);
        }

        $html .= '</div>';

        $form_fields['attached_objects'] = array(
            'label' => 'Attached Objects',
            'input' => 'html',
            'html'  => $html,
            'required' => false, // Set required to false
        );
    }
    $form_fields['attached_objects']['show_in_edit'] = false;
    $form_fields['attached_objects']['show_in_modal'] = true;

    return $form_fields;
}


add_filter('attachment_fields_to_edit', 'my_plugin_add_attached_objects_field_to_media_modal', 10, 2);
