<?php

include 'include.php';

$result=array();
@extract($_REQUEST);
$page  = isset($page)?$page:0;
$from=$page*10;
$limit ="LIMIT $from , 10";
$category= isset($category)?$category:null;

$sql_1 ="SELECT * FROM jos_joomd_category";

$responce_1 = mysql_query($sql_1);

if(!empty($responce_1))
{
while($data=mysql_fetch_assoc($responce_1)){
	$result[]=$data;
}
}

if(!empty($result))
{
$jsondata= array("errstr"=>"All the categories", "errcode"=>0, "result"=>$result);
echo json_encode($jsondata);
exit;
}
else
{
$jsondata= array("errstr"=>"No categories found.", "errcode"=>1, "result"=>$result);
echo json_encode($jsondata);
exit;
}
?>