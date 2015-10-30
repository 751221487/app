<?php if (!defined('THINK_PATH')) exit();?><form>
	<table cellspacing="6" width="100%">
		<tr>
			<td width="90">上级地区：</td>
			<td><input class="easyui-combotree" data-options="url:'<?php echo U('Admin/public_areaSelectTree');?>'" name="info[parentid]" value="<?php echo ((isset($_GET['parentid']) && ($_GET['parentid'] !== ""))?($_GET['parentid']):0); ?>" style="width:230px;height:24px" /></td>
		</tr>
		<tr>
			<td>地区名称：</td>
			<td><input class="easyui-validatebox" data-options="required:true,validType:['length[2,20]','remote[\'<?php echo U('Admin/public_areaNameCheck');?>\',\'name\']']" name="info[name]" type="text" style="width:220px" /></td>
		</tr>
		<tr>
			<td>是否显示地区：</td>
			<td>
				<label><input name="info[display]" value="1" type="radio" checked />是</label>
				<label><input name="info[display]" value="0" type="radio" />否</label>
			</td>
		</tr>
	</table>
</form>