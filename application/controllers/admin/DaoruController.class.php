<?php
//导入数据控制器
class DaoruController extends BaseController{
	
	 
	//载入添加导入数据页面
	public function addAction(){
	    
	    include CUR_VIEW_PATH ."Sdaoru".DS. "daoru_add.html";
	}

	 

	//定义insert方法，完成导入数据的插入
	public function insertAction(){
	    require_once(LIB_PATH."PHPExcel-1.8".DS."Classes".DS."PHPExcel.php");
	    $filename="";
	    //上传文件到服务器
	    $this->library("Upload"); //载入文件上传类
	    $upload = new Upload(); //实例化上传对象
	    if ($filename = $upload->up($_FILES["wenjian"])){
	        //成功
	        //echo "上传文件成功";
	        //flush();
	         
	    }
	    //echo $filename;die();
	    
// 	    $objPHPExcelReader = PHPExcel_IOFactory::load($filename);  //加载excel文件
	    
// 	    foreach($objPHPExcelReader->getWorksheetIterator() as $sheet)  //循环读取sheet
// 	    {
// 	        foreach($sheet->getRowIterator() as $row)  //逐行处理
// 	        {
// 	            if($row->getRowIndex()<2)  //确定从哪一行开始读取
// 	            {
// 	                continue;
// 	            }
// 	            foreach($row->getCellIterator() as $cell)  //逐列读取
// 	            {
// 	                //var_dump($cell);die();
// 	                $data = $cell->getValue(); //获取cell中数据
// 	                echo $data;
// 	            }
// 	            echo '<br/>';
// 	        }
// 	    }

        //文件名为文件路径和文件名的拼接字符串
	    //$objReader = \PHPExcel_IOFactory::createReader('Excel5');//创建读取实例
	    $objPHPExcel = PHPExcel_IOFactory::load($filename);//加载文件
	    $sheet = $objPHPExcel->getSheet(0);//取得sheet(0)表
	    $highestRow = $sheet->getHighestRow(); // 取得总行数
	    $highestColumn = $sheet->getHighestColumn(); // 取得总列数
	    
	    //如果是村管理员1 
	    $adminModel = new AdminModel('admin');
	    $user = $adminModel->selectByPk($_SESSION['admin']['user_id']);
	    //村ID
	    $cun_id =  $user["cun_id"];
	    
	    $userModel = new Model("user");
	    for($i=2;$i<=$highestRow;$i++)
	    {
	        $data['yonghuming']=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
	        $data['xingming'] = $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
	        $data['mima'] =md5(substr($data['yonghuming'],5,6));
	        $data['suoshucun']=$cun_id;
	        $data['suoshuzu']=$cun_id;
	        $data['daorurenxinxi']="姓名:".$user["nickname"]." 用户名:".$user["username"];
	        
	        if(count($userModel->select("select * from sl_user where yonghuming='{$data['yonghuming']}' "))>0)
	        {
	            //用户名已存在
	            echo $data['yonghuming']."导入失败：用户名已存在<br/>";
	            continue;
	        }else 
	        {
	            $userModel->insert($data);
	            echo $data['yonghuming']."导入成功<br/>";
	            flush();
	        }
	        
	    }
	    
	    
// 		//接受表单提交过来的数据
// 		$data['daoru_name'] = trim($_POST['daoru_name']);
// 		$data['url'] = trim($_POST['url']);
// 		$data['daoru_desc'] = trim($_POST['daoru_desc']);
// 		$data['sort_order'] = trim($_POST['sort_order']);
// 		$data['is_show'] = trim($_POST['is_show']);
// 		//对提交过来的数据需要做一些验证、过滤等一些处理(此处忽略)
// 		$this->helper("input");
// 		$data = deepspecialchars($data); //实体转义处理
// 		//处理文件上传,需要使用到Upload.class.php
// 		$this->library("Upload"); //载入文件上传类
// 		$upload = new Upload(); //实例化上传对象
// 		if ($filename = $upload->up($_FILES["logo"])){
// 			//成功
// 			$data['logo'] = $filename;
// 			//调用模型完成入库操作，并给出相应的提示
// 			$daoruModel = new daoruModel("daoru");
// 			if ($daoruModel->insert($data)){//添加成功	
// 				$this->jump("index.php?p=admin&c=daoru&a=index","添加商品导入数据成功",2);
// 			}else {//添加失败			
// 				$this->jump("index.php?p=admin&c=daoru&a=add","添加商品导入数据失败",3);
// 			}
// 		}else{//失败
// 			$this->jump("index.php?p=admin&c=daoru&a=add",$upload->error(),3);
// 		}

		
	}
 
}