<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>在线客服系统--客服注册</title>
	<link href="/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/style.css" rel="stylesheet">
	<script type="text/javascript" src="/js/jquery.min.js"></script>
</head>
<body>
		<div class="container">
       <div class="jumbotron">
         <div class="page-header text-center">
           <h1>注册客服
        </h1>
       </div>
        <form class="form-horizontal" role="form" method="post" action="checkregister">
          <div class="form-group">
            <label for="account" class="col-sm-3 control-label">账号</label>
            <div class="col-sm-6">
               <input type="text" class="form-control" name="account" placeholder="请输入您的账号">
            </div> 
          </div>
           <div class="form-group">
            <label for="user_name" class="col-sm-3 control-label">昵称</label>
            <div class="col-sm-6">
               <input type="text" class="form-control" name="user_name" placeholder="请输入您的昵称">
            </div> 
          </div>
           <div class="form-group">
            <label for="pwd" class="col-sm-3 control-label">密码</label>
            <div class="col-sm-6">
               <input type="password" class="form-control" name="pwd" placeholder="请输入您的密码">
            </div> 
          </div>
           <div class="form-group">
            <label for="sex" class="col-sm-3 control-label ">性别</label>
            <div class="col-sm-6">
             <label class="radio-inline">
             <input type="radio" name="sex" value="1" checked> 男
             </label>
             <label class="radio-inline">
             <input type="radio" name="sex" value="0">女
             </label>
            </div> 
          </div>
           <div class="form-group">
            <div class="col-sm-offset-8 col-sm-7">
               <button type="submit" class="btn btn-default">登录</button>
            </div>
          </div>
        </form>
       </div>
	</div>
</body>
</html>