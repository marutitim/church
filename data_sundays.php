<?php

require_once 'config/config.php';
switch ($_GET['action']) {

    case 1://add new sunday record
        $insert_query = "INSERT INTO offering(`date`) VALUES(now())";
        if (mysqli_query($con, $insert_query)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error($con);
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case 2://delete sunday record
        $delete_query = "DELETE FROM offering WHERE id = {$_GET['id']}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 3; //edit sunday record
        $id = $_POST["id"];
        $field = $_POST["field"];
        $fieldvalue = filter_input(INPUT_POST, 'fieldvalue', FILTER_SANITIZE_NUMBER_INT);

        $qry = "UPDATE offering SET {$field} = '{$fieldvalue}' WHERE id = '{$id}'";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);

        if ($res)
            $data['data'] = array('success' => true);
        else
            $data['data'] = array('success' => false,);

        echo json_encode($data);
        break;

    case 4; //fetches data to the sunday record
        $month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT) ?: date('m');
        $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT) ?: 2016;
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT id,date,men,women,youth,children,tithe,thanks,seed FROM offering WHERE MONTH(`date`)=$month AND YEAR(`date`)=$year ORDER BY id DESC";
        //echo $query;exit;
        $result = mysqli_query($con, $query) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<row id="' . $row["id"] . '">';
            $total = $row["men"] + $row["women"] + $row["youth"] + $row["children"] + $row["tithe"] + $row["thanks"] + $row["seed"];
            echo '<cell><![CDATA[' . $row["date"] . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["men"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["women"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["youth"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["children"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["tithe"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["thanks"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["seed"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($total) . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 5://combo
        $id = $_GET['id'];
        $query = "SELECT SUM(men) men, sum(women) women ,SUM(youth) youth ,SUM(tithe) tithe,SUM(thanks)thanks,SUM(seed)seed,SUM(children) children,(SUM(men) +SUM(women)+ SUM(youth) + SUM(children))total FROM  offering ";
        $result = mysqli_query($con, $query) or die(mysqli_error());
        $row = mysqli_fetch_assoc($result);
        $men = numberformart($row['men']);
        $women = numberformart($row['women']);
        $youth = numberformart($row['youth']);
        $children = numberformart($row['children']);
        $tithe = numberformart($row['tithe']);
        $thanks = numberformart($row['thanks']);
        $seed = numberformart($row['seed']);
        $totals = numberformart($row['total']);
        $data['data'] = array('success' => true, 'men' => $men, 'women' => $women, 'youth' => $youth, 'children' => $children, 'tithe' => $tithe, 'thanks' => $thanks, 'seed' => $seed, 'totals' => $totals);
        echo json_encode($data);
        break;
    case 6://fetch one finance record
        $id = $_GET["id"];
        header("Content-type:text/xml");
        $qry = "SELECT * from offering WHERE id='$id'";
        // echo $qry;exit;
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        while ($row = mysqli_fetch_assoc($res)) {
            $total = $row["men"] + $row["women"] + $row["youth"] + $row["children"] + $row["tithe"] + $row["thanks"] + $row["seed"];
            echo "<data>";
            echo "<id><![CDATA[{$row["id"]}]]></id>";
            echo "<date><![CDATA[" . $row["date"] . "]]></date>";
            echo "<men><![CDATA[" . numberformart($row["men"]) . "]]></men>";
            echo "<women><![CDATA[" . numberformart($row["women"]) . "]]></women>";
            echo "<youth><![CDATA[" . numberformart($row["youth"]) . "]]></youth>";
            echo "<children><![CDATA[" . numberformart($row["children"]) . "]]></children>";
            echo "<tithe><![CDATA[" . numberformart($row["tithe"]) . "]]></tithe>";
            echo "<thanks><![CDATA[" . numberformart($row["thanks"]) . "]]></thanks>";
            echo "<seed><![CDATA[" . numberformart($row["seed"]) . "]]></seed>";
            echo "<total><![CDATA[" . numberformart($total) . "]]></total>";
            echo "</data>";
        }
        break;
    case 7://save contribution records

        @$id = $_GET["id"] ?: 0;
        @$date = $_POST['date'] ?: 0;
        @$men = $_POST['men'] ?: 0;
        @$women = $_POST['women'] ?: 0;
        @$youth = $_POST['youth'] ?: 0;
        @$children = $_POST['children'] ?: 0;
        @$tithe = $_POST['tithe'] ?: 0;
        @$thanks = $_POST['thanks'] ?: 0;
        @$seed = $_POST['seed'] ?: 0;
        @$total = $men + $women + $youth + $children + $tithe + $thanks + $seed;
        $query = "UPDATE offering SET  "
                . "date='" . mysqli_real_escape_string($con, $date) . "',"
                . "men= '" . mysqli_real_escape_string($con, $men) . "',"
                . "women= '" . mysqli_real_escape_string($con, $women) . "',"
                . "youth= '" . mysqli_real_escape_string($con, $youth) . "',"
                . "children= '" . mysqli_real_escape_string($con, $children) . "', "
                . "tithe= '" . mysqli_real_escape_string($con, $thanks) . "', "
                . "thanks= '" . mysqli_real_escape_string($con, $thanks) . "', "
                . "seed= '" . mysqli_real_escape_string($con, $seed) . "' "
                . " WHERE id='{$id}'";
        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);
        if ($results)
            $data = array('success' => true, 'date' => $date,
                'men' => numberformart($men), 'women' => numberformart($women), 'youth' => numberformart($youth),
                'children' => numberformart($children), 'tithe' => $tithe, 'thanks' => $thanks, 'seed' => $seed, 'totals' => numberformart($total));
        else
            $data = array('success' => false,);
        echo json_encode($data);
        break;
}

function numberformart($number) {
    return number_format((float) $number, 2, '.', '');
}
