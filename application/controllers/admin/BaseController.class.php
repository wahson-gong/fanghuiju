<?php
//后台基础控制器
class BaseController extends Controller {
	//构造方法
	public function __construct(){
		$this->checkLogin();
	}
	
	//验证用户是否登录
	public function checkLogin(){
		//注意，此处的admin是我在登录成功时保存的登录标识符
		if (!isset($_SESSION['admin'])) {
			$this->jump('index.php?p=admin&c=login&a=login','你还没有登录呢');
		}
	}
	
	/*
	 * 加载自定义控件方法
	 * $kjName        $v['u2']
	 * $filedName     $v['u1']
	 * $selectValue   $wenzhang[$v['u1']]
	 * $tipString     $v['u3']
	 * $filedId
	 * */
	public function getKj($type,$kjName,$filedName,$selectValue,$tipString,$filedId){
	    if($type=="文本域")
	    {
	        return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>
				    			<textarea name="'.$filedName.'" class="input" style="height: 127px; margin: 0px; width: 100%;">'.$selectValue.'</textarea><i>'.$tipString.'</i>
				    		    </td>
				    	     </tr>';
	        
	    }else if($type=="图片")
	    {
	        return ' <tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
				    		    <td>
	                            <input type = "file" name = "'.$filedName.'" class="input"  />
                                <img src="'.$selectValue.'" style="max-width: 200px;max-height: 100px;overflow: hidden;" onerror="javascript:this.src=\'application/views/admin/images/nopic.jpg\';" />
                                <i>'.$tipString.'</i>
				    		    </td>
				    	     </tr>';
	        
	    }else if($type=="单选")
	    {
             $filedModel1=new FiledModel("filed");
             $filedVal=$filedModel1->getFiledDefaultValue($filedId);
             $temp_radio_html=' ';
             $i=1; 
             foreach($filedVal as $key=>$val)
             {
                 if($selectValue==trim($val) ) 
                 { 
                     $temp_radio_html.='<input id="'.$filedName.'" type="radio" name="'.$filedName.'" checked="checked" value="'.$val.'">'.$val;
                 }else{ 
                     $temp_radio_html.='<input id="'.$filedName.'" type="radio" name="'.$filedName.'"  value="'.$val.'">'.$val;
                  }
               $i++;
              }
             
	        return '<tr style="display: table-row;">
				    		    <th>'.$kjName.'</th>
								<td>
								
								<!-- 循环默认值 -->
								'.$temp_radio_html.'
								</td>

							</tr>  ';
	        
	        
	    }else if($type=="文本编辑器")
	    {
	        return ' <div class="bjq">
                       <p class="title" style="font-size: 15px;font-weight: bold;color: #555;"><em class="txt-blue ficon ficon-deng"></em> '.$kjName.'</em></p>
                          <script id="'.$filedName.'" name="'.$filedName.'" type="text/plain">'.html_entity_decode($selectValue).'</script>
                          <script type="text/javascript">
                            var ue_'.$filedName.' = UE.getEditor("'.$filedName.'");
                          </script>
                      </div>';
	        
	    }else if($type=="时间框")
	    {
	        return '  <div class="marb-20">
					        <p class="title"><em class="txt-blue ficon ficon-riqi"></em> '.$kjName.'</p>
					        <p><input name="'.$filedName.'" type="text" id="'.$filedName.'" class="dinput" value="'.$selectValue.'" onClick="WdatePicker()" /></p>
					    </div>';
	        
	    }else if($type=="数字")
	    {
	        return ' <div class="marb-20">
					        <p class="title"><em class="txt-blue ficon ficon-deng"></em>'.$kjName.'</p>
					        <p>排序： <input type="text" class="input" style="width: 50px;"  name="'.$filedName.'" value="'.$selectValue.'"/> <span class="txt-blue">数值越小越前面</span></p>
				        </div>';
	        
	    }
	    else if($type=="密码")
	    {
	        return '<tr>
				    	 <th>'.$kjName.'</th>
    				    <td>
                            <input name="'.$filedName.'" type="text" class="input" /><i>密码为空则不修改'.$tipString.'</i>
    				    </td>
				   </tr>'; 
	    }else 
	    {
	       return '<tr>
				    	 <th>'.$kjName.'</th>
    				    <td>
                            <input name="'.$filedName.'" type="text" class="input" value="'.$selectValue.'"/><i>'.$tipString.'</i>
    				    </td>
				   </tr>'; 
	    }
	    
	}
	
	
}