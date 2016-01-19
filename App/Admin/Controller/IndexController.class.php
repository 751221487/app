<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 后台管理通用模块
 */
class IndexController extends CommonController {
	/**
	 * 后台首页
	 */
	public function index(){
		$admin_db = D('Admin');
		$menu_db  = D('Menu');
		$area_db = D('Area');
		
		$userid     = session('userid');
		$userInfo  = $admin_db->getUserInfo($userid);    //获取用户基本信息

		$menuList = $menu_db->getMenu();                //头部菜单列表
		$this->assign('userInfo', $userInfo);
		$this->assign('menuList', $menuList);
		$this->display('index');
	}

	
	/**
	 * 用户登录
	 */
	public function login(){
		
		if (I('get.dosubmit')){
			$admin_db = D('Admin');
			
			$username = I('post.username', '', 'trim') ? I('post.username', '', 'trim') : $this->error('用户名不能为空', HTTP_REFERER);
			$password = I('post.password', '', 'trim') ? I('post.password', '', 'trim') : $this->error('密码不能为空', HTTP_REFERER);
			$admin_db->login($username, $password);
			
			if($admin_db->login($username, $password)){
				$this->success('登录成功', U('Index/index'));
			}else{
				$this->error($admin_db->error, HTTP_REFERER);
			}

		}else {
			$this->display();
		}
	}
	
	/**
	 * 退出登录
	 */
	public function logout() {
		session('userid', null);
		session('roleid', null);
		cookie('username', null);
		cookie('userid', null);
		
		$this->success('安全退出！', U('Index/login'));
	}
	
	/**
	 * 验证码
	 */
	public function code(){
		$verify = new \Think\Verify();
		$verify->useCurve = true;
		$verify->useNoise = false;
		$verify->bg = array(255, 255, 255);
		
		if (I('get.code_len')) $verify->length = intval(I('get.code_len'));
		if ($verify->length > 8 || $verify->length < 2) $verify->length = 4;
		
		if (I('get.font_size')) $verify->fontSize = intval(I('get.font_size'));
		
		if (I('get.width')) $verify->imageW = intval(I('get.width'));
		if ($verify->imageW <= 0) $verify->imageW = 130;
		
		if (I('get.height')) $verify->imageH = intval(I('get.height'));
		if ($verify->imageH <= 0) $verify->imageH = 50;

		$verify->entry('admin');
	}
	
	/**
	 * 左侧菜单
	 */
	public function public_menuLeft($menuid = 0) {
		$menu_db = D('Menu');
		$datas = array();
		$list = $menu_db->getMenu($menuid);
		foreach ($list as $k=>$v){
			$datas[$k]['name'] = $v['name'];
			$son_datas = $menu_db->getMenu($v['id']);
			foreach ($son_datas as $k2=>$v2){
				$datas[$k]['son'][$k2]['text'] = $v2['name'];
				$datas[$k]['son'][$k2]['id']   = $v2['id'];
				$datas[$k]['son'][$k2]['url'] = U($v2['c'].'/'.$v2['a'].'?menuid='.$v2['id'].'&'.$v2['data']);
				$datas[$k]['son'][$k2]['iconCls'] = 'icons-application-application_go';
			}
		}
		$this->ajaxReturn($datas);
	}
	
