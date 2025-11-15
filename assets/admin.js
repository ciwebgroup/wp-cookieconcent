/**
 * Admin JavaScript for Cookie Consent Manager
 *
 * @package WP_CookieConsent
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize color pickers if present
        if ($.fn.wpColorPicker) {
            $('.wp-cookieconsent-color-picker').wpColorPicker();
        }

        // Add confirmation for reset actions
        $('.wp-cookieconsent-reset').on('click', function(e) {
            if (!confirm('Are you sure you want to reset all settings to default? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
        });

        // Form validation
        $('form[action="options.php"]').on('submit', function(e) {
            var isValid = true;
            var errors = [];

            // Validate required fields
            $(this).find('[required]').each(function() {
                if ($(this).val() === '') {
                    isValid = false;
                    errors.push($(this).attr('name') + ' is required');
                    $(this).css('border-color', '#dc3232');
                } else {
                    $(this).css('border-color', '');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields:\n' + errors.join('\n'));
                return false;
            }
        });

        // Live preview functionality (optional enhancement)
        var previewTimeout;
        $('.wp-cookieconsent-settings-container input, .wp-cookieconsent-settings-container select, .wp-cookieconsent-settings-container textarea').on('change input', function() {
            clearTimeout(previewTimeout);
            previewTimeout = setTimeout(function() {
                // Future: trigger live preview update
            }, 500);
        });

        // Tab navigation (for future enhancement)
        $('.wp-cookieconsent-tabs a').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');

            $('.wp-cookieconsent-tabs li').removeClass('active');
            $(this).parent().addClass('active');

            $('.wp-cookieconsent-tab-content').hide();
            $(target).show();
        });

        // Toggle advanced settings
        $('.wp-cookieconsent-toggle-advanced').on('click', function(e) {
            e.preventDefault();
            $(this).next('.wp-cookieconsent-advanced-settings').slideToggle();
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');
        });

        // Copy configuration to clipboard (for debugging)
        $('.wp-cookieconsent-copy-config').on('click', function(e) {
            e.preventDefault();
            var config = $(this).data('config');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(JSON.stringify(config, null, 2))
                    .then(function() {
                        alert('Configuration copied to clipboard!');
                    })
                    .catch(function(err) {
                        console.error('Failed to copy configuration:', err);
                    });
            } else {
                // Fallback for older browsers
                var textArea = document.createElement('textarea');
                textArea.value = JSON.stringify(config, null, 2);
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('Configuration copied to clipboard!');
                } catch (err) {
                    console.error('Failed to copy configuration:', err);
                }
                document.body.removeChild(textArea);
            }
        });

        // Handle checkbox labels to improve UX
        $('.form-table label[for]').on('click', function() {
            var inputId = $(this).attr('for');
            if (inputId) {
                $('#' + inputId).trigger('click');
            }
        });

        // Tooltip functionality
        if ($.fn.tooltip) {
            $('.wp-cookieconsent-tooltip').tooltip({
                position: {
                    my: 'center bottom-20',
                    at: 'center top',
                    using: function(position, feedback) {
                        $(this).css(position);
                        $('<div>')
                            .addClass('arrow')
                            .addClass(feedback.vertical)
                            .addClass(feedback.horizontal)
                            .appendTo(this);
                    }
                }
            });
        }

        // Initialize sortable elements if present
        if ($.fn.sortable) {
            $('.wp-cookieconsent-sortable').sortable({
                handle: '.wp-cookieconsent-drag-handle',
                placeholder: 'wp-cookieconsent-sortable-placeholder',
                update: function(event, ui) {
                    // Update hidden field with new order
                    var order = $(this).sortable('toArray', {attribute: 'data-id'});
                    $(this).next('.wp-cookieconsent-order-field').val(order.join(','));
                }
            });
        }

        // Character counter for text fields
        $('.wp-cookieconsent-char-counter').each(function() {
            var input = $(this);
            var maxLength = input.attr('maxlength');
            if (maxLength) {
                var counter = $('<span class="character-count"></span>');
                input.after(counter);

                var updateCounter = function() {
                    var length = input.val().length;
                    counter.text(length + ' / ' + maxLength);

                    if (length > maxLength * 0.9) {
                        counter.css('color', '#dc3232');
                    } else {
                        counter.css('color', '#646970');
                    }
                };

                input.on('input', updateCounter);
                updateCounter();
            }
        });
    });

})(jQuery);

