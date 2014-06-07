-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 07. Jun 2014 um 15:43
-- Server Version: 5.5.37
-- PHP-Version: 5.4.4-14+deb7u10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `nopaste`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attplans`
--

CREATE TABLE IF NOT EXISTS `attplans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(6) NOT NULL DEFAULT '',
  `adminkey` varchar(6) NOT NULL DEFAULT '',
  `time` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `notes` text NOT NULL,
  `server` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=847837 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attplans_actions`
--

CREATE TABLE IF NOT EXISTS `attplans_actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attplan_id` int(10) unsigned NOT NULL DEFAULT '0',
  `typ` int(11) NOT NULL DEFAULT '0',
  `from` varchar(11) NOT NULL DEFAULT '',
  `to` varchar(11) NOT NULL DEFAULT '',
  `runtime` int(10) unsigned NOT NULL DEFAULT '0',
  `senddate` int(10) unsigned NOT NULL DEFAULT '0',
  `arrive` bigint(20) unsigned NOT NULL DEFAULT '0',
  `note` text NOT NULL,
  `spear` int(11) NOT NULL DEFAULT '0',
  `sword` int(11) NOT NULL DEFAULT '0',
  `axe` int(11) NOT NULL DEFAULT '0',
  `archer` int(11) NOT NULL DEFAULT '0',
  `spy` int(11) NOT NULL DEFAULT '0',
  `light` int(11) NOT NULL DEFAULT '0',
  `marcher` int(11) NOT NULL DEFAULT '0',
  `heavy` int(11) NOT NULL DEFAULT '0',
  `ram` int(11) NOT NULL DEFAULT '0',
  `catapult` int(11) NOT NULL DEFAULT '0',
  `knight` int(11) NOT NULL DEFAULT '0',
  `priest` int(11) NOT NULL DEFAULT '0',
  `snob` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `attplan_id` (`attplan_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3720680 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `farmmanagers`
--

CREATE TABLE IF NOT EXISTS `farmmanagers` (
  `id` varchar(10) NOT NULL DEFAULT '',
  `server` varchar(10) NOT NULL DEFAULT '',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `time` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `farms`
--

CREATE TABLE IF NOT EXISTS `farms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `saveid` varchar(10) NOT NULL DEFAULT '',
  `latest_report_id` int(10) unsigned NOT NULL,
  `time` bigint(20) unsigned NOT NULL DEFAULT '0',
  `wood` int(10) unsigned NOT NULL DEFAULT '0',
  `loam` int(10) unsigned NOT NULL DEFAULT '0',
  `iron` int(10) unsigned NOT NULL DEFAULT '0',
  `bonus` varchar(10) NOT NULL DEFAULT 'none',
  `av_name` varchar(30) NOT NULL DEFAULT '',
  `av_coords` varchar(9) NOT NULL DEFAULT '',
  `v_id` int(10) unsigned NOT NULL DEFAULT '0',
  `v_name` varchar(30) NOT NULL DEFAULT '',
  `v_coords` varchar(9) NOT NULL DEFAULT '',
  `b_main` tinyint(3) unsigned DEFAULT '1',
  `b_barracks` tinyint(3) unsigned DEFAULT '0',
  `b_stable` tinyint(3) unsigned DEFAULT '0',
  `b_garage` tinyint(3) unsigned DEFAULT '0',
  `b_snob` tinyint(3) unsigned DEFAULT '0',
  `b_smith` tinyint(3) unsigned DEFAULT '0',
  `b_place` tinyint(3) unsigned DEFAULT '1',
  `b_market` tinyint(3) unsigned DEFAULT '0',
  `b_wood` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `b_loam` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `b_iron` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `b_farm` tinyint(3) unsigned DEFAULT '1',
  `b_wall` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `b_storage` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `b_hide` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `farmable` int(10) unsigned NOT NULL DEFAULT '0',
  `note` varchar(100) NOT NULL DEFAULT '',
  `farmed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `performance` double DEFAULT NULL,
  `performance_updates` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `av_name` (`av_name`),
  KEY `saveid` (`saveid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1492145 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `farm_reports`
--

CREATE TABLE IF NOT EXISTS `farm_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fm_id` varchar(10) NOT NULL,
  `time` int(11) NOT NULL,
  `wood` int(11) NOT NULL,
  `loam` int(11) NOT NULL,
  `iron` int(11) NOT NULL,
  `av_id` int(11) NOT NULL,
  `av_name` varchar(30) NOT NULL,
  `av_coords` varchar(9) NOT NULL,
  `b_wood` int(11) NOT NULL,
  `b_loam` int(11) NOT NULL,
  `b_iron` int(11) NOT NULL,
  `b_wall` int(11) NOT NULL,
  `b_storage` int(11) NOT NULL,
  `b_hide` int(11) NOT NULL,
  `farmable` int(11) NOT NULL,
  `booty` int(11) NOT NULL,
  `booty_expected` int(11) NOT NULL,
  `raw_report` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fm_id` (`fm_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9488 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
