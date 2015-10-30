<?php if (!defined('THINK_PATH')) exit();?>
<table id="system_loglist_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => false, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 20, 1 => 30, 2 => 50, 3 => 80, 4 => 100, ), 'pageSize' => '20', ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>

<div id="system-loglist-datagrid-toolbar" style="padding:5px;height:auto">
	<form>
		用户名: 
		<select name="search[username]" class="easyui-combobox" panelHeight="auto" style="width:100px">
			<option value="">全部用户</option>
			<?php if(is_array($list["admin"])): foreach($list["admin"] as $key=>$username): ?><option value="<?php echo ($username); ?>"><?php echo ($username); ?></option><?php endforeach; endif; ?>
		</select>
		模块: 
		<select name="search[controller]" class="easyui-combobox" panelHeight="auto" style="width:100px">
			<option value="">所有模块</option>
			<?php if(is_array($list["module"])): foreach($list["module"] as $key=>$module): ?><option value="<?php echo ($module); ?>"><?php echo ($module); ?></option><?php endforeach; endif; ?>
		</select>
		时 间: <input name="search[begin]" class="easyui-datebox" style="width:100px">
		至: <input name="search[end]" class="easyui-datebox" style="width:100px">
		
		<a href="javascript:;" onclick="systemLogModule.search(this);" class="easyui-linkbutton" iconCls="icons-table-table">搜索</a>
		<a href="javascript:;" onclick="systemLogModule.delete();" class="easyui-linkbutton" iconCls="icons-table-table_delete">删除一月前数据</a>
	</form>
</div>

<script type="text/javascript">
var systemLogModule = {
	dialog:   '#globel-dialog-div',
	datagrid: '#system_loglist_datagrid',
	
	//详细参数格式化
	querystring: function(val){
		return '<a href="javascript:;" onclick="systemLogModule.view(this);">'+val+'</a>';
	},
	
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
	
	//删除
	delete: function(){
		$.messager.progress({text:'处理中，请稍候...'});
		$.post('<?php echo U('System/logDelete');?>', {week: 4}, function(res){
			$.messager.progress('close');
			
			if(!res.status){
				$.app.method.tip('提示信息', res.info, 'error');
			}else{
				$('#'+system_loglist_datagrid_id).datagrid('reload');
				$.app.method.tip('提示信息', res.info, 'info');
			}
		}, 'json');
	},
	
	//查看
	view: function(object){
		var that = this;
		var content = $(object).html();
		content = this.formatter(content);
		
		$(that.dialog).dialog({
			title: '详细参数',
			iconCls: 'icons-application-application_view_detail',
			width: 380,
			height: 300,
			cache: false,
			href: null,
			content: '<pre>' + content + '</pre>',
			modal: true,
			collapsible: false,
			minimizable: false,
			resizable: true,
			maximizable: true,
			buttons:[{
				text:'关闭',
				iconCls:'icons-arrow-cross',
				handler: function(){
					$(that.dialog).dialog('close');
				}
			}]
		});
	},
	
	//json格式化
	formatter: function(string){
		var result      = '',
			pos         = 0,
			prevChar    = '',
			outOfQuotes = true;
		
		for(var i=0; i<string.length; i++){
			var char = string.substring(i, i+1);
			if (char == '"' && prevChar != '\\') {
				outOfQuotes = !outOfQuotes;
			}else if((char == '}' || char == ']') && outOfQuotes) {
				result += "\n";
				pos--;
				for (j=0; j<pos; j++) result += '  ';
			}
			result += char;
			if ((char == ',' || char == '{' || char == '[') && outOfQuotes) {
				result += "\n";
				if (char == '{' || char == '[') pos++;
				for (j = 0; j < pos; j++) result += '  ';
			}
			prevChar = char;
		}
		return result;
	}
};
</script>