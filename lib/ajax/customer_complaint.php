<?php

/*
 * Production - linux
 */
include_once'./set_inc_path.php';

include_once 'util/ses.php';
$ajxSess = new Session();


include_once 'PhpRbac/database/database.config';

try {
    $Db = new PDO("mysql:host={$host};dbname={$dbname}", $user, $pass);
} catch (Exception $exc) {
    
}


header('Content-Type: application/json');


/*
 * make sures you can call only from the host we set
 * and only post request
 * so you cant access the script directly from url
 */

if($_POST["ColumnValue"]){
    
	$customer_table = $tablePrefix . 'customer';
	$enquiry_table = $tablePrefix . 'enquiry';
	$customerorder_table = $tablePrefix . 'customerorder';
	$customerorder_detail_table = $tablePrefix . 'customerorder_detail';
	$product_table = $tablePrefix . 'product';
    
    $entityID = $ajxSess->get('user')['entity_ID'];
    
  
    $sql_query ="SELECT $product_table.ID,$customer_table.PermntAddress1,$product_table.ProductName FROM $customer_table 
	             INNER JOIN $enquiry_table ON $enquiry_table.customer_ID=$customer_table.ID 
				 INNER JOIN $customerorder_table ON $customerorder_table.enquiry_ID=$enquiry_table.ID
				 INNER JOIN $customerorder_detail_table ON $customerorder_detail_table.custorder_ID=$customerorder_table.ID
				 INNER JOIN $product_table ON $customerorder_detail_table.Product_ID=$product_table.ID
				 WHERE $customer_table.ID=".$_POST['ColumnValue']."";

    	try {
                $stmt = $Db->prepare($sql_query);
              // var_dump($sql_query);
                if ($stmt->execute()) {

                $Data_rows = $stmt->fetchAll(2);
               
                if($Data_rows){
		            http_response_code();		                 
                 echo json_encode($Data_rows);
                 }else{
        	 	http_response_code(204);
        	 	echo json_encode(array('noData' => 'noData'));
    		    }   
                    
                } else {
                  http_response_code(204);
                }
            } catch (Exception $exc) {
              http_response_code(500);
            }

}