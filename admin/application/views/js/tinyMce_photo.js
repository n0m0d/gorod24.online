(function() {
	
    tinymce.PluginManager.add('photo', function(editor, url) {
	  // Add a button that opens a window
	  editor.addButton('gallery', {
		text: 'Фото-галерея',
		title : 'Вставить фото-галерею из медиафайлов (shortcode)',
		icon: false,
		
		onclick: function() {
			openFilesPopup({
				contentType:'image',
				multi:true,
				onSelect: function(result){
					console.log(result);
					var list="";
					for(i in result){
						var item=result[i];
						list += item.id + ";";
					}
					//editor.insertContent('[photo gallery="gallery1" maxwidth="100%" src="' + list + '"]');
					editor.insertContent('[photo width="300px" maxheight="150px" maxwidth="100%" gallery="gallery1" id="' + list + '"]');
				},

			});
		}
	  });
	  
	  editor.addButton('photo', {
		text: 'Фото',
		title : 'Вставить фото из медиафайлов',
		icon: false,
		
		onclick: function() {
			openFilesPopup({
				contentType:'image',
				multi:false,
				onSelect: function(result){
					console.log(result);
					/*
					var list="";
					for(i in result){
						var item=result[i];
						//list += item.id + ";";
						//console.log(item);
					}
					*/
					
					//editor.insertContent('[photo gallery="gallery1" maxwidth="100%" src="' + list + '"]');
					editor.insertContent('<img src="' + result['src'] + '" style="max-width:100%; height:auto;" />');
				},

			});
		}
	  });

	});
	
})();