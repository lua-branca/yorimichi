document.addEventListener('DOMContentLoaded', () => {
    console.log('Yorimichi Living LP Loaded');

    // Hamburger Menu Logic
    const hamburger = document.querySelector('.hamburger-menu');
    const nav = document.querySelector('.site-nav');
    const navLinks = document.querySelectorAll('.nav-list a');

    if (hamburger && nav) {
        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            nav.classList.toggle('active');
        });

        // Close menu when a link is clicked
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                nav.classList.remove('active');
            });
        });
    }

    // Form Validation Logic
    const contactForm = document.querySelector('.contact-form');
    const successMessage = document.getElementById('form-success');
    const submitButton = document.querySelector('.btn-submit');

    // Google Apps Script Web App URL
    const GAS_URL = 'https://script.google.com/macros/s/AKfycbwWncIpV4fsbO91QLlJdSInUTiTUa9z58l4bEysnG1Bomm_55Li81yoWZDvOJ4ZUMpb-g/exec';

    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            let isValid = true;

            // Reset errors
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('has-error');
            });

            // Name Validation
            const nameInput = document.getElementById('name');
            if (!nameInput.value.trim()) {
                showError(nameInput);
                isValid = false;
            }

            // Email Validation
            const emailInput = document.getElementById('email');
            if (!validateEmail(emailInput.value)) {
                showError(emailInput);
                isValid = false;
            }

            // Type Validation
            const typeInput = document.getElementById('type');
            if (!typeInput.value) {
                showError(typeInput);
                isValid = false;
            }

            if (isValid) {
                // Show loading state
                const originalBtnText = submitButton.textContent;
                submitButton.textContent = '送信中...';
                submitButton.disabled = true;

                // Prepare data for GAS
                const formData = new FormData(contactForm);

                // If URL is not set yet, simulate success (for demo/testing)
                if (GAS_URL === 'YOUR_GAS_WEB_APP_URL_HERE') {
                    console.warn('GAS URL not set. Simulating success.');
                    setTimeout(() => {
                        showSuccess();
                    }, 1500);
                    return;
                }

                // Send data to GAS
                fetch(GAS_URL, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.result === 'success') {
                            showSuccess();
                        } else {
                            alert('送信に失敗しました。もう一度お試しください。');
                            resetButton();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('送信エラーが発生しました。');
                        resetButton();
                    });
            }
        });
    }

    function showError(input) {
        const formGroup = input.closest('.form-group');
        formGroup.classList.add('has-error');
        input.classList.add('error');

        // Check if error message element exists, if not create it
        let errorMsg = formGroup.querySelector('.error-message');
        if (!errorMsg) {
            errorMsg = document.createElement('p');
            errorMsg.className = 'error-message';
            formGroup.appendChild(errorMsg);
        }
        errorMsg.textContent = message;
    }

    function clearErrors() {
        const errorGroups = document.querySelectorAll('.form-group.has-error');
        errorGroups.forEach(group => {
            group.classList.remove('has-error');
            const input = group.querySelector('.form-input, .form-select, .form-textarea');
            if (input) input.classList.remove('error');
        });
    }
});
