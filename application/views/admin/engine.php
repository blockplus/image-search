<div class="container" role="main" style="margin-top: 50px;">
  <div class ="starter-template">
    
    <div class="container" style="text-align: left;">
      <div class="panel panel-default">
        <div class="panel-heading">Search Engine Operation</div>
        <div class="panel-body">
           <form method="post" action="" role="form">

                    <?php if(!empty(@$notif)){ ?>
                    <div id="resultalert" class="alert alert-<?php echo @$notif['type'];?>">
                        <p><?php echo @$notif['message'];?></p>
                        <span></span>
                    </div>
                    <?php } ?>
              <div class="col-md-offset-0 col-md-4" style="text-align: center;">
                  <input type="submit" class="btn btn-default" value=" &nbsp Delete All Index &nbsp" style="width: 100%;" name="delete">
              </div>
              <div class="col-md-offset-0 col-md-4" style="text-align: center;">
                  <input type="submit" class="btn btn-default" value=" &nbsp Start Engine &nbsp" style="width: 100%;" name="start">
              </div>
              <div class="col-md-offset-0 col-md-4" style="text-align: center;">
                  <input type="submit" class="btn btn-default" value=" &nbsp Restart Engine &nbsp" style="width: 100%;" name="restart">
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>