(function($) {
    'use strict';

    $(document).ready(function() {

        // Handle Repeater Rows
        $(document).on('click', '.ffp-add-row', function(e) {
            e.preventDefault();
            const repeaterId = $(this).data('repeater');
            const $rows = $(`#${repeaterId} .ffp-repeater-rows`);
            $rows.append(`
                <div class="ffp-row">
                    <input type="text" class="ffp-row-input" placeholder="Value">
                    <button class="ffp-remove-row">×</button>
                </div>
            `);
        });

        $(document).on('click', '.ffp-remove-row', function(e) {
            e.preventDefault();
            $(this).parent().remove();
        });

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
                const id = $(this).attr('id');
                if ($(this).hasClass('ffp-repeater')) {
                    const rows = [];
                    $(this).find('.ffp-row-input').each(function() {
                        rows.push([$(this).val()]);
                    });
                    formData[id] = rows;
                } else {
                    formData[id] = $(this).val();
                }
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
            // Use a hidden form to send data via POST (avoiding URL length limits)
            const $form = $('<form>', {
                action: ffpData.ajax_url,
                method: 'POST',
                target: '_blank'
            });

            $form.append($('<input>', { type: 'hidden', name: 'action', value: 'ffp_generate_document' }));
            $form.append($('<input>', { type: 'hidden', name: 'nonce', value: ffpData.nonce }));
            $form.append($('<input>', { type: 'hidden', name: 'template_id', value: templateId }));
            $form.append($('<input>', { type: 'hidden', name: 'format', value: format }));
            $form.append($('<input>', { type: 'hidden', name: 'payload', value: JSON.stringify(data) }));

            $form.appendTo('body').submit().remove();
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

                $.ajax({
                    url: ffpData.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'ffp_save_to_vault',
                        nonce: ffpData.nonce,
                        attachment_id: attachment.id
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('File added to your secure vault!');
                            location.reload();
                        }
                    }
                });
            });

            frame.open();
        });

    });

})(jQuery);
