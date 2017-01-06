<?php
class PublicAction extends Action {
	public function web() {
		$web['name'] = "12306.CN 助手";
		$web['smtp_host'] = "smtp.exmail.qq.com";
		$web['smtp_user'] = "post@mfpad.com";
		$web['smtp_pass'] = "111111";
		$web['smtp_from'] = "post@mfpad.com";
		$this -> assign("web", $web); 
	} 
} 

?>