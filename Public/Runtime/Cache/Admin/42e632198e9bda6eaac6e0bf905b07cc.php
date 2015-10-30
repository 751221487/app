<?php if (!defined('THINK_PATH')) exit();?>
<table id="admin_loginlog_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => false, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 20, 1 => 30, 2 => 50, 3 => 80, 4 => 100, ), 'pageSize' => '20', ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>

<div id="admin-loginlog-datagrid-toolbar" style="padding:5px;height:auto">
	<form>
		时 间: <input name="search[begin]" class="easyui-datebox" style="width:100px">
		至: <input name="search[end]" class="easyui-datebox" style="width:100px">
		
		<a href="javascript:;" onclick="adminLoginLogModule.search(this);" class="easyui-linkbutton" iconCls="icons-table-table">搜索</a>
		<a href="javascript:;" onclick="adminLoginLogModule.delete();" class="easyui-linkbutton" iconCls="icons-table-table_delete">删除一月前数据</a>
	</form>
</div>

<script type="text/javascript">
var adminLoginLogModule = {
	dialog:   '#globel-dialog-div',
	datagrid: '#admin_loginlog_datagrid',
	
	//刷新
	refresh: function(){
		$(this.datagrid).datagrid('reload');
	},
	
	//搜索
	search: function(that){
		var queryParams = $(this.datagrid).datagrid('options').queryParams;
		$.each($(that).parent('form').serializeArray(), function() {
			queryParams[this['name']] = this['value'];
		});
		$(this.datagrid).datagrid({pageNumber: 1});
	},
	
	//删除记录
	delete: function(){
		var that = this;
		$.messager.progress({text:'处理中，请稍候...'});
		$.post('<?php echo U('Admin/loginLogDelete');?>', {week: 4}, function(res){
			$.messager.progress('close');
			
			if(!res.status){
				$.app.method.tip('提示信息', res.info, 'error');
			}else{
				$.app.method.tip('提示信息', res.info, 'info');
				that.refresh();
			}
		}, 'json');
	}
}
</script>