	/**
	 * 后台欢迎页
	 */
	public function public_main(){
		$admin_db = D('Admin');
		$area_db = D('Area');
		$userid   = session('userid');
		$userInfo = $admin_db->where(array('userid'=>$userid))->find();    //获取用户基本信息

		$admin_log = M('admin_log');
		$loginList = $admin_log->where(array('userid'=>$userid))->order("time desc")->limit(5)->select();

		$message_db = D('Message');
		$unreadMessage = $message_db->where(array('user'=>$userid, 'isread'=>0))->count();

		$setting_db = D('Setting');
		$settingList = $setting_db->select();
		$settings = array();
		for($i = 0; $i < count($settingList); $i++){
			$settings[$settingList[$i]['key']] = $settingList[$i]['value'];
		}

		if($userInfo['area'] == 0){
			$userInfo['areaname'] = '全国';
		} else {
			$area = $area_db->where(array('id'=>$userInfo['area']))->find();
			$userInfo['areaname'] = $area['name'];
		}
		$contract_db = D('Contract');
		$cond1['time_finish'] = array(
			array('lt', date('Y-m-d', time() + 24 * 7 * 3600)),
			array('gt', date('Y-m-d', time())),
			);
		$cond2['time_finish'] = array(
			array('lt', date('Y-m-d', time() + 24 * 14 * 3600)),
			array('gt', date('Y-m-d', time())),
			);
		$cond3['time_finish'] = array(
			array('like', '%'.date('Y-m', time()).'%'),
			array('gt', date('Y-m-d', time())),
			);
		if($userInfo['position'] == '理财顾问'){
			$cond['user'] = $userInfo['userid'];
		} else if($userInfo['position'] == '财务'){
			$cond['create_user'] = $userInfo['userid'];
		}else {
			$adminList = $admin_db->where(array('area'=>array('in', $area_db->getChild($userInfo['area']))))->getField('userid', true);
			$cond['user'] = array('in', implode(',', $adminList));
		}

		$contract_week_count = $contract_db->where(array_merge($cond1, $cond))->count();
		$contract_week_money = $contract_db->where(array_merge($cond1, $cond))->sum('money');

		$contract_next_week_count = $contract_db->where(array_merge($cond2, $cond))->count();
		$contract_next_week_money = $contract_db->where(array_merge($cond2, $cond))->sum('money');
		
		$contract_month_count = $contract_db->where(array_merge($cond3, $cond))->count();
		$contract_month_money = $contract_db->where(array_merge($cond3, $cond))->sum('money');

		if($userInfo['position'] == '财务'){
			$cond_caiwu1[] = 'YEAR(arrive_date) = YEAR(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH))
							AND 
								WEEK(arrive_date) = WEEK(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH)) 
							AND 
								time_finish > ADDDATE(NOW(), INTERVAL -1 MONTH) 
							AND
								create_user='.$userInfo['userid'];
			$cond_caiwu2[] = 'YEAR(arrive_date) = YEAR(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH)) 
							AND 
								WEEK(arrive_date) = WEEK(ADDDATE(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH), -7))
							AND 
								time_finish > ADDDATE(NOW(), INTERVAL -1 MONTH) 
							AND
								create_user='.$userInfo['userid'];
			$cond_caiwu3[] = 'YEAR(arrive_date) = YEAR(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH))
							AND 
								MONTH(arrive_date) = MONTH(ADDDATE(NOW(), INTERVAL -(paid_finish+1)*income_cycle MONTH)) 
							AND 
								time_finish > ADDDATE(NOW(), INTERVAL -1 MONTH) 
							AND 
								create_user='.$userInfo['userid'];
			
			$contract_pay_week_count = $contract_db->where($cond_caiwu1)->count();
			$contract_pay_week_money = $contract_db->where($cond_caiwu1)->sum('money');
			
			$contract_pay_next_week_count = $contract_db->where($cond_caiwu2)->count();
			$contract_pay_next_week_money = $contract_db->where($cond_caiwu2)->sum('money');
			
