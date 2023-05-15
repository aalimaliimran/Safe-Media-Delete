jQuery(document).ready(function($) {
    // Run the script after the media modal is opened
    $(document).on('click', '.media-modal-open', function() {
        var frame = wp.media({
            title: 'Media',
            multiple: false
        });

        frame.on('open', function() {
            // Find the Attached Objects field
            var attachedObjectsField = frame.content
                .find('.attachment-details')
                .find('.attachment-info')
                .find('.attachment-display-settings')
                .find('.attached-objects');

            if (attachedObjectsField.length > 0) {
                // Hide the Attached Objects field
                attachedObjectsField.hide();
            }
        });

        frame.open();
    });
});

