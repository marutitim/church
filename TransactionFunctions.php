<?php
function getTransactionsTotal($transaction_id)
{
    $qryTotal = "SELECT SUM(amount) as amount FROM bk_transaction WHERE parent_id = {$transaction_id}";
    $resTotal = mysql_query($qryTotal) or die(mysql_error ());    
    $rowTotal = mysql_fetch_array($resTotal);

    return $rowTotal["amount"];
}

function getTransactionsBBF($company_id,$journal_id,$trans_year,$trans_month)
{
    $trans_month = date('m', strtotime("{$trans_month} 1 {$trans_year}"));
    
    $trans_month = $trans_month - 1;

    $qryTotal = "SELECT SUM(amount) as amount FROM bk_transaction WHERE parent_id = 0 AND branch_id = {$company_id}";
    
    if ($journal_id > 0)
    {
        $qryTotal .= " AND journal_id = {$journal_id}";  
    }
    
    if ($trans_year > 0)
    {
        $qryTotal .= " AND YEAR(date) = {$trans_year}";    
    } 

    if (isset($trans_month) && $trans_month != "All")
    {
        $qryTotal .= " AND month(date) <= {$trans_month}"; 
    } 
    
    $resTotal = mysql_query($qryTotal) or die(mysql_error ().$qryTotal);    
    $rowTotal = mysql_fetch_array($resTotal);

    return $rowTotal["amount"];
}

function getTransactionsCumulative($company_id,$ledger_id,$trans_date,$currency)
{
    
    $qryTotal = "SELECT amount,date,currency FROM bk_transaction WHERE branch_id = {$company_id}";
    $qryTotal .= " AND date <= '{$trans_date}'";    
    
    foreach ($ledger_id AS $ledger) 
    {
        $ledger = getTableDetailField("bk_ledger_to_description",$ledger,"id","ledger_id");
        
        if($count == 0) $qryTotal .= " AND (";
        if($count <> 0) $qryTotal .= " OR";
        
        $qryTotal .= " trim(ledger_id) = '".trim($ledger)."'";

        $count++;
        
        if(sizeof($ledger_id) == $count) $qryTotal .= ")";
    }

    $resTotal = mysql_query($qryTotal) or die(mysql_error ().$qryTotal);    
    
    while ($rowTotal = mysql_fetch_array($resTotal)) 
    {
        $amount = $amount + getDateExchangeRateAmount($rowTotal["amount"],$rowTotal["date"],$currency,$rowTotal["currency"]);
    }
    
    return $amount;
}

function getLedgerTransactionsTotal($company_id,$ledger_id,$date,$month,$year,$currency)
{
    $qryTotal = "SELECT date,amount,currency FROM bk_transaction WHERE item_id > 0";

    if($company_id > 0) $qryTotal .= " AND bk_transaction.branch_id = {$company_id}";
    if(!empty($ledger_id)) $qryTotal .= " AND trim(bk_transaction.ledger_id) = '".trim($ledger_id)."'";

    if($month > 0) $qryTotal .= " AND MONTH(date) = {$month}";
    if($year > 0) $qryTotal .= " AND YEAR(date) = {$year}";
    if(!empty($date)) $qryTotal .= " AND {$date}";
    
    $resTotal = mysql_query($qryTotal) or die(mysql_error ());    
    set_time_limit(0);
    
    $amount = 0;
    
    while ($rowTotal = mysql_fetch_array($resTotal))
    {
        $amount = $amount + getDateExchangeRateAmount($rowTotal["amount"],$rowTotal["date"],$currency,$rowTotal["currency"]); 
    }

    return $amount;
}

function getLedgerTransactionsBalance($company_id,$ledger_id,$date,$month,$year,$currency)
{
    $qryBalance = "SELECT item_id,date,currency,amount FROM bk_transaction bt WHERE bt.parent_id = 0";

    if($company_id > 0) $qryBalance .= " AND bt.branch_id = {$company_id}";
    if(!empty($ledger_id)) $qryBalance .= " AND trim(bt.ledger_id) = '".trim($ledger_id)."'";

    if($month > 0) $qryBalance .= " AND MONTH(bt.date) = {$month}";
    if($year > 0) $qryBalance .= " AND YEAR(bt.date) = {$year}";
    if(!empty($date)) $qryBalance .= " AND {$date}";
    
    $qryBalance .= " AND bt.amount <> ( SELECT COALESCE(SUM(amount) * -1,0) as amount FROM bk_transaction WHERE parent_id = bt.item_id)";

    $resBalance = mysql_query($qryBalance) or die(mysql_error ());    
    set_time_limit(0);
    $amount = 0;
    
    while ($rowBalance = mysql_fetch_array($resBalance))
    {
        $contra_amount = getDateExchangeRateAmount(getTransactionsTotal($rowBalance["item_id"]),$rowBalance["date"],$currency,$rowBalance["currency"]);
        $trans_amount = getDateExchangeRateAmount($rowBalance["amount"],$rowBalance["date"],$currency,$rowBalance["currency"]); 
        
        $amount = $amount + ($trans_amount - $contra_amount); 
    }

    return $amount;
}

function getYearExchangeRateAmount($amount,$year,$to_currency,$from_currency)
{ 
    if(trim($to_currency) != trim($from_currency))
    {
          
        $from_currency_id = getTableTextField("xoops_shop_currencies",$from_currency,"code","currencies_id");
    
        $curr_sql = "SELECT EuroValue FROM exchange_rates WHERE year(ExchDate) = '{$year}' AND CurrencyId = {$from_currency_id} ORDER BY ExchDate DESC LIMIT 1";
        
        $curr_res = mysql_query($curr_sql) or die(mysql_error ()).$curr_sql;
        
        $curr_row = mysql_fetch_array($curr_res); 
        
        $usd_rate =  $curr_row["EuroValue"];
        
        $usd_value = $amount * $usd_rate;

        $to_currency_id = getTableTextField("xoops_shop_currencies",$to_currency,"code","currencies_id");
        
        $curr_sql2 = "SELECT EuroValue FROM exchange_rates WHERE year(ExchDate) = '{$year}' AND CurrencyId = {$to_currency_id} ORDER BY ExchDate DESC LIMIT 1";
        
        $curr_res2 = mysql_query($curr_sql2) or die(mysql_error ()).$curr_sql2;
        
        $curr_row2 = mysql_fetch_array($curr_res2); 
        
        $euro_rate2 =  $curr_row2["EuroValue"];

        $new_value = $usd_value / $euro_rate2;
        
        return $new_value;
    }
    else
    {
        return $amount;
    }  
}

