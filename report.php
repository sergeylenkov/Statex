<?php

function common() {
	global $setting, $content;
  
	$file = @fopen('stats/today.txt', r);
	$data[0] = @fread($file, filesize('stats/today.txt'));
	@fclose($file);

	if (!empty($data[0])) {
    	$counts = explode('|', $data[0]);
  
    	if (isset($counts))	{
      		$content['today']['hits'] = $counts[3];
			$content['today']['unique'] = $counts[1];
			$content['today']['audience'] = $counts[7]; 
			$content['today']['other'] = $counts[6];
			$content['today']['search'] = $counts[5];
			$content['today']['inside'] = $counts[8];
			$content['today']['robots'] = $counts[9];
  
			$content['today_percent']['unique'] = str_replace('.', ',', round(($counts[1] / $content['today']['unique']) * 100, 1));
			$content['today_percent']['audience'] = str_replace('.', ',', round(($counts[7] / $content['today']['unique']) * 100, 1)); 
			$content['today_percent']['other'] = str_replace('.', ',', round(($counts[6] / $content['today']['unique']) * 100, 1));
			$content['today_percent']['search'] = str_replace('.', ',', round(($counts[5] / $content['today']['unique']) * 100, 1));
			$content['today_percent']['robots'] = str_replace('.', ',', round(($counts[9] / $content['today']['unique']) * 100, 1));
  
			$report = st_read_log('stats/daily.txt');
			$report = array_merge($report, $data);
			
			$days = count($report);
			$content['total']['unique'] = 0;
			$content['total']['hits'] = 0;
			$content['total']['audience'] = 0;
			$content['total']['other'] = 0;
			$content['total']['search'] = 0;
			$content['total']['robots'] = 0;
  
			$counts = explode('|', $report[0]);
			$start_date = $counts[0];
			$counts = explode('|', $report[$days - 1]);
			$end_date = $counts[0];
  
			for ($i = 0; $i < count($report); $i++) {
				$counts = explode('|', $report[$i]);
    
				$content['total']['unique'] += $counts[1];
				$content['total']['hits'] += $counts[3];
				$content['total']['audience'] += $counts[7];
				$content['total']['other'] += $counts[6];
				$content['total']['search'] += $counts[5];
				$content['total']['robots'] += $counts[9];
			}  
					
			if ($content['total']['unique'] > 0) {
				$content['percent']['audience'] = str_replace('.', ',', round(($content['total']['audience'] / $content['total']['unique']) * 100, 1));
				$content['percent']['other'] = str_replace('.', ',', round(($content['total']['other'] / $content['total']['unique']) * 100, 1));
				$content['percent']['search'] = str_replace('.', ',', round(($content['total']['search'] / $content['total']['unique']) * 100, 1));
				$content['percent']['robots'] = str_replace('.', ',', round(($content['total']['robots'] / $content['total']['unique']) * 100, 1));
			} else {
				$content['percent']['audience'] = str_replace('.', ',', round(0, 1));
				$content['percent']['other'] = str_replace('.', ',', round(0, 1));
				$content['percent']['search'] = str_replace('.', ',', round(0, 1));
				$content['percent']['robots'] = str_replace('.', ',', round(0, 1));
			}		

			$content['date']['weekday'] = st_day_to_text(st_str_to_date($end_date));
			$content['date']['text'] = st_month_to_text(st_str_to_date($end_date)) . ' ' . date('j', st_str_to_date($end_date)) . ', ' . date('Y', st_str_to_date($end_date));
			$content['date']['start'] = st_month_to_text(st_str_to_date($start_date)) . ' ' . date('j', st_str_to_date($start_date)) . ', ' . date('Y', st_str_to_date($start_date));
			$content['date']['end'] = st_month_to_text(st_str_to_date($end_date)) . ' ' . date('j', st_str_to_date($end_date)) . ', ' . date('Y', st_str_to_date($end_date));
  
			$content['days'] = count($report);
    	}
	}
  
	return 0;
}

