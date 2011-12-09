
/* ТАБЛИЦА ПОЛЬЗОВАТЕЛЕЙ */
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
	`id` 					INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	`login`					VARCHAR(100),
	`password`				VARCHAR(100),
	`dn`					VARCHAR(255),
	`dn_cn`					VARCHAR(255),
	`surname`				VARCHAR(255),
	`name`					VARCHAR(255),
	`level`					SMALLINT,
	`regdate`				INT(10) UNSIGNED,
	`profile`				TEXT,
	`default_voms`			INTEGER,
	myproxy_manual_login	BOOLEAN,
	myproxy_no_password		BOOLEAN,
	myproxy_login			VARCHAR(255),
	myproxy_password		VARCHAR(255),
	myproxy_server_id		INT,
	myproxy_expire_date		INT DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO users (login, password, surname, name, level, regdate) VALUES('root', 'b1a838a7ee5413752554941c22926a1615866622', 'root', 'root', 50, 0);

/* СТАТИЧЕСКИЕ СТРАНИЦЫ */
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) COLLATE utf8_bin NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `published` char(1) COLLATE utf8_bin DEFAULT '0',
  `locked` char(1) COLLATE utf8_bin DEFAULT '0',
  `modif_date` int(10) unsigned DEFAULT '0',
  `create_date` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СТАТИЧЕСКИЕ СТРАНИЦЫ RU */
