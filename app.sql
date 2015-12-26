-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: 2015-12-26 05:57:21
-- 服务器版本： 5.5.42
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `app`
--

-- --------------------------------------------------------

--
-- 表的结构 `app2_admin`
--

CREATE TABLE `app2_admin` (
  `userid` mediumint(6) unsigned NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `position` varchar(30) DEFAULT NULL,
  `area` int(11) DEFAULT NULL,
  `target` int(11) DEFAULT NULL,
  `status_change_time` date DEFAULT NULL,
  `join_time` date DEFAULT NULL,
  `left_time` date DEFAULT NULL,
  `job` int(11) DEFAULT '0',
  `target_time` date NOT NULL,
  `encrypt` varchar(6) DEFAULT NULL,
  `lastloginip` varchar(15) DEFAULT NULL,
  `lastlogintime` int(10) unsigned DEFAULT '0',
  `email` varchar(40) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `permission` int(11) NOT NULL COMMENT '权限（1，2）',
  `remark` varchar(100) DEFAULT NULL,
  `realname` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_admin`
--

INSERT INTO `app2_admin` (`userid`, `username`, `password`, `position`, `area`, `target`, `status_change_time`, `join_time`, `left_time`, `job`, `target_time`, `encrypt`, `lastloginip`, `lastlogintime`, `email`, `tel`, `permission`, `remark`, `realname`) VALUES
(1, 'admin', '2297df0c385f306c883bced3c23ed1e8', '超级管理员', 0, 30000, '2015-11-30', '2015-10-01', '2017-12-08', 2, '2016-01-29', 'TIMCDe', '0.0.0.0', 1451039481, '', '123456', 1, 'admin', 'asd'),
(3, '12345', '6bce23e60359eefb359149dbbdc745eb', '财务', 0, 10000, '2015-10-04', '2015-10-01', NULL, 3, '2015-12-16', 'SQMjgC', '0.0.0.0', 1450265238, 'asdasd@fes.fes', '123456', 1, 'test', 'test'),
(5, '1234', '916d3a72879e9f5a8ea28fd6fab36d26', '超级管理员', 9, 20000, '2015-09-07', '2015-11-11', NULL, 1, '2015-12-28', 'oHlctd', NULL, 0, '', '123', 1, 'asd', 'asd');

-- --------------------------------------------------------

--
-- 表的结构 `app2_area`
--

CREATE TABLE `app2_area` (
  `id` int(11) NOT NULL COMMENT 'PK',
  `name` varchar(50) DEFAULT NULL COMMENT '地区名',
  `parentid` int(11) DEFAULT NULL COMMENT '所属地区',
  `display` int(11) DEFAULT NULL COMMENT '是否显示'
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_area`
--

INSERT INTO `app2_area` (`id`, `name`, `parentid`, `display`) VALUES
(4, '江苏省', 0, 1),
(5, '南京市', 4, 1),
(6, '玄武区', 5, 1),
(7, '秦淮区', 5, 1),
(8, 'asd', 7, 1),
(9, '上海', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `app2_contract`
--

CREATE TABLE `app2_contract` (
  `id` int(11) NOT NULL COMMENT 'PK',
  `code` varchar(50) DEFAULT NULL COMMENT '合同编号',
  `product` varchar(30) DEFAULT NULL COMMENT '产品种类',
  `money` int(11) DEFAULT NULL COMMENT '投资金额',
  `create_date` date DEFAULT NULL COMMENT '签单日',
  `income_rate` int(11) DEFAULT NULL COMMENT '收益率',
  `time_limit` int(11) DEFAULT NULL COMMENT '投资期限',
  `income` int(11) DEFAULT NULL COMMENT '每期收益',
  `income_cycle` int(11) DEFAULT NULL COMMENT '收益支付周期',
  `paid_finish` int(11) NOT NULL DEFAULT '0' COMMENT '已付期数',
  `time_finish` date DEFAULT NULL COMMENT '满期日',
  `customer` int(11) DEFAULT NULL COMMENT '客户',
  `idcard` varchar(100) DEFAULT NULL COMMENT '身份证',
  `bankcard` varchar(100) DEFAULT NULL COMMENT '银行卡',
  `contract_file` varchar(100) DEFAULT NULL COMMENT '合同电子档',
  `user` int(11) NOT NULL COMMENT '理财顾问',
  `area` int(11) NOT NULL,
  `remark` varchar(500) DEFAULT NULL COMMENT '备注',
  `arrive_date` date DEFAULT NULL COMMENT '到账日',
  `total_income` int(11) DEFAULT NULL COMMENT '总收益',
  `create_user` int(11) DEFAULT NULL COMMENT '经办（财务）',
  `is_float` int(11) DEFAULT NULL COMMENT '是否浮动',
  `float_income` int(11) DEFAULT NULL COMMENT '浮动收益',
  `idnumber` varchar(30) DEFAULT NULL COMMENT '身份证号码',
  `banknumber` varchar(30) DEFAULT NULL COMMENT '银行卡号码',
  `bank` varchar(30) DEFAULT NULL COMMENT '开户行',
  `emerge_person` varchar(30) DEFAULT NULL COMMENT '紧急联系人',
  `emerge_tel` varchar(30) DEFAULT NULL COMMENT '紧急联系人电话'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='合同';

--
-- 转存表中的数据 `app2_contract`
--

INSERT INTO `app2_contract` (`id`, `code`, `product`, `money`, `create_date`, `income_rate`, `time_limit`, `income`, `income_cycle`, `paid_finish`, `time_finish`, `customer`, `idcard`, `bankcard`, `contract_file`, `user`, `area`, `remark`, `arrive_date`, `total_income`, `create_user`, `is_float`, `float_income`, `idnumber`, `banknumber`, `bank`, `emerge_person`, `emerge_tel`) VALUES
(1, '123123', '2', 10000, '2015-09-13', 10, 2, 3, 1, 5, '2015-12-20', 1, '2015-11-05/563a66b6a2724.jpg', '2015-10-30/563262a9b7435.jpg', '2015-10-30/563262a9b7a8a.jpg', 3, 0, 'qwfesdvfda', '2015-10-16', 10000, 3, 0, 1, '123123123', '123123123123', 'asdasdaa', 'asfasdas', '314325435q'),
(2, '111111', '3', 20000, '2015-09-01', 1, 1, 1, 1, 0, '2016-01-05', 1, '', '', '', 5, 9, '1', '2015-11-04', 1, 3, 1, 10000, '1', '1', '1', '1', '1');

-- --------------------------------------------------------

--
-- 表的结构 `app2_contract_log`
--

CREATE TABLE `app2_contract_log` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `operate` int(11) NOT NULL,
  `contract` int(11) NOT NULL,
  `time` datetime NOT NULL,
  `ip` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `app2_contract_log`
--

INSERT INTO `app2_contract_log` (`id`, `user`, `operate`, `contract`, `time`, `ip`) VALUES
(1, 3, 1, 1, '2015-11-13 06:53:52', 0),
(2, 3, 1, 2, '2015-11-13 19:23:08', 0),
(3, 3, 2, 2, '2015-11-13 20:08:06', 0),
(4, 3, 2, 1, '2015-11-13 20:10:09', 0),
(5, 3, 2, 2, '2015-11-13 20:10:55', 0),
(6, 3, 2, 1, '2015-11-13 20:11:25', 0),
(7, 3, 2, 2, '2015-11-13 20:12:12', 0),
(8, 3, 2, 1, '2015-11-13 20:12:20', 0),
(9, 3, 2, 1, '2015-11-14 17:23:29', 0),
(10, 3, 2, 2, '2015-11-14 17:23:38', 0),
(11, 3, 2, 1, '2015-12-16 02:13:05', 0),
(12, 3, 2, 2, '2015-12-16 02:13:15', 0),
(13, 3, 2, 2, '2015-12-16 02:15:20', 0),
(14, 3, 2, 2, '2015-12-16 02:15:25', 0);

-- --------------------------------------------------------

--
-- 表的结构 `app2_job`
--

CREATE TABLE `app2_job` (
  `id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `description` varchar(100) CHARACTER SET utf8 NOT NULL,
  `target` varchar(60) CHARACTER SET utf8 DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `permission` int(11) DEFAULT NULL,
  `display` int(11) DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `app2_job`
--

INSERT INTO `app2_job` (`id`, `name`, `description`, `target`, `time`, `permission`, `display`) VALUES
(1, '新人', 'qweqweqwe', '2', 30, 1, 1),
(2, '正式员工', 'qweefds', '3', 60, NULL, 1),
(3, '无', '', '0', 0, NULL, 1);

-- --------------------------------------------------------

--
-- 表的结构 `app2_log`
--

CREATE TABLE `app2_log` (
  `logid` int(10) unsigned NOT NULL,
  `controller` varchar(15) NOT NULL,
  `action` varchar(20) NOT NULL,
  `querystring` mediumtext NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_member`
--

CREATE TABLE `app2_member` (
  `memberid` int(11) unsigned NOT NULL,
  `idcard` varchar(30) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `age` int(11) DEFAULT NULL COMMENT '年龄',
  `province` varchar(20) DEFAULT NULL,
  `place` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `education` varchar(20) DEFAULT NULL,
  `job` varchar(100) NOT NULL COMMENT '行业',
  `income` int(11) NOT NULL COMMENT '收入',
  `address` varchar(100) NOT NULL COMMENT '地址',
  `corp` varchar(30) NOT NULL COMMENT '单位',
  `origin` varchar(255) DEFAULT NULL COMMENT '来源',
  `name` varchar(50) DEFAULT NULL COMMENT '姓名',
  `tel` varchar(20) DEFAULT NULL COMMENT '电话',
  `user` int(11) DEFAULT NULL COMMENT '负责人',
  `create_time` date DEFAULT NULL COMMENT '创建时间',
  `status` varchar(20) DEFAULT NULL COMMENT '状态',
  `department` int(11) DEFAULT NULL COMMENT '负责部门',
  `remark` text COMMENT '备注'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_member`
--

INSERT INTO `app2_member` (`memberid`, `idcard`, `gender`, `age`, `province`, `place`, `birthday`, `education`, `job`, `income`, `address`, `corp`, `origin`, `name`, `tel`, `user`, `create_time`, `status`, `department`, `remark`) VALUES
(1, '410303199312061017', 1, 1993, '江苏', '苏州', '1992-11-13', '本科', '计算机', 100, 'asdasdasdasdasd', 'asdasd', '广告', '郝杰', '123456789', 1, '2015-10-24', '新客户', 0, 'asdasdasd'),
(2, '1', 1, 2011, '江苏', '南京', '2011-11-13', '1', '1', 1, '1', '1', '1', 'a', '1', 1, '2015-11-13', '准客户', 0, '1');

-- --------------------------------------------------------

--
-- 表的结构 `app2_menu`
--

CREATE TABLE `app2_menu` (
  `id` smallint(6) unsigned NOT NULL,
  `name` varchar(40) NOT NULL DEFAULT '',
  `parentid` smallint(6) NOT NULL DEFAULT '0',
  `c` varchar(20) NOT NULL DEFAULT '',
  `a` varchar(20) NOT NULL DEFAULT '',
  `data` varchar(255) NOT NULL DEFAULT '',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0',
  `display` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_menu`
--

INSERT INTO `app2_menu` (`id`, `name`, `parentid`, `c`, `a`, `data`, `listorder`, `display`) VALUES
(1, '我的面板', 0, 'Admin', 'top', '', 1, '1'),
(2, '系统管理', 0, 'System', 'top', '', 2, '1'),
(3, '合同管理', 0, 'Content', 'top', '', 4, '1'),
(6, '消息中心', 1, 'Admin', 'userLeft', '', 0, '1'),
(91, '产品管理', 0, 'Product', 'a', '', 3, '1'),
(8, '删除登录日志', 7, 'Admin', 'loginLogDelete', '', 1, '1'),
(9, '系统设置', 2, 'System', 'settingLeft', '', 1, '1'),
(80, '客户管理', 0, 'Member', 'memberList', '', 3, '1'),
(11, '菜单设置', 9, 'System', 'menuList', '', 2, '1'),
(12, '查看列表', 11, 'System', 'menuViewList', '', 0, '1'),
(13, '添加菜单', 11, 'System', 'menuAdd', '', 0, '1'),
(14, '修改菜单', 11, 'System', 'menuEdit', '', 0, '1'),
(15, '删除菜单', 11, 'System', 'menuDelete', '', 0, '1'),
(16, '菜单排序', 11, 'System', 'menuOrder', '', 0, '1'),
(17, '菜单导出', 11, 'System', 'menuExport', '', 0, '1'),
(18, '菜单导入', 11, 'System', 'menuImport', '', 0, '1'),
(19, '员工管理', 0, 'Admin', 'left', '', 2, '1'),
(20, '员工信息', 19, 'Admin', 'memberList', '', 1, '1'),
(87, '员工列表', 20, 'Admin', 'memberList', '', 0, '1'),
(88, '客户列表', 82, 'Member', 'memberList', '', 0, '1'),
(89, '统计报表', 0, 'Contract', 'contract', '', 5, '1'),
(73, '权限管理', 2, 'Admin', 'permissionLeft', '', 0, '1'),
(26, '查看列表', 25, 'Admin', 'roleViewList', '', 0, '1'),
(27, '添加角色', 25, 'Admin', 'roleAdd', '', 0, '1'),
(28, '编辑角色', 25, 'Admin', 'roleEdit', '', 0, '1'),
(29, '删除角色', 25, 'Admin', 'roleDelete', '', 0, '1'),
(30, '角色排序', 25, 'Admin', 'roleOrder', '', 0, '1'),
(31, '权限设置', 25, 'Admin', 'rolePermission', '', 0, '1'),
(32, '栏目权限', 25, 'Admin', 'roleCategory', '', 0, '1'),
(90, '合同统计', 89, 'Contract', 'contract', '', 0, '1'),
(34, '日志管理', 33, 'System', 'logList', '', 3, '1'),
(35, '查看列表', 34, 'System', 'logViewList', '', 0, '1'),
(39, '内容管理', 38, 'Content', 'index', '', 0, '1'),
(40, '栏目管理', 38, 'Category', 'categoryList', '', 0, '1'),
(41, '查看列表', 40, 'Category', 'categoryViewList', '', 0, '1'),
(42, '添加栏目', 40, 'Category', 'categoryAdd', '', 0, '1'),
(43, '编辑栏目', 40, 'Category', 'categoryEdit', '', 0, '1'),
(44, '删除栏目', 40, 'Category', 'categoryDelete', '', 0, '1'),
(45, '栏目排序', 40, 'Category', 'categoryOrder', '', 0, '1'),
(46, '栏目导出', 40, 'Category', 'categoryExport', '', 0, '1'),
(47, '栏目导入', 40, 'Category', 'categoryImport', '', 0, '1'),
(49, '会员列表', 48, 'Member', 'memberList', '', 0, '1'),
(50, '会员分类', 48, 'Member', 'typeList', '', 0, '1'),
(51, '查看列表', 49, 'Member', 'memberViewList', '', 0, '1'),
(52, '添加会员', 49, 'Member', 'memberAdd', '', 0, '1'),
(53, '编辑用户', 49, 'Member', 'memberEdit', '', 0, '1'),
(54, '删除用户', 49, 'Member', 'memberDelete', '', 0, '1'),
(55, '用户详情', 49, 'Member', 'memberView', '', 0, '1'),
(56, '添加分类', 50, 'Member', 'typeAdd', '', 0, '1'),
(57, '编辑分类', 50, 'Member', 'typeEdit', '', 0, '1'),
(58, '删除分类', 50, 'Member', 'typeDelete', '', 0, '1'),
(59, '分类排序', 50, 'Member', 'typeOrder', '', 0, '1'),
(60, '查看列表', 50, 'Member', 'typeViewList', '', 0, '1'),
(62, '重置密码', 49, 'Member', 'memberResetPassword', '', 0, '1'),
(64, '模版添加', 63, 'System', 'emailAdd', '', 0, '1'),
(65, '模版编辑', 63, 'System', 'emailEdit', '', 0, '1'),
(66, '模版删除', 63, 'System', 'emailDelete', '', 0, '1'),
(67, '模版列表', 63, 'System', 'emailList', '', 0, '1'),
(68, '上传管理', 38, 'Storage', 'index', '', 0, '1'),
(70, '备份数据库', 69, 'Database', 'exportlist', '', 0, '1'),
(71, '还原数据库', 69, 'Database', 'importlist', '', 0, '1'),
(74, '地区管理', 73, 'Admin', 'areaList', '', 0, '1'),
(79, '状态管理', 73, 'Admin', 'jobList', '', 0, '1'),
(81, '合同信息', 3, 'Contract', 'contractList', '', 0, '1'),
(82, '客户信息', 80, 'Member', 'memberList', '', 0, '1'),
(83, '合同列表', 81, 'Contract', 'contractList', '', 0, '1'),
(84, '站内消息', 6, 'Message', 'messageList', '', 0, '1'),
(85, '统计报表', 90, 'Contract', 'contractStatistics', '', 0, '1'),
(86, '合同日志', 81, 'Contract', 'contractLog', '', 0, '1'),
(92, '产品', 91, 'A', 'a', '', 0, '1'),
(93, '产品列表', 92, 'Product', 'productList', '', 0, '1');

-- --------------------------------------------------------

--
-- 表的结构 `app2_message`
--

CREATE TABLE `app2_message` (
  `id` int(11) NOT NULL,
  `user` int(11) DEFAULT NULL,
  `content` varchar(300) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `type` int(11) NOT NULL,
  `link` int(11) NOT NULL,
  `isread` int(11) NOT NULL DEFAULT '0' COMMENT '是否已读'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_message`
--

INSERT INTO `app2_message` (`id`, `user`, `content`, `time`, `type`, `link`, `isread`) VALUES
(4, 1, '客户郝杰的生日到了', '2015-11-13 00:27:13', 1, 1, 1),
(5, 1, '合同123456需要付款', '2015-11-13 00:51:39', 2, 1, 1),
(6, 1, '客户a的生日到了', '2015-11-13 19:25:29', 1, 2, 1),
(7, 3, '合同123123需要付款', '2015-11-14 17:08:31', 2, 1, 3),
(8, 3, '合同123123需要付款', '2015-12-15 03:54:13', 2, 1, 0),
(9, 3, '合同123123需要付款', '2015-12-15 03:54:13', 2, 2, 0),
(10, 3, '合同123123需要付款', '2015-12-16 02:07:49', 2, 2, 0);

-- --------------------------------------------------------

--
-- 表的结构 `app2_product`
--

CREATE TABLE `app2_product` (
  `id` int(11) unsigned NOT NULL,
  `code` varchar(20) DEFAULT NULL COMMENT '编号',
  `name` varchar(20) DEFAULT NULL COMMENT '名称',
  `money_total` int(11) DEFAULT NULL COMMENT '募集额度',
  `money_finish` int(11) DEFAULT NULL COMMENT '以募集',
  `remark` varchar(100) DEFAULT NULL COMMENT '客户方案'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_product`
--

INSERT INTO `app2_product` (`id`, `code`, `name`, `money_total`, `money_finish`, `remark`) VALUES
(2, '123', 'asvdfda', 100000, 200, 'dafsdbf'),
(3, '12312', 'hregfger', 20000, 1000, 'erdfsdf');

-- --------------------------------------------------------

--
-- 表的结构 `app2_setting`
--

CREATE TABLE `app2_setting` (
  `key` varchar(50) NOT NULL,
  `value` varchar(5000) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_setting`
--

INSERT INTO `app2_setting` (`key`, `value`) VALUES
('rate', '100');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app2_admin`
--
ALTER TABLE `app2_admin`
  ADD PRIMARY KEY (`userid`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `app2_area`
--
ALTER TABLE `app2_area`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app2_contract`
--
ALTER TABLE `app2_contract`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app2_contract_log`
--
ALTER TABLE `app2_contract_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app2_job`
--
ALTER TABLE `app2_job`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app2_log`
--
ALTER TABLE `app2_log`
  ADD PRIMARY KEY (`logid`),
  ADD KEY `module` (`controller`,`action`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `app2_member`
--
ALTER TABLE `app2_member`
  ADD PRIMARY KEY (`memberid`),
  ADD KEY `username` (`corp`);

--
-- Indexes for table `app2_menu`
--
ALTER TABLE `app2_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listorder` (`listorder`),
  ADD KEY `parentid` (`parentid`),
  ADD KEY `module` (`c`,`a`);

--
-- Indexes for table `app2_message`
--
ALTER TABLE `app2_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app2_product`
--
ALTER TABLE `app2_product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app2_setting`
--
ALTER TABLE `app2_setting`
  ADD PRIMARY KEY (`key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app2_admin`
--
ALTER TABLE `app2_admin`
  MODIFY `userid` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `app2_area`
--
ALTER TABLE `app2_area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `app2_contract`
--
ALTER TABLE `app2_contract`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `app2_contract_log`
--
ALTER TABLE `app2_contract_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `app2_job`
--
ALTER TABLE `app2_job`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `app2_log`
--
ALTER TABLE `app2_log`
  MODIFY `logid` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app2_member`
--
ALTER TABLE `app2_member`
  MODIFY `memberid` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `app2_menu`
--
ALTER TABLE `app2_menu`
  MODIFY `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=94;
--
-- AUTO_INCREMENT for table `app2_message`
--
ALTER TABLE `app2_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `app2_product`
--
ALTER TABLE `app2_product`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;