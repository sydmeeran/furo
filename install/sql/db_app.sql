-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 12, 2021 at 10:26 AM
-- Server version: 10.5.10-MariaDB-2
-- PHP Version: 7.4.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app`
--
CREATE DATABASE IF NOT EXISTS `app` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `app`;

-- --------------------------------------------------------

--
-- Table structure for table `addon`
--

DROP TABLE IF EXISTS `addon`;
CREATE TABLE IF NOT EXISTS `addon` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `image_url` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` enum('ACTIVE','DELETED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE',
  `del` char(0) GENERATED ALWAYS AS (if(`status` = 'ACTIVE','',NULL)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ukey` (`name`,`price`,`del`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `addon`
--

TRUNCATE TABLE `addon`;
--
-- Dumping data for table `addon`
--

INSERT INTO `addon` (`id`, `name`, `price`, `image_url`, `status`) VALUES
(2, 'Ser', '3.00', '', 'ACTIVE'),
(3, 'Sos pikantny', '0.00', '', 'ACTIVE'),
(4, 'Sos łagodny', '0.00', '', 'ACTIVE'),
(5, 'Szynka', '1.50', '', 'ACTIVE'),
(6, 'Kurczak', '0.00', '', 'ACTIVE'),
(7, 'Szynka', '3.50', '', 'ACTIVE'),
(17, 'Ananas', '1.53', '', 'ACTIVE'),
(20, 'Ananas', '2.50', '', 'ACTIVE'),
(21, 'Ananas', '3.50', '', 'ACTIVE'),
(22, 'Kiełbasa', '1.50', '', 'ACTIVE'),
(23, 'Baranina', '0.00', '', 'ACTIVE'),
(24, 'Kiełbasa', '3.50', '', 'ACTIVE'),
(25, 'Ser', '2.00', '', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `addon_group`
--

DROP TABLE IF EXISTS `addon_group`;
CREATE TABLE IF NOT EXISTS `addon_group` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `size` enum('S','M','L','XL','XXL','XXXL') NOT NULL DEFAULT 'S',
  `multiple` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('ACTIVE','DELETED') NOT NULL DEFAULT 'ACTIVE',
  `del` char(0) GENERATED ALWAYS AS (if(`status` = 'ACTIVE','',NULL)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uKey` (`name`,`size`,`del`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `addon_group`
--

TRUNCATE TABLE `addon_group`;
--
-- Dumping data for table `addon_group`
--

INSERT INTO `addon_group` (`id`, `name`, `size`, `multiple`, `required`, `status`) VALUES
(17, 'Sosy', 'S', 0, 1, 'ACTIVE'),
(18, 'Mięso', 'S', 0, 1, 'ACTIVE'),
(19, 'Dodatki', 'S', 1, 0, 'ACTIVE'),
(20, 'Sosy', 'M', 0, 1, 'ACTIVE'),
(22, 'Mięso', 'M', 0, 1, 'ACTIVE'),
(23, 'Dodatki', 'M', 1, 0, 'ACTIVE'),
(27, 'Dodatki', 'L', 1, 0, 'ACTIVE'),
(28, 'Dodatki', 'XL', 1, 0, 'ACTIVE'),
(31, 'Mięso', 'L', 0, 1, 'ACTIVE'),
(32, 'Mięso', 'XL', 0, 1, 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `addon_group_variant`
--

DROP TABLE IF EXISTS `addon_group_variant`;
CREATE TABLE IF NOT EXISTS `addon_group_variant` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `group_id` bigint(22) NOT NULL,
  `addon_id` bigint(22) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ukey` (`addon_id`,`group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `addon_group_variant`
--

TRUNCATE TABLE `addon_group_variant`;
--
-- Dumping data for table `addon_group_variant`
--

INSERT INTO `addon_group_variant` (`id`, `group_id`, `addon_id`) VALUES
(69, 27, 2),
(55, 17, 3),
(50, 20, 3),
(56, 17, 4),
(54, 20, 4),
(63, 19, 5),
(70, 18, 6),
(76, 31, 6),
(71, 22, 7),
(67, 27, 7),
(62, 19, 17),
(66, 23, 20),
(79, 28, 20),
(68, 27, 21),
(75, 18, 23),
(77, 31, 23),
(74, 22, 24),
(80, 28, 25);

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `addres_id` bigint(22) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(22) UNSIGNED NOT NULL,
  `firstname` varchar(250) NOT NULL DEFAULT '',
  `lastname` varchar(250) NOT NULL DEFAULT '',
  `country` char(2) NOT NULL DEFAULT 'PL',
  `region` varchar(250) NOT NULL DEFAULT '',
  `street` varchar(250) NOT NULL DEFAULT '',
  `district` varchar(250) NOT NULL DEFAULT '',
  `city` varchar(250) NOT NULL DEFAULT '',
  `zip` varchar(50) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(250) NOT NULL DEFAULT '',
  `lng` decimal(10,8) NOT NULL DEFAULT 0.00000000,
  `lat` decimal(10,8) NOT NULL DEFAULT 0.00000000,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`addres_id`),
  UNIQUE KEY `address_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `address`
--

TRUNCATE TABLE `address`;
-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iso` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso3` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phonecode` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=254 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `country`
--

TRUNCATE TABLE `country`;
--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `iso`, `name`, `iso3`, `numcode`, `phonecode`) VALUES
(1, 'AF', 'Afghanistan', 'AFG', 4, 93),
(2, 'AL', 'Albania', 'ALB', 8, 355),
(3, 'DZ', 'Algeria', 'DZA', 12, 213),
(4, 'AS', 'American Samoa', 'ASM', 16, 1684),
(5, 'AD', 'Andorra', 'AND', 20, 376),
(6, 'AO', 'Angola', 'AGO', 24, 244),
(7, 'AI', 'Anguilla', 'AIA', 660, 1264),
(8, 'AQ', 'Antarctica', NULL, NULL, 0),
(9, 'AG', 'Antigua and Barbuda', 'ATG', 28, 1268),
(10, 'AR', 'Argentina', 'ARG', 32, 54),
(11, 'AM', 'Armenia', 'ARM', 51, 374),
(12, 'AW', 'Aruba', 'ABW', 533, 297),
(13, 'AU', 'Australia', 'AUS', 36, 61),
(14, 'AT', 'Austria', 'AUT', 40, 43),
(15, 'AZ', 'Azerbaijan', 'AZE', 31, 994),
(16, 'BS', 'Bahamas', 'BHS', 44, 1242),
(17, 'BH', 'Bahrain', 'BHR', 48, 973),
(18, 'BD', 'Bangladesh', 'BGD', 50, 880),
(19, 'BB', 'Barbados', 'BRB', 52, 1246),
(20, 'BY', 'Belarus', 'BLR', 112, 375),
(21, 'BE', 'Belgium', 'BEL', 56, 32),
(22, 'BZ', 'Belize', 'BLZ', 84, 501),
(23, 'BJ', 'Benin', 'BEN', 204, 229),
(24, 'BM', 'Bermuda', 'BMU', 60, 1441),
(25, 'BT', 'Bhutan', 'BTN', 64, 975),
(26, 'BO', 'Bolivia', 'BOL', 68, 591),
(27, 'BA', 'Bosnia and Herzegovina', 'BIH', 70, 387),
(28, 'BW', 'Botswana', 'BWA', 72, 267),
(29, 'BV', 'Bouvet Island', NULL, NULL, 0),
(30, 'BR', 'Brazil', 'BRA', 76, 55),
(31, 'IO', 'British Indian Ocean Territory', NULL, NULL, 246),
(32, 'BN', 'Brunei Darussalam', 'BRN', 96, 673),
(33, 'BG', 'Bulgaria', 'BGR', 100, 359),
(34, 'BF', 'Burkina Faso', 'BFA', 854, 226),
(35, 'BI', 'Burundi', 'BDI', 108, 257),
(36, 'KH', 'Cambodia', 'KHM', 116, 855),
(37, 'CM', 'Cameroon', 'CMR', 120, 237),
(38, 'CA', 'Canada', 'CAN', 124, 1),
(39, 'CV', 'Cape Verde', 'CPV', 132, 238),
(40, 'KY', 'Cayman Islands', 'CYM', 136, 1345),
(41, 'CF', 'Central African Republic', 'CAF', 140, 236),
(42, 'TD', 'Chad', 'TCD', 148, 235),
(43, 'CL', 'Chile', 'CHL', 152, 56),
(44, 'CN', 'China', 'CHN', 156, 86),
(45, 'CX', 'Christmas Island', NULL, NULL, 61),
(46, 'CC', 'Cocos (Keeling) Islands', NULL, NULL, 672),
(47, 'CO', 'Colombia', 'COL', 170, 57),
(48, 'KM', 'Comoros', 'COM', 174, 269),
(49, 'CG', 'Congo', 'COG', 178, 242),
(50, 'CD', 'Congo, the Democratic Republic of the', 'COD', 180, 242),
(51, 'CK', 'Cook Islands', 'COK', 184, 682),
(52, 'CR', 'Costa Rica', 'CRI', 188, 506),
(53, 'CI', 'Cote D\'Ivoire', 'CIV', 384, 225),
(54, 'HR', 'Croatia', 'HRV', 191, 385),
(55, 'CU', 'Cuba', 'CUB', 192, 53),
(56, 'CY', 'Cyprus', 'CYP', 196, 357),
(57, 'CZ', 'Czech Republic', 'CZE', 203, 420),
(58, 'DK', 'Denmark', 'DNK', 208, 45),
(59, 'DJ', 'Djibouti', 'DJI', 262, 253),
(60, 'DM', 'Dominica', 'DMA', 212, 1767),
(61, 'DO', 'Dominican Republic', 'DOM', 214, 1809),
(62, 'EC', 'Ecuador', 'ECU', 218, 593),
(63, 'EG', 'Egypt', 'EGY', 818, 20),
(64, 'SV', 'El Salvador', 'SLV', 222, 503),
(65, 'GQ', 'Equatorial Guinea', 'GNQ', 226, 240),
(66, 'ER', 'Eritrea', 'ERI', 232, 291),
(67, 'EE', 'Estonia', 'EST', 233, 372),
(68, 'ET', 'Ethiopia', 'ETH', 231, 251),
(69, 'FK', 'Falkland Islands (Malvinas)', 'FLK', 238, 500),
(70, 'FO', 'Faroe Islands', 'FRO', 234, 298),
(71, 'FJ', 'Fiji', 'FJI', 242, 679),
(72, 'FI', 'Finland', 'FIN', 246, 358),
(73, 'FR', 'France', 'FRA', 250, 33),
(74, 'GF', 'French Guiana', 'GUF', 254, 594),
(75, 'PF', 'French Polynesia', 'PYF', 258, 689),
(76, 'TF', 'French Southern Territories', NULL, NULL, 0),
(77, 'GA', 'Gabon', 'GAB', 266, 241),
(78, 'GM', 'Gambia', 'GMB', 270, 220),
(79, 'GE', 'Georgia', 'GEO', 268, 995),
(80, 'DE', 'Germany', 'DEU', 276, 49),
(81, 'GH', 'Ghana', 'GHA', 288, 233),
(82, 'GI', 'Gibraltar', 'GIB', 292, 350),
(83, 'GR', 'Greece', 'GRC', 300, 30),
(84, 'GL', 'Greenland', 'GRL', 304, 299),
(85, 'GD', 'Grenada', 'GRD', 308, 1473),
(86, 'GP', 'Guadeloupe', 'GLP', 312, 590),
(87, 'GU', 'Guam', 'GUM', 316, 1671),
(88, 'GT', 'Guatemala', 'GTM', 320, 502),
(89, 'GN', 'Guinea', 'GIN', 324, 224),
(90, 'GW', 'Guinea-Bissau', 'GNB', 624, 245),
(91, 'GY', 'Guyana', 'GUY', 328, 592),
(92, 'HT', 'Haiti', 'HTI', 332, 509),
(93, 'HM', 'Heard Island and Mcdonald Islands', NULL, NULL, 0),
(94, 'VA', 'Holy See (Vatican City State)', 'VAT', 336, 39),
(95, 'HN', 'Honduras', 'HND', 340, 504),
(96, 'HK', 'Hong Kong', 'HKG', 344, 852),
(97, 'HU', 'Hungary', 'HUN', 348, 36),
(98, 'IS', 'Iceland', 'ISL', 352, 354),
(99, 'IN', 'India', 'IND', 356, 91),
(100, 'ID', 'Indonesia', 'IDN', 360, 62),
(101, 'IR', 'Iran, Islamic Republic of', 'IRN', 364, 98),
(102, 'IQ', 'Iraq', 'IRQ', 368, 964),
(103, 'IE', 'Ireland', 'IRL', 372, 353),
(104, 'IL', 'Israel', 'ISR', 376, 972),
(105, 'IT', 'Italy', 'ITA', 380, 39),
(106, 'JM', 'Jamaica', 'JAM', 388, 1876),
(107, 'JP', 'Japan', 'JPN', 392, 81),
(108, 'JO', 'Jordan', 'JOR', 400, 962),
(109, 'KZ', 'Kazakhstan', 'KAZ', 398, 7),
(110, 'KE', 'Kenya', 'KEN', 404, 254),
(111, 'KI', 'Kiribati', 'KIR', 296, 686),
(112, 'KP', 'Korea, Democratic People\'s Republic of', 'PRK', 408, 850),
(113, 'KR', 'Korea, Republic of', 'KOR', 410, 82),
(114, 'KW', 'Kuwait', 'KWT', 414, 965),
(115, 'KG', 'Kyrgyzstan', 'KGZ', 417, 996),
(116, 'LA', 'Lao People\'s Democratic Republic', 'LAO', 418, 856),
(117, 'LV', 'Latvia', 'LVA', 428, 371),
(118, 'LB', 'Lebanon', 'LBN', 422, 961),
(119, 'LS', 'Lesotho', 'LSO', 426, 266),
(120, 'LR', 'Liberia', 'LBR', 430, 231),
(121, 'LY', 'Libyan Arab Jamahiriya', 'LBY', 434, 218),
(122, 'LI', 'Liechtenstein', 'LIE', 438, 423),
(123, 'LT', 'Lithuania', 'LTU', 440, 370),
(124, 'LU', 'Luxembourg', 'LUX', 442, 352),
(125, 'MO', 'Macao', 'MAC', 446, 853),
(126, 'MK', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389),
(127, 'MG', 'Madagascar', 'MDG', 450, 261),
(128, 'MW', 'Malawi', 'MWI', 454, 265),
(129, 'MY', 'Malaysia', 'MYS', 458, 60),
(130, 'MV', 'Maldives', 'MDV', 462, 960),
(131, 'ML', 'Mali', 'MLI', 466, 223),
(132, 'MT', 'Malta', 'MLT', 470, 356),
(133, 'MH', 'Marshall Islands', 'MHL', 584, 692),
(134, 'MQ', 'Martinique', 'MTQ', 474, 596),
(135, 'MR', 'Mauritania', 'MRT', 478, 222),
(136, 'MU', 'Mauritius', 'MUS', 480, 230),
(137, 'YT', 'Mayotte', NULL, NULL, 269),
(138, 'MX', 'Mexico', 'MEX', 484, 52),
(139, 'FM', 'Micronesia, Federated States of', 'FSM', 583, 691),
(140, 'MD', 'Moldova, Republic of', 'MDA', 498, 373),
(141, 'MC', 'Monaco', 'MCO', 492, 377),
(142, 'MN', 'Mongolia', 'MNG', 496, 976),
(143, 'MS', 'Montserrat', 'MSR', 500, 1664),
(144, 'MA', 'Morocco', 'MAR', 504, 212),
(145, 'MZ', 'Mozambique', 'MOZ', 508, 258),
(146, 'MM', 'Myanmar', 'MMR', 104, 95),
(147, 'NA', 'Namibia', 'NAM', 516, 264),
(148, 'NR', 'Nauru', 'NRU', 520, 674),
(149, 'NP', 'Nepal', 'NPL', 524, 977),
(150, 'NL', 'Netherlands', 'NLD', 528, 31),
(151, 'AN', 'Netherlands Antilles', 'ANT', 530, 599),
(152, 'NC', 'New Caledonia', 'NCL', 540, 687),
(153, 'NZ', 'New Zealand', 'NZL', 554, 64),
(154, 'NI', 'Nicaragua', 'NIC', 558, 505),
(155, 'NE', 'Niger', 'NER', 562, 227),
(156, 'NG', 'Nigeria', 'NGA', 566, 234),
(157, 'NU', 'Niue', 'NIU', 570, 683),
(158, 'NF', 'Norfolk Island', 'NFK', 574, 672),
(159, 'MP', 'Northern Mariana Islands', 'MNP', 580, 1670),
(160, 'NO', 'Norway', 'NOR', 578, 47),
(161, 'OM', 'Oman', 'OMN', 512, 968),
(162, 'PK', 'Pakistan', 'PAK', 586, 92),
(163, 'PW', 'Palau', 'PLW', 585, 680),
(164, 'PS', 'Palestinian Territory, Occupied', NULL, NULL, 970),
(165, 'PA', 'Panama', 'PAN', 591, 507),
(166, 'PG', 'Papua New Guinea', 'PNG', 598, 675),
(167, 'PY', 'Paraguay', 'PRY', 600, 595),
(168, 'PE', 'Peru', 'PER', 604, 51),
(169, 'PH', 'Philippines', 'PHL', 608, 63),
(170, 'PN', 'Pitcairn', 'PCN', 612, 0),
(171, 'PL', 'Poland', 'POL', 616, 48),
(172, 'PT', 'Portugal', 'PRT', 620, 351),
(173, 'PR', 'Puerto Rico', 'PRI', 630, 1787),
(174, 'QA', 'Qatar', 'QAT', 634, 974),
(175, 'RE', 'Reunion', 'REU', 638, 262),
(176, 'RO', 'Romania', 'ROM', 642, 40),
(177, 'RU', 'Russian Federation', 'RUS', 643, 70),
(178, 'RW', 'Rwanda', 'RWA', 646, 250),
(179, 'SH', 'Saint Helena', 'SHN', 654, 290),
(180, 'KN', 'Saint Kitts and Nevis', 'KNA', 659, 1869),
(181, 'LC', 'Saint Lucia', 'LCA', 662, 1758),
(182, 'PM', 'Saint Pierre and Miquelon', 'SPM', 666, 508),
(183, 'VC', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784),
(184, 'WS', 'Samoa', 'WSM', 882, 684),
(185, 'SM', 'San Marino', 'SMR', 674, 378),
(186, 'ST', 'Sao Tome and Principe', 'STP', 678, 239),
(187, 'SA', 'Saudi Arabia', 'SAU', 682, 966),
(188, 'SN', 'Senegal', 'SEN', 686, 221),
(189, 'CS', 'Serbia and Montenegro', NULL, NULL, 381),
(190, 'SC', 'Seychelles', 'SYC', 690, 248),
(191, 'SL', 'Sierra Leone', 'SLE', 694, 232),
(192, 'SG', 'Singapore', 'SGP', 702, 65),
(193, 'SK', 'Slovakia', 'SVK', 703, 421),
(194, 'SI', 'Slovenia', 'SVN', 705, 386),
(195, 'SB', 'Solomon Islands', 'SLB', 90, 677),
(196, 'SO', 'Somalia', 'SOM', 706, 252),
(197, 'ZA', 'South Africa', 'ZAF', 710, 27),
(198, 'GS', 'South Georgia and the South Sandwich Islands', NULL, NULL, 0),
(199, 'ES', 'Spain', 'ESP', 724, 34),
(200, 'LK', 'Sri Lanka', 'LKA', 144, 94),
(201, 'SD', 'Sudan', 'SDN', 736, 249),
(202, 'SR', 'Suriname', 'SUR', 740, 597),
(203, 'SJ', 'Svalbard and Jan Mayen', 'SJM', 744, 47),
(204, 'SZ', 'Swaziland', 'SWZ', 748, 268),
(205, 'SE', 'Sweden', 'SWE', 752, 46),
(206, 'CH', 'Switzerland', 'CHE', 756, 41),
(207, 'SY', 'Syrian Arab Republic', 'SYR', 760, 963),
(208, 'TW', 'Taiwan, Province of China', 'TWN', 158, 886),
(209, 'TJ', 'Tajikistan', 'TJK', 762, 992),
(210, 'TZ', 'Tanzania, United Republic of', 'TZA', 834, 255),
(211, 'TH', 'Thailand', 'THA', 764, 66),
(212, 'TL', 'Timor-Leste', NULL, NULL, 670),
(213, 'TG', 'Togo', 'TGO', 768, 228),
(214, 'TK', 'Tokelau', 'TKL', 772, 690),
(215, 'TO', 'Tonga', 'TON', 776, 676),
(216, 'TT', 'Trinidad and Tobago', 'TTO', 780, 1868),
(217, 'TN', 'Tunisia', 'TUN', 788, 216),
(218, 'TR', 'Turkey', 'TUR', 792, 90),
(219, 'TM', 'Turkmenistan', 'TKM', 795, 7370),
(220, 'TC', 'Turks and Caicos Islands', 'TCA', 796, 1649),
(221, 'TV', 'Tuvalu', 'TUV', 798, 688),
(222, 'UG', 'Uganda', 'UGA', 800, 256),
(223, 'UA', 'Ukraine', 'UKR', 804, 380),
(224, 'AE', 'United Arab Emirates', 'ARE', 784, 971),
(225, 'GB', 'United Kingdom', 'GBR', 826, 44),
(226, 'US', 'United States', 'USA', 840, 1),
(227, 'UM', 'United States Minor Outlying Islands', NULL, NULL, 1),
(228, 'UY', 'Uruguay', 'URY', 858, 598),
(229, 'UZ', 'Uzbekistan', 'UZB', 860, 998),
(230, 'VU', 'Vanuatu', 'VUT', 548, 678),
(231, 'VE', 'Venezuela', 'VEN', 862, 58),
(232, 'VN', 'Viet Nam', 'VNM', 704, 84),
(233, 'VG', 'Virgin Islands, British', 'VGB', 92, 1284),
(234, 'VI', 'Virgin Islands, U.s.', 'VIR', 850, 1340),
(235, 'WF', 'Wallis and Futuna', 'WLF', 876, 681),
(236, 'EH', 'Western Sahara', 'ESH', 732, 212),
(237, 'YE', 'Yemen', 'YEM', 887, 967),
(238, 'ZM', 'Zambia', 'ZMB', 894, 260),
(239, 'ZW', 'Zimbabwe', 'ZWE', 716, 263),
(240, 'RS', 'Serbia', 'SRB', 688, 381),
(241, 'AP', 'Asia / Pacific Region', '0', 0, 0),
(242, 'ME', 'Montenegro', 'MNE', 499, 382),
(243, 'AX', 'Aland Islands', 'ALA', 248, 358),
(244, 'BQ', 'Bonaire, Sint Eustatius and Saba', 'BES', 535, 599),
(245, 'CW', 'Curacao', 'CUW', 531, 599),
(246, 'GG', 'Guernsey', 'GGY', 831, 44),
(247, 'IM', 'Isle of Man', 'IMN', 833, 44),
(248, 'JE', 'Jersey', 'JEY', 832, 44),
(249, 'XK', 'Kosovo', '---', 0, 381),
(250, 'BL', 'Saint Barthelemy', 'BLM', 652, 590),
(251, 'MF', 'Saint Martin', 'MAF', 663, 590),
(252, 'SX', 'Sint Maarten', 'SXM', 534, 1),
(253, 'SS', 'South Sudan', 'SSD', 728, 211);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_area`
--

DROP TABLE IF EXISTS `delivery_area`;
CREATE TABLE IF NOT EXISTS `delivery_area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(250) NOT NULL DEFAULT '',
  `color` varchar(7) NOT NULL DEFAULT '#0099ff',
  `polygon` text NOT NULL DEFAULT '',
  `delivery_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `delivery_min_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `delivery_free_on` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_free_from` decimal(15,2) NOT NULL DEFAULT 0.00,
  `delivery_time` int(11) NOT NULL DEFAULT 60,
  `delivery_on` tinyint(1) NOT NULL DEFAULT 1,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `delivery_area`
--

TRUNCATE TABLE `delivery_area`;
--
-- Dumping data for table `delivery_area`
--

INSERT INTO `delivery_area` (`id`, `name`, `description`, `color`, `polygon`, `delivery_cost`, `delivery_min_cost`, `delivery_free_on`, `delivery_free_from`, `delivery_time`, `delivery_on`, `time`) VALUES
(1, 'Dostawa Wilanów', 'Obszar dostawy Wilanów', '#0099ff', '{\"coordinates\":[{\"lat\":52.079668036108124,\"lng\":21.035302624511726},{\"lat\":52.08515354842626,\"lng\":21.020883068847663},{\"lat\":52.10582365081967,\"lng\":21.018823132324226},{\"lat\":52.165249504734334,\"lng\":21.0174498413086},{\"lat\":52.16482832387728,\"lng\":21.034615979003913},{\"lat\":52.171987856302856,\"lng\":21.046288952636726},{\"lat\":52.1943013574588,\"lng\":21.0504088256836},{\"lat\":52.20061446664579,\"lng\":21.060021862792976},{\"lat\":52.20482270795414,\"lng\":21.084054455566413},{\"lat\":52.213658717595145,\"lng\":21.093667492675788},{\"lat\":52.206085102638056,\"lng\":21.101220593261726},{\"lat\":52.183777516090146,\"lng\":21.10808704833985},{\"lat\":52.16903995306031,\"lng\":21.13555286865235},{\"lat\":52.15556133642413,\"lng\":21.142419323730476},{\"lat\":52.122690104120515,\"lng\":21.175378308105476},{\"lat\":52.094013336479435,\"lng\":21.207650646972663},{\"lat\":52.06658447817172,\"lng\":21.1987242553711},{\"lat\":52.05856362812557,\"lng\":21.159585461425788},{\"lat\":52.041672919473896,\"lng\":21.086801037597663},{\"lat\":52.06489599786286,\"lng\":21.037362561035163}]}', '6.00', '80.00', 1, '100.00', 60, 1, '2020-10-25 16:23:08'),
(2, 'Dostawa Ursynów', 'Obszar dostawy Ursynów', '#20be00', '{\"coordinates\":[{\"lat\":52.17367754648817,\"lng\":20.91136311035157},{\"lat\":52.16230641810709,\"lng\":20.951188549804694},{\"lat\":52.153460197944625,\"lng\":20.962861523437507},{\"lat\":52.14419084342019,\"lng\":20.969727978515632},{\"lat\":52.1395554424948,\"lng\":20.986207470703132},{\"lat\":52.14124109865308,\"lng\":21.01573322753907},{\"lat\":52.16820292150535,\"lng\":21.015046582031257},{\"lat\":52.167360611713896,\"lng\":21.030152783203132},{\"lat\":52.17115088020686,\"lng\":21.039765820312507},{\"lat\":52.177467276847175,\"lng\":21.044572338867194},{\"lat\":52.19178112230851,\"lng\":21.046632275390632},{\"lat\":52.19977802921807,\"lng\":21.052812084960944},{\"lat\":52.203986349730734,\"lng\":21.06791828613282},{\"lat\":52.20566956635349,\"lng\":21.082337841796882},{\"lat\":52.2149261180971,\"lng\":21.090577587890632},{\"lat\":52.217450297490764,\"lng\":21.075471386718757},{\"lat\":52.22165694438293,\"lng\":21.052812084960944},{\"lat\":52.235536052263654,\"lng\":21.038392529296882},{\"lat\":52.21702961088666,\"lng\":20.960114941406257},{\"lat\":52.18967643399676,\"lng\":20.917542919921882}]}', '6.00', '60.00', 0, '80.00', 60, 1, '2020-10-25 16:25:27'),
(4, 'Dostawa Wola', 'Obszar dostawy Wola', '#071b35', '{\"coordinates\":[{\"lat\":52.17367719257221,\"lng\":20.910676464843757},{\"lat\":52.16188485538461,\"lng\":20.949128613281257},{\"lat\":52.14966806840841,\"lng\":20.962174877929694},{\"lat\":52.14081933641716,\"lng\":20.970414624023444},{\"lat\":52.13871223629759,\"lng\":20.984834179687507},{\"lat\":52.13913366429619,\"lng\":21.017106518554694},{\"lat\":52.08431458501719,\"lng\":21.019166455078132},{\"lat\":52.077984968901724,\"lng\":21.034272656250007},{\"lat\":52.06996616653549,\"lng\":20.995133862304694},{\"lat\":52.09106518619682,\"lng\":20.934709057617194},{\"lat\":52.11173254999822,\"lng\":20.84819172363282},{\"lat\":52.12480287533783,\"lng\":20.874970898437507},{\"lat\":52.14419048926968,\"lng\":20.885270581054694},{\"lat\":52.16272726883018,\"lng\":20.907243237304694}]}', '5.00', '60.00', 0, '100.00', 60, 1, '2021-02-21 08:24:22');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `sorting` bigint(22) NOT NULL DEFAULT 0,
  `onstock` tinyint(1) NOT NULL DEFAULT 1,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('ACTIVE','DELETED') NOT NULL DEFAULT 'ACTIVE',
  `del` char(0) GENERATED ALWAYS AS (if(`status` = 'ACTIVE','',NULL)) STORED,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`,`del`) USING BTREE,
  UNIQUE KEY `uKey` (`name`,`del`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `product`
--

TRUNCATE TABLE `product`;
--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `slug`, `sorting`, `onstock`, `visible`, `status`, `time`) VALUES
(21, 'Pizza hawajska', 'pizza-hawajska', 2, 1, 1, 'ACTIVE', '2020-06-24 17:29:26'),
(22, 'Kebab', 'kebab', 1, 1, 1, 'ACTIVE', '2020-06-24 17:29:59'),
(23, 'Pizza wegetariańska', 'pizza-weganska', 2, 0, 1, 'ACTIVE', '2020-06-26 10:08:20'),
(29, 'Pierogi z kapustą', 'pierogi-z-kapusta', 1, 1, 1, 'ACTIVE', '2021-02-18 16:43:04');

-- --------------------------------------------------------

--
-- Table structure for table `product_variant`
--

DROP TABLE IF EXISTS `product_variant`;
CREATE TABLE IF NOT EXISTS `product_variant` (
  `id` bigint(22) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(22) NOT NULL,
  `size` varchar(50) NOT NULL,
  `price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `price_sale` decimal(15,2) NOT NULL DEFAULT 0.00,
  `packaging` decimal(15,2) NOT NULL DEFAULT 0.00,
  `onsale` tinyint(1) NOT NULL DEFAULT 0,
  `sorting` bigint(22) NOT NULL DEFAULT 0,
  `onstock` tinyint(1) NOT NULL DEFAULT 1,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `image_url` varchar(250) NOT NULL DEFAULT '',
  `about` varchar(250) NOT NULL DEFAULT '',
  `status` enum('ACTIVE','DELETED') NOT NULL DEFAULT 'ACTIVE',
  `del` char(0) GENERATED ALWAYS AS (if(`status` = 'ACTIVE','',NULL)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uKey` (`product_id`,`size`,`del`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `product_variant`
--

TRUNCATE TABLE `product_variant`;
-- --------------------------------------------------------

--
-- Table structure for table `product_variant_addon`
--

DROP TABLE IF EXISTS `product_variant_addon`;
CREATE TABLE IF NOT EXISTS `product_variant_addon` (
  `id` bigint(22) UNSIGNED NOT NULL AUTO_INCREMENT,
  `variant_id` bigint(22) UNSIGNED NOT NULL,
  `group_id` bigint(22) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `variant_id` (`variant_id`,`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `product_variant_addon`
--

TRUNCATE TABLE `product_variant_addon`;
-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ukey` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=334 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Truncate table before insert `settings`
--

TRUNCATE TABLE `settings`;
--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'company', 'West Station'),
(2, 'street', 'Al. Jerozolimskie 142'),
(3, 'city', 'Warszawa'),
(4, 'district', 'Śródmieście'),
(5, 'voivodeship', 'Mazowieckie'),
(6, 'email', 'email@host.name'),
(7, 'mobile', '+48 100 100 100'),
(8, 'about', 'Biura do wynajęcia w Warszawie. West Station to inwestycja oferująca w jednym miejscu nowoczesny dworzec kolejowy, dwa najwyższej klasy budynki biurowe.'),
(9, 'website', 'https://host.name'),
(10, 'delivery_on', '0'),
(11, 'pay_card', '0'),
(12, 'pay_money', '0'),
(13, 'pay_pickup', '1'),
(14, 'pay_online', '1'),
(15, 'social_facebook', ''),
(16, 'social_twitter', ''),
(17, 'social_instagram', ''),
(18, 'social_youtube', ''),
(19, 'map_lng', '20.954650'),
(20, 'map_lat', '52.214080'),
(283, 'open_day_1', '1'),
(284, 'open_hour_1', '11:30'),
(285, 'close_hour_1', '21:30'),
(286, 'open_day_2', '1'),
(287, 'open_hour_2', '11:30'),
(288, 'close_hour_2', '21:30'),
(289, 'open_day_3', '1'),
(290, 'open_hour_3', '11:30'),
(291, 'close_hour_3', '21:30'),
(292, 'open_day_4', '1'),
(293, 'open_hour_4', '11:30'),
(294, 'close_hour_4', '24:00'),
(295, 'open_day_5', '1'),
(296, 'open_hour_5', '11:30'),
(297, 'open_day_6', '1'),
(298, 'open_hour_6', '11:30'),
(299, 'close_hour_6', '21:30'),
(300, 'open_day_7', '1'),
(301, 'open_hour_7', '11:30'),
(302, 'close_hour_7', '21:30'),
(333, 'close_hour_5', '21:30');

-- --------------------------------------------------------

--
-- Table structure for table `shop_order`
--

DROP TABLE IF EXISTS `shop_order`;
CREATE TABLE IF NOT EXISTS `shop_order` (
  `id` bigint(22) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(22) NOT NULL DEFAULT 0,
  `worker_id` bigint(22) NOT NULL DEFAULT 0,
  `cost` decimal(15,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `delivery_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `packaging_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('PENDING','ACCEPTED','CANCELED') NOT NULL DEFAULT 'PENDING',
  `payment_type` enum('MONEY','CARD','PICKUP','ONLINE') NOT NULL DEFAULT 'MONEY',
  `payment_status` enum('NEW','PENDING','ACCEPTED','CANCELED','WAITING_FOR_CONFIRMATION','FAILED','REFUNDED') NOT NULL DEFAULT 'PENDING',
  `payment_gateway` varchar(50) NOT NULL DEFAULT 'NONE',
  `pick_up_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `visible_user` tinyint(1) NOT NULL DEFAULT 1,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `shop_order`
--

TRUNCATE TABLE `shop_order`;
--
-- Dumping data for table `shop_order`
--

INSERT INTO `shop_order` (`id`, `user_id`, `worker_id`, `cost`, `delivery_cost`, `packaging_cost`, `status`, `payment_type`, `payment_status`, `payment_gateway`, `pick_up_time`, `visible_user`, `visible`, `deleted`, `time`) VALUES
(1, 0, 0, '38.00', '23.00', '3.00', 'PENDING', 'MONEY', 'PENDING', 'NONE', '2021-06-08 17:31:32', 1, 1, 0, '2021-06-08 17:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `shop_order_product`
--

DROP TABLE IF EXISTS `shop_order_product`;
CREATE TABLE IF NOT EXISTS `shop_order_product` (
  `id` bigint(22) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint(22) NOT NULL,
  `variant_id` bigint(22) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `shop_order_product`
--

TRUNCATE TABLE `shop_order_product`;
-- --------------------------------------------------------

--
-- Table structure for table `shop_order_product_addon`
--

DROP TABLE IF EXISTS `shop_order_product_addon`;
CREATE TABLE IF NOT EXISTS `shop_order_product_addon` (
  `id` bigint(22) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_product_id` bigint(22) NOT NULL,
  `addon_id` bigint(22) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `shop_order_product_addon`
--

TRUNCATE TABLE `shop_order_product_addon`;
-- --------------------------------------------------------

--
-- Table structure for table `token`
--

DROP TABLE IF EXISTS `token`;
CREATE TABLE IF NOT EXISTS `token` (
  `token_id` bigint(22) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(22) NOT NULL,
  `hash` text NOT NULL,
  `expires` timestamp NOT NULL DEFAULT current_timestamp(),
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `token_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `token`
--

TRUNCATE TABLE `token`;
--
-- Dumping data for table `token`
--

INSERT INTO `token` (`token_id`, `user_id`, `hash`, `expires`, `created`) VALUES
(11, 1, '3eb4187e-bd90-11eb-a49c-f6089a8b7b46', '2021-05-26 19:34:38', '2021-05-25 19:34:38');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(22) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(190) NOT NULL,
  `role` enum('user','admin','worker') NOT NULL,
  `is_driver` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(250) NOT NULL DEFAULT '',
  `location` varchar(250) NOT NULL DEFAULT '',
  `mobile` varchar(100) NOT NULL DEFAULT '',
  `about` varchar(250) NOT NULL DEFAULT '',
  `www` varchar(250) NOT NULL DEFAULT '',
  `status` enum('ACTIVE','ONHOLD','DELETED','BANED') NOT NULL DEFAULT 'ONHOLD',
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  `pass` varchar(100) NOT NULL,
  `del` char(0) GENERATED ALWAYS AS (if(`status` in ('ACTIVE','ONHOLD','BANNED'),'',NULL)) STORED,
  `code` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`del`) USING BTREE,
  UNIQUE KEY `email` (`email`,`del`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

--
-- Truncate table before insert `user`
--

TRUNCATE TABLE `user`;
--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `role`, `is_driver`, `name`, `location`, `mobile`, `about`, `www`, `status`, `time`, `pass`, `code`) VALUES
(1, 'admin', 'admin@woo.xx', 'admin', 0, 'Admin', 'Usa', '+48100100100', 'Administrator', '', 'ACTIVE', '2021-05-17 13:55:13', '5f4dcc3b5aa765d61d8327deb882cf99', ''),
(2, 'user', 'user@woo.xx', 'user', 0, 'User', 'Poland', '+48500600700', 'Client', '', 'ACTIVE', '2021-03-17 14:55:13', '5f4dcc3b5aa765d61d8327deb882cf99', ''),
(3, 'driver', 'driver@woo.xx', 'worker', 1, 'Driver', 'Swiss', '+48100200300', 'Driver', '', 'ONHOLD', '2021-05-20 15:52:41', '5f4dcc3b5aa765d61d8327deb882cf99', '60c46878a2bf9'),
(4, 'worker', 'worker@woo.xx', 'worker', 0, 'Worker', 'Swiss', '+400200300600', 'Worker', '', 'ACTIVE', '2021-06-09 08:44:27', '5f4dcc3b5aa765d61d8327deb882cf99', '60c46f456cca0'),
(6, 'max', 'max@woo.xx', 'worker', 0, 'Max', 'Canada', '100 200 300', 'Web dev', '', 'ACTIVE', '2021-06-09 08:44:27', '5f4dcc3b5aa765d61d8327deb882cf99', '60c46f4566d61');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
