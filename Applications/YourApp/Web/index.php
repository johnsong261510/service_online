<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>在线客服系统--访客界面</title>
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
    var ws, name, service_list={};
    var service_id='';
    //链接服务器端
    function connect(){
    	// 创建websocket
       ws = new WebSocket("ws://"+document.domain+":7272");
       ws.open=open();
        // 当有消息时根据消息类型显示不同信息
       ws.onmessage = onmessage; 
       // ws.onclose = function() {
    	  // console.log("连接关闭，定时重连");
       //    connect();
       // };
       ws.onerror = function() {
     	  console.log("出现错误");
       };
    }

    setInterval(fresh_list,15000); 

    function sendMessage(msg) {
        waitForSocketConnection(ws, function() {
            ws.send(msg);
        });
    };

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
    };

    function open(){
       var login_data='{"type":"login"}';
        console.log("websocket握手成功，发送登录数据:"+login_data);
        sendMessage(login_data);       
    }

    function onmessage(e){
        console.log(e.data);

        var data = JSON.parse(e.data);
        switch(data['type']){
        	case 'ping':
            ws.send('{"type":"pong"}');
            break;
            case 'login':
            // var list=JSON.stringify(data);
            // var client_id=list.client_id;
               var data=eval("("+e.data+")");
               var list=data.list;
              flush_client_list(list);
             $.each(list,function(i,n){
             	service_list[i]=n;
             });
             console.log("登录成功");
            break;
            case 'begin_connect':
                sayHello(data.message);

            case 'say':
                say(data.from_client_id,data.content,data.time);
            case 'fresh_list':
                var list=data.list;
                flush_client_list(list);
                break;
        }    	
    }

    //选择客服人员
    function select_server(obj){
         $("#dialog").show();
         $("form").show();
         var begin_connect='{"type":"begin_connect"}';
          sendMessage(begin_connect);
          console.log("与服务器及建立链接:"+begin_connect);
          service_id=$(obj).attr("id");


    }
    

        // 刷新用户列表框
    function flush_client_list(list){
    	var userlist_window = $("#userlist");
    	userlist_window.empty();
    	userlist_window.append('<h4>在线客服</h4><ul>');
        $.each(list,function(id,name){
           userlist_window.append('<li id="'+name+'" onclick="select_server(this);">'+'<a href="#">'+name+'</a>'+'</li>');
             });
    	userlist_window.append('</ul>');
    }
    //建立连接sayhello
    function sayHello(data){
       $("#dialog").append('<div class="speech_item"><div style="clear:both;"></div><p class="triangle-isosceles top">'+data+'</p></div>');
    }
    
    function onSubmit(){
    	var input= $("#textarea").val();
    	var to_client_id =service_id;
        sendMessage('{"type":"say","to_client_id":"'+to_client_id+'","content":"'+input.replace(/"/g, '\\"').replace(/\n/g,'\\n').replace(/\r/g, '\\r')+'"}'); 
        $("#textarea").val("");
        $("#textarea").focus();
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

    function fresh_list(){
        sendMessage('{"type":"fresh_list","request_group":"service"}');
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
	               <div class="caption" style="display: none;" id="dialog"></div>
	           </div>
	           <form onsubmit="onSubmit(); return false;" style="display: none;">
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
               <a href="http://workerman.net:8383" target="_blank"></a>
	        </div>
      </div>
	</div>
</body>
</html>