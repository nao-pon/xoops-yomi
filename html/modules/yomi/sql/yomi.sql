# phpMyAdmin MySQL-Dump
# version 2.4.0
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Apr 30, 2003 at 01:17 PM
# Server version: 3.23.54
# PHP Version: 4.2.4-dev
# Database : `xoops2`
# --------------------------------------------------------

#
# Table structure for table `yomi_key`
#

CREATE TABLE `yomi_key` (
  `word` varchar(50) default NULL,
  `time` int(10) unsigned default NULL,
  `ip` varchar(15) default NULL,
  KEY `word` (`word`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `yomi_log`
#

CREATE TABLE `yomi_log` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `mark` char(3) default NULL,
  `last_time` varchar(21) default NULL,
  `passwd` varchar(255) default NULL,
  `message` text,
  `comment` text,
  `name` varchar(255) default NULL,
  `mail` varchar(255) default NULL,
  `category` varchar(255) default NULL,
  `stamp` int(10) unsigned default NULL,
  `banner` varchar(255) default NULL,
  `renew` tinyint(3) unsigned default NULL,
  `ip` varchar(15) default NULL,
  `keywd` varchar(255) default NULL,
  `build_time` int(10) unsigned default NULL,
  `uid` int(5) unsigned NOT NULL default '0',
  `rating` double(6,4) NOT NULL default '0.0000',
  `votes` int(11) unsigned NOT NULL default '0',
  `comments` int(11) unsigned NOT NULL default '0',
  `count` int(11) unsigned NOT NULL default '0',
  `count_rev` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `mark` (`mark`),
  KEY `category` (`category`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `yomi_rank`
#

CREATE TABLE `yomi_rank` (
  `id` int(10) unsigned default NULL,
  `time` int(10) unsigned default NULL,
  `ip` varchar(15) default NULL,
  KEY `id` (`id`),
  KEY `time` (`time`)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table `yomi_rev`
#

CREATE TABLE `yomi_rev` (
  `id` int(10) unsigned default NULL,
  `time` int(10) unsigned default NULL,
  `ip` varchar(15) default NULL,
  KEY `id` (`id`),
  KEY `time` (`time`)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table `yomi_votedata`
#

CREATE TABLE `yomi_votedata` (
  ratingid int(11) unsigned NOT NULL auto_increment,
  lid int(11) unsigned NOT NULL default '0',
  ratinguser int(11) unsigned NOT NULL default '0',
  rating tinyint(3) unsigned NOT NULL default '0',
  ratinghostname varchar(60) NOT NULL default '',
  ratingtimestamp int(10) NOT NULL default '0',
  PRIMARY KEY  (ratingid),
  KEY ratinguser (ratinguser),
  KEY ratinghostname (ratinghostname)
) TYPE=MyISAM;

