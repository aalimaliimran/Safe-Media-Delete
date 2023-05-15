<?php
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