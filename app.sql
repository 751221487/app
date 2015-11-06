-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: 2015-11-06 14:51:09
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
  `join_time` date DEFAULT NULL,
  `job` int(11) DEFAULT '0',
  `encrypt` varchar(6) DEFAULT NULL,
  `lastloginip` varchar(15) DEFAULT NULL,
  `lastlogintime` int(10) unsigned DEFAULT '0',
  `email` varchar(40) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `permission` int(11) NOT NULL COMMENT '权限（1，2）',
  `remark` varchar(100) DEFAULT NULL,
  `realname` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_admin`
--

INSERT INTO `app2_admin` (`userid`, `username`, `password`, `position`, `area`, `join_time`, `job`, `encrypt`, `lastloginip`, `lastlogintime`, `email`, `tel`, `permission`, `remark`, `realname`) VALUES
(1, 'admin', '0c85dd37ceb4d51ea4ed9e78c468ee13', '财务', 0, '2015-10-01', 1, 'MgaxbA', '0.0.0.0', 1446662786, 'admin@admin.com', '123456', 1, 'admin', 'asd'),
(3, '12345', '6bce23e60359eefb359149dbbdc745eb', '总经理', 5, '2015-10-01', 1, 'SQMjgC', '0.0.0.0', 1446204743, 'a@b.c', '123456', 2, 'test', 'test');

-- --------------------------------------------------------

--
-- 表的结构 `app2_area`
--

CREATE TABLE `app2_area` (
  `id` int(11) NOT NULL COMMENT 'PK',
  `name` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '地区名',
  `parentid` int(11) DEFAULT NULL COMMENT '所属地区',
  `display` int(11) DEFAULT NULL COMMENT '是否显示'
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `app2_area`
--

INSERT INTO `app2_area` (`id`, `name`, `parentid`, `display`) VALUES
(4, '江苏省', 0, 1),
(5, '南京市', 4, 1),
(6, '玄武区', 5, 1),
(7, '秦淮区', 5, 1),
(8, 'asd', 7, 1);

-- --------------------------------------------------------

--
-- 表的结构 `app2_contract`
--

CREATE TABLE `app2_contract` (
  `id` int(11) NOT NULL COMMENT 'PK',
  `code` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '合同编号',
  `product` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '产品种类',
  `money` int(11) DEFAULT NULL COMMENT '投资金额',
  `create_date` date DEFAULT NULL COMMENT '签单日',
  `income_rate` int(11) DEFAULT NULL COMMENT '收益率',
  `time_limit` int(11) DEFAULT NULL COMMENT '投资期限',
  `income` int(11) DEFAULT NULL COMMENT '每期收益',
  `income_cycle` int(11) DEFAULT NULL COMMENT '收益支付周期',
  `time_finish` date DEFAULT NULL COMMENT '满期日',
  `customer` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '客户',
  `idcard` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '身份证',
  `bankcard` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '银行卡',
  `contract_file` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '合同电子档',
  `user` int(11) NOT NULL COMMENT '理财顾问',
  `remark` varchar(500) CHARACTER SET utf8 DEFAULT NULL COMMENT '备注',
  `arrive_date` date DEFAULT NULL COMMENT '到账日',
  `total_income` int(11) DEFAULT NULL COMMENT '总收益',
  `create_user` int(11) DEFAULT NULL COMMENT '经办（财务）',
  `is_float` int(11) DEFAULT NULL COMMENT '是否浮动',
  `float_income` int(11) DEFAULT NULL COMMENT '浮动收益',
  `idnumber` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '身份证号码',
  `banknumber` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '银行卡号码',
  `bank` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '开户行',
  `emerge_person` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '紧急联系人',
  `emerge_tel` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '紧急联系人电话'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='合同';

--
-- 转存表中的数据 `app2_contract`
--

INSERT INTO `app2_contract` (`id`, `code`, `product`, `money`, `create_date`, `income_rate`, `time_limit`, `income`, `income_cycle`, `time_finish`, `customer`, `idcard`, `bankcard`, `contract_file`, `user`, `remark`, `arrive_date`, `total_income`, `create_user`, `is_float`, `float_income`, `idnumber`, `banknumber`, `bank`, `emerge_person`, `emerge_tel`) VALUES
(1, '123456', '1', 2, '2015-10-05', 1, 2, 3, 4, '2015-10-08', '1', '2015-11-05/563a66b6a2724.jpg', '2015-10-30/563262a9b7435.jpg', '2015-10-30/563262a9b7a8a.jpg', 1, 'qwfesdvfda', '2015-10-16', 1, NULL, 0, 1, '123123123', '123123123123', 'asdasdaa', 'asfasdas', '314325435q');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `app2_job`
--

INSERT INTO `app2_job` (`id`, `name`, `description`, `target`, `time`, `permission`, `display`) VALUES
(1, '新人', 'qweqweqwe', '2', 30, 1, 1),
(2, '正式员工', 'qweefds', '3', 60, NULL, 1);

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
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_member`
--

