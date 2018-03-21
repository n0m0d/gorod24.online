var MD5 = function(s){function L(k,d){return(k<<d)|(k>>>(32-d))}function K(G,k){var I,d,F,H,x;F=(G&2147483648);H=(k&2147483648);I=(G&1073741824);d=(k&1073741824);x=(G&1073741823)+(k&1073741823);if(I&d){return(x^2147483648^F^H)}if(I|d){if(x&1073741824){return(x^3221225472^F^H)}else{return(x^1073741824^F^H)}}else{return(x^F^H)}}function r(d,F,k){return(d&F)|((~d)&k)}function q(d,F,k){return(d&k)|(F&(~k))}function p(d,F,k){return(d^F^k)}function n(d,F,k){return(F^(d|(~k)))}function u(G,F,aa,Z,k,H,I){G=K(G,K(K(r(F,aa,Z),k),I));return K(L(G,H),F)}function f(G,F,aa,Z,k,H,I){G=K(G,K(K(q(F,aa,Z),k),I));return K(L(G,H),F)}function D(G,F,aa,Z,k,H,I){G=K(G,K(K(p(F,aa,Z),k),I));return K(L(G,H),F)}function t(G,F,aa,Z,k,H,I){G=K(G,K(K(n(F,aa,Z),k),I));return K(L(G,H),F)}function e(G){var Z;var F=G.length;var x=F+8;var k=(x-(x%64))/64;var I=(k+1)*16;var aa=Array(I-1);var d=0;var H=0;while(H<F){Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=(aa[Z]| (G.charCodeAt(H)<<d));H++}Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=aa[Z]|(128<<d);aa[I-2]=F<<3;aa[I-1]=F>>>29;return aa}function B(x){var k="",F="",G,d;for(d=0;d<=3;d++){G=(x>>>(d*8))&255;F="0"+G.toString(16);k=k+F.substr(F.length-2,2)}return k}function J(k){k=k.replace(/rn/g,"n");var d="";for(var F=0;F<k.length;F++){var x=k.charCodeAt(F);if(x<128){d+=String.fromCharCode(x)}else{if((x>127)&&(x<2048)){d+=String.fromCharCode((x>>6)|192);d+=String.fromCharCode((x&63)|128)}else{d+=String.fromCharCode((x>>12)|224);d+=String.fromCharCode(((x>>6)&63)|128);d+=String.fromCharCode((x&63)|128)}}}return d}var C=Array();var P,h,E,v,g,Y,X,W,V;var S=7,Q=12,N=17,M=22;var A=5,z=9,y=14,w=20;var o=4,m=11,l=16,j=23;var U=6,T=10,R=15,O=21;s=J(s);C=e(s);Y=1732584193;X=4023233417;W=2562383102;V=271733878;for(P=0;P<C.length;P+=16){h=Y;E=X;v=W;g=V;Y=u(Y,X,W,V,C[P+0],S,3614090360);V=u(V,Y,X,W,C[P+1],Q,3905402710);W=u(W,V,Y,X,C[P+2],N,606105819);X=u(X,W,V,Y,C[P+3],M,3250441966);Y=u(Y,X,W,V,C[P+4],S,4118548399);V=u(V,Y,X,W,C[P+5],Q,1200080426);W=u(W,V,Y,X,C[P+6],N,2821735955);X=u(X,W,V,Y,C[P+7],M,4249261313);Y=u(Y,X,W,V,C[P+8],S,1770035416);V=u(V,Y,X,W,C[P+9],Q,2336552879);W=u(W,V,Y,X,C[P+10],N,4294925233);X=u(X,W,V,Y,C[P+11],M,2304563134);Y=u(Y,X,W,V,C[P+12],S,1804603682);V=u(V,Y,X,W,C[P+13],Q,4254626195);W=u(W,V,Y,X,C[P+14],N,2792965006);X=u(X,W,V,Y,C[P+15],M,1236535329);Y=f(Y,X,W,V,C[P+1],A,4129170786);V=f(V,Y,X,W,C[P+6],z,3225465664);W=f(W,V,Y,X,C[P+11],y,643717713);X=f(X,W,V,Y,C[P+0],w,3921069994);Y=f(Y,X,W,V,C[P+5],A,3593408605);V=f(V,Y,X,W,C[P+10],z,38016083);W=f(W,V,Y,X,C[P+15],y,3634488961);X=f(X,W,V,Y,C[P+4],w,3889429448);Y=f(Y,X,W,V,C[P+9],A,568446438);V=f(V,Y,X,W,C[P+14],z,3275163606);W=f(W,V,Y,X,C[P+3],y,4107603335);X=f(X,W,V,Y,C[P+8],w,1163531501);Y=f(Y,X,W,V,C[P+13],A,2850285829);V=f(V,Y,X,W,C[P+2],z,4243563512);W=f(W,V,Y,X,C[P+7],y,1735328473);X=f(X,W,V,Y,C[P+12],w,2368359562);Y=D(Y,X,W,V,C[P+5],o,4294588738);V=D(V,Y,X,W,C[P+8],m,2272392833);W=D(W,V,Y,X,C[P+11],l,1839030562);X=D(X,W,V,Y,C[P+14],j,4259657740);Y=D(Y,X,W,V,C[P+1],o,2763975236);V=D(V,Y,X,W,C[P+4],m,1272893353);W=D(W,V,Y,X,C[P+7],l,4139469664);X=D(X,W,V,Y,C[P+10],j,3200236656);Y=D(Y,X,W,V,C[P+13],o,681279174);V=D(V,Y,X,W,C[P+0],m,3936430074);W=D(W,V,Y,X,C[P+3],l,3572445317);X=D(X,W,V,Y,C[P+6],j,76029189);Y=D(Y,X,W,V,C[P+9],o,3654602809);V=D(V,Y,X,W,C[P+12],m,3873151461);W=D(W,V,Y,X,C[P+15],l,530742520);X=D(X,W,V,Y,C[P+2],j,3299628645);Y=t(Y,X,W,V,C[P+0],U,4096336452);V=t(V,Y,X,W,C[P+7],T,1126891415);W=t(W,V,Y,X,C[P+14],R,2878612391);X=t(X,W,V,Y,C[P+5],O,4237533241);Y=t(Y,X,W,V,C[P+12],U,1700485571);V=t(V,Y,X,W,C[P+3],T,2399980690);W=t(W,V,Y,X,C[P+10],R,4293915773);X=t(X,W,V,Y,C[P+1],O,2240044497);Y=t(Y,X,W,V,C[P+8],U,1873313359);V=t(V,Y,X,W,C[P+15],T,4264355552);W=t(W,V,Y,X,C[P+6],R,2734768916);X=t(X,W,V,Y,C[P+13],O,1309151649);Y=t(Y,X,W,V,C[P+4],U,4149444226);V=t(V,Y,X,W,C[P+11],T,3174756917);W=t(W,V,Y,X,C[P+2],R,718787259);X=t(X,W,V,Y,C[P+9],O,3951481745);Y=K(Y,h);X=K(X,E);W=K(W,v);V=K(V,g)}var i=B(Y)+B(X)+B(W)+B(V);return i.toLowerCase()};
var xhr = null;
function split( val ) {
  return val.split( /,\s*/ );
}
function extractLast( term ) {
  return split( term ).pop();
}
/**/

