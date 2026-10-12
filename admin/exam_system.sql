-- ETS-Async Exam Manager tables. Import into the same database as question_bank.
CREATE TABLE IF NOT EXISTS exams (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  test_code VARCHAR(20) NOT NULL UNIQUE,
  instructions MEDIUMTEXT NOT NULL,
  time_limit_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 60,
  question_count SMALLINT UNSIGNED NOT NULL DEFAULT 20,
  subject_id INT NULL,
  topic_id INT NULL,
  topic_ids_json MEDIUMTEXT NULL,
  status ENUM('draft','published','closed') NOT NULL DEFAULT 'draft',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_exams_status (status), KEY idx_exams_code (test_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exam_draft_questions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  exam_id INT UNSIGNED NOT NULL,
  position_no SMALLINT UNSIGNED NOT NULL,
  question_id INT NOT NULL,
  UNIQUE KEY uq_draft_position (exam_id, position_no),
  UNIQUE KEY uq_draft_question (exam_id, question_id),
  KEY idx_draft_exam (exam_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exam_set_questions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  exam_id INT UNSIGNED NOT NULL,
  variant CHAR(1) NOT NULL,
  position_no SMALLINT UNSIGNED NOT NULL,
  question_id INT NOT NULL,
  choice_order_json TEXT NOT NULL,
  UNIQUE KEY uq_exam_variant_position (exam_id, variant, position_no),
  UNIQUE KEY uq_exam_variant_question (exam_id, variant, question_id),
  KEY idx_set_exam_variant (exam_id, variant)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exam_attempts (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  exam_id INT UNSIGNED NOT NULL,
  student_name VARCHAR(180) NOT NULL,
  student_number VARCHAR(80) NULL,
  variant CHAR(1) NOT NULL,
  started_at DATETIME NOT NULL,
  expires_at DATETIME NOT NULL,
  last_seen_at DATETIME NOT NULL,
  submitted_at DATETIME NULL,
  status ENUM('in_progress','submitted','timed_out') NOT NULL DEFAULT 'in_progress',
  score SMALLINT UNSIGNED NULL,
  total_items SMALLINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_attempt_exam_status (exam_id, status),
  KEY idx_attempt_started (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS exam_attempt_answers (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  attempt_id BIGINT UNSIGNED NOT NULL,
  question_id INT NOT NULL,
  selected_choice VARCHAR(8) NULL,
  correct_choice VARCHAR(8) NULL,
  is_correct TINYINT(1) NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_attempt_question (attempt_id, question_id),
  KEY idx_answers_attempt (attempt_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
