-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Anamakine: localhost
-- Üretim Zamanı: 26 Temmuz 2009 saat 18:52:43
-- Sunucu sürümü: 5.1.33
-- PHP Sürümü: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Veritabanı: `fso_ekmekci`
--

-- --------------------------------------------------------

--
-- Tablo yapısı: `siparis`
--

CREATE TABLE IF NOT EXISTS `siparis` (
  `ID` int(11) NOT NULL,
  `adres` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `tel` varchar(100) NOT NULL,
  `depoID` int(11) NOT NULL,
  `personelID` int(11) NOT NULL,
  `stokID` int(11) NOT NULL,
  `miktar` int(11) NOT NULL,
  `birim` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

