<div class="container" role="main" style="margin-top: 50px;">
  <div class ="starter-template">
    <loading></loading>
    <?php echo isset($error) ? $error : '';?> 

      <div class="panel panel-primary">
        <div class="panel-heading">Please choose image to upload</div>
        <div class="panel-body">
          <?php echo form_open_multipart('');?>
          <!-- <form method="post" action="" role="form"> -->
            <div class="row">
              <div class="col-md-10">
                  <input type="file" name='userfile' required style="width: 100%;" />
              </div>
              <div class="col-md-2" style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Upload</button>
              </div>
            </div>
            <input type="hidden" name="type" value="file" />
          </form>
        
          <div class="panel-body-sep"></div>
          <form method="post" action="" role="form">

            <div class="row">
              <div class="col-md-10" style="text-align: left;">
                <div class="input-group">
                  <span class="input-group-addon">Link</span>
                  <input type="text" class="form-control" name='file' placeholder="image url for advertisement" required>
                </div>
              </div>
              <div class="col-md-2" style="text-align: right;">
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add</button>
              </div>
            </div>
            <input type="hidden" name="type" value="link" />
          </form>
        </div>
      </div>

      <div>
      <?php foreach ($items as $item) { ?>
        <form method="post" action="" role="form">
          <div class="row" style="align-items: center; border: solid 1px #eee; padding: 5px;">
              <div class="col-md-3" style="text-align: center;">
                <img src="<?php echo site_url(ADVERTISE_THUMB_PATH.$item['imagename']);?>" class="img-rounded" style="max-height: 160px; max-width: 160px;" alt="<?php echo $item['info'];?>"/>
              </div>
              <div class="col-md-8" style="text-align: left;">
                <div style="width: 100%; word-break: break-all;">
                  <a href="<?php echo site_url(ADVERTISE_PATH.$item['imagename']);?>" class="text-primary" style="font-size: medium;"  target='_blank'>View original image</a>
                  &nbsp;&nbsp;&nbsp;<?php echo $item['info'];?>
                </div>
              </div>
              <div class="col-md-1" style="text-align: right; padding: 5px;">
                  <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 5px;">Delete</button>
              </div>
          </div>
          <input type="hidden" name="id" value="<?php echo $item['id'];?>"/>
          <input type="hidden" name="type" value="delete" />
        </form>
      <?php }?>
      </div>

  </div>
</div>