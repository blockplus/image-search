<div class="welcome-content">

    <div class="logo-div">
        <img src="<?php echo site_url('assets/images/logo.png');?>">
        <span class="title-font">motuin</span>    
    </div>

  <div class="search-box">
    <div>
        <span style="font-size: xx-large; color: white;">Original Image Search For Science</span>

        <div style="padding-top: 10px;">
            <div class="col-sm-6 col-sm-offset-3">
                <label class="btn btn-primary" style="float: left;">
                    Upload <input type="file" style="display: none !important;" required>
                </label> 

                <div class="input-group stylish-input-group">

                   <input type="text" class="form-control" ng-model="url" placeholder="Upload or enter Image URL" >
                    <span class="input-group-addon">
                        <button ng-click="searchUrl()">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
  </div>

</div>