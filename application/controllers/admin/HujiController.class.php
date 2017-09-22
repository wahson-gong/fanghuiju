<?php
//自动表控制器
class HujiController extends BaseController{
	
	//显示自动表列表
	public function indexAction(){
	    
	    $model_id = $_REQUEST['model_id'];
	    //$sort_id = $_REQUEST['sort_id'];
	    $u6 = $_REQUEST['u6'];
	    $where = " 1=1 ";
	    // 获得当前表名
	    $moxingModel = new MoxingModel("moxing");
	    $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
	    //先获取文章信息
	    $tableModel = new Model($tableName);
	    //查询条件
	    if(trim(str_replace("1=1", " ", $tableModel->getSqlWhereStr()))!="")
	    {
	        $where .= " and ".$tableModel->getSqlWhereStr();
	    }
	    
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
	    
	    //echo $_SESSION['admin']['zuming'];die();
	     
	    //得到字段模型
	    $filedModel=new FiledModel("filed");
	    if(trim($u6)!='')
	    {
	        $filedListU6=$filedModel->select("select * from sl_filed where model_id='{$model_id}' and u6='是' ");//模糊查询字段
	        if(count($filedListU6)>0)
	        {
	            $where=$where." and (";
	            foreach ($filedListU6 as $v)
	            {
	                $where=$where."  {$v['u1']} like '%{$u6}%' or ";
	            }
	            $where=$where." 1=2 )";
	        }
	        
	    }
	    
	    
	    
	    //需要显示的字段
	    $filedLists=$filedModel->select("select * from sl_filed where model_id='{$model_id}' and u5='是' order by u10 asc ");//显示查询字段
	    
	   
	    // 载入分页类
	    include LIB_PATH . "Page.class.php";
	    // 获取wenzhang总的记录数
	    $total = $tableModel->total($where);
	    // 指定分页数，每一页显示的记录数
	    $pagesize = 10;
	    // $pagesize = $GLOBALS['config']['pagesize'];
	    // 获取当前页数，默认是1
	    $current = isset($_GET['page']) ? $_GET['page'] : 1;
	    $offset = ($current - 1) * $pagesize;
	    // 使用模型完成数据的查询
	    $tableModel = $tableModel->pageRows($offset, $pagesize, $where);
	    // 使用分页类获取分页信息
	    $page = new Page($total, $pagesize, $current, "index.php", array(
	        "p" => "admin",
	        "c" => "huji",
	        "a" => "index",
	        "model_id" => "{$model_id}",
	        //"sort_id" => "{$sort_id}"
	    ));
	    $pageinfo = $page->showPage();
	    
		include CUR_VIEW_PATH ."Shuji".DS. "huji_list.html";
	}

	//载入添加自动表页面
	public function addAction(){
	    
	    $model_id = $_REQUEST['model_id'];
	    //得到字段模型
	    $filedModel=new Model("filed");
	    $filedLists=$filedModel->select("select * from sl_filed where model_id='{$model_id}'  order by  u10 asc,id desc ");//显示查询字段
		
	    
	    include CUR_VIEW_PATH . "Shuji" . DS ."huji_add.html";
	}

	//载入编辑自动表页面
	public function editAction(){
	    $model_id = $_REQUEST['model_id'];
	    // 获取wenzhang_id
	    $wenzhang_id = $_GET['id'] ;
	    //得到字段模型
	    $filedModel=new Model("filed");
	    $filedLists=$filedModel->select("select * from sl_filed where model_id='{$model_id}'  order by  u10 asc,id desc ");//显示查询字段
	    
	    // 获得当前表名
	    $moxingModel = new MoxingModel("moxing");
	    $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
	    //先获取文章信息
	    $tableModel = new Model($tableName);
	    
	    $wenzhang = $tableModel->selectByPk($wenzhang_id);
	    include CUR_VIEW_PATH . "Shuji" . DS . "huji_edit.html";
	    
	}

