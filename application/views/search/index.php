<div class="header">
      <div class="col-sm-3 logo">
        <img src="<?php echo site_url('assets/images/logo.png');?>" style="height: 100%;">
        <span class="title-font-small">motuin</span> 
      </div>
      <div class="col-sm-6 searchbox">
        <?php echo form_open_multipart('', 'id="fileForm"');?>
          <label class="btn btn-primary" style="float: left;">
              Upload <input type="file" style="display: none !important;" name='userfile' id='userfile' required>
          </label> 

          <div class="input-group stylish-input-group">

             <input type="text" class="form-control" name="search_url" id="search_url" placeholder="Upload or enter Image URL" >
              <span class="input-group-addon">
                  <button id='search_submit'>
                      <span class="glyphicon glyphicon-search"></span>
                  </button>  
              </span>
          </div>
        <?php echo "</form>"; ?>
      </div>
</div>

<div class="container content" role="main">
  <div class ="starter-template col-md-8">
    <?php if (count($origin) > 0) { ?>
      <div class="row" style="align-items: center; padding: 5px; margin-bottom: 20px;">
        <div class="col-md-3" style="text-align: center;">
          <img src="<?php echo site_url(SEARCH_THUMB_PATH.$origin['image']);?>" class="img-rounded" style="max-height: 160px; max-width: 100%;" alt="Original image"/>
        </div>
        <div class="col-md-8" style="text-align: left;">
          <div style="width: 100%; word-break: break-all;margin: 5px;"><span class="text-default" style="font-size: small;">File name: <?php echo $origin['filename'];?></span></div>

          <div style="width: 100%; word-break: break-all;margin: 5px;"><span class="text-info" style="font-size: small;"> Match count: <?php echo $match_count;?></span></div>
        </div>
      </div>
    <?php } ?>

    <div>
    <?php foreach ($items as $item) { ?>
      <div class="row" style="align-items: center; border: solid 1px #eee; padding: 5px;">
        <div class="col-md-3" style="text-align: center;">
          <img src="<?php echo site_url(BANK_THUMB_PATH.$item->{'image'});?>" class="img-rounded" style="max-height: 160px; max-width: 100%;" alt="Search image"/>
          <button type="button" class="btn btn-link" click="javascript: onCompare(<?php echo $origin->{'image'};?>);">Compare Similarity</button>
        </div>
        <div class="col-md-8" style="text-align: left;">
          <div style="width: 100%;"><a href="<?php echo $item->{'link'};?>" style="font-size: large;" target='_blank'><?php echo $item->{'title'};?></a></div>
          <div style="width: 100%; word-break: break-all;">
            <span class="text-success" style="font-size: medium;">
              <?php echo $item->{'description'};?>
            </span>
          </div>
          <div style="width: 100%; word-break: break-all;">
            <span class="text-primary" style="font-size: small;">
              Similarity score: <?php echo $item->{'similarity'};?>%
            </span>
          </div>

        </div>
      </div>
    <?php } ?>
    </div>

        <!-- Page navigation -->
        <div class="col-md-12" style="text-align: right;">
            <?php echo $this->pagination->create_links(); ?>
        </div>
  </div>
  <div class ="starter-template col-md-4">
    <?php foreach ($advertise_items as $item) { ?>
      <img src="<?php echo site_url(ADVERTISE_PATH.$item->{'ta_imagename'});?>" style="width: 100%; margin-bottom: 15px;">
    <?php } ?>
  </div>
</div>

<div class="footer2">
    © MOTUIN All Rights Reserved | <a href="<?php echo site_url('policy');?>" target='_blank'>Website Policy</a> | <a href="<?php echo site_url('about'); ?>" target='_blank'>About</a> | <a href="<?php echo site_url('contact');?>" target='_blank'>Contact Us</a> | <a  href="<?php echo site_url('admin');?>" target='_blank'>Admin</a>
</div>

<div style="text-align: center; margin: 10px;">
  <div><b><?php echo $search_image_count;?></b></div>
  <div>TOTAL IMAGES SEARCHED</div>
</div>


<script type="text/javascript">
  $('#userfile').on('change',function ()
  {
      var filePath = $(this).val();
      $('#search_url').val(filePath);
  });

  $('#search_submit').on('click',function ()
  {
      $('#fileForm').submit();
  });

</script>