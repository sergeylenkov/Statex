<?php

date_default_timezone_set('Europe/Moscow');

function st_load_setting() {
	global $setting, $path;
  
	if (!file_exists($path . 'stats/settings.php')) {
		return 1;
	}
  
	$file = @fopen($path . 'stats/settings.php', 'r');
	
	if (!$file) {
		return 2;
	}
	
	$setting = unserialize(@fread($file, filesize($path . 'stats/settings.php')));
	@fclose($file);
  
	$setting['title'] = 'Statex 1.5';
 
	return 0;
}

function st_save_setting() {
	global $setting, $path;
  
	$file = @fopen($path . 'stats/settings.php', 'w+');
	if (!$file) {
		return 3;
	}
  
	@fwrite($file, serialize($setting));
	@fclose($file); 
  
	return 0;
}

function st_read_log($file_name) {
	clearstatcache();
  
	if (!file_exists($file_name)) {
		return array();
	}
     
	$file = @fopen($file_name, r);
  
	@flock($file, LOCK_SH);
  
	$content = @fread($file, filesize($file_name));
	@fclose($file);

	@flock($file, LOCK_UN);

	$content = str_replace("\r", '', $content);  
	$log = explode("\n", trim($content));
  
	return $log;
}

function st_write_log($file_name, $text) { 
	clearstatcache();
    
	$log = st_read_log($file_name);
	$key = array_search($text, $log);

	if ($key === false) {     
		$log[] = $text;
		$log[] = 1;  
    
		$result = true; 
	} else {
		$log[$key + 1]++; 
		$result = false;
	}
  
	$file = @fopen($file_name, 'wt+');
  
	@flock($file, LOCK_EX);
  
	@fwrite($file, implode("\n", $log));
	@fclose($file);
  
	@flock($file, LOCK_UN);
  
	return $result;
}

function st_write_raw_data($file_name, $ip, $request, $referer, $agent) {
    global $setting, $path;
    
    $file = @fopen($file_name, 'a');

	@flock($file, LOCK_EX);

	@fwrite($file, $ip . "\t" . $request . "\t" . $referer . "\t" . $agent . "\n");
    @fclose($file);

	@flock($file, LOCK_UN);
}

function st_search_engine($file_name, $refer, $request) { 
	global $setting, $path;
    
	$url = $refer;
	$refer = urldecode($refer);
  
	if ((stristr($refer, 'yandpage')) || (stristr($refer, 'yandsearch'))) { 
		$search_word = 'text='; 
		$engine = 'Yandex'; 
	}
  
	if (stristr($refer, 'rambler.ru')) { 
		$search_word = 'words='; 
		$engine = 'Rambler'; 
	}
  
	if (stristr($refer, 'sm.aport.ru')) { 
		$search_word = 'r='; 
		$engine = 'Aport'; 
	}
  
	if (stristr($refer, 'google.')) { 
		$search_word = 'q=';
		$engine = 'Google'; 
	}
  
	if (stristr($refer, 'google.yahoo.com')) { 
		$search_word = 'p='; 
		$engine = 'Yahoo'; 
	}

	if (stristr($refer, 'go.mail.ru')) {
		$search_word = 'q=';
		$engine = 'Mail';
	}

	if (stristr($refer, 'bing.com')) {
		$search_word = 'q=';
		$engine = 'Bing';
	}
	
	if (isset($engine)) {
		preg_match('/' . $search_word . '([^&]*)/', $refer . '&', $refer);
		
    	$search_words = urldecode($refer[1]); 
    
    	if ($engine == 'Yandex' && stristr($url, 'yandpage')) {
    		$search_words = convert_cyr_string($search_words, 'k', 'w');
    	}
    
    	if ($engine == 'Google') {
			$search_words = st_utf_to_win($search_words);
		}
        
		$file = @fopen($file_name, 'a');
    
		@flock($file, LOCK_EX);
    
		@fwrite($file, $engine . "\t" . $search_words . "\t" . $url . "\t" . $request . "\n");
	    @fclose($file);
    
		@flock($file, LOCK_UN);

		return true;
	}
  
	return false;
}

