--
-- Table structure for table `#__joomd_newsletter`
--

DROP TABLE IF EXISTS `#__joomd_newsletter`;
CREATE TABLE `#__joomd_newsletter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` int(11) NOT NULL,
  `catid` int(11) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `typeid` (`typeid`,`catid`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_newstemp`
--

DROP TABLE IF EXISTS `#__joomd_newstemp`;
CREATE TABLE `#__joomd_newstemp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` int(11) NOT NULL,
  `catid` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `typeid` (`typeid`,`catid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci AUTO_INCREMENT=1 ;