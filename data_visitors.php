<?php

require_once 'config/config.php';
switch ($_GET['action']) {
    case 1://add new name
        $insert_query = "INSERT INTO visitors(`date`) VALUES(now())";
        if (mysqli_query($con, $insert_query)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error();
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case 2://delete lecturer
        $delete_query = "DELETE FROM visitors WHERE id = {$_GET['id']}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 3; //edit lecture grid
        $id = $_POST["id"];
        $field = $_POST["field"];
        $fieldvalue = $_POST["fieldvalue"];

        $qry = "UPDATE visitors SET {$field} = '{$fieldvalue}' WHERE id = '{$id}'";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);

        if ($res)
            $data['data'] = array('success' => true);
        else
            $data['data'] = array('success' => false,);

        echo json_encode($data);
        break;

    case 4; //fetches data to the members grid
        $month       =filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT)? :date('m');
        $year        =filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT)? :2016;
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT * FROM visitors WHERE MONTH(`date`)=$month AND YEAR(`date`)=$year  ORDER BY  id DESC";
        $gender = array(
            2 => "Male",
            3 => "Female"
        );
        $marital = array(
            14 => "Single",
            15 => "Married",
            16 => "Engaged"
        );
        $result = mysqli_query($con, $query) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<row id="' . $row["id"] . '">';
            $num = 4;
            $num_padded = sprintf("%04d", $row["id"]);
            $regNo = 'V/NUMBER/' . $num_padded;
            echo '<cell><![CDATA[' . $regNo . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["phone"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["email"] . ']]></cell>';
            echo "<cell><![CDATA[" . $gender[$row["gender"]] . "]]></cell>";
            echo "<cell><![CDATA[" . $row["homecell"] . "]]></cell>";
            echo "<cell><![CDATA[" . $marital[$row["marital"]] . "]]></cell>";
            echo "<cell><![CDATA[" . $row["category"] . "]]></cell>";
            echo '</row>';
        }
        echo '</rows>';

        break;
    case 5://add new name
        $insert_query = "INSERT INTO dedicated(`deddate`) VALUES(now())";
        if (mysqli_query($con, $insert_query)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error($con);
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case 6://delete lecturer
        $delete_query = "DELETE FROM dedicated WHERE id = {$_GET['id']}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 7; //edit lecture grid
        $id = $_POST["id"];
        $field = $_POST["field"];
        $fieldvalue = $_POST["fieldvalue"];

        $qry = "UPDATE dedicated SET {$field} = '{$fieldvalue}' WHERE id = '{$id}'";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);

        if ($res)
            $data['data'] = array('success' => true);
        else
            $data['data'] = array('success' => false,);

        echo json_encode($data);
        break;

    case 8; //fetches data to the members grid
       $month       =filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT)? :date('m');
        $year        =filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT)? :2016;
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT * FROM dedicated WHERE MONTH(`deddate`)=$month AND YEAR(`deddate`)=$year  ORDER BY  id DESC";
        $gender = array(
            2 => "Male",
            3 => "Female"
        );
        $marital = array(
            14 => "Single",
            15 => "Married",
            16 => "Engaged"
        );
        $result = mysqli_query($con, $query) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<row id="' . $row["id"] . '">';
            $num_padded = sprintf("%04d", $row["id"]);
            $regNo = 'D/NUMBER/' . $num_padded;
            echo '<cell><![CDATA[' . $regNo . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
             echo '<cell><![CDATA[' . $row["place"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["dob"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["mother"] . ']]></cell>';
            echo "<cell><![CDATA[" . $row["father"] . "]]></cell>";
            echo "<cell><![CDATA[" . $row["deddate"] . "]]></cell>";
            echo "<cell><![CDATA[" . $row["church"] . "]]></cell>";
            echo '</row>';
        }
        echo '</rows>';
        break;
    case '9'://loads members grid
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT m.reg_no,m.name
                    FROM members m ";
        //echo $query;exit;
        $result = mysqli_query($con, $query) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<row id="' . $row["reg_no"] . '">';
            $num = 4;
            $num_padded = sprintf("%04d", $row["reg_no"]);
            $regNo = 'M/NUMBER/' . $num_padded;
            echo '<cell><![CDATA[' . ']]></cell>';
            echo '<cell><![CDATA[' . $regNo . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 10:

        $selected_id = $_POST['selected_id'];

        $id = explode(',', $selected_id);
        foreach ($id as $key => $value) {

            $query = "INSERT INTO baptised(name,phone,email,gender,home_cell,marital,dateofbaptism)  SELECT name,phone,email,gender,home_cell,marital,NOW() FROM members WHERE reg_no={$value}";

            $res1 = mysqli_query($con, $query);

            $qry = "UPDATE members SET baptisedyes = 0 WHERE reg_no = '{$value}'";
            $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
        }
        if ($res1) {
            $msg = "success";
        } else {
            $msg = mysqli_error($con);
        }
        echo json_encode(array("response" => $msg));
        break;
    case 11:
        @$visitor_id = $_GET['visitor_id'];
        $query = "INSERT INTO members(name,phone,email,gender,home_cell,marital)  SELECT name,phone,email,gender,homecell,marital FROM visitors WHERE id={$visitor_id}";
        $res = mysqli_query($con, $query);

        $ress = mysqli_query($con, "DELETE FROM visitors WHERE id={$visitor_id}");
        if ($res) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error($con);
        }
        echo json_encode(array("response" => $msg));
        break;
    case 12:
        $reg_no = $_GET["reg_no"];
        header("Content-type:text/xml");
        $qry = "SELECT * from visitors WHERE id='$reg_no'";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        while ($row = mysqli_fetch_assoc($res)) {
            $_SESSION['reg_no'] = $_GET["reg_no"];
            echo "<data>";
            echo "<upload><![CDATA[{$row["image"]}]]></upload>";
            echo "<name><![CDATA[{$row["name"]}]]></name>";
            echo "<phone><![CDATA[{$row["phone"]}]]></phone>";
            echo "<email><![CDATA[{$row["email"]}]]></email>";
            echo "<address><![CDATA[{$row["address"]}]]></address>";
            echo "<gender><![CDATA[{$row["gender"]}]]></gender>";
            echo "<marital><![CDATA[{$row["marital"]}]]></marital>";
            echo "<homecell><![CDATA[{$row["homecell"]}]]></homecell>";
            echo "<category><![CDATA[{$row["category"]}]]></category>";
            echo "<marital><![CDATA[{$row["marital"]}]]></marital>";
            echo "<s><![CDATA[{$row["saved"]}]]></s>";
            echo "<d_saved><![CDATA[{$row["date_saved"]}]]></d_saved>";
            echo "<p_church><![CDATA[{$row["previous_church"]}]]></p_church>";
            echo "<moved><![CDATA[{$row["moved"]}]]></moved>";
            echo "<visiting><![CDATA[{$row["visitor"]}]]></visiting>";
            echo "<baptised><![CDATA[{$row["baptised"]}]]></baptised>";
            echo "<pay_th><![CDATA[{$row["pay_tithe"]}]]></pay_th>";
            echo "<see><![CDATA[{$row["see_pastor"]}]]></see>";
            echo "<agree_t><![CDATA[{$row["agree_teaching"]}]]></agree_t>";
            echo "<agree_s><![CDATA[{$row["agree_support_church"]}]]></agree_s>";
            echo "<active><![CDATA[{$row["active"]}]]></active>";
            echo "<prayer><![CDATA[{$row["prayer"]}]]></prayer>";
            echo "</data>";
        }
        break;
    case 13://save contribution records
        @$reg_no = $_GET["id"];
        $name = $_POST['name'] ?: '';
        ;
        $phone = $_POST['phone'] ?: '0';
        $email = $_POST['email'] ?: '';
        ;
        $address = $_POST['address'] ?: '0';
        ;
        $gender = $_POST['gender'] ?: '';
        ;
        $category = $_POST['category'] ?: '';
        $homecell = $_POST['homecell'] ?: '';
        ;
        $marital = $_POST['marital'] ?: '1';
        $saved = $_POST['s'] ?: '0';
        $date_saved = $_POST['d_saved'] ?: '0';
        $previous_church = $_POST['p_church'] ?: '0';
        $moved = $_POST['moved'] ?: '0';
        $visitor = $_POST['visiting'] ?: '0';
        $baptised = $_POST['baptised'] ?: '0';
        $pay_tithe = $_POST['pay_th'] ?: '0';
        $see_pastor = $_POST['see'] ?: '0';
        $agree_teaching = $_POST['agree_t'] ?: '0';
        $agree_support_church = $_POST['agree_s'] ?: '0';
        $active = $_POST['active'];
        $prayer = $_POST['prayer'];
        $query = "UPDATE visitors SET"
                . " name='" . mysqli_real_escape_string($con, $name) . "',"
                . "phone= '" . mysqli_real_escape_string($con, $phone) . "',"
                . "homecell= '" . mysqli_real_escape_string($con, $homecell) . "',"
                . "category= '" . mysqli_real_escape_string($con, $category) . "',"
                . "email= '" . mysqli_real_escape_string($con, $email) . "',"
                . "address= '" . mysqli_real_escape_string($con, $address) . "',"
                . "gender= '" . mysqli_real_escape_string($con, $gender) . "',"
                . "marital= '" . mysqli_real_escape_string($con, $marital) . "',"
                . "saved='" . mysqli_real_escape_string($con, $saved) . "',"
                . "date_saved= '" . mysqli_real_escape_string($con, $date_saved) . "',"
                . "previous_church= '" . mysqli_real_escape_string($con, $previous_church) . "',"
                . "moved='" . mysqli_real_escape_string($con, $moved) . "',"
                . "visitor= '" . mysqli_real_escape_string($con, $visitor) . "',"
                . "baptised= '" . mysqli_real_escape_string($con, $baptised) . "',"
                . " pay_tithe='" . mysqli_real_escape_string($con, $pay_tithe) . "',"
                . "see_pastor= '" . mysqli_real_escape_string($con, $see_pastor) . "',"
                . "agree_teaching= '" . mysqli_real_escape_string($con, $agree_teaching) . "',"
                . "agree_support_church= '" . mysqli_real_escape_string($con, $agree_support_church) . "',"
                . "active= '" . mysqli_real_escape_string($con, $active) . "',"
                . "prayer= '" . mysqli_real_escape_string($con, $prayer) . "',"
                . "gender = '" . $gender . "',"
                . "active='{$active}' "
                . " WHERE id='{$reg_no}'";

        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);
        if ($results)
            $data = array('success' => true, 'name' => $_POST['name'],
                'phone' => $_POST['phone'], 'email' => $_POST['email'], 'gender' => getlookupname($gender, $con),
                'homecell' => $homecell, 'marital' => getlookupname($marital, $con));
        else
            $data = array('success' => false,);
        echo json_encode($data);
        break;
    case 14://save contribution records
        @$reg_no = $_GET["id"];
        $name = $_POST['name'] ?: '';

        $dob = $_POST['dob'] ?: '0';
        $mother = $_POST['mother'] ?: '';

        $father = $_POST['father'] ?: '0';

        $place = $_POST['place'] ?: '';
        $church = $_POST['church'] ?: '';

        $query = "UPDATE dedicated SET"
                . " name='" . mysqli_real_escape_string($con, $name) . "',"
                . "dob= '" . mysqli_real_escape_string($con, $dob) . "',"
                . "mother= '" . mysqli_real_escape_string($con, $mother) . "',"
                . "father= '" . mysqli_real_escape_string($con, $father) . "',"
                . "place= '" . mysqli_real_escape_string($con, $place) . "',"
                . "church= '" . mysqli_real_escape_string($con, $church) . "'"
                . " WHERE id='{$reg_no}'";

        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);
        if ($results)
            $data = array('success' => true, 'name' => $name,
                'dob' => $dob, 'mother' => $mother, 'father' => $father,
                'place' => $place, 'church' => $church);
        else
            $data = array('success' => false,);
        echo json_encode($data);
        break;

    case 15://selects details for a specific members to a form
        //session_start();
        $reg_no = $_GET["reg_no"];
        header("Content-type:text/xml");
        $qry = "SELECT * from dedicated WHERE id='$reg_no'";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        while ($row = mysqli_fetch_assoc($res)) {
            $_SESSION['reg_no'] = $_GET["reg_no"];
            echo "<data>";

            echo "<name><![CDATA[{$row["name"]}]]></name>";
            echo "<dob><![CDATA[{$row["dob"]}]]></dob>";
            echo "<mother><![CDATA[{$row["mother"]}]]></mother>";
            echo "<father><![CDATA[{$row["father"]}]]></father>";
            echo "<deddate><![CDATA[{$row["deddate"]}]]></deddate>";
            echo "<place><![CDATA[{$row["place"]}]]></place>";
            echo "<church><![CDATA[{$row["church"]}]]></church>";
            echo "</data>";
        }
        break;
    case 16: //edit lecture grid
        $reg_no = $_POST["reg_no"];
        $field = $_POST["field"];
        $fieldvalue = $_POST["fieldvalue"];

        $qry = "UPDATE baptised SET {$field} = '{$fieldvalue}' WHERE reg_no = '{$reg_no}'";

        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);

        if ($res)
            $data['data'] = array('success' => true);
        else
            $data['data'] = array('success' => false,);

        echo json_encode($data);
        break;
    case 17; //fetches data to the members grid
        $month       =filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT)? :date('m');
        $year        =filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT)? :2016;
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT * FROM baptised  WHERE MONTH(`dateofbaptism`)=$month AND YEAR(`dateofbaptism`)=$year ORDER BY  reg_no DESC";
        $result = mysqli_query($con, $query) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<row id="' . $row["reg_no"] . '">';
            $num_padded = sprintf("%04d", $row["reg_no"]);
            $regNo = 'B/NUMBER/' . $num_padded;
            echo '<cell><![CDATA[' . $regNo . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["dateofbirth"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["dateofbaptism"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["place"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["minister"] . ']]></cell>';

            echo '</row>';
        }
        echo '</rows>';
        break;
    case 18://selects details for a specific members to a form
        //session_start();
        $reg_no = $_GET["reg_no"];
        header("Content-type:text/xml");
        $qry = "SELECT * from baptised WHERE reg_no='$reg_no'";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        while ($row = mysqli_fetch_assoc($res)) {
            $num_padded = sprintf("%04d", $row["reg_no"]);
            $regNo = 'B/NUMBER/' . $num_padded;
            echo "<data>";
            echo "<no>" . $regNo . "</no>";
            echo "<name><![CDATA[{$row["name"]}]]></name>";
            echo "<dateofbaptism><![CDATA[{$row["dateofbaptism"]}]]></dateofbaptism>";
            echo "<dob><![CDATA[{$row["dateofbirth"]}]]></dob>";
            echo "<minister><![CDATA[{$row["minister"]}]]></minister>";
            echo "<place><![CDATA[{$row["place"]}]]></place>";
            echo "</data>";
        }
        break;
    case 19://save contribution records
        @$reg_no = $_GET["id"];
        $name = $_POST['name'] ?: '';
        $dateofbaptism = $_POST['dateofbaptism'] ?: '';
        $dob = $_POST['dob'] ?: '';
        $minister = $_POST['minister'] ?: '';
        $place = $_POST['place'] ?: '';
        $query = "UPDATE baptised SET"
                . " name='" . mysqli_real_escape_string($con, $name) . "',"
                 . " dateofbirth='" . mysqli_real_escape_string($con, $dob) . "',"
                 . " place='" . mysqli_real_escape_string($con, $place) . "',"
                 . " minister='" . mysqli_real_escape_string($con, $minister) . "',"
                . "dateofbaptism= '" . mysqli_real_escape_string($con, $dateofbaptism) . "'"
                . " WHERE reg_no='{$reg_no}'";

        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);
        if ($results)
            $data = array('success' => true, 'name' => $name,
                'dateofbaptism' => $dateofbaptism,'dob'=>$dob,'minister'=>$minister,'place'=>$place);
        else
            $data = array('success' => false,);
        echo json_encode($data);
        break;
    case 20://delete lecturer
        $delete_query = "DELETE FROM baptised WHERE reg_no = {$_GET['id']}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
}

function getlookupname($value, $con) {
    $qrylookup = "SELECT item_name FROM lookup WHERE  item_id={$value}";
    $qryres = mysqli_query($con, $qrylookup) or die(mysqli_error($con) . $qrylookup);
    $row = mysqli_fetch_assoc($qryres);
    return $row['item_name'];
}
