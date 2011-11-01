
/* ТАБЛИЦА ПОЛЬЗОВАТЕЛЕЙ */
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
	`id` 			INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	`login`			VARCHAR(100) NOT NULL,
	`password`		VARCHAR(100) NOT NULL,
	`surname`		VARCHAR(255),
	`name`			VARCHAR(255),
	`level`			SMALLINT,
	`regdate`		INT(10) UNSIGNED
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO users (login, password, surname, name, level, regdate) VALUES('root', 'b1a838a7ee5413752554941c22926a1615866622', 'root', 'root', 50, 0);

/* СТАТИЧЕСКИЕ СТРАНИЦЫ */
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` 				INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `alias` 			VARCHAR(255) NOT NULL,
  `author` 			INT(10) UNSIGNED NOT NULL,
  `published` 		CHAR(1) DEFAULT '0',
  `locked`			CHAR(1) DEFAULT '0',
  `modif_date`		INT(10) UNSIGNED DEFAULT '0',
  `create_date`		INT(10) UNSIGNED DEFAULT '0',
  INDEX(`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СТАТИЧЕСКИЕ СТРАНИЦЫ RU */
DROP TABLE IF EXISTS `pages_ru`;
CREATE TABLE `pages_ru` (
  `page_id` 		INT(10) UNSIGNED PRIMARY KEY,
  `title` 			TEXT NOT NULL,
  `body` 			TEXT,
  `meta_description` TEXT,
  `meta_keywords`	TEXT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СТАТИЧЕСКИЕ СТРАНИЦЫ EN */
DROP TABLE IF EXISTS `pages_en`;
CREATE TABLE `pages_en` (
  `page_id` 		INT(10) UNSIGNED PRIMARY KEY,
  `title` 			TEXT NOT NULL,
  `body` 			TEXT,
  `meta_description` TEXT,
  `meta_keywords`	TEXT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СТАТИЧЕСКИЕ СТРАНИЦЫ UA */
DROP TABLE IF EXISTS `pages_ua`;
CREATE TABLE `pages_ua` (
  `page_id` 		INT(10) UNSIGNED PRIMARY KEY,
  `title` 			TEXT NOT NULL,
  `body` 			TEXT,
  `meta_description` TEXT,
  `meta_keywords`	TEXT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СОХРАНЕНИЕ ОШИБОК */
DROP TABLE IF EXISTS `error_log`;
CREATE TABLE `error_log` (
  `id` int(10) 	UNSIGNED NOT NULL AUTO_INCREMENT,
  `url`			TEXT,
  `description` TEXT,
  `session_dump` TEXT,
  `hash`		CHAR(32),
  `lastdate` 	INT(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ПОЛНАЯ ПОЛЬЗОВАТЕЛЬСКАЯ СТАТИСТИКА */
DROP TABLE IF EXISTS `user_statistics`;
CREATE TABLE `user_statistics` (
  `id` 				INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` 			INT(10) UNSIGNED DEFAULT 0,
  `request_urls`	TEXT,
  `user_ip`			VARCHAR(255),
  `referer`			VARCHAR(255),
  `user_agent_raw`	VARCHAR(255),
  `has_js`			BOOLEAN,
  `browser_name`	VARCHAR(50),
  `browser_version`	VARCHAR(50),
  `screen_width`	SMALLINT UNSIGNED,
  `screen_height`	SMALLINT UNSIGNED,
  `date`			INT(10) UNSIGNED,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ЗАДАЧИ */
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id`				int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid`				INT(10) UNSIGNED,
  `name`			VARCHAR(255),
  `xrsl_command`	TEXT,
  `state`			SMALLINT,
  `date` 			INT(10) UNSIGNED,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
