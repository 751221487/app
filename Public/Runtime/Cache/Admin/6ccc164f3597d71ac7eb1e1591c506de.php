<?php if (!defined('THINK_PATH')) exit();?><form>
	<table cellspacing="10" width="100%">
		<tr>
			<td width="90">用户名：</td>
			<td><?php echo ($info["username"]); ?> (真实姓名： <?php echo ($info["realname"]); ?>)</td>
		</tr>
		<tr>
			<td>E-mail：</td>
			<td><?php echo ($info["email"]); ?></td>
		</tr>
		<tr>
			<td>旧密码：</td>
			<td><input class="easyui-validatebox" type="password"  name="old_password" data-options="required:true,validType:['length[6,20]','remote[\'<?php echo U('Admin/public_checkPassword');?>\',\'password\']']" style="width:180px" /></td>
		</tr>
		<tr>
			<td>新密码：</td>
			<td><input id="admin_public_editpwd_form_password" class="easyui-validatebox" type="password" name="new_password" data-options="required:true,validType:{length:[6,20]}" style="width:180px" /></td>
		</tr>
		<tr>
			<td>重复新密码：</td>
			<td><input class="easyui-validatebox" type="password" data-options="required:true,validType:'equals[\'#admin_public_editpwd_form_password\']'" style="width:180px" /></td>
		</tr>
	</table>
</form>