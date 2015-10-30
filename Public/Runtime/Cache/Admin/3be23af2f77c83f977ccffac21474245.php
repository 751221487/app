<?php if (!defined('THINK_PATH')) exit();?><form>
	<table cellspacing="6" width="100%">
		<tr>
			<td width="90">上级地区：</td>
			<td><input class="easyui-combotree" data-options="url:'<?php echo U('Admin/public_areaSelectTree');?>'" name="info[parentid]" value="<?php echo ($data["parentid"]); ?>" style="width:230px;height:24px" /></td>
		</tr>
		<tr>
			<td>地区名称：</td>
			<td><input class="easyui-validatebox" data-options="required:true,validType:['length[2,20]','remote[\'<?php echo U('Admin/public_areaNameCheck', array('default'=>$data['name']));?>\',\'name\']']" name="info[name]" value="<?php echo ($data["name"]); ?>" type="text" style="width:220px" /></td>
		</tr>
		<tr>
			<td>是否显示菜单：</td>
			<td>
				<label><input name="info[display]" value="1" type="radio" <?php if(($data["display"]) == "1"): ?>checked<?php endif; ?> />是</label>
				<label><input name="info[display]" value="0" type="radio" <?php if(($data["display"]) == "0"): ?>checked<?php endif; ?> />否</label>
			</td>
		</tr>
	</table>
</form>