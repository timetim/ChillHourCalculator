/**
 * Custom JavaScript for the Chill Hours Calculator
 */
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handler
    const form = document.getElementById('chill-hours-form');
    const zipCodeInput = document.getElementById('zip-code');
    const errorMessage = document.getElementById('error-message');
    const loadingMessage = document.getElementById('loading-message');
    const resultsContainer = document.getElementById('results-container');
    
    // Create a results container if it doesn't exist
    if (!resultsContainer) {
        const newResultsContainer = document.createElement('div');
        newResultsContainer.id = 'results-container';
        newResultsContainer.style.display = 'none';
        form.parentNode.insertBefore(newResultsContainer, form.nextSibling);
    }
    
    // Form submit event
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Hide previous results if shown
        const resultsContainer = document.getElementById('results-container');
        if (resultsContainer) {
            resultsContainer.style.display = 'none';
        }
        
        // Hide any previous error messages
        errorMessage.style.display = 'none';
        
        // Get ZIP code value
        const zipCode = zipCodeInput.value.trim();
        
        // Validate ZIP code format
        const zipRegex = /^\d{5}(-\d{4})?$/;
        if (!zipRegex.test(zipCode)) {
            errorMessage.innerHTML = '<p>Please enter a valid 5-digit ZIP code.</p>';
            errorMessage.style.display = 'block';
            return;
        }
        
        // Show loading message
        loadingMessage.style.display = 'block';
        
        // Send AJAX request to get data
        fetch('check-zip-code.php?zip_code=' + zipCode)
            .then(response => response.json())
            .then(data => {
                // Hide loading message
                loadingMessage.style.display = 'none';
                
                if (data.success) {
                    // Display results
                    displayResults(data.data);
                } else {
                    // Show error message
                    errorMessage.innerHTML = '<p>' + data.message + '</p>';
                    errorMessage.style.display = 'block';
                }
            })
            .catch(error => {
                // Hide loading message
                loadingMessage.style.display = 'none';
                
                // Show error message
                errorMessage.innerHTML = '<p>An error occurred while retrieving data. Please try again.</p>';
                errorMessage.style.display = 'block';
                console.error('Error:', error);
            });
    });
    
    // Function to display results
    function displayResults(data) {
        const resultsContainer = document.getElementById('results-container');
        
        // Create HTML for results
        let html = `
            <div class="results-header">
                <h3>Chill Hours Results for ZIP Code: ${data.zip_code}</h3>
            </div>
            <div class="results-grid">
        `;
        
        // Add each method result
        html += createMethodCard(data.method1);
        html += createMethodCard(data.method2);
        html += createMethodCard(data.method3);
        
        html += `
            </div>
        `;
        
        // Update results container
        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
        
        // Add animation to cards (after they are in the DOM)
        setTimeout(() => {
            const cards = document.querySelectorAll('.result-card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('visible');
                }, index * 150);
            });
        }, 100);
        
        // Scroll to results
        resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Function to create a method result card
    function createMethodCard(method) {
        // Generate a unique CSS class for each method type
        let methodClass = '';
        if (method.name.includes('Utah')) {
            methodClass = 'utah-method';
        } else if (method.name.includes('North Carolina')) {
            methodClass = 'nc-method';
        } else if (method.name.includes('California')) {
            methodClass = 'ca-method';
        }
        
        return `
            <div class="result-card ${methodClass}">
                <h4>${method.name}</h4>
                <p class="method-description">${method.description}</p>
                <div class="hours-value">${method.value}</div>
                <div class="hours-label">chill hours</div>
                <p class="method-hint">Commonly used for ${method.crops || 'various fruit trees'}</p>
            </div>
        `;
    }
});