function browser() {
	global $setting, $content;
  
	$report = st_read_log('stats/agents.txt');
  
	if (count($report) > 0)	{
		$total = 0;
		$content['firefox']['percent'] = 0;
		$content['opera']['percent'] = 0;
		$content['ie']['percent'] = 0;
		$content['safari']['percent'] = 0;
		$content['chrome']['percent'] = 0;
		$content['other']['percent'] = 0;
  
		for ($i = 0; $i < count($report); $i++)	{
			if (strstr($report[$i], 'Chrome')) {
				$content['chrome']['percent'] = $content['chrome']['percent'] + $report[$i + 1];
			} elseif (strstr($report[$i], 'Firefox')) {
				$content['firefox']['percent'] = $content['firefox']['percent'] + $report[$i + 1];
			} elseif (strstr($report[$i], 'Opera')) {
				$content['opera']['percent'] = $content['opera']['percent'] + $report[$i + 1];
			} elseif (strstr($report[$i], 'MSIE')) {
				$content['ie']['percent'] = $content['ie']['percent'] + $report[$i + 1];
			} elseif (strstr($report[$i], 'Safari')) {
				$content['safari']['percent'] = $content['safari']['percent'] + $report[$i + 1];
			} else {
				$content['other']['percent'] = $content['other']['percent'] + $report[$i + 1];
			}
    
			$total = $total + $report[$i + 1];
			$i++;
		} 

		$content['ie']['percent'] = str_replace('.', ',', round(($content['ie']['percent'] / $total) * 100, 1));
		$content['firefox']['percent'] = str_replace('.', ',', round(($content['firefox']['percent'] / $total) * 100, 1));
		$content['opera']['percent'] = str_replace('.', ',', round(($content['opera']['percent'] / $total) * 100, 1));
		$content['safari']['percent'] = str_replace('.', ',', round(($content['safari']['percent'] / $total) * 100, 1));
		$content['chrome']['percent'] = str_replace('.', ',', round(($content['chrome']['percent'] / $total) * 100, 1));
		$content['other']['percent'] = str_replace('.', ',', round(($content['other']['percent'] / $total) * 100, 1));
  
		for ($i = 0; $i < count($report); $i++)	{
			if (strstr($report[$i], 'Chrome')) {
				$version = strstr($report[$i], 'Chrome/'); 

				if (strpos($version, ' ') === false) {
					$version = substr($version, 7);
				} else {
					$version = substr($version, 7, strpos($version, ' ') - 7);
				}
				
				if ($_GET['name'] == 'chrome') {
					$content['chrome_v'][$version] = $content['chrome_v'][$version] + $report[$i + 1];
					$content['total']['chrome'] = $content['total']['chrome'] + $report[$i + 1];
				}
			} elseif (strstr($report[$i], 'Firefox/')) {
				$version = strstr($report[$i], 'Firefox/'); 

				if (strpos($version, ' ') === false) {
					$version = substr($version, 8);
				} else {
					$version = substr($version, 8, strpos($version, ' ') - 8);
				}
				
				$version = str_replace('(', '', $version);
				$version = str_replace(')', '', $version);
				$version = str_replace(',', '', $version);
								
				if ($_GET['name'] == 'firefox') {
					$content['firefox_v'][$version] = $content['firefox_v'][$version] + $report[$i + 1];
					$content['total']['firefox'] = $content['total']['firefox'] + $report[$i + 1];
				}
			} elseif  (strstr($report[$i], 'Opera')) {
				if (strstr($report[$i], 'Version/')) {
					$version = strstr($report[$i], 'Version/'); 
				
					if (strpos($version, ' ') === false) {
						$version = substr($version, 8);
					} else {
						$version = substr($version, 8, strpos($version, ' ') - 8);
					}
				
					if ($_GET['name'] == 'opera') {
						$content['opera_v'][$version] = $content['opera_v'][$version] + $report[$i + 1];
						$content['total']['opera'] = $content['total']['opera'] + $report[$i + 1];
					}
				}
			} elseif (strstr($report[$i], 'MSIE')) {
				$version = strstr($report[$i], 'MSIE'); 
				$version = substr($version, 5, 3);
				
				if ($_GET['name'] == 'ie') {
					$content['ie_v'][$version] = $content['ie_v'][$version] + $report[$i + 1];
					$content['total']['ie'] = $content['total']['ie'] + $report[$i + 1];
				}
			} elseif (strstr($report[$i], 'Safari')) {
				if (strstr($report[$i], 'Version/')) {
					$version = strstr($report[$i], 'Version/'); 

					if (strpos($version, ' ') === false) {
						$version = substr($version, 8);
					} else {
						$version = substr($version, 8, strpos($version, ' ') - 8);
					}
				
					if (strstr($report[$i], 'Mobile')) {
						$version = 'Mobile ' . $version;
					}
				
					if ($_GET['name'] == 'safari') {
						$content['safari_v'][$version] = $content['safari_v'][$version] + $report[$i + 1];
						$content['total']['safari'] = $content['total']['safari'] + $report[$i + 1];
					}
				}
			}

			$i++;
		}

		if (isset($content['ie_v'])) {
			foreach (array_keys($content['ie_v']) as $key) {
				$content['ie_v'][$key] = str_replace('.', ',', round(($content['ie_v'][$key] / $content['total']['ie']) * 100, 1));  
			}
		}

		if (isset($content['firefox_v'])) {
			foreach (array_keys($content['firefox_v']) as $key)	{
				$content['firefox_v'][$key] = str_replace('.', ',', round(($content['firefox_v'][$key] / $content['total']['firefox']) * 100, 1));  
			}
		}

		if (isset($content['opera_v']))	{
			foreach (array_keys($content['opera_v']) as $key) {
				$content['opera_v'][$key] = str_replace('.', ',', round(($content['opera_v'][$key] / $content['total']['opera']) * 100, 1));  
			}
		}
		
		if (isset($content['safari_v'])) {
			foreach (array_keys($content['safari_v']) as $key) {
				$content['safari_v'][$key] = str_replace('.', ',', round(($content['safari_v'][$key] / $content['total']['safari']) * 100, 1));  
			}
		}
		
		if (isset($content['chrome_v'])) {
			foreach (array_keys($content['chrome_v']) as $key) {
				$content['chrome_v'][$key] = str_replace('.', ',', round(($content['chrome_v'][$key] / $content['total']['chrome']) * 100, 1));  
			}
		}
	}
  
	return 0;
}

