<?php

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
}
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function str_contains($haystack, $needle) {
  return strpos($haystack, $needle) !== false;
}

function single($var,$single,$many){
  return (1 == $var)?"$var $single":"$var $many";
}

function iconStack($icon, $top, $class=null,$flip=false){
  if ($flip){
      return "<span class='fa-stack fa-lg'>
      <i class='fa fa-$icon fa-stack-2x'></i>
      <i class='fa fa-$top fa-stack-1x $class'></i>
    </span>";
  }
  return "<span class='fa-stack fa-lg'>
  <i class='fa fa-$icon fa-stack-1x'></i>
  <i class='fa fa-$top fa-stack-2x $class'></i>
</span>";
}

function baseIcon($icon,$base){
  $html = "<span class='fa-stack fa-lg'>";
  if(is_array($icon)){
    foreach ($icon as $i){
      $html.= "<i class='fa fa-$i fa-stack-1x'></i>";
    }
  } else {
    $html.= "<i class='fa fa-$icon fa-stack-1x'></i>";
  }
  $html.= "<i class='fa fa-$base fa-stack-2x'></i></span>";
  return $html;
}

function icon($icon){
  return baseIcon($icon,'circle-thin');
}

function alert($msg, $level='info'){
  switch($level){
    default:
    case 'info':
    case 2:
      $class = 'info';
    break;

    case 'success':
    case 1:
    case TRUE:
      $class = 'success';
    break;

    case 'danger':
    case 0:
    case FALSE:
      $class = 'danger';
    break;
  }
  return "<div class='alert alert-$class'>$msg</div>";
}

/**
 * pick
 *
 * Grabs one item from a list or an array of choices
 *
 * @param $list (mixed) Either a comma separated list or an array of choices to
 * pick from
 *
 * @return (string) A random item from the specified list
 */

function pick($list) {
  if (!is_array($list)) {
    $list = explode(',',$list);
  }
  return $list[floor(rand(0,count($list)-1))];
}

//Via https://gist.github.com/mattytemple/3804571
function relativeTime($ts,$prefix=null) {
    if(!ctype_digit($ts)) {
        $ts = strtotime($ts);
    }
    $diff = time() - $ts;
    if($diff == 0) {
        return 'now';
    } elseif($diff > 0) {
        $day_diff = floor($diff / 86400);
        if($day_diff == 0) {
            if($diff < 60) return 'just now';
            if($diff < 120) return '1 minute ago';
            if($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if($diff < 7200) return '1 hour ago';
            if($diff < 86400) return floor($diff / 3600) . ' hours ago';
        }
        if($day_diff == 1) { return 'Yesterday'; }
        if($day_diff < 7) { return $day_diff . ' days ago'; }
        if($day_diff < 31) { return ceil($day_diff / 7) . ' weeks ago'; }
        if($day_diff < 60) { return 'last month'; }
        return date('F Y', $ts);
    } else {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);
        if($day_diff == 0) {
            if($diff < 120) { return 'in a minute'; }
            if($diff < 3600) { return 'in ' . floor($diff / 60) . ' minutes'; }
            if($diff < 7200) { return 'in an hour'; }
            if($diff < 86400) { return 'in ' . floor($diff / 3600) . ' hours'; }
        }
        if($day_diff == 1) { return 'Tomorrow'; }
        if($day_diff < 4) { return date('l', $ts); }
        if($day_diff < 7 + (7 - date('w'))) { return 'next week'; }
        if(ceil($day_diff / 7) < 4) { return 'in ' . ceil($day_diff / 7) . ' weeks'; }
        if(date('n', $ts) == date('n') + 1) { return 'next month'; }
        return date('F Y', $ts);
    }
}

function timestamp($date,$str=null) {
  $actual = date(DATE_FORMAT, strtotime($date));
  $return = "<span class='time' data-toggle='tooltip' title='$actual'>";
  if ($str) {
    $return.= "$str";}
  else {
    $return.= relativeTime($date);
  }
  $return.= "</span>";
  return $return;
}

/**
 * Replace links in text with html links
 *
 * @param  string $text
 * @return string
 */
function auto_link_text($text) {
  $regex = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
  return preg_replace_callback($regex, function ($matches) {
    return "<a href='{$matches[0]}' target='_blank'>{$matches[0]}</a>";
  }, $text);
}

function returnError($msg) {
  return json_encode(array('message'=>$msg,'level'=>0));
}

function returnMessage($msg) {
  return json_encode(array('message'=>$msg,'level'=>2));
}

function returnSuccess($msg) {
  return json_encode(array('message'=>$msg,'level'=>1));
}

function parseReturn($msg){
  $msg = json_decode($msg);
  switch ($msg->level){
    case 0:
      $class = "danger";
    break;

    case 1:
      $class = "success";
    break;

    default:
      $class = "info";
    break;
  }
  echo alert($class,$msg->message);
}