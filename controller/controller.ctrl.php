<?php

/* import libraries */
require_once "model/Model.mod.php";
require_once "model/Model.math.php";
require_once 'libraries/Excel/reader.php';
require_once "includes/safemysql.class.php";


$post = $_POST;
$get = $_GET;
$db = new MysqliDb($db_host, $db_user, $db_pass, $db_name);

/* Controller class definition and implementation */

class controller {

    private $current_page = '';
    private $db;
    private $model;
    private $stockList;

    /* constructor */

    public function __construct($db, $post, $get) {
        //print '</pre>';print_r($_GET);print '</pre>';
        /* router */
        $this->db = $db;
//              session_destroy();  
        if ($_SESSION['logged_in'] || $_SESSION['logged_in_sales']) {
            $page = $_GET['view'];
            if (!$_GET['view'] && $_SESSION['logged_in'] ) {
                $this->current_page = 'view/home.php';
            }
            else if( !$_GET['view'] && $_SESSION['logged_in_sales']){
                $this->current_page = 'view/homesales.php';
            }else {
                //check the directory for the requested file
                $request_file = false;
                $handle = opendir('view');
                if ($handle = opendir('view')) {
                    while (false !== ($file = readdir($handle))) {
                        if (($file != ".") && ($file != "..")) {
                            if ($file == $page . '.php'):
                                $request_file = true;
                            endif;
                        }
                    }
                    closedir($handle);
                }
                if (!$request_file) {
                    session_destroy();
                }
                if ($request_file && $page == 'login') {
                    $this->current_page = 'view/home.php';
                } else if ($request_file && $page == 'register') {
                    $this->current_page = 'view/home.php';
                } else if ($request_file && $page == 'referral') {
                    $this->current_page = 'view/home.php';
                } else if ($request_file && $page == 'payment') {
                    $this->current_page = 'view/payment.php';
                }else if ($request_file && $page == 'documentation') {
                    $this->current_page = 'view/documentation.php';
                }
                else {
                    $this->current_page = $request_file ? 'view/' . $page . '.php' : 'view/pageserror.php';
                }
            }
        } else {
            if ($get['view'] == 'register') {
                $this->current_page = 'view/register.php';
            } else if ($get['view'] == 'referral') {
                $this->current_page = 'view/referral.php';
            } else if ($get['view'] == 'forgotpassword') {
                $this->current_page = 'view/forgotpassword.php';
            } else if ($get['view'] == 'payment') {
                $this->current_page = 'view/payment.php';
            }else if ($get['view'] == 'documentation') {
                $this->current_page = 'view/documentation.php';
            }  
            else {
                $this->current_page = 'view/login.php';
            }
//                $this->current_page = $get['view'] == 'register'?'view/register.php':'view/login.php';
        }
        //set view
        $this->handleFormActions();
        require_once $this->current_page;
    }

    function generateCompanyCode() {
        $codeExist = true;

        while ($codeExist != false) {
            $code = rand(1000, 9999);
            $res = $this->model->map_request('retrieve', 'company', $_GET, 'company_code', $code);

            if (!empty($res)) {
                
            } else {
                $codeExist = false;
                return $code;
            }
        }
    }

    /* random password function */

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /* form actions */

