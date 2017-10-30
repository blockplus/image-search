<div class="welcome-content">

    <div class="logo-div">
        <img src="<?php echo site_url('assets/images/logo.png');?>">
        <span class="title-font">motuin</span>    
    </div>

  <div class="search-box">
    <div style="width: 100%;">
        <span style="font-size: xx-large; color: white;">Original Image Search For Science</span>

        <?php echo form_open_multipart('search/', 'id="fileForm"');?>
            <div style="padding-top: 10px;">
                <div class="col-sm-6 col-sm-offset-3">
                        <label class="btn btn-primary" style="float: left;">
                            Upload <input type="file" style="display: none !important;" name='userfile' id='userfile' required>
                        </label> 
                        <input type="hidden" name="type" value="file" />

                        <div class="input-group stylish-input-group">
                           <input type="text" class="form-control" name="search_url" id="search_url" placeholder="Upload or enter Image URL" >
                            <span class="input-group-addon">
                                <button id='search_submit'>
                                    <span class="glyphicon glyphicon-search"></span>
                                </button>
                            </span>
                        </div>
                        <input type="hidden" name="type" value="url" />
                </div>
            </div>
        <?php echo "</form>"; ?>

    </div>
  </div>

</div>
<script>
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