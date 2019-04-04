<?php
/**
 * Thanks To : Janu Yoga & Aan Ahmad
 * Date Share : 27-03-2019
**/
date_default_timezone_set("Asia/Jakarta");
class Marlboro
{
	protected $cookie;
	protected $modules;

	public function __construct()
	{
		$this->modules = new modules();
	}

	public function akun()
	{
		$file = "wilganscokies.txt";
		foreach(explode("\n", str_replace("\r", "", file_get_contents($file))) as $a => $data)
		{
			return array("cookie" => @explode("|", trim($data))[0], "email" => @explode("|", trim($data))[1], "password" => @explode("|", trim($data))[2], "deviceid" => @explode("|", trim($data))[3]);
		}
	}

	public function login($email, $password)
	{
		if(@file_exists("wilganscokies.txt") == true && @file_exists("marlboro.txt") == true)
		{
			@unlink("marlboro.txt");
			@unlink("wilganscokies.txt");
		}

		$cook = $this->modules->fetchCookies($this->modules->curl("https://www.marlboro.id", null, false, false, true, array("Host: www.marlboro.id"), 'GET'));
		$headers = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
		$headers[] = "Cookie: decide_session=".$cook['decide_session'];
		$headers[] = "Host: www.marlboro.id";
		$headers[] = "Origin: https://www.marlboro.id";
		$headers[] = "Referer: https://www.marlboro.id/";
		$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36";
		$headers[] = "X-Requested-With: XMLHttpRequest";
		$csrf = $this->modules->getStr($this->modules->curl("https://www.marlboro.id", null, false, false, false, $headers, 'GET'), 'name="decide_csrf" value="', '"', 1, 0);
		$login = $this->modules->curl("https://www.marlboro.id/auth/login", "email=".str_replace("@", "%40", $email)."&password=".$password."&decide_csrf=".$csrf."&ref_uri=%2Fprofile", true, true, true, $headers);
	   	$cookies = $this->modules->fetchCookies($login)['decide_session'];
		$deviceid = $this->modules->fetchCookies($login)['deviceId'];
	    $this->modules->fwrite("wilganscokies.txt", @$cookies."|".$email."|".$password."|".$deviceid);
		return $login;
	}

	public function idVidio()
	{
		$headers = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
		$headers[] = "Cookie: deviceId=".$this->akun()['deviceid']."; decide_session=".$this->akun()['cookie'];
		$headers[] = "Host: www.marlboro.id";
		$listIdVidio = $this->modules->curl("https://www.marlboro.id", null, false, true, false, $headers, 'GET');
		return $listIdVidio;
	}

	public function view($idVidio)
	{
		$headers = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
		$headers[] = "Cookie: deviceId=".$this->akun()['deviceid']."; decide_session=".$this->akun()['cookie'];
		$headers[] = "Host: www.marlboro.id";
		$headers[] = "Origin: https://www.marlboro.id";
		$headers[] = "Referer: https://www.marlboro.id/";
		$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36";
		$headers[] = "X-Requested-With: XMLHttpRequest";
		$csrf = $this->modules->getStr($this->modules->curl("https://www.marlboro.id", null, false, false, false, $headers, 'GET'), 'name="decide_csrf" value="', '"', 1, 0);
		$view = $this->modules->curl("https://www.marlboro.id/article/play-video/".$idVidio, "decide_csrf=".$csrf."&page=full category", false, true, true, $headers);
		return $view;
	}

	public function update($idVidio, $decide_session)
	{
		$headers = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
		$headers[] = "Cookie: ev=1; decide_session=".$decide_session;
		$headers[] = "Host: www.marlboro.id";
		$headers[] = "Origin: https://www.marlboro.id";
		$headers[] = "Referer: https://www.marlboro.id/";
		$headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36";
		$headers[] = "X-Requested-With: XMLHttpRequest";
		$csrf = $this->modules->getStr($this->modules->curl("https://www.marlboro.id", null, false, false, false, $headers, 'GET'), 'name="decide_csrf" value="', '"', 1, 0);
		$update = $this->modules->curl("https://www.marlboro.id/article/play-video/".$idVidio."/update", "decide_csrf=".$csrf."&page=full category", false, true, false, $headers);
		return $update;
	}

	public function getPoint()
	{
		$headers = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8";
		$headers[] = "Cookie: "."deviceId=".$this->akun()['deviceid']."; decide_session=".$this->akun()['cookie'];
		$headers[] = "Host: www.marlboro.id";
		$get = $this->modules->curl("https://www.marlboro.id/profile", null, false, true, false, $headers, 'GET');
		return @$this->modules->getStr($get, 'data-current="', '"', 1, 0);
	}

