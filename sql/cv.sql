SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
CREATE DATABASE IF NOT EXISTS `cv` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `cv`;

DROP TABLE IF EXISTS `fields`;
CREATE TABLE IF NOT EXISTS `fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `data` text,
  `fieldset_id` int(11) unsigned NOT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `fieldset_id` (`fieldset_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=159 ;

INSERT INTO `fields` (`id`, `name`, `data`, `fieldset_id`, `position`) VALUES
(1, 'Naslov', 'Iz tega planeta\n', 1, 3),
(2, 'Priimek', 'sfsdf sd 34 54 45 343 34 žžsdž\n\n\n\nsd\n\n\nsdvsdv\nsdfsf', 1, 2),
(3, 'Ime', 'dfgd 343 4 s f s dgdfgdfg jhkjhkhjk\n', 1, 1),
(4, 'Angleško', 'Super...\n', 2, 0),
(5, 'Srbo/hrvaško', 'Precej dobro razumem<br>\n<br>\na malo <b>slabše</b>  govorim ...', 2, 3),
(6, 'Ime firme', 'Voljatel', 3, 3),
(7, 'Čas zaposlitve', 'april 2005 - avgust 2008', 3, 0),
(18, 'Kaj je to...', 'NEKAJ PAČ <em>JE</em>', 1, 8),
(20, 'sdfsd...', 'sdfij sld jfsdlč jfžsd\n f\nsd\nf\nsd\n f\nsd\nf...', 1, 9),
(21, 'Te je volja?', 'Kakopak! :)\n', 3, 4),
(46, '54646...', 'fghfgh\n', 2, 2),
(49, 'NOVO', 'POljE\n...', 1, 5),
(52, 'AVATAR', '<img src="https://lh5.googleusercontent.com/-Mkp0XrCsjT8/AAAAAAAAAAI/AAAAAAAAAgk/QRE8w8riAVY/s200-c-k/photo.jpg">', 1, 0),
(53, 'sddfksn', '...dfgdfb', 3, 1),
(54, 'neki', 'bla', 5, 2),
(58, 'sdkh flsdk f...', 'dgdfg...', 5, 1),
(59, 'lisj ljsdlf SDFs...', '54654645645', 5, 3),
(60, 'MEdo...', 'Pooh...', 5, 0),
(62, 'Nekaj', '<h1>hello</h1>', 3, 2),
(65, '..sdfsdfsdf.', 'sdfsdfs sdf sd  fsd fsd fsd  43 r34t...', 2, 4),
(66, 'HOOLAHOOP', 'dfgdfg...', 2, 1),
(82, '2343564574', '546546546', 1, 10),
(108, '...', 'sdfsdf...', 55, 0),
(109, 'Programiranje', 'PHP, JS, AS3, XSLT, MYSQL, ...', 60, 0),
(110, 'sdfsfd...', '6546345\n', 63, 0),
(111, '4564', 'dfgdfgdfg\n', 63, 1),
(113, 'HOLLY...', 'ŠĐ\n...', 64, 0),
(118, '...', '...', 61, 0),
(126, 'KIRA tabelca', '<table>\n<tr><th>Ena</th><th>Dve</th></tr>\n<tr><th>1</th><th>A</th></tr>\n<tr><th>2</th><th>B</th></tr>\n<tr><th>3</th><th>C</th></tr>\n</table>', 60, 1),
(132, 'Mislim?', 'Torej sem... nekaj............', 1, 4),
(133, 'Ko bodo prašiči leteli', '... bodo tudi sloni :) fgh sdf s\n', 60, 3),
(136, '...gfhftg', 'dfg\n', 1, 7),
(137, '...', '...', 62, 1),
(138, '...', 'fdgdfg', 61, 2),
(139, '...', '...', 61, 1),
(141, 'Fax', '...', 72, 2),
(143, 'Izobrazba', 'Sredja šola', 72, 1),
(149, 'Prva zaposlitev...', 'GEM', 70, 0),
(150, '...', '...', 72, 0),
(151, 'ghjghj', 'ghjghj..', 62, 0),
(153, '...', '...', 74, 0),
(155, '...', '...', 76, 0),
(156, 'dfgchfg', 'Hello\n', 79, 0),
(157, '666', '654735...435345', 60, 2),
(158, '56756u...', '56567567...', 72, 3);

DROP TABLE IF EXISTS `fields_types`;
CREATE TABLE IF NOT EXISTS `fields_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `fieldsets`;
CREATE TABLE IF NOT EXISTS `fieldsets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `section_id` int(11) unsigned NOT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=80 ;

INSERT INTO `fieldsets` (`id`, `name`, `section_id`, `position`) VALUES
(1, 'Osebni pOdatki 666', 1, 1),
(2, 'Znanje jezikov', 1, 3),
(3, 'THE FIRST ONE EVAR [sic]', 2, 2),
(5, 'Pretekla zaposlitev 3 ?', 2, 1),
(55, 'dfklčighldičrjfg...', 5, 0),
(60, 'Kompetence', 1, 0),
(61, '2', 5, 4),
(62, '3', 5, 2),
(63, '4...', 5, 3),
(64, '1', 5, 1),
(70, '...', 4, 0),
(72, 'dsfsf...', 4, 1),
(74, 'Ena zadeva', 3, 1),
(76, '...', 5, 5),
(78, '...', 3, 0),
(79, '...', 1, 2);

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `position` tinyint(1) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

INSERT INTO `sections` (`id`, `name`, `position`, `user_id`) VALUES
(1, 'Glavn Sekšn !!!', 0, 1),
(2, 'LOS AMIGOS!', 3, 1),
(3, '3fg 666', 2, 1),
(4, 'Druga', 1, 1),
(5, 'peta', 4, 1);

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `pasw` varchar(127) NOT NULL,
  `salt` varchar(127) DEFAULT NULL,
  `hash` varchar(127) NOT NULL,
  `cc` varchar(127) DEFAULT NULL,
  `sc` varchar(127) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `users` (`id`, `email`, `pasw`, `salt`, `hash`, `cc`, `sc`) VALUES
(1, 'aaa@bbb.com', '3a95c1e12e4e3addc85e10ddca0e45cbf11391a2', '21268ae858f710e40f0a6e1dcdae900616a6821a', 'a85119daee05c761a8e94db7b482faa0a59f0d46', NULL, NULL);


ALTER TABLE `fields`
  ADD CONSTRAINT `fields_fk` FOREIGN KEY (`fieldset_id`) REFERENCES `fieldsets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `fieldsets`
  ADD CONSTRAINT `fieldsets_fk` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sections`
  ADD CONSTRAINT `sections_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
