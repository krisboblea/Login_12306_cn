<?php
class Email {
	protected $loc_host = "MFPad_Mailer";
	protected $smtp_host = null;
	protected $smtp_user = null;
	protected $smtp_pass = null;
	protected $from      = null;
	protected $fromname      = null;
	protected $charset   = 'GB2312';
	protected $contentType = "html";
	
	public $sendTo  = "";
	public $subject = "";
	public $content = "";
	
	
	
	public function setConfig($key, $value)
	{
		$this->$key = $value;
	}
	
	
	public function send() {
		 $contentType = ('html' == strtolower($this->contentType))? 'text/html':'text/plain';
		 $space = "\r\n";
		 
   		 $headers  = sprintf("Content-Type:%s; charset=%s\r\nContent-Transfer-Encoding: base64", $contentType, $this->charset);         $hdr      = explode($space, $headers);

   		 if($this->content) $bdy = preg_replace("/^\./", "..", explode($space, $this->content));
		 
		 /** 电子邮件数据 */
         $smtp[] = array("EHLO {$this->loc_host}{$space}","220,250","HELO error: ");
		 $smtp[] = array("AUTH LOGIN{$space}","334","AUTH error:");
		 $smtp[] = array(base64_encode($this->smtp_user).$space, "334","AUTHENTIFICATION error : ");
		 $smtp[] = array(base64_encode($this->smtp_pass).$space, "235","AUTHENTIFICATION error : ");
        
         $smtp[] = array("MAIL FROM: <{$this->from}>{$space}", "250","MAIL FROM error: ");
         $smtp[] = array("RCPT TO: <{$this->sendTo}>{$space}", "250","RCPT TO error: ");
         $smtp[] = array("DATA{$space}", "354", "DATA error: ");
		 
         $smtp[] = array("From: {$this->fromname} <{$this->from}>{$space}", "", "");
         $smtp[] = array("To: {$this->sendTo}{$space}", "", "");
         $smtp[] = array("Subject: {$this->subject}{$space}", "", "");
		 
         foreach($hdr as $h)
		 {
			 $smtp[] = array($h.$space, "", "");
		 }
		 
         $smtp[] = array($space,"","");
		 
		 
         if($bdy) {foreach($bdy as $b) {$smtp[] = array(base64_encode($b.$space).$space, "", "");}}
		 
         $smtp[] = array(".{$space}", "250", "DATA(end)error: ");
         $smtp[] = array("QUIT{$space}", "221", "QUIT error: ");

         /** 创建socket对象 */
         $fp = @fsockopen($this->smtp_host, 25);
         if (!$fp) return "Error: Cannot conect to {$this->smtp_host}";	//服务器连接失败
		 
         while($result = @fgets($fp, 1024)){
			 if(substr($result, 3, 1) == " ") break;
		 }

         $result_str="";        
         foreach($smtp as $req){                 
			@fputs($fp, $req[0]);
			
			if($req[1]){                        
				while($result = @fgets($fp, 1024)){
					if(substr($result,3,1) == " ") break;
				};
			
				if (!strstr($req[1], substr($result, 0, 3))){
					$result_str .= $req[2].$result."";
				}
			}
         }
        @fclose($fp);

        return (0 == strlen($result_str))? true:$result_str;
	}
}
?>