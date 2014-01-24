<?php

class Runtastic
{
	const BASE_URL = 'https://www.runtastic.com';
	protected $last_headers;
	protected $user_name;
	protected $curl_session;
	protected $login_target;
	protected $session_id;

	protected function Init($session_id)
	{
		$this->curl_session = curl_init(self::BASE_URL);
		$this->session_id = $session_id;
		curl_setopt_array($this->curl_session, array(
				CURLOPT_COOKIEFILE => 'sessions/sess_'.$this->session_id.'.txt',
				CURLOPT_COOKIEJAR => 'sessions/jar_'.$this->session_id.'.txt',
				CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0',
		//		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_AUTOREFERER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
			));
	}

	public function Login($username, $password)
	{
//		$text = file_get_contents('sessions/update.html');
//		$this->FindUsername($text);

		$this->Init(uniqid());
		$form_fields = $this->GetLoginFormFields();
		$this->TryLogin($form_fields, $username, $password);
	}

	public function ReopenSession($session_id)
	{
		$this->Init($session_id);
	}

	public function GetTracks()
	{
		$tracks_page = $this->LoadRelativeUrl('sport-sessions');
		file_put_contents('sessions/tracks_page.html', $tracks_page);
		//$tracks_page = file_get_contents("sessions/tracks_page.html");

		if (!preg_match('|\sindex_data\s*=\s*(.*?);|', $tracks_page, $match))
			throw new Exception("Unable to find tracks list");

		$tracks_data = json_decode($match[1], true);
		if ($tracks_data === false)
			throw new Exception("Failed to parse tracks data. ".$match[1]);
		// Index data in JSON format
		//var index_data = [[11504068,"2012-04-11",3,4,2,63569,5,1,3,0],[13110897,"2012-05-06",34,0,1,0,0,0,0,0],[13245788,"2012-05-08",34,0,1,0,0,0,0,0],[13400777,"2012-05-10",34,0,1,0,0,0,0,0],[13560562,"2012-05-12",19,1,2,0,0,0,4,0],[13612961,"2012-05-13",34,0,1,0,0,0,0,0],[13759104,"2012-05-15",34,0,1,0,0,0,0,0],[14172094,"2012-05-20",34,0,1,0,0,0,0,0],[14329557,"2012-05-22",34,0,1,0,0,0,0,0],[14586170,"2012-05-25",34,0,1,0,0,0,0,0],[14748735,"2012-05-27",34,0,1,0,0,0,0,0],[15110555,"2012-05-31",34,0,1,0,0,0,0,0],[15154931,"2012-05-31",3,2,2,0,5,2,1,0],[15296928,"2012-06-02",3,5,6,0,5,2,1,0],[16082662,"2012-06-12",3,4,3,0,2,2,1,0],[16166241,"2012-06-13",3,4,3,0,1,1,1,0],[16428942,"2012-06-17",34,0,1,0,0,0,0,0],[16461776,"2012-06-17",3,5,5,0,1,1,1,0],[16649639,"2012-06-19",3,3,2,0,1,1,1,0],[16953061,"2012-06-23",3,5,3,0,2,2,1,0],[18171353,"2012-07-08",4,5,3,0,1,1,3,0],[18484555,"2012-07-11",4,3,3,0,1,1,1,0],[20531066,"2012-08-02",3,3,2,13060,5,1,1,0],[23822297,"2012-09-01",3,5,3,13060,1,2,1,0],[24319638,"2012-09-05",3,4,3,13060,1,1,1,0],[24739402,"2012-09-09",3,2,1,13060,1,1,1,0],[24772510,"2012-09-09",3,3,3,13060,1,1,1,0],[25441252,"2012-09-15",3,6,5,13060,1,1,1,0],[27931769,"2012-10-06",3,5,4,13060,1,1,1,0],[28740306,"2012-10-14",3,2,1,0,1,1,1,0],[28843762,"2012-10-14",3,5,5,13060,1,1,1,0],[29513102,"2012-10-20",3,3,2,13060,1,2,1,0],[33770953,"2012-11-25",18,1,3,0,1,0,0,0],[37623119,"2012-12-30",3,4,3,13060,1,1,0,0],[43657969,"2013-02-02",3,3,2,13060,1,2,4,0],[46593520,"2013-02-17",3,4,3,13060,0,1,0,0],[46673461,"2013-02-17",19,1,3,13060,0,1,0,0],[53498466,"2013-03-23",3,2,1,0,5,2,1,0],[54499979,"2013-03-28",3,4,2,23263,5,2,1,0],[54719777,"2013-02-23",9,5,7,3540,0,0,0,0],[54719868,"2013-02-26",9,5,7,14278,0,0,0,0],[54719883,"2013-02-27",9,5,7,14278,0,0,0,0],[54763486,"2013-03-29",3,3,2,13060,0,2,0,0],[54795438,"2013-03-29",3,5,3,23263,5,2,3,0],[55215033,"2013-03-31",3,4,2,13060,5,2,1,0],[55537199,"2013-04-01",19,2,5,13060,1,1,3,0],[56713345,"2013-04-06",3,6,6,13060,1,1,1,0],[59577360,"2013-04-14",3,6,5,13060,1,1,1,0],[60103626,"2013-04-15",3,5,2,13060,1,1,1,0],[61293768,"2013-04-18",3,5,3,13060,1,2,1,0],[61824990,"2013-04-20",3,5,5,13060,2,1,1,0],[62976122,"2013-04-23",3,5,2,13060,1,1,1,0],[63748845,"2013-04-25",3,5,3,13060,1,2,1,0],[65356081,"2013-04-30",3,5,3,13060,1,1,1,0],[65924272,"2013-05-01",3,5,4,13060,1,1,1,0],[67106005,"2013-05-04",3,6,6,13060,5,2,1,0],[68288632,"2013-05-06",3,5,3,0,1,1,1,0],[68682336,"2013-05-07",3,5,3,13060,1,1,1,0],[70185715,"2013-05-11",3,5,4,13060,2,0,1,0],[72325676,"2013-05-16",3,5,3,13060,1,1,1,0],[72939942,"2013-05-18",3,6,5,13060,1,2,1,0],[73491603,"2013-05-20",3,4,2,0,0,0,0,0],[74265547,"2013-05-21",3,5,3,13060,1,1,1,0],[75011967,"2013-05-23",3,5,4,13060,1,2,1,0],[76297939,"2013-05-27",3,6,6,13060,1,1,1,0],[78095230,"2013-06-01",3,6,5,13060,5,1,1,0],[79072632,"2013-06-03",3,5,4,13060,1,1,1,0],[79972731,"2013-06-05",3,5,3,13060,5,1,1,0],[80397781,"2013-06-06",3,5,3,13060,1,1,1,0],[81049177,"2013-06-08",3,6,6,8927,1,1,1,0],[83818874,"2013-06-15",3,2,1,13060,0,1,0,0],[85215730,"2013-06-16",3,6,7,0,1,2,1,0]];

		$new_tracks_data = array();
		foreach ($tracks_data as $t_data)
		{
			$new_tracks_data[] = $t_data[0];
		}
		return array_reverse($new_tracks_data);
	}

