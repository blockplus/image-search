<div class="container" role="main" style="margin-top: 50px;">
  <div class ="starter-template">
    
    <div class="container" style="text-align: left;">
      <div class="panel panel-default">
        <div class="panel-heading">Content setting</div>
        <div class="panel-body">
           <form method="post" action="" role="form">

                    <?php if(!empty(@$notif)){ ?>
                    <div id="resultalert" class="alert alert-<?php echo @$notif['type'];?>">
                        <p><?php echo @$notif['message'];?></p>
                        <span></span>
                    </div>
                    <?php } ?>

              <div class="form-group">
                <label for="excel_path label-default" style="text-align: left;">About:</label>
                <textarea class="form-control" rows="5" required name='about_content'><?php echo isset($items['about']) ? $items['about']['content'] : '';?></textarea>
                <input type="hidden" name="about_id" value="<?php echo isset($items['about']) ? $items['about']['id'] : '';?>">
              </div>

              <div class="form-group">
                <label for="excel_path label-default" style="text-align: left;">Website Policy:</label>
                <textarea class="form-control" rows="5" name="policy_content" required><?php echo isset($items['policy']) ? $items['policy']['content'] : '';?></textarea>
                <input type="hidden" name="policy_id" value="<?php echo isset($items['policy']) ? $items['policy']['id'] : '';?>">
              </div>

              <div class="form-group">
                <label for="excel_path label-default" style="text-align: left;">Contact Us:</label>
                <textarea class="form-control" rows="5" name="contact_content" required><?php echo isset($items['policy']) ? $items['contact']['content'] : '';?></textarea>
                <input type="hidden" name="contact_id" value="<?php echo isset($items['contact']) ? $items['contact']['id'] : '';?>">
              </div>

              <div class="col-md-offset-3 col-md-6" style="text-align: center;">
                  <input type="submit" class="btn btn-default" value=" &nbsp Save &nbsp">
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>