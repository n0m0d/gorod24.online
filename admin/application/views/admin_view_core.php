<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= $this->registry['title']; ?> </title>
<meta name="keywords" content="<?= $this->registry['keywords'];?>" />
<meta name="description" content="<?= $this->registry['description'];?>" />
<meta name="robots" content="all" />
<meta name ="revisit-after" Content="15 days">
<meta http-equiv="content-language" content="ru" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,900|Quicksand:400,700|Questrial" rel="stylesheet" />
<link href="//<?= $this->registry['domain'];?>css/pongu_default.css" rel="stylesheet" type="text/css" media="all" />
<!--<link href="//<?= $this->registry['domain'];?>css/pongu_fonts.css" rel="stylesheet" type="text/css" media="all" /> -->
<link rel="shortcut icon" href="//<?= $this->registry['domain'];?>images/pencil.jpg" type="image/x-icon">
<!--<script type="text/javascript" language="JavaScript" src="//<?= $this->registry['domain'];?>/js/jquery-1.6.2.js"></script>-->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<link href="//<?= $this->registry['domain'];?>admin/css/admin.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" language="JavaScript" src="//<?= $this->registry['domain'];?>js/admin.js"></script>
<script src="/js/zelect.js"></script>

<?= do_action('the_head'); ?>
</head>
<!--[if IE 6]><link href="default_ie6.css" rel="stylesheet" type="text/css" /><![endif]-->
<body>
<div id="admin-content">
	<?php 
		if(file_exists($content_view))
			include $content_view; 
		else {
			if(function_exists($content_view))
				$content_view();
			else echo "File or function not found";
		}
		
	?>
</div>
</body>
</html>