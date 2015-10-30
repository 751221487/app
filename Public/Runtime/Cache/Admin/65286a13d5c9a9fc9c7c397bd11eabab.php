<?php if (!defined('THINK_PATH')) exit(); if(($sysinfo["sysReShow"]) == "show"): ?><style type="text/css">
		#index-system-info-dialog-tabs .bar {border:1px solid #999999; background:#FFFFFF; height:5px; font-size:2px; width:89%; margin:2px 0 5px 0;padding:1px;overflow: hidden;}
		#index-system-info-dialog-tabs .bar_1 {border:1px dotted #999999; background:#FFFFFF; height:5px; font-size:2px; width:89%; margin:2px 0 5px 0;padding:1px;overflow: hidden;}
		#index-system-info-dialog-tabs .barli_red{background:#ff6600; height:5px; margin:0px; padding:0;}
		#index-system-info-dialog-tabs .barli_blue{background:#0099FF; height:5px; margin:0px; padding:0;}
		#index-system-info-dialog-tabs .barli_green{background:#36b52a; height:5px; margin:0px; padding:0;}
		#index-system-info-dialog-tabs .barli_1{background:#999999; height:5px; margin:0px; padding:0;}
		#index-system-info-dialog-tabs .barli{background:#36b52a; height:5px; margin:0px; padding:0;}
	</style><?php endif; ?>

<div class="easyui-tabs" data-options="fit:true,border:false,plain:true">
	<div title="系统信息">
		<table class="easyui-propertygrid"  data-options="fit:true,border:false,scrollbarSize:0,columns:[[{field:'name',title:'属性',width:100},{field:'value',title:'参数',width:250}]],data: [
		{name:'服务器域名/IP地址', value:'<?php echo ($_SERVER['SERVER_NAME']); ?>(<?php if(DIRECTORY_SEPARATOR == '/'): echo ($_SERVER['SERVER_ADDR']); else: echo @gethostbyname($_SERVER['SERVER_NAME']); endif; ?>)'},
		{name:'服务器标识', value:<?php if($sysinfo['win_n'] != ''): echo var_export($sysinfo['win_n'], true); else: echo var_export(@php_uname(), true); endif; ?>},
		{name:'服务器操作系统', value:<?php echo var_export($os[0] . '&nbsp;内核版本：' . (DIRECTORY_SEPARATOR == '/' ? $os[2] : $os[1]) , true);?>},
		{name:'服务器解译引擎', value:<?php echo var_export($_SERVER['SERVER_SOFTWARE'], true);?>},
		{name:'服务器语言', value:<?php echo var_export(getenv('HTTP_ACCEPT_LANGUAGE'), true);?>},
		{name:'服务器端口', value:'<?php echo ($_SERVER['SERVER_PORT']); ?>'},
		{name:'服务器主机名', value:<?php echo var_export(DIRECTORY_SEPARATOR == '/' ? $os[1] : $os[2] , true);?>},
		{name:'管理员邮箱', value:'<?php echo ($_SERVER['SERVER_ADMIN']); ?>'},
		{name:'绝对路径', value:<?php echo var_export(SITE_DIR, true);?>},
		{name:'上传文件最大限制', value:'（upload_max_filesize）：<?php echo get_cfg_var('upload_max_filesize');?>'}
		]"></table>
	</div>

	<?php if(($sysinfo["sysReShow"]) == "show"): ?><div title="实时数据">
			<div id="index-system-info-dialog-tabs" style="line-height: 1.7;padding: 5px 10px;">
				服务器当前时间：<span><?php echo ($sysinfo["stime"]); ?></span> <br />
				服务器已运行时间：<span><?php echo ($sysinfo["uptime"]); ?></span> <br />
				总空间：<?php echo ($sysinfo["DiskTotal"]); ?>&nbsp;GB &nbsp;&nbsp;&nbsp;&nbsp;<span title="显示的是网站所在的目录的可用空间，非服务器上所有磁盘之可用空间！">可用空间</span>： <font color='#CC0000'><span><?php echo ($sysinfo["freeSpace"]); ?></span></font>&nbsp;GB<br />
				CPU型号 [<?php echo ($sysinfo["cpu"]["num"]); ?>核]：<?php echo ($sysinfo["cpu"]["model"]); ?> <br />
				内存使用状况：物理内存：共<font color='#CC0000'><?php echo ($sysinfo["TotalMemory"]); ?></font>, 已用<font color='#CC0000'><span><?php echo ($sysinfo["UsedMemory"]); ?></span></font>, 空闲<font color='#CC0000'><span><?php echo ($sysinfo["FreeMemory"]); ?></span></font>, 使用率<span><?php echo ($sysinfo["memPercent"]); ?></span> <br />
				<div class="bar"><div class="barli_green" style="width:<?php echo ($sysinfo["memPercent"]); ?>">&nbsp;</div> </div>
				<?php if($sysinfo['CachedMemory'] > 0): ?>Cache化内存为 <span><?php echo ($sysinfo["CachedMemory"]); ?></span>, 使用率<span><?php echo ($sysinfo["memCachedPercent"]); ?></span> %	| Buffers缓冲为  <span><?php echo ($sysinfo["Buffers"]); ?></span>
					<div class="bar"><div class="barli_blue" style="width:<?php echo ($sysinfo["barmemCachedPercent"]); ?>">&nbsp;</div></div>
					真实内存使用 <span><?php echo ($sysinfo["memRealUsed"]); ?></span>, 真实内存空闲<span><?php echo ($sysinfo["memRealFree"]); ?></span>, 使用率<span><?php echo ($sysinfo["memRealPercent"]); ?></span> %
					<div class="bar_1"><div class="barli_1" style="width:<?php echo ($sysinfo["barmemRealPercent"]); ?>">&nbsp;</div></div><?php endif; ?>
				<?php if($sysinfo['TotalSwap'] > 0): ?>SWAP区：共<?php echo ($sysinfo["TotalSwap"]); ?>, 已使用<span><?php echo ($sysinfo["swapUsed"]); ?></span>, 空闲<span><?php echo ($sysinfo["swapFree"]); ?></span>, 使用率<span><?php echo ($sysinfo["swapPercent"]); ?></span> %
					<div class="bar"><div class="barli_red" style="width:<?php echo ($sysinfo["barswapPercent"]); ?>">&nbsp;</div> </div><?php endif; ?>
				系统平均负载：<span><?php echo ($sysinfo["loadAvg"]); ?></span>
			</div>
		</div><?php endif; ?>

	<?php if(($net_state) != ""): ?><div title="网络情况" style="line-height: 1.7;padding: 5px 10px;">
			<?php echo ($net_state); ?>
		</div><?php endif; ?>
</div>