function getMonthExchangeRateAmount($amount,$month,$to_currency,$from_currency)
{
    
    if(trim($to_currency) != trim($from_currency))
    {
        $from_currency_id = getTableTextField("xoops_shop_currencies",$from_currency,"code","currencies_id");
    
        $curr_sql = "SELECT EuroValue FROM exchange_rates WHERE month(ExchDate) = {$month} AND CurrencyId = {$from_currency_id} ORDER BY ExchDate DESC LIMIT 1";
        
        $curr_res = mysql_query($curr_sql) or die(mysql_error ()).$curr_sql;
        
        $curr_row = mysql_fetch_array($curr_res); 
        
        $euro_rate =  $curr_row["EuroValue"];
        
        if($euro_rate > 1)
        {
            $euro_value = $amount / $euro_rate;
        }
        else
        {
            $euro_value = $amount * $euro_rate;
        }

        $to_currency_id = getTableTextField("xoops_shop_currencies",$to_currency,"code","currencies_id");
        
        $curr_sql2 = "SELECT EuroValue FROM exchange_rates WHERE month(ExchDate) = {$month} AND CurrencyId = {$to_currency_id} ORDER BY ExchDate DESC LIMIT 1";
        
        $curr_res2 = mysql_query($curr_sql2) or die(mysql_error ()).$curr_sql2;
        
        $curr_row2 = mysql_fetch_array($curr_res2); 
        
        $euro_rate2 =  $curr_row2["EuroValue"];
        
        if($euro_rate2 > 1)
        {
            $new_value = $euro_value * $euro_rate2;
        }
        else
        {
            $new_value = $euro_value / $euro_rate2;
        }
        
        return $new_value;
    }
    else
    {
        return $amount;
    }  
}

function getDateExchangeRateAmount($amount,$date,$to_currency,$from_currency)
{    
    $year = date("Y", strtotime($date));
    
    if(trim($to_currency) != trim($from_currency))
    {
        $from_currency_id = getTableTextField("xoops_shop_currencies",$from_currency,"code","currencies_id");
        
        $curr_sql = "SELECT EuroValue FROM exchange_rates WHERE ExchDate = '{$date}' AND CurrencyId = {$from_currency_id} LIMIT 1";
        
        $curr_res = mysql_query($curr_sql) or die(mysql_error ()).$curr_sql;
        
        $curr_row = mysql_fetch_array($curr_res); 
        
        $usd_rate =  $curr_row["EuroValue"];
    
        if(empty($usd_rate))
        {
            $new_value = getYearExchangeRateAmount($amount,$year,$to_currency,$from_currency);
        }
        else
        {
            $usd_value = $amount * $usd_rate;
            
            // Second the USD Value to Selected Currency
            
            $to_currency_id = getTableTextField("xoops_shop_currencies",$to_currency,"code","currencies_id");
            
            $curr_sql2 = "SELECT EuroValue FROM exchange_rates WHERE ExchDate = '{$date}' AND CurrencyId = {$to_currency_id} LIMIT 1";
            
            $curr_res2 = mysql_query($curr_sql2) or die(mysql_error ()).$curr_sql2;
            
            $curr_row2 = mysql_fetch_array($curr_res2); 
            
            $usd_rate2 =  $curr_row2["EuroValue"];
            
            $new_value = $usd_value / $usd_rate2;
        }
        
        return $new_value;
        
    }
    else
    {
        return $amount;
    }  
}

function getLedgerParentTransactionsTotal($company_id,$language_id,$ledger_id,$month,$year,$currency)
{    
    $qryParentLedgers = "SELECT bk_ledger.ledger_id as ledgerid
    FROM bk_ledger,bk_ledger_to_description,bk_company_to_ledger 
    WHERE trim(bk_ledger.ledger_id) = trim(bk_ledger_to_description.ledger_id) 
    AND trim(bk_ledger.ledger_id)  = trim(bk_company_to_ledger.ledger_id) 
    AND parent_id = '".trim($ledger_id)."'";
    $qryParentLedgers.= " AND company_id = {$company_id} AND active = 1";
    $qryParentLedgers.= " AND language_id = {$language_id}";
    $qryParentLedgers.= " ORDER BY trim(ledgerid) ASC";
        
    $resParentLedgers = mysql_query($qryParentLedgers) or die(mysql_error ().$qryParentLedgers);  
    
    while ($rowParentLedgers = mysql_fetch_array($resParentLedgers))
    {
    
        $qryParentTotal = "SELECT date,amount,currency FROM bk_transaction WHERE item_id > 0";

        if($company_id > 0) $qryParentTotal .= " AND branch_id = {$company_id}";
        if(!empty($ledger_id)) $qryParentTotal .= " AND trim(ledger_id) = '".trim($rowParentLedgers["ledgerid"])."'";
        
        if($_SESSION['date_type'] == "norm")
        {
            if($month > 0) $qryParentTotal .= " AND MONTH(date) = {$month}";
            if($year > 0) $qryParentTotal .= " AND YEAR(date) = {$year}";
        }
        else
        {
            if($month > 0) $qryParentTotal .= " AND MONTH(booking_date) = {$month}";
            if($year > 0) $qryParentTotal .= " AND YEAR(booking_date) = {$year}";
        }
        
        $resParentTotal = mysql_query($qryParentTotal) or die(mysql_error ().$qryParentTotal);    
        
        while ($rowParentTotal = mysql_fetch_array($resParentTotal))
        {
            $total = $total +  getDateExchangeRateAmount($rowParentTotal["amount"],$rowParentTotal["date"],$currency,$rowParentTotal["currency"]);;
        }

        $ledger_total = getLedgerParentTransactionsTotal($company_id,$language_id,$rowParentLedgers["ledgerid"],$month,$year,$currency);
        
        $total = $total + $ledger_total;
        
    }

    return $total;
}

function getChildLedgerXml2($company_id,$language_id,$year_text,$parentid,$currency)
{
    $qryLedgers = "SELECT bk_ledger_to_description.id as grid_id,bk_ledger.ledger_id as ledgerid,description,parent_id,active 
    FROM bk_ledger,bk_ledger_to_description,bk_company_to_ledger 
    WHERE trim(bk_ledger.ledger_id) = trim(bk_ledger_to_description.ledger_id) 
    AND trim(bk_ledger.ledger_id)  = trim(bk_company_to_ledger.ledger_id) 
    AND bk_ledger.parent_id <> 0";
    
    $qryLedgers.= " AND bk_ledger.parent_id = '".trim($parentid)."'";
    
    $qryLedgers.= " AND company_id = {$company_id} AND active = 1";
    $qryLedgers.= " AND language_id = {$language_id}";
    $qryLedgers.= " ORDER BY trim(ledgerid) ASC";

    $resLedgers = mysql_query($qryLedgers) or die(mysql_error());
    set_time_limit(0);
    
    while ($rowLedgers = mysql_fetch_array($resLedgers))
    {      
        $child_count = getTableRowCount("bk_ledger",$rowLedgers["ledgerid"],"parent_id");
        
        if($child_count == 0)       
        {
            echo "<row id = '{$rowLedgers["grid_id"]}'>";
            echo "<cell> {$rowLedgers["ledgerid"]} </cell>";
            echo "<cell> ".xmlEscape($rowLedgers["description"])."</cell>";
            
            for ($i=1; $i<=12; $i++)
            {
                echo "<cell> ".getLedgerTransactionsTotal($company_id,$rowLedgers["ledgerid"],$date,$i,$year_text,$currency)."</cell>";
            }
            echo "<cell> ".getLedgerTransactionsTotal($company_id,$rowLedgers["ledgerid"],$date,0,$year_text,$currency)."</cell>";
            
            echo "</row>";  
        } 
        else
        {
            echo "<row id = '-{$rowLedgers["grid_id"]}' style='color:#000155;'>
            <cell> {$rowLedgers["ledgerid"]} </cell>
            <cell> ".xmlEscape($rowLedgers["description"])." </cell>
            </row>";  
                      
            getChildLedgerXml2($company_id,$language_id,$year_text,$rowLedgers["ledgerid"],$currency);
            
            echo "<row id = '-{$rowLedgers["ledgerid"]}' style='color:#000155;'>";
            echo "<cell> </cell>";
            echo "<cell> </cell>";
            
            for ($i=1; $i<=12; $i++)
            {
                echo "<cell> ". getLedgerParentTransactionsTotal($company_id,$language_id,$rowLedgers["ledgerid"],$i,$year_text,$currency) ." </cell>";
            }
            echo "<cell> ". getLedgerParentTransactionsTotal($company_id,$language_id,$rowLedgers["ledgerid"],0,$year_text,$currency) ."</cell>";
            
            echo "</row>";
        }      
    }

}