//spf.init();

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
function gcd (a, b) {
	return (b == 0) ? a : gcd (b, a%b);
}
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

function closeFilesPopup(cb){ $('.MODAL-FILES, .overlay').hide().remove(); if(cb) { cb(); } }

function popupItem(data, multi){
  var result = '';
  multi = multi || false;
  switch(data['type']){
    case 'folder':
      result += '\
    <li class="col-sm-2 folder" >\
      <label for="s_'+data['name']+'" data-title="'+data['name']+'">\
        <div class="img-container"><img style="width:100%;" src="/admin/application/views/img/folder.png"></div>\
        <div class="title">'+data['name']+'</div>\
      </label>\
    </li>';
    break;
    case 'image':
      result += '\
    <li class="col-sm-2 image" id="file_'+data['id']+'">\
      <label>\
        <input class="onchange-selected '+(multi?'multi':'')+'" name="file" type="'+(multi?'checkbox':'radio')+'" value="'+data['id']+'" data-name="'+data['original_name']+'" data-type="'+data['type']+'" data-thrumb="/uploads/image/thrumb/'+data['id']+'_150_150.'+data['ext']+'" data-url="'+data['url']+'" />\
        <div class="img-container cont"><img style="width:100%;" src="/uploads/image/thrumb/'+data['id']+'_150_150.'+data['ext']+'"></div>\
      </label>\
	  <span class="edit-image fa fa-pencil fa-2" data-id="'+data['id']+'" data-name="'+data['original_name']+'" data-url="'+data['url']+'" data-width="'+data['image']['width']+'" data-height="'+data['image']['height']+'" ></span>\
    </li>';

    break;
    case 'audio':
      result += '\
    <li class="col-sm-2 audio" id="file_'+data['id']+'">\
      <label>\
        <input class="onchange-selected '+(multi?'multi':'')+'" name="file" type="'+(multi?'checkbox':'radio')+'" value="'+data['id']+'" data-name="'+data['original_name']+'" data-type="'+data['type']+'" data-url="'+data['url']+'" />\
        <div class="img-container cont"><img style="width:100%;" src="/admin/application/views/img/audio.jpg"></div>\
        <div><audio style="width:150px;margin: 0 auto;display: block;" controls src="'+data['url']+'"/></div>\
        <div class="title">'+data['original_name']+'</div>\
      </label>\
    </li>';

    break;

  }
  return result;
}

