<?php
//自动表控制器
class ZoufangjiluController extends BaseController{
	
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
	        "c" => "zoufangjilu",
	        "a" => "index",
	        "model_id" => "{$model_id}",
	        //"sort_id" => "{$sort_id}"
	    ));
	    $pageinfo = $page->showPage();
	    
		include CUR_VIEW_PATH ."Szoufangjilu".DS. "zoufangjilu_list.html";
	}

	//载入添加自动表页面
	public function addAction(){
	    
	    $model_id = $_REQUEST['model_id'];
	    //得到字段模型
	    $filedModel=new Model("filed");
	    $filedLists=$filedModel->select("select * from sl_filed where model_id='{$model_id}'  order by  u10 asc,id desc ");//显示查询字段
		
	    
	    include CUR_VIEW_PATH . "Szoufangjilu" . DS ."zoufangjilu_add.html";
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
	    include CUR_VIEW_PATH . "Szoufangjilu" . DS . "zoufangjilu_edit.html";
	    
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
	        $this->jump('index.php?p=admin&c=zoufangjilu&a=index&model_id='.$model_id,'添加成功',2);
	    } else {
	        $this->jump('index.php?p=admin&c=zoufangjilu&a=add&model_id='.$model_id,'添加失败');
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
	        $this->jump('index.php?p=admin&c=zoufangjilu&a=index&model_id='.$model_id,"更新成功",2);
	    }else{
	        $this->jump('index.php?p=admin&c=zoufangjilu&a=edit&id='.$data['id'].'&model_id='.$model_id,"更新失败",2);
	    }
	}

	//定义delete方法，完成自动表的删除
	public function deleteAction(){
	    $model_id = $_REQUEST['model_id'];
	    // 获取wenzhang_id
	    if($_REQUEST['id']=='')
	    {
	        $this->jump('index.php?p=admin&c=zoufangjilu&a=index&model_id='.$model_id,"删除失败，参数不能为空",3);
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
	        $this->jump('index.php?p=admin&c=zoufangjilu&a=index&model_id='.$model_id, "删除成功", 2);
	    } else {
	        $this->jump('index.php?p=admin&c=zoufangjilu&a=index&model_id='.$model_id, "删除失败", 3);
	    }
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
	    $host = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
	    //$host="222.82.243.27:8282";
	    //创建文件夹
	    //$dir = iconv("UTF-8", "GBK", "public/word/".$_SESSION['admin']['username']."/".time()."/");
	    $dir = iconv("UTF-8", "GBK", time()."/");
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
// 	    echo $temp_filename;
	    echo '<script language="javascript" type="text/javascript"> 
window.location.href="'.$temp_filename.'"; 
</script> ';
	    
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
	                //addFileToZip($path."/".$filename, $zip);
	            }else{ //将文件加入zip对象
	                $zip->addFile($path."/".$filename);
	            }
	        }
	    }
	    @closedir($path);
	}
	
}