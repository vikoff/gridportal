#SKD101|gridjobs|5|2011.06.26 15:11:43|2|1|1

DROP TABLE IF EXISTS `error_log`;
CREATE TABLE `error_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `url` text /*!40101 collate utf8_bin */,
  `description` text /*!40101 collate utf8_bin */,
  `session_dump` text /*!40101 collate utf8_bin */,
  `hash` char(32) /*!40101 collate utf8_bin */ default NULL,
  `lastdate` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` text /*!40101 collate utf8_bin */ NOT NULL,
  `alias` varchar(255) /*!40101 collate utf8_bin */ NOT NULL,
  `body` text /*!40101 collate utf8_bin */,
  `author` int(10) unsigned NOT NULL,
  `published` char(1) /*!40101 collate utf8_bin */ default '0',
  `locked` char(1) /*!40101 collate utf8_bin */ default '0',
  `meta_description` text /*!40101 collate utf8_bin */,
  `meta_keywords` text /*!40101 collate utf8_bin */,
  `modif_date` int(10) unsigned default '0',
  `create_date` int(10) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned default NULL,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `xrsl_command` text /*!40101 collate utf8_bin */,
  `state` smallint(6) default NULL,
  `date` int(10) unsigned default NULL,
  `is_test` char(1) /*!40101 collate utf8_bin */ default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `tasks` VALUES
(13, 1, '1', NULL, 1, 1308572320, '0');

DROP TABLE IF EXISTS `user_statistics`;
CREATE TABLE `user_statistics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned default '0',
  `request_urls` text /*!40101 collate utf8_bin */,
  `user_ip` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `referer` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `user_agent_raw` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `has_js` tinyint(1) default NULL,
  `browser_name` varchar(50) /*!40101 collate utf8_bin */ default NULL,
  `browser_version` varchar(50) /*!40101 collate utf8_bin */ default NULL,
  `screen_width` smallint(5) unsigned default NULL,
  `screen_height` smallint(5) unsigned default NULL,
  `date` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `login` varchar(100) /*!40101 collate utf8_bin */ NOT NULL,
  `password` varchar(100) /*!40101 collate utf8_bin */ NOT NULL,
  `surname` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `level` smallint(6) default NULL,
  `regdate` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `users` VALUES
(1, 'root', 'b1a838a7ee5413752554941c22926a1615866622', 'root', 'root', 50, 0);

