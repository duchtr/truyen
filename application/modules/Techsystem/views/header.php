<div class="PageHeader row margin0">
   
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