function os() {
	global $setting, $content;
  
	$report = st_read_log('stats/agents.txt');
  
	if (count($report) > 0)	{
		$total = 0;
		$content['windows']['percent'] = 0;
		$content['mac']['percent'] = 0;
		$content['other']['percent'] = 0;
  
		for ($i = 0; $i < count($report); $i++)	{
			if (strstr($report[$i], 'Windows')) {
				$content['windows']['percent'] = $content['windows']['percent'] + $report[$i + 1];
			} elseif (strstr($report[$i], 'Macintosh')) {
				$content['mac']['percent'] = $content['mac']['percent'] + $report[$i + 1];
			} elseif (strstr($report[$i], 'iPhone')) {
				$content['iphone']['percent'] = $content['iphone']['percent'] + $report[$i + 1];
			} elseif (strstr($report[$i], 'Linux')) {
				$content['linux']['percent'] = $content['linux']['percent'] + $report[$i + 1];
			} else {
				$content['other']['percent'] = $content['other']['percent'] + $report[$i + 1];
			}
    
			$total = $total + $report[$i + 1];
			$i++;
		} 

		$content['windows']['percent'] = str_replace('.', ',', round(($content['windows']['percent'] / $total) * 100, 1));
		$content['mac']['percent'] = str_replace('.', ',', round(($content['mac']['percent'] / $total) * 100, 1));
		$content['iphone']['percent'] = str_replace('.', ',', round(($content['iphone']['percent'] / $total) * 100, 1));
		$content['linux']['percent'] = str_replace('.', ',', round(($content['linux']['percent'] / $total) * 100, 1));
		$content['other']['percent'] = str_replace('.', ',', round(($content['other']['percent'] / $total) * 100, 1));
  
		for ($i = 0; $i < count($report); $i++)	{
			if (strstr($report[$i], 'Windows ')) {
				$version = strstr($report[$i], 'Windows '); 

				if (strpos($version, ';') === false) {
					$version = substr($version, 8, 6);
				} else {
					$version = substr($version, 8, strpos($version, ';') - 8);
				}
				
				if ($version == '95') {
					$version = '95';
				}
				
				if ($version == '98') {
					$version = '98';
				}

				if ($version == 'NT 4.0') {
					$version = 'NT';
				}

				if ($version == 'NT 5.0') {
					$version = '2000';
				}
				
				if ($version == 'NT 5.1') {
					$version = 'XP';
				}
				
				if ($version == 'NT 5.2') {
					$version = '2003';
				}
				
				if ($version == 'NT 6.0') {
					$version = 'Vista';
				}
				
				if ($version == 'NT 6.1') {
					$version = '7';
				}
				
				if ($version == 'NT 6.2') {
					$version = '8';
				}

				$version = str_replace(')', '', $version);
				$version = str_replace('(', '', $version);
				
				if ($_GET['name'] == 'win') {
					$content['windows_v'][$version] = $content['windows_v'][$version] + $report[$i + 1];
					$content['total']['windows'] = $content['total']['windows'] + $report[$i + 1];
				}
			} elseif (strstr($report[$i], 'Macintosh')) {
				$verion = '';
				$result = preg_match('/mac os x ([0-9]{1,2}[_.][0-9][_.][0-9]|[0-9]{1,2}[_.][0-9])/i', $report[$i], $maches);
				
				if ($result > 0) {
					$version = trim($maches[1]);
					$version = str_replace('_', '.', $version);
				}
				
				if ($version != '') {
					if ($_GET['name'] == 'mac') {
						$content['mac_v'][$version] = $content['mac_v'][$version] + $report[$i + 1];
						$content['total']['mac'] = $content['total']['mac'] + $report[$i + 1];
					}
				}
			} elseif (strstr($report[$i], 'iPhone')) {
				if (strstr($report[$i], 'iPhone OS')) {
					$version = strstr($report[$i], 'iPhone OS '); 
					$version = str_replace('iPhone OS ', '', $version);
					$version = substr($version, 0, strpos($version, ' '));
				
					$version = str_replace('(', '', $version);
					$version = str_replace(')', '', $version);
					$version = str_replace('_', '.', $version);
				
					if ($_GET['name'] == 'iphone') {
						$content['iphone_v'][$version] = $content['iphone_v'][$version] + $report[$i + 1];
						$content['total']['iphone'] = $content['total']['iphone'] + $report[$i + 1];
					}
				}
			} elseif (strstr($report[$i], 'Linux')) {
				if (strstr($report[$i], 'Ubuntu')) {
					$version = strstr($report[$i], 'Ubuntu/'); 

					if (strpos($version, ' ') === false) {
						$version = substr($version, 7);
					} else {
						$version = substr($version, 7, strpos($version, ' ') - 7);
					}
					
					$version = 'Ubuntu ' . $version;
				} else {
					$version = 'Unknown';
				}
				
				if ($_GET['name'] == 'linux') {
					$content['linux_v'][$version] = $content['linux_v'][$version] + $report[$i + 1];
					$content['total']['linux'] = $content['total']['linux'] + $report[$i + 1];
				}
			}

			$i++;
		}

		if ($_GET['name'] == 'mac') {
			$total = 0;
			
			for ($i = 0; $i < count($report); $i++)	{
				if (strstr($report[$i], 'Mac OS X ')) {
					if (strstr($report[$i], 'Intel')) {
						$version = 'Intel';
						$content['mac_proc'][$version] = $content['mac_proc'][$version] + $report[$i + 1];
					} elseif (strstr($report[$i], 'PPC')) {
						$version = 'PPC';
						$content['mac_proc'][$version] = $content['mac_proc'][$version] + $report[$i + 1];
					}				
				}
			
				$i++;
			}
		}
		
		if (isset($content['windows_v'])) {
			foreach (array_keys($content['windows_v']) as $key) {
				$content['windows_v'][$key] = str_replace('.', ',', round(($content['windows_v'][$key] / $content['total']['windows']) * 100, 1));  
			}
		}

		if (isset($content['mac_v'])) {
			foreach (array_keys($content['mac_v']) as $key)	{
				$content['mac_v'][$key] = str_replace('.', ',', round(($content['mac_v'][$key] / $content['total']['mac']) * 100, 1));  
			}
		}
		
		if (isset($content['mac_proc'])) {
			foreach (array_keys($content['mac_proc']) as $key)	{
				$content['mac_proc'][$key] = str_replace('.', ',', round(($content['mac_proc'][$key] / $content['total']['mac']) * 100, 1));  
			}
		}
		
		if (isset($content['iphone_v'])) {
			foreach (array_keys($content['iphone_v']) as $key)	{
				$content['iphone_v'][$key] = str_replace('.', ',', round(($content['iphone_v'][$key] / $content['total']['iphone']) * 100, 1));  
			}
		}
		
		if (isset($content['linux_v'])) {
			foreach (array_keys($content['linux_v']) as $key)	{
				$content['linux_v'][$key] = str_replace('.', ',', round(($content['linux_v'][$key] / $content['total']['linux']) * 100, 1));  
			}
		}

	}
  
	return 0;
}

