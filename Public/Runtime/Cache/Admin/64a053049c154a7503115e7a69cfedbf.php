<?php if (!defined('THINK_PATH')) exit();?><form>
	<table cellspacing="6" width="100%">
		<tr>
			<td width="90">工号：</td>
			<td><input class="easyui-validatebox" type="text" name="info[username]" data-options="required:true,validType:['length[2,20]','remote[\'<?php echo U('Admin/public_checkName');?>\',\'name\']']" style="width:200px" /></td>
		</tr>
		<tr>
			<td>密码：</td>
			<td>123456</td>
		</tr>
		<tr>
			<td>真实姓名：</td>
			<td><input class="easyui-validatebox" type="text" name="info[realname]" data-options="required:true,validType:{length:[2,20]}" style="width:200px" /></td>
		</tr>
		<tr>
			<td>入职时间：</td>
			<td><input class="easyui-datebox" type="text" name="info[join_time]" data-options="required:true,validType:{length:[2,20]}" style="width:200px" /></td>
		</tr>
		<tr>
			<td>地区：</td>
			<td><input class="easyui-combotree" data-options="url:'<?php echo U('Admin/public_areaSelectTree');?>'" name="info[area]" style="width:200px"/></td>
		</tr>
		<tr>
			<td>职位：</td>
			<td>
				<select class="easyui-combobox" data-options="required:true" name="info[position]" style="width:200px;">
					<option value="总经理">总经理</option>
					<option value="副总经理">副总经理</option>
					<option value="财务">财务</option>
					<option value="业务发展经理">业务发展经理</option>
					<option value="区域经理">区域经理</option>
					<option value="总监">总监</option>
					<option value="营业部经理">营业部经理</option>
					<option value="理财顾问">理财顾问</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>状态：</td>
			<td>
				<select class="easyui-combobox" data-options="editable:false,panelHeight:'auto'" name="info[job]" style="width:200px;">
					<?php if(is_array($jobList)): foreach($jobList as $key=>$job): ?><option value="<?php echo ($job["id"]); ?>"><?php echo ($job["name"]); ?></option><?php endforeach; endif; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>权限：</td>
			<td>
				<select class="easyui-combobox" data-options="editable:false,panelHeight:'auto'" name="info[permission]" style="width:200px;">
					<option value="1">可查看所在区域全部员工</option>
					<option value="2">只能查看自己信息</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>E-mail：</td>
			<td><input class="easyui-validatebox" type="text" name="info[email]" data-options="required:true,validType:['email','length[3,40]','remote[\'<?php echo U('Admin/public_checkEmail');?>\',\'email\']']" style="width:200px" /></td>
		</tr>
		<tr>
			<td>电话：</td>
			<td><input class="easyui-validatebox" type="text" name="info[tel]" data-options="required:true" style="width:200px" /></td>
		</tr>
		<tr>
			<td>备注：</td>
			<td>
				<textarea class="easyui-validatebox" name="info[remark]" style="width:200px"></textarea>
			</td>
		</tr>
	</table>
</form>