<?php session_start();
echo $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>在线客服系统--客服界面</title>
	 <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/jquery-sinaEmotion-2.1.0.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
	
  <script type="text/javascript" src="/js/swfobject.js"></script>
  <script type="text/javascript" src="/js/web_socket.js"></script>
  <script type="text/javascript" src="/js/jquery.min.js"></script>
  <script type="text/javascript" src="/js/jquery-sinaEmotion-2.1.0.min.js"></script>
  <script type="text/javascript">
   if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
   // 如果浏览器不支持websocket，会使用这个flash自动模拟websocket协议，此过程对开发者透明
    WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
    // 开启flash的websocket debug
    WEB_SOCKET_DEBUG = true;
    var ws, name, group,client_list={};
    var customer_id='';
    //连接服务器端
    function connect(){
      ws = new WebSocket("ws://"+document.domain+":7272");
      ws.onopen = onopen;
      ws.onmessage = onmessage; 
      // ws.onclose = function() {
    	 //  console.log("连接关闭，定时重连");
      //     connect();
      //  };
      // ws.onerror = function() {
     	//   console.log("出现错误");
      //  };

    }
     setInterval(fresh_list,15000); 

    function onmessage(e){
    	console.log(e.data);
    	var data = JSON.parse(e.data);
    	switch(data['type']){
    		case 'ping':
                ws.send('{"type":"pong"}');
                break;
            case 'service_login':
                var list=data.list;
                flush_client_list(list);
                break;
            case 'begin_connect':
                sayHello(data.message);
                break;
            case 'say':
                say(data.from_client_id,data.content,data.time);
                break;
            case 'fresh_list':
                var list=data.list;
                flush_client_list(list);
                break;
            
                        
    }
    }
    function sendMessage(msg) {
        waitForSocketConnection(ws, function() {
            ws.send(msg);
        });
    }

    function waitForSocketConnection(socket, callback){
        setTimeout(
            function(){
                if (socket.readyState === 1) {
                    if(callback !== undefined){
                        callback();
                    }
                    return;
                } else {
                    waitForSocketConnection(socket,callback);
                }
            }, 5);
    }
    
    function onopen(){
       if(!name && !group){
       	var name=$("#sessionId").val();
       	var group='service';
       	var login_data='{"type":"service_login","user_name":"'+name+'","group":"'+group+'"}';
       	console.log("websocket握手成功，发送登录数据:"+login_data);
       	sendMessage(login_data);
       }
    }

        // 刷新用户列表框
    function flush_client_list(list){
    	var userlist_window = $("#userlist");
    	userlist_window.empty();
    	userlist_window.append('<h4>在线用户</h4><ul>');
        $.each(list,function(id,name){
          userlist_window.append('<li id="'+name+'" onclick="select_customer(this);">'+'<a href="#">'+'访客'+id+'</a>'+'</li>');
        });
    	userlist_window.append('</ul>');
    }

    function select_customer(obj){
    	$("#dialog").show();
         $("form").show();
        var begin_connect='{"type":"begin_connect"}';
          sendMessage(begin_connect);
          console.log("与服务器及建立链接:"+begin_connect);
          customer_id=$(obj).attr("id");
    }

      //建立连接sayhello
    function sayHello(data){
       $("#dialog").append('<div class="speech_item"><div style="clear:both;"></div><p class="triangle-isosceles top">'+'已与访客建立连接'+'</p></div>');
    }

    function say(from_client_id,content,time){
    	//解析新浪微博图片
       content = content.replace(/(http|https):\/\/[\w]+.sinaimg.cn[\S]+(jpg|png|gif)/gi, function(img){
            return "<a target='_blank' href='"+img+"'>"+"<img src='"+img+"'>"+"</a>";}
        );
               //解析url
        content = content.replace(/(http|https):\/\/[\S]+/gi, function(url){
            if(url.indexOf(".sinaimg.cn/") < 0)
                return "<a target='_blank' href='"+url+"'>"+url+"</a>";
            else
                return url;
        }
        );
        //追加到方框中
    	$("#dialog").append('<div class="speech_item"><img src="http://lorempixel.com/38/38/?'+from_client_id+'" class="user_icon" /> '+' <br> '+time+'<div style="clear:both;"></div><p class="triangle-isosceles top">'+content+'</p> </div>');        

    } 

    function onSubmit(){
    	var input= $("#textarea").val();
    	var to_client_id =customer_id;
        sendMessage('{"type":"say","to_client_id":"'+to_client_id+'","content":"'+input.replace(/"/g, '\\"').replace(/\n/g,'\\n').replace(/\r/g, '\\r')+'"}'); 
        $("#textarea").val("");
        $("#textarea").focus();
    }   

    function fresh_list(){
        sendMessage('{"type":"fresh_list","request_group":"customer"}');
    }
    

   </script>
</head>
<body onload="connect();">
	<div class="container">
	    <div class="row clearfix">
	        <div class="col-md-1 column">
	        </div>
	        <div class="col-md-6 column">
	           <div class="thumbnail">
	               <div class="caption" style="display:none;" id="dialog"></div>
	           </div>
	           <form onsubmit="onSubmit(); return false;" style="display:none;">
	                <input type="hidden" id="sessionId" value=<?php echo $_GET['user_name'] ?>>
                    <textarea class="textarea thumbnail" id="textarea"></textarea>
                    <div class="say-btn">
                        <input type="button" class="btn btn-default face pull-left" value="表情" />
                        <input type="submit" class="btn btn-default" value="发表" />
                    </div>
               </form>
	        </div>
	        <div class="col-md-3 column">
	           <div class="thumbnail">
                   <div class="caption" id="userlist"></div>
               </div>
	        </div>
	    </div>
    </div>
</body>
</html>
