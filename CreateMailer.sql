CREATE TABLE `maillists` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

CREATE TABLE `mailmessages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `subject` varchar(45) NOT NULL,
  `sender` varchar(45) NOT NULL,
  `numbersent` int(10) unsigned default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `lastsend` datetime default NULL,
  `htmltext` text,
  `plaintext` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

CREATE TABLE `mailqueue` (
  `inx` int(10) unsigned NOT NULL auto_increment,
  `messageid` int(10) unsigned default NULL,
  `list` varchar(45) NOT NULL,
  `listid` int(10) unsigned default NULL,
  `queuetime` datetime default NULL,
  `lastrow` int(10) unsigned default NULL,
  `subject` varchar(45) default NULL,
  `html` text,
  `plain` text,
  `filter` varchar(45) default NULL,
  `status` varchar(8) default NULL,
  `totalsent` int(10) unsigned default NULL,
  PRIMARY KEY  (`inx`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

CREATE TABLE `mailsystem` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `nexttable` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `mailusers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `level` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

CREATE TABLE `templates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(45) default NULL,
  `htmlText` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

