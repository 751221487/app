<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 后台用户相关模块
 * @author wangdong
 */
class AdminController extends CommonController {
	/**
	 * 修改个人信息
	 */
	public function public_editInfo($info = array()){
		$userid = session('userid');
		$admin_db = D('Admin');
		if (IS_POST){
			$fields = array('email','realname');
			foreach ($info as $k=>$value) {
				if (!in_array($k, $fields)){
					unset($info[$k]);
				}
			}
			$state = $admin_db->where(array('userid'=>$userid))->save($info);
			$state ? $this->success('修改成功') : $this->error('修改失败');
		}else {
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$info = $admin_db->where(array('userid'=>$userid))->find();
			
			$this->assign('info',$info);
			$this->assign(currentpos, $currentpos);
			$this->display('edit_info');
		}
	}
	
	/**
	 * 修改密码
	 */
	public function public_editPwd(){
		$userid = session('userid');
		$admin_db = D('Admin');
		if(IS_POST){
			$info = $admin_db->where(array('userid'=>$userid))->field('password,encrypt')->find();
			if(password(I('post.old_password'), $info['encrypt']) !== $info['password'] ) $this->error('旧密码输入错误');
			if(I('post.new_password')) {
				$state = $admin_db->editPassword($userid, I('post.new_password'));
				if(!$state) $this->error('密码修改失败');
			}
			$this->success('密码修改该成功,请使用新密码重新登录', U('Index/logout'));
		}else{
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$info = $admin_db->where(array('userid'=>$userid))->find();
			
			$this->assign('info',$info);
			$this->assign(currentpos, $currentpos);
			$this->display('edit_password');
		}
	}
	
	/**
	 * 登录日志
	 */
	public function loginLog($page=1, $rows=10, $search = array(), $sort = 'time', $order = 'desc'){
		if(IS_POST){
			$userid = session('userid');
			
			$admin_log = M('admin_log');
			//搜索
			$where = array("`type` = 'login'", "`userid` = {$userid}");
			foreach ($search as $k=>$v){
				if(!$v) continue;
				switch ($k){
					case 'begin':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['end'] && $search['end'] < $v) $v = $search['end'];
						$where[] = "`time` >= '{$v}'";
						break;
					case 'end':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['begin'] && $search['begin'] > $v) $v = $search['begin'];
						$where[] = "`time` <= '{$v}'";
						break;
				}
			}
			$where = implode(' and ', $where);
				
			$limit = ($page - 1) * $rows . "," . $rows;
			$order = $sort.' '.$order;
			$total = $admin_log->where($where)->count();
			$list  = $total ? $admin_log->where($where)->order($order)->limit($limit)->select() : array();

