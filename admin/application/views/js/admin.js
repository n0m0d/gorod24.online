var xhr = null;
//Если с английского на русский, то передаём вторым параметром true.
transliterate = (
	function() {
		var
			rus = "щ   ш  ч  ц  ю  я  ё  ж  ъ  ы  э  а б в г д е з и й к л м н о п р с т у ф х ь".split(/ +/g),
			eng = "shh sh ch cz yu ya yo zh `` y' e` a b v g d e z i j k l m n o p r s t u f h `".split(/ +/g)
		;
		return function(text, engToRus) {
			var x;
			for(x = 0; x < rus.length; x++) {
				text = text.split(engToRus ? eng[x] : rus[x]).join(engToRus ? rus[x] : eng[x]);
				text = text.split(engToRus ? eng[x].toUpperCase() : rus[x].toUpperCase()).join(engToRus ? rus[x].toUpperCase() : eng[x].toUpperCase());	
			}
			return text;
		}
	}
)();

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

toURL = (
	function(){
		
		return function(text){
			return url = transliterate(text).replace(/\s/ig, '-').replace(/[^-a-z0-9]/gim, '').toLowerCase();
		}
	}

)();

$(function() {
	$( document ).ajaxSend(function(event, jqxhr, settings) {
		var Load = '<div class="fadeLoad"></div><div class="loadGif"></div>';
		$('body').append(Load);
		//console.log( "Triggered ajaxSend handler." );
	});
	
	$( document ).ajaxComplete(function(event, xhr, settings) {
		$('.fadeLoad, .loadGif').hide().remove();
		//console.log( "Triggered ajaxComplete handler." );
	});
	
	$( document ).on('change', '.onchange-selected', function(e){
		if(!$(this).hasClass('multi')){
			$('.MODAL-FILES li label').removeClass('selected');
		}
		if($(this).prop('checked')){
			$(this).parent().find('label').addClass('selected');
		}else{
			$(this).parent().find('label').removeClass('selected');
		}
	});
});

msdown = false;
msx = 0;
msy = 0;
var selectedItems = new Array();
function disableSelection(target){
    if (typeof target.onselectstart!="undefined") //IE route
        target.onselectstart=function(){return false}
    else if (typeof target.style.MozUserSelect!="undefined") //Firefox route
        target.style.MozUserSelect="none"
    else //All other route (ie: Opera)
        target.onmousedown=function(){return false}
    target.style.cursor = "default"
}
function coords(e){
    var posx = 0;
    var posy = 0;
	var offset = $("#tabs-2").offset();
	if($('#tabs-2').length>0){
		posx = e.pageX - offset.left + $('#tabs-2').scrollLeft() ;
		posy = e.pageY - offset.top + $('#tabs-2').scrollTop();
	}

    return new Array(posx,posy);
}
function onMouseDown(e){
    if (!e) var e = window.event;
    msdown = true;
    var mousexy = coords(e);
    msx = mousexy[0];
    msy = mousexy[1];
    return false;
}
function onMouseMove(e){
	if (!e) var e = window.event;
    var x1=0;
    var x2=0;
    var y1=0;
    var y2=0;
    var mousexy = coords(e);
    x1 = msx;
    y1 = msy;
    x2 = mousexy[0];
    y2 = mousexy[1];
    if (x1==x2){return;}
    if (y1==y2){return;}
    if (x1>x2){
        x1 = x1+x2;
        x2 = x1-x2;
        x1 = x1-x2;
    }
    if (y1>y2){
        y1 = y1+y2;
        y2 = y1-y2;
        y1 = y1-y2;
    }
    var sframe = document.getElementById('selectFrame');
	if(sframe!=null){
		$(sframe).css({
			"top":y1,
			"left":x1,
			"width":x2-x1,
			"height":y2-y1,
			"visibility": (msdown?'visible':'hidden'),
		});
	}
}
function onMouseUp(e){
    if (!e) var e = window.event;
    msdown = false;
    var mousexy = coords(e);
    doSelection(msx,msy,mousexy[0],mousexy[1]);
	var sframe = document.getElementById('selectFrame');
	if(sframe!==null){
		sframe.style.visibility = msdown?'visible':'hidden';
	}
}

