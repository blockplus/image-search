<div class="header general">
      <div class="col-sm-12 logo">
        <img src="<?php echo site_url('assets/images/logo.png');?>" style="height: 100%;">
        <span class="title-font-small">motuin</span> 
      </div>
</div>


<div class="container" role="main" style="min-height: calc(100vh - 95px);">
  <div class ="starter-template col-md-8">

      <div class="form-area">  
        <form role="form">
          <br style="clear:both">
                  <h1 style="margin-bottom: 25px; text-align: center;">Contact Us</h1>
                  <span class="text-info"><?php echo isset($content) ? $content : ''; ?></span> 
              <div class="form-group">
              <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Mobile Number" required>
            </div>
            <div class="form-group">
              <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
            </div>
                      <div class="form-group">
                      <textarea class="form-control" type="textarea" id="message" placeholder="Message" maxlength="140" rows="7"></textarea>
                          <span class="help-block"><p id="characterLeft" class="help-block ">You have reached the limit</p></span>                    
                      </div>
              
          <button type="button" id="submit" name="submit" class="btn btn-primary pull-right">Submit Form</button>
          </form>
      </div>
  </div>
  
  <div class ="starter-template col-md-4">
    <?php foreach ($advertise_items as $item) { ?>
      <img src="<?php echo site_url(ADVERTISE_PATH.$item['imagename']);?>" style="width: 100%; margin-bottom: 15px;">
    <?php } ?>
  </div>
</div>