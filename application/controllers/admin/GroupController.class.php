<?php
//用户组控制器
class GroupController extends BaseController{
	
	//显示用户组列表
	public function indexAction(){
		//先获取用户组信息
		$groupModel = new Model("group");
		// $groups = $groupModel->getgroups();
		//载入分页类
		$where = ""; //查询条件
		$this->library("Page");
		//获取group总的记录数
		$total = $groupModel->total($where);
		//指定分页数，每一页显示的记录数
		$pagesize = 2;
		// $pagesize = $GLOBALS['config']['pagesize'];
		//获取当前页数，默认是1
		$current = isset($_GET['page']) ? $_GET['page'] : 1;
		$offset = ( $current - 1 ) * $pagesize;
		//使用模型完成数据的查询
		$groups= $groupModel->pageRows($offset, $pagesize, $where);
		foreach ($groups as $k=>$v)
		{
		    $adminModel = new Model("admin");
		    $groups[$k]["memberNum"]=$adminModel->select("select count(*) as count_id from sl_admin where group_id={$v[id]} ")[0][count_id];
		}
		//使用分页类获取分页信息
		$page = new Page($total,$pagesize,$current,"index.php",array("p"=>"admin","c"=>"group","a"=>"index"));
		$pageinfo = $page->showPage();
		include CUR_VIEW_PATH . "Sgroup".DS."group_list.html";
	}

	//载入添加用户组页面
	public function addAction(){
		include CUR_VIEW_PATH . "Sgroup".DS."group_add.html";
	}

	//载入编辑用户组页面
	public function editAction(){
		//获取该用户组信息
		$groupModel = new groupModel("group");
		//条件
		$group_id = $_GET['group_id'] + 0; //出于考虑
		//使用模型获取
		$group = $groupModel->selectByPk($group_id);
		// var_dump($group);
		include CUR_VIEW_PATH . "group_edit.html";
	}

	//定义insert方法，完成用户组的插入
	public function insertAction(){
		//接受表单提交过来的数据
		$data['group_name'] = trim($_POST['group_name']);
		$data['url'] = trim($_POST['url']);
		$data['group_desc'] = trim($_POST['group_desc']);
		$data['sort_order'] = trim($_POST['sort_order']);
		$data['is_show'] = trim($_POST['is_show']);
		//对提交过来的数据需要做一些验证、过滤等一些处理(此处忽略)
		$this->helper("input");
		$data = deepspecialchars($data); //实体转义处理
		//处理文件上传,需要使用到Upload.class.php
		$this->library("Upload"); //载入文件上传类
		$upload = new Upload(); //实例化上传对象
		if ($filename = $upload->up($_FILES["logo"])){
			//成功
			$data['logo'] = $filename;
			//调用模型完成入库操作，并给出相应的提示
			$groupModel = new groupModel("group");
			if ($groupModel->insert($data)){//添加成功	
				$this->jump("index.php?p=admin&c=group&a=index","添加商品用户组成功",2);
			}else {//添加失败			
				$this->jump("index.php?p=admin&c=group&a=add","添加商品用户组失败",3);
			}
		}else{//失败
			$this->jump("index.php?p=admin&c=group&a=add",$upload->error(),3);
		}

		
	}

	//定义update方法，完成用户组的更新
	public function updateAction(){
		//获取条件及数据
		$data['group_id'] = $_POST['group_id'];
		$data['group_name'] = trim($_POST['group_name']);
		$data['group_desc'] = trim($_POST['group_desc']);
		$data['sort_order'] = trim($_POST['sort_order']);
		$data['url'] = trim($_POST['url']);
		$data['is_show'] = $_POST['is_show'];

		//图片怎么办，需要判断是否有上传，如何判断
		if($_FILES['logo']['name']){
			//有上传,完成上传操作
		}
		
		//调用模型完成更新
		$groupModel = new groupModel("group");
		if($groupModel->update($data)){
			$this->jump("index.php?p=admin&c=group&a=index","更新成功",2);
		}else{
			$this->jump("index.php?p=admin&c=group&a=edit&group_id=".$data['group_id'],"更新失败",2);
		}
	}

	//定义delete方法，完成用户组的删除
	public function deleteAction(){
		//获取group_id
		$group_id = $_GET['group_id'] + 0;
		$groupModel = new groupModel("group");
		$group = $groupModel->selectByPk($group_id);
		//得到图片的全路径
		$img = UPLOAD_PATH . $group['logo'];
		if ($groupModel->delete($group_id)){
			//成功的同时删除对应的图片
			@unlink($img);
			$this->jump("index.php?p=admin&c=group&a=index","删除成功",2);
		}else{
			$this->jump("index.php?p=admin&c=group&a=index","删除失败",3);
		}
	}
}