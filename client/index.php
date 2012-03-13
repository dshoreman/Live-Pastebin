<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title>Live Pastebin</title>
		<meta name="description" content="">
		<meta name="author" content="">

		<meta name="viewport" content="width=device-width">

		<link rel="stylesheet/less" href="less/style.less">
		<script src="js/libs/less-1.2.1.min.js"></script>

		<script src="js/libs/modernizr-2.5.3-respond-1.1.0.min.js"></script>
	</head>
	<body>
		<!--[if lt IE 7]><p class=chromeframe>Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</p><![endif]-->

		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>
					<a class="brand" href="#">Live Pastebin</a>
				</div>
			</div>
		</div>

		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span8">
					<h2 class="pull-left">Untitled Paste</h2>
					<p class="pull-right"><a class="btn btn-large" id="socket_status">offline</a></p>
					<div class="row-fluid">
						<div class="span12">
							<div class="well">
								<pre>code goes here</pre>
							</div>
						</div>
					</div>
				</div>
				<div class="span4">
					<h3>Live Chat</h3>
					<blockquote class="pull-left">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do.</p>
						<small>user1</small>
					</blockquote>
					<blockquote class="pull-right">
						<p>eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
						<small>user2</small>
					</blockquote>
					<blockquote class="pull-left">
						<p>LUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
						<small>user1</small>
					</blockquote>
					<blockquote class="pull-right">
						<p>Duis aute irure dolor in reprehenderit in voluptate velit essecillum dolore eu fugiat nulla pariatur.</p>
						<small>user3</small>
					</blockquote>
					<blockquote class="pull-left">
						<p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
						<small>user3</small>
					</blockquote>
					<div class="row-fluid">
						<div class="span12">
							<form class="well form-search" id="chatForm">
								<input class="search-query" type="text">
							</form>
						</div>
					</div>
				</div>
			</div>

			<hr>

			<footer>
				<p>Footer stuff here</p>
			</footer>
		</div>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>

		<script src="js/libs/fancywebsocket.js"></script>

		<script src="js/script.js"></script>
	</body>
</html>