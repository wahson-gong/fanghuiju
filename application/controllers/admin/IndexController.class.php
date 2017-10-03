<?php
//后台首页控制器
class IndexController extends BaseController {
	//生成验证码
	public function codeAction(){
		//载入验证码类
		$this->library('Captcha');
		$c = new Captcha();
		$c->generateCode();
	}
	public function indexAction(){
	    
	    //如果是村管理员1
	    if($_SESSION['admin']['zuming']=="村管理员")
	    {
	        $adminModel = new AdminModel('admin');
	        $user = $adminModel->selectByPk($_SESSION['admin']['user_id']);
	        //村ID
	        $cun_id =  $user["cun_id"];
	        //只查询本村
	        if(!empty($cun_id))
	        {
	            $where .= " and suoshucun={$cun_id} ";
	        }
	    }
	    
	   
	    $cunModel=new Model("canshu");
	    $zoufangjiluModel= new Model("zoufangjilu");
	    
	    $tableModel=new Model("article");
	    $systemMode=new Model("system");
	    $feedbookMode=new Model("feedbook");
	    //村分类  SELECT COUNT(*) as count_id from (SELECT  u3,dtime from sl_system where u1='访客' and u4='访客记录'  GROUP BY u3 )as t   
	    $suoshucun=$cunModel->select("select * from sl_canshu where id in ( SELECT suoshucun from sl_zoufangjilu where DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(dtime) ".$where." GROUP BY suoshucun) order by id desc ");
	    //走访时间
	    $dtime=$zoufangjiluModel->select("SELECT distinct  date_format(dtime,'%y-%m-%d') as dtime  from 	sl_zoufangjilu where DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(dtime) ".$where." ORDER BY dtime desc");
	    
	    include CUR_VIEW_PATH . "index.html";
	}
	public function topAction(){
		include CUR_VIEW_PATH . "top.html";
	}
	public function menuAction(){
		include CUR_VIEW_PATH . "menu.html";
	}
	public function dragAction(){
		include CUR_VIEW_PATH . "drag.html";
	}
	public function mainAction(){
		//实例化模型
		$adminModel = new AdminModel('admin');
		$admins = $adminModel->getAdmins();
		// echo "<pre>";
		// var_dump($admins);
		include CUR_VIEW_PATH . "main.html";
	}
	
	//ajax接口
	public function apiAction(){
	    $type=$_GET["type"];
	    header('Content-Type:application/json; charset=utf-8');
	    $temp_str='';
	    if($type=="getLiandongHtml")
	    {
	        $classid=$_GET["classid"];
	        //filedName
	        $filedName=$_GET["filedName"];
	        if($classid!="")
	        {
	            $temp_str=$this->$type($classid,$filedName);
	            echo $temp_str;
	        }
	        else 
	        {
	            echo "";
	        }
	        
	    }
	    
	}
}