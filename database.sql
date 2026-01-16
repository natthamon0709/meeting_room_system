/*
 Navicat Premium Data Transfer

 Source Server         : MAC_ME
 Source Server Type    : MySQL
 Source Server Version : 100428
 Source Host           : localhost:3306
 Source Schema         : meeting_room

 Target Server Type    : MySQL
 Target Server Version : 100428
 File Encoding         : 65001

 Date: 16/01/2026 09:17:26
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bookings
-- ----------------------------
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `requester_name` varchar(255) NOT NULL,
  `department` varchar(100) NOT NULL,
  `objective` text NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of bookings
-- ----------------------------
BEGIN;
INSERT INTO `bookings` (`id`, `room_id`, `requester_name`, `department`, `objective`, `start_time`, `end_time`, `created_at`) VALUES (1, 1, 'ดหดหก', 'ฝ่ายบริหารงานบุคคล', 'ดกหดกหด', '2026-01-04 20:59:00', '2026-01-04 20:59:00', '2026-01-04 20:59:28');
INSERT INTO `bookings` (`id`, `room_id`, `requester_name`, `department`, `objective`, `start_time`, `end_time`, `created_at`) VALUES (2, 1, 'ดหกดกห', 'ฝ่ายบริหารงานบุคคล', 'ดหดกหด', '2026-01-04 21:00:00', '2026-01-04 21:00:00', '2026-01-04 21:00:08');
INSERT INTO `bookings` (`id`, `room_id`, `requester_name`, `department`, `objective`, `start_time`, `end_time`, `created_at`) VALUES (3, 1, 'ทดสอบ', 'ฝ่ายบริหารทั่วไป', '่าส่าส', '2026-01-04 21:04:00', '2026-01-04 22:04:00', '2026-01-04 21:04:15');
INSERT INTO `bookings` (`id`, `room_id`, `requester_name`, `department`, `objective`, `start_time`, `end_time`, `created_at`) VALUES (4, 1, 'ทดสอบ', 'ฝ่ายแผนงานและงบประมาณ', '่าส่าส่าส่สาสา่', '2026-01-04 21:05:00', '2026-01-04 23:04:00', '2026-01-04 21:04:36');
INSERT INTO `bookings` (`id`, `room_id`, `requester_name`, `department`, `objective`, `start_time`, `end_time`, `created_at`) VALUES (5, 1, 'ดกหดห', 'ฝ่ายบริหารบุคคล', 'ดดไหกด', '2026-01-04 00:06:00', '2026-01-04 02:06:00', '2026-01-04 21:06:36');
INSERT INTO `bookings` (`id`, `room_id`, `requester_name`, `department`, `objective`, `start_time`, `end_time`, `created_at`) VALUES (6, 1, 'ดหกดหกด', '', 'ดกหดกหด', '2026-01-05 21:45:00', '2026-01-06 21:45:00', '2026-01-04 21:45:30');
COMMIT;

-- ----------------------------
-- Table structure for rooms
-- ----------------------------
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_name` varchar(100) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of rooms
-- ----------------------------
BEGIN;
INSERT INTO `rooms` (`room_id`, `room_name`, `capacity`, `location`) VALUES (1, 'ห้องประชุมวิชาการ 1', 30, 'อาคาร 1 ชั้น 2');
INSERT INTO `rooms` (`room_id`, `room_name`, `capacity`, `location`) VALUES (2, 'ห้องประชุมพูนสุข', 100, 'อาคาร 3 ชั้น 1');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `role`) VALUES (2, 'admin', 'admin', '1234', 'admin');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
