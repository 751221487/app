<?php if (!defined('THINK_PATH')) exit();?><form>
	<table cellspacing="10" width="100%">
		<tr>
			<td width="90">用户名：</td>
			<td><?php echo ($info["username"]); ?></td>
		</tr>
		<tr>
			<td>最后登录时间</td> 
			<td><?php if(($info["lastlogintime"]) > "0"): echo (date('Y-m-d H:i:s',$info["lastlogintime"])); else: ?>-<?php endif; ?></td>
		</tr>
		<tr>
			<td>最后登录IP</td> 
			<td><?php echo ((isset($info["lastloginip"]) && ($info["lastloginip"] !== ""))?($info["lastloginip"]):'-'); ?></td>
		</tr>
		<tr>
			<td>真实姓名</td>
			<td><input class="easyui-validatebox" type="text" name="info[realname]" value="<?php echo ($info["realname"]); ?>" data-options="required:true,validType:{length:[2,20]}" style="width:180px" /></td>
		</tr>
		<tr>
			<td>E-mail：</td>
			<td><input class="easyui-validatebox" type="text" name="info[email]" value="<?php echo ($info["email"]); ?>" data-options="required:true,validType:['email','length[3,40]','remote[\'<?php echo U('Admin/public_checkEmail', array('default'=>$info['email']));?>\',\'email\']']" style="width:180px" /></td>
		</tr>
	</table>
</form>