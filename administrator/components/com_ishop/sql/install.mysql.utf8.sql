DELETE FROM `#__menu_types` WHERE `menutype` = 'com_ishop';
DELETE FROM `#__menu` WHERE `menutype` = 'com_ishop';
INSERT INTO `#__menu_types` (`id`, `menutype`, `title`, `description`) VALUES
(NULL, 'com_ishop', 'Магазин', 'Меню для интернет магазина');

-- --------------------------------------------------------

--
-- Структура таблицы `#__ishop_categories`
--
DROP TABLE IF EXISTS `#__ishop_categories`;
CREATE TABLE IF NOT EXISTS `#__ishop_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL,
  `producttype_id` int(11) NOT NULL,
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL,
  `site_alias` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `source_url` varchar(250) NOT NULL,
  `img` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `description` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `parser_id` int(11) NOT NULL,
  `category_sourse_path` varchar(255) NOT NULL,
  `parce_data` TEXT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `title` (`title`),
  KEY `site_alias` (`site_alias`),
  KEY `idx_left_right` (`lft`,`rgt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

INSERT INTO `#__ishop_categories` (`id`, `parent_id`, `producttype_id`, `lft`, `rgt`, `level`, `title`, `alias`, `access`, `path`, `site_alias`, `name`, `source_url`, `img`, `note`, `desc`, `description`, `ordering`, `state`, `checked_out`, `checked_out_time`, `created_by`, `parser_id`, `category_sourse_path`) VALUES
(1, 0, 0, 0, 1, 0, 'root', 'root', 1, '', '', '', '', '', '', '', '', 0, 1, 0, '0000-00-00 00:00:00', 0, 0, '');


-- --------------------------------------------------------
--
-- Структура таблицы `#__ishop_producttypes`
--
DROP TABLE IF EXISTS `#__ishop_producttypes`;
CREATE TABLE IF NOT EXISTS `#__ishop_producttypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `cena_mag` decimal(15,2) NOT NULL,
  `cena_tut` decimal(15,2) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=163 ;

--
-- Структура таблицы `#__ishop_orders`
--
DROP TABLE IF EXISTS `#__ishop_orders`;
CREATE TABLE IF NOT EXISTS `#__ishop_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT 'ИД пользователя в системе',
  `order_status_id` int(2) NOT NULL COMMENT 'Статус заказа',
  `order_dt` datetime NOT NULL COMMENT 'Дата и время заказа',
  `sum` decimal(15,2) NOT NULL COMMENT 'Сумма заказа',
  `caddy` text NOT NULL COMMENT 'Детали заказа',
  `ch_status` text NOT NULL COMMENT 'Инф. об изменении заказа',
  `oplata_id` int(2) NOT NULL,
  `dostavka_id` int(2) NOT NULL,
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `created_dt` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `order_status_id` (`order_status_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Заказы';

-- --------------------------------------------------------

--
-- Структура таблицы `#__ishop_order_statuses`
--
DROP TABLE IF EXISTS `#__ishop_order_statuses`;
CREATE TABLE IF NOT EXISTS `#__ishop_order_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT 'Наименование статуза заказа',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Статусы заказа' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `#__ishop_products`
--
DROP TABLE IF EXISTS `#__ishop_products`;
CREATE TABLE IF NOT EXISTS `#__ishop_products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `site_alias` varchar(100) NOT NULL,
  `desc` text NOT NULL,
  `artikul` varchar(255) NOT NULL,
  `dopinfo` text NOT NULL,
  `cena_mag` decimal(15,2) NOT NULL,
  `cena_tut` decimal(15,2) NOT NULL,
  `cena_skidka` decimal(15,2) NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `novinka_dt` date NOT NULL,
  `hits` int(14) NOT NULL,
  `recommended_flag` tinyint(1) NOT NULL DEFAULT '0',
  `spets_predl` int(1) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT '1',
  `created_dt` date NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `#__ishop_users`
--
DROP TABLE IF EXISTS `#__ishop_users`;
CREATE TABLE IF NOT EXISTS `#__ishop_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT 'ИД пользователя в системе',
  `user_type_id` int(2) NOT NULL COMMENT 'Тип клиента',
  `fam` varchar(25) NOT NULL COMMENT 'Фамилия',
  `im` varchar(25) NOT NULL COMMENT 'Имя',
  `ot` varchar(25) NOT NULL COMMENT 'Отчество',
  `address` varchar(100) NOT NULL COMMENT 'Почтовый адрес',
  `phone` varchar(20) NOT NULL COMMENT 'Телефон',
  `email` varchar(70) NOT NULL COMMENT 'E-mail',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `user_type_id` (`user_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Клиенты';

-- --------------------------------------------------------

--
-- Структура таблицы `#__ishop_dostavka`
--
DROP TABLE IF EXISTS `#__ishop_dostavka`;
CREATE TABLE IF NOT EXISTS `#__ishop_dostavka` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `#__ishop_dostavka`
--

INSERT INTO `#__ishop_dostavka` (`id`, `name`) VALUES
(1, 'Курьером (только для Тюмени)'),
(2, 'Доставка СПСР (по всей России)');

-- --------------------------------------------------------

--
-- Структура таблицы `#__ishop_oplata`
--
DROP TABLE IF EXISTS `#__ishop_oplata`;
CREATE TABLE IF NOT EXISTS `#__ishop_oplata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `#__ishop_oplata`
--

INSERT INTO `#__ishop_oplata` (`id`, `name`) VALUES
(1, 'Наличными при получении'),
(2, 'Банковскими картами, электронными деньгами 50% от суммы, но не более 15 000, остальное курьеру службы СПСР-Экспресс после доставки.'),
(3, ' Оплата 100% наличными при получении курьеру службы СПСР-Экспресс.');

-- --------------------------------------------------------

--
-- Структура таблицы `#__ishop_sites`
--
DROP TABLE IF EXISTS `#__ishop_sites`;
CREATE TABLE IF NOT EXISTS `#__ishop_sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `base_url` varchar(255) NOT NULL,
  `products` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `checked_out` int(11) NOT NULL,
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `alias` (`alias`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__ishop_product_category`;
CREATE TABLE IF NOT EXISTS `#__ishop_product_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_category` (`product_id`,`category_id`),
  KEY `product_id` (`product_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__ishop_product_purchase`;
CREATE TABLE IF NOT EXISTS `#__ishop_product_purchase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_purchase` (`product_id`,`purchase_id`),
  KEY `purchase_id` (`product_id`),
  KEY `product_id` (`purchase_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;