<?php if (!defined('THINK_PATH')) exit();?>
<table id="admin_memberlist_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => false, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 20, 1 => 30, 2 => 50, 3 => 80, 4 => 100, ), 'pageSize' => '20', ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>

<script type="text/javascript">
var adminMemberModule = {
	dialog:   '#globel-dialog-div',
	datagrid: '#admin_memberlist_datagrid',
	
	//工具栏
	toolbar: [
		{ text: '添加员工', iconCls: 'icons-table-table_add', handler: function(){adminMemberModule.add();}},
		{ text: '刷新', iconCls: 'icons-table-table_refresh', handler: function(){adminMemberModule.refresh();}}
	],
	
	//时间字段格式化
	time: function(val){
		return val != '1970-01-01 08:00:00' ? val : '';
	},

	target: function(val){
		return val + '万元';
	},
	
	//操作字段格式化
	operate: function(val){
		var btn = [];
		btn.push('<a href="javascript:;" onclick="adminMemberModule.edit('+val+')">编辑员工</a>');
		btn.push('<a href="javascript:;" onclick="adminMemberModule.password('+val+')">重置密码</a>');
		btn.push('<a href="javascript:;" onclick="adminMemberModule.delete('+val+')">删除</a>');
		return btn.join(' | ');
	},
	
	//刷新列表
	refresh: function(){
		$(this.datagrid).datagrid('reload');
	},
	
	//添加员工
	add: function(){
		var that = this;
		$(that.dialog).dialog({
			title: '添加员工',
			iconCls: 'icons-application-application_add',
			width: 370,
			height: 460,
			cache: false,
			href: '<?php echo U('Admin/memberAdd');?>',
			modal: true,
			collapsible: false,
			minimizable: false,
			resizable: false,
			maximizable: false,
			buttons:[{
				text:'确定',
				iconCls:'icons-other-tick',
				handler: function(){
					$(that.dialog).find('form').eq(0).form('submit', {
						onSubmit: function(){
							var isValid = $(this).form('validate');
							if (!isValid) return false;
							
							$.messager.progress({text:'处理中，请稍候...'});
							$.post('<?php echo U('Admin/memberAdd?dosubmit=1');?>', $(this).serialize(), function(res){
								$.messager.progress('close');
								if(!res.status){
									$.app.method.tip('提示信息', res.info, 'error');
								}else{
									$.app.method.tip('提示信息', res.info, 'info');
									$(that.dialog).dialog('close');
									that.refresh();
								}
							}, 'json');
							
							return false;
						}
					});
				}
			},{
				text:'取消',
				iconCls:'icons-arrow-cross',
				handler: function(){
					$(that.dialog).dialog('close');
				}
			}]
		});
	},
	
	//编辑员工
	edit: function(id){
		if(typeof(id) !== 'number'){
			$.app.method.tip('提示信息', '未选择员工', 'error');
			return false;
		}
		var href = '<?php echo U('Admin/memberEdit');?>';
		href += href.indexOf('?') != -1 ? '&id='+id : '?id='+id;

		var that = this;
		$(that.dialog).dialog({
			title: '编辑员工',
			iconCls: 'icons-application-application_edit',
			width: 370,
			height: 460,
			cache: false,
			href: href,
			modal: true,
			collapsible: false,
			minimizable: false,
			resizable: false,
			maximizable: false,
			buttons:[{
				text:'确定',
				iconCls:'icons-other-tick',
				handler:function(){
					$(that.dialog).find('form').eq(0).form('submit', {
						onSubmit: function(){
							var isValid = $(this).form('validate');
							if (!isValid) return false;
							
							$.messager.progress({text:'处理中，请稍候...'});
							var action = '<?php echo U('Admin/memberEdit', array('dosubmit'=>1));?>';
							action += action.indexOf('?') != -1 ? '&id='+id : '?id='+id;
							$.post(action, $(this).serialize(), function(res){
								$.messager.progress('close');
								if(!res.status){
									$.app.method.tip('提示信息', res.info, 'error');
								}else{
									$.app.method.tip('提示信息', res.info, 'info');
									$(that.dialog).dialog('close');
									that.refresh();
								}
							}, 'json');
							
							return false;
						}
					});
				}
			},{
				text: '取消',
				iconCls: 'icons-arrow-cross',
				handler: function(){
					$(that.dialog).dialog('close');
				}
			}]
		});
	},
	
	//删除员工
	delete: function(id){
		if(typeof(id) !== 'number'){
			$.app.method.tip('提示信息', '未选择员工', 'error');
			return false;
		}
		
		var that = this;
		$.messager.confirm('提示信息', '确定要删除吗？', function(result){
			if(!result) return false;
			
			$.messager.progress({text:'处理中，请稍候...'});
			$.post('<?php echo U('Admin/memberDelete');?>', {id: id}, function(res){
				$.messager.progress('close');
				
				if(!res.status){
					$.app.method.tip('提示信息', res.info, 'error');
				}else{
					$.app.method.tip('提示信息', res.info, 'info');
					that.refresh();
				}
			}, 'json');
		});
	},
	
	//重置密码
	password: function(id){
		if(typeof(id) !== 'number'){
			$.app.method.tip('提示信息', '未选择员工', 'error');
			return false;
		}
		
		var that = this;
		$.messager.confirm('提示信息', '确定要重置密码吗？', function(result){
			if(!result) return false;
			
			$.messager.progress({text:'处理中，请稍候...'});
			$.post('<?php echo U('Admin/memberResetPassword');?>', {id: id}, function(res){
				$.messager.progress('close');
				
				if(!res.status){
					$.app.method.tip('提示信息', res.info, 'error');
				}else{
					$.app.method.tip('提示信息', res.info, 'info');
					$.messager.alert('提示信息', '密码已重置为：' + res.password + '，请牢记新密码！', 'info');
				}
			}, 'json');
		});
	}
}
</script>