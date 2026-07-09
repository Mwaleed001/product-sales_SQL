function togglePasswordVisibility(fieldId) {
    const passwordField = document.getElementById(fieldId);
    if (!passwordField) {
        return;
    }

    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
}


function trimLoginFields() {
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.value = emailField.value.trim();
    }

    return true;
}