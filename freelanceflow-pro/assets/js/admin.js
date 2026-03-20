(function($) {
    'use strict';

    $(document).ready(function() {

        // Handle Template Selection
        $('.ffp-tpl-select').on('click', function(e) {
            e.preventDefault();
            const templateId = $(this).data('id');
            loadTemplateFields(templateId);
        });

        // Handle Load Sample Button
        $(document).on('click', '.ffp-load-sample', function(e) {
            e.preventDefault();
            const fieldId = $(this).data('field');
            const sampleValue = $(this).data('sample');
            $(`#${fieldId}`).val(sampleValue);
        });

        // Handle Document Generation
        $(document).on('click', '.ffp-generate-doc', function(e) {
            e.preventDefault();
            const format = $(this).data('format');
            const templateId = $('#ffp-active-tpl').val();
            const formData = {};

            $('.ffp-field').each(function() {
                formData[$(this).attr('id')] = $(this).val();
            });

            generateDocument(templateId, format, formData);
        });

        function loadTemplateFields(templateId) {
            const $container = $('#ffp-generator-form');
            $container.html('<p>Loading fields...</p>');

            $.ajax({
                url: ffpData.ajax_url,
                method: 'POST',
                data: {
                    action: 'ffp_get_template_fields',
                    nonce: ffpData.nonce,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success) {
                        $container.html(response.data.html);
                    } else {
                        $container.html('<p>Error loading fields.</p>');
                    }
                }
            });
        }

        function generateDocument(templateId, format, data) {
            // Document generation usually opens a new window/tab for the file stream
            const query = $.param({
                action: 'ffp_generate_document',
                nonce: ffpData.nonce,
                template_id: templateId,
                format: format,
                payload: JSON.stringify(data)
            });

            window.open(`${ffpData.ajax_url}?${query}`, '_blank');
        }

        // WP Media Uploader for Vault
        $('.ffp-upload-file').on('click', function(e) {
            e.preventDefault();
            const frame = wp.media({
                title: 'Select or Upload Document',
                button: { text: 'Add to Vault' },
                multiple: false
            });

            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                console.log('File uploaded to vault:', attachment.url);
                // Add logic to save attachment ID to custom table / user meta
            });

            frame.open();
        });

    });

})(jQuery);
