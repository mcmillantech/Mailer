CREATE TABLE `maillists` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
   type tinyint(3) unsigned,
  `rtable` varchar(45) NOT NULL,
  `nSent` int(10) unsigned default NULL,
  `email` varchar(45) default NULL,
  `forename` varchar(45) default NULL,
  `surname` varchar(45) default NULL,
  `business` varchar(45) default NULL,
  `user1` varchar(45) default NULL,
  `user2` varchar(45) default NULL,
  `fcol` varchar(45) default NULL,
  `fop` varchar(6) default NULL,
  `fval` varchar(45) default NULL,
  `ky` varchar(45) default NULL,
  `lastsend` varchar(16) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `mailmessages` (
  `id` int(10) UNSIGNED NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `subject` varchar(45) NOT NULL,
  `sender` varchar(45) NOT NULL,
  `numbersent` int(10) UNSIGNED DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `lastsend` datetime DEFAULT NULL,
  `htmltext` text DEFAULT NULL,
  `template` int(10) UNSIGNED DEFAULT NULL,
  `attachment` varchar(45) DEFAULT NULL,
  `archive` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `mailqueue` (
  `inx` int(10) unsigned NOT NULL auto_increment,
  `messageid` int(10) unsigned default NULL,
  `list` varchar(45) default NULL,
  `listid` int(10) unsigned default NULL,
  `queuetime` datetime default NULL,
  `lastrow` int(10) unsigned default NULL,
  `subject` varchar(45) default NULL,
  `html` text,
  `plain` text,
  `filter` varchar(45) default NULL,
  `status` varchar(8) default NULL,
  `totalsent` int(10) unsigned default NULL,
  `attachment` varchar(127) DEFAULT NULL,
  `sender` varchar(45) DEFAULT NULL,
  PRIMARY KEY  (`inx`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `mailsystem` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nexttable` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `mailsystem` (`id`, `nexttable`) VALUES
(1, 1);

CREATE TABLE `mailusers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `level` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `templates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) default NULL,
  `htmlText` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `sentmessages` (
  `id` int(10) UNSIGNED NOT NULL auto_increment,
  `name` varchar(45) default NULL,
  `subject` varchar(45) default NULL,
  `sender` varchar(45) default NULL,
  `numbersent` int(10) UNSIGNED DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `lastsend` datetime DEFAULT NULL,
  `archive` tinyint(4) DEFAULT 0,
  `htmltext` text, 
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
