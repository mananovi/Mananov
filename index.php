<!DOCTYPE html>

<html>
    <head>
        <meta charset="windows-1251">
        <title></title>
    </head>
    <body>
        <?php
        
        require_once('Logger.php');
        setlocale(LC_ALL, "ru_RU.CP1251");
        Logger::$PATH = dirname(__FILE__);
        // Logger::$PATH = $_SERVER['DOCUMENT_ROOT'];
              //���� true, �� $Logger->log($mess) ������� � ��,
                                          //���� false, �� � ����
        if( $_POST['select1'] == 1 )
            Logger::$isOutINBD = true;
        else
            Logger::$isOutINBD = false;
        $host = $_POST['host'];
        $usrName = $_POST['usrName'];//Log
        $pass = $_POST['pass'];
        $tableName = 'Log1';
        $Logger =  Logger::getLogger( $host, $usrName, $pass, $tableName ); 
        
        //����� ������� ��� ������������ ������, ������� ������, ��� ������, ��� ���������� 
        
        $Mass = array(
           '������' => array('���' => '����', '��������' => '��������',
           '��� ��������' => 1966),
           '�������' => array('���' => '������', '��������' => '����������',
           '��� ��������' => 1980)
        );
        /*
        $Mass = "�������� ������� �� ����� ���������� ��� ���������� �����
        �� ������������� ���������������� ������� � ������ ���������. ��������
        ���������� ����������, ���������� ����� ��������� �������� ��������
        ��� ���Ļ � ���������. ����������� ������ �������� �����������-���������
        ������ ������� � ������ ������ �������, ��������� ����, ��������������� �
        �������� ������� � ������, ���������� �����, �������� ����, �������������
        ���������� ������ ����������� �������. ����� �����, ���������� ������� �����
        �������. ��� ������������� ������ ������ ���� ��������� �� ������� 31 ������� 
        2015 ����.������������ ��������� ���������, � ������ �������, ���������
        ����������, �������, ����������� � ������, ������� ������� � �� ��������� ��
        ������, � ����� ���� ������, �������� ����������, ��������� � �����������
        �����, �� ����������� ������ �� �������������� ������, ����������� ����������� ������� �������� 139,4 �������� ������.";
         */
        $var = $_SERVER['DOCUMENT_ROOT'];
        $data = $GLOBALS;
        //$data = new Demo();
        //$data = new Exception('������� �� ����.'); 
        
        $Logger->log(  $data  );          //�������� ������� ������
  
 

       
        ?>
    </body>
</html>