function doSelection(x1,y1,x2,y2){
    if (x1==x2){return;}
    if (y1==y2){return;}
    if (x1>x2){
        x1 = x1+x2;
        x2 = x1-x2;
        x1 = x1-x2;
    }
    if (y1>y2){
        y1 = y1+y2;
        y2 = y1-y2;
        y1 = y1-y2;
    }
    selectedItems = new Array();
    var wlw = document.getElementById('wlw');
    for (var childItem in wlw.childNodes) {
        if (wlw.childNodes[childItem].nodeType == 1 && wlw.childNodes[childItem].id!='selectFrame'){
            var item = wlw.childNodes[childItem];
            if(item.offsetLeft>=x1 && item.offsetLeft<=x2 && item.offsetTop>=y1 && item.offsetTop<=y2){
                selectedItems.push(item.id);
                $(item).find('label').addClass('selected');
                $(item).find('.onchange-selected').prop('checked', true);
				
				//item.className = 'itemSelected';
            }else{
                //item.className = 'item';
            }
        }
    }
}
function init(){
    var wlw = document.getElementById('wlw');
    for (var childItem in wlw.childNodes) {
        var item = wlw.childNodes[childItem];
        if(item.nodeType == 1 && item.id!='selectFrame'){
            item.onclick = function(e){
                if (!e) var e = window.event;
                if(e.ctrlKey){
                    selectedItems.push(this.id);
                    this.className = 'itemSelected';
                }
            }
        }
    }
}