	public function execute_login($email, $password)
	{
		while (true)
		{
			@$saldo_awal = $this->getPoint();
			$login = $this->login($email, $password);
			@$cookies = $this->modules->fetchCookies($login)['decide_session'];
			@$deviceid = $this->modules->fetchCookies($login)['deviceId'];
			if(strpos($login, '"code":200,"message":"success"'))
			{
				$this->modules->fwrite("wilganscokies.txt", @$cookies."|".$email."|".$password."|".$deviceid.PHP_EOL);
				if(@$this->getPoint() == $saldo_awal)
				{
					print PHP_EOL."Limit Get Point Login...";
					return false;
				}else{
					print PHP_EOL."Success Login!, Point Anda : ".$this->getPoint();
				}
			}else{
				print PHP_EOL."Failed Login";
				return false;
			}
		}
	}

	public function execute_nonton($email, $password)
	{
		print PHP_EOL."Go Bot Nonton";
		sleep(1);echo".";sleep(1);echo".";sleep(1);echo".";sleep(1);
		for($b = 1; $b <= 10; $b++)
		{	
			$saldo_awal = $this->getPoint();
			$idVidio = $this->modules->getStr($this->idVidio(), 'data-stringid="', '"', $b, 0);
			if(!empty($idVidio)){
				$view = $this->view($idVidio);
				$decide_session = $this->modules->fetchCookies($view)['decide_session'];
				if(strpos($view, '"message":"Success to store log play video."'))
				{
					print PHP_EOL."Sedang Menonton : ".$idVidio;
					sleep(35);
					$update = $this->update($idVidio, $decide_session);
					if(strpos($update, '"finished":true'))
					{
						if($this->getPoint() == $saldo_awal)
						{
							print PHP_EOL."Limit Get Point Nonton!, Done : ".$email;
							return false;
						}else{	
							print PHP_EOL."Success Menonton!, Point anda : ".$this->getPoint()." ";
						}	
					}else{
						print PHP_EOL."Failed!".PHP_EOL;
					}
				}elseif(strpos($view, '"message":"Action is not allowed"')){
					print PHP_EOL."Action is not allowed..".$email;
					return false;
				}else{
					print PHP_EOL.$view.PHP_EOL;
				}	
			}else{
				print PHP_EOL."Account Password Wrong";
				return false;
			}
		}
	}
}

class modules 
{
	public function curl($url, $params, $cookie, $cookiefile, $header, $httpheaders, $request = 'POST', $socks = "")
	{
		$cookies = "marlboro.txt";
		$this->ch = curl_init();
			
		curl_setopt($this->ch, CURLOPT_URL, $url);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $request);

		if($cookie == true)
		{	
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookies);
		}

		if($cookiefile == true)
		{
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookies);
		}

		curl_setopt($this->ch, CURLOPT_HEADER, $header);
		@curl_setopt($this->ch, CURLOPT_HTTPHEADER, $httpheaders);

		curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		curl_setopt($this->ch, CURLOPT_PROXY, $socks);
		curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);

		curl_setopt($this->ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$response = curl_exec($this->ch);
		return $response;
		curl_close($this->ch);
	}

	public function getStr($page, $str1, $str2, $line_str2, $line)
	{
		$get = explode($str1, $page);
		$get2 = explode($str2, $get[$line_str2]);
		return $get2[$line];
	}

	public function fetchCookies($source) 
	{
		preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $source, $matches);
		$cookies = array();
		foreach($matches[1] as $item) 
		{
			parse_str($item, $cookie);
			$cookies = array_merge($cookies, $cookie);
		}

		return $cookies;
	}

	public function fwrite($namafile, $data)
	{
		$fh = fopen($namafile, "a");
		fwrite($fh, $data);
		fclose($fh);  
	}
}	

$modules = new modules();
$marlboro = new marlboro();

echo "Input FIle Akun Marlboro : ";
$fileakun = trim(fgets(STDIN));

print PHP_EOL."Total Ada : ".count(explode(PHP_EOL, @file_get_contents($fileakun)))." Akun ".PHP_EOL."Letsgo..";

while(true)
{
	$time = date("Y-m-d H:i:s");
	echo PHP_EOL."Start : ".$time;
	foreach(@explode("\n", @file_get_contents($fileakun)) as $c => $akon)
	{	
		$date = date("Y-m-d H:i:s");
		$email = explode("|", trim($akon))[0];
		$password = explode("|", trim($akon))[1];
		echo PHP_EOL.PHP_EOL."Ekse Akun : ".$email.PHP_EOL;
		$marlboro->execute_login($email, $password);
		$marlboro->execute_nonton($email, $password);
	}
	
	echo PHP_EOL.PHP_EOL."Sleep Time : ".$date;
	print PHP_EOL."All Done Run!, Sleep 24 Hours";
	print PHP_EOL."Start Besok : ".date('Y-m-d H:i:s', time() + (60 * 60 * 24));
	sleep(86400);
}

?>
