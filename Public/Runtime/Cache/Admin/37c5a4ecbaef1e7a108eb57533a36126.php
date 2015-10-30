<?php if (!defined('THINK_PATH')) exit();?><form>
	<table cellspacing="6" width="100%">
		<tr>
			<td width="90">角色名称：</td>
			<td><input class="easyui-validatebox" type="text" name="info[name]" value="<?php echo ($info["name"]); ?>" data-options="required:true,validType:['length[2,20]','remote[\'<?php echo U('Admin/public_checkJobName', array('default'=>$info['name']));?>\',\'name\']']" style="width:220px" /></td>
		</tr>
		<tr>
			<td>角色描述：</td>
			<td><textarea class="easyui-validatebox" name="info[description]" data-options="validType:{length:[0,200]}" style="width:220px;height:60px;font-size:12px"><?php echo ($info["description"]); ?></textarea></td>
		</tr>
		<tr>
			<td>业务目标：</td>
			<td>
				<input class="easyui-numberbox" type="text" name="info[target]" data-options="required:true" style="width:220px;" value="<?php echo ($info["target"]); ?>">万元
			</td>
		</tr>
		<tr>
			<td>业务期限：</td>
			<td>
				<input type="text" class="easyui-numberbox" name="info[time]" data-options="required:true" style="width:220px" value="<?php echo ($info["time"]); ?>">日
			</td>
		</tr>
	</table>
</form>