	public function GetTrackTCX($track_id)
	{
		$url = 'sport-sessions/'.$track_id.'.tcx';
		$track_content = $this->LoadRelativeUrl($url);

		$filename = $track_id.'.tcx';
		if (isset($this->last_headers['Content-Disposition']))
		{
			$disp_header = $this->last_headers['Content-Disposition'];
			if (preg_match('|filename="(.*?)"|', $disp_header, $matches))
				$filename = $matches[1];
		}

		return array($filename, $track_content);
	}

	public function Logout()
	{
	}

	private function LoadRelativeUrl($url)
	{
		//http://www.runtastic.com/en/users/Alexander-P-2/sport-sessions
		$full_url = self::BASE_URL.'/en/users/'.$this->user_name.'/'.$url;
		curl_setopt($this->curl_session, CURLOPT_URL, $full_url);
		curl_setopt($this->curl_session, CURLOPT_HEADER, true);
		curl_setopt($this->curl_session, CURLOPT_POST, false);

		$res = curl_exec($this->curl_session);
		if ($res === false)
			throw new Exception("Failed to load page $full_url. ".curl_error($this->curl_session));

		list($this->last_headers, $res) = explode("\r\n\r\n", $res, 2);
		$this->last_headers = $this->ParseHeaders($this->last_headers);

		return $res;
	}

