<?php


/*
 * Following code will list all the products
 */


// array for JSON response
 header('Content-Type: text/html; charset=utf-8');
echo $_POST['data']."ppp";
$response = array();



// include db connect class

require_once __DIR__ . '/db_connect.php';


// connecting to db

$db = new DB_CONNECT();

mysql_query("SET NAMES 'utf8'");
mysql_query('SET CHARACTER SET utf8');

// get all products from products table

$result = mysql_query("SELECT *FROM sma_warehouses") or die(mysql_error());


// check for empty result
if (mysql_num_rows($result) > 0) {
    
// looping through all results
    
// products node
    
$response["products"] = array();
    
    
while ($row = mysql_fetch_array($result)) {
        
// temp user array
        
$product = array();
        
$product["code"] = $row["code"];
        
$product["name"] = $row["name"];
        
$product["phone"] = $row["phone"];
        
$product["email"] = $row["email"];
        
$product["address"] = $row["address"];
        
$product["price_group_id"] = $row["price_group_id"];



        
$product["map"] = $row["map"];



        
//$product["s_img"] = $row["s_img"];



        
//$product["access_code"] = $row["access_code"];


$result1 = mysql_query("INSERT INTO sma_warehouses_temp(code, name, phone,email,address,price_group_id,map) VALUES('".$product["code"]."','".$product["name"]."','".$product["phone"]."','".$product["email"]."','".$product["address"]."','".$product["price_group_id"]."','".$product["map"]."')");

        
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