DROP TABLE IF EXISTS `pages_ru`;
CREATE TABLE `pages_ru` (
  `page_id` int(10) unsigned NOT NULL,
  `title` text COLLATE utf8_bin,
  `body` text COLLATE utf8_bin,
  `meta_description` text COLLATE utf8_bin,
  `meta_keywords` text COLLATE utf8_bin,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СТАТИЧЕСКИЕ СТРАНИЦЫ EN */
DROP TABLE IF EXISTS `pages_en`;
CREATE TABLE `pages_en` (
  `page_id` int(10) unsigned NOT NULL,
  `title` text COLLATE utf8_bin,
  `body` text COLLATE utf8_bin,
  `meta_description` text COLLATE utf8_bin,
  `meta_keywords` text COLLATE utf8_bin,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СТАТИЧЕСКИЕ СТРАНИЦЫ UA */
DROP TABLE IF EXISTS `pages_ua`;
CREATE TABLE `pages_ua` (
  `page_id` int(10) unsigned NOT NULL,
  `title` text COLLATE utf8_bin,
  `body` text COLLATE utf8_bin,
  `meta_description` text COLLATE utf8_bin,
  `meta_keywords` text COLLATE utf8_bin,
  PRIMARY KEY (`page_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СОХРАНЕНИЕ ОШИБОК */
DROP TABLE IF EXISTS `error_log`;
CREATE TABLE `error_log` (
  `id` int(10) 	UNSIGNED AUTO_INCREMENT,
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
  `id` 				INT(10) UNSIGNED AUTO_INCREMENT,
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

/* ЯЗЫКОВЫЕ ФРАГМЕНТЫ */
DROP TABLE IF EXISTS `lng_snippets`;
CREATE TABLE `lng_snippets` (
  `id`				int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name`			VARCHAR(255) UNIQUE,
  `description`		TEXT,
  `is_external`		BOOLEAN
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* РУССКИЙ ЯЗЫК */
DROP TABLE IF EXISTS `lng_ru`;
CREATE TABLE `lng_ru` (
  `snippet_id`		int(10) UNSIGNED UNIQUE,
  `text`			TEXT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* АНГЛИЙСКИЙ ЯЗЫК */
DROP TABLE IF EXISTS `lng_en`;
CREATE TABLE `lng_en` (
  `snippet_id`		int(10) UNSIGNED UNIQUE,
  `text`			TEXT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* УКРАИНСКИЙ ЯЗЫК */
DROP TABLE IF EXISTS `lng_ua`;
CREATE TABLE `lng_ua` (
  `snippet_id`		int(10) UNSIGNED UNIQUE,
  `text`			TEXT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СПИСОК ВИРТУАЛЬНЫХ ОРГАНИЗАЦИЙ */
DROP TABLE IF EXISTS `voms`;
CREATE TABLE IF NOT EXISTS `voms` (
	`id` 		INT(11) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
	`name`		TEXT,
	`url`		TEXT
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ЧЛЕНСТВО ПОЛЬЗОВАТЕЛЕЙ В ВИРТУАЛЬНЫХ ОРГАНИЗАЦИЯХ */
DROP TABLE IF EXISTS `user_accepted_voms`;
CREATE TABLE IF NOT EXISTS `user_accepted_voms` (
	`uid`		INTEGER,
	`voms_id`	INTEGER,
	PRIMARY KEY(`uid`, `voms_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ПРОЕКТЫ */
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id`				INT(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name_id`			INT(10) UNSIGNED,
  `text_id`			INT(10) UNSIGNED
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ПРОГРАМНОЕ ОБЕСПЕЧЕНИЕ */
DROP TABLE IF EXISTS `software`;
CREATE TABLE `software` (
  `id`				int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name`			VARCHAR(255),
  `project_id`		INT UNSIGNED
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СЕРВЕРА MYPROXY */
DROP TABLE IF EXISTS `myproxy_servers`;
CREATE TABLE `myproxy_servers` (
  `id`				int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name`			VARCHAR(255),
  `url`				TEXT,
  `port`			INT
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ПРОГРАМНОЕ ОБЕСПЕЧЕНИЕ */
DROP TABLE IF EXISTS `project_allowed_voms`;
CREATE TABLE `project_allowed_voms` (
  `project_id`		INT UNSIGNED,
  `voms_id`			INT UNSIGNED,
	PRIMARY KEY(`project_id`, `voms_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ПРОЕКТЫ, ВЫБРАННЫЕ ПОЛЬЗОВАТЕЛЯМИ */
DROP TABLE IF EXISTS `user_allowed_projects`;
CREATE TABLE `user_allowed_projects` (
  `uid`				INT UNSIGNED,
  `project_id`		INT UNSIGNED,
  `default_vo` 		INT UNSIGNED,
	PRIMARY KEY(`uid`, `project_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ПРОГРАММЫ, ВЫБРАННЫЕ ПОЛЬЗОВАТЕЛЯМИ */
DROP TABLE IF EXISTS `user_allowed_software`;
CREATE TABLE `user_allowed_software` (
  `uid`				INT UNSIGNED,
  `software_id`		INT UNSIGNED,
	PRIMARY KEY(`uid`, `software_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



-----------------------------------------------------------------------------

/* ПРОФИЛИ СОЗДАНИЯ ЗАДАЧ */
DROP TABLE IF EXISTS `task_profiles`;
CREATE TABLE `task_profiles` (
  `id`				int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `is_user_defined`	BOOLEAN,
  `uid`				INT(10) UNSIGNED,
  `name`			VARCHAR(255),
  `is_gridjob_loaded` tinyint(1) default '0',
  `project_id`		INT(10) UNSIGNED,
  `create_date`		INT(10) UNSIGNED
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* НАБОРЫ ДЛЯ ЗАПУСКА ЗАДАЧ */
DROP TABLE IF EXISTS `task_sets`;
CREATE TABLE `task_sets` (
  `id` int(10) unsigned NOT NULL PRIMARY KEY auto_increment,
  `uid` int(10) unsigned default NULL,
  `project_id` int(10) unsigned default NULL,
  `profile_id` int(10) unsigned default NULL,
  `name` varchar(255) collate utf8_bin default NULL,
  `gridjob_name` varchar(255) collate utf8_bin default NULL,
  `is_gridjob_loaded` tinyint(1) default '0',
  `num_submits` int(10) unsigned default NULL,
  `create_date` int(10) unsigned default NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ЗАПУСКИ ЗАДАЧ */
DROP TABLE IF EXISTS `task_submits`;
CREATE TABLE `task_submits` (
  `id`				BIGINT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `set_id`			INT(10) UNSIGNED,
  `uid`				INT(10) UNSIGNED,
  `index`			INT UNSIGNED,
  `prefered_server`	VARCHAR(255),
  `jobid`			VARCHAR(255),
  `status`			SMALLINT,
  `is_submitted`	SMALLINT,  /* 0 - не отправлена; 1 - отправлена в grid; 2 - отправлена и присвоен статус */
  `is_completed`	SMALLINT, /* 0 - в процессе; 1 - успешно завершена; 2 - завершена с ошибкой; 3 - удалена */
  `is_fetched`		BOOLEAN,  /* получена ли задача из grid */
  `create_date`		INT(10) UNSIGNED, /* дата создания */
  `start_date`		INT(10) UNSIGNED, /* дата запуска */
  `finish_date`		INT(10) UNSIGNED /* дата завершения */
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* ОЧЕРЕДЬ ЗАПУСКА ЗАДАЧ */
DROP TABLE IF EXISTS `task_submit_queue`;
CREATE TABLE `task_submit_queue` (
  `trigger_task_id`		INT(10) UNSIGNED,
  `dependent_task_id`	INT(10) UNSIGNED,
  `error_code`			INT(10) /* 1 - авторизация myproxy не пройдена */
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/* СТАТУСЫ ЗАДАЧ */
DROP TABLE IF EXISTS `task_states`;
CREATE TABLE `task_states` (
  `id`				int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name`			VARCHAR(255)
  `title`			VARCHAR(255)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;






