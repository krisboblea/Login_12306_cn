<?php 
// 获取客户端IP地址
function get_client_ip() {
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];
	else
		$ip = "unknown";
	return($ip);
} 

/**
 * GET/POST重写
 */
function Request($key, $m = 'request') {
	$v = "";
	switch ($m) {
		case 'get':
			if (isset($_GET[$key])) $v = $_GET[$key];
			break;
		case 'post':
			if (isset($_POST[$key])) $v = $_POST[$key];
			break;
		default:
			if (isset($_REQUEST[$key])) $v = $_REQUEST[$key];
	} 
	return $v;
} 
function admin_qx($allrole) {
	$role = $_SESSION['admin_role']; 
	// echo stripos($allrole,$role).">".$allrole.">".$role;
	if (!stripos("{" . $allrole, $role)) die("deney Access!<p>" . stripos($allrole, $role) . ">" . $allrole . ">" . $role);
} 

function paidui($user,$step) {
	$M=M('paidui');
	$rs=$M->getByUser($user);
	$_time=date('Y-m-d H:i:s', time()-40);
	if($rs){
		if($rs['last_time']<=$_time) $rs['enter_time']=tim();
		$rs['last_time']=tim();
		$rs['step']=$step;
		$M->save($rs);
	}else{
		$rs['user']=$user;
		$rs['enter_time']=tim();
		$rs['last_time']=tim();
		$rs['step']=$step;
		$M->add($rs);
	}
} 

// 发送邮件函数
function sendmail($sendTo, $subject, $content) {
		$web['name'] = "12306.CN 助手";
		$web['smtp_host'] = "smtp.exmail.qq.com";
		$web['smtp_user'] = "post@xxx.com";
		$web['smtp_pass'] = "111111";
		$web['smtp_from'] = "post@xxx.com";
	import('ORG.Net.Phpmailer');

	$mail = new phpmailer;
	$mail -> IsSMTP();
	$mail -> CharSet = "UTF-8";
	$mail -> Host = $web['smtp_host'];
	$mail -> SMTPAuth = true;
	$mail -> Username = $web['smtp_user'];
	$mail -> Password = $web['smtp_pass'];
	$mail -> From = $web['smtp_user'];
	$mail -> FromName = $web['name'];
	$mail -> AddAddress($sendTo);
	//$mail -> AddReplyTo($web['smtp_from'], $web['name']);
	$mail -> IsHTML(true);
	$mail -> Subject = $subject;
	$mail -> Body = $content; 
	// $mail->AltBody = $content;
	if ($mail -> Send()) {
		return true;
	} else {
		return false;
	} 
} 
function tim() {
	return date('Y-m-d H:i:s', time());
} 
function postData($url, $p,$cookie_file) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url); 
	if($p){
		$p = http_build_query($p);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $p);
	}else{
		curl_setopt($ch, CURLOPT_POST, 0);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if($cookie_file){
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
	curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
	}
	$r = curl_exec($ch);
	curl_close($ch);
	return $r;
} 
/**
 * 远程打开URL
 * 
 * @param string $url 打开的url，　如 http://www.baidu.com/123.htm
 * @param int $limit 取返回的数据的长度
 * @param string $post 要发送的 POST 数据，如uid=1&password=1234
 * @param string $cookie 要模拟的 COOKIE 数据，如uid=123&auth=a2323sd2323
 * @param bool $bysocket TRUE/FALSE 是否通过SOCKET打开
 * @param string $ip IP地址
 * @param int $timeout 连接超时时间
 * @param bool $block 是否为阻塞模式
 * @return 取到的字符串
 */
function uc_fopen($url, $post = '', $ip = '', $limit = 0, $cookie = '', $bysocket = false, $timeout = 15, $block = true) {
	$return = '';
	$matches = parse_url($url);
	!isset($matches['host']) && $matches['host'] = '';
	!isset($matches['path']) && $matches['path'] = '';
	!isset($matches['query']) && $matches['query'] = '';
	!isset($matches['port']) && $matches['port'] = '';
	$host = $matches['host'];
	$path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
	$port = !empty($matches['port']) ? $matches['port'] : 80;
	if ($post) {
		$out = "POST $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n"; 
		// $out .= "Referer: $boardurl\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "User-Agent: MFPad_Server/v2 (z@desenz.com)\r\n";
		$out .= "Host: $host\r\n";
		$out .= 'Content-Length: ' . strlen($post) . "\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cache-Control: no-cache\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
		$out .= $post;
	} else {
		$out = "GET $path HTTP/1.0\r\n";
		$out .= "Accept: */*\r\n"; 
		// $out .= "Referer: $boardurl\r\n";
		$out .= "Accept-Language: zh-cn\r\n";
		$out .= "User-Agent: $_SERVER[HTTP_USER_AGENT]\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Cookie: $cookie\r\n\r\n";
	} 
	$fp = @fsockopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout);
	if (!$fp) {
		return ''; //note $errstr : $errno \r\n
	} else {
		stream_set_blocking($fp, $block);
		stream_set_timeout($fp, $timeout);
		@fwrite($fp, $out);
		$status = stream_get_meta_data($fp);
		if (!$status['timed_out']) {
			while (!feof($fp)) {
				if (($header = @fgets($fp)) && ($header == "\r\n" || $header == "\n")) {
					break;
				} 
			} 

			$stop = false;
			while (!feof($fp) && !$stop) {
				$data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
				$return .= $data;
				if ($limit) {
					$limit -= strlen($data);
					$stop = $limit <= 0;
				} 
			} 
		} 
		@fclose($fp);
		return $return;
	} 
} 

