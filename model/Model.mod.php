<?php
    class Model{
        /**
         * A variable that holds the type of database operation which is
         * usually either an insert, update, retrieve or delete
         *
         * @var string
         *
         */
        private $opType;//[insert,update,retrieve,delete,custom1,custom2]
        /**
         *An instance of the MysqliDb class
         *
         * @var MysqliDb
         */
        private $db;
        private $query;

        /**
         *Constructor for the Model class
         *
         * @param MysqliDb $db Database object
         *
         */
        function Model($db){
            $this->db = $db;
        }
        /**
         *Method to set the database operation type
         *
         * @param string $opType The operation type is usually an insert, update, retrieve or delete
         */
        function set_opType($opType){
            $this->opType = $opType;
        }
        /**
         *Method to get the database operation type
         *
         *
         */
        function get_opType(){
            return $this->opType;
        }
        function set_query($query){
            $this->query = $query;
        }
        function get_query(){
            return $this->query;
        }
        function map_request($opType,$table,$data,$whereF=null,$whereV=null,$params=null){

            switch ($opType) {
                case 'insert':
                   return ($this->db->insert($table,$data));
                        break;
                case 'update':
                    return ($this->db->where($whereF,$whereV)->update($table,$data));
                        break;
                case 'retrieve':
                    return ($whereF==null)?$this->db->get($table):$this->db->where($whereF,$whereV)->get($table);
                        break;
                case 'delete':
                    return ($this->db->where($whereF,$whereV)->delete($table));
                        break;
                case 'raw_query':
                    if(isset($this->query)):
                        if($params[0]){
                            return ($this->db->rawQuery($this->get_query(), $params));
                        }
                    return ($this->db->rawQuery($this->get_query()));
                    endif;
                        break;
                case 'generic_query':
                      if(isset($this->query)):
                        return ($this->db->query($this->get_query()));
                        endif;
                        break;
                default:
                        return 'invalid optype';
                        break;
            }
        }
        function map_request_mulwhere($opType,$table,$data,$whereF,$whereV){
            switch($opType){
                case 'update':
                    for($i=0;$i<count($whereF);$i++){
                        $this->db->where($whereF[$i],$whereV[$i]);
                    }
                    return $this->db->update($table,$data);
                    break;
                case 'retrieve':
                    for($i=0;$i<count($whereF);$i++){
                        $this->db->where($whereF[$i],$whereV[$i]);
                    }
                    return $this->db->get($table);
                    break;
                case 'delete':
                    for($i=0;$i<count($whereF);$i++){
                        $this->db->where($whereF[$i],$whereV[$i]);
                    }
                    return $this->db->delete($table);
                    break;
            }
        }
    }
?>
