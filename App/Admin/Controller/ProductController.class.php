<?php
namespace Admin\Controller;
use Admin\Controller\CommonController;

/**
 * 前台产品模块
 */
class ProductController extends CommonController {
	/**
	 * 产品管理
	 */
	public function productList($page = 1, $rows = 10, $search = array(), $sort = 'id', $order = 'asc'){
		if(IS_POST){
			$product_db = D('product');
			$total = $product_db->count();
			$order = $sort.' '.$order;
			$limit = ($page - 1) * $rows . "," . $rows;
			$Model = new \Think\Model();
			$sql = "SELECT a.*, COUNT(b.id) as contractcount, SUM(b.money) as contractmoney FROM 
						app2_product a 
					LEFT JOIN 
						app2_contract b 
					ON 
						a.id=b.product
					GROUP BY
						a.id
					ORDER BY 
						$order 
					LIMIT 
						$limit";
			$list = $Model->query($sql);
			if(!$list) $list = array();
			$data = array('total'=>$total, 'rows'=>$list);
			$this->ajaxReturn($data);
		}else{
			$admin_db = D('admin');
			$area_db = D('Area');
			$currentAdmin = $admin_db->where(array('userid'=>session('userid')))->find();
			$adminArea = $area_db->where(array('id'=>$currentAdmin['area']))->find();
			if($currentAdmin['position'] != "超级管理员"){
				echo "<p>你无权访问该内容</p>";
				exit();
			}
			$menu_db = D('Menu');
			$currentpos = $menu_db->currentPos(I('get.menuid'));  //栏目位置
			$datagrid = array(
				'options' => array(
					'title'   => $currentpos,
					'url'     => U('Product/productList', array('grid'=>'datagrid')),
					'toolbar' => 'ProductproductModule.toolbar',
				),
				'fields' => array(
					'项目代号'  => array('field'=>'code','width'=>15,'sortable'=>true),
					'项目名称'  => array('field'=>'name','width'=>15),
					'募集额度'  => array('field'=>'money_total', 'width'=>15),
					'已募集数量'  => array('field'=>'contractcount', 'width'=>15),
					'已募集金额'  => array('field'=>'contractmoney', 'width'=>15),
					'募集进度'  => array('field'=>'progess', 'width'=>15, 'formatter'=>'ProductproductModule.progess'),
					'客户方案'  => array('field'=>'remark','width'=>20),
					'操作'  => array('field'=>'id','width'=>20,'formatter'=>'ProductproductModule.operate'),
				)
			);
			$this->assign('datagrid', $datagrid);
			$this->display('product_list');
		}
	}
	
	/**
	 * 添加产品
	 */
	public function productAdd(){
		if(IS_POST){
			$product_db = D('product');
			$data = I('post.info');
			if($product_db->where(array('name'=>$data['name']))->field('name')->find()){
				$this->error('产品名称已存在');
			}
			$id = $product_db->add($data);
			if($id){
				$this->success('添加成功');
			}else {
				$this->error('添加失败');
			}
		}else{
			$this->display('product_add');
		}
	}
	
	/**
	 * 编辑产品
	 */
	public function productEdit($id){
		$product_db = D('product');
		if(IS_POST){
			$data = I('post.info');

			$id = $product_db->where(array('id'=>$id))->save($data);
			if($id){
				$this->success('修改成功');
			}else {
				$this->error('修改失败');
			}
		}else{
			$info = $product_db->where(array('id'=>$id))->find();
			$this->assign('info', $info);
			$this->display('product_edit');
		}
	}

	
	/**
	 * 删除产品
	 */
	public function productDelete($id){
		$product_db = D('product');
		$result = $product_db->where(array('id'=>$id))->delete();
		
		if ($result){
			$this->success('删除成功');
		}else {
			$this->error('删除失败');
		}
	}


	/**
	* 选择产品combobox
	*/
	public function public_selectProduct(){
		$product_db = D('product');
		$productList = $product_db->field(array('id', 'name'))->select();
		for($i = 0; $i < count($productList); $i++){
			$productList[$i]['text'] = $productList[$i]['name'];
		}
		$this->ajaxReturn($productList);
	}

}