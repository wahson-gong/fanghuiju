<?php
//首页控制器
class IndexController extends   BaseController {
	//index方法
	public function indexAction(){
// 	    ini_set("display_errors", "On");
// 	    error_reporting(E_ALL | E_STRICT);
	    
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
		                $temp_arr[$k]["dtime"]=$commonClass->formatTime($v["dtime"]);
		                $temp_arr[$k]["dtime1"]=$v["dtime"];
		            }
		            
// 		            //给人员表添加姓名拼音首字母
// 		            if($t=="sl_renyuan")
// 		            {
// 		                //生成首字母 start
		                
// 		                //挂载中文转拼音类
// 		                include_once LIB_PATH . "CUtf8_PY.class.php";
// 		                $ch2ypClass =new CUtf8_PY();
// 		                //转换后的参数
// 		                if($v["xingming"]=="")
// 		                {
// 		                    $shouzimu="";
// 		                }
// 		                else
// 		                {
// 		                    $shouzimu=substr( $ch2ypClass->encode($v["xingming"],"all"), 0, 1 );
// 		                }
		                
// 		                $temp_arr[$k]["shouzimu"]=$shouzimu;
// 		                //生成首字母 end
// 		            }
		            
// 		            //给人员表添加姓名拼音首字母
// 		            if($t=="sl_huji")
// 		            {
// 		                //生成首字母 start
		                
// 		                //挂载中文转拼音类
// 		                include_once LIB_PATH . "CUtf8_PY.class.php";
// 		                $ch2ypClass =new CUtf8_PY();
// 		                //转换后的参数
// 		                if($v["huzhuxingming"]=="")
// 		                {
// 		                    $shouzimu="";
// 		                }
// 		                else
// 		                {
// 		                    $shouzimu=substr( $ch2ypClass->encode($v["huzhuxingming"],"all"), 0, 1 );
// 		                }
		                
// 		                $temp_arr[$k]["shouzimu"]=$shouzimu;
// 		                //生成首字母 end
// 		            }
		            
		            
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
		        
		        
		        $System1 = new SystemModel('System');
		        $date2 = $System1->oneRowCol("dtime", " u1='" . $username. "' and u4='用户登录' order by id desc ");
		        $date2 = $date2['dtime'];
		        $date1 = date('Y-m-d H:i:s', time());
		        $minute = floor((strtotime($date1) - strtotime($date2)) % 86400 );
		        
		        
// 		        echo $date1." date1<br>";
// 		        echo $date2." date2<br>";
// 		        echo $secs."计算秒数   {$minute}<br>";
// 		        die();
		        
