<?php


$response = array();




$db=mysql_pconnect("localhost","root","");
mysql_select_db("ffa_db");
mysql_query("SET NAMES 'cp1256'",$db);
mysql_query('SET CHARACTER SET cp1256',$db);


		            $q=mysql_query("SELECT * FROM supervisors");
                        $no=mysql_num_rows($q);
if($no>0){
$response["supervisors"] = array();
    
    

                        for($i=0;$i<$no;$i++){
                        $row=mysql_fetch_array($q);




$product = array();
        



$product["s_no"] = iconv('windows-1256', 'utf-8', $row["s_no"]);
        
$product["s_name"] = iconv('windows-1256', 'utf-8', $row["s_name"]);
        
$product["s_title"] = iconv('windows-1256', 'utf-8', $row["s_title"]);
        
$product["gov"] = iconv('windows-1256', 'utf-8', $row["gov"]);
        
$product["district"] = iconv('windows-1256', 'utf-8', $row["district"]);
 
 
$product["asset"] = iconv('windows-1256', 'utf-8', $row["asset"]);       
$product["tel_no"] = iconv('windows-1256', 'utf-8', $row["tel_no"]);        
$product["s_img"] =  iconv('windows-1256', 'utf-8', $row["s_img"]);



        
$product["access_code"] = iconv('windows-1256', 'utf-8', $row["access_code"]);







        



// push single product into final response array
        
array_push($response["supervisors"], $product);
    

                        

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