	private function GetLoginFormFields()
	{
		curl_setopt($this->curl_session, CURLOPT_URL, self::BASE_URL);
		$res = curl_exec($this->curl_session);
		if ($res === false)
			throw new Exception("Failed to load login page. ".curl_error($this->curl_session));

		//$res = file_get_contents('sessions/login.html');
		file_put_contents('sessions/login.html', $res);

		$found_forms = preg_match_all('|<form[^>]+>.*?</form>|s', $res, $matches, PREG_PATTERN_ORDER);
		if (!$found_forms)
			throw new Exception("Couldn't find any forms on the page");

		$this->login_target = '';
		$form_fields = array();
		foreach ($matches as $match)
		{
			$form_text = $match[0];
			if (!strpos($form_text, 'name="authenticity_token"'))
				continue;

			$form_xml = simplexml_load_string($form_text);
			if ($form_xml === false)
				throw new Exception("Failed to parse the form text");

			$this->login_target = $form_xml['action'];

			$inputs = $form_xml->xpath("//input");
			foreach ($inputs as $input)
			{
				$form_fields[(string)($input['name'])] = (string)($input['value']);
			}
			break;
		}

		if (!$this->login_target)
			throw new Exception("Login form not found");

		return $form_fields;
/*
<form accept-charset="UTF-8" action="/en/d/users/sign_in.json" class="formtastic user" id="login_form" method="post">
	<div style="margin:0;padding:0;display:inline">
		<input name="utf8" type="hidden" value="&#x2713;" />
		<input name="authenticity_token" type="hidden" value="U7+qjpX3N/Ge8SzKA9Hr9y6DbLCm8UGknGLbfpx472U=" />
	</div>
	<fieldset class="inputs">
		<ol>
			<li class="string optional" id="user_email_input">
				<label for="user_email">Email</label>
				<input id="user_email" maxlength="255" name="user[email]" tabindex="10" type="text" />
			</li>
			<p class='forgot_password'>
				<a href="/en/d/users/password/new">Forgot your password?</a>
			</p>
			<li class="password optional" id="user_password_input">
				<label for="user_password">Password</label>
				<input id="user_password" name="user[password]" tabindex="11" type="password" />
			</li>
		</ol>
	</fieldset>
	<fieldset class="buttons">
		<ol>
			<li class="commit">
				<input class="bttn create" name="commit" tabindex="13" type="submit" value="Login" />
			</li>
		</ol>
	</fieldset>
</form>
*/
	}

	private function TryLogin($form_fields, $username, $password)
	{
		$form_fields['user[email]'] = $username;
		$form_fields['user[password]'] = $password;

		curl_setopt($this->curl_session, CURLOPT_POST, true);
		curl_setopt($this->curl_session, CURLOPT_POSTFIELDS, $form_fields);
		curl_setopt($this->curl_session, CURLOPT_URL, self::BASE_URL.$this->login_target);

		$res = curl_exec($this->curl_session);
		if ($res === false)
			throw new Exception("Failed to submit the login form. ".curl_error($this->curl_session));

		$data = json_decode($res, true);

		//echo $res."\n";
		//var_dump($data);

		if (isset($data['error']) && $data['error'])
			throw new Exception("Failed to log in: ".$data['error']);

		if (isset($data['success']) && $data['success'])
		{
			$text = $data['update'];
			file_put_contents('sessions/update.html', $text);
			$this->FindUsername($text);
		}

		curl_setopt($this->curl_session, CURLOPT_POST, false);
		curl_setopt($this->curl_session, CURLOPT_URL, self::BASE_URL);
		$res = curl_exec($this->curl_session);
		file_put_contents("sessions/after_login.html", $res);
	}

	private function FindUsername($text)
	{
		$urls = preg_match_all('@["\']('.preg_quote(self::BASE_URL).')?/\w\w/users/([\w\d\-]+)(/.*?["\']|["\'])@', $text, $matches, PREG_PATTERN_ORDER);
		//$urls = preg_match_all('@["\']/\w\w/users/([\w\d\-]+)(/[^"\']*["\']|["\'])@', $text, $matches, PREG_PATTERN_ORDER);
		if (!$urls)
			throw new Exception("No URLs with user name found");

		$usernames = array();
		foreach ($matches[2] as $user_name)
		{
			if (!isset($usernames[$user_name]))
				$usernames[$user_name] = 0;
			++$usernames[$user_name];
		}

		$max = max($usernames);
		foreach ($usernames as $user_name => $number)
		{
			if ($number == $max)
			{
				$this->user_name = $user_name;
				return;
			}
		}

		throw new Exception("Failed to find user name");
	}


	public function Close()
	{
		curl_close($this->curl_session);
	}

	private function ParseHeaders($headers_str)
	{
		$result = array();
		$arr = explode("\r\n", $headers_str);
		foreach ($arr as $line)
		{
			@list($name, $value) = explode(':', $line, 2);
			if (!empty($name))
				$result[trim($name)] = trim($value);
		}
		return $result;
	}
}