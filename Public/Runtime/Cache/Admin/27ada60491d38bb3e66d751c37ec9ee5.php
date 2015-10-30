<?php if (!defined('THINK_PATH')) exit();?><div class="easyui-panel" data-options="fit:true,title:'后台首页',border:false,onResize:function(){$('#index-main-portal').portal({border:false,fit:true});}">
	<div id="index-main-portal">
		<div style="width:50%">
			<div title="我的个人信息" collapsible="true" style="padding:8px;line-height:1.8;height: 132px;">
				您好，<?php echo ($userInfo["username"]); ?><br />
				所属角色：<?php echo ($userInfo["rolename"]); ?> <br />
				最后登录时间：<?php if($userInfo['lastlogintime'] > 0): echo (date('Y-m-d H:i:s',$userInfo["lastlogintime"])); endif; ?><br />
				最后登录IP：<?php echo ($userInfo["lastloginip"]); ?> <br />
			</div>

			<div title="近期登录" collapsible="true" style="padding:8px;line-height:1.8">
				<?php if(is_array($loginList)): foreach($loginList as $key=>$log): ?>[<?php echo ($log["time"]); ?>] 登录IP：<?php echo ($log["ip"]); ?><br/><?php endforeach; endif; ?>
			</div>
		</div>
		<div style="width:50%"></div>
	</div>
</div>