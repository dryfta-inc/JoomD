<?php
include 'include.php';
@extract($_REQUEST);

$result=array();

$id= isset($id)?$id:null;

$sql_1 ="SELECT * FROM j6cm_coupon_cnc WHERE cat_id='".$id."'";

$responce_1 = mysql_query($sql_1);

if(!empty($responce_1))
{
while($data=mysql_fetch_assoc($responce_1)){

	$sql_2="select * from j6cm_coupons where id='".$data['coupon_id']."'";
	$responce_2 = mysql_query($sql_2);
	if(!empty($responce_2))
	{
	while($data_1=mysql_fetch_assoc($responce_2))
	{
		$sql_3="select name from j6cm_coupon_store where id='".$data_1['store_id']."'";
		$responce_3 = mysql_query($sql_3);
		$data_3=mysql_fetch_assoc($responce_3);
		$data_1['store']=$data_3['name'];
		$sql_5="SELECT name FROM j6cm_users WHERE id='".$data_1['created_by']."'";
	$responce_5 = mysql_query($sql_5);
	if(!empty($responce_5))
	{
		$data_5=mysql_fetch_assoc($responce_5);
		$data_1['user']=$data_5['name'];
	}
		$result[]=$data_1;
	}
	}else{
	continue;
	}
	
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