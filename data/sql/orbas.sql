-- phpMyAdmin SQL Dump
-- version 4.2.9
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生時間： 2014 年 10 月 04 日 02:20
-- 伺服器版本: 5.5.23
-- PHP 版本： 5.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `orbasnet_orbas`
--

-- --------------------------------------------------------

--
-- 資料表結構 `board`
--

CREATE TABLE IF NOT EXISTS `board` (
`SN` int(10) unsigned NOT NULL,
  `USER_SN` int(11) NOT NULL,
  `CONTENT` text NOT NULL,
  `DATETIME` datetime NOT NULL,
  `IP` varchar(30) NOT NULL,
  `REPLY_DATETIME` datetime DEFAULT NULL COMMENT '最後回應時間',
  `BOARD_SN` int(11) DEFAULT NULL COMMENT '回應的文章',
  `REMOVED` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=881 DEFAULT CHARSET=utf8 COMMENT='塗鴉牆';

-- --------------------------------------------------------

--
-- 資料表結構 `inform`
--

CREATE TABLE IF NOT EXISTS `inform` (
`SN` int(11) NOT NULL,
  `USER_SN` int(11) NOT NULL COMMENT '被通知的使用者',
  `SEND_USER_SN` int(11) NOT NULL COMMENT '發送訊息的使用者',
  `BOARD_SN` int(11) NOT NULL,
  `CONTENT` varchar(255) NOT NULL COMMENT '通知內容',
  `IS_READ` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否讀取'
) ENGINE=InnoDB AUTO_INCREMENT=6919 DEFAULT CHARSET=utf8 COMMENT='訊息通知';

-- --------------------------------------------------------

--
-- 資料表結構 `like`
--

CREATE TABLE IF NOT EXISTS `like` (
`SN` int(10) unsigned NOT NULL,
  `USER_SN` int(11) NOT NULL,
  `TYPE_KEY` tinyint(4) NOT NULL,
  `BOARD_SN` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=677 DEFAULT CHARSET=utf8 COMMENT='讚與爛';

-- --------------------------------------------------------

--
-- 資料表結構 `mine`
--

CREATE TABLE IF NOT EXISTS `mine` (
  `USER_SN` int(11) NOT NULL,
  `MINE_NO` int(11) NOT NULL COMMENT '礦產編號',
  `DIG_TIME` int(11) NOT NULL COMMENT '挖掘時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='礦產恢復記錄';

-- --------------------------------------------------------

--
-- 資料表結構 `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `ID` varchar(32) NOT NULL,
  `MODIFIED` int(11) NOT NULL,
  `LIFETIME` int(11) NOT NULL,
  `DATA` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`SN` int(11) NOT NULL,
  `EMAIL` varchar(100) NOT NULL COMMENT '帳號',
  `NAME` varchar(100) NOT NULL COMMENT '暱稱',
  `PASSWORD` varchar(100) NOT NULL COMMENT '密碼',
  `AVATAR` varchar(50) DEFAULT NULL COMMENT '頭像檔名',
  `INFORMED_UNREAD` int(11) NOT NULL DEFAULT '0' COMMENT '未讀訊息數量',
  `NOTIFICATION` tinyint(4) NOT NULL DEFAULT '0' COMMENT '桌面通知',
  `LAST_ONLINE_TIME` int(11) DEFAULT NULL COMMENT '最後在線上時間',
  `PH` int(11) DEFAULT NULL COMMENT '剩餘體力',
  `PH_MAX` int(11) DEFAULT NULL COMMENT '體力最大值',
  `PH_RELOAD_LASTTIME` int(11) DEFAULT NULL COMMENT '最後體力補充時間',
  `PH_RELOAD_TIME` int(11) DEFAULT NULL COMMENT '多久增加一次體力',
  `PH_RELOAD_ONCE` int(11) DEFAULT NULL COMMENT '每次增加的體力'
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='使用者';

-- --------------------------------------------------------

--
-- 資料表結構 `user_log`
--

CREATE TABLE IF NOT EXISTS `user_log` (
  `USER_SN` int(11) NOT NULL,
  `CONTENT` varchar(1000) NOT NULL COMMENT '紀錄內容',
  `DATETIME` datetime NOT NULL COMMENT '紀錄時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='使用者紀錄';

-- --------------------------------------------------------

--
-- 資料表結構 `user_mine`
--

CREATE TABLE IF NOT EXISTS `user_mine` (
  `USER_SN` int(11) NOT NULL COMMENT '使用者',
  `MIME_TYPE_KEY` int(11) NOT NULL COMMENT '礦產種類',
  `AMOUNT` int(11) NOT NULL COMMENT '數量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='使用者擁有的礦產'

;

-- --------------------------------------------------------

--
-- 資料表結構 `user_session`
--

CREATE TABLE IF NOT EXISTS `user_session` (
`SN` int(10) unsigned NOT NULL,
  `SESSION_ID` varchar(50) NOT NULL,
  `USER_SN` int(11) NOT NULL,
  `ROLE` varchar(50) NOT NULL,
  `DATETIME` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `board`
--
ALTER TABLE `board`
 ADD PRIMARY KEY (`SN`);

--
-- 資料表索引 `inform`
--
ALTER TABLE `inform`
 ADD PRIMARY KEY (`SN`);

--
-- 資料表索引 `like`
--
ALTER TABLE `like`
 ADD PRIMARY KEY (`SN`), ADD UNIQUE KEY `USER_SN` (`USER_SN`,`TYPE_KEY`,`BOARD_SN`);

--
-- 資料表索引 `session`
--
ALTER TABLE `session`
 ADD PRIMARY KEY (`ID`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`SN`);

--
-- 資料表索引 `user_session`
--
ALTER TABLE `user_session`
 ADD PRIMARY KEY (`SN`), ADD UNIQUE KEY `SESSION_ID` (`SESSION_ID`,`USER_SN`,`ROLE`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `board`
--
ALTER TABLE `board`
MODIFY `SN` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=881;
--
-- 使用資料表 AUTO_INCREMENT `inform`
--
ALTER TABLE `inform`
MODIFY `SN` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6919;
--
-- 使用資料表 AUTO_INCREMENT `like`
--
ALTER TABLE `like`
MODIFY `SN` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=677;
--
-- 使用資料表 AUTO_INCREMENT `user`
--
ALTER TABLE `user`
MODIFY `SN` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=14;
--
-- 使用資料表 AUTO_INCREMENT `user_session`
--
ALTER TABLE `user_session`
MODIFY `SN` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=32;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
