-- CultureGraph schema
-- Atlas personal de películas, series, discos y videojuegos

SET NAMES utf8mb4;
SET time_zone = '-03:00';

CREATE TABLE IF NOT EXISTS settings (
  `key` VARCHAR(64) NOT NULL PRIMARY KEY,
  `value` TEXT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS works (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  type ENUM('movie','series','album','game') NOT NULL,
  title VARCHAR(255) NOT NULL,
  original_title VARCHAR(255) NULL,
  year SMALLINT UNSIGNED NULL,
  year_end SMALLINT UNSIGNED NULL,
  status ENUM('wishlist','in_progress','finished','abandoned','revisit') NOT NULL DEFAULT 'finished',
  rating TINYINT UNSIGNED NULL,
  synopsis TEXT NULL,
  notes TEXT NULL,
  cover VARCHAR(512) NULL,
  runtime_minutes SMALLINT UNSIGNED NULL,
  seasons SMALLINT UNSIGNED NULL,
  episodes SMALLINT UNSIGNED NULL,
  tracks SMALLINT UNSIGNED NULL,
  platform VARCHAR(120) NULL,
  finished_at DATE NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY idx_works_type (type),
  KEY idx_works_status (status),
  KEY idx_works_year (year),
  KEY idx_works_title (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS creators (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  bio TEXT NULL,
  country VARCHAR(120) NULL,
  born_year SMALLINT UNSIGNED NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_creators_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS work_creators (
  work_id INT UNSIGNED NOT NULL,
  creator_id INT UNSIGNED NOT NULL,
  role VARCHAR(40) NOT NULL DEFAULT 'other',
  PRIMARY KEY (work_id, creator_id, role),
  CONSTRAINT fk_wc_work FOREIGN KEY (work_id) REFERENCES works(id) ON DELETE CASCADE,
  CONSTRAINT fk_wc_creator FOREIGN KEY (creator_id) REFERENCES creators(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS genres (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  color VARCHAR(16) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_genres_slug (slug),
  UNIQUE KEY uq_genres_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS work_genres (
  work_id INT UNSIGNED NOT NULL,
  genre_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (work_id, genre_id),
  CONSTRAINT fk_wg_work FOREIGN KEY (work_id) REFERENCES works(id) ON DELETE CASCADE,
  CONSTRAINT fk_wg_genre FOREIGN KEY (genre_id) REFERENCES genres(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS eras (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  year_start SMALLINT UNSIGNED NULL,
  year_end SMALLINT UNSIGNED NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_eras_slug (slug),
  UNIQUE KEY uq_eras_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS work_eras (
  work_id INT UNSIGNED NOT NULL,
  era_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (work_id, era_id),
  CONSTRAINT fk_we_work FOREIGN KEY (work_id) REFERENCES works(id) ON DELETE CASCADE,
  CONSTRAINT fk_we_era FOREIGN KEY (era_id) REFERENCES eras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS themes (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  description TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_themes_slug (slug),
  UNIQUE KEY uq_themes_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS work_themes (
  work_id INT UNSIGNED NOT NULL,
  theme_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (work_id, theme_id),
  CONSTRAINT fk_wt_work FOREIGN KEY (work_id) REFERENCES works(id) ON DELETE CASCADE,
  CONSTRAINT fk_wt_theme FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS experiences (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  description TEXT NULL,
  mood VARCHAR(80) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_experiences_slug (slug),
  UNIQUE KEY uq_experiences_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS work_experiences (
  work_id INT UNSIGNED NOT NULL,
  experience_id INT UNSIGNED NOT NULL,
  note VARCHAR(255) NULL,
  PRIMARY KEY (work_id, experience_id),
  CONSTRAINT fk_wx_work FOREIGN KEY (work_id) REFERENCES works(id) ON DELETE CASCADE,
  CONSTRAINT fk_wx_exp FOREIGN KEY (experience_id) REFERENCES experiences(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS work_links (
  work_id INT UNSIGNED NOT NULL,
  related_work_id INT UNSIGNED NOT NULL,
  relation_type VARCHAR(40) NOT NULL DEFAULT 'related',
  PRIMARY KEY (work_id, related_work_id, relation_type),
  CONSTRAINT fk_wl_work FOREIGN KEY (work_id) REFERENCES works(id) ON DELETE CASCADE,
  CONSTRAINT fk_wl_related FOREIGN KEY (related_work_id) REFERENCES works(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO settings (`key`, `value`) VALUES
  ('library_name', 'Archivo personal'),
  ('default_status', 'finished'),
  ('theme', 'system')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

INSERT INTO genres (name, slug, color) VALUES
  ('Ciencia ficción', 'ciencia-ficcion', '#1F6F78'),
  ('Drama', 'drama', '#B85A45'),
  ('Comedia', 'comedia', '#C9922A'),
  ('Terror', 'terror', '#3A4550'),
  ('Aventura', 'aventura', '#4F6F5C'),
  ('Indie', 'indie', '#5A6B7A'),
  ('Electrónica', 'electronica', '#1F6F78'),
  ('Rock', 'rock', '#B85A45')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO eras (name, slug, year_start, year_end, notes) VALUES
  ('Años 80', 'anos-80', 1980, 1989, 'Síntesis, VHS y arcades'),
  ('Años 90', 'anos-90', 1990, 1999, 'CD, cable y pixel art'),
  ('Años 2000', 'anos-2000', 2000, 2009, 'DVD, blogs y online'),
  ('Años 2010', 'anos-2010', 2010, 2019, 'Streaming y remakes'),
  ('Años 2020', 'anos-2020', 2020, 2029, 'Plataformas y remaster')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO themes (name, slug, description) VALUES
  ('Identidad', 'identidad', 'Quiénes somos y cómo nos miramos'),
  ('Memoria', 'memoria', 'Recuerdos, nostalgia y archivo'),
  ('Tecnología', 'tecnologia', 'Máquinas, redes y futuro'),
  ('Amistad', 'amistad', 'Vínculos elegidos'),
  ('Viaje', 'viaje', 'Tránsito, escape o búsqueda')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO experiences (name, slug, description, mood) VALUES
  ('Noche de lluvia', 'noche-de-lluvia', 'Ventana empañada y volumen bajo', 'contemplativo'),
  ('Maratón de finde', 'maraton-de-finde', 'Varias horas seguidas sin prisa', 'inmersivo'),
  ('Viaje en colectivo', 'viaje-en-colectivo', 'Auriculares y paisaje urbano', 'móvil'),
  ('Con amigos', 'con-amigos', 'Compartido, comentarios en vivo', 'social'),
  ('Solo en casa', 'solo-en-casa', 'Atención plena, sin distracciones', 'íntimo')
ON DUPLICATE KEY UPDATE name = VALUES(name);
