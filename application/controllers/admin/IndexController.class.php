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
	    $sortModel=new Model("sort");
	    $tableModel= new Model("article");
	    $systemMode=new Model("system");
	    $feedbookMode=new Model("feedbook");
	    //统计分类  SELECT COUNT(*) as count_id from (SELECT  u3,dtime from sl_system where u1='访客' and u4='访客记录'  GROUP BY u3 )as t   
	    $sort=$sortModel->select("select * from sl_sort where id in ( SELECT sort_id from sl_article where DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(dtime) GROUP BY sort_id) order by id desc ");
	    //发布时间
	    $dtime=$tableModel->select("SELECT distinct  date_format(dtime,'%y-%m-%d') as dtime  from sl_article where DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= date(dtime)  ORDER BY dtime desc");

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
}