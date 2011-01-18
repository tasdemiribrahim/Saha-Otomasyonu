-- phpMyAdmin SQL Dump
-- version 3.1.3.1
-- http://www.phpmyadmin.net
--
-- Anamakine: localhost
-- Üretim Zamanı: 24 Kasım 2009 saat 10:43:56
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
-- Tablo yapısı: `birim`
--

CREATE TABLE IF NOT EXISTS `birim` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `birimTur` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `birim` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `birimKisaltma` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65536 ;


-- --------------------------------------------------------

--
-- Tablo yapısı: `borc`
--

CREATE TABLE IF NOT EXISTS `borc` (
  `hareketID` datetime NOT NULL,
  `konumID` smallint(5) unsigned NOT NULL,
  `alinan` mediumint(8) unsigned NOT NULL,
  `eklenen` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo yapısı: `depo`
--

CREATE TABLE IF NOT EXISTS `depo` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `kod` smallint(5) unsigned NOT NULL,
  `tanim` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `durum` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `eksiBakiyeUyari` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `eksiBakiyeIzin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `personel` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `kod` (`kod`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `dinamikbilgi`
--

CREATE TABLE IF NOT EXISTS `dinamikbilgi` (
  `ID` smallint(5) unsigned NOT NULL,
  `stokID` smallint(5) unsigned NOT NULL,
  `title` varchar(20) NOT NULL,
  `value` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo yapısı: `fiyat`
--

CREATE TABLE IF NOT EXISTS `fiyat` (
  `stokID` smallint(5) unsigned NOT NULL,
  `konumID` smallint(5) unsigned NOT NULL,
  `fiyat` mediumint(8) unsigned NOT NULL,
  `tarih` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo yapısı: `hareket`
--

CREATE TABLE IF NOT EXISTS `hareket` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `konumID` smallint(5) unsigned NOT NULL,
  `personelID` smallint(5) unsigned NOT NULL,
  `stokID` smallint(5) unsigned NOT NULL,
  `alinanMiktar` mediumint(8) unsigned NOT NULL,
  `iadeMiktar` mediumint(8) unsigned NOT NULL,
  `tarih` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `irsaliye`
--

CREATE TABLE IF NOT EXISTS `irsaliye` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `musteri` smallint(5) unsigned NOT NULL,
  `tarih` date NOT NULL,
  `teslimTarih` date NOT NULL,
  `tur` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `irsaliyeayar`
--

CREATE TABLE IF NOT EXISTS `irsaliyeayar` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `irsaliyedetay`
--

CREATE TABLE IF NOT EXISTS `irsaliyedetay` (
  `detayID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ID` smallint(5) unsigned NOT NULL,
  `stokID` smallint(5) unsigned NOT NULL,
  `depoID` smallint(5) unsigned NOT NULL,
  `miktar` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`detayID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `konum`
--

CREATE TABLE IF NOT EXISTS `konum` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `kod` smallint(5) unsigned DEFAULT NULL,
  `tanim` varchar(100) NOT NULL,
  `adres` varchar(300) DEFAULT NULL,
  `telefon` bigint(20) unsigned DEFAULT NULL,
  `vergiNo` mediumint(8) unsigned DEFAULT NULL,
  `vergiDaire` varchar(100) DEFAULT NULL,
  `tur` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `personel`
--

CREATE TABLE IF NOT EXISTS `personel` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `sifre` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tanim` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=232 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `personelgider`
--

CREATE TABLE IF NOT EXISTS `personelgider` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `personelID` smallint(5) unsigned NOT NULL,
  `miktar` mediumint(8) unsigned NOT NULL,
  `birim` smallint(5) unsigned NOT NULL,
  `tarih` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `access` int(10) unsigned DEFAULT NULL,
  `data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo yapısı: `siparis`
--

CREATE TABLE IF NOT EXISTS `siparis` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `konumID` smallint(5) unsigned NOT NULL,
  `depoID` smallint(5) unsigned NOT NULL,
  `personelID` smallint(5) unsigned NOT NULL,
  `stokID` smallint(5) unsigned NOT NULL,
  `miktar` mediumint(8) unsigned NOT NULL,
  `durum` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=39 ;


-- --------------------------------------------------------

--
-- Tablo yapısı: `stok`
--

CREATE TABLE IF NOT EXISTS `stok` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `kod` smallint(5) unsigned NOT NULL,
  `stokTur` tinyint(1) unsigned NOT NULL,
  `tanim1` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `tanim2` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `depoID` smallint(5) unsigned DEFAULT NULL,
  `fiyatBirim` smallint(5) unsigned DEFAULT NULL,
  `alisFiyat` mediumint(8) unsigned DEFAULT NULL,
  `alisKDV` tinyint(3) unsigned DEFAULT NULL,
  `satisFiyat` mediumint(8) unsigned DEFAULT NULL,
  `satisKDV` tinyint(3) unsigned DEFAULT NULL,
  `temelOlcuBirim` smallint(5) unsigned DEFAULT NULL,
  `en` mediumint(8) unsigned DEFAULT NULL,
  `boy` mediumint(8) unsigned DEFAULT NULL,
  `yukseklik` mediumint(8) unsigned DEFAULT NULL,
  `agirlik` mediumint(8) unsigned DEFAULT NULL,
  `barkod` bigint(20) unsigned DEFAULT NULL,
  `enBirim` smallint(5) unsigned DEFAULT NULL,
  `boyBirim` smallint(5) unsigned DEFAULT NULL,
  `yukseklikBirim` smallint(5) unsigned DEFAULT NULL,
  `agirlikBirim` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `kod` (`kod`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=181 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `stokbirimdonusum`
--

CREATE TABLE IF NOT EXISTS `stokbirimdonusum` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `stokID` smallint(5) unsigned NOT NULL,
  `temelBirimDeger` mediumint(8) unsigned NOT NULL,
  `ikinciBirim` smallint(5) unsigned NOT NULL,
  `ikinciBirimDeger` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=149 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `stokdepo`
--

CREATE TABLE IF NOT EXISTS `stokdepo` (
  `ID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `stokID` smallint(5) unsigned NOT NULL,
  `depoID` smallint(5) unsigned NOT NULL,
  `miktar` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=186 ;

-- --------------------------------------------------------

--
-- Tablo yapısı: `stokdosya`
--

CREATE TABLE IF NOT EXISTS `stokdosya` (
  `stokID` smallint(5) unsigned NOT NULL,
  `adres` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tablo yapısı: `stokresim`
--

CREATE TABLE IF NOT EXISTS `stokresim` (
  `stokID` smallint(5) unsigned NOT NULL,
  `adres` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

