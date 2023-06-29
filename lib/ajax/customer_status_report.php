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

if($_POST["customer_ID"]){
    
	$productionorder_table = $tablePrefix . 'productionorder';
	$dispatchsupply_table = $tablePrefix . 'dispatchsupply';
   
    
    $entityID = $ajxSess->get('user')['entity_ID'];
    
  
    $sql_query ="SELECT $productionorder_table.ID,$productionorder_table.PdnOrderNo FROM $productionorder_table 
                 LEFT JOIN $dispatchsupply_table ON $dispatchsupply_table.ProductionID=$productionorder_table.ID 
                 WHERE $dispatchsupply_table.CustomerID=".$_POST['customer_ID']."";

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