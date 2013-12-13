<?php
ini_set("display_errors", 0);
function sub_clean($s){
	return str_replace("\n", " ", str_replace(">", "&#62", str_replace("<", "&#60", str_replace("\"", "&#34", trim($s)))));
}
function msg_clean($s){
	return str_replace("\n", "<br/>", str_replace(">", "&#62", str_replace("<", "&#60", str_replace("\"", "&#34", trim($s)))));
}
function msg_clean_show2($delLeft, $delRight, $pattern, $startpos, $s, $reapply){
	if($reapply == true)
		$s = msg_clean_show1($startpos + 1, $s);
	$endpos = strpos($s, $delRight, $startpos);
	if($endpos != false){
		$length = $endpos - ($startpos + mb_strlen($delLeft));
		$content = trim(substr($s, $startpos + mb_strlen($delLeft), $length));
		$s = substr($s, 0, $startpos) . str_replace("$", $content, $pattern) . substr($s, $endpos + mb_strlen($delRight));
	}
	return $s;
}
function msg_clean_show1($start, $s){
	$min = mb_strlen($s) + 1;
	$pos = strpos($s, "[emph]", $start);
	if($pos !== false && $min > $pos)
		$min = $pos;
	$pos = strpos($s, "[spoiler]", $start);
	if($pos !== false && $min > $pos)
		$min = $pos;
	$pos = strpos($s, "[code]", $start);
	if($pos !== false && $min > $pos)
		$min = $pos;$pos = strpos($s, "[url]", $start);
	if($pos !== false && $min > $pos)
		$min = $pos;$pos = strpos($s, "[b]", $start);
	if($pos !== false && $min > $pos)
		$min = $pos;
	$pos = strpos($s, "[u]", $start);
	if($pos !== false && $min > $pos)
		$min = $pos;
	$pos = strpos($s, "[i]", $start);
	if($pos != false && $min > $pos)
		return msg_clean_show2("[i]", "[/i]", "<i>$</i>", $pos, $s, true);
	if($min === strpos($s, "[emph]", $start))
		return msg_clean_show2("[emph]", "[/emph]", "<a style=\"background-color: pink;\">$</a>", $min, $s, true);
	if($min === strpos($s, "[spoiler]", $start))
		return msg_clean_show2("[spoiler]", "[/spoiler]", "<a style=\"background-color: yellow;\" alt=\"$\" title=\"$\">SPOILER</a>", $min, $s, false);
	if($min === strpos($s, "[code]", $start))
		return msg_clean_show2("[code]", "[/code]", "<pre style=\"background-color: black; color: white;\">$</pre>", $min, $s, false);
	if($min === strpos($s, "[url]", $start))
		return msg_clean_show2("[url]", "[/url]", "<a href=\"$\" target=\"_blank\" style=\"text-decoration: underline;\">$</a>", $min, $s, false);
	if($min === strpos($s, "[b]", $start))
		return msg_clean_show2("[b]", "[/b]", "<b>$</b>", $min, $s, true);
	if($min === strpos($s, "[u]", $start))
		return msg_clean_show2("[u]", "[/u]", "<u>$</u>", $min, $s, true);
	return $s;
}
function msg_clean_show($s){
	return msg_clean_show1(0, msg_clean($s));
}
function gen_id(){
	return sha1($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'], false);
}
$websitename = "domain.com";
$showthreads = 40;
$maxmsgchars = 1500;
$maxmsglines = 50;
$maxsubchars = 48;
$minmsgchars = 2;
$minsubchars = 3;
$msgpreviews = 5;
$dbname = "maindb";
$dbuser = "root";
$dbpass = "";
?>