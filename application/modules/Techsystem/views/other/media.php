

<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">

    <ul>

        <li class="home"><a href="<?php echo base_url(''); ?>Admin"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>

        <li class="SecondLast"><a href="Techsystem/viewSitemap"><?php echo alang("MEDIA_MANAGER") ?></a></li>

    </ul>

</div>

<div id="cph_Main_ContentPane">

   <div class="widget row margin0">

      <div class="widget-title aclr">

       

         <h4>

            <i class="icon-qrcode"></i>&nbsp; <?php echo alang("MEDIA_MANAGER") ?>

         </h4>

         <div id="hiddenToolBarScroll" class="scrollBox" style="display:none">

            <h4>

               <i class="icon-qrcode"></i>&nbsp;<?php echo alang("MEDIA_MANAGER") ?>

            </h4>



         </div>

      </div>

      <script type="text/javascript">

      function toggleFullScreen() {

        var elem = document.getElementById("full");

      if (!document.fullscreenElement &&    

          !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  

        if (document.documentElement.requestFullscreen) {

          elem.requestFullscreen();

        } else if (document.documentElement.msRequestFullscreen) {

          elem.msRequestFullscreen();

        } else if (document.documentElement.mozRequestFullScreen) {

          elem.mozRequestFullScreen();

        } else if (document.documentElement.webkitRequestFullscreen) {

          elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);

        }

      } else {

        if (document.exitFullscreen) {

          document.exitFullscreen();

        } else if (document.msExitFullscreen) {

          document.msExitFullscreen();

        } else if (document.mozCancelFullScreen) {

          document.mozCancelFullScreen();

        } else if (document.webkitExitFullscreen) {

          document.webkitExitFullscreen();

        }

      }

    }

      </script>

      <style>

      .fullcren{

        position: absolute;

        right: 0px;

        font-size: 30px;

        right: 20px;

      }

      </style>

      <div class="widget-body">

      <i alt="Full màn hình" onclick="toggleFullScreen();" class="fullscreen icon-fullscreen fullcren"></i>

        <iframe id="full" style="width:92vw;min-height:100vh;border: none;" src="<?php echo base_url() ?>Techsystem/Media/media"></iframe>

        

     </div>







    





























      </div>

   </div>

</div>





