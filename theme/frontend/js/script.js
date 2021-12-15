

var win = $(window);
var html = $('html');
var body = $('body');
var m_nav = $('.main-nav');
var m_nav_btn = $('.btn-menu');
var nav = m_nav.children('ul');
var mMenu = function(){

    m_nav_btn.click(function(e){
        if(nav.is(":hidden")){
            $(this).addClass('active');
            nav.slideDown(200);
        }
        else {
            nav.slideUp(200);
            $(this).removeClass('active');
        }
        e.stopPropagation();
    });
    m_nav.find("ul li").each(function() {
        if($(this).find("ul>li").length > 0){
            $(this).append('<i></i>');
        }
    });
    if(win.width() < 991)
    {
        m_nav.find("li i").click(function(){
            var ul = $(this).parent().children('ul');
            if(ul.is(':hidden'))
            {
                ul.slideToggle(200);
                $(this).toggleClass('rotateZ');
            }
            else
            {
                ul.slideUp(300);
                $(this).removeClass('rotateZ');
            }
        });
    }

}
var _initWinClick = function()
{
    win.click(function(e) {
        if(win.width() < 991 && nav.has(e.target).length == 0 && !nav.is(e.target))
        {
            nav.slideUp(250);
            nav.find('ul').slideUp(250);
            nav.find('i').removeClass('rotateZ');
        }
    });
}
var GUI ={};
GUI._initFixTop = function()
{
 var menu = $('.mid_header');
 var _posHeight = $('header').height();
 var logo = $('header .logo');
 win.scroll(function(event) {
    if(win.scrollTop() > _posHeight)
    {
        menu.addClass('fix-top');
        logo.addClass('fix-logo');
    }
    else
    {
        menu.removeClass('fix-top');
        logo.removeClass('fix-logo');
    } 
});

}
GUI._scrool_navs_bar = function(){
    win.scroll(function(){
        if($('.fixed_top_button').length>0){
            if (win.scrollTop()>$('.fixed_top_button').offset().top+150&&win.scrollTop()<$('.comment_fb').offset().top) {
                $('.cover_fixed').addClass('fixed');
            }else{
                $('.cover_fixed').removeClass('fixed');
            }
            
        }
    });
}
// GUI._initMusic = function()
// {
//     $('#music2').click(function(){
//         $('#player').trigger('pause');
//         $('#music').css("display","block");
//         $(this).css("display","none");
//     });
//     $('#music').click(function(){
//         $('#player').trigger('play');
//         $('#music2').css("display","block");
//         $(this).css("display","none");
//     });
// }
GUI._op_menu_chapter = function(){
    $('.op_menu').click(function(event) {
        event.stopPropagation();
        $('.chapter_pro').stop().toggleClass('active');
    });
    $(win).click(function(event){
        $('.chapter_pro').removeClass('active');
    });
}
GUI._chapter = function(){
    if ($('.chapter_ajax').length>0) {
        var id =$('.title_pro_detail').attr('data-id');
        $(document).ajaxStart(function() {
          $('.bgloading').fadeIn(500);
      });
        event.preventDefault();
        $(document).ajaxComplete(function(event, xhr, settings) {
          $('.bgloading').delay(100).fadeOut(500); 
      });
        $.ajax({
            url: 'chapter',
            type: 'GET',
            data: {id: id},
        })
        .done(function(data) {
            $('.chapter_ajax').html(data);
        })
        
        
    }
    $(document).on('click','.row_xxx a',function(event){
        event.preventDefault();
        $.ajax({
          url: $(this).attr('href'),
          type: 'GET',
          dataType: 'html',
      })
        .done(function(html) {

          $('.chapter_ajax').html(html);
          var xxx = $('.content_pro').offset().top;
          $('html,body').animate({
            scrollTop: xxx
        }, 700);
      })
    });  
}
GUI._scroll_comment = function(){
    $('span.comment').click(function(){
        var h_comment = $('.comment_fb').offset().top;
        $('html,body').animate({
            scrollTop: h_comment
        }, 300);
    });
}
GUI._scroll_top = function(){
    $('.up').click(function(event) {
        var h_title = $('.title_cover').offset().top;
        $('html,body').animate({
            scrollTop: h_title
        }, 500);
    });
}
GUI._story_update = function(){
    if ($('.story_update')) {
        $.ajax({
            url: 'truyen-moi',
            type: 'GET',
            dataType: 'HTML',
        })
        .done(function(data) {
            $('.story_update').html(data);
        })
        
        
    }
}
var uiRate = function(){
    var starw = 0;
    $('body').on('mouseenter', '.star-rating.on .star-base', function(e) {
        starw = $(this).children('.star-rate').width();
    });
    $('body').on('mousemove', '.star-rating.on .star-base', function(e) {
        $(this).children('.star-rate').width(e.pageX - $(this).offset().left);
    });
    $('body').on('mouseleave', '.star-rating.on .star-base', function(e) {
        $(this).children('.star-rate').width(starw);
    });
}

