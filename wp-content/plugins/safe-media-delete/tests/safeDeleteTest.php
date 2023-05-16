<?php
namespace Tests;

use PHPUnit\Framework\TestCase;

/* class helloTest extends TestCase{

  public function test_function()
    {
    $expected = "hello";

    $results = "hello";

    $actual = $results;

    $this->assertSame($expected, $actual);
    }
} */

// Test cases for the safe media delete functionality
class SafeMediaDeleteTest extends WP_UnitTestCase {

    // Add Image Taxonomy & Term Unit Test Case
    public function test_custom_term_image_metabox() {
        // Create a new term and assign it to a taxonomy
        $term_id = $this->factory->term->create(array(
            'taxonomy' => 'category',
        ));

        // Set up the necessary WordPress globals
        $_POST = array(
            'object_id'   => $term_id,
            'object_type' => 'term',
            'taxonomy'    => 'category',
        );

        // Call the custom_term_image_metabox function
        custom_term_image_metabox();

        // Get the CMB2 instance for the metabox
        $cmb = cmb2_get_metabox('custom_term_image_metabox', 'term');

        // Verify that the metabox is set up correctly
        $this->assertEquals('Term Image', $cmb->prop('title'));
        $this->assertEquals(array('term'), $cmb->prop('object_types'));
        $this->assertEquals(array('category', 'post_tag'), $cmb->prop('taxonomies'));
        $this->assertEquals('side', $cmb->prop('context'));
        $this->assertEquals('default', $cmb->prop('priority'));

        // Get the CMB2 field instance for the image field
        $field_id = 'custom_term_image_image';
        $field = $cmb->get_field($field_id);

        // Verify that the image field is set up correctly
        $this->assertEquals('Image', $field->args('name'));
        $this->assertEquals($field_id, $field->args('id'));
        $this->assertEquals('file', $field->args('type'));
        $this->assertFalse($field->args('options.url'));
        $this->assertEquals('Add or Upload Image', $field->args('text.add_upload_file_text'));
        $this->assertEquals(array('image/jpeg', 'image/png'), $field->args('query_args.type'));
        $this->assertTrue($field->args('save_id'));

        // Simulate a file upload
        $_FILES[$field_id] = array(
            'name'     => 'test-image.jpg',
            'type'     => 'image/jpeg',
            'tmp_name' => '/path/to/tmp/file',
            'error'    => 0,
            'size'     => 12345,
        );

        // Save the file field value
        do_action('cmb2_save_field', $field, $term_id, $field->updated(), $field->args('query_args'));

        // Verify that the attachment ID is saved
        $attachment_id = get_term_meta($term_id, $field_id, true);
        $this->assertNotFalse(wp_attachment_is_image($attachment_id));
    }
}

