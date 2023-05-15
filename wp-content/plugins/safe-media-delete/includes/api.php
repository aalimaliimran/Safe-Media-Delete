<?php
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