	//定义insert方法，完成自动表的插入
	public function insertAction(){
	    $model_id = $_REQUEST['model_id'];
	    // 获得当前表名
	    $moxingModel = new MoxingModel("moxing");
	    $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
	    //先获取文章信息
	    $tableModel = new Model($tableName);
	    
	    //1.收集表单数据
	    $data=$tableModel->getFieldArray();
	    
	    //2.验证和处理
	    
	    $this->helper('input');
	    $data = deepspecialchars($data);
	    //$data = deepslashes($data);
	    
	    
	    //如果字段设置为当前时间
	    $filedModel=new Model("filed");
	    $filedAraay=$filedModel->select("select u1 from sl_filed where model_id='{$model_id}' ");
	    foreach ($filedAraay as $v)
	    {
	        $filedList=$filedModel->select("select * from sl_filed where  u7='时间框' and u8='CURRENT_TIMESTAMP' and model_id='{$model_id}' and u1='{$v['u1']}' ");
	        if(count($filedList)>0)
	        {
	            $data[$v['u1']]= date('Y-m-d H:i:s',time());
	        }
	    }
	    //如果字段设置为文件和图片
	    foreach ($filedAraay as $v)
	    {
	        $filedList=$filedModel->select("select * from sl_filed where  u7='图片'  and model_id='{$model_id}' and u1='{$v['u1']}' ");
	        //判断是否为图片，请图片参数不为空
	        if(count($filedList)>0 )
	        {
	            //echo "select * from sl_filed where  u7='图片'  and model_id='{$model_id}' and u1='{$v['u1']}'  <br>";
	            //$data[$v['u1']]= date('Y-m-d H:i:s',time());
	            //处理文件上传,需要使用到Upload.class.php
	            $this->library("Upload"); //载入文件上传类
	            $upload = new Upload(); //实例化上传对象
	            if ($filename = $upload->up($_FILES[$v['u1']])){
	                //成功
	                $data[$v['u1']] = $filename;
	                //调用模型完成入库操作，并给出相应的提示
	                 
	            }else {
	                //print_r($_FILES[$v['u1']]);
	                //$this->jump('index.php?p=admin&c=wenzhang&a=add&model_id='.$model_id."&sort_id=".$sort_id.$data['sort_id'],$upload->error(),3);
	            }
	    
	        }
	    }
	    
	    
	    //单独处理密码
	    foreach ($filedAraay as $v)
	    {
	        $filedList=$filedModel->select("select * from sl_filed where  u7='密码' and model_id='{$model_id}' and u1='{$v['u1']}' ");
	        if(count($filedList)>0)
	        {
	            $data[$v['u1']]=md5($data[$v['u1']]);
	        }
	    }
	     
	    //3调用模型完成入库并给出提示
	    if ($tableModel->insert($data)) {
	        $this->jump('index.php?p=admin&c=huji&a=index&model_id='.$model_id,'添加成功',2);
	    } else {
	        $this->jump('index.php?p=admin&c=huji&a=add&model_id='.$model_id,'添加失败');
	    }
	     
		
	}

	//定义update方法，完成自动表的更新
	public function updateAction(){
	    $model_id = $_REQUEST['model_id'];
	    
	    // 获得当前表名
	    $moxingModel = new MoxingModel("moxing");
	    $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
	    //先获取文章信息
	    $tableModel = new Model($tableName);
	    
	    //1.收集表单数据
	    $data=$tableModel->getFieldArray();
	    
	    //2.验证和处理
	    
	    $this->helper('input');
	    $data = deepspecialchars($data);
	    //如果字段设置为文件和图片
	    $filedModel=new Model("filed");
	    $filedAraay=$filedModel->select("select u1 from sl_filed where model_id='{$model_id}' ");
	    foreach ($filedAraay as $v)
	    {
	        $filedList=$filedModel->select("select * from sl_filed where  u7='图片'  and model_id='{$model_id}' and u1='{$v['u1']}' ");
	        //判断是否为图片，请图片参数不为空
	        if(count($filedList)>0 )
	        {
	            //echo "select * from sl_filed where  u7='图片'  and model_id='{$model_id}' and u1='{$v['u1']}'  <br>";
	            //$data[$v['u1']]= date('Y-m-d H:i:s',time());
	            //处理文件上传,需要使用到Upload.class.php
	            $this->library("Upload"); //载入文件上传类
	            $upload = new Upload(); //实例化上传对象
	            if ($filename = $upload->up($_FILES[$v['u1']])){
	                //成功
	                $data[$v['u1']] = $filename;
	                //调用模型完成入库操作，并给出相应的提示
	                 
	            }else {
	                //print_r($_FILES[$v['u1']]);
	                //$this->jump('index.php?p=admin&c=wenzhang&a=add&model_id='.$model_id."&sort_id=".$sort_id.$data['sort_id'],$upload->error(),3);
	            }
	    
	        }
	    }
	    //调用模型完成更新
	    //var_dump($data);die();
	    if($tableModel->update($data)){
	        $this->jump('index.php?p=admin&c=huji&a=index&model_id='.$model_id,"更新成功",2);
	    }else{
	        $this->jump('index.php?p=admin&c=huji&a=edit&id='.$data['id'].'&model_id='.$model_id,"更新失败",2);
	    }
	}

	//定义delete方法，完成自动表的删除
	public function deleteAction(){
	    $model_id = $_REQUEST['model_id'];
	    // 获取wenzhang_id
	    if($_REQUEST['id']=='')
	    {
	        $this->jump('index.php?p=admin&c=huji&a=index&model_id='.$model_id,"删除失败，参数不能为空",3);
	    }
	    $sys_id = $_REQUEST['id'];
	    $array_id=explode(",", $sys_id);
	    $array_id=array_unique($array_id);
	    
	    // 获得当前表名
	    $moxingModel = new MoxingModel("moxing");
	    $tableName = $moxingModel->oneRowCol("u1", "id={$model_id}")['u1'];
	    //先获取文章信息
	    $tableModel = new Model($tableName);
	    
	    if ($tableModel->delete($array_id)!="false") {
	        $this->jump('index.php?p=admin&c=huji&a=index&model_id='.$model_id, "删除成功", 2);
	    } else {
	        $this->jump('index.php?p=admin&c=huji&a=index&model_id='.$model_id, "删除失败", 3);
	    }
	}
}