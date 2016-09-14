<?php

require_once 'config/config.php';
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
switch ($action) {
    case 1://fetch course tree
        header("Content-type:text/xml");
        $qry = "SELECT id,name FROM `groups`  WHERE parent = 0";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con));
        print("<tree id='0'" . ">\n");
        while ($row = mysqli_fetch_assoc($res)) {
            $id = $row['id'];
            $Name = htmlspecialchars($row['name'], ENT_QUOTES, "utf-8");
            print("<item  text=' " . $Name . "' id='" . $id . "'  >" . "\n");
            fetchMenuDirectories($id, $con);
            print("</item>\n");
        }
        print("</tree>\n");

        break;

    case 2://add new name
        $insert_query = "INSERT INTO members(`date`) VALUES(now())";
        if (mysqli_query($con, $insert_query)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error();
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case 3://delete lecturer
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $delete_query = "DELETE FROM members WHERE reg_no = {$id}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 4; //edit lecture grid
        $reg_no = filter_input(INPUT_GET, 'reg_no', FILTER_SANITIZE_STRING);
        $field = filter_input(INPUT_GET, 'field', FILTER_SANITIZE_STRING);
        $fieldvalue = filter_input(INPUT_GET, 'fieldvalue', FILTER_SANITIZE_STRING);

        $qry = "UPDATE members SET {$field} = '{$fieldvalue}' WHERE reg_no = '{$reg_no}'";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);

        if ($res)
            $data['data'] = array('success' => true);
        else
            $data['data'] = array('success' => false,);

        echo json_encode($data);
        break;

    case 5; //fetches data to the members grid
        $month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT) ?: date('m');
        $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT) ?: 2016;
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT * FROM members   WHERE MONTH(`date`)=$month AND YEAR(`date`)=$year ORDER BY  name ASC";

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
            echo '<row id="' . $row["reg_no"] . '">';
            $num = 4;
            $num_padded = sprintf("%04d", $row["reg_no"]);
            $regNo = 'M/NUMBER/' . $num_padded;
            echo '<cell><![CDATA[' . $regNo . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["phone"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["email"] . ']]></cell>';
            echo "<cell><![CDATA[" . $gender[$row["gender"]] . "]]></cell>";
            echo "<cell><![CDATA[" . $row["home_cell"] . "]]></cell>";
            echo "<cell><![CDATA[" . $marital[$row["marital"]] . "]]></cell>";
            echo "<cell><![CDATA[" . "]]></cell>";
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 6: //updates lecturer form values
        $reg_no = filter_input(INPUT_GET, 'reg_no', FILTER_SANITIZE_STRING);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING)?:'';
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT)?:0;
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)?:'';
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING)?:'';
        $gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING)?:'';
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING)?:'';
        $homecell = filter_input(INPUT_POST, 'homecell', FILTER_SANITIZE_STRING)?:'';
        $marital = filter_input(INPUT_POST, 'marital', FILTER_SANITIZE_STRING) ?: '1';
        $saved = filter_input(INPUT_POST, 's', FILTER_SANITIZE_STRING) ?: '0';
        $date_saved = filter_input(INPUT_POST, 'd_saved', FILTER_SANITIZE_STRING) ?: '0';
        $previous_church = filter_input(INPUT_POST, 'p_church', FILTER_SANITIZE_STRING) ?: '0';
        $moved = filter_input(INPUT_POST, 'moved', FILTER_SANITIZE_STRING) ?: '0';
        $visitor = filter_input(INPUT_POST, 'visiting', FILTER_SANITIZE_STRING) ?: '0';
        $baptised = filter_input(INPUT_POST, 'baptised', FILTER_SANITIZE_STRING) ?: '0';
        $pay_tithe = filter_input(INPUT_POST, 'pay_th', FILTER_SANITIZE_STRING) ?: '0';
        $see_pastor = filter_input(INPUT_POST, 'see', FILTER_SANITIZE_STRING) ?: '0';
        $agree_teaching = filter_input(INPUT_POST, 'agree_t', FILTER_SANITIZE_STRING) ?: '0';
        $agree_support_church = filter_input(INPUT_POST, 'agree_s', FILTER_SANITIZE_STRING) ?: '0';
        $active = filter_input(INPUT_POST, 'active', FILTER_SANITIZE_STRING);
        $prayer = filter_input(INPUT_POST, 'prayer', FILTER_SANITIZE_STRING);
        $query = "UPDATE members SET"
                . " name='" . mysqli_real_escape_string($con, $name) . "',"
                . "phone= '" . mysqli_real_escape_string($con, $phone) . "',"
                . "home_cell= '" . mysqli_real_escape_string($con, $homecell) . "',"
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
                . " WHERE reg_no='{$reg_no}'";
        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);
        if ($results)
            $data = array('success' => true, 'name' => $_POST['name'],
                'phone' => $_POST['phone'], 'email' => $_POST['email'], 'gender' => getlookupname($gender, $con),
                'homecell' => $homecell, 'marital' => getlookupname($marital, $con));
        else
            $data = array('success' => false,);
        echo json_encode($data);
        break;
    case 7://selects details for a specific members to a form
        //session_start();
        $reg_no = filter_input(INPUT_GET, 'reg_no', FILTER_SANITIZE_STRING);
        header("Content-type:text/xml");
        $qry = "SELECT * from members WHERE reg_no='$reg_no'";
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
            echo "<homecell><![CDATA[{$row["home_cell"]}]]></homecell>";
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
    case 8://add new role
        $reg_no = filter_input(INPUT_GET, 'reg_no', FILTER_SANITIZE_STRING);
        ;
        $insert_query = "INSERT INTO finances(`reg_no`,date) VALUES($reg_no,NOW())";
        if (mysqli_query($con, $insert_query)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error();
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case 9://delete role
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $delete_query = "DELETE FROM finances WHERE id = {$id}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 10; //edit role grid
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
        $field = filter_input(INPUT_POST, 'field', FILTER_SANITIZE_STRING);
        $fieldvalue = filter_input(INPUT_POST, 'fieldvalue', FILTER_SANITIZE_STRING);

        $qry = "UPDATE finances SET {$field} = '{$fieldvalue}' WHERE id = '{$id}'";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);

        if ($res)
            $data['data'] = array('success' => true);
        else
            $data['data'] = array('success' => false,);

        echo json_encode($data);
        break;
    case 11://financials
        $reg_no = filter_input(INPUT_GET, 'reg_no', FILTER_SANITIZE_STRING);

        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT id, date,tithe,first_fruit,love_offering,evangelism,seed,others,thanksgiving,paster_blessing,welfare,(tithe +first_fruit +love_offering + evangelism +seed +others +thanksgiving +paster_blessing)as total FROM  finances  WHERE reg_no={$reg_no} ORDER BY  id DESC";
        //echo $query;exit;
        $result = mysqli_query($con, $query) or die(mysqli_error());
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $i++;
            echo '<row id="' . $row["id"] . '">';
            echo '<cell><![CDATA[' . $row["date"] . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["tithe"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["first_fruit"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["love_offering"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["evangelism"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["seed"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["others"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["thanksgiving"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["paster_blessing"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["welfare"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["total"]) . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 12://combo
        $id = filter_input(INPUT_GET, 'reg_no', FILTER_SANITIZE_STRING);
        $query = "SELECT SUM(tithe) tithe, sum(welfare) welfare,sum(first_fruit) fruit ,SUM(love_offering) love ,SUM(evangelism) evang,SUM(seed) seed,SUM(others) others,SUM(thanksgiving) thanks,SUM(paster_blessing) bless ,(SUM(tithe)+ SUM(first_fruit) +sum(welfare)+SUM(love_offering) + SUM(evangelism)+ SUM(seed) +SUM(others) +SUM(thanksgiving)+SUM(paster_blessing) ) total FROM  finances
 WHERE reg_no={$id}";
        $result = mysqli_query($con, $query) or die(mysqli_error());
        $row = mysqli_fetch_assoc($result);
        $first_fruit = numberformart($row['fruit']);
        $love_offering = numberformart($row['love']);
        $evangelism = numberformart($row['evang']);
        $seed = numberformart($row['seed']);
        $welfare = numberformart($row["welfare"]);
        $others = numberformart($row['others']);
        $thanksgiving = numberformart($row['thanks']);
        $paster_blessing = numberformart($row['bless']);
        $tithe = numberformart($row['tithe']);
        $totals = numberformart($row['total']);
        $data['data'] = array('success' => true, 'tithe' => $tithe, 'welfare' => $welfare, 'first_fruit' => $first_fruit, 'love_offering' => $love_offering, 'evangelism' => $evangelism, 'seed' => $seed, 'others' => $others, 'thanksgiving' => $thanksgiving, 'blessing' => $paster_blessing, 'totals' => $totals);

        echo json_encode($data);
        break;
    case 13://fetch one finance record
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        header("Content-type:text/xml");
        $qry = "SELECT * from finances WHERE id='$id'";
        // echo $qry;exit;
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        while ($row = mysqli_fetch_assoc($res)) {

            echo "<data>";
            echo "<no><![CDATA[" . $row["date"] . "]]></no>";
            echo "<tithe><![CDATA[" . numberformart($row["tithe"]) . "]]></tithe>";
            echo "<first_fruit><![CDATA[" . numberformart($row["first_fruit"]) . "]]></first_fruit>";
            echo "<love><![CDATA[" . numberformart($row["love_offering"]) . "]]></love>";
            echo "<evang><![CDATA[" . numberformart($row["evangelism"]) . "]]></evang>";
            echo "<seed><![CDATA[" . numberformart($row["seed"]) . "]]></seed>";
            echo "<others><![CDATA[" . numberformart($row["others"]) . "]]></others>";
            echo "<thanks><![CDATA[" . numberformart($row["thanksgiving"]) . "]]></thanks>";
            echo "<blessings><![CDATA[" . numberformart($row["paster_blessing"]) . "]]></blessings>";
            echo "<welfare><![CDATA[" . numberformart($row["welfare"]) . "]]></welfare>";
            $total = $row["tithe"] + $row["welfare"] + $row["first_fruit"] + $row["love_offering"] + $row["evangelism"] + $row["seed"] + $row["others"] + $row["thanksgiving"] + $row["paster_blessing"];
            echo "<total><![CDATA[" . numberformart($total) . "]]></total>";
            echo "</data>";
        }
        break;
    case 14://save contribution records

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $tithe = filter_input(INPUT_POST, 'tithe', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $first_fruit = filter_input(INPUT_POST, 'first_fruit', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $love = filter_input(INPUT_POST, 'love', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $evang = filter_input(INPUT_POST, 'evang', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $seed = filter_input(INPUT_POST, 'seed', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $others = filter_input(INPUT_POST, 'others', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $thanks = filter_input(INPUT_POST, 'thanks', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $blessings = filter_input(INPUT_POST, 'blessings', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $welfare = filter_input(INPUT_POST, 'welfare', FILTER_SANITIZE_NUMBER_FLOAT) ?: 0;
        $total = $tithe + $first_fruit + $love + $evang + $seed + $others + $thanks + $blessings + $welfare;
        $query = "UPDATE finances SET  "
                . "tithe='" . mysqli_real_escape_string($con, $tithe) . "',"
                . "first_fruit= '" . mysqli_real_escape_string($con, $first_fruit) . "',"
                . "love_offering= '" . mysqli_real_escape_string($con, $love) . "',"
                . "evangelism= '" . mysqli_real_escape_string($con, $evang) . "',"
                . "seed= '" . mysqli_real_escape_string($con, $seed) . "',"
                . "others= '" . mysqli_real_escape_string($con, $others) . "',"
                . "thanksgiving='" . mysqli_real_escape_string($con, $thanks) . "',"
                . "paster_blessing= '" . mysqli_real_escape_string($con, $blessings) . "',"
                . "welfare= '" . mysqli_real_escape_string($con, $welfare) . "',"
                . "totals= '" . mysqli_real_escape_string($con, $total) . "'"
                . " WHERE id='{$id}'";
        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);
        if ($results)
            $data = array('success' => true, 'tithe' => $tithe,
                'first_fruit' => $first_fruit, 'love' => $love, 'evang' => $evang, 'welfare' => $welfare,
                'seed' => $seed, 'others' => $others, 'thanksgiving' => $thanks, 'blessings' => $blessings, 'total' => $total);
        else
            $data = array('success' => false,);
        echo json_encode($data);
        break;
    case 15; //assign member
        $reg_no = filter_input(INPUT_POST, 'reg_no', FILTER_SANITIZE_STRING);
        $group_id = filter_input(INPUT_POST, 'group_id', FILTER_SANITIZE_STRING);
        $insert_query = "INSERT INTO member_to_group(`reg_no`,group_id) VALUES('$reg_no','$group_id')";
        //  echo $insert_query;
        mysqli_query($con, $insert_query) or die(mysqli_error($con));
        $id = mysqli_insert_id($con);
        $msg = 'success';
        echo json_encode(array("response" => $msg, "newId" => $id));
        break;
    case 16://adds new programme
        header("Content-type:text/xml");
        $reg_no = filter_input(INPUT_GET, 'reg_no', FILTER_SANITIZE_STRING);
        $query = "SELECT group_id FROM member_to_group WHERE reg_no=$reg_no";

        $res = mysqli_query($con, $query) or die(mysqli_error($con));
        $rows = mysqli_fetch_array($res);

        $groups = $rows['group_id'] ?: 0;

        $qry = "SELECT id,name FROM `groups`  WHERE parent = 0 AND id IN('$groups')";

        $res = mysqli_query($con, $qry) or die(mysqli_error($con));
        print("<tree id='0'" . ">\n");
        while ($row = mysqli_fetch_assoc($res)) {
            $id = $row['id'];
            $Name = htmlspecialchars($row['name'], ENT_QUOTES, "utf-8");
            print("<item  text=' " . $Name . "' id='" . $id . "' checked='1'  >" . "\n");
            fetchMenuDirectoriesgroups($id, $con, $groups);
            print("</item>\n");
        }
        print("</tree>\n");

        break;
    case 17://cell group
        header("Content-type:text/xml");
        $qry = "SELECT id,cell_name FROM `home_cell`";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con));
        print("<tree id='0'" . ">\n");
        while ($row = mysqli_fetch_assoc($res)) {

            $Name = htmlspecialchars($row['cell_name'], ENT_QUOTES, "utf-8");
            print("<item  text=' " . $Name . "' id='" . $Name . "'  >" . "\n");
            print("</item>\n");
        }
        print("</tree>\n");
        break;
    case 18: //select members from a cell
        $cell_no = $_GET['id'];
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT * FROM members  WHERE home_cell='{$cell_no}'";
        $result = mysqli_query($con, $query) or die(mysqli_error());
        $gender = array(
            2 => "Male",
            3 => "Female"
        );
        $marital = array(
            14 => "Single",
            15 => "Married",
            16 => "Engaged"
        );
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<row id="' . $row["reg_no"] . '">';
            $num = 4;
            $num_padded = sprintf("%04d", $row["reg_no"]);
            $regNo = 'M/NUMBER/' . $num_padded;
            echo '<cell><![CDATA[' . $regNo . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["phone"] . ']]></cell>';
            echo "<cell><![CDATA[" . $gender[$row["gender"]] . "]]></cell>";
            echo "<cell><![CDATA[" . $marital[$row["marital"]] . "]]></cell>";
            echo "<cell><![CDATA[" . $row["category"] . "]]></cell>";
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 19:
        $cell_no = $_POST['cell_no'];
        $selected_id = $_POST['selected_id'];
        $id = explode(',', $selected_id);
        foreach ($id as $key => $value) {
            $insert_query = "INSERT INTO member_to_cell(`reg_no`,`cell_no`,date) "
                    . "VALUES('$value','$cell_no',NOW()) "
                    . "ON DUPLICATE KEY UPDATE cell_no='$cell_no'";
            $res = mysqli_query($con, $insert_query);
        }
        if ($res) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error($con);
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case '20'://loads members grid
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT m.reg_no,m.name FROM members m WHERE m.reg_no NOT IN(SELECT reg_no FROM member_to_cell )  ORDER BY  m.name ASC";
        //echo $query;exit;
        $result = mysqli_query($con, $query) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<row id="' . $row["reg_no"] . '">';
            $num = 4;
            $num_padded = sprintf("%04d", $row["reg_no"]);
            $regNo = 'KAG/' . $num_padded;
            echo '<cell><![CDATA[' . ']]></cell>';
            echo '<cell><![CDATA[' . $regNo . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 21://delete from list
        $delete_query = "DELETE FROM member_to_cell WHERE id = {$_GET['id']}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 22://add new name
        $insert_query = "INSERT INTO assests(`name`,date) VALUES('',NOW())";
        if (mysqli_query($con, $insert_query)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error();
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case 23://delete lecturer
        $delete_query = "DELETE FROM assests WHERE id = {$_GET['id']}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 24; //edit lecture grid
        $assest_id = $_POST["assest_id"];
        $field = $_POST["field"];
        $fieldvalue = $_POST["fieldvalue"];

        $qry = "UPDATE assests SET {$field} = '{$fieldvalue}' WHERE id = '{$assest_id}'";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);

        if ($res)
            $data['data'] = array('success' => true);
        else
            $data['data'] = array('success' => false,);

        echo json_encode($data);
        break;
    case 25://delete submission items
        $delete_query = "DELETE FROM assests WHERE id = {$_GET['assest_id']}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 26; //edit submission items grid
        $month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT) ?: date('m');
        $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT) ?: 2016;
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT * FROM assests WHERE MONTH(`date`)=$month AND YEAR(`date`)=$year ORDER BY  id asc";

        $result = mysqli_query($con, $query) or die(mysqli_error());
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $i++;
            echo '<row id="' . $row["id"] . '">';
            echo '<cell><![CDATA[' . $row["id"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["value"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["location"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["status"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["dateb"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["serial"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["quality"] . ']]></cell>';
            echo '<cell><![CDATA[' . getpersonsname($row["person"], $con) . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 27://saves document submited
        $assests_id = $_GET["assests_id"];
        $name = $_POST['name'];
        $value = $_POST['value'];
        $location = $_POST['location'];
        $status = $_POST['status'];
        $dateb = $_POST['dateb'];
        $serial = $_POST['serial'];
        $quality = $_POST['quality'];
        $person = $_POST['person'];
        $query = "UPDATE assests SET "
                . "name='" . mysqli_real_escape_string($con, $name) . "',"
                . "value= '" . mysqli_real_escape_string($con, $value) . "',"
                . "location= '" . mysqli_real_escape_string($con, $location) . "',"
                . "status= '" . mysqli_real_escape_string($con, $status) . "',"
                . "dateb= '" . mysqli_real_escape_string($con, $dateb) . "',"
                . "serial= '" . mysqli_real_escape_string($con, $serial) . "',"
                . "quality= '" . mysqli_real_escape_string($con, $quality) . "',"
                . "person= '" . mysqli_real_escape_string($con, $person) . "'"
                . " WHERE id='{$assests_id}'";
        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);
        if ($results)
            $data = array('success' => true, 'name' => $name,
                'value' => $value, 'location' => $location, 'status' => $status, 'dateb' => $dateb, 'serial' => $serial, 'quality' => $quality, 'person' => getpersonsname($person, $con));
        else
            $data = array('success' => false);
        echo json_encode($data);
        break;
    case 28:
        $id = $_GET["id"];
        header("Content-type:text/xml");
        $qry = "SELECT * from assests WHERE id='$id'";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        while ($row = mysqli_fetch_assoc($res)) {

            echo "<data>";
            echo "<name><![CDATA[{$row["name"]}]]></name>";
            echo "<value><![CDATA[{$row["value"]}]]></value>";
            echo "<location><![CDATA[{$row["location"]}]]></location>";
            echo "<dateb><![CDATA[{$row["dateb"]}]]></dateb>";
            echo "<status><![CDATA[{$row["status"]}]]></status>";
            echo "<serial><![CDATA[{$row["serial"]}]]></serial>";
            echo "<quality><![CDATA[{$row["quality"]}]]></quality>";
            echo "<person><![CDATA[" . getpersonsname($row["person"], $con) . "]]></person>";
            echo "</data>";
        }
        break;
    case 29://course combo
        $month = $_GET['month'];
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT * FROM members  WHERE MONTH(date)={$month} ORDER BY  name ASC";
        //echo $query;exit;
        $result = mysqli_query($con, $query) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<row id="' . $row["reg_no"] . '">';
            $num = 4;
            $num_padded = sprintf("%04d", $row["reg_no"]);
            $regNo = 'KAG/' . $num_padded;
            echo '<cell><![CDATA[' . $regNo . ']]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["phone"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["email"] . ']]></cell>';
            echo "<cell><![CDATA[" . ((isset($row["gender"])) ? getlookupname($row["gender"], $con) : '') . "]]></cell>";
            echo "<cell><![CDATA[" . $row["home_cell"] . "]]></cell>";
            echo "<cell><![CDATA[" . ((isset($row["marital"])) ? getlookupname($row["marital"], $con) : '') . "]]></cell>";
            echo "<cell><![CDATA[" . ((isset($row["category"])) ? getlookupname($row["category"], $con) : '') . "]]></cell>";
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 30;
        $month = $_GET['month'];
        $reg_no = $_GET['regno'];
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT id, tithe,first_fruit,love_offering,evangelism,seed,others,thanksgiving,paster_blessing,(tithe +first_fruit +love_offering + evangelism +seed +others +thanksgiving +paster_blessing)as total "
                . " FROM  finances  WHERE reg_no={$reg_no} AND MONTH(date)={$month} ORDER BY  id DESC";
        //echo $query;exit;
        $result = mysqli_query($con, $query) or die(mysqli_error());
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $i++;
            echo '<row id="' . $row["id"] . '">';
            echo '<cell><![CDATA[' . $i . ']]></cell>';
            echo '<cell><![CDATA[' . $row["tithe"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["first_fruit"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["love_offering"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["evangelism"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["seed"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["others"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["thanksgiving"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["paster_blessing"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["total"] . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 31:
        header("Content-type:text/xml");
        print("<?xml version=\"1.0\"?>");
        echo "<complete>";
        $qry = "SELECT reg_no,name FROM members order by name ASC";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        $i = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $i++;
            echo"<option value='{$row["reg_no"]}' >";
            $name = $row["name"];
            echo clean_this($name);
            echo "</option>";
        }
        echo "</complete>";
        break;
    case 32://add new login record
        $qry = "INSERT INTO login(`username`) VALUES('')";
        if (mysqli_query($con, $qry)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error();
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case 33://delete login
        $qry = "DELETE FROM login WHERE login_id = {$_GET['id']}";
        if (mysqli_query($con, $qry)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error($con);
        }
        echo json_encode(array("message" => $msg));
        break;
    case 34; //edit login grid
        $login_id = $_POST["login_id"];
        $field = $_POST["field"];
        $fieldvalue = $_POST["fieldvalue"];
        $qry = "UPDATE login SET {$field} = '{$fieldvalue}' WHERE login_id = '{$login_id}'";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
        if ($res)
            $data['data'] = array('success' => true);
        else
            $data['data'] = array('success' => false,);

        echo json_encode($data);
        break;
    case 35://selects login records
        header("Content-type:text/xml");
        print("<?xml version = \"1.0\"?>");
        $qry = "SELECT * from login";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
        echo '<rows>';
        while ($row = mysqli_fetch_assoc($res)) {
//            $num_padded = sprintf("%04d", $row["username"] );
//             $regNo = 'M/NUMBER/' . $num_padded;
            echo '<row id="' . $row["login_id"] . '">';
            echo '<cell><![CDATA[' . $row["login_id"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["username"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["password"] . ']]></cell>';
            echo '<cell><![CDATA[' . ((isset($row["previlages"])) ? getlookupname($row["previlages"], $con) : '') . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 36://selects details for a specific lecturer to a form
        $user_id = $_GET["user_id"];
        header("Content-type:text/xml");
        $qry = "SELECT * from login WHERE login_id='$user_id' order by  username ASC";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
        while ($row = mysqli_fetch_assoc($res)) {
            //var_dump($row);exit;
            echo "<data>";
            echo "<userid><![CDATA[{$row["login_id"]}]]></userid>";
            echo "<username><![CDATA[" . $row["username"] . "]]></username>";
            echo "<password><![CDATA[{$row["password"]}]]></password>";
            //echo "<category><![CDATA[{$row["previlages"]}]]></category>";
            echo "<active><![CDATA[{$row["active"]}]]></active>";
            echo "</data>";
        }
        break;
    case 37: //updates user  form values
        $userid = $_GET["user_id"];
        $username = $_POST["username"];
        $password = $_POST['password'];
        $category = $_POST["category"];

        $num_padded = sprintf("%04d", $_POST['username']);
        $regNo = 'M/NUMBER/' . $num_padded;
        $active = $_POST["active"];
        $query = "UPDATE login SET"
                . " username='" . mysqli_real_escape_string($con, $regNo) . "',"
                . "password= '" . mysqli_real_escape_string($con, $password) . "',"
                // . "previlages= '" . mysqli_real_escape_string($con,$category) . "',"
                . "active='{$active}' "
                . " WHERE login_id='{$userid}'";
        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);

        if ($results)
            $data = array('success' => true, 'username' => $regNo,
                'password' => $_POST['password'], 'active' => $_POST['active']);
        else
            $data = array('success' => false);
        echo json_encode($data);
        break;
    case 38:
        header("Content-type:text/xml");
        print("<?xml version=\"1.0\"?>");
        echo "<complete>";
        $qry = "SELECT item_id,item_name FROM lookup WHERE  item_value=22";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        $i = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $i++;
            echo"<option value='{$row["item_id"]}' >";
            $name = $row["item_name"];
            echo clean_this($name);
            echo "</option>";
        }
        echo "</complete>";
        break;
    case 39://add expense
        $insert_query = "INSERT INTO expenses(`date`,amount) VALUES(now(),0)";
        if (mysqli_query($con, $insert_query)) {
            $msg = "new row inserted";
        } else {
            $msg = mysqli_error();
        }
        echo json_encode(array("response" => $msg, "newId" => mysqli_insert_id($con)));
        break;
    case 40://delete expense
        $delete_query = "DELETE FROM expenses WHERE id = {$_GET['expense_id']}";
        if (mysqli_query($con, $delete_query)) {
            $msg = "Deleted";
        } else {
            $msg = "Error : " . mysqli_error();
        }
        echo json_encode(array("message" => $msg));
        break;
    case 41://selects expenditure records
        $month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT) ?: date('m');
        $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT) ?: 2016;
        header("Content-type:text/xml");
        print("<?xml version = \"1.0\"?>");
        $qry = "SELECT * from expenses  WHERE MONTH(`date`)=$month AND YEAR(`date`)=$year ORDER BY id DESC";
        //   echo $qry;exit;
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
        echo '<rows>';
        while ($row = mysqli_fetch_assoc($res)) {
            echo '<row id="' . $row["id"] . '">';
            echo '<cell><![CDATA[]]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["amount"] . ']]></cell>';
            echo '<cell><![CDATA[' . ((isset($row['person'])) ? getlookupname($row['person'], $con) : '') . ']]></cell>';
            echo '<cell><![CDATA[' . ((isset($row['category'])) ? getlookupname($row['category'], $con) : '') . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 42://marital combo
        $itemvalue = $_GET['item_value'];
        header("Content-type:text/xml");
        print("<?xml version=\"1.0\"?>");
        echo "<complete>";
        $qrylookup = "SELECT item_id,item_name FROM lookup WHERE  item_value={$itemvalue}";
        $res = mysqli_query($con, $qrylookup) or die(mysqli_error() . $qrylookup);
        $i = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $i++;
            echo"<option value='{$row["item_id"]}' >";
            $role = $row["item_name"];
            echo clean_this($role);
            echo "</option>";
        }
        echo "</complete>";
        break;
    case 43://cell combo
        header("Content-type:text/xml");
        print("<?xml version=\"1.0\"?>");
        echo "<complete>";
        $qrylookup = "SELECT id,cell_name FROM home_cell";
        $res = mysqli_query($con, $qrylookup) or die(mysqli_error() . $qrylookup);
        $i = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $i++;
            echo"<option value='{$row["id"]}' >";
            $role = $row["cell_name"];
            echo clean_this($role);
            echo "</option>";
        }
        echo "</complete>";
        break;
    case 44://category combo
        $itemvalue = $_GET['item_value'];
        header("Content-type:text/xml");
        print("<?xml version=\"1.0\"?>");
        echo "<complete>";
        $qrylookup = "SELECT item_id,item_name FROM lookup WHERE  item_value={$itemvalue}";
        $res = mysqli_query($con, $qrylookup) or die(mysqli_error() . $qrylookup);
        $i = 0;
        while ($row = mysqli_fetch_assoc($res)) {
            $i++;
            echo"<option value='{$row["item_id"]}' >";
            $role = $row["item_name"];
            echo clean_this($role);
            echo "</option>";
        }
        echo "</complete>";
        break;
    case 45:
        $expense_id = $_GET["expense_id"];
        $name = $_POST["name"];
        $amount = $_POST['amount'];
        $category = $_POST["category"];
        $person = $_POST["person"];
        $purpose = $_POST["purpose"];
        $query = "UPDATE expenses SET"
                . " name='" . mysqli_real_escape_string($con, $name) . "',"
                . "amount= '" . mysqli_real_escape_string($con, $amount) . "',"
                . "category= '" . mysqli_real_escape_string($con, $category) . "',"
                . "person= '" . mysqli_real_escape_string($con, $person) . "',"
                . "purpose= '" . mysqli_real_escape_string($con, $purpose) . "'"
                . " WHERE id='{$expense_id}'";
        $results = mysqli_query($con, $query) or die(mysqli_error($con) . $query);
        if ($results)
            $data = array('success' => true, 'name' => $_POST['name'],
                'amount' => $_POST['amount'], 'category' => getlookupname($_POST['category'], $con), 'person' => getpersonsname($_POST['person'], $con));
        else
            $data = array('success' => false);
        echo json_encode($data);
        break;
    case 46:
        $id = $_GET["id"];
        header("Content-type:text/xml");
        $qry = "SELECT * from expenses WHERE id='$id' ";
        $res = mysqli_query($con, $qry) or die(mysqli_error() . $qry);
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<data>";
            echo "<name><![CDATA[{$row["name"]}]]></name>";
            echo "<amount><![CDATA[{$row["amount"]}]]></amount>";
            echo "<date><![CDATA[{$row["date"]}]]></date>";
            echo "<category><![CDATA[{$row["category"]}]]></category>";
            echo "<purpose><![CDATA[{$row["purpose"]}]]></purpose>";
            echo "<person><![CDATA[" . getpersonsname($row["person"], $con) . "]]></person>";
            echo "</data>";
        }
        break;
    case 47://selects expenditure category records
        header("Content-type:text/xml");
        print("<?xml version = \"1.0\"?>");
        $qry = "SELECT * from expenses where category='" . $_GET['category_id'] . "'";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
        echo '<rows>';
        while ($row = mysqli_fetch_assoc($res)) {
            echo '<row id="' . $row["id"] . '">';
            echo '<cell><![CDATA[]]></cell>';
            echo '<cell><![CDATA[' . $row["name"] . ']]></cell>';
            echo '<cell><![CDATA[' . $row["amount"] . ']]></cell>';
            echo '<cell><![CDATA[' . ((isset($row['person'])) ? getlookupname($row['person'], $con) : '') . ']]></cell>';
            echo '<cell><![CDATA[' . ((isset($row['category'])) ? getlookupname($row['category'], $con) : '') . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 48:
        $month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_NUMBER_INT) ?: date('m');
        $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_NUMBER_INT) ?: 2016;
        header("Content-type:text/xml");
        echo "<rows>";
        $query = "SELECT 
                    `date`,
                     SUM(men) -( SELECT sum(amount)  FROM `expenses`  where category=25  ) men,
                     SUM(women) -( SELECT sum(amount)  FROM `expenses`  where category=26  ) women,
                     SUM(youth) -( SELECT sum(amount)  FROM `expenses`  where category=23  ) youth,
                     SUM(children) -( SELECT sum(amount)  FROM `expenses`  where category=24  ) children,
                    SUM(tithe) +SUM(thanks)+SUM(seed)-( SELECT sum(amount)  FROM `expenses`  where category =27 ) offerings
                    FROM  offering   WHERE MONTH(`date`)=$month AND YEAR(`date`)=$year
         GROUP BY `date` 
ORDER BY  `date`  DESC";
        $result = mysqli_query($con, $query) or die(mysqli_error());
        while ($row = mysqli_fetch_assoc($result)) {


            echo '<row id="' . $row["id"] . '">';
            echo '<cell><![CDATA[' . $row["date"] . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["men"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["women"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["youth"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["children"]) . ']]></cell>';
            echo '<cell><![CDATA[' . numberformart($row["offerings"]) . ']]></cell>';
            echo '</row>';
        }
        echo '</rows>';
        break;
    case 50:
        $query = "SELECT SUM(tithe) tithe, sum(welfare) welfare,sum(first_fruit) fruit ,SUM(love_offering) love ,SUM(evangelism) evang,SUM(seed) seed,SUM(others) others,SUM(thanksgiving) thanks,SUM(paster_blessing) bless ,(SUM(tithe)+ SUM(first_fruit) +sum(welfare)+SUM(love_offering) + SUM(evangelism)+ SUM(seed) +SUM(others) +SUM(thanksgiving)+SUM(paster_blessing) ) total FROM  finances
 WHERE reg_no={$id}";
        $result = mysqli_query($con, $query) or die(mysqli_error());
        $row = mysqli_fetch_assoc($result);
        $men = numberformart($row['men']);
        $women = numberformart($row['women']);
        $youth = numberformart($row['youth']);
        $children = numberformart($row['children']);
        $total = numberformart($row["total"]);
        $data['data'] = array('success' => true, 'men' => $tithe, 'women' => $women, 'youth' => $youth, 'children' => $children, 'total' => $total);
        echo json_encode($data);
        break;
    case 51:
        $assests_id = $_POST['assests_id'] ?: 0;
        $manual = $_POST['manual'] ?: '';
        $qry = "UPDATE assests SET  manual='".mysqli_real_escape_string($con,$manual)."' WHERE id={$assests_id}";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
        $msg = "Updated Successfully!";
        echo json_encode(array("response" => $msg));
        break;
    case 52:
        $id = $_GET['id'];
        $qry = "SELECT manual  FROM `assests` WHERE id={$id}";
        $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
        $row = mysqli_fetch_array($res);
        if ($res) {
            $msg = "success";
        } else {
            $msg = mysqli_error($con);
        }
        echo json_encode(array("response" => $row["manual"]?:''));
        break;
}

function numberformart($number) {
    return number_format((float) $number, 2, '.', '');
}

function fetchMenuDirectories($id, $con) {
    $qry = "SELECT id,name FROM `groups`  WHERE parent =" . $id . " order by id asc";
    $res = mysqli_query($con, $qry) or die(mysqli_error());
    while ($row = mysqli_fetch_assoc($res)) {
        $id = $row['id'];
        $Name = htmlspecialchars($row['name'], ENT_QUOTES, "utf-8");
        print("<item  text=' " . $Name . "' id='" . $id . "' checked='1' >" . "\n");
        fetchMenuDirectories($id, $con);
        print("</item>\n");
    }
}

function fetchMenuDirectoriesgroups($id, $con, $groups) {
    $qry = "SELECT id,name FROM `groups`  WHERE parent =" . $id . " AND id IN($groups) order by id asc";
    $res = mysqli_query($con, $qry) or die(mysqli_error());
    while ($row = mysqli_fetch_assoc($res)) {
        $id = $row['id'];
        $Name = htmlspecialchars($row['name'], ENT_QUOTES, "utf-8");
        print("<item  text=' " . $Name . "' id='" . $id . "' checked='1' >" . "\n");
        fetchMenuDirectories($id, $con, $groups);
        print("</item>\n");
    }
}

function getlookupname($value, $con) {
    $qrylookup = "SELECT item_name FROM lookup WHERE  item_id={$value}";
    $qryres = mysqli_query($con, $qrylookup) or die(mysqli_error($con) . $qrylookup);
    $row = mysqli_fetch_assoc($qryres);
    return $row['item_name'];
}

function getmarital($value, $con) {
    $$value = $value ? $value : 0;
    $qry = "SELECT  cell_name FROM home_cell WHERE  id={$value}";
    $res = mysqli_query($con, $qry) or die(mysqli_error($con) . $qry);
    $row = mysqli_fetch_assoc($res);
    return $row['cell_name'];
}

function getpersonsname($id, $con) {
    $qrycoursername = "SELECT name FROM members WHERE  reg_no='{$id}'";

    $qrylectname = mysqli_query($con, $qrycoursername) or die(mysqli_error() . $qrycoursername);
    $row = mysqli_fetch_assoc($qrylectname);
    return $row['name'];
}

function getlookupid($item_name) {
    $qrylookup = "SELECT item_id FROM lookup WHERE  item_name='{$item_name}'";

    $qryres = mysqli_query($qrylookup) or die(mysqli_error() . $qrylookup);
    $row = mysqli_fetch_assoc($qryres);
    return $row['item_id'];
}

function clean_this($cName) {
    $strout = null;

    for ($i = 0; $i < strlen($cName); $i++) {
        $ord = ord($cName[$i]);

        if (($ord > 0 && $ord < 32) || ($ord >= 127)) {
            $strout .= "&#{$ord};";
        } else {
            switch ($cName[$i]) {
                case '<':
                    $strout .= '&lt;';
                    break;
                case '>':
                    $strout .= '&gt;';
                    break;
                case '&':
                    $strout .= '&amp;';
                    break;
                case '"':
                    $strout .= '&quot;';
                    break;
                case '\'':
                    $strout .= '&apos;';
                    break;
                case '½':
                    $strout .= '&frac12';
                    break;
                default:
                    $strout .= $cName[$i];
            }
        }
    }
    return strip_tags($strout);
}
