<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>elRTE</title>

	<!-- jQuery and jQuery UI -->
	<script src="js/jquery-1.6.1.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/jquery-ui-1.8.13.custom.min.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="css/smoothness/jquery-ui-1.8.13.custom.css" type="text/css" media="screen" charset="utf-8">
	

	<!-- elRTE -->
	<script src="js/elrte.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="js/elRTE.options.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="css/elrte.min.css" type="text/css" media="screen" charset="utf-8">
	

	<!-- elRTE translation messages -->
	
	<script src="js/i18n/elrte.es.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript" charset="utf-8">


		$().ready(function() { 
				$('#elFinder a').hover(
					function () {
						$('#elFinder a').animate({
							'background-position' : '0 -45px'
						}, 300);
					},
					function () {
						$('#elFinder a').delay(400).animate({
							'background-position' : '0 0'
						}, 300);
					}
				);

			$('#elFinder a').delay(800).animate({'background-position' : '0 0'}, 300);



			var opts = {
				cssClass : 'el-rte',
				 lang     : 'es',
				height   : 450,
				toolbar  : 'maxi',
				cssfiles : ['css/elrte-inner.css']
			}
			$('#editor').elrte(opts);
		})
	</script>

	<style type="text/css" media="screen">
		body { padding:20px;}
	</style>
	
</head>
<body>
      <form action="accion.php"> 

	<div id="editor">
	
	</div>
	
	<input type="submit" name="submi" /> 
	</form>
	
</body>
</html>
