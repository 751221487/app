<?php if (!defined('THINK_PATH')) exit();?><form>
	<table cellspacing="6" width="100%">
		<tr>
			<td width="90">职位名称：</td>
			<td><input class="easyui-validatebox" type="text" name="info[name]" data-options="required:true,validType:['length[2,20]','remote[\'<?php echo U('Admin/public_checkJobName');?>\',\'name\']']" style="width:220px" /></td>
		</tr>
		<tr>
			<td>职位描述：</td>
			<td><textarea class="easyui-validatebox" name="info[description]" data-options="validType:{length:[0,200]}" style="width:220px;height:60px;font-size:12px"></textarea></td>
		</tr>
		<tr>
			<td>业务目标：</td>
			<td>
				<input class="easyui-validatebox" type="text" name="info[target]" data-options="required:true" style="width:220px;">
			</td>
		</tr>
		<tr>
			<td>业务期限：</td>
			<td>
				<input type="text" class="easyui-validatebox" name="info[time]" data-options="required:true" style="width:220px">天
			</td>
		</tr>
	</table>
</form>