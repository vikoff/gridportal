-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Хост: localhost
-- Время создания: Июн 30 2011 г., 20:38
-- Версия сервера: 5.1.50
-- Версия PHP: 5.2.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- База данных: `ngrid`
-- 

-- --------------------------------------------------------

-- 
-- Структура таблицы `lng_en`
-- 

DROP TABLE IF EXISTS `lng_en`;
CREATE TABLE `lng_en` (
  `snippet_id` int(10) unsigned DEFAULT NULL,
  `text` text COLLATE utf8_bin,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Дамп данных таблицы `lng_en`
-- 

INSERT INTO `lng_en` (`snippet_id`, `text`) VALUES 
(1, 'Main'),
(2, 'Projects'),
(3, 'Tasks'),
(6, 'Grid Certificates'),
(7, 'Virtual Organizations'),
(8, 'GRID Task Manager'),
(9, 'Version'),
(10, 'Hello'),
(11, 'You logged as'),
(12, 'Exit');

-- --------------------------------------------------------

-- 
-- Структура таблицы `lng_ru`
-- 

DROP TABLE IF EXISTS `lng_ru`;
CREATE TABLE `lng_ru` (
  `snippet_id` int(10) unsigned DEFAULT NULL,
  `text` text COLLATE utf8_bin,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Дамп данных таблицы `lng_ru`
-- 

INSERT INTO `lng_ru` (`snippet_id`, `text`) VALUES 
(1, 'Главная'),
(2, 'Проекты'),
(3, 'Задачи'),
(6, 'Сертификаты для Грид'),
(7, 'Виртуальные организации'),
(8, 'Диспетчер задач для Грид'),
(9, 'Версия'),
(10, 'Здравствуйте'),
(11, 'Вы вошли как'),
(12, 'Выход');

-- --------------------------------------------------------

-- 
-- Структура таблицы `lng_snippets`
-- 

DROP TABLE IF EXISTS `lng_snippets`;
CREATE TABLE `lng_snippets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `description` text COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=15 ;

-- 
-- Дамп данных таблицы `lng_snippets`
-- 

INSERT INTO `lng_snippets` (`id`, `name`, `description`) VALUES 
(1, 'top-menu.main', 'Пункты главного меню'),
(2, 'top-menu.projects', 'Пункты главного меню'),
(3, 'top-menu.tasks', 'Пункты главного меню'),
(6, 'top-menu.grid-certificates', 'Пункты главного меню'),
(7, 'top-menu.virtual-organizations', 'Пункты главного меню'),
(8, 'top.title', 'Шапка сайта'),
(9, 'top.version', 'Шапка сайта'),
(10, 'logged-block.greeting', 'Окошко профиля'),
(11, 'logged-block.enter-level', 'Окошко профиля'),
(12, 'logged-block.exit-btn', 'Окошко профиля');

-- --------------------------------------------------------

-- 
-- Структура таблицы `lng_ua`
-- 

DROP TABLE IF EXISTS `lng_ua`;
CREATE TABLE `lng_ua` (
  `snippet_id` int(10) unsigned DEFAULT NULL,
  `text` text COLLATE utf8_bin,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- 
-- Дамп данных таблицы `lng_ua`
-- 

INSERT INTO `lng_ua` (`snippet_id`, `text`) VALUES 
(1, NULL),
(2, NULL),
(3, NULL),
(7, NULL),
(6, NULL),
(8, NULL),
(9, NULL),
(10, NULL),
(11, NULL),
(12, NULL);
