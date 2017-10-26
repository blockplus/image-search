<div class="header general">
      <div class="col-sm-12 logo">
        <img src="<?php echo site_url('assets/images/logo.png');?>" style="height: 100%;">
        <span class="title-font-small">motuin</span> 
      </div>
</div>


<div class="container" role="main" style="min-height: calc(100vh - 95px);">
  <div class ="starter-template col-md-8">
      <h1 style="margin-bottom: 25px; text-align: center;">Website Policy</h1> 
      <span class="text-info"><?php echo isset($content) ? $content : ''; ?></span> 
  </div>
  
  <div class ="starter-template col-md-4">
    <img ng-src="{{item.url}}" style="width: 100%; margin-bottom: 15px;" ng-repeat="item in ads_images">
  </div>
</div>