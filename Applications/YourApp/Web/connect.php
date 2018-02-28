<?php 
$dbhost='localhost:3306';
$dbuser='root';
$dbpass='123456';
if($account && $pwd !=''){
	$conn=mysqli_connect($dbhost,$dbuser,$dbpass);
	if(!$conn){
		echo json_encode(array('status'=>'2','msg'=>mysqli_error()));
	}
	mysqli_query($conn,"set names utf8");
	mysqli_select_db($conn,'chat');
}


?>