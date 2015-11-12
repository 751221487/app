<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 合同模块
 */
class ContractController extends CommonController {
	
	/**
	 * 合同列表
	 */
	public function contractList($page = 1, $rows = 10, $search = array(), $sort = 'id', $order = 'asc'){
		if(IS_POST){
			$contract_db      = M('contract');
			$area_db = D('Area');
			//搜索
			$where = array();
			foreach ($search as $k=>$v){
				if(!$v) continue;
				switch ($k){
					case 'customer':
						$where['customer'] = $v;
					case 'area':
						$where['area'] = array('in', $area_db->getChild($v));
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
			// $where = implode(' and ', $where);
			$admin_db = D('Admin');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			if($currentAdmin['position'] != '财务'){
				$where['user'] = session('userid');
			}
			$total = $contract_db->where($where)->count();
			$limit = ($page - 1) * $rows . "," . $rows;
			$order = $sort.' '.$order;
			$list = $total ? $contract_db->where($where)->order($order)->limit($limit)->select() : array();
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
			$data['create_user'] = session('userid');
			$admin_db = D('Admin');
			$adminList = $admin_db->select();
			for($i = 0; $i < count($adminList); $i++){
				if($data['user'] == $adminList[$i]['userid']) {
					$data['area'] = $adminList[$i]['area'];
				}
			}
			$id = $contract_db->add($data);
			header("Content-Type:text/html");
			if($id){
				echo json_encode(array('status'=>1, 'info'=>'修改成功'));
			}else {
				echo json_encode(array('status'=>0, 'info'=>'修改失败'));
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
		$member_db = D('Member');
		$memberList = $member_db->select();
		for($i = 0; $i < count($adminList); $i++){
			if($info['user'] == $adminList[$i]['userid']) {
				$info['charge'] = $adminList[$i]['realname'];
			}
			if($info['customer'] == $memberList[$i]['memberid']){
				$info['customername'] = $memberList[$i]['name'];
			}
		}
		$now = time();
		$create_time = strtotime($info['create_date']);
		$month_diff = ($now['y'] - $create_time['y']) * 12 + ($now['m'] - $create_time['m']);
		$info['paid_finish'] = intval($month_diff / $info['income_cycle']);
		$this->assign('admin', $admin_db->where(array('userid'=>session('userid')))->find());
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
			if(isset($info['idcard'])){
				$data['idcard'] = $info['idcard']['savepath'].$info['idcard']['savename'];
			}
			if(isset($info['bankcard'])){
				$data['bankcard'] = $info['bankcard']['savepath'].$info['bankcard']['savename'];
			}
			if(isset($info['contract_file'])){
				$data['contract_file'] = $info['contract_file']['savepath'].$info['contract_file']['savename'];
			}
			$admin_db = D('Admin');
			$adminList = $admin_db->select();
			for($i = 0; $i < count($adminList); $i++){
				if($data['user'] == $adminList[$i]['userid']) {
					$data['area'] = $adminList[$i]['area'];
				}
			}
			$result = $contract_db->where(array('id'=>$id))->save($data);
			header("Content-Type:text/html");
			if($result){
				echo json_encode(array('status'=>1, 'info'=>'修改成功'));
			}else {
				echo json_encode(array('status'=>0, 'info'=>'修改失败'));
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
	* 合同付款
	*/
	public function contractpay(){
		$id = I('post.id');
		$contract_db = D('contract');
		$contract = $contract_db->where(array('id'=>$id))->find();
		$now = time();
		$create_time = strtotime($contract['create_date']);
		$month_diff = (date('Y') - date('Y', $create_time)) * 12 + (date('m') - date('m', $create_time));
		$to_paid_finish = intval($month_diff / $contract['income_cycle']);
		if($to_paid_finish <= $contract['paid_finish']){
			$this->error('已付期数超出应付期数');
		} else {
			$result = $contract_db->where(array('id'=>$id))->save(array('paid_finish'=>($contract['paid_finish'] + 1)));

			if ($result){
				$this->success('付款成功');
			}else {
				$this->error('付款失败');
			}
		}
	}

	/**
	 * 删除合同
	 */
	public function contractDelete($id){

		$contract_db = M('contract');
		$result = $contract_db->where(array('memberid'=>$id))->delete();

		if ($result){
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}

	/**
	* 合同统计
	*/
	public function contractStatistics(){
		$admin_db = D('admin');
		if(IS_POST){
			$begin = I('post.begin');
			$end = I('post.end');
			$area = I('post.area');
			$area_db = D('Area');
			$contract_db = D("Contract");

			$info = array();
			if($area == 0) {
				$area = array('id'=>0, 'name'=>'全国');
			}else {
				$area = $area_db->where(array('id'=>$area))->find();
			}
			$info['area'] = $area['name'];
			$info['areas'] = array();
			$info['pie'] = array();
			$areas = $area_db->where(array('parentid'=>$area['id']))->select();
			for($i = 0; $i < count($areas); $i++){
				array_push($info['areas'], $areas[$i]);
			}
			$info['pie']['countData'] = array();
			for($i = 0; $i < count($areas); $i++){
				$where['area'] = array('in', $area_db->getChild($areas[$i]['id']));
				$where['create_date'] = array(array('gt',$begin),array('lt',$end),'and'); 
				$count = $contract_db->where($where)->count();
				array_push($info['pie']['countData'], array('value'=>$count, 'name'=>$areas[$i]['name']));
			}


			$info['pie']['moneyData'] = array();
			for($i = 0; $i < count($areas); $i++){
				$where['area'] = array('in', $area_db->getChild($areas[$i]['id']));
				$where['create_date'] = array(array('gt',$begin),array('lt',$end),'and'); 
				$income = $contract_db->where($where)->getField('SUM(total_income)');
				array_push($info['pie']['moneyData'], array('value'=>$income, 'name'=>$areas[$i]['name']));
			}
			$info['bar'] = array();
			$info['bar']['months'] = array();
			$info['bar']['count'] = array();
			$info['bar']['money'] = array();

			$time = strtotime($begin);
			while ($time < strtotime($end)) {
				$where['area'] = array('in', $area_db->getChild($area['id']));
				$month = date('Y-m', $time);
				$where['create_date'] = array('like', "%$month%");
				array_push($info['bar']['months'], $month);
				array_push($info['bar']['count'], $contract_db->where($where)->count());
				$income = $contract_db->where($where)->getField('SUM(total_income)');
				if(!$income){
					$income = 0;
				}
				array_push($info['bar']['money'], $income);
				$time = strtotime("+1 months", $time);
			}

			$this->ajaxReturn($info);
		}
		$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
		if($currentAdmin['position'] == '财务' || $currentAdmin['position'] == '超级管理员'){
			$this->display('contract_statistic');
		} else {
			echo "您的权限不足";
			exit();
		}
	}

}