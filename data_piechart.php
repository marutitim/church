<?php

require_once 'config/config.php';
switch (@$_GET['case']) {

    default:
  @$date=$_GET['date']?:date('Y-m-d');
   echo "<data>";
        $qry = "SELECT men,women,youth, children,tithe,thanks,seed FROM offering  WHERE `date` LIKE '%$date%' ";
        $result = mysqli_query($con, $qry) or die(mysqli_error());   
        $row = mysqli_fetch_assoc($result);
        $total= $row["men"]+$row["women"]+ $row["youth"]+$row["children"]+$row['tithe']+$row['thanks']+$row['thanks'];
        $men= ($row['men']/$total) *100;
        $women = ($row['women']/$total) *100;
        $youth = ($row['youth']/$total) *100;
        $children = ($row['children']/$total) *100;      
        $tithe = ($row['tithe']/$total) *100;  
        $thanks = ($row['thanks']/$total) *100;  
        $seed= ($row['seed']/$total) *100;  
       echo "<item id='01'>
		<sales>".$men."</sales><year>Men</year>
	</item>
	<item id='11'>
		<sales>".$women."</sales><year>Women</year>
	</item>
	<item id='21'>
		<sales>".$youth."</sales><year>Youth</year>
	</item>
	<item id='31'>
		<sales>".$children."</sales><year>Children</year>
	</item>
       <item id='41'>
		<sales>".$tithe."</sales><year>Tithe</year>
	</item>
         <item id='51'>
		<sales>".$thanks."</sales><year>Thanksgiving</year>
	</item>
         <item id='61'>
		<sales>".$seed."</sales><year>Prophetic Seed</year>
	</item>";
            
	 
echo "</data>";


        break;


}
?>
