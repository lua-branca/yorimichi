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

    // URL Parameter Logic (Pre-fill Event Name)
    const urlParams = new URLSearchParams(window.location.search);
    const eventParam = urlParams.get('event');
    const eventSelect = document.getElementById('event_name');

    if (eventParam && eventSelect) {
        // Check if the option exists
        let optionExists = false;
        for (let i = 0; i < eventSelect.options.length; i++) {
            if (eventSelect.options[i].value === eventParam) {
                eventSelect.selectedIndex = i;
                optionExists = true;
                break;
            }
        }
        // If option doesn't exist (e.g. old event), maybe add it or just ignore? 
        // For now, we only pre-select if it matches.
    }

    // Form Validation Logic
    // Contact form now points to local PHP script instead of GAS directly
    setupFormValidation('.contact-form', 'php/contact.php');

    // Event Application Form
    // TODO: Replace with the actual GAS Web App URL provided by the user
    const EVENT_GAS_URL = 'https://script.google.com/macros/s/AKfycbwJ-0uWCJsY5GPIzpH7MkZtNtSB2fUrGRRXqAYrWGj5_Ly4JeduDk9Q3z5nE3TTh16Mdw/exec';
    // setupFormValidation('.event-apply-form', EVENT_GAS_URL);

    // Member Type Logic for Event Form
    const eventForm = document.querySelector('.event-apply-form');
    if (eventForm) {
        const memberTypeRadios = eventForm.querySelectorAll('input[name="member_type"]');
        const introducerGroup = document.getElementById('introducer-group');
        const introducerInput = document.getElementById('introducer');

        memberTypeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.value === 'ゲスト') {
                    introducerGroup.style.display = 'block';
                    introducerInput.setAttribute('required', 'required');
                } else {
                    introducerGroup.style.display = 'none';
                    introducerInput.removeAttribute('required');
                    introducerInput.value = ''; // Clear input
                    // Remove error state if hidden
                    introducerGroup.classList.remove('has-error');
                }
            });
        });
    }

    function setupFormValidation(formSelector, gasUrl) {
        const form = document.querySelector(formSelector);
        if (!form) return;

        const successMessage = document.getElementById('form-success');
        const submitButton = form.querySelector('.btn-submit');

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            let isValid = true;

            // Reset errors
            form.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('has-error');
            });

            // Generic Required Field Validation
            form.querySelectorAll('[required]').forEach(input => {
                if (!input.value.trim()) {
                    showError(input);
                    isValid = false;
                }
            });

            // Radio Button Validation (specifically for member_type)
            const memberTypeRadios = form.querySelectorAll('input[name="member_type"]');
            if (memberTypeRadios.length > 0) {
                let checked = false;
                memberTypeRadios.forEach(radio => {
                    if (radio.checked) checked = true;
                });
                if (!checked) {
                    // Find the container to show error
                    const group = memberTypeRadios[0].closest('.form-group');
                    group.classList.add('has-error');
                    isValid = false;
                }
            }

            // Email Validation
            const emailInput = form.querySelector('input[type="email"]');
            if (emailInput && !validateEmail(emailInput.value)) {
                showError(emailInput);
                isValid = false;
            }

            if (isValid) {
                if (gasUrl === 'YOUR_GAS_WEB_APP_URL_HERE') {
                    alert('システムエラー: 送信先URLが設定されていません。管理者にお問い合わせください。');
                    return;
                }

                // Show loading state
                const originalBtnText = submitButton.textContent;
                submitButton.textContent = '送信中...';
                submitButton.disabled = true;

                // Prepare data for GAS
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const pair of formData.entries()) {
                    params.append(pair[0], pair[1]);
                }

                // Send data to GAS
                fetch(gasUrl, {
                    method: 'POST',
                    body: params
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.result === 'success') {
                            showSuccess(form, successMessage);
                        } else {
                            console.error('GAS Error:', data);
                            alert('送信に失敗しました。もう一度お試しください。');
                            resetButton(submitButton, originalBtnText);
                        }
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                        alert('送信エラーが発生しました。');
                        resetButton(submitButton, originalBtnText);
                    });
            }
        });
    }

    function showError(input) {
        const formGroup = input.closest('.form-group');
        formGroup.classList.add('has-error');
    }

    function showSuccess(form, successMessage) {
        form.style.display = 'none';
        if (successMessage) {
            successMessage.classList.add('visible');
            successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function resetButton(button, originalText) {
        button.textContent = originalText || '送信する';
        button.disabled = false;
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
});
