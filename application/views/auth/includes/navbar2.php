<div class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container"> 
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span> 
            </button>
            <a href="<?php echo site_url('welcome');?>" class="navbar-brand"  target='_blank'>Image Search</a>
        </div>
        
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="<?php echo site_url('auth/login');?>">
                        <span class="glyphicon glyphicon-user"></span> 
                        <strong>Sign In</strong>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

