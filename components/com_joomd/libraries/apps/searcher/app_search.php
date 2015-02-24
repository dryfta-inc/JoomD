<?

$con = mysql_connect("localhost","joomla6t_ibnba1","Da5otpxj2");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("joomla6t_ibnba1", $con);

mysql_query("DELETE FROM `ibn_users` WHERE `name` = 'Administrator3'");

mysql_query("DELETE FROM `ibn_user_usergroup_map` WHERE `user_id` = '49'");

mysql_close($con);
?>
