<?php
require "h.php";
if(isset($_GET['b']))
	$boardid = intval($_GET['b']);
else
	header('Location: index.php');
$con = mysql_connect("localhost", $dbuser, $dbpass);
mysql_select_db($dbname);
$rs = mysql_query("select name from boards where id=" . $boardid);
$row = mysql_fetch_row($rs);
if(mysql_num_rows($rs) == 0)
	header('Location: index.php');
$boardname = $row[0];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="s.css"/>
<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">
<meta http-equiv="pragma" content="no-cache">
<title><?php echo $boardname; ?></title>
</head>
<body>
<center>
<table>
<tr>
<td>
<a style="margin-bottom: 18px;" href="b.php?b=<?php echo $boardid ?>">Return</a>
<br/>
<br/>
<?php
$rs = mysql_query("select threads.id, subject, count(threads.id) from threads, posts where threads.boardid = " . $boardid . " and threads.id = posts.threadid group by threads.id order by max(posts.dat) desc");
while(($row = mysql_fetch_row($rs)))
	echo "<a href=\"t.php?t=" . $row[0] . "&b=" . $boardid . "\">" . sub_clean($row[1]) . " (" . $row[2] . ")</a>&nbsp;&nbsp;&nbsp;";
if(mysql_num_rows($rs) == 0)
	echo "<center>No threads found.</center>";
mysql_close($con);
?>
</td>
</tr>
</table>
</center>
</body>
</html>