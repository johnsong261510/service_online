<?php 
session_start();
$dbhost='localhost:3306';
$dbuser='root';
$dbpass='123456';
$account=$_POST['account'];
$pwd=$_POST['pwd'];
if($account && $pwd !=''){
	$conn=mysqli_connect($dbhost,$dbuser,$dbpass);
	if(!$conn){
		echo json_encode(array('status'=>'2','msg'=>mysqli_error()));
	}
	mysqli_query($conn,"set names utf8");
	mysqli_select_db($conn,'chat');
	$sql="select user_id,name from tp_user where user_id='$account' and pwd='$pwd'";
	$result=mysqli_query($conn,$sql);
	$row=mysqli_fetch_array($result,MYSQL_ASSOC);
	// echo $_SESSION['user_id'];
	if(!$row['user_id']){
		echo json_encode(array('status'=>'0','msg'=>'账号或密码错误,请重新输入'));
	}else{
       $_SESSION["user_id"]=$row['user_id'];
       $_SESSION['group']="service";
		echo json_encode(array("status"=>'1','msg'=>$row['name']));
	}
}



?>