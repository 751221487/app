<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 合同模块
 * @author wangdong
 */
class ContractController extends CommonController {
	
	/**
	 * 合同列表
	 */
	public function contractList($page = 1, $rows = 10, $search = array(), $sort = 'id', $order = 'asc'){
		if(IS_POST){
			$contract_db      = M('contract');

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
						$where[] = "`create_date` >= '{$v}'";
						break;
					case 'end':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['begin'] && $search['begin'] > $v) $v = $search['begin'];
						$where[] = "`create_date` <= '{$v}'";
						break;
				}
			}
			$where = implode(' and ', $where);
			$total = $contract_db->where($where)->count();
			$order = $sort.' '.$order;
			$limit = ($page - 1) * $rows . "," . $rows;
			$list = $total ? $contract_db->where($where)->order($order)->limit($limit)->select() : array();
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
			$admin_db = D('Admin');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			$datagrid = array(
				'options'     => array(
					'title'   => $currentpos,
					'url'     => U('Contract/contractlist', array('grid'=>'datagrid')),
					'toolbar' => '#contract-contractlist-datagrid-toolbar',
				),
				'fields' => array(
					'合同编号'      => array('field'=>'code','width'=>10,'sortable'=>true),
					'产品种类'        => array('field'=>'product','width'=>10,'sortable'=>true),
					'客户'        => array('field'=>'customer','width'=>10),
					'负责人'          => array('field'=>'charge','width'=>10),
					'投入时间' => array('field'=>'create_date','width'=>10,'sortable'=>true),
					'投入金额'          => array('field'=>'money','width'=>10),
					'投资期限'          => array('field'=>'time_limit','width'=>10),
					'管理操作'      => array('field'=>'id','width'=>30,'formatter'=>'contractContractModule.operate'),
				)
			);
			$this->assign('admin', $currentAdmin);
			$this->assign('datagrid', $datagrid);
			$this->display('contract_list');
		}
	}
	
	/**
	 * 添加合同
	 */
	public function contractAdd(){
		if(IS_POST){
			$contract_db = M('contract');
			$data = I('post.info');
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize   =     314572800 ;// 设置附件上传大小
			$upload->exts      =     array('jpg', 'png', 'jpeg', 'gif', 'doc', 'docx', 'pdf');// 设置附件上传类型
			$upload->rootPath  =     './Public/upload/'; // 设置附件上传根目录
			$upload->saveName = array('uniqid','');
			$info   =   $upload->upload($_FILES);
			$data['idcard'] = $info['idcard']['savepath'].$info['idcard']['savename'];
			$data['bankcard'] = $info['bankcard']['savepath'].$info['bankcard']['savename'];
			$data['contract_file'] = $info['contract_file']['savepath'].$info['contract_file']['savename'];
			$id = $contract_db->add($data);
			if($id){
				$this->success('添加成功');
			}else {
				$this->error('添加失败');
			}
		}else{
			$this->display('contract_add');
		}
	}

	/**
	* 合同详情
	*/
	public function contractDetail($id){
		$contract_db = M('contract');
		$info = $contract_db->where(array('id'=>$id))->find();
		$admin_db = D('Admin');
		$adminList = $admin_db->select();
		for($i = 0; $i < count($adminList); $i++){
			if($info['user'] == $adminList[$i]['userid']) {
				$info['charge'] = $adminList[$i]['realname'];
			}
		}
		$this->assign('info', $info);
		$this->display('contract_detail');
	}
	
	/**
	 * 编辑合同
	 */
	public function contractEdit($id){
		$contract_db = M('contract');
		if(IS_POST){
			$data = I('post.info');
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize   =     314572800 ;// 设置附件上传大小
			$upload->exts      =     array('jpg', 'png', 'jpeg', 'gif', 'doc', 'docx', 'pdf');// 设置附件上传类型
			$upload->rootPath  =     './Public/upload/'; // 设置附件上传根目录
			$upload->saveName = array('uniqid','');
			$info   =   $upload->upload($_FILES);
			if($info['idcard']['name'] != ''){
				$data['idcard'] = $info['idcard']['savepath'].$info['idcard']['savename'];
			}
			if($info['bankcard']['name'] != ''){
				$data['bankcard'] = $info['bankcard']['savepath'].$info['bankcard']['savename'];
			}
			if($info['contract_file']['name'] != ''){
				$data['contract_file'] = $info['contract_file']['savepath'].$info['contract_file']['savename'];
			}
			$result = $contract_db->where(array('id'=>$id))->save($data);
			if($result){
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$info = $contract_db->where(array('id'=>$id))->find();
			$admin_db = D('Admin');
			$adminList = $admin_db->select();
			for($i = 0; $i < count($adminList); $i++){
				if($info['user'] == $adminList[$i]['userid']) {
					$info['charge'] = $adminList[$i]['realname'];
				}
			}
			$this->assign('info', $info);
			$this->display('contract_edit');
		}
	}
	
	/**
	 * 删除合同
	 */
	public function memberDelete($id){
		$member_oauth_db = M('member_oauth');
		$member_oauth_db->where(array('memberid'=>$id))->delete();

		$contract_db = M('member');
		$result = $contract_db->where(array('memberid'=>$id))->delete();

		if ($result){
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}

}