function openFilesPopup(options){
	options = options || {};
	var contentType = options.contentType || 'image';
	var multi = options.multi || false;
	var onSelect = options.onSelect || null;
	var onClose = options.onClose || null;
	$('.MODAL-FILES, .overlay').hide().remove();
	
	function openFolder(name){
		xhr = $.ajax({
			url : "/admin/ajax/",
			type : "post",
			data : { 'ajax_action' : 'getFiles',  'contentType' : contentType, 'folder':name},
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				if (IsJsonString(data)) {
					var result = '<ul id="wlw">';
					$(data).each(function(i){
						result += '\
						<li id="file_'+data[i]['post_id']+'">\
							<label for="s_'+data[i]['post_id']+'">\
								<div class="container"><img style="width:100%;" src="/uploads/image/100/'+data[i]['post_id']+'.jpg"></div>\
							</label>\
							<input class="onchange-selected '+(multi?'multi':'')+'" id="s_'+data[i]['post_id']+'" name="file" type="'+(multi?'checkbox':'radio')+'" value="'+data[i]['post_id']+'" data-url="'+data[i]['post_content']['url']+'" />\
						</li>'; 
					});
					result += '</ul>'; 
			$("#selectFrame").html(result);
				}
			}
		});
	}
	
	xhr = $.ajax({
		url : "/admin/ajax/",
		type : "post",
		data : { 'ajax_action' : 'getFolder',  'contentType' : contentType},
		beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
		complete: function(){ xhr = null; },
		success: function(data){
			if (IsJsonString(data)) {
				data = JSON.parse(data);
				//console.log(data);
				var result = '<ul id="wlw">';
				$(data).each(function(i){
					result += '\
					<li id="file_'+data[i]['post_id']+'">\
						<label for="s_'+data[i]['post_id']+'">\
							<div class="container"><img style="width:100%;" src="/uploads/image/100/'+data[i]['post_id']+'.jpg"></div>\
						</label>\
						<input class="onchange-selected '+(multi?'multi':'')+'" id="s_'+data[i]['post_id']+'" name="file" type="'+(multi?'checkbox':'radio')+'" value="'+data[i]['post_id']+'" data-url="'+data[i]['post_content']['url']+'" />\
					</li>'; 
				});
				result += '</ul>'; 
				
				$('body').append('<div class="overlay"></div><div class="MODAL-FILES">\
					<div class="block">\
						<div class="controls-header">Медиафайлы: <span class="close"><a href="javascript:void(0);">Закрыть</a></span></div>\
						<div class="controls-body">\
						<div class="tabs" style="position: absolute;top: 40px;bottom: 26px;right: 0px;left: 0px;width: auto;">\
						<ul>\
							<li><a href="#tabs-1">Загрузить фото</a></li>\
							<li><a href="#tabs-2">Выбрать из библиотеки</a></li>\
						</ul>\
							<div id="tabs-1" class="content controls-body-row">\
							<div id="filelist">Вашбраузер устарел, вам необходимо Flash, Silverlight или поддерка HTML5.</div>\
								<br />\
								<div id="myuploader">\
									<div id="dragdropzone"><p>Кликни или перетащи файлы сюда, чтобы добавить их в очередь закачки.</p></div>\
									<button class="button" id="uploadfiles" style="margin:15px;">Начать загрузку</button>\
									<div id="que-filelist"><ul></ul></div>\
								</div>\
								<br />\
								<pre id="console"></pre>\
							</div>\
							<div id="tabs-2" class="content controls-body-row">\
							<div id="selectFrame"></div>\
							' + result + '\
							</div>\
						</div>\
							<div class="buttons-set controls-body-row"><button id="selectImage" style="float:right;" class="button">Выбрать</button></div>\
						</div>\
					</div>\
				</div>');
				init();
				$('#tabs-2').mousedown(onMouseDown).mouseup(onMouseUp).mousemove(onMouseMove);
				$( '.MODAL-FILES .close' ).click(function(e){ return closeFilesPopup(onClose); });
				$("#selectImage").click(function(){ return selectImage(options);})
				$("#tabs-2 li label").dblclick(function(){ return selectImage(options);})
				
				$( ".MODAL-FILES .tabs" ).tabs();

				var uploader = new plupload.Uploader({
					runtimes : 'html5,flash,silverlight,html4',
					drop_element : 'dragdropzone',
					browse_button : 'dragdropzone', // you can pass in id...
					container: document.getElementById('myuploader'), // ... or DOM Element itself
					url : "/admin/ajax/",
					multipart_params:{ 'ajax_action' : 'action_upload' },
					filters : {
						max_file_size : '10mb',
						mime_types: [
							{title : "Image files", extensions : "jpg,gif,png"},
						]
					},
					flash_swf_url : '/plupload/js/Moxie.swf',
					silverlight_xap_url : '/plupload/js/Moxie.xap',
					init: {
						PostInit: function() {
							document.getElementById('filelist').innerHTML = '';
							document.getElementById('uploadfiles').onclick = function() { uploader.start(); return false; };
						},
				 
						FilesAdded: function(up, files) {
							plupload.each(files, function(file) {
								document.getElementById('que-filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
							});
						},
				 
						UploadProgress: function(up, file) {
							document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
						},
						
						FileUploaded: function(up, file, response) {
							if (IsJsonString(response.response)) {
								data = JSON.parse(response.response);
								
								var result = '\
								<li id="file_'+data['id']+'">\
									<label for="s_'+data['id']+'">\
										<div class="container"><img style="width:100%;" src="'+data['url']+'"></div>\
									</label>\
									<input class="onchange-selected" id="s_'+data['id']+'" name="file" type="radio" value="'+data['id']+'" data-url="'+data['url']+'" />\
								</li>'; 
								var result2 = '\
								<li id="que_'+data['id']+'">\
									<label for="q_'+data['id']+'">\
										<div class="container"><img style="width:100%;" src="'+data['url']+'"></div>\
									</label>\
									<input class="onchange-selected" id="q_'+data['id']+'" name="file" type="radio" value="'+data['id']+'" data-url="'+data['url']+'" />\
								</li>'; 
								$('#tabs-2 ul').append(result);
								$('#'+file.id).hide().remove();
								$('#tabs-1 #que-filelist ul').append(result2);
								$("#tabs-2 li label").dblclick(function(){ return selectImage(options);})
								$("#tabs-1 li label").dblclick(function(){ return selectImage(options);})
							}
							//document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
						},
						
						
						Error: function(up, err) {
							document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
						}
					}
				});

				uploader.init();

			}
		},
		error: function(jqXHR, textStatus, errorThrown){ console.error(jqXHR); }
	});
}
function closeFilesPopup(cb){ $('.MODAL-FILES, .overlay').hide().remove(); if(cb) { cb(); } }

function selectImage(options){
	options = options || {};
	var multi = options.multi || false;
	var onSelect = options.onSelect || null;
	var onClose = options.onClose || null;
	if(multi){
		var result=[];
		var $img = $('.MODAL-FILES .content input[type="checkbox"]:checked');
		$img.each(function(){
			result.push({id : $(this).val(), src : $(this).attr('data-url')})
		});
	}else{
		var $img = $('.MODAL-FILES .content input[type="radio"]:checked');
		var result = { id : $img.val(), src : $img.attr('data-url')}
	}
	
	if(onSelect) { onSelect(result); }
	closeFilesPopup(onClose);
}