INSERT INTO `app2_member` (`memberid`, `idcard`, `gender`, `age`, `province`, `place`, `birthday`, `education`, `job`, `income`, `address`, `corp`, `origin`, `name`, `tel`, `user`, `create_time`, `status`, `department`, `remark`) VALUES
(1, '410303199312061017', 1, 1993, '江苏', '苏州', '1993-12-06', '本科', '计算机', 100, 'asdasdasdasdasd', 'asdasd', '广告', '郝杰', '123456789', 1, '2015-10-24', '准客户', 0, 'asdasdasd');

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
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_menu`
--

INSERT INTO `app2_menu` (`id`, `name`, `parentid`, `c`, `a`, `data`, `listorder`, `display`) VALUES
(1, '我的面板', 0, 'Admin', 'top', '', 1, '1'),
(2, '系统管理', 0, 'System', 'top', '', 2, '1'),
(3, '客户信息管理', 0, 'Content', 'top', '', 3, '1'),
(6, '消息中心', 1, 'Admin', 'userLeft', '', 0, '1'),
(7, '登录日志', 6, 'Admin', 'loginLog', '', 1, '1'),
(8, '删除登录日志', 7, 'Admin', 'loginLogDelete', '', 1, '1'),
(9, '系统设置', 2, 'System', 'settingLeft', '', 1, '1'),
(80, '客户管理', 3, 'Member', 'memberList', '', 0, '1'),
(11, '菜单设置', 9, 'System', 'menuList', '', 2, '1'),
(12, '查看列表', 11, 'System', 'menuViewList', '', 0, '1'),
(13, '添加菜单', 11, 'System', 'menuAdd', '', 0, '1'),
(14, '修改菜单', 11, 'System', 'menuEdit', '', 0, '1'),
(15, '删除菜单', 11, 'System', 'menuDelete', '', 0, '1'),
(16, '菜单排序', 11, 'System', 'menuOrder', '', 0, '1'),
(17, '菜单导出', 11, 'System', 'menuExport', '', 0, '1'),
(18, '菜单导入', 11, 'System', 'menuImport', '', 0, '1'),
(19, '员工设置', 2, 'Admin', 'left', '', 2, '1'),
(20, '员工管理', 19, 'Admin', 'memberList', '', 1, '1'),
(21, '查看列表', 20, 'Admin', 'memberViewList', '', 0, '1'),
(22, '添加用户', 20, 'Admin', 'memberAdd', '', 0, '1'),
(23, '编辑用户', 20, 'Admin', 'memberEdit', '', 0, '1'),
(24, '删除用户', 20, 'Admin', 'memberDelete', '', 0, '1'),
(73, '权限管理', 2, 'Admin', 'permissionLeft', '', 0, '1'),
(26, '查看列表', 25, 'Admin', 'roleViewList', '', 0, '1'),
(27, '添加角色', 25, 'Admin', 'roleAdd', '', 0, '1'),
(28, '编辑角色', 25, 'Admin', 'roleEdit', '', 0, '1'),
(29, '删除角色', 25, 'Admin', 'roleDelete', '', 0, '1'),
(30, '角色排序', 25, 'Admin', 'roleOrder', '', 0, '1'),
(31, '权限设置', 25, 'Admin', 'rolePermission', '', 0, '1'),
(32, '栏目权限', 25, 'Admin', 'roleCategory', '', 0, '1'),
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
(61, '重置密码', 20, 'Admin', 'memberResetPassword', '', 0, '1'),
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
(81, '合同管理', 3, 'Contract', 'contractList', '', 0, '1'),
(82, '客户列表', 80, 'Member', 'memberList', '', 0, '1'),
(83, '合同列表', 81, 'Contract', 'contractList', '', 0, '1'),
(84, '站内消息', 6, 'Message', 'messageList', '', 0, '1');

-- --------------------------------------------------------

--
-- 表的结构 `app2_message`
--

CREATE TABLE `app2_message` (
  `id` int(11) NOT NULL,
  `user` int(11) DEFAULT NULL,
  `content` varchar(300) CHARACTER SET utf8 DEFAULT NULL,
  `time` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 `app2_message`
--

INSERT INTO `app2_message` (`id`, `user`, `content`, `time`) VALUES
(1, 1, 'srdgfadsfdgres', '2015-11-06 11:26:18');

-- --------------------------------------------------------

--
-- 表的结构 `app2_setting`
--

CREATE TABLE `app2_setting` (
  `key` varchar(50) NOT NULL,
  `value` varchar(5000) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  MODIFY `userid` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `app2_area`
--
ALTER TABLE `app2_area`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `app2_contract`
--
ALTER TABLE `app2_contract`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'PK',AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `app2_job`
--
ALTER TABLE `app2_job`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `app2_log`
--
ALTER TABLE `app2_log`
  MODIFY `logid` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app2_member`
--
ALTER TABLE `app2_member`
  MODIFY `memberid` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `app2_menu`
--
ALTER TABLE `app2_menu`
  MODIFY `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=85;
--
-- AUTO_INCREMENT for table `app2_message`
--
ALTER TABLE `app2_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;