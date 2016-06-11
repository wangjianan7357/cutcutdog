<?php
return array(
	/** 
	 * 超级管理员才可进行角色分配
	 */
	'role' => '
		`id` INT( 2 ) NOT NULL AUTO_INCREMENT , 
		`name` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
		`power` VARCHAR( 128 ) NOT NULL DEFAULT "" , 
		PRIMARY KEY ( `id` )',

	/** 
	 * 管理员账号及角色
	 * 密码最长为 12 个字符，密码可用字符 0-9a-zA-Z_@-
	 */
	'admin' => '
		`id` INT( 4 ) NOT NULL AUTO_INCREMENT , 
		`rid` INT( 2 ) NOT NULL , 
		`name` VARCHAR( 20 ) NOT NULL DEFAULT "" , 
		`pass` CHAR( 32 ) NOT NULL DEFAULT "" , 
		`realname` VARCHAR( 20 ) NOT NULL DEFAULT "" , 
		PRIMARY KEY ( `id` )',

    /** 
     * 会员
     */
    'member' => '
        `id` int( 8 ) NOT NULL AUTO_INCREMENT,
        `name` varchar( 16 ) NOT NULL DEFAULT "",
        `pass` char( 32 ) NOT NULL,
        `salt` varchar( 128 ) NOT NULL,
        `src` varchar( 32 ) NOT NULL DEFAULT "",
        `sex` varchar( 1 ) NOT NULL DEFAULT 0,
        `phone` varchar( 16 ) NOT NULL DEFAULT "",
        `email` varchar( 128 ) NOT NULL DEFAULT "",
        `address` varchar( 256 ) NOT NULL DEFAULT "",
        `desp` TEXT NOT NULL , 
        `fields` TEXT NOT NULL DEFAULT "",
        `valid` tinyint( 1 ) NOT NULL DEFAULT 0,
        `type` int( 2 ) NOT NULL DEFAULT 1 COMMENT "1: 普通会员，10: 美容师",
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY ( `id` )',

    /** 
     * 会员宠物
     */
    'mypet' => '
        `id` int( 8 ) NOT NULL AUTO_INCREMENT,
        `mid` INT( 8 ) NOT NULL DEFAULT 0 , 
        `type` varchar( 16 ) NOT NULL DEFAULT "",
        `size` varchar( 16 ) NOT NULL DEFAULT "",
        `name` varchar( 16 ) NOT NULL DEFAULT "",
        `src` varchar( 32 ) NOT NULL DEFAULT "",
        `number` varchar( 10 ) NOT NULL DEFAULT "",
        `remark` varchar( 256 ) NOT NULL DEFAULT "",
        `valid` tinyint( 1 ) NOT NULL DEFAULT 0,
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY ( `id` )',

    /** 
     * 会员订单
     * mid 会员 id
     * type;
     * status 订单状态
     */
    'order' => '
        `id` INT( 8 ) NOT NULL AUTO_INCREMENT , 
        `mid` INT( 8 ) NOT NULL DEFAULT 0 , 
        `number` VARCHAR( 18 ) NOT NULL DEFAULT "" , 
        `email` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
        `name` VARCHAR( 90 ) NOT NULL DEFAULT "" , 
        `phone` VARCHAR( 18 ) NOT NULL DEFAULT "" , 
        `address` VARCHAR( 256 ) NOT NULL DEFAULT "" , 
        `amount` DECIMAL( 8, 2 ) NOT NULL DEFAULT 0 , 
        `fields` MEDIUMTEXT NOT NULL , 
        `status` INT( 2 ) NOT NULL DEFAULT 0 , 
        `read` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
        `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        PRIMARY KEY ( `id` )',

    /** 
     * 网站预约
     */
    'booking' => '
        `id` INT( 8 ) NOT NULL AUTO_INCREMENT , 
        `mid` INT( 8 ) NOT NULL DEFAULT 0 , 
        `tid` INT( 8 ) NOT NULL DEFAULT 0 , 
        `service` VARCHAR( 90 ) NOT NULL DEFAULT "" , 
        `pet` VARCHAR( 32 ) NOT NULL DEFAULT "" , 
        `size` VARCHAR( 32 ) NOT NULL DEFAULT "" , 
        `name` VARCHAR( 32 ) NOT NULL DEFAULT "" , 
        `phone` VARCHAR( 32 ) NOT NULL DEFAULT "" , 
        `address` VARCHAR( 128 ) NOT NULL DEFAULT "" , 
        `time` VARCHAR( 32 ) NOT NULL DEFAULT "" COMMENT "上门日期" ,
        `remark` VARCHAR( 512 ) NOT NULL DEFAULT "" , 
        `valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
        `read` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
        `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        PRIMARY KEY ( `id` ),
        KEY ( `mid` )',

	/** 
	 * 网站留言信息
	 */
	'message' => '
		`id` INT( 8 ) NOT NULL AUTO_INCREMENT , 
		`aid` INT( 6 ) NOT NULL DEFAULT 0 COMMENT "目标内容ID" ,
		`atype` INT( 3 ) NOT NULL DEFAULT 0 COMMENT "目标内容类型" ,
        `mid` INT( 8 ) NOT NULL DEFAULT 0 COMMENT "发送者ID" ,
        `tid` INT( 8 ) NOT NULL DEFAULT 0 COMMENT "回复对应ID" ,
		`content` TEXT NOT NULL , 
		`valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
		`read` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
		`type` TINYINT( 1 ) NOT NULL DEFAULT 1 COMMENT "信件类型" ,
		`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
		PRIMARY KEY ( `id` ) , 
		KEY ( `aid` )',

    /** 
     * 网站点赞
     */
    'likes' => '
        `id` INT( 8 ) NOT NULL AUTO_INCREMENT , 
        `aid` INT( 6 ) NOT NULL DEFAULT 0 COMMENT "目标内容ID" ,
        `atype` INT( 3 ) NOT NULL DEFAULT 0 COMMENT "目标内容类型" ,
        `mid` INT( 8 ) NOT NULL DEFAULT 0 COMMENT "发送者ID" ,
        `valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
        `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        PRIMARY KEY ( `id` ) , 
        KEY ( `aid` )',

    /** 
     * 网站聊天
     */
    'chat' => '
        `id` INT( 8 ) NOT NULL AUTO_INCREMENT , 
        `aid` INT( 6 ) NOT NULL DEFAULT 0 COMMENT "目标内容ID" ,
        `atype` INT( 3 ) NOT NULL DEFAULT 0 COMMENT "目标内容类型" ,
        `mid` INT( 8 ) NOT NULL DEFAULT 0 COMMENT "发送者ID" ,
        `tid` INT( 8 ) NOT NULL DEFAULT 0 COMMENT "回复对应ID" ,
        `content` TEXT NOT NULL , 
        `valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
        `read` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
        `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        PRIMARY KEY ( `id` ) , 
        KEY ( `aid` )',

    /** 
     * 验证
     */
    'verify' => '
        `id` int( 8 ) NOT NULL AUTO_INCREMENT,
        `mid` INT( 8 ) NOT NULL DEFAULT 0 COMMENT "会员ID" ,
        `sign` char( 64 ) NOT NULL,
        `time` INT( 10 ) NOT NULL DEFAULT 0 COMMENT "有效时间" ,
        `valid` tinyint( 1 ) NOT NULL DEFAULT 0,
        `content` VARCHAR( 256 ) NOT NULL DEFAULT "" , 
        `type` int( 2 ) NOT NULL DEFAULT 1 COMMENT "1: 邮箱验证",
        PRIMARY KEY ( `id` )',

	/** 
	 * 多国语言
	 * connect 页面复制参照语言，否则独立新建页面
	 */
	'language' => '
		`id` INT( 2 ) NOT NULL AUTO_INCREMENT , 
		`queue` INT( 7 ) NOT NULL DEFAULT 0 , 
		`abbr` VARCHAR( 2 ) NOT NULL DEFAULT "" , 
		`connect` VARCHAR( 2 ) NOT NULL DEFAULT "" , 
		`valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
		PRIMARY KEY ( `id` )',

	/** 
	 * 普通文字变量
	 */
	'constant' => '
		`id` INT( 6 ) NOT NULL AUTO_INCREMENT , 
		`various` TEXT NOT NULL , 
		PRIMARY KEY ( `id` )',

	/** 
	 * 记录操作日志
	 */
	'log' => '
		`id` INT( 8 ) NOT NULL AUTO_INCREMENT , 
		`user` VARCHAR( 10 ) NOT NULL DEFAULT "" , 
		`operate` SET("add", "edt", "del", "vfy", "otr"),
		`detail` TEXT NOT NULL , 
		`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
		PRIMARY KEY ( `id` )',

	/** 
	 * 系统参数
	 * tid 类型 id，1：系统信息; 2：公司信息; 3：常用设置
	 */
	'system' => '
		`id` INT( 3 ) NOT NULL AUTO_INCREMENT , 
		`tid` TINYINT( 1 ) NOT NULL DEFAULT 1 , 
		`varname` VARCHAR( 25 ) NOT NULL DEFAULT "" , 
		`info` VARCHAR( 100 ) NOT NULL DEFAULT "" , 
		`type` SET("string", "integer", "boolean", "array", "hidden", "date") DEFAULT "string",
		`lang` VARCHAR( 2 ) NOT NULL DEFAULT "" ,
		`value` TEXT NOT NULL , 
		PRIMARY KEY ( `id` )',

    /** 
     * 属性值
     */
    'property_content' => '
        `id` INT( 8 ) NOT NULL AUTO_INCREMENT , 
        `sort` INT( 8 ) NOT NULL DEFAULT 1 COMMENT "1：资料库服务所属，2：资料库相册，3：美容师服务所属，7：產品相冊", 
        `vid` INT( 8 ) NOT NULL DEFAULT 0 COMMENT "属性值ID", 
        `pid` INT( 8 ) NOT NULL DEFAULT 0 COMMENT "内容ID", 
        `content` VARCHAR( 80 ) NOT NULL DEFAULT "" , 
        PRIMARY KEY ( `id` ),
        KEY ( `pid` )',


	// multi language
	/** 
	 * 页面描述及 meta 用做 SEO 优化
	 * type 0：普通可编辑页面; 1：不可删页面; 2：不可删的父级页面且不索引
	 */
	'page' => '
		`id` INT( 5 ) NOT NULL AUTO_INCREMENT , 
		`path` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
		`key1` VARCHAR( 80 ) NOT NULL DEFAULT "" , 
		`key2` VARCHAR( 80 ) NOT NULL DEFAULT "" , 
		`title` TEXT NOT NULL , 
		`keyword` TEXT NOT NULL , 
		`description` TEXT NOT NULL , 
		`desp` TEXT NOT NULL , 
		`info` TEXT NOT NULL , 
		`fields` MEDIUMTEXT NOT NULL , 
		`priority` DECIMAL( 3, 1 ) DEFAULT NULL, 
		`type` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
		`module` VARCHAR( 20 ) NOT NULL DEFAULT "" , 
		PRIMARY KEY ( `id` )',

	/** 
	 * 分类信息
	 * 第一层为模块，第二层为模块下的分类
	 * type 1：院系模块; 2：耗材模块; 3：标签模块; 4：链接模块; 
	 * style 展示方式：列表或显示单页
	 */
	'catalog' => '
		`id` INT( 4 ) NOT NULL AUTO_INCREMENT , 
		`type` SMALLINT( 2 ) NOT NULL DEFAULT 0 , 
		`queue` INT( 7 ) NOT NULL DEFAULT 0 , 
		`parent` VARCHAR( 40 ) NOT NULL DEFAULT "" , 
		`name` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
		`path` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
		`src` VARCHAR( 55 ) NOT NULL DEFAULT "" , 
		`order` SMALLINT( 2 ) NOT NULL DEFAULT 0 , 
		`valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
		`navi` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
		`fields` TEXT NOT NULL , 
		`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
		PRIMARY KEY ( `id` )',

	/** 
	 * 信息模块
	 */
	'info' => '
		`id` INT( 6 ) NOT NULL AUTO_INCREMENT , 
        `mid` INT( 8 ) NOT NULL DEFAULT 0 , 
		`cid` VARCHAR( 40 ) NOT NULL DEFAULT "" , 
		`queue` INT( 7 ) NOT NULL DEFAULT 0 , 
		`name` VARCHAR( 80 ) NOT NULL DEFAULT "" , 
		`path` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
        `tel` VARCHAR( 20 ) NOT NULL DEFAULT "" , 
        `address` VARCHAR( 128 ) NOT NULL DEFAULT "" , 
		`src` VARCHAR( 60 ) NOT NULL DEFAULT "" , 
		`visitor` INT( 6 ) NOT NULL DEFAULT 0 , 
		`valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
        `read` INT( 1 ) NOT NULL DEFAULT 0 , 
		`desp` TEXT NOT NULL , 
        `type` INT( 1 ) NOT NULL DEFAULT 0 , 
		`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
		PRIMARY KEY ( `id` )',

	/** 
	 * 产品模块
	 */
	'product' => '
		`id` INT( 5 ) NOT NULL AUTO_INCREMENT , 
        `mid` INT( 8 ) NOT NULL DEFAULT 0 , 
		`cid` VARCHAR( 40 ) NOT NULL , 
		`queue` INT( 7 ) NOT NULL DEFAULT 0 , 
        `recom` INT( 7 ) NOT NULL DEFAULT 0 , 
		`name` VARCHAR( 80 ) NOT NULL DEFAULT "" , 
		`path` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
		`src` VARCHAR( 60 ) NOT NULL DEFAULT "" , 
		`price` DECIMAL( 8, 2 ) NOT NULL DEFAULT 0 , 
		`sale` DECIMAL( 8, 2 ) NOT NULL DEFAULT 0 , 
		`valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
		`fields` TEXT NOT NULL , 
		`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
		PRIMARY KEY ( `id` )',

    /** 
     * 服務
     */
    'service' => '
        `id` INT( 8 ) NOT NULL AUTO_INCREMENT , 
        `queue` INT( 7 ) NOT NULL DEFAULT 0 , 
        `name` varchar( 32 ) NOT NULL DEFAULT "",
        `path` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
        `src` VARCHAR( 60 ) NOT NULL DEFAULT "" , 
        `icon` VARCHAR( 60 ) NOT NULL DEFAULT "" , 
        `desp` TEXT NOT NULL , 
        `valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
        `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        PRIMARY KEY ( `id` )',

	/** 
	 * 标签属性
	 */
	'tag' => '
		`id` INT( 4 ) NOT NULL AUTO_INCREMENT , 
		`cid` VARCHAR( 40 ) NOT NULL DEFAULT "" ,  
		`tid` INT( 4 ) NOT NULL DEFAULT 0 ,  
		`queue` INT( 7 ) NOT NULL DEFAULT 0 , 
		`name` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
		`define` INT( 3 ) NOT NULL , 
		`mark` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
		`path` VARCHAR( 50 ) NOT NULL DEFAULT "" , 
		`valid` TINYINT( 1 ) NOT NULL DEFAULT 0 , 
		`fields` MEDIUMTEXT NOT NULL , 
		PRIMARY KEY ( `id` )',

);
?>