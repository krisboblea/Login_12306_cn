<?php
class IndexAction extends Action
{
	public function _initialize() {
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
		R('Public', 'web');
	}
    public function Index()
    {
		dcookie('pwd',null);
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
		$user=dcookie('user');
		if(!dcookie('user')||!dcookie('pwd')){
			dcookie('user',request('user'),999000);
			dcookie('pwd',request('pwd'));
			dcookie('code',request('code'));
			$user=request('user');
			$msg='欢迎使用^_^<br/>';
			$start=true;
		}
		if(!$user){
			$msg.='请输入正确的登录信息<br/>并保证浏览器开启了Cookies!';
			$info['error']=2;
			paidui($user,3);
			$this->assign('info',$info);
			$this->assign('msg',$msg);
			$this->display();
			die();
		}
		$M=M('paidui');
		$_time=date('Y-m-d H:i:s', time()-40);
		$_time2=date('Y-m-d H:i:s', time()-1200);
		$_limit=C('limit_login');
		paidui($user,1);
		$rs=$M->getByUser($user);
		$count=$M->where("last_time>'".$_time."' and enter_time<'".$rs['enter_time']."' and step=1")->count();
		$count2=M('paidui')->where("last_time>'".$_time."' and step=2")->count();
		if($count<4&&$count2<=$_limit){
			$msg='<b>正在启动……</b><br/>';
			$info['error']=0;
		}else{
			$info['error']=1;
			$show_rs=request('i');
			//if($show_rs&&$count>$show_rs)	$count=intval($show_rs);
			$msg.='<b>正在排队进入系统，请耐心等待</b><br/>您前面还有'.($count+1).'位用户……<br/><br/>';
			$this->assign('show_rs',$count);
		}
        $this->assign('info',$info);
        $this->assign('msg',$msg);
        $this->display();
    }
    public function Count()
    {
		$_limit=C('limit_login');
		$M=M('paidui');
		$user=$_GET['user'];
		$rs=$M->getByUser($user);
		$_time=date('Y-m-d H:i:s', time()-40);
		$_time2=date('Y-m-d H:i:s', time()-1200);
		$count1=M('paidui')->where("last_time>'".$_time."' and step=1")->count();
		$count2=M('paidui')->where("last_time>'".$_time."' and step=2")->count();
		$count=M('paidui')->where("last_time>'".$_time."' and enter_time<'".$rs['enter_time']."' and step=1")->count();

		echo '系统可容纳'.$_limit.'人。有'.$count1.'位用户等待，有 '.$count2.'位用户已经进入系统.'.$user.'前有'.$count.'位用户<br/>'.$M->getLastSql();
    }
    public function Login()
    {
		$user=dcookie('user');
		$pwd=dcookie('pwd');
		$code=dcookie('code');
		if(!$user||!$pwd){
			$msg.='请输入正确的登录信息';
			$info['error']=1;
			paidui($user,3);
			$this->assign('info',$info);
			$this->assign('msg',$msg);
			$this->display();
			die();
		}
		paidui($user,2);
		$url = "https://dynamic.12306.cn/otsweb/loginAction.do?method=loginAysnSuggest";
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
		$r = curl_exec($ch);
		curl_close($ch);
		preg_match('|loginRand":"(.+?)",|is',$r,$loginrand);

		$cookie_file = dirname(__FILE__).'/../../../Public/Cookies/'.$user.'_cook.txt';
		$p['loginUser.user_name']=$user;
		$p['user.password']=$pwd;
		$p["loginRand"]=$loginrand[1];
		$p["org.apache.struts.taglib.html.TOKEN"]=request("token");
		$p['randCode']=$code;
		$i=intval(request('i'))+1;
		$url = "https://dynamic.12306.cn/otsweb/loginAction.do?method=login";
		//https://dynamic.12306.cn/otsweb/loginAction.do?method=initForMy12306
		if($i==1){
			$url = "https://dynamic.12306.cn/otsweb/loginAction.do?method=initForMy12306";
			$p=null;
		}
		set_time_limit(9);
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
		$r = curl_exec($ch);
		curl_close($ch);

		if(strpos($r,'欢迎您')){
			preg_match('|class="text_16">(.+?)，欢迎您！</h1>|is',$r,$info);
			$msg=$info[1].'恭喜登录成功!';
			$this->assign('user',$user);
			$info['success']=1;
		}else{
			preg_match('|style="color: red;">(.+?)</span>|is',$r,$info);
			preg_match('|var message = "(.+?)";|is',$r,$info1);
			preg_match('|taglib.html.TOKEN" value="(.+?)">|is',$r,$info['token']);
			if(strpos($info1[1],'var')) $info1[1]='';
			$jinfo=$info1[1].$info[1];
			if(strpos($jinfo,'正确')||strpos($jinfo,'错误')||strpos($jinfo,'不存在')||strpos($jinfo,'超过')||strpos($jinfo,'锁定')){
				$msg.=$jinfo;
				paidui($user,3);
				$info['error']=1;
			}else{
				$info['error']=0;
				if(strpos($jinfo,'刷新页面')) $jinfo='当前访问用户过多';
				$msg.='<b>请不要关闭浏览器，正在使用'.$user.'加油登录中……</b><br/>第'.$i.'次尝试……<br/>12306返回:'.$jinfo.'<br/><br/>铁道部坑爹？点点下面的广告来增加人品吧～/微笑';
				//echo $p;
				if($i>=50){
					$info['error']=1;
					$msg='<b>已经尝试使用'.$user.'登录50次，但仍未成功，请返回重试……</b><br/>请确保用户名('.$user.')及密码('.$pwd.')正确！<br/><br/>';
				}
				$this->assign('i',$i);
				//if(!$info[1]) dump($r);
			}
		}
        $this->assign('info',$info);
        $this->assign('msg',$msg);
        $this->display();
    }
    public function Ok()
	{
		$user=$_GET['user'];
		paidui($user,3);
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
		$url = "https://dynamic.12306.cn/otsweb/passCodeAction.do?rand=lrand";
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