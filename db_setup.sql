CREATE DATABASE IF NOT EXISTS cv_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cv_db;

-- Админ хэрэглэгчийн хүснэгт
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Хувийн мэдээллийн хүснэгт
CREATE TABLE IF NOT EXISTS personal_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    profession VARCHAR(100) NOT NULL,
    bio TEXT,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    photo VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Боловсролын хүснэгт
CREATE TABLE IF NOT EXISTS education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution VARCHAR(100) NOT NULL,
    degree VARCHAR(100) NOT NULL,
    field VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    description TEXT,
    order_num INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ажлын туршлагын хүснэгт
CREATE TABLE IF NOT EXISTS experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    description TEXT,
    order_num INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Ур чадварын хүснэгт
CREATE TABLE IF NOT EXISTS skills (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    level INT NOT NULL, -- 1-100 хүртэлх түвшин
    category VARCHAR(50), -- Техникийн, хэлний, бусад гэх мэт
    order_num INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Төслүүдийн хүснэгт
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    technologies VARCHAR(255),
    image VARCHAR(255),
    url VARCHAR(255),
    start_date DATE,
    end_date DATE,
    order_num INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Холбоо барих хүснэгт (нэмэлт холбоосууд)
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    order_num INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Анхны админ хэрэглэгч үүсгэх (нууц үг: admin123)
INSERT INTO admin (username, password, email) 
VALUES ('admin', '$2y$10$8X4XK5XyoM0Ew6eCAlg8.e7uQ8e4zbVU0UE9QIMmMi7Ndh6BJ3v4G', 'admin@example.com');

-- Анхны хувийн мэдээлэл
INSERT INTO personal_info (name, profession, bio, email, phone)
VALUES ('Нэр Овог', 'Програм хангамжийн инженер', 'Энд товч танилцуулга бичнэ', 'your@email.com', '99112233'); 