<!DOCTYPE html>
<html lang="en">
    
<!-- Mirrored from aqvatarius.com/themes/intuitive/pages-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 13 Sep 2015 11:22:10 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>        
        <!-- meta section -->
        <title>Boss Trader - Activate</title>
        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
        <meta http-equiv="X-UA-Compatible" content="IE=edge" >
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" >
        
        <link rel="icon" href="favicon.ico" type="image/x-icon" >
        <!-- /meta section -->        
        
        <!-- css styles -->
        <link rel="stylesheet" type="text/css" href="css/default-blue-white.css" id="dev-css">
        <!-- ./css styles -->                               
                
        <!--[if lte IE 9]>
        <link rel="stylesheet" type="text/css" href="css/dev-other/dev-ie-fix.css">
        <![endif]-->
        
        <!-- javascripts -->
        <script type="text/javascript" src="js/plugins/modernizr/modernizr.js"></script>
        <!-- ./javascripts -->
        
        <style>.dev-page{visibility: hidden;}</style>
    </head>
    <body>
                
        <!-- page wrapper -->
        <div class="dev-page dev-page-login dev-page-login-v2">
                      
            <div class="dev-page-login-block">
                <a class="dev-page-login-block__logo">Boss Trader</a>
                <div class="dev-page-login-block__form">
                    <?php if($_SESSION['account_expired'] == 1){ ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                         Your account has expired!
                    </div>
                    <?php } ?>
                    <form action="forgotpassword" method="post">                        
                        <div class="form-group no-border margin-top-20">
                            <button class="btn btn-success btn-block"><?php echo $_SESSION['account_expired'] == 1?"Activate Account":"Top Account"; ?> </button>
                        </div>
                        <p><center><a href="home">Back Home</a></center>                         
                    </form>
                </div>
                <div class="dev-page-login-block__footer">
                    © 2016 <strong>Boss Trader</strong>. All rights reserved.
                </div>
            </div>
            
        </div>
        <!-- ./page wrapper -->                
        
        <!-- javascript -->
        <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>       
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>        
        <!-- ./javascript -->
    </body>

<!-- Mirrored from aqvatarius.com/themes/intuitive/pages-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 13 Sep 2015 11:22:10 GMT -->
</html>