function getChildLedgerXml3($company_id,$parent_id,$language_id,$currency,$start_date,$end_date,$trans_year,$date)
{
    $qryLedgers = "SELECT 
    bk_ledger_to_description.id as grid_id,
    bk_ledger.ledger_id as ledgerid,
    description,
    parent_id,
    bk_company_to_ledger.currency as company_curr,
    active 
    FROM bk_ledger,bk_ledger_to_description,bk_company_to_ledger 
    WHERE trim(bk_ledger.ledger_id) = trim(bk_ledger_to_description.ledger_id)";
    
    $qryLedgers.=" AND trim(bk_ledger.ledger_id)  = trim(bk_company_to_ledger.ledger_id)";
    $qryLedgers.=" AND language_id = {$language_id}";
    $qryLedgers.=" AND bk_ledger.parent_id = '".trim($parent_id)."'";
    $qryLedgers.=" AND company_id = {$company_id} AND active = 1";
    $qryLedgers.=" ORDER BY trim(bk_ledger.ledger_id) ASC";
    
    $resLedgers = mysql_query($qryLedgers) or die(mysql_error().$qryLedgers);
    set_time_limit(0);
    
    while ($rowLedgers = mysql_fetch_array($resLedgers))
    {  
        echo "<row id = '{$rowLedgers["grid_id"]}'>";
            echo "<cell> </cell>";
            echo "<cell > {$rowLedgers["ledgerid"]} </cell>";
            echo "<cell> ".xmlEscape($rowLedgers["description"])."</cell>";
            
            $cumulative = getLedgerTransactionsTotal($company_id,$rowLedgers["ledgerid"]," date BETWEEN '{$start_date}' AND '{$end_date}'",0,$trans_year,$currency);
            $cumulative = str_replace(',', '', $cumulative); 
            
            $balance = getLedgerTransactionsBalance($company_id,$rowLedgers["ledgerid"]," date BETWEEN '{$start_date}' AND '{$end_date}'",0,$trans_year,$currency);
            $balance = str_replace(',', '', $balance); 
            
            $today = getLedgerTransactionsTotal($company_id,$rowLedgers["ledgerid"]," date = '{$date}'",0,$trans_year,$currency);
            $today = str_replace(',', '', $today);
                
            echo "<cell> ".$cumulative."</cell>";
            echo "<cell> ".$balance."</cell>";
            echo "<cell> ".$today."</cell>";
            echo "<cell> {$rowLedgers["company_curr"]} </cell>";    
                    
            getChildLedgerXml3($company_id,$rowLedgers["ledgerid"],$language_id,$currency,$trans_year,$start_date,$end_date,$date);
            
        echo "</row>";  
    }
}

function getTransactionsRefTotal($reference_no)
{
  $qryTotal = "SELECT SUM(amount) as amount FROM bk_transaction WHERE trim(reference_no) = '".trim($reference_no)."'";
  $resTotal = mysql_query($qryTotal) or die(mysql_error ());    
  $rowTotal = mysql_fetch_array($resTotal);
  
  return $rowTotal["amount"];
}

function getFormatAmount($myAmount)
{
    if (!empty($myAmount) || isset($myAmount) || $myAmount == "")
    {
        $newAmount = trim(str_replace(",",".",$myAmount));
    }
    
    return $newAmount;
}

function getLedgerName($ledger_id,$language_id)
{
    if(isset($ledger_id))
    {
        if($ledger_id == 0)
        {
           return "All"; 
        }
        else
        {
            $qryLedger = "SELECT description FROM bk_ledger_to_description WHERE trim(ledger_id) = '".trim($ledger_id)."' AND language_id = {$language_id}";
            $resLedger = mysql_query($qryLedger) or die(mysql_error ());    
            $rowLedger = mysql_fetch_array($resLedger);
            
            return $rowLedger["description"];
        }
    }
    else
    {
        return "";
    }
    
}

function getLedgerID($desc_id,$language_id)
{
    $qryLedger = "SELECT ledger_id FROM bk_ledger_to_description WHERE id = {$desc_id} AND language_id = {$language_id}";
    $resLedger = mysql_query($qryLedger) or die(mysql_error ());    
    $rowLedger = mysql_fetch_array($resLedger);
    
    return $rowLedger["ledger_id"];
}

function getChildLedgerXml($parentid,$language_id,$company_id,$tree_level,$show_all)
{

    $tree_level++;
    if ($tree_level <> 3) $curr_type = "ro";
        
    $qryLedgers = "SELECT bk_ledger_to_description.id as grid_id,bk_ledger.ledger_id as ledgerid,description,parent_id,active,bk_company_to_ledger.currency as company_curr 
    FROM bk_ledger,bk_ledger_to_description,bk_company_to_ledger 
    WHERE trim(bk_ledger.ledger_id) = trim(bk_ledger_to_description.ledger_id) 
    AND trim(bk_ledger.ledger_id)  = trim(bk_company_to_ledger.ledger_id) 
    AND language_id = {$language_id}
    AND bk_ledger.parent_id = '".trim($parentid)."'";
         
    if ($show_all <> 1) $qryLedgers.= " AND company_id = {$company_id} AND active = 1";
    
    $qryLedgers.= " ORDER BY trim(ledgerid) ASC";

    $resLedgers = mysql_query($qryLedgers) or die(mysql_error().$qryLedgers);
    while ($rowLedgers = mysql_fetch_array($resLedgers))
    {
        echo "<row id = '{$rowLedgers["grid_id"]}'>";
            echo "<cell> {$rowLedgers["ledgerid"]} </cell>";
            echo "<cell> ".xmlEscape($rowLedgers["description"])."</cell>";
            echo "<cell> ".getLedgerActive($rowLedgers["ledgerid"],$company_id)."</cell>";
            getChildLedgerXml($rowLedgers["ledgerid"],$language_id,$company_id,$tree_level,$show_all);
        echo "</row>";  
    }
}