			$data = array('total'=>$total, 'rows'=>$list);
			$this->ajaxReturn($data);
		}else{
			$menu_db = D('Menu');				
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			
		
			$datagrid = array(
				'options'     => array(
					'title'   => $currentpos,
					'url'     => U('Admin/loginLog', array('grid'=>'datagrid')),
					'toolbar' => '#admin-loginlog-datagrid-toolbar',
				),
				'fields' => array(
					'用户名' => array('field'=>'username','width'=>20),
					'登录时间'   => array('field'=>'time','width'=>30,'sortable'=>true),
					'浏览器标识'   => array('field'=>'httpuseragent','width'=>100),
					'IP'    => array('field'=>'ip','width'=>25,'sortable'=>true),
				)
			);
			$this->assign('datagrid', $datagrid);
			$this->display('login_log');
		}
	}
	
	/**
	 * 删除登录日志
	 */
	public function loginLogDelete($week = 4){
		$userid = session('userid');
		$admin_log = M('admin_log');
		$start = time() - $week*7*24*3600;
		$d = date('Y-m-d', $start);
		$where = "`type` = 'login' AND `userid` = {$userid} AND left(`time`, 10) <= '$d'";
		$result = $admin_log->where($where)->delete();
		$result ? $this->success('删除成功') : $this->error('没有数据或已删除过了，请稍后再试');
	}
	
	/**
	 * 用户管理
	 */
	public function memberList($page = 1, $rows = 10, $sort = 'userid', $order = 'asc'){
		if(IS_POST){
			$admin_db = D('Admin');
			$admin_role_db = D('AdminRole');
			$rolelist = $admin_role_db->where(array('disabled'=>'0'))->order('listorder asc')->getField('roleid,rolename', true);

			$total = $admin_db->count();
			$order = $sort.' '.$order;
			$limit = ($page - 1) * $rows . "," . $rows;
			$list = $total ? $admin_db->field('userid,username,lastloginip,email,realname,lastlogintime,roleid')->order($order)->limit($limit)->select() : array();
			foreach($list as &$info){
				$info['rolename']      = isset($rolelist[$info['roleid']]) ? $rolelist[$info['roleid']] : '-';
				$info['lastlogintime'] = $info['lastlogintime'] ? date('Y-m-d H:i:s', $info['lastlogintime']) : '-';
			}
			$data = array('total'=>$total, 'rows'=>$list);
			$this->ajaxReturn($data);
		}else{
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$datagrid = array(
				'options'     => array(
					'title'   => $currentpos,
					'url'     => U('Admin/memberList', array('grid'=>'datagrid')),
					'toolbar' => 'adminMemberModule.toolbar',
				),
				'fields' => array(
					'用户名'      => array('field'=>'username','width'=>15,'sortable'=>true),
					'所属角色'    => array('field'=>'rolename','width'=>15),
					'最后登录IP'  => array('field'=>'lastloginip','width'=>15,'sortable'=>true),
					'最后登录时间' => array('field'=>'lastlogintime','width'=>15,'sortable'=>true,'formatter'=>'adminMemberModule.time'),
					'E-mail'     => array('field'=>'email','width'=>25,'sortable'=>true),
					'真实姓名'    => array('field'=>'realname','width'=>15,'sortable'=>true),
					'管理操作'    => array('field'=>'userid','width'=>25,'formatter'=>'adminMemberModule.operate'),
				)
			);
			$this->assign('datagrid', $datagrid);
			$this->display('member_list');
		}
	}
	
	/**
	 * 添加用户
	 */
	public function memberAdd(){
		if(IS_POST){
			$admin_db = D('Admin');
			$data = I('post.info');
			if($admin_db->where(array('username'=>$data['username']))->field('username')->find()){
				$this->error('用户名称已经存在');
			}

			//邮件模版
			$email_db = M('email');
			$email    = $email_db->field(array('subject', 'content'))->where(array('code'=>'user_register'))->find();
			if($email){
				$email = array_merge($email, array(
					'email' => $data['email'],
					'content' => str_replace(array('{username}', '{password}', '{site}'), array($data['username'], $data['password'], SITE_URL), htmlspecialchars_decode($email['content']))
				));
			}

			$passwordinfo = password($data['password']);
			$data['password'] = $passwordinfo['password'];
			$data['encrypt'] = $passwordinfo['encrypt'];

			$id = $admin_db->add($data);
			if($id){
				if($email) send_email($email['email'], $email['subject'], $email['content'], array('isHtml'=>true, 'charset'=>'GB2312'));
				$this->success('添加成功');
			}else {
				$this->error('添加失败');
			}
		}else{
			$admin_role_db = D('AdminRole');
			$rolelist = $admin_role_db->where(array('disabled'=>'0'))->order('listorder asc')->getField('roleid,rolename', true);
			$this->assign('rolelist', $rolelist);
			$this->display('member_add');
		}
	}
	
	/**
	 * 编辑用户
	 */
	public function memberEdit($id){
		$admin_db = D('Admin');
		if(IS_POST){
			if($id == '1') $this->error('该用户不能被修改');
			$data = I('post.info');
			
			if(isset($data['password'])) unset($data['password']);
			
			$result = $admin_db->where(array('userid'=>$id))->save($data);
			if($result){
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$admin_role_db = D('AdminRole');
			$info = $admin_db->getUserInfo($id);
			$rolelist = $admin_role_db->where(array('disabled'=>'0'))->order('listorder asc')->getField('roleid,rolename', true);
			$this->assign('info', $info);
			$this->assign('rolelist', $rolelist);
			$this->display('member_edit');
		}
	}
	
	/**
	 * 重置用户密码
	 */
	public function memberResetPassword($id){
		$admin_db = D('Admin');
		$password = rand(100000, 999999);
		$info = password($password);
		$data = array(
			'password' => $info['password'],
			'encrypt'  => $info['encrypt']
		);
		$result = $admin_db->where(array('userid'=>$id))->save($data);

		//邮件模版
		$email_db = M('email');
		$email    = $email_db->field(array('subject', 'content'))->where(array('code'=>'user_reset_password'))->find();
		if($email){
			$userInfo = $admin_db->field('username,email')->where(array('userid'=>$id))->find();
			$email = array_merge($email, array(
				'email' => $userInfo['email'],
				'content' => str_replace(array('{username}', '{password}', '{site}'), array($userInfo['username'], $password, SITE_URL), htmlspecialchars_decode($email['content']))
			));
		}
		
		if ($result){
			if($email) send_email($email['email'], $email['subject'], $email['content'], array('isHtml'=>true, 'charset'=>'GB2312'));
			$this->ajaxReturn(array('status'=>1, 'info'=>'重置成功', 'password'=>$password));
		}else {
			$this->error('重置失败');
		}
	}
	
	/**
	 * 删除用户
	 */
	public function memberDelete($id){
		if($id == '1') $this->error('该用户不能被删除');
		$admin_db = D('Admin');
		$result = $admin_db->where(array('userid'=>$id))->delete();
		if ($result){
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}
	


	/**
	 * 权限设置
	 */
	public function rolePermission($id){
		if(IS_POST) {
			$menu_db = D('Menu');
			if (I('get.dosubmit')){
				$admin_role_priv_db = M('admin_role_priv');
				$admin_role_priv_db->where(array('roleid'=>$id))->delete();
				$menuids = explode(',', I('post.menuids'));
				$menuids = array_unique($menuids);
				if(!empty($menuids)){
					$menuList = array();
					$menuinfo = $menu_db->field(array('id','c','a'))->select();
					foreach ($menuinfo as $v) $menuList[$v['id']] = $v;
					foreach ($menuids as $menuid){
						$info = array(
							'roleid' => $id,
							'c'      => $menuList[$menuid]['c'],
							'a'      => $menuList[$menuid]['a'],
						);
						$admin_role_priv_db->add($info);
					}
				}
				$this->success('权限设置成功');
			//获取列表数据
			}else{
				$data = $menu_db->getRoleTree(0, $id);
				$this->ajaxReturn($data);
			}
		} else {
			$this->assign('id', $id);
			$this->display('role_permission');
		}
	}
	
	/**
	 * 栏目权限
	 */
	public function roleCategory($id){
		if(IS_POST){
			$category_priv_db = D('CategoryPriv');
			if (I('get.dosubmit')){
				$data = I('post.info');
				$category_priv_db->where(array('roleid'=>$id))->delete();
				foreach ($data as $catid=>$actionList){
					foreach ($actionList as $action){
						$category_priv_db->add(array(
							'catid'    => $catid,
							'roleid'   => $id,
							'is_admin' => 1,
							'action'   => $action,
						));
					}
				}
				$this->success('权限设置成功');
			}else{
				$data = $category_priv_db->getTreeGrid($id);
				$this->ajaxReturn($data);
			}
		}else{
			$treegrid = array(
				'options' => array(
					'url'       => U('Admin/roleCategory', array('id'=>$id, 'grid'=>'treegrid')),
					'idField'   => 'catid',
					'treeField' => 'catname',
				),
				'fields' => array(
					'全选/取消'  => array('field'=>'operateid','width'=>30,'align'=>'center','formatter'=>'adminRoleModule.checkbox'),
					'栏目ID'   => array('field'=>'catid','width'=>20,'align'=>'center'),
					'栏目名称'   => array('field'=>'catname','width'=>120),
					'查看'       => array('field'=>'field_view','width'=>15,'align'=>'center','formatter'=>'adminRoleModule.field'),
					'添加'       => array('field'=>'field_add','width'=>15,'align'=>'center','formatter'=>'adminRoleModule.field'),
					'编辑'       => array('field'=>'field_edit','width'=>15,'align'=>'center','formatter'=>'adminRoleModule.field'),
					'删除'       => array('field'=>'field_delete','width'=>15,'align'=>'center','formatter'=>'adminRoleModule.field'),
				)
			);
			$this->assign('id', $id);
			$this->assign('treegrid', $treegrid);
			$this->display('role_category');
		}
	}
	
	/**
	 * 验证邮箱是否存在
	 */
	public function public_checkEmail($email = 0){
		if (I('get.default') == $email) {
			exit('true');
		}
		$admin_db = D('Admin');
		$exists = $admin_db->where(array('email'=>$email))->field('email')->find();
		if ($exists) {
			exit('false');
		}else{
			exit('true');
		}
	}
	
	/**
	 * 验证密码
	 */
	public function public_checkPassword($password = 0){
		$userid = session('userid');
		$admin_db = D('Admin');
		$info = $admin_db->where(array('userid'=>$userid))->field('password,encrypt')->find();
		if (password($password, $info['encrypt']) == $info['password'] ) {
			exit('true');
		}else {
			exit('false');
		}
	}
	
	/**
	 * 验证用户名
	 */
	public function public_checkName($name){
		if (I('get.default') == $name) {
			exit('true');
		}
		$admin_db = D('Admin');
		$exists = $admin_db->where(array('username'=>$name))->field('username')->find();
		if ($exists) {
			exit('false');
		}else{
			exit('true');
		}
	}
	
	/**
	 * 验证角色名称是否存在
	 */
	public function public_checkRoleName($rolename){
		if (I('get.default') == $rolename) {
			exit('true');
		}
		$admin_role_db = D('AdminRole');
		$exists = $admin_role_db->where(array('rolename'=>$rolename))->field('rolename')->find();
		if ($exists) {
			exit('false');
		}else{
			exit('true');
		}
	}

	/**
	 * 地区列表
	 */
	public function areaList(){
		$area_db = D('Area');
		$menu_db = D('Menu');
		if(IS_POST){
			$data = $area_db->getTree();
			$this->ajaxReturn($data);
		}else{
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$treegrid = array(
				'options'       => array(
					'title'     => $currentpos,
					'url'       => U('Admin/areaList', array('grid'=>'treegrid')),
					'idField'   => 'id',
					'treeField' => 'name',
					'toolbar'   => 'adminAreaModule.toolbar',
				),
				'fields' => array(
					'地区ID'    => array('field'=>'id','width'=>20,'align'=>'center'),
					'地区名称' => array('field'=>'name','width'=>200),
					'管理操作' => array('field'=>'operateid','width'=>80,'align'=>'center','formatter'=>'adminAreaModule.operate'),
				)
			);
			$this->assign('treegrid', $treegrid);
			$this->display('area_list');
		}
	}

	/**
	 * 添加地区
	 */
	public function areaAdd($parentid = 0){
		if(IS_POST){
			$area_db = D('Area');
			$data = I('post.info');
			$data['display'] = $data['display'] ? '1' : '0';
			$id = $area_db->add($data);
			if($id){
				$area_db->clearCache();
				$this->success('添加成功');
			}else {
				$this->error('添加失败');
			}
		}else{
			$this->assign('parentid', $parentid);
			$this->display('area_add');
		}
	}

	/**
	 * 验证地区名称是否已存在
	 */
	public function public_areaNameCheck($name){
		if(I('get.default') == $name) {
			exit('true');
		}
		
		$area_db = D('Area');
		$exists = $area_db->checkName($name);
		if ($exists) {
			exit('false');
		}else{
			exit('true');
		}
	}


	/**
	 * 地区下拉框
	 */
	public function public_areaSelectTree(){
		$area_db = D('Area');
		$data = $area_db->getSelectTree();
		$data = array(0=>array('id'=>0,'text'=>'作为一级菜单','children'=>$data));
		S('system_public_areaselecttree', $data);
		$this->ajaxReturn($data);
	}

	/**
	 * 编辑地区
	 */
	public function areaEdit($id = 0){
		if(!$id) $this->error('未选择菜单');
		$area_db = D('Area');
		if(IS_POST){
			$data = I('post.info');
			if(!$area_db->checkParentId($id, $data['parentid'])){
				$this->error('上级菜单设置失败');
			}

			$data['display'] = $data['display'] ? '1' : '0';
			$result = $area_db->where(array('id'=>$id))->save($data);
			if($result){
				$area_db->clearCache();
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$data = $area_db->where(array('id'=>$id))->find();
			$this->assign('data', $data);
			$this->display('area_edit');
		}
	}

	/**
	 * 删除地区
	 */
	public function areaDelete($id = 0){
		if($id && IS_POST){
			$area_db = D('Area');
			$result = $area_db->where(array('id'=>$id))->delete();
			if($result){
				$area_db->clearCache();
				$this->success('删除成功');
			}else {
				$this->error('删除失败');
			}
		}else{
			$this->error('删除失败');
		}
	}

	/**
	* 职位管理
	*/
	public function jobList($page = 1, $rows = 10, $sort = 'id', $order = 'asc'){
		if(IS_POST){
			$job_db = D('Job');
			$total = $job_db->count();
			$order = $sort.' '.$order;
			$limit = ($page - 1) * $rows . "," . $rows;
			$list = $job_db->order($order)->limit($limit)->select();
			if(!$list) $list = array();
			$data = array('total'=>$total, 'rows'=>$list);
			$this->ajaxReturn($data);
		}else{
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$datagrid = array(
				'options' => array(
					'title'   => $currentpos,
					'url'     => U('Admin/jobList', array('grid'=>'datagrid')),
					'toolbar' => 'adminJobModule.toolbar',
				),
				'fields' => array(
					'ID'     => array('field'=>'id','width'=>5,'align'=>'center','sortable'=>true),
					'职位名称'  => array('field'=>'name','width'=>15,'sortable'=>true),
					'职位描述'  => array('field'=>'description','width'=>25),
					'状态'      => array('field'=>'display','width'=>15,'sortable'=>true,'formatter'=>'adminJobModule.state'),
					'管理操作'  => array('field'=>'operateid','width'=>20,'formatter'=>'adminJobModule.operate'),
				)
			);
			$this->assign('datagrid', $datagrid);
			$this->display('job_list');
		}
		$this->display('job_list');
	}


	/**
	 * 添加职位
	 */
	public function jobAdd(){
		if(IS_POST){
			$job_db = D('Job');
			$data = I('post.info');
			if($job_db->where(array('name'=>$data['name']))->field('name')->find()){
				$this->error('职位名称已存在');
			}
			$id = $job_db->add($data);
			if($id){
				$this->success('添加成功');
			}else {
				$this->error('添加失败');
			}
		}else{
			$this->display('job_add');
		}
	}
	
	/**
	 * 编辑职位
	 */
	public function jobEdit($id){
		$job_db = D('Job');
		if(IS_POST){
			$data = I('post.info');
			$id = $admin_job_db->where(array('id'=>$id))->save($data);
			if($id){
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$info = $admin_job_db->where(array('id'=>$id))->find();
			$this->assign('info', $info);
			$this->display('job_edit');
		}
	}
	
	/**
	 * 删除职位
	 */
	public function jobDelete($id) {
		if($id == '1') $this->error('该职位不能被删除');

		$admin_db = D('Admin');
		$count = $admin_db->where(array('roleid'=>$id))->count();
		if($count) $this->error("该职位下面仍有 <b>{$count}</b> 个用户");

		$job_db = D('Job');
		$result = $job_db->where(array('id'=>$id))->delete();
		
		if ($result){
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}

}