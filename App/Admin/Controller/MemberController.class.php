<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 前台客户模块
 */
class MemberController extends CommonController {
	/**
	 * 客户管理
	 */
	public function memberList($page = 1, $rows = 10, $search = array(), $sort = 'memberid', $order = 'asc'){
		if(IS_POST){
			$member_db      = M('member');
			$member_type_db = M('member_type');
			$typelist       = $member_type_db->where(array('disabled'=>'0'))->order('listorder asc')->getField('typeid,typename', true);
			$area_db = D('Area');
			//搜索
			$admin_db = D('admin');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			$areas = $area_db->getChild($currentAdmin['area']);
			$where[] = "b.area in (".implode(',', $areas).")";
			foreach ($search as $k=>$v){
				if(!$v) continue;
				switch ($k){
					case 'user':
						$where[] = "a.user = '{$v}'";
						break;
					case 'name':
					case 'tel':
					case 'idcard':
						$where[] = "a.{$k} like '%{$v}%'";
						break;
					case 'area':
						$areas = $area_db->getChild($search['area']);
						$where[] = "b.area in (".implode(',', $areas).")";
						break;
					case 'begin':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['end'] && $search['end'] < $v) $v = $search['end'];
						$where[] = "`create_time` >= '{$v}'";
						break;
					case 'end':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['begin'] && $search['begin'] > $v) $v = $search['begin'];
						$where[] = "`create_time` <= '{$v}'";
						break;
					case 'department':
						$where[] = "b.area in (".implode(',', $areas).")";
						// $where['department'] = array('in', $area_db->getChild($v));
						break;
				}
			}
			$where = implode(' and ', $where);
			$Model = new \Think\Model();
			$sql = "SELECT COUNT(*) as count FROM app2_member a left join app2_admin b ON a.user=b.userid WHERE $where";
			$total = $Model->query($sql);
			$total = $total[0]['count'];
			$order = $sort.' '.$order;
			$limit = ($page - 1) * $rows . "," . $rows;
			$sql = "SELECT 
						a.*, 
						b.realname as charge, 
						ifnull(SUM(d.money), 0) as contractmoney,
						COUNT(d.id) as contractcount
					FROM 
						app2_admin b, app2_contract d
					RIGHT join
						app2_member a
					ON
						a.memberid=d.customer
					WHERE 
						$where
					AND 
						a.user=b.userid
					GROUP BY
						a.memberid
					ORDER BY 
						$order 
					LIMIT 
						$limit";
			$list = $total ? $Model->query($sql) : array();
			$data = array('total'=>$total, 'rows'=>$list);
			$this->ajaxReturn($data);
		}else{
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$setting_db = D('Setting');
			$settingList = $setting_db->select();
			$settings = array();
			for($i = 0; $i < count($settingList); $i++){
				$settings[$settingList[$i]['key']] = $settingList[$i]['value'];
			}
			
			$datagrid = array(
				'options'     => array(
					'title'   => $currentpos,
					'url'     => U('Member/memberList', array('grid'=>'datagrid')),
					'toolbar' => '#member-member-datagrid-toolbar',
				),
				'fields' => array(
					'来源'        => array('field'=>'origin','width'=>10,'sortable'=>true),
					'姓名'        => array('field'=>'name','width'=>10),
					'电话'    => array('field'=>'tel','width'=>10,'sortable'=>false),
					'负责人'          => array('field'=>'charge','width'=>10),
					'合同总数'	=> array('field'=>'contractcount', 'width'=>10, 'sortable'=>true, 'formatter'=>'memberMemberModule.contract'),
					'合同总金额'	=> array('field'=>'contractmoney', 'width'=>12, 'sortable'=>true),
					'积分'	 => array('field'=>'score', 'width'=>12, 'formatter'=>'memberMemberModule.score'),
					'管理操作'      => array('field'=>'memberid','width'=>30,'formatter'=>'memberMemberModule.operate'),
				)
			);
			$this->assign('setting', $settings);
			$dict = dict('', 'Member');
			$this->assign('dict', $dict);
			$this->assign('datagrid', $datagrid);
			$this->display('member_list');
		}
	}
	
	/**
	 * 添加客户
	 */
	public function memberAdd(){
		if(IS_POST){
			$member_db = M('member');
			$data = I('post.info');

			if(!$data['user']){
				$data['user'] = session('userid');
			}
			$data['create_time'] = date("Y-m-d", time());
			$admin_db = D('Admin');
			$charger = $admin_db->where(array('userid'=>session('userid')))->find();
			$data['department'] = $charger['area'];

			$id = $member_db->add($data);
			if($id){
				$this->success('添加成功');
			}else {
				$this->error('添加失败');
			}
		}else{
			$admin_db = D('admin');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			$member_type_db = M('member_type');
			$typelist = $member_type_db->where(array('disabled'=>'0'))->getField('typeid,typename', true);
			$dict = dict('', 'Member');
			$this->assign('currentAdmin', $currentAdmin);
			$this->assign('dict', $dict);
			$this->assign('typelist', $typelist);
			$this->display('member_add');
		}
	}
	
	/**
	 * 编辑客户
	 */
	public function memberEdit($id){
		$member_db = M('member');
		if(IS_POST){
			$data = I('post.info');
			if(!$data['user']){
				$data['user'] = session('userid');
			}
			$admin_db = D('Admin');
			$charger = $admin_db->where(array('userid'=>$data['user']))->find();
			$data['department'] = $charger['area'];
			$result = $member_db->where(array('memberid'=>$id))->save($data);
			if($result){
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$info = $member_db->where(array('memberid'=>$id))->find();
			$this->assign('info', $info);
			$this->display('member_edit');
		}
	}

	/**
	* 客户详情
	*/
	public function memberDetail($id){
		$member_db = M('member');
		$admin_db = D('Admin');
		$info = $member_db->where(array('memberid'=>$id))->find();
		$adminList = $admin_db->select();
		for($i = 0; $i < count($adminList); $i++){
			if($info['user'] == $adminList[$i]['userid']) {
				$info['charge'] = $adminList[$i]['realname'].'('.$adminList[$i]['username'].')';
			}
		}
		$this->assign('info', $info);
		$this->display('member_detail');
	}
	
	/**
	 * 删除客户
	 */
	public function memberDelete($id){
		$member_oauth_db = M('member_oauth');
		$member_oauth_db->where(array('memberid'=>$id))->delete();

		$member_db = M('member');
		$result = $member_db->where(array('memberid'=>$id))->delete();

		if ($result){
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}


	/**
	* 选择客户combobox
	*/
	public function public_selectCustomer(){
		$admin_db = D('Admin');
		$member_db = D('Member');
		$admin = $admin_db->where(array('userid'=>session('userid')))->find();
		$area_db = D('Area');
		$memberList = $member_db->where(array('department'=>array('in', $area_db->getChild($admin['area']))))->field(array('memberid', 'name', 'idcard'))->select();
		for($i = 0; $i < count($memberList); $i++){
			$memberList[$i]['id'] = $memberList[$i]['memberid'];
			$memberList[$i]['text'] = $memberList[$i]['name']."(".$memberList[$i]['idcard'].")";
		}
		$this->ajaxReturn($memberList);
	}

	/**
	 * 验证身份证重复
	 */
	public function public_checkId($name){
		if (I('get.default') == $name) {
			exit('true');
		}
		$member_db = D('Member');
		$exists = $member_db->where(array('idcard'=>$name))->field('idcard')->find();
		if ($exists) {
			exit('false');
		}else{
			exit('true');
		}
	}

}