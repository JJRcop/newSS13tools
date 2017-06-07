-- Create syntax for TABLE 'flagged_auth'
CREATE TABLE `flagged_auth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ckey` varchar(32) DEFAULT NULL,
  `remote_addr` int(10) DEFAULT NULL,
  `db_addr` int(10) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

-- Create syntax for TABLE 'hidden_poll_results'
CREATE TABLE `hidden_poll_results` (
  `pollid` int(11) DEFAULT NULL,
  `replyid` int(11) DEFAULT NULL,
  `hiddenby` varchar(32) DEFAULT NULL,
  `hidden` timestamp NULL DEFAULT NULL,
  `hide` tinyint(1) DEFAULT NULL,
  UNIQUE KEY `pollid` (`pollid`,`replyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'monthly_stats'
CREATE TABLE `monthly_stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rounds` int(11) DEFAULT NULL,
  `var_name` varchar(32) DEFAULT NULL,
  `details` longtext,
  `var_value` int(16) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `var_name` (`var_name`,`month`,`year`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'round_comments'
CREATE TABLE `round_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `round` int(11) DEFAULT NULL,
  `text` longtext,
  `texthash` varchar(64) DEFAULT NULL,
  `author` varchar(32) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `flagged` enum('P','A','R','H') NOT NULL DEFAULT 'P',
  `reporter` varchar(32) DEFAULT NULL,
  `reported_time` timestamp NULL DEFAULT NULL,
  `flag_change` timestamp NULL DEFAULT NULL,
  `flag_changer` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `texthash` (`texthash`,`round`,`author`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'tracked_months'
CREATE TABLE `tracked_months` (
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `stats` int(11) DEFAULT NULL,
  `rounds` int(11) DEFAULT NULL,
  `firstround` int(11) DEFAULT NULL,
  `lastround` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `year` (`year`,`month`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
