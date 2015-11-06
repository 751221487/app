<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 站内消息模块
 */
class MessageController extends CommonController {
	
	/**
	* 消息列表
	*/
	public function messageList($page = 1, $rows = 10, $search = array(), $sort = 'id', $order = 'asc'){
		if(IS_POST){
			$message_db      = M('message');

			//搜索
			$where = array();
			$where['user'] = session('userid');
			$where = implode(' and ', $where);
			$total = $message_db->where($where)->count();
			$order = $sort.' '.$order;
			$limit = ($page - 1) * $rows . "," . $rows;
			$list = $total ? $message_db->where($where)->order($order)->limit($limit)->select() : array();
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
					'url'     => U('Message/messageList', array('grid'=>'datagrid')),
					'toolbar' => '#message-messagelist-datagrid-toolbar',
				),
				'fields' => array(
					'时间'      => array('field'=>'time','width'=>10,'sortable'=>true),
					'内容'      => array('field'=>'content', 'width'=>50),
				)
			);
			$this->assign('admin', $currentAdmin);
			$this->assign('datagrid', $datagrid);
			$this->display('message_list');
		}
	}

}