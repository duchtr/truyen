<div class="PageHeader row margin0">
         <div class="LogoHeader col-md-10 col-xs-12 padding0">
            <div class="logoimage hidden-sm hidden-xs">
            <?php
            $logoImage = "https://tech5s.com.vn/theme/frontend/images/logo3.png";
             $logo = ' <img style="max-height: 55px; max-width: 200px;background: #ececec;" border="0" class="imglogo img-responsive" src="'.$logoImage.'" alt="logo" title="logo">';
             $resultHook = $this->hooks->call_hook(['tech5s_admin_logo_dashboard',"logo"=>$logo]);
             if(!is_bool($resultHook)){
                  extract($resultHook);
              }
            ?>
                <a class="SiteName" href="<?php echo base_url().'Techsystem' ?>">
               <?php echo $logo ?>
               </a>
            </div>
            <div class="linkroot">
               <a class="SiteName" href="<?php echo base_url() ?>" target="_blank">
               <?php echo base_url() ?>
               </a>
            </div>
            <div class="menutop">
               <ul class="aclr">
                  <li class="fl"><i class="icon-trash"></i><a onclick="clearCache();return false;" href="Techsystem/deleteCache"><?php echo lang("ADMIN_CLEAR_CACHE") ?></a></li>
                  <li class="fl"><i class="icon-cogs"></i><a  href="Techsystem/historyAccess"><?php echo lang("ADMIN_VISIT_HISTORIES") ?></a></li>
                  <li class="fl"><i class="icon-cogs"></i><a  href="Techsystem/editRobot">Robot</a></li>
                  <li class="fl"><i class="icon-list-alt"></i><a href="Techsystem/viewSitemap">Sitemap</a></li>
                  <li class="fl"><i class="icon-cogs"></i><a  href="Techsystem/editHtaccess">Htaccess</a></li>
                  <?php $resultHook = $this->hooks->call_hook(['tech5s_admin_quick_menu']); ?>
               </ul>
               <script type="text/javascript">
               function clearCache(){
                  $.ajax({
                     url: 'Techsystem/deleteCache',
                     type: 'POST',
                     data: {param1: 'value1'},
                  })
                  .done(function() {
                     window.location.reload();
                  });
                  
               }
               </script>
            </div>
         </div>
         <div class="SystemMenu col-md-2 col-xs-12">
            <div style="display: block;">
               <ul class="sysMenu">
                  <li class="last">
                     <div class="btn-group">
                        <a href="" class="btn account-info btn-info">
                        <i class="icon-user"></i>
                        <?php echo getAdminUser()['user']['username']; ?>
                        </a><a href="" data-toggle="dropdown" class="btn btn-info dropdown-toggle dropdown-toggle-acount"><span class="icon-caret-down"></span></a>
                        <ul class="dropdown-menu custome">
                           <li><a href="" onclick="changePass();return false;"><i class="icon-key"></i><?php echo lang("ADMIN_CHANGE_PASS") ?></a>
                           </li>
                           <li>
                              <a id="siteUser_Lbtn_Logout" class="NormalGray" href="Techsystem/logout"><i class="icon-signout"></i> <?php echo lang("ADMIN_LOGOUT") ?></a>
                           </li>
                        </ul>
                        <script type="text/javascript">
                         $(document).ready(function() {
                              $('#modal-login .close').click(function(event) {
                                 $('#modal-login').hide(500);
                              });
                           });
                        function changePass(){
                              $('#modal-login').show(500);
                           }
                        </script>
                     </div>
                  </li>
               </ul>
               <div style="clear: both"></div>
            </div>
         </div>
      </div>