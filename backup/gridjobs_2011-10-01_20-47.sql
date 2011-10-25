#SKD101|gridjobs|22|2011.10.01 20:47:26|568|113|113|113|113|2|4|4|4|4|4|9|3|2|13|13|16|21|7|6|4

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

DROP TABLE IF EXISTS `lng_en`;
CREATE TABLE `lng_en` (
  `snippet_id` int(10) unsigned default NULL,
  `text` text /*!40101 collate utf8_bin */,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `lng_en` VALUES
(1, 'Main'),
(2, 'Projects'),
(3, 'Tasks'),
(6, 'Grid Certificates'),
(7, 'Virtual Organizations'),
(8, 'Crimean Environment Grid Portal'),
(9, 'Version'),
(10, 'Welcome'),
(15, 'You are not in the correct VO'),
(12, 'Exit'),
(16, 'detail'),
(17, 'task id'),
(18, 'task name'),
(19, 'xrsl_command'),
(20, 'status'),
(21, 'date'),
(22, 'xrsl presence'),
(23, 'options'),
(24, 'add new task'),
(25, 'rename'),
(26, 'run'),
(27, 'delete'),
(28, 'files'),
(29, 'Sorry, an error occurred! Our experts are already working on a fix.'),
(30, 'Task settings startup '),
(31, 'myproxy server'),
(32, 'Uploading task files to server'),
(33, 'enter the path to file'),
(34, 'send file'),
(35, 'list of files on the server'),
(36, 'username'),
(37, 'password'),
(38, 'max time'),
(39, 'Run a task !'),
(40, 'Will be loaded sample test task executing the command /bin/sleep for 2850 seconds, and using a single processor'),
(41, 'Help'),
(42, 'state'),
(43, 'submitted'),
(44, 'prepared'),
(45, 'analyze'),
(46, 'go to the curent task'),
(47, 'to the task list'),
(48, 'in queue: Q'),
(49, 'analyze'),
(50, 'no tasks'),
(51, 'login'),
(52, 'password'),
(53, 'term certificate'),
(54, 'day'),
(55, 'week'),
(56, 'month'),
(57, 'months'),
(58, 'save'),
(59, 'to register every time in manual mode'),
(60, 'THEI'),
(61, 'thei.org.ua'),
(62, '7512'),
(63, 'GRID.ORG.UA'),
(64, 'GRID.ORG.UA'),
(65, 'server'),
(66, 'specify the server myproxy'),
(67, 'THEI.ORG.UA'),
(68, 'stop'),
(69, 'get'),
(70, 'Do you want to delete the task'),
(71, '(test)'),
(72, 'proceed'),
(73, 'cancel'),
(75, 'Get task files '),
(74, 'with all files'),
(76, 'cancel'),
(77, 'proceed'),
(78, 'Files of the task obtained'),
(79, 'Record added successfully !'),
(80, 'Record was successfully deleted !'),
(81, 'Creating a new task'),
(84, 'Enter the name of the new task:'),
(85, 'no present'),
(86, 'present'),
(87, 'Run'),
(88, 'Back to tasks list'),
(89, 'The task was successfully started'),
(90, 'Unable to run the task:'),
(98, 'Personal data'),
(97, 'Rename task'),
(95, 'Request mayproxy well! Continuing:'),
(96, 'task submit successfully!'),
(99, 'Check VOMS'),
(100, 'Provisional certificate'),
(101, 'Change of personal data'),
(102, 'Your DN:'),
(103, 'Contacts'),
(104, 'e-mail:'),
(105, 'Phone:'),
(106, 'Messenger:'),
(107, 'Differents'),
(108, 'Projects'),
(109, 'Task category'),
(110, 'and software:'),
(111, 'Choose virtual organization for verification:'),
(112, '(you are a member of this organization)'),
(113, 'verify'),
(114, 'Choice VO by default'),
(115, 'Choose VO use default:'),
(116, 'Choose VO ...'),
(117, 'Тимчасовий сертифікат myproxy'),
(118, 'Specify the settings for the myproxy server '),
(119, '(you are not enter this organization)'),
(120, 'Project does not contain any VO'),
(121, 'Error !!!'),
(122, 'Finished'),
(123, 'in queue: R'),
(124, 'Access denied');

DROP TABLE IF EXISTS `lng_ru`;
CREATE TABLE `lng_ru` (
  `snippet_id` int(10) unsigned default NULL,
  `text` text /*!40101 collate utf8_bin */,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `lng_ru` VALUES
(1, 'Главная'),
(2, 'Проекты'),
(3, 'Задачи'),
(6, 'Сертификаты для Грид'),
(7, 'Виртуальные организации'),
(8, 'Крымский экологический Грид портал '),
(9, 'Версия'),
(10, 'Приветствуем Вас'),
(15, 'Вы не состоите в нужной VO'),
(12, 'Выход'),
(16, 'подробней'),
(17, 'id задачи'),
(18, 'имя задачи'),
(19, 'команда_xrsl'),
(20, 'статус'),
(21, 'дата'),
(22, 'наличие xrls'),
(23, 'опции'),
(24, 'Добавить новую задачу'),
(25, 'переименовать'),
(26, 'запуск'),
(27, 'удалить'),
(28, 'файлы'),
(29, 'Извините, произошла ошибка! Наши специалисты уже работают над ее устранением.'),
(30, 'Установка параметров запуска задачи'),
(31, 'сервер myproxy:'),
(32, 'Загрузка файлов задачи на сервер'),
(33, 'укажите путь к  файлу'),
(34, 'Отправить файл'),
(35, 'список файлов на сервере'),
(36, 'имя пользователя'),
(37, 'password'),
(38, 'max time'),
(39, 'Запуск задачи !'),
(40, 'Будет загружен пример тестовой задачи выполняющей команду /bin/sleep на протяжении 2850 секунд и используя один процессор'),
(41, 'Помощь'),
(42, 'статус'),
(43, 'принята'),
(44, 'приготовлена'),
(45, 'Анализ'),
(46, 'перейти к текущей задаче'),
(47, 'к списку задач'),
(48, 'в очереди: Q'),
(49, 'Анализ'),
(50, 'нет ни одной задачи'),
(51, 'логин'),
(52, 'пароль'),
(53, 'срок сертификата'),
(54, 'день'),
(55, 'неделя'),
(56, 'месяц'),
(57, 'месяцев'),
(58, 'сохранить'),
(59, 'производить регистрацию каждый раз в ручном режиме'),
(60, 'THEI'),
(61, 'thei.org.ua'),
(62, '7512'),
(63, 'GRID.ORG.UA'),
(64, 'GRID.ORG.UA'),
(65, 'сервер'),
(66, 'укажите сервер myproxy'),
(67, 'THEI.ORG.UA'),
(68, 'остановить'),
(69, 'забрать'),
(70, 'Хотите удалить задачу'),
(71, '(тестовая)'),
(72, 'продолжить'),
(73, 'отменить'),
(74, 'со всеми файлами'),
(75, 'Забрать файлы задачи'),
(76, 'отменить'),
(77, 'продолжить'),
(78, 'Файлы задачи получены'),
(79, 'Запись успешно добавлена !'),
(80, 'Запись успешно удалена !'),
(81, 'Создание новой задачи'),
(84, 'Введите имя новой задачи:'),
(87, 'Запуск'),
(85, 'нет'),
(86, 'есть'),
(88, 'Вернуться к списку задач'),
(89, 'Задача успешно запущена'),
(90, 'Не удалось запустить задачу:'),
(97, 'Переименовать задчу'),
(95, 'Запрос Майпрокси удачно! Продолжаем:'),
(96, ' Задача успешно запущена!'),
(98, 'Личные данные'),
(99, 'Проверка VOMS'),
(100, 'Временный сертификат'),
(101, 'Изменение личных данных'),
(102, 'Ваш DN:'),
(103, 'Контакты'),
(104, 'e-mail:'),
(105, 'Телефон:'),
(106, 'Мессенджер:'),
(107, 'Разное'),
(108, 'Проекты'),
(109, 'Категории задач'),
(110, 'и програмное обеспечение:'),
(111, 'Выберите виртуальные организации для проверки:'),
(112, '(вы состоите в этой организации)'),
(113, 'проверить'),
(114, 'Выбор ВО по умолчанию'),
(115, 'Выберите ВО, используемую по умолчанию:'),
(116, 'Выберите ВО...'),
(117, 'Временный сертификат myproxy'),
(118, 'Укажите параметры доступа к серверу myproxy'),
(119, '(вы не состоите в этой организации)'),
(120, 'Проект не содержит ни одной ВО'),
(121, 'Ошибка !!!'),
(122, 'Завершена'),
(123, 'в очереди: R'),
(124, 'Доступ запрещен');

DROP TABLE IF EXISTS `lng_snippets`;
CREATE TABLE `lng_snippets` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `description` text /*!40101 collate utf8_bin */,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=125 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

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
(29, 'error.usermessage', ''),
(30, 'xrls_edit.taskset', ''),
(31, 'xrls_edit.server', ''),
(32, 'upload_files.uploadfilemsg', ''),
(33, 'upload_files.uploadfiles', ''),
(34, 'upload_files.sendfile', ''),
(35, 'upload_files.sendfileslist', ''),
(36, 'xrls_edit.username', ''),
(37, 'xrls_edit.password', ''),
(38, 'xrls_edit.max-time', ''),
(39, 'xrls_edit.starting-task', ''),
(40, 'edit.help-test-task', ''),
(41, 'edit.help-alt', ''),
(42, 'tasklist.statetitle', ''),
(43, 'task.state.submitted', ''),
(44, 'task.state.prepared', ''),
(45, 'top-menu.analyze', ''),
(46, 'task.go-to-current-task', ''),
(47, 'task.go-to-list', ''),
(48, 'task.state.inlrms: q', ''),
(49, 'task.analyze', ''),
(50, 'tasklist.no-task', ''),
(51, 'login', ''),
(52, 'password', ''),
(53, 'profile.myproxy.cert_ttl', ''),
(54, 'profile.myproxy.day', ''),
(55, 'profile.myproxy.week', ''),
(56, 'profile.myproxy.month', ''),
(57, 'profile.myproxy.6month', ''),
(58, 'save', ''),
(59, 'profile.myproxy.not-register', ''),
(60, 'THEI', ''),
(61, 'thei.org.ua', ''),
(62, '7512', ''),
(63, 'GRID.ORG.UA', ''),
(64, 'grid.org.ua', ''),
(65, 'server', ''),
(66, 'profile.myproxy.select-server', ''),
(67, 'THEI.ORG.UA', ''),
(68, 'task.stop', ''),
(69, 'task.get-result', ''),
(70, 'task.delete-1', ''),
(71, 'task.delete-2', ''),
(72, 'task.delete-4', ''),
(73, 'task.delete-5', ''),
(74, 'task.delete-3', ''),
(75, 'xrls_edit.get-task', ''),
(76, 'xrls_edit.cancel', ''),
(77, 'xrls_edit.get', ''),
(78, 'xrls_edit.success', ''),
(79, 'task.controller.RecordAddSucsess', ''),
(80, 'task.controller.RecordRemoveSucsess', ''),
(81, 'edit.addNewTask', ''),
(84, 'edit.name', ''),
(87, 'xrls_edit.start-task', ''),
(85, 'TaskList.no', ''),
(86, 'TaskList.present', ''),
(88, 'xrls_edit.go-to-task-list', ''),
(89, 'Task.controller.task-run-success', ''),
(90, 'Task.controller.task-run-fail', ''),
(99, 'profile.check-voms', ''),
(98, 'profile.private-data', ''),
(97, 'edit.renameTask', ''),
(95, 'Task.model.myproxy-success-proceed', ''),
(96, 'Task.model.rusk-run-success', ''),
(100, 'profile.temparal-cert-voms', ''),
(101, 'profile.chenge-privat-data', ''),
(102, 'profile.you-dn', ''),
(103, 'profile.contacts', ''),
(104, 'profile.e-mail', ''),
(105, 'profile.phon', ''),
(106, 'profile.messager', ''),
(107, 'profile.different', ''),
(108, 'profile.projects', ''),
(109, 'profile.task-type', ''),
(110, 'profile.software', ''),
(111, 'profile.enter-voms-for-check', ''),
(112, 'profile.you-member-vo', ''),
(113, 'profile.check', ''),
(114, 'default-vo', ''),
(115, 'enter-defaul-vo', ''),
(116, 'enter-vo', ''),
(117, 'project-temporary-cert', ''),
(118, 'enter-parametrs-myproxy', ''),
(119, 'profile.you-not-member-vo', ''),
(120, 'project-not-contains-vo', ''),
(121, 'task.state.failed', ''),
(122, 'task.state.finished', ''),
(123, 'task.state.inlrms: r', ''),
(124, 'access denided', '');

DROP TABLE IF EXISTS `lng_ua`;
CREATE TABLE `lng_ua` (
  `snippet_id` int(10) unsigned default NULL,
  `text` text /*!40101 collate utf8_bin */,
  UNIQUE KEY `snippet_id` (`snippet_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `lng_ua` VALUES
(1, 'Головна'),
(2, 'Проекти'),
(3, 'Завдання'),
(7, 'Віртуальні організації'),
(6, 'Сертификати'),
(8, 'Кримський екологічний Грід портал'),
(9, 'Версія'),
(10, 'Вітаємо Вас'),
(15, 'Ви не перебуваєте в потрібній VO'),
(12, 'Вихід'),
(16, 'детальніше'),
(17, 'id завдання'),
(18, 'ім\'я завдання'),
(19, 'команда_xrsl'),
(20, 'статус'),
(21, 'дата'),
(22, 'наявність xrls'),
(23, 'опції'),
(24, 'Додати нове завдання'),
(25, 'перейменувати'),
(26, 'запуск'),
(27, 'видалити'),
(28, 'файли'),
(29, 'Вибачте, сталася помилка! Наші фахівці вже працюють над її усуненням.'),
(30, 'Установка параметрів запуску завдання'),
(31, 'сервер myproxy:'),
(32, 'Завантаження файлів завдання на сервер'),
(33, 'вкажіть шлях до файлу'),
(34, 'Надіслати файл'),
(35, 'список файлів на сервері'),
(36, 'ім\'я користувача'),
(37, 'password'),
(38, 'max time'),
(39, 'Запуск завдання !'),
(40, 'Буде завантажений приклад тестового завдання який виконує команду /bin/sleep протягом 2850 секунд і використовуючи один процесор'),
(41, 'Допомога'),
(42, 'статус'),
(43, 'прийнята'),
(44, 'приготовлена'),
(45, 'Аналіз'),
(46, 'перейти до поточного завдання'),
(47, 'до списку завдань'),
(48, 'в черзі: Q'),
(49, 'Аналіз'),
(50, 'немає жодного завдання'),
(51, 'логін'),
(52, 'пароль'),
(53, 'термін сертифіката'),
(54, 'день'),
(55, 'тиждень'),
(56, 'місяць'),
(57, 'місяців'),
(58, 'сберегти'),
(59, 'проводити реєстрацію щоразу в ручному режимі'),
(60, 'THEI'),
(61, 'thei.org.ua'),
(62, '7512'),
(63, 'GRID.ORG.UA'),
(64, 'GRID.ORG.UA'),
(65, 'сервер'),
(66, 'вкажіть сервер myproxy'),
(67, 'THEI.ORG.UA'),
(68, 'зупинити'),
(69, 'забрати'),
(70, 'Хочете видалити завдання'),
(71, '(тестова)'),
(72, 'продовжити'),
(73, 'відмінити'),
(74, 'з усіма файлами'),
(75, 'Забрати файли завдання'),
(76, 'відмінити'),
(77, 'продовжити'),
(78, 'Файли завдання отримані'),
(79, 'Запис успішно добавлена​​ !'),
(80, 'Запис успішно видалена !'),
(81, 'Створення нового завдання'),
(84, 'Введіть ім\'я нового завдання:'),
(85, 'немає'),
(86, 'є'),
(87, 'Запуск'),
(88, 'Повернутися до списку завдань'),
(89, 'Завдання успішно запущено'),
(90, 'Не вдалося запустити завдання:'),
(97, 'Перейменувати завдання'),
(95, 'Запит Майпроксі вдало! продовжуємо:'),
(96, 'Завдання успішно запущено!'),
(98, 'Особисті дані'),
(99, 'Перевірка VOMS'),
(100, 'Tимчасовий сертифікат'),
(101, 'Зміна особистих даних'),
(102, 'Ваш DN:'),
(103, 'Контакти'),
(104, 'e-mail:'),
(105, 'Телефон:'),
(106, 'Месенджер:'),
(107, 'Різне'),
(108, 'Проекти'),
(109, 'Категорії завдань'),
(110, 'і програмне забезпечення:'),
(111, 'Виберіть віртуальні організації для перевірки:'),
(112, '(ви перебуваєте в цій організації)'),
(113, 'перевірити'),
(114, 'Вибір ВО за замовчуванням'),
(115, 'Виберіть ВО, використовувану за замовчунням:'),
(116, 'Виберіть ВО ...'),
(117, 'Temporary certificate myproxy'),
(118, 'Вкажіть параметри доступу до сервера myproxy'),
(119, '(ви не перебуваєте в цій організації)'),
(120, 'Проект не містить жодної ВО'),
(121, 'Помилка !!!'),
(122, 'Завершена'),
(123, 'в черзі: R'),
(124, 'Доступ заборонений');

DROP TABLE IF EXISTS `myproxy_servers`;
CREATE TABLE `myproxy_servers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `url` text /*!40101 collate utf8_bin */,
  `port` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `myproxy_servers` VALUES
(1, 'THEI.ORG.UA', 'thei.org.ua', 7512),
(2, 'GRID.ORG.UA', 'grid.org.ua', 7512);

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
) ENGINE=MyISAM AUTO_INCREMENT=5 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `page` VALUES
(1, 'Главная страница', 'main', '<p>Добро пожаловать в ГРИД!</p><p>&nbsp;</p><p>В скором времени:</p><ul style=\"list-style: disc;\"><li>регистрация новых пользователей&nbsp;</li><li>авторизация по сертификату&nbsp;</li><li>мультиязычность&nbsp;</li></ul>', 1, '1', '1', '', '', 1309216069, 1309215473),
(2, 'Сертификаты для Грид', 'grid-certificates', '<p>Список сертификатов появится немного позже</p>', 1, '1', '1', '', '', 1309215506, 1309215506),
(3, 'Виртуальные организации', 'virtual-organizations', '<p>Список виртуальных организаций появиться немного позже.</p>', 1, '1', '1', '', '', 1309215532, 1309215525),
(4, 'Вступление в виртуальную организацию', 'join-vo', '<p>Как вступить в ВО.</p>', 3, '1', '0', '', '', 1311096724, 1311096724);

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `alias` varchar(255) /*!40101 collate utf8_bin */ NOT NULL,
  `type` tinyint(3) unsigned NOT NULL default '1',
  `author` int(10) unsigned NOT NULL,
  `published` char(1) /*!40101 collate utf8_bin */ default '0',
  `locked` char(1) /*!40101 collate utf8_bin */ default '0',
  `modif_date` int(10) unsigned default '0',
  `create_date` int(10) unsigned default '0',
  PRIMARY KEY  (`id`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM AUTO_INCREMENT=5 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `pages` VALUES
(1, 'main', 1, 1, '1', '0', 1317364525, 1316504239),
(2, 'virtual-organizations', 1, 2, '1', '0', 1316546628, 1316546628),
(3, 'projects', 1, 2, '1', '0', 1317364079, 1316547171),
(4, '4', 2, 2, '1', '0', 1317364736, 1316797163);

DROP TABLE IF EXISTS `pages_en`;
CREATE TABLE `pages_en` (
  `page_id` int(10) unsigned NOT NULL,
  `title` text /*!40101 collate utf8_bin */,
  `body` text /*!40101 collate utf8_bin */,
  `meta_description` text /*!40101 collate utf8_bin */,
  `meta_keywords` text /*!40101 collate utf8_bin */,
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `pages_en` VALUES
(1, 'main page', '<p>content of main page</p><p>this is real Ololo!</p>', NULL, NULL),
(2, NULL, NULL, NULL, NULL),
(3, NULL, '<p><strong>Under development</strong></p>', NULL, NULL),
(4, NULL, '<p><strong>Welcome new user Grid-portal We are glad to see you!</strong><br /><br /><strong>Please fill out a profile for that would simplify</strong><br /><br /><strong> with the portal in the future.</strong></p>', NULL, NULL);

DROP TABLE IF EXISTS `pages_ru`;
CREATE TABLE `pages_ru` (
  `page_id` int(10) unsigned NOT NULL,
  `title` text /*!40101 collate utf8_bin */,
  `body` text /*!40101 collate utf8_bin */,
  `meta_description` text /*!40101 collate utf8_bin */,
  `meta_keywords` text /*!40101 collate utf8_bin */,
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `pages_ru` VALUES
(1, 'Главная страница', '<p><span style=\"font-size: medium;\">Для работы на грид портале требуется скачать и настроить утилиту:</span></p><p><span style=\"font-size: medium;\">Certificate Managment Wizard for Grid</span></p><h3><span>Запустить используя java WebStart</span></h3><td><script src=\"http://www.java.com/js/deployJava.js\"></script><script type=\"text/javascript\">// <![CDATA[      deployJava.createWebStartLaunchButton(\"http://www.thei.org.ua/grid/myproxy.jnlp\", \"1.6.0\");// ]]></script></td><tr><td><span>&nbsp;<a href=\"http://www.thei.org.ua/grid/myproxy.jnlp\">http://www.thei.org.ua/grid/myproxy.jnlp</a></span></td><td>&nbsp;</td></tr><script src=\"http://www.java.com/js/deployJava.js\"></script><script type=\"text/javascript\">// <![CDATA[deployJava.createWebStartLaunchButton(\"http://www.thei.org.ua/grid/myproxy.jnlp\", \"1.6.0\");// ]]></script>', NULL, NULL),
(2, 'Виртуальные организации', '<p>...</p>', NULL, NULL),
(3, 'Проекты', '<p><strong>В стадии разработки&nbsp;</strong></p>', NULL, NULL),
(4, 'приветствие в профиле', '<p><strong>Здравствуйте, новый пользователь Grid-портала! Мы рады Вас видеть!</strong></p><p><strong>Пожалуйста, заполните профиль для того, что бы упростить работу</strong></p><p><strong>&nbsp;с порталом в дальнейшем.&nbsp;</strong></p>', NULL, NULL);

DROP TABLE IF EXISTS `pages_ua`;
CREATE TABLE `pages_ua` (
  `page_id` int(10) unsigned NOT NULL,
  `title` text /*!40101 collate utf8_bin */,
  `body` text /*!40101 collate utf8_bin */,
  `meta_description` text /*!40101 collate utf8_bin */,
  `meta_keywords` text /*!40101 collate utf8_bin */,
  PRIMARY KEY  (`page_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `pages_ua` VALUES
(1, NULL, NULL, NULL, NULL),
(2, NULL, NULL, NULL, NULL),
(3, NULL, '<p><strong>В стадіі розробки</strong></p>', NULL, NULL),
(4, NULL, '<p>Доброго дня, новий користувач Grid-порталу! Ми раді Вас бачити!<br /><br />Будь ласка, заповніть профіль для того, щоб спростити роботу<br /><br /> з порталом надалі.</p>', NULL, NULL);

DROP TABLE IF EXISTS `project_allowed_voms`;
CREATE TABLE `project_allowed_voms` (
  `project_id` int(10) unsigned NOT NULL default '0',
  `voms_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`project_id`,`voms_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `project_allowed_voms` VALUES
(1, 3),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(4, 3),
(4, 4),
(4, 5),
(4, 6);

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `projects` VALUES
(1, 'FOREST FIRE PREDICTION'),
(2, 'MOLECULAR DYNAMICS'),
(4, 'TEST');

DROP TABLE IF EXISTS `software`;
CREATE TABLE `software` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `project_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `software` VALUES
(1, 'FDS симулятор', 1),
(2, 'Gromax', 2);

DROP TABLE IF EXISTS `task_states`;
CREATE TABLE `task_states` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `title` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `task_states` VALUES
(1, 'SUBMITTED', 'task.state.submitted'),
(2, 'ACCEPTING', 'task.state.accepting'),
(6, 'FAILED', 'task.state.failed'),
(4, 'FINISHED', 'task.state.finished'),
(5, 'INLRMS', 'task.state.inlrms'),
(7, 'INLRMS: R', 'task.state.inlrms: r'),
(8, 'INLRMS: Q', 'task.state.inlrms: q'),
(9, 'DELETED', 'task.state.deleted'),
(10, 'PREPARED', 'task.state.prepared'),
(11, 'INLRMS:R', 'task.state.inlrms:r'),
(12, 'PREPARING', 'task.state.preparing'),
(13, 'INLRMS:Q', 'task.state.inlrms:q'),
(14, 'EXECUTED', 'task.state.executed');

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(10) unsigned default NULL,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `jobid` varchar(255) /*!40101 collate utf8_bin */ NOT NULL,
  `xrsl_command` text /*!40101 collate utf8_bin */,
  `state` smallint(6) default NULL,
  `date` int(10) unsigned default NULL,
  `is_test` char(1) /*!40101 collate utf8_bin */ default NULL,
  `is_gridjob_loaded` char(1) /*!40101 collate utf8_bin */ NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `tasks` VALUES
(13, 4, '1', '', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 10, 1316830106, '1', '1'),
(14, 6, 'TEST', 'gsiftp://uagrid.org.ua:2811/jobs/1557313168854121989188400', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 6, 1316885362, '1', '1'),
(26, 2, 'TEST!', '', NULL, 10, 1317266944, '0', '0'),
(17, 2, 'Forest-fire', 'gsiftp://arc.univ.kiev.ua:21/job/13891317267429518848720', 'a:10:{s:10:\"executable\";s:6:\"job.sh\";s:11:\"executables\";s:26:\"fds5_openmp_intel_linux_32\";s:10:\"inputFiles\";s:65:\"(\"fds5_openmp_intel_linux_32\" \"\")(\"job.sh\" \"\")(\"forest_3.fds\" \"\")\";s:6:\"stdout\";s:11:\"\"hello.txt\"\";s:6:\"stderr\";s:11:\"\"hello.err\"\";s:11:\"outputFiles\";s:54:\"(\"hello.txt\" \"\")(\"forest_fire.tar\" \"\")(\"hello.err\" \"\")\";s:5:\"gmlog\";s:9:\"\"gridlog\"\";s:7:\"jobname\";s:21:\"\"forest fire2_openMP\"\";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 6, 1317153162, '0', '1'),
(9, 2, 'TEST', 'gsiftp://arc.univ.kiev.ua:21/job/1332513172498411489561350/CrimeaEco.err', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 4, 1316703634, '1', '1'),
(18, 2, 'TEST_NEW', 'gsiftp://thei.org.ua:2811/jobs/173313171537911151683717/CrimeaEco.txt', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 4, 1317153769, '1', '1'),
(28, 2, 'TEST!', 'gsiftp://arc.univ.kiev.ua:21/job/211213172706021880237163/CrimeaEco.err', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 4, 1317268812, '1', '1'),
(19, 6, 'test_dima', '', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 10, 1317240149, '1', '1'),
(29, 2, 'TEST123', 'gsiftp://arc.hpcc.kpi.ua:2811/jobs/526613174801781417957473', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 6, 1317270837, '1', '1'),
(30, 2, 'Start1', 'gsiftp://thei.org.ua:2811/jobs/678013174802531454462177', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 7, 1317277251, '1', '1'),
(31, 2, '123', '', NULL, 10, 1317390913, '0', '0'),
(32, 2, 'test', 'gsiftp://arc.univ.kiev.ua:21/job/44301317392049336680053/CrimeaEco.err', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 4, 1317390967, '1', '1'),
(33, 9, 'test', 'gsiftp://arc.univ.kiev.ua:21/job/172013174747531266208357', 'a:9:{s:10:\"executable\";s:12:\"\"/bin/sleep\"\";s:9:\"arguments\";s:6:\"\"2850\"\";s:7:\" stdout\";s:15:\"\"CrimeaEco.txt\"\";s:7:\" stderr\";s:15:\"\"CrimeaEco.err\"\";s:12:\" outputFiles\";s:40:\"(\"CrimeaEco.txt\" \"\")(\"CrimeaEco.err\" \"\")\";s:6:\" gmlog\";s:10:\"\"gridlog\" \";s:8:\" jobname\";s:27:\"\"Test job on CrimeaEco VO\" \";s:8:\" cputime\";s:5:\"2850 \";s:6:\" count\";s:2:\"1 \";}', 4, 1317474724, '1', '1');

DROP TABLE IF EXISTS `user_accepted_voms`;
CREATE TABLE `user_accepted_voms` (
  `uid` int(11) NOT NULL default '0',
  `voms_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`voms_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `user_accepted_voms` VALUES
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(4, 3),
(6, 3),
(7, 3),
(7, 4),
(7, 5),
(7, 6),
(9, 3),
(9, 6),
(10, 3),
(10, 4),
(10, 5),
(10, 6);

DROP TABLE IF EXISTS `user_allowed_projects`;
CREATE TABLE `user_allowed_projects` (
  `uid` int(10) unsigned NOT NULL default '0',
  `project_id` int(10) unsigned NOT NULL default '0',
  `default_vo` int(11) default NULL,
  PRIMARY KEY  (`uid`,`project_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `user_allowed_projects` VALUES
(1, 1, NULL),
(1, 2, NULL),
(1, 4, NULL),
(2, 1, 3),
(2, 2, 3),
(2, 4, 4),
(4, 4, 3),
(4, 2, NULL),
(4, 1, 3),
(7, 1, NULL),
(7, 2, NULL),
(7, 4, NULL),
(6, 4, NULL),
(6, 2, NULL),
(6, 1, NULL),
(9, 1, 3),
(9, 2, 3),
(9, 4, 3),
(10, 4, 3),
(10, 2, 3),
(10, 1, 3);

DROP TABLE IF EXISTS `user_allowed_software`;
CREATE TABLE `user_allowed_software` (
  `uid` int(10) unsigned NOT NULL default '0',
  `software_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`uid`,`software_id`)
) ENGINE=MyISAM /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `user_allowed_software` VALUES
(2, 1),
(2, 2),
(4, 1),
(6, 1),
(6, 2),
(9, 1),
(9, 2);

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
  `login` varchar(100) /*!40101 collate utf8_bin */ default NULL,
  `password` varchar(100) /*!40101 collate utf8_bin */ default NULL,
  `dn` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `dn_cn` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `surname` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `name` varchar(255) /*!40101 collate utf8_bin */ default NULL,
  `level` smallint(6) default NULL,
  `regdate` int(10) unsigned default NULL,
  `profile` text /*!40101 collate utf8_bin */ NOT NULL,
  `myproxy_manual_login` tinyint(1) NOT NULL,
  `myproxy_no_password` tinyint(1) NOT NULL,
  `myproxy_login` varchar(255) /*!40101 collate utf8_bin */ NOT NULL,
  `myproxy_password` varchar(255) /*!40101 collate utf8_bin */ NOT NULL,
  `myproxy_server_id` int(11) NOT NULL,
  `myproxy_expire_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `users` VALUES
(1, 'root', 'b1a838a7ee5413752554941c22926a1615866622', 'test test', '', 'root', 'root', 50, 0, 'a:3:{s:5:\"email\";s:16:\"vlad_dip@ukr.net\";s:5:\"phone\";s:13:\"+300955144228\";s:8:\"messager\";s:11:\"FDS, gammes\";}', 0, 0, '', '', 0, 0),
(2, '', '', '/DC=org/DC=ugrid/O=people/O=UGRID/CN=Vadim Khramov', 'Vadim Khramov', 'Khramov', 'Vadim', 50, 1311091980, 'a:3:{s:5:\"email\";s:16:\"vlad_dip@ukr.net\";s:5:\"phone\";s:12:\"+38955144228\";s:8:\"messager\";s:14:\"icq: 125656695\";}', 0, 0, 'vlad1', 'ZnJlZWRvbQ==', 1, 1317565883),
(6, NULL, NULL, '/DC=org/DC=ugrid/O=people/O=THEI/CN=Dmitriy Mickiy', 'Dmitriy Mickiy', 'Mickiy', 'Dmitriy', 10, 1316885291, 'a:3:{s:5:\"email\";s:16:\"vlad_dip@ukr.net\";s:5:\"phone\";s:13:\"+380955144228\";s:8:\"messager\";s:14:\"icq: 125656695\";}', 0, 0, 'vlad', 'ZnJlZWRvbQ==', 1, 0),
(7, NULL, NULL, '/DC=org/DC=ugrid/O=people/O=KNU/CN=Ievgen Sliusar', 'Ievgen Sliusar', 'Sliusar', 'Ievgen', 10, 1316885442, 'a:3:{s:5:\"email\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:8:\"messager\";s:0:\"\";}', 0, 0, '', '', 0, 0),
(8, NULL, NULL, '/DC=org/DC=ugrid/O=people/O=KNU/CN=Oleksandr Sudakov', 'Oleksandr Sudakov', 'Sudakov', 'Oleksandr', 10, 1317388718, '', 0, 0, '', '', 0, 0),
(10, NULL, NULL, '/DC=org/DC=ugrid/O=people/O=UGRID/CN=Vasiliy Kuzmenko', 'Vasiliy Kuzmenko', 'Kuzmenko', 'Vasiliy', 10, 1317477352, 'a:3:{s:5:\"email\";s:0:\"\";s:5:\"phone\";s:0:\"\";s:8:\"messager\";s:0:\"\";}', 0, 0, '', '', 0, 0);

DROP TABLE IF EXISTS `voms`;
CREATE TABLE `voms` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` text /*!40101 collate utf8_bin */,
  `url` text /*!40101 collate utf8_bin */,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 /*!40101 DEFAULT CHARSET=utf8 */ /*!40101 COLLATE=utf8_bin */;

INSERT INTO `voms` VALUES
(3, 'CrimeaEco', 'grid.org.ua/voms/crimeaeco'),
(4, 'Ukraine', 'grid.org.ua/voms/ukraine'),
(5, 'Moldyngrid', 'grid.org.ua/voms/moldyngrid'),
(6, 'testbed.univ.kiev.ua', 'grid.org.ua/voms/testbed.univ.kiev.ua');

