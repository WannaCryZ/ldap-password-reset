/*
Navicat MySQL Data Transfer

Source Server         : 127.0.0.1
Source Server Version : 80017
Source Host           : localhost:3306
Source Database       : ldap_manage

Target Server Type    : MYSQL
Target Server Version : 80017
File Encoding         : 65001

Date: 2025-06-24 13:40:58
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for reset_pass_log
-- ----------------------------
DROP TABLE IF EXISTS `reset_pass_log`;
CREATE TABLE `reset_pass_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `operator` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '操作者',
  `operation_date` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '操作时间',
  `operation_type` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '操作类型',
  `operation_content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '操作内容',
  `ip_addr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'IP地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=223 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='操作日志表';

-- ----------------------------
-- Table structure for reset_pass_temp
-- ----------------------------
DROP TABLE IF EXISTS `reset_pass_temp`;
CREATE TABLE `reset_pass_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(10) NOT NULL COMMENT 'LDAP的用户名',
  `code` char(32) NOT NULL COMMENT '校验码',
  `start_time` varchar(100) NOT NULL COMMENT '申请时间',
  `expire_time` varchar(100) NOT NULL COMMENT '过期时间',
  `is_expire` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否过期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
