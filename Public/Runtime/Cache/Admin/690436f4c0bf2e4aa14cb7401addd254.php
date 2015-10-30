<?php if (!defined('THINK_PATH')) exit();?>
<table id="database_importlist_treegrid" class="easyui-treegrid" data-options='<?php $dataOptions = array_merge(array ( 'border' => false, 'fit' => true, 'fitColumns' => true, 'rownumbers' => true, 'singleSelect' => true, 'animate' => true, ), $treegrid["options"]);if(isset($dataOptions['toolbar']) && substr($dataOptions['toolbar'],0,1) != '#'): unset($dataOptions['toolbar']); endif; echo trim(json_encode($dataOptions), '{}[]').((isset($treegrid["options"]['toolbar']) && substr($treegrid["options"]['toolbar'],0,1) != '#')?',"toolbar":'.$treegrid["options"]['toolbar']:null); ?>' style=""><thead><tr><?php if(is_array($treegrid["fields"])):foreach ($treegrid["fields"] as $key=>$arr):if(isset($arr['formatter'])):unset($arr['formatter']);endif;echo "<th data-options='".trim(json_encode($arr), '{}[]').(isset($treegrid["fields"][$key]['formatter'])?",\"formatter\":".$treegrid["fields"][$key]['formatter']:null)."'>".$key."</th>";endforeach;endif; ?></tr></thead></table>

<script type="text/javascript">
var databaseImportModule = {
	  dialog: '#globel-dialog-div',
	treegrid: '#database_importlist_treegrid',
	//操作格式化
	operate: function(time,row){
		var btn = [];
		btn.push("<a href='javascript:;' time='"+time+"' onclick='databaseImportModule.import(this)'>还原</a>");
		btn.push("<a href='javascript:;' time='"+time+"' onclick='databaseImportModule.del(this)'>删除</a>");
		return btn.join(' | ');
	},
	//还原备份数据
	import:function(that){
		var time = $(that).attr("time");
		var href = "<?php echo U('Database/import');?>"+"?time="+time;
		$.messager.confirm('提示信息', '确定要开始还原数据吗？', function(result){
			if(!result) return false;
			$.messager.progress({text:"开始还原数据"});
			$.get(href,success, 'json');
		});
		
		function success(data){
			if(data.status){
				if(data.gz){
					//启用了压缩,无法获知具体的总数量,模拟一个progressbar
					$($.messager.progress("bar")).progressbar({text:data.info});
				}else{
					//未启用压缩
					$($.messager.progress("bar")).progressbar({text:data.info,value:data.rate});
				}
	            if(data.part){
	            	$.get(href,{"part":data.part,"start":data.start},success,"json");
	            }else{
	            	$.app.method.tip('提示信息', data.info, 'info');
	            	$.messager.progress("close");
	            }
	        } else {
	            $.app.method.tip('错误信息', data.info, 'error');
	            $.messager.progress("close");
	        }
		}
	},
	//删除备份数据
	del:function(that){
		var time = $(that).attr("time");
		$.messager.confirm('提示信息', '确定要删除该备份吗？', function(result){
			if(!result) return false;
			$.messager.progress({text:'处理中，请稍候...'});
			$.post("<?php echo U('Database/del');?>", {time: time}, function(res){
				$.messager.progress('close');
				if(!res.status){
					$.app.method.tip('错误信息', res.info, 'error');
				}else{
					$.app.method.tip('提示信息', res.info, 'info');
					databaseImportModule.refresh();
				}
			}, 'json');
		});
	},
	//刷新
	refresh: function(){
		$(this.treegrid).treegrid('reload');
	}
};
</script>