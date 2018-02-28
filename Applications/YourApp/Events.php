<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
use \GatewayWorker\Lib\Gateway;

class Events
{
   
   /**
    * 有消息时
    * @param int $client_id
    * @param mixed $message
    */
   public static function onMessage($client_id, $message)
   {
        // debug
        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";
        
        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if(!$message_data)
        {
            return ;
        }
        
        // 根据类型执行不同的业务
        switch($message_data['type'])
        {
            //客服登录将客服id与client_id绑定并放置在客服组中
            case 'service_login':
              //将客服id和client_id绑定，将客服放入客服组
             $user_name=$message_data['user_name'];
             $service_group=$message_data['group'];
             Gateway::setSession($client_id,array('user_name'=>$user_name,'group'=>$service_group));
             $session=Gateway::getSession($client_id);
             Gateway::bindUid($client_id,$user_name);
             Gateway::joinGroup($client_id,$service_group);
             // 获取当前在线访客详情
             $customer_group="customer";
             $customer_list=Gateway::getClientSessionsByGroup($customer_group);
             $customer_client_id_list=array_keys($customer_list);
             $list=array('type'=>'service_login','list'=>$customer_client_id_list,'session'=>$session['user_name']);
             Gateway::sendToCurrentClient(json_encode($list));
             return;

            // 客户端回应服务端的心跳
            case 'pong':
                return;
            // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'login':
                //将访客加入customer 组别
                $customer_group='customer';
                Gateway::joinGroup($client_id,$customer_group);
                //获取客服列表
                $service_group="service";
                $service_list = Gateway::getClientSessionsByGroup($service_group);
                $login['type']='login';
                $login['client_id']=$client_id;
                foreach($service_list as $k=>$v)
                {
                    $login['list'][$k] = Gateway::getSession($k)['user_name'];

                }
                Gateway::sendToCurrentClient(json_encode($login));
                return;
            case 'begin_connect':
                $message=array('type'=>'begin_connect','message'=>'您好,很高兴为您服务,请提交您的问题.');
                Gateway::sendToCurrentClient(json_encode($message)); 
                return;   
            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
            case 'say':
                 if($message_data['to_client_id'] != ''){
                    $new_message=array('type'=>'say',
                        'from_client_id'=>$client_id,
                        'to_client_id'=>$message_data['to_client_id'],
                        'content'=>nl2br(htmlspecialchars($message_data['content'])),
                        'time'=>date('Y-m-d H:i:s'),
                        'ip'=>$_SERVER['REMOTE_ADDR']
                        );
                    Gateway::sendToClient($message_data['to_client_id'],json_encode($new_message));
                    //还要给自己发一遍，用于显示在当前访客页面中
                    $new_message['content'] = nl2br(htmlspecialchars($message_data['content']));
                    return Gateway::sendToCurrentClient(json_encode($new_message));

                 }
            case 'fresh_list':
               $group=$message_data['request_group'];
               $group_list = Gateway::getClientSessionsByGroup($group);
               $member_list=array_keys($group_list);
               $return_message=array('type'=>'fresh_list','list'=>$member_list);
               return Gateway::sendToCurrentClient(json_encode($return_message));
                  


        }
   }
   
   /**
    * 当客户端断开连接时
    * @param integer $client_id 客户端id
    */
   public static function onClose($client_id)
   {
       // debug
       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
       
       // 从房间的客户端列表中删除
       if(isset($_SESSION['room_id']))
       {
           $room_id = $_SESSION['room_id'];
           $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
           Gateway::sendToGroup($room_id, json_encode($new_message));
       }
   }
  
}
