/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.7.14 : Database - gamecard_admin
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`gamecard_admin` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `gamecard_admin`;

/*Table structure for table `admin_apkrecord` */

DROP TABLE IF EXISTS `admin_apkrecord`;

CREATE TABLE `admin_apkrecord` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apkid` int(10) unsigned NOT NULL COMMENT 'apk ID',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT 'apk包名称',
  `version` varchar(15) NOT NULL DEFAULT '' COMMENT '版本号',
  `size` int(10) unsigned NOT NULL COMMENT 'APK包大小，单位Kb',
  `date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否正式',
  PRIMARY KEY (`id`),
  KEY `apkid` (`apkid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='APK历史记录表';

/*Data for the table `admin_apkrecord` */

/*Table structure for table `admin_apksynchro` */

DROP TABLE IF EXISTS `admin_apksynchro`;

CREATE TABLE `admin_apksynchro` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL DEFAULT '' COMMENT '游戏名称',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT 'APK名称',
  `logo` varchar(100) NOT NULL DEFAULT '' COMMENT 'APK包logo图片',
  `banner` text NOT NULL COMMENT 'APK包推广页轮播图片',
  `score` decimal(2,1) NOT NULL DEFAULT '5.0' COMMENT '评分',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否上线，0为不上线，1为上线',
  `download` int(10) unsigned NOT NULL DEFAULT '1000' COMMENT '下载次数，默认1000',
  `shortdesc` varchar(100) NOT NULL DEFAULT '' COMMENT 'APK简短介绍',
  `desc` text NOT NULL COMMENT 'APK详细描述',
  `applestoreurl` varchar(120) NOT NULL DEFAULT '' COMMENT '苹果商店URL链接',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='APK同步公网表';

/*Data for the table `admin_apksynchro` */

insert  into `admin_apksynchro`(`id`,`title`,`name`,`logo`,`banner`,`score`,`status`,`download`,`shortdesc`,`desc`,`applestoreurl`) values (4,'长沙棋牌','majiangcs','','','5.0',0,0,'长沙棋牌真的好啊','是真滴好玩呢','');

/*Table structure for table `admin_gid_uid` */

DROP TABLE IF EXISTS `admin_gid_uid`;

CREATE TABLE `admin_gid_uid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` int(10) unsigned NOT NULL COMMENT '后台用户ID',
  `gid` int(10) unsigned NOT NULL COMMENT '后台用户组ID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台用户组关系表';

/*Data for the table `admin_gid_uid` */

/*Table structure for table `admin_log` */

DROP TABLE IF EXISTS `admin_log`;

CREATE TABLE `admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `admin_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '后台用户ID',
  `log_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '日志类型：0-默认登录日志',
  `log_data` text COLLATE utf8mb4_unicode_ci COMMENT '日志信息，json数据',
  `login_ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0.0.0.0' COMMENT '登录IP',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='后台日志表';

/*Data for the table `admin_log` */

/*Table structure for table `admin_menu` */

DROP TABLE IF EXISTS `admin_menu`;

CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `menu_name` varchar(30) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单图标',
  `controller` varchar(30) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(30) NOT NULL DEFAULT '' COMMENT '操作行为',
  `sort` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '菜单等级：0-隐藏菜单，1-一级菜单，2-二级菜单',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态，0禁用，1开启',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单描述',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台菜单表';

/*Data for the table `admin_menu` */

/*Table structure for table `admin_mobile_version` */

DROP TABLE IF EXISTS `admin_mobile_version`;

CREATE TABLE `admin_mobile_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `site` tinyint(3) unsigned NOT NULL COMMENT '平台',
  `channel` int(10) unsigned NOT NULL COMMENT '渠道',
  `bigversion` tinyint(3) unsigned NOT NULL COMMENT '大版本号',
  `version` smallint(5) unsigned NOT NULL COMMENT '版本号',
  `isskip` tinyint(3) unsigned NOT NULL COMMENT '是否可以跳过',
  `des` varchar(1000) NOT NULL COMMENT '更新描叙',
  `url` varchar(500) NOT NULL COMMENT '最新的下载地址',
  `online_version` varchar(20) NOT NULL COMMENT '线上版本',
  `white_list` varchar(2000) NOT NULL COMMENT '线上白名单',
  `test_url` varchar(500) NOT NULL COMMENT '测试地址',
  `lasttime` int(10) unsigned NOT NULL COMMENT '上次更新时间',
  `forceurl` varchar(500) NOT NULL COMMENT '强更URL',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='移动版本配置表';

/*Data for the table `admin_mobile_version` */

/*Table structure for table `admin_params_conf` */

DROP TABLE IF EXISTS `admin_params_conf`;

CREATE TABLE `admin_params_conf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `conf_id` int(10) unsigned NOT NULL COMMENT '主配置ID，例如：params_type=0时，取菜单表主键ID',
  `key` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '参数key',
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '参数值',
  `params_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '参数类型：默认0为菜单参数',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `conf_id_params_type` (`conf_id`,`params_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `admin_params_conf` */

/*Table structure for table `admin_session` */

DROP TABLE IF EXISTS `admin_session`;

CREATE TABLE `admin_session` (
  `sid` char(32) NOT NULL DEFAULT '' COMMENT 'session ID（md5）',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `client_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT '客户端IP',
  `user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '请求代理浏览器',
  `sdata` text NOT NULL COMMENT 'session数据',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='后台用户session数据表';

/*Data for the table `admin_session` */

/*Table structure for table `admin_user` */

DROP TABLE IF EXISTS `admin_user`;

CREATE TABLE `admin_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `gender` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别：0-未知，1-男，2-女',
  `nickname` varchar(40) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(20) NOT NULL DEFAULT '' COMMENT '密钥，随机字符串',
  `extends` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展字段',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态，0：禁用，1：开启',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户创建时间',
  `utime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台用户表';

/*Data for the table `admin_user` */

/*Table structure for table `admin_user_group` */

DROP TABLE IF EXISTS `admin_user_group`;

CREATE TABLE `admin_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `group_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用户组状态：1为正常，0为禁用',
  `menu_ids` text NOT NULL COMMENT '菜单id,多个用逗号隔开',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `utime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_name` (`group_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='用户组表';

/*Data for the table `admin_user_group` */

insert  into `admin_user_group`(`id`,`group_name`,`status`,`menu_ids`,`ctime`,`utime`) values (1,'超级管理员',1,'0',0,0);

/* Function  structure for function  `getChildrenAgents` */

/*!50003 DROP FUNCTION IF EXISTS `getChildrenAgents` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`dev`@`10.%` FUNCTION `getChildrenAgents`(
		`childId` INT 
) RETURNS varchar(4000) CHARSET latin1
BEGIN
DECLARE sTemp VARCHAR(4000);
DECLARE sTempChd VARCHAR(4000);
SET sTemp = '$';
SET sTempChd = cast(childId as char);
WHILE sTempChd is not NULL DO
SET sTemp = CONCAT(sTemp,',',sTempChd);
SELECT group_concat(mid) INTO sTempChd FROM memberagents where FIND_IN_SET(parent_id,sTempChd)>0;
END WHILE;
return sTemp;
END */$$
DELIMITER ;

/* Function  structure for function  `getChildrenAgentsByBid` */

/*!50003 DROP FUNCTION IF EXISTS `getChildrenAgentsByBid` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`dev`@`10.%` FUNCTION `getChildrenAgentsByBid`(
		`bussId` INT 
) RETURNS varchar(4000) CHARSET latin1
BEGIN
DECLARE sTemp VARCHAR(4000);
DECLARE sTempChd VARCHAR(4000);
DECLARE sTempAgents VARCHAR(4000);
SET sTemp = '$';
SELECT group_concat(mid) INTO sTempAgents FROM memberagents WHERE belongid=bussId;
SET sTempChd = cast(sTempAgents as char);
WHILE sTempChd is not NULL DO
SET sTemp = CONCAT(sTemp,',',sTempChd);
SELECT group_concat(mid) INTO sTempChd FROM memberagents where FIND_IN_SET(parent_id,sTempChd)>0;
END WHILE;
return sTemp;
END */$$
DELIMITER ;

/* Procedure structure for procedure `pay` */

/*!50003 DROP PROCEDURE IF EXISTS  `pay` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`dev`@`10.%` PROCEDURE `pay`(
		IN `player` INT ,
		IN `pay` BIGINT ,
		IN `force` BOOL ,
		OUT err INT ,
		OUT `old_gold` BIGINT ,
		OUT `new_gold` BIGINT 
)
BEGIN
	SELECT `gold` INTO `old_gold`  FROM `membercommongame0` WHERE `mid` = `player`;
	IF `old_gold` + `pay` < 0 THEN
		IF `force` = FALSE THEN
			SET err = -1;
		ELSE 
			SET err = 0;
			UPDATE  `membercommongame0` SET `gold` = IF (ABS(`pay`) > `gold`, 0, `gold` + `pay`) WHERE `membercommongame0`.`mid` = `player`;
		END IF;
	ELSE 
		SET err = 0;
		UPDATE  `membercommongame0` SET `gold` = `gold` + `pay` WHERE `mid` = `player`;
	END IF;
	
	SELECT `gold` INTO `new_gold`  FROM `membercommongame0` WHERE `mid` = `player`;
 END */$$
DELIMITER ;

/* Procedure structure for procedure `paybeans` */

/*!50003 DROP PROCEDURE IF EXISTS  `paybeans` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`dev`@`%` PROCEDURE `paybeans`(IN `player` INT ,
		IN `pay` BIGINT ,
		IN `force` BOOL ,
		OUT err INT ,
		OUT `old_beans` BIGINT ,
		OUT `new_beans` BIGINT)
BEGIN
	SELECT `beans` INTO `old_beans`  FROM `membercommongame0` WHERE `mid` = `player`;
	IF `old_beans` + `pay` < 0 THEN
		IF `force` = FALSE THEN
			SET err = -1;
		ELSE 
			SET err = 0;
			UPDATE  `membercommongame0` SET `beans` = IF (ABS(`pay`) > `beans`, 0, `beans` + `pay`) WHERE `membercommongame0`.`mid` = `player`;
		END IF;
	ELSE 
		SET err = 0;
		UPDATE  `membercommongame0` SET `beans` = `beans` + `pay` WHERE `mid` = `player`;
	END IF;
	
	SELECT `beans` INTO `new_beans`  FROM `membercommongame0` WHERE `mid` = `player`;
 END */$$
DELIMITER ;

/* Procedure structure for procedure `payclub` */

/*!50003 DROP PROCEDURE IF EXISTS  `payclub` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`dev`@`10.%` PROCEDURE `payclub`(
		IN `player` INT ,
		IN `pay` BIGINT ,
		IN `force` INT ,
		OUT `err` INT ,
		OUT `old_gold` BIGINT ,
		OUT `new_gold` BIGINT 
)
BEGIN
	SELECT `gold` INTO `old_gold`  FROM `clubgold` WHERE `clubid` = `player`;
	IF `old_gold` = NULL THEN
		SET err = -1;
	END IF;
	IF `old_gold` + `pay` < 0 THEN
		IF `force` = FALSE THEN
			SET err = -1;
		ELSE 
			SET err = 0;
			UPDATE  `clubgold` SET `gold` = IF (ABS(`pay`) > `gold`, 0, `gold` + `pay`) WHERE `clubgold`.`clubid` = `player`;
		END IF;
	ELSE 
		SET err = 0;
		UPDATE  `clubgold` SET `gold` = `gold` + `pay` WHERE `clubid` = `player`;
	END IF;
	
	SELECT `gold` INTO `new_gold`  FROM `clubgold` WHERE `clubid` = `player`;
END */$$
DELIMITER ;

/* Procedure structure for procedure `sp_createChildLst` */

/*!50003 DROP PROCEDURE IF EXISTS  `sp_createChildLst` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`dev`@`10.%` PROCEDURE `sp_createChildLst`(
		IN rootId int(10),
		IN nDepth INT 
)
BEGIN
DECLARE done INT DEFAULT 0;
DECLARE b VARCHAR(20);
DECLARE cur1 CURSOR FOR SELECT mid FROM memberagents where parent_id=rootId;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
insert into tmpLst values (null,rootId,nDepth);
SET @@max_sp_recursion_depth = 50;
OPEN cur1;
FETCH cur1 INTO b;
WHILE done=0 DO
CALL sp_createChildLst(b,nDepth+1);
FETCH cur1 INTO b;
END WHILE;
CLOSE cur1;
END */$$
DELIMITER ;

/* Procedure structure for procedure `sp_showChildLst` */

/*!50003 DROP PROCEDURE IF EXISTS  `sp_showChildLst` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`dev`@`10.%` PROCEDURE `sp_showChildLst`(
		IN rootId int(10)
)
BEGIN
CREATE TEMPORARY TABLE IF NOT EXISTS tmpLst
(sno int primary key auto_increment,mid int(10),depth int);
DELETE FROM tmpLst;
CALL sp_createChildLst(rootId,0);
select tmpLst.* from tmpLst,memberagents where tmpLst.mid=memberagents.mid order by tmpLst.depth ASC;
END */$$
DELIMITER ;

/* Procedure structure for procedure `update_score` */

/*!50003 DROP PROCEDURE IF EXISTS  `update_score` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`dev`@`10.%` PROCEDURE `update_score`(
		IN `player` INT ,
		IN `score` BIGINT ,
		OUT `old_score` BIGINT ,
		OUT `new_score` BIGINT 
)
BEGIN
SELECT `jifen` INTO `old_score`  FROM `membergame0` WHERE `mid` = `player` AND `type` = 0;
UPDATE  `membergame0` SET `jifen` = `jifen` + `score` WHERE `mid` = `player` AND `type` = 0;
SELECT `jifen` INTO `new_score`  FROM `membergame0` WHERE `mid` = `player` AND `type` = 0;
END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
