<!DOCTYPE html>
<html>
	<head>
		<title>{$result.meta.title|htmlspecialchars}</title>
		<meta charset="{$config.charset}">
		<link rel="stylesheet" href="/css/style.css" type="text/css">
		<script src="http://code.jquery.com/jquery-3.2.1.min.js"></script>
		<script src="/message"></script>
		<script src="/js/script.js"></script>
	</head>
	<body>
		<div class="page-overlay">
			<nav class="page-navigation">
				<ul>
					<li>
						<a href="/position">Должности</a>
					</li>
					<li>
						<a href="/employer">Работники</a>
					</li>
				</ul>
				<div class="both"></div>
			</nav>


			<div class="page-header">
				<h3>{$result.meta.title|htmlspecialchars}</h3>
			</div>
			<div class="page-body">
				<h1 class="page-body-overlay">{$result.meta.title|htmlspecialchars}</h1>
				<div class="page-body-body">{include file="$include"}</div>
			</div>
			<footer>
				<h3>{$result.meta.title|htmlspecialchars}</h3>
				<p>Автор: {$config.author|htmlspecialchars}
			</footer>
		</div>
	</body>
</html>