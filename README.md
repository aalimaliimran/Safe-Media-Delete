# Safe Media Delete

Safe Media Delete is a WordPress plugin that enhances the WP Admin interface by providing additional features related to image management and deletion within the WordPress Media Library. This plugin helps prevent accidental deletion of images that are being used in various contexts across the website, such as featured images, post content, and term pages.

## Features

1. Image Upload and Selection in Term Pages
   - Adds a field on the Term Add and Term Edit pages to allow users to upload or select an existing PNG or JPEG image from the WordPress Media Library.
   - Provides an image preview functionality on the same screen using third-party libraries like CMB2.

2. Image Deletion Restrictions
   - Prevents the deletion of an image if it is set as a Featured Image in an article.
   - Prevents the deletion of an image if it is being used in the content of a post (Post Body).
   - Prevents the deletion of an image if it is being used in a Term Edit page (as implemented in point 1).
   - Displays a message to the user when attempting to delete an image that is being used elsewhere, asking them to remove the image from the associated posts or terms.
   - The interface displays the IDs of the affected post(s) or term(s) along with edit links for easy access.

3. Media Library Enhancements
   - Adds an "Attached Objects" column to the Media Library table, displaying a comma-separated list of IDs linked to the corresponding edit page.
   - Users can determine whether the ID corresponds to a post or a term, aiding in the identification of image usage.

4. REST API Functionality
   - Provides REST API endpoints under the `/assignment/v1/` namespace.
   - **Endpoint 1**: Returns details of a given image ID.
     - Response is a JSON object containing information such as ID, Date, Slug, Type (JPEG or PNG), Link, Alt text, and Attached Objects.
     - Attached Objects field contains Post or Term IDs to which the given image is attached, structured accordingly.
   - **Endpoint 2**: Deletes a given image if it is not attached to any posts or terms.
     - If the image is attached to any posts or terms, the response indicates that deletion has failed.

## Installation

1. Download the `safe-media-delete.zip` file from the latest release.
2. In your WordPress admin panel, navigate to **Plugins → Add New**.
3. Click the **Upload Plugin** button at the top of the page.
4. Choose the `safe-media-delete.zip` file and click **Install Now**.
5. After installation, activate the plugin.

## Usage

Once the plugin is activated, the additional features will be available in the WP Admin interface as described in the Features section.

### Term Pages

1. When adding or editing a term, a new field will appear allowing you to upload or select an existing PNG or JPEG image from the Media Library.
2. The selected image will be displayed as a preview on the same screen.

### Image Deletion

1. When attempting to delete an image from the Media Library, the plugin will check if the image is being used as a Featured Image, in post content, or in term pages.
2. If the image is being used, a message will be displayed indicating the associated post(s) or term(s) where the image is being used.
3. The message will include edit links for easy access to the affected post(s) or term(s).
4. To delete the image, remove it from the associated posts or terms before retrying the deletion.

### Media Library Table

1. The Media Library table will now include a new column named "Attached Objects" that displays a comma-separated list of IDs linked to the corresponding

 edit page.
2. You can determine whether the ID corresponds to a post or a term, helping identify image usage.

## REST API

The plugin provides the following REST API endpoints under the `/assignment/v1/` namespace:

1. **Endpoint 1**: `wp-json/assignment/v1/media/{id}`
   - Method: GET
   - Retrieves details of a given image ID.
   - Response format: JSON
   - Example response:
     ```json
     {
       "ID": 123,
       "Date": "2023-05-17 10:30:00",
       "Slug": "image-slug",
       "Type": "JPEG",
       "Link": "https://example.com/wp-content/uploads/2023/05/image.jpg",
       "Alt text": "Alternative text for the image",
       "Attached Objects": {
         "Posts": [1, 2, 3],
         "Terms": [4, 5]
       }
     }
     ```

2. **Endpoint 2**: `wp-json/assignment/v1/media/{id}/delete`
   - Method: GET
   - Deletes a given image if it is not attached to any posts or terms.
   - If the image is attached to any posts or terms, the response will indicate that deletion has failed.

## Contribution

Contributions to the Safe Media Delete plugin are welcome! If you encounter any issues or have suggestions for improvements, please open an issue on the [GitHub repository](https://github.com/your-repository).

## License

This plugin is licensed under the [MIT License](LICENSE).