			$contract_pay_month_count = $contract_db->where($cond_caiwu3)->count();
			$contract_pay_month_money = $contract_db->where($cond_caiwu3)->sum('money');
		}

		$this->assign('contract_week_count', $contract_week_count);
		$this->assign('contract_week_money', $contract_week_money);
		$this->assign('contract_next_week_count', $contract_next_week_count);
		$this->assign('contract_next_week_money', $contract_next_week_money);
		$this->assign('contract_month_count', $contract_month_count);
		$this->assign('contract_month_money', $contract_month_money);

		$this->assign('contract_pay_week_count', $contract_pay_week_count);
		$this->assign('contract_pay_week_money', $contract_pay_week_money);
		$this->assign('contract_pay_next_week_count', $contract_pay_next_week_count);
		$this->assign('contract_pay_next_week_money', $contract_pay_next_week_money);
		$this->assign('contract_pay_month_count', $contract_pay_month_count);
		$this->assign('contract_pay_month_money', $contract_pay_month_money);

		$this->assign('setting', $settings);
		$this->assign('userInfo', $userInfo);
		$this->assign('unread', $unreadMessage);
		$this->display('main');
	}

	//修改积分比例
	public function changeRate(){
		$rate = I('post.rate');
		$setting_db = D('Setting');
		if($rate < 0){
			$this->error('数据更新失败');
		} else {
			$setting_db->where(array('key'=>'rate'))->save(array('value'=>$rate));
			$this->success('数据更新成功');
		}
	}

	/**
	 * 服务器信息
	 */
	public function systemInfo(){
		$sysinfo = \Admin\Plugin\SysinfoPlugin::getinfo();
		$os = explode(' ', php_uname());
		//网络使用状况
		$net_state = null;
		if ($sysinfo['sysReShow'] == 'show' && false !== ($strs = @file("/proc/net/dev"))) {
			for ($i = 2; $i < count($strs); $i++) {
				preg_match_all("/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs[$i], $info);
				$net_state.="{$info[1][0]} : 已接收 : <font color=\"#CC0000\"><span id=\"NetInput{$i}\">" . $sysinfo['NetInput' . $i] . "</span></font> GB &nbsp;&nbsp;&nbsp;&nbsp;已发送 : <font color=\"#CC0000\"><span id=\"NetOut{$i}\">" . $sysinfo['NetOut' . $i] . "</span></font> GB <br />";
			}
		}

		$this->assign('sysinfo', $sysinfo);
		$this->assign('os', $os);
		$this->assign('net_state', $net_state);
		$this->display("systeminfo");
	}
	
	/**
	 * 更新后台缓存
	 */
	public function public_clearCatche(){
		$list = dict('', 'Cache');
		if(is_array($list) && !empty($list)){
			foreach ($list as $modelName=>$funcName){
				D($modelName)->$funcName();
			}
		}
		$this->success('缓存更新成功');
	}
	
	/**
	 * 防止登录超时
	 */
	public function public_sessionLife(){
		$userid = session('userid');
		//单点登录判断
		if(C('LOGIN_ONLY_ONE')){
			if(session_id() != S('SESSION_ID_' . $userid)){
				$this->error('帐号已在其他地方登录，您已被迫下线！', U('Index/logout'));
			}
		}
		$this->success('正常登录');
	}

	/*导入excel*/
	public function uploadxls(){
		$type = I('post.type');
		$uploadType = I('post.upload');
		import("Org.Util.PHPExcel");
		import("Org.Util.PHPExcel.IOFactory.php");
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   = 314572800 ;// 设置附件上传大小
		$upload->exts      = array('xls');// 设置附件上传类型
		$upload->rootPath  = './Public/upload/';

		$info   =   $upload->uploadOne($_FILES[$type]);
		if(!$info) {// 上传错误提示错误信息
			$data['status'] = 0;
			$data['error'] = $upload->getError();
		}else{// 上传成功 获取上传文件信息
			$data['status'] = 1;
			$data['path'] = $info['savepath'];
			$data['name'] = $info['savename'];
			$inputFileType = 'Excel5';
			$inputFileName = $data['path'].$data['name'];
			$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcelReader = $objReader->load('Public/upload/'.$inputFileName);

			$loadedSheetNames = $objPHPExcelReader->getSheetNames();

			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcelReader, 'CSV');

			foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
				$objWriter->setSheetIndex($sheetIndex);
				$objWriter->save('Public/upload/'.$loadedSheetName.'.csv');
				switch ($uploadType) {
					case 'stuff':
						$count = $this->importStuffData('Public/upload/'.$loadedSheetName.'.csv');
						break;
					
					default:
						break;
				}
			}
		}
		$data['count'] = $count;
		header("Content-Type:text/html");
		echo json_encode($data);
	}

	// public function testuploadxls(){
	// 	import("Org.Util.PHPExcel");
	// 	import("Org.Util.PHPExcel.IOFactory.php");
	// 	$inputFileType = 'Excel5';
	// 	$inputFileName = 'stuff.xls';
	// 	$objReader = \PHPExcel_IOFactory::createReader($inputFileType);
	// 	$objPHPExcelReader = $objReader->load('Public/upload/'.$inputFileName);

	// 	$loadedSheetNames = $objPHPExcelReader->getSheetNames();

	// 	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcelReader, 'CSV');

	// 	foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
	// 		$objWriter->setSheetIndex($sheetIndex);
	// 		$objWriter->save('Public/upload/'.$loadedSheetName.'.csv');
	// 		$this->importStuffData('Public/upload/'.$loadedSheetName.'.csv');
	// 	}
	// }

	private function input_csv($handle) { 
		$out = array(); 
		$n = 0; 
		while ($data = fgetcsv($handle, 10000)) { 
			$num = count($data); 
			for ($i = 0; $i < $num; $i++) { 
				$out[$n][$i] = $data[$i]; 
			} 
			$n++; 
		} 
		return $out; 
	} 

	/*
	 * import stuff data
	*/
	public function importStuffData($file){
		$f = fopen($file, "r");
		$result = $this->input_csv($f); //解析csv
		$len_result = count($result); 
		if($len_result == 0){ 
			exit(); 
		} 
		$admin_db = D('admin');
		$area_db = D('area');
		$job_db = D('job');
		$count = 0;
		for ($i = 1; $i < $len_result; $i++) { //循环获取各字段值
			if($result[$i][0] != "") {
				$username = $result[$i][0];
				$position = $result[$i][1]; 
				$_area = $result[$i][2];
				$join_time = $result[$i][3];
				$left_time = $result[$i][4];
				$target =  $result[$i][5];
				$_job = $result[$i][6];
				$email = $result[$i][7];
				$tel = $result[$i][8];
				$truename = $result[$i][9];
				$remark = $result[$i][10];
				$area = $area_db->where("name='$_area'")->find();
				$job = $job_db->where("name='$_job'")->find();
				if(isset($area['id']) && isset($job['id']) && (!$admin_db->where(array('username'=>$username))->find())) {
					$data['username'] = $username;
					$data['password'] = md5('123456');
					$data['position'] = $position;
					$data['area'] = $area['id'];
					if($target){
						$data['job'] = $job['id'];
						$data['target'] = $target;
					} else {
						$data['job'] = $job['id'];
						$data['target'] = $job['target'];
					}
					$data['join_time'] = $join_time;
					$data['left_time'] = $left_time;
					$data['target_time'] = date('Y-m-d', strtotime('+'.($job['time']/30).' month', time()));
					$data['email'] = $email;
					$data['tel'] = $tel;
					if($position == '员工' || $position == '理财顾问'){
						$data['permission'] = 2;
					} else {
						$data['permission'] = 1;
					}
					$data['remark'] = $remark;
					$data['realname'] = $truename;
					$admin_db->add($data); 
					$count++;
				}
			}
		} 
		return $count;
	}

}