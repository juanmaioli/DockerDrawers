/*
 Navicat Premium Data Transfer

 Source Server         : drawer.juanmaioli.com.ar
 Source Server Type    : MariaDB
 Source Server Version : 101104 (10.11.4-MariaDB-1~deb12u1)
 Source Host           : juanmaioli.com.ar:3306
 Source Schema         : admin_drawer

 Target Server Type    : MariaDB
 Target Server Version : 101104 (10.11.4-MariaDB-1~deb12u1)
 File Encoding         : 65001

 Date: 09/01/2024 10:20:23
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for drawers_action
-- ----------------------------
DROP TABLE IF EXISTS `drawers_action`;
CREATE TABLE `drawers_action`  (
  `action_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  PRIMARY KEY (`action_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for drawers_category
-- ----------------------------
DROP TABLE IF EXISTS `drawers_category`;
CREATE TABLE `drawers_category`  (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `category_color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for drawers_drawer
-- ----------------------------
DROP TABLE IF EXISTS `drawers_drawer`;
CREATE TABLE `drawers_drawer`  (
  `drawer_id` int(11) NOT NULL AUTO_INCREMENT,
  `drawer_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `drawer_category` int(11) NULL DEFAULT 1,
  `drawer_owner` int(11) NULL DEFAULT NULL,
  `drawer_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `drawer_descriptinon` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `drawer_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT 'default.png',
  `drawer_image_full` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT 'default.png',
  `drawer_date` datetime NULL DEFAULT NULL,
  `drawer_update` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `drawer_delete` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`drawer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 130 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for drawers_fav
-- ----------------------------
DROP TABLE IF EXISTS `drawers_fav`;
CREATE TABLE `drawers_fav`  (
  `fav_id` int(11) NOT NULL AUTO_INCREMENT,
  `fav_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `fav_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `fav_img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `fav_price` float(10, 2) NULL DEFAULT NULL,
  `fav_mla` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `fav_delete` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`fav_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 59 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for drawers_inches_mm
-- ----------------------------
DROP TABLE IF EXISTS `drawers_inches_mm`;
CREATE TABLE `drawers_inches_mm`  (
  `inches_mm_id` int(11) NOT NULL AUTO_INCREMENT,
  `inches_mm_mm` float(10, 2) NULL DEFAULT NULL,
  `inches_mm_inches` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `inches_mm_ml_inches` float(10, 2) NULL DEFAULT NULL,
  `inches_mm_tool` int(11) NULL DEFAULT 0,
  PRIMARY KEY (`inches_mm_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 192 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for drawers_items
-- ----------------------------
DROP TABLE IF EXISTS `drawers_items`;
CREATE TABLE `drawers_items`  (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_drawer` int(11) NULL DEFAULT NULL,
  `item_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `item_category` int(11) NULL DEFAULT NULL,
  `item_descrption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `item_amount` int(11) NULL DEFAULT NULL,
  `item_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT 'default.png',
  `item_owner` int(11) NULL DEFAULT NULL,
  `item_date` datetime NULL DEFAULT curdate(),
  `item_update` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `item_delete` int(11) NULL DEFAULT 0,
  `item_price` float(10, 2) NULL DEFAULT 0.00,
  PRIMARY KEY (`item_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 629 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for drawers_session
-- ----------------------------
DROP TABLE IF EXISTS `drawers_session`;
CREATE TABLE `drawers_session`  (
  `sess_id` int(11) NOT NULL AUTO_INCREMENT,
  `sess_usr` int(255) NULL DEFAULT NULL,
  `sess_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `sess_date` datetime NULL DEFAULT NULL,
  `sess_action` int(255) NULL DEFAULT 0 COMMENT '1 in 2 out',
  PRIMARY KEY (`sess_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 72 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for drawers_usr
-- ----------------------------
DROP TABLE IF EXISTS `drawers_usr`;
CREATE TABLE `drawers_usr`  (
  `usr_id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `usr_lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `usr_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `usr_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT 'images/usr/avatar.png',
  `usr_pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `usr_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish2_ci NULL DEFAULT NULL,
  `usr_timezone` int(11) NOT NULL DEFAULT 0,
  `usr_right` int(255) NULL DEFAULT 2,
  `usr_delete` int(255) NULL DEFAULT 0,
  PRIMARY KEY (`usr_id`) USING BTREE,
  UNIQUE INDEX `usr_email`(`usr_email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_spanish2_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- View structure for total_price
-- ----------------------------
DROP VIEW IF EXISTS `total_price`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `total_price` AS select `drawers_items`.`item_amount` * `drawers_items`.`item_price` AS `total_price`,`drawers_items`.`item_category` AS `category` from `drawers_items` where `drawers_items`.`item_delete` = 0;

-- ----------------------------
-- View structure for total_price_drawer
-- ----------------------------
DROP VIEW IF EXISTS `total_price_drawer`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `total_price_drawer` AS select `drawers_items`.`item_amount` * `drawers_items`.`item_price` AS `total_price`,`drawers_items`.`item_drawer` AS `drawer` from `drawers_items` where `drawers_items`.`item_delete` = 0;

SET FOREIGN_KEY_CHECKS = 1;
