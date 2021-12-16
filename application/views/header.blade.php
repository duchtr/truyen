<header>
    {@$pp = $this->CI->uri->segment(1,0);@}
    
    <div class="top_header md_hidden">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2">
                   
                  </div>
                  <div class="col-lg-10">
                    <marquee  onmouseover="this.stop();" onmouseout="this.start();">
                        <!--DBS-loop.say_high.1|where:act = 1|order:|limit:-->
                        <span class="say_high"><a>Chào mừng đến với web đọc truyện</a></span>
                        <!--DBE-loop.say_high.1-->
                    </marquee>
                </div>
                
            </div>
        </div>
    </div>
    <div class="mid_header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 col-sm-5 col-md-5 col-lg-3 mb-lg-0">
                    <a class="logo" href="">
                        <img src="https://www.pngmart.com/files/21/Open-Book-PNG-Pic.png" alt = "logo">
                    </a>
                </div>
                <div class="col-6 col-sm-7 col-md-7 col-lg-2 px-lg-0 d-lg-block d-flex align-items-center position-static justify-content-between seach_qaa">
                     <form action="tim-kiem" method="get" class="align-items-center justify-content-between frm-search">
                        <input type="text" name="q" class="" placeholder="Tìm kiếm tên truyện..." value="@if(isset($keyword)){{$keyword}}@endif">
                        <button class="d-block">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </form>
                    <span class="show-search d-lg-none d-block  btm_search_qa">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                    <button class="btn-menu ml-3" type="button"><i class="fa fa-bars" ></i></button> 
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-7">
                    <nav class="main-nav d-flex justify-content-end">
                        <!--DBS-menu.1|where:act = 1,group_id = 1,ord asc-->
                        <!--DBE-menu.1-->
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>