uiRate();
GUI._hover_pro = function(){
    // $('.item_cover_pro .c-img').stop().hover(function(event){
    //     event.preventDefault();
    //     $(this).mousemove(function(event) {
    //         event.preventDefault();
    //         if (event.pageX>765) {
    //             $(this).closest('.item_cover_pro').children('.item_pro_hover').css({
    //                 top: event.pageY,
    //                 left:'calc('+event.pageX+'px - 570px )',
    //                 display:'block',
    //                 opacity:'1',
    //             });

    //         }else{
    //             $(this).closest('.item_cover_pro').children('.item_pro_hover').css({
    //                 top: event.pageY,
    //                 left:event.pageX,
    //                 display:'block',
    //                 opacity:'1',
    //             });
    //         }
    //     });

    // });
    // win.hover(function(event){
    //     if ($('.hover_right').length>0) {
    //         $('.item_pro_hover').css({
    //             top: event.pageY,
    //             left:event.pageX,
    //             display:'none',
    //             position:'absolute'
    //         });
    //     }else{
    //         $('.item_pro_hover').css({
    //             top: event.pageY,
    //             left:event.pageX,
    //             display:'none',
    //             position:'absolute'
    //         });
    //     }
    // });
    // $('.item_cover_pro .c-img').stop().hover(function(event) {
    //     console.log(event);
    //     $(this).parents('.item_cover_pro').find('.item_pro_hover').css({
    //         top: event.screenY,
    //         left:event.screenX,
    //         display:'block',
    //         opacity:'1',
    //     });
    // }, function() {
    //     /* Stuff to do when the mouse leaves the element */
    // });
    // var globalSkipCounter = 0;
    // var globalSkipRate = 5;
    // $('.item_cover_pro .c-img').mousemove(function(event) {
    //     event.preventDefault();
    //     event.stopPropagation();
    //      if(globalSkipCounter >= globalSkipRate){
    //          var mouseX = event.pageX;
    //          var mouseY = event.pageY;
    //          $('.item_pro_hover').first().css({
    //         top: event.pageY,
    //         left:event.pageX,
    //         display:'block',
    //         opacity:'1',
    //     });
    //          globalSkipCounter = 0;
    //        }
    //        else{
    //          globalSkipCounter+=1;
    //        }

    // });
}
GUI._initToogleSearch = function()
{
    var btnSearch = $('.show-search');
    var formSearch = $('.frm-search');
    btnSearch.click(function(event) {
        console.log('x');
        formSearch.slideToggle(300);
    });
}

GUI._zoom_text = function(){
    $(document).on("click",".zoom_text",function() {
        event.preventDefault();
        var data_zoom = $(this).attr('data-zoom');
        var font = $('.content_story.s-content').attr('data-font');
        if (data_zoom=="+") {
            if(font < 30)
            {
                font++;
                $('.content_story.s-content').css({
                    'font-size': font+'px'
                });
                $('.content_story.s-content').attr('data-font',font);
                localStorage.setItem('fontsize',font);
            }
            else
            {
                $('.content_story.s-content').css({
                    'font-size': font
                });
                $('.content_story.s-content').attr('data-font',30);
                localStorage.setItem('fontsize',font);
            }
        }else {
            if(font > 14)
            {
                font--;
                $('.content_story.s-content').css({
                    'font-size': font+'px'
                });
                $('.content_story.s-content').attr('data-font',font);
                localStorage.setItem('fontsize',font);
            }
            else
            {
                $('.content_story.s-content').css({
                    'font-size': font
                });
                $('.content_story.s-content').attr('data-font',14);
                localStorage.setItem('fontsize',font);
            }
        }
    })
}
$(function(){
    mMenu();
    GUI._scrool_navs_bar();
    GUI._op_menu_chapter();
    GUI._chapter();
    GUI._scroll_comment();
    GUI._scroll_top();
    GUI._story_update();
    GUI._hover_pro();
    _initWinClick();
    GUI._zoom_text();
    // GUI._initMusic();
    GUI._initFixTop();
    GUI._initToogleSearch();
})
$( document ).ready(function() {
    $('.loading').addClass('dp_none');
    if (localStorage.getItem("fontsize") != null && typeof(Storage) !== "undefined") {
        var fontIndex = localStorage.getItem("fontsize");
        $('.content_story.s-content').css('font-size', fontIndex + 'px');
        $('.content_story.s-content').attr('data-font', fontIndex);
    }
});