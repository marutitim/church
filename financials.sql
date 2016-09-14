/*
MySQL Data Transfer
Source Host: localhost
Source Database: financials
Target Host: localhost
Target Database: financials
Date: 9/8/2016 10:48:48 AM
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for assests
-- ----------------------------
CREATE TABLE `assests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `location` varchar(250) DEFAULT NULL,
  `value` varchar(250) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `date` varchar(250) DEFAULT NULL,
  `dateb` varchar(250) DEFAULT NULL,
  `status` varchar(250) DEFAULT NULL,
  `serial` varchar(250) DEFAULT NULL,
  `quality` varchar(250) DEFAULT NULL,
  `person` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for baptised
-- ----------------------------
CREATE TABLE `baptised` (
  `reg_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `gender` varchar(250) DEFAULT NULL,
  `home_cell` varchar(250) DEFAULT NULL,
  `marital` varchar(250) DEFAULT NULL,
  `category` varchar(250) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `member` tinyint(250) DEFAULT NULL,
  `saved` varchar(250) DEFAULT NULL,
  `date_saved` varchar(250) DEFAULT NULL,
  `previous_church` varchar(250) DEFAULT NULL,
  `moved` tinyint(4) DEFAULT NULL,
  `visitor` tinyint(4) DEFAULT NULL,
  `baptised` tinyint(4) DEFAULT NULL,
  `seek_baptism` tinyint(4) DEFAULT NULL,
  `pay_tithe` tinyint(4) DEFAULT NULL,
  `see_pastor` tinyint(4) DEFAULT NULL,
  `agree_teaching` tinyint(4) DEFAULT NULL,
  `agree_support_church` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `prayer` longtext,
  `date` varchar(250) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `dedicated` tinyint(4) DEFAULT '0',
  `dateofbaptism` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`reg_no`),
  UNIQUE KEY `id` (`reg_no`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dedicated
-- ----------------------------
CREATE TABLE `dedicated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `dob` varchar(200) DEFAULT NULL,
  `mother` varchar(200) DEFAULT NULL,
  `father` varchar(200) DEFAULT NULL,
  `deddate` varchar(200) DEFAULT NULL,
  `minister` varchar(200) DEFAULT NULL,
  `place` varchar(200) DEFAULT NULL,
  `church` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for events
-- ----------------------------
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` varchar(255) DEFAULT NULL,
  `event_items` longtext,
  `start_date` varchar(255) DEFAULT NULL,
  `end_date` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for expenses
-- ----------------------------
CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(250) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `amount` int(4) DEFAULT NULL,
  `person` varchar(200) DEFAULT NULL,
  `purpose` longtext,
  `category` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for finances
-- ----------------------------
CREATE TABLE `finances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_no` int(11) NOT NULL DEFAULT '0',
  `tithe` int(11) DEFAULT NULL,
  `first_fruit` int(11) DEFAULT NULL,
  `love_offering` int(11) DEFAULT NULL,
  `evangelism` int(11) DEFAULT NULL,
  `seed` int(11) DEFAULT NULL,
  `others` int(11) DEFAULT NULL,
  `thanksgiving` int(11) DEFAULT NULL,
  `paster_blessing` int(11) DEFAULT NULL,
  `totals` int(11) DEFAULT NULL,
  `date` varchar(250) DEFAULT NULL,
  `welfare` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for home_cell
-- ----------------------------
CREATE TABLE `home_cell` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cell_name` varchar(250) DEFAULT NULL,
  `reg_no` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `position` varchar(250) DEFAULT NULL,
  `cell_info` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for login
-- ----------------------------
CREATE TABLE `login` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `previlages` varchar(100) DEFAULT NULL,
  `reg_no` int(11) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`login_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for lookup
-- ----------------------------
CREATE TABLE `lookup` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) DEFAULT NULL,
  `item_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for member_to_group
-- ----------------------------
CREATE TABLE `member_to_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_no` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for members
-- ----------------------------
CREATE TABLE `members` (
  `reg_no` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `gender` varchar(250) DEFAULT NULL,
  `home_cell` varchar(250) DEFAULT NULL,
  `marital` varchar(250) DEFAULT NULL,
  `category` varchar(250) DEFAULT NULL,
  `image` varchar(250) DEFAULT NULL,
  `member` tinyint(250) DEFAULT NULL,
  `saved` varchar(250) DEFAULT NULL,
  `date_saved` varchar(250) DEFAULT NULL,
  `previous_church` varchar(250) DEFAULT NULL,
  `moved` tinyint(4) DEFAULT NULL,
  `visitor` tinyint(4) DEFAULT NULL,
  `baptised` tinyint(4) DEFAULT NULL,
  `seek_baptism` tinyint(4) DEFAULT NULL,
  `pay_tithe` tinyint(4) DEFAULT NULL,
  `see_pastor` tinyint(4) DEFAULT NULL,
  `agree_teaching` tinyint(4) DEFAULT NULL,
  `agree_support_church` tinyint(4) DEFAULT NULL,
  `active` tinyint(4) DEFAULT NULL,
  `prayer` longtext,
  `date` varchar(250) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `baptisedyes` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`reg_no`),
  UNIQUE KEY `id` (`reg_no`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for members_to_dedicated
-- ----------------------------
CREATE TABLE `members_to_dedicated` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_no` int(11) DEFAULT NULL,
  `ded_id` int(11) DEFAULT NULL,
  `date` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for offering
-- ----------------------------
CREATE TABLE `offering` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `men` int(11) DEFAULT NULL,
  `women` int(11) DEFAULT NULL,
  `youth` int(11) DEFAULT NULL,
  `children` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `date` varchar(200) DEFAULT NULL,
  `tithe` int(11) DEFAULT NULL,
  `thanks` int(11) DEFAULT NULL,
  `seed` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for visitors
-- ----------------------------
CREATE TABLE `visitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT NULL,
  `phone` int(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `gender` varchar(200) DEFAULT NULL,
  `homecell` varchar(200) DEFAULT NULL,
  `marital` varchar(200) DEFAULT NULL,
  `category` varchar(200) DEFAULT NULL,
  `date` varchar(200) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `saved` varchar(200) DEFAULT NULL,
  `date_saved` varchar(200) DEFAULT NULL,
  `previous_church` varchar(200) DEFAULT NULL,
  `moved` varchar(200) DEFAULT NULL,
  `visitor` varchar(200) DEFAULT NULL,
  `baptised` varchar(200) DEFAULT NULL,
  `pay_tithe` varchar(200) DEFAULT NULL,
  `see_pastor` varchar(200) DEFAULT NULL,
  `agree_teaching` varchar(200) DEFAULT NULL,
  `agree_support_church` varchar(200) DEFAULT NULL,
  `active` varchar(200) DEFAULT NULL,
  `prayer` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `assests` VALUES ('1', 's', null, null, null, '2016-06-21 14:06:13', null, null, null, null, null);
INSERT INTO `assests` VALUES ('2', 'Camp Tents', 'kahawa', '12000', '2.jpg', '2016-06-21 14:09:06', '2016-05-10', 'good', 'e23423kjksd', 'goo', '9');
INSERT INTO `assests` VALUES ('3', 'Piano2', 'main church', '10000', '3.jpg', '2016-06-21 14:10:16', '2016-06-08', 'good', '034234324', 'yes', '7');
INSERT INTO `assests` VALUES ('4', 'chair', 'kitchen', '1111', '4.jpg', '2016-06-21 14:10:46', '2016-07-20', 'Destroyed', '8867hfgfgd', 'sas', '11');
INSERT INTO `assests` VALUES ('5', 'tables', 'Main Church', '1200', '5.jpg', null, '', 'good', '', '', '2');
INSERT INTO `assests` VALUES ('6', 'rr', 'hhjk', '400', '6.jpg', null, '2016-08-12', 'Destroyed', '66673bsms', '50', '17');
INSERT INTO `baptised` VALUES ('15', 'timothy', 'kisuk@nts.nl', '3', 'Ebenezar', '16', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '834', null, '0', '2016-07-10');
INSERT INTO `baptised` VALUES ('18', 'eewwec   xzx', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '2016-07-10');
INSERT INTO `baptised` VALUES ('19', 'weeew', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '2016-07-10');
INSERT INTO `baptised` VALUES ('20', 'Eunice njeri', 'njeri@gmail.com', '2', 'Elishadai', '14', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '7435435', null, '0', null);
INSERT INTO `baptised` VALUES ('21', 'david', 'dave@gmail.com', '2', 'Elishadai', '14', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '72344', null, '0', null);
INSERT INTO `baptised` VALUES ('22', 'test', null, '2', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null);
INSERT INTO `baptised` VALUES ('23', 'James Karanja', 'james@gmail.com', null, null, '', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '7123123', null, '0', null);
INSERT INTO `baptised` VALUES ('24', 'testing today', 'mukhwanatimothy@yahoo.com', '3', 'Patmos', '15', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '71604565', null, '0', null);
INSERT INTO `baptised` VALUES ('25', 'jim', 'jom@h.com', '2', 'Elishadai', '14', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '87666', null, '0', null);
INSERT INTO `dedicated` VALUES ('6', 'test', null, null, null, null, null, null, null);
INSERT INTO `dedicated` VALUES ('7', null, null, null, null, null, null, null, null);
INSERT INTO `dedicated` VALUES ('8', 'sdfsfdfa', '2016-07-31', 'test', 'kim', 'NOW()', null, '', '');
INSERT INTO `dedicated` VALUES ('9', 'aaa', '2016-07-31', 'ann', 'timo', '2016-07-27', null, null, null);
INSERT INTO `dedicated` VALUES ('11', 'eewwec   xzx', '2016-07-06', 'df', 'fgds', '2016-07-28', null, null, null);
INSERT INTO `dedicated` VALUES ('12', 'james', '2016-06-28', 'sdfds', 'sdd', '2016-08-13', '2016-08-13', null, null);
INSERT INTO `events` VALUES ('1', '1468226723779', 'testing valueseee wfwrew', '2016-12-29 03:00', '2016-12-29 03:05', null);
INSERT INTO `events` VALUES ('11', '1468235322665', 'choir practice', '2015-12-29 08:00', '2015-12-29 14:15', '4');
INSERT INTO `events` VALUES ('12', '1468235322666', 'meeting with guests', '2015-12-29 15:00', '2015-12-29 15:05', '4');
INSERT INTO `events` VALUES ('13', '1468235322667', 'welcome visitors', '2015-12-29 10:20', '2015-12-29 12:55', '4');
INSERT INTO `events` VALUES ('14', '1468236572389', 'New eventgfhgfhfh', '2016-07-11 00:00', '2016-07-11 11:25', '');
INSERT INTO `events` VALUES ('15', '1468236572392', 'New event', '2016-07-12 00:00', '2016-07-12 03:55', '');
INSERT INTO `events` VALUES ('16', '1468236572393', 'hggggggggggggggggggggggg', '2016-07-06 00:00', '2016-07-06 00:05', null);
INSERT INTO `events` VALUES ('17', '1468236572394', 'tyuytutyu', '2016-08-17 00:00', '2016-08-17 00:05', null);
INSERT INTO `events` VALUES ('18', '1468236572395', 'ytutyutyutyu', '2016-06-08 00:00', '2016-06-08 00:05', null);
INSERT INTO `events` VALUES ('19', '1468236572396', 'ytutyutyuyt', '2016-06-13 00:00', '2016-06-13 00:05', null);
INSERT INTO `events` VALUES ('20', '1468236572397', 'ytuytutyuyt', '2016-06-11 00:00', '2016-06-11 00:05', null);
INSERT INTO `events` VALUES ('24', '1468268300996', 'construct new room', '2016-01-05 01:00', '2016-01-10 01:20', '4');
INSERT INTO `events` VALUES ('25', '1468302852900', 'woship', '2016-02-05 01:30', '2016-03-01 01:15', '1');
INSERT INTO `events` VALUES ('27', '1469942551020', 'Making on church plans', '2016-01-05 11:25', '2016-01-05 11:30', '4');
INSERT INTO `events` VALUES ('28', '1469943081529', 'contribution toward church', '2015-12-29 11:00', '2015-12-29 11:05', '1');
INSERT INTO `events` VALUES ('29', '1469943081530', 'choir practice', '2015-12-29 13:40', '2015-12-29 13:45', '2');
INSERT INTO `events` VALUES ('30', '1469943081531', 'discussing on education', '2015-12-31 14:10', '2015-12-31 14:15', '3');
INSERT INTO `events` VALUES ('31', '1469978076615', 'Revival Meeting', '2016-08-22 06:30', '2016-08-28 18:00', '4');
INSERT INTO `events` VALUES ('32', '1469978076616', 'Sunday school day ff', '2016-09-04 08:00', '2016-09-04 12:00', '4');
INSERT INTO `events` VALUES ('33', '1469978076617', 'Board Meeting', '2016-08-06 02:00', '2016-08-06 05:00', '1');
INSERT INTO `events` VALUES ('34', '1469978076620', 'Board Meeting', '2016-08-06 02:00', '2016-08-06 05:00', '4');
INSERT INTO `events` VALUES ('35', '1469978863605', 'New event', '2016-08-29 00:45', '2016-08-29 00:50', '');
INSERT INTO `events` VALUES ('36', '1471077272327', 'New event', '2016-01-01 00:00', '2016-01-01 00:05', '1');
INSERT INTO `events` VALUES ('37', '1472755563502', 'doing abc', '2016-09-01 00:00', '2016-09-01 00:05', '4');
INSERT INTO `expenses` VALUES ('23', '2016-08-27 20:53:26', 'comps', '100', '11', 't', '23');
INSERT INTO `expenses` VALUES ('24', '2016-08-27 21:00:32', 'test', '50', '16', 'were', '25');
INSERT INTO `expenses` VALUES ('25', '2016-08-27 21:03:32', 'weww', '100', '11', 'sfd', '25');
INSERT INTO `expenses` VALUES ('26', '2016-08-27 21:04:01', 'werer', '500', '11', 'sdf', '26');
INSERT INTO `expenses` VALUES ('30', '2016-08-30 07:11:33', null, '0', null, null, null);
INSERT INTO `expenses` VALUES ('31', '2016-08-30 07:14:52', null, '0', null, null, null);
INSERT INTO `finances` VALUES ('2', '1', null, null, null, '555', null, null, null, null, null, null, null);
INSERT INTO `finances` VALUES ('3', '1', null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `finances` VALUES ('4', '1', null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `finances` VALUES ('5', '1', null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `finances` VALUES ('6', '1', null, '1', '3', '4', '2', '23', '34', '700', null, null, null);
INSERT INTO `finances` VALUES ('8', '2', '0', '0', '0', '0', '0', '0', '0', '0', '0', '2016-06-20 12:43:41', null);
INSERT INTO `finances` VALUES ('9', '2', '100', '500', '400', '100', '300', '300', '250', '150', '1600', '2016-07-18 09:18:27', null);
INSERT INTO `finances` VALUES ('11', '2', '1200', '100', '300', '0', '0', '0', '0', '0', '1600', '2016-07-31 17:48:26', null);
INSERT INTO `finances` VALUES ('13', '17', '9000', '0', '0', '0', '0', '0', '90', '0', '9090', '2016-07-31 18:54:23', null);
INSERT INTO `finances` VALUES ('14', '17', '0', '800', '0', '0', '0', '0', '0', '0', '800', '2016-07-31 18:54:47', null);
INSERT INTO `finances` VALUES ('15', '17', null, null, null, null, null, null, null, null, null, '2016-07-31 18:55:07', null);
INSERT INTO `finances` VALUES ('16', '17', '0', '0', '789', '0', '0', '0', '0', '0', '789', '2016-07-31 18:55:09', null);
INSERT INTO `finances` VALUES ('17', '7', '400', '300', '600', '700', '200', '600', '400', '500', '4100', '2016-08-27 09:38:21', '400');
INSERT INTO `finances` VALUES ('18', '11', '100', '0', '0', '0', '0', '0', '0', '0', '100', '2016-08-28 16:06:38', '0');
INSERT INTO `finances` VALUES ('19', '11', null, null, null, null, null, null, null, null, null, '2016-08-28 16:08:04', null);
INSERT INTO `home_cell` VALUES ('1', 'Reharboth', null, null, null, null);
INSERT INTO `home_cell` VALUES ('2', 'Patmos', null, null, null, null);
INSERT INTO `home_cell` VALUES ('3', 'Elishadai', null, null, null, null);
INSERT INTO `home_cell` VALUES ('4', 'Ebenezar', null, null, null, null);
INSERT INTO `home_cell` VALUES ('5', 'Bethsaida', null, null, null, null);
INSERT INTO `home_cell` VALUES ('6', 'Emanuel', null, null, null, null);
INSERT INTO `login` VALUES ('1', 'M/NUMBER/0002', '1234', '10', '2', '1', '');
INSERT INTO `login` VALUES ('2', 'M/NUMBER/0007', '1111', '11', '8', '1', null);
INSERT INTO `login` VALUES ('3', 'ndiku', '2222', '11', '13', '1', null);
INSERT INTO `login` VALUES ('6', '7', 'nderu1', '12', '14', '0', null);
INSERT INTO `lookup` VALUES ('1', 'Gender', '11');
INSERT INTO `lookup` VALUES ('2', 'Male', '1');
INSERT INTO `lookup` VALUES ('3', 'Female', '1');
INSERT INTO `lookup` VALUES ('4', 'Financials', '12');
INSERT INTO `lookup` VALUES ('5', 'Tithe', '4');
INSERT INTO `lookup` VALUES ('6', 'Thangsgiving', '4');
INSERT INTO `lookup` VALUES ('7', 'Offering', '4');
INSERT INTO `lookup` VALUES ('8', 'DLT', '4');
INSERT INTO `lookup` VALUES ('9', 'Previlages', '15');
INSERT INTO `lookup` VALUES ('10', 'Admin', '9');
INSERT INTO `lookup` VALUES ('11', 'User', '9');
INSERT INTO `lookup` VALUES ('12', 'Guest', '9');
INSERT INTO `lookup` VALUES ('13', 'Marital', '14');
INSERT INTO `lookup` VALUES ('14', 'Single', '13');
INSERT INTO `lookup` VALUES ('15', 'Married', '13');
INSERT INTO `lookup` VALUES ('16', 'Engaged', '13');
INSERT INTO `lookup` VALUES ('17', 'category', '16');
INSERT INTO `lookup` VALUES ('18', 'Visitor', '17');
INSERT INTO `lookup` VALUES ('19', 'Child', '17');
INSERT INTO `lookup` VALUES ('20', 'Member', '17');
INSERT INTO `lookup` VALUES ('21', 'cells', '18');
INSERT INTO `lookup` VALUES ('22', 'category', '0');
INSERT INTO `lookup` VALUES ('23', 'youth', '22');
INSERT INTO `lookup` VALUES ('24', 'children', '22');
INSERT INTO `lookup` VALUES ('25', 'men', '22');
INSERT INTO `lookup` VALUES ('26', 'women', '22');
INSERT INTO `lookup` VALUES ('27', 'Others', '22');
INSERT INTO `member_to_group` VALUES ('1', '1', '2');
INSERT INTO `member_to_group` VALUES ('2', '1', '5');
INSERT INTO `member_to_group` VALUES ('3', '7', '2');
INSERT INTO `member_to_group` VALUES ('4', '7', '3');
INSERT INTO `members` VALUES ('1', 'sdfsfdfa', 'weee', '2', 'Patmos', '14', '20', '1.jpg', null, '1', '2016-06-30', 'uii', '1', '1', '1', null, '1', '0', '1', '1', '1', 'tyrtytuy', null, '2345324', 'test', '1');
INSERT INTO `members` VALUES ('2', 'Timothy Maruti', 'asd@gmail.com', '2', 'Patmos', '14', '18', '2.jpg', null, '1', '2016-06-29', 'tttt', '1', '1', '0', null, '1', '1', '1', '0', '0', 'fggggghfgh', null, '435345', '123', '0');
INSERT INTO `members` VALUES ('5', 'timothy', 'kisuk@nts.nl', '3', 'Ebenezar', '16', '', '5.jpg', null, '1', '2016-07-06', 'ewwe', '0', '1', '0', null, '0', '1', '0', '0', '0', '', '2016-06-20 10:39:02', '834', '2343', '0');
INSERT INTO `members` VALUES ('6', 'test', null, '2', null, null, null, '6.jpg', null, null, null, null, null, null, null, null, null, null, null, null, null, null, '2016-06-20 14:27:15', null, null, '0');
INSERT INTO `members` VALUES ('7', 'Eunice njeri', 'njeri@gmail.com', '2', 'Elishadai', '14', '', '7.jpg', null, '1', '', '', '0', '0', '0', null, '0', '1', '0', '0', '0', '', null, '7435435', '', '0');
INSERT INTO `members` VALUES ('8', 'James Karanja', 'james@gmail.com', null, null, '', null, '8.jpg', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '7123123', null, '0');
INSERT INTO `members` VALUES ('9', 'testing today', 'mukhwanatimothy@yahoo.com', '3', 'Patmos', '15', '', null, null, '1', '', '', '0', '0', '0', null, '0', '1', '0', '0', '0', '', null, '71604565', '123', '0');
INSERT INTO `members` VALUES ('10', 'tIMOTHY FINAL TESTsdfdf', 'gilbertmok@yahoo.com', '2', 'Emanuel', '14', '', null, null, '0', '2016-07-26', '0', '0', '0', '0', null, '0', '0', '0', '0', '1', '', null, '75656', '456456', '1');
INSERT INTO `members` VALUES ('11', 'Ann Kibe', 'ann@gmail.com', '3', 'Ebenezar', '15', '', '11.jpg', null, '0', '2016-07-27', '0', '0', '0', '0', null, '0', '0', '1', '1', '0', '', null, '7234324', '', '0');
INSERT INTO `members` VALUES ('12', 'weeew', null, null, null, null, null, '12.jpg', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `members` VALUES ('13', 'visitor 1', 'tim@gmail.com', '2', 'Reharboth', '14', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '743534', null, '1');
INSERT INTO `members` VALUES ('14', 'Antony Kimani', 'sdk@df.com', '2', 'Reharboth', '14', null, '14.jpg', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '73444', null, '1');
INSERT INTO `members` VALUES ('15', 'Tim Kimani', 'tim@gmail.com', '2', 'Reharboth', '14', '', '15.jpg', null, '0', '2013-08-06', '0', '0', '0', '0', null, '0', '0', '0', '0', '1', '', '2016-07-31 17:44:26', '716099902', '1250 Thika', '1');
INSERT INTO `members` VALUES ('16', 'david', 'dave@gmail.com', '2', 'Elishadai', '14', null, '16.jpg', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '72344', null, '0');
INSERT INTO `members` VALUES ('17', 'jim', 'jom@h.com', '2', 'Elishadai', '14', '', null, null, '0', '2016-07-27', '0', '0', '0', '0', null, '0', '0', '0', '0', '1', '', '2016-07-31 18:53:39', '87666', '67', '0');
INSERT INTO `members_to_dedicated` VALUES ('1', '7', null, '2016-07-16 23:56:12');
INSERT INTO `offering` VALUES ('20', '200', '100', '400', '300', null, '2016-08-27 20:52:41', '50', '50', '100');
INSERT INTO `offering` VALUES ('22', '400', '1000', '300', '150', null, '2016-08-27 21:26:19', '330', '330', '600');
INSERT INTO `offering` VALUES ('23', '700', '300', '100', '50', null, '2016-08-28 16:17:40', '0', '0', '0');
INSERT INTO `offering` VALUES ('25', null, null, null, null, null, '2016-09-04 12:29:22', null, null, null);
INSERT INTO `visitors` VALUES ('13', 'ann Kimani', '7345', 'ann@yahoo.com', '3', 'Reharboth', '14', '', '2016-07-31 07:59:33', '0', '1', '0', '0', '0', '0', '0', '0', '1', '0', '0', '', '');
INSERT INTO `visitors` VALUES ('14', 'james karanja', '7345345', 'james@gmail.com', '2', 'Reharboth', '14', '', '2016-07-31 07:59:36', '1213', '1', '2016-07-31', '0', '0', '0', '0', '0', '1', '0', '0', '', '');
INSERT INTO `visitors` VALUES ('15', 'sdf', '56745654', 'wer@gmail.com', '2', 'Reharboth', '14', '', '2016-08-27 22:47:40', 'fg', '1', '2016-08-28', '0', '0', '0', '0', '0', '1', '0', '0', '', '');
INSERT INTO `visitors` VALUES ('16', '', null, null, null, null, null, null, '2016-08-27 22:47:40', null, null, null, null, null, null, null, null, null, null, null, null, null);