		        //小于两秒
		        if ($minute< 2) {
		            $rdata['msg']="您的操作过于频繁，请{$minute}秒后再试";
		            //返回接口数据
		            $this->ajaxReturn($rdata);
		        }
		        
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
		        /*
		         * 走访记录
		         * 
		         * 
		         * 
		         * */
		        
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
		                $temp_arr[$k]["dtime"]=$commonClass->formatTime($v["dtime"]);
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
		        
		    }else if($type=="hujiliebiao")
		    {
		        /*
		         * 户籍列表
		         *
		         *
		         *
		         * */
		        
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
		            //户籍下的人员
		            $renyuan_model= new Model("renyuan");
		            $renyuan= $renyuan_model->select("select * from sl_renyuan where hukoubianhao='{$v["huhao"]}' ");
		            $temp_arr[$k]["renyuan"]=$renyuan;
		            //上次走访时间
		            $zoufangjilu_model= new Model("zoufangjilu");
		            $zoufangjilu= $zoufangjilu_model->select("select * from sl_zoufangjilu where huhao='{$v["huhao"]}' order by dtime desc limit 0 , 1 ");
		            
		            $temp_arr[$k]["zoufangjilu"]=$zoufangjilu;
		            
		            //生成首字母 start
		            
    		            //挂载中文转拼音类
		            include_once LIB_PATH . "CUtf8_PY.class.php";
    		            $ch2ypClass =new CUtf8_PY();
    		            //转换后的参数
    		            if($v["xingming"]=="")
    		            {
    		                $shouzimu="";
    		            }
    		            else 
    		            {
    		                $shouzimu=substr( $ch2ypClass->encode($v["allxingming"],"all"), 0, 1 );
    		            }
    		            
    		            $temp_arr[$k]["shouzimu"]=$shouzimu;
		            //生成首字母 end
		        }
		        
		        //下面两个方法都是输出查询结果
		        //var_dump($temp_model->select($_sql));
		        //$print($temp_model->select($_sql));
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }
		    else if($type=="changqiweizoufang")
		    {
		        /*
		         * 长期未走访列表
		         *
		         *
		         *
		         * */
		        $t="sl_huji";
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
		        
		        if(empty($sqlvalue))
		        {
		            $sqlvalue=" huhao in (select huhao from sl_zoufangjilu )";
		        }else 
		        {
		            $sqlvalue=" and huhao in (select huhao from sl_zoufangjilu )";
		        }
		        //拼接为sql语句
		        $_sql=$this->getSql($t,$liemingcheng,999999,$page,$ordertype,$orderby,$sqlvalue);
		        if($print=="yes")
		        {
		            echo $_sql;
		            die();
		        }
  
		        $huji_model = new Model("huji");
		        $huji=$huji_model->select($_sql);
		        //间隔时间内是否有访问记录，如果没有则返回最近一次的走访记录，如果有则不加入数组
		        $zoufangjilu_model = new Model("zoufangjilu");
		        $canshu_model = new Model("canshu");
		        //长期未走访记录表
		        $data_huji="";
		        foreach ($huji as $k=>$v)
		        {
		           $hujileixing= $v["hujileixing"];
		           if($hujileixing=="")
		               continue;
		            $temp_arr= array( "classid" => "31", "u1" => $hujileixing );
		            $fangwenshijian = $canshu_model->selectByArrayAnd( $temp_arr)[0]["u2"];
		            //$fangwenshijian = $this->NumberFilter($fangwenshijian);
		            $zoufangjilu = $zoufangjilu_model->select("select * from sl_zoufangjilu where huhao='{$v["huhao"]}'   and DATEDIFF(NOW(),dtime)<{$fangwenshijian}  ");
		             //该户籍属于长期未走访的对象
		            if(count($zoufangjilu)==0)
		            {
		                
		                    $temp_zuijinzoufangjilu= $zoufangjilu_model->select("select * from sl_zoufangjilu where huhao='{$v["huhao"]}' order by dtime desc  limit 0,1 ")[0];
		                    if(!empty($temp_zuijinzoufangjilu))
		                    {
		                        
		                        $data_huji_temp["huji"] = $v;
		                        $data_huji_temp["zuijinzoufangjilu"] = $temp_zuijinzoufangjilu;
		                        //组名
		                        $canshu = $canshu_model->selectByPk($temp_zuijinzoufangjilu["suoshuzu"]);
		                        $data_huji_temp["zu"]=$canshu;
		                        //村名
// 		                        $canshu = $canshu_model->selectByPk($v["suoshucun"]);
// 		                        $data_huji[$cur_huji_num]["cun"]=$canshu;
		                        
		                        //长期未走访记录表
		                         
// 		                        $changqiweizoufangModel->select("delete from sl_changqiweizoufang where yonghuming='{$v["yonghuming"]}' ");
// 		                        $temp_data["yonghuming"]=$v["yonghuming"];
// 		                        $temp_data["huhao"]=$v["huhao"];
// 		                        $changqiweizoufangModel->insert($temp_data);
		                        $data_huji[]=$data_huji_temp;
		                    }
		                    
		                
		                
		            }
		        }
		        
		        //返回对应条数
		        foreach ($data_huji as $k=>$v)
		        {
		            $per_pagenum=($page-1)*$number;
		            $next_pagenum=$page*$number;
		            //echo $per_pagenum."  |  ".$next_pagenum." "."<br/>";flush();
		            $cur_huji_num=count($data_huji);
		            //echo $cur_huji_num."<br/>";
		            if($per_pagenum<=$cur_huji_num&&$cur_huji_num<$next_pagenum)
		            {
		                $data_huji1[]=$v;
		            }
		        }
		         
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($data_huji1);
		        
		    }else if($type=="hujil_div")
		    {
		        /*
		         * 户籍列表
		         *
		         *
		         *
		         * */
		        
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
		            //该户籍对应的计划
		            $zoufangjihuaModel = new Model("zoufangjihua");
		            $zoufangjihua = $zoufangjihuaModel->select("select yonghuming,id from sl_zoufangjihua where huhao='{$v["huhao"]}' limit 0,1 ")  ;
		            if(count($zoufangjihua)>0)
		            {
		                $temp_arr[$k]["bl"]="no";
		            }
		            else {
		                $temp_arr[$k]["bl"]="yes";
		            }
		            $temp_arr[$k]["zoufangjihua"]=$zoufangjihua;
		            
		        }
		        
		        //下面两个方法都是输出查询结果
		        //var_dump($temp_model->select($_sql));
		        //$print($temp_model->select($_sql));
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }
		    else if($type=="hujil_div1")
		    {
		        /*
		         * 户籍列表
		         *
		         *
		         *
		         * */
		        
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
		        
		        $userModel = new Model("user");
		        $yonghuming=$_GET["xt_yhm"];
		        $user=$userModel->select("select suoshuzu from sl_user where yonghuming='{$yonghuming}' ");
		        $suoshuzu=$user[0]["suoshuzu"];
		        if(empty($sqlvalue))
		        {
		            $sqlvalue=$sqlvalue."  huhao not in (select huhao from sl_zoufangjihua WHERE suoshuzu='{$suoshuzu}' and  TO_DAYS(NOW()) - TO_DAYS(dtime) > 1   )";
		        }else 
		        {
		            $sqlvalue=$sqlvalue." and huhao not in (select huhao from sl_zoufangjihua WHERE suoshuzu='{$suoshuzu}' and  TO_DAYS(NOW()) - TO_DAYS(dtime) > 1   )";
		            
		        }
		        //拼接为sql语句
		        $_sql=$this->getSql($t,$liemingcheng,$number,$page,$ordertype,$orderby,$sqlvalue);
		        if($print=="yes")
		        {
		            echo $_sql;
		            die();
		        }
		        
		        $temp_model = new Model($t);
		        $temp_arr=$temp_model->select($_sql);
		         
		        
		        //下面两个方法都是输出查询结果
		        //var_dump($temp_model->select($_sql));
		        //$print($temp_model->select($_sql));
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }
		    else if($type=="hujil_div_add_zoufangjihua")
		    {
		        /*
		         * 户籍列表
		         *
		         *
		         *
		         * */
		        
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
		            // 		            //户籍下的人员
		            // 		            $renyuan_model= new Model("renyuan");
		            // 		            $renyuan= $renyuan_model->select("select * from sl_renyuan where hukoubianhao='{$v["huhao"]}' ");
		            // 		            $temp_arr[$k]["renyuan"]=$renyuan;
		            // 		            //上次走访时间
		            // 		            $zoufangjilu_model= new Model("zoufangjilu");
		            // 		            $zoufangjilu= $zoufangjilu_model->select("select * from sl_zoufangjilu where huhao='{$v["huhao"]}' order by dtime desc limit 0 , 1 ");
		            
		            // 		            $temp_arr[$k]["zoufangjilu"]=$zoufangjilu;
		            
		            //该户籍对应的计划
		            $zoufangjihuaModel = new Model("zoufangjihua");
		            $zoufangjihua = $zoufangjihuaModel->select("select yonghuming,id from sl_zoufangjihua where huhao='{$v["huhao"]}' limit 0,1 ")  ;
		            if(count($zoufangjihua)>0)
		            {
		                $temp_arr[$k]["bl"]="no";
		            }
		            else {
		                $temp_arr[$k]["bl"]="yes";
		            }
		            $temp_arr[$k]["zoufangjihua"]=$zoufangjihua;
		            
		        }
		        
		        //下面两个方法都是输出查询结果
		        //var_dump($temp_model->select($_sql));
		        //$print($temp_model->select($_sql));
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }
		    else if($type=="zoufangjihua")
		    {
		        /*
		         * 走访列表
		         *
		         *
		         *
		         * */
		        
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
		            // 		            //户籍下的人员
		            // 		            $renyuan_model= new Model("renyuan");
		            // 		            $renyuan= $renyuan_model->select("select * from sl_renyuan where hukoubianhao='{$v["huhao"]}' ");
		            // 		            $temp_arr[$k]["renyuan"]=$renyuan;
		            // 		            //上次走访时间
		            // 		            $zoufangjilu_model= new Model("zoufangjilu");
		            // 		            $zoufangjilu= $zoufangjilu_model->select("select * from sl_zoufangjilu where huhao='{$v["huhao"]}' order by dtime desc limit 0 , 1 ");
		            
		            // 		            $temp_arr[$k]["zoufangjilu"]=$zoufangjilu;
		            
		            //该户籍对应的计划
		            $zoufangjihuaModel = new Model("zoufangjihua");
		            $zoufangjihua = $zoufangjihuaModel->select("select yonghuming,id from sl_zoufangjihua where huhao='{$v["huhao"]}' limit 0,1 ")  ;
		            if(count($zoufangjihua)>0)
		            {
		                $temp_arr[$k]["bl"]="no";
		            }
		            else {
		                $temp_arr[$k]["bl"]="yes";
		            }
		            $temp_arr[$k]["zoufangjihua"]=$zoufangjihua;
		            
		        }
		        
		        //下面两个方法都是输出查询结果
		        //var_dump($temp_model->select($_sql));
		        //$print($temp_model->select($_sql));
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }
		    else if($type=="liandong")
		    {
		        /*
		         * 多级联动
		         *
		         *
		         *
		         * */
		         
		        //返回条数
		        $id=$this->NumberFilter($_GET["id"]);
		        $canshu_model = new Model("canshu");
		        $canshu1 = $canshu_model->select("select id , u1  from sl_canshu where classid={$id}");
		        //echo "select id , u1  from sl_canshu where classid={$id}";die();
		       foreach ($canshu1 as $k1=>$v1)
		       {
		           $temp_arr[$k1]["value"]=$v1['id'];
		           $temp_arr[$k1]["text"]=$v1['u1'];
		           $canshu2 = $canshu_model->select("select id , u1  from sl_canshu where classid={$v1['id']}");
		           if(count($canshu2)==0)
		           {
		               $temp_arr[$k1]["children"]="[]";
		           }
		           foreach ($canshu2 as $k2=>$v2)
		           {
		               $temp_arr[$k1]["children"][$k2]["value"]=$v2['id'];
		               $temp_arr[$k1]["children"][$k2]["text"]=$v2['u1'];
		               $canshu3 = $canshu_model->select("select id , u1  from sl_canshu where classid={$v2['id']}");
		               if(count($canshu3)==0)
		               {
		                   $temp_arr[$k1]["children"][$k2]["children"]="[]";
		               }
		               foreach ($canshu3 as $k3=>$v3)
		               {
		                   $temp_arr[$k1]["children"][$k2]["children"][$k3]["value"]=$v3['id'];
		                   $temp_arr[$k1]["children"][$k2]["children"][$k3]["text"]=$v3['u1'];
		               }
		           }
		           
		       }
		         
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=json_encode($temp_arr);
		        
		    }
		    else if($type=="renyuanchaxun")
		    {
		        /*
		         * 人员查询
		         *
		         *
		         *
		         * */
		        
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
		        
		        $search_str=$commonClass->SafeFilterStr($_GET["search_str"]);
		        if(!empty($search_str))
		        {
		            $search_str=" (xingming like '%{$search_str}%' or shenfenzhenghao	 like '%{$search_str}%' or shouji	 like '%{$search_str}%' or suozaihuji	 in (select id from sl_huji where allxingming like '%{$search_str}%' )  )";
		            if(empty($sqlvalue))
		            {
		                $sqlvalue=$search_str;
		            }else
		            {
		                $sqlvalue= $sqlvalue." and  ".$search_str;
		            }
		        }
		        
		        
		        
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
		                $temp_arr[$k]["dtime"]=$commonClass->formatTime($v["dtime"]);
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
		    else if($type=="del")
		    {
		        /*
		         * 仅删除走访计划
		         *
		         *
		         * */
		        $zoufangjihua_model = new Model("zoufangjihua");
		        $huhaos=$_REQUEST["huhaos"];
		        if(!empty($huhaos))
		        {
		            $temp_sqlStr="select * from sl_zoufangjihua where ". $zoufangjihua_model->getSqlWhereStr()." and huhao in ({$huhaos})";
		        }else
		        {
		            $temp_sqlStr="select * from sl_zoufangjihua where ". $zoufangjihua_model->getSqlWhereStr();
		        }
		       
		        //echo $temp_sqlStr;
		        $zoufangjihua = $zoufangjihua_model->select($temp_sqlStr);
		        if(count($zoufangjihua)>0)
		        {
		            if($zoufangjihua_model->delete($zoufangjihua[0]["id"]))
		            {
		               //删除成功
		                $rdata['status'] = "true";
		                $rdata['msg']="删除成功";
		            }else 
		            {
		               //删除失败
		                $rdata['status'] = "false";
		                $rdata['msg']="删除失败";
		            }
		        }else 
		        {
		            //计划不存在
		            $rdata['status'] = "false";
		            $rdata['msg']="计划不存在";
		            
		        }
		        
		        
		        
		    }else if($type=="del1")
		    {
		        /*
		         * 仅删除走访计划，民情日记
		         *
		         *
		         * */
		        $tableName=$_GET["t"];
		        if($tableName=="minqingriji" || $tableName=="zoufangjihua")
		        {
		            $temp_model=new Model($tableName);
		            $ids=$_GET["id"];
		            $id_arr = explode(',',$ids);
		            if(count($id_arr)==0)
		            {
		                $temp_model->delete($ids);
		            }else
		            {
		                foreach ($id_arr as $k => $v) {
		                    $temp_model->delete($v);
		                }
		            }
		            
		            $rdata['status'] = "true";
		            $rdata['msg']="删除成功";
		            
		        }else
		        {
		            $rdata['status'] = "false";
		            $rdata['msg']="删除失败:{$tableName} 没有删除权限";
		            
		        }
		        
		        
		        
		        
		    }
		    else if($type=="chaxunyuangong")
		    {
		        // 1.获取用户名和密码
		        $username = trim($_REQUEST['yonghuming']);
		        $password = trim($_REQUEST['mima']);
		        // 对用户名和密码进行转义
		        $username = addslashes($username);
		        $password = addslashes($password);
		        $password=md5($password);
		         
		        
		        // 3.调用模型来完成验证操作并给出提示
		        $userModel = new Model('user');
		        $canshu_model = new Model("canshu"); 
		        $user = $userModel->select("select * from {$t} where yonghuming='$username' and mima='$password'  ");
		        if (count($user)>0) {
		            $user[0]["suoshucun"]=$canshu_model->selectByPk($user[0]["suoshucun"])["u1"];
		            $user[0]["suoshuzu"]=$canshu_model->selectByPk($user[0]["suoshuzu"])["u1"];
		             
		        }  
		        
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=$user;
		        
		    }
		    
		    else if($type=="shenfenzhengshibie")
		    {
		        $img_url=$_REQUEST["img_url"];
		        //身份证识别接口
		        $token = $this->access_token("fHez0lEMXLnXAClEjizHfm77", "MCLYSfuU1jVW4ZvAvsNjH23sUR8Tex26");
		        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/idcard?access_token=' . $token;
		        $img = file_get_contents($img_url);
		        $img = base64_encode($img);
		        $bodys = array(
		            "image" => $img,
		            "id_card_side"=>"front"
		        );
		        $res =$this->request_post($url, $bodys);
		          
		        if($print=="yes")
		        {
		            var_dump(json_decode($res,true));
		            die();
		        }
		        
		        
		        $res_arr =json_decode($res,true);
		        
		        $data_sfz["shenfenzhengdizhi"]=$res_arr['words_result']['住址']['words'];
		        $data_sfz["xingming"]=$res_arr['words_result']['姓名']['words'];
		        $data_sfz["shenfenzhenghao"]=$res_arr['words_result']['公民身份号码']['words'];
		        $data_sfz["xingbie"]=$res_arr['words_result']['性别']['words'];
		        $data_sfz["minzu"]=$res_arr['words_result']['民族']['words']."族";
		        $data_sfz["chushengriqi"]=date('Y-m-d',strtotime($res_arr['words_result']['出生']['words']));
		        
		        $rdata['status'] = "true";
		        $rdata['msg']=$data_sfz;
		        
		    }else if($type=="随便自己命名的方法")
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
	
	
	//生成word对于的html
	public function daochuAction(){
	    $model_id = $_REQUEST['model_id'];
	    // 获取wenzhang_id
	    $id = $_GET['id'] ;
	    //得到字段模型
	    $filedModel=new Model("filed");
	    $canshuModel= new Model("canshu");
	    $yuangongModel = new Model("user");
	    $hujiModel = new Model("huji");
	    $renyuanModel = new Model("renyuan");
	    
	    $filedLists=$filedModel->select("select * from sl_filed where model_id='{$model_id}'  order by  u10 asc,id desc ");//显示查询字段
	    
	    // 获得当前表名
	    $moxingModel = new MoxingModel("moxing");
	    $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
	    //先获取文章信息
	    $tableModel = new Model($tableName);
	    $wenzhang = $tableModel->selectByPk($id);
	    //村详情
	    $cun = $canshuModel->selectByPk($wenzhang["suoshucun"]);
	    //员工详情
	    // $yuangong = $yuangongModel->selectByCol("yonghuming", $wenzhang["ruhurenyuan"]);
	    //户籍详情
	    $huji = $hujiModel->selectByPk($wenzhang["huhao"]);
	    //帮教对象亲属
	    $renyuan = $renyuanModel->selectByCol("hukoubianhao", $wenzhang["huhao"]);
	    foreach ($renyuan as $k => $v) {
	        if(empty($qinshuNames))
	        {
	            $qinshuNames=$v["xingming"];
	        }
	        else
	        {
	            $qinshuNames.=",".$v["xingming"];
	            
	        }
	    }
	    //走访图片
	    $zutu=str_replace("{next}","|",$wenzhang["zutu"]);
	    $zutuArrTemp= explode("|",$zutu);
	    //主机地址
	    //$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
	    $host="222.82.243.27:8282";
	    foreach ($zutuArrTemp as $zt) {
	        $temp_arr=explode("|",str_replace("{title}","|",$zt)) ;
	        $zutuArr[]="http://".$host.DS.$temp_arr[0];
	    }
	    //echo $cun["u1"];die();
	    include CUR_VIEW_PATH . "Szoufangjilu" . DS . "zoufangjilu_daochu.html";
	    
	}
	
	
	//生成并下载word
	public function xiazaiAction(){
	    $this->helper("ToWord");
	    //$host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
	    $host="222.82.243.27:8282";
	    
	    //创建文件夹
	    $dir = iconv("UTF-8", "GBK", "public/word/".$_SESSION['admin']['username']."/".time()."/");
	    if (!file_exists($dir)){
	        mkdir ($dir,0777,true);
	    }
	    
	    $ids = $_REQUEST['id'];
	    $array_id=explode(",", $ids);
	    $array_id=array_unique($array_id);
	    for ($i = 0; $i < count($array_id); $i++) {
	        $url="http://".$host.DS."index.php?p=admin&c=zoufangjilu&a=daochu&model_id=66&id=".$array_id[$i];
	        $html= file_get_contents($url);
	        $ToWord= new ToWord();
	        $ToWord->start();
	        $wordname = $dir.'入户帮教登记表'.$array_id[$i].".doc";
	        echo $html;
	        $ToWord->save($wordname);
	        //ob_flush();//每次执行前刷新缓存
	        flush();
	    }
	    
	    //打包并下载
	    $filename="入户帮教登记表".time().".zip";
	    $temp_filename="public/word/".$_SESSION['admin']['username']."/".$filename;
	    $z = new ZipArchive();
	    $z->open($temp_filename, ZIPARCHIVE::CREATE);
	    $this->addFileToZip($dir, $z);
	    $z->close();
	    
	    //设置打包完自动下载
	    echo $filename;
	    // 	    header("Cache-Control: public");
	    // 	    header("Content-Description: File Transfer");
	    // 	    header('Content-disposition: attachment; filename='.basename($temp_filename)); //文件名
	    // 	    header("Content-Type: application/zip"); //zip格式的
	    // 	    header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
	    // 	    header('Content-Length: '. filesize($temp_filename)); //告诉浏览器，文件大小
	    // 	    @readfile($temp_filename);
	    
	    
	    
	}
	
	function addFileToZip($path,$zip){
	    $handler=opendir($path); //打开当前文件夹由$path指定。
	    while(($filename=readdir($handler))!==false){
	        if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
	            if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
	                addFileToZip($path."/".$filename, $zip);
	            }else{ //将文件加入zip对象
	                $zip->addFile($path."/".$filename);
	            }
	        }
	    }
	    @closedir($path);
	}
	
	
	 
	
	/**
	 * 发起http post请求(REST API), 并获取REST请求的结果
	 * @param string $url
	 * @param string $param
	 * @return - http response body if succeeds, else false.
	 */
	public function request_post($url = '', $param = '')
	{
	    if (empty($url) || empty($param)) {
	        return false;
	    }
	    
	    $postUrl = $url;
	    $curlPost = $param;
	    // 初始化curl
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $postUrl);
	    curl_setopt($curl, CURLOPT_HEADER, 0);
	    // 要求结果为字符串且输出到屏幕上
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    // post提交方式
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
	    // 运行curl
	    $data = curl_exec($curl);
	    curl_close($curl);
	    
	    return $data;
	}
	
	
	/**
	 * 获取API访问授权码
	 * @param ak: ak    fHez0lEMXLnXAClEjizHfm77
	 * @param sk: sk    MCLYSfuU1jVW4ZvAvsNjH23sUR8Tex26 
	 * @return - access_token string if succeeds, else false.
	 */
   function access_token($ak, $sk) {
	    $url = 'https://aip.baidubce.com/oauth/2.0/token';
	    
	    $post_data = array();
	    $post_data['grant_type']  = 'client_credentials';
	    $post_data['client_id']   = $ak;
	    $post_data['client_secret'] = $sk;
	    
	    $res =$this->request_post($url, $post_data);
	    if (!!$res) {
	        $res = json_decode($res, true);
	        return $res['access_token'];
	    } else {
	        return false;
	    }
	}
	
	
	
		
}