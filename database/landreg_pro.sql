-- LandReg Pro Database Schema
-- Default admin: admin@landreg.com / admin123

CREATE DATABASE IF NOT EXISTS landreg_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE landreg_pro;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30),
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    profile_photo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE owners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    full_name VARCHAR(150) NOT NULL,
    national_id VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(30),
    email VARCHAR(150),
    address TEXT,
    photo VARCHAR(255),
    registration_date DATE DEFAULT (CURRENT_DATE),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE lands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plot_number VARCHAR(100) NOT NULL UNIQUE,
    land_number VARCHAR(100) NOT NULL UNIQUE,
    region VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    neighborhood VARCHAR(100) NOT NULL,
    full_address TEXT,
    land_size VARCHAR(100) NOT NULL,
    land_type ENUM('Residential','Commercial','Agricultural','Industrial') NOT NULL,
    registration_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('Active','Pending','Transferred') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_number VARCHAR(100) NOT NULL UNIQUE,
    registration_number VARCHAR(100) NOT NULL UNIQUE,
    owner_id INT NOT NULL,
    land_id INT NOT NULL,
    ownership_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('Active','Transferred','Disputed') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES owners(id) ON DELETE CASCADE,
    FOREIGN KEY (land_id) REFERENCES lands(id) ON DELETE CASCADE
);

CREATE TABLE ownership_transfers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    current_owner_id INT NOT NULL,
    new_owner_id INT NOT NULL,
    transfer_reason TEXT,
    transfer_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('Pending','Approved','Rejected') DEFAULT 'Pending',
    admin_remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (current_owner_id) REFERENCES owners(id) ON DELETE CASCADE,
    FOREIGN KEY (new_owner_id) REFERENCES owners(id) ON DELETE CASCADE
);

CREATE TABLE certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    certificate_number VARCHAR(100) NOT NULL UNIQUE,
    qr_code VARCHAR(255),
    issue_date DATE DEFAULT (CURRENT_DATE),
    status ENUM('Valid','Cancelled') DEFAULT 'Valid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info','success','warning','danger') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    system_name VARCHAR(150) DEFAULT 'LandReg Pro',
    office_name VARCHAR(150),
    office_address TEXT,
    office_phone VARCHAR(30),
    office_email VARCHAR(150),
    logo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Default admin password set by install.php (admin123)
-- Or manually: UPDATE users SET password = '<bcrypt_hash>' WHERE email = 'admin@landreg.com';

INSERT INTO users (full_name, email, phone, password, role, status) VALUES (
    'System Administrator',
    'admin@landreg.com',
    '252600000000',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy',
    'admin',
    'active'
);

INSERT INTO settings (system_name, office_name, office_address, office_phone, office_email) VALUES (
    'LandReg Pro',
    'Ministry of Land Registration',
    'Mogadishu, Somalia',
    '252600000000',
    'info@landreg.gov.so'
);
