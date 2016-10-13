<?php

function st_read_rss()
{
  global $setting, $path;
  
  $file = @fopen($path . 'rss/rss.xml', r);
  $contents = @fread($file, filesize($path . 'rss/rss.xml'));
  @fclose($file);
  
  preg_match_all('/<item[^>]*>(.*?)<\/item>/si', $contents, $items);
   
  foreach ($items[1] as $item) 
  { 
    preg_match('/<guid[^>]*>(.*?)<\/guid>/si', $item, $guid);
    preg_match('/<title[^>]*>(.*?)<\/title>/si', $item, $title);
    preg_match('/<description[^>]*>(.*?)<\/description>/si', $item, $description);
    preg_match('/<pubDate[^>]*>(.*?)<\/pubDate>/si', $item, $date);
    preg_match('/<link[^>]*>(.*?)<\/link>/si', $item, $link);
    preg_match('/<author[^>]*>(.*?)<\/author>/si', $item, $author);
    preg_match('/<category[^>]*>(.*?)<\/category>/si', $item, $category);
    preg_match('/<comments[^>]*>(.*?)<\/comments>/si', $item, $comments);
    
    $rss[] = array('guid' => $guid[1], 'title' => $title[1], 'description' => $description[1], 'date' => $date[1], 
                   'link' => $link[1], 'author' => $author[1], 'category' => $category[1], 'comments' => $comments[1]);
  }
    
  return($rss);
}

function st_write_rss()
{
  global $setting, $path;
  
  $rss = st_read_rss();
  
  $report = st_read_log($path . 'stats/daily.txt');
  $counts = explode('|', $report[count($report) - 1]);
  
  $date = st_month_to_text(st_str_to_date($counts[0])) . ' ' . date('j', st_str_to_date($counts[0])) . ', ' . date('Y', st_str_to_date($counts[0]));
  
  $guid = md5(date('d.m.Y H:i:s'));
  $title = 'Statistics on ' . $date;
  $description = 'Visitors &amp;mdash; ' . $counts[1] . ': &lt;p&gt;Audience &amp;mdash; ' . $counts[7] . '&lt;br&gt;Referers &amp;mdash; ' . $counts[6] .
                 '&lt;br&gt;Search &amp;mdash; ' . $counts[5] . '&lt;br&gt;Bots &amp;mdash; ' . $counts[9] . '&lt;p&gt;Total &amp;mdash; ' . $counts[2];
  $date = date('U', st_str_to_date($counts[0]));
  
  $rss[] = array('guid' => $guid, 'title' => $title, 'description' => $description, 'date' => $date, 
                 'link' => $link, 'author' => $author, 'category' => $category, 'comments' => $comments);
  
  $file = @fopen($path . 'rss/rss.xml', w);
  @flock($file, LOCK_EX);
  
  $content = '<?xml version="1.0" encoding="utf-8"?><rss version="2.0"><channel><title>Statistics of "' . $setting['name'] . '"</title><link>http://'. $setting['host'] . '/</link><description>Statistics of "' . $setting['name'] . '"</description><language>en</language>';
  
  foreach ($rss as $item)
  { 
    $content .= '<item>';
    $content .= '<guid>' . $item['guid'] . '</guid>';
    $content .= '<title>' . $item['title'] . '</title>';
    $content .= '<description>' . $item['description'] . '</description>';
    $content .= '<pubDate>' .  $item['date'] . '</pubDate>';
    $content .= '</item>';
  }
  
  $content .= '</channel></rss>';
  
  @flock($file, LOCK_UN);
  @fwrite($file, $content);
  @fclose($file);
}

?>
