<?php

include 'include.php';

$result=array();
@extract($_REQUEST);

$cat= isset($cat)?$cat:null;

if($cat=="latest")
{
	$sql_1 = "SELECT id,name,img logo FROM jos_joomd_category ORDER BY created";
}else if($cat=="featured"){

	$sql_1 = "SELECT id,name,img logo FROM jos_joomd_category where featured='1'";
}else
{
	$sql_1 = "SELECT id,name,img logo FROM jos_joomd_category";
}
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