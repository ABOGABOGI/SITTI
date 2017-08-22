<?php
class SQLData{
    var $schema;
    var $conn;
    var $rs;
    var $msg;
    var $lastInsertId;
    function SQLData(){

        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = 'root';
        $nameDb = 'SITTI';
        $this->host = $CONFIG['HOST'];
        $this->username = $CONFIG['USERNAME'];
        $this->password = $CONFIG['PASSWORD'];
        $this->database = $CONFIG['DATABASE'];
        $this->msg="";

    }
    function open(){
        if($this->conn==NULL){
            $this->conn = mysql_connect($this->host,$this->username,$this->password);
            $this->addMessage("Open Connection -->".$this->conn);
        }else{
            $this->addMessage("Connection already opened : ".$this->conn."<br/>");
        }
    }
    function addMessage($msg){
        $this->msg.=$msg."<br/>";
    }
    function close(){
        if($this->conn!=NULL){
            if(@mysql_close($this->conn)){
                $this->addMessage("Connection closed --> ".$this->conn);
                $this->conn=NULL;
            }
        }else{
            $this->addMessage("Connection already closed --> ".$this->conn);
        }
    }
    function setSchema($schema){
        $this->schema = $schema;
    }
    function fetch($str,$flag=0){
        $sql = $this->query($str);

        if($flag){
            $n=0;
            while($fetch = mysql_fetch_array($sql,MYSQL_ASSOC)){
                $rs[$n] = $fetch;
                $n++;
            }
        }else{
            $rs = mysql_fetch_array($sql,MYSQL_ASSOC);
        }

        mysql_free_result($sql);
        return $rs;
    }
    function reset(){
        $this->rs = NULL;
    }
    function query($sql,$flag=0){
        //print $sql;
        //if($this->conn==NULL){$this->open();}
        $this->addMessage("do query using ".$this->conn.": <br/>");
        $rs = mysql_db_query($this->database,$sql);
        if($flag){
            $this->lastInsertId = mysql_insert_id();
        }
        $this->addMessage($rs);
        $this->addMessage(mysql_error()."<br/>");
        return $rs;
    }
    function getMessage(){
        $msg=mysql_error();
        $msg.="<br/>";
        $msg.=$this->msg;
        return $msg;
    }
    function getConsoleMessage(){
        $msg=mysql_error();
        $msg.="\n";
        $msg.=str_replace("<br/>","\n",$this->msg);
        return $msg;
    }
    function getLastInsertId(){
        return $this->lastInsertId;
    }
}
?>