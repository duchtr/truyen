jQuery(function($) {
    $(document).on('click', '.mdi-img[rel="mdi"]', function(event) {
        event.preventDefault();
        var checkbox = $(this).parents('.media-item').children('.mdi-check').find('input[type="checkbox"]');
        checkbox.prop("checked", !checkbox.prop("checked")).trigger('change');

    });
    $(document).on('change', '.media-item .mdi-check input[type="checkbox"]', function(event) {
        event.preventDefault();
        var a = $(this).parents('.media-item').find('.mdi-img[rel="mdi"]');
        if(a.length==0) return;
        if (!a.hasClass('fileSelected')) {
            a.addClass('fileSelected');
        } else {
            a.removeClass('fileSelected');
        }
        var inputs = $('input.selectfile:checked');
        var objs = [];
        for (var i = 0; i < inputs.length; i++) {
            var item = $(inputs[i]);
            objs.push(item.val());
        }
        $('input[name=listselected]').val(JSON.stringify(objs));
    });
    $("a.preview").fancybox({
        speedIn: 600,
        speedOut: 200,
        overlayShow: false,
        autoScale: true
    });
    $(".media-item").on('click', '.selectfile', function(event) {
        var inputs = $('input.selectfile:checked');
        var objs = [];
        for (var i = 0; i < inputs.length; i++) {
            var item = $(inputs[i]);
            objs.push(item.val());
        }
        $('input[name=listselected]').val(JSON.stringify(objs));
    });
    $(".media-item").on('click', '.mdi-btn .name-edit', function(event) {
        event.preventDefault();
        var item = $(this).parents('.media-item');
        var title = item.find('.mdi-title');
        var id = $(this).attr("dt-id");
        title.append('<input type="text" dt-id="' + id + '" class="edit-ct" rows="3" value="' + title.text() + '" onfocus="this.select()">');
        title.children('.edit-ct').focus();
    });
    $('.media-item').on('blur', '.edit-ct', function() {
        if ($(this).val() != '' && $(this).val().trim() != '') {
            $(this).parent('.mdi-title').text($(this).val());
        }
        $(this).remove();
    });
    $('.media-item').on('keyup', '.edit-ct', function(e) {
        if (e.keyCode == 13) {
            if ($(this).val().trim() == "") return;
            $.ajax({
                    url: globalBaseUrl + '/Media/rename',
                    type: 'POST',
                    data: {
                        id: $(this).attr('dt-id'),
                        newname: $(this).val()
                    },
                })
                .done(function(data) {
                    try {
                        var json = JSON.parse(data);
                        if (json.code == SUCCESS) {}
                    } catch (e) {}
                });
            $(this).remove();
        }

    });
    $(document).bind("contextmenu", function(event) {
        event.preventDefault();
        var ele = document.elementFromPoint(event.clientX, event.clientY);
        ele = $(ele).closest('.fileitem');
        var datafile = $(ele).attr('data-file');
        if (datafile == undefined) return;
        try {
            var objfile = JSON.parse(datafile);
            if (!objfile.hasOwnProperty('extra')) {
                return;
            }
            var extra = JSON.parse(objfile.extra);
            globalObjectFile = objfile;
            if (!objfile.hasOwnProperty('is_file') || objfile.is_file == 0) {
                return;
            }
            var str = "";
            if (extimgs.indexOf(extra.extension) != -1) {
                /*str+="<li data-action='advance'><i class='fa fa-file-archive-o'></i>Metadata ảnh</li>";  */
                /*str+="<li data-action='editimage'><i class='fa fa-edit'></i>Chỉnh sửa ảnh</li>";*/
            }
            str += "<li data-action='showpath'><i class='fa fa-link'></i>Hiện đường dẫn file</li>";
            if(!is_trash_view){
                str += "<li data-action='duplicate'><i class='fa fa-paperclip'></i>Nhân đôi file</li>";
            }
            
            /*str+="<li data-action='copy'><i class='fa fa-copy'></i>Copy</li>";
            str+="<li data-action='cut'><i class='fa fa-cut'></i>Cut</li>";*/
            str += "<li style='text-align:center;background:#ddd'><p style='text-align:center;display:inline'>THÔNG TIN FILE</p></li>"
            str += "<li><i class='fa fa-tag'></i>" + extra.extension + "</li>";
            if (extra.hasOwnProperty('width') && extra.hasOwnProperty('height')) {
                str += "<li><i class='fa fa-desktop'></i>" + extra.width + 'x' + extra.height + "</li>";
            }
            str += "<li><i class='fa fa-fire'></i>" + extra.size + "</li>";
            var t = new Date(1970, 0, 1);
            t.setSeconds(extra.date);
            str += "<li><i class='fa fa-calendar'></i>" + (t.getDate() < 10 ? "0" + t.getDate() : t.getDate()) + "/" + (t.getMonth() < 9 ? "0" + (t.getMonth() + 1) : (t.getMonth() + 1)) + "/" + t.getFullYear() + " " + (t.getHours() < 10 ? "0" + t.getHours() : t.getHours()) + ":" + (t.getMinutes() < 10 ? "0" + t.getMinutes() : t.getMinutes()) + ":" + (t.getSeconds() < 10 ? "0" + t.getSeconds() : t.getSeconds()) + "</li>";

            $('ul.custom-menu').html(str);
        } catch (ex) {}

        var checkShow = event.pageX+200 > $(window).width();
        if(checkShow){
            var right = $(window).width()- event.pageX;
            $(".custom-menu").show()
            .css({
                top: event.pageY + "px",
                right: right + "px",
                left:'auto'
            });
        }
        else{
            $(".custom-menu").show()
            .css({
                top: event.pageY + "px",
                left: event.pageX + "px",
                right:'auto'
            });
        }
        
    });
    $('.custom-menu').on('click', '>li', function(event) {
        $(this).parent().hide();
        var extra = JSON.parse(globalObjectFile.extra);
        var action = $(this).attr('data-action');
        switch (action) {
            case 'editimage':
                $('#aviary-image').attr('src', _baseurl + extra.path);
                launchEditor('aviary-image', $('#aviary-image').attr('src'));
                break;
            case 'showpath':
                bootbox.dialog({
                    title: "Đường dẫn tệp tin",
                    message: "<input style='width:100%' value='" + globalObjectFile.path + globalObjectFile.file_name + "'/>"
                });
                break;
            case 'duplicate':
                duplicateFile(globalObjectFile.id);
                break;
            case 'copy':
                MediaManager.getListFolder();
                break;
            case 'cut':
                MediaManager.getListFolderMove();
                break;
            case 'advance':
                MediaManager.getMetadataFile(globalObjectFile.id);
                break;
        }
    });

    function duplicateFile(id) {
        $.ajax({
                url: globalBaseUrl + "/Media/duplicateFile",
                type: 'POST',
                data: {
                    id: id
                },
            })
            .done(function(e) {
                try {
                    var json = $.parseJSON(e);
                    if (json.code == SUCCESS) {
                        MediaManager.getLastedFileCreated(json.message);
                    }
                } catch (ex) {
                    bootbox.alert('Xảy ra lỗi trong quá trình thực hiện!');
                }

            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
                console.log("complete");
            });
    }
    $(document).click(function(event) {
        if (!$(event.target).closest('.custom-menu').length) {
            if ($('.custom-menu').is(":visible")) {
                $('.custom-menu').hide()
            }
        }
    })
    var $grid = $('.media-content .row').isotope({
        itemSelector: '.media-it',
        layoutMode: 'fitRows',
        getSortData: {
            titleAsc: '.mdi-title',
            titleDesc: '.mdi-title',
            sizeAsc: '.mdi-size',
            sizeDesc: '.mdi-size',
            dateAsc: function(e) {
                date = 0;
                if ($(e).find('.mdi-date').length > 0) {
                    date = Date.parse($(e).find('.mdi-date').text())
                }
                return date;
            },
            dateDesc: function(e) {
                date = 0;
                if ($(e).find('.mdi-date').length > 0) {
                    date = Date.parse($(e).find('.mdi-date').text())
                }
                return date;
            }
        },
        sortAscending: {
            titleAsc: true,
            titleDesc: false,
            sizeAsc: true,
            sizeDesc: false,
            dateAsc: true,
            dateDesc: false,
        },
    });

    var qsRegex;
    var $input = $('.media-bar-s > input').keyup(debounce(function(e) {
        
    }, 100));

    function debounce(fn, threshold) {
        var timeout;
        return function debounced() {
            if (timeout) {
                clearTimeout(timeout);
            }

            function delayed() {
                fn();
                timeout = null;
            }
            timeout = setTimeout(delayed, threshold || 100);
        }
    }
    var filterFns = '';
    $('.menu-filter').on('click', 'button', function() {
        var filterValue = $(this).attr('data-filter');
        filterValue = filterFns[filterValue] || filterValue;
        $grid.isotope({
            filter: filterValue,
        });
        $(this).parents('.menu-filter').find('button').removeClass('active');
        $(this).addClass('active');
    });
    $('.media-sort li').on('click', 'a', function(event) {
        event.preventDefault();
        var sortValue = $(this).attr('data-sort');
        $grid.isotope({
            sortBy: sortValue,
        });
        $(this).parents('.media-sort').find('li>a').removeClass('active');
        $(this).addClass('active');
    });
    $('.media-bar-b').on('click', '#refresh', function(event) {
        event.preventDefault();
        $grid.isotope({
            filter: '*',
            sortBy: 'original-order',
        });
        $('.menu-filter button').removeClass('active');
        $('.media-sort li>a').removeClass('active');
    })


    $(".list-notify").mCustomScrollbar({
        theme: "dark-3"
    });
    $('#view').click(function() {
        if ($(this).attr('data-view') == "list") {
            $('.media-item').parent().addClass('media-item-l');
            $(this).attr('data-view', "box");
            $grid.isotope();
        } else {
            $('.media-item').parent().removeClass('media-item-l');
            $(this).attr('data-view', "list");
            $grid.isotope();
        }

    });

    function addNotifyUpload(file) {

    }
    ENLESS_PAGE.init();
    $("img.lazy").lazyload({
        event: "sporty"
    });

});
var ENLESS_PAGE = {};
ENLESS_PAGE.loading = false;
ENLESS_PAGE.track_page = 0;
ENLESS_PAGE.finish = false;
ENLESS_PAGE.loadpage = function(index) {
    if (is_trash_view ==0 && ENLESS_PAGE.loading == false && !ENLESS_PAGE.finish) {
        ENLESS_PAGE.loading = true; //set loading flag on
        $('.loading-info').show(); //show loading animation 
        var baseurl = window.location.origin + window.location.pathname + "/";
        $.get(baseurl + index * MEDIA_PER_PAGE + location.search, function(data) {
            ENLESS_PAGE.loading = false;
            if (data.trim().length == 0) {
                ENLESS_PAGE.finish = true;
                $('.loading-info').html("Đã tải hết!");
                return;
            }
            $('.loading-info').hide(); //hide loading animation once data is received
            $(".media-content>.row").append(data).isotope('reloadItems').isotope();

        });
    }

}
ENLESS_PAGE.init = function() {
    $(window).scroll(function() {
        if (!ENLESS_PAGE.loading && $(window).scrollTop() + $(window).height() >= $(document).height() - 20) {
            ENLESS_PAGE.track_page++;
            ENLESS_PAGE.loadpage(ENLESS_PAGE.track_page);
        }
    });
}

function launchEditor(id, src, imageEditName) {
    featherEditor.launch({
        image: id,
        url: src
    });
    return false;
}
const SEARCH_MEDIA ={};
SEARCH_MEDIA.insertParam = function (key, value)
{
    key = encodeURIComponent(key); value = encodeURIComponent(value);

    var kvp = document.location.search.substr(1).split('&');

    var i=kvp.length; var x; while(i--) 
    {
        x = kvp[i].split('=');

        if (x[0]==key)
        {
            x[1] = value;
            kvp[i] = x.join('=');
            break;
        }
    }

    if(i<0) {kvp[kvp.length] = [key,value].join('=');}
    return kvp.join('&'); 
}
SEARCH_MEDIA.initSearch = function(){
    $(".media-bar-s button").click(function(event) {
        event.preventDefault();
        var qsRegex = $('.media-bar-s > input').val();
        window.location.search = SEARCH_MEDIA.insertParam("keyword",qsRegex);
    });
    $('.media-bar-s > input').keyup(function(e) {
        if (e.keyCode === 13) {
            window.location.search = SEARCH_MEDIA.insertParam("keyword",$(this).val());
        }
    });
}
$(function() {
    SEARCH_MEDIA.initSearch();
});