function editImage(){
	var tabs = $( ".MODAL-FILES .tabs" ).tabs();
	var $obj = $(this);
	var tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span></li>";
	var img_id = $obj.data('id');
	var label = $obj.data('name');
	var url = $obj.data('url');
	var width = $obj.data('width');
	var height = $obj.data('height');
	var id = "tabs-" + img_id;
	var img = "img-edit-" + img_id;
	var li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );
	
	var ratio = gcd(width, height);
	var aspectRatio = width/ratio + ':' + height/ratio;
	
	tabContentHtml = '<div class="img-editor">\
		<div class="img-editor-container"><div class="img"><img id="'+img+'" src="'+url+'" /></div></div>\
		<div class="img-editor-controls">\
			<table>\
				<tr><td>Ширина: </td><td>'+width+'px</td><tr>\
				<tr><td>Высота: </td><td>'+height+'px</td><tr>\
				<tr><td>Размер: </td><td><input class="input" type="number" id = "'+img+'-size" value="100" min="0" max="100" />%</td><tr>\
				<tr><td>Пропорция: </td><td><input class="input" type="text" id = "'+img+'-ratio" value="'+aspectRatio+'" /><label><input type="checkbox" id = "'+img+'-ratio-l" value="1" /> зафиксировать</label></td><tr>\
				<tr><td>Выделение: </td><td><div id = "'+img+'-selection" ></div></td><tr>\
				<tr><td> </td><td><button class="btn btn-success" id="'+img+'-save" >Сохранить</button></td><tr>\
				<tr><td colspan=2><ul id="'+img+'-ul"></ul></td><tr>\
			</table>\
		</div>\
	</div>';
	
	function selectEnd(_img, selection){
		r = gcd(selection.width, selection.height);
		if(r!=0){
			aspectRatio = selection.width/r + ':' + selection.height/r;
			
			var id = _img.id;
			
			var ratio_lock = $('#'+id+'-ratio-l').prop('checked');
			var size = $('#'+id+'-size').val()/100;
		
			var porcX = (_img.naturalWidth / _img.width) * size;
			var porcY = (_img.naturalHeight / _img.height) * size;
		
		    var x1 = Math.round(selection.x1 * porcX);
			var y1 = Math.round(selection.y1 * porcY);
			var x2 = Math.round(selection.x2 * porcX);
			var y2 = Math.round(selection.y2 * porcY);
			var w = Math.round(selection.width * porcX);
			var h = Math.round(selection.height * porcY);
			
			if(!ratio_lock) $('#'+id+'-ratio').val(aspectRatio);
			$('#'+id+'-selection').html('Ширина: ' + w + 'px; Высота: ' + h + 'px;');
		}
	}
	
	if($('#'+id).length==0){
		tabs.find( ".ui-tabs-nav" ).append( li );
		tabs.append( "<div id='" + id + "' class='content controls-body-row'><p>" + tabContentHtml + "</p></div>" );
		tabs.tabs( "refresh" );
	}
	tabs.tabs( "load", '#'+id );
	
	
	var _parent = $('#'+img).parent();
	
	var ias = $('#'+img).imgAreaSelect({
		instance: true,
        parent: _parent,
        handles: true,
		setOptions:true,
		onSelectEnd: selectEnd,
    });
	
	$('#'+img+'-size').on('click change keyup focusout', function(){
		var i = $('#'+img).get(0);
		var size = $(this).val()/100;
		var selection = ias.getSelection(true);
		var porcX = (i.naturalWidth / i.width) * size;
		var porcY = (i.naturalHeight / i.height) * size;
		
		var x1 = Math.round(selection.x1 * porcX);
		var y1 = Math.round(selection.y1 * porcY);
		var x2 = Math.round(selection.x2 * porcX);
		var y2 = Math.round(selection.y2 * porcY);
		var w = Math.round(selection.width * porcX);
		var h = Math.round(selection.height * porcY);
		
		$('#'+img+'-selection').html('Ширина: ' + w + 'px; Высота: ' + h + 'px;');
		
	});
	
	
	$('#'+img+'-save').click(function(){
		var i = $('#'+img).get(0);
		var size = $('#'+img+'-size').val();
		var selection = ias.getSelection(true);
		if(selection.height==0 && selection.width==0){
			alert("Вы не выбрали область.");
		}
		else {
		var porcX = (i.naturalWidth / i.width);
		var porcY = (i.naturalHeight / i.height);
		
		var x1 = Math.round(selection.x1 * porcX);
		var y1 = Math.round(selection.y1 * porcY);
		var x2 = Math.round(selection.x2 * porcX);
		var y2 = Math.round(selection.y2 * porcY);
		var w = Math.round(selection.width * porcX);
		var h = Math.round(selection.height * porcY);
		
		var image = {
			"id" : img_id,
			"x1" : x1,
			"y1" : y1,
			"x2" : x2,
			"y2" : y2,
			"w" : w,
			"h" : h,
			"percent" : size
		};
		
		xhr = $.ajax({
			url : "/admin/ajax/cropImage",
			type : "post",
			dataType: 'json',
			data : { 'id':img_id, 'image':image },
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				var result = '<ul class="row">';
				result += popupItem(data, false);
				result += '</ul>';
				console.log(data);
			},
			complete:function (response, status ) {
				if(response.status==401){
					alert("Время сессии истекло.");
					window.location.reload();
				}
			}
		});
		}
	});
	
	$('#'+img+'-ratio').on('click change keyup focusout', function(){
		var ch = $('#'+img+'-ratio-l').prop('checked');
		if(ch){
			ias.setOptions({ aspectRatio : $(this).val() });
		}
	});
	
	$('#'+img+'-ratio-l').change(function(){
		var ch = $(this).prop('checked');
		if(ch){
			ias.setOptions({ aspectRatio : $('#'+img+'-ratio').val() });
		}
		else {
			ias.setOptions({ aspectRatio : false });
		}
		ias.update();
	});
	
	
}

