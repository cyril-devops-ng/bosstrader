<!DOCTYPE html>
<html lang="en">
    
<!-- Mirrored from aqvatarius.com/themes/intuitive/pages-registration.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 13 Sep 2015 11:22:11 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>        
        <!-- meta section -->
        <title>Boss Trader - Become a referral</title>
        
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
         <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="script/js.js"></script>
        <!-- ./javascripts -->
        
        <style>.dev-page{visibility: hidden;}</style>
    </head>
    <body>
                
        <!-- page wrapper -->
        <div class="dev-page dev-page-login dev-page-login-v2 dev-page-registration">
                      
            <div class="dev-page-login-block">
                <a class="dev-page-login-block__logo">Intuitive</a>
                <div class="dev-page-login-block__form">      
                    <div class="title" style="font-style: italic;"><strong>Hey!!</strong>, Become a <a href='home'>Boss Trader</a> referral and start making money now!!!</div>
                    <?php if ( $_SESSION['referral_active'] == 1 ): ?>
                                <div class="alert alert-success alert-dismissible" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <strong>Congratulations!</strong> You just became a referral of Boss Trader.
                                </div>
                           <?php endif; unset($_SESSION['referral_active']);?>
                    <form action="referral" method="post">                        
                        <div class="row">
                            <div class="col-md-6">                                    
                                <div class="form-group">                            
                                    <input type="text" class="form-control" placeholder="First Name" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">                            
                                    <input type="text" class="form-control" placeholder="Last Name" name="last_name" required>
                                </div>    
                            </div>
                            <div class="col-md-12">
                                <div class="form-group no-margin">
                                    <input type="text" class="form-control" placeholder="Username" name="username" required>                                    
                                </div>
                                <span class="help-block"><a href="home">http://www.bosstrader.com.ng ->Back Home</a></strong></span>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group margin-top-30">
                                    <input type="email" class="form-control" placeholder="Email" name="email" required>
                                </div>
                                 <div class="form-group margin-top-30">
                                    <input type="number" class="form-control" placeholder="Phone number" name="phoneNo" required>
                                </div>
                                <div class="form-group">                            
                                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                                </div>
                                <div class="form-group">                            
                                    <input type="password" class="form-control" placeholder="Repeat Password" name="re_password" required>
                                </div>
                                
                            </div>
                        </div>
                          <div class="form-group no-border">
                              <button class="btn btn-danger btn-block" type="submit">Sign up</button>
                        </div>
                        
                        
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

<!-- Mirrored from aqvatarius.com/themes/intuitive/pages-registration.html by HTTrack Website Copier/3.x [XR&CO'2014], Sun, 13 Sep 2015 11:22:11 GMT -->
</html>






