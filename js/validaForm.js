/**
 * validaForm.js
 * Client-side form validation utility functions.
 */

/**
 * Displays an error message for a given field.
 * @param {HTMLInputElement} field - The input field.
 * @param {string} message - The error message to display.
 */
function displayError(field, message) {
    clearError(field); // Clear existing error first
    field.classList.add('input-error');
    const errorSpan = document.createElement('span');
    errorSpan.className = 'error-message';
    errorSpan.textContent = message;
    // Insert after the field or its parent if it's more complex (e.g., wrapped in a div)
    if (field.parentNode.classList.contains('form-field-group') || field.parentNode.tagName === 'DIV') {
         field.parentNode.appendChild(errorSpan);
    } else {
        field.parentNode.insertBefore(errorSpan, field.nextSibling);
    }
}

/**
 * Clears the error message for a given field.
 * @param {HTMLInputElement} field - The input field.
 */
function clearError(field) {
    field.classList.remove('input-error');
    const parent = field.parentNode;
    const errorSpan = parent.querySelector('span.error-message');
    if (errorSpan) {
        parent.removeChild(errorSpan);
    }
}

/**
 * Validates if a field is not empty.
 * @param {HTMLInputElement} field - The input field.
 * @param {string} fieldName - The user-friendly name of the field.
 * @returns {boolean} - True if valid, false otherwise.
 */
function validateNotEmpty(field, fieldName) {
    if (field.value.trim() === '') {
        displayError(field, `${fieldName} is required.`);
        return false;
    }
    clearError(field);
    return true;
}

/**
 * Validates email format.
 * @param {HTMLInputElement} emailField - The email input field.
 * @returns {boolean} - True if valid, false otherwise.
 */
function validateEmail(emailField) {
    if (!validateNotEmpty(emailField, 'Email')) {
        return false;
    }
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailField.value.trim())) {
        displayError(emailField, 'Invalid email format.');
        return false;
    }
    clearError(emailField);
    return true;
}

/**
 * Validates password length.
 * @param {HTMLInputElement} passwordField - The password input field.
 * @param {number} minLength - The minimum required length for the password.
 * @returns {boolean} - True if valid, false otherwise.
 */
function validatePasswordLength(passwordField, minLength) {
    if (!validateNotEmpty(passwordField, 'Password')) {
        return false;
    }
    if (passwordField.value.length < minLength) {
        displayError(passwordField, `Password must be at least ${minLength} characters long.`);
        return false;
    }
    clearError(passwordField);
    return true;
}

/**
 * Validates if password and confirm password fields match.
 * @param {HTMLInputElement} passwordField - The password input field.
 * @param {HTMLInputElement} confirmPasswordField - The confirm password input field.
 * @returns {boolean} - True if they match, false otherwise.
 */
function validatePasswordMatch(passwordField, confirmPasswordField) {
    if (!validateNotEmpty(confirmPasswordField, 'Confirm Password')) {
        return false;
    }
    if (passwordField.value !== confirmPasswordField.value) {
        displayError(confirmPasswordField, 'Passwords do not match.');
        return false;
    }
    clearError(confirmPasswordField);
    return true;
}

// Example of how to attach to a form (will be done in respective HTML files or specific JS for those pages)
/*
document.addEventListener('DOMContentLoaded', () => {
    const exampleForm = document.getElementById('exampleForm'); // Assuming a form with this ID
    if (exampleForm) {
        exampleForm.addEventListener('submit', function(event) {
            let isValid = true;
            // Example field validation
            const nameField = document.getElementById('name');
            if (nameField && !validateNotEmpty(nameField, 'Your Name')) {
                isValid = false;
            }
            // Add more validations here...

            if (!isValid) {
                event.preventDefault(); // Stop submission if validation fails
            }
        });
    }
});
*/