/*


    // Attached Objects Unit Test Case
    public function test_get_attached_objects_with_featured_image() {
        // Create a test post
        $post_id = $this->factory->post->create();

        // Create a test media attachment and set it as the featured image for the post
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', $post_id );
        set_post_thumbnail( $post_id, $attachment_id );

        // Call the get_attached_objects function
        $attached_objects = get_attached_objects( $attachment_id );

        // Verify that the attachment is associated with the featured image in the post
        $this->assertCount( 1, $attached_objects );
        $this->assertEquals( $post_id, $attached_objects[0]['id'] );
        $this->assertEquals( 'post', $attached_objects[0]['type'] );
        $this->assertEquals( '', $attached_objects[0]['taxonomy'] );
    }

    public function test_get_attached_objects_with_attachment_in_editor() {
        // Create a test post and insert the attachment ID in the content editor
        $post_id = $this->factory->post->create();
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        $post_content = "This is a test post.\n";
        $post_content .= "<img src=\"" . wp_get_attachment_url( $attachment_id ) . "\" alt=\"Test Image\" />\n";
        $post_content .= "Lorem ipsum dolor sit amet.";

        wp_update_post( array(
            'ID'           => $post_id,
            'post_content' => $post_content,
        ) );

        // Call the get_attached_objects function
        $attached_objects = get_attached_objects( $attachment_id );

        // Verify that the attachment is associated with the post in the content editor
        $this->assertCount( 1, $attached_objects );
        $this->assertEquals( $post_id, $attached_objects[0]['id'] );
        $this->assertEquals( 'post', $attached_objects[0]['type'] );
        $this->assertEquals( '', $attached_objects[0]['taxonomy'] );
    }

    public function test_get_attached_objects_with_cmb2_term() {
        // Create a test media attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        // Create a test term and associate the media attachment using CMB2
        $term_id = $this->factory->term->create( array(
            'taxonomy' => 'category',
            'name'     => 'Test Category',
        ) );
        update_term_meta( $term_id, 'custom_term_image_image', $attachment_id );

        // Call the get_attached_objects function
        $attached_objects = get_attached_objects( $attachment_id );

        // Verify that the attachment is associated with the term
        $this->assertCount( 1, $attached_objects );
        $this->assertEquals( $attachment_id, $attached_objects[0]['id'] );
        $this->assertEquals( 'term', $attached_objects[0]['type'] );
        $this->assertEquals( 'category', $attached_objects[0]['taxonomy'] );
        $this->assertCount( 1, $attached_objects[0]['attached_terms'] );
        $this->assertEquals( $term_id, $attached_objects[0]['attached_terms'][0]['id'] );
    }

    public function test_get_attached_objects_with_no_associations() {
        // Create a test media attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        // Call the get_attached_objects function
        $attached_objects = get_attached_objects( $attachment_id );

        // Verify that there are no associations for the attachment
        $this->assertEmpty( $attached_objects );
    }

    public function test_get_attached_terms() {
        // Create a test media attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        // Create a test term and associate the media attachment using CMB2
        $term_id = $this->factory->term->create( array(
            'taxonomy' => 'category',
            'name'     => 'Test Category',
        ) );
        update_term_meta( $term_id, 'custom_term_image_image', $attachment_id );

        // Call the get_attached_terms function
        $attached_terms = get_attached_terms( $attachment_id );

        // Verify that the attachment is associated with the term
        $this->assertCount( 1, $attached_terms );
        $this->assertEquals( $term_id, $attached_terms[0] );
    }

    public function test_populate_attached_objects_column() {
        // Create a test media attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        // Create a test post and insert the attachment ID in the content editor
        $post_id = $this->factory->post->create();
        $post_content = "This is a test post.\n";
        $post_content .= "<img src=\"" . wp_get_attachment_url( $attachment_id ) . "\" alt=\"Test Image\" />\n";
        $post_content .= "Lorem ipsum dolor sit amet.";

        wp_update_post( array(
            'ID'           => $post_id,
            'post_content' => $post_content,
        ) );

        // Create a test term and associate the media attachment using CMB2
        $term_id = $this->factory->term->create( array(
            'taxonomy' => 'category',
            'name'     => 'Test Category',
        ) );
        update_term_meta( $term_id, 'custom_term_image_image', $attachment_id );

        // Capture the output of the populate_attached_objects_column function
        ob_start();
        populate_attached_objects_column( 'attached_objects', $attachment_id );
        $output = ob_get_clean();

        // Verify that the output includes the post and term information
        $this->assertContains( 'Posts: <a href="' . get_edit_post_link( $post_id ) . '">' . $post_id . '</a>', $output );
        $this->assertContains( 'Terms: <a href="' . get_edit_term_link( $term_id, 'category' ) . '">' . $term_id . '</a>', $output );
    }

    public function test_add_attached_objects_column() {
        // Add the custom column to the media library columns
        $columns = add_attached_objects_column( array() );

        // Verify that the custom column is added
        $this->assertArrayHasKey( 'attached_objects', $columns );
        $this->assertEquals( 'Attached Objects', $columns['attached_objects'] );
    }




    // Prevent Image Deletion Unit Test Case
    public function test_prevent_image_deletion_with_associated_featured_image() {
        // Create a test media attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        // Create a test post with the media attachment as the featured image
        $post_id = $this->factory->post->create( array(
            'post_title'     => 'Test Post',
            'post_content'   => 'This is a test post.',
            'post_status'    => 'publish',
            'post_type'      => 'post',
            'post_author'    => 1,
            'post_thumbnail' => $attachment_id,
        ) );

        // Call the prevent_image_deletion function
        $result = prevent_image_deletion( true, get_post( $attachment_id ) );

        // Verify that deletion is prevented and the correct error message is displayed
        $this->assertFalse( $result );
        $this->expectOutputString( 'This media is used as a featured image or is attached in the content editor in the following posts: <a href="' . get_edit_post_link( $post_id ) . '">post ID: ' . $post_id . '</a>. Please remove the media from the posts before deleting.' );
    }

    public function test_prevent_image_deletion_with_associated_content_editor_attachment() {
        // Create a test media attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        // Create a test post with the media attachment in the content editor
        $post_id = $this->factory->post->create( array(
            'post_title'   => 'Test Post',
            'post_content' => 'This is a test post with an attachment: [caption id="attachment_' . $attachment_id . '" align="alignnone" width="300"]<img src="' . wp_get_attachment_url( $attachment_id ) . '" alt="Test Image" class="wp-image-' . $attachment_id . '" />[/caption]',
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_author' => 1,
            ) );
                // Call the prevent_image_deletion function
    $result = prevent_image_deletion( true, get_post( $attachment_id ) );

    // Verify that deletion is prevented and the correct error message is displayed
    $this->assertFalse( $result );
    $this->expectOutputString( 'This media is attached in the content editor in the following posts: <a href="' . get_edit_post_link( $post_id ) . '">post ID: ' . $post_id . '</a>. Please remove the media from the posts before deleting.' );
}

public function test_prevent_image_deletion_with_associated_cmb2_term() {
    // Create a test media attachment
    $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

    // Create a test term and associate the media attachment using CMB2
    $term_id = $this->factory->term->create( array(
        'taxonomy' => 'category',
        'name'     => 'Test Category',
    ) );
    update_term_meta( $term_id, 'custom_term_image_image', $attachment_id );

    // Call the prevent_image_deletion function
    $result = prevent_image_deletion( true, get_post( $attachment_id ) );

    // Verify that deletion is prevented and the correct error message is displayed
    $this->assertFalse( $result );
    $this->expectOutputString( 'This media attachment is associated with the following terms: <a href="' . get_edit_term_link( $term_id ) . '">Term ID: ' . $term_id . '</a>. Please remove the media from the terms before deleting.' );
}

public function test_prevent_image_deletion_with_no_associations() {
    // Create a test media attachment
    $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

    // Call the prevent_image_deletion function
    $result = prevent_image_deletion( true, get_post( $attachment_id ) );

    // Verify that deletion is allowed
    $this->assertTrue( $result );
}




    // Image Details API Unit Test Case
    public function test_get_image_details_with_valid_image_id() {
        // Create a test image attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/image.jpg', 0 );
        $image = get_post( $attachment_id );

        // Mock the REST API request
        $request = new WP_REST_Request( 'GET', '/assignment/v1/media/' . $attachment_id );

        // Call the get_image_details function
        $response = get_image_details( $request );

        // Verify the response data
        $this->assertEquals( $attachment_id, $response['ID'] );
        $this->assertEquals( $image->post_date, $response['Date'] );
        $this->assertEquals( $image->post_name, $response['Slug'] );
        $this->assertEquals( get_post_mime_type( $attachment_id ), $response['Type'] );
        $this->assertEquals( wp_get_attachment_url( $attachment_id ), $response['Link'] );
        $this->assertEquals( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ), $response['Alt text'] );
        $this->assertNotEmpty( $response['Attached Objects'] );
    }

    public function test_get_image_details_with_invalid_image_id() {
        // Mock the REST API request with an invalid image ID
        $request = new WP_REST_Request( 'GET', '/assignment/v1/media/999' );

        // Call the get_image_details function
        $response = get_image_details( $request );

        // Verify the response is a WP_Error with the correct status code
        $this->assertInstanceOf( 'WP_Error', $response );
        $this->assertEquals( 404, $response->get_status() );
    }

    public function test_get_attached_objects_details() {
        // Create a test post
        $post_id = $this->factory->post->create();

        // Create a test term and assign it to the image
        $term_id = $this->factory->term->create();
        wp_set_post_terms( $post_id, $term_id, 'category' );

        // Create a test image attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/image.jpg', 0 );
        wp_set_object_terms( $attachment_id, $term_id, 'category' );
        update_post_meta( $attachment_id, '_thumbnail_id', $attachment_id );
        update_post_meta( $attachment_id, '_product_image_gallery', $attachment_id );

        // Call the get_attached_objects_details function
        $attached_objects_details = get_attached_objects_details( $attachment_id );

        // Verify the attached objects details
        $this->assertCount( 2, $attached_objects_details );
        $this->assertEquals( array(
            array( 'ID' => $post_id, 'Type' => 'post' ),
            array( 'ID' => $term_id, 'Type' => 'term' ),
        ), $attached_objects_details );
    }




    // Media Delete API Test Case
    public function test_delete_media_by_id_with_valid_media_id() {
        // Create a test media attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        // Call the delete_media_by_id function
        $response = delete_media_by_id( $attachment_id );

        // Verify the response is a WP_REST_Response with a success message
        $this->assertInstanceOf( 'WP_REST_Response', $response );
        $this->assertEquals( 200, $response->get_status() );
        $this->assertEquals( 'Media deleted successfully.', $response->get_data()['message'] );

        // Verify that the media is deleted
        $this->assertFalse( get_post( $attachment_id ) );
    }

    public function test_delete_media_by_id_with_invalid_media_id() {
        // Call the delete_media_by_id function with an invalid media ID
        $response = delete_media_by_id( 999 );

        // Verify the response is a WP_Error with the correct status code
        $this->assertInstanceOf( 'WP_Error', $response );
        $this->assertEquals( 404, $response->get_status() );
    }

    public function test_handle_delete_media_request_with_valid_media_id() {
        // Create a test media attachment
        $attachment_id = $this->factory->attachment->create_upload_object( 'path/to/media.jpg', 0 );

        // Mock the REST API request
        $request = new WP_REST_Request( 'GET', '/assignment/v1/media/' . $attachment_id . '/delete' );

        // Call the handle_delete_media_request function
        $response = handle_delete_media_request( $request );

        // Verify the response is a WP_REST_Response with a success message
        $this->assertInstanceOf( 'WP_REST_Response', $response );
        $this->assertEquals( 200, $response->get_status() );
        $this->assertEquals( 'Media deleted successfully.', $response->get_data()['message'] );

        // Verify that the media is deleted
        $this->assertFalse( get_post( $attachment_id ) );
    }

    public function test_handle_delete_media_request_with_invalid_media_id() {
        // Mock the REST API request with an invalid media ID
        $request = new WP_REST_Request( 'GET', '/assignment/v1/media/999/delete' );

        // Call the handle_delete_media_request function
        $response = handle_delete_media_request( $request );

        // Verify the response is a WP_Error with the correct status code
        $this->assertInstanceOf( 'WP_Error', $response );
        $this->assertEquals( 404, $response->get_status() );
    }
}
 */