function request() {
	global $setting, $content;
  
	$report = st_read_log('stats/request.txt');
  
	if (count($report) > 0) {
    	$total = 0;
  
		for ($i = 0; $i < count($report); $i++) {
			$report[$i] = str_replace('www.', '', urldecode($report[$i]));
			
			if ($setting['no_variables']) {
				$report[$i] = st_remove_vars($report[$i]);
			}
      
      		$content['request'][$report[$i]] = $content['request'][$report[$i]] + $report[$i + 1];
      		$total = $total + $report[$i + 1];
      		$i++;                       
    	}

    	if (isset($content['request'])) {
      		foreach (array_keys($content['request']) as $key) {
        		$content['request'][$key] = str_replace('.', ',', round(($content['request'][$key] / $total) * 100, 1));  
      		}
    	}  
	}
  
	return 0;
}

function refer() {
	global $setting, $content;
  
	$report = st_read_log('stats/referers.txt');
  
	if (count($report) > 0) {
		$total = 0;
  
		for ($i = 0; $i < count($report); $i++) {
			$host = parse_url($report[$i], PHP_URL_HOST);
			$host = str_replace('www.', '', $host);
      
			$content['refer'][$host] = $content['refer'][$host] + $report[$i + 1];
			$total = $total + $report[$i + 1];
			$i++;                       
		}

		if (isset($content['refer'])) {
			foreach (array_keys($content['refer']) as $key) {
				$content['refer'][$key] = str_replace('.', ',', round(($content['refer'][$key] / $total) * 100, 1));  
			}
		}
		
		if (isset($_GET['host'])) {
			$total = 0;

			for ($i = 0; $i < count($report); $i++) {
				$host = parse_url($report[$i], PHP_URL_HOST);
				$host = str_replace('www.', '', $host);
				
				$url = str_replace('www.', '', $report[$i]);
				$url = strtolower(trim($url, '/'));
				
				if ($host == $_GET['host']) {					
					$content['detail'][$url] = $content['detail'][$url] + $report[$i + 1];
					$total = $total + $report[$i + 1];					
				}
				
				$i++;
			}
			
			if (isset($content['detail'])) {
				foreach (array_keys($content['detail']) as $key) {
					$content['detail'][$key] = str_replace('.', ',', round(($content['detail'][$key] / $total) * 100, 1));
				}
			}			
		}
	}
  
	return 0;
}

