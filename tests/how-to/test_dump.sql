/*
 Navicat Premium Data Transfer

 Source Server         : localserver
 Source Server Type    : MySQL
 Source Server Version : 50616
 Source Host           : localhost
 Source Database       : phppuli

 Target Server Type    : MySQL
 Target Server Version : 50616
 File Encoding         : utf-8

 Date: 07/01/2016 15:52:14 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `pp_simple_table`
-- ----------------------------
DROP TABLE IF EXISTS `pp_simple_table`;
CREATE TABLE `pp_simple_table` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_date` datetime DEFAULT NULL,
  `table_col_1` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Records of `pp_simple_table`
-- ----------------------------
BEGIN;
INSERT INTO `pp_simple_table` VALUES ('1', '2016-07-01 15:31:16', 'test_value_for_fetch'), ('2', '2016-02-27 16:54:28', 'test_value_for_fetch'), ('3', '2016-02-28 20:40:49', 'persisted_all'), ('4', '2016-02-28 20:42:19', 'persisted_all'), ('5', '2016-02-28 20:45:44', 'persisted_all'), ('6', '2016-02-28 20:46:24', 'persisted_all'), ('7', '2016-02-28 20:52:16', 'persisted_all'), ('8', '2016-02-28 20:55:40', 'persisted_all_4171245294'), ('9', '2016-02-28 20:59:39', 'persisted_all_982851437'), ('10', '2016-02-28 21:00:31', 'persisted_all_6968952283'), ('11', '2016-03-31 22:17:22', 'persisted_all_84441526607'), ('12', '2016-07-01 15:13:13', 'persisted_all_99410567247'), ('13', '2016-07-01 15:14:14', 'persisted_all_84596775845'), ('14', '2016-07-01 15:17:00', 'persisted_all_35791900381'), ('15', '2016-07-01 15:17:48', 'persisted_all_39113744488'), ('16', '2016-07-01 15:18:07', 'persisted_all_31957439380'), ('17', '2016-07-01 15:27:06', 'persisted_all_44264187943'), ('18', '2016-07-01 15:27:48', 'persisted_all_3633017512'), ('19', '2016-07-01 15:28:07', 'persisted_all_28419190831'), ('20', '2016-07-01 15:28:59', 'persisted_all_59859937057'), ('21', '2016-07-01 15:30:04', 'persisted_all_85242438829'), ('22', '2016-07-01 15:30:33', 'persisted_all_97234202642'), ('23', '2016-07-01 15:31:16', 'persisted_all_68405135535');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