function getLedgerExist($ledger_id,$company_id)
{
    $qryActive = "SELECT count(*) as active FROM bk_company_to_ledger WHERE trim(ledger_id) = '".trim($ledger_id)."' AND company_id = {$company_id}";
    $resActive = mysql_query($qryActive) or die(mysql_error ().$qryActive);    
    $rowActive = mysql_fetch_array($resActive);
    
    return $rowActive["active"];
}

function getLedgerActive($ledger_id,$company_id)
{
    $qryActive = "SELECT active FROM bk_company_to_ledger WHERE trim(ledger_id) = '".trim($ledger_id)."' AND company_id = {$company_id}";
    $resActive = mysql_query($qryActive) or die(mysql_error ().$qryActive);    
    $rowActive = mysql_fetch_array($resActive);
    
    return $rowActive["active"];
}

function getLedgerExistMessage($ledger_id,$company_id)
{
    $chk_sql = "SELECT count(*) as ledger_count FROM bk_ledger WHERE trim(ledger_id) = '".trim($ledger_id)."'";
    $chk_res = mysql_query($chk_sql) or die(mysql_error ());    
    $chk_row = mysql_fetch_array($chk_res);

    $ledger_count =  $chk_row["ledger_count"];
    
    if($ledger_count > 0)
    {
        $data['data'] = array('success' => true,'message' => "Ledger ID {$ledger_id} Already Exists!");
    }
    else
    {
        $data['data'] = array('success' => false);
    }
    return $data;
}

function checkTableRecordExist($table,$id,$idcol)
{
    $chk_sql = "SELECT count(*) as table_count FROM {$table} WHERE {$idcol} = {$id}";
    $chk_res = mysql_query($chk_sql) or die(mysql_error ()).$chk_sql;    
    $chk_row = mysql_fetch_array($chk_res);

    $table_count =  $chk_row["table_count"];
    
    return $table_count;
}

function checkTextRecordExist($table,$id,$idcol)
{
    $chk_sql = "SELECT count(*) as table_count FROM {$table} WHERE trim({$idcol}) = '".trim($id)."'";
    $chk_res = mysql_query($chk_sql) or die(mysql_error ()).$chk_sql;    
    $chk_row = mysql_fetch_array($chk_res);

    $table_count =  $chk_row["table_count"];
    
    return $table_count;
}

function getExchangeRate($currency,$date)
{
    
    $currency_id = getTableTextField("xoops_shop_currencies",$currency,"code","currencies_id");
    
    $curr_sql = "SELECT EuroValue FROM exchange_rates WHERE ExchDate = '{$date}' AND CurrencyId = {$currency_id}";
    $curr_res = mysql_query($curr_sql) or die(mysql_error ()).$curr_sql;
    
    $curr_row = mysql_fetch_array($curr_res); 
    
    $euro_value =  $curr_row["EuroValue"];
    
    if(!empty($euro_value))
    {
        return $euro_value;
    }
    else
    {
        return 0.00;
    }
}

function getExchangeRateAmount($amount,$rate)
{
    if($rate > 1)
    {
        $ex_amount = $amount / $rate;
    }
    else
    {
        $ex_amount = $amount * $rate;
    }
    
    return $ex_amount;
}

function getCreditorsStatus($status_id)
{    
    switch ($status_id)
    {
        case 1: $status_name = "Please Select"; break;
        case 4201: $status_name = "To be purchased"; break;
        case 4202: $status_name = "Purchased"; break;
        case 4205: $status_name = "Cancelled"; break;
        case 4209: $status_name = "Delivered"; break;
    }
    return $status_name;
}

function getEmployeeName($contact_id)
{
    $contact_data = getItemByID("relation_contact",$contact_id,"contact_id");
    $contact_fname = $contact_data["contact_firstname"];
    $contact_lname = $contact_data["contact_lastname"];
    
    if(!empty($contact_fname) || !empty($contact_lname))
    {
        return $contact_fname." ".$contact_lname;
    }
    else
    {
        return "Select Employee";
    }
}

function getSupervisorName($eid)
{
    $employee_data = getItemByID("employee",$eid,"EID");
    $employee_name = $employee_data["Naam"];
    
    if(!empty($employee_name))
    {
        return $employee_name;
    }
    else
    {
        return "Select Supervisor";
    }
}

function getRelationName($relation_id)
{
    mysql_select_db("nts_site");
    
    $relation_data = getItemByID("relation",$relation_id,"relation_id");
    $relation_name = $relation_data["relation_company"];
    
    if(!empty($relation_name))
    {
        return $relation_name;
    }
    else
    {
        return "";
    }
}

function geContactName($contact_id,$relation_id)
{
    $contact_data = getItemByIDCondition("relation_contact",$contact_id,"contact_id"," AND relation_id = {$relation_id}");
    $contact_fname = $contact_data["contact_firstname"];
    $contact_lname = $contact_data["contact_lastname"];
    
    if(!empty($contact_fname) || !empty($contact_lname))
    {
        return $contact_fname." ".$contact_lname;
    }
    else
    {
        return "Select Contact";
    }
}

function getItemName($id,$sort_id,$return_default)
{
 $language_id = $_COOKIE['lang_id'];
 if($language_id == null){$language_id =4;}
    if($sort_id == 48)
    {
        $cond = " AND (Sort_ID = {$sort_id} || Sort_ID = 2)";
    } 
    else
    {
        $cond = " AND Sort_ID = {$sort_id}";
    }
    
    $data = getItemByIDCondition("lookuptable",$id,"Item_Value"," AND Language_ID = '$language_id'  {$cond}");
    $name = $data["Item_name"];
    
  
    
    if($id != "")//!empty($id)
    {
        return $name;
    }
    else
    {
        return $return_default;
    }
}

function getReferenceID($branch_id,$process_id)
{
     //branch_id: 1 value , process_id : last two values , date : last two values ,new value from process tables as the last six values
             
    $date = date("y");
    $sql = "SELECT New_Value FROM process_values WHERE Branch_ID = {$branch_id} AND Process_ID = {$process_id} LIMIT 1";
    $res  = mysql_query($sql) or die (mysql_error());
    
    if ($row = mysql_fetch_array($res)) 
    {
            $new_value    = $row['New_Value'];
            
            $branch_id    = sprintf("%01s", $branch_id);
            $process_id   = substr($process_id, -2);
            $new_value    = sprintf("%06s", $new_value);
            
            $reference_id = $branch_id . "" . $process_id . "" . $date . "" . $new_value;
    }
    
    return $reference_id; 
}

