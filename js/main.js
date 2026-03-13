// =============================================
// js/main.js - Global JavaScript
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

// ---- Confirm Delete ----
document.querySelectorAll('.confirm-delete').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
        var name = this.dataset.name || 'mục này';
        if (!confirm('Bạn có chắc muốn xoá "' + name + '"? Hành động này không thể hoàn tác.')) {
            e.preventDefault();
        }
    });
});

// ---- Auto-hide alerts after 5 seconds ----
setTimeout(function () {
    document.querySelectorAll('.alert').forEach(function (alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(function () { alert.style.display = 'none'; }, 500);
    });
}, 5000);

