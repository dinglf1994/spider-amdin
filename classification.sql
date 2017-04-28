-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017-04-28 18:49:57
-- 服务器版本： 5.7.17-log
-- PHP Version: 7.0.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `classification`
--

-- --------------------------------------------------------

--
-- 表的结构 `cs_classify`
--

CREATE TABLE `cs_classify` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `text_addr` varchar(32) NOT NULL,
  `text_url` varchar(32) NOT NULL,
  `come_from` int(32) NOT NULL,
  `title` varchar(64) NOT NULL,
  `content` text NOT NULL,
  `push_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` int(2) NOT NULL,
  `belong_food` int(11) NOT NULL DEFAULT '0',
  `belong_wine` int(11) NOT NULL DEFAULT '0',
  `belong_meat` int(11) NOT NULL DEFAULT '0',
  `belong_milk` int(11) NOT NULL DEFAULT '0',
  `belong_others` int(11) NOT NULL DEFAULT '0',
  `need_classify` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cs_user`
--

CREATE TABLE `cs_user` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `name` varchar(14) NOT NULL COMMENT '学生姓名',
  `gender` int(1) NOT NULL COMMENT '性别',
  `number` varchar(12) NOT NULL COMMENT '学号',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `last_login_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后登录时间',
  `login_times` int(11) NOT NULL DEFAULT '0' COMMENT '登录次数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学生登录信息表';

-- --------------------------------------------------------

--
-- 表的结构 `cs_user_classify`
--

CREATE TABLE `cs_user_classify` (
  `id` int(11) NOT NULL COMMENT '自增ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `classify_id` int(11) NOT NULL COMMENT '文本ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cs_classify`
--
ALTER TABLE `cs_classify`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cs_user`
--
ALTER TABLE `cs_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_number` (`number`);

--
-- Indexes for table `cs_user_classify`
--
ALTER TABLE `cs_user_classify`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `cs_classify`
--
ALTER TABLE `cs_classify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
--
-- 使用表AUTO_INCREMENT `cs_user`
--
ALTER TABLE `cs_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
--
-- 使用表AUTO_INCREMENT `cs_user_classify`
--
ALTER TABLE `cs_user_classify`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID';COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
