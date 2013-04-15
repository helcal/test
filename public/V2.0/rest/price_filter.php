<?php
session_start();
session_regenerate_id();

function regexp_escape(&$x){
    $x = str_replace('.', '[[...]]', $x);
    $x = str_replace('(', '[[.(.]]', $x);
    $x = str_replace(')', '[[.).]]', $x);
    return $x;
}

function permutate($possible, $done = array()){
    $var_name = array_keys($possible);
    $var_name = $var_name[0];
    $set = array_shift($possible);
    
    $temp = array();
    foreach ($set as $item){
        if (count($done) == 0)
            $temp[] = "|$var_name=$item";
        else 
            foreach ($done as $old)
                $temp[] = "$old|$var_name=$item";
    }
    $done = $temp;
    if (count($possible) == 0)
        return $done;
    else     
        return permutate($possible, $done);
    
}

require_once $_SERVER['DOCUMENT_ROOT'].'/includes/init.php';
connect_internal_db();

$get = $_GET;

if (!isset($get['guid']) || !isset($get['brand'])){
    echo "<table><tr><td></td></tr></table>";
    exit;
}

$guid = $get['guid'];
$brand = $get['brand'];
unset($get['guid']);
unset($get['brand']);

$filter = $get;


$tables = get_brnd_tbl_name($brand."_EN");
$query="SELECT spec_field, spec_val FROM {$tables[2]} WHERE var_options='1' AND guid=$guid";
$results = query_general($query);
$price_mods = array();
while($row = mysql_fetch_assoc($results)){
    if (!isset($price_mods[$row['spec_field']]))
        $price_mods[$row['spec_field']] = array();
    $price_mods[$row['spec_field']][] = $row['spec_val'];
}
ksort($price_mods);

//var_dump($price_mods);

foreach ($filter as $key => $val){
    if ($val == 'All')
        unset($filter[$key]);
    else 
        $price_mods[str_replace("_", " ", $key)] = array(str_replace("_", " ", $val));
}
//var_dump($price_mods);
//var_dump($filter);

$q = "SELECT * FROM brnd_{$brand}_pricing WHERE guid=$guid";
foreach ($filter as $key => $val){
    $key = str_replace(" ", "_", $key);
    $val = str_replace(" ", "_", $val);
    regexp_escape($key);
    regexp_escape($val);
    $q .= " AND `criteria` REGEXP '[[.|.]]".$key."[[.=.]]".$val."([[.|.]]|$)'";
}

//var_dump($q);

$res = query_general($q);

$set = array();

$temp_set = permutate($price_mods);
foreach ($temp_set as $key){
    $criteria = $key;
    $crit = explode('|', $criteria);
    array_shift($crit);
    $tCrit = array();
    
    foreach ($crit as $item){
        $item = explode('=', $item);
        $tCrit[$item[0]] = $item[1];
    }
    
    ksort($tCrit);
    
    $set[$key]['criteria'] = $tCrit;
}

while($row = mysql_fetch_assoc($res)){
    $criteria = $row['criteria'];
    $crit = explode('|', $criteria);
    array_shift($crit);
    $tCrit = array();
    
    foreach ($crit as $item){
        $item = explode('=', $item);
        $tCrit[str_replace("_", " ", $item[0])] = str_replace("_", " ", $item[1]);
    }
    
    ksort($tCrit);
    $row['criteria'] = $tCrit;
    
    $set[str_replace("_", " ", $criteria)] = $row;
}

ksort($set);
$set_keys = array_keys($set);

$tabs = array(
    'model' => 0,
    'price' => 10000
);
?>
<form id="permutations" method="POST">
    <table width="100%">
        <tr>
            <th style="text-align: left">
                Variant Model Number
            </th>
            <?php if (count($set) == 0) { ?>
                <th style="text-align: center; width:10000px">
                    Criteria
                </th>
            <?php } else { ?>
                <?php foreach ($set[$set_keys[0]]['criteria'] as $header => $not_used) { ?>
                    <th style="text-align: center; width:10000px">
                        <?= $header ?>
                    </th>
                <?php } ?>
            <?php } ?>
            <th style="text-align: center; width:0px">
                Price
            </th>
            <?php if (!defined('PRICING_VAR_ADMIN') || PRICING_VAR_ADMIN == 0) { ?>
                <?php if(defined('PRICING_VAR_FAST_SHIP') && PRICING_VAR_FAST_SHIP == 1) { ?>
                    <th>
                        Fast Ship<br />(Days)
                    </th>
                <?php } ?>
                <th>
                    Override Pricing
                </th>
                <th>
                    Quantity
                </th>
            <?php } ?>
        </tr>
        <?php foreach ($set as $i => $perm){ ?>
            <tr>
                <td style="text-align: left">
                    <input type="text" name="variants[<?= $i ?>][variant_model]" tabindex="<?= $tabs['model'] += 5 ?>" value="<?= $set[$i]['variant_model'] ?>" />
                    <input type="hidden" name="variants[<?= $i ?>][criteria]" value="<?= str_replace(" ", "_", $i) ?>" />
                </td>
                <?php foreach ($perm['criteria'] as $value) { ?>
                    <td style="text-align: center; width:10000px">
                        <?= $value ?>
                    </td>
                <?php } ?>
                <?php if (defined('PRICING_VAR_ADMIN') && PRICING_VAR_ADMIN == 1) { ?>
                    <td style="text-align: right; width:0px">
                        <input type="hidden" name="variants[<?= $i ?>][from_db]" value="<?= (isset($set[$i]['price']) && $set[$i]['price'] > 0 ) || (isset($set[$i]['overide_price']) && $set[$i]['overide_price'] > 0 ) ? 1 : 0 ?>" />
                        <input size="10" type="text" name="variants[<?= $i ?>][price]" class="price" tabindex="<?= $tabs['price'] += 5 ?>" value="<?= $set[$i]['price'] ?>" />
                    </td>
                <?php } else { ?>
                    <td style="text-align: right; width:0px">
                        <input type="hidden" name="variants[<?= $i ?>][from_db]" value="<?= (isset($set[$i]['price']) && $set[$i]['price'] > 0 ) || (isset($set[$i]['overide_price']) && $set[$i]['overide_price'] > 0 ) ? 1 : 0 ?>" />
                        <input type="hidden" name="variants[<?= $i ?>][price]" value="<?= $set[$i]['price'] ?>" />
                        <?= (intval($set[$i]['price']) > 0 ? $set[$i]['price'] : 0.00) ?>
                    </td>
                    <?php if(defined('PRICING_VAR_FAST_SHIP') && PRICING_VAR_FAST_SHIP == 1) { ?>
                        <td style="text-align: right; width:0px">
                            <input size="3" type="text" name="variants[<?= $i ?>][fast_ship]" tabindex="<?= $tabs['price'] += 5 ?>" value="<?= $set[$i]['fast_ship'] > 0 ? $set[$i]['fast_ship'] : 0 ?>" />
                        </td>
                    <?php } ?>
                    <td style="text-align: right; width:0px">
                        <input size="10" type="text" name="variants[<?= $i ?>][overide_price]" class="price" tabindex="<?= $tabs['price'] += 5 ?>" value="<?= $set[$i]['overide_price'] ?>" />
                    </td>
                    <td style="text-align: right; width:0px">
                        <input size="3" type="text" name="variants[<?= $i ?>][qty]" tabindex="<?= $tabs['price'] += 5 ?>" value="<?= intval($set[$i]['qty']) ?>" onblur="if(this.value=='') this.value='0' ;" onfocus="if(this.value=='0') this.value='';" />
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>
</form>
