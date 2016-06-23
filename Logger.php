<?php

class Logger {

    //статические переменные
    public static $PATH;
    public static $isOutINBD;
    protected static $loggers = array();
    protected $host;
    protected $usrName;
    protected $pass;
    protected $tableName;
    protected $db;
    protected $fp;
  

    public function __construct( $host, $usrName, $pass, $tableName ) {
        $this->host = $host;
        $this->usrName = $usrName;
        $this->pass = $pass;
        $this->tableName = $tableName;
        
        
    }

    public function open() {
        if (self::$PATH == null) {
            return;
        }

        $this->fp = fopen( self::$PATH . '/' . 'Log.txt' , 'a+' );
    }

    public static function getLogger( $host, $usrName, $pass, $tableName ) {
        if (!isset(self::$loggers[$usrName])) {
            self::$loggers[$usrName] = new Logger( $host, $usrName, $pass, $tableName );
        }

        return self::$loggers[$usrName];
    }

    public function fileLog($message) {
        $this->open();
        if (!is_string($message)) {
            $this->logPrint($message);
            return;
        }

        $log = '';
        $log.='[' . date('Y-m-d H:i:s', time()) . '] ';
        /*
        if( $message instanceof Exception )
            $message = $message->getMessage();
        */
        $log.=$message;
        $log.="\n";
        $this->_write($log);
    }

    public function logPrint($obj) {
        ob_start();
        echo '<pre>';
        print_r($obj);
        echo '</pre>';
        $ob = ob_get_clean();
        //$this->parsePrint_r( $ob, true );  //Альтернатива функции $this->fileLog($ob);
                                             //в этом месте
        $this->fileLog($ob);
    }

    protected function _write($string) {
       
        fwrite($this->fp, $string);
        echo $string;
    }

    public function __destruct() {
        if($this->fp)
             fclose($this->fp);
        
    }
    public function log( $message ){
        if( Logger::$isOutINBD  ){
            $this->getTable();
            $this->DBlog( $message );   
        }
        else
            $this->fileLog( $message ) ;
           
    }
    public function DBlog( $message ) {
        
            $this->db->query("SET NAMES cp1251");   
             /*
            if( $message instanceof Exception )
            {
                $message = $message->getMessage();
                $this->DBlog( $message );
                return;
            }
             
             */
            if ( is_string($message) && strlen($message) > 99 ) { 
                $parts = array();
                $partCount = strlen($message) /100;
                $pos = 0;
                for ($i = 0; $i < $partCount; $i++, $pos += 100 ) {
                    $parts[] = substr( $message, $pos, 100);
                }
                 foreach(   $parts as $inKey => $inValue)
                        {
                            $this->db->query("INSERT INTO $this->tableName VALUES 
                            (NULL, CURDATE(),CURTIME(), '".$inValue."' )" );  
                        }
                        
            }
            else if ( is_string($message) ) { 
                $query = "INSERT INTO  $this->tableName  VALUES
                (NULL, CURDATE(),CURTIME(), '".$message."' )";
                $this->db->query( $query );
            }
            else {
               
                ob_start();
                echo '<pre>';
                print_r($message);
                echo '</pre>';
                $message = ob_get_clean();  
                            
               
                $this->parsePrint_r( $message, false );

               
            }    
            
            $res = $this->db->query("SELECT * FROM  $this->tableName  ORDER BY `id_log` ");
            echo 'Число строк в таблице ' .  $res->num_rows . '<br>';
            while ($pole = $res->fetch_assoc()) {
                echo $pole['id_log'] . '-[  ' . $pole['Date'] . '   ' . $pole['Time'] 
                        . ' ]- ' . $pole['Message'] . '<br>';
            }
            $res->close();
            $query = "DROP TABLE $this->tableName  ";
            $this->db->query($query);
            
            $this->db->close();
    
    }
    public function getTable() {
        if ( @$this->db = new mysqli( $this->hostName, $this->usrName, $this->pass )
             or die( "Could not connect to MySQL.") ) {
            $this->db->select_db("Log")or die( "Не удалось выбрать базу".mysql_error() );           
            $result = $this->db->query(
            "SHOW TABLES LIKE '" . $this->tableName . "';"
            ) or die( mysql_error() );
            $found = $result->num_rows > 0; //Проверяем, была ли раньше создана таблица логов
            if($found)
            {
                echo "Table $this->tableName is exist". '<br>';
                            //Таблица логов  была создана  
            }                           //Ничего не делаем
            else 
            {
                echo "Table $this->tableName is not exist". '<br>';//Таблицы логов ещё не было создано
                $query = " CREATE TABLE $this->tableName  (        
                `id_log`  INT NOT NULL AUTO_INCREMENT,
                `Date` DATE,
                `Time` TIME,
                `Message` CHAR(255) DEFAULT 'None',
                PRIMARY KEY (`id_log`)
                ) ENGINE=MyISAM DEFAULT CHARSET=cp1251";
                $this->db->query($query);                         //Создаём таблицу логов
                echo "Table $tableName is created ". '<br>';
            
          
            }
        }
        else {
            echo "Не удалось установить подключение к базе данных";
        }
    }
    function printMass( $Mass )       //Функция пока не используется
    {
        foreach(   $Mass as $Key => $Value)
        {
            if( is_array( $Value ) )
            {
                $this->printMass( $Value );
 
            }
            else
            {       
           
                $this->db->query("INSERT INTO $this->tableName VALUES 
                (NULL, CURDATE(),CURTIME(), '".$Value."' )" );  
            }
        }
        
            
    }
     function parsePrint_r( $Mass, $flag )
    {
      
         for( $i = 0; $i < strlen( $Mass ); $i++ )
         {
             if( substr( $Mass, $i, 1 ) == "[" ) 
             {
                $j = $i++;

                while( substr( $Mass, $i, 1 ) != "[" )
                {
                    if( $i >= strlen( $Mass )  )
                        break;
                        
                    ++$i;
                }
                if( $flag == false )
                {
                    $Value = substr( $Mass, $j  , $i - $j );
                    $this->db->query("INSERT INTO $this->tableName VALUES 
                    (NULL, CURDATE(),CURTIME(), '".$Value."' )" );   
                    --$i;
                }
                else{
                    
                    $Value  = '[' . date('Y-m-d H:i:s', time()) . '] ';
                    $Value .= substr( $Mass, $j  , $i - $j );
                    $Value .= "\n";
                    fwrite($this->fp, $Value);
                    echo $Value . "<br>";
                    --$i;
                }
     
             }
         }
     
     }

}
class Demo{                      //Класс для примера вывода объекта в логи
    public $digit;
    public $Str;
    public $Mass;
    public function __construct() {
        $this->Mass['Один'] = 1;
        $this->Mass['Два'] = 2;
        $this->Mass['Три'] = 3;
        $this->Mass['Четыре'] = 4;
        $this->digit = 68769;
        $this->Str = "Строка";
    }
    public function __destruct() {
        unset($this->Str); 
        unset($this->Mass);
    }
}
?>


