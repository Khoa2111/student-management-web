# 🎓 Student Management System

Website quản lý sinh viên được xây dựng bằng PHP thuần, MySQL, HTML và CSS – phù hợp làm đồ án môn Lập trình Web cho sinh viên năm 2.

## 🛠️ Công nghệ sử dụng

- **PHP** thuần (không framework)
- **MySQL** / MariaDB
- **HTML5** + **CSS3** (Flexbox, Grid)
- **JavaScript** cơ bản (form validation, scroll-to-top, mobile menu)
- Tương thích **XAMPP** / **Laragon**

## ✨ Tính năng chính

- 🔐 Đăng ký / Đăng nhập / Đăng xuất
- 👥 Quản lý sinh viên (CRUD: thêm, sửa, xoá, xem)
- 🏫 Quản lý lớp học (CRUD)
- 🔍 Tìm kiếm và lọc sinh viên theo lớp
- 📊 Dashboard tổng quan với thống kê
- 📚 Xem môn học và điểm số của từng sinh viên

## 🗄️ Cơ sở dữ liệu

5 bảng có quan hệ với nhau:

| Bảng | Mô tả |
|------|-------|
| `users` | Tài khoản đăng nhập |
| `classes` | Lớp học |
| `students` | Thông tin sinh viên |
| `subjects` | Môn học |
| `enrollments` | Đăng ký môn học & điểm số |

## 📁 Cấu trúc thư mục

```
student-management-web/
├── config/
│   └── config.php          # Kết nối database & cấu hình chung
├── includes/
│   ├── header.php           # HTML head
│   ├── navbar.php           # Thanh điều hướng
│   └── footer.php           # Footer
├── auth/
│   ├── login.php            # Trang đăng nhập
│   ├── register.php         # Trang đăng ký
│   └── logout.php           # Xử lý đăng xuất
├── students/
│   ├── index.php            # Danh sách sinh viên
│   ├── add.php              # Thêm sinh viên
│   ├── edit.php             # Sửa sinh viên
│   ├── view.php             # Xem chi tiết sinh viên
│   └── delete.php           # Xoá sinh viên
├── classes/
│   ├── index.php            # Danh sách lớp học
│   ├── add.php              # Thêm lớp học
│   ├── edit.php             # Sửa lớp học
│   └── delete.php           # Xoá lớp học
├── css/
│   └── style.css            # CSS chính (Flexbox + Grid, responsive)
├── js/
│   └── main.js              # JavaScript cơ bản
├── database/
│   └── schema.sql           # Script tạo database và dữ liệu mẫu
├── index.php                # Trang chủ
├── about.php                # Trang giới thiệu
├── contact.php              # Trang liên hệ
└── dashboard.php            # Dashboard quản lý
```

## 🚀 Hướng dẫn cài đặt

### Yêu cầu
- XAMPP hoặc Laragon (PHP 7.4+, MySQL 5.7+)

### Các bước cài đặt

1. **Sao chép project** vào thư mục web server:
   - XAMPP: `C:/xampp/htdocs/student-management-web/`
   - Laragon: `C:/laragon/www/student-management-web/`

2. **Tạo database:**
   - Mở phpMyAdmin (`http://localhost/phpmyadmin`)
   - Tạo database mới tên `student_management`
   - Import file `database/schema.sql`

3. **Cấu hình kết nối database** (nếu cần):
   - Mở file `config/config.php`
   - Chỉnh sửa `DB_HOST`, `DB_USER`, `DB_PASS` cho phù hợp

4. **Truy cập website:**
   - Mở trình duyệt và vào `http://localhost/student-management-web`

### Tài khoản demo
- **Tên đăng nhập:** `admin`
- **Mật khẩu:** `password`

> ⚠️ Mật khẩu mặc định trong `schema.sql` được hash bằng `PASSWORD_DEFAULT` của PHP. Nếu không đăng nhập được, hãy đọc phần **Đặt lại mật khẩu** bên dưới.

### Đặt lại mật khẩu admin

Chạy đoạn PHP sau để lấy hash mật khẩu mới:
```php
<?php echo password_hash('admin123', PASSWORD_DEFAULT); ?>
```
Rồi cập nhật vào database:
```sql
UPDATE users SET password = '<hash_mới>' WHERE username = 'admin';
```

## 📌 Ghi chú cho sinh viên

- File `config/config.php` chứa toàn bộ cấu hình kết nối database và các hàm tiện ích
- Sử dụng **prepared statements** để phòng chống SQL Injection
- Sử dụng `htmlspecialchars()` để phòng chống XSS
- Sử dụng `password_hash()` / `password_verify()` để bảo mật mật khẩu
- Session được quản lý qua `$_SESSION` để xác thực người dùng