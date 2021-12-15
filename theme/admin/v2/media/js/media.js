var MediaManager = {};
var counter = 0;
MediaManager.isShowLoading = false;
MediaManager.isUploading = false;
MediaManager.showLoading = function(){
	if(MediaManager.isShowLoading ) return;
	MediaManager.isShowLoading = true;
	$(".loading").fadeIn(500);
}
MediaManager.hideLoading = function(){
	if(MediaManager.isUploading) return;
	MediaManager.isShowLoading  = false;
	$(".loading").fadeOut(500);
}
MediaManager.initDropzone = function(){
	$("div#media-manage").dropzone({
		params: globalobj ,
		maxFiles: 20,
		parallelUploads:2,
		url: globalBaseUrl+"/Media/uploadFile",
		uploadMultiple: true,
		clickable:false,
		addedfile: function(file) {
		},
		thumbnail: function(file, dataUrl) {
		},
		sendingmultiple: function(x){
			MediaManager.isUploading =true;
			MediaManager.showLoading();
		},
		queuecomplete: function(x){
			MediaManager.isUploading =false;
			MediaManager.hideLoading();
		},
		uploadprogress: function(file, progress, bytesSent) {
			var item = $('p:contains("'+file.name+'")');
			if(item==undefined || item.length==0){
				$(".list-notify #mCSB_1_container").append('<div class="notify">'+
					'<div class="notify-item">'+
					'<div class="pull-left thumb">'+

					'</div>'+
					'<div class="pull-left info">'+
					'<p class="name">'+file.name+'</p>'+
					'<p class="progress"><span>'+(Math.round(progress*100)/100)+'%</span></p>'+
					'</div>'+
					'</div>'+
					'</div>');
			}
			$('p:contains("'+file.name+'")').next().find("span").css({width:progress+"%"}).text((Math.round(progress*100)/100)+'%');	
			if(progress>=100){
				$('p:contains("'+file.name+'")').parents(".notify").fadeOut(2000,function(){
					$(this).remove();
				})	
			}

		},
		dragenter:function(event){
			$('.dz-drag-hover .hover-upload').show();
			counter++;
		},
		dragleave:function(event){
			counter--;
			if(counter==0){
				$('.dz-drag-hover .hover-upload').fadeOut(500);
			}
		},
		drop:function(event){
			$('.dz-drag-hover .hover-upload').fadeOut(500);
			counter==0;
		},
		successmultiple:function(files){
			try{
				if(files.length>0){
					var json =JSON.parse(files[0].xhr.response);
					if(json!=undefined){
						MediaManager.getLastedFileCreated(json);
					}
				}
				
			}
			catch(e){}
		}
	});
	$("#upload-modal .modal-body").dropzone({
		params: globalobj ,
		url: globalBaseUrl+"/Media/uploadFile",
		uploadMultiple: true,
		addedfile: function(file) {
    		// console.log(file);
    	},
    	thumbnail: function(file, dataUrl) {
	    // console.log(dataUrl);
	},
	sendingmultiple: function(x){
		MediaManager.isUploading =true;
		MediaManager.showLoading();
	},
	queuecomplete: function(x){
		MediaManager.isUploading =false;
		MediaManager.hideLoading();
	},
	uploadprogress: function(file, progress, bytesSent) {
		var item = $('p:contains("'+file.name+'")');
		if(item==undefined || item.length==0){
			$(".list-notify #mCSB_1_container").append('<div class="notify">'+
				'<div class="notify-item">'+
				'<div class="pull-left thumb">'+

				'</div>'+
				'<div class="pull-left info">'+
				'<p class="name">'+file.name+'</p>'+
				'<p class="progress"><span>'+(Math.round(progress*100)/100)+'%</span></p>'+
				'</div>'+
				'</div>'+
				'</div>');
		}
		$('p:contains("'+file.name+'")').next().find("span").css({width:progress+"%"}).text((Math.round(progress*100)/100)+'%');	
		if(progress>=100){
			$('p:contains("'+file.name+'")').parents(".notify").fadeOut(2000,function(){
				$(this).remove();
			})	
		}
	},
	dragenter:function(event){
		$('.dz-drag-hover .hover-upload').show();
		counter++;
	},
	dragleave:function(event){
		counter--;
		if(counter==0){
			$('.dz-drag-hover .hover-upload').fadeOut(500);
		}
	},
	drop:function(event){
		$('.dz-drag-hover .hover-upload').fadeOut(500);
		counter==0;
	},
	successmultiple:function(files){
		try{
			if(files.length>0){
				var json =JSON.parse(files[0].xhr.response);
				if(json!=undefined){
					MediaManager.getLastedFileCreated(json);
				}
			}

		}
		catch(e){}
	}
});
}
MediaManager.ajaxSetup = function(){
	$(document).ajaxStart(function() {
	    MediaManager.showLoading();
	});
	$(document).ajaxComplete(function(event, xhr, settings) {
	    MediaManager.hideLoading();
	});
}
MediaManager.init = function(){
	MediaManager.initDropzone();
	MediaManager.ajaxSetup();
}
MediaManager.getLastedFolderCreated = function($id){
	window.location.reload();
	return;
	$.ajax({
		url: globalBaseUrl+"/Media/getInfoLasted",
		type: 'POST',
		data: {id: $id},
	})
	.done(function(data) {
		var $newItems = $(data);
		$('.media-content .row').prepend( $newItems).isotope( 'reloadItems' ).isotope();
	});

}
MediaManager.getLastedFileCreated = function($id){
	$.ajax({
		url: globalBaseUrl+"/Media/getInfoFileLasted",
		type: 'POST',
		data: {id: $id},
	})
	.done(function(data) {
		var $newItems = $(data);
		$('.media-content .row').prepend( $newItems).isotope( 'reloadItems' ).isotope();
	});
	
}
MediaManager.submitNewFolder = function(_this){
	$('#folder-modal').modal("hide");
	$.ajax({
		url: $(_this).attr("action"),
		type: "POST",
		data: $(_this).serialize(),
	})
	.done(function($data) {
		try{
			var json = JSON.parse($data);
			if(json.code == SUCCESS){
				if(parseInt(json.message)>0 ){
					MediaManager.getLastedFolderCreated(json.message);
				}
			}
			if(json.message !=undefined){
				MediaManager.showMessageBox(json.message);
			}
		}
		catch(e){}
	})
	.fail(function() {
	});
	return false;
}
MediaManager.showMessageBox = function(text){
	if(!Number.isNaN(text) && parseInt(text)==NaN){
		$('#snackbar').html(text);
		$('#snackbar').stop().fadeIn().delay(500).fadeOut();
	}
	
}
MediaManager.deleteFolder = function(_this){
	bootbox.confirm("Bạn có chắc chắn muốn xóa?!", function(result){
	if (result) {
		var id = $(_this).attr("dt-id");
		if(id!=undefined){
			$.ajax({
				url: globalBaseUrl+'/Media/deleteFolder',
				type: 'POST',
				data: {id: id},
			})
			.done(function(data) {
				try{
					var json = JSON.parse(data);
					if(json.code==SUCCESS){
						$('.media-content .row').isotope( 'remove', $('#folder'+json.message) ).isotope();
					}
					if(json.message !=undefined){
						MediaManager.showMessageBox(json.message);
					}
				}catch(e){}
			});

		}
	}});
}
MediaManager.deleteFolderPermanent = function(_this){
	bootbox.confirm("Bạn có chắc chắn muốn xóa vĩnh viễn?!", function(result){
	if (result) {
		var id = $(_this).attr("dt-id");
		if(id!=undefined){
			$.ajax({
				url: globalBaseUrl+'/Media/deleteFolder',
				type: 'POST',
				data: {id: id,permanent:true},
			})
			.done(function(data) {
				try{
					var json = JSON.parse(data);
					if(json.code==SUCCESS){
						window.location.reload();
					}
					if(json.message !=undefined){
						MediaManager.showMessageBox(json.message);
					}
				}catch(e){}
			});

		}
	}});
}
MediaManager.deleteFile = function (_this){
	bootbox.confirm("Bạn có chắc chắn muốn xóa?!", function(result){
		if (result) {
			var id = $(_this).attr("dt-id");
			if(id!=undefined){
				$.ajax({
					url: globalBaseUrl+'/Media/deleteFile',
					type: 'POST',
					data: {id: id},
				})
				.done(function(data) {
					try{
						var json = JSON.parse(data);
						if(json.code==SUCCESS){
							$('.media-content .row').isotope( 'remove', $('#file'+json.message) ).isotope();
						}
						if(json.message !=undefined){
							MediaManager.showMessageBox(json.message);
						}
					}catch(e){}
				});

			}
		}});
}
MediaManager.deleteFilePermanent = function (_this){
	bootbox.confirm("Bạn có chắc chắn muốn xóa vĩnh viễn file này?!", function(result){
		if (result) {
			var id = $(_this).attr("dt-id");
			if(id!=undefined){
				$.ajax({
					url: globalBaseUrl+'/Media/deleteFile',
					type: 'POST',
					data: {id: id,permanent:true},
				})
				.done(function(data) {
					try{
						var json = JSON.parse(data);
						if(json.code==SUCCESS){
							$('.media-content .row').isotope( 'remove', $('#file'+json.message) ).isotope();
						}
						if(json.message !=undefined){
							MediaManager.showMessageBox(json.message);
						}
					}catch(e){}
				});

			}
		}});
}
MediaManager.restore = function (_this){
	bootbox.confirm("Bạn có chắc chắn muốn restore?!", function(result){
		if (result) {
			var id = $(_this).attr("dt-id");
			if(id!=undefined){
				$.ajax({
					url: globalBaseUrl+'/Media/restore',
					type: 'POST',
					data: {id: id},
				})
				.done(function(data) {
					try{
						var json = JSON.parse(data);
						if(json.code==SUCCESS){
							window.location.reload();
						}
						if(json.message !=undefined){
							MediaManager.showMessageBox(json.message);
						}
					}catch(e){}
				});

			}
		}});
}
MediaManager.deleteMultiFile = function (){
	bootbox.confirm("Bạn có chắc chắn muốn xóa?!", function(result){
		if (result) {
			var ids =$('input[name=listselected]').val();
			$.ajax({
				url: globalBaseUrl+'/Media/deleteAll',
				type: 'POST',
				data: {ids: ids},
			})
			.done(function(data) {
				try{
					var json = JSON.parse(data);
					if(json.code==200){
						window.location.reload();
					}
				}
				catch(e){}
			});
		}
	});
	
	
}

