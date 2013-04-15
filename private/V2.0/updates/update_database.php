<?
    //USE THIS UPDATE TO ADD A NEW DATABASE TO ALL BRAND TABLES
    require ('../../../includes/init.php');
    connect_internal_db();
    /*
    //**************************************************************************
    //THIS PORTION IS USED TO ADD VERIF_DEALER_GROUPING TABLES TO THE CURRENT EXISTING VERIF_TBLS
    $result = query_general("select * from assigned_domain");
    $num_rows = mysql_num_rows($result);
    for($i=0;$i<$num_rows;$i++)
    {
        $row = mysql_fetch_array($result);
        echo '<br>verif_tbl=', $row[verif_tbl];
        $new_tbl = $row[verif_tbl]."_grouping";
        echo ' _____ $new_tbl=', $new_tbl;                    
        $result2 = query_general ("CREATE TABLE `$new_tbl` (                                  
                                  `groupID` INT NOT NULL,
                                  `brand_master_id` INT NOT NULL,
                                  UNIQUE ( `groupID` ) , INDEX( `brand_master_id` )
                                  ) ENGINE = MYISAM ;   
                                   ");
      if($result2)
          echo 'success!';
      else
          echo 'failed';
      echo '<br>Error: ', mysql_error();
    }
    */
    
    /*
    //**************************************************************************
    //THIS PORTION IS USED TO ADD NEWBRND_BRAND_GROUPING TABLES TO THE CURRECT EXISTING BRAND TABLES.
    $result = query_general("select * from brand_master");
    $num_rows = mysql_num_rows($result);
    for($i=0;$i<$num_rows;$i++)
    {
        $row = mysql_fetch_array($result);
        get_brand_name_new($row[brand_master_id], &$brand_name_arr);          
        $brand_name = $brand_name_arr[brand_name];
        echo '<br>$brand_name=', $brand_name; 
        
        if($brand_name!='')
        {
            $brnd_cat_tbl = "brnd_$brand_name"."_grouping";                    
            $result2 = query_general ("CREATE TABLE `$brnd_cat_tbl` (
                                  `guid` INT NOT NULL COMMENT 'registered guid from guid tbl',
                                  `groupID` INT NOT NULL,
                                  `enable` INT NOT NULL DEFAULT '1',
                                  `price` FLOAT( 10,2 ) NOT NULL ,
                                  INDEX ( `guid` ) 
                                  ) ENGINE = MYISAM ;   
                                   ");
            if($result2)
                echo "Tbl $brnd_cat_tbl created!";
            else
                echo "<font color=RED>Failed on $brnd_cat_tbl";
        }
    }
    */
    //USE THE FOLLOWING SQL TO ALTER THE TABLES
    //$sql = "ALTER TABLE `brnd_rado_watch_EN` CHANGE `pg_name` `pg_name` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
    
?>