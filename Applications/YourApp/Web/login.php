<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>在线访客系统-客服登录界面</title>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/style.css" rel="stylesheet">
	<script type="text/javascript" src="/js/jquery.min.js"></script>
</head>
<body>
	<div class="container">
     <div class="jumbotron">
       <div class="page-header text-center">
        <h1>客服管理系统
          <small><h3>采用Gateworker框架</h3></small>
        </h1>
       </div>
       <form class="form-horizontal" role="form" method="post" onsubmit="onSubmit();return false;">
          <div class="form-group">
            <label for="account" class="col-sm-3 control-label">账号</label>
                <div class="col-sm-5">
                 <input type="text" class="form-control" id="account" placeholder="请输入您的名字">
                </div> 
          </div>
          <div class="form-group">
            <label for="password" class="col-sm-3 control-label">密码</label>
            <div class="col-sm-5">
              <input type="password" id="pwd" class="form-control">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-7 col-sm-7">
               <button type="submit" class="btn btn-default">登录</button>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-6 col-sm-7">
              <a href="./register.php">如您尚无账号,请点击注册</a>
            </div>
          </div>
       </div>
     </div>
</body>
</html>
<script type="text/javascript">
  function onSubmit(){
    var account=$("#account").val();
    var pwd=$("#pwd").val();
    $.post("checklogin.php",{account:account,pwd:pwd},function(data){
    	  data=eval("("+data+")");
    	  name=data.msg;
    	  if(data.status == '1'){
    	  	console.log(data.msg);
    	  	var service='http://'+window.location.host+'/service.php'+'?user_name='+name;
    	  	$(location).attr('href', service);
    	  }else if(data.status == '2'){
             alert("数据库连接错误");
    	  }else if(data.status=='0'){
    	  	alert(data.msg);
    	  }else{
    	  	console.log(data);
    	  }
    });

  }
 
</script>