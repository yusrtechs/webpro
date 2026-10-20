<?php


$response = array();




$db=mysql_pconnect("localhost","root","");
mysql_select_db("ffa_db");
mysql_query("SET NAMES 'cp1256'",$db);
mysql_query('SET CHARACTER SET cp1256',$db);
$access_code= @$_POST["access_code"];
$message= @$_POST["message"];





$tr_d=date("d");
$tr_m=date("m");
$tr_y=date("Y");


$s_no="";
		            $q=mysql_query("SELECT s_no FROM supervisors where access_code='".$access_code."'");
                        $no=mysql_num_rows($q);

                        for($i=0;$i<$no;$i++){
                        $row=mysql_fetch_array($q);







$s_no = iconv('windows-1256', 'utf-8', $row["s_no"]);
       
}







$length = count($message);
if($length>0){
$response["workers"] = array();
    
    

for ($i = 0; $i < $length; $i++) {

    $result = mysql_query("INSERT INTO trans(w_no, s_no, tr_d, tr_m, tr_y) VALUES('".$message[$i]."', '".$s_no."', '".$tr_d."', '".$tr_m."', '".$tr_y."')");
    if ($result) {
$product = array();
        



$product["w_no"] = iconv('windows-1256', 'utf-8', $message[$i]);
        

array_push($response["workers"], $product);
    

}

}
$response["success"] = 1;

    
// echoing JSON response
    
echo json_encode($response);


}
else {
    
// no products found
    
$response["success"] = 0;
    
// echo no users JSON
    
echo json_encode($response);

}






        
?>