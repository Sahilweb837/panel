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
  deleted_at TIMESTAMP NULL,
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
  deleted_at TIMESTAMP NULL,
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
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL
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
  deleted_at TIMESTAMP NULL,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
);

CREATE TABLE attendances (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id BIGINT UNSIGNED NOT NULL,
  status ENUM('Present','Absent','Late','Leave') NOT NULL DEFAULT 'Present',
  attendance_date DATE NOT NULL,
  check_in_time VARCHAR(20) NULL,
  check_out_time VARCHAR(20) NULL,
  fine DECIMAL(10,2) NOT NULL DEFAULT 0,
  remarks TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY student_date (student_id, attendance_date),
  FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE employee_attendances (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  employee_id BIGINT UNSIGNED NOT NULL,
  status ENUM('Present','Absent','Late','Leave') NOT NULL DEFAULT 'Present',
  check_in_time VARCHAR(20) NULL,
  check_out_time VARCHAR(20) NULL,
  attendance_date DATE NOT NULL,
  remarks TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  UNIQUE KEY employee_date (employee_id, attendance_date),
  FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
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
  transaction_id VARCHAR(100) NULL,
  utr_no VARCHAR(100) NULL,
  status ENUM('Paid','Partial','Unpaid') NOT NULL DEFAULT 'Unpaid',
  remarks TEXT NULL,
  created_by BIGINT UNSIGNED NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  deleted_at TIMESTAMP NULL,
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
  deleted_at TIMESTAMP NULL,
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
  deleted_at TIMESTAMP NULL,
  FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

INSERT INTO roles (role_name, slug, description) VALUES
('Super Admin', 'super-admin', 'Full system administrator'),
('Root Admin', 'root-admin', 'Root-level administrator'),
('Admin', 'admin', 'Institute operations administrator'),
('Staff', 'staff', 'Teaching or office staff');

INSERT INTO `courses` (`id`, `name`, `code`, `duration`, `fee`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Web Development',	'WEB',	'6 Months',	18000.00,	1,	'2026-05-29 00:49:00',	'2026-05-29 00:49:00'),
(2,	'Graphic Design',	'GD',	'6 Months',	35000.00,	1,	'2026-05-29 00:49:00',	'2026-05-29 08:12:45'),
(3,	'Digital Marketing',	'CIDM',	'6 Months',	35000.00,	1,	'2026-05-29 00:49:00',	'2026-05-29 08:13:15'),
(4,	'Full Stack Development',	'FSD',	'1 Year',	80000.00,	1,	'2026-05-29 00:49:00',	'2026-05-29 08:15:49'),
(5,	'Data Science AI',	'DIDSA',	'1 Month',	105000.00,	1,	'2026-05-29 08:14:20',	'2026-05-29 08:16:57'),
(6,	'Software Engineering with Python',	'SEIP',	'1 Year',	85000.00,	1,	'2026-05-29 08:15:35',	'2026-05-29 08:15:35'),
(7,	'Diploma in Cyber Security',	'DICS',	'1 Year',	95000.00,	1,	'2026-05-29 08:16:35',	'2026-05-29 08:16:35'),
(8,	'Certificate in Data Analytics',	'CIDA',	'6 Months',	45000.00,	1,	'2026-05-29 08:17:41',	'2026-05-29 08:17:41'),
(9,	'Certificate in Ethical Hacking',	'CIEH',	'6 Months',	45000.00,	1,	'2026-05-29 08:18:23',	'2026-05-29 08:18:23'),
(10,	'Diploma in Data Science & Business Analytics',	'DIDSBA',	'1 Year',	95000.00,	1,	'2026-05-29 08:19:15',	'2026-05-29 08:19:15'),
(11,	'Certificate in UI/UX Design',	'CIUIUX',	'6 Months',	35000.00,	1,	'2026-05-29 08:20:15',	'2026-05-29 08:20:15'),
(12,	'Certificate in Web Design',	'CIWD',	'6 Months',	35000.00,	1,	'2026-05-29 08:21:21',	'2026-05-29 08:21:21'),
(13,	'Diploma in 2D & 3D Animation',	'DIA',	'1 Year',	85000.00,	1,	'2026-05-29 08:22:03',	'2026-05-29 08:22:03'),
(14,	'Diploma in Graphic & Web Designing',	'DIGWD',	'1 Year',	70000.00,	1,	'2026-05-29 08:22:50',	'2026-05-29 08:22:50'),
(15,	'Diploma in Auto CAD',	'DICAD',	'6 Months',	35000.00,	1,	'2026-05-29 08:23:52',	'2026-05-29 08:23:52'),
(16,	'Certificate in Auto CAD',	'CICAD',	'3 Months',	15000.00,	1,	'2026-05-29 08:24:24',	'2026-05-29 08:24:24'),
(17,	'Diploma in Digital Content Creation',	'DIDCC',	'6 Months',	40000.00,	1,	'2026-05-29 08:25:21',	'2026-05-29 08:25:21'),
(18,	'Certificate in Motion Graphics',	'CIMG',	'6 Months',	40000.00,	1,	'2026-05-29 08:26:03',	'2026-05-29 08:26:03'),
(19,	'Certificate in Social Media Marketing',	'CISMM',	'6 Months',	35000.00,	1,	'2026-05-29 08:26:58',	'2026-05-29 08:26:58'),
(20,	'Certificate in SEO',	'CISEO',	'6 Months',	35000.00,	1,	'2026-05-29 08:27:40',	'2026-05-29 08:27:40'),
(21,	'Diploma in DevOps',	'DIDOPS',	'1 Year',	95000.00,	1,	'2026-05-29 08:30:56',	'2026-05-29 08:30:56'),
(22,	'Algorithms & Data Structures (DSA) in Python',	'DSAIP',	'3 Months',	30000.00,	1,	'2026-05-29 08:31:41',	'2026-05-29 08:31:41'),
(23,	'System Design & Operating System',	'SD&OS',	'6 Months',	55000.00,	1,	'2026-05-29 08:32:30',	'2026-05-29 08:32:30');

INSERT INTO users (name, email, username, password, role_id, status, created_at, updated_at)
VALUES
('Super Admin', 'superadmin@example.com', 'superadmin', '$2y$10$8zHd5f0Xg5rgQPbrj6q4WuLWi9m5mJPHvcnRTjhT0U/4dudIIacYS', 1, 1, NOW(), NOW());
