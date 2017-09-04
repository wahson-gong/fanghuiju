<?php
// 品牌控制器
class YonghuController extends BaseController
{
    
    // 显示品牌列表
    public function indexAction()
    {
        // 先获取品牌信息
        $adminModel = new Model("admin");
        $adminDetail = $adminModel->oneRowCol("*", "username='{$_SESSION['admin']['username']}'");
        // print_r($adminDetail);
        include CUR_VIEW_PATH . "Syonghuguanli" . DS . "yonghu_edit.html";
    }
    
    // 定义update方法，完成品牌的更新
    public function updateAction()
    {
        // //获取条件及数据
        $data['username'] = trim($_POST['username']);
        $data['email'] = trim($_POST['email']);
        $data['password'] = trim($_POST['password']);
        $data['new_password'] = trim($_POST['new_password']);
        $data['re_password'] = trim($_POST['re_password']);
        $data['user_id'] = trim($_POST['user_id']);
        
        if ($data['username'] == '' || $data['password'] == '' || $data['new_password'] == '' || $data['re_password'] == '') {
            $this->jump("index.php?p=admin&c=yonghu&a=index", "用户名或当前密码或新密码或确认密码为空", 2);
        }
        if ($data['new_password'] != $data['re_password']) {
            $this->jump("index.php?p=admin&c=yonghu&a=index", "新密码与确认密码不相同", 2);
        }
        $adminModel = new AdminModel('admin');
        $user = $adminModel->checkUser($data['username'], $data['password']);
        if ($user) {
            // 当前密码正确
            $data['password'] = md5($data['new_password']);
        } else {
            $this->jump("index.php?p=admin&c=yonghu&a=index", "当前密码不正确", 2);
        }
        // 调用模型完成更新
        $Common = new Common();
        $System = new SystemModel('System');
        
        if ($adminModel->update($data)) {
            // 写入日志
            $System->addSystem($_SESSION['admin']['username'], $_SESSION['admin']['username'] . ":更改用户信息：成功。操作页面:" . $Common->getUrl(), $Common->getIP(), "用户管理");
            $this->jump("index.php?p=admin&c=yonghu&a=index", "更新成功", 2);
        } else {
            $System->addSystem($_SESSION['admin']['username'], $_SESSION['admin']['username'] . ":更改用户信息：失败。操作页面:" . $Common->getUrl(), $Common->getIP(), "用户管理");
            $this->jump("index.php?p=admin&c=yonghu&a=index", "更新失败", 2);
        }
    }
}