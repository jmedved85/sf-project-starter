/* Login form validation */
const loginTemplate = document.querySelector('#login-template');

if (loginTemplate) {
    const loginForm = loginTemplate.querySelector('#login-form');
    const usernameInput = loginForm.querySelector('input[id="username"]');
    const passwordInput = loginForm.querySelector('input[id="password"]');

    if (usernameInput) {
        // Disable browser's built-in validation
        usernameInput.setAttribute('novalidate', true);

        // Add custom validation message
        usernameInput.oninvalid = function(event) {
            event.target.setCustomValidity('');

            if (event.target.validity.valueMissing) {
                event.target.setCustomValidity('Please enter your email address');
            } else if (event.target.validity.typeMismatch) {
                event.target.setCustomValidity('Please enter a valid email address containing the "@" symbol');
            }
        };

        // Clear custom validation message on input
        usernameInput.oninput = function(event) {
            event.target.setCustomValidity('');
        };
    }

    if (passwordInput) {
        passwordInput.setAttribute('novalidate', true);

        passwordInput.oninvalid = function(event) {
            event.target.setCustomValidity('');

            if (event.target.validity.valueMissing) {
                event.target.setCustomValidity('Please enter your password');
            }

        };

        passwordInput.oninput = function(event) {
            event.target.setCustomValidity('');
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.querySelector('input[type="email"]');
    
        if (emailInput) {
            emailInput.addEventListener('input', function(event) {
                if (emailInput.validity.typeMismatch) {
                    event.target.setCustomValidity('Please enter a valid email address containing the "@" symbol');
                } else {
                    event.target.setCustomValidity('');
                }
            });
        }
    });
}