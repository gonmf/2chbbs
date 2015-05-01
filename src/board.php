<?php
require "header.php";
if(isset($_GET['b']))
	$boardid = intval($_GET['b']);
else
	header('Location: index.php');
$con = mysql_connect("localhost", $dbuser, $dbpass);
mysql_select_db($dbname);
if(isset($_POST['captcha']) && isset($_POST['msg']) && isset($_POST['subject'])){
	if($_POST['captcha'] == false || $_POST['msg'] == false || $_POST['subject'] == false){
		if(isset($_POST['subject']))
			$subject = str_replace("\"", "&#34", str_replace("'", "&#39", trim($_POST['subject'])));
		if(isset($_POST['msg']))
			$msg = str_replace("\"", "&#34", str_replace("'", "&#39", trim($_POST['msg'])));
		$error = "You must fill all fields";
	}else{
		$subject = str_replace("'", "&#39", trim($_POST['subject']));
		$msg = str_replace("'", "&#39", trim($_POST['msg']));
		if(!mb_check_encoding($subject, "Shift_JIS") || mb_strlen($subject, "Shift_JIS") > $maxsubchars || !mb_check_encoding($msg, "Shift_JIS") || mb_strlen($msg, "Shift_JIS") > $maxmsgchars || mb_substr_count($msg, "\n", "Shift_JIS") > $maxmsglines){
			$error = "Illegal encoding or string too long";
		}else{
			if(mb_strlen($subject, "Shift_JIS") < $minsubchars || mb_strlen($msg, "Shift_JIS") < $minmsgchars){
				$error = "String too short";
			}else{
				session_start();
				if($_SESSION['6_letters_code'] != $_POST['captcha']){
					$error = "Wrong captcha answer";
				}else{
					mysql_query("insert into threads(boardid, subject) values(" . $boardid . ", '" . $subject . "')");
					$rs = mysql_query("select max(id) from threads where boardid=" . $boardid . " and subject='" . $subject . "'");
					$row = mysql_fetch_row($rs);
					$threadid = $row[0];
					mysql_query("insert into posts(threadid, boardid, author, msg, dat) values(" . $threadid . ", " . $boardid . ", '" . gen_id() . "', '" . $msg . "', NOW())");
					header("Location: thread.php?b=" . $boardid . "&t=" . $threadid);
				}
			}
		}
	}
}
$rs = mysql_query("select name, notice from boards where id=" . $boardid);
$row = mysql_fetch_row($rs);
if(mysql_num_rows($rs) == 0)
	header('Location: index.php');
$boardname = $row[0];
$notice = $row[1];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta http-equiv="pragma" content="no-cache">
<title><?php echo $boardname; ?></title>
<link rel="stylesheet" href="stylesheet.css"/>
</head>
<body>
<center>
<a name="menu"></a>
<table>
<?php
if($notice != null)
	echo "<tr><td>" . $notice . "</td></tr>";
?>
<tr>
<td>
<?php
if(isset($error))
	echo $error . "</td></tr><tr><td>";
