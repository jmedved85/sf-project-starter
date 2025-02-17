/* Register form validation */
const registerTemplate = document.querySelector('#register-template');

if (registerTemplate) {
    const registerForm = registerTemplate.querySelector('#register-form');
    const firstNameInput = registerForm.querySelector('input[id="registration_form_firstName"]');
    const lastNameInput = registerForm.querySelector('input[id="registration_form_lastName"]');
    const emailInput = registerForm.querySelector('input[id="registration_form_email"]');
    const passwordInput = registerForm.querySelector('input[id="registration_form_plainPassword"]');

    if (firstNameInput) {
        // Disable browser's built-in validation
        firstNameInput.setAttribute('novalidate', true);

        // Add custom validation message
        firstNameInput.oninvalid = function(event) {
            event.target.setCustomValidity('');

            if (event.target.validity.valueMissing) {
                event.target.setCustomValidity('Please enter your first name');
            }
        };

        // Clear custom validation message on input
        firstNameInput.oninput = function(event) {
            event.target.setCustomValidity('');
        };
    } 

    if (lastNameInput) {
        lastNameInput.setAttribute('novalidate', true);

        lastNameInput.oninvalid = function(event) {
            event.target.setCustomValidity('');

            if (event.target.validity.valueMissing) {
                event.target.setCustomValidity('Please enter your last name');
            }

        };

        lastNameInput.oninput = function(event) {
            event.target.setCustomValidity('');
        };
    }

    if (emailInput) {
        emailInput.setAttribute('novalidate', true);

        emailInput.oninvalid = function(event) {
            event.target.setCustomValidity('');

            if (event.target.validity.valueMissing) {
                event.target.setCustomValidity('Please enter your email address');
            } else if (event.target.validity.typeMismatch) {
                event.target.setCustomValidity('Please enter a valid email address containing the "@" symbol');
            }
        };

        emailInput.oninput = function(event) {
            event.target.setCustomValidity('');
        };
    }

    if (passwordInput) {
        passwordInput.setAttribute('novalidate', true);

        passwordInput.oninvalid = function(event) {
            event.target.setCustomValidity('');

            if (event.target.validity.valueMissing) {
                event.target.setCustomValidity('Please enter a password');
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