MediaManager.getListFolder = function(){
	var s = $('#list-folder-modal');
	if(s.length>0){
		s.remove();
	}
	$.ajax({
		url: globalBaseUrl+'/Media/listFolder',
		type: 'POST',
		data: {param1: 'value1'},
	})
	.done(function($data) {
		$('body').append($data);
		$('#list-folder-modal').modal();
	});
	
}
MediaManager.getListFolderMove = function(){
	var s = $('#list-folder-modal');
	if(s.length>0){
		s.remove();
	}
	$.ajax({
		url: globalBaseUrl+'/Media/listFolderMove',
		type: 'POST',
		data: {param1: 'value1'},
	})
	.done(function($data) {
		$('body').append($data);
		$('#list-folder-modal').modal();
	});
	
}
MediaManager.copyFile = function (active){
	if(globalObjectFile!=undefined && active !=undefined){
		$.ajax({
			url: globalBaseUrl+'/Media/copyFile',
			type: 'POST',
			data: {id:globalObjectFile.id,idfolder:$(active).attr('dt-id')},
		})
		.done(function(data) {
			try{
				var json = JSON.parse(data);
				bootbox.alert(json.message);
			}
			catch(e){}
		});
		
	}
	return false;
}
MediaManager.moveFile = function (active){
	if(globalObjectFile!=undefined && active !=undefined){
		$.ajax({
			url: globalBaseUrl+'/Media/moveFile',
			type: 'POST',
			data: {id:globalObjectFile.id,idfolder:$(active).attr('dt-id')},
		})
		.done(function(data) {
			try{
				var json = JSON.parse(data);
				if(json.code==SUCCESS){
					$('.media-content .row').isotope( 'remove', $('#file'+json.message) ).isotope();
				}
				if(json.message !=undefined){
					MediaManager.showMessageBox(json.message);
				}
			}
			catch(e){}
		});
		
	}
	return false;
}
MediaManager.getDetailFile = function(id){
	$.ajax({
		url: globalBaseUrl+'/Media/getDetailFile',
		type: 'POST',
		data: {id:id},
	})
	.done(function(data) {
		try{
			$('#info-modal .modal-body').html(data);
			$('#info-modal').modal("show");
		}	
		catch(e){}
	});
	
}
MediaManager.saveDetailFile = function(_this){
	$.ajax({
		url: globalBaseUrl+'/Media/saveDetailFile',
		type: 'POST',
		data: $(_this).serialize(),
	})
	.done(function(data) {
		$('#info-modal').modal("hide");
	});
	
}
MediaManager.writeMetadata = function(_this){
	$.ajax({
		url: globalBaseUrl+'/Media/writeMetadata',
		type: 'POST',
		data: $(_this).serialize(),
	})
	.done(function(data) {
		$('#advance-modal').modal("hide");
	});
	
}
MediaManager.getMetadataFile = function (id){
	$.ajax({
		url: globalBaseUrl+'/Media/getMedatata',
		type: 'POST',
		data: {id:id},
	})
	.done(function(data) {
		try{
			$('#advance-modal .modal-body').html(data);
			$('#advance-modal').modal("show");
		}	
		catch(e){}
	});
}
MediaManager.getParameterByName = function (name) {
	name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
	var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
	results = regex.exec(location.search);
	return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
MediaManager.applyChooseImage = function(){
	var istiny = MediaManager.getParameterByName('istiny');
	var arrFiles = $('.fileSelected');
	if(istiny==1){
		var html = "";
		for (var i = 0; i < arrFiles.length; i++) {
			var item = $(arrFiles[i]);
			var _parent = item.parent().parent().parent();
			var datafile =  _parent.attr('data-file');
			var objfile = JSON.parse(datafile);
			html +="<img title='"+objfile.title+"' alt='"+objfile.alt+"' class='img-responsive' src='"+objfile.path+objfile.file_name+"'/></br>"+ (objfile.caption==null || objfile.caption=="null"?"":"<span>"+objfile.caption+"</span>") ;
		}
		top.tinymce.activeEditor.windowManager.getParams().oninsert(html);
	}
	else if(istiny==2){
		if(arrFiles.length>0){
			var item = $(arrFiles[0]);
			var _parent = item.parent().parent().parent();
			var datafile =  _parent.attr('data-file');
			var objfile = JSON.parse(datafile);
			top.tinymce.activeEditor.windowManager.getParams().setUrl(objfile.path+objfile.file_name);

		}
	}
	else{
		var arrItem = [];
		for (var i = 0; i < arrFiles.length; i++) {
			var item = $(arrFiles[i]);
			var _parent = item.parent().parent().parent();
			var datafile =  _parent.attr('data-file');
			var objfile = JSON.parse(datafile);
			arrItem.push(objfile);
		}
		parent.hungvtApplyCallbackFile(arrItem,istiny);
		if (typeof parent.closeIFrame === "function") {
			parent.closeIFrame();
		}
		else{
			parent.close_window();
		}
	}

}
$(function() {
	MediaManager.init();
});