<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:77:"C:\wamp64\www\new_petmall\public/../application/admin\view\good_type\add.html";i:1536721848;s:77:"C:\wamp64\www\new_petmall\public/../application/admin\view\public\layout.html";i:1539084273;}*/ ?>
﻿<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>PetMall后台管理系统</title>
		<!-- basic styles -->
		<link href="/static/admin/css/bootstrap.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="/static/admin/css/font-awesome.min.css" />
		<!--[if IE 7]>
		  <link rel="stylesheet" href="/static/admin/css/font-awesome-ie7.min.css" />
		<![endif]-->

		<!-- ace styles -->

		<link rel="stylesheet" href="/static/admin/css/ace.min.css" />
		<link rel="stylesheet" href="/static/admin/css/ace-rtl.min.css" />
		<link rel="stylesheet" href="/static/admin/css/ace-skins.min.css" />
		<link rel="stylesheet" href="/static/admin/css/myace.css" />

		<!--[if lte IE 8]>
		  <link rel="stylesheet" href="/static/admin/css/ace-ie.min.css" />
		<![endif]-->

		<!-- inline styles related to this page -->

		<!--[if !IE]> -->
		
		<!-- <![endif]-->
		<!--[if IE]>
		<script src="/static/admin/js/jquery-2.0.3.min.js"></script>
		<![endif]-->
		<script src="/static/admin/js/jquery-2.0.3.min.js"></script>
		<!-- 加载layer弹窗插件 -->
		<script src="/static/layer/layer.min.js"></script>
		<!-- ace settings handler -->

		<script src="/static/admin/js/ace-extra.min.js"></script>

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

		<!--[if lt IE 9]>
		<script src="/static/admin/js/html5shiv.js"></script>
		<script src="/static/admin/js/respond.min.js"></script>
		<![endif]-->
		<!-- uploadify图像上传插件 -->
		<link rel="stylesheet" href="/static/uploadify/Huploadify.css">
		<script src='/static/uploadify/jquery.Huploadify.js'></script>
		<!-- 百度在线编辑器 -->
		<script type="text/javascript">
    		window.UEDITOR_HOME_URL = '/static/ueditor/';
		</script> 
		<script type="text/javascript" src="/static/ueditor/ueditor.config.js"></script>
		<script type="text/javascript" src="/static/ueditor/ueditor.all.min.js"></script>
		<script type="text/javascript" src="/static/ueditor/ueditor.parse.min.js"></script>
		<script type="text/javascript" src="/static/ueditor/lang/zh-cn/zh-cn.js"></script>
		<!-- 百度在线编辑器 结束-->
		
		
		
		
		
	</head>

	<body class="skin-2">
		<?php echo $menuList; ?>
		<div class="navbar navbar-default" id="navbar">
			<script type="text/javascript">
				try{ace.settings.check('navbar' , 'fixed')}catch(e){}
			</script>

			<div class="navbar-container" id="navbar-container">
				<div class="navbar-header pull-left">
					<a href="/<?php echo request()->module(); ?>/Index/index" class="navbar-brand">
						<small>
							<i class="icon-leaf"></i>
							PetMall后台管理系统
						</small>
					</a><!-- /.brand -->
				</div>
				<!-- /.navbar-header -->
				<!-- 顶部导航栏开始 -->
				<div class="navbar-header pull-right" role="navigation">
					<ul class="nav ace-nav" style="height:auto">
						<!-- 要发货的订单 -->
						<li class="purple">
							
						</li>

						<li class="green">
							
						</li>
						<!-- 登录后的用户信息 -->
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<img class="nav-user-photo" src="/static/<?php echo \think\Session::get('admin.img'); ?>" alt="<?php echo \think\Session::get('admin.admin_name'); ?>" />
								<span class="user-info">
									<small>欢迎登录</small>
									<?php echo \think\Session::get('admin.admin_name'); ?>
								</span>

								<i class="icon-caret-down"></i>
							</a>

							<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="/<?php echo request()->module(); ?>/ShopConfig/index">
										<i class="icon-cog"></i>
										设置
									</a>
								</li>

								<li>
									<a href="__ROOT__/home/" target="_blank">
										<i class="icon-home"></i>
										网站首页
									</a>
								</li>

								<li class="divider"></li>
								<li>
									<a href="/<?php echo request()->module(); ?>/AdminUser/changepwd">
										<i class="icon-cog"></i>
										修改密码
									</a>
								</li>

								<li>
									<a href="/<?php echo request()->module(); ?>/Login/logout">
										<i class="icon-off"></i>
										退出
									</a>
								</li>
							</ul>
						</li>
					</ul><!-- /.ace-nav -->
				</div>
				<!-- /.navbar-header -->
				<!-- 顶部导航结束 -->
			</div><!-- /.container -->
		</div>

		<div class="main-container" id="main-container">
			<script type="text/javascript">
				try{ace.settings.check('main-container' , 'fixed')}catch(e){}
			</script>

			<div class="main-container-inner">
				<a class="menu-toggler" id="menu-toggler" href="#">
					<span class="menu-text"></span>
				</a>

				<div class="sidebar" id="sidebar">
					<script type="text/javascript">
						try{ace.settings.check('sidebar' , 'fixed')}catch(e){}
					</script>

					<div class="sidebar-shortcuts" id="sidebar-shortcuts">
						<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
							<a class="btn btn-success" href="/<?php echo request()->module(); ?>/Order/index" title="订单管理">
								<i class="icon-signal"></i>
							</a>

							<a class="btn btn-info" href="/<?php echo request()->module(); ?>/Article/index" title="文章管理">
								<i class="icon-pencil"></i>
							</a>

							<a class="btn btn-warning" href="/<?php echo request()->module(); ?>/Goods/index" title="商品管理">
								<i class="icon-group"></i>
							</a>

							<a class="btn btn-danger" href="/<?php echo request()->module(); ?>/ShopConfig/index" title="系统设置">
								<i class="icon-cogs"></i>
							</a>
						</div>

						<div class="sidebar-shortcuts-mini" id="sidebar-shortcuts-mini">
							<span class="btn btn-success"></span>

							<span class="btn btn-info"></span>

							<span class="btn btn-warning"></span>

							<span class="btn btn-danger"></span>
						</div>
					</div>

					<!-- 左侧导航栏开始 -->
					<ul class="nav nav-list">
						<li class="active">
							<a href="/<?php echo request()->module(); ?>/Index">
								<i class="icon-home"></i>
								<span class="menu-text"> 后台首页 </span>
							</a>
						</li>
						<!-- 
						<li class="<eq name='userz' value='User'>active open</eq>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-user"></i>
								<span class="menu-text">会员管理</span>
								<b class="arrow icon-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<eq name='userList' value='UserIndex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/User/index">
										<i class="icon-double-angle-right"></i>
										会员列表
									</a>
								</li>
								<li class="<eq name='userList' value='UserAdd'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/User/add">
										<i class="icon-double-angle-right"></i>
										添加会员
									</a>
								</li>
							</ul>
						</li>
						 -->
						<li class="<?php if($menu == 'Goods'): ?>active open<?php endif; ?>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-text-width"></i>
								<span class="menu-text"> 商品管理</span>
								<b class="arrow icon-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<?php if($menuList == 'Goods-index'): ?>active open<?php endif; ?>">
									<a href="/<?php echo request()->module(); ?>/Good/index">
										<i class="icon-double-angle-right"></i>
										商品列表
									</a>
								</li>
								<li class="<eq name='menuList' value='Goods-addEditGood'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Good/addEditGood">
										<i class="icon-double-angle-right"></i>
										添加商品
									</a>
								</li>

								<li class="<?php if($menuList == 'GoodType-index'): ?>active open<?php endif; ?>">
									<a href="/<?php echo request()->module(); ?>/good_type/index">
										<i class="icon-double-angle-right"></i>
										商品分类列表
									</a>
								</li>
								<li class="<?php if($menuList == 'GoodType-addeditcategory'): ?>active open<?php endif; ?>">
									<a href="/<?php echo request()->module(); ?>/good_type/addeditcategory">
										<i class="icon-double-angle-right"></i>
										添加商品分类
									</a>
								</li>

								<li class="<eq name='menuList' value='Brand-index'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Brand/index">
										<i class="icon-double-angle-right"></i>
										品牌列表
									</a>
								</li>
								<li class="<eq name='menuList' value='Brand-add'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Brand/addeditbrand">
										<i class="icon-double-angle-right"></i>
										添加商品品牌
									</a>
								</li>

								<li class="<eq name='menuList' value='Comment-index'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Comment/index">
										<i class="icon-double-angle-right"></i>
										评论列表
									</a>
								</li>
								<li class="<eq name='menuList' value='Recycle-index'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Good/recycle">
										<i class="icon-double-angle-right"></i>
										商品回收站
									</a>
								</li>

							</ul>
						</li>
						<!-- 商品分类和商品管理 结束-->

						

						<!-- <li>
							<a href="#" class="dropdown-toggle">
								<i class="icon-briefcase"></i>
								<span class="menu-text"> 促销管理 </span>
								<b class="arrow icon-angle-down"></b>
							</a>
							<ul class="submenu">
								
						
							</ul>
						</li> -->

						

					

						<!-- 订单管理 -->
						<li class="<eq name='menu' value='Order'>active open</eq>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-group"></i>
								<span class="menu-text"> 订单管理 </span>
								<b class="arrow icon-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<eq name='menuList' value='Orderindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Order/index">
										<i class="icon-double-angle-right"></i>
										订单列表
									</a>
								</li>
								
								<li class="<eq name='menuList' value='Ordercancel'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Order/cancel">
										<i class="icon-double-angle-right"></i>
										已删除订单
									</a>
								</li>
								<li class="<eq name='menuList' value='Orderbook'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Order/book">
										<i class="icon-double-angle-right"></i>
										缺货登记
									</a>
								</li>
							</ul>
						</li>
						<!-- 订单管理结束 -->
						<!-- 会员管理 -->
						<li class="<eq name='userz' value='User'>active open</eq>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-user"></i>
								<span class="menu-text">会员管理</span>
								<b class="arrow icon-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<eq name='userList' value='UserIndex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/User/index">
										<i class="icon-double-angle-right"></i>
										会员列表
									</a>
								</li>
								<li class="<eq name='userList' value='UserAdd'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/User/add">
										<i class="icon-double-angle-right"></i>
										添加会员
									</a>
								</li>
							</ul>
						</li>
						<!-- 会员管理结束 -->
						<!-- 促销管理 -->
						<li class="<eq name='menu' value='sale'>active open</eq>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-bullhorn"></i>
								<span class="menu-text"> 促销管理 </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li class="<eq name='menuList' value='Gift-index'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Gift/index">
										<i class="icon-double-angle-right"></i>
										赠品列表
									</a>
								</li>
								<li class="<eq name='menuList' value='Gift-add'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Gift/add">
										<i class="icon-double-angle-right"></i>
										添加赠品
									</a>
								</li>
								<li class="<eq name='menuList' value='Sale-salelist'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Sale/salelist">
										<i class="icon-double-angle-right"></i>
										秒杀管理
									</a>
								</li>
							</ul>
						</li>
						<!-- 促销管理结束 -->
						
						<!-- 系统设置 -->
						<li class="<eq name='menu' value='ShopConfig'>active open</eq>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-cogs"></i>
								<span class="menu-text"> 系统设置 </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li class="<eq name='menuList' value='ShopConfigindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/ShopConfig/index">
										<i class="icon-double-angle-right"></i>
										商店设置
									</a>
								</li>

								<li class="<eq name='menuList' value='PayTypeindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/PayType/index">
										<i class="icon-double-angle-right"></i>
										支付管理
									</a>
								</li>

								<li class="<eq name='menuList' value='Shippingindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Shipping/index">
										<i class="icon-double-angle-right"></i>
										配送方式管理
									</a>
								</li>
								<!-- <li class="<eq name='menuList' value='SetMailindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/SetMail/index">
										<i class="icon-double-angle-right"></i>
										邮件设置
									</a>
								</li> -->
								<li class="<eq name='menuList' value='Messageindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Message/index">
										<i class="icon-double-angle-right"></i>
										我的消息
									</a>
								</li>
							</ul>
						</li>
						<!-- 系统设置 结束 -->
						<!-- 权限设置 -->
						<li class="<eq name='admin' value='AdminUser'>active open</eq>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-lock"></i>
								<span class="menu-text"> 权限设置 </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li class="<eq name='adminList' value='AdminUserIndex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/AdminUser/index">
										<i class="icon-double-angle-right"></i>
										管理员列表
									</a>
								</li>

								<li class="<eq name='adminList' value='AdminUserAdd'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/AdminUser/add">
										<i class="icon-double-angle-right"></i>
										添加管理员
									</a>
								</li>

								<li class="<eq name='adminList' value='AdminUserChangepwd'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/AdminUser/changepwd">
										<i class="icon-double-angle-right"></i>
										修改密码
									</a>
								</li>

								<li class="<eq name='adminList' value='AdminUsergroup'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Rule/group">
										<i class="icon-double-angle-right"></i>
										管理员组
									</a>
								</li>

								<li class="<eq name='adminList' value='AdminUserrule'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Rule/rule">
										<i class="icon-double-angle-right"></i>
										添加规则
									</a>
								</li>
							</ul>
						</li>
						<!-- 权限设置 结束-->
						
						<!-- 文章管理 -->
						<li class="<eq name='menu' value='Article'>active open</eq>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-text-width"></i>
								<span class="menu-text"> 文章管理 </span>
								<b class="arrow icon-angle-down"></b>
							</a>
							<ul class="submenu">
								<li class="<eq name='menuList' value='Articleindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Article/index">
										<i class="icon-double-angle-right"></i>
										文章列表
									</a>
								</li>
								<li class="<eq name='menuList' value='Articleadd'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Article/add">
										<i class="icon-double-angle-right"></i>
										添加文章
									</a>
								</li>
								<li class="<eq name='menuList' value='ArticleTypeindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/ArticleType/index">
										<i class="icon-double-angle-right"></i>
										文章分类
									</a>
								</li>
								<li class="<eq name='menuList' value='ArticleTypeadd'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/ArticleType/add">
										<i class="icon-double-angle-right"></i>
										添加文章分类
									</a>
								</li>
							</ul>
						</li>
						<!-- 文章管理结束 -->
						
						<!-- 广告管理 开始 -->
						<li class="<?php if($menu == 'Ad'): ?>active open<?php endif; ?>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-facetime-video"></i>
								<span class="menu-text"> 广告管理 </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li class="<?php if($menuList == 'Adindex'): ?>active open<?php endif; ?>">
									<a href="/<?php echo request()->module(); ?>/Ad/index">
										<i class="icon-double-angle-right"></i>
										广告列表
									</a>
								</li>

								<li class="<?php if($menuList == 'Adadd'): ?>active open<?php endif; ?>">
									<a href="/<?php echo request()->module(); ?>/Ad/add">
										<i class="icon-double-angle-right"></i>
										添加广告
									</a>
								</li>
							</ul>
						</li>
						<!-- 广告管理结束 -->
						<!-- 友情链接管理 -->
						<li class="<eq name='menu' value='Flink'>active open</eq>">
							<a href="#" class="dropdown-toggle">
								<i class="icon-exchange"></i>
								<span class="menu-text"> 友情链接 </span>

								<b class="arrow icon-angle-down"></b>
							</a>

							<ul class="submenu">
								<li class="<eq name='menuList' value='Flinkindex'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Flink/index">
										<i class="icon-double-angle-right"></i>
										友情链接列表
									</a>
								</li>

								<li class="<eq name='menuList' value='Flinkadd'>active open</eq>">
									<a href="/<?php echo request()->module(); ?>/Flink/add">
										<i class="icon-double-angle-right"></i>
										添加友情链接
									</a>
								</li>
							</ul>
						</li>
						<!-- 友情链接结束 -->
						<!-- <li>
							<a href="gallery.html">
								<i class="icon-picture"></i>
								<span class="menu-text"> 商品相册 </span>
							</a>
						</li> -->
					</ul><!-- /.nav-list -->
					<!-- 左侧导航结束 -->


					<!-- 这个代码是控制收缩左侧导航栏 -->
					<div class="sidebar-collapse" id="sidebar-collapse">
						<i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
					</div>

					<script type="text/javascript">
						try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}
					</script>
				</div>

				<div class="main-content">
					<div class="breadcrumbs" id="breadcrumbs">
						<script type="text/javascript">
							try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}
						</script>
						
						<!-- 页面标题 -->
						<ul class="breadcrumb">
							<li>
								<i class="icon-home home-icon"></i>
								<a href="/<?php echo request()->module(); ?>/Index/index">首页</a>
							</li>
							
							
						</ul>
						<!-- 页面标题结束 -->

						<!-- 标题右侧的搜索框 -->
						<!--<div class="nav-search" id="nav-search">
							<form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="icon-search nav-search-icon"></i>
								</span>
							</form>
						</div>-->
						<!-- 标题右侧的搜索框  结束-->
					</div>
					
					<!-- 继承main -->
					

	<div class="page-content">
		<div class="page-header">
			<h1>
				商品分类
				<small>
					<i class="icon-double-angle-right"></i>
					添加分类
				</small>
			</h1>
		</div><!-- /.page-header -->

		<div class="row">
			<div class="col-xs-12">
				<!-- PAGE CONTENT BEGINS -->

				<form class="form-horizontal" role="form" action="/<?php echo request()->module(); ?>/good_type/addEditCategory" method="post" id="form">
					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="form-field-1">
							<!-- 选择商品 -->所属分类</label>
						<div class="col-sm-9">

							<div class="btn-group">
								<div data-toggle="dropdown" class="drop_down btn btn-white dropdown-toggle" style="border:1px solid #ccc;width:250px;">
									<span class="dropdown_sp">请选择</span> <i class="icon-angle-down icon-on-right "></i>
									<input type="hidden" name="pid" value="" sel="select">
								</div>

								<ul class="dropdown-menu dropdown-success pull-left dropdown_ul" id="">
									<li value="0">
										<a href="javascript:;">顶级分类</a>
									</li>
									<?php if(is_array($type) || $type instanceof \think\Collection || $type instanceof \think\Paginator): if( count($type)==0 ) : echo "" ;else: foreach($type as $key=>$vo): ?>
										<li  value="<?php echo $vo['id']; ?>">
											<a href="javascript:;">&nbsp;&nbsp;&nbsp;<?php echo str_repeat("&nbsp;&nbsp;&nbsp;",$vo['level']); ?><?php echo $vo['type_name']; ?></a>
										</li>
									<?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> 分类名称 </label>

						<div class="col-sm-9">
							<input type="text" id="form-field-1" placeholder="分类名称" class="col-xs-10 col-sm-5" name="type_name" required/><span></span>
						</div>
					</div>

					<div class="space-4"></div>

					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 分类排序 </label>

						<div class="col-sm-9">
							<input type="text" id="form-field-2" placeholder="分类排序" class="col-xs-10 col-sm-5" name="type_order" /><span></span>
							
						</div>
					</div>

					<div class="space-4"></div>

					<div class="form-group">
						<label class="col-sm-3 control-label no-padding-right" for="form-field-2"> 是否显示 </label>

						<div class="col-sm-9">
							<div class="col-xs-12 col-sm-6">
								<div class="control-group">
									
										<label>
											<input name="is_show" type="radio" class="ace" value="1" />
											<span class="lbl"> 显示</span>
										</label>
									
										<label>
											<input name="is_show" type="radio" class="ace" value="0" />
											<span class="lbl"> 不显示</span>
										</label>
									
								</div>
							</div>
						</div>
					</div>

					<div class="clearfix form-actions">
						<div class="col-md-offset-3 col-md-9">
							<button class="btn btn-info" type="button">
								<i class="icon-ok bigger-110"></i>
								提　交
							</button>

							&nbsp; &nbsp; &nbsp;
							<button class="btn" type="reset">
								<i class="icon-undo bigger-110"></i>
								重　置
							</button><span></span>
							
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
	$(function() {
		var type = false;
		$('.dropdown_ul>li').bind('click',function() {
	    		$(this).parent().prev().children('span').html($(this).html());
	    		$(this).parent().prev().children('input[sel]').val($(this).val());
	    		type = true;
	    	})

		var bool = false;
		$('#form-field-1').blur(function() {
			if(!$(this).val()) {
				$(this).next().css('color','red').html('请填入分类名称');
			}else{
				$(this).next().html('');
				bool = true;
			}
		})

		//排序
		var boolb =true;
		$('#form-field-2').blur(function() {
			var reg = /^[0-9]+$/;
			if(!reg.test($(this).val())) {
				$(this).next().css('color','red').html('请填入数字');
				boolb = false;
			}else{
				boolb = true;
			}
		})

		$('button[type="button"]').click(function() {
			if(bool && boolb && type) {
				$('#form').submit();
			}else{
				$(this).next().next().css('color','red').html('请填入完整合法的分类信息！')
			}
		})
	})
	</script>

				</div><!-- /.main-content -->

				<div class="ace-settings-container" id="ace-settings-container">
					<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
						<i class="icon-cog bigger-150"></i>
					</div>

					<div class="ace-settings-box" id="ace-settings-box">
						<div>
							<div class="pull-left">
								<select id="skin-colorpicker" class="hide">
									<option data-skin="default" value="#438EB9">#438EB9</option>
									<option data-skin="skin-1" value="#222A2D">#222A2D</option>
									<option data-skin="skin-2" value="#C6487E">#C6487E</option>
									<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
								</select>
							</div>
							<span>&nbsp; 选择皮肤</span>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-navbar" />
							<label class="lbl" for="ace-settings-navbar"> 固定导航条</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-sidebar" />
							<label class="lbl" for="ace-settings-sidebar"> 固定滑动条</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-breadcrumbs" />
							<label class="lbl" for="ace-settings-breadcrumbs">固定面包屑</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" />
							<label class="lbl" for="ace-settings-rtl">切换到左边</label>
						</div>

						<div>
							<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-add-container" />
							<label class="lbl" for="ace-settings-add-container">
								切换窄屏
								<b></b>
							</label>
						</div>
					</div>
				</div><!-- /#ace-settings-container -->
			</div><!-- /.main-container-inner -->

			<a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
				<i class="icon-double-angle-up icon-only bigger-110"></i>
			</a>
		</div><!-- /.main-container -->

		<!-- basic scripts -->

		
		<!--[if !IE]> -->

		<script type="text/javascript">
			window.jQuery || document.write("<script src='/static/admin/js/jquery-2.0.3.min.js'>"+"<"+"script>");
		</script>

		<!-- <![endif]-->

		<!--[if IE]>
		<script type="text/javascript">
 			window.jQuery || document.write("<script src='/static/admin/js/jquery-1.10.2.min.js'>"+"<"+"script>");
		</script>
		<![endif]-->

		<script type="text/javascript">
			if("ontouchend" in document) document.write("<script src='/static/admin/js/jquery.mobile.custom.min.js'>"+"<"+"script>");
		</script>
		<script src="/static/admin/js/bootstrap.min.js"></script>
		<script src="/static/admin/js/typeahead-bs2.min.js"></script>

		<!-- page specific plugin scripts -->

		<!--[if lte IE 8]>
		  <script src="/static/admin/js/excanvas.min.js"></script>
		<![endif]-->

		<script src="/static/admin/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="/static/admin/js/jquery.ui.touch-punch.min.js"></script>
		<script src="/static/admin/js/jquery.slimscroll.min.js"></script>
		<script src="/static/admin/js/jquery.easy-pie-chart.min.js"></script>
		<script src="/static/admin/js/jquery.sparkline.min.js"></script>
		

		<!-- ace scripts -->

		<script src="/static/admin/js/ace-elements.min.js"></script>
		<script src="/static/admin/js/ace.min.js"></script>

		<!-- inline scripts related to this page -->

		
</body>
</html>

