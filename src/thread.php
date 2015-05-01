<?php
require "header.php";
if(isset($_GET['b']))
	$boardid = intval($_GET['b']);
else
	header('Location: index.php');
if(isset($_GET['t']))
	$threadid = intval($_GET['t']);
else
	header('Location: board.php?b=' . $boardid);
$con = mysql_connect("localhost", $dbuser, $dbpass);
mysql_select_db($dbname);
if(isset($_POST['captcha']) && isset($_POST['msg'])){
	if(isset($_POST['msg']))
		$msg = str_replace("'", "&#39", trim($_POST['msg']));
	if($_POST['captcha'] == false || $_POST['msg'] == false){
		$error = "You must fill all fields";
	}else{
		if(!mb_check_encoding($msg, "Shift_JIS"))
			$error = "Illegal encoding or string too long";
		else{
			if(mb_strlen($msg, "Shift_JIS") > $maxmsgchars){
				$error = "String too long";
			}else{
				if(mb_strlen($msg, "Shift_JIS") < $minmsgchars){
					$error = "String too short";
				}else{
					if(mb_substr_count($msg, "\n", "Shift_JIS") > $maxmsglines)
						$error = "Too many line breaks";
					else{
						session_start();
						if($_SESSION['6_letters_code'] != $_POST['captcha'])
							$error = "Wrong captcha answer";
						else{
							mysql_query("insert into posts(threadid, boardid, author, msg, dat) values(" . $threadid . ", " . $boardid . ", '" . gen_id() . "', '" . $msg . "', NOW())");
							unset($msg);
						}
					}
				}
			}
		}
	}
}
$rs = mysql_query("select subject, count(posts.id) from threads, posts where threads.id=" . $threadid . " and threads.boardid=" . $boardid . " and posts.threadid=threads.id and posts.boardid=threads.boardid");
$row = mysql_fetch_row($rs);
if(mysql_num_rows($rs) != 1 || $row[1] == 0)
	header('Location: board.php?b=' . $boardid);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<link rel="icon" href="shortcut favicon.ico">
<link rel="stylesheet" href="stylesheet.css"/>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta http-equiv="pragma" content="no-cache">
<title><?php echo sub_clean($row[0]); ?></title>
</head>
<body>
<center>
<table align="center">
<?php
if(isset($error))
	echo "<tr><td colspan='2'>" . $error . "</td></tr>";
?>
<tr><td colspan="2" style="text-align: left;"><a style="display: block; margin-bottom: 18px;" href="board.php?b=<?php echo $boardid; ?>">Return</a><b>[1:<?php echo $row[1] ?>]</b>&nbsp;&nbsp;&nbsp;<a style="color: red; font-size: 20px; font-weight: bold;"><?php echo sub_clean($row[0]); ?></a></td></tr><tr><td colspan="3"><dl>
<?php
$rs2 = mysql_query("select author, msg, dat from posts where threadid=" . $threadid . " and boardid=" . $boardid . " order by id");
$rowcount = mysql_num_rows($rs2);
$num2 = 1;
while(($row2 = mysql_fetch_row($rs2)))
	echo "<dt>" . $num2++ . " ID: <font color=\"green\">" . substr($row2[0], 0, 10) . "</font>&nbsp;&nbsp;&nbsp;Date: " . $row2[2] . "</dt><dd>" . msg_clean_show($row2[1]) . "</dd>";
?>
<dd><form method="POST" style="margin-top: 25px;"><tfoot><tr><td align="right"><img src="captcha.php?rand=<?php echo rand(); ?>" onclick="refreshCaptcha();" id="captchaimg"><br/><input id="6_letters_code" name="captcha" size="16" type="text" required="true"></td><td><textarea rows="5" cols="50" name="msg" required="true"><?php if(isset($msg)) echo $msg; ?></textarea><br/><input value="Submit" name="submit" type="submit"></td></tr></tfoot></form></dd></dl></td></tr></table>
</center>
</body>
</html>
<?php
mysql_close($con);
?>