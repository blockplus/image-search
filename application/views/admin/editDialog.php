<div class="container" role="main" style="margin-top: 50px;">
  <div class ="starter-template">
    
    <div class="container" style="text-align: left;">
      <div class="panel panel-default">
        <div class="panel-heading">Edit Item</div>
        <div class="panel-body">
           <form method="post" action="<?php echo site_url('admin/edit_item');?>" role="form">

                    <?php if(!empty(@$notif)){ ?>
                    <div id="resultalert" class="alert alert-<?php echo @$notif['type'];?>">
                        <p><?php echo @$notif['message'];?></p>
                        <span></span>
                    </div>
                    <?php } ?>

                    <div class="col-md-12" style="text-align: center;">
                      <img src="<?php echo site_url(BANK_PATH.$item->{'tb_image'});?>" class="img-rounded" style="max-height: 320px; max-width: 320px;" alt="Bank image"/>
                    </div>

              <div class="form-group">
                <label for="title label-default" style="text-align: left;">Title:</label>
                <input type="text" class="form-control" name='title' value="<?php echo $item->{'tb_title'};?>" required/>
              </div>

              <div class="form-group">
                <label for="description label-default" style="text-align: left;">Description:</label>
                <textarea class="form-control" name="description" rows='5' required><?php echo $item->{'tb_desc'};?></textarea>
              </div>

              <div class="form-group">
                <label for="excel_path label-default" style="text-align: left;">Link:</label>
                <input type="text" class="form-control" name="link" value="<?php echo $item->{'tb_url'};?>" required />
              </div>

              <div class="col-md-offset-3 col-md-6" style="text-align: center;">
                  <input type="submit" class="btn btn-default" name="save" value=" &nbsp Save &nbsp">
              </div>

              <input type="hidden" name="id" value="<?php echo $item->{'tb_id'}?>">

            </form>
        </div>
      </div>
    </div>
  </div>
</div>