$rs = mysql_query("select threads.id, subject, count(threads.id) from threads, posts where threads.boardid = " . $boardid . " and threads.id = posts.threadid group by threads.id order by max(posts.dat) desc limit " . $showthreads);
$num = 0;
$body = "";
while(($row = mysql_fetch_row($rs))){
	$num++;
	echo "<a class=\"menua\" href='#" . $num . "'>" . $num . ": " . sub_clean($row[1]) . " (" . $row[2] . ")</a>&nbsp;&nbsp;&nbsp;";
	$rs2 = mysql_query("select author, msg, dat from posts where threadid=" . $row[0] . " and boardid=" . $boardid . " order by id");
	$postcount = mysql_num_rows($rs2);
	$body .= "<table><tr><td colspan=\"2\" style=\"text-align: left;\"><a name=\"" . $num . "\"></a><b>[" . $num . ":" . $postcount . "]</b>&nbsp;&nbsp;&nbsp;<a href=\"thread.php?b=" . $boardid . "&t=" . $row[0] . "\" style=\"color: red; font-size: 20px; font-weight: bold;\">" . sub_clean($row[1]) . "</a></td><td align=\"right\"><a href=\"#menu\">&#9632;</a> <a href=\"#";
	if($num == 1)
		$body .= $showthreads . "\">&#9650;</a> <a href=\"#2\">&#9660;</a></td></tr><tr><td colspan=\"3\"><dl>";
	else
		if($num == $showthreads)
			$body .= ($showthreads - 1) . "\">&#9650;</a> <a href=\#1\">&#9660;</a></td></tr><tr><td colspan=\"3\"><dl>";
		else
			$body .= ($num - 1) . "\">&#9650;</a> <a href=\"#" . ($num + 1) . "\">&#9660;</a></td></tr><tr><td colspan=\"3\"><dl>";
	
	$num2 = 0;
	$row2 = mysql_fetch_row($rs2);
	$body .= "<dt>" . ++$num2 . " ID: <font color=\"green\">" . substr($row2[0], 0, 10) . "</font>&nbsp;&nbsp;&nbsp;Date: " . $row2[2] . "</dt><dd>" . msg_clean_show($row2[1]) . "</dd>";
	if($postcount < $msgpreviews + 1){
		while(($row2 = mysql_fetch_row($rs2)))
			$body .= "<dt>" . ++$num2 . " ID: <font color=\"green\">" . substr($row2[0], 0, 10) . "</font>&nbsp;&nbsp;&nbsp;Date: " . $row2[2] . "</dt><dd>" . msg_clean_show($row2[1]) . "</dd>";
	}else{
		while($postcount - $msgpreviews >= $num2){
			$row2 = mysql_fetch_row($rs2);
			++$num2;
		}
		$body .= "<a href=\"thread.php?b=" . $boardid . "&t=" . $row[0] . "\">Read this thread from the beginning</a><br/><br/>";
		while(($row2 = mysql_fetch_row($rs2)))
			$body .= "<dt>" . ++$num2 . " ID: <font color=\"green\">" . substr($row2[0], 0, 10) . "</font>&nbsp;&nbsp;&nbsp;Date: " . $row2[2] . "</dt><dd>" . msg_clean_show($row2[1]) . "</dd>";
	}
	$body .= "</dl></td></tr></table>";
}
if($num == 0)
	echo "<center>No threads found. You can start your own using the form bellow or <a href=\"index.php\">return</a>.</center></td></tr></table>";
else
	echo "<span style=\"display: block; text-align: right;\"><a href=\"index.php\">Return</a>&nbsp;&nbsp;&nbsp;<a href=\"all.php?b=" . $boardid . "\">All Threads</a></span></td></tr></table>" . $body;
?>
<form method="POST">
<table style="padding-bottom: 15px;">
<?php
if($num > 0)
	echo "<tr><td colspan=\"3\" align=\"right\"><a href=\"#menu\">&#9632;</a> <a href=\"#" . $num . "\">&#9650;</a> <a href=\"#1\">&#9660;</a></td></tr>";
?>
<tr><td align="right">Subject</td><td colspan="2"><input name="subject" size="40" type="text" value="<?php if(isset($subject)) echo $subject; ?>" required="true"> <input value="Create new thread" name="submit" type="submit"></td></tr><tr><td align="right"><img src="captcha.php?rand=<?php echo rand(); ?>" onclick="refreshCaptcha();" id="captchaimg"><br/><input id="6_letters_code" name="captcha" size="16" type="text" required="true"></td><td colspan="2"><textarea rows="5" cols="50" name="msg" required="true"><?php if(isset($msg)) echo $msg; ?></textarea></td></tr>
</table>
</form>
</center>
</body>
</html>
<?php
mysql_close($con);
?>
