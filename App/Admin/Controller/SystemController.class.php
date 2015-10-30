<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 后台管理模块
 * @author wangdong
 */
class SystemController extends CommonController{
	private $fileBathPath = RUNTIME_PATH;   //文件管理根目录
		
	/**
	 * 菜单列表
	 */
	public function menuList(){
		$menu_db = D('Menu');
		if(IS_POST){
			if(S('system_menulist')){
				$data = S('system_menulist');
			}else{
				$data = $menu_db->getTree();
				S('system_menulist', $data);
			}
			$this->ajaxReturn($data);
		}else{
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$treegrid = array(
				'options'       => array(
					'title'        => $currentpos,
					'url'          => U('System/menuList', array('grid'=>'treegrid')),
					'idField'    => 'id',
					'treeField' => 'name',
					'toolbar'   => 'systemMenuModule.toolbar',
				),
				'fields' => array(
					'排序'        => array('field'=>'listorder','width'=>20,'align'=>'center','formatter'=>'systemMenuModule.sort'),
					'菜单ID'    => array('field'=>'id','width'=>20,'align'=>'center'),
					'菜单名称' => array('field'=>'name','width'=>200),
					'管理操作' => array('field'=>'operateid','width'=>80,'align'=>'center','formatter'=>'systemMenuModule.operate'),
				)
			);
			$this->assign('treegrid', $treegrid);
			$this->display('menu_list');
		}
	}
	
	/**
	 * 添加菜单
	 */
	public function menuAdd($parentid = 0){
		if(IS_POST){
			$menu_db = D('Menu');
			$data = I('post.info');
			$data['display'] = $data['display'] ? '1' : '0';
			$id = $menu_db->add($data);
			if($id){
				$menu_db->clearCatche();
				$this->success('添加成功');
			}else {
				$this->error('添加失败');
			}
		}else{
			$this->assign('parentid', $parentid);
			$this->display('menu_add');
		}
	}
	
	/**
	 * 编辑菜单
	 */
	public function menuEdit($id = 0){
		if(!$id) $this->error('未选择菜单');
		$menu_db = D('Menu');
		if(IS_POST){
			$data = I('post.info');
			if(!$menu_db->checkParentId($id, $data['parentid'])){
				$this->error('上级菜单设置失败');
			}

			$data['display'] = $data['display'] ? '1' : '0';
			$result = $menu_db->where(array('id'=>$id))->save($data);
			if($result){
				$menu_db->clearCatche();
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$data = $menu_db->where(array('id'=>$id))->find();
			$this->assign('data', $data);
			$this->display('menu_edit');
		}
	}
	
	/**
	 * 删除菜单
	 */
	public function menuDelete($id = 0){
		if($id && IS_POST){
			$menu_db = D('Menu');
			$result = $menu_db->where(array('id'=>$id))->delete();
			if($result){
				$menu_db->clearCatche();
				$this->success('删除成功');
			}else {
				$this->error('删除失败');
			}
		}else{
			$this->error('删除失败');
		}
	}

	/**
	 * 菜单排序
	 */
	public function menuOrder(){
		if(IS_POST) {
			$menu_db = D('Menu');
			foreach(I('post.order') as $id => $listorder) {
				$menu_db->where(array('id'=>$id))->save(array('listorder'=>$listorder));
			}
			$menu_db->clearCatche();
			$this->success('操作成功');
		} else {
			$this->error('操作失败');
		}
	}
	
	/**
	 * 菜单导出
	 */
	public function menuExport($filename = ''){
		if(IS_POST) {
			$menu_db = D('Menu');
			$data    = array('type'=>'menu');
			$data['data']   = $menu_db->order('id asc')->getField('id,name,parentid,c,a,data,listorder,display', true);
			$data['verify'] = md5(var_export($data['data'], true) . $data['type']);
			
			//数据进行多次加密，防止数据泄露
			$data = base64_encode(gzdeflate(json_encode($data)));
			
			$uniqid = uniqid();
			$filename = UPLOAD_PATH . 'export/' . $uniqid . '.data';
			if(file_write($filename, $data)){
				$this->success('导出成功', U('System/menuExport', array('filename'=>$uniqid)));
			}
			$this->error('导出失败，请重试！');
		}else{
			//过滤特殊字符，防止非法下载文件
			$filename = str_replace(array('.', '/', '\\'), '', $filename);
			$filename = UPLOAD_PATH . 'export/' . $filename . '.data';
			if(!file_exist($filename)) $this->error('非法访问');
			
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename="菜单管理.data"');
			echo file_read($filename);
			
			file_delete($filename);
		}
	}
	
	/**
	 * 菜单导入
	 */
	public function menuImport($filename = ''){
		if(IS_POST) {
			//过滤特殊字符，防止非法下载文件
			$filename = str_replace(array('.', '/', '\\'), '', $filename);
			$filename = UPLOAD_PATH . 'import/' . $filename . '.data';
			if(!file_exist($filename)) $this->error('导入失败');
			
			$content = file_read($filename);
			
			//解密
			try {
				$data  = gzinflate(base64_decode($content));
			}catch (\Exception $e){};
			if(!isset($data)){
				file_delete($filename);
				$this->error('非法数据');
			}
			
			//防止非法数据
			try {
				$data = json_decode($data, true);
			}catch (\Exception $e){};
			if(!is_array($data) || !isset($data['type']) || $data['type'] != 'menu' || !isset($data['verify']) || !isset($data['data'])){
				file_delete($filename);
				$this->error('非法数据');
			}
			
			if($data['verify'] != md5(var_export($data['data'], true) . $data['type'])){
				file_delete($filename);
				$this->error('非法数据');
			}
			
			$menu_db = D('Menu');
			
			//先清空数据再导入
			$menu_db->where('id > 0')->delete();
			$menu_db->clearCatche();
			
			//开始导入
			asort($data['data']);
			foreach ($data['data'] as $add){
				$menu_db->add($add);
			}
			
			file_delete($filename);
			$this->success('导入成功');
		}else{
			$this->error('非法访问');
		}
	}

	/**
	 * 菜单下拉框
	 */
	public function public_menuSelectTree(){
		if(S('system_public_menuselecttree')){
			$data = S('system_public_menuselecttree');
		}else {
			$menu_db = D('Menu');
			$data = $menu_db->getSelectTree();
			$data = array(0=>array('id'=>0,'text'=>'作为一级菜单','children'=>$data));
			S('system_public_menuselecttree', $data);
		}
		$this->ajaxReturn($data);
	}
	
	/**
	 * 验证菜单名称是否已存在
	 */
	public function public_menuNameCheck($name){
		if(I('get.default') == $name) {
			exit('true');
		}
		
		$menu_db = D('Menu');
		$exists = $menu_db->checkName($name);
		if ($exists) {
			exit('false');
		}else{
			exit('true');
		}
	}
	
	

}
