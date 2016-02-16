<?php
    class Math{
        /**
         *
         * @var string method name
         */
        private $method_name;
        private $data;
        function Math($method_name){
            $this->method_name = $method_name;
            if(method_exists($this, $this->method_name)){
                $this->$method_name();
            }
        }
        function get_method_name(){
            return $this->method_name;
        }
        /**
         * Init method
         */
        function init(){
            
        }
        function set_data($data){
            $this->data = $data;
        }
        /**
         *Method to call methods in the Math class
         *
         * @param string $method_name 
         * @return method 
         * 
         */
        function  call_method($method_name){
            $this->method_name = $method_name;
            return $this->$method_name();
        }
        /**
         *Model.math Tax calculator method
         * @return string
         */
        function taxcalculator(){
            /* Reliefs and allowances deducted
             * housing 150, leave allowance 10% of basic,transport 20, utility 10,
             * meal 5, entertainment 6, child allowance 2.5 * 4
             * dependent relative allowance 2 * 2
             * relief 20% + 5
             * nhf 2.5% of basic salary
             * pension 7.5% of basic salary + transport and housing
             */
            $annual_salary = 0;
            foreach($this->data as $k=>$v){
                if($v != '')
                $annual_salary += $v;
            }

            $basic =  $this->data['basic_salary'];
            $housing = $this->data['housing'];
            $meal = $this->data['meal'];
            $entertainment = $this->data['entertainment'];
            $utility = $this->data['utility'];
            $transport = $this->data['transport'];
            $leave = $this->data['leave'];
            $others = $this->data['others'];

            /*make deductions */
            $deductions += 150000;//housing allowance
            $deductions += 20000;//transport allowance
            $deductions += 5000;//meal allowance
            $deductions += 6000;//entertainment allowance
            $deductions += 10000;//utility allowance
            $deductions += (0.2 * $annual_salary) + 5000;//personal relief allowance
            $deductions += 10000;//child allowance
            $deductions += 4000;//dependent relative allowance
            $deductions += (0.1 * $basic);//leave allowance
            $deductions += (0.025 * $basic);//nhf allowance
            $deductions += (0.075 * ($basic + $transport + $housing));//pension allowance
            
            $balance_after_deductions = $annual_salary - $deductions;
            /* Tax the first 160000 of the balance*/
            $tax_part_1 = 22000;
            $tax_part_2 = 0.25 * ($balance_after_deductions - 160000);
            $annual_tax = $tax_part_1 + $tax_part_2;
            $monthly_tax = $annual_tax / 12;
            return array('taxable_income'=>round($balance_after_deductions,2),'annual_tax'=>  round($annual_tax, 2),'monthly_tax'=> round($monthly_tax,2));
        }
        static function generatepassword(){
            //generate random string of length n
            //n could be a minimum of 5 and a maximum of 10
            $n = rand(3, 5);
            $alp = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
            $alphabets = explode(',',$alp);

            $pass_char = '';
            for($i=0;$i<$n;$i++){
                $pass_char .= $alphabets[rand(0, 25)];
            }
            $pass_int = rand(1000,9999);

            $_pass = $pass_char.$pass_int;
            return $_pass;
        }
        static function encrypt_pass($pass){
            $salt = 'jesusislord';
            return md5($pass . $salt);
        }

    }
?>
