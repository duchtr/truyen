var OFFLINE = (function(){
	var currentChapter = {};
	var showConfirm = function (){
		$(document).on('click', '.offline_btn', function(event) {
			event.preventDefault();
			var name = $(this).data('name');
			if(name!=undefined){
				$('#confirm_offline').find('span.name').text("Truyện \""+name+"\" ");
			}
			var chapter = $(this).data('chapter');
			if(chapter!=undefined){
				$('#confirm_offline').find('span.name').text("Chương \""+chapter+"\" ");
			}
			var slug = $(this).data('slug');
			var id = $(this).data('id');
			var img = $(this).data('img');
			var chapterid = $(this).data('chapter-id');
			var chapterslug = $(this).data('chapter-slug');
			var chaptername = $(this).data('chapter');
			currentChapter = {
				slug:slug,
				id:id,
				img:img,
				name:name,
				chapterslug:chapterslug,
				chapterid:chapterid,
				chaptername:chaptername,
			};
			$(this).remove();
			$('#confirm_offline').modal('show');
		});
	}
	var download = function (link,showAlert){
		var iframe = document.createElement('iframe');
		iframe.style = 'visibility: hidden;position: absolute;left: 0; top: 0;height:0; width:0;border: none;';
		iframe.onload = function() { if(showAlert){ alert('Đã tải xong!');} iframe.remove(); $('#loader').fadeOut(300); };

		iframe.src = link; 
		document.body.appendChild(iframe); 
	}
	var saveStory = function (){
		var key = 'stories_offline';
		var stories = localStorage.getItem(key);
		var id = currentChapter.id;
		var chapterid = currentChapter.chapterid;
		if(stories==null){
			stories = {};
			
		}
		else{
			stories = JSON.parse(stories);
		}
		if(stories[id]==undefined){
			stories[id] = {};
			stories[id]['story'] = {id:id,img:currentChapter.img,slug:currentChapter.slug,name:currentChapter.name};
			download(currentChapter.slug,false);
		}
		if(stories[id][chapterid] == undefined){
			stories[id][chapterid] = currentChapter;
		}
		localStorage.setItem(key, JSON.stringify(stories));
		$('#loader').fadeIn(300);
		download(currentChapter.chapterslug,true);
		$('#confirm_offline').modal('hide');
		
	}
	var _initOneItem =function(item){
		var item = 
		`<div class="col-lg-4 col-sm-6 col-6 xs_pd_5">
	    	<div class="item_story_high row mar_5 d-flex align-items-center">
	         <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
	            <a href="`+item.slug+`" class="c-img">
	                  <img src="`+item.img+`" class="img-fluid">
	            </a>
	        </div>
	        <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
	            <div class="text">
	               <h3 class="name"><a href="`+item.slug+`">`+item.name+`</a></h3>
	            </div>
	        </div>
	      	</div>
	   </div>`;
	   return item;
	}
	var initViewHome = function(){
		var stories = localStorage.getItem('stories_offline')
		stories = JSON.parse(stories);
		if(stories==null) return;
		var str = `<div class="story_highlight">
				    <div class="container"> <h2 class="title_border">Truyện Đã Lưu Offline</h2>
				    <div class="row xs_mar_5">`;
				    for(var kstory in stories){
				    var story = stories[kstory];
				    for(var kchapter in story){
				    	if(kchapter=='story') continue;
				        var chapter = story[kchapter];
				        str += _initOneItem(chapter);
				        break;
				    }
				}
		str +=		    `</div>
				    </div>
				</div>`;
		$('#stories_offline').html(str);
	}

	var _initViewPro = function (){
		if($('.content_pro').length==0) return;
		var stories = localStorage.getItem('stories_offline')
		stories = JSON.parse(stories);
		if(stories==null) return;
		var firstBtn = $('.item_chapter .offline_btn').first();
		if(firstBtn.length>0){
			var id = firstBtn.data('id');
			var story = stories[id];
			if(story !=undefined){
				for(var kchapter in story){
			        var chapter = story[kchapter];
			        $('.item_chapter .offline_btn[data-chapter-id='+chapter['chapterid']+']').remove();
			    }
			}
		}

	}
	var initViewPro = function (){
		if (/offline=true/.test(location.search)) {
			var stories = localStorage.getItem('stories_offline')
			stories = JSON.parse(stories);
			if(stories==null) return;
			for(var kstory in stories){
			    var story = stories[kstory];
			    if(story['story'].slug != window.location.href) continue;
			    var str = '<div class="row">';
			    for(var kchapter in story){
			    	if(kchapter == 'story') continue;
			    	str +=`<div class="col-lg-6 col-12"><div class="item_chapter">
			            <h2 class="name_chapter item_chapter_detail">
			            <a href="`+story[kchapter].chapterslug+`">`+story[kchapter].chaptername+`</a></h2>
			        </div></div>`;
			    }
			    str +='</div>';
			    $('.chapter_ajax').html(str);
			}
			$('.story_highlight').remove();
		}
		else{
			var count = 0;
			var interval = setInterval(function(){
				if($('.item_chapter').length>0){
					clearInterval(interval);
					_initViewPro();
				}
				count++;
				if(count>10){
					clearInterval(interval);
				}
			}, 400);
		}
		
	}
	function checkOnlineWebsite(){
		window.addEventListener("load", () => {
		  function handleNetworkChange(event) {
		    if (navigator.onLine) {
		      document.body.classList.remove("offline");
		    } else {
		      document.body.classList.add("offline");
		    }
		  }
		  window.addEventListener("online", handleNetworkChange);
		  window.addEventListener("offline", handleNetworkChange);
		});
	}
	checkOnlineWebsite();
	showConfirm();
	initViewHome();
	initViewPro();
	return {
		saveStory:function(){
			saveStory();
		}
	};
})();