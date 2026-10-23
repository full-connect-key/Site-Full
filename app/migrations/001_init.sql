-- Estrutura inicial do site Full Connect Key (MySQL 5.7+ / MariaDB 10.3+).

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(190) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL DEFAULT '',
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  failed_attempts INT UNSIGNED NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  last_login_at DATETIME NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conteúdo: páginas, plataformas, cases, artigos, imprensa, pessoas,
-- configurações globais... "draft" é a cópia de trabalho do painel;
-- "published" é o que o site exibe (NULL = nunca publicado).
CREATE TABLE IF NOT EXISTS entries (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(40) NOT NULL,
  template VARCHAR(40) NOT NULL DEFAULT '',
  slug VARCHAR(190) NOT NULL DEFAULT '',
  title VARCHAR(255) NOT NULL DEFAULT '',
  status ENUM('draft','published','changed') NOT NULL DEFAULT 'draft',
  menu_order INT NOT NULL DEFAULT 0,
  draft LONGTEXT NULL,
  published LONGTEXT NULL,
  published_slug VARCHAR(190) NULL,
  published_title VARCHAR(255) NULL,
  published_at DATETIME NULL,
  seed_key VARCHAR(120) NULL UNIQUE,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  updated_by INT UNSIGNED NULL,
  KEY idx_type (type, menu_order),
  KEY idx_type_slug (type, published_slug),
  KEY idx_template (template)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS revisions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  entry_id INT UNSIGNED NOT NULL,
  title VARCHAR(255) NOT NULL DEFAULT '',
  slug VARCHAR(190) NOT NULL DEFAULT '',
  data LONGTEXT NOT NULL,
  action VARCHAR(20) NOT NULL DEFAULT 'save',
  user_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL,
  KEY idx_entry (entry_id, id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS media (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  path VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL DEFAULT '',
  mime VARCHAR(80) NOT NULL,
  size INT UNSIGNED NOT NULL DEFAULT 0,
  width INT UNSIGNED NULL,
  height INT UNSIGNED NULL,
  sizes TEXT NULL,
  alt VARCHAR(255) NOT NULL DEFAULT '',
  decorative TINYINT(1) NOT NULL DEFAULT 0,
  caption VARCHAR(255) NOT NULL DEFAULT '',
  credit VARCHAR(160) NOT NULL DEFAULT '',
  license VARCHAR(160) NOT NULL DEFAULT '',
  rights_confirmed TINYINT(1) NOT NULL DEFAULT 0,
  focal VARCHAR(20) NOT NULL DEFAULT '',
  poster_id INT UNSIGNED NULL,
  folder VARCHAR(80) NOT NULL DEFAULT '',
  seed_key VARCHAR(120) NULL UNIQUE,
  created_at DATETIME NOT NULL,
  created_by INT UNSIGNED NULL,
  KEY idx_folder (folder)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS redirects (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  from_path VARCHAR(255) NOT NULL UNIQUE,
  to_path VARCHAR(500) NOT NULL,
  match_type ENUM('exact','prefix') NOT NULL DEFAULT 'exact',
  automatic TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS submissions (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  subject VARCHAR(20) NOT NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(254) NOT NULL,
  company VARCHAR(120) NOT NULL DEFAULT '',
  message TEXT NOT NULL,
  consent_at DATETIME NOT NULL,
  delivery ENUM('pending','sent','stored','failed') NOT NULL DEFAULT 'pending',
  delivery_error VARCHAR(255) NOT NULL DEFAULT '',
  created_at DATETIME NOT NULL,
  KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  name VARCHAR(80) PRIMARY KEY,
  value LONGTEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rate_limits (
  bucket VARCHAR(120) PRIMARY KEY,
  hits INT UNSIGNED NOT NULL DEFAULT 0,
  expires_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS migrations (
  name VARCHAR(120) PRIMARY KEY,
  applied_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
