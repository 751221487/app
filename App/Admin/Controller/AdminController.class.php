<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 后台用户相关模块
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
	 * 员工管理
	 */
	public function memberList($page = 1, $rows = 10, $search = array(), $sort = 'userid', $order = 'asc'){
		if(IS_POST){
			$admin_db = D('Admin');
			$job_db = D('Job');
			$area_db = D('Area');
			$admin = $admin_db->where(array('userid'=>session('userid')))->find();
			$areas = $area_db->getChild($admin['area']);
			$where[] = "a.area in (".implode(',', $areas).")";
			foreach ($search as $k=>$v){
				if(!$v) continue;
				switch ($k){
					case 'job':
						$where[] = "a.job = ".$search['job'];
						break;
					case 'position':
						$where[] = "a.position = '".$search['position']."'";
						break;
					case 'realname':
					case 'tel':
					case 'username':
					case 'eamil':
						$where[] = "a.{$k} like '%{$v}%'";
						break;
					case 'area':
						$areas = $area_db->getChild($search['area']);
						$where[] = "a.area in (".implode(',', $areas).")";
						break;
					case 'joinbegin':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['end'] && $search['end'] < $v) $v = $search['end'];
						$where[] = "`join_time` >= '{$v}'";
						break;
					case 'joinend':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['begin'] && $search['begin'] > $v) $v = $search['begin'];
						$where[] = "`join_time` <= '{$v}'";
						break;
					case 'leftbegin':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['end'] && $search['end'] < $v) $v = $search['end'];
						$where[] = "`left_time` >= '{$v}'";
						break;
					case 'leftend':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['begin'] && $search['begin'] > $v) $v = $search['begin'];
						$where[] = "`left_time` <= '{$v}'";
						break;
				}
			}
			$where = implode(' and ', $where);
			$Model = new \Think\Model();
			$sql = "SELECT COUNT(*) as count FROM app2_admin a WHERE $where";
			$total = $Model->query($sql);
			$total = $total[0]['count'];
			$order = $sort.' '.$order;
			$limit = ($page - 1) * $rows . "," . $rows;
			$sql = "SELECT * FROM 
					(SELECT 
						a.*, 
						b.name as jobname, 
						b.time as target_limit, 
						c.name as areaname,
						ifnull(SUM(d.money), 0) as contractmoney,
						COUNT(d.id) as contractcount
					FROM 
						app2_admin a 
						LEFT join app2_job b ON a.job = b.id 
						LEFT join app2_contract d ON a.userid = d.user
						LEFT JOIN app2_area c ON a.area = c.id
					WHERE 
						$where 
					GROUP BY
						a.userid
					) res1
					INNER JOIN 
					(SELECT 
						a.userid as userid2, 
						ifnull(SUM(d.money), 0) - a.target as gap 
					FROM 
						app2_admin a 
						LEFT join app2_job b ON a.job = b.id 
						LEFT join app2_contract d ON (a.userid = d.user AND MONTH(d.create_date) = MONTH(NOW()) AND YEAR(d.create_date) = YEAR(NOW()))
						LEFT JOIN app2_area c ON a.area = c.id
					WHERE 
						$where 
					GROUP BY
						a.userid
					) res2 ON res1.userid = res2.userid2
					ORDER BY 
						$order 
					LIMIT 
						$limit";
			$list = $total ? $Model->query($sql) : array();
			if($admin['permission'] == 2){
				$total = 1;
				$sql = "SELECT * FROM 
					(SELECT 
						a.*, 
						b.name as jobname, 
						b.time as target_limit, 
						c.name as areaname,
						ifnull(SUM(d.money), 0) as contractmoney,
						COUNT(d.id) as contractcount
					FROM 
						app2_admin a 
						LEFT join app2_job b ON a.job = b.id 
						LEFT join app2_contract d ON (a.userid = d.user AND MONTH(d.create_date) = MONTH(NOW()) AND YEAR(d.create_date) = YEAR(NOW()))
						LEFT JOIN app2_area c ON a.area = c.id
					WHERE 
						a.userid=".session('userid').
					"
					GROUP BY
						a.userid
					) res1
					INNER JOIN 
					(SELECT 
						a.userid as userid2, 
						ifnull(SUM(d.money), 0) - a.target*10000 as gap 
					FROM 
						app2_admin a 
						LEFT join app2_job b ON a.job = b.id 
						LEFT join app2_contract d ON a.userid = d.user
						LEFT JOIN app2_area c ON a.area = c.id
					WHERE 
						d.create_date >= a.status_change_time
					AND 
						$where 
					AND
						a.userid=".session('userid').
					"GROUP BY
						a.userid
					) res2 ON res1.userid = res2.userid2
					ORDER BY 
						$order 
					LIMIT 
						$limit";
				$list = $Model->query($sql);
			}
			foreach($list as &$info){
				if($info['area'] == 0){
					$info['areaname'] = '全国';
				}
				$info['target_time'] = date('Y-m');
			}
			$data = array('total'=>$total, 'rows'=>$list);
			$this->ajaxReturn($data);
		}else{
			$area_db = D('Area');
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$admin_db = D('admin');
			$job_db = D('job');
			$admin = $admin_db->where(array('userid'=>session('userid')))->find();

			$setting_db = D('Setting');
			$settingList = $setting_db->select();
			$settings = array();
			for($i = 0; $i < count($settingList); $i++){
				$settings[$settingList[$i]['key']] = $settingList[$i]['value'];
			}

			$datagrid = array(
				'options'     => array(
					'title'   => $currentpos,
					'url'     => U('Admin/memberList', array('grid'=>'datagrid')),
					'toolbar' => '#admin-memberlist-datagrid-toolbar',
				),
				'fields' => array(
					'用户名'      => array('field'=>'username','width'=>10,'sortable'=>true),
					'真实姓名'     => array('field'=>'realname','width'=>10,'sortable'=>true),
					'职位'     => array('field'=>'position','width'=>10,'sortable'=>true),
					'状态'    	 => array('field'=>'jobname','width'=>10, 'sortable'=>true),
					'部门'		 => array('field'=>'areaname', 'width'=>10, 'sortable'=>true),
					'业务目标'	=> array('field'=>'target', 'width'=>10, 'sortable'=>true),
					'合同总数'	=> array('field'=>'contractcount', 'width'=>10, 'sortable'=>true, 'formatter'=>'adminMemberModule.contract'),
					'合同总金额'	=> array('field'=>'contractmoney', 'width'=>12, 'sortable'=>true),
					'业务差距'	=> array('field'=>'gap', 'width'=>12, 'sortable'=>true), 
					'业务期限'	 => array('field'=>'target_time', 'width'=>12),
					'积分'	 => array('field'=>'score', 'width'=>12, 'formatter'=>'adminMemberModule.score'),
					'管理操作'     => array('field'=>'userid','width'=>30,'formatter'=>'adminMemberModule.operate'),
				)
			);
			
			$this->assign('setting', $settings);
			$this->assign('jobList', $job_db->select());
			$this->assign('admin', $admin);
			$this->assign('datagrid', $datagrid);
			$this->display('member_list');
		}
	}

	/**
	* 员工详情
	*/
	public function memberDetail($id){
		$admin_db = M('Admin');
		$info = $admin_db->where(array('userid'=>$id))->find();

		$job_db = D('Job');
		$area_db = D('Area');
		$contract_db = D('contract');

		$jobList = $job_db->select();
		$areaList = $area_db->select();

		$info['contractCount'] = $contract_db->where(array('user'=>$id))->count();
		if($info['contractCount'] == 0){
			$info['contractMoney'] = 0;
		} else {
			$info['contractMoney'] = $contract_db->where(array('user'=>$id))->getField('SUM(money)');
		}
		$info['jobname'] = '-';
		$info['target_limit'] = '-';
		for ($i=0; $i < count($jobList); $i++) { 
			if($jobList[$i]['id'] == $info['job']){
				$info['jobname'] = $jobList[$i]['name'];
			}
		}
		$info['target_time'] = date('Y-m');
		$info['areaname'] = '全国';
		for ($i=0; $i < count($areaList); $i++) { 
			if($areaList[$i]['id'] == $info['area']){
				$info['areaname'] = $areaList[$i]['name'];
			}
		}
		
		
		$this->assign('info', $info);
		$this->display('member_detail');
	}
		
	/**
	 * 添加员工
	 */
	public function memberAdd(){
		if(IS_POST){
			$admin_db = D('Admin');
			$data = I('post.info');
			if($admin_db->where(array('username'=>$data['username']))->field('username')->find()){
				$this->error('用户名称已经存在');
			}

			$job_db = D('job');

			$data['password'] = '123456';
			$passwordinfo = password($data['password']);
			$data['password'] = $passwordinfo['password'];
			$data['encrypt'] = $passwordinfo['encrypt'];
			$job_db = D('Job');
			$job = $job_db->where(array('id'=>$data['job']))->find();
			$data['target_time'] =  date('Y-m-d', strtotime('+'.($job['time']/30).' month', time()));
			if($data['target'] == ''){
				$job = $job_db->where(array('id'=>$data['job']))->find();
				$data['target'] = $job['target'];
			}
			$data['target'] = $data['target'] * 10000;
			$data['status_change_time'] = date('Y-m-d', time());
			$id = $admin_db->add($data);
			if($id){
				if($email) send_email($email['email'], $email['subject'], $email['content'], array('isHtml'=>true, 'charset'=>'GB2312'));
				$this->success('添加成功');
			}else {
				$this->error('添加失败');
			}
		}else{
			$job_db = D('Job');
			$jobList = $job_db->select();
			$this->assign('jobList', $jobList);
			$this->display('member_add');
		}
	}
	
	/**
	 * 编辑员工
	 */
	public function memberEdit($id){
		$admin_db = D('Admin');
		if(IS_POST){
			$data = I('post.info');
			$job_db = D('job');

			$admin = $admin_db->where(array('userid'=>$id))->find();
			if($data['job'] != $admin['job']){
				$job = $job_db->where(array('id'=>$data['job']))->find();
				$data['target_time'] =  date('Y-m-d', strtotime('+'.$job['time'].' month', time()));
				$data['status_change_time'] = date('Y-m-d', time());
			}
			if($data['target'] == ''){
				$job = $job_db->where(array('id'=>$data['job']))->find();
				$data['target'] = $job['target'];
			}
			$data['target'] = $data['target'] * 10000;

			$result = $admin_db->where(array('userid'=>$id))->save($data);
			if($result){
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$info = $admin_db->where(array('userid'=>$id))->find();
			$this->assign('info', $info);
			$job_db = D('Job');
			$jobList = $job_db->select();
			$this->assign('jobList', $jobList);
			$this->display('member_edit');
		}
	}
	
	/**
	 * 重置用户密码
	 */
	public function memberResetPassword($id){
		$admin_db = D('Admin');
		$password = '123456';
		$info = password($password);
		$data = array(
			'password' => $info['password'],
			'encrypt'  => $info['encrypt']
		);
		$result = $admin_db->where(array('userid'=>$id))->save($data);

		if ($result){
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
	 * 地区列表
	 */
	public function areaList(){
		$area_db = D('Area');
		$admin_db = D('Admin');
		$menu_db = D('Menu');
		if(IS_POST){
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			$data = $area_db->getTree($currentAdmin['area']);
			$this->ajaxReturn($data);
		}else{
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			$adminArea = $area_db->where(array('id'=>$currentAdmin['area']))->find();
			if($currentAdmin['position'] != '超级管理员'){
				echo "<p>你无权访问该内容</p>";
				exit();
			}
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
		$admin_db = D('Admin');
		$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
		$adminArea = $area_db->where(array('id'=>$currentAdmin['area']))->find();
		$data = $area_db->getSelectTree($currentAdmin['area']);
		if($currentAdmin['area'] == 0){
			$data = array(0=>array('id'=>0,'text'=>'全国','children'=>$data));
		} else {
			$data = array(0=>array('id'=>$adminArea['id'],'text'=>$adminArea['name'],'children'=>$data));
		}
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
			$admin_db = D('admin');
			$area_db = D('Area');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			$adminArea = $area_db->where(array('id'=>$currentAdmin['area']))->find();
			if($adminArea['parentid']){
				echo "<p>你无权访问该内容</p>";
				exit();
			}
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$datagrid = array(
				'options' => array(
					'title'   => $currentpos,
					'url'     => U('Admin/jobList', array('grid'=>'datagrid')),
					'toolbar' => 'adminJobModule.toolbar',
				),
				'fields' => array(
					'目标名称'  => array('field'=>'name','width'=>15,'sortable'=>true),
					'目标描述'  => array('field'=>'description','width'=>25),
					'业务目标'  => array('field'=>'target', 'width'=>25, 'formatter'=>'adminJobModule.target'),
					'业务期限'  => array('field'=>'time', 'width'=>15, 'formatter'=>'adminJobModule.timeLimit'),
					'管理操作'  => array('field'=>'id','width'=>20,'formatter'=>'adminJobModule.operate'),
				)
			);
			$this->assign('datagrid', $datagrid);
			$this->display('job_list');
		}
	}


	/**
	 * 添加职位
	 */
	public function jobAdd(){
		if(IS_POST){
			$job_db = D('Job');
			$data = I('post.info');
			$data['display'] = 1;
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
			$data['display'] = 1;

			$id = $job_db->where(array('id'=>$id))->save($data);
			if($id){
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$info = $job_db->where(array('id'=>$id))->find();
			$this->assign('info', $info);
			$this->display('job_edit');
		}
	}
	
	/**
	 * 删除职位
	 */
	public function jobDelete($id) {
		$admin_db = D('Admin');
		$count = $admin_db->where(array('job'=>$id))->count();
		if($count) $this->error("该职位下面仍有 <b>{$count}</b> 个用户");

		$job_db = D('Job');
		$result = $job_db->where(array('id'=>$id))->delete();
		
		if ($result){
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}

	/**
	 * 验证 职位名称是否存在
	 */
	public function public_checkJobName($name){
		if (I('get.default') == $name) {
			exit('true');
		}
		$job_db = D('Job');
		$exists = $job_db->where(array('name'=>$name))->field('name')->find();
		if ($exists) {
			exit('false');
		}else{
			exit('true');
		}
	}

	/**
	* 选择员工combobox
	*/
	public function public_selectAdmin(){
		$admin_db = D('Admin');
		$admin = $admin_db->where(array('userid'=>session('userid')))->find();
		$area_db = D('Area');
		$adminList = $admin_db->where(array('area'=>array('in', $area_db->getChild($admin['area']))))->field(array('userid', 'username', 'realname'))->select();
		for($i = 0; $i < count($adminList); $i++){
			$adminList[$i]['id'] = $adminList[$i]['userid'];
			$adminList[$i]['text'] = $adminList[$i]['realname'].'('.$adminList[$i]['username'].')';
		}
		$this->ajaxReturn($adminList);
	}

}