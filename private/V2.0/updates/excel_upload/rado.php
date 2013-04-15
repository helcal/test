<?
    ini_set('default_charset', 'UTF-8');
    session_start();
    session_regenerate_id();
    require_once("$_SERVER[DOCUMENT_ROOT]/includes/init.php");
    connect_internal_db();
?>
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset="utf-8">
<title>New Page 1</title>
</head>
<body>
<?      
      $lines = file('Rado7.txt');
      echo 'count($lines)=', count($lines);
      
      for($i=0;$i<3;$i++)
      {
          $col_arr = explode("\t", $lines[$i]);
          $txt = $col_arr[37];  //37: PARENT GUID
          $result = query_general("insert into test (value) values ('$txt') ");
          if($result)
              echo '<br>Item inserted';
          else
              echo '<br>failed';
          /*
          for($j=0;$j<count($col_arr);$j++)
          {
              echo "<br>>>>>>>>", ($col_arr[$j]);
          }
          */
      }
      $result = query_general("select value from test ");
      $num_rows = mysql_num_rows($result);
      for($i=0;$i<$num_rows;$i++)
      {
          $row = mysql_fetch_array($result);
          echo '<hr>';
          echo $row[value];
      }
?> 
</body>
</html>