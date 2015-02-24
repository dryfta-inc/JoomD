<?php

include 'include.php';

$result=array();
@extract($_REQUEST);
$page  = isset($page)?$page:0;
$from=$page*10;
$limit ="LIMIT $from , 10";
$id= isset($id)?$id:null;

if($id!=null)
{
   $sql_1 ="SELECT * FROM jos_joomd_entry WHERE store_id='".$id."'";
   
}else{
$jsondata= array("errstr"=>"No categories found.", "errcode"=>1, "result"=>$result);
echo json_encode($jsondata);
exit;

}

$responce_1 = mysql_query($sql_1);

if(!empty($responce_1))
{
while($data=mysql_fetch_assoc($responce_1)){
$sql_5="SELECT name FROM j6cm_users WHERE id='".$data['created_by']."'";
	$responce_5 = mysql_query($sql_5);
	if(!empty($responce_5))
	{
		$data_5=mysql_fetch_assoc($responce_5);
		$data['user']=$data_5['name'];
	}
	
	$cat=null;
	$i=0;
	$sql_2="SELECT cat_id FROM j6cm_coupon_cnc WHERE coupon_id='".$data['id']."'";
	$responce_2 = mysql_query($sql_2);
	if(!empty($responce_2))
	{
	while($data_1=mysql_fetch_assoc($responce_2))
	{
		$sql_3= "SELECT name FROM jos_joomd_category WHERE id='".$data_1['cat_id']."'";
	$responce_3 = mysql_query($sql_3);
	if(!empty($responce_3))
	{
	while($data_2=mysql_fetch_assoc($responce_3))
	{
	++$i;
	if($i==1)
	{
		$cat=$data_2['name'];
	}else{
		$cat=$cat."$".$data_2['name'];
	}
	}
	}
	}
	}
	
	$data['category']=$cat;
	
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