function st_is_search($refer) {
	$search_engines = array('yandpage', 'yandsearch', 'google.', 'google.yahoo.com', 'rambler.ru', 'sm.aport.ru', 'go.mail.ru', 'bing.');
  
	foreach ($search_engines as $engine) {
		if (stristr($refer, $engine)) {
			return true;
		}
	}
  
	return false;
}

function st_is_bot($agent) {   
	$robots = array('Googlebot', 'Yandex', 'Aport', 'StackRambler', 'msnbot', 'Gokubot', 'Nigma.ru', 'NetStat.ru', 'Yahoo! Slurp', 
                    'W3C_Validator', 'TurtleScanner', 'Gigabot', 'eStyleSearch', 'Goku', 'WebAlta Crawler', 'TurnitinBot', 
                    'Sogou web spider', 'zomba-bot', 'NaverBot', 'Baiduspider', 'PostRank', 'bingbot', 'crawler@alexa.com');
  
	foreach ($robots as $bot) {   
		if (stristr($agent, $bot)) {
			return true;
		}
	}
  
	return false;
}

function st_is_inner($refer) {
    global $setting;
    
    $refer = str_replace('http://', '', $refer);
    $pos = stripos($refer, $setting['host']);

    if ($pos === false) {
		return false;
	} else if ($pos == 0) {
	    return true;
    }
	
	return false;
}

function st_month_to_text($date) { 
	$months = array('', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');  
  
	if (date('n', $date) < 1 || date('n', $date) > 12) {
		return '';
	}
	
	return $months[date('n', $date)];
}

function st_day_to_text($date) { 
	$days = array('воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота');  
  
	if (date('w', $date) < 0 || date('w', $date) > 6) {
  		return 'тяпница';
  	}
  	
	return $days[date('w', $date)];  
}

function st_noun_to_text($number, $words) {
	switch (substr($number, -1)) {
		case 1 : 
      		$text = $words[0]; 
    	break;
        
    	case 2 : 
    	case 3 :
    	case 4 :
			$text = $words[1];         
    	break;
        
		default :
			$text = $words[2]; 
	}

	return $text;
}
  
function st_str_to_date($date) {
	$date = $date[6] . $date[7] . $date[8] . $date[9] . '-' . $date[3] . $date[4] . '-' . $date[0] . $date[1];
	$date = strtotime($date);
  
	return $date;
}

function st_no_cache_headers() {
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time() - 3600) . ' GMT');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header("Pragma: no-cache");  
}
  
function st_get_city_by_ip($ip) {
	$city_id = 0;
	$city = 'Unknown';
	$ip = sprintf('%u', ip2long($ip));

	$file = @fopen('ip_compact.txt', 'r');  
  
	while ($data = @fgetcsv($file, 1000, "\t")) {
		if ($ip >= $data[0] && $ip <= $data[1]) {
			$city_id = $data[2];
		}
	}  
  
	@fclose($file);

	$file = @fopen('cities.txt', 'r');
  
	while ($data = @fgetcsv($file, 1000, "\t")) {
    	if ($city_id == $data[0]) {
    		$city = $data[2];
    	}
	}
  
	@fclose($file);
  
	return $city;
}

function st_remove_vars($url) {
	$pos = strpos($url, '&'); 
	
	if ($pos > 0) {
		$url = substr($url, 0, $pos);
	}
	
	return $url;
}

function st_utf_to_win($str) {
	$table = array("\xD0\x81" => "\xA8", "\xD1\x91" => "\xB8");
	return preg_replace('#([\xD0-\xD1])([\x80-\xBF])#se', 'isset($table["$0"]) ? $table["$0"] : chr(ord("$2")+("$1" == "\xD0" ? 0x30 : 0x70))', $str);
}

?>
