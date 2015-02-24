--
-- Table structure for table `#__joomd_reviews`
--

DROP TABLE IF EXISTS `#__joomd_reviews`;
CREATE TABLE `#__joomd_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rate` tinyint(1) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `typeid` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rate` (`rate`,`typeid`,`itemid`,`created_by`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_rnrconfig`
--

DROP TABLE IF EXISTS `#__joomd_rnrconfig`;
CREATE TABLE `#__joomd_rnrconfig` (
  `id` int(11) NOT NULL DEFAULT '1',
  `comment_enable` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `comment_type` enum('1','2') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `comment_access` tinyint(1) NOT NULL,
  `moderate` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `omoderate` enum('0','1') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

--
-- Dumping data for table `#__joomd_rnrconfig`
--

INSERT INTO `#__joomd_rnrconfig` (`id`, `comment_enable`, `comment_type`, `comment_access`, `moderate`, `omoderate`) VALUES
(1, '1', '1', 1, '0', '0');