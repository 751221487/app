<?php
namespace Admin\Model;
use Think\Model;

class AdminModel extends Model{
	protected $tableName = 'admin';
	protected $pk             = 'userid';
	public      $error;


	/**
	* 处理消息
	*/
	private function handleMessage($userid){
		$member_db = D('Member');
		$contract_db = D('Contract');
		$message_db = D('message');

		//处理生日
		$memberList = $member_db->where(array('user'=>$userid))->select();
		for($i = 0; $i < count($memberList); $i++){
			if(date('m-d', strtotime($memberList[$i]['birthday'])) == date('m-d', time())){
				$where = array(
					'type' => 1,
					'link' => $memberList[$i]['memberid'],
					'MONTH(time)' => date('m', time()),
					'DAY(time)'=> date('d', time())
					);
				if(!$message_db->where($where)->find()){
					$data['user'] = $userid;
					$data['content'] = '客户'.$memberList[$i]['name'].'的生日到了';
					$data['time'] = date('Y-m-d H:i:s', time());
					$data['type'] = 1;
					$data['link'] = $memberList[$i]['memberid'];
					$message_db->add($data);
				}
			}
		}
		//处理合同
		$contractList = $contract_db->where(array('user'=>$userid))->select();
		for($i = 0; $i < count($contractList); $i++) {
			$now = time();
			$create_time = strtotime($contractList[$i]['create_date']);
			$month_diff = (date('Y') - date('Y', $create_time)) * 12 + (date('m') - date('m', $create_time));
			$to_paid_finish = intval($month_diff / $contractList[$i]['income_cycle']);
			if($to_paid_finish > $contractList[$i]['paid_finish'] && $now < strtotime($contractList[$i]['time_finish'])){
				$where = array(
					'type' => 2,
					'link' => $contractList[$i]['id'],
					'MONTH(time)' => date('m', time()),
					'DAY(time)'=> date('d', time())
					);
				if(!$message_db->where($where)->find()){
					$data['user'] = $userid;
					$data['content'] = '合同'.$contractList[$i]['code'].'需要付款';
					$data['time'] = date('Y-m-d H:i:s', time());
					$data['type'] = 2;
					$data['link'] = $contractList[$i]['id'];
					$message_db->add($data);
				}
			}
		}
			
	}
	
	/**
	 * 登录验证
	 */
	public function login($username, $password){
		$times_db = M('times');

		//查询帐号
		$r = $this->where(array('username'=>$username))->find();
		if(!$r){
			$this->error = '用户不存在';
			return false;
		}
		

		$password = md5(md5($password).$r['encrypt']);
		$ip             = get_client_ip(0, true);

		if($r['password'] != $password) {
			$this->error = "密码错误";
			return false;
		}

		$this->where(array('userid'=>$r['userid']))->save(array('lastloginip'=>$ip,'lastlogintime'=>time()));
		$this->handleMessage($r['userid']);
		
		session('userid', $r['userid']);
		session('roleid', $r['roleid']);
		cookie('username', $username);
		cookie('userid', $r['userid']);
		S('SESSION_ID_' . $r['userid'] , session_id());  //单点登录用
		return true;
	}

	/**
	 * 获取用户信息
	 */
	public function getUserInfo($userid){
		$admin_role_db = D('AdminRole');
		$info = $this->field('password, encrypt', true)->where(array('userid'=>$userid))->find();
		if($info) $info['rolename'] = $admin_role_db->getRoleName($info['roleid']);    //获取角色名称
		return $info;
	}
	
	
	/**
	 * 修改密码
	 */
	public function editPassword($userid, $password){
		$userid = intval($userid);
		if($userid < 1) return false;
		$passwordinfo = password($password);
		return $this->where(array('userid'=>$userid))->save($passwordinfo);
	}
}