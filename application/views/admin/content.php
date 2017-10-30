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
                <textarea class="form-control" rows="5" required name='about'><?php echo $items['about'];?></textarea>
              </div>

              <div class="form-group">
                <label for="excel_path label-default" style="text-align: left;">Website Policy:</label>
                <textarea class="form-control" rows="5" name="policy" required><?php echo $items['policy'];?></textarea>
              </div>

              <div class="form-group">
                <label for="excel_path label-default" style="text-align: left;">Contact Us:</label>
                <textarea class="form-control" rows="5" name="contact" required><?php echo $items['contact'];?></textarea>
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