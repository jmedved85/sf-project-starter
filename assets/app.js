/*
* Welcome to your app's main JavaScript file!
*
* This file will be included onto the page via the importmap() Twig function,
* which should already be in your base.html.twig.
*/

import './styles/app.css';
// import './bootstrap.js';
import './confirmation-modal.js';
import './login.js';
import './register.js';

// Tooltip initialization
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

// Flatpick initialization
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#date", {
        locale: "hr",
        dateFormat: "d. m. Y.",
        defaultDate: null
    });
});