function bot() {
	global $setting, $content;
  
	$report = st_read_log('stats/robots.txt');
  
	if (count($report) > 0) {
		$content['robots']['total'] = 0;
		$content['robots']['yandex'] = 0;
	    $content['robots']['google'] = 0;
		$content['robots']['rambler'] = 0;
		$content['robots']['aport'] = 0;
		$content['robots']['msn'] = 0;
		$content['robots']['yahoo'] = 0;
  
		for ($i = 0; $i < count($report); $i++) {
			if (stristr($report[$i], 'Googlebot')) $content['robots']['google'] = $content['robots']['google'] + $report[$i + 1];
      		elseif (stristr($report[$i], 'Yandex')) $content['robots']['yandex'] = $content['robots']['yandex'] + $report[$i + 1];
      		elseif (stristr($report[$i], 'StackRambler')) $content['robots']['rambler'] = $content['robots']['rambler'] + $report[$i + 1];
     		elseif (stristr($report[$i], 'Aport')) $content['robots']['aport'] = $content['robots']['aport'] + $report[$i + 1];
      		elseif (stristr($report[$i], 'msnbot')) $content['robots']['msn'] = $content['robots']['msn'] + $report[$i + 1];
			elseif (stristr($report[$i], 'Yahoo')) $content['robots']['yahoo'] = $content['robots']['yahoo'] + $report[$i + 1];
			else $content['robots']['other'] = $content['robots']['other'] + $report[$i + 1];
    
			$content['robots']['total'] = $content['robots']['total'] + $report[$i + 1];
			$i++;
		} 
  
		$content['robots']['google'] = str_replace('.', ',', round(($content['robots']['google'] / $content['robots']['total']) * 100, 1));
		$content['robots']['yandex'] = str_replace('.', ',', round(($content['robots']['yandex'] / $content['robots']['total']) * 100, 1));
		$content['robots']['rambler'] = str_replace('.', ',', round(($content['robots']['rambler'] / $content['robots']['total']) * 100, 1));
		$content['robots']['aport'] = str_replace('.', ',', round(($content['robots']['aport'] / $content['robots']['total']) * 100, 1));
		$content['robots']['msn'] = str_replace('.', ',', round(($content['robots']['msn'] / $content['robots']['total']) * 100, 1));
		$content['robots']['yahoo'] = str_replace('.', ',', round(($content['robots']['yahoo'] / $content['robots']['total']) * 100, 1));
		$content['robots']['other'] = str_replace('.', ',', round(($content['robots']['other'] / $content['robots']['total']) * 100, 1));
	}
  
	return 0;
}

