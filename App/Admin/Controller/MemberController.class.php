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
			$where = array();
			foreach ($search as $k=>$v){
				if(!$v) continue;
				switch ($k){
					case 'name':
					case 'tel':
						$where[] = "`{$k}` like '%{$v}%'";
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
						$where['department'] = array('in', $area_db->getChild($v));
						break;
				}
			}
			$where = implode(' and ', $where);
			$total = $member_db->where($where)->count();
			$order = $sort.' '.$order;
			$limit = ($page - 1) * $rows . "," . $rows;
			$list = $total ? $member_db->where($where)->order($order)->limit($limit)->select() : array();
			$admin_db = D('Admin');
			$adminList = $admin_db->select();
			foreach($list as &$info){
				for($i = 0; $i < count($adminList); $i++){
					if($info['user'] == $adminList[$i]['userid']) {
						$info['charge'] = $adminList[$i]['realname'];
					}
				}
			}
			$data = array('total'=>$total, 'rows'=>$list);
			$this->ajaxReturn($data);
		}else{
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
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
					'创建时间' => array('field'=>'create_time','width'=>15,'sortable'=>true,'formatter'=>'memberMemberModule.time'),
					'管理操作'      => array('field'=>'memberid','width'=>30,'formatter'=>'memberMemberModule.operate'),
				)
			);
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

			$data['user'] = session('userid');
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
			$member_type_db = M('member_type');
			$typelist = $member_type_db->where(array('disabled'=>'0'))->getField('typeid,typename', true);
			$dict = dict('', 'Member');
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
			$data['user'] = session('userid');
			$admin_db = D('Admin');
			$charger = $admin_db->where(array('userid'=>session('userid')))->find();
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
		$memberList = $member_db->where(array('department'=>array('in', $area_db->getChild($admin['area']))))->field(array('memberid', 'name'))->select();
		for($i = 0; $i < count($memberList); $i++){
			$memberList[$i]['id'] = $memberList[$i]['memberid'];
			$memberList[$i]['text'] = $memberList[$i]['name'];
		}
		$this->ajaxReturn($memberList);
	}

}