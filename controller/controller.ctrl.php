<?php
    require_once "model/Model.mod.php";
    require_once "model/Model.math.php";
    $post = $_POST;$get = $_GET;
    $db = new MysqliDb($db_host,$db_user,$db_pass,$db_name);
    class controller{
        private $current_page = '';
        private $db;
        private $model;
        private $stockList;
        public function __construct($db,$post,$get) {
            //print '</pre>';print_r($_GET);print '</pre>';
            /* router */
            $this->db = $db;
//              session_destroy();  
            if ( $_SESSION['logged_in']){
            $page = $_GET['view'];
            if(!$_GET['view']){
                $this->current_page = 'view/home.php';
            }
            else {
                //check the directory for the requested file
                $request_file = false;
                $handle = opendir('view');
                if($handle = opendir('view')){
                    while (false !== ($file = readdir($handle)))
                    {
                        if (($file != ".") && ($file != ".."))
                        {
                            if($file == $page.'.php'):
                                $request_file = true;
                            endif;
                        }
                    }
                    closedir($handle);
                }
                if(!$request_file){
                    session_destroy();
                }
                 if ( $request_file && $page == 'login'){
                     $this->current_page = 'view/home.php';
                 }
                 else if ( $request_file && $page == 'register'){
                     $this->current_page = 'view/home.php';
                 }
                 else if ( $request_file && $page == 'referral'){
                     $this->current_page = 'view/home.php';
                 }
                 else{
                     $this->current_page = $request_file?'view/'.$page.'.php':'view/pageserror.php';
                 }
                 
            }
            }else{
                if( $get['view'] == 'register'){
                    $this->current_page = 'view/register.php';
                }else if( $get['view'] == 'referral' ){
                    $this->current_page = 'view/referral.php';
                }else{
                    $this->current_page = 'view/login.php';
                }
                $this->current_page = $get['view'] == 'register'?'view/register.php':'view/login.php';
            }
            //set view
            $this->handleFormActions();
            require_once $this->current_page;
            
            
        }
        
        
        private function handleFormActions(){
            $this->model = new Model($this->db);
           
            if ( $_GET['view'] == 'login' || !$_GET['view']){
//                print'<pre>';print_r($_SESSION);print'</pre>';
                if ( $_POST['boss_login']){
                    
                    if( $_POST['login_type'] == 'Boss'){
                        $res = $this->model->map_request_mulwhere('retrieve','company',$_POST,array('boss_user','boss_password','company_id'),array($_POST['boss_login'] , $_POST['boss_password'] , $_POST['company_id']));
                        if (!empty($res) ){
                            //success
                            $_SESSION['logged_in'] = true;
                            $_SESSION['company'] = $res;
                            $this->current_page = 'view/home.php';
                        }else{
                            $this->current_page = 'view/login.php';
                        }
                    }else{
                        $res = $this->model->map_request_mulwhere('retrieve','company',$_POST,array('sales_user','sales_password'),array($_POST['boss_login'] , $_POST['boss_password']));
                        if (!empty($res) ){
                            //success
                            $_SESSION['logged_in'] = true;
                            $_SESSION['company'] = $res;
                            $this->current_page = 'view/homesales.php';
                        }else{
                            $this->current_page = 'view/login.php';
                        }
                    }
                }
            }
            if ( $_GET['view'] == 'allstock' || $_GET['view'] == 'allstocksales'){
                $companyProfile = $_SESSION['company'];
                $res =  $this->model->map_request('retrieve','stock',$companyProfile,'company_id',$companyProfile[0]['company_id']);
                $this->stockList = $res;
                $_SESSION['stocks']= $this->stockList;
            }
            if ( $_GET['view'] == 'addstock' || $_GET['view'] == 'addstocksales'){
                $companyProfile = $_SESSION['company'];
                $companyId = $companyProfile[0]['company_id'];
                $date = date( "Y-m-d H:i:s" );
                if ( $_POST['stockName']){
                    
                    $data = array(
                        'stock_name'=>$_POST['stockName'],
                        'quantity'=>$_POST['quantity'],
                        'cost_price'=>$_POST['costPrice'],
                        'sell_price'=>$_POST['sellPrice'],
                        'company_id'=>$companyId,
                        'date'=>$date
                    );
                    
                    $inserted = $this->model->map_request('insert','stock',$data);
                    if ( $inserted ){
                        $_SESSION['newstock'] = 1;
                    }
                }
            }
            if ( $_GET['view'] == 'logoff' ){
                session_destroy();
                $this->current_page = 'view/login.php';
            }
            
            if ( $_GET['view'] == 'register'){
                $regdate = date("Y-m-d H:i:s");
                if( $_POST['bossUserReg'] ){
                    $insertdata = array(
                        'boss_user'=>$_POST['bossUserReg'],
                        'boss_password'=>$_POST['bossPasswordReg'],
                        'sales_user'=>$_POST['salesUserReg'],
                        'sales_password'=>$_POST['salesPasswordReg'],
                        'company_name'=>$_POST['companyNameReg'],
                        'email'=>$_POST['companyEmailAddress'],
                        'phone_no'=>$_POST['companyPhoneNumber'],
                        'reg_date'=>$regdate,
                        'val_period'=>'30'
                    );
                    
                    $i = $this->model->map_request('insert','company',$insertdata);
                    
                    if ( $i ){
                        $_SESSION['newcompany'] = 1;
                    }
                    
                }
            }
            
            if ( $_GET['view'] == 'sales'){
                $companyProfile = $_SESSION['company'];
                $res =  $this->model->map_request('retrieve','sales',$companyProfile,'company_id',$companyProfile[0]['company_id']);
                $_SESSION['sales']= $res;
            }
            
            if ( $_GET['view'] == 'makesales'){
                $companyProfile = $_SESSION['company'];
                $res =  $this->model->map_request('retrieve','stock',$companyProfile,'company_id',$companyProfile[0]['company_id']);
                $_SESSION['allstocks'] = $res;
                
                if( $_POST['selectedStock']){
                    $stocks = $_SESSION['allstocks'];
                    foreach ($stocks as $key => $value) {
                        if( $value['stock_name'] == $_POST['selectedStock']){
                            echo json_encode( array('sellingPrice'=>$value['sell_price']) );
                            exit();
                        }
                    }
                }
                if( $_POST['quantityCheck']){
                    $stocks = $_SESSION['allstocks'];
                    foreach( $stocks as $key=>$value){
                        if( $value['stock_name'] == $_POST['stock']){
                            if ( $value['quantity'] >= $_POST['quantityCheck']){
                                echo json_encode( array('message'=>array('msg'=>'true','quantity'=>$value['quantity'])) );
                            }else{
                                echo json_encode( array('message'=>array('msg'=>'false','quantity'=>$value['quantity'])) );
                            }
                            exit();
                        }
                    }
                }
                if( $_POST['sellPrice']){
                    $companyProfile = $_SESSION['company'];
                    $date = date("Y-m-d H:i:s");
                    foreach( $_SESSION['allstocks'] as $key=>$value){
                        if( $value['stock_name'] == $_POST['stockName']){
                           $totalPrice = $_POST['sellPrice'] * $_POST['quantity'];
                           $data = array(
                               'stock_id'=>$value['stock_id'],
                               'stock_name'=>$value['stock_name'],
                               'quantity'=>$_POST['quantity'],
                               'unit_price'=>$_POST['sellPrice'],
                               'total_price'=>$totalPrice,
                               'company_id'=>$companyProfile[0]['company_id'],
                               'cost_price'=>$value['cost_price'],
                               'salesDate'=> $date
                           ) ;
                           
                           /* Add sales record */
                           $insert = $this->model->map_request('insert','sales',$data);
                           
                           
                           /*Update stock */
                           $stockCount = $value['quantity'] - $_POST['quantity'];
                           $stockdata = array('quantity'=>$stockCount);
                           $query = $this->model->map_request('update','stock', $stockdata , 'stock_id', $value['stock_id']);
                       
                           if( $insert && $query ){
                               $_SESSION['sales_made'] = 1;
                           }
                        }
                    }
                }
            }
//             print'<pre>';print_r($_POST);print'</pre>';
        }
        
        public function getStockList(){
            return $this->stockList;
        }
        function home(){

        }
        function login(){

        }

        public function getPage(){
            return $this->current_page;
        }
        function curPageURL() {
             $pageURL = 'http';
             if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
             $pageURL .= "://";
             if ($_SERVER["SERVER_PORT"] != "80") {
              $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
             } else {
              $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
             }
             $cd = explode('/', $pageURL);
             return $cd[count($cd)-2];
             //return $pageURL;
        }
        function curPageName() {
            return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
        }


        static function to_single( $db_data , $field_name ){
            $_single = array(); $_c = 0 ;
            foreach ($db_data as $key => $value) {
                $_single[$_c++] = $value[$field_name];
            }
            return $_single;
        }

        }
    
    $controller = new controller($db,$post,$get);
    
    
?>

