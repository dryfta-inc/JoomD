<?php

include 'include.php';

$result=array();
@extract($_REQUEST);
$page  = isset($page)?$page:0;
$from=$page*10;
$category=$_POST['category'];
$limit ="LIMIT $from , 10";
$id= isset($id)?$id:null;
//$category=isset($category)?$category:null;
//echo 'category is '.$category;
echo $id;
if($category == 'featured')
{
   $sql_1 ="SELECT * FROM jos_joomd_entry WHERE published=1 AND featured=1";

}else if($category=='latest')
{

   $sql_1 ="SELECT * FROM jos_joomd_entry WHERE published='1' ORDER BY created";
   
}else{
$jsondata= array("errstr"=>"No categories found.", "errcode"=>1, "result"=>$result);
echo json_encode($jsondata);

exit;

}

$responce_1 = mysql_query($sql_1);
print_r($responce_1);
if(!empty($responce_1))
{
while($data=mysql_fetch_assoc($responce_1)){

$sql_5="SELECT name FROM jos_users WHERE id='".$data['created_by']."'";
	$responce_5 = mysql_query($sql_5);
	if(!empty($responce_5))
	{
		$data_5=mysql_fetch_assoc($responce_5);
		$data['user']=$data_5['name'];
	}
	
	$cat=null;
	$i=0;
	$sql_2="SELECT cat_id FROM jos_coupon_cnc WHERE coupon_id='".$data['id']."'";
	$responce_2 = mysql_query($sql_2);
	if(!empty($responce_2))
	{
	while($data_1=mysql_fetch_assoc($responce_2))
	{
		$sql_3= "SELECT name FROM jos_coupons_category WHERE id='".$data_1['cat_id']."'";
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
	$sql_4="SELECT * FROM jos_coupon_store WHERE id='".$data['store_id']."'";
	$responce_4 = mysql_query($sql_4);
	
	if(!empty($responce_4))
	{
	while($data_4=mysql_fetch_assoc($responce_4))
	{
		$data['store']=$data_4['name'];
	}
	
	}
	
	$data['category']=$cat;
	
	$result[]=$data;
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
}else
{
$jsondata= array("errstr"=>"No categories found.", "errcode"=>1, "result"=>$result);
echo json_encode($jsondata);
echo "Shahzad";
exit;
}


?>