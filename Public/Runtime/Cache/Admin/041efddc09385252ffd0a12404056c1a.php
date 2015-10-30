<?php if (!defined('THINK_PATH')) exit();?>
<table id="database_exportlist_treegrid" class="easyui-treegrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => false, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'animate' => true, ), $treegrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($treegrid["options"]['toolbar']) && substr($treegrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$treegrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($treegrid["fields"])):foreach ($treegrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($treegrid["fields"][$key]['formatter'])?",\"formatter\":".$treegrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>

<script type="text/javascript">
var databaseExprotModule = {
	  dialog: '#globel-dialog-div',
	treegrid: '#database_exportlist_treegrid',
	
	//工具栏
	toolbar: [
		{ text: '立即备份', iconCls: 'icons-other-database_save', handler: function(){databaseExprotModule.export()}},
		{ text: '优化表', iconCls: 'icons-other-database_lightning', handler: function(){databaseExprotModule.optimizeOrRepairAll("<?php echo U('Database/optimize');?>")}},
		{ text: '修复表', iconCls: 'icons-other-database_refresh', handler: function(){databaseExprotModule.optimizeOrRepairAll("<?php echo U('Database/repair');?>")}},
		{ text: '刷新', iconCls: 'icons-arrow-arrow_refresh', handler: function(){databaseExprotModule.refresh()}}
	],
	
	//操作格式化
	operate: function(name,row){
		var btn = [];
		btn.push("<a href='javascript:;' edit='optimize' tablesname='"+row.name+"' onclick='databaseExprotModule.optimizeOrRepair(this)'>优化表</a>");
		btn.push("<a href='javascript:;' edit='repair' tablesname='"+row.name+"' onclick='databaseExprotModule.optimizeOrRepair(this)'>修复表</a>");
		return btn.join(' | ');
	},
	
	progressFormatter:function(value,rowData,rowIndex){
		return '<div  class="easyui-progressbar" style="width:100%" value="'+value+'"></div>';
	},
	
	//优化修复表
	optimizeOrRepair:function(that){
		var type = $(that).attr("edit");
		var tables = $(that).attr("tablesname");
		var url = type=="optimize"?"<?php echo U('Database/optimize');?>":"<?php echo U('Database/repair');?>";
		$.messager.confirm('提示信息', '确定要继续吗？', function(result){
			if(!result) return false;
			$.messager.progress({text:'处理中，请稍候...'});
			$.post(url, {tables: tables}, function(res){
				$.messager.progress('close');
				if(!res.status){
					$.app.method.tip('提示信息', res.info, 'error');
				}else{
					$.app.method.tip('提示信息', res.info, 'info');
				}
			}, 'json');
		});
	},
	
	//优化与修复所有表
	optimizeOrRepairAll:function(href){
		var that = this;
		$.messager.confirm('提示信息', '确定要继续吗？', function(result){
			if(!result) return false;
			$.messager.progress({text:'处理中，请稍候...'});
			$.post(href, $(that.treegrid).parent('div').find('input[name="tables[]"]').serialize() , function(res){
				$.messager.progress('close');
				if(!res.status){
					$.app.method.tip('错误信息', res.info, 'error');
				}else{
					$.app.method.tip('提示信息', res.info, 'info');
				}
			}, 'json');
		});
	},
	
	//备份数据
	export:function(){
		var that = this;
		$.messager.confirm('提示信息', '确定要开始备份数据吗？', function(result){
			$.messager.progress({text:'正在发送备份请求...'});
			$.post(
				"<?php echo U('export');?>",
				$(that.treegrid).parent('div').find('input[name="tables[]"]').serialize(),
				function(data){
					$.messager.progress('close');
			  		if(data.status){
						$.app.method.tip('提示信息', data.info, 'info');
					    that.backup(data.tab);
			  			window.onbeforeunload = function(){ return "正在备份数据库，请不要关闭！" }
			  		} else {
			  			$.app.method.tip('错误信息', data.info, 'error');
					}
			},"json");
			return false;
		});
	},
	backup:function(tab, status){
		var that = this;
		status && that.showmsg(tab.id, "0% 开始备份");
        $.get("<?php echo U('Database/export');?>", tab, function(data){
            if(data.status){
                that.showmsg(tab.id, data.info);
                if(!$.isPlainObject(data.tab)){
                    $.app.method.tip('提示信息', "备份完成!", 'info');
                    window.onbeforeunload = function(){ return null }
                    return;
                }
                that.backup(data.tab, tab.id != data.tab.id);
            } else {
                $.app.method.tip('错误信息', data.info, 'error');
            }
        }, "json");
	},
	showmsg:function(id, msg){
		msg = msg.split(" ");
		$(this.treegrid).prev().find("input:checked").parent().parent().parent().find('.easyui-progressbar').eq(id).progressbar({text:msg[0]+'% '+msg[1],value:msg[0]});
	},
	
	//刷新
	refresh: function(){
		$(this.treegrid).treegrid('reload');
	}
};
$(databaseExprotModule.treegrid).treegrid({
	onLoadSuccess: function(data){
		$("."+$(this).treegrid("getColumnOption","progress").cellClass).children(".easyui-progressbar").progressbar({text:'0% 未备份'});
	}
});
</script>