/**
* APP基础表
*/

-- 用户session表 --
CREATE TABLE `user_session` (
  `sid` char(32) NOT NULL DEFAULT '' COMMENT 'session ID',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `client_ip` varchar(15) NOT NULL DEFAULT '0.0.0.0' COMMENT '客户端IP',
  `user_agent` varchar(200) NOT NULL DEFAULT '' COMMENT '请求代理浏览器',
  `data` text NOT NULL COMMENT 'session数据',
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户session表';