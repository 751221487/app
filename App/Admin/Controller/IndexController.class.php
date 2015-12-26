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
		$cond1['time_finish'] = array('lt', date('Y-m-d', time() + 24 * 7 * 3600));
		$cond2['time_finish'] = array('lt', date('Y-m-d', time() + 24 * 14 * 3600));
		$cond3['time_finish'] = array('lt', date('Y-m-d', time() + 24 * 30 * 3600));
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
			$cond_caiwu1[] = 'create_date < ADDDATE(ADDDATE(NOW(), INTERVAL -paid_finish*income_cycle MONTH), -7) AND create_user='.$userInfo['userid'];
			$cond_caiwu2[] = 'create_date < ADDDATE(ADDDATE(NOW(), INTERVAL -paid_finish*income_cycle MONTH), -14) AND create_user='.$userInfo['userid'];
			$cond_caiwu3[] = 'create_date < ADDDATE(ADDDATE(NOW(), INTERVAL -paid_finish*income_cycle MONTH), -30) AND create_user='.$userInfo['userid'];
			
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

}