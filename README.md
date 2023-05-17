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
   - Provides REST API endpoints under the `wp-json/assignment/v1/media/{id}` namespace.
   - **Endpoint 1**: Returns details of a given image ID.
     - Response is a JSON object containing information such as ID, Date, Slug, Type (JPEG or PNG), Link, Alt text, and Attached Objects.
     - Attached Objects field contains Post or Term IDs to which the given image is attached, structured accordingly.
   - **Endpoint 2**: Deletes a given image if it is not attached to any posts or terms.
     - If the image is attached to any posts or terms, the response indicates that deletion has failed.

## Installation

1. Download the `safe-media-delete.zip` file. file path: https://github.com/aalimaliimran/Safe-Media-Delete/tree/main/wp-content/plugins/
2. In your WordPress admin panel, navigate to **Plugins → Add New**.
3. Click the **Upload Plugin** button at the top of the page.
4. Choose the `safe-media-delete.zip` file and click **Install Now**.
5. After installation, activate the plugin.
6. Download the plugin dependency CMB2 intall it and activate. 

## Manual Installation

Download the Plugin: Obtain the plugin files for "Safe Media Delete" and "CMB2" to your computer. You can find these files on the https://github.com/aalimaliimran/Safe-Media-Delete repository. path: wp-content/plugins/

Wordpress Installation: Install wordpress latest version on your computer localhost or server.

Upload the Plugin Files: Within the root directory of wordpress, locate the wp-content/plugins folder. Extract the "Safe Media Delete" plugin files you downloaded in the previous step. Upload the entire plugin folders (e.g., safe-media-delete) to the wp-content/plugins directory.

Install the Dependency (CMB2): Similarly, extract the CMB2 plugin files from its ZIP archive. Upload the entire plugin folder (e.g., cmb2) to the wp-content/plugins directory as well.

Activate the Dependency (CMB2): Log in to your WordPress admin panel. Navigate to the "Plugins" section and locate the CMB2 plugin. Click on the "Activate" link below the CMB2 plugin to activate it.

Activate the Plugin (Safe Media Delete): In the WordPress admin panel, go to the "Plugins" section. Look for the "Safe Media Delete" plugin in the list. Click on the "Activate" link below the plugin to activate it.

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

## Test Cases

I have previously worked on writing core PHP unit test cases for various projects. However, this is my first opportunity to write unit tests for a WordPress plugin. Unfortunately, I am encountering conflicts between the PHPUnit library and the WordPress unit test framework, which has hindered my ability to successfully execute the unit test cases.

The unit test cases are located in the `test/` folder, but they have not been tested yet. The issue arises because the WordPress functions require the WordPress testing framework to be present in order to properly test the unit test cases.

I hope this explanation clarifies the issue at hand.

– Test cases are in the source code: https://github.com/aalimaliimran/Safe-Media-Delete/wp-content/plugins/safe-media-delete/tests/ folder please find the git repository link below.


## Screenshots

![2023-05-17_11-19-02](https://github.com/aalimaliimran/Safe-Media-Delete/assets/108981157/8e49de4f-16ae-4167-a83d-c84bb67bf6b8)
![2023-05-17_11-21-46](https://github.com/aalimaliimran/Safe-Media-Delete/assets/108981157/fa38d9d6-1991-4848-9497-c5edc919c3a7)
![2023-05-17_11-22-43](https://github.com/aalimaliimran/Safe-Media-Delete/assets/108981157/2a9bf7fe-42b0-403c-a873-46a5a5006098)
![2023-05-17_11-23-37](https://github.com/aalimaliimran/Safe-Media-Delete/assets/108981157/e1993625-e339-4e05-889b-9d1d7646c1c5)
![2023-05-17_11-24-46](https://github.com/aalimaliimran/Safe-Media-Delete/assets/108981157/e84a7400-9e5d-47c2-a4bb-156f5a47407d)
![2023-05-17_11-25-20](https://github.com/aalimaliimran/Safe-Media-Delete/assets/108981157/62c4b8cd-62cf-40cb-81b7-16ce846ebbc9)


## Contribution

Contributions to the Safe Media Delete plugin are welcome! If you encounter any issues or have suggestions for improvements, please open an issue on the [GitHub repository](https://github.com/your-repository).

## License

This plugin is licensed under the [MIT License](LICENSE).