function daily() {
	global $setting, $content;
  
	$file = @fopen('stats/today.txt', r);
	$data[0] = @fread($file, filesize('stats/today.txt'));
	@fclose($file);

	$report = st_read_log('stats/daily.txt'); 

	$report = array_merge($report, $data);
	$report = array_reverse($report);

	$to = $setting['days']; 
	if ($setting['days'] > count($report)) {
		$to = count($report);
	}
  
	$start_date = explode('|', $report[$to - 1]);
	$start_date = $start_date[0];
	$end_date = explode('|', $report[0]);
	$end_date = $end_date[0];
 
	$max = 0;
   
	for ($i = 0; $i < $to; $i++) {
		$counts = explode('|', $report[$i]); 
		$date = st_month_to_text(st_str_to_date($counts[0])) . ' ' . date('j', st_str_to_date($counts[0])) . ', ' . date('Y', st_str_to_date($counts[0]));
		$content['daily'][] = array($date, $counts[0], $counts[1], $counts[3], $counts[7], $counts[6], $counts[5], $counts[9]);
    
		if ($counts[1] > $max) {
			$max = $counts[1];
		}
	}
  
	for ($i = 0; $i < $to; $i++) {
		$content['daily'][$i][8] = round(($content['daily'][$i][2] / $max) * 100, 0);
	}  
    
	$content['date']['start'] = st_month_to_text(st_str_to_date($start_date)) . ' ' . date('j', st_str_to_date($start_date)) . ', ' . date('Y', st_str_to_date($start_date));
	$content['date']['end'] =  st_month_to_text(st_str_to_date($end_date)) . ' ' . date('j', st_str_to_date($end_date)) . ', ' . date('Y', st_str_to_date($end_date));
  
	if (isset($_GET['date'])) {
		for ($i = 0; $i < $to; $i++) {
			$counts = explode('|', $report[$i]);
      
			if ($counts[0] == $_GET['date']) {
				$content['detail']['unique'] = $counts[1];        
				$content['detail']['audience'] = $counts[7];
				$content['detail']['other'] = $counts[6];
				$content['detail']['search'] = $counts[5];
				$content['detail']['robots'] = $counts[9];
        
				$content['percent']['unique'] = str_replace('.', ',', round(($counts[1] / $counts[1]) * 100, 0));
				$content['percent']['audience'] = str_replace('.', ',', round(($counts[7] / $counts[1]) * 100, 0));
				$content['percent']['other'] = str_replace('.', ',', round(($counts[6] / $counts[1]) * 100, 0));
				$content['percent']['search'] = str_replace('.', ',', round(($counts[5] / $counts[1]) * 100, 0));
				$content['percent']['robots'] = str_replace('.', ',', round(($counts[9] / $counts[1]) * 100, 0));
        
				$content['date']['detail'] = st_month_to_text(st_str_to_date($counts[0])) . ' ' . date('j', st_str_to_date($counts[0])) . ', ' . date('Y', st_str_to_date($start_date));
			}
		}
	}
  
	return 0;
}