function getProcessNewValue($branch_id,$process_id)
{
    $sql = "SELECT New_Value FROM process_values WHERE Branch_ID = {$branch_id} AND Process_ID = {$process_id} LIMIT 1";
    $res  = mysql_query($sql) or die (mysql_error());
    
    if ($row = mysql_fetch_array($res)) 
    {
            $new_value    = $row['New_Value'];
    }
    
    return $new_value; 
}

function getInvoiceDefaultText($relation_id)
{
    $sql = "SELECT DefaultText FROM relation_grootboekrek WHERE Relation_ID = {$relation_id} AND Grootboekrek_ID_Parent = 1300 AND Status_ID = 5 LIMIT 1";
    $res  = mysql_query($sql) or die (mysql_error());
    
    if ($row = mysql_fetch_array($res)) 
    {
            $default_text    = $row['DefaultText'];
    }
    
    return $default_text; 
}

function getInvoiceDefaultTextCount($relation_id)
{
    $sql = "SELECT count(*) as invoice_count FROM relation_grootboekrek WHERE Relation_ID = {$relation_id} AND Grootboekrek_ID_Parent = 1300 AND Status_ID = 5 LIMIT 1";
    $res  = mysql_query($sql) or die (mysql_error());
    
    if ($row = mysql_fetch_array($res)) 
    {
            $invoice_count    = $row['invoice_count'];
    }
    
    return $invoice_count; 
}

function getInvoiceDefaultTextLedger($relation_id)
{
    $sql = "SELECT ledger_id FROM relation_grootboekrek WHERE Relation_ID = {$relation_id} AND Grootboekrek_ID_Parent = 1300 AND Status_ID = 5 LIMIT 1";
    $res  = mysql_query($sql) or die (mysql_error());
    
    if ($row = mysql_fetch_array($res)) 
    {
            $ledger_id    = $row['ledger_id'];
    }
    
    return $ledger_id; 
}

function getEventItemsTotal($event_id)
{
    $sql = "SELECT et.Price,et.Q1,et.Q4 FROM event_item et,event e WHERE e.ReferenceId = et.EventID AND e.ReferenceID = {$event_id} AND e.ProcessId = 5 GROUP BY et.eventitemid";
    $res  = mysql_query($sql) or die (mysql_error());
    
    $total_value = 0;
    
    while ($row = mysql_fetch_array($res)) 
    {
        $total = ($row["Price"] * $row["Q1"])+ ($row["Q4"]*$row["Q1"]);

        $total_value = $total_value + $total;
    }   
    return $total_value;
}

function getOtherOptTax($event_id,$EvtTax)
{
   $sql = "SELECT Shipping_Value,Order_Value,Discount_Value,EvtTax FROM event WHERE ReferenceID = {$event_id}";
   $res  = mysql_query($sql) or die (mysql_error());
   $row = mysql_fetch_array($res);
   //$EvtTax = $row['EvtTax'];
   $Shipping_Value = $row['Shipping_Value'] * ($EvtTax); 
   $Order_Value = $row['Order_Value'] * ( $EvtTax);
   $Discount_Value = $row['Discount_Value'] * ($EvtTax);
   $totTax = ($Shipping_Value + $Order_Value) - $Discount_Value;    
   return $totTax;
}

/*
function getEventItemsVAT($event_id)
{
    $sql = "SELECT et.Price,et.Q1,et.Q4,et.Tax FROM event_item et, event e WHERE e.ReferenceID = et.EventID AND e.ReferenceID = {$event_id} AND e.ProcessId = 5 GROUP BY et.eventitemid";
    $res  = mysql_query($sql) or die (mysql_error());
    
    $tax = 0;
    
    while ($row = mysql_fetch_array($res)) 
    {
        $sub_total = $row["Price"] * $row["Q1"] + ($row["Q1"] * $row["Q4"]);
        $vat = $sub_total * $row["Tax"];
        
        $vat_total = $vat_total + $vat;
    } 
    $vat_total = $vat_total + getOtherOptTax($event_id); 
    return $vat_total;
}
*/

function getEventItemsVAT($event_id)
{
    $sql = "
        SELECT ei.Q1,FORMAT((ei.Q4),2)as Q4,ei.Discount,

        FORMAT((IF(ei.PriceCalc != '', ei.PriceCalc, ei.Price)-(IF(ei.Discount != '', (FORMAT(ei.Discount/100 * ei.PriceCalc,2)) ,0))), 2) as Price,

        ei.Tax as TaxDecimal,
        
        concat(FORMAT((ei.Tax*100),0),'%')as Tax,

        FORMAT((((IF(ei.PriceCalc != '', ei.PriceCalc, ei.Price)) * ei.Q1) + ((ei.Q4*ei.Q1)  - IF(ei.Discount != '',(ei.Discount/100 * ei.PriceCalc * ei.Q1),0)    )),2) as Total,

        
        FORMAT((((IF(ei.PriceCalc != '', ei.PriceCalc, ei.Price)) * ei.Q1) + ((ei.Q4*ei.Q1)  - IF(ei.Discount != '',(ei.Discount/100 * ei.PriceCalc * ei.Q1),0)    )) * ei.Tax,2) as SumTax,


        IF(ei.Discount != '', (FORMAT(ei.Discount/100 * ei.PriceCalc,2)) ,0) as discountCalc, 

        pv.VendorSKU,pv.VendorProductName,FORMAT(ei.PriceCalc, 2) as PriceCalc

        from
        event_Item ei
        LEFT JOIN product_to_vendorsku pv ON  pv.ProductId = ei.ProductID
        
        LEFT JOIN lookuptable l ON  l.Item_Value = ei.WarrantyID and l.Language_Id = 1 and sort_id = 47 
        
        where 
        ei.EventID = '".$event_id."'
        group by ei.EventItemID ORDER BY SortID ASC         
            "; //- IF(ei.Discount != '',(ei.Discount/100 * ei.PriceCalc * ei.Q1),0)
    $res  = mysql_query($sql) or die (mysql_error());
    
    $total_value = 0;
    
    while ($row = mysql_fetch_array($res)) 
    {
        $total = str_replace(",", "", $row["SumTax"]);
        $tax = $row["TaxDecimal"];
        $total_value = $total_value + $total;
    }
     $vat_total = $total_value + getOtherOptTax($event_id,$tax); 
    return $vat_total;
}




function getEventItemsMargin($event_id)
{
    $sql = "SELECT sum(Margin) as M FROM event_item WHERE EventID = {$event_id}";
    $res  = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($res);  
    return $row['M'];
}
function getEventItemsPurprice($event_id)
{
    $sql = "SELECT sum(PriceInt) as Pur FROM event_item WHERE EventID = {$event_id}";
    $res  = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($res);  
    return $row['Pur'];
}


