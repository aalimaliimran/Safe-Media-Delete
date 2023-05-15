<?php
/**
 * Plugin Name: Safe Media Delete
 * Plugin URI: [Plugin URI]
 * Description: Safe Media Delete
 * Version: 1.0.0
 * Author: Ali Imran
 * Author URI: https://github.com/aalimaliimran/Safe-Media-Delete
 * License: GPL2
 */

// Add CMB2 library
require_once plugin_dir_path(__FILE__) . '../cmb2/init.php';

// CMB2 image field for taxonomy terms
function custom_term_image_metabox()
{
    $prefix = 'custom_term_image_';

    $cmb = new_cmb2_box(array(
        'id'           => $prefix . 'metabox',
        'title'        => __('Term Image', 'cmb2'),
        'object_types' => array('term'), // Set the object types where the metabox should appear
        'taxonomies'   => array('category', 'post_tag'), // Add the taxonomies where the metabox should appear
        'context'      => 'side', // Display the metabox in the sidebar
        'priority'     => 'default',
    ));

    $cmb->add_field(array(
        'name'       => __('Image', 'cmb2'),
        'id'         => $prefix . 'image',
        'type'       => 'file',
        'options'    => array(
            'url' => false,
        ),
        'text'       => array(
            'add_upload_file_text' => 'Add or Upload Image', // Customize the button text
        ),
        'query_args' => array(
            'type' => array(
                'image/jpeg',
                'image/png',
            ),
        ),
        'save_id'    => true, // Save attachment ID instead of URL
    ));
}
add_action('cmb2_admin_init', 'custom_term_image_metabox');


// Chech if attachment is attached in editor
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





 


/* API */

// Image Details API

// Register REST API endpoints
 function register_assignment_api_endpoints() {
    register_rest_route( 'assignment/v1', '/media/(?P<id>\d+)', array(
        'methods'  => 'GET',
        'callback' => 'get_image_details',
    ) );


}
add_action( 'rest_api_init', 'register_assignment_api_endpoints' );
// Callback function for retrieving image details
 function get_image_details( $request ) {
    $image_id = $request->get_param( 'id' );
    $image = get_post( $image_id );

    if ( empty( $image ) || 'attachment' !== $image->post_type ) {
        return new WP_Error( 'invalid_image', 'Invalid image ID', array( 'status' => 404 ) );
    }

    $image_details = array(
        'ID'          => $image->ID,
        'Date'        => $image->post_date,
        'Slug'        => $image->post_name,
        'Type'        => get_post_mime_type( $image->ID ),
        'Link'        => wp_get_attachment_url( $image->ID ),
        'Alt text'    => get_post_meta( $image->ID, '_wp_attachment_image_alt', true ),
        'Attached Objects' => get_attached_objects_details( $image->ID ),
    );

    return $image_details;
}

function get_attached_objects_details( $image_id ) {
    $attached_objects = get_attached_objects( $image_id );
    $attached_objects_details = array();

    foreach ( $attached_objects as $attached_object ) {
        if ( 'post' === $attached_object['type'] ) {
          
            $attached_objects_details[] = array(
                'ID'   => $attached_object['id'],
                'Type' => 'post',
            );
        }
        } foreach ( $attached_objects as $attached_object ) {
            if ( 'term' === $attached_object['type'] ) {
            $attached_terms = get_attached_terms( $image_id );
            $term_objects_details = array();
            
            foreach ( $attached_terms as $attached_term ) {
                $term_objects_details[] = array(
                    'ID'   => $attached_term,
                    'Type' => 'term',
                );
            }

        }
        }
        if ( ! empty( $term_objects_details ) ) {
            $attached_objects_details = array_merge( $attached_objects_details, $term_objects_details );
        }
    
    
    return $attached_objects_details;
}



// Prevent Media Delete API

function delete_media_by_id($id) {
    // Check if the attachment exists
    $attachment = get_post($id);
    if (!$attachment || $attachment->post_type !== 'attachment') {
        return new WP_Error('media_not_found', 'Media not found.', array('status' => 404));
    }

    // Check if the attachment is attached as a featured image
    $is_featured_image = get_posts(array(
        'post_type'      => 'any',
        'meta_key'       => '_thumbnail_id',
        'meta_value'     => $id,
        'posts_per_page' => 1,
    ));

    if ($is_featured_image) {
        return new WP_Error('media_attached', 'Deletion failed. Media is set as a featured image.', array('status' => 400));
    }

    // Check if the attachment is used in the block editor

    global $wpdb;
    $query = $wpdb->prepare("
        SELECT ID
        FROM {$wpdb->posts}
        WHERE post_content LIKE %s
            AND post_type IN ('post', 'page')
            AND post_status IN ('publish', 'draft', 'future', 'pending', 'private')
    ", '%wp-image-' . $id . '%');
    $is_block_editor_image = $wpdb->get_col($query);

    if ($is_block_editor_image) {
        return new WP_Error('media_attached', 'Deletion failed. Media is used in the block editor.', array('status' => 400));
    }

    // Check if the attachment is associated with CMB2 image fields in taxonomy terms
    $is_cmb2_image_field_attachment = get_attached_terms($id);
  
    if ($is_cmb2_image_field_attachment) {
        return new WP_Error('media_attached', 'Deletion failed. Media is associated with CMB2 image fields in taxonomy terms.', array('status' => 400));
    }

    // Delete the attachment
    $result = wp_delete_attachment($id, true);
    if ($result === false) {
        return new WP_Error('media_deletion_failed', 'Failed to delete the media.', array('status' => 500));
    }

    // Media deleted successfully
    return new WP_REST_Response(array('message' => 'Media deleted successfully.'), 200);
}

// Register the custom REST API route
function register_delete_media_route() {
    register_rest_route('assignment/v1', '/media/(?P<id>\d+)/delete', array(
        'methods' => 'GET',
        'callback' => 'handle_delete_media_request',
    ));
}
add_action('rest_api_init', 'register_delete_media_route');

// Handle the POST request to delete the media
function handle_delete_media_request($request) {
    $media_id = $request->get_param('id');

    // Delete the media
    $response = delete_media_by_id($media_id);
    return $response;
} 
/* API */