<?php if (!defined('THINK_PATH')) exit();?><style>
	tr{
		padding: 4px 0;
	}
	img{
		width: 180px;
		height: 180px;
	}
	td{
		height: 30px;
		text-align: center;
	}
</style>
<form>
	<table cellspacing="5" width="100%" border=1 style="border-collapse: collapse;">
		<tr>
			<td>合同编号</td>
			<td>
				<?php echo ($info["code"]); ?>
			</td>
			<td>产品种类</td>
			<td>
				<?php echo ($info["product"]); ?>
			</td>
		</tr>
		<tr>
			<td>投资金额</td>
			<td>
				<?php echo ($info["money"]); ?>元
			</td>
			<td>签单日</td>
			<td>
				<?php echo ($info["create_date"]); ?>
			</td>
		</tr>
		<tr>
			<td>收益率</td>
			<td>
				<?php echo ($info["income_rate"]); ?>％
			</td>
			<td>投资期限</td>
			<td>
				<?php echo ($info["time_limit"]); ?>年
			</td>
		</tr>
		<tr>
			<td>每期收益</td>
			<td>
				<?php echo ($info["income"]); ?>元
			</td>
			<td>收益支付周期</td>
			<td>
				<?php echo ($info["income_cycle"]); ?>月
			</td>
		</tr>
		<tr>
			<td>满期日</td>
			<td>
				<?php echo ($info["time_finish"]); ?>
			</td>
			<td>客户</td>
			<td>
				<?php echo ($info["customer"]); ?>
			</td>
		</tr>
<!-- 	财务可以查看	
		<tr>
			<td>身份证号码</td>
			<td>
				<?php echo ($info["idnumber"]); ?>
			</td>
			<td>银行卡号码</td>
			<td>
				<?php echo ($info["banknumber"]); ?>
			</td>
		</tr>
		<tr>
			<td>身份证</td>
			<td>
				<img src="/app/Public/upload/<?php echo ($info["idcard"]); ?>" alt="">
			</td>
			<td>银行卡</td>
			<td>
				<img src="/app/Public/upload/<?php echo ($info["bankcard"]); ?>" alt="">
			</td>
		</tr> -->
		<tr>
			<td>到账日</td>
			<td>
				<?php echo ($info["arrive_date"]); ?>
			</td>
			<td>总收益</td>
			<td>
				<?php echo ($info["total_income"]); ?>
			</td>
		</tr>
		<tr>
			<td>是否浮动</td>
			<td>
				<?php echo ($info["is_float"]); ?>
			</td>
			<td>浮动收益</td>
			<td>
				<?php echo ($info["float_income"]); ?>
			</td>
		</tr>
		<tr>
			<td>开户行</td>
			<td>
				<?php echo ($info["bank"]); ?>
			</td>
			<td>已付期数</td>
			<td>
				<?php echo ($info["paid_finish"]); ?>
			</td>
		</tr>
		<tr>
			<td>紧急联系人</td>
			<td>
				<?php echo ($info["emerge_person"]); ?>
			</td>
			<td>紧急联系方式</td>
			<td>
				<?php echo ($info["emerge_tel"]); ?>
			</td>
		</tr>
		<tr>
			<td>合同电子档</td>
			<td>
				<a href="javascript:" onclick="window.open('/app/Public/upload/<?php echo ($info["contract_file"]); ?>')">下载</a>
			</td>
			<td>理财顾问</td>
			<td>
				<?php echo ($info["charge"]); ?>
			</td>
		</tr>
		<tr>
			<td>备注</td>
			<td colspan=3>
				<?php echo ($info["remark"]); ?>
			</td>
		</tr>
	</table>
</form>