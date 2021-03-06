<?php

if (!defined('CSCMSPATH')) exit('No permission resources');

return array(
		  //歌手列表
         "CREATE TABLE IF NOT EXISTS `{prefix}singer` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `name` varchar(128) default '' COMMENT '歌手名称',
            `bname` varchar(64) default '' COMMENT '英文别名',
            `color` varchar(10) default '' COMMENT '名称颜色',
            `tags` varchar(64) default '' COMMENT '歌手标签',
            `pic` varchar(255) default '' COMMENT '萎缩图',
            `cid` mediumint(5) default '0' COMMENT '分类ID',
            `reco` tinyint(1) default '0' COMMENT '推荐星级',
            `yid` tinyint(1) default '0' COMMENT '是否隐藏',
            `hid` tinyint(1) default '0' COMMENT '是否回收站',
            `hits` int(10) unsigned default '0' COMMENT '总人气',
            `yhits` int(10) unsigned default '0' COMMENT '月人气',
            `zhits` int(10) unsigned default '0' COMMENT '周人气',
            `rhits` int(10) unsigned default '0' COMMENT '日人气',
            `sex` varchar(5) default '男' COMMENT '歌手性别',
            `nichen` varchar(30) default '暂无' COMMENT '歌手别名',
            `nat` varchar(30) default '暂无' COMMENT '歌手国籍',
            `yuyan` varchar(10) default '暂无' COMMENT '歌手语言',
            `city` varchar(64) default '暂无' COMMENT '歌手出生地',
            `sr` varchar(15) default '暂无' COMMENT '歌手生日',
            `xingzuo` varchar(10) default '暂无' COMMENT '歌手星座',
            `height` varchar(10) default '暂无' COMMENT '歌手身高',
            `weight` varchar(10) default '暂无' COMMENT '歌手体重',
            `content` text COMMENT '歌手介绍',
            `addtime` int(10) unsigned default '0' COMMENT '增加时间',
            `title` varchar(64) default '' COMMENT 'SEO标题',
            `keywords` varchar(150) default '' COMMENT 'SEO关键词',
            `description` varchar(200) default '' COMMENT 'SEO介绍',
             PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM  DEFAULT charset=utf8 COMMENT='歌手表';",

         //歌手分类
         "CREATE TABLE IF NOT EXISTS `{prefix}singer_list` (
            `id` mediumint(5) unsigned NOT NULL auto_increment,
            `name` varchar(64) default '' COMMENT '名称',
            `bname` varchar(30) default '' COMMENT '英文别名',
            `fid` tinyint(1) default '0' COMMENT '上级ID',
            `xid` tinyint(1) default '0' COMMENT '排序ID',
            `yid` tinyint(1) default '0' COMMENT '是否显示',
            `skins` varchar(64) default 'list.html' COMMENT '默认模板',
            `title` varchar(64) default '' COMMENT 'SEO标题',
            `keywords` varchar(150) default '' COMMENT 'SEO关键词',
            `description` varchar(200) default '' COMMENT 'SEO介绍',
             PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM  DEFAULT charset=utf8 COMMENT='歌手分类表';",

		  //默认分类数据
         "INSERT INTO `{prefix}singer_list` (`id`, `name`, `bname`, `fid`, `xid`, `yid`, `skins`, `title`, `keywords`, `description`) VALUES
            (1, '华语', 'hy', 0, 1, 0, 'list.html', '', '', ''),
            (2, '欧美', 'om', 0, 2, 0, 'list.html', '', '', ''),
            (3, '日韩', 'rh', 0, 3, 0, 'list.html', '', '', ''),
            (4, '华语男歌手', 'hynan', 1, 1, 0, 'list.html', '', '', ''),
            (5, '华语女歌手', 'hynv', 1, 2, 0, 'list.html', '', '', ''),
            (6, '华语乐队组合', 'hyzh', 1, 3, 0, 'list.html', '', '', ''),
            (7, '欧美男歌手', 'omnan', 2, 1, 0, 'list.html', '', '', ''),
            (8, '欧美女歌手', 'omnv', 2, 2, 0, 'list.html', '', '', ''),
            (9, '欧美乐队组合', 'omzh', 2, 3, 0, 'list.html', '', '', ''),
            (10, '日韩男歌手', 'rhnan', 3, 1, 0, 'list.html', '', '', ''),
            (11, '日韩女歌手', 'rhnv', 3, 2, 0, 'list.html', '', '', ''),
            (12, '日韩乐队组合', 'rhzh', 3, 2, 0, 'list.html', '', '', '');"
);