function getEventItemsTotalVAT($event_id)
{
    $sql = "SELECT Price,Q1,Tax FROM event_item WHERE EventID = {$event_id}";
    $res  = mysql_query($sql) or die (mysql_error());
    
    $tax = 0;
    
    while ($row = mysql_fetch_array($res)) 
    {
        $sub_total = $row["Price"] * $row["Q1"];
        $vat = $sub_total * ($row["Tax"] + 1);
        
        $vat_total = $vat_total + $vat;
    }
    
    return $vat_total;
}

function getTransactionsRef($journal_id,$trans_date)
{
    $year = date("Y", strtotime($trans_date));
    
    $sql = "SELECT max(reference_no) as ref_no  FROM bk_transaction WHERE journal_id = {$journal_id}  AND parent_id = 0 AND YEAR(date) = {$year}  ORDER BY date,sort_id ASC";
    $res  = mysql_query($sql) or die (mysql_error());
    $row = mysql_fetch_array($res);
    
    $year = date("y", strtotime($trans_date));
    $ref_no = substr($row["ref_no"],-5) + 1;
    
    
    if (strlen($ref_no) == 1) 
    {
        $no = "0000".$ref_no."";
    }
    else if(strlen($ref_no) == 2)
    {
        $no = "000".$ref_no."";
    }
    else if(strlen($ref_no) == 3)
    {
        $no = "00".$ref_no."";
    }
    else if(strlen($ref_no) == 4)
    {
        $no = "0".$ref_no."";
    }
    else
    {
        $no = $ref_no;
    }
        
    return $journal_id.$year.$no;
}

function getChildTransactionXml($transaction_id,$parent_id,$ledger_id,$ledger_year,$company_id,$parent_journal,$cumulative)
{
    
    if(!empty($parent_id))
    {
        $qryTransaction = "SELECT 
        item_id,
        sort_id,
        date,
        reference_no,
        bk_transaction.ledger_id as t_lid,
        relation_id,
        bk_journal.ledger_id as j_lid,
        journal_name,
        bk_transaction.description as t_description,
        eid,
        relation,
        amount,
        parent_id,
        period,
        bk_transaction.currency as trans_curr
        FROM bk_transaction,bk_journal 
        WHERE bk_transaction.journal_id = bk_journal.journal_id";
        if($ledger_year > 0) $qryTransaction .= " AND YEAR(date) = {$ledger_year}";
        $qryTransaction .= " AND bk_transaction.branch_id = {$company_id}";
        $qryTransaction .= " AND bk_transaction.journal_id <> {$parent_journal}";
        $qryTransaction.= "  AND trim(reference_no) = '".trim($parent_id)."'";

        $qryTransaction.= " ORDER BY date ASC";
        
        $resTransaction = mysql_query($qryTransaction) or die(mysql_error().$qryTransaction);
        
        while ($rowTransaction = mysql_fetch_array($resTransaction))
        {
            $balance_amount = 0;
            
            echo "<row id = '{$rowTransaction["item_id"]}'>";
                echo "<cell> {$rowTransaction["item_id"]} </cell>";
                echo "<cell> {$rowTransaction["sort_id"]} </cell>";
                echo "<cell> {$rowTransaction["date"]} </cell>";
                echo "<cell> {$rowTransaction["reference_no"]} </cell>";
                echo "<cell> {$rowTransaction["relation_id"]} </cell>";
                
                if($rowTransaction["relation"] != "")
                {
                    if($rowTransaction["relation_id"] > 0)
                    {
                        $relation_name = getTableDetailField("relation",$rowTransaction["relation_id"],"relation_id","relation_company");
                    }
                     else
                    {
                        $relation_name = "";
                    }
                }
                else
                {
                    $relation_name = $rowTransaction["relation"];
                }
                
                echo "<cell> ".xmlEscape($relation_name)." </cell>";
                echo "<cell> {$rowTransaction["journal_name"]} </cell>";
                echo "<cell> {$rowTransaction["t_description"]} </cell>";
                echo "<cell> {$rowTransaction["eid"]} </cell>";
                
                if($rowTransaction["amount"] > 0) $sign = "+"; else $sign = "";
                echo "<cell> ".$sign."{$rowTransaction["amount"]} </cell>";
                
                $cumulative = $cumulative + $rowTransaction["amount"];
                
                echo "<cell> ".number_format($cumulative,2)." </cell>";     
                         
                echo "<cell> {$rowTransaction["period"]} </cell>"; 
                echo "<cell> {$rowTransaction["trans_curr"]} </cell>";              
            echo "</row>";  
        }
    } 
}

function getChildCostXml($parent_id,$company_id)
{
    $qryCost = "SELECT * FROM cost_heading WHERE cost_heading_parent_id = {$parent_id} AND cost_heading_branch_id = {$company_id}";
    $resCost = mysql_query($qryCost) or die(mysql_error().$qryCost);
    
    while ($rowCost = mysql_fetch_array($resCost))
    {
        echo "<row id = '{$rowCost["cost_heading_id"]}'>";
            echo "<cell>  </cell>";
            echo "<cell> {$rowCost["cost_heading_id"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_code"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_name"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_start_date"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_end_date"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_assigned_to"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_amount"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_currency"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_info"]} </cell>";
            getChildCostXml($rowCost["cost_heading_id"],$company_id);
        echo "</row>";  
    }
}

function getChildCostXml2($parent_id,$company_id,$year_text)
{
    $qryCost = "SELECT * FROM cost_heading WHERE cost_heading_parent_id = {$parent_id} AND cost_heading_branch_id = {$company_id}";
    $resCost = mysql_query($qryCost) or die(mysql_error().$qryCost);
    
    while ($rowCost = mysql_fetch_array($resCost))
    {
        echo "<row id = '{$rowCost["cost_heading_id"]}'>";
            echo "<cell> {$rowCost["cost_heading_code"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_name"]} </cell>";
            
            for ($i=1; $i<=12; $i++)
            {
                echo "<cell> ".getCostHeadingTransactionTotal($rowCost["cost_heading_id"],$i,$year_text)."</cell>";
            }
            
            echo "<cell> ".getCostHeadingTransactionTotal($rowCost["cost_heading_id"],0,$year_text)."</cell>";
            
            getChildCostXml2($rowCost["cost_heading_id"],$company_id,$year_text);
            
        echo "</row>";  
    }
}

function getChildCostXml3($parent_id,$company_id)
{
    $qryCost = "SELECT * FROM cost_heading WHERE cost_heading_parent_id = {$parent_id} AND cost_heading_branch_id = {$company_id}";
    $resCost = mysql_query($qryCost) or die(mysql_error().$qryCost);
    
    while ($rowCost = mysql_fetch_array($resCost))
    {
        echo "<row id = '{$rowCost["cost_heading_id"]}'>";
            echo "<cell> {$rowCost["cost_heading_code"]} </cell>";
            echo "<cell> {$rowCost["cost_heading_name"]} </cell>";
            
            for ($i=2011; $i<=2020; $i++)
            {
                echo "<cell> ".getCostHeadingTransactionTotal($rowCost["cost_heading_id"],0,$i)."</cell>";
            }
            
            echo "<cell> ".getCostHeadingTransactionTotal($rowCost["cost_heading_id"],0,0)."</cell>";
            
            getChildCostXml3($rowCost["cost_heading_id"],$company_id);
            
        echo "</row>";  
    }
}

