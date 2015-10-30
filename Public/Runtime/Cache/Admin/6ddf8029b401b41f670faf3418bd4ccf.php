<?php if (!defined('THINK_PATH')) exit();?><style>
	tr{
		padding: 4px 0;
	}
	.easyui-validatebox, .easyui-datebox, .easyui-numberbox, .easyui-filebox, input[type='file'], .easyui-combobox{
		width: 150px;
	}

</style>
<script>
	$(function(){
		$('#charge').combobox({
			valueField: 'id',
        	textField: 'text',
			url : '<?php echo U('Admin/public_selectAdmin');?>',
			onLoadSuccess : function(){
				$('#charge').combobox('setValue', '<?php echo ($info["user"]); ?>');
			}
		});
	})
</script>
<form>
	<table cellspacing="5" width="100%">
		<tr>
			<td>合同编号</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[code]" value="<?php echo ($info["code"]); ?>">
			</td>
			<td>产品种类：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[product]" value="<?php echo ($info["product"]); ?>">
			</td>
		</tr>
		<tr>
			<td>投资金额：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[money]" value="<?php echo ($info["money"]); ?>">
			</td>
			<td>签单日：</td>
			<td>
				<input type="text" class="easyui-datebox" data-options="required:true" name="info[create_date]" value="<?php echo ($info["create_date"]); ?>">
			</td>
		</tr>
		<tr>
			<td>收益率：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[income_rate]" value="<?php echo ($info["income_rate"]); ?>">％
			</td>
			<td>投资期限：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[time_limit]" value="<?php echo ($info["time_limit"]); ?>">年
			</td>
		</tr>
		<tr>
			<td>每期收益：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[income]" value="<?php echo ($info["income"]); ?>">元
			</td>
			<td>收益支付周期：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[income_cycle]" value="<?php echo ($info["income_cycle"]); ?>">月
			</td>
		</tr>
		<tr>
			<td>满期日：</td>
			<td>
				<input type="text" class="easyui-datebox" data-options="required:true" name="info[time_finish]" value="<?php echo ($info["time_finish"]); ?>">
			</td>
			<td>客户：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[customer]" value="<?php echo ($info["customer"]); ?>">
			</td>
		</tr>
		<tr>
			<td>身份证号码：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[idnumber]" value="<?php echo ($info["idnumber"]); ?>">
			</td>
			<td>银行卡号码：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[banknumber]" value="<?php echo ($info["banknumber"]); ?>">
			</td>
		</tr>
		<tr>
			<td>身份证：</td>
			<td>
				<input type="file" name="idcard" id="idcard">
			</td>
			<td>银行卡：</td>
			<td>
				<input type="file" name="bankcard" id="bankcard">
			</td>
		</tr>
		<tr>
			<td>到账日：</td>
			<td>
				<input type="text" class="easyui-datebox" data-options="required:true" name="info[arrive_date]" value="<?php echo ($info["arrive_date"]); ?>">
			</td>
			<td>总收益：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[total_income]" value="<?php echo ($info["total_income"]); ?>">元
			</td>
		</tr>
		<tr>
			<td>是否浮动</td>
			<td>
				<select class="easyui-combobox" name="info[is_float]" value="<?php echo ($info["is_float"]); ?>">
					<option value="0">否</option>
					<option value="1">是</option>
				</select>
			</td>
			<td>浮动收益</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[float_income]" value="<?php echo ($info["float_income"]); ?>">元
			</td>
		</tr>
		<tr>
			<td>开户行：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[bank]" value="<?php echo ($info["bank"]); ?>">
			</td>
			<td>已付期数：</td>
			<td>
				<input type="text" class="easyui-numberbox" data-options="required:true" name="info[paid_finish]" value="<?php echo ($info["paid_finish"]); ?>">
			</td>
		</tr>
		<tr>
			<td>紧急联系人：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[emerge_person]" value="<?php echo ($info["emerge_person"]); ?>">
			</td>
			<td>紧急联系方式：</td>
			<td>
				<input type="text" class="easyui-validatebox" data-options="required:true" name="info[emerge_tel]" value="<?php echo ($info["emerge_tel"]); ?>">
			</td>
		</tr>
		<tr>
			<td>合同电子档</td>
			<td colspan=3>
				<input type="file" name="contract_file" id="contract_file" style="width:450px">
			</td>
		</tr>
		<tr>
			<td>理财顾问：</td>
			<td colspan=3>
				<select type="text" id="charge" class="easyui-combobox" name="info[user]" style="width:450px">
				</select>
			</td>
		</tr>
		<tr>
			<td>备注：</td>
			<td colspan=3>
				<textarea class="easyui-validatebox" name="info[remark]" style="width:450px"><?php echo ($info["remark"]); ?></textarea>
			</td>
		</tr>
	</table>
</form>