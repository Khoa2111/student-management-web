// =============================================
// js/main.js - JavaScript cơ bản
// Student Management System
// =============================================

// ---- Scroll To Top ----
var scrollBtn = document.getElementById('scrollTopBtn');

window.addEventListener('scroll', function () {
    if (scrollBtn) {
        if (window.scrollY > 300) {
            scrollBtn.classList.add('visible');
        } else {
            scrollBtn.classList.remove('visible');
        }
    }
});

if (scrollBtn) {
    scrollBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// ---- Mobile Menu Toggle ----
var navToggle = document.getElementById('navbarToggle');
var navMenu   = document.getElementById('navbarMenu');

if (navToggle && navMenu) {
    navToggle.addEventListener('click', function () {
        navMenu.classList.toggle('open');
    });
}

// ---- Form Validation: Login ----
var loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
        var valid = true;

        var username = document.getElementById('username');
        var password = document.getElementById('password');

        clearErrors(loginForm);

        if (!username.value.trim()) {
            showError(username, 'Vui lòng nhập tên đăng nhập');
            valid = false;
        }
        if (!password.value.trim()) {
            showError(password, 'Vui lòng nhập mật khẩu');
            valid = false;
        }
        if (!valid) e.preventDefault();
    });
}

// ---- Form Validation: Register ----
var registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        var valid = true;

        var username  = document.getElementById('username');
        var email     = document.getElementById('email');
        var password  = document.getElementById('password');
        var password2 = document.getElementById('password2');

        clearErrors(registerForm);

        if (!username.value.trim() || username.value.trim().length < 3) {
            showError(username, 'Tên đăng nhập phải có ít nhất 3 ký tự');
            valid = false;
        }
        if (!email.value.trim() || !isValidEmail(email.value)) {
            showError(email, 'Vui lòng nhập email hợp lệ');
            valid = false;
        }
        if (!password.value || password.value.length < 6) {
            showError(password, 'Mật khẩu phải có ít nhất 6 ký tự');
            valid = false;
        }
        if (password2 && password.value !== password2.value) {
            showError(password2, 'Mật khẩu xác nhận không khớp');
            valid = false;
        }
        if (!valid) e.preventDefault();
    });
}

// ---- Form Validation: Student ----
var studentForm = document.getElementById('studentForm');
if (studentForm) {
    studentForm.addEventListener('submit', function (e) {
        var valid = true;

        var studentCode = document.getElementById('student_code');
        var fullName    = document.getElementById('full_name');
        var email       = document.getElementById('email');

        clearErrors(studentForm);

        if (!studentCode.value.trim()) {
            showError(studentCode, 'Vui lòng nhập mã sinh viên');
            valid = false;
        }
        if (!fullName.value.trim()) {
            showError(fullName, 'Vui lòng nhập họ và tên');
            valid = false;
        }
        if (email && email.value.trim() && !isValidEmail(email.value)) {
            showError(email, 'Email không hợp lệ');
            valid = false;
        }
        if (!valid) e.preventDefault();
    });
}

// ---- Form Validation: Class ----
var classForm = document.getElementById('classForm');
if (classForm) {
    classForm.addEventListener('submit', function (e) {
        var valid = true;

        var classCode = document.getElementById('class_code');
        var className = document.getElementById('class_name');

        clearErrors(classForm);

        if (!classCode.value.trim()) {
            showError(classCode, 'Vui lòng nhập mã lớp');
            valid = false;
        }
        if (!className.value.trim()) {
            showError(className, 'Vui lòng nhập tên lớp');
            valid = false;
        }
        if (!valid) e.preventDefault();
    });
}

// ---- Contact Form Validation ----
var contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
        var valid = true;

        var name    = document.getElementById('contact_name');
        var email   = document.getElementById('contact_email');
        var message = document.getElementById('contact_message');

        clearErrors(contactForm);

        if (!name.value.trim()) {
            showError(name, 'Vui lòng nhập họ tên');
            valid = false;
        }
        if (!email.value.trim() || !isValidEmail(email.value)) {
            showError(email, 'Vui lòng nhập email hợp lệ');
            valid = false;
        }
        if (!message.value.trim()) {
            showError(message, 'Vui lòng nhập nội dung tin nhắn');
            valid = false;
        }
        if (!valid) e.preventDefault();
    });
}

// ---- Confirm Delete ----
document.querySelectorAll('.confirm-delete').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        var name = this.dataset.name || 'mục này';
        if (!confirm('Bạn có chắc muốn xoá "' + name + '"? Hành động này không thể hoàn tác.')) {
            e.preventDefault();
        }
    });
});

// ---- Helper Functions ----
function showError(input, message) {
    input.classList.add('is-invalid');
    var span = document.createElement('span');
    span.className = 'form-error';
    span.textContent = message;
    input.parentNode.appendChild(span);
}

function clearErrors(form) {
    form.querySelectorAll('.is-invalid').forEach(function (el) {
        el.classList.remove('is-invalid');
    });
    form.querySelectorAll('.form-error').forEach(function (el) {
        el.remove();
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Auto-hide alerts after 5 seconds
setTimeout(function () {
    document.querySelectorAll('.alert').forEach(function (alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function () { alert.style.display = 'none'; }, 500);
    });
}, 5000);
