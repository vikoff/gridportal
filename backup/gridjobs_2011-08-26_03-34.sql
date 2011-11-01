#SKD101|gridjobs|9|2011.08.26 03:34:27|110|24|24|24|24|4|6|4 #

DROP TABLE IF EXISTS `error_log`;
CREATE TABLE `error_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` text /*!40101 COLLATE utf8_bin */,
  `description` text /*!40101 COLLATE utf8_bin */,
  `session_dump` text /*!40101 COLLATE utf8_bin */,
  `hash` char(32) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `lastdate` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

DROP TABLE IF EXISTS `lng_en`;
CREATE TABLE `lng_en` (
  `snippet_id` int(10) unsigned DEFAULT NULL,
  `text` text /*!40101 COLLATE utf8_bin */,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `lng_en` VALUES
(1, 'Main'),
(2, 'Projects'),
(3, 'Tasks'),
(6, 'Grid Certificates'),
(7, 'Virtual Organizations'),
(8, 'GRID Task Manager'),
(9, 'Version'),
(10, 'Welcome'),
(15, 'You are not in the correct VO'),
(12, 'Exit'),
(16, 'detail'),
(17, 'id'),
(18, 'task name'),
(19, NULL),
(20, 'status'),
(21, 'date'),
(22, 'xrsl presence'),
(23, 'options'),
(24, 'add new task'),
(25, 'rename'),
(26, 'run'),
(27, 'delete'),
(28, 'files'),
(29, 'Sorry, some error happened..');

DROP TABLE IF EXISTS `lng_ru`;
CREATE TABLE `lng_ru` (
  `snippet_id` int(10) unsigned DEFAULT NULL,
  `text` text /*!40101 COLLATE utf8_bin */,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `lng_ru` VALUES
(1, 'Главная'),
(2, 'Проекты'),
(3, 'Задачи'),
(6, 'Сертификаты для Грид'),
(7, 'Виртуальные организации'),
(8, 'Диспетчер задач для Грид'),
(9, 'Версия'),
(10, 'Приветствуем Вас'),
(15, 'Вы не состоите в нужной VO'),
(12, 'Выход'),
(16, 'подробней'),
(17, 'id'),
(18, 'имя задачи'),
(19, NULL),
(20, 'статус'),
(21, 'Дата'),
(22, 'Наличие xrls'),
(23, 'опции'),
(24, 'Добавить новую задачу'),
(25, 'переименовать'),
(26, 'запуск'),
(27, 'удалить'),
(28, 'файлы'),
(29, 'Извините, произошла ошибка! Наши специалисты уже работают над ее устранением.');

DROP TABLE IF EXISTS `lng_snippets`;
CREATE TABLE `lng_snippets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `description` text /*!40101 COLLATE utf8_bin */,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=30 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `lng_snippets` VALUES
(1, 'top-menu.main', 'Пункты главного меню'),
(2, 'top-menu.projects', 'Пункты главного меню'),
(3, 'top-menu.tasks', 'Пункты главного меню'),
(6, 'top-menu.grid-certificates', 'Пункты главного меню'),
(7, 'top-menu.virtual-organizations', 'Пункты главного меню'),
(8, 'top.title', 'Шапка сайта'),
(9, 'top.version', 'Шапка сайта'),
(10, 'logged-block.greeting', 'Окошко профиля'),
(15, 'logged-block.not-in-vo', ''),
(12, 'logged-block.exit-btn', 'Окошко профиля'),
(16, 'detail', ''),
(17, 'tasklist.id', ''),
(18, 'tasklist.name', ''),
(19, 'tasklist.xrsl_command', ''),
(20, 'tasklist.state', ''),
(21, 'tasklist.date', ''),
(22, 'tasklist.xrsl-presence', ''),
(23, 'options', ''),
(24, 'tasklist.add-new', ''),
(25, 'rename', ''),
(26, 'task.run', ''),
(27, 'task.delete', ''),
(28, 'task.files', ''),
(29, 'error.usermessage', '');

DROP TABLE IF EXISTS `lng_ua`;
CREATE TABLE `lng_ua` (
  `snippet_id` int(10) unsigned DEFAULT NULL,
  `text` text /*!40101 COLLATE utf8_bin */,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `lng_ua` VALUES
(1, 'Головна'),
(2, 'Проекти'),
(3, 'Завдання'),
(7, 'Віртуальні організації'),
(6, 'Сертификати'),
(8, 'Диспетчер завдань для Грід'),
(9, 'Версія'),
(10, 'Вітаємо Вас'),
(15, 'Ви не перебуваєте в потрібній VO'),
(12, 'Вихід'),
(16, 'детальніше'),
(17, NULL),
(18, NULL),
(19, NULL),
(20, NULL),
(21, NULL),
(22, NULL),
(23, NULL),
(24, NULL),
(25, NULL),
(26, NULL),
(27, NULL),
(28, NULL),
(29, NULL);

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text /*!40101 COLLATE utf8_bin */ NOT NULL,
  `alias` varchar(255) /*!40101 COLLATE utf8_bin */ NOT NULL,
  `body` text /*!40101 COLLATE utf8_bin */,
  `author` int(10) unsigned NOT NULL,
  `published` char(1) /*!40101 COLLATE utf8_bin */ DEFAULT '0',
  `locked` char(1) /*!40101 COLLATE utf8_bin */ DEFAULT '0',
  `meta_description` text /*!40101 COLLATE utf8_bin */,
  `meta_keywords` text /*!40101 COLLATE utf8_bin */,
  `modif_date` int(10) unsigned DEFAULT '0',
  `create_date` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=5 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `page` VALUES
(1, 'Главная страница', 'main', '<p>Добро пожаловать в ГРИД!</p><p>&nbsp;</p><p>В скором времени:</p><ul style=\"list-style: disc;\"><li>регистрация новых пользователей&nbsp;</li><li>авторизация по сертификату&nbsp;</li><li>мультиязычность&nbsp;</li></ul>', 1, '1', '1', '', '', 1309216069, 1309215473),
(2, 'Сертификаты для Грид', 'grid-certificates', '<p>Список сертификатов появится немного позже</p>', 1, '1', '1', '', '', 1309215506, 1309215506),
(3, 'Виртуальные организации', 'virtual-organizations', '<p>Список виртуальных организаций появиться немного позже.</p>', 1, '1', '1', '', '', 1309215532, 1309215525),
(4, 'Вступление в виртуальную организацию', 'join-vo', '<p>Как вступить в ВО.</p>', 3, '1', '0', '', '', 1311096724, 1311096724);

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `xrsl_command` text /*!40101 COLLATE utf8_bin */,
  `state` smallint(6) DEFAULT NULL,
  `date` int(10) unsigned DEFAULT NULL,
  `is_test` char(1) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `is_gridjob_loaded` char(1) /*!40101 COLLATE utf8_bin */ NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `tasks` VALUES
(67, 1, '123', NULL, 1, 1313273163, '0', '0'),
(69, 1, '1', 'a:10:{s:10:\"executable\";s:6:\"job.sh\";s:11:\"executables\";s:26:\"fds5_openmp_intel_linux_32\";s:10:\"inputFiles\";s:65:\"(\"fds5_openmp_intel_linux_32\" \"\")(\"job.sh\" \"\")(\"forest_3.fds\" \"\")\";s:6:\"stdout\";s:11:\"\"hello.txt\"\";s:6:\"stderr\";s:11:\"\"hello.err\"\";s:11:\"outputFiles\";s:54:\"(\"hello.txt\" \"\")(\"forest_fire.tar\" \"\")(\"hello.err\" \"\")\";s:5:\"gmlog\";s:9:\"\"gridlog\"\";s:7:\"jobname\";s:21:\"\"forest fire2_openMP\"\";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 1, 1313505792, '0', '1'),
(59, 2, 'forestfire', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 1, 1311530553, '1', '1'),
(60, 2, 'forestfire1', NULL, 1, 1311530693, '0', '0'),
(61, 2, 'TEST', NULL, 1, 1312741161, '0', '0'),
(66, 1, 'TEST', NULL, 1, 1313273112, '1', '1');

DROP TABLE IF EXISTS `user_statistics`;
CREATE TABLE `user_statistics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT '0',
  `request_urls` text /*!40101 COLLATE utf8_bin */,
  `user_ip` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `referer` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `user_agent_raw` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `has_js` tinyint(1) DEFAULT NULL,
  `browser_name` varchar(50) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `browser_version` varchar(50) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `screen_width` smallint(5) unsigned DEFAULT NULL,
  `screen_height` smallint(5) unsigned DEFAULT NULL,
  `date` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(100) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `password` varchar(100) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `dn` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `dn_cn` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `surname` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `name` varchar(255) /*!40101 COLLATE utf8_bin */ DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `regdate` int(10) unsigned DEFAULT NULL,
  `profile` text /*!40101 COLLATE utf8_bin */ NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `users` VALUES
(1, 'root', 'b1a838a7ee5413752554941c22926a1615866622', NULL, NULL, 'root', 'root', 50, 0, ''),
(2, NULL, NULL, '/DC=org/DC=ugrid/O=people/O=UGRID/CN=Vadim Khramov', 'Vadim Khramov', 'Khramov', 'Vadim', 50, 1311091980, ''),
(4, NULL, NULL, '/DC=org/DC=ugrid/O=people/O=THEI/CN=Dmitriy Mickiy', 'Dmitriy Mickiy', 'Mickiy', 'Dmitriy', 5, 1311121485, ''),
(5, NULL, NULL, '/DC=org/DC=ugrid/O=people/O=KNU/CN=Ievgen Sliusar', 'Ievgen Sliusar', 'Sliusar', 'Ievgen', 5, 1311534890, '');

