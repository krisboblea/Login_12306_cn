<?php
class OrderAction extends Action
{
	public function _initialize() {
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
		R('Public', 'web');
	}
    public function Index()
    {
		dcookie('pcode',null);
		$_limit=C('limit_login');
		$_time1=date('Y-m-d H:i:s', time()-40);
		$_time2=date('Y-m-d H:i:s', time()-40);
		$_time3=date('Y-m-d H:i:s', time()-1860);
		$count1=M('paidui')->where("last_time>'".$_time1."' and step=1")->count();
		$count2=M('paidui')->where("last_time>'".$_time2."' and step=2")->count();
		$count3=M('paidui')->where("last_time>'".$_time3."' and step=3")->count();
		$sysinfo='欢迎使用12306登录助手，当前有'.$count1.'位用户排队，'.$count2.'位用户正在登录，半小时内帮助超过'.$count3.'位用户成功登录。';
		if($count1==0) $sysinfo='欢迎使用12306登录助手，当前系统非常畅通，可快速帮您登录12306。半小时内帮助超过'.$count3.'位用户成功登录。';
		$this->assign('sysinfo',$sysinfo);
        $this->display();
    }
    public function Start()
    {
		$M=M('Order');
		$data['user']=request('user');
		$data['email']=request('email');
		$data['seat']=request('seat');
		$data['ticket']=request('ticket');
		$data['passenger']=request('passenger');
		$data['train_no']=request('train_no');
		$data['train_date']=request('train_date');
		$data['from_station_telecode']=request('from_station_telecode');
		$data['to_station_telecode']=request('to_station_telecode');
		$data['pcode']=request('code');
		$id=$M->add($data);
		$user=request('user');
		$msg='欢迎使用^_^<br/>';
		$cookie_file = dirname(__FILE__).'/../../../Public/Cookies/'.$user.'_cook.txt';
		$url = "https://dynamic.12306.cn/otsweb/loginAction.do?method=initForMy12306";
		$p=null;
		$r=postData($url,$p,$cookie_file);
		if(strpos($r,'欢迎您')){
			preg_match('|class="text_16">(.+?)，欢迎您！</h1>|is',$r,$info);
			$msg=$info[1].'欢迎您使用!';
			$msg.='<br/><b>正在启动……</b><br/>';
			$info['error']=0;
		}else{
			$msg.='<br/><b>登录失败，请先使用登录助手登录</b><br/>';
			$info['error']=1;
		}
        $this->assign('info',$info);
        $this->assign('id',$id);
        $this->assign('msg',$msg);
        $this->display();
    }
    public function Quire()
    {
		$M=M('Order');
		$id=request('id');
		$rs=$M->find($id);
		$user=$rs['user'];
		if(!$user||!$rs['pcode']){
			$msg='无登录信息，请返回<br/>';
			$info['error']=1;
		}
	$p['orderRequest.train_date']=$rs['train_date'];
	$p['orderRequest.from_station_telecode']=$rs['from_station_telecode'];
	$p['orderRequest.to_station_telecode']=$rs['to_station_telecode'];
	$p['orderRequest.train_no']=$rs['train_no'];
	$p['trainPassType']='QB';
	$p['includeStudent']=$rs['includeStudent'];
	$p['orderRequest.start_time_str']='00:00--24:00';
	$p['trainClass']=$rs['trainClass'];
	$p2 = http_build_query($p);
		$url = "https://dynamic.12306.cn/otsweb/order/querySingleAction.do?method=queryLeftTicket";
		$cookie_file = dirname(__FILE__).'/../../../Public/Cookies/'.$user.'_cook.txt';
		$r=postData($url,$p,$cookie_file);
		//echo ($r);
		$r=str_replace('\'','"',$r);
		$r = preg_replace( "@<font(.*?)>@is", "", $r ); 
		$r = preg_replace( "@</font>@is", "", $r ); 
		$r=explode('\n',$r);
		$i=0;
		$sel=-1;
		foreach($r as $vo){
			preg_match('|>(.+?)</span>|is',$vo,$pinfo[$i]['no']);
			preg_match('|getSelected\("(.+?)"|is',$vo,$pinfo[$i]['data']);
			preg_match('|--,--,--,--,--,(.+?)<input type=|is',$vo,$pinfo[$i]['info']);
			$pinfo[$i]['info'][1]=str_replace('--','无',$pinfo[$i]['info'][1]);
			$pinfo[$i]['p']=explode(',',$pinfo[$i]['info'][1]);
			$pinfo[$i]['rs']=explode('#',$pinfo[$i]['data'][1]);
			if($pinfo[$i]['p'][1]!='无'&&$pinfo[$i]['p'][1]) $sel=$i;
			//echo $sel;
			$i++;
		}
		//dump($pinfo);
		if($sel!='-1'){
			$msg='有预选票，进行预订!';
			$msg.='<br/><b>正在启动……</b><br/>';
			$info['error']=2;
			$i=$sel;
$p['station_train_code']=$pinfo[$i]['rs'][0];
$p['trainno']=$pinfo[$i]['rs'][3];
$p['train_date']='2012-01-22';
//$p['round_train_date']='2012-01-11';
$p['from_station_telecode']=$pinfo[$i]['rs'][4];
$p['to_station_telecode']=$pinfo[$i]['rs'][5];
$p['include_student']='0X00';
$p['from_station_telecode_name']=$pinfo[$i]['rs'][7];
$p['to_station_telecode_name']=$pinfo[$i]['rs'][8];
$p['round_start_time_str']='00:00--24:00';
$p['single_round_type']='1';
$p['train_pass_type']='QB';
$p['train_class_arr']='QB#D#Z#T#K#QT#';
$p['start_time_str']='00:00--24:00';
$p['lishi']=$pinfo[$i]['rs'][1];
$p['train_start_time']=$pinfo[$i]['rs'][2];
$p['arrive_time']=$pinfo[$i]['rs'][6];
$p['from_station_name']=$pinfo[$i]['rs'][7];
$p['to_station_name']=$pinfo[$i]['rs'][8];
$p['ypInfoDetail']=$pinfo[$i]['rs'][9];
	$url = "https://dynamic.12306.cn/otsweb/order/querySingleAction.do?method=submutOrderRequest";
	$r2=postData($url,$p,$cookie_file);
	$rs['data']=serialize($p);
	$M->save($rs);
			$msg.='查询成功，准备预订';
			$email=$rs['email'];
			$email_status=S('email_'.$email);
			if(!$email_status){
				sendmail($email,'硬卧'.$pinfo[$i]['p'][1].'-软卧'.$pinfo[$i]['p'][0].'-硬座'.$pinfo[$i]['p'][3],'速度订票');
				S('email_'.$email,true,60);
			}
			$info['success']=1;
		}elseif(!$pinfo[0]['info'][1]){
			$msg.='<br/><b>每天次日列车信息</b><br/>';
			$info['error']=1;
		}else{
			$msg.='<br/><b>当前信息</b><br/>';
			$info['error']=0;
		}
        $this->assign('pinfo',$pinfo);
        $this->assign('info',$info);
        $this->assign('id',$id);
        $this->assign('msg',$msg);
        $this->display();
    }
    public function Order()
    {
		$M=M('Order');
		$id=request('id');
		$rs=$M->find($id);
		$user=$rs['user'];
		if(request('act')=='pcode'){
			$rs['pcode']=request('code');
			$M->Save($rs);
		}
		if(!$rs){
			$msg.='系统出错';
			$info['error']=1;
			$this->assign('info',$info);
			$this->assign('msg',$msg);
			$this->display();
			die();
		}
		if(!dcookie('user')||!dcookie('pcode')){
			$msg='无登录信息，请返回<br/>';
			$info['error']=1;
		}
		$user=dcookie('user');
		$cookie_file = dirname(__FILE__).'/../../../Public/Cookies/'.$user.'_cook.txt';
		$url = "https://dynamic.12306.cn/otsweb/order/confirmPassengerAction.do?method=init";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
		$r2 = curl_exec($ch);
		curl_close($ch);
		//dump($r2);
		if(!strpos($r2,'车票预订')){
			$msg='页面载入失败!';
			$info['error']=0;
			$this->assign('msg',$msg);
			//$info['success']=1;
		}else{
			$msg='结果:';
			preg_match('|<td>硬卧(.+?)票</td>|is',$r2,$info[]);
			preg_match('|<td>软卧(.+?)票</td>|is',$r2,$info[]);
			preg_match('|<td>硬座(.+?)票</td>|is',$r2,$info[]);
			preg_match('|train_date_str_ = "(.+?)";|is',$r2,$info['train_date']);
			preg_match('|station_train_code_ = "(.+?)";|is',$r2,$info['train']);
			preg_match('|from_station_name_ = "(.+?)";|is',$r2,$info['from']);
			preg_match('|to_station_name_ = "(.+?)";|is',$r2,$info['to']);
			preg_match('|taglib.html.TOKEN" value="(.+?)">|is',$r2,$info['token']);
			if(!strpos($info[3][1],'无')){
				$info['success']=1;
				$msg='前往订票';
			}else{
				$info['error']=0;
			}

		}
        $this->assign('info',$info);
        $this->assign('id',$id);
        $this->assign('msg',$msg);
        $this->display();
    }
    public function Submit()
    {
		$M=M('Order');
		$id=request('id');
		$rs=$M->find($id);
		$user=$rs['user'];
		if(request('act')=='pcode'){
			$rs['pcode']=request('code');
			$M->Save($rs);
		}
		$data=unserialize($rs['data']);
		$cookie_file = dirname(__FILE__).'/../../../Public/Cookies/'.$user.'_cook.txt';
$p['org.apache.struts.taglib.html.TOKEN']=request('token');
$p['randCode']=$rs['pcode'];
$p['orderRequest.train_date']=$rs['train_date'];
$p['orderRequest.train_no']=$rs['train_no'];
$p['orderRequest.station_train_code']=$data['station_train_code'];
$p['orderRequest.from_station_telecode']=$data['from_station_telecode'];
$p['orderRequest.to_station_telecode']=$data['to_station_telecode'];
$p['orderRequest.bed_level_order_num']='000000000000000000000000000000';
$p['orderRequest.start_time']=$data['train_start_time'];
$p['orderRequest.end_time']=$data['arrive_time'];
$p['orderRequest.from_station_name']=$data['from_station_name'];
$p['orderRequest.to_station_name']=$data['to_station_name'];
$p['orderRequest.cancel_flag']='1';
$p['orderRequest.id_mode']='Y';
$p['passengerTickets']=$rs['seat'].','.$rs['ticket'].','.$rs['passenger'];
$temp=explode(',',$rs['passenger']);
$p['oldPassengers']=$temp[0].','.$temp[1].','.$temp[2];
$p['passenger_1_seat']=$rs['seat'];
$p['passenger_1_ticket']=$rs['ticket'];
$p['orderRequest.reserve_flag']='A';
		$i=intval(request('i'))+1;
		//dump($p);
		$url = "https://dynamic.12306.cn/otsweb/order/confirmPassengerAction.do?method=confirmPassengerInfoSingle";
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
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //使用上面获取的cookies
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
		$r = curl_exec($ch);
		curl_close($ch);


			preg_match('|var message = "(.+?)";|is',$r,$info1);
			if(strpos($info1[1],'var')) $info1[1]='';
		if(strpos($r,'订单信息')){
			$msg='订票成功!';
			sendmail($rs['email'],'订票成功','请速度上网完成支付');
			$info['error']=0;
			$this->assign('msg',$msg);
		}elseif(strpos($r,'系统忙')){
			$msg='系统忙，重试……';
			$info['error']=2;
		}elseif(strpos($info1[1],'验证码')){
			$msg=$info1[1].'，请输入验证码：';
			$info['error']=3;
		}elseif(strpos($info1[1],'重复提交')){
			$msg='请不要重复提交，无奈铁道部……';
			$info['error']=2;
		}elseif(strpos($info1[1],'未处理的订单')){
			dump($r);
			$msg='您的账户中可能存在未处理的订单，或所需座席已经售空，请返回验证码环节。';
			$info['error']=1;
		}elseif($info1[1]){
			$msg=$info1[1];
			$info['error']=2;
		}else{
			$msg='订票出错，重试……';
			dump($r);
			$info['error']=2;
		}
        $this->assign('info',$info);
        $this->assign('id',$id);
        $this->assign('user',$user);
        $this->assign('msg',$msg);
        $this->display();
    }
    public function Ok()
	{
		$user=$_GET['user'];
		$cookie_file = dirname(__FILE__).'/../../../Public/Cookies/'.$user.'_cook.txt';
		$cook=file_get_contents($cookie_file);
		preg_match('|JSESSIONID\t(.+?)\n|is',$cook,$Pid);
		preg_match('|BIGipServerotsweb\t(.+?)\n|is',$cook,$Pid2);
        $this->assign('j_id',$Pid[1]);
        $this->assign('j_b',$Pid2[1]);
        $this->assign('msg',request("msg"));
        $this->display();

	}
    public function code()
    {
		header("Content-type: image/gif");
		set_time_limit(9);
		$cookie_file = dirname(__FILE__).'/../../../Public/Cookies/'.$_GET['user'].'_cook.txt';
		$url = "https://dynamic.12306.cn/otsweb/passCodeAction.do?rand=randp";
		$ch = curl_init($url); //初始化
		curl_setopt($ch, CURLOPT_HEADER, 0); //不返回header部分
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); //返回字符串，而非直接输出
		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
		curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
		curl_exec($ch);
		curl_close($ch);
    }


}
?>