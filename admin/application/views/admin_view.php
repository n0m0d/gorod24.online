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
<link rel="shortcut icon" href="//<?= $this->registry['domain'];?>favicon.ico" type="image/x-icon">
<!--<script type="text/javascript" language="JavaScript" src="//<?= $this->registry['domain'];?>/js/jquery-1.6.2.js"></script>-->
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="/js/jquery/jquery-1.10.1.min.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type='text/javascript' src='/js/jquery.ui.datepicker-ru.js'></script>
<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
<script src="/js/tinyMce_photo.js"></script>
<link href="/js/plupload/jquery.ui.plupload/css/jquery.ui.plupload.css" rel="stylesheet" type="text/css" media="all" />
<link href="/css/jquery.timepicker.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/js/plupload/jquery.ui.plupload/jquery.ui.plupload.min.js"></script>
<script type="text/javascript" src="/js/plupload/i18n/ru.js"></script>
<script src="/admin/js/admin.js"></script>
<script src="/js/zelect.js"></script>
<script src="/js/jquery.timepicker.min.js"></script>

<link href="//<?= $this->registry['domain'];?>admin/css/admin.css" rel="stylesheet" type="text/css" media="all" />
<script>
tinymce.init({
		selector: ".editor-text",
		height: 300,
		language_url : "/js/tinyMce_ru.js",
		forced_root_block : false,
		force_p_newlines : false,
		force_br_newlines : true,
		browser_spellcheck : true,
		contextmenu: false,
		
		cleanup_on_startup: false,
		trim_span_elements: false,
		verify_html: false,
		cleanup: false,
		convert_urls: false,
		
		//autosave_ask_before_unload: false,
		plugins: [
			"advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker save",
			"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			"table contextmenu directionality emoticons template textcolor paste  textcolor colorpicker textpattern photo" //fullpage
		],
		toolbar1: "newdocument | save print | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect", //newdocument fullpage
		toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
		toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft | photo",
		
		save_oncancelcallback: function () { console.log("Save canceled"); },
		save_onsavecallback: function () { tinymce.triggerSave(); $("button[name=submitbtn]").click();console.log("Saved"); },
		
		convert_urls: false,
		menubar: true,
		toolbar_items_size: "small",

		style_formats: [{title: "Bold text",inline: "b"}, {title: "Red text",inline: "span",styles: {color: "#ff0000"}}, {title: "Red header",block: "h1",styles: {color: "#ff0000"}}, {title: "Example 1",inline: "span",classes: "example1"}, {title: "Example 2",inline: "span",classes: "example2"}, {title: "Table styles"}, {title: "Table row 1",selector: "tr",classes: "tablerow1"}],
		templates: [{title: "Test template 1",content: "Test 1"}, {title: "Test template 2",content: "Test 2"}],
		content_css: ["//www.tinymce.com/css/codepen.min.css"],
		/* setup: function (editor) { editor.on("change", function () { tinymce.triggerSave();}); } */
	  });
</script>
<script type="text/javascript">
	var GLOBAL = [];
	GLOBAL['destination'] = [];
	GLOBAL['destination']['city_id']='<?= $this->registry['currentCity']['city_id']; ?>';
	GLOBAL['destination']['url']='<?= $this->registry['currentCity']['url']; ?>';
	GLOBAL['destination']['range']='<?= $this->registry['IPGeo']['range']; ?>';
	GLOBAL['destination']['cc']='<?= $this->registry['IPGeo']['cc']; ?>';
	GLOBAL['destination']['city']='<?= $this->registry['IPGeo']['city']; ?>';
	GLOBAL['destination']['region']='<?= $this->registry['IPGeo']['region']; ?>';
	GLOBAL['destination']['district']='<?= $this->registry['IPGeo']['district']; ?>';
	GLOBAL['destination']['lat']='<?= $this->registry['IPGeo']['lat']; ?>';
	GLOBAL['destination']['lng']='<?= $this->registry['IPGeo']['lng']; ?>';
	<?php if ($_SESSION['user_destination']['questuion']) { echo "GLOBAL['destination']['questuion'] = 1;"; } else {echo "GLOBAL['destination']['questuion'] = 0;";} ?>
	
	GLOBAL['domain']='<?= $this->registry['domain']; ?>';
</script>	
<script type="text/javascript" language="JavaScript" src="//<?= $this->registry['domain'];?>js/admin.js"></script>
<?= do_action('the_head'); ?>
</head>
<!--[if IE 6]><link href="default_ie6.css" rel="stylesheet" type="text/css" /><![endif]-->
<body>
<div id="admin-header">
	<div id="admin-user"><a href="/admin/?exit=1" title="Выход"><?=$_SESSION['user_name']; ?></a></div>
</div>
<div id="admin-content">
	
	<div id="left-menu" class="Tm">
		<?php generate_menu(); ?>
	</div>
	<div id="main-content">
	
	<?php 
		if(!empty($_GET['menu'])){
			global $menu;
			for ($i = 0; $i <= count($menu); $i++) {
				if($menu[$i]['menu_slug'] == $_GET['menu']){
					if(method_exists($menu[$i]['action'][0], $menu[$i]['action'][1])){
						echo call_user_func_array($menu[$i]['action'], $_GET);
					}
				}
			}
		}
		elseif(is_array($content_view)){
			if(method_exists($content_view[0], $content_view[1])) call_user_func($content_view); else echo "File or function not found";
		}
		elseif(file_exists($content_view))
			include $content_view; 
		elseif(function_exists($content_view))
			$content_view();
		elseif(method_exists($this, $content_view))
			call_user_func(array($this, $content_view));
		else {
			echo "File or function not found";
		}
		
	?>
</div>

</div>
</body>
</html>