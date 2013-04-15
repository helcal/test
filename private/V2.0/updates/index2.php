<?
    require ('../includes/init.php');
    require ('../includes/update_funcyions.php');
    //connect_internal_db();
    /*
    $query = "select * from category";
    $result = mysql_query($query);   
    $num_rows = mysql_num_rows($result);
    echo $num_rows;
    for($i=0;$i<$num_rows;$i++)
    {
        $row = mysql_fetch_array($result);
        $catID=$row[catID];
        $item_id = $row[item_id];
        $status = $row[status];
        if($status == 1)
        {
            $query2 = "insert into verif_sluxur set catID='$catID', enable='1' ";
            $result2 = mysql_query($query2);
        }
    }
    echo 'DONE';
    $query = "select * from items";
    $result = mysql_query($query);   
    $num_rows = mysql_num_rows($result);
    echo $num_rows;
    for($i=0;$i<$num_rows;$i++)
    {
        $row = mysql_fetch_array($result);
        //$catID=$row[catID];
        $item_id = $row[item_id];
        $enable = $row[enable];
        $cat_status = $row[cat_status];
        if($enable == 1 && $cat_status == 1)
        {
            $query2 = "insert into verif_sluxur set item_id='$item_id', enable='1' ";
            $result2 = mysql_query($query2);
        }
    }
    */
    echo 'DONE';
?>