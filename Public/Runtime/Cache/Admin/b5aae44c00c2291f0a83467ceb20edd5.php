<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="/app/Public/static/js/ajaxFileUpload.js"></script>
<script>
	(function($){  
        $.fn.serializeJson=function(){  
            var serializeObj={};  
            $(this.serializeArray()).each(function(){  
                serializeObj[this.name]=this.value;  
            });  
            return serializeObj;  
        };  
    })(jQuery);
</script>
<table id="contract_contractlist_datagrid" class="easyui-datagrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => false, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'striped' => true, 'pagination' => true, 'pageList' => array ( 0 => 20, 1 => 30, 2 => 50, 3 => 80, 4 => 100, ), 'pageSize' => '20', ), $datagrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($datagrid["options"]['toolbar']) && substr($datagrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$datagrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($datagrid["fields"])):foreach ($datagrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($datagrid["fields"][$key]['formatter'])?",\"formatter\":".$datagrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>

<div id="contract-contractlist-datagrid-toolbar" style="padding:1px;height:auto">
	<form style="border-bottom:1px solid #ddd;margin-bottom:1px;padding:5px">
		投入时间: <input name="search[begin]" class="easyui-datebox" style="width:100px">
		至: <input name="search[end]" class="easyui-datebox" style="width:100px">
		客户名:
		<input type="text" name="search[name]" style="width:100px;padding:2px"/>
		电话:
		<input type="text" name="search[tel]" style="width:100px;padding:2px"/>

		<a href="javascript:;" onclick="contractContractModule.search(this)" class="easyui-linkbutton" iconCls="icons-table-table">搜索</a>
	</form>
	<div>
		<?php if($admin["position"] == '财务'): ?><a href="javascript:;" class="easyui-linkbutton" data-options="plain:true,iconCls:'icons-table-table_add'" onclick="contractContractModule.add()">添加合同</a><?php endif; ?>
		<a href="javascript:;" class="easyui-linkbutton" data-options="plain:true,iconCls:'icons-table-table_refresh'" onclick="contractContractModule.refresh()">刷新</a>
	</div>
</div>

<script type="text/javascript">
var contractContractModule = {
	dialog:   '#globel-dialog-div',
	datagrid: '#contract_contractlist_datagrid',

	
	//操作格式化
	operate: function(val){
		var btn = [];
		btn.push('<a href="javascript:;" onclick="contractContractModule.detail('+val+')">详情</a>');
		<?php if($admin["position"] == '财务'): ?>btn.push('<a href="javascript:;" onclick="contractContractModule.edit('+val+')">编辑</a>');
		btn.push('<a href="javascript:;" onclick="contractContractModule.delete('+val+')">删除</a>');<?php endif; ?>
		return btn.join(' | ');
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
	
	<?php if($admin["position"] == '财务'): ?>//添加
	add: function(){
		var that = this;
		$(that.dialog).dialog({
			title: '添加合同',
			iconCls: 'icons-application-application_add',
			width: 600,
			height: 520,
			cache: false,
			href: '<?php echo U('Contract/contractAdd');?>',
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
							$.ajaxFileUpload({
								url : '<?php echo U('Contract/contractAdd?dosubmit=1');?>', //你处理上传文件的服务端
								secureuri : false,
								fileElementId : ['idcard', 'bankcard', 'contract_file'],
								dataType : 'jsonp',
								data : $(this).serializeJson(),
								success : function(data, status){
									data = JSON.parse(data);
									if(data.status == 1){
										$.app.method.tip('提示信息', res.info, 'info');
										$(that.dialog).dialog('close');
										that.refresh();
									} else {
										$.app.method.tip('提示信息', res.info, 'error');
									}
								},
								error: function(data, status){
									return;
								}
							});
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
	},<?php endif; ?>

	//详情
	detail: function(id){
		if(typeof(id) !== 'number'){
			$.app.method.tip('提示信息', '未选择合同', 'error');
			return false;
		}
		var href = '<?php echo U('Contract/contractDetail');?>';
		href += href.indexOf('?') != -1 ? '&id='+id : '?id='+id;
		var that = this;
		$(that.dialog).dialog({
			title: '合同详情',
			iconCls: 'icons-application-application_edit',
			width: 700,
			height: 620,
			cache: false,
			href: href,
			modal: true,
			collapsible: false,
			minimizable: false,
			resizable: false,
			maximizable: false,
			buttons:[{
				text:'确定',
				iconCls:'icons-arrow-cross',
				handler: function(){
					$(that.dialog).dialog('close');
				}
			}]
		});
	},
	

	<?php if($admin["position"] == '财务'): ?>//编辑
	edit: function(id){
		if(typeof(id) !== 'number'){
			$.app.method.tip('提示信息', '未选择合同', 'error');
			return false;
		}
		var href = '<?php echo U('Contract/contractEdit');?>';
		href += href.indexOf('?') != -1 ? '&id='+id : '?id='+id;
		var that = this;
		$(that.dialog).dialog({
			title: '编辑合同',
			iconCls: 'icons-application-application_edit',
			width: 600,
			height: 520,
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
				handler: function(){
					$(that.dialog).find('form').eq(0).form('submit', {
						onSubmit: function(){
							var isValid = $(this).form('validate');
							if (!isValid) return false;
							
							var action = '<?php echo U('Contract/contractEdit?dosubmit=1');?>';
							action += action.indexOf('?') != -1 ? '&id='+id : '?id='+id;
							
							$.messager.progress({text:'处理中，请稍候...'});
							$.ajaxFileUpload({
								url : action, //你处理上传文件的服务端
								secureuri : false,
								fileElementId : ['idcard', 'bankcard', 'contract_file'],
								dataType : 'jsonp',
								data : $(this).serializeJson(),
								success : function(data, status){
									data = JSON.parse(data);
									if(data.status == 1){
										$.app.method.tip('提示信息', res.info, 'info');
										$(that.dialog).dialog('close');
										that.refresh();
									} else {
										$.app.method.tip('提示信息', res.info, 'error');
									}
								},
								error: function(data, status){
									return;
								}
							});
							
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
	
	//删除
	delete: function(id){
		if(typeof(id) !== 'number'){
			$.app.method.tip('提示信息', '未选择客户', 'error');
			return false;
		}
		var that = this;
		$.messager.confirm('提示信息', '确定要删除吗？', function(result){
			if(!result) return false;
			
			$.messager.progress({text:'处理中，请稍候...'});
			$.post('<?php echo U('Member/memberDelete');?>', {id: id}, function(res){
				$.messager.progress('close');
				
				if(!res.status){
					$.app.method.tip('提示信息', res.info, 'error');
				}else{
					$.app.method.tip('提示信息', res.info, 'info');
					that.refresh();
				}
			}, 'json');
		});
	},<?php endif; ?>
	
};
</script>