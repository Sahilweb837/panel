CREATE DATABASE IF NOT EXISTS institute_erp;
USE institute_erp;

CREATE TABLE roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_name VARCHAR(100) NOT NULL UNIQUE,
  slug VARCHAR(100) NOT NULL UNIQUE,
  description TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  username VARCHAR(100) NOT NULL UNIQUE,
  role_id BIGINT UNSIGNED NULL,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  profile_pic VARCHAR(255) NULL DEFAULT 'default.png',
  status TINYINT(1) NOT NULL DEFAULT 1,
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

CREATE TABLE employees (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  employee_code VARCHAR(60) NOT NULL UNIQUE,
  phone VARCHAR(30) NULL,
  department VARCHAR(100) NULL,
  designation VARCHAR(100) NULL,
  salary DECIMAL(10,2) NOT NULL DEFAULT 0,
  joining_date DATE NULL,
  address TEXT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE courses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  code VARCHAR(50) NULL UNIQUE,
  duration VARCHAR(50) NULL,
  fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);

CREATE TABLE students (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  admission_no VARCHAR(60) NOT NULL UNIQUE,
  roll_no VARCHAR(60) NULL,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NULL,
  guardian_name VARCHAR(120) NULL,
  email VARCHAR(150) NULL UNIQUE,
  phone VARCHAR(30) NULL,
  dob DATE NULL,
  gender ENUM('Male','Female','Other') NOT NULL DEFAULT 'Male',
  address TEXT NULL,
  course_id BIGINT UNSIGNED NULL,
  course_duration VARCHAR(50) NULL,
  class VARCHAR(80) NULL,
  section VARCHAR(80) NULL,
  admission_date DATE NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
);

CREATE TABLE attendances (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id BIGINT UNSIGNED NOT NULL,
  status ENUM('Present','Absent','Late','Leave') NOT NULL DEFAULT 'Present',
  attendance_date DATE NOT NULL,
  fine DECIMAL(10,2) NOT NULL DEFAULT 0,
  remarks TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY student_date (student_id, attendance_date),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE fee_invoices (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id BIGINT UNSIGNED NOT NULL,
  invoice_no VARCHAR(100) NOT NULL UNIQUE,
  fee_category VARCHAR(120) NULL,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  paid_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  discount DECIMAL(10,2) NOT NULL DEFAULT 0,
  fine DECIMAL(10,2) NOT NULL DEFAULT 0,
  due_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  payment_date DATE NULL,
  payment_method VARCHAR(120) NULL,
  status ENUM('Paid','Partial','Unpaid') NOT NULL DEFAULT 'Unpaid',
  remarks TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE expenses (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(120) NULL,
  amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  description TEXT NULL,
  expense_date DATE NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE salary_slips (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  month VARCHAR(30) NOT NULL,
  year YEAR NOT NULL,
  basic_salary DECIMAL(10,2) NOT NULL DEFAULT 0,
  allowances DECIMAL(10,2) NOT NULL DEFAULT 0,
  deductions DECIMAL(10,2) NOT NULL DEFAULT 0,
  net_pay DECIMAL(10,2) NOT NULL DEFAULT 0,
  payment_date DATE NULL,
  status ENUM('Pending','Paid') NOT NULL DEFAULT 'Pending',
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO roles (role_name, slug, description) VALUES
('Super Admin', 'super-admin', 'Full system administrator'),
('Root Admin', 'root-admin', 'Root-level administrator'),
('Admin', 'admin', 'Institute operations administrator'),
('Staff', 'staff', 'Teaching or office staff');

INSERT INTO courses (name, code, duration, fee, status, created_at, updated_at) VALUES
('Web Development', 'WEB', '6 Months', 18000.00, 1, NOW(), NOW()),
('Graphic Design', 'GD', '45 Days', 7000.00, 1, NOW(), NOW()),
('Digital Marketing', 'DM', '1 Month', 9000.00, 1, NOW(), NOW()),
('Full Stack Development', 'FSD', '1 Year', 42000.00, 1, NOW(), NOW());

INSERT INTO users (name, email, username, password, role_id, status, created_at, updated_at)
VALUES
('Super Admin', 'superadmin@example.com', 'superadmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, NOW(), NOW());