function getCostHeadingTransactionTotal($costHeading_id,$month,$year)
{
    $qryCostTotal = "SELECT  sum(amount)amount FROM bk_transaction WHERE item_id > 0";
    
    if ($costHeading_id > 0) $qryCostTotal .= " AND cost_heading = {$costHeading_id}";
    if ($year > 0) $qryCostTotal .= " AND YEAR(date) = {$year}";
    if ($month > 0) $qryCostTotal .= " AND MONTH(date) = {$month}";
    
    $resCostTotal = mysql_query($qryCostTotal) or die(mysql_error().$qryCostTotal);
    
    $rowCostTotal = mysql_fetch_array($resCostTotal);
    
    $amount = $rowCostTotal["amount"];
    
    return number_format($amount,2);
}

function getErrorMessages($transaction_id)
{
    $trans_data = getItemByID("bk_transaction",$transaction_id,"item_id");
    $ledger_id = $trans_data["ledger_id"];
    $book_date = $trans_data["booking_date"];
    $currency = $trans_data["currency"];
    $date = $trans_data["date"];
    $relation_id = $trans_data["relation_id"];
    $company_id = $trans_data["branch_id"];
    $journal_id = $trans_data["journal_id"];
    $amount = $trans_data["amount"];
    $parent_id = $trans_data["parent_id"];

    $balance = $amount + getTransactionsTotal($transaction_id);
    $count = getTableRowCountCondition("bk_transaction",$transaction_id,"parent_id","AND journal_id = {$journal_id}");
    $ledger_desc = getTableTextField("bk_ledger_to_description",$ledger_id,"ledger_id","description");
    $ledger_xid = getTableTextField("bk_ledger",$ledger_id,"ledger_id","id");
    $relation_name = getTableDetailField("relation",$relation_id,"relation_id","relation_company");
    $journal_name = getTableDetailField("bk_journal",$journal_id,"journal_id","journal_name");
    $ledger_active = intval(getTableTextFieldCondition("bk_company_to_ledger",$ledger_id,"ledger_id","id"," AND company_id = {$company_id} AND active = 1"));
    $ledger_kids = getTableTextField("bk_ledger",$ledger_id,"parent_id","id");
    $journal_ledger = getTableDetailField("bk_journal",$journal_id,"journal_id","ledger_id");
    $standard_contra = getTableTextField("bk_ledger",$journal_ledger,"ledger_id","standard_contra");
    $parent_date = getTableDetailField("bk_transaction",$parent_id,"item_id","date");
    
    $qryErrors = "SELECT error_message FROM bk_errors";
    $resErrors = mysql_query($qryErrors) or die(mysql_error().$qryErrors);
    while ($rowErrors[] = mysql_fetch_array($resErrors)) {}
    
    $errors = "";
    
    
    if($parent_id == 0)
    {
        //1.balance
        if($balance != 0)
        {
            $errors .= $rowErrors["0"]["error_message"];
        }
        
        //2.trans
        if($count == 0)
        {
            $errors .= $rowErrors["1"]["error_message"];
        }   
    }    
    
    //3.booking_date
    if($book_date == "")
    {
        $errors .= $rowErrors["2"]["error_message"];
    }
    
    //4.ledger_id
    if($ledger_id == "")
    {
        $errors .= $rowErrors["3"]["error_message"];
    }
    
    //5.ledger_info
    if($ledger_desc == "")
    {
        $errors .= $rowErrors["4"]["error_message"];
    }
    
    //6.currency
    if($currency == "")
    {
        $errors .= $rowErrors["5"]["error_message"];
    }
    
    //7.date
    if($date == "")
    {
        $errors .= $rowErrors["6"]["error_message"];
    }
    
    //8.relation
    if(!isset($relation_name))
    {
        if($relation_id != 0)
        {
            $errors .= $rowErrors["7"]["error_message"];
        }
    }
    
    //9.Ledger ID
    if($ledger_xid == "")
    {
        $errors .= $rowErrors["8"]["error_message"];
    }
    
    //10.Amount
    if($amount == "" || $amount == 0)
    {
        $errors .= $rowErrors["9"]["error_message"];
    }
    
    //11.Journal Name
    if(!isset($journal_name))
    {
        $errors .= $rowErrors["10"]["error_message"];
    }
    
    //12.Active Ledger
    if($ledger_active == 0)
    {
        $errors .= $rowErrors["11"]["error_message"];
    }
    
    //13.Journal ID
    if($journal_id == "")
    {
        $errors .= $rowErrors["12"]["error_message"];
    }

    if($parent_id > 0)
    {      
        //14. Using Parent Ledger
        if($ledger_kids > 0)
        {
            $errors .= $rowErrors["13"]["error_message"];
        }
        
        //15.Non Standard comtra
        if($standard_contra = 1)
        {
            $ledger_count = getTableDetailField("bk_journal_contra_accounts",$journal_id." AND trim(ledger_id) = '".trim($ledger_id)."'","journal_id","id"); 
            
            if($ledger_count == 0)
            {
                $errors .= $rowErrors["14"]["error_message"];
            }
        }
        
        //16.Date
        if($date != $parent_date)
        {
            $errors .= $rowErrors["15"]["error_message"];
        }
    }

    
    return $errors;
}

function getChildErrorsXml($parent_id,$journal_id)
{
    $qry = "SELECT 
        item_id,
        date,
        description
        FROM bk_transaction 
        WHERE parent_id = {$parent_id}
        ORDER BY date ASC";

    $res = mysql_query($qry) or die(mysql_error().$qry);
    
    while ($row = mysql_fetch_array($res))
    {
        $errors = getErrorMessages($row["item_id"]);
        
        echo "<row id = '{$row["item_id"]}'>";
            echo "<cell> {$row["item_id"]} </cell>";
            echo "<cell> {$row["date"]} </cell>";
            echo "<cell> {$row["description"]} </cell>";
            echo "<cell style='color:red;'> {$errors} </cell>";
        echo "</row>";  
    }
}

