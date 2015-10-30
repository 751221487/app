<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="/app/Public/static/js/jquery.cityselect.js"></script>
<script>
	$(function(){
		$("#city").citySelect({
			prov:"<?php echo ($info["province"]); ?>",
			city:"<?php echo ($info["place"]); ?>"
		}); 
	});
</script>
<style>
	tr{
		padding: 4px 0;
	}
	.easyui-validatebox, .easyui-datebox, .easyui-numberbox{
		width: 120px;
	}
</style>
<form>
	<table cellspacing="5" width="100%">
		<tr>
			<td>客户姓名：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[name]" value="<?php echo ($info["name"]); ?>">
			</td>
			<td>身份证：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[idcard]" value="<?php echo ($info["idcard"]); ?>">
			</td>
		</tr>
		<tr>
			<td>生日：</td>
			<td>
				<input type="text" class="easyui-datebox" data-options="required:true" name="info[birthday]" value="<?php echo ($info["birthday"]); ?>">
			</td>
			<td>性别：</td>
			<td>
				<select class="easyui-validatebox" data-options="required:true" name="info[gender]" value="<?php echo ($info["gender"]); ?>">
					<option value="1">男</option>
					<option value="2">女</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>籍贯：</td>
			<td colspan=3>
				<div id="city">
				<select class="easyui-validatebox prov" style="width:180px" name="info[province]"></select>  
				<select class="easyui-validatebox city" data-options="required:true" name="info[place]" style="width:180px"></select>
				</div>
			</td>
		</tr>
		<tr>
			<td>学历：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[education]" value="<?php echo ($info["education"]); ?>">
			</td>
			<td>从事行业：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[job]" value="<?php echo ($info["job"]); ?>">
			</td>
		</tr>
		<tr>
			<td>收入：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[income]" value="<?php echo ($info["income"]); ?>">
			</td>
			<td>联系电话：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[tel]" value="<?php echo ($info["tel"]); ?>">
			</td>
		</tr>
		<tr>
			<td>单位</td>
			<td colspan=3>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[corp]" style="width:360px" value="<?php echo ($info["corp"]); ?>">
			</td>
		</tr>
		<tr>
			<td>联系地址：</td>
			<td colspan=3>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[address]" style="width:360px" value="<?php echo ($info["address"]); ?>">
			</td>
		</tr>
		<tr>
			<td>客户来源：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[origin]" value="<?php echo ($info["origin"]); ?>">
			</td>
			<td>客户状态：</td>
			<td>
				<select type="text" class="easyui-validatebox" data-options="required:true" name="info[status]" value="<?php echo ($info["status"]); ?>">
					<option value="准客户">准客户</option>
					<option value="新客户">新客户</option>
					<option value="老客户">老客户</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>备注：</td>
			<td colspan=3>
				<textarea class="easyui-validatebox" name="info[remark]" style="width:360px"><?php echo ($info["remark"]); ?></textarea>
			</td>
		</tr>
	</table>
</form>