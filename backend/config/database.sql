-- RCT Education Portal Database Schema
-- MySQL Database Setup

CREATE DATABASE IF NOT EXISTS rct_education;
USE rct_education;

-- Users Table (Patients & Admins)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role ENUM('patient', 'admin', 'dentist') DEFAULT 'patient',
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('M', 'F', 'Other'),
    profile_image VARCHAR(255),
    language ENUM('en', 'ta', 'hi', 'te') DEFAULT 'en',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_status (status)
);

-- Appointments Table
CREATE TABLE appointments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    dentist_id INT NOT NULL,
    appointment_number INT NOT NULL, -- 1, 2, 3, or Follow-up
    appointment_type ENUM('diagnosis', 'procedure', 'restoration', 'followup') NOT NULL,
    scheduled_date DATETIME,
    completed_date DATETIME,
    status ENUM('scheduled', 'completed', 'cancelled', 'rescheduled') DEFAULT 'scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dentist_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_patient (patient_id),
    INDEX idx_status (status)
);

-- Questionnaires Table
CREATE TABLE questionnaires (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    type ENUM('baseline', 'post') NOT NULL,
    appointment_number INT,
    total_questions INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_appointment (appointment_id)
);

-- Questions Table
CREATE TABLE questions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    questionnaire_id INT NOT NULL,
    question_text_en VARCHAR(500) NOT NULL,
    question_text_ta VARCHAR(500),
    question_text_hi VARCHAR(500),
    question_text_te VARCHAR(500),
    question_type ENUM('multiple_choice', 'true_false', 'short_answer', 'rating') NOT NULL,
    is_mandatory BOOLEAN DEFAULT TRUE,
    display_order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (questionnaire_id) REFERENCES questionnaires(id) ON DELETE CASCADE,
    INDEX idx_questionnaire (questionnaire_id)
);

-- Question Options Table
CREATE TABLE question_options (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    option_text_en VARCHAR(500) NOT NULL,
    option_text_ta VARCHAR(500),
    option_text_hi VARCHAR(500),
    option_text_te VARCHAR(500),
    is_correct BOOLEAN DEFAULT FALSE,
    display_order INT,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_question (question_id)
);

-- User Answers Table
CREATE TABLE user_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option_id INT,
    answer_text VARCHAR(1000),
    rating INT,
    is_correct BOOLEAN,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    INDEX idx_appointment (appointment_id),
    INDEX idx_question (question_id)
);

-- Education Content Table
CREATE TABLE education_content (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_number INT NOT NULL,
    content_type ENUM('diagnosis', 'procedure', 'restoration') NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    title_ta VARCHAR(255),
    title_hi VARCHAR(255),
    title_te VARCHAR(255),
    description_en LONGTEXT NOT NULL,
    description_ta LONGTEXT,
    description_hi LONGTEXT,
    description_te LONGTEXT,
    video_url VARCHAR(255),
    image_url VARCHAR(255),
    duration_minutes INT,
    display_order INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_appointment_number (appointment_number)
);

-- Post-Operative Instructions Table
CREATE TABLE post_operative_instructions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    instruction_en LONGTEXT NOT NULL,
    instruction_ta LONGTEXT,
    instruction_hi LONGTEXT,
    instruction_te LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_appointment (appointment_id)
);

-- Digital Consent Table
CREATE TABLE digital_consent (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    consent_type ENUM('treatment', 'data_collection', 'photography') NOT NULL,
    consent_text_en LONGTEXT NOT NULL,
    consent_text_ta LONGTEXT,
    consent_text_hi LONGTEXT,
    consent_text_te LONGTEXT,
    patient_accepted BOOLEAN DEFAULT FALSE,
    accepted_at TIMESTAMP,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_appointment (appointment_id),
    INDEX idx_patient_accepted (patient_accepted)
);

-- Quiz Results Table
CREATE TABLE quiz_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    quiz_type ENUM('post_quiz_1', 'post_quiz_2', 'post_quiz_3', 'final_assessment') NOT NULL,
    total_questions INT,
    correct_answers INT,
    score DECIMAL(5, 2),
    time_spent_seconds INT,
    passed BOOLEAN,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_appointment (appointment_id)
);

-- Counseling Session Table
CREATE TABLE counseling_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    appointment_id INT NOT NULL,
    session_date DATETIME,
    notes_en LONGTEXT,
    notes_ta LONGTEXT,
    notes_hi LONGTEXT,
    notes_te LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_appointment (appointment_id)
);

-- Attendance Table
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    appointment_id INT NOT NULL,
    attended BOOLEAN,
    check_in_time TIMESTAMP,
    check_out_time TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_patient (patient_id),
    INDEX idx_appointment (appointment_id)
);

-- Audit Log Table
CREATE TABLE audit_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255),
    entity_type VARCHAR(100),
    entity_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_created_at (created_at)
);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_appointments_patient ON appointments(patient_id);
CREATE INDEX idx_appointments_status ON appointments(status);