function getChildContraErrors($company_id,$ledger_id,$language_id,$trans_year,$parent_id)
{
    $qryErrors = "SELECT 
    item_id,
    date,
    ledger_id,
    description,
    amount,
    currency
    FROM bk_transaction bt
    WHERE item_id > 0";
    
    $qryErrors .= " AND parent_id = {$parent_id}";
    $qryErrors .= " ORDER BY date ASC";

    $resErrors = mysql_query($qryErrors) or die("<row><cell>".mysql_error()."</cell></row></rows>");
    
    while ($rowErrors = mysql_fetch_array($resErrors))
    {
        $errors = getErrorMessages($rowErrors["item_id"]);
    
            echo "<row id = '{$rowErrors["item_id"]}'>";
                echo "<cell> {$rowErrors["item_id"]} </cell>";
                echo "<cell> {$rowErrors["date"]} </cell>";
                echo "<cell> {$rowErrors["ledger_id"]} </cell>";
                
                $ledger_name = getLedgerName($rowErrors["ledger_id"],$language_id);

                echo "<cell> {$ledger_name} </cell>";
                echo "<cell> {$rowErrors["description"]} </cell>";
                echo "<cell> {$rowErrors["amount"]} </cell>";
                echo "<cell> {$rowErrors["currency"]} </cell>";
                echo "<cell style='color:red;'> {$errors}</cell>";
            echo "</row>";  
    }    
}
   
function win_kill($pid)
{ 
    $wmi=new COM("winmgmts:{impersonationLevel=impersonate}!\\\\.\\root\\cimv2"); 
    $procs=$wmi->ExecQuery("SELECT * FROM Win32_Process WHERE ProcessId='".$pid."'"); 
    foreach($procs as $proc) 
      $proc->Terminate(); 
}

function setLogErrors($transaction_id)
{    
    $errors = getErrorMessages($transaction_id);
    
    $qryErrors = "UPDATE bk_transaction SET log = '{$errors}' WHERE item_id = {$transaction_id}";
    $resErrors = mysql_query($qryErrors) or die(mysql_error().$qryErrors);
    
}

function childLoop($gen)
{
    if($gen->getName() == "KING_JOURNAAL")
    {
        foreach ($gen->children() as $hgen) 
        {
            if($hgen->getName() == "BOEKINGSGANGEN")
            {
                $newid = getMinMaxSortID($parentid,$parentfield,"item_id","item_id","bk_transaction","MAX") + 1;

                $date = new DateTime();
                $today = $date->format('Y-m-d');
                $time = $date->format('H:i:s');
                $eid = intval($_COOKIE["eid"]);
                
                $qryImport = "INSERT INTO bk_transaction(item_id,date,journal_id,parent_id,branch_id,entry_date,entry_time,eid,amount,info,ledger_id,sort_id,currency,booking_date) VALUES ({$newid},'{$today}',77,0,10,'{$today}','{$time}',{$eid},0,'','9100.200',1,'EUR','{$today}')";
                $resImport = mysql_query($qryImport) or die (mysql_error().$qryImport);
        
                foreach ($hgen->children() as $igen) 
                {
                    if($igen->getName() == "BOEKINGSGANG")
                    {
                        foreach ($igen->children() as $jgen) 
                        {
                            if($jgen->getName() == "JOURNAALPOSTEN")
                            {
                                foreach ($jgen->children() as $kgen) 
                                {     
                                    foreach ($kgen->children() as $ngen) 
                                    {
                                        if($ngen->getName() == "JP_BOEKDATUM")
                                        {
                                            $entry_date = $ngen;
                                        }
                                        foreach ($ngen->children() as $zgen) 
                                        {                     
                                            foreach ($zgen->children() as $agen) 
                                            {
                                                if($agen->getName() == "JR_REKENINGNUMMER")
                                                {
                                                    $rid = $agen;
                                                }
                                                
                                                if($agen->getName() == "JR_BOEKZIJDE")
                                                {
                                                    $deb_cred = $agen;
                                                }

                                                if($agen->getName() == "JR_VALUTABEDRAG")
                                                {
                                                    $amount = $agen;
                                                    
                                                    if($deb_cred == "DEB")
                                                    {
                                                        $sortid = getMinMaxSortID2($newid,"parent_id","sort_id","sort_id","bk_transaction","MAX",77) + 1;
                                                        $new_contraid = getMinMaxSortID($parentid,$parentfield,"item_id","item_id","bk_transaction","MAX") + 1;
                                                        
                                                        $qryImport = "INSERT INTO bk_transaction(item_id,date,journal_id,parent_id,branch_id,entry_date,entry_time,eid,amount,ledger_id,sort_id,currency,booking_date,relation_id) VALUES ({$new_contraid},'{$entry_date}',77,{$newid},10,'{$today}','{$time}',{$eid},{$amount},'1210.001',{$sortid},'EUR','{$entry_date}',{$rid})";
                                                        $resImport = mysql_query($qryImport) or die (mysql_error().$qryImport);
                                                    }
                                                }
                                                
                                                if($agen->getName() == "JR_FACTUURNUMMER")
                                                {
                                                    $ref_no = "F".$agen;
                                                }
                                                
                                                if($agen->getName() == "JR_OMSCHRIJVING")
                                                {
                                                    $name = $agen;
                                                }
                                                
                                                if($agen->getName() == "JR_BETALINGSKENMERK")
                                                {
                                                    $order_no = $agen;
                                                    
                                                    $desc = $order_no;
                                                    
                                                    $order_no = mysql_escape_string($order_no);
                                                    $desc = mysql_escape_string($desc);
                                                    $name = mysql_escape_string($name);
                                                    
                                                    $qryImport = "UPDATE bk_transaction SET reference_no = '{$ref_no}',description = '{$desc}',relation = '{$name}' WHERE item_id = {$new_contraid}";
                                                    $resImport = mysql_query($qryImport) or die (mysql_error().$qryImport);
                                                }

                                                foreach ($agen->children() as $bgen) 
                                                {
                                                    $amount = $bgen;
                                                    
                                                    if($bgen->getName() == "HULP_VALUTABEDRAG")
                                                    {
                                                        $amount = "-".$amount;
                                                        
                                                        $sortid = getMinMaxSortID2($newid,"parent_id","sort_id","sort_id","bk_transaction","MAX",77) + 1;
                                                        $new_contraid = getMinMaxSortID($parentid,$parentfield,"item_id","item_id","bk_transaction","MAX") + 1;
                                                    
                                                        $qryImport = "INSERT INTO bk_transaction(item_id,date,journal_id,parent_id,branch_id,entry_date,entry_time,eid,amount,description,ledger_id,sort_id,currency,booking_date,reference_no,relation_id,relation) VALUES ({$new_contraid},'{$entry_date}',77,{$newid},10,'{$today}','{$time}',{$eid},{$amount},'{$desc}','1350',{$sortid},'EUR','{$entry_date}','{$ref_no}',{$rid},'{$name}')";
                                                        $resImport = mysql_query($qryImport) or die (mysql_error().$qryImport);
                                                        
                                                        
                                                        
                                                        
                                                        
                                                    }
                                                }
                                            }
                                        }
                                    }   
                                }
                            }
                        }
                    }
                }
            }    
        }
    }
    
    $total = getTransactionsTotal($newid);                                                 
    $total = $total * -1;
    
    $qryImport = "UPDATE bk_transaction SET date = '{$entry_date}',booking_date = '{$entry_date}',amount = {$total} WHERE item_id = {$newid}";
    $resImport = mysql_query($qryImport) or die (mysql_error().$qryImport);
}