    private function handleFormActions() {
        $this->model = new Model($this->db);

        /* handle login */
        if ($_GET['view'] == 'login' || !$_GET['view']) {
//                print'<pre>';print_r($_SESSION);print'</pre>';
            if ($_POST['boss_login']) {

                if ($_POST['login_type'] == 'Boss') {
                    $res = $this->model->map_request_mulwhere('retrieve', 'company', $_POST, array('boss_user', 'boss_password', 'company_id'), array($_POST['boss_login'], $_POST['boss_password'], $_POST['company_id']));
                    $res2 = $this->model->map_request_mulwhere('retrieve', 'company', $_POST, array('boss_user', 'boss_password', 'company_code'), array($_POST['boss_login'], $_POST['boss_password'], $_POST['company_id']));
                    if (!empty($res) || !empty($res2)) {
                        //success
                        if ($this->checkActive($res2)) {
                            if (isset($_SESSION['account_expired'])) {
                                unset($_SESSION['account_expired']);
                            }
                            $_SESSION['logged_in'] = true;
                            $_SESSION['company'] = !empty($res) ? $res : $res2;
                            $this->current_page = 'view/home.php';
                        } else {
                            $_SESSION['account_expired'] = 1;
                            $this->current_page = 'view/payment.php';
                        }
                    } else {
                        $_SESSION['login_failed'] = 1;
                        $this->current_page = 'view/login.php';
                    }
                } else {
                    $res = $this->model->map_request_mulwhere('retrieve', 'company', $_POST, array('sales_user', 'sales_password' , 'company_code'), array($_POST['boss_login'], $_POST['boss_password'] , $_POST['company_id']));
                    if (!empty($res)) {
                        //success
                        if ($this->checkActive($res)) {
                            if (isset($_SESSION['account_expired'])) {
                                unset($_SESSION['account_expired']);
                            }
                            $_SESSION['logged_in_sales'] = true;
                            $_SESSION['company'] = $res;
                            $this->current_page = 'view/homesales.php';
                        } else {
                            $_SESSION['account_expired'] = 1;
                            $this->current_page = 'view/payment.php';
                        }
                    } else {
                        $_SESSION['login_failed'] = 1;
                        $this->current_page = 'view/login.php';
                    }
                }
            }
        }

        if ($_GET['view'] == 'forgotpassword') {
            if ($_POST['boss_login_ret']) {
                $me = $_POST['boss_login_ret'];
                if ($_POST['login_type'] == 'Boss') {
                    $res = $this->model->map_request_mulwhere('retrieve', 'company', $_POST, array('boss_user', 'company_code'), array($_POST['boss_login_ret'], $_POST['company_id_ret']));
                    $profile = $res;
                    $password = $this->randomPassword();
                    
                    
                    if (!empty($res)) {
                        $p = array("boss_password"=>$password);
                        $msg = "Dear $me,\n"
                                . "\n\nA Request for a Password Reset has been Recieved," .
                                "\n\nNew Password: $password"
                                . "\n\nThanks for Counting on us to help Streamline your Sales Activity."
                                . "\n\nRegards"
                                . "\mBossTrader Admin"
                                . "\n\nFor Assistance"
                                . "\nEmail: support@bosstrader.com.ng"
                                . "\nMobile: +2348067934227"
                                . "\nHome:  +2349093216507";
                        $status = $this->model->map_request_mulwhere('update', 'company', $p, array('boss_user','company_code'), array($me,$_POST['company_id_ret']));

                        if ($status) {
                            $this->sendMail($profile[0]['email'], "Password Reset", $msg);
                            $this->sendSMS("", $profile[0]['phone_no'], $profile[0]['email'], "Your new password is "
                                    . $password." ", "");
                            $_SESSION['password_sent'] = 1;
                        } else {
                            $_SESSION['password_sent'] = 0;
                        }
                    } else {
                        $_SESSION['password_sent'] = 0;
                    }
                } else {
                    $res = $this->model->map_request_mulwhere('retrieve', 'company', $_POST, array('sales_user', 'company_code'), array($_POST['boss_login_ret'], $_POST['company_id_ret']));
                    if (!empty($res)) {
                        $profile = $res;
                        $password = $this->randomPassword();
                        $p = array("sales_password"=>$password);
                        $msg = "Dear $me,\n"
                                . "\n\nA Request for a Password Reset has been Recieved," .
                                "\n\nNew Password: $password"
                                . "\n\nThanks for Counting on us to help Streamline your Sales Activity."
                                . "\n\nRegards"
                                . "\mBossTrader Admin"
                                . "\n\nFor Assistance"
                                . "\nEmail: support@bosstrader.com.ng"
                                . "\nMobile: +2348067934227"
                                . "\nHome:  +2349093216507";
                        $status = $this->model->map_request_mulwhere('update', 'company', $p, array('sales_user' , 'company_code'), array($me,$_POST['company_id_ret']));
                        if ($status) {
                            $this->sendMail($profile[0]['email'], "Password Recovery", $msg);
                            $this->sendSMS("", $profile[0]['phone_no'], $profile[0]['email'], "Your new password is "
                                    . $password." ", "");
                            $_SESSION['password_sent'] = 1;
                        } else {
                            $_SESSION['password_sent'] = 0;
                        }
                    } else {
                        $_SESSION['password_sent'] = 0;
                    }
                }
            }
        }

        if ($_GET['view'] == 'todaysales') {
            $companyProfile = $_SESSION['company'];
            $this->model->set_query('SELECT * FROM `sales` WHERE sales.salesDate like ? AND sales.company_id=? ORDER BY sales.salesDate ASC');
            $date = date("Y-m-d") . '%';
            $result = $this->model->map_request('raw_query', '', '', '', '', array($date, $companyProfile[0]['company_id']));
//                print '<pre>';print_r($result);print'</pre>';

            $_SESSION['sales_today'] = $result;
            
            $done = 0;
            if ($_POST['sales_del_id']) {
                $res = $this->model->map_request("delete", "sales", $_POST, "sales_id", $_POST['sales_del_id']);
                if ($res) {
                    foreach ( $result as $k=>$v){
//                        print  $v['sales_id']; print "="; print $_POST['sales_del_id'] ;
                        if ( $v['sales_id'] == $_POST['sales_del_id'] ){
                            $stock_id = $v['stock_id'];
                            //retrieve the quantity
                            $date = date("Y-m-d H:i:s");
                            $data1 = $this->model->map_request_mulwhere('retrieve' , 'stock' , $_POST,array('stock_id','company_id'),array($stock_id,$companyProfile[0]['company_id'] ));
                            $oldquantity = $data1[0]['quantity'];
                            $newquantity = $oldquantity + $v['quantity'];
                            $_udata = array(
//                                'quantity'=>'quantity + '.$v['quantity']
                                'quantity'=>$newquantity,
                            );
                            
                            $upd = $this->model->map_request_mulwhere('update','stock',$_udata,array('stock_id','company_id') , array($stock_id,$companyProfile[0]['company_id'] ));
                            if ( $upd){
                                echo json_encode(array("success" => "Stock deleted successfully!"));
                                exit();
                            }else {
                                echo json_encode(array("failure" => "Stock refill failed!"));
                                exit();
                            }
                        }
                    }
                    
                } else {
                    echo json_encode(array("failure" => "Stock was not deleted!"));
                    exit();
                }
            }
        }
        /* home */
//                if( $_GET['view'] == 'home' ){
        if (($_GET['view'] == 'login' && isset($_SESSION['logged_in'])) || $_GET['view'] == 'home' 
                || isset($_SESSION['logged_in']) || isset($_SESSION['logged_in_sales'])) {
            $companyProfile = $_SESSION['company'];

//                $res =  $this->model->map_request('retrieve','sales',$companyProfile,'company_id',$companyProfile[0]['company_id']);
            $this->model->set_query('SELECT * FROM `sales` WHERE sales.salesDate like ? AND sales.company_id=? ORDER BY sales.salesDate ASC');
            $date = date("Y-m-d") . '%';
            $result = $this->model->map_request('raw_query', '', '', '', '', array($date, $companyProfile[0]['company_id']));
//                print '<pre>';print_r($result);print'</pre>';

            $_SESSION['homesales'] = $result;


            $sqls = new SafeMySQL();
            $not_stock = "SELECT * FROM stock WHERE ?n <= ?n AND ?n =?s";
            $result2 = $sqls->getAll($not_stock, "quantity", "notification", "company_id", $companyProfile[0]['company_id']);

            if (!EMPTY($result2)) {
                $_SESSION['stockrefill'] = $result2;
//                    print'<pre>';print_r($_SESSION['stockrefill']);print'</pre>';
            }
        }
        /* allstock */
        if ($_GET['view'] == 'allstock' || $_GET['view'] == 'allstocksales') {
            $companyProfile = $_SESSION['company'];
            $res = $this->model->map_request('retrieve', 'stock', $companyProfile, 'company_id', $companyProfile[0]['company_id']);
            $this->stockList = $res;
            $_SESSION['stocks'] = $this->stockList;

            if ($_POST['stock_del_id']) {
                $res = $this->model->map_request("delete", "stock", $_POST, "stock_id", $_POST['stock_del_id']);
                if ($res) {
                    echo json_encode(array("success" => "Stock deleted successfully!"));
                    exit();
                } else {
                    echo json_encode(array("failure" => "Stock was not deleted!"));
                    exit();
                }
            }
        }

        /* add stock */
        if ($_GET['view'] == 'addstock' || $_GET['view'] == 'addstocksales') {
            $companyProfile = $_SESSION['company'];
            $companyId = $companyProfile[0]['company_id'];
            $date = date("Y-m-d H:i:s");
            
            $companyProfile = $_SESSION['company'];
            $res = $this->model->map_request('retrieve', 'stock', $companyProfile, 'company_id', $companyProfile[0]['company_id']);
            $_SESSION['stocksadd'] = $res;
            
            
            if ($_POST['selectedStock']) {
                $stocks = $_SESSION['stocksadd'];
                foreach ($stocks as $key => $value) {
                    if ($value['stock_name'] == $_POST['selectedStock']) {
                        echo json_encode(array('sellingPrice' => $value['sell_price'] , 'costPrice'=> $value['cost_price']));
                        exit();
                    }
                }
            }
            
            
            if ($_POST['stockName']) {
                $cp = $_POST['costPrice'];
                $sp = $_POST['sellPrice'];

                if (preg_match('/[0-9]+(\.[0-9]{2})?/', $cp)) {
                    # Successful match
                    $cp = str_replace(",", "", $cp);
                    $sp = str_replace(",", "", $sp);
                    $data = array(
                        'stock_name' => $_POST['stockName'],
                        'quantity' => $_POST['quantity'],
                        'cost_price' => $cp,
                        'sell_price' => $sp,
                        'company_id' => $companyId,
                        'date' => $date
                    );
                    
//                    print'<pre>';print_r($_POST);print'</pre>';
//                    print $_SESSION['company'][0]['company_id'];
                    
                    //check if the stock name exist
                    $thisstock = $this->model->map_request_mulwhere('retrieve', 'stock', $_POST, array( 'stock_name' , 'company_id'), array($_POST['stockName'],  $_SESSION['company'][0]['company_id']));
                    
                    if ( empty($thisstock)) {
                        $inserted = $this->model->map_request('insert', 'stock', $data);
                        if ($inserted) {
                            $_SESSION['newstock'] = 1;
                        } else {
                            $_SESSION['newstock'] = 2;
                        }
                    }else{
//                        print $thisstock[0]['quantity'];
                        $data = array(
                            'stock_name' => $_POST['stockName'],
                            'quantity' => ( $thisstock[0]['quantity'] +  $_POST['quantity'] ),
                            'cost_price' => $cp,
                            'sell_price' => $sp,
                            'company_id' => $companyId,
                            'date' => $date
                        );
                        $update = $this->model->map_request_mulwhere('update', 'stock', $data, array('stock_name',  'company_id'), array($_POST['stockName'],  $_SESSION['company'][0]['company_id']));
//                        print'<pre>';print_r($update);print'</pre>';
                        if ( $update ){
                            $_SESSION['newstock'] = 4;
                        }
                        
                    }
                } else {
                    # Match attempt failed
                    $_SESSION['newstock'] = 3;
                }
            }
        }

        /* logoff */
        if ($_GET['view'] == 'logoff') {
            session_destroy();
            $this->current_page = 'view/login.php';
        }


        /* Register */
        if ($_GET['view'] == 'register') {
            $regdate = date("Y-m-d H:i:s");
            if ($_POST['bossUserReg']) {
                $companyCode = $this->generateCompanyCode();
                $insertdata = array(
                    'boss_user' => $_POST['bossUserReg'],
                    'boss_password' => $_POST['bossPasswordReg'],
                    'sales_user' => $_POST['salesUserReg'],
                    'sales_password' => $_POST['salesPasswordReg'],
                    'company_name' => $_POST['companyNameReg'],
                    'email' => $_POST['companyEmailAddress'],
                    'phone_no' => $_POST['companyPhoneNumber'],
                    'reg_date' => $regdate,
                    'val_period' => '30',
                    'company_code' => $companyCode
                );
                if ($_POST['tokenReg'] == $_SESSION['match_token']) {
                    $salesPasswordValid = 0;
                    $bossPasswordValid = 0;
                    if ($_POST['bossPasswordReg'] != $_POST['bossRepasswordReg']) {
                        $bossPasswordValid = 1;
                        $_SESSION['bosspassword'] = 1;
                    }
                    if ($_POST['salesPasswordReg'] != $_POST['salesRepasswordReg']) {
                        $salesPasswordValid = 1;
                        $_SESSION['salespassword'] = 1;
                    }

                    if ($salesPasswordValid == 0 && $bossPasswordValid == 0) {
                        //check if company already exist
//                        $companydata = $this->model->map_request('retrieve', 'company', $_POST, 'company_name', $_POST['companyNameReg']);
//                        if (empty($companydata)) {
//                            //company does not exist;
                            $i = $this->model->map_request('insert', 'company', $insertdata);

                            if ($i) {//successful registration
                                $bossuser = $_POST['bossUserReg'];
                                $bosspass = $_POST['bossRepasswordReg'];
                                $salesuser = $_POST['salesUserReg'];
                                $salespass = $_POST['salesPasswordReg'];
                                $companyname = $_POST['companyNameReg'];



                                $msg = "Congratulations! Bossusername: " . $_POST['bossUserReg'] . " Bosspass: " . $_POST['bossRepasswordReg'] .
                                        " Company code: " . $companyCode . " Salesperson: $salesuser" . " Salespass: $salespass "
                                ;
                                $_SESSION['newcompany'] = 1;
                                $token = "";
                                $this->sendSMS($token, $_POST['companyPhoneNumber'], $_POST['companyEmailAddress'], $msg, '');
                                $this->sendMail($_POST['companyEmailAddress'], "Welcome to BossTrader", "Dear " . $_POST['bossUserReg'] . ","
                                        . "\nAn Account has been Created for your Company on BossTrader with the Following Credentials:"
                                        . " \n\nBoss Username: " . $bossuser
                                        . "\nBoss Password: $bosspass"
                                        . "\n\nSales Person Username: $salesuser"
                                        . "\nSales Person Password: $salespass"
                                        . "\n\nCompany Name: $companyname"
                                        . "\nCompany Code: $companyCode"
                                        . "\n\nWelcome on board!"
                                        . "\nBossTrader Admin"
                                        . "\n\nFor Assistance:"
                                        . "\nEmail: support@bosstrader.com.ng"
                                        . "\nMobile: +2348067934227"
                                        . "\nHome: +2349093216507");
                            }
//                        } else {
//                            //company does exist
//                            $_SESSION['company_exist'] = 1;
//                        }
                    }
                } else {
                    $_SESSION['newcompany'] = 0;
                }
            }
            if ($_POST['token']) {


                $token = $_POST['token'];
                $phoneno = $_POST['phoneNumber'];
                $email = $_POST['emailAdd'];
                $owneremail = 'cyrilsayeh@gmail.com';
                $subacct = 'BOSSTRADER';
                $subacctpwd = 'passw0rd%%';
                $sender = 'BossTrader';
                $msg = "*DO NOT DISCLOSE* You have a pending registration on BossTrader. Use this $token as your one time password. Queries: 08067934227";
                $msg2 = "Dear " . $_POST['username'] . ", "
                        . "\n\nA Unique One Time Password (OTP) has been generated for your pending Registeration on BossTrader."
                        . "\n\nOne Time Password: " . $token
                        . "\n\nRegards."
                        . "\n\nFor Assistance:"
                        . "\nEmail: support@bosstrader.com.ng"
                        . "\nMobile:+2348067934227"
                        . "\nHome:  +2349093216507 ";
                
                $this->sendMail($email, "BossTrader - Your OTP for registration", $msg2);
                $this->sendSMS($token, $phoneno, $email, $msg, 'match_token');
                
//                    $url = "http://www.smslive247.com/http/index.aspx?"
//                    . "cmd=sendquickmsg"  
//                    . "&owneremail=" . UrlEncode($owneremail)
//                    . "&subacct=" . UrlEncode($subacct)
//                    . "&subacctpwd=" . UrlEncode($subacctpwd)
//                    . "&message=" . UrlEncode($msg)
//                    . "&sender=" . UrlEncode($sender)       
//                    . "&sendto=" . UrlEncode($phoneno)  
//                    . "&msgtype=" . UrlEncode(0)        
//                            ;
//                    if ($f = @fopen($url, "r"))
//                    {
//                        
//                        $answer = fgets($f, 255);
//                        
//                        
//                        if (substr($answer, 0, 2) == "OK")
//                        {
//                            $_SESSION['match_token'] = $token;
//                            echo json_encode(array("success"=>"SMS to $phoneno was successful."));
//                            exit();
//                        }
//                        else
//                        {
//                            echo json_encode(array("failure"=>"SMS to $phoneno was unsuccessful.[$answer]"));
//                            exit();
//                        }
//                    }
//                    else
//                    {
//                        echo json_encode(array("failure"=>"Error: URL could not be opened.")) ;
//                        exit();
//                    }
            }
        }


        /* Sales */
        if ($_GET['view'] == 'sales') {
            $companyProfile = $_SESSION['company'];
            $res = $this->model->map_request('retrieve', 'sales', $companyProfile, 'company_id', $companyProfile[0]['company_id']);
            $_SESSION['sales'] = $res;
        }


        /* Makesales */
        if ($_GET['view'] == 'makesales') {
            $companyProfile = $_SESSION['company'];
            $res = $this->model->map_request('retrieve', 'stock', $companyProfile, 'company_id', $companyProfile[0]['company_id']);
            $_SESSION['allstocks'] = $res;

            if ($_POST['selectedStock']) {
                $stocks = $_SESSION['allstocks'];
                foreach ($stocks as $key => $value) {
                    if ($value['stock_name'] == $_POST['selectedStock']) {
                        echo json_encode(array('sellingPrice' => $value['sell_price'] , 'costPrice'=>$value['cost_price']));
                        exit();
                    }
                }
            }
            if ($_POST['quantityCheck']) {
                $stocks = $_SESSION['allstocks'];
                foreach ($stocks as $key => $value) {
                    if ($value['stock_name'] == $_POST['stock']) {
                        if ($value['quantity'] >= $_POST['quantityCheck']) {
                            echo json_encode(array('message' => array('msg' => 'true', 'quantity' => $value['quantity'])));
                        } else {
                            echo json_encode(array('message' => array('msg' => 'false', 'quantity' => $value['quantity'])));
                        }
                        exit();
                    }
                }
            }
            if ($_POST['sellPrice']) {
                $companyProfile = $_SESSION['company'];
                $date = date("Y-m-d H:i:s");
                foreach ($_SESSION['allstocks'] as $key => $value) {
                    if ($value['stock_name'] == $_POST['stockName']) {
                        $totalPrice = $_POST['sellPrice'] * $_POST['quantity'];
                        $data = array(
                            'stock_id' => $value['stock_id'],
                            'stock_name' => $value['stock_name'],
                            'quantity' => $_POST['quantity'],
                            'unit_price' => $_POST['sellPrice'],
                            'total_price' => $totalPrice,
                            'company_id' => $companyProfile[0]['company_id'],
                            'cost_price' => $value['cost_price'],
                            'salesDate' => $date
                                );

                        /* Add sales record */
                        $insert = $this->model->map_request('insert', 'sales', $data);


                        /* Update stock */
                        $stockCount = $value['quantity'] - $_POST['quantity'];
                        $stockdata = array('quantity' => $stockCount);
                        $query = $this->model->map_request('update', 'stock', $stockdata, 'stock_id', $value['stock_id']);

                        if ($insert && $query) {
                            $_SESSION['sales_made'] = 1;
                        }
                    }
                }
            }
        }


        /* Referral */
        if ($_GET['view'] == 'referral') {
            if ($_POST['first_name']) {
//                        print '<pre>';
//                        print_r($_POST);
//                        print '</PRE>';

                $name = $_POST['first_name'] . " " . $_POST['last_name'];
                $ref_code = rand(100000, 999999);
                $token = $ref_code;
                $msg = "Dear  " . $_POST['first_name']
                        . "\n\nYou have been registered as a Referal on BossTrader."
                        . "\n\nBelow is your Unique Referral Code:"
                        . "\n\nUnique Referral Code: {$token}"
                        . "\n\nPlease make use of this Unique Referral Code whenever you are Activating a Bussiness for a Customer, as this serves as a unique identifier for you."
                        . "\n\nWelcome on board!"
                        . "\nBossTrader Admin"
                        . "\n\nFor Assistance:"
                        . "\nEmail: support@bosstrader.com.ng "
                        . "\nMobile:+2348067934227"
                        . "\nHome:  +2349093216507";
                        
                $data = array(
                    'email' => $_POST['email'],
                    'name' => $name,
                    'password' => $_POST['password'],
                    'referral_code' => $token,
                    'username' => $_POST['username'],
                    'phone_no' => $_POST['phoneNo'],
                    'username' => $_POST['username']
                );
                $res = $this->model->map_request('insert', 'referral', $data);
                if ($res) {
                    $this->sendSMS($token, $_POST['phoneNo'], $_POST['email'], "Dear " . $_POST['username'] . ",\nYour Referral Code is $token.\n\n\nBestRegards!", 'referral_active');
                    $this->sendMail($_POST['email'], "Welcome to BossTrader", $msg);
                    $_SESSION['referral_active'] = 1;
                } else {
                    
                }
            }
        }


        /* Mass upload */
        if ($_GET['view'] == 'massupload' || $_GET['view'] == 'massuploadsales') {
//               print '<pre>';print_r($_POST);print'</pre>';
            if ($_POST['uploadFile']) {
                $companyProfile = $_SESSION['company'];
                $target_dir = "uploads/";
                $target_file = $target_dir . 'ynapmoc' . $companyProfile[0]['company_id'] . basename($_FILES["stockName"]["name"]);
                $uploadOk = 1;
                $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

                $f_name = $_FILES["stockName"]["name"];
//                    $check = getimagesize($_FILES["stockName"]["tmp_name"]);
                $errors = array();
                if ($this->endsWith($f_name, '.xls') || $this->endsWith($f_name, '.xlsx')) {
                    $uploadOk = 1;
                } else {
                    $uploadOk = 0;
                    array_push($errors, 2);
                }


                if (file_exists($target_file)) {
//                        echo "Sorry, file already exists.";
//                        $uploadOk = 0;
                    array_push($errors, 3);
                }

                if ($_FILES["stockName"]["size"] > 500000) {
//                        echo "Sorry, your file is too large.";
                    $uploadOk = 0;
                    $_SESSION['massuploaded'] = 3;
                    array_push($errors, 4);
                }
                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
//                        echo "Sorry, your file was not uploaded.";
                    // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["stockName"]["tmp_name"], $target_file)) {
//                            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                        
                        if (  $this->uploadFileToStockTable($target_file , $companyProfile[0]['company_id'] ) ) {
                            array_push($errors, 1);
                        }else{
                            array_push($errors, 6);
                        }
                    } else {
//                            echo "Sorry, there was an error uploading your file.";
                        array_push($errors, 5);
                    }
                }
                $_SESSION['massuploaded'] = $errors;
//                    print'<pre>';print_r($_SESSION['massuploaded']);print'</pre>';
            }
        }

        /* Customer Service */
        if ($_GET['view'] == 'customerservice' || $_GET['view'] == 'customerservicesales') {
            if ($_POST['yourName']) {
                $companyProfile = $_SESSION['company'];
                $send = $this->sendMail("support@bosstrader.com.ng", $_POST['yourName'], $_POST['yourMessage'], $companyProfile[0]['email']);

                if ($send) {
                    $_SESSION['complain_sent'] = 1;
                } else {
                    $_SESSION['complain_sent'] = 1;
                }
            }
        }
        /* notifications */
        if ($_GET['view'] == 'notificationsetup') {
//                $stocks = $this->model->map_request("retrieve","stock");

            if ($_POST['notValue']) {
                $safesql = new SafeMySQL();
                $val = $_POST['notValue'];
                $sql = "update `stock` set ?u ";
                $p = array("notification" => $val);
                $result = $safesql->query($sql, $p);

                if ($result) {
                    $_SESSION['notSet'] = 1;
                } else {
                    $_SESSION['notSet'] = 0;
                }
            }
        }
        /* Profile edit */
        if ($_GET['view'] == 'editprofile' || $_GET['view'] == 'editprofilesales') {


            $companyProfile = $_SESSION['company'];
            if ($_POST['bossUser'] || $_POST['salesUser']) {
//                print '<pre>' ;print_r($_POST);print'</pre>';
//                print '<pre>' ;print_r($_FILES);print'</pre>';
                if ($_GET['view'] == 'editprofile') {
                    $bossuser = $_POST['bossUser'];
                    $bosspass = $_POST['bossPass'];
                    $bossrepass = $_POST['bossRepass'];
                }
                $salesuser = $_POST['salesUser'];
                $salespass = $_POST['salesPass'];
                $salesrepass = $_POST['salesRepass'];
                if ($_GET['view'] == 'editprofile') {
                    $phone_no = $_POST['phoneNo'];
                    $email = $_POST['email'];

                    if ($bosspass != $bossrepass) {
                        $_SESSION['profileupdated'] = 2;
                    }
                } else {
                    $bosspass = 1;
                    $bossrepass = 1;
                }
                if ($salespass != $salesrepass) {
                    $_SESSION['profileupdated'] = 3;
                }
//                    print'<pre>';print_r("rgegui".$companyProfile[0]['company_id'].$bosspass.",".$bossrepass.",".$salespass.",".$salesrepass);print'</pre>';
                if ($bosspass == $bossrepass && $salespass == $salesrepass) {

                    $sql = "update `company` set ?u where company_id=?s";
                    $p = array(
                        "boss_user" => $bossuser,
                        "boss_password" => $bosspass,
                        "sales_user" => $salesuser,
                        "sales_password" => $salespass,
                        "phone_no" => $phone_no,
                        "email" => $email,
                    );
                    $p2 = array(
                        "sales_user" => $salesuser,
                        "sales_password" => $salespass,
                    );
                    $safesql = new SafeMySQL();

                    if ($_GET['view'] == 'editprofile') {
                        $r = $safesql->query($sql, $p, $companyProfile[0]['company_id']);
                    } else {
                        $r = $safesql->query($sql, $p2, $companyProfile[0]['company_id']);
                    }
                    if ($r) {
                        $_SESSION['profileupdated'] = 1;
                        if ( $_GET['view'] == 'editprofile'){
                            $_SESSION['company'][0]['boss_user'] = $bossuser;
                            $_SESSION['company'][0]['boss_password'] = $bosspass;
                            $_SESSION['company'][0]['phone_no'] = $phone_no;
                            $_SESSION['company'][0]['email'] = $email;
                        }
                        $_SESSION['company'][0]['sales_user'] = $salesuser;
                        $_SESSION['company'][0]['sales_password'] = $salespass;
                        
                        
                    }
                    if ($_FILES["filePic"]["name"] != "") {
                        //handle pic
                        $target_dir = "assets/images/users/";
                        if ($_GET['view'] == 'editprofile') {
                            $target_file = $target_dir . $companyProfile[0]['company_id'] . "_user_1.jpg";
                        } else {
                            $target_file = $target_dir . $companyProfile[0]['company_id'] . "_user_2.jpg";
                        }$uploadOk = 1;
                        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

                        $f_name = $_FILES["filePic"]["name"];
                        //                    $check = getimagesize($_FILES["stockName"]["tmp_name"]);
                        $errors = array();
                        if ($this->endsWith($f_name, '.jpg') || $this->endsWith($f_name, '.jpeg')
                                || $this->endsWith($f_name, '.JPG') ||
                                $this->endsWith($f_name, '.JPEG')) {
                            $uploadOk = 1;
                        } else {
                            $uploadOk = 0;
                            $_SESSION['image_error'] = 1;
                        }



                        if (file_exists($target_file)) {
                            if (unlink($target_file)) {
                                
                            }
                        }

                        if ($_FILES["filePic"]["size"] > 500000) {
                            //                        echo "Sorry, your file is too large.";
                            $uploadOk = 0;
                            $_SESSION['image_error'] = 2;
                        }
                        // Check if $uploadOk is set to 0 by an error
                        if ($uploadOk == 0) {
                            //                        echo "Sorry, your file was not uploaded.";
                            // if everything is ok, try to upload file
                        } else {
                            if (move_uploaded_file($_FILES["filePic"]["tmp_name"], $target_file)) {
                                //                            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                                $_SESSION['profileupdated'] = 1;
                            } else {
                                //                            echo "Sorry, there was an error uploading your file.";
                                $_SESSION['image_error'] = 3;
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function addAmountPlaceHolder( $amount ){
        
    }

    /* check active function */

    public function checkActive($company_data) {
        $plan = $company_data[0]['val_period'];
        $regdate = $company_data[0]['reg_date'];
        $today = date("Y-m-d H:i:s");

        $rd = date_create(explode(" ", $regdate)[0]);
        $td = date_create(explode(" ", $today)[0]);

        $diff = date_diff($td, $rd, true);
        $d = $diff->format("%a");
        return $plan >= $d;
    }

    /* function to upload files to stock table */

    public function uploadFileToStockTable($filename , $companyid ) {
         $excel = new Spreadsheet_Excel_Reader();      // creates object instance of the class
        $excel->setOutputEncoding('CP1251');
        $excel->read($filename);   // reads and stores the excel file data
            foreach( $excel->sheets as $k=>$record ){
                $cells = $record['cells'];
                $update = 0;
                $i_count = 0;
                for($i=1;$i<=$record['numRows'];$i++){
                    if ( $i > 1 ){
                        $date = date("Y-m-d H:i:s");
                        //prepare insert for every record / update
                        $check = $this->model->map_request_mulwhere("retrieve","stock",$_POST,array('stock_name','company_id'),array( $cells[$i][1] , $companyid));
                        
                        if ( !empty($check) ){
                            //update
                            $u_data = array(
                                'quantity'=>( $check[0]['quantity'] + $cells[$i][2] ),
                                'cost_price'=> $cells[$i][3]  ,
                                'date'=>$date,
                                'sell_price'=>$cells[$i][4]
                            );
                            $upd = $this->model->map_request_mulwhere('update','stock' , $u_data , 
                                    array( 'stock_name','company_id' ) , array( $cells[$i][1] , $companyid ));
                            
                             $update = $upd?$update + 1 : $update;
                        }else{
                            //insert
                            $i_data = array(
                                'stock_name'=>$cells[$i][1],
                                'quantity'=>$cells[$i][2],
                                'cost_price'=>$cells[$i][3],
                                'date'=>$date,
                                'sell_price'=>$cells[$i][4],
                                'company_id' =>$companyid,
                                'notification'=>'10'
                            );
                            
                            $insert = $this->model->map_request('insert','stock', $i_data);
                            if ( $insert ){
                               $i_count += 1;
                            }
                        }
                        
                    }
                }
                
                return ( $i_count > 0 || $update > 0 ) ;
                        
            }
    }

    /* function to return stock list */

    public function getStockList() {
        return $this->stockList;
    }

    function home() {
        
    }

    function login() {
        
    }

    /* function to send mail */

    function sendMail($to, $subject, $message, $sender = null) {
        if ($sender == null) {
            $headers = 'From: support@bosstrader.com.ng' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        } else {
            $headers = 'From: ' . $sender . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        }

        if (mail($to, $subject, $message, $headers))
            return true;
        else
            return false;
    }

    /* function send sms */

    function sendSMS($token, $phoneno, $email, $msg, $setvar) {
        $owneremail = 'cyrilsayeh@gmail.com';
        $subacct = 'BOSSTRADER';
        $subacctpwd = 'passw0rd%%';
        $sender = 'BossTrader';
        $url = "http://www.smslive247.com/http/index.aspx?"
                . "cmd=sendquickmsg"
                . "&owneremail=" . UrlEncode($owneremail)
                . "&subacct=" . UrlEncode($subacct)
                . "&subacctpwd=" . UrlEncode($subacctpwd)
                . "&message=" . UrlEncode($msg)
                . "&sender=" . UrlEncode($sender)
                . "&sendto=" . UrlEncode($phoneno)
                . "&msgtype=" . UrlEncode(0)
        ;
        if ($f = @fopen($url, "r")) {

            $answer = fgets($f, 255);


            if (substr($answer, 0, 2) == "OK") {

                $_SESSION[$setvar] = $token;
                IF ($_POST['token']) {
                    echo json_encode(array("success" => "OTP sent to $phoneno."));
                    exit();
                }
            } else {
                IF ($_POST['token']) {
                    echo json_encode(array("failure" => "OTP not sent to $phoneno.[$answer]"));
                    exit();
                }
            }
        } else {
            IF ($_POST['token']) {
                echo json_encode(array("failure" => "Error: URL could not be opened."));
                exit();
            }
        }
    }

    /* function to get current page */

    public function getPage() {
        return $this->current_page;
    }

    /* startwith string function */

    function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    /* endswith string function */

    function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    /* current page url function */

    function curPageURL() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        $cd = explode('/', $pageURL);
        return $cd[count($cd) - 2];
        //return $pageURL;
    }

    /* current page name function */

    function curPageName() {
        return substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
    }

    /* to single function */

    static function to_single($db_data, $field_name) {
        $_single = array();
        $_c = 0;
        foreach ($db_data as $key => $value) {
            $_single[$_c++] = $value[$field_name];
        }
        return $_single;
    }

}

/* Controller class instantiation */
$controller = new controller($db, $post, $get);
?>