function Ymd($date) {
	return date('Y-m-d', $date);
} 

function wordcut($str, $len, $add = true) {
	if (utf8_strlen($str) < $len) {
		return $str;
	} else {
		$i = 0;
		$newword = '';
		while ($i < $len) {
			if (ord($str[$i]) > 224) {
				$newword .= $str[$i] . $str[$i + 1] . $str[$i + 2];
				$i = $i + 3;
			} else if (ord($str[$i] > 192)) {
				$newword .= $str[$i] . $str[$i + 1];
				$i = $i + 2;
			} else {
				$newword .= $str[$i];
				$i++;
			} 
		} 
		if ($add) {
			return $newword . '...';
		} else
			return $newword;
	} 
} 

function friendlydate($the_time) {
	$now_time = date("Y-m-d H:i:s", time()); 
	// $now_time = date("Y-m-d H:i:s",time()+8*60*60);
	$now_time = strtotime($now_time);
	$show_time = strtotime($the_time);
	$dur = $now_time - $show_time;
	if ($dur < 0) {
		return $the_time;
	} else {
		if ($dur < 60) {
			return $dur . '秒前';
		} else {
			if ($dur < 3600) {
				return floor($dur / 60) . '分钟前';
			} else {
				if ($dur < 86400) {
					return floor($dur / 3600) . '小时前';
				} else {
					if ($dur < 259200) { // 3天内
						return floor($dur / 86400) . '天前';
					} else {
						return $the_time;
					} 
				} 
			} 
		} 
	} 
}

/**
 * +----------------------------------------------------------
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
+----------------------------------------------------------
 * 
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * +----------------------------------------------------------
 * @return string +----------------------------------------------------------
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1:
			$chars = str_repeat('0123456789', 3);
			break;
		case 2:
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3:
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 4:
			$chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
			break;
		default : 
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	} 
	if ($len > 10) { // 位数过长重复字符串一定次数
		$chars = $type == 1? str_repeat($chars, $len) : str_repeat($chars, 5);
	} 
	if ($type != 4) {
		$chars = str_shuffle($chars);
		$str = substr($chars, 0, $len);
	} else {
		// 中文随机字
		for($i = 0;$i < $len;$i++) {
			$str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8')-1)), 1);
		} 
	} 
	return $str;
} 

/**
 * +----------------------------------------------------------
 * 获取登录验证码 默认为4位数字
+----------------------------------------------------------
 * 
 * @param string $fmode 文件名
+----------------------------------------------------------
 * @return string +----------------------------------------------------------
 */
function build_verify ($length = 4, $mode = 1) {
	return rand_string($length, $mode);
} 

/**
 * +----------------------------------------------------------
 * Cookie 设置，获取，清除 ，支持数组，对象直接设置
 * +----------------------------------------------------------
 * 可通过config.php或调用参数进行相关设置
+----------------------------------------------------------
 * 
 * @param mixed $name : cookie名称
 * 获取某个cookie: cookie('name')
 * 清空所有包含当前设置前缀的cookie: cookie(null)
 * 删除指定前缀的所有相关cookie: cookie(null,'think_')
 * 注：基于默认配置设置原则，前缀将不区分大小写
+----------------------------------------------------------
 * @param mixed $value 要设置的cookie值
设置某cookie值: cookie('name','value') 或 cookie('name','value',3600)
 * 删除指定cookie: cookie('name',null)
 * +----------------------------------------------------------
 * @param mixed $option 设置，若存在该参数，则与默认值进行整合
可用设置prefix,expire,path,domain,base64
 * 支持数组形式:cookie('name','value',array('expire'=>1,'prefix'=>'think_'))
 * 支持query形式字符串:cookie('name','value','prefix=tp_&expire=10000')
 * 支持直接设置保存时间 cookie('name','value',3600)或cookie('name','value','3600')
 * +----------------------------------------------------------
 * @return void +----------------------------------------------------------
 */
function dcookie($name, $value = '', $option = null) {
	// 默认设置
	$config = array('prefix' => C('COOKIE_PREFIX'), // cookie 名称前缀
		'expire' => C('COOKIE_EXPIRE'), // cookie 保存时间
		'path' => C('COOKIE_PATH'), // cookie 保存路径
		'domain' => C('COOKIE_DOMAIN'), // cookie 有效域名
		); 
	// 参数设置(会覆盖黙认设置)
	if (!empty($option)) {
		if (is_numeric($option))
			$option = array('prefix' => $option);
		elseif (is_string($option))
			parse_str($option, $option);
		array_merge($config, array_change_key_case($option));
	} 
	// 清除指定前缀的所有cookie
	if (is_null($name)) {
		if (empty($_COOKIE)) return; 
		// 要删除的cookie前缀 不指定则删除配置设置的指定前缀
		$prefix = empty($value)? $config['prefix'] : $value;
		foreach($_COOKIE as $key => $val) {
			if (0 === stripos($key, $prefix)) {
				setcookie($_COOKIE[$key], '', time()-3600, $config['path'], $config['domain']);
				unset($_COOKIE[$key]);
			} 
		} 
		return;
	} 
	$name = $config['prefix'] . $name;
	if ($value === '') {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; // 获取指定Cookie
	} else {
		if (is_null($value)) {
			setcookie($name, '', time()-3600, $config['path'], $config['domain']);
			unset($_COOKIE[$name]); // 删除指定cookie
		} else {
			// 设置cookie
			$expire = !empty($config['expire'])? time() + intval($config['expire']):0;
			setcookie($name, $value, $expire, $config['path'], $config['domain']);
			$_COOKIE[$name] = $value;
		} 
	} 
} 

?>