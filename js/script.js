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

    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // Reset previous errors
            clearErrors();

            // Validation
            let isValid = true;

            // Name
            const nameInput = document.getElementById('name');
            if (!nameInput.value.trim()) {
                showError(nameInput, 'お名前を入力してください');
                isValid = false;
            }

            // Email
            const emailInput = document.getElementById('email');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim()) {
                showError(emailInput, 'メールアドレスを入力してください');
                isValid = false;
            } else if (!emailPattern.test(emailInput.value.trim())) {
                showError(emailInput, '正しいメールアドレスの形式で入力してください');
                isValid = false;
            }

            // Type
            const typeInput = document.getElementById('type');
            if (!typeInput.value) {
                showError(typeInput, 'お問い合わせ種別を選択してください');
                isValid = false;
            }

            if (isValid) {
                // Simulate submission
                const submitBtn = contactForm.querySelector('.btn-submit');
                const originalBtnText = submitBtn.textContent;

                submitBtn.disabled = true;
                submitBtn.textContent = '送信中...';

                setTimeout(() => {
                    // Success
                    contactForm.style.display = 'none';
                    successMessage.classList.add('visible');

                    // Scroll to success message
                    successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    // Reset form
                    contactForm.reset();

                    // Restore button state (in case we want to reuse)
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }, 1500);
            }
        });
    }

    function showError(input, message) {
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
