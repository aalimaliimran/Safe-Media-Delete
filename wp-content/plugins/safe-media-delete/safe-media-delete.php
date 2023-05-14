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

