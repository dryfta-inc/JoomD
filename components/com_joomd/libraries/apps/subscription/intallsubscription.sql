--
-- Table structure for table `#__joomd_orders`
--

CREATE TABLE IF NOT EXISTS `#__joomd_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `order_number` varchar(25) NOT NULL,
  `packid` int(11) NOT NULL,
  `payment_date` datetime NOT NULL,
  `recur_date` datetime NOT NULL,
  `payment_method` varchar(25) NOT NULL,
  `payment_price` float NOT NULL,
  `payment_currency` varchar(5) NOT NULL,
  `order_status` enum('P','p','c','e','r') NOT NULL,
  `txn_id` varchar(55) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`),
  UNIQUE KEY `order_number` (`order_number`),
  UNIQUE KEY `txn_id` (`txn_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_package`
--

CREATE TABLE IF NOT EXISTS `#__joomd_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `amount` float NOT NULL,
  `period` tinyint(3) NOT NULL,
  `unit` varchar(1) NOT NULL,
  `items` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `language` char(7) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `#__joomd_package`
--

INSERT INTO `#__joomd_package` (`id`, `name`, `amount`, `period`, `unit`, `items`, `published`, `ordering`, `created`, `created_by`, `language`, `params`) VALUES
(1, 'Free Trial for 15 Days', 0, 15, 'W', 50, 1, 1, '0000-00-00 00:00:00', 0, '*', '{"types":["1"],"cats":["28","33","29","34","30","35","31","32","37"],"featured":"1"}');

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_packagesconfig`
--

CREATE TABLE IF NOT EXISTS `#__joomd_packagesconfig` (
  `id` int(11) NOT NULL,
  `entryedit` tinyint(1) NOT NULL,
  `currency` varchar(5) NOT NULL,
  `sandbox` tinyint(1) NOT NULL,
  `paypal_email` varchar(255) NOT NULL,
  `grace_period` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_packagesconfig`
--

INSERT INTO `#__joomd_packagesconfig` (`id`, `entryedit`, `currency`, `sandbox`, `paypal_email`, `grace_period`) VALUES
(1, 1, 'USD', 1, 'you@yourdomain.com', 0);

-- --------------------------------------------------------

--
-- Table structure for table `#__joomd_pusers`
--

CREATE TABLE IF NOT EXISTS `#__joomd_pusers` (
  `userid` int(11) NOT NULL,
  `packid` int(11) NOT NULL,
  `credit` int(5) NOT NULL,
  `remaining` int(5) NOT NULL,
  `expiry` datetime NOT NULL,
  `free` enum('0','1') NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `#__joomd_pusers`
--

INSERT INTO `#__joomd_pusers` (`userid`, `packid`, `credit`, `remaining`, `expiry`, `free`) VALUES
(42, 1, 0, 0, '2012-08-31 00:00:00', '1');
