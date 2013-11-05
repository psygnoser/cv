# SQL Manager 2010 for MySQL 4.5.0.9
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : cv


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE `cv`
    CHARACTER SET 'utf8'
    COLLATE 'utf8_general_ci';

USE `cv`;

#
# Structure for the `sections` table : 
#

DROP TABLE IF EXISTS `sections`;

CREATE TABLE `sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

#
# Structure for the `fieldsets` table : 
#

DROP TABLE IF EXISTS `fieldsets`;

CREATE TABLE `fieldsets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `section_id` int(11) unsigned NOT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `fieldsets_fk` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

#
# Structure for the `fields` table : 
#

DROP TABLE IF EXISTS `fields`;

CREATE TABLE `fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `data` text,
  `fieldset_id` int(11) unsigned NOT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `fieldset_id` (`fieldset_id`),
  CONSTRAINT `fields_fk` FOREIGN KEY (`fieldset_id`) REFERENCES `fieldsets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8;

#
# Structure for the `fields_types` table : 
#

DROP TABLE IF EXISTS `fields_types`;

CREATE TABLE `fields_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#
# Data for the `sections` table  (LIMIT 0,500)
#

INSERT INTO `sections` (`id`, `name`, `position`) VALUES 
  (1,'prva',2),
  (2,'druga',1),
  (3,'tretja',3),
  (4,'četrta',4),
  (5,'peta',0);
COMMIT;

#
# Data for the `fieldsets` table  (LIMIT 0,500)
#

INSERT INTO `fieldsets` (`id`, `name`, `section_id`, `position`) VALUES 
  (1,'Osebni podatki',1,0),
  (2,'... Znanje jezikov',1,2),
  (3,'THE FIRST ONE EVAR [sic]',2,1),
  (4,'Kr-Pretekla zaposlitev 2',2,0),
  (5,'Pretekla zaposlitev 3 ?',2,3),
  (55,'dfklčighldičrjfg...',5,1),
  (58,'...',5,2),
  (60,'Kompetence',1,3);
COMMIT;

#
# Data for the `fields` table  (LIMIT 0,500)
#

INSERT INTO `fields` (`id`, `name`, `data`, `fieldset_id`, `position`) VALUES 
  (1,'Naslov','666 434 34\ndfgdfg\ndfgdfg',1,1),
  (2,'Priimek','sfsdf sd 34 54 45 343 34 žžsdž\n\n\n\nsd\n\n\nsdvsdv\nsdfsf',1,4),
  (3,'Ime','dfgd 343 4 s f s',1,2),
  (4,'Angleško','Super...\n',2,4),
  (5,'Srbo/hrvaško','Precej dobro razumem<br>\n<br>\na malo <b>slabše</b>  govorim ...',2,6),
  (6,'Ime firme','Voljatel',3,4),
  (7,'Čas zaposlitve','april 2005 - avgust 2008',3,3),
  (8,'Ime firme','Tušmobil',4,2),
  (9,'Čas zaposlitve','Avgust 2008 - Maj 2010',4,1),
  (17,'sdfsf','sdfsdf',1,6),
  (18,'Kaj je to...','NEKAJ PAČ <em>JE</em>',1,8),
  (20,'sdfsd...','sdfij sld jfsdlč jfžsd\n f\nsd\nf\nsd\n f\nsd\nf...',1,11),
  (21,'Te je volja?','Kakopak! :)\n',3,5),
  (46,'54646...','fghfgh\n',2,3),
  (49,'NOVO','POljE\n...',1,3),
  (50,'djxufjf','Hdufjjdjd',2,7),
  (51,'Nek podatek','To je pa res kul :D',4,0),
  (52,'AVATAR','<img src=\"https://lh5.googleusercontent.com/-Mkp0XrCsjT8/AAAAAAAAAAI/AAAAAAAAAgk/QRE8w8riAVY/s200-c-k/photo.jpg\">',1,0),
  (53,'sddfksn','...dfgdfb',3,0),
  (54,'neki','bla',5,3),
  (57,'das','...',1,5),
  (58,'sdkh flsdk f...','dgdfg...',5,2),
  (59,'lisj ljsdlf SDFs...','54654645645',5,0),
  (60,'MEdo...','Pooh...',5,1),
  (62,'Nekaj','<h1>hello</h1>',3,1),
  (65,'..sdfsdfsdf.','sdfsdfs sdf sd  fsd fsd fsd  43 r34t...',2,2),
  (66,'HOOLAHOOP','dfgdfg...',2,5),
  (78,'...','fgdrggg...',2,8),
  (82,'34.','...',1,12),
  (108,'...','sdfsdf...',55,0),
  (109,'Programiranje','PHP, JS, AS3, XSLT, MYSQL, ...',60,0);
COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;