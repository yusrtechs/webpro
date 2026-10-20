<?php


/*
 * Following code will list all the products
 */


// array for JSON response

$response = array();



// include db connect class

require_once __DIR__ . '/db_connect.php';


// connecting to db

$db = new DB_CONNECT();


// get all products from products table

$result = mysql_query("SELECT *FROM supervisors") or die(mysql_error());


// check for empty 
result
if (mysql_num_rows($result) > 0) {
    
// looping through all results
    
// products node
    
$response["products"] = array();
    
    
while ($row = mysql_fetch_array($result)) {
        
// temp user array
        
$product = array();
        
$product["s_no"] = $row["s_no"];
        
$product["s_name"] = $row["s_name"];
        
$product["s_title"] = $row["s_title"];
        
$product["gov"] = $row["gov"];
        
$product["district"] = $row["district"];
        
$product["asset"] = $row["asset"];



        
$product["tel_no"] = $row["tel_no"];



        
$product["s_img"] = $row["s_img"];



        
$product["access_code"] = $row["access_code"];



        
// push single product into final response array
        
array_push($response["products"], $product);
    
}
    
// success
    
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
