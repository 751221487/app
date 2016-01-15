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
			$member_db = D('Member');
			//搜索
			$where = array('1');
			if(I('get.userid')){
				$where[] = "a.user = ".I('get.userid');
			}
			if(I('get.memberid')){
				$where[] = "a.customer = ".I('get.memberid');
			}
			if(I('get.week')){
				if(I('get.pay')){
					$where[] = 'YEAR(arrive_date) = YEAR(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH))
							AND 
								WEEK(arrive_date) = WEEK(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH)) 
							AND 
								time_finish > ADDDATE(NOW(), INTERVAL -1 MONTH)';
				} else {
					$where[] = "a.time_finish < '".date('Y-m-d', time() + 24 * 7 * 3600)."' AND a.time_finish > NOW()";
				}
			}
			if(I('get.nextweek')){
				if(I('get.pay')){
					$where[] = 'YEAR(arrive_date) = YEAR(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH)) 
							AND 
								WEEK(arrive_date) = WEEK(ADDDATE(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH), -7))
							AND 
								time_finish > ADDDATE(NOW(), INTERVAL -1 MONTH)';
				} else {
					$where[] = "a.time_finish < '".date('Y-m-d', time() + 24 * 14 * 3600)."' AND a.time_finish > NOW()";
				}
			}
			if(I('get.month')){
				if(I('get.pay')){
					$where[] = 'YEAR(arrive_date) = YEAR(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH))
							AND 
								MONTH(arrive_date) = MONTH(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH)) 
							AND 
								time_finish > ADDDATE(NOW(), INTERVAL -1 MONTH)';
				} else {
					$where[] = "a.time_finish like '%".date('Y-m', time())."%' AND a.time_finish > NOW()";
				}
			}
			foreach ($search as $k=>$v){
				if(!$v) continue;
				switch ($k){
					case 'product':
						$where[] = "a.{$k} like '%{$v}%'";
						break;
					case 'customer':
					case 'user':
						$where[] = "a.{$k} = '{$v}'";
						break;
					case 'area':
						$where[] = "b.area in (".implode(',', $area_db->getChild($search['area'])).")";
					case 'startbegin':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['end'] && $search['end'] < $v) $v = $search['end'];
						$where[] = "`create_date` >= '{$v}'";
						break;
					case 'startend':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['begin'] && $search['begin'] > $v) $v = $search['begin'];
						$where[] = "`create_date` <= '{$v}'";
						break;
					case 'finishbegin':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['end'] && $search['end'] < $v) $v = $search['end'];
						$where[] = "`time_finish` >= '{$v}'";
						break;
					case 'finishend':
						if(!preg_match("/^\d{4}(-\d{2}){2}$/", $v)){
							unset($search[$k]);
							continue;
						}
						if($search['begin'] && $search['begin'] > $v) $v = $search['begin'];
						$where[] = "`time_finish` <= '{$v}'";
						break;
				}
			}
			$admin_db = D('Admin');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			if($currentAdmin['position'] != '财务' && $currentAdmin['position'] != '超级管理员'){
				$where[] = "user=".session('userid');
			}
			$where = implode(' and ', $where);
			$Model = new \Think\Model();
			$sql = "SELECT COUNT(*) as count, ifnull(SUM(a.money), 0) as money FROM app2_contract a left join app2_admin b on a.user=b.userid WHERE $where";
			$res = $Model->query($sql);
			$total = $res[0]['count'];
			$money = $res[0]['money'];
			$limit = ($page - 1) * $rows . "," . $rows;
			$order = $sort.' '.$order;
			$sql = "SELECT 
						a.*, 
						b.realname as charge,
						e.name as productname,
						d.name as customername
					FROM 
						app2_contract a
					LEFT JOIN (app2_admin b LEFT JOIN app2_area c ON b.area = c.id) ON a.user = b.userid
					LEFT JOIN  app2_member d ON a.customer = d.memberid
					LEFT JOIN app2_product e ON a.product = e.id
					WHERE
						$where
					ORDER BY 
						$order 
					LIMIT 
						$limit";
			$list = $total ? $Model->query($sql) : array();
			foreach($list as &$info){
				if(time() > strtotime($info['time_finish'])){
					$info['status'] = '已完成';
				} else {
					$info['status'] = '履行中';
				}
			}
			$data = array('total'=>$total, 'rows'=>$list, 'money'=>$money);
			$this->ajaxReturn($data);
		}else{
			$menu_db = D('Menu');
			$area_db = D('area');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$admin_db = D('Admin');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			$cond = array('grid'=>'datagrid');
			if(I('get.userid')){
				$cond['userid'] = I('get.userid');
			}
			if(I('get.memberid')){
				$cond['memberid'] = I('get.memberid');
			}
			if(I('get.week')){
				$cond['week'] = 1;
			}
			if(I('get.nextweek')){
				$cond['nextweek'] = 1;
			}
			if(I('get.month')){
				$cond['month'] = 1;
			}
			if(I('get.pay')){
				$cond['pay'] = 1;
			}
			$datagrid = array(
				'options'     => array(
					'title'   => $currentpos,
					'url'     => $url = U('Contract/contractlist', $cond),
					'toolbar' => '#contract-contractlist-datagrid-toolbar',
				),
				'fields' => array(
					'合同编号'      => array('field'=>'code','width'=>10,'sortable'=>true),
					'产品种类'        => array('field'=>'productname','width'=>10,'sortable'=>true),
					'客户'        => array('field'=>'customername','width'=>10, 'formatter'=>'contractContractModule.customer'),
					'负责人'          => array('field'=>'charge','width'=>10,'sortable'=>true),
					'投入时间' => array('field'=>'create_date','width'=>10,'sortable'=>true),
					'投入金额'          => array('field'=>'money','width'=>10,'sortable'=>true),
					'投资期限'          => array('field'=>'time_limit','width'=>8, 'sortable'=>true, 'formatter'=>'contractContractModule.timeLimit'),
					'合同状态'          => array('field'=>'status','width'=>10),
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
			$contract_log_db = D('Contract_log');
			$log['user'] = session('userid');
			$log['time'] = date("Y-m-d H:i:s", time());
			$log['ip'] = get_client_ip(0, true);
			$log['contract'] = $id;
			$log['operate'] = 1;
			$contract_log_db->add($log);
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
		$product_db = D('product');
		$productList = $product_db->select();
		for($i = 0; $i < count($adminList); $i++){
			if($info['user'] == $adminList[$i]['userid']) {
				$info['charge'] = $adminList[$i]['realname'];
			}
			if($info['create_user'] == $adminList[$i]['userid']){
				$info['creater'] = $adminList[$i]['realname'];
			}
			
		}
		for($i = 0; $i < count($memberList); $i++){
			if($info['customer'] == $memberList[$i]['memberid']){
				$info['customername'] = $memberList[$i]['name'];
			}
		}
		for($i = 0; $i < count($productList); $i++){
			if($info['product'] == $productList[$i]['id']){
				$info['productname'] = $productList[$i]['name'];
			}
		}
		$now = time();
		// $create_time = strtotime($info['create_date']);
		// $month_diff = ($now['y'] - $create_time['y']) * 12 + ($now['m'] - $create_time['m']);
		// $info['paid_finish'] = intval($month_diff / $info['income_cycle']);
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
			$contract_log_db = D('Contract_log');
			$log['user'] = session('userid');
			$log['time'] = date("Y-m-d H:i:s", time());
			$log['ip'] = get_client_ip(0, true);
			$log['contract'] = $id;
			$log['operate'] = 2;
			$contract_log_db->add($log);
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
		$create_time = strtotime($contract['arrive_data']);
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
		$result = $contract_db->where(array('id'=>$id))->delete();

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
			$admin_db = D('Admin');
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
				$adminList = $admin_db->where(array('area'=>array('in', $area_db->getChild($areas[$i]['id']))))->getField('userid', true);
				$where['user'] = array('in', implode(',', $adminList));
				$where['create_date'] = array(array('gt',$begin),array('lt',$end),'and'); 
				$income = $contract_db->where($where)->getField('SUM(money)');
				array_push($info['pie']['moneyData'], array('value'=>$income, 'name'=>$areas[$i]['name']));
			}
			$info['bar'] = array();
			$info['bar']['months'] = array();
			$info['bar']['count'] = array();
			$info['bar']['money'] = array();

			unset($where);
			$time = strtotime($begin);
			while ($time < strtotime($end)) {
				$where['area'] = array('in', $area_db->getChild($area['id']));
				$month = date('Y-m', $time);
				$where['create_date'] = array('like', "%$month%");
				array_push($info['bar']['months'], $month);
				array_push($info['bar']['count'], $contract_db->where($where)->count());
				$income = $contract_db->where($where)->getField('SUM(money)');
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

	public function contractLog($page = 1, $rows = 10, $search = array(), $sort = 'id', $order = 'desc'){
		if(IS_POST){
			$contract_db      = M('contract');
			$contract_log_db = D('Contract_log');
			$admin_db = D('admin');
			
			$total = $contract_log_db->count();
			$limit = ($page - 1) * $rows . "," . $rows;
			$order = $sort.' '.$order;
			$list = $total ? $contract_log_db->order($order)->limit($limit)->select() : array();
			$adminList = $admin_db->select();
			$contractList = $contract_db->select();
			foreach($list as &$info){
				for($i = 0; $i < count($adminList); $i++){
					if($info['user'] == $adminList[$i]['userid']) {
						$info['charge'] = $adminList[$i]['realname'];
					}
				}
				for($i = 0; $i < count($contractList); $i++){
					if($info['contract'] == $contractList[$i]['id']){
						$info['contractcode'] = $contractList[$i]['code'];
					}
				}
			}
			$data = array('total'=>$total, 'rows'=>$list);
			$this->ajaxReturn($data);
		}else{
			$admin_db = D('admin');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			if($currentAdmin['position'] != '超级管理员'){
				echo "您的权限不足";
				exit();
			}
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$datagrid = array(
				'options'     => array(
					'title'   => $currentpos,
					'url'     => U('Contract/contractlog', array('grid'=>'datagrid')),
					'toolbar' => '#contract-contractloglist-datagrid-toolbar',
				),
				'fields' => array(
					'合同编号'      => array('field'=>'contractcode','width'=>15,'formatter'=>'contractContractlogModule.contractformatter'),
					'财务员工'          => array('field'=>'user','width'=>10, 'formatter'=>'contractContractlogModule.user'),
					'操作' => array('field'=>'operate','width'=>10,'formatter'=>'contractContractlogModule.operate'),
					'时间'          => array('field'=>'time','width'=>10),
					'IP地址'          => array('field'=>'ip','width'=>10),
				)
			);
			$this->assign('admin', $currentAdmin);
			$this->assign('datagrid', $datagrid);
			$this->display('contract_log');
		}
	}

}