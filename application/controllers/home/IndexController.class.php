<?php
//首页控制器
class IndexController extends   BaseController {
	//index方法
	public function indexAction(){
	    //设置默认数据格式，便于测试时使用
	    header("Content-type: text/html; charset=utf-8");
	    
	    //初始化返回参数
	    $rdata['status'] = "false";
	    $rdata['msg'] = "操作失败";
	    //调用common类
	    $commonClass=new Common();
	    //接收参数
		$type=$_GET["type"];
		if($type=="")
		{
		    $rdata['msg']="type参数不能为空";
		}else 
		{
		   $rdata['status'] = "true";
		    //防止跨脚本攻击     
		   $type=$commonClass->SafeFilterStr($type);
		   //处理查询表
		   $t=$this->TablenameFilter($_GET["t"]);
		   // Print 打印测试，如需把sql打印出来，把改成yes，需要显示结果可不传或为空。
		   $print=$_GET["print"];
		    //自定义查询开始
		   if($type=="search")
		    {
		        //处理返回的列名称，用于多表查询
		        $liemingcheng=$this->LiemingchengFilter($_GET["liemingcheng"]);
		        //返回条数
		        $number=$this->NumberFilter($_GET["number"]);
		        //当前页数
		        $page=$this->NumberFilter($_GET["page"]);
		        
		        // 		   ordertype：排序字段，默认已有ID，如不需要排序请为空
		        $ordertype=$commonClass->SafeFilterStr($_GET["ordertype"]);
		        // 		   orderby：排序方式，升序和降序
		        $orderby=$commonClass->SafeFilterStr($_GET["orderby"]);
		        // 		   sqlvalue：默认查询方式,如果有多个用逗号分隔，“|”会替换成=号
		        $sqlvalue=$this->SqlvalueFilter($_GET["sqlvalue"]);
		       
		        
		        //拼接为sql语句
		        $_sql=$this->getSql($t,$liemingcheng,$number,$page,$ordertype,$orderby,$sqlvalue);
		        if($print=="yes")
		        {
		            echo $_sql;
		            die();
		        }
		        $temp_model = new Model("moxing");
		        $temp_arr=$temp_model->select($_sql);
		        //单独处理时间的格式
		        foreach ($temp_arr as $k=>$v)
		        {
		            if($temp_arr[$k]["dtime"]!=null)
		            {
		                $temp_arr[$k]["dtime"]=$commonClass->time_tran($v["dtime"]);
		                $temp_arr[$k]["dtime1"]=$v["dtime"];
		            }
		            //echo $temp_arr[$k]["dtime"];
		        }
		        
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }else if ($type=="add")
		    {
		        $tableName=str_replace("sl_","",$t); 
		        //待处理的表
		        $tableModel = new Model($tableName);
		        $moxingModel = new Model("moxing");
		        $model_id=$moxingModel->selectByCol("u1", $t)["id"];
		        //1.收集表单数据
		        $data=$tableModel->getFieldArray();
		        //2.验证和处理
		        $this->helper('input');
		        $data = deepspecialchars($data);
		        
		        
		        if($tableModel->insert($data)){
		            $rdata['status'] = "true";
		            $rdata['msg'] = "更新成功";
		        
		        }else{
		            $rdata['status'] = "false";
		            $rdata['msg'] = "更新失败";
		        }
		        
		        
		    }else if ($type=="edit")
		    {
		        $tableName=str_replace("sl_","",$t);
		        //待处理的表
		        $tableModel = new Model($tableName);
		        $moxingModel = new Model("moxing");
		        $model_id=$moxingModel->selectByCol("u1", $t)["id"];
		        //1.收集表单数据
		        $data=$tableModel->getFieldArray();
		        //2.验证和处理
		        $this->helper('input');
		        $data = deepspecialchars($data);
		        
		        
		        if($tableModel->update($data)){
		            $rdata['status'] = "true";
		            $rdata['msg'] = "更新成功";
		            
		        }else{
		            $rdata['status'] = "false";
		            $rdata['msg'] = "更新失败";
		        }
		        
		        
		    }else if ($type=="login")
		    {
		        $rdata['status'] = "false";
		        $rdata['msg'] = "操作失败";
		        
		        // 1.获取用户名和密码
		        $username = trim($_REQUEST['yonghuming']);
		        $password = trim($_REQUEST['mima']);
		        // 对用户名和密码进行转义
		        $username = addslashes($username);
		        $password = addslashes($password);
		        $password=md5($password);
		        //echo $password;die();
		        // 2.判断登录频率
		        $Common = new Common();
		        $System = new SystemModel('System');
		        
		        $date1 = date('Y-m-d H:i:s', time());
		        $date2 = $date1;
		        $System1 = new SystemModel('System');
		        $date2 = $System1->oneRowCol("dtime", " u1='" . $username. "' and u4='用户登录' order by id desc ");
		        $date2 = $date2['dtime'];
		        $minute = floor((strtotime($date1) - strtotime($date2)) % 86400 / 60);
		        
// 		        echo $date1." date1<br>";
// 		        echo $date2." date2<br>";
// 		        echo $minute."minute3<br>";
// 		        die();
		        
// 		        if ($minute < 1) {
// 		            $rdata['msg']="您的操作过于频繁";
// 		            //返回接口数据
// 		            $this->ajaxReturn($rdata);
// 		        }
		        // 验证和处理
		        if (! ($Common->isName($username) || $Common->isName($password))) {
		            // 写入日志
		            $sysData["u1"]=$username;
		            $sysData["yonghuming"]=$username;
		            $sysData["u4"]='用户登录';
		            $sysData["u3"]=$Common->getIP();
		            $sysData["u2"]=$username . ":登录失败，用户名或密码不合法。". $Common->getUrl();
		            $System->insert($sysData);
		            $rdata['msg']="用户名或密码不能为空";
		            //返回接口数据
		            $this->ajaxReturn($rdata);
		        }
		        
		        // 3.调用模型来完成验证操作并给出提示
		        $userModel = new Model('user');
		        $user = $userModel->select("select * from {$t} where yonghuming='$username' and mima='$password'  ");
		        if (count($user)>0) {
		            // 写入日志
		            $sysData["u1"]=$username;
		            $sysData["yonghuming"]=$username;
		            $sysData["u4"]='用户登录';
		            $sysData["u3"]=$Common->getIP();
		            $sysData["u2"]=$username . ":登录成功。 ". $Common->getUrl();
		            $System->insert($sysData);
		            
		            // 登录成功,保存登录标识符
		            $token=$commonClass->encrypt($username, "ghy");
		            $user[0]["token"]=$token;
		            $rdata['status'] = "true";
		            $rdata['msg']=$user;
		            
		        } else {
		            // 写入日志
		            $sysData["u1"]=$username;
		            $sysData["yonghuming"]=$username;
		            $sysData["u4"]='用户登录';
		            $sysData["u3"]=$Common->getIP();
		            $sysData["u2"]=$username . ":登录失败，用户名或密码错误。". $Common->getUrl();
		            $System->insert($sysData);
		            // 失败
		            $rdata['msg']="用户名或密码错误";
		        }
		        
		    }
		    else if($type=="updateimg")
		    {
		        $rdata['status'] = false;
		        $rdata['msg'] = "上传失败";
		        
		        $base64_image_content = $_REQUEST['img'];
		        if( $base64_image_content=="")
		        {
		            $rdata['status'] = true;
		            $rdata['msg'] = "上传失败！";
		            $this->ajaxReturn($rdata);
		        }
		        //匹配出图片的格式
		        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
		            $type = $result[2];
		            $new_file = "public/app/uploads/";
		            if(!file_exists($new_file))
		            {
		                //检查是否有该文件夹，如果没有就创建，并给予最高权限
		                mkdir($new_file, 0700,true);
		            }
		            $new_fileName="fromApp".time().mt_rand(1,100).".{$type}";
		            $new_file = $new_file.$new_fileName;
		            
		            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
		                
		                $rdata['status'] = true;
		                $rdata['msg'] = $new_file;
		                
		            }else{
		                $rdata['status'] = false;
		                $rdata['msg'] = "上传失败！";
		            }
		        }
		        
		        
		    }
		    else if($type=="zoufangjilu")
		    {
		        //处理返回的列名称，用于多表查询
		        $liemingcheng=$this->LiemingchengFilter($_GET["liemingcheng"]);
		        //返回条数
		        $number=$this->NumberFilter($_GET["number"]);
		        //当前页数
		        $page=$this->NumberFilter($_GET["page"]);
		        
		        // 		   ordertype：排序字段，默认已有ID，如不需要排序请为空
		        $ordertype=$commonClass->SafeFilterStr($_GET["ordertype"]);
		        // 		   orderby：排序方式，升序和降序
		        $orderby=$commonClass->SafeFilterStr($_GET["orderby"]);
		        // 		   sqlvalue：默认查询方式,如果有多个用逗号分隔，“|”会替换成=号
		        $sqlvalue=$this->SqlvalueFilter($_GET["sqlvalue"]);
		        
		        
		        //拼接为sql语句
		        $_sql=$this->getSql($t,$liemingcheng,$number,$page,$ordertype,$orderby,$sqlvalue);
		        if($print=="yes")
		        {
		            echo $_sql;
		            die();
		        }
		        
		        $temp_model = new Model($t);
		        $temp_arr=$temp_model->select($_sql);
		        //处理多表数据
		        foreach ($temp_arr as $k=>$v)
		        {
		            if($temp_arr[$k]["dtime"]!=null)
		            {
		                $temp_arr[$k]["dtime"]=$commonClass->time_tran($v["dtime"]);
		                $temp_arr[$k]["dtime1"]=$v["dtime"];
		            }
		            //户籍数据
		            $huji_model= new Model("huji");
		            $huji = $huji_model->selectByCol("huhao", $v["huhao"]);
		            $temp_arr[$k]["huji"]=$huji;
		            //组名
		            $canshu_model= new Model("canshu");
		            $canshu = $canshu_model->selectByPk($v["suoshuzu"]);
		            $temp_arr[$k]["zu"]=$canshu;
		            //村名
		            $canshu = $canshu_model->selectByPk($v["suoshucun"]);
		            $temp_arr[$k]["cun"]=$canshu;
		            //echo $temp_arr[$k]["dtime"];
		        }
		        
		        //下面两个方法都是输出查询结果
		        //var_dump($temp_model->select($_sql));
		        //$print($temp_model->select($_sql));
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }
		    else if($type=="随便自己命名的方法")
		    {
		        $_sql="select * from sl_user ";
		        $temp_model = new Model("moxing");
		        $temp_arr=$temp_model->select($_sql);
		       
		        
		        //下面两个方法都是输出查询结果
		        //var_dump($temp_model->select($_sql));
		        //$print($temp_model->select($_sql));
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }
		    
		  
		}
		//测试数据
		//echo  $rdata['msg'];die();
		//返回接口数据
		$this->ajaxReturn($rdata);
		
	}
		
}