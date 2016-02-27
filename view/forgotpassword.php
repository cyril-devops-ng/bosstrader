<!DOCTYPE html>
<html lang="en">
    
<!-- Mirrored from aqvatarius.com/themes/intuitive/pages-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 13 Sep 2015 11:22:10 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>        
        <!-- meta section -->
        <title>Boss Trader - Forgot password</title>
        
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
                    <?php if($_SESSION['password_sent'] == 1 ): ?>
                    <div class="alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                         Password sent to your phone and email.
                    </div>
                    <?php unset($_SESSION['password_sent']);endif; ?>
                    
                    <?php if($_SESSION['password_sent'] == 0 && isset($_SESSION['password_sent']) ): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                         Username does not match.
                    </div>
                    <?php unset($_SESSION['password_sent']);endif; ?>
                    
                    <div class="title"><center>Forgot your password</center></div>
                    <form action="forgotpassword" method="post">                        
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                <input type="text" class="form-control" placeholder="Username" name='boss_login_ret' required>
                            </div>
                        </div>                        
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <input type="text" class="form-control" placeholder="Company Code" name='company_id_ret' required>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                <select name="login_type" class="form-control dropdown-dark" >
                                    <option value="Boss">Boss</option>
                                    <option value="Sales Person">Sales Person</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group no-border margin-top-20">
                            <button class="btn btn-success btn-block">Retrieve Password</button>
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






