<?php
return array(
	'APP_DEBUG' => false, // 开启调试模式
    'DATA_CACHE_TIME'		=> -1,      // 数据缓存有效期
    'limit_login'		=> 5,      // 同时登录限制人数
	'db_type'=> 'mysql',          // 数据库类型
	'db_host'=> 'localhost', // 数据库服务器地址
	'db_name'=>'12306',  // 数据库名称
	'db_user'=>'test', // 数据库用户名
	'db_pwd'=>'test', // 数据库密码
	'db_port'=>'3306', // 数据库端口
	'db_prefix'=>'12306_', // 数据表前缀);	
    'COOKIE_PREFIX'         => '12306_',      // Cookie前缀 避免冲突
    'COOKIE_EXPIRE'         => 360000,    // Coodie有效期
    'TMPL_TEMPLATE_SUFFIX'  => '.dtp',     // 默认模板文件后缀
	'URL_MODEL' => 1,
	'URL_DISPATCH_ON'=>true,
	'URL_ROUTER_ON'=>true, 
    'DEFAULT_MODULE'        => 'Index', // 默认模块名称
    'DEFAULT_ACTION'        => 'Index', // 默认操作名称
    'TMPL_PARSE_STRING'     => array(
			'__PUBLIC__' => '/12306/Public',
			'__LOC__' => '/Public',
			),
	'TOKEN_ON'=>false,  // 是否开启令牌验证
	'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
	'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则默认为MD5
	'LANG_SWITCH_ON'=>True,
);
?>