
/**
 * Chill Hours Calculator frontend JavaScript
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Form submission handler
        $('#chill-hours-form').on('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            // Get ZIP code
            var zipCode = $('#zip-code').val().trim();
            console.log('ZIP code:', zipCode);
            
            // Validate ZIP code format
            if (!validateZipCode(zipCode)) {
                showError('Please enter a valid 5-digit ZIP code.');
                return;
            }
            
            // Show loading indicator
            showLoading();
            
            // Hide previous results or errors
            $('#chill-hours-results').hide();
            $('#chill-hours-error').hide();
            
            console.log('Sending AJAX request');
            // Send AJAX request
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    action: 'get_chill_hours',
                    zip_code: zipCode
                },
                beforeSend: function() {
                    console.log('AJAX request starting');
                },
                success: function(response) {
                    hideLoading();
                    try {
                        response = JSON.parse(response);
                        if (response.success) {
                            displayResults(response.data);
                        } else {
                            showError(response.data.message || 'An error occurred while retrieving chill hours data.');
                        }
                    } catch(e) {
                        showError('Invalid response from server.');
                    }
                },
                error: function() {
                    hideLoading();
                    showError('Failed to connect to the server. Please try again later.');
                }
            });
        });
        
        function validateZipCode(zipCode) {
            var zipRegex = /^\d{5}(-\d{4})?$/;
            return zipRegex.test(zipCode);
        }
        
        function showError(message) {
            $('#error-message').text(message);
            $('#chill-hours-error').show();
            $('#chill-hours-results').hide();
            $('#chill-hours-loading').hide();
        }
        
        function showLoading() {
            $('#chill-hours-loading').show();
            $('#chill-hours-results').hide();
            $('#chill-hours-error').hide();
        }
        
        function hideLoading() {
            $('#chill-hours-loading').hide();
        }
        
        function displayResults(data) {
            // Update ZIP code display
            $('#result-zip-code').text(data.zip_code);
            
            // Update method 1 data
            $('#method1-name').text(data.method1.name);
            $('#method1-description').text(data.method1.description);
            $('#method1-value').text(data.method1.value);
            
            // Update method 2 data
            $('#method2-name').text(data.method2.name);
            $('#method2-description').text(data.method2.description);
            $('#method2-value').text(data.method2.value);
            
            // Update method 3 data
            $('#method3-name').text(data.method3.name);
            $('#method3-description').text(data.method3.description);
            $('#method3-value').text(data.method3.value);
            
            // Show results
            $('#chill-hours-results').fadeIn();
        }
    });
})(jQuery);
