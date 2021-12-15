<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/updateCore">Update Core</a></li>
    </ul>
</div>

<div id="cph_Main_ContentPane " class="update_core">
  <div class="row">
  <div class="col-xs-12">
      <table>
          <tr>
              <td>Môi trường phát triển</td>
              <td><span><?php echo ENVIRONMENT ?></span></td>
          </tr>
          <tr>
              <td>Base Path</td>
              <td><span><?php echo BASEPATH ?></span></td>
          </tr>
          <tr>
              <td>PHP Version</td>
              <td><span><?php echo phpversion() ?></span></td>
          </tr>
          <tr>
              <td>Base Url</td>
              <td><span><?php echo base_url() ?></span></td>
          </tr>
          <tr>
              <td>Phiên bản code</td>
              <td><span><?php echo CMS_VERSION ?></span></td>
          </tr>
          <tr>
              <td>Phiên bản mới nhất</td>
              <td><span><?php echo $versions['version'] ?></span></td>
          </tr>
          <tr>
                <td colspan="2" class="text-center">
                    <?php if(CMS_VERSION < (int)$versions['version']): ?>
                    <p style="color:red">Bạn nên cập nhật CMS phiên bản mới nhất!</p>
                    <form action="" method="post">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <input placeholder="Tài khoản" type="text" name="username" >
                        <input placeholder="Mật khẩu" type="password" name="password" >
                        <br>
                        <button type="submit" class="togglePopup">Update</button>
                    </form>
                <?php endif; ?>
                </td>
          </tr>
      </table>
  </div>

  </div>
</div>
</div>
<style type="text/css">
    .update_core table{
     width: 100%;
    }
    .update_core table,
    .update_core tr,
    .update_core td{

    border-collapse: collapse;
    border: 1px solid #ccc;
    padding: 5px;
    }
</style>


