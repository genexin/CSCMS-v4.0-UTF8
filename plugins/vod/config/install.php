<?php

if (!defined('CSCMSPATH')) exit('No permission resources');

return array(
		  //视频列表
         "CREATE TABLE IF NOT EXISTS `{prefix}vod` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `name` varchar(128) default '' COMMENT '名称',
            `bname` varchar(64) default '' COMMENT '英文别名',
            `color` varchar(10) default '' COMMENT '名称颜色',
            `tags` varchar(255) default '' COMMENT 'TAGS标签',
            `type` varchar(255) default '' COMMENT '剧情分类',
            `pic` varchar(255) default '' COMMENT '视频图片',
            `pic2` varchar(255) default '' COMMENT '幻灯图片',
            `cid` mediumint(5) default '0' COMMENT '分类ID',
            `tid` mediumint(5) default '0' COMMENT '专题ID',
            `remark` varchar(32) default '完结' COMMENT '更新状态',
            `zhuyan` varchar(128) default '' COMMENT '主演',
            `daoyan` varchar(128) default '' COMMENT '导演',
            `year` varchar(64) default '' COMMENT '上映时间',
            `yuyan` varchar(64) default '' COMMENT '语言',
            `diqu` varchar(64) default '' COMMENT '地区',
            `reco` tinyint(1) default '0' COMMENT '推荐星级',
            `yid` tinyint(1) default '0' COMMENT '是否隐藏',
            `hid` tinyint(1) default '0' COMMENT '是否回收站',
            `singerid` int(10) unsigned default '0' COMMENT '歌手ID',
            `uid` int(10) unsigned default '0' COMMENT '会员ID',
            `hits` int(10) unsigned default '0' COMMENT '总人气',
            `shits` int(10) unsigned default '0' COMMENT '收藏人气',
            `xhits` int(10) unsigned default '0' COMMENT '下载人气',
            `yhits` int(10) unsigned default '0' COMMENT '月人气',
            `zhits` int(10) unsigned default '0' COMMENT '周人气',
            `rhits` int(10) unsigned default '0' COMMENT '日人气',
            `dhits` int(10) unsigned default '0' COMMENT '顶人气',
            `chits` int(10) unsigned default '0' COMMENT '踩人气',
            `phits` int(10) unsigned default '0' COMMENT '评分次数',
            `pfen` int(10) unsigned default '0' COMMENT '评分总数',
            `cion` mediumint(5) default '0' COMMENT '观看需要金币',
            `dcion` mediumint(5) default '0' COMMENT '下载需要金币',
            `vip` mediumint(5) default '0' COMMENT '可观看组',
            `level` mediumint(5) default '0' COMMENT '可观看等级',
            `info` varchar(64) default '' COMMENT '简单介绍',
            `text` text COMMENT '详细介绍',
            `purl` text COMMENT '播放地址',
            `durl` text COMMENT '下载地址',
            `playtime` int(10) unsigned default '0' COMMENT '播放时间',
            `addtime` int(10) unsigned default '0' COMMENT '增加时间',
            `skins` varchar(64) default 'play.html' COMMENT '默认模板',
            `title` varchar(64) default '' COMMENT 'SEO标题',
            `keywords` varchar(150) default '' COMMENT 'SEO关键词',
            `description` varchar(200) default '' COMMENT 'SEO介绍',
             PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM  DEFAULT charset=utf8 COMMENT='视频表';",

         //视频分类
         "CREATE TABLE IF NOT EXISTS `{prefix}vod_list` (
            `id` mediumint(5) unsigned NOT NULL auto_increment,
            `name` varchar(64) default '' COMMENT '名称',
            `bname` varchar(30) default '' COMMENT '英文别名',
            `fid` tinyint(1) default '0' COMMENT '上级ID',
            `xid` tinyint(1) default '0' COMMENT '排序ID',
            `yid` tinyint(1) default '0' COMMENT '是否显示',
            `skins` varchar(64) default 'list.html' COMMENT '分类模板',
            `skins2` varchar(64) default 'show.html' COMMENT '内容模板',
            `skins3` varchar(64) default 'play.html' COMMENT '播放模板',
            `title` varchar(64) default '' COMMENT 'SEO标题',
            `keywords` varchar(150) default '' COMMENT 'SEO关键词',
            `description` varchar(200) default '' COMMENT 'SEO介绍',
             PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM  DEFAULT charset=utf8 COMMENT='视频分类表';",

         //剧情分类
         "CREATE TABLE IF NOT EXISTS `{prefix}vod_type` (
            `id` mediumint(5) unsigned NOT NULL auto_increment,
            `name` varchar(64) default '' COMMENT '名称',
            `cid` mediumint(5) default '0' COMMENT '分类ID',
            `xid` tinyint(1) default '0' COMMENT '排序ID',
            `yid` tinyint(1) default '0' COMMENT '是否显示',
             PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM  DEFAULT charset=utf8 COMMENT='视频剧情表';",

         //收藏记录
         "CREATE TABLE IF NOT EXISTS `{prefix}vod_fav` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `sid` tinyint(1) default '0' COMMENT '类型ID',
            `name` varchar(64) default '' COMMENT '数据名称',
            `cid` mediumint(5) unsigned default '0' COMMENT '数据分类ID',
            `did` int(10) unsigned default '0' COMMENT '数据ID',
            `uid` int(10) unsigned default '0' COMMENT '会员ID',
            `addtime` int(10) unsigned default '0' COMMENT '时间',
             PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM  DEFAULT charset=utf8 COMMENT='视频收藏记录';",

         //观看下载记录
         "CREATE TABLE IF NOT EXISTS `{prefix}vod_look` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `sid` tinyint(1) default '0' COMMENT '类型ID',
            `name` varchar(64) default '' COMMENT '视频名称',
            `cid` mediumint(5) unsigned default '0' COMMENT '视频分类ID',
            `did` varchar(128) default '' COMMENT '视频ID',
            `uid` int(10) unsigned default '0' COMMENT '会员ID',
            `cion` int(10) default '0' COMMENT '扣除金币',
            `ip` varchar(20) default '' COMMENT '观看IP',
            `addtime` int(10) unsigned default '0' COMMENT '时间',
             PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM  DEFAULT charset=utf8 COMMENT='视频观看下载记录';",


         //视频专题
         "CREATE TABLE IF NOT EXISTS `{prefix}vod_topic` (
            `id` int(10) unsigned NOT NULL auto_increment,
            `name` varchar(64) default '' COMMENT '名称',
            `bname` varchar(20) default '' COMMENT '别名',
            `pic` varchar(255) default '' COMMENT '图片',
            `toppic` varchar(255) default '' COMMENT '顶部图片',
            `tid` tinyint(1) default '0' COMMENT '是否推荐',
            `yid` tinyint(1) default '0' COMMENT '是否审核',
            `hits` int(10) unsigned default '0' COMMENT '总人气',
            `yhits` int(10) unsigned default '0' COMMENT '月人气',
            `zhits` int(10) unsigned default '0' COMMENT '周人气',
            `rhits` int(10) unsigned default '0' COMMENT '日人气',
            `shits` int(10) unsigned default '0' COMMENT '收藏人气',
            `neir` text COMMENT '介绍',
            `skins` varchar(64) default 'topic.html' COMMENT '默认模板',
            `addtime` int(10) unsigned default '0' COMMENT '时间',
            `title` varchar(64) default '' COMMENT 'SEO标题',
            `keywords` varchar(150) default '' COMMENT 'SEO关键词',
            `description` varchar(200) default '' COMMENT 'SEO介绍',
             PRIMARY KEY  (`id`)
          ) ENGINE=MyISAM  DEFAULT charset=utf8 COMMENT='视频专题表';",

		  //默认分类数据
         "INSERT INTO `{prefix}vod_list` (`id`, `name`, `bname`, `fid`, `xid`, `yid`, `skins`, `title`, `keywords`, `description`) VALUES
            (1, '电影', 'movie', 0, 1, 0, 'list.html', '', '', ''),
            (2, '电视剧', 'tv', 0, 2, 0, 'list.html', '', '', ''),
            (3, '综艺', 'fun', 0, 3, 0, 'list.html', '', '', ''),
            (4, '动漫', 'cartoon', 0, 4, 0, 'list.html', '', '', ''),
            (5, '微电影', 'weidy', 0, 5, 0, 'list.html', '', '', ''),
            (6, '动作片', 'action', 1, 1, 0, 'list.html', '', '', ''),
            (7, '喜剧片', 'comedy', 1, 2, 0, 'list.html', '', '', ''),
            (8, '爱情片', 'romance', 1, 3, 0, 'list.html', '', '', ''),
            (9, '科幻片', 'fiction', 1, 4, 0, 'list.html', '', '', ''),
            (10, '恐怖片', 'horror', 1, 5, 0, 'list.html', '', '', ''),
            (11, '战争片', 'war', 1, 6, 0, 'list.html', '', '', ''),
            (12, '剧情片', 'drama', 1, 7, 0, 'list.html', '', '', ''),
            (13, '记录片', 'documentary', 1, 8, 0, 'list.html', '', '', ''),
            (14, '动画片', 'cartoonmovie', 1, 9, 0, 'list.html', '', '', ''),
            (15, '大陆剧', 'mainland', 2, 1, 0, 'list.html', '', '', ''),
            (16, '港台剧', 'tvb', 2, 2, 0, 'list.html', '', '', ''),
            (17, '日韩剧', 'korea', 2, 3, 0, 'list.html', '', '', ''),
            (18, '欧美剧', 'occident', 2, 4, 0, 'list.html', '', '', ''),
            (19, '海外剧', 'overseas', 2, 8, 0, 'list.html', '', '', ''),
            (20, '爱情', 'aq', 5, 1, 0, 'list.html', '', '', ''),
            (21, '励志', 'lz', 5, 2, 0, 'list.html', '', '', ''),
            (22, '搞笑', 'gx', 5, 3, 0, 'list.html', '', '', ''),
            (23, '恐怖', 'kb', 5, 4, 0, 'list.html', '', '', ''),
            (24, '动画', 'dh', 5, 5, 0, 'list.html', '', '', ''),
            (25, '职场', 'zc', 5, 6, 0, 'list.html', '', '', ''),
            (26, '明星', 'mx', 5, 7, 0, 'list.html', '', '', ''),
            (27, '生活', 'sh', 5, 8, 0, 'list.html', '', '', '');",

		  //默认剧情数据
         "INSERT INTO `{prefix}vod_type` (`id`, `name`, `cid`, `xid`, `yid`) VALUES
            (1, '惊悚', 1, 1, 0),
            (2, '悬疑', 1, 2, 0),
            (3, '魔幻', 1, 3, 0),
            (4, '罪案', 1, 4, 0),
            (5, '灾难', 1, 5, 0),
            (6, '动画', 1, 6, 0),
            (7, '古装', 1, 7, 0),
            (8, '青春', 1, 8, 0),
            (9, '歌舞', 1, 9, 0),
            (10, '文艺', 1, 10, 0),
            (11, '生活', 1, 11, 0),
            (12, '历史', 1, 12, 0),
            (13, '励志', 1, 13, 0),
            (14, '预告片', 1, 14, 0),
            (15, '言情', 2, 1, 0),
            (16, '都市', 2, 2, 0),
            (17, '家庭', 2, 3, 0),
            (18, '生活', 2, 4, 0),
            (19, '偶像', 2, 5, 0),
            (20, '喜剧', 2, 6, 0),
            (21, '历史', 2, 7, 0),
            (22, '古装', 2, 8, 0),
            (23, '武侠', 2, 9, 0),
            (24, '刑侦', 2, 10, 0),
            (25, '战争', 2, 11, 0),
            (26, '神话', 2, 12, 0),
            (27, '军旅', 2, 13, 0),
            (28, '谍战', 2, 14, 0),
            (29, '商战', 2, 15, 0),
            (30, '校园', 2, 16, 0),
            (31, '穿越', 2, 17, 0),
            (32, '悬疑', 2, 18, 0),
            (33, '犯罪', 2, 19, 0),
            (34, '科幻', 2, 20, 0),
            (35, '预告片', 2, 21, 0),
            (36, '脱口秀', 3, 1, 0),
            (37, '真人秀', 3, 2, 0),
            (38, '选秀', 3, 3, 0),
            (39, '情感', 3, 4, 0),
            (40, '访谈', 3, 5, 0),
            (41, '时尚', 3, 6, 0),
            (42, '晚会', 3, 7, 0),
            (43, '财经', 3, 8, 0),
            (44, '益智', 3, 9, 0),
            (45, '音乐', 3, 10, 0),
            (46, '游戏', 3, 11, 0),
            (47, '职场', 3, 12, 0),
            (48, '美食', 3, 13, 0),
            (49, '旅游', 3, 14, 0),
            (50, '冒险', 4, 1, 0),
            (51, '热血', 4, 2, 0),
            (52, '搞笑', 4, 3, 0),
            (53, '少女', 4, 4, 0),
            (54, '推理', 4, 5, 0),
            (55, '竞技', 4, 6, 0),
            (56, '益智', 4, 7, 0),
            (57, '童话', 4, 8, 0),
            (58, '经典', 4, 9, 0);"
);
