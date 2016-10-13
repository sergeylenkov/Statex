<?php 

$path = dirname(__FILE__) . '/';

include($path . 'functions.php');
include($path . 'rss.php');

st_load_setting();

st_write_raw_data($path . 'stats/raw.txt', $_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $_SERVER['HTTP_REFERER'], $_SERVER['HTTP_USER_AGENT']);

$today = date('d.m.Y');

// читаем показания счетчика (ниже формат счетчика) 
// дата | посетителей сегодня | посетителей всего | хиты сегодня | хиты всего | с поисковиков | с других сайтов | публика | внутренние переходы | боты

if (@file_exists($path . 'stats/today.txt')) {
	clearstatcache();
  
	$file = @fopen($path . 'stats/today.txt', r);
	$content = @fread($file, filesize($path . 'stats/today.txt'));
	@fclose($file);

	$counts = explode('|', $content);
} else {
	$counts[0] = $today; 
	$counts[1] = 0;
	$counts[2] = 0;
	$counts[3] = 0;
	$counts[4] = 0;
	$counts[5] = 0;
	$counts[6] = 0;
	$counts[7] = 0;
	$counts[8] = 0;
	$counts[9] = 0;
}

// если настал новый день обнуляем значения и пишем статистику за день

if ($counts[0] != $today) {    
	$temp = $counts[0] . '|'. $counts[1] . '|' . $counts[2] . '|' . $counts[3] . '|' . $counts[4] . '|' . $counts[5] . '|' . $counts[6] . '|' . $counts[7] . '|' . $counts[8] . '|' . $counts[9];
  
	$counts[0] = $today; 
	$counts[1] = 0;
	$counts[3] = 0;
	$counts[5] = 0;
	$counts[6] = 0;
	$counts[7] = 0;
	$counts[8] = 0;
	$counts[9] = 0;
  
	$file = @fopen($path . 'stats/daily.txt', 'a');
	@fwrite($file, $temp . "\n");
	@fclose($file);
  
	@unlink($path . 'stats/ip.txt');  
  
	// пишем статистику в rss
  
	if ($setting['use_rss']) {
		st_write_rss();
	}
}
  
// считаем уникальных поситителей

if (st_write_log($path . 'stats/ip.txt', $_SERVER['REMOTE_ADDR'])) {
	$counts[1]++;
	$counts[2]++;
  
	if ($_SERVER['HTTP_REFERER'] == '') { // аудитория
		if (st_is_bot($_SERVER['HTTP_USER_AGENT'])) $counts[9]++; else $counts[7]++; // бот или нет
  	} else { // с других сайтов
		  if (!st_is_inner($_SERVER['HTTP_REFERER'])) {      
      		if (st_is_search($_SERVER['HTTP_REFERER'])) { // с поисковика или нет
      			$counts[5]++; 
      		} else {
      			$counts[6]++;
      		}
		}
	}
}

// считаем внутренние переходы

if (st_is_inner($_SERVER['HTTP_REFERER'])) {
	$counts[8]++;
}

// увечичиваем хиты

$counts[3]++; 
$counts[4]++;

// сохраняем значения счетчика

$file = @fopen($path . 'stats/today.txt', 'wb+');
@fwrite($file, implode('|', $counts));
@fclose($file);

// считаем браузеры или ботов

if (st_is_bot($_SERVER['HTTP_USER_AGENT'])) {
	st_write_log($path . 'stats/robots.txt', $_SERVER['HTTP_USER_AGENT']);
} else {
	st_write_log($path . 'stats/agents.txt', $_SERVER['HTTP_USER_AGENT']);
}  
   
// считаем запрошенные страницы  

st_write_log($path . 'stats/request.txt', $_SERVER['REQUEST_URI']);

// если зашли с поисковика сохраняем запрос и куда попали, если нет сохраняем рефер

if ($_SERVER['HTTP_REFERER'] != '' && !st_is_inner($_SERVER['HTTP_REFERER'])) {
	if (!st_search_engine($path . 'stats/search.txt', $_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI'])) {
		st_write_log($path . 'stats/referers.txt', $_SERVER['HTTP_REFERER']);
	}  
}  

// если будем показывать счетчик надо его сгенерить

if ($setting['use_counter']) {
	$image = imagecreatefrompng($path . 'images/blank.png');
	$color = imagecolorallocate($image, 255, 255, 255);
	imagestring($image, 1, 25, 4, $counts[1] . '/' . $counts[2], $color);
	imagepng($image, $path . 'images/counter.png');
	imagedestroy($image);
}  

?>
