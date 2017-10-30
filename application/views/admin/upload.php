<div class="container" role="main" style="margin-top: 50px;">
                    <div class ="starter-template">
                    <?php if(!empty(@$notif)){ ?>
                    <div id="resultalert" class="alert alert-<?php echo @$notif['type'];?>">
                        <p><?php echo @$notif['message'];?></p>
                        <span></span>
                    </div>
                    <?php } ?>

        <?php echo form_open_multipart('');?>
          <div class="panel panel-default">
            <div class="panel-heading">Please choose image to upload</div>
            <div class="panel-body" style="text-align: left;">

                  <div class="form-group">
                      <input type="file" name='userfile' required/>
                  </div>
                  <div class="form-group">
                    <label for="title label-default">Title:</label>
                    <input type="text" class="form-control" name="title" required/>
                  </div>
                  <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" rows="5" name="description" required></textarea>
                  </div>
                  <div class="form-group">
                    <label for="link">Link:</label>
                    <input type="text" class="form-control" name="link" required/>
                  </div>
                  <div class="col-md-offset-3 col-md-6" style="text-align: center;">
                      <input type="submit" class="btn btn-default" value=" &nbsp Save &nbsp">
                  </div>
            </div>
          </div>
          <input type="hidden" name="type" value="file" />
        </form>


        <?php echo form_open_multipart('');?>
          <div class="panel panel-default">
            <div class="panel-heading">Batch upload</div>
            <div class="panel-body" style="text-align: left;">
                  <div class="form-group">
                    <label for="excel_path label-default">Excel file name:</label>
                    <input type="text" class="form-control" name="excel_file" required/>
                  </div>
                  <div class="col-md-offset-3 col-md-6" style="text-align: center;">
                      <input type="submit" class="btn btn-default" value=" &nbsp Batch Upload &nbsp">
                  </div>
            </div>
          </div>
          <input type="hidden" name="type" value="batch" />
        </form>
  </div>
</div>