function ip() {
	global $setting, $content;
  
	$report = st_read_log('stats/ip.txt');  
	$total = 0;
  
	for ($i = 0; $i < count($report); $i++) {
		$content['ip'][$report[$i]] = $content['ip'][$report[$i]] + $report[$i + 1];
    
    	$city = st_get_city_by_ip($report[$i]);
    	$content['cities'][$city] = $content['cities'][$city] + $report[$i + 1];
    
    	$total = $total + $report[$i + 1];    
    	$i++;                       
	}

	if (isset($content['ip'])) {
		foreach (array_keys($content['ip']) as $key) {       
			$content['ip'][$key] = str_replace('.', ',', round(($content['ip'][$key] / $total) * 100, 1));  
		}
	}
  
	if (isset($content['cities'])) {
		foreach (array_keys($content['cities']) as $key) {       
			$content['cities'][$key] = str_replace('.', ',', round(($content['cities'][$key] / $total) * 100, 1));  
		}
	}
  
	return 0;
}

function search() {
	global $setting, $content;
  
	$report = st_read_log('stats/search.txt');  
  
	if (count($report) > 0) {
    	$report = array_reverse($report);

		$to = $setting['search']; 
		
		if ($setting['search'] > count($report)) {
			$to = count($report);
		}
  
		for ($i = 0; $i < $to; $i++) {
			$content['search'][] = explode("\t", $report[$i]);;                  
		}
	}
  
	return 0;
}

function click() {
	global $setting, $content;
  
	$report = st_read_log('stats/clicks.txt');
  	
	if (count($report) > 0) {
		$total = 0;
  
		for ($i = 0; $i < count($report); $i++)	{
			$click = explode('|', $report[$i]);
			$url = strtolower(trim($click[1], '/'));
			
			$content['clicks'][$url] = $content['clicks'][$url] + $report[$i + 1];
			$total = $total + $report[$i + 1];    
			$i++;                       
		}

		if (isset($content['clicks'])) {
			foreach (array_keys($content['clicks']) as $key) {  
				$content['clicks'][$key] = $content['clicks'][$key];   
				$content['clicks_percent'][$key] = str_replace('.', ',', round(($content['clicks'][$key] / $total) * 100, 1));  
			}
		}
		
		if (isset($_GET['url'])) {			
			$total = 0;
			
			for ($i = 0; $i < count($report); $i++)	{
				$click = explode('|', $report[$i]);
				$url = strtolower(trim($click[1], '/'));
			
				if ($url == $_GET['url']) {
					$content['detail'][$click[0]] = $report[$i + 1];
					$total = $total + $report[$i + 1];
				} 
			}
			
			if (isset($content['detail'])) {
				foreach (array_keys($content['detail']) as $key) {  
					$content['detail'][$key] = $content['detail'][$key];   
					$content['detail_percent'][$key] = str_replace('.', ',', round(($content['detail'][$key] / $total) * 100, 1));  
				}
				
				$content['detail'] = array_reverse($content['detail']);
			}
		}
	}
  
	return 0;
}

if (!isset($_COOKIE['statex_id'])) 
{
  header('location: ./');
}  

$path = dirname(__FILE__) . '/';

include($path . 'functions.php');

st_load_setting();
st_no_cache_headers();

unset($content);

if (!isset($_GET['action'])) { 
  common();
  include('templates/index.html'); 
}

if ($_GET['action'] == 'browser') {
  browser(); 
  include('templates/browser.html');
}

if ($_GET['action'] == 'os') {
  os(); 
  include('templates/os.html');
}
 
if ($_GET['action'] == 'request') {
  request();  
  include('templates/request.html');
}

if ($_GET['action'] == 'refer') {
  refer();
  include('templates/refer.html');
}

if ($_GET['action'] == 'ip') {
  ip();
  include('templates/ip.html');
}

if ($_GET['action'] == 'search') {
  search();
  include('templates/search.html');
}

if ($_GET['action'] == 'bot') {
  bot();    
  include('templates/bot.html');
}

if ($_GET['action'] == 'click') {
  click();
  include('templates/click.html');
}

if ($_GET['action'] == 'daily') {
  daily();
  include('templates/daily.html');
}

?>
