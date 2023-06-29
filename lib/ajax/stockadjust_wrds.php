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
    
	
	// $supplier_table = $tablePrefix . 'supplier';
  $productionorder_table = $tablePrefix . 'productionorder';
	$workorder_table = $tablePrefix . 'workorder';


	
   
    
    $entityID = $ajxSess->get('user')['entity_ID'];
    
  
     
  $sql_query ="SELECT $workorder_table.ID, $workorder_table.WorkOrderNo FROM $productionorder_table, $workorder_table 
                WHERE 
                      $productionorder_table.ID = $workorder_table.PdnOrderID  AND 
                      $productionorder_table.ID=".$_POST['ColumnValue'].""; 

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