function openFilesPopup(options){
	options = options || {};
	var contentType = options.contentType || 'image';
	var accept = options.accept || '*';
	var multi = options.multi || false;
	var onSelect = options.onSelect || null;
	var onClose = options.onClose || null;
	$('.MODAL-FILES, .overlay').hide().remove();

	var tabs=null,
	tabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Remove Tab</span></li>",
    tabCounter = 3;

	function openFolder(name){
		xhr = $.ajax({
			url : "/admin/ajax/uploads/files",
			type : "post",
			data : { 'folder':name, 'accept':accept },
			beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
			complete: function(){ xhr = null; },
			success: function(data){
				if (IsJsonString(data)) {
					data = JSON.parse(data);
					var result = '<ul class="row">';
					$(data).each(function(i){
            result += popupItem(data[i], multi);
					});
					result += '</ul>';
					$("#tabs-"+name).html(result);
					$(".edit-image").click(editImage);
					
					$("#tabs-"+name+" li.image .cont").dblclick(function(){ return selectImage(options);})

				}
			},
			complete:function (response, status ) {
				if(response.status==401){
					alert("Время сессии истекло.");
					window.location.reload();
				}
			}
		});
	}

	xhr = $.ajax({
		url : "/admin/ajax/uploads/folders",
		type : "post",
		data : { 'accept':accept },
		beforeSend: function( jqXHR, settings ){ if(xhr != null){ xhr.abort();console.log('ajax is aborting');	} },
		complete: function(){ xhr = null; },
		success: function(data){
			if (IsJsonString(data)) {
				data = JSON.parse(data);
				var result = '<ul class="row" id="wlw">';
				$(data).each(function(i){
					result += '\
				<li class="col-sm-2 folder" id="file_'+data[i]+'" >\
					<label for="s_'+data[i]+'" data-title="'+data[i]+'">\
						<div class="img-container"><img style="width:100%;" src="/admin/application/views/img/folder.png"></div>\
						<div class="title">'+data[i]+'</div>\
					</label>\
				</li>';
				});
				result += '</ul>';

				$('body').append('<div class="overlay"></div><div class="MODAL-FILES">\
				<div class="block">\
					<div class="controls-header">Медиафайлы: <span class="close"><a href="javascript:void(0);">Закрыть</a></span></div>\
					<div class="controls-body">\
					<div class="tabs" style="position: absolute;top: 40px;bottom: 40px;right: 0px; left: 0px; width: auto;">\
					<ul>\
						<li><a href="#tabs-1">Загрузить фото</a></li>\
						<li><a href="#tabs-2">Выбрать из библиотеки</a></li>\
					</ul>\
						<div id="tabs-1" class="content controls-body-row">\
							<div id="filelist">Вашбраузер устарел, вам необходимо Flash, Silverlight или поддерка HTML5.</div>\
								<br />\
								<div id="myuploader">\
									<div id="dragdropzone"><p>Кликни или перетащи файлы сюда, чтобы добавить их в очередь закачки.</p></div>\
									<button class="btn btn-primary" id="uploadfiles" style="margin:15px;">Начать загрузку</button>\
									<div id="que-filelist"><ul class="row"></ul></div>\
								</div>\
								<br />\
								<pre id="console"></pre>\
							</div>\
						<div id="tabs-2" class="content controls-body-row">\
							<div id=""></div>\
							' + result + '\
						</div>\
					</div>\
						<div class="buttons-set controls-body-row"><button id="selectImage" style="float:right;" class="btn btn-info">Выбрать</button></div>\
					</div>\
				</div>\
			</div>');
				//init();
				//$('#tabs-2').mousedown(onMouseDown).mouseup(onMouseUp).mousemove(onMouseMove);
				$( '.MODAL-FILES .close' ).click(function(e){ return closeFilesPopup(onClose); });
				$("#selectImage").click(function(){ return selectImage(options);})

				tabs = $( ".MODAL-FILES .tabs" ).tabs();

				$('.MODAL-FILES .tabs #tabs-2 .folder label').click(function(event){
					var $obj = $(this);
					var label = $obj.data('title') || "Tab " + tabCounter,
					id = "tabs-" + label,
					li = $( tabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) ),
					tabContentHtml = "Tab " + tabCounter + " content.";
					if($('#'+id).length==0){
						tabs.find( ".ui-tabs-nav" ).append( li );
						tabs.append( "<div id='" + id + "' class='content controls-body-row'><p>" + tabContentHtml + "</p></div>" );
						tabs.tabs( "refresh" );
						tabCounter++;
					}
					tabs.tabs( "load", '#'+id );
					openFolder(label);
				});

				// Close icon: removing the tab on click
				tabs.on( "click", "span.ui-icon-close", function() {
					var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
					$( "#" + panelId ).remove();
					tabs.tabs( "refresh" );
				});
				switch(accept){
					case 'audio': var filters={
						max_file_size : '20mb',
						mime_types: [{title : "Audio files", extensions : "mp3,ogg,wma"},]
					};
						break;
					case 'image': var filters={
						max_file_size : '20mb',
						mime_types: [{title : "Image files", extensions : "jpg,gif,png"},]
					};
						break;
					default: var filters=false; break;
				}
				
				var uploader = new plupload.Uploader({
					runtimes : 'html5,flash,silverlight,html4',
					drop_element : 'dragdropzone',
					browse_button : 'dragdropzone', // you can pass in id...
					container: document.getElementById('myuploader'), // ... or DOM Element itself
					url : "/admin/control/files/upload/",
					filters : filters,
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
              console.log(response.response);
              if (IsJsonString(response.response)) {
								data = JSON.parse(response.response);
                console.log(data);
                if(data.OK==1){
                var result = '';
                result += popupItem(data, multi);
								$('#'+file.id).hide().remove();
								$('#tabs-1 #que-filelist ul').append(result);
                $('#tabs-1 #que-filelist ul li label').dblclick(function(){ return selectImage(options);})
              } else {
                  $('#console').append('<div>'+data['info']+'</div>');
              }
								//$("#tabs-2 li label").dblclick(function(){ return selectImage(options);})
								//$("#tabs-1 li label").dblclick(function(){ return selectImage(options);})
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
		error: function(jqXHR, textStatus, errorThrown){ console.error(jqXHR); },
		complete:function (response, status ) {
			if(response.status==401){
				alert("Время сессии истекло.");
				window.location.reload();
			}
		}
	});
}


function selectImage(options){
	options = options || {};
	var multi = options.multi || false;
	var onSelect = options.onSelect || null;
	var onClose = options.onClose || null;
	if(multi){
		var result=[];
		var $img = $('.MODAL-FILES .content input[type="checkbox"]:checked');
		$img.each(function(){
			result.push({id : $(this).val(), src : $(this).attr('data-url'), thrumb : $img.attr('data-thrumb')})
		});
	}else{
		var $img = $('.MODAL-FILES .content input[type="radio"]:checked');
		var result = { id : $img.val(), name: $img.attr('data-name'), type : $img.attr('data-type'), src : $img.attr('data-url'), thrumb : $img.attr('data-thrumb')}
	}

	if(onSelect) { onSelect(result); }
	closeFilesPopup(onClose);
}

var InitChosen = function($o){
	$o.each(function(i, element){
		var $element = $(element);
		var value = $element.val();

		var CHOSEN_OPTIONS = {
				width: ($(window).width()<768?"100%":"300px"),
				no_results_text: "Не найдено ",
				placeholder_text: "",
				disable_search: true,
				disable_search_threshold: 20,
			};
		if($element.data('own')=='1' && $element.data('own')!=undefined){
			CHOSEN_OPTIONS.create_option_text = 'Свой вариант';
			CHOSEN_OPTIONS.create_option = true;
			CHOSEN_OPTIONS.persistent_create_option = true;
			CHOSEN_OPTIONS.skip_no_results = true;
			CHOSEN_OPTIONS.disable_search = false;
			CHOSEN_OPTIONS.disable_search_threshold = 0;
		}
		if($element.data('data_type')){
			CHOSEN_OPTIONS.data_type=$element.data('data_type');
		}
		if($element.data('multiple')){
			CHOSEN_OPTIONS.max_selected_options=$element.data('multiple');
		}
		if($element.attr('placeholder')){
			CHOSEN_OPTIONS.placeholder_text_multiple=$element.attr('placeholder');
		}
		if($element.data('width')){
			CHOSEN_OPTIONS.width=$element.data('width');
		}
		if($element.data('search')){
			CHOSEN_OPTIONS.disable_search = false;
			CHOSEN_OPTIONS.disable_search_threshold = $element.data('search');
		}

		$element.chosen(CHOSEN_OPTIONS);
	});
}

$(function() {

	$(document).on("click", ".ajax-delete", function (e) {
        e.preventDefault();
        NProgress.start();
        var href = window.location.href;
		var $form = $(this).parents('form:first');
        $.ajax({
            type : 'POST',
            data: $form.serialize() + '&del=1',
            url : $form.attr('action'),
            success : function (result) {
                NProgress.done();
                try {
                    var $main_content = $(result).find(".sectright").html() || $(result).filter(".sectright").html();
                    $('.sectright').html($main_content);

                    var state = { 'page_id': MD5(href) };
                    var title = $(result).filter('title').text();
                    InitChosen($('.filter-select'));
                    main();
                } catch(e) { console.error(e); }
                history.pushState(state, title, href);
            },
			complete:function (response, status ) {
				if(response.status==401){
					alert("Время сессии истекло.");
					window.location.reload();
				}
			}
        });
    });
	
	$(document).on("click", ".ajax-on", function (e) {
        e.preventDefault();
        NProgress.start();
        var href = window.location.href;
		var $form = $(this).parents('form:first');
        $.ajax({
            type : 'POST',
            data: $form.serialize() + '&on=1',
            url : $form.attr('action'),
            success : function (result) {
				NProgress.done();
                try {
                    var $main_content = $(result).find(".sectright").html() || $(result).filter(".sectright").html();
                    $('.sectright').html($main_content);

                    var state = { 'page_id': MD5(href) };
                    var title = $(result).filter('title').text();
                    InitChosen($('.filter-select'));
                    main();
                } catch(e) { console.error(e); }
                history.pushState(state, title, href);
            },
			complete:function (response, status ) {
				if(response.status==401){
					alert("Время сессии истекло.");
					window.location.reload();
				}
			}
        });
    });
	
	$(document).on("click", ".ajax-off", function (e) {
        e.preventDefault();
        NProgress.start();
        var href = window.location.href;
		var $form = $(this).parents('form:first');
        $.ajax({
            type : 'POST',
            data: $form.serialize() + '&off=1',
            url : $form.attr('action'),
            success : function (result) {
				NProgress.done();
                try {
                    var $main_content = $(result).find(".sectright").html() || $(result).filter(".sectright").html();
                    $('.sectright').html($main_content);

                    var state = { 'page_id': MD5(href) };
                    var title = $(result).filter('title').text();
                    InitChosen($('.filter-select'));
                    main();
                } catch(e) { console.error(e); }
                history.pushState(state, title, href);
            },
			complete:function (response, status ) {
				if(response.status==401){
					alert("Время сессии истекло.");
					window.location.reload();
				}
			}
        });
    });
	

    $(document).on("click", "#maincheck", function() {
        if($('#maincheck').prop('checked')) {
            $('.check-in').prop('checked', true);
        } else {
            $('.check-in').prop('checked', false);
        }
    });

$.timepicker.regional['ru'] = {
	timeOnlyTitle: 'Выберите время',
	timeText: 'Время',
	hourText: 'Часы',
	minuteText: 'Минуты',
	secondText: 'Секунды',
	millisecText: 'Миллисекунды',
	timezoneText: 'Часовой пояс',
	currentText: 'Сейчас',
	closeText: 'Закрыть',
	timeFormat: 'HH:mm',
	amNames: ['AM', 'A'],
	pmNames: ['PM', 'P'],
	isRTL: false
};
$.timepicker.setDefaults($.timepicker.regional['ru']);

$.datepicker.regional['ru'] = {
	closeText: 'Закрыть',
	prevText: '<Пред',
	nextText: 'След>',
	currentText: 'Сегодня',
	monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
	'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
	monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
	'Июл','Авг','Сен','Окт','Ноя','Дек'],
	dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
	dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
	dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
	weekHeader: 'Не',
	dateFormat: 'yy-mm-dd',
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional['ru']);

	$(document).on("click", 'a.ajax-load', function(e){
		e.preventDefault();
		var $a = $(this);
		var href = $(this).attr('href');
		var _confirm = $(this).data('confirm');
		
		if(_confirm) {
			swal({
			  title: _confirm,
			  type: 'warning',
			  showCancelButton: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Да',
			  cancelButtonText: 'Отмена',
			  confirmButtonClass: 'btn btn-success',
			  cancelButtonClass: 'btn btn-danger',
			  buttonsStyling: false,
			  reverseButtons: true
			}).then((result) => {
			  if (result.value) {
		
				NProgress.start();
				$.ajax({
					type : 'POST',
					data:{"side":"server"},
					url : href,
					success : function (result) {
						NProgress.done();
						try {
							$a.parent().find('a.active').removeClass('active');
							$a.addClass('active');
							var $sectcenter = $(result).find('.sectcenter');
							var $sectright = $(result).find('.sectright');
							var $help = $(result).find('a#help-button');
							if($help.length){
								if($help.parent().hasClass('hidden')){ $('a#help-button').parent().addClass('hidden'); } else { $('a#help-button').parent().removeClass('hidden'); }
								$('a#help-button').data('src', $help.data('src'));
							}
							var center = $a.data('center');
							var left = $a.data('left');
							var _history = $a.data('history');
							if(center!==false){ $('.sectcenter').html($sectcenter.html()); }
							$('.sectright').html($sectright.html());

							var state = { 'page_id': MD5(href) };
							var title = $(result).filter('title').text();
							var description = $('meta[name="description"]').attr("content");
							$('head title').text(title);
							$('head meta[name="description"]').attr("content", description);
							InitChosen($('.filter-select'));
							main();
						} catch(e) { console.error(e); }
						
						if(_history!==false){ history.pushState(state, title, href); }

					},
					complete:function (response, status ) {
						if(response.status==401){
							alert("Время сессии истекло.");
							window.location.reload();
						}
					}

		});
				
			  } else if (result.dismiss === 'cancel') {
				swal(
				  'Отменено',
				  '',
				  'error'
				)
			  }
			});
		}
		else{
		
		
		NProgress.start();
		$.ajax({
			type : 'POST',
			data:{"side":"server"},
			url : href,
			success : function (result) {
				NProgress.done();
				try {
					$a.parent().find('a.active').removeClass('active');
					$a.addClass('active');
					var $sectcenter = $(result).find('.sectcenter');
					var $sectright = $(result).find('.sectright');
					var $help = $(result).find('a#help-button');
					if($help.length){
						if($help.parent().hasClass('hidden')){ $('a#help-button').parent().addClass('hidden'); } else { $('a#help-button').parent().removeClass('hidden'); }
						$('a#help-button').data('src', $help.data('src'));
					}
					var center = $a.data('center');
					var left = $a.data('left');
					var _history = $a.data('history');
					if(center!==false){ $('.sectcenter').html($sectcenter.html()); }
					$('.sectright').html($sectright.html());

					var state = { 'page_id': MD5(href) };
					var title = $(result).filter('title').text();
					var description = $('meta[name="description"]').attr("content");
					$('head title').text(title);
					$('head meta[name="description"]').attr("content", description);
					InitChosen($('.filter-select'));
					main();
				} catch(e) { console.error(e); }
				
				if(_history!==false){ history.pushState(state, title, href); }

			},
			complete:function (response, status ) {
				if(response.status==401){
					alert("Время сессии истекло.");
					window.location.reload();
				}
			}

		});
		}
	});
	
	window.onpopstate = function( e ) {
		var href = window.location.href
		NProgress.start();
		$.ajax({
			type : 'POST',
			data:{"side":"server"},
			url : href,
			success : function (result) {
				NProgress.done();
				try {
					var $sectcenter = $(result).find('.sectcenter');
					var $sectright = $(result).find('.sectright');
					var center = true;
					var left = true;
					var _history = false;
					$('.sectright').html($sectright.html());
					var $help = $(result).find('a#help-button');
					if($help.length){
						if($help.parent().hasClass('hidden')){ $('a#help-button').parent().addClass('hidden'); } else { $('a#help-button').parent().removeClass('hidden'); }
						$('a#help-button').data('src', $help.data('src'));
					}
					var state = { 'page_id': MD5(href) };
					var title = $(result).filter('title').text();
					var description = $('meta[name="description"]').attr("content");
					$('head title').text(title);
					$('head meta[name="description"]').attr("content", description);
					InitChosen($('.filter-select'));
					main();
				} catch(e) { console.error(e); }
				
				if(_history!==false){ history.pushState(state, title, href); }

			},
			complete:function (response, status ) {
				if(response.status==401){
					alert("Время сессии истекло.");
					window.location.reload();
				}
			}

		});
	}

	$(document).on("click", '.dropdown-button', function(e) {
		e.preventDefault();
		$(this).toggleClass('changed');
		var id = $(this).attr("id");
		$(".dropdown-list-" + id).slideToggle('fast');

	});

	$(document).on("click", '.indropdown-button', function() {
		$(this).toggleClass('changed');
		var id = $(this).attr("id");
		$(".indropdown-list-" + id).slideToggle('fast');
	});


	InitChosen($('.filter-select'));

	$(document).on("click", '.check-all', function() {
		var name = $(this).data('name');
		if($(this).prop('checked')) {
			$('input[type=checkbox][name="'+name+'"]').prop('checked', true);
		} else {
			$('input[type=checkbox][name="'+name+'"]').prop('checked', false);
		}
	});
	
	var main = function() {
		$('.arrows-hamburger-wrap').on("click", function() {

			$('.arrows-hamburger-wrap').css("opacity", "0");

			$('.mobile-menu').css("visibility", "visible");

			$('.mobile-menu').animate({
				left: '0px'
			}, 200);
			/*
			$('.main-header').animate({
				left: '285px'
			}, 200);

			$('body').animate({
				left: '285px'
			}, 200);
			*/
		});

		$('.arrows-remove-wrap').on("click", function() {

			$('.arrows-hamburger-wrap').css("opacity", "1");

			$('.mobile-menu').animate({
				left: '-285px'
			}, 200, function(){ $('.mobile-menu').css("visibility", "hidden"); });
			/*
			$('.main-header').animate({
				left: '0px'
			}, 200);

			$('body').animate({
				left: '0px'
			}, 200);
			*/
		});
		
		$(".doubleScroll").doubleScroll({
			contentElement: undefined, // Widest element, if not specified first child element will be used
			scrollCss: {                
				'overflow-x': 'auto',
				'overflow-y': 'hidden'
			},
			contentCss: {
				'overflow-x': 'auto',
				'overflow-y': 'hidden'
			},
			onlyIfScroll: true, // top scrollbar is not shown if the bottom one is not present
			resetOnWindowResize: true // recompute the top ScrollBar requirements when the window is resized
		});
		
		//$('.table-responsive').responsiveTable();
		/*
		$('.table-adapt').footable({
			"filtering": {
				"enabled": false
			},
			"sorting": {
				"enabled": true
			},
		}).on('ready.ft.table', function(){		});
		*/
	};

	$(document).ready(main);
});
