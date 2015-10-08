-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: 2015-10-08 15:19:57
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
  `roleid` smallint(5) DEFAULT '0',
  `encrypt` varchar(6) DEFAULT NULL,
  `lastloginip` varchar(15) DEFAULT NULL,
  `lastlogintime` int(10) unsigned DEFAULT '0',
  `email` varchar(40) DEFAULT NULL,
  `realname` varchar(50) NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_admin`
--

INSERT INTO `app2_admin` (`userid`, `username`, `password`, `roleid`, `encrypt`, `lastloginip`, `lastlogintime`, `email`, `realname`) VALUES
(1, 'admin', 'f8b1ca0f80ecd19fba014a89ca59388c', 1, 'vVlrUF', '::1', 1444306120, 'admin@admin.com', '');

-- --------------------------------------------------------

--
-- 表的结构 `app2_admin_log`
--

CREATE TABLE `app2_admin_log` (
  `logid` int(10) unsigned NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL,
  `httpuseragent` text NOT NULL,
  `sessionid` varchar(30) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(30) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_admin_log`
--

INSERT INTO `app2_admin_log` (`logid`, `userid`, `username`, `httpuseragent`, `sessionid`, `ip`, `time`, `type`) VALUES
(1, 1, 'admin', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', 'c9a896007a23332dc66551d407d67e', '0.0.0.0', '2015-10-08 19:38:36', 'login'),
(2, 1, 'admin', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', 'c9a896007a23332dc66551d407d67e', '0.0.0.0', '2015-10-08 19:52:01', 'login'),
(3, 1, 'admin', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', 'c9a896007a23332dc66551d407d67e', '0.0.0.0', '2015-10-08 19:52:49', 'login'),
(4, 1, 'admin', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', 'c9a896007a23332dc66551d407d67e', '0.0.0.0', '2015-10-08 19:54:49', 'login'),
(5, 1, 'admin', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', 'c9a896007a23332dc66551d407d67e', '0.0.0.0', '2015-10-08 20:02:13', 'login'),
(6, 1, 'admin', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', 'c9a896007a23332dc66551d407d67e', '0.0.0.0', '2015-10-08 20:07:35', 'login'),
(7, 1, 'admin', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', 'c9a896007a23332dc66551d407d67e', '0.0.0.0', '2015-10-08 20:07:41', 'login'),
(8, 1, 'admin', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36', 'c9a896007a23332dc66551d407d67e', '::1', '2015-10-08 20:08:40', 'login');

-- --------------------------------------------------------

--
-- 表的结构 `app2_admin_role`
--

CREATE TABLE `app2_admin_role` (
  `roleid` tinyint(3) unsigned NOT NULL,
  `rolename` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_admin_role`
--

INSERT INTO `app2_admin_role` (`roleid`, `rolename`, `description`, `listorder`, `disabled`) VALUES
(1, '超级管理员', '超级管理员', 99, 0),
(2, '普通用户', '普通用户', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `app2_admin_role_priv`
--

CREATE TABLE `app2_admin_role_priv` (
  `roleid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `c` varchar(20) NOT NULL,
  `a` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_article`
--

CREATE TABLE `app2_article` (
  `id` int(11) unsigned NOT NULL,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uuid` varchar(40) NOT NULL,
  `title` varchar(80) NOT NULL DEFAULT '',
  `keywords` varchar(40) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `thumb` varchar(100) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `islink` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `istop` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `author` varchar(20) NOT NULL,
  `addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_category`
--

CREATE TABLE `app2_category` (
  `catid` smallint(5) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parentid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `catname` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `model` varchar(50) NOT NULL DEFAULT 'article' COMMENT '模型',
  `setting` text,
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用',
  `ismenu` tinyint(1) NOT NULL DEFAULT '1' COMMENT '前台显示'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_category_priv`
--

CREATE TABLE `app2_category_priv` (
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `roleid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_admin` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `action` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_email`
--

CREATE TABLE `app2_email` (
  `id` smallint(4) unsigned NOT NULL,
  `code` varchar(40) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `addtime` int(10) DEFAULT '0',
  `edittime` int(10) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `username` varchar(30) NOT NULL COMMENT '帐号',
  `head` varchar(255) DEFAULT NULL COMMENT '头像',
  `nick` varchar(50) DEFAULT NULL COMMENT '昵称',
  `gender` tinyint(1) DEFAULT '0' COMMENT '0:保密,1:男,2:女',
  `password` varchar(32) NOT NULL,
  `encrypt` varchar(6) NOT NULL,
  `typeid` smallint(5) DEFAULT '0',
  `status` tinyint(1) DEFAULT '0' COMMENT '0:待认证1:已认证',
  `remark` text COMMENT '备注',
  `lastloginip` varchar(15) DEFAULT NULL,
  `lastlogintime` int(10) DEFAULT '0',
  `regip` varchar(15) NOT NULL,
  `regtime` int(10) NOT NULL DEFAULT '0' COMMENT '注册时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_member_oauth`
--

CREATE TABLE `app2_member_oauth` (
  `id` int(11) unsigned NOT NULL,
  `memberid` int(11) NOT NULL COMMENT '本站用户id',
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT '唯一标识',
  `email` varchar(40) DEFAULT NULL COMMENT '邮箱',
  `nick` varchar(80) DEFAULT NULL COMMENT '昵称',
  `head` varchar(255) DEFAULT NULL COMMENT '用户图像',
  `gender` varchar(10) DEFAULT NULL COMMENT '性别',
  `link` varchar(255) DEFAULT NULL COMMENT '用户链接',
  `type` varchar(50) NOT NULL DEFAULT '' COMMENT '类型',
  `addtime` int(10) DEFAULT '0' COMMENT '添加时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_member_type`
--

CREATE TABLE `app2_member_type` (
  `typeid` tinyint(3) unsigned NOT NULL,
  `typename` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_member_type`
--

INSERT INTO `app2_member_type` (`typeid`, `typename`, `description`, `listorder`, `disabled`) VALUES
(1, '普通用户', '本地用户', 0, 0);

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
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `app2_menu`
--

INSERT INTO `app2_menu` (`id`, `name`, `parentid`, `c`, `a`, `data`, `listorder`, `display`) VALUES
(1, '我的面板', 0, 'Admin', 'top', '', 1, '1'),
(2, '用户管理', 0, 'User', 'top', '', 2, '1'),
(3, '信息管理', 0, 'Information', 'top', '', 3, '1'),
(6, '安全记录', 1, 'Admin', 'userLeft', '', 0, '1'),
(72, '登陆日志', 6, 'Admin', 'LoginLog', '', 1, '1');

-- --------------------------------------------------------

--
-- 表的结构 `app2_page`
--

CREATE TABLE `app2_page` (
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `uuid` varchar(40) NOT NULL,
  `title` varchar(160) NOT NULL,
  `keywords` varchar(40) NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_setting`
--

CREATE TABLE `app2_setting` (
  `key` varchar(50) NOT NULL,
  `value` varchar(5000) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `app2_times`
--

CREATE TABLE `app2_times` (
  `username` char(40) NOT NULL,
  `ip` char(15) NOT NULL,
  `logintime` int(10) unsigned NOT NULL DEFAULT '0',
  `isadmin` tinyint(1) NOT NULL DEFAULT '0',
  `times` tinyint(1) unsigned NOT NULL DEFAULT '0'
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
-- Indexes for table `app2_admin_log`
--
ALTER TABLE `app2_admin_log`
  ADD PRIMARY KEY (`logid`),
  ADD KEY `username` (`username`);

--
-- Indexes for table `app2_admin_role`
--
ALTER TABLE `app2_admin_role`
  ADD PRIMARY KEY (`roleid`),
  ADD KEY `listorder` (`listorder`),
  ADD KEY `disabled` (`disabled`);

--
-- Indexes for table `app2_admin_role_priv`
--
ALTER TABLE `app2_admin_role_priv`
  ADD KEY `roleid` (`roleid`,`c`,`a`);

--
-- Indexes for table `app2_article`
--
ALTER TABLE `app2_article`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catid` (`catid`,`status`,`id`);

--
-- Indexes for table `app2_category`
--
ALTER TABLE `app2_category`
  ADD PRIMARY KEY (`catid`);

--
-- Indexes for table `app2_category_priv`
--
ALTER TABLE `app2_category_priv`
  ADD KEY `catid` (`catid`,`roleid`,`is_admin`,`action`);

--
-- Indexes for table `app2_email`
--
ALTER TABLE `app2_email`
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
  ADD KEY `username` (`username`);

--
-- Indexes for table `app2_member_oauth`
--
ALTER TABLE `app2_member_oauth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app2_member_type`
--
ALTER TABLE `app2_member_type`
  ADD PRIMARY KEY (`typeid`);

--
-- Indexes for table `app2_menu`
--
ALTER TABLE `app2_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `listorder` (`listorder`),
  ADD KEY `parentid` (`parentid`),
  ADD KEY `module` (`c`,`a`);

--
-- Indexes for table `app2_page`
--
ALTER TABLE `app2_page`
  ADD KEY `catid` (`catid`);

--
-- Indexes for table `app2_setting`
--
ALTER TABLE `app2_setting`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `app2_times`
--
ALTER TABLE `app2_times`
  ADD PRIMARY KEY (`username`,`isadmin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app2_admin`
--
ALTER TABLE `app2_admin`
  MODIFY `userid` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `app2_admin_log`
--
ALTER TABLE `app2_admin_log`
  MODIFY `logid` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `app2_admin_role`
--
ALTER TABLE `app2_admin_role`
  MODIFY `roleid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `app2_article`
--
ALTER TABLE `app2_article`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app2_category`
--
ALTER TABLE `app2_category`
  MODIFY `catid` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app2_email`
--
ALTER TABLE `app2_email`
  MODIFY `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app2_log`
--
ALTER TABLE `app2_log`
  MODIFY `logid` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app2_member`
--
ALTER TABLE `app2_member`
  MODIFY `memberid` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app2_member_oauth`
--
ALTER TABLE `app2_member_oauth`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `app2_member_type`
--
ALTER TABLE `app2_member_type`
  MODIFY `typeid` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `app2_menu`
--
ALTER TABLE `app2_menu`
  MODIFY `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=73;
