--
-- Table structure for table `#__joomd_apps`
--

DROP TABLE IF EXISTS `#__joomd_apps`;
CREATE TABLE `#__joomd_apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `label` varchar(55) NOT NULL,
  `descr` text NOT NULL,
  `type` tinyint(1) NOT NULL,
  `item` tinyint(1) NOT NULL,
  `prio` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `iscore` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `published` (`published`),
  KEY `iscore` (`iscore`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `#__joomd_apps`
--

INSERT INTO `#__joomd_apps` (`id`, `name`, `label`, `descr`, `type`, `item`, `prio`, `ordering`, `params`, `published`, `iscore`) VALUES
(5, 'category', 'CATEGORIES', 'APP_CATEGORY_DESCR', 1, 0, 2, 4, '{"aview":["category"],"sview":["category"]}', 1, 1),
(6, 'field', 'FIELDS', 'APP_FIELD_DESCR', 1, 0, 2, 5, '{"aview":["field"],"sview":[]}', 1, 1),
(7, 'item', 'ITEMS', 'APP_ITEM_DESCR', 1, 1, 2, 6, '{"aview":["item"],"sview":["item", "itempanel"]}', 1, 1),
(4, 'type', 'TYPES', 'APP_TYPE_DESCR', 1, 0, 2, 3, '{"aview":["type"],"sview":[]}', 1, 1),
(8, 'css', 'CSS', 'APP_APPS_DESCR', 5, 0, 2, 7, '{"aview":["css"],"sview":[]}', 1, 1),
(3, 'config', 'CONFIG', 'APP_CONFIG_DESCR', 1, 0, 3, 2, '{"aview":["config"],"sview":[]}', 1, 1),
(1, 'joomd', 'DASHBOARD', 'APP_DASHBOARD_DESCR', 1, 0, 1, 0, '{"aview":["joomd"],"sview":["joomd"]}', 1, 1),
(2, 'apps', 'APPS', 'APP_APPS_DESCR', 1, 0, 1, 1, '{"aview":["apps"],"sview":[]}', 1, 1),
(10, 'classic', 'CLASSIC', 'APP_CLASSIC_DESCR', 4, 0, 2, 9, '', 1, 1),
(9, 'search', 'SEARCH', 'APP_SEARCH_DESCR', 1, 0, 2, 8, '{"aview":[],"sview":["search"]}', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_apptype`
--

DROP TABLE IF EXISTS `#__joomd_apptype`;
CREATE TABLE `#__joomd_apptype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `#__joomd_apptype`
--

INSERT INTO `#__joomd_apptype` (`id`, `name`) VALUES
(1, 'CORE'),
(2, 'ITEM_SPECIFIC'),
(3, 'CUSTOM_FIELD'),
(4, 'TEMPLATE'),
(5, 'OTHER');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_category`
--

DROP TABLE IF EXISTS `#__joomd_category`;
CREATE TABLE `#__joomd_category` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `introtext` varchar(255) NOT NULL,
  `fulltext` text NOT NULL,
  `img` varchar(255) NOT NULL,
  `featured` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `hits` int(11) NOT NULL,
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=38 ;

--
-- Dumping data for table `#__joomd_category`
--

INSERT INTO `#__joomd_category` (`id`, `parent`, `name`, `alias`, `introtext`, `fulltext`, `img`, `featured`, `ordering`, `access`, `published`, `created`, `created_by`, `hits`, `language`) VALUES
(22, 0, 'Joomd Component', 'joomd-component', '', '', '', 1, 31, 1, 1, '2012-05-09 11:31:28', 42, 0, '*'),
(23, 0, 'Joomla Templates', 'joomla-templates', '', '', '', 1, 32, 1, 1, '2012-05-09 11:32:25', 42, 0, '*'),
(24, 0, 'Coupon Manager', 'coupon-manager', '', '', '', 1, 33, 1, 1, '2012-05-09 11:33:27', 42, 0, '*'),
(25, 0, 'Obituary Manager', 'obituary-manager', '', '', '', 1, 34, 1, 1, '2012-05-09 11:33:59', 42, 0, '*'),
(26, 0, 'Recipes Manager', 'recipes-manager', '', '', '', 1, 35, 1, 1, '2012-05-09 11:34:46', 42, 0, '*'),
(27, 0, 'Church Manager', 'church-manager', '', '', '', 1, 36, 1, 1, '2012-05-09 11:35:19', 42, 0, '*'),
(28, 0, 'Acura', 'acura', '', '', '', 1, 38, 1, 1, '2012-05-10 05:11:48', 42, 0, '*'),
(29, 0, 'DeSoto', 'desoto', '', '', '', 1, 37, 1, 1, '2012-05-10 05:49:09', 42, 0, '*'),
(30, 0, 'Isuzu', 'isuzu', '', '', '', 1, 39, 1, 1, '2012-05-10 05:49:42', 42, 0, '*'),
(31, 0, 'Mitsubishi', 'mitsubishi', '', '', '', 1, 40, 1, 1, '2012-05-10 05:50:10', 42, 0, '*'),
(32, 0, 'Scion', 'scion', '', '', '', 1, 41, 1, 1, '2012-05-10 05:51:11', 42, 0, '*'),
(33, 0, 'Alfa Romeo', 'alfa-romeo', '', '', '', 1, 42, 1, 1, '2012-05-10 05:52:44', 42, 0, '*'),
(34, 0, 'Dodge', 'dodge', '', '', '', 1, 43, 1, 1, '2012-05-10 05:54:49', 42, 0, '*'),
(35, 0, 'Jaguar', 'jaguar', '', '', '', 1, 44, 1, 1, '2012-05-10 05:55:27', 42, 0, '*'),
(36, 0, 'Ferrari', 'ferrari', '', '', '', 1, 45, 1, 1, '2012-05-10 05:55:55', 42, 0, '*'),
(37, 0, 'Smart', 'smart', '', '', '', 1, 46, 1, 1, '2012-05-10 05:56:20', 42, 0, '*');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_cnf`
--

DROP TABLE IF EXISTS `#__joomd_cnf`;
CREATE TABLE `#__joomd_cnf` (
  `catid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  PRIMARY KEY (`catid`,`fieldid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_cnf`
--

INSERT INTO `#__joomd_cnf` (`catid`, `fieldid`) VALUES
(22, 29),
(22, 30),
(22, 31),
(23, 29),
(23, 30),
(23, 31),
(24, 29),
(24, 30),
(24, 31),
(25, 29),
(25, 30),
(25, 31),
(26, 29),
(26, 30),
(26, 31),
(27, 29),
(27, 30),
(27, 31),
(28, 32),
(28, 33),
(28, 34),
(28, 35),
(28, 36),
(28, 37),
(28, 38),
(28, 39),
(28, 40),
(28, 41),
(28, 42),
(28, 43),
(28, 44),
(28, 45),
(28, 46),
(28, 48),
(28, 49),
(28, 50),
(28, 51),
(28, 52),
(29, 32),
(29, 33),
(29, 34),
(29, 35),
(29, 36),
(29, 37),
(29, 38),
(29, 39),
(29, 40),
(29, 41),
(29, 42),
(29, 43),
(29, 44),
(29, 45),
(29, 46),
(29, 48),
(29, 49),
(29, 50),
(29, 51),
(29, 52),
(30, 32),
(30, 33),
(30, 34),
(30, 35),
(30, 36),
(30, 37),
(30, 38),
(30, 39),
(30, 40),
(30, 41),
(30, 42),
(30, 43),
(30, 44),
(30, 45),
(30, 46),
(30, 48),
(30, 49),
(30, 50),
(30, 51),
(30, 52),
(31, 32),
(31, 33),
(31, 34),
(31, 35),
(31, 36),
(31, 37),
(31, 38),
(31, 39),
(31, 40),
(31, 41),
(31, 42),
(31, 43),
(31, 44),
(31, 45),
(31, 46),
(31, 48),
(31, 49),
(31, 50),
(31, 51),
(31, 52),
(32, 32),
(32, 33),
(32, 34),
(32, 35),
(32, 36),
(32, 37),
(32, 38),
(32, 39),
(32, 40),
(32, 41),
(32, 42),
(32, 43),
(32, 44),
(32, 45),
(32, 46),
(32, 48),
(32, 49),
(32, 50),
(32, 51),
(32, 52),
(33, 32),
(33, 33),
(33, 34),
(33, 35),
(33, 36),
(33, 37),
(33, 38),
(33, 39),
(33, 40),
(33, 41),
(33, 42),
(33, 43),
(33, 44),
(33, 45),
(33, 46),
(33, 48),
(33, 49),
(33, 50),
(33, 51),
(33, 52),
(34, 32),
(34, 33),
(34, 34),
(34, 35),
(34, 36),
(34, 37),
(34, 38),
(34, 39),
(34, 40),
(34, 41),
(34, 42),
(34, 43),
(34, 44),
(34, 45),
(34, 46),
(34, 48),
(34, 49),
(34, 50),
(34, 51),
(34, 52),
(35, 32),
(35, 33),
(35, 34),
(35, 35),
(35, 36),
(35, 37),
(35, 38),
(35, 39),
(35, 40),
(35, 41),
(35, 42),
(35, 43),
(35, 44),
(35, 45),
(35, 46),
(35, 48),
(35, 49),
(35, 50),
(35, 51),
(35, 52),
(36, 32),
(36, 33),
(36, 34),
(36, 35),
(36, 36),
(36, 37),
(36, 38),
(36, 39),
(36, 40),
(36, 41),
(36, 42),
(36, 43),
(36, 44),
(36, 45),
(36, 46),
(36, 48),
(36, 49),
(36, 50),
(36, 51),
(36, 52),
(37, 32),
(37, 33),
(37, 34),
(37, 35),
(37, 36),
(37, 37),
(37, 38),
(37, 39),
(37, 40),
(37, 41),
(37, 42),
(37, 43),
(37, 44),
(37, 45),
(37, 46),
(37, 48),
(37, 49),
(37, 50),
(37, 51),
(37, 52);

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_config`
--

DROP TABLE IF EXISTS `#__joomd_config`;
CREATE TABLE `#__joomd_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` int(11) NOT NULL,
  `captcha` tinyint(1) NOT NULL,
  `thumb_width` int(3) NOT NULL,
  `thumb_height` int(3) NOT NULL,
  `email` text NOT NULL,
  `copyright` tinyint(1) NOT NULL,
  `scroll` tinyint(1) NOT NULL,
  `asearch` int(11) NOT NULL,
  `social` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `#__joomd_config`
--

INSERT INTO `#__joomd_config` (`id`, `template`, `captcha`, `thumb_width`, `thumb_height`, `email`, `copyright`, `scroll`, `asearch`, `social`) VALUES
(1, 1, 1, 200, 150, 'you@yourdomain.com', 1, 1, 1, '{"share":"1","fblike":"1","tweet":"1","gplus":"1"}');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_field`
--

DROP TABLE IF EXISTS `#__joomd_field`;
CREATE TABLE `#__joomd_field` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `default` text NOT NULL,
  `type` int(11) NOT NULL,
  `custom` text NOT NULL,
  `cssclass` varchar(25) NOT NULL,
  `category` tinyint(1) NOT NULL,
  `list` tinyint(1) NOT NULL,
  `detail` tinyint(1) NOT NULL,
  `search` tinyint(1) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `showtitle` tinyint(1) NOT NULL DEFAULT '1',
  `showicon` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `language` char(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

--
-- Dumping data for table `#__joomd_field`
--

INSERT INTO `#__joomd_field` (`id`, `name`, `text`, `default`, `type`, `custom`, `cssclass`, `category`, `list`, `detail`, `search`, `required`, `icon`, `showtitle`, `showicon`, `ordering`, `published`, `access`, `created`, `created_by`, `language`) VALUES
(29, 'blog image', '', '', 10, '{"multiple":"0","slide":"0","filetypes":".jpeg,.png,.gif,.jpg","maxsize":"5000","thumb_width":"200","thumb_height":"150","default":""}', 'bphoto', 0, 1, 1, 0, 0, '', 0, 0, 19, 1, 1, '2012-05-09 11:39:30', 42, '*'),
(31, 'Title', '', '', 1, '[]', 'btitle', 0, 1, 1, 1, 0, '', 0, 0, 18, 1, 1, '2012-05-09 12:42:31', 42, '*'),
(32, 'Name of Car', '', '', 1, '[]', 'cname', 0, 1, 1, 1, 0, '', 1, 0, 22, 1, 1, '2012-05-10 05:57:59', 42, '*'),
(33, 'Car Image', '', '', 10, '{"multiple":"1","slide":"1","filetypes":".jpeg,.png,.gif,.jpg","maxsize":"5000","thumb_width":"200","thumb_height":"100","default":""}', 'cphoto', 0, 1, 1, 0, 0, '', 0, 0, 23, 1, 1, '2012-05-10 05:59:14', 42, '*'),
(34, 'Summary', '', '', 6, '{"rows":"7","cols":"50","content":"1"}', 'cdes', 0, 1, 1, 0, 0, '', 0, 0, 42, 1, 1, '2012-05-10 06:10:20', 42, '*'),
(35, 'Price', '', '', 1, '[]', 'cprice', 0, 0, 1, 0, 0, '', 1, 0, 24, 1, 1, '2012-05-10 06:16:17', 42, '*'),
(36, 'Year', '', '', 1, '[]', 'cyear', 0, 0, 1, 0, 0, '', 1, 0, 25, 1, 1, '2012-05-10 06:19:05', 42, '*'),
(37, 'Make', '', '', 1, '[]', 'cmake', 0, 0, 1, 0, 0, '', 1, 0, 26, 1, 1, '2012-05-10 06:20:44', 42, '*'),
(38, 'Model', '', '', 1, '[]', 'cmodel', 0, 0, 1, 1, 0, '', 1, 0, 27, 1, 1, '2012-05-10 06:23:58', 42, '*'),
(39, 'Trim', '', '', 1, '[]', 'ctrim', 0, 0, 1, 0, 0, '', 1, 0, 28, 1, 1, '2012-05-10 06:25:20', 42, '*'),
(40, 'Engine', '', '', 1, '[]', 'cengine', 0, 0, 1, 0, 0, '', 1, 0, 29, 1, 1, '2012-05-10 06:46:38', 42, '*'),
(41, 'Fuel', '', '', 2, '{"options":"Petrol\\r\\nDiesel\\r\\nGasoline"}', 'cfuel', 0, 0, 1, 0, 0, '', 1, 0, 31, 1, 1, '2012-05-10 06:49:00', 42, '*'),
(42, 'Color', '', '', 1, '[]', 'ccolor', 0, 0, 1, 0, 0, '', 1, 0, 32, 1, 1, '2012-05-10 06:51:23', 42, '*'),
(43, 'Interior', '', '', 1, '[]', 'cinterior', 0, 0, 1, 0, 0, '', 1, 0, 33, 1, 1, '2012-05-10 06:52:39', 42, '*'),
(44, 'Miles', '', '', 1, '[]', 'cmiles', 0, 0, 1, 0, 0, '', 1, 0, 34, 1, 1, '2012-05-10 06:54:31', 42, '*'),
(45, 'Stock #', '', '', 1, '[]', 'cstock', 0, 0, 1, 0, 0, '', 1, 0, 36, 1, 1, '2012-05-10 06:59:03', 42, '*'),
(46, 'Body Style', '', '', 1, '[]', 'cbstyle', 0, 0, 1, 0, 0, '', 1, 0, 37, 1, 1, '2012-05-10 06:59:56', 42, '*'),
(52, 'Condition', '', '', 1, '[]', 'ccondition', 0, 0, 1, 0, 0, '', 1, 0, 39, 1, 1, '2012-05-10 13:30:21', 42, '*'),
(48, 'Category', '', '', 1, '[]', 'ccategory', 0, 0, 1, 0, 0, '', 1, 0, 40, 1, 1, '2012-05-10 07:03:56', 42, '*'),
(49, 'Trans', '', '', 1, '[]', 'ctrans', 0, 0, 1, 0, 0, '', 1, 0, 30, 1, 1, '2012-05-10 11:55:19', 42, '*'),
(50, 'VIN', '', '', 1, '[]', 'cvin', 0, 0, 1, 0, 0, '', 1, 0, 35, 1, 1, '2012-05-10 11:56:21', 42, '*'),
(51, 'Video', '', '', 13, '{"width":"405","height":"325"}', 'cyou', 0, 0, 1, 0, 0, '', 0, 0, 41, 1, 1, '2012-05-10 12:23:18', 42, '*'),
(30, 'desciption', '', '', 6, '{"rows":"7","cols":"50","content":"1"}', 'bdes', 0, 1, 1, 0, 0, '', 0, 0, 20, 1, 1, '2012-05-09 12:40:49', 42, '*');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_fieldtypes`
--

DROP TABLE IF EXISTS `#__joomd_fieldtypes`;
CREATE TABLE `#__joomd_fieldtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(25) NOT NULL,
  `label` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `#__joomd_fieldtypes`
--

INSERT INTO `#__joomd_fieldtypes` (`id`, `type`, `label`) VALUES
(1, 'textfield', 'TEXTFIELD'),
(2, 'radio', 'RADIO_BUTTON'),
(3, 'checkbox', 'CHECKBOX'),
(4, 'select', 'SELECT_LIST'),
(5, 'textarea', 'TEXTAREA'),
(6, 'wysiwig', 'WYSIWIG'),
(7, 'date', 'DATE'),
(8, 'email', 'EMAIL'),
(9, 'url', 'URL'),
(10, 'image', 'IMAGE'),
(11, 'file', 'FILE'),
(12, 'video', 'VIDEO'),
(13, 'youtube', 'YOUTUBE'),
(14, 'address', 'ADDRESS');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_field_address`
--

DROP TABLE IF EXISTS `#__joomd_field_address`;
CREATE TABLE `#__joomd_field_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `zoom` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_iconfig`
--

DROP TABLE IF EXISTS `#__joomd_iconfig`;
CREATE TABLE `#__joomd_iconfig` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `config` text NOT NULL,
  `acl` text NOT NULL,
  `listconfig` text NOT NULL,
  `detailconfig` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_iconfig`
--

INSERT INTO `#__joomd_iconfig` (`id`, `config`, `acl`, `listconfig`, `detailconfig`) VALUES
(1, '{"notify":"1","list":"1","detail":"1"}', '{"addaccess":["6","7","2","3","4","5","10","12","8"],"editaccess":["6","7","2","3","4","5","10","12","8"],"editall":["6","7","4","5","8"],"stateaccess":["6","7","5","8"],"stateall":["6","7","5","8"],"deleteaccess":["6","7","8"],"deleteall":["6","7","8"],"featureaccess":["6","7","3","4","5","8"],"featureall":["6","7","4","5","8"]}', '{"header":"1","more":"1","contact":"1","report":"1","save":"1","hits":"1","created":"1","author":"1","print":"1","email":"1"}', '{"contact":"1","report":"1","save":"1","hits":"1","created":"1","author":"1","print":"1","email":"1"}');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_item`
--

DROP TABLE IF EXISTS `#__joomd_item`;
CREATE TABLE `#__joomd_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `typeid` int(11) NOT NULL DEFAULT '1',
  `featured` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `publish_up` datetime NOT NULL,
  `publish_down` datetime NOT NULL,
  `hits` int(11) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `language` char(7) NOT NULL,
  `metadata` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=46 ;

--
-- Dumping data for table `#__joomd_item`
--

INSERT INTO `#__joomd_item` (`id`, `alias`, `typeid`, `featured`, `ordering`, `published`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `hits`, `access`, `language`, `metadata`) VALUES
(23, 'recipes-manager-component-for-joomla', 2, 1, 22, 1, '2012-07-13 12:06:32', 44, '2012-07-13 12:20:06', 42, '2012-07-13 12:06:32', '0000-00-00 00:00:00', 311, 1, '*', '{"meta_desc":"","meta_key":"","robots":"index, follow","author":""}'),
(24, 'obituary-listing-component-for-joomla', 2, 1, 23, 1, '2012-05-09 13:33:42', 42, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 84, 1, '*', ''),
(25, 'coupon-manager-component-for-joomla', 2, 1, 24, 1, '2012-05-09 13:44:02', 42, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 106, 1, '*', ''),
(26, 'joomd-component-for-joomla', 2, 1, 25, 1, '2012-05-09 13:55:46', 42, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 112, 1, '*', ''),
(36, '2001-acura-rl', 1, 1, 35, 1, '2012-06-26 12:25:15', 44, '2012-07-31 08:16:07', 44, '2012-07-31 08:16:07', '0000-00-00 00:00:00', 762, 1, '*', '{"meta_desc":"","meta_key":"","robots":"index, follow","author":""}'),
(45, '2009-smart-fortwo-pure', 1, 1, 44, 1, '2012-05-11 12:24:34', 42, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 45, 1, '*', '');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_item_cat`
--

DROP TABLE IF EXISTS `#__joomd_item_cat`;
CREATE TABLE `#__joomd_item_cat` (
  `catid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  PRIMARY KEY (`catid`,`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_item_cat`
--

INSERT INTO `#__joomd_item_cat` (`catid`, `itemid`) VALUES
(22, 26),
(24, 25),
(25, 24),
(26, 23),
(28, 36),
(37, 45);

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_templates`
--

DROP TABLE IF EXISTS `#__joomd_templates`;
CREATE TABLE `#__joomd_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `#__joomd_templates`
--

INSERT INTO `#__joomd_templates` (`id`, `name`) VALUES
(1, 'classic');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_tnc`
--

DROP TABLE IF EXISTS `#__joomd_tnc`;
CREATE TABLE `#__joomd_tnc` (
  `typeid` int(11) NOT NULL,
  `catid` int(11) NOT NULL,
  PRIMARY KEY (`typeid`,`catid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_tnc`
--

INSERT INTO `#__joomd_tnc` (`typeid`, `catid`) VALUES
(1, 28),
(1, 29),
(1, 30),
(1, 31),
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 44);

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_tnf`
--

DROP TABLE IF EXISTS `#__joomd_tnf`;
CREATE TABLE `#__joomd_tnf` (
  `typeid` int(11) NOT NULL,
  `fieldid` int(11) NOT NULL,
  PRIMARY KEY (`typeid`,`fieldid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_tnf`
--

INSERT INTO `#__joomd_tnf` (`typeid`, `fieldid`) VALUES
(1, 32),
(1, 33),
(1, 34),
(1, 35),
(1, 36),
(1, 37),
(1, 38),
(1, 39),
(1, 40),
(1, 41),
(1, 42),
(1, 43),
(1, 44),
(1, 45),
(1, 46),
(1, 48),
(1, 49),
(1, 50),
(1, 51),
(1, 52),
(2, 29),
(2, 30),
(2, 31);

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_type1`
--

DROP TABLE IF EXISTS `#__joomd_type1`;
CREATE TABLE `#__joomd_type1` (
  `itemid` int(11) NOT NULL,
  `field_32` varchar(255) NOT NULL,
  `field_33` varchar(255) NOT NULL,
  `field_34` text,
  `field_35` varchar(255) NOT NULL,
  `field_36` varchar(255) NOT NULL,
  `field_37` varchar(255) NOT NULL,
  `field_38` varchar(255) NOT NULL,
  `field_39` varchar(255) NOT NULL,
  `field_40` varchar(255) NOT NULL,
  `field_41` varchar(255) NOT NULL,
  `field_42` varchar(255) NOT NULL,
  `field_43` varchar(255) NOT NULL,
  `field_44` varchar(255) NOT NULL,
  `field_45` varchar(255) NOT NULL,
  `field_46` varchar(255) NOT NULL,
  `field_52` varchar(255) NOT NULL,
  `field_48` varchar(255) NOT NULL,
  `field_49` varchar(255) NOT NULL,
  `field_50` varchar(255) NOT NULL,
  `field_51` varchar(255) NOT NULL,
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_type1`
--

INSERT INTO `#__joomd_type1` (`itemid`, `field_32`, `field_33`, `field_34`, `field_35`, `field_36`, `field_37`, `field_38`, `field_39`, `field_40`, `field_41`, `field_42`, `field_43`, `field_44`, `field_45`, `field_46`, `field_52`, `field_48`, `field_49`, `field_50`, `field_51`) VALUES
(36, '2001 Acura RL', '1336730866metallic02.jpg|1336730866jh4ka96501c000253_1.jpg', '<h4>Vehicle Description</h4>\r\n<p>{phocamaps view=map|id=1}</p>\r\n<p>Our Sales Process: Simple. No Pressure. No gimmicks. Tell us what you want...then let the ETC Team go to work for you! We''ll find the vehicle you desire, at the absolute best price possible. Our Pledge: We pledge to find our customers the highest quality vehicles available. Offer them at the best possible prices. Excellence is the standard. Satisfaction is guaranteed! Contact Us Today: 614-277-2000 Grove City Office 614-905-7432 Denny Gray (cell) 614-565-0918 Steve Roper (cell)</p>\r\n<h4>Vehicle Options</h4>\r\n<p> 4-Wheel ABS,Adjustable Steering Wheel,Air Bag-Passenger Sensor,Air Conditioning,Aluminum Wheels,AM/FM Stereo,Auto Dimming R/V Mirror,Auto-Off Headlights,Auxiliary Power Outlet,Bucket Seats,Cassette Player,CD Changer,CD Single-Disc Player,Child Proof Locks,Climate Control / Automatic,Cruise Control,Disc Brakes,Driver Illuminated Vanity Mirror,Drivers Air Bag,Electric Fuel System,Electronic Stability Control,Emergency Trunk Release,Fog Lights,Front Wheel Drive,Garage Door Opener,Gasoline Fuel,Heated Mirror(s),Heated Seats,HID Headlamps,Intermittent Wipers,Keyless Entry,Leather Seats,Mirror Memory,Pass-Through Rear Seat,Passenger Air Bag,Passenger Illuminated Visor Mirror,Passenger Vanity Mirror,Power Locks,Power Mirrors,Power Passenger Seat,Power Seat,Power Steering,Power Tilt/Sliding Sunroof,Power Windows,Premium Sound System,Reading Light(s),Rear Reading Lamps,Rear Window Defrost,Remote Trunk Release,Seat Memory,Side Curtain Air Bag,Steering Wheel Audio,Steering Wheel Controls,Sun/Moon Roof,Tires - Front Performance,Tires - Rear Performance,Traction Control,Vanity Mirrors,Variable Speed Intermittent Wipers,Wood Trim</p>', '$5,995', '2001', 'Acura', 'RL', '3.5', '6-Cylinder V-6 cyl', 'Diesel', 'Grey', 'Charcoal', '192000', '000253', 'Sedan', 'Used', 'Used Cars for sale', '4-Speed Automatic', 'jh4ka96501c000253', 'iGYzF8nlir4'),
(45, '2009 smart fortwo Pure', '1336739074Nuevo-diseo-para-el-Smart-Fortwo-2012.jpg|13367390742012KIA-Sorento.jpg', '<h4>Vehicle Description</h4>\r\n<p>This 2dr Car generally a joy to drive. You will find its Gas I3 1.0/61 and 5-Speed Manual is in great running condition. Call and speak with one of our sales consultants now to setup an appointment.</p>\r\n<h4>Vehicle Options</h4>\r\n<p>Rear Wheel Drive,Manual Steering,Front Disc/Rear Drum Brakes,Wheel Covers,Steel Wheels,Tires - Front All-Season,Tires - Rear Performance,Intermittent Wipers,Auxiliary Audio Input,Cloth Seats,Bucket Seats,Leather Steering Wheel,Keyless Entry,Power Door Locks,Rear Defrost,Remote Trunk Release,Power Outlet,Passenger Vanity Mirror,Traction Control,Stability Control,ABS,Passenger Air Bag Sensor,Front Head Air Bag,Tire Pressure Monitor. </p>', '$11,395', '2009', 'smart', 'fortwo', 'Pure', '0-Cylinder 1.0', 'Gasoline', 'Deep Black', 'Call for color', '26372', '260723B', 'Coupe', 'Used', 'Used Cars for sale', 'Manual', 'WMEEJ31X69K238342', 'kxFEgiBSMgQ'),
(49, 'Concept Car #3', '13437231428-Peugeot-EX1-jpg-080505-jpg_054826.jpg', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_type2`
--

DROP TABLE IF EXISTS `#__joomd_type2`;
CREATE TABLE `#__joomd_type2` (
  `itemid` int(11) NOT NULL,
  `field_29` varchar(255) NOT NULL,
  `field_30` text NOT NULL,
  `field_31` varchar(255) NOT NULL,
  PRIMARY KEY (`itemid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_type2`
--

INSERT INTO `#__joomd_type2` (`itemid`, `field_29`, `field_30`, `field_31`) VALUES
(23, '1336571578reci.jpg', 'Complete Recipes Solution for Joomla! It is a standalone Recipes Manager component to build your Recipes portals.\r\nRecipes Manager Component: Frontend\r\n\r\n    Recipe detail page\r\n    Fields for Preparation time, Cook time and Ready In\r\n    Button to “Save” the recipe\r\n    No. of peoples who have saved this recipe\r\n    Add your Youtube video by simply inserting the youtube link into the given field from the backend\r\n    Descriptions for Ingredients for US and Metric\r\n    Calculator option in the frontend for conversion from US to Metric\r\n    Other descriptions\r\n\r\nAjax Search Module\r\n\r\n    Simply enter the keywords and it will instantly return the recipes with the entered keywords\r\n\r\nReviews and Rating system\r\n\r\n    User reviews\r\n    Ajax rating system\r\n    Overall average of all reviews and ratings for a given recipe\r\n\r\nWhat user can do from the frontend\r\n\r\n    My Recipes – list of all the recipes you have Saved and Submitted\r\n    Add your recipes\r\n    Update your recipes\r\n    Delete your recipes\r\n    My Saved Recipes module\r\n\r\nBackend Control Panel\r\n\r\n    Manage all the recipes\r\n    Add Recipes\r\n    Update Recipes\r\n    Delete Recipes\r\n    Add images\r\n    Add videos\r\n    Category Manager for recipes -Add categories and sub-categories\r\n    Manage Reviews\r\n    Approve/Disapprove reviews\r\n\r\nRecipes Manager Modules\r\n\r\n    Latest Recipes with their links and a thumbnail\r\n    Latest Recipes’ photos with links to the respective recipes’ pages\r\n    Recipes’ photos slider\r\n    Featured Recipes with thumbnails and links to the detail pages\r\n    Featured photos from the recipes\r\n    My Saved Recipes – users can save recipes in their control panel after they register and log in\r\n    Popular Recipes as per the hits they generate', 'Recipes Manager Component For Joomlä'),
(24, '1336571435obituary-Manager.jpg', 'The Complete Obituary Solution for Joomla! It is a standalone Obituary \r\nManager component to build your Obituary Listing portals. List \r\nobituaries with videos, audios, candles and slideshow, let others write \r\ndown their condolences and allow users to manage their own obituaries \r\nfrom frontend.\r\nFeatures and Functionality:\r\n\r\n    Control Panel\r\n    Obituary Manager\r\n    Condolences Manager\r\n    Configuration Manager\r\n    Front End Control Panel to Manage User Comments\r\n    Option to Approve Comments submitted by Users\r\n    Share obituaries on Facebook, Twitter, Linkedin\r\n    Submit Comment for Obituary with Captcha Spam Security\r\n    Print Obituaries from frontend\r\n    Add Audio/Video for the Obituary\r\n    Sign Guestbook for Obituary in the frontend\r\n\r\nObituary Manager Modules:\r\n\r\n    Obituary Condolences Module\r\n    Obituary Alphabet Search Module\r\n    Obituary Search Module\r\n    Obituary Listing Module\r\nThe Complete Obituary Solution for Joomla! It is a standalone Obituary \r\nManager component to build your Obituary Listing portals. List \r\nobituaries with videos, audios, candles and slideshow, let others write \r\ndown their condolences and allow users to manage their own obituaries \r\nfrom frontend.', 'Obituary Listing Component For Joomla'),
(25, '1336571218coupon.jpg', 'The Complete Coupon solution for Joomla! It is a standalone Coupon \r\nComponent to build your Deals, Vouchers and Discounts websites.\r\nFeatures and Functionality:\r\n\r\n    Store Manager\r\n    Category Manager\r\n    Coupon Manager\r\n    Frontend Control panel for users to Add and Manage Coupons\r\n    Featured Coupons Manager\r\n    User Manager\r\n    Like/Dislike buttons\r\n    Report an Error button\r\n    Add to Print and Printer box\r\n    Rate Coupons\r\n    Store Badge with each coupon\r\n    Coupon Type on the top right of each coupon\r\n\r\nCoupon Manager Modules:\r\n\r\n    Latest Coupons Module\r\n    Most Printed Coupons Module\r\n    Featured Coupons Module\r\n    Most Liked Coupons Module\r\n    Most Rated Coupons Module\r\n    Top Stores Module\r\n    Coupon Search Module\r\n    Coupon by Category Module\r\n    Add to Print and Printer box\r\n    Featured Category Module\r\n    Featured Store Module\r\n\r\nThe Complete Coupon solution for Joomla! It is a standalone Coupon \r\nComponent to build your Deals, Vouchers and Discounts websites.', 'Coupon Manager Component For Joomla'),
(26, '1336571746joomd.jpg', 'Feature-packed, Simple and Flexible Directory for Joomla.\r\nFeatures and Functionality:\r\n\r\n    Entry Manager\r\n    Category Manager\r\n    Custom Fields Manager – Ability to Add, Edit and Delete custom \r\nfields\r\n    Various types to choose from for a custom field: Image, PDF, Doc, \r\nExcel, Zip, Drop Down, Radio button, WYSIWIG Editor, Textfield, \r\nTextarea, URL, Email\r\n    Option to make a field as Required\r\n    Option to Include custom fields in Search\r\n    Add CSS Class in a custom field\r\n    Option to Display Custom field in Quick View/ Detailed View\r\n    Frontend Control panel for users to Add and Manage entries\r\n    Latest Entries Module\r\n    Search Module\r\n    Option to choose if User registration is required to add entries and\r\n manage those entries (As obvious, only registered users can manage \r\ntheir entries)\r\n    Option to Add Captcha in the Entry Submission form\r\n    Option to display entries to Registered users only and making user \r\nregistration as required for guest users to view detailed entries.\r\n    Set emails for Notification to one or more administrators.\r\n    Option to Approve entries from the backend or set it to approve \r\nautomatically\r\n    Multi-Language Support\r\n    Simple and Intuitive control panel\r\n\r\nHow to make the most of JoomD?\r\n\r\nWith its features and nice-looking UI, it’s quite easy to build \r\ndirectories. Option to apply restricted access for Detail page of an \r\nentry. Add Captcha in user-submission forms. Display custom fields in \r\nsearch page and search results page. Create custom fields from variety \r\nof field types. Optional control panel for users to manage their \r\nentries. Option to approve entries from the backend.\r\nWith JoomD v2.3, you can do the following:\r\n\r\n    Entry listings\r\n    Files Listings\r\n    Download Manager with Restricted Access\r\n    Event listings\r\n    Business directory\r\n    Course Listing directory\r\n    Blogs\r\n    Forms\r\n    User-generated contents', 'JoomD Component For Joomla');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_types`
--

DROP TABLE IF EXISTS `#__joomd_types`;
CREATE TABLE `#__joomd_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `descr` text NOT NULL,
  `appid` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `access` tinyint(1) NOT NULL,
  `language` char(7) NOT NULL,
  `config` text NOT NULL,
  `acl` text NOT NULL,
  `listconfig` text NOT NULL,
  `detailconfig` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `#__joomd_types`
--

INSERT INTO `#__joomd_types` (`id`, `name`, `alias`, `descr`, `appid`, `ordering`, `published`, `access`, `language`, `config`, `acl`, `listconfig`, `detailconfig`) VALUES
(2, 'JoomD Blog Listing', 'joomd-blog', 'Blogs', 7, 2, 1, 1, '*', '{"template":"1","notify":"1","moderate":"0","list":"1","detail":"1","meta":"4","publishing":"4"}', '{"addaccess":["6","7","2","3","4","5","10","12","8"],"editaccess":["6","7","2","3","4","5","10","12","8"],"editall":["6","7","4","5","8"],"stateaccess":["6","7","5","8"],"stateall":["6","7","5","8"],"deleteaccess":["6","7","8"],"deleteall":["6","7","8"],"featureaccess":["6","7","3","4","5","8"],"featureall":["6","7","4","5","8"]}', '{"header":"0","more":"1","rss":"1","add":"1","contact":"1","report":"1","save":"1","hits":"1","created":"1","author":"1","modified":"1","modified_by":"1","print":"1","email":"1"}', '{"add":"0","contact":"1","report":"1","save":"1","hits":"1","created":"1","author":"1","modified":"1","modified_by":"1","print":"1","email":"1"}'),
(1, 'Car Listing', 'car-listing', '', 7, 6, 1, 1, '*', '{"template":"1","notify":"1","moderate":"0","list":"1","detail":"1","meta":"2","publishing":"2"}', '{"addaccess":["1","6","7","2","3","4","5","10","12","8"],"editaccess":["6","7","2","3","4","5","10","12","8"],"editall":["6","7","4","5","8"],"stateaccess":["6","7","5","8"],"stateall":["6","7","5","8"],"deleteaccess":["6","7","8"],"deleteall":["6","7","8"],"featureaccess":["6","7","3","4","5","8"],"featureall":["6","7","4","5","8"]}', '{"header":"0","more":"1","rss":"1","add":"1","contact":"0","report":"0","save":"0","hits":"0","created":"0","author":"0","modified":"0","modified_by":"0","print":"1","email":"1"}', '{"add":"1","contact":"1","report":"1","save":"1","hits":"1","created":"1","author":"1","modified":"0","modified_by":"0","print":"1","email":"1"}');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_user_item`
--

DROP TABLE IF EXISTS `#__joomd_user_item`;
CREATE TABLE `#__joomd_user_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `hits` int(10) unsigned NOT NULL,
  `hit_date` datetime NOT NULL,
  `save` tinyint(1) NOT NULL,
  `report` datetime NOT NULL,
  `contact` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `#__joomd_user_item`
--

INSERT INTO `#__joomd_user_item` (`id`, `userid`, `itemid`, `hits`, `hit_date`, `save`, `report`, `contact`, `ip`) VALUES
(1, 65, 23, 1, '2012-04-04 00:00:00', 1, '2012-04-13 00:00:00', '2012-04-12 00:00:00', ''),
(2, 42, 23, 1, '2012-05-17 13:41:57', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(3, 42, 36, 0, '0000-00-00 00:00:00', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(4, 0, 45, 1, '2012-05-17 05:04:15', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '127.0.0.1'),
(5, 0, 24, 1, '2012-05-17 05:04:15', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '127.0.0.1'),
(6, 0, 26, 1, '2012-05-17 05:30:27', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '127.0.0.1'),
(7, 0, 45, 1, '2012-05-17 05:30:30', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '127.0.0.1'),
(8, 0, 45, 1, '2012-05-17 05:30:35', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '127.0.0.1'),
(9, 42, 45, 1, '2012-05-17 13:07:44', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', ''),
(10, 42, 45, 1, '2012-05-17 13:09:41', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');
