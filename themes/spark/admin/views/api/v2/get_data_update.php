<?php


$response = array();




$db=mysql_pconnect("localhost","root","");
mysql_select_db("ffa_db");
mysql_query("SET NAMES 'cp1256'",$db);
mysql_query('SET CHARACTER SET cp1256',$db);
$access_code= @$_POST["access_code"];

$s_name="";
		            $q=mysql_query("SELECT s_name FROM supervisors where access_code='".$access_code."'");
                        $no=mysql_num_rows($q);

                        for($i=0;$i<$no;$i++){
                        $row=mysql_fetch_array($q);







$s_name = iconv('windows-1256', 'utf-8', $row["s_name"]);
       
}






		            $q=mysql_query("SELECT * FROM workers where s_name='".$s_name."'");
                        $no=mysql_num_rows($q);
if($no>0){
$response["workers"] = array();
    
    

                        for($i=0;$i<$no;$i++){
                        $row=mysql_fetch_array($q);




$product = array();
        



$product["w_no"] = iconv('windows-1256', 'utf-8', $row["w_no"]);
        
$product["w_name"] = iconv('windows-1256', 'utf-8', $row["w_name"]);
        
$product["id_type"] = iconv('windows-1256', 'utf-8', $row["id_type"]);
        
$product["id_no"] = iconv('windows-1256', 'utf-8', $row["id_no"]);
        
$product["cp_id"] = iconv('windows-1256', 'utf-8', $row["cp_id"]);
 
 
$product["gov"] = iconv('windows-1256', 'utf-8', $row["gov"]);
        
$product["district"] = iconv('windows-1256', 'utf-8', $row["district"]);
 
 
$product["asset"] = iconv('windows-1256', 'utf-8', $row["asset"]);       
$product["s_name"] = iconv('windows-1256', 'utf-8', $row["s_name"]);        
$product["tel_no"] = iconv('windows-1256', 'utf-8', $row["tel_no"]);        
$product["card_img"] =  iconv('windows-1256', 'utf-8', $row["card_img"]);



        



// push single product into final response array
        
array_push($response["workers"], $product);
    

                        

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