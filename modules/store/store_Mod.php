<?php

/**
 * Description of Purchase_Mod
 *
 * @author psmahadevan
 */
class store_Mod {

    private $crg;
    private $ses;
    private $db;
    private $sd;
    private $tpl;
    private $rbac;

    public function __construct($reg = NULL) {
        /*
         * Receiving $rg array
         */
        $this->crg = $reg;

        /*
         * geting object from reg array
         */
        $this->ses = $this->crg->get('ses');
        $this->db = $this->crg->get('db');
        $this->sd = $this->crg->get('SD');
        $this->tpl = $this->crg->get('tpl');
        $this->rbac = $this->crg->get('rbac');
    }



///////////////////////////////////////
//////////////////////////////////////
//////////////////////////////// material_inward



function materialinward(){
     
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
         
        /////////////////////////////////////////////////////////////////
        //////////////////////////////access condition applied///////////
        /////////////////////////////////////////////////////////////////
        
        include_once 'util/DBUTIL.php';
        $dbutil = new DBUTIL($this->crg);
        
        include_once 'util/genUtil.php';
        $util = new GenUtil();
         
        $entityID = $this->ses->get('user')['entity_ID'];
        $userID = $this->ses->get('user')['ID'];
        $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
        $unit_table = $this->crg->get('table_prefix') . 'unit';
        $po_detail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
        $po_master_tab = $this->crg->get('table_prefix') . 'purchaseorder';
        $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
        $indent_master_tab = $this->crg->get('table_prefix') . 'purchaseindent';
        $indent_det_tab = $this->crg->get('table_prefix') . 'purchaseindentdetail';
        $state_tab = $this->crg->get('table_prefix') . 'state';
        $unit_tab = $this->crg->get('table_prefix') . 'unit';
        $material_inward_tab = $this->crg->get('table_prefix') . 'material_inward';
        $material_inward_detail_tab = $this->crg->get('table_prefix') . 'material_inward_detail';
        $stock_tab = $this->crg->get('table_prefix') . 'stock';
        
        //indent table data
       
        $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1 and $indent_master_tab.PI_Status=1 ORDER BY $indent_master_tab.ID DESC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $indent_data  = $stmt->fetchAll();	
        $this->tpl->set('indent_data', $indent_data);


         //PURCHASE  Order table data
       
         $sql = "SELECT ID, PurchaseOrderNo, purchaseindent_ID FROM $po_master_tab WHERE  $po_master_tab.Status=1 AND $po_master_tab.PU_Status=1 AND entity_ID = $entityID ORDER BY $po_master_tab.ID DESC";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();             
         $po_master_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('po_master_tab', $po_master_tab_data);   
         
         $sql = "SELECT ID, PurchaseOrderNo, purchaseindent_ID FROM $po_master_tab WHERE  $po_master_tab.Status=1 ORDER BY $po_master_tab.ID DESC";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();             
         $po_master_tab_dataa  = $stmt->fetchAll();	
         $this->tpl->set('po_master_taba', $po_master_tab_dataa);  
        
        //supplier table data
       
        $sql = "SELECT ID,SupplierName FROM $supplier_tab ORDER BY $supplier_tab.SupplierName ASC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $supplier_data  = $stmt->fetchAll();	
        $this->tpl->set('supplier_data', $supplier_data);
        
        //rawmaterial table data
       
        $sql = "SELECT ID,RMName FROM $rawmaterial_table";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $rawmaterial_data  = $stmt->fetchAll();	
        $this->tpl->set('rawmaterial_data', $rawmaterial_data);
        
        //unit table data
       
        $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
        $stmt = $this->db->prepare($pdt_sql);            
        $stmt->execute();
        $unit_data  = $stmt->fetchAll();	
        $this->tpl->set('unit_data', $unit_data);
 
        $this->tpl->set('page_title', 'Material Inward');	          
        $this->tpl->set('page_header', 'Store');
        
        //Add Role when u submit the add role form
        $thisPageURL = $this->crg->get('route')['base_path'] . '/' . $this->crg->get('route')['module'] . '/' . $this->crg->get('route')['controller'] . '/' . $this->crg->get('route')['action'];

        $crud_string = null;

        if (isset($_POST['req_from_list_view'])) {
            $crud_string = strtolower($_POST['req_from_list_view']);
        }              
        
        //Edit submit
        if (!empty($_POST['edit_submit_button']) && $_POST['edit_submit_button'] == 'edit') {
            $crud_string = 'editsubmit';
        }
        
        //Add submit
        if (!empty($_POST['add_submit_button']) && $_POST['add_submit_button'] == 'add') {
            $crud_string = 'addsubmit';
        }
        

        switch ($crud_string) {
             case 'delete':                    
                  $data = trim($_POST['ycs_ID']);
                   
                   
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                   
                }
               
                $sqlsrr = "SELECT * FROM $material_inward_tab WHERE ID=$data";
                $material_inward_data = $dbutil->getSqlData($sqlsrr); 
                
                $sqlsrr = "SELECT * FROM  $material_inward_detail_tab WHERE material_inward_id=$data";
                $material_det_data = $dbutil->getSqlData($sqlsrr);    
                
                $purchase_no=(count($material_inward_data)>0)?$material_inward_data[0]['PurchaseNO']:'';        
                 
                $sqlsrr = "SELECT  * FROM  $po_detail_tab WHERE $po_detail_tab.purchaseorder_ID = '$purchase_no'";
                $po_det_data = $dbutil->getSqlData($sqlsrr);
                
                 
                 if(!empty($purchase_no)){
                     $sql_update="Update $po_master_tab SET $po_master_tab.PU_Status=1 WHERE  $po_master_tab.ID=$purchase_no";
                                 $masterstmt1 = $this->db->prepare($sql_update);
                                 $masterstmt1->execute();

                                 
                    foreach($material_det_data as $k=>$v){

                        // echo '<pre>';
                        // print_r($v); 

                        foreach($po_det_data as $kk=>$value){

                            // echo '<pre>';
                            // print_r($value); die;
                          
                            if(($value['rawmaterial_ID']==$v['item_id'])){
                                
                                $sql_updates="Update $po_detail_tab SET $po_detail_tab.ItemStatus=1,$po_detail_tab.poquantity_stat=(poquantity_stat+$v[received_qty]) WHERE $po_detail_tab.ID=$value[ID]";
                                $detstmt1 = $this->db->prepare($sql_updates);
                              $detstmt1->execute();
                            }
                            
                        }
                        
                    }
                                 
                      
                 }
                 
                $sqldetdelete="DELETE $material_inward_detail_tab,$material_inward_tab FROM $material_inward_tab
                               LEFT JOIN  $material_inward_detail_tab ON $material_inward_tab.ID=$material_inward_detail_tab.material_inward_id 
                               WHERE $material_inward_tab.ID=$data";  
                $stmt = $this->db->prepare($sqldetdelete);            
                    
                    if($stmt->execute()){
                    $this->tpl->set('message', 'Material Inward deleted successfully');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/materialinward');
                    // $this->tpl->set('label', 'List');
                    // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    } 
                    
        break;
            case 'view':                    
                $data = trim($_POST['ycs_ID']);
             
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to view!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', 'view');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         
                            
                //  $sqlsrr = "SELECT  $po_master_tab.*,
                //                    $po_detail_tab.*,
                //                    $rawmaterial_table.RMName,
                //                    $supplier_tab.SupplierName,
                //                    $supplier_tab.AddressLine1,
                //                    $supplier_tab.AddressLine2,
                //                    $supplier_tab.City,
                //                    $state_tab.StateName,
                //                    $unit_table.UnitName
                //            FROM  $po_master_tab
                //            LEFT JOIN $po_detail_tab ON $po_master_tab.ID=$po_detail_tab.purchaseorder_ID
                //            LEFT JOIN $rawmaterial_table ON  $po_detail_tab.rawmaterial_ID=$rawmaterial_table.ID
                //            LEFT JOIN $supplier_tab  ON $supplier_tab.ID=$po_master_tab.supplier_ID 
                //            LEFT JOIN $state_tab  ON $state_tab.ID=$supplier_tab.State_ID 
                //            LEFT JOIN $unit_table  ON $unit_table.ID=$po_detail_tab.unit_ID 
                //            WHERE $po_master_tab.ID = '$data' 
                //            ORDER BY $po_detail_tab.ID ASC";
                // $indent_data = $dbutil->getSqlData($sqlsrr); 

                $sqlsrr="SELECT $material_inward_tab.*,$material_inward_detail_tab.*,
                $rawmaterial_table.RMName, 
                $unit_table.UnitName,
                $po_detail_tab.poquantity_stat 

        FROM    $material_inward_tab,
                $material_inward_detail_tab,
                $rawmaterial_table,
                $unit_table,
                $po_master_tab,
                $po_detail_tab

         WHERE  $material_inward_tab.ID = $material_inward_detail_tab.material_inward_id
         AND    $material_inward_detail_tab.item_id = $rawmaterial_table.ID   
         AND    $material_inward_detail_tab.unit = $unit_table.ID        
         AND    $material_inward_tab.PurchaseNO = $po_master_tab.ID      
         AND    $po_master_tab.ID = $po_detail_tab.purchaseorder_ID 
         AND    $material_inward_detail_tab.item_id = $po_detail_tab.rawmaterial_ID 
         AND    $material_inward_tab.ID = $data";        
  
                
                $indent_data = $dbutil->getSqlData($sqlsrr);              
              
                $sql = "SELECT ID, PurchaseOrderNo, purchaseindent_ID FROM $po_master_tab WHERE  $po_master_tab.Status=1  ORDER BY $po_master_tab.ID DESC";
                $stmt = $this->db->prepare($sql);            
                $stmt->execute();             
                $po_master_tab_dat  = $stmt->fetchAll();	
                $this->tpl->set('po_master_tab', $po_master_tab_dat);
                        
               
                
                
                //edit option     
                $this->tpl->set('message', 'You can view Material Inward form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/material_inward.php'));                    
                break;
            
            case 'edit':                    
                $data = trim($_POST['ycs_ID']);
                $mode='edit';
                if(isset($_SESSION['ycs_ID']))
                {
                    $data = trim($_SESSION['ycs_ID']);
                    unset($_SESSION['ycs_ID']);
                    $mode='Confirm';
                }
           // var_dump($data);
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to edit!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', $mode);
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         
                            
                // $sqlsrr = "SELECT  *,$enquiry_tab.PONo,
                //            FROM  $po_master_tab
                //            LEFT JOIN $po_detail_tab ON $po_master_tab.ID=$po_detail_tab.purchaseorder_ID
                //            LEFT JOIN $rawmaterial_table ON  $po_detail_tab.rawmaterial_ID=$rawmaterial_table.ID
                //            LEFT JOIN $supplier_tab  ON $supplier_tab.ID=$po_master_tab.supplier_ID 
                //            LEFT JOIN $state_tab  ON $state_tab.ID=$supplier_tab.State_ID 
                //            LEFT JOIN $unit_table  ON $unit_table.ID=$po_detail_tab.unit_ID 
                //            WHERE $po_master_tab.ID = '$data' 
                //            ORDER BY $po_detail_tab.ID ASC";


                $sqlsrr="SELECT $material_inward_tab.*,$material_inward_detail_tab.*,
                                    $rawmaterial_table.RMName, 
                                    $unit_table.UnitName,
                                    $po_detail_tab.poquantity_stat 

                            FROM    $material_inward_tab,
                                    $material_inward_detail_tab,
                                    $rawmaterial_table,
                                    $unit_table,
                                    $po_master_tab,
                                    $po_detail_tab

                             WHERE  $material_inward_tab.ID = $material_inward_detail_tab.material_inward_id
                             AND    $material_inward_detail_tab.item_id = $rawmaterial_table.ID   
                             AND    $material_inward_detail_tab.unit = $unit_table.ID        
                             AND    $material_inward_tab.PurchaseNO = $po_master_tab.ID      
                             AND    $po_master_tab.ID = $po_detail_tab.purchaseorder_ID 
                             AND    $material_inward_detail_tab.item_id = $po_detail_tab.rawmaterial_ID 
                             AND    $material_inward_tab.ID = $data";

                $indent_data = $dbutil->getSqlData($sqlsrr);               
                $indentsel_data  = $stmt->fetchAll();	
                $this->tpl->set('indent_data', $indentsel_data);
            
                //edit option     
                $this->tpl->set('message', 'You can edit Material Inward form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/material_inward.php'));                    
                break;
            
            case 'editsubmit':             
                $data = trim($_POST['ycs_ID']);
              

                //mode of form submit
                $this->tpl->set('mode', 'edit');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);

                //Post data
                include_once 'util/genUtil.php';
                $util = new GenUtil();
                $form_post_data = $util->arrFltr($_POST);

                //      echo '<pre>';
                //    print_r($form_post_data); die;
                    
                // $sqldet_del = "DELETE FROM $po_detail_tab WHERE purchaseorder_ID=$data";
                // $stmt = $this->db->prepare($sqldet_del);
                // $stmt->execute();   
                        
                        try{
                            
                                                    
                    
        
                        
                        $MaterialNo= $form_post_data['MaterialNo'];
                        $PurchaseNO= $form_post_data['PurchaseNO'];
                       
                        // $Purchase_Indent_No= $form_post_data['Purchase_Indent_No'];
                        $supplier_ID= $form_post_data['supplier_ID']; 
                        $Supplier_Name= $form_post_data['Supplier_Name'];      
                        $date=!empty($form_post_data['Date'])?date("Y-m-d", strtotime($form_post_data['Date'])):'';
                        
                        $sql_update="Update $material_inward_tab SET  Date='$date', 
                                                                      MaterialNo='$MaterialNo',
                                                                      PurchaseNO='$PurchaseNO',
                                                                      supplier_ID='$supplier_ID',
                                                                      Supplier_Name='$Supplier_Name',
                                                                      trans_date=CURDATE()
                                                                      WHERE  ID=$data"; 

                        $stmt1 = $this->db->prepare($sql_update);
                        $stmt1->execute();

                        
						 $sql3 = "DELETE FROM $material_inward_detail_tab WHERE material_inward_id=$data";
					     $stmt3 = $this->db->prepare($sql3);

                    if($stmt3->execute()){
                        
            //    echo         $form_post_data['maxCount'];    die;

                    FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                            
                            $invoice_date=!empty($form_post_data['invoice_date_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['invoice_date_'. $entry_count])):'';
                            $itemid =$form_post_data['item_id_' . $entry_count]; 
                            $poqty =$form_post_data['po_qty_' . $entry_count]; 
                            $pendingqt =$form_post_data['pending_qty_' . $entry_count];
                            $rqt =$form_post_data['received_qty_' . $entry_count];								
                            $unit =$form_post_data['unit_' . $entry_count];
                            $receivedqt =$form_post_data['received_qt_in_kg_' . $entry_count];
                            $batch =$form_post_data['batch_no_' . $entry_count];
                            $costunit =$form_post_data['cost_unit_' . $entry_count];
                            $total =$form_post_data['total_' . $entry_count];
                            $supplier =$form_post_data['supplier_invoice_no_' . $entry_count];
                           
                            if(!empty($total)){
                           
                          

                                    $vals = "'" . $data . "'," .
                                    "'" . $itemid . "'," .
                                    "'" . $poqty . "'," .
                                    "'" . $pendingqt . "'," .
                                    "'" . $rqt . "'," .
                                    "'" . $unit . "'," .
                                    "'" . $receivedqt . "'," .
                                    "'" . $batch . "'," .
                                    "'" . $costunit . "'," .
                                    "'" . $total . "'," .
                                    "'" . $supplier . "'," .
                                    "'" . $invoice_date . "'" ;   

                            $sql2 = "INSERT INTO $material_inward_detail_tab
                                    ( 

                                        `material_inward_id`, 
                                        `item_id`,
                                        `po_qty`,
                                        `pending_qty`,
                                        `received_qty`,
                                        `unit`,
                                        `received_qt_in_kg`,
                                        `batch_no`,
                                        `cost_unit`,
                                        `total`,
                                        `supplier_invoice_no`,
                                        `invoice_date`
                                    ) 
                            VALUES ($vals)";        

                            $stmt = $this->db->prepare($sql2);
                            $stmt->execute();               
                        }
                        }           

                        
                    }
                                    
                        $this->tpl->set('message', 'Material Inward form edited successfully!');   
                        header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/materialinward');
                       
                        } catch (Exception $exc) {
                         //edit failed option
                        $this->tpl->set('message', 'Failed to edit, try again!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/material_inward.php'));
                        }

                break;
                
            case 'addsubmit':
                
                if (isset($crud_string)) {
                     
                    $form_post_data = $dbutil->arrFltr($_POST);
                    // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                    // $delivery_date=!empty($form_post_data['DeliveryDate'])?date("Y-m-d", strtotime($form_post_data['DeliveryDate'])):'';
                    // "'" . $dispatch_date . "'," .
                    $PurchaseNO=$form_post_data['PurchaseNO'];
                    $supplier_id= $form_post_data['supplier_ID'];
                //     echo '<pre>';
                //    print_r($form_post_data); die;
                $date=!empty($form_post_data['Date'])?date("Y-m-d", strtotime($form_post_data['Date'])):'';
                    
                            $val =  "'" . $date . "'," .
                                    "'" . $form_post_data['MaterialNo'] . "'," .                                  
                                    // "'" . $form_post_data['PurchaseNO'] . "'," .
                                    "'" . $PurchaseNO . "'," .
                                   
                                    "'" . $form_post_data['supplier_ID'] . "'," .
                                    "'" . $form_post_data['Supplier_Name'] . "'," .
                                            "CURDATE()," .
                                    "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                    "'" .  $this->ses->get('user')['ID'] . "'";

                         $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "material_inward`
                                        ( 
                                        `Date`,
                                        `MaterialNo`,
                                        `PurchaseNO`,
                                        `supplier_ID`,
                                        `SupplierName`,
                                        `trans_date`,
                                        `entity_ID`,
                                        `users_ID`
                                        ) 
                                VALUES ( $val )";         
                                $stmt = $this->db->prepare($sql);       
                    if ($stmt->execute()) { 
                    
                    $lastInsertedID = $this->db->lastInsertId();
                      
                    $count=0;
                    $countt=0;
                
                  FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {

                             $invoice_date=!empty($form_post_data['invoice_date_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['invoice_date_'. $entry_count])):'';
                            $itemid =$form_post_data['item_id_' . $entry_count]; 
                            $poqty =$form_post_data['po_qty_' . $entry_count]; 
                            $pendingqt =$form_post_data['pending_qty_' . $entry_count];
                            $rqt =$form_post_data['received_qty_' . $entry_count];								
                            $unit =$form_post_data['unit_' . $entry_count];
                            $receivedqt =$form_post_data['received_qt_in_kg_' . $entry_count];
                            $batch =$form_post_data['batch_no_' . $entry_count];
                            $costunit =$form_post_data['cost_unit_' . $entry_count];
                            $total =$form_post_data['total_' . $entry_count];
                            $supplier =$form_post_data['supplier_invoice_no_' . $entry_count];
                            // $invoice =$form_post_data['invoice_date_' . $entry_count];
                            
                            $count += $poqty;
                            $countt += $rqt;
                            
                            if(!empty($itemid) && !empty($rqt) && !empty($total) ){   
                                // echo  '<pre>';

                             $sql_update="Update $po_detail_tab SET extra_clm =(extra_clm - '$rqt') 
                                 WHERE 
                                    $po_detail_tab.purchaseorder_ID=$PurchaseNO AND
                                    $po_detail_tab.rawmaterial_ID=$itemid AND 
                                    $po_detail_tab.ItemStatus=1";   
                            $update_stmt = $this->db->prepare($sql_update);
                            $update_stmt->execute(); 
                            
                           
                            
                            $vals = "'" . $lastInsertedID . "'," .
                                    "'" . $itemid . "'," .
                                    "'" . $poqty . "'," .
                                    "'" . $pendingqt . "'," .
                                    "'" . $rqt . "'," .
                                    "'" . $unit . "'," .
                                    "'" . $receivedqt . "'," .
                                    "'" . $batch . "'," .
                                    "'" . $costunit . "'," .
                                    "'" . $total . "'," .
                                    "'" . $supplier . "'," .
                                    "'" . $invoice_date . "'" ;   
                                            $sql2 = "INSERT INTO $material_inward_detail_tab
                                    ( 
                                        `material_inward_id`, 
                                        `item_id`,
                                        `po_qty`,
                                        `pending_qty`,
                                        `received_qty`,
                                        `unit`,
                                        `received_qt_in_kg`,
                                        `batch_no`,
                                        `cost_unit`,
                                        `total`,
                                        `supplier_invoice_no`,
                                        `invoice_date`
                                    ) 
                            VALUES ($vals)";
                            $stmt = $this->db->prepare($sql2);
                       // $stmt->execute();  
                       
                        if($stmt->execute()){
                             $sql_update="UPDATE $stock_tab  SET $stock_tab.batch_no =$batch where $stock_tab.rawmaterial_id = $itemid AND entity_ID = $entityID";
                             $update_stmt = $this->db->prepare($sql_update);
                             $update_stmt->execute();  
                             
                            //  $sql_update="UPDATE ycias_stock_trans  SET ycias_stock_trans.stock_value = (ycias_stock_trans.stock_value+$costunit) where ycias_stock_trans.raw_material_ID = $itemid";
                            //  $update_stmt = $this->db->prepare($sql_update);
                            //  $update_stmt->execute();  
                      }
                       
                                    }
                        }   
                        
                        $count;
                        $countt;
                        if($countt!=0){
                            $percent=100;
                            $rating=35;
                        }
                            else{
                                $percent=0; 
                                $rating=0;
                            }
                        

                        // $sql_update =     "INSERT INTO ycias_supplier_report(supplier_id, schedule, achieved, percent, rating, entity_ID, users_ID)
                        //                   VALUES ($supplier_id, $count, $countt,$percent,$rating, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";           
                                     
                        //                   $update_stmt = $this->db->prepare($sql_update);
                        //                   $update_stmt->execute();
                        
                        $sql_update="UPDATE ycias_supplier_report  SET trans_date=DATE_FORMAT(NOW(), '%Y-%m-%d'),schedule=$count,achieved=$countt,percent=$percent,rating=$rating where ycias_supplier_report.supplier_id = $supplier_id";
                        $update_stmt = $this->db->prepare($sql_update);
                        $update_stmt->execute();  
                        
                      
                    }

                    $sqlsrr = "SELECT  * FROM  $po_detail_tab WHERE $po_detail_tab.purchaseorder_ID = '$PurchaseNO'";
                    $po_det_data = $dbutil->getSqlData($sqlsrr);
                    $checkunique=[];
                    
                         if(!empty($po_det_data)){
                             
                             foreach($po_det_data as $k=>$v){
                                 
                                 if($v['extra_clm']<= '0'){
                                     $checkunique[]=1;
                                     $sql_update="Update $po_detail_tab SET ItemStatus='2' WHERE  $po_detail_tab.purchaseorder_ID=$PurchaseNO AND $po_detail_tab.rawmaterial_ID=$v[rawmaterial_ID]";
                                     $stmt1 = $this->db->prepare($sql_update);
                                     $stmt1->execute();
                                     
                                 }else{
                                     $checkunique[]=2; 
                                 }
                            
                                 
                             }
                            
                             if(count($checkunique)>0){
                                if(count(array_unique($checkunique))==1 && end($checkunique) == 1){
                                 $sql_update="Update $po_master_tab SET PU_Status='2' WHERE  $po_master_tab.ID=$PurchaseNO";
                                 $stmt1 = $this->db->prepare($sql_update);
                                 $stmt1->execute();
                                } 
                             }
                         }
                                                       
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('message', '- Success -');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/materialinward');
                 } else {
                        //edit option
                        //if submit failed to insert form
                        $this->tpl->set('message', 'Failed to submit!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/material_inward.php'));
                 }
                 
                break;

            case 'add':
                $this->tpl->set('mode', 'add');
                $this->tpl->set('page_header', 'Store');
                $MaterialNo=$dbutil->keyGeneration('material_inward','MNO','','MaterialNo');   

                $this->tpl->set('MaterialNo', $MaterialNo);
                $this->tpl->set('content', $this->tpl->fetch('factory/form/material_inward.php'));
                break;

            default:
                /*
                 * List form
                 */
                 
                ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
     $colArr = array(
            "$material_inward_tab.ID",
            "$material_inward_tab.MaterialNo",
            "$material_inward_tab.PurchaseNO",
           
           
            
            // "$po_master_tab.ID"

        );
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

            if (strpos($colNames, 'DATE') !== false) {
                list($colNames,$x) = $dbutil->dateFilterFormat($colNames);                    
            }else {
                $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);        		    
            }

              if ('' != $x) {
               $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
       IF (count($wsarr) >= 1) {
            $whereString = ' AND '. implode(' AND ', $wsarr);
        }
       } else {
         $whereString ="ORDER BY $material_inward_tab.ID DESC";
       }
       
    $sql = "SELECT "
                . implode(',',$colArr)
                . " FROM $material_inward_tab "
                . " WHERE "
                . " $material_inward_tab.entity_ID = $entityID"
                . " $whereString";  
        
     

            $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
        /*
         * SET DATA TO TEMPLATE
                    */
       $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
     
     
        $this->tpl->set('table_columns_label_arr', array('ID','Material NO','Purchase Order No'));
        
        /*
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
        // $this->tpl->set('dcpdf','Generate Pdf');            
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
               
        //////////////////////close//////////////////////////////////////  
                 
                include_once $this->tpl->path . '/factory/form/material_inward_crud_form.php';
                $cus_form_data = Form_Elements::data($this->crg);
                include_once 'util/crud3_1.php';
                new Crud3($this->crg, $cus_form_data);
                break;
        }

    ///////////////Use different template////////////////////
    $this->tpl->set('master_layout', 'layout_datepicker.php');
//     $this->tpl->set('master_layout', 'layout_chart.php');  
      // $this->tpl->set('master_layout', 'layout_datepicker.php'); 
    ///////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then /////
    ///////////////////////////////////////////////////////////////////
 } else {
         if ($this->ses->get('user')['ID']) {
             $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
         } else {
             header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
         }
     }
} 

//////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////
    //////////////////////////////Qc Material Inwards /////
    ///////////////////////////////////////////////////////////////////

function qcmaterialinward(){
     
        if ($this->crg->get('wp') || $this->crg->get('rp')) {
             
            /////////////////////////////////////////////////////////////////
            //////////////////////////////access condition applied///////////
            /////////////////////////////////////////////////////////////////
            
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
            
            include_once 'util/genUtil.php';
            $util = new GenUtil();
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
            $unit_table = $this->crg->get('table_prefix') . 'unit';
            $po_detail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
            $po_master_tab = $this->crg->get('table_prefix') . 'purchaseorder';
            $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
            $indent_master_tab = $this->crg->get('table_prefix') . 'purchaseindent';
            $indent_det_tab = $this->crg->get('table_prefix') . 'purchaseindentdetail';
            $state_tab = $this->crg->get('table_prefix') . 'state';
            $unit_tab = $this->crg->get('table_prefix') . 'unit';
            $material_inward_tab = $this->crg->get('table_prefix') . 'material_inward';
            $material_inward_detail_tab = $this->crg->get('table_prefix') . 'material_inward_detail';
            $qc_mast_tab = $this->crg->get('table_prefix') . 'qc_materialinward';
            $qc_detail_tab = $this->crg->get('table_prefix') . 'qc_materialinward_detail';
            
            $stock_tab = $this->crg->get('table_prefix') . 'stock';
            $stock_trans_tab = $this->crg->get('table_prefix') . 'stock_trans';
            
            //indent table data
           
            $sql = "SELECT $po_master_tab.ID,$po_master_tab.PurchaseOrderNo FROM $material_inward_tab,$po_master_tab WHERE $material_inward_tab.PurchaseNO = $po_master_tab.ID AND $material_inward_tab.Status=1 and $material_inward_tab.mrn_Status=1 and $material_inward_tab.entity_ID = $entityID GROUP BY $po_master_tab.ID,$po_master_tab.PurchaseOrderNo ORDER BY $material_inward_tab.ID DESC";
            $stmt = $this->db->prepare($sql);            
            $stmt->execute();
            $indent_dataa  = $stmt->fetchAll();	
            $this->tpl->set('indent_dataa', $indent_dataa); 
    
             //Material Inward table data
           
             $sql = "SELECT ID,MaterialNo FROM $material_inward_tab WHERE entity_ID = $entityID";
             $stmt = $this->db->prepare($sql);            
             $stmt->execute();
             $material_inward_id  = $stmt->fetchAll();	
             $this->tpl->set('material_inward_id', $material_inward_id);      

        
    
             //perchase Order table data
           
             $sql = "SELECT ID,PurchaseOrderNo FROM $po_master_tab";
             $stmt = $this->db->prepare($sql);            
             $stmt->execute();
             $po_master_tab_data  = $stmt->fetchAll();	
             $this->tpl->set('po_master_tab', $po_master_tab_data);

              //Material  Order table data
           
            //  $sql = "SELECT DISTINCT $po_master_tab.ID,$po_master_tab.PurchaseOrderNo FROM $material_inward_tab JOIN $po_master_tab ON $material_inward_tab.PurchaseNO = $po_master_tab.ID";
               $sql = "SELECT DISTINCT $po_master_tab.ID,$po_master_tab.PurchaseOrderNo 
              FROM      $material_inward_tab 
              JOIN      $po_master_tab 
              ON        $material_inward_tab.PurchaseNO = $po_master_tab.ID 
              WHERE     $material_inward_tab.Status=1 
              AND       $material_inward_tab.mrn_Status=1 
              AND entity_ID = $entityID
              ORDER BY  $material_inward_tab.ID DESC";
              $stmt = $this->db->prepare($sql);            
              $stmt->execute();
              $po_master_tab_data  = $stmt->fetchAll();	        
              $this->tpl->set('po_master_tabb', $po_master_tab_data);
            
            //supplier table data
           
            $sql = "SELECT ID,SupplierName FROM $supplier_tab ORDER BY $supplier_tab.SupplierName ASC";
            $stmt = $this->db->prepare($sql);            
            $stmt->execute();
            $supplier_data  = $stmt->fetchAll();	
            $this->tpl->set('supplier_data', $supplier_data);
            
            //rawmaterial table data
           
            $sql = "SELECT ID,RMName FROM $rawmaterial_table";
            $stmt = $this->db->prepare($sql);            
            $stmt->execute();
            $rawmaterial_data  = $stmt->fetchAll();	
            $this->tpl->set('rawmaterial_data', $rawmaterial_data);
            
            //unit table data
           
            $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
            $stmt = $this->db->prepare($pdt_sql);            
            $stmt->execute();
            $unit_data  = $stmt->fetchAll();	
            $this->tpl->set('unit_data', $unit_data);
     
            $this->tpl->set('page_title', 'Qc Material Inward');	          
            $this->tpl->set('page_header', 'Store');
            
            //Add Role when u submit the add role form
            $thisPageURL = $this->crg->get('route')['base_path'] . '/' . $this->crg->get('route')['module'] . '/' . $this->crg->get('route')['controller'] . '/' . $this->crg->get('route')['action'];
    
            $crud_string = null;
    
            if (isset($_POST['req_from_list_view'])) {
                $crud_string = strtolower($_POST['req_from_list_view']);
            }              
            
            //Edit submit
            if (!empty($_POST['edit_submit_button']) && $_POST['edit_submit_button'] == 'edit') {
                $crud_string = 'editsubmit';
            }
            
            //Add submit
            if (!empty($_POST['add_submit_button']) && $_POST['add_submit_button'] == 'add') {
                $crud_string = 'addsubmit';
            }
            
    
            switch ($crud_string) {
                 case 'delete':                    
                      $data = trim($_POST['ycs_ID']);
                       
                       
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                       
                    }
                   
                  //
                  
                    $sqlsrr = "SELECT * FROM $qc_mast_tab WHERE ID=$data";
                $qc_data = $dbutil->getSqlData($sqlsrr);                        
                
                $sqlsrr = "SELECT * FROM  $qc_detail_tab WHERE qc_material_id=$data";
                $qc_det_data = $dbutil->getSqlData($sqlsrr);    
                // print_r($qc_det_data[0]['item_id']); die;
                
                $material_no=(count($qc_data)>0) ? $qc_data[0]['material_inward_id']:'';       
                 
                $sqlsrr = "SELECT  * FROM  $material_inward_detail_tab WHERE $material_inward_detail_tab.material_inward_id = '$material_no'";
                $material_det_data = $dbutil->getSqlData($sqlsrr);     
                
                 
                 if(!empty($material_no)){
                     $sql_update="Update $material_inward_tab SET $material_inward_tab.mrn_Status=1 WHERE  $material_inward_tab.ID=$material_no";
                                 $masterstmt1 = $this->db->prepare($sql_update);        
                                 $masterstmt1->execute();       

                                 
                    foreach($qc_det_data as $k=>$v){

                        // echo '<pre>';
                        // print_r($v); die;

                        foreach($material_det_data as $kk=>$value){

                            // echo '<pre>';
                            // print_r($value); die;
                          
                            if(($value['item_id']==$v['item_id'])){
                                
                                $sql_updates="Update $material_inward_detail_tab SET $material_inward_detail_tab.ItemStatus=1,$material_inward_detail_tab.material_quantity_stat=(material_quantity_stat - $v[accepted_qty]) WHERE $material_inward_detail_tab.ID=$value[ID]";
                                $detstmt1 = $this->db->prepare($sql_updates);
                              $detstmt1->execute(); 
                            }
                            
                        }
                        
                    }
                                 
                      
                 }
                  
                  $sql_update="UPDATE ycias_stock
                  LEFT JOIN ycias_qc_materialinward_detail
                  ON ycias_qc_materialinward_detail.item_id=ycias_stock.rawmaterial_id
                  SET ycias_stock.available_qty=(ycias_stock.available_qty-ycias_qc_materialinward_detail.accepted_qty)
                  WHERE ycias_stock.rawmaterial_id=ycias_qc_materialinward_detail.item_id AND ycias_qc_materialinward_detail.qc_material_id=$data";          
             
                  $update_stmt = $this->db->prepare($sql_update);
                  $update_stmt->execute();
                  
                     
                    $sqldetdelete="DELETE $qc_detail_tab,$qc_mast_tab FROM $qc_mast_tab
                                   LEFT JOIN  $qc_detail_tab ON $qc_mast_tab.ID=$qc_detail_tab.qc_material_id 
                                   WHERE $qc_mast_tab.ID=$data";  
                    $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'QC Material Inward deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/qcmaterialinward');
                        // $this->tpl->set('label', 'List');
                        // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                        } 
                        
            break;
                case 'view':                    
                    $data = trim($_POST['ycs_ID']);
                 
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to view!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                        break;
                    }
                    
                    //mode of form submit
                    $this->tpl->set('mode', 'view');
                    //set id to edit $ycs_ID
                    $this->tpl->set('ycs_ID', $data);         
                                
                   
                    $sqlsrr="SELECT 
                    $qc_mast_tab.material_inward_id,
                    $qc_mast_tab.purchaseorder,
                    $qc_detail_tab.accepted_qty,
                    $qc_detail_tab.rejected_qty,
                    $qc_detail_tab.rejection_reason,
                    $qc_detail_tab.material_quantity_stat,
                  

                    $qc_mast_tab.supplier_ID,
                    $supplier_tab.SupplierName,
                    $material_inward_detail_tab.item_id,
                    $rawmaterial_table.RMName,
                    $unit_table.UnitName,
                    $material_inward_detail_tab.received_qty,
                    
                    $material_inward_detail_tab.received_qt_in_kg,
                    $material_inward_detail_tab.batch_no,
                    $material_inward_detail_tab.supplier_invoice_no,
                    $material_inward_detail_tab.invoice_date,
                    
                    $material_inward_detail_tab.unit
                   
                
                FROM
                     $qc_mast_tab,
                     $qc_detail_tab,
                    --  $po_master_tab,
                     $supplier_tab, 
                     $material_inward_tab,
                   
                     $material_inward_detail_tab,
                     $rawmaterial_table,
                     $unit_table

                 WHERE 
                     $qc_mast_tab.ID = $qc_detail_tab.qc_material_id
                     AND $qc_mast_tab.supplier_ID = $supplier_tab.ID
                     AND $qc_mast_tab.material_inward_id = $material_inward_tab.ID
                     AND $qc_mast_tab.purchaseorder = $material_inward_tab.PurchaseNO
                     AND $material_inward_tab.ID = $material_inward_detail_tab.material_inward_id
                     AND $qc_detail_tab.item_id = $material_inward_detail_tab.item_id
                     AND $qc_detail_tab.item_id = $rawmaterial_table.ID
                     AND $material_inward_detail_tab.unit = $unit_table.ID
                     AND $material_inward_detail_tab.received_qty >=0
                     AND $qc_mast_tab.ID = $data";              

                  
                  $indent_data = $dbutil->getSqlData($sqlsrr);
                            
                   $sql = "SELECT DISTINCT $po_master_tab.ID,$po_master_tab.PurchaseOrderNo 
              FROM      $material_inward_tab 
              JOIN      $po_master_tab 
              ON        $material_inward_tab.PurchaseNO = $po_master_tab.ID 
             
                 
               
              ORDER BY  $material_inward_tab.ID DESC";
              $stmt = $this->db->prepare($sql);            
              $stmt->execute();
              $po_master_tab_data  = $stmt->fetchAll();	        
              $this->tpl->set('indent_dataa', $po_master_tab_data);
                    
                    
                    //edit option     
                    $this->tpl->set('message', 'You can view Qc Material Inward form');
                    $this->tpl->set('page_header', 'Store');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/qc_material_inward.php'));                    
                    break;
                
                case 'edit':                    
                    $data = trim($_POST['ycs_ID']);
                    $mode='edit';
                    if(isset($_SESSION['ycs_ID']))
                    {
                        $data = trim($_SESSION['ycs_ID']);
                        unset($_SESSION['ycs_ID']);
                        $mode='Confirm';
                    }
               // var_dump($data);
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to edit!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                        break;
                    }
                    
                    //mode of form submit
                    $this->tpl->set('mode', $mode);
                    //set id to edit $ycs_ID
                    $this->tpl->set('ycs_ID', $data);         
                    

           $sqlsrr="SELECT 
                        $qc_mast_tab.qcmaterial_inw_id,
                        $qc_mast_tab.material_inward_id,
                        $qc_mast_tab.purchaseorder,
                        $qc_detail_tab.accepted_qty,
                        $qc_detail_tab.rejected_qty,
                        $qc_detail_tab.rejection_reason,
                        $qc_detail_tab.material_quantity_stat,

                        $qc_mast_tab.supplier_ID,
                        $supplier_tab.SupplierName,
                        $material_inward_detail_tab.item_id,
                        $rawmaterial_table.RMName,
                        $unit_table.UnitName,
                        $material_inward_detail_tab.received_qty,
                        
                        $material_inward_detail_tab.received_qt_in_kg,
                        $material_inward_detail_tab.batch_no,
                        $material_inward_detail_tab.supplier_invoice_no,
                        $material_inward_detail_tab.invoice_date,
                       
                        $material_inward_detail_tab.unit                
                       
                    
                    FROM
                         $qc_mast_tab,
                         $qc_detail_tab,
                        
                         $supplier_tab, 
                         $material_inward_tab,
                       
                         $material_inward_detail_tab,
                         $rawmaterial_table,
                         $unit_table

                     WHERE 
                         $qc_mast_tab.ID = $qc_detail_tab.qc_material_id
                         AND $qc_mast_tab.supplier_ID = $supplier_tab.ID
                         AND $qc_mast_tab.material_inward_id = $material_inward_tab.ID
                         AND $qc_mast_tab.purchaseorder = $material_inward_tab.PurchaseNO
                         AND $material_inward_tab.ID = $material_inward_detail_tab.material_inward_id
                         AND $qc_detail_tab.item_id = $material_inward_detail_tab.item_id
                         AND $qc_detail_tab.item_id = $rawmaterial_table.ID
                         AND $material_inward_detail_tab.unit = $unit_table.ID
                         AND $material_inward_detail_tab.received_qty >=0
                         AND $qc_mast_tab.ID = $data";                          

                    //  AND $qc_mast_tab.purchaseorder = $po_master_tab.ID
                    //  AND $po_master_tab.ID = $material_inward_tab.PurchaseNO 
                    //  AND $material_inward_tab.supplier_ID = $supplier_tab.ID 
                    //  AND $qc_mast_tab.material_inward_id = $material_inward_tab.ID
                    //  AND $material_inward_tab.ID = $material_inward_detail_tab.material_inward_id
                    //  AND $material_inward_detail_tab.item_id = $rawmaterial_table.ID
                    //  AND $material_inward_detail_tab.unit = $unit_table.ID                   
                    //  AND $material_inward_detail_tab.received_qty >=0
                    //  AND $qc_mast_tab.ID = $data";                          die;


                          


    
                    $indent_data = $dbutil->getSqlData($sqlsrr);               
                    $indentsel_data  = $stmt->fetchAll();	
                    $this->tpl->set('indent_data', $indentsel_data);
                
                    //edit option     
                    $this->tpl->set('message', 'You can edit Qc Material Inward form');
                    $this->tpl->set('page_header', 'Store');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/qc_material_inward.php'));                    
                    break;
                
                case 'editsubmit':             
                    $data = trim($_POST['ycs_ID']);
                    
                    //mode of form submit
                    $this->tpl->set('mode', 'edit');
                    //set id to edit $ycs_ID
                    $this->tpl->set('ycs_ID', $data);
    
                    //Post data
                    include_once 'util/genUtil.php';
                    $util = new GenUtil();
                    $form_post_data = $util->arrFltr($_POST);

                    //      echo '<pre>';
                    //    print_r($form_post_data); die;
    
                    
                    // $sqldet_del = "DELETE FROM $po_detail_tab WHERE purchaseorder_ID=$data";
                    // $stmt = $this->db->prepare($sqldet_del);
                    // $stmt->execute();   
                            
                            try{              
                            $qcmaterial_inw_id= $form_post_data['qcmaterial_inw_id'];      
                              $material_inward_id= $form_post_data['material_inward_id'];
                            $purchaseorder= $form_post_data['purchaseorder'];
                            $supplier_ID= $form_post_data['supplier_ID'];
                            
                            $sql_update="Update $qc_mast_tab SET  qcmaterial_inw_id='$qcmaterial_inw_id',
                            material_inward_id='$material_inward_id',
                            purchaseorder='$purchaseorder',
                            supplier_ID='$supplier_ID'
                            WHERE  ID=$data";     

                            $stmt1 = $this->db->prepare($sql_update);
                            $stmt1->execute();

                            $sql3 = "DELETE FROM $qc_detail_tab WHERE qc_material_id=$data";
					     $stmt3 = $this->db->prepare($sql3);

                        if($stmt3->execute()){
                        
                            FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {

                                $item_description =$form_post_data['item_id_' . $entry_count]; 
                                $unit =$form_post_data['unit_' . $entry_count];
                                $batch_no =$form_post_data['batch_no_' . $entry_count];
                                $material_quantity_stat =$form_post_data['material_quantity_stat_' . $entry_count];
                                $accepted_qty =$form_post_data['accepted_qty_' . $entry_count];
                                $acceptedqty =$form_post_data['acceptedqty_' . $entry_count];
                                $rejected =$form_post_data['rejected_qty_' . $entry_count];
                                $rejection_reason =$form_post_data['rejection_reason_' . $entry_count];
                                
                                $sql_update="Update $stock_tab SET available_qty=(available_qty-'$acceptedqty') 
                                WHERE 
                                $stock_tab.rawmaterial_id=$item_description ";               
                                $update_stmt = $this->db->prepare($sql_update);
                                $update_stmt->execute();
                               
                                if(!empty($item_description) && !empty($accepted_qty)){

                                    // echo '<pre>';
                                    
                                    $sql_update="Update $material_inward_detail_tab SET material_quantity_stat=(material_quantity_stat+'$accepted_qty') 
                                    WHERE 
                                        $material_inward_detail_tab.material_inward_id=$material_id AND 
                                        $material_inward_detail_tab.item_id=$item_description ";               
                                         
    
                                    $update_stmt = $this->db->prepare($sql_update);
                                    $update_stmt->execute();
                                    
                                    $sql_update="Update $stock_tab SET available_qty=(available_qty+'$accepted_qty') 
                                    WHERE 
                                    $stock_tab.rawmaterial_id=$item_description ";               
                                    $update_stmt = $this->db->prepare($sql_update);
                                    $update_stmt->execute();
                                
                            
                                
                               
                                
                                $vals = "'" . $data  . "'," .
                                        "'" . $item_description . "'," .
                                        "'" . $unit . "'," .
                                        "'" . $batch_no . "'," .
                                        "'" . $material_quantity_stat . "'," .
                                        "'" . $accepted_qty . "'," .
                                      
                                        "'" . $rejected . "'," .
                                        "'" . $rejection_reason . "'" ;   
                                                $sql2 = "INSERT INTO $qc_detail_tab
                                        ( 
                                            `qc_material_id`, 
                                            `item_id`,
                                            `unit`,
                                            `batch_no`,
                                            `material_quantity_stat`,
                                            `accepted_qty`,
                                            `rejected_qty`,
                                            `rejection_reason`
                                        ) 
                                VALUES ($vals)";
                                $stmt = $this->db->prepare($sql2);
                            $stmt->execute();                           
                           
                               
                            }           
                            
                        }          
    
                            
                        }
                                        
                            $this->tpl->set('message', 'Qc Material Inward form edited successfully!');   
                            header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/qcmaterialinward');
                           
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/qc_material_inward.php'));
                            }
    
                    break;
                    
                case 'addsubmit':
                    
                    if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                        // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                        // $delivery_date=!empty($form_post_data['DeliveryDate'])?date("Y-m-d", strtotime($form_post_data['DeliveryDate'])):'';
                        // "'" . $dispatch_date . "'," .
                    //     // $purchase_indentno=$form_post_data['purchaseindent_ID'];
                    //     echo '<pre>';
                    //    print_r($form_post_data); die;
                    $material_id=$form_post_data['material_inward_id'];
                    $supplier_id=$form_post_data['supplier_ID'];
                        
                                $val ="'" . $form_post_data['qcmaterial_inw_id'] . "'," .  
                                        // "'" . $form_post_data['material_inward_id'] . "'," .     
                                        "'" . $material_id . "'," .                             
                                        "'" . $form_post_data['purchaseorder'] . "'," .
                                        "'" . $form_post_data['supplier_ID'] . "'," .
                                       
                                       
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";
    
                             $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "qc_materialinward`
                                            ( 
                                                `qcmaterial_inw_id`,
                                            `material_inward_id`,
                                            `purchaseorder`,           
                                            `supplier_ID`,
                                           
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                    VALUES ( $val )";
                                    $stmt = $this->db->prepare($sql);       
                        if ($stmt->execute()) { 
                        
                        $lastInsertedID = $this->db->lastInsertId();
                        
                        $count=0;
                        $countt=0;
                        $counttt=0;
                          
                      FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
    
                        
                                $item_description =$form_post_data['item_id_' . $entry_count]; 
                                $hai =$form_post_data['RMName_' . $entry_count];
                                $receivedqty =$form_post_data['received_qty_' . $entry_count];
                                $unit =$form_post_data['unit_' . $entry_count];
                                $unit_name =$form_post_data['UnitName_' . $entry_count];
                                $batch_no =$form_post_data['batch_no_' . $entry_count];
                                $total_accepted_qty =$form_post_data['material_quantity_stat_' . $entry_count];
                                $accepted_qty =$form_post_data['accepted_qty_' . $entry_count];
                           
                                $rejected =$form_post_data['rejected_qty_' . $entry_count];
                                $rejection_reason =$form_post_data['rejection_reason_' . $entry_count];
                                
                                $count += $accepted_qty;
                                $countt += $rejected;
                                $counttt += $receivedqty;
                               
                                if(!empty($item_description)){

                                    // echo '<pre>';
                                    
                                     $sql_update="Update $material_inward_detail_tab SET material_quantity_stat=(material_quantity_stat+'$accepted_qty') 
                                                  WHERE 
                                                  $material_inward_detail_tab.material_inward_id=$material_id AND 
                                                  $material_inward_detail_tab.item_id=$item_description       AND
                                                  $material_inward_detail_tab.ItemStatus=1";               
                                         
                                                  $update_stmt = $this->db->prepare($sql_update);
                                                  $update_stmt->execute();
                                
                                $vals = "'" . $lastInsertedID . "'," .
                                        "'" . $item_description . "'," .
                                        "'" . $unit . "'," .
                                        "'" . $batch_no . "'," .
                                        "'" . $total_accepted_qty . "'," .
                                        "'" . $accepted_qty . "'," .
                                      
                                        "'" . $rejected . "'," .
                                        "'" . $rejection_reason . "'" ;   
                                                $sql2 = "INSERT INTO $qc_detail_tab
                                        ( 
                                            `qc_material_id`, 
                                            `item_id`,
                                            `unit`,
                                            `batch_no`,
                                            `material_quantity_stat`,
                                            `accepted_qty`,
                                            `rejected_qty`,
                                            `rejection_reason`
                                        ) 
                                VALUES ($vals)";
                                $stmt = $this->db->prepare($sql2);
                            //$stmt->execute();  
                           
                          if($stmt->execute()){
                        //  $sql_update="UPDATE ycias_stock
                        //                      LEFT JOIN ycias_qc_materialinward_detail
                        //                      ON ycias_qc_materialinward_detail.item_id=ycias_stock.rawmaterial_id
                        //                      SET ycias_stock.available_qty=(ycias_stock.available_qty+$accepted_qty)
                        //                      WHERE ycias_stock.rawmaterial_id=ycias_qc_materialinward_detail.item_id";          
                                         
                        //                  $update_stmt = $this->db->prepare($sql_update);
                        //                  $update_stmt->execute();
                        if($accepted_qty >0){
                        $sql_update="UPDATE ycias_stock                                        
                                             SET available_qty=(available_qty+$accepted_qty)
                                             WHERE rawmaterial_id =  $item_description AND  entity_ID = $entityID";                                                   
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                        }            
                          $sqlsrr = "SELECT * FROM  ycias_material_inward_detail WHERE ycias_material_inward_detail.item_id = $item_description AND ycias_material_inward_detail.material_inward_id = $material_id";
                                         $qc_det_data = $dbutil->getSqlData($sqlsrr);    
                                         // print_r($qc_det_data[0]['item_id']); die;
                                     $item_id=(count($qc_det_data)>0) ? $qc_det_data[0]['cost_unit']:'';  
                                         
                         $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType, raw_material_ID, raw_material_name, stockin, unit_name,stock_value, entity_ID, users_ID)
                                         VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Qc material inward', $item_description, '$hai', $accepted_qty, '$unit_name',$item_id, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                     
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                          }
                               
                            }           
                            
                        }
                        
                        $count;
                        $countt;
                        $counttt;

                        $percent = ($count / $counttt) * 100;
                        if($percent>=70){
                            $percent=$percent;
                            $rating=35;
                        }
                            else{
                                $percent=$percent;
                                $rating=0;
                            }

                        $sql_update="UPDATE ycias_supplier_report  SET trans_date=DATE_FORMAT(NOW(), '%Y-%m-%d'),received = (received+$count),rejected=(rejected+$countt),percent1=$percent,rating1=$rating where ycias_supplier_report.supplier_id = $supplier_id";
                        $update_stmt = $this->db->prepare($sql_update);
                        $update_stmt->execute();  
                            
                          
                        }
                        
                        //
                         $sqlsrr = "SELECT  * FROM  $material_inward_detail_tab WHERE $material_inward_detail_tab.material_inward_id = '$material_id'";
                        $material_det_data = $dbutil->getSqlData($sqlsrr);     
                        $checkunique=[];
                        
                             if(!empty($material_det_data)){
                                 
                                 foreach($material_det_data as $k=>$v){
                                     
                                    //  if($v['material_quantity_stat']==$v['received_qty']){
                                    if($v['material_quantity_stat']>=$v['received_qty']){
                                         $checkunique[]=1;
                                         $sql_update="Update $material_inward_detail_tab SET ItemStatus='2' WHERE  $material_inward_detail_tab.material_inward_id=$material_id AND $material_inward_detail_tab.item_id=$v[item_id]";
                                         $stmt1 = $this->db->prepare($sql_update);
                                         $stmt1->execute();
                                         
                                     }else{
                                         $checkunique[]=2; 
                                     }
                                
                                     
                                 }
                                
                                 if(count($checkunique)>0){
                                    if(count(array_unique($checkunique))==1 && end($checkunique) == 1){
                                     $sql_update="Update $material_inward_tab SET mrn_Status='2' WHERE  $material_inward_tab.ID=$material_id";
                                     $stmt1 = $this->db->prepare($sql_update);
                                     $stmt1->execute();
                                    } 
                                 }
                             }
                        //
                                                           
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/qcmaterialinward');
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/qc_material_inward.php'));
                     }
                     
                    break;
    
                case 'add':
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('page_header', 'Store');
                    $qcmaterial_inw_id=$dbutil->keyGeneration('qc_materialinward','QCM','','qcmaterial_inw_id');
                    $this->tpl->set('qcmaterial_inw_id', $qcmaterial_inw_id);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/qc_material_inward.php'));
                    break;
    
                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$qc_mast_tab.ID",
                "$qc_mast_tab.qcmaterial_inw_id"
               
               
               
                
                // "$po_master_tab.ID"
    
            );
            
           

            $this->tpl->set('FmData', $_POST);
            foreach($_POST as $k=>$v){
                if(strpos($k,'^')){
                    unset($_POST[$k]);
                }
                $_POST[str_replace('^','_',$k)] = $v;
            }
            $PD=$_POST;
            if($_POST['list']!=''){
                $this->tpl->set('FmData', NULL);
                $PD=NULL;
            }
    
            IF (count($PD) >= 2) {
                $wsarr = array();
                foreach ($colArr as $colNames) {
    
                if (strpos($colNames, 'DATE') !== false) {
                    list($colNames,$x) = $dbutil->dateFilterFormat($colNames);                    
                }else {
                    $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);        		    
                }
    
                  if ('' != $x) {
                   $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                    }
                }
                
           IF (count($wsarr) >= 1) {
                $whereString = ' AND '. implode(' AND ', $wsarr);
            }
           } else {
             $whereString ="ORDER BY $qc_mast_tab.ID DESC";
           }
           
         

        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $qc_mast_tab"
                    . " WHERE "

 
                    . " $qc_mast_tab.entity_ID = $entityID"
                    . " $whereString";  
            
         
    
                $results_per_page = 50;     
            
                if(isset($PD['pageno'])){$page=$PD['pageno'];}
                else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
                else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
                else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
                else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
                else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
                else{$page=1;} 
            /*
             * SET DATA TO TEMPLATE
                        */
           $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Material NO'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
            // $this->tpl->set('dcpdf','Generate Pdf');            
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/qc_material_inward_crud_form.php';
                    $cus_form_data = Form_Elements::data($this->crg);
                    include_once 'util/crud3_1.php';
                    new Crud3($this->crg, $cus_form_data);
                    break;
            }
    
        ///////////////Use different template////////////////////
        // $this->tpl->set('master_layout', 'layout_chart.php'); 
        // $this->tpl->set('master_layoutt', 'layout_datepicker.php'); 
          $this->tpl->set('master_layout', 'layout_datepicker.php'); 
        // $this->tpl->set('master_layout', 'layout_datepicker.php'); 
        ///////////////////////////////////////////////////////////////////
        //////////////////////////////on access condition failed then /////
        ///////////////////////////////////////////////////////////////////
     } else {
             if ($this->ses->get('user')['ID']) {
                 $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
             } else {
                 header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
             }
         }
    }  


////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
/////////////////     Raw Material Issue form  

function rawmaterialissue(){
     
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
         
        /////////////////////////////////////////////////////////////////
        //////////////////////////////access condition applied///////////
        /////////////////////////////////////////////////////////////////
        
        include_once 'util/DBUTIL.php';
        $dbutil = new DBUTIL($this->crg);
        
        include_once 'util/genUtil.php';
        $util = new GenUtil();

        $username=$this->ses->get('user')['user_nicename'];
            $this->tpl->set('preparedby', $username);
         
        $entityID = $this->ses->get('user')['entity_ID'];
        $userID = $this->ses->get('user')['ID'];

        $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
        $unit_table = $this->crg->get('table_prefix') . 'unit';
        $po_detail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
        $po_master_tab = $this->crg->get('table_prefix') . 'purchaseorder';
        $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
        $indent_master_tab = $this->crg->get('table_prefix') . 'purchaseindent';
        $indent_det_tab = $this->crg->get('table_prefix') . 'purchaseindentdetail';
        $state_tab = $this->crg->get('table_prefix') . 'state';
        $unit_tab = $this->crg->get('table_prefix') . 'unit';
        $material_inward_tab = $this->crg->get('table_prefix') . 'material_inward';
        $material_inward_detail_tab = $this->crg->get('table_prefix') . 'material_inward_detail';
        $rawmeterial_issue_tab = $this->crg->get('table_prefix') . 'rawmeterial_issue';
        $rawmeterial_issue_det_tab = $this->crg->get('table_prefix') . 'rawmeterial_issue_detail';
        $productionorder_tab = $this->crg->get('table_prefix') . 'productionorder';
        $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
        $processmaster_tab = $this->crg->get('table_prefix') . 'ProcessMaster';
        $employee_tab = $this->crg->get('table_prefix') . 'employee';
        $bomprocessmaster_table = $this->crg->get('table_prefix') . 'BOMProcessMaster';
        $bomprocessdetail_table = $this->crg->get('table_prefix') . 'BOMProcessDetail';
        $product_table = $this->crg->get('table_prefix') . 'product';
        $approvaltype_tab = $this->crg->get('table_prefix') . 'approvaltype';
        $workorder_detail_table = $this->crg->get('table_prefix') . 'workorder_detail';
        $stock_table = $this->crg->get('table_prefix') . 'stock';
       
        
        $stock_table = $this->crg->get('table_prefix') . 'stock';
        $stock_trans_tab = $this->crg->get('table_prefix') . 'stock_trans';

        $user_table = $this->crg->get('table_prefix') . 'users';
 

        $sql = "SELECT $user_table.ID,$user_table.user_nicename FROM $user_table,$approvaltype_tab where $user_table.ID=$approvaltype_tab.approver_ID"; 
            // $stmt = $this->db->prepare($sql);            
            // $stmt->execute();
            // $approver_data= $stmt->fetchAll();	
            $approver_data = $dbutil->getSqlData($sql); 
            $this->tpl->set('approver_data', $approver_data);

        
        //indent table data
       
        $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1 and $indent_master_tab.PI_Status=1 ORDER BY $indent_master_tab.ID DESC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $indent_data  = $stmt->fetchAll();	
        $this->tpl->set('indent_data', $indent_data);

         //purchase Order table data
       
         $sql = "SELECT ID,PdnOrderNo FROM $productionorder_tab where entity_ID = $entityID";  
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $productionorder_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('productionorder_tab_data', $productionorder_tab_data);

          //work Order table data
       
         $sql = "SELECT ID,WorkOrderNo FROM $workorder_tab where $workorder_tab.Statuss=1 AND $workorder_tab.rmt_Status=1 AND entity_ID = $entityID ORDER BY $workorder_tab.ID DESC";  
          $stmt = $this->db->prepare($sql);            
          $stmt->execute();
          $workorder_tab_data  = $stmt->fetchAll();	
          $this->tpl->set('workorder_tab_data1', $workorder_tab_data);  

          $Employeetype_data = array(array("ID"=>"1","Title"=>"Employee"),array("ID"=>"2","Title"=>"Sub Contractor"));
                                    $this->tpl->set('Employeetype_data', $Employeetype_data);

           //Production Order table data
       
           $sql = "SELECT ID,MaterialNo FROM $material_inward_tab";
           $stmt = $this->db->prepare($sql);            
           $stmt->execute();
           $material_inward_tab_data  = $stmt->fetchAll();	
           $this->tpl->set('material_inward_tab_data', $material_inward_tab_data);

         //perchase Order table data
       
         $sql = "SELECT ID,PurchaseOrderNo,purchaseindent_ID FROM $po_master_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $po_master_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('po_master_tab', $po_master_tab_data);
        
        //supplier table data
       
        $sql = "SELECT ID,SupplierName FROM $supplier_tab ORDER BY $supplier_tab.SupplierName ASC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $supplier_data  = $stmt->fetchAll();	
        $this->tpl->set('supplier_data', $supplier_data);
        
        //rawmaterial table data
       
        $sql = "SELECT ID,RMName FROM $rawmaterial_table";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $rawmaterial_data  = $stmt->fetchAll();	
        $this->tpl->set('rawmaterial_data', $rawmaterial_data);
        
        //unit table data
       
        $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
        $stmt = $this->db->prepare($pdt_sql);            
        $stmt->execute();
        $unit_data  = $stmt->fetchAll();	
        $this->tpl->set('unit_data', $unit_data);
 
        $this->tpl->set('page_title', 'Raw Material Issue');	          
        $this->tpl->set('page_header', 'Store');
        
        //Add Role when u submit the add role form
        $thisPageURL = $this->crg->get('route')['base_path'] . '/' . $this->crg->get('route')['module'] . '/' . $this->crg->get('route')['controller'] . '/' . $this->crg->get('route')['action'];

        $crud_string = null;

        if (isset($_POST['req_from_list_view'])) {
            $crud_string = strtolower($_POST['req_from_list_view']);
        }              
        
        //Edit submit
        if (!empty($_POST['edit_submit_button']) && $_POST['edit_submit_button'] == 'edit') {
            $crud_string = 'editsubmit';
        }
        
        //Add submit
        if (!empty($_POST['add_submit_button']) && $_POST['add_submit_button'] == 'add') {
            $crud_string = 'addsubmit';
        }
        

        switch ($crud_string) {
             case 'delete':                    
                  $data = trim($_POST['ycs_ID']);
                   
                   
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                   
                }
               
               $sqlsrr = "SELECT * FROM $rawmeterial_issue_tab WHERE ID=$data";
                $rmt_data = $dbutil->getSqlData($sqlsrr); 
                
                $sqlsrr = "SELECT * FROM  $rawmeterial_issue_det_tab WHERE rawmaterial_issue_id=$data";
                $rmt_det_data = $dbutil->getSqlData($sqlsrr); 
                
                $work_order=(count($rmt_data)>0)?$rmt_data[0]['work_order_no']:'';
                 
                $sqlsrr = "SELECT  * FROM  $workorder_detail_table WHERE $workorder_detail_table.Workorder_ID = '$work_order'";
                $workorder_det_data = $dbutil->getSqlData($sqlsrr);
                
               

                 if(!empty($work_order)){
                     $sql_update="Update $workorder_tab SET $workorder_tab.rmt_Status=1 WHERE  $workorder_tab.ID=$work_order";
                                 $masterstmt1 = $this->db->prepare($sql_update);
                                 $masterstmt1->execute();
                                 
                    foreach($rmt_det_data as $k=>$v){
                    
                        foreach($workorder_det_data as $kk=>$value){
                            
                            if(($value['rawmaterial_ID']==$v['rawmaterial_ID'])){
                                
                                $sql_updates="Update $workorder_detail_table SET $workorder_detail_table.ItemStatus=1,$workorder_detail_table.rmt_qty=(rmt_qty-$v[issued_qty]) WHERE $workorder_detail_table.ID=$value[ID]";
                                $detstmt1 = $this->db->prepare($sql_updates);
                              $detstmt1->execute();
                             
                            }
                            
                        }
                        
                    }
                                 
                      
                 }
                
                $sql_update="UPDATE $stock_table
                LEFT JOIN $rawmeterial_issue_det_tab
                ON $rawmeterial_issue_det_tab.rawmaterial_ID=$stock_table.rawmaterial_id
                SET $stock_table.available_qty=($stock_table.available_qty+$rawmeterial_issue_det_tab.issued_qty)
                WHERE $stock_table.rawmaterial_id=$rawmeterial_issue_det_tab.rawmaterial_ID AND $rawmeterial_issue_det_tab.rawmaterial_ID=$data";          
                $update_stmt = $this->db->prepare($sql_update);
                $update_stmt->execute();
                 
                $sqldetdelete="DELETE $rawmeterial_issue_tab,$rawmeterial_issue_det_tab FROM $rawmeterial_issue_tab
                               LEFT JOIN  $rawmeterial_issue_det_tab ON $rawmeterial_issue_tab.ID=$rawmeterial_issue_det_tab.rawmaterial_issue_id 
                               WHERE $rawmeterial_issue_tab.ID=$data";  
                               
                $stmt = $this->db->prepare($sqldetdelete);            
                    
                    if($stmt->execute()){
                    $this->tpl->set('message', 'Raw Material Issue deleted successfully');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/rawmaterialissue');
                    // $this->tpl->set('label', 'List');
                    // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    } 
                    
        break;
            case 'view':                    
                $data = trim($_POST['ycs_ID']);
             
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to view!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', 'view');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);  
                
                $sql = "SELECT ID,WorkOrderNo FROM $workorder_tab  ORDER BY $workorder_tab.ID DESC";  
                $stmt = $this->db->prepare($sql);            
                $stmt->execute();
                $workorder_tab_data  = $stmt->fetchAll();	
                $this->tpl->set('workorder_tab_data1', $workorder_tab_data);
              

                // $sqlsrr ="SELECT 
                //             $rawmeterial_issue_tab.*,
                //             $rawmeterial_issue_det_tab.*,
                //               $workorder_tab.ProcessID,
                //             $processmaster_tab.ProcessName,
                //             $workorder_tab.ProductSize, 
                //             $workorder_tab.Thickness,
                //             $workorder_tab.Colour, 
                //             $workorder_tab.Design,
                //             $workorder_tab.NoofQuantity,                                
                //             $workorder_tab.CompletedDate,
                //             $workorder_tab.SequenceMaterialIssued, 
                //             $workorder_tab.Details, 
                //             $workorder_tab.Remarks, 
                //             $workorder_tab.product_ID,
                //             $workorder_tab.EmployeeID,
                //             $employee_tab.EmpName,
                //             $workorder_tab.Subcontractor_ID,
                //             $supplier_tab.SupplierName,
                //             $product_table.ProductName,
                            
                //             $workorder_detail_table.RMName,
                //             $workorder_detail_table.rawmaterial_ID,
                //             $workorder_detail_table.unit_ID,
                //             $workorder_detail_table.UnitName,
                            
                //             $stock_table.available_qty,
                //             $workorder_detail_table.rmt_qty,
                //             $workorder_detail_table.Quantity2

                //     FROM 
                //             $rawmeterial_issue_tab
                //     LEFT JOIN $rawmeterial_issue_det_tab  ON $rawmeterial_issue_det_tab.rawmaterial_issue_id = $rawmeterial_issue_tab.ID 
                //     LEFT JOIN $workorder_tab  ON $rawmeterial_issue_tab.work_order_no = $workorder_tab.ID
                    
                //     LEFT JOIN $workorder_detail_table  ON $workorder_tab.ID = $workorder_detail_table.Workorder_ID
                //     LEFT JOIN $stock_table  ON $workorder_detail_table.rawmaterial_ID = $stock_table.rawmaterial_id
                    
                    
                    
                //     LEFT JOIN $product_table  ON $workorder_tab.product_ID = $product_table.ID
                //     LEFT JOIN $employee_tab ON  $workorder_tab.EmployeeID = $employee_tab.ID
                //     LEFT JOIN $supplier_tab ON  $workorder_tab.Subcontractor_ID = $supplier_tab.ID
                //     LEFT JOIN $processmaster_tab ON  $workorder_tab.ProcessID = $processmaster_tab.ID
                //     WHERE
                               
            
                //         $rawmeterial_issue_tab.ID= $data"; 
                 $sqlsrr ="SELECT * from  
                 $rawmeterial_issue_tab,
                 $rawmeterial_issue_det_tab 
            WHERE 
                 $rawmeterial_issue_tab.ID = $rawmeterial_issue_det_tab.rawmaterial_issue_id  
             AND $rawmeterial_issue_tab.entity_ID = $entityID AND $rawmeterial_issue_tab.ID=$data";
                
                $indent_data = $dbutil->getSqlData($sqlsrr);              
              
                
                    //  $sqlsrr ="SELECT $rawmeterial_issue_det_tab.* FROM $rawmeterial_issue_tab LEFT JOIN $rawmeterial_issue_det_tab  ON $rawmeterial_issue_det_tab.rawmaterial_issue_id = $rawmeterial_issue_tab.ID  
                    //           WHERE $rawmeterial_issue_tab.ID= $data";  
                    //  $indent1_data = $dbutil->getSqlData($sqlsrr);  
                
                //edit option     
                $this->tpl->set('message', 'You can view Raw Material Issue form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                //$this->tpl->set('indent1_data', $indent1_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/rawmaterial_issuse_form.php'));                    
                break;
            
            case 'edit':                    
                $data = trim($_POST['ycs_ID']);
                $mode='edit';
                if(isset($_SESSION['ycs_ID']))
                {
                    $data = trim($_SESSION['ycs_ID']);
                    unset($_SESSION['ycs_ID']);
                    $mode='Confirm';
                }
           // var_dump($data);
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to edit!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', $mode);
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         
               

            $sqlsrr ="SELECT 
                            $rawmeterial_issue_tab.*,
                            $rawmeterial_issue_det_tab.*,
                            $workorder_tab.ProcessID,
                            $processmaster_tab.ProcessName,
                            $workorder_tab.ProductSize, 
                            $workorder_tab.Thickness,
                            $workorder_tab.Colour, 
                            $workorder_tab.Design,
                            $workorder_tab.Quantity1,                                
                            $workorder_tab.CompletedDate,
                            $workorder_tab.SequenceMaterialIssued, 
                            $workorder_tab.Details, 
                            $workorder_tab.Remarks, 
                            $workorder_tab.product_ID,
                            $workorder_tab.EmployeeID,
                            $employee_tab.EmpName,
                            $workorder_tab.Subcontractor_ID,
                            $supplier_tab.SupplierName,
                            $product_table.ProductName,
                            $bomprocessdetail_table.rawmaterial_ID,
                            $rawmaterial_table.RMName,
                            $bomprocessdetail_table.unit_ID,
                            $unit_tab.UnitName,
                            $workorder_detail_table.Quantity2

                    FROM 
                            $rawmeterial_issue_tab
                            
                    LEFT JOIN $rawmeterial_issue_det_tab  ON $rawmeterial_issue_tab.ID = $rawmeterial_issue_det_tab.rawmaterial_issue_id
                    LEFT JOIN $workorder_tab  ON $rawmeterial_issue_tab.work_order_no = $workorder_tab.ID
                    
                     LEFT JOIN $workorder_detail_table  ON $workorder_tab.ID = $workorder_detail_table.Workorder_ID
                     
                    LEFT JOIN $product_table  ON $workorder_tab.product_ID = $product_table.ID
                    LEFT JOIN $employee_tab ON  $workorder_tab.EmployeeID = $employee_tab.ID
                    LEFT JOIN $supplier_tab ON  $workorder_tab.Subcontractor_ID = $supplier_tab.ID
                    LEFT JOIN $processmaster_tab ON  $workorder_tab.ProcessID = $processmaster_tab.ID
                    LEFT JOIN $bomprocessmaster_table ON $workorder_tab.product_ID = $bomprocessmaster_table.product_ID
                    LEFT JOIN $bomprocessdetail_table ON $bomprocessmaster_table.ID = $bomprocessdetail_table.BOMProcessMaster_ID
                    LEFT JOIN $rawmaterial_table ON $bomprocessdetail_table.rawmaterial_ID =  $rawmaterial_table.ID
                    LEFT JOIN $unit_tab ON $bomprocessdetail_table.unit_ID = $unit_tab.ID
                    WHERE
                               
            
                        $rawmeterial_issue_tab.ID= $data";      

     
                $indent_data = $dbutil->getSqlData($sqlsrr);               
                $indentsel_data  = $stmt->fetchAll();	
                $this->tpl->set('indent_data', $indentsel_data);
            
                //edit option     
                $this->tpl->set('message', 'You can edit Raw Material Issue form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/rawmaterial_issuse_form.php'));                    
                break;
            
            case 'editsubmit':             
                $data = trim($_POST['ycs_ID']);
                
                //mode of form submit
                $this->tpl->set('mode', 'edit');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);

                //Post data
                include_once 'util/genUtil.php';
                $util = new GenUtil();
                $form_post_data = $util->arrFltr($_POST);

                //        echo '<pre>';
                //    print_r($form_post_data); die;
                
                // $sqldet_del = "DELETE FROM $po_detail_tab WHERE purchaseorder_ID=$data";
                // $stmt = $this->db->prepare($sqldet_del);
                // $stmt->execute();   
                        
                        try{
                            
                                                    
                    
                            $completed_on=!empty($form_post_data['completed_on'])?date("Y-m-d", strtotime($form_post_data['completed_on'])):'';
                        
                        
                        $po_no= $form_post_data['po_no'];
                        $work_order_no= $form_post_data['work_order_no'];
                        $EmployeeID= $form_post_data['EmployeeID'];
                        $product_ID= $form_post_data['product_ID'];
                        $ProcessID= $form_post_data['ProcessID'];

                        $sql2 = "UPDATE $rawmeterial_issue_tab set
                                                                  po_no='$po_no',
                                                                  work_order_no='$work_order_no',
                                                                  EmployeeID=$EmployeeID,
                                                                  product_ID='$product_ID',
                                                                  ProcessID='$ProcessID'
                                 WHERE ID=$data";	
                        
                        $stmt1 = $this->db->prepare($sql2);
                        $stmt1->execute()     ;

                                // $stmt->execute();
                        // $sql_update="Update $material_inward_tab SET  MaterialNo='$MaterialNo',
                        // PurchaseNO='$PurchaseNO',
                        // Store='$Store',
                        // Purchase_Indent_No='$Purchase_Indent_No',
                        // Supplier_Name='$Supplier_Name'
                                                                
                                                        //  WHERE  ID=$data";      
                        // $stmt1 = $this->db->prepare($sql_update);
                        // $sql3 = "DELETE FROM $material_inward_detail_tab WHERE proforma_invoice_ID=$data";
                        //  $stmt3 = $this->db->prepare($sql3);

                        $sql3 = "DELETE FROM $rawmeterial_issue_det_tab WHERE rawmaterial_issue_id=$data";
                        $stmt3 = $this->db->prepare($sql3);

                    if($stmt3->execute()){ 
                        // if($stmt1->execute()){
                    FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                           
                            $rawmaterial_ID =$form_post_data['rawmaterial_ID_' . $entry_count];  
                            $unit_ID =$form_post_data['unit_ID_' . $entry_count];	
                             $availabelqty =$form_post_data['available_qty_' . $entry_count];
                            $rmtqty =$form_post_data['rmt_qty_' . $entry_count];
                            $issued_qty =$form_post_data['issued_qty_' . $entry_count];
                            $acceptedqty =$form_post_data['acceptedqty_' . $entry_count];
                            $excess_qty =$form_post_data['excess_qty_' . $entry_count];
                           
                            $sql_update="Update $stock_tab SET available_qty=(available_qty-'$acceptedqty') 
                                         WHERE 
                                         $stock_tab.rawmaterial_id=$rawmaterial_ID ";               
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                                    
                            if(!empty($rawmaterial_ID) && !empty($unit_ID)){
                                
                                  $sql_update="Update $stock_tab SET available_qty=(available_qty+'$issued_qty') 
                                               WHERE 
                                               $stock_tab.rawmaterial_id=$rawmaterial_ID ";               
                                               $update_stmt = $this->db->prepare($sql_update);
                                               $update_stmt->execute();

                                    $vals = "'" . $data . "'," .
                                    "'" . $rawmaterial_ID . "'," .
                                    "'" . $unit_ID . "'," .
                                    "'" . $availabelqty . "',".
                                    "'" . $rmtqty . "',".
                                    "'" . $issued_qty . "',".
                                    "'" . $excess_qty . "'";

                            $sql2 = "INSERT INTO $rawmeterial_issue_det_tab
                                    ( 

                                        `rawmaterial_issue_id`, 
                                        `rawmaterial_ID`,
                                        `unit_ID`,
                                        `stock_qty`,
                                        `given_qty`,
                                        `issued_qty`,
                                        `excess_qty`
                                        
                                    ) 
                            VALUES ($vals)";        

                            $stmt = $this->db->prepare($sql2);
                            $stmt->execute();              
                        }
                        }  

                        
                    }
                                    
                        $this->tpl->set('message', 'Raw Material Issue form edited successfully!');   
                        header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/rawmaterialissue');
                       
                        } catch (Exception $exc) {
                         //edit failed option
                        $this->tpl->set('message', 'Failed to edit, try again!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/rawmaterial_issuse_form.php'));
                        }

                break;
                
            case 'addsubmit':
                
                if (isset($crud_string)) {
                     
                    $form_post_data = $dbutil->arrFltr($_POST);
                    // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                     $completed_on=!empty($form_post_data['completed_on'])?date("Y-m-d", strtotime($form_post_data['completed_on'])):'';
                    // "'" . $dispatch_date . "'," .
                    $workid=$form_post_data['work_order_no'];
                    // echo '<pre>';
                    // print_r($form_post_data); die;
                    
                           $val =                           
                                    "'" . $form_post_data['po_no'] . "'," .
                                    "'" . $form_post_data['work_order_no'] . "'," .
                                    "'" . $form_post_data['EmployeeID'] . "'," .
                                    "'" . $form_post_data['person_name'] . "'," .
                                    "'" . $form_post_data['product_ID'] . "'," .
                                    "'" . $form_post_data['ProductName'] . "'," . 
                                    "'" . $form_post_data['product_number'] . "'," .      
                                    
                                    "'" . $form_post_data['ProcessID'] . "'," .    
                                    "'" . $form_post_data['process'] . "'," .    
                                    "'" . $form_post_data['product_size'] . "'," .    
                                    "'" . $form_post_data['thickness'] . "'," .    
                                    "'" . $form_post_data['color'] . "'," .    
                                    "'" . $form_post_data['design'] . "'," .    
                                    "'" . $form_post_data['quantity'] . "'," .    
                                    "'" . $form_post_data['completed_on'] . "'," .    
                                    "'" . $form_post_data['lay_up_sequence'] . "'," . 
                                    "'" . $form_post_data['details'] . "'," . 
                                    "'" . $form_post_data['remarks'] . "'," . 
                                    "'" . $form_post_data['statuss'] . "'," . 
                                    // "'" . $form_post_data['design'] . "'," . 

                                    
                                    // "'" . $form_post_data['ProcessID'] . "'," .
                                    "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                    "'" .  $this->ses->get('user')['ID'] . "'";

                       $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "rawmeterial_issue`
                                        ( 
                                        
                                       `po_no`,
                                        `work_order_no`,
                                        `EmployeeID`,
                                        `person_name`,
                                        `product_ID`,
                                       `product_name`,
                                       `product_number`, 
                                        `ProcessID`,
                                       `process`,
                                       `product_size`,
                                       `thickness`,
                                       `color`,
                                       `design`,
                                       `quantity`,
                                       `completed_on`,
                                       `lay_up_sequence`,
                                       `details`,
                                       `remarks`,
                                       `statuss`,

                                        `entity_ID`,
                                        `users_ID`
                                        ) 
                                VALUES ( $val )";                       
                                $stmt = $this->db->prepare($sql);      
                    if ($stmt->execute()) { 
                    
                    $lastInsertedID = $this->db->lastInsertId();  
                  FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {

                            //  $invoice_date=!empty($form_post_data['completed_on_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['completed_on_'. $entry_count])):'';
                             $rawmaterial_ID =$form_post_data['rawmaterial_ID_' . $entry_count]; 
                            $RMName =$form_post_data['RMName_' . $entry_count]; 
                            $Quantity2 =$form_post_data['Quantity2_' . $entry_count]; 
                            $unit_ID =$form_post_data['unit_ID_' . $entry_count];
                            $availabelqty =$form_post_data['available_qty_' . $entry_count];
                            $rmtqty =$form_post_data['rmt_qty_' . $entry_count];
                            $issued_qty =$form_post_data['issued_qty_' . $entry_count];
                            $excess_qty =$form_post_data['excess_qty_' . $entry_count];	
                            $unit_name =$form_post_data['UnitName_' . $entry_count];
                           
                            
                            $sql_update="Update $workorder_detail_table SET rmt_qty=(rmt_qty+'$issued_qty') WHERE $workorder_detail_table.Workorder_ID=$workid AND $workorder_detail_table.rawmaterial_ID=$rawmaterial_ID AND $workorder_detail_table.ItemStatus=1";   
					            $update_stmt = $this->db->prepare($sql_update);
					            $update_stmt->execute(); 
                        
                            
                           
                            
                            $vals = "'" . $lastInsertedID . "'," .
                                     "'" . $rawmaterial_ID . "'," .
                                     "'" . $RMName . "'," .
                                     "'" . $Quantity2 . "'," .
                                    "'" . $unit_ID . "'," .
                                    "'" . $unit_name . "'," .
                                    "'" . $availabelqty . "',".
                                    "'" . $rmtqty . "',".
                                    
                                    "'" . $issued_qty . "'," .
                                    "'" . $excess_qty . "'";
                                    // "'" . $unit . "'," .
                                    // "'" . $receivedqt . "'," .
                                    // "'" . $batch . "'," .
                                    // "'" . $costunit . "'," .
                                    // "'" . $total . "'," .
                                    // "'" . $supplier . "'," .
                                    // "'" . $invoice_date . "'" ;   
                      $sql2 = "INSERT INTO $rawmeterial_issue_det_tab
                                    ( 
                                        `rawmaterial_issue_id`,
                                        `rawmaterial_ID`, 
                                        `raw_materials`,
                                        `request_quantity`,
                                        `unit_ID`,
                                        `uom`,
                                        `stock_qty`,
                                        `given_qty`,
                                        
                                        `issued_qty`,
                                        `excess_qty`
                                       
                                    ) 
                            VALUES ($vals)";
                            $stmt = $this->db->prepare($sql2);
                        //$stmt->execute();    
                        
                         if($stmt->execute()){
                            // $sql_update="UPDATE $stock_table
                            //                  LEFT JOIN $rawmeterial_issue_det_tab
                            //                  ON $rawmeterial_issue_det_tab.rawmaterial_ID=$stock_table.rawmaterial_id
                            //                  SET $stock_table.available_qty=($stock_table.available_qty-$issued_qty)
                            //                  WHERE $stock_table.rawmaterial_id=$rawmeterial_issue_det_tab.rawmaterial_ID";          
                                         
                            //              $update_stmt = $this->db->prepare($sql_update);
                            //              $update_stmt->execute();
                             if($issued_qty>0)
                             {   
                            $sql_update="UPDATE ycias_stock                                        
                                             SET available_qty=(available_qty-$issued_qty)
                                             WHERE rawmaterial_id =  $rawmaterial_ID AND entity_ID = $entityID";                                                   
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                             }           
                            $sqlsrr = "SELECT * FROM  ycias_material_inward_detail WHERE ycias_material_inward_detail.item_id = $rawmaterial_ID";
                                 $qc_det_data = $dbutil->getSqlData($sqlsrr);    
                                         // print_r($qc_det_data[0]['item_id']); die;
                                 $item_id=(count($qc_det_data)>0) ? $qc_det_data[0]['cost_unit']:''; 
                                         
                             $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType, raw_material_ID, raw_material_name, stockout, unit_name,stock_value, entity_ID, users_ID)
                                         VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Rawmaterial issue', $rawmaterial_ID, '$hai', $issued_qty, '$unit_name',$item_id, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                         
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                          }
                       
                           
                        }  
                        
                        
                      
                    }
                     $sqlsrr = "SELECT  * FROM  $workorder_detail_table WHERE $workorder_detail_table.Workorder_ID = '$workid'";
                        $workdet_data = $dbutil->getSqlData($sqlsrr);
                        $checkunique=[];
                        
                             if(!empty($workdet_data)){
                                 
                                 foreach($workdet_data as $k=>$v){
                                     
                                     if($v['rmt_qty']==$v['Quantity2']){
                                         $checkunique[]=1;
                                         $sql_update="Update $workorder_detail_table SET ItemStatus='2' WHERE  $workorder_detail_table.Workorder_ID=$workid AND $workorder_detail_table.rawmaterial_ID=$v[rawmaterial_ID]";
                                         $stmt1 = $this->db->prepare($sql_update);
                                         $stmt1->execute();
                                         
                                     }else{
                                         $checkunique[]=2; 
                                     }
                                
                                     
                                 }
                                
                                 if(count($checkunique)>0){
                                    if(count(array_unique($checkunique))==1 && end($checkunique) == 1){
                                     $sql_update="Update $workorder_tab SET rmt_Status='2' WHERE  $workorder_tab.ID=$workid";
                                     $stmt1 = $this->db->prepare($sql_update);
                                     $stmt1->execute();
                                    } 
                                 }
                             }
                                                       
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('message', '- Success -');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/rawmaterialissue');
                 } else {
                        //edit option
                        //if submit failed to insert form
                        $this->tpl->set('message', 'Failed to submit!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/rawmaterial_issuse_form.php'));
                 }
                 
                break;

            case 'add':
                $this->tpl->set('mode', 'add');
                $this->tpl->set('page_header', 'Store');
                $MaterialNo=$dbutil->keyGeneration('materialinward','MNO','','MaterialNo');
                $this->tpl->set('MaterialNo', $MaterialNo);
                $this->tpl->set('content', $this->tpl->fetch('factory/form/rawmaterial_issuse_form.php'));
                break;

            default:
                /*
                 * List form
                 */
                 
                ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
     $colArr = array(
            "$rawmeterial_issue_tab.ID",
           
            "$workorder_tab.WorkOrderNo",
            
           
            
            // "$po_master_tab.ID"

        ); 
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

            if (strpos($colNames, 'DATE') !== false) {
                list($colNames,$x) = $dbutil->dateFilterFormat($colNames);                    
            }else {
                $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);        		    
            }

              if ('' != $x) {
               $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
       IF (count($wsarr) >= 1) {
            $whereString = ' AND '. implode(' AND ', $wsarr);
        }
       } else {
         $whereString ="ORDER BY $rawmeterial_issue_tab.ID DESC";
       }
       $productionorder_tab = $this->crg->get('table_prefix') . 'productionorder';
        $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
       
    $sql = "SELECT "
                . implode(',',$colArr)
                . " FROM $rawmeterial_issue_tab, $workorder_tab "
                . " WHERE "
                . " $rawmeterial_issue_tab.work_order_no = $workorder_tab.ID "
                . " AND "
                . " $rawmeterial_issue_tab.entity_ID = $entityID"
                . " $whereString";         
     

            $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
        /*
         * SET DATA TO TEMPLATE
                    */
       $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
     
     
        $this->tpl->set('table_columns_label_arr', array('ID','Work Order Number'));
        
        /*
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
        // $this->tpl->set('dcpdf','Generate Pdf');            
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
               
        //////////////////////close//////////////////////////////////////  
                 
                include_once $this->tpl->path . '/factory/form/rawmaterial_issue_crud_form.php';
                $cus_form_data = Form_Elements::data($this->crg);
                include_once 'util/crud3_1.php';
                new Crud3($this->crg, $cus_form_data);
                break;
        }

    ///////////////Use different template////////////////////
    // $this->tpl->set('master_layout', 'layout_chart.php'); 
    $this->tpl->set('master_layout', 'layout_datepicker.php'); 
    ///////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then /////
    ///////////////////////////////////////////////////////////////////
 } else {
         if ($this->ses->get('user')['ID']) {
             $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
         } else {
             header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
         }
     }
}  

////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////
//////////////////////////  stock return
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////




function stockadjustment(){
     
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
         
        /////////////////////////////////////////////////////////////////
        //////////////////////////////access condition applied///////////
        /////////////////////////////////////////////////////////////////
        
        include_once 'util/DBUTIL.php';
        $dbutil = new DBUTIL($this->crg);
        
        include_once 'util/genUtil.php';
        $util = new GenUtil();
         
        $entityID = $this->ses->get('user')['entity_ID'];
        $userID = $this->ses->get('user')['ID'];
        $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
        $unit_table = $this->crg->get('table_prefix') . 'unit';
        $po_detail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
        $po_master_tab = $this->crg->get('table_prefix') . 'purchaseorder';
        $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
        $indent_master_tab = $this->crg->get('table_prefix') . 'purchaseindent';
        $indent_det_tab = $this->crg->get('table_prefix') . 'purchaseindentdetail';
        $state_tab = $this->crg->get('table_prefix') . 'state';
        $unit_tab = $this->crg->get('table_prefix') . 'unit';
        $material_inward_tab = $this->crg->get('table_prefix') . 'material_inward';
        $material_inward_detail_tab = $this->crg->get('table_prefix') . 'material_inward_detail';
        $rawmeterial_issue_tab = $this->crg->get('table_prefix') . 'rawmeterial_issue';
        $rawmeterial_issue_det_tab = $this->crg->get('table_prefix') . 'rawmeterial_issue_detail';
        $stockreturn_mast_tab = $this->crg->get('table_prefix') . 'stockreturn_mast';
        $stockreturn_detail_tab = $this->crg->get('table_prefix') . 'stockreturn_detail';
        $stockadjustment_tab = $this->crg->get('table_prefix') . 'stockadjustment';
        $productionorder_tab = $this->crg->get('table_prefix') . 'productionorder';
        $rawmaterialsubtype_tab = $this->crg->get('table_prefix') . 'rawmaterialsubtype';
        $rawmaterialtype_tab = $this->crg->get('table_prefix') . 'rawmaterialtype';
        $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
        $employee_tab = $this->crg->get('table_prefix') . 'employee';
        $supplier_table = $this->crg->get('table_prefix') . 'supplier';
        $stock_tab = $this->crg->get('table_prefix') . 'stock';
        $stock_trans_tab = $this->crg->get('table_prefix') . 'stock_trans';
        
        $Employeetype_data = array(array("ID"=>"1","Title"=>"Employee"),array("ID"=>"2","Title"=>"Sub Contractor"));
        $this->tpl->set('Employeetype_data', $Employeetype_data);
        
        $adjustment_data = array(array("ID"=>"1","Title"=>"stockin"),array("ID"=>"2","Title"=>"stockout"));
        $this->tpl->set('adjustment_data', $adjustment_data);
        //indent table data
       
        $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1 and $indent_master_tab.PI_Status=1 ORDER BY $indent_master_tab.ID DESC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $indent_data  = $stmt->fetchAll();	
        $this->tpl->set('indent_data', $indent_data);

         //workOrder table data
       
         $sql = "SELECT ID,WorkOrderNo FROM $workorder_tab WHERE entity_ID=$entityID";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $workorder_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('workorder_tab_data', $workorder_tab_data);


            //Employee table data
       
             $employee_sql = "SELECT ID,EmpName FROM $employee_tab";
             $stmt = $this->db->prepare($employee_sql);            
             $stmt->execute();
             $employee_data  = $stmt->fetchAll();	
             $this->tpl->set('employee_data', $employee_data);
             
              $employee_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE SupplierType='Sub contractor'";
              $stmt = $this->db->prepare($employee_sql);            
              $stmt->execute();
              $subcontractor_data  = $stmt->fetchAll();	
              $this->tpl->set('subcontractor_data', $subcontractor_data);


         //perchase Order table data

        
       
         $sql = "SELECT ID,MaterialNo FROM $material_inward_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $material_inward_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('material_inward_tab_data', $material_inward_tab_data);

          //rawmaterial subtype table data

          $sql = "SELECT ID,MaterialNo FROM $material_inward_tab";
          $stmt = $this->db->prepare($sql);            
          $stmt->execute();
          $material_inward_tab_data  = $stmt->fetchAll();	
          $this->tpl->set('material_inward_tab_data', $material_inward_tab_data);
          
        //   rawmaterial Category type
        
          $sql = "SELECT ID,RawMaterialType FROM $rawmaterialtype_tab";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();     
        $rawmaterialtype_tab_data  = $stmt->fetchAll();	
        $this->tpl->set('rawmaterialtype_tab_data', $rawmaterialtype_tab_data);


        // rawmaterial sub Material

          $sql = "SELECT ID,RawMaterialSubType FROM $rawmaterialsubtype_tab";
          $stmt = $this->db->prepare($sql);            
          $stmt->execute();     
          $rawmaterialsubtype_tab_data  = $stmt->fetchAll();	
          $this->tpl->set('rawmaterialsubtype_tab_data', $rawmaterialsubtype_tab_data);

        //   $sql = "SELECT ID,$rawmaterialtype_tab.RawMaterialType,$rawmaterialsubtype_tab.RawMaterialSubType FROM $rawmaterial_table LEFT JOIN $rawmaterialtype_tab ON $rawmaterialtype_tab.";
        //   $stmt = $this->db->prepare($sql);            
        //   $stmt->execute();   
        //   $rawmaterialsubtype_tab_data  = $stmt->fetchAll();	
        //   $this->tpl->set('rawmaterialsubtype_tab_data', $rawmaterialsubtype_tab_data);

         
         //production Order table data
       
         $sql = "SELECT ID,PdnOrderNo FROM $productionorder_tab WHERE entity_ID=$entityID";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $productionorder_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('productionorder_tab_data', $productionorder_tab_data);

          //Employee table data
       
          $sql = "SELECT ID,EmpName FROM $employee_tab";
          $stmt = $this->db->prepare($sql);            
          $stmt->execute();
          $employee_tab_data  = $stmt->fetchAll();	
          $this->tpl->set('employee_tab_data', $employee_tab_data);

         //perchase Order table data
       
         $sql = "SELECT ID,PurchaseOrderNo,purchaseindent_ID FROM $po_master_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $po_master_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('po_master_tab', $po_master_tab_data);
        
        //supplier table data
       
        $sql = "SELECT ID,SupplierName FROM $supplier_tab ORDER BY $supplier_tab.SupplierName ASC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $supplier_data  = $stmt->fetchAll();	
        $this->tpl->set('supplier_data', $supplier_data);
        
        //rawmaterial table data
       
        $sql = "SELECT ID,RMName FROM $rawmaterial_table";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $rawmaterial_data  = $stmt->fetchAll();	
        $this->tpl->set('rawmaterial_data', $rawmaterial_data);
        
        //unit table data
       
        $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
        $stmt = $this->db->prepare($pdt_sql);            
        $stmt->execute();
        $unit_data  = $stmt->fetchAll();	
        $this->tpl->set('unit_data', $unit_data);
 
        $this->tpl->set('page_title', 'Stock Adjustment');	          
        $this->tpl->set('page_header', 'Store');
        
        //Add Role when u submit the add role form
        $thisPageURL = $this->crg->get('route')['base_path'] . '/' . $this->crg->get('route')['module'] . '/' . $this->crg->get('route')['controller'] . '/' . $this->crg->get('route')['action'];

        $crud_string = null;

        if (isset($_POST['req_from_list_view'])) {
            $crud_string = strtolower($_POST['req_from_list_view']);
        }              
        
        //Edit submit
        if (!empty($_POST['edit_submit_button']) && $_POST['edit_submit_button'] == 'edit') {
            $crud_string = 'editsubmit';
        }
        
        //Add submit
        if (!empty($_POST['add_submit_button']) && $_POST['add_submit_button'] == 'add') {
            $crud_string = 'addsubmit';
        }
        

        switch ($crud_string) {
             case 'delete':                    
                  $data = trim($_POST['ycs_ID']);
                   
                   
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                   
                }
               
                // $sqlsrr = "SELECT * FROM $po_master_tab WHERE ID=$data";
                // $po_data = $dbutil->getSqlData($sqlsrr); 
                
                // $sqlsrr = "SELECT * FROM  $po_detail_tab WHERE purchaseorder_ID=$data";
                // $podet_data = $dbutil->getSqlData($sqlsrr); 
                
                // $purchase_indentno=(count($po_data)>0)?$po_data[0]['purchaseindent_ID']:'';
                 
                // $sqlsrr = "SELECT  * FROM  $indent_det_tab WHERE $indent_det_tab.purchaseindent_ID = '$purchase_indentno'";
                // $indentdet_data = $dbutil->getSqlData($sqlsrr);
                
                 
                //  if(!empty($purchase_indentno)){
                //      $sql_update="Update $indent_master_tab SET $indent_master_tab.PI_Status=1 WHERE  $indent_master_tab.ID=$purchase_indentno";
                //                  $masterstmt1 = $this->db->prepare($sql_update);
                //                  $masterstmt1->execute();
                                 
                //     foreach($podet_data as $k=>$v){
                        
                //         foreach($indentdet_data as $kk=>$value){
                            
                //             if(($value['rawmaterial_ID']==$v['rawmaterial_ID'])){
                                
                //                 $sql_updates="Update $indent_det_tab SET $indent_det_tab.ItemStatus=1,$indent_det_tab.RaisedPOQty=(RaisedPOQty-$v[POQuantity]) WHERE $indent_det_tab.ID=$value[ID]";
                //                 $detstmt1 = $this->db->prepare($sql_updates);
                //               $detstmt1->execute();
                             
                //             }
                            
                //         }
                        
                //     }
                                 
                      
                //  }
                 
                // $sqldetdelete="DELETE $stockreturn_mast_tab,$stockreturn_detail_tab FROM $stockreturn_mast_tab
                //                LEFT JOIN  $stockreturn_detail_tab ON $stockreturn_mast_tab.ID=$stockreturn_detail_tab.stockreturn_id 
                //                WHERE $stockreturn_mast_tab.ID=$data";  

                $sql_update="UPDATE ycias_stock
                LEFT JOIN ycias_stockadjustment
                ON ycias_stockadjustment.item_name=ycias_stock.rawmaterial_id
                SET ycias_stock.available_qty=(ycias_stock.available_qty-ycias_stockadjustment.quantity_to_be_adjust)
                WHERE ycias_stock.rawmaterial_id=ycias_stockadjustment.item_name AND $stockadjustment_tab.ID=$data AND entity_ID = $entityID";          
                $update_stmt = $this->db->prepare($sql_update);
                $update_stmt->execute();
                
                
                $sqldetdelete="DELETE $stockadjustment_tab FROM $stockadjustment_tab
                               
                               WHERE $stockadjustment_tab.ID=$data";  
                               
                $stmt = $this->db->prepare($sqldetdelete);            
                    
                    if($stmt->execute()){
                    $this->tpl->set('message', 'Stock Return deleted successfully');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/stockadjustment');
                    // $this->tpl->set('label', 'List');
                    // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    } 
                    
        break;
            case 'view':                    
                $data = trim($_POST['ycs_ID']);
             
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to view!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', 'view');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         
                            
                //  $sqlsrr = "SELECT  $po_master_tab.*,
                //                    $po_detail_tab.*,
                //                    $rawmaterial_table.RMName,
                //                    $supplier_tab.SupplierName,
                //                    $supplier_tab.AddressLine1,
                //                    $supplier_tab.AddressLine2,
                //                    $supplier_tab.City,
                //                    $state_tab.StateName,
                //                    $unit_table.UnitName
                //            FROM  $po_master_tab
                //            LEFT JOIN $po_detail_tab ON $po_master_tab.ID=$po_detail_tab.purchaseorder_ID
                //            LEFT JOIN $rawmaterial_table ON  $po_detail_tab.rawmaterial_ID=$rawmaterial_table.ID
                //            LEFT JOIN $supplier_tab  ON $supplier_tab.ID=$po_master_tab.supplier_ID 
                //            LEFT JOIN $state_tab  ON $state_tab.ID=$supplier_tab.State_ID 
                //            LEFT JOIN $unit_table  ON $unit_table.ID=$po_detail_tab.unit_ID 
                //            WHERE $po_master_tab.ID = '$data' 
                //            ORDER BY $po_detail_tab.ID ASC";
                // $indent_data = $dbutil->getSqlData($sqlsrr); 
               
                // $sqlsrr="SELECT * FROM $stockreturn_mast_tab,$stockreturn_detail_tab
                // WHERE  $stockreturn_mast_tab.ID = $stockreturn_detail_tab.stockreturn_id
                // AND $stockreturn_mast_tab.ID = $data"; 
                
                
                $sqlsrr="SELECT * FROM $stockadjustment_tab
                         WHERE  $stockadjustment_tab.ID = $data AND entity_ID = $entityID";  
                
                $indent_data = $dbutil->getSqlData($sqlsrr);               
              
                
                        
               
                
                
                //edit option     
                $this->tpl->set('message', 'You can view Stock Return form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/stockadjustment_form.php'));                    
                break;
            
            case 'edit':                    
                $data = trim($_POST['ycs_ID']);
                $mode='edit';
                if(isset($_SESSION['ycs_ID']))
                {
                    $data = trim($_SESSION['ycs_ID']);
                    unset($_SESSION['ycs_ID']);
                    $mode='Confirm';
                }
           // var_dump($data);
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to edit!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', $mode);
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         
                            
                // $sqlsrr = "SELECT  *,$enquiry_tab.PONo,
                //            FROM  $po_master_tab
                //            LEFT JOIN $po_detail_tab ON $po_master_tab.ID=$po_detail_tab.purchaseorder_ID
                //            LEFT JOIN $rawmaterial_table ON  $po_detail_tab.rawmaterial_ID=$rawmaterial_table.ID
                //            LEFT JOIN $supplier_tab  ON $supplier_tab.ID=$po_master_tab.supplier_ID 
                //            LEFT JOIN $state_tab  ON $state_tab.ID=$supplier_tab.State_ID 
                //            LEFT JOIN $unit_table  ON $unit_table.ID=$po_detail_tab.unit_ID 
                //            WHERE $po_master_tab.ID = '$data' 
                //            ORDER BY $po_detail_tab.ID ASC";


            //    $sqlsrr="SELECT * FROM $stockreturn_mast_tab,$stockreturn_detail_tab
            //     WHERE  $stockreturn_mast_tab.ID = $stockreturn_detail_tab.stockreturn_id
            //     AND $stockreturn_mast_tab.ID = $data";         

            $sqlsrr="SELECT * FROM $stockadjustment_tab
                         WHERE  $stockadjustment_tab.ID = $data"; 

                $indent_data = $dbutil->getSqlData($sqlsrr);               
                $indentsel_data  = $stmt->fetchAll();	
                $this->tpl->set('indent_data', $indentsel_data);
            
                //edit option     
                $this->tpl->set('message', 'You can edit Stock Return form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/stockadjustment_form.php'));                   
                break;
            
            case 'editsubmit':             
                $data = trim($_POST['ycs_ID']);
                
                //mode of form submit
                $this->tpl->set('mode', 'edit');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);

                //Post data
                include_once 'util/genUtil.php';
                $util = new GenUtil();
                $form_post_data = $util->arrFltr($_POST);

                //        echo '<pre>';
                //    print_r($form_post_data); die;
                
                // $sqldet_del = "DELETE FROM $po_detail_tab WHERE purchaseorder_ID=$data";
                // $stmt = $this->db->prepare($sqldet_del);
                // $stmt->execute();   
                        
                        try{
                            
                                                    
                    
        
                        
                        // $store= $form_post_data['store'];
                        $stock_return_no= $form_post_data['stock_return_no'];
                        $item_type= $form_post_data['item_type'];
                        $category_type_id= $form_post_data['category_type_id'];
                        $item_name= $form_post_data['item_name'];
                        $batch_no= $form_post_data['batch_no'];  
                       $EmployeeType= $form_post_data['EmployeeType'];
                        $EmployeeID= $form_post_data['EmployeeID'];
                        $prod_order_no= $form_post_data['prod_order_no'];
                        $work_order_no= $form_post_data['work_order_no'];
                        $availabel_qty= $form_post_data['availabel_qty'];
                        $adjustment_type= $form_post_data['adjustment_type'];
                        $quantity_to_be_adjust= $form_post_data['quantity_to_be_adjust'];
                        $acceptedqty =$form_post_data['acceptedqty_'];
                        $reasone_for_adjust= $form_post_data['reasone_for_adjust'];
                        $comment= $form_post_data['comment'];
                        // $design= $form_post_data['design']; 
                        // $quantity= $form_post_data['quantity']; 
                        // $completed_on= $form_post_data['completed_on']; 
                        // $lay_up_sequence= $form_post_data['lay_up_sequence'];   
                        // $details= $form_post_data['details'];  
                        // $remarks= $form_post_data['remarks'];  
                        // $created_by= $form_post_data['created_by']; 
                        
                          $sql_update="Update $stock_tab SET available_qty=(available_qty-'$acceptedqty') 
                                       WHERE 
                                       $stock_tab.rawmaterial_id=$item_name ";               
                                       $update_stmt = $this->db->prepare($sql_update);
                                       $update_stmt->execute();


                       $sql2 = "UPDATE $stockadjustment_tab set 
                                                                    -- store='$store',
                                                                    stock_return_no = '$stock_return_no',
                                                                    item_type = '$item_type',
                                                                    category_type_id='$category_type_id',
                                                                    item_name = '$item_name',
                                                                    batch_no = '$batch_no',
                                                                    EmployeeType='$EmployeeType',
                                                                    EmployeeID='$EmployeeID',
                                                                    prod_order_no = '$prod_order_no',
                                                                    work_order_no = '$work_order_no',
                                                                    availabel_qty = '$availabel_qty',
                                                                    adjustment_type = '$adjustment_type',
                                                                    quantity_to_be_adjust = '$quantity_to_be_adjust',
                                                                    reasone_for_adjust = '$reasone_for_adjust',
                                                                    comment = '$comment'
                                                                    WHERE ID = $data";	
                        
                        $stmt = $this->db->prepare($sql2);     
                       // $stmt->execute();               
                     if($stmt->execute()) {
                            $sql_update="Update $stock_tab SET available_qty=(available_qty+'$quantity_to_be_adjust') 
                                         WHERE 
                                         $stock_tab.rawmaterial_id=$item_name ";               
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                           }  
                                    
                        $this->tpl->set('message', 'Stock Return form edited successfully!');   
                        header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/stockadjustment');
                       
                        } catch (Exception $exc) {
                         //edit failed option
                        $this->tpl->set('message', 'Failed to edit, try again!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/stockadjustment_form.php'));
                        }

                break;
                
            case 'addsubmit':
                
                if (isset($crud_string)) {
                     
                    $form_post_data = $dbutil->arrFltr($_POST);
                    // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                    //  $completed_on=!empty($form_post_data['completed_on'])?date("Y-m-d", strtotime($form_post_data['completed_on'])):'';
                    // "'" . $dispatch_date . "'," .
                    // $purchase_indentno=$form_post_data['purchaseindent_ID'];
                //     echo '<pre>';
                //    print_r($form_post_data); die;
                    
                  $availabel_qty=$form_post_data['quantity_to_be_adjust'];
                  $item_name     =$form_post_data['item_name'];
                  $comment=$form_post_data['comment'];
                  $adjustment_type = $form_post_data['adjustment_type'];
                  
                            $val = 
                                    //  "'" . $form_post_data['store'] . "'," .                                  
                                    "'" . $form_post_data['stock_return_no'] . "'," .
                                    "'" . $form_post_data['item_type'] . "'," .
                                     "'" . $form_post_data['category_type_id'] . "'," .
                                    "'" . $form_post_data['item_name'] . "'," .
                                    "'" . $form_post_data['batch_no'] . "'," .
                                     "'" . $form_post_data['EmployeeType'] . "'," .
                                     "'" . $form_post_data['EmployeeID'] . "'," .
                                     "'" . $form_post_data['Subcontractor_ID'] . "'," .
                                    "'" . $form_post_data['prod_order_no'] . "'," .
                                    "'" . $form_post_data['work_order_no'] . "'," .
                                    "'" . $form_post_data['availabel_qty'] . "'," .
                                    "'" . $form_post_data['adjustment_type'] . "'," .
                                    "'" . $form_post_data['quantity_to_be_adjust'] . "'," .
                                    "'" . $form_post_data['reasone_for_adjust'] . "'," .
                                    "'" . $form_post_data['comment'] . "'," .
                                    // "'" . $form_post_data['design'] . "'," .
                                    // "'" . $form_post_data['quantity'] . "'," .
                                    // "'" . $form_post_data['completed_on'] . "'," .
                                    // "'" . $form_post_data['lay_up_sequence'] . "'," .
                                    // "'" . $form_post_data['details'] . "'," .
                                    // "'" . $form_post_data['remarks'] . "'," .
                                    // "'" . $form_post_data['created_by'] . "'," .
                                    "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                    "'" .  $this->ses->get('user')['ID'] . "'";

                       $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "stockadjustment`
                                        ( 
                                     
                                        `stock_return_no`,
                                        `item_type`,
                                        `category_type_id`,
                                        `item_name`,
                                        `batch_no`,
                                       `EmployeeType`, 
                                        `EmployeeID`, 
                                        `Subcontractor_ID`, 
                                        `prod_order_no`,
                                        `work_order_no`,
                                        `availabel_qty`,
                                        `adjustment_type`,
                                        `quantity_to_be_adjust`,
                                        `reasone_for_adjust`,
                                        `comment`,
                                        
                                        `entity_ID`,
                                        `users_ID`
                                        ) 
                                VALUES ( $val )";
                                $stmt = $this->db->prepare($sql);     
                               // $stmt->execute();  
                               
                                if($stmt->execute()){
                            //   $sql_update="UPDATE ycias_stock
                            //                         LEFT JOIN ycias_stockadjustment
                            //                         ON ycias_stockadjustment.item_name=ycias_stock.rawmaterial_id
                            //                         SET ycias_stock.available_qty=(ycias_stock.available_qty+$availabel_qty)
                            //                         WHERE ycias_stock.rawmaterial_id=ycias_stockadjustment.item_name";          
                                                
                            //                     $update_stmt = $this->db->prepare($sql_update);
                            //                     $update_stmt->execute();
                                                
                                // $sql_update="UPDATE ycias_stock                                        
                                //              SET available_qty=(available_qty+$availabel_qty)
                                //              WHERE rawmaterial_id =  $item_name";                                                   
                                //              $update_stmt = $this->db->prepare($sql_update);
                                //              $update_stmt->execute();
                                
                                if($adjustment_type == '1')
                                    {
                               $sql_update="UPDATE ycias_stock
                                                    
                                                    SET available_qty=(available_qty + $availabel_qty)
                                                    WHERE rawmaterial_id= $item_name AND entity_ID = $entityID";          
                                                
                                                $update_stmt = $this->db->prepare($sql_update);
                                                $update_stmt->execute();
                                    }
                                    if($adjustment_type == '2')
                                    {
                                        $sql_update="UPDATE ycias_stock
                                                    
                                                    SET available_qty=(available_qty - $availabel_qty)
                                                    WHERE rawmaterial_id= $item_name AND entity_ID = $entityID";          
                                                
                                                $update_stmt = $this->db->prepare($sql_update);
                                                $update_stmt->execute();
                                    }
                                                
                                 $sqlsrr = "SELECT * FROM  ycias_material_inward_detail WHERE ycias_material_inward_detail.item_id = $item_name";
                                 $qc_det_data = $dbutil->getSqlData($sqlsrr);    
                                         // print_r($qc_det_data[0]['item_id']); die;
                                 $item_id=(count($qc_det_data)>0) ? $qc_det_data[0]['cost_unit']:''; 
                                                
                                $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType, raw_material_ID, stockin,remarks,stock_value, entity_ID, users_ID)
                                                VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Stock Adjustment', $item_name, $availabel_qty,'$comment',$item_id, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                            
                                                $update_stmt = $this->db->prepare($sql_update);
                                                $update_stmt->execute();
                                 }
                    // if ($stmt->execute()) { 
                    
                //     $lastInsertedID = $this->db->lastInsertId();  
                //   FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {

                //             //  $invoice_date=!empty($form_post_data['completed_on_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['completed_on_'. $entry_count])):'';
                //             $raw_material =$form_post_data['raw_material_' . $entry_count]; 
                //             $size =$form_post_data['size_' . $entry_count]; 
                //             $unit =$form_post_data['unit_' . $entry_count];
                //             $qty_issued =$form_post_data['qty_issued_' . $entry_count];								
                //             $batch_no =$form_post_data['batch_no_' . $entry_count];
                //             $total_qty_retn =$form_post_data['total_qty_retn_' . $entry_count];
                //             $qty_return =$form_post_data['qty_return_' . $entry_count];
                //             // $costunit =$form_post_data['cost_unit_' . $entry_count];
                //             // $total =$form_post_data['total_' . $entry_count];
                //             // $supplier =$form_post_data['supplier_invoice_no_' . $entry_count];
                //             // $invoice =$form_post_data['invoice_date_' . $entry_count];
                            
                            
                        
                            
                           
                            
                //             $vals = "'" . $lastInsertedID . "'," .
                //                     "'" . $raw_material . "'," .
                //                     "'" . $size . "'," .
                //                     "'" . $unit . "'," .
                //                     "'" . $qty_issued . "',".
                //                     "'" . $batch_no . "'," .
                //                     "'" . $total_qty_retn . "'," .
                //                     "'" . $qty_return . "'" ;
                //                     // "'" . $costunit . "'," .
                //                     // "'" . $total . "'," .
                //                     // "'" . $supplier . "'," .
                //                     // "'" . $invoice_date . "'" ;   
                //                          $sql2 = "INSERT INTO $stockreturn_detail_tab
                //                     ( 
                //                         `stockreturn_id`, 
                //                         `raw_material`,
                //                         `size`,
                //                         `unit`,
                //                         `qty_issued`,
                //                         `batch_no`,
                //                         `total_qty_retn`,
                //                         `qty_return`
                                       
                //                     ) 
                //             VALUES ($vals)";
                //             $stmt = $this->db->prepare($sql2);
                //         $stmt->execute();                           
                       
                        
                           
                //         }   
                        
                        
                      
                    // }
                                                       
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('message', '- Success -');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/stockadjustment');
                 } else {
                        //edit option
                        //if submit failed to insert form
                        $this->tpl->set('message', 'Failed to submit!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/stockadjustment_form.php'));
                 }
                 
                break;

            case 'add':
                $this->tpl->set('mode', 'add');
                $this->tpl->set('page_header', 'Store');
                $stock_return_no=$dbutil->keyGeneration('stockadjustment','SRN','','stock_return_no');
                $this->tpl->set('stock_return_no', $stock_return_no);
                $this->tpl->set('content', $this->tpl->fetch('factory/form/stockadjustment_form.php'));
                break;

            default:
                /*
                 * List form
                 */
                 
                ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
     $colArr = array(
            "$stockadjustment_tab.ID",
            // "$stockadjustment_tab.store",
            "$stockadjustment_tab.stock_return_no",
            "$stockadjustment_tab.item_name"
           
            
            // "$po_master_tab.ID"

        );
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

            if (strpos($colNames, 'DATE') !== false) {
                list($colNames,$x) = $dbutil->dateFilterFormat($colNames);                    
            }else {
                $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);        		    
            }

              if ('' != $x) {
               $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
       IF (count($wsarr) >= 1) {
            $whereString = ' AND '. implode(' AND ', $wsarr);
        }
       } else {
         $whereString ="ORDER BY $stockadjustment_tab.ID DESC";
       }
       
    $sql = "SELECT "
                . implode(',',$colArr)
                . " FROM $stockadjustment_tab "
                . " WHERE "
                . " $stockadjustment_tab.entity_ID = $entityID"
                . " $whereString"; 
        
     

            $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
        /*
         * SET DATA TO TEMPLATE
                    */
       $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
     
     
        $this->tpl->set('table_columns_label_arr', array('ID','Stock Return No','Item Type'));
        
        /*
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
        // $this->tpl->set('dcpdf','Generate Pdf');            
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
               
        //////////////////////close//////////////////////////////////////  
                 
                include_once $this->tpl->path . '/factory/form/stockadjustment_crud_form.php';
                $cus_form_data = Form_Elements::data($this->crg);
                include_once 'util/crud3_1.php';
                new Crud3($this->crg, $cus_form_data);
                break;
        }

    ///////////////Use different template////////////////////
    // $this->tpl->set('master_layout', 'layout_chart.php'); 
    $this->tpl->set('master_layout', 'layout_datepicker.php'); 
    ///////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then /////
    ///////////////////////////////////////////////////////////////////
 } else {
         if ($this->ses->get('user')['ID']) {
             $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
         } else {
             header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
         }
     }
}

//////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
//////////////////////////  inventory Transfer

function inventorytransfer(){
     
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
         
        /////////////////////////////////////////////////////////////////
        //////////////////////////////access condition applied///////////
        /////////////////////////////////////////////////////////////////
        
        include_once 'util/DBUTIL.php';
        $dbutil = new DBUTIL($this->crg);
        
        include_once 'util/genUtil.php';
        $util = new GenUtil();
         
        $entityID = $this->ses->get('user')['entity_ID'];
        $userID = $this->ses->get('user')['ID'];
        $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
        $unit_table = $this->crg->get('table_prefix') . 'unit';
        $po_detail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
        $po_master_tab = $this->crg->get('table_prefix') . 'purchaseorder';
        $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
        $indent_master_tab = $this->crg->get('table_prefix') . 'purchaseindent';
        $indent_det_tab = $this->crg->get('table_prefix') . 'purchaseindentdetail';
        $state_tab = $this->crg->get('table_prefix') . 'state';
        $unit_tab = $this->crg->get('table_prefix') . 'unit';
        $material_inward_tab = $this->crg->get('table_prefix') . 'material_inward';
        $material_inward_detail_tab = $this->crg->get('table_prefix') . 'material_inward_detail';
        $rawmeterial_issue_tab = $this->crg->get('table_prefix') . 'rawmeterial_issue';
        $rawmeterial_issue_det_tab = $this->crg->get('table_prefix') . 'rawmeterial_issue_detail';
        $stockreturn_mast_tab = $this->crg->get('table_prefix') . 'stockreturn_mast';
        $stockreturn_detail_tab = $this->crg->get('table_prefix') . 'stockreturn_detail';
        $inventory_transfer_tab = $this->crg->get('table_prefix') . 'inventory_transfer';
        $inventory_transfer_detail_tab = $this->crg->get('table_prefix') . 'inventory_transfer_detail';
        $stock_tab = $this->crg->get('table_prefix') . 'stock';
        $stock_trans_tab = $this->crg->get('table_prefix') . 'stock_trans';
        $entity_tab = $this->crg->get('table_prefix') . 'entity';
        
        
         // enttity Table

        $sql = "SELECT ID,Title FROM $entity_tab";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $entity_data  = $stmt->fetchAll();	
        $this->tpl->set('entity_data', $entity_data);
        
         // entity Set
        $this->tpl->set('entityID', $entityID);
 
        if($entityID == 13)
        {
            $this->tpl->set('area', 14);
        }
        else{
            
            $this->tpl->set('area',13);      
        
        }
        // 
        //indent table data
       
        $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1 and $indent_master_tab.PI_Status=1 ORDER BY $indent_master_tab.ID DESC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $indent_data  = $stmt->fetchAll();	
        $this->tpl->set('indent_data', $indent_data);

         //perchase Order table data
       
         $sql = "SELECT ID,MaterialNo FROM $material_inward_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $material_inward_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('material_inward_tab_data', $material_inward_tab_data);

                       //Material Inward Detail table data
       
           $sql = "SELECT ID,batch_no FROM $material_inward_detail_tab";    
           $stmt = $this->db->prepare($sql);            
           $stmt->execute();
           $material_inward_detail_tab_data  = $stmt->fetchAll();	
           $this->tpl->set('material_inward_detail_tab_data', $material_inward_detail_tab_data);


         //perchase Order table data
       
         $sql = "SELECT ID,PurchaseOrderNo,purchaseindent_ID FROM $po_master_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $po_master_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('po_master_tab', $po_master_tab_data);
        
        //supplier table data
       
        $sql = "SELECT ID,SupplierName FROM $supplier_tab ORDER BY $supplier_tab.SupplierName ASC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $supplier_data  = $stmt->fetchAll();	
        $this->tpl->set('supplier_data', $supplier_data);
        
        //rawmaterial table data
       
        $sql = "SELECT ID,RMName FROM $rawmaterial_table";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $rawmaterial_data  = $stmt->fetchAll();	
        $this->tpl->set('rawmaterial_data', $rawmaterial_data);
        
        //unit table data
       
        $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
        $stmt = $this->db->prepare($pdt_sql);            
        $stmt->execute();
        $unit_data  = $stmt->fetchAll();	
        $this->tpl->set('unit_data', $unit_data);
 
        $this->tpl->set('page_title', 'Inventory Transfer');	          
        $this->tpl->set('page_header', 'Store');
        
        //Add Role when u submit the add role form
        $thisPageURL = $this->crg->get('route')['base_path'] . '/' . $this->crg->get('route')['module'] . '/' . $this->crg->get('route')['controller'] . '/' . $this->crg->get('route')['action'];

        $crud_string = null;

        if (isset($_POST['req_from_list_view'])) {
            $crud_string = strtolower($_POST['req_from_list_view']);
        }              
        
        //Edit submit
        if (!empty($_POST['edit_submit_button']) && $_POST['edit_submit_button'] == 'edit') {
            $crud_string = 'editsubmit';
        }
        
        //Add submit
        if (!empty($_POST['add_submit_button']) && $_POST['add_submit_button'] == 'add') {
            $crud_string = 'addsubmit';
        }
        

        switch ($crud_string) {
             case 'delete':                    
                  $data = trim($_POST['ycs_ID']);
                   
                   
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                   
                }
               
                // $sqlsrr = "SELECT * FROM $po_master_tab WHERE ID=$data";
                // $po_data = $dbutil->getSqlData($sqlsrr); 
                
                // $sqlsrr = "SELECT * FROM  $po_detail_tab WHERE purchaseorder_ID=$data";
                // $podet_data = $dbutil->getSqlData($sqlsrr); 
                
                // $purchase_indentno=(count($po_data)>0)?$po_data[0]['purchaseindent_ID']:'';
                 
                // $sqlsrr = "SELECT  * FROM  $indent_det_tab WHERE $indent_det_tab.purchaseindent_ID = '$purchase_indentno'";
                // $indentdet_data = $dbutil->getSqlData($sqlsrr);
                
                 
                //  if(!empty($purchase_indentno)){
                //      $sql_update="Update $indent_master_tab SET $indent_master_tab.PI_Status=1 WHERE  $indent_master_tab.ID=$purchase_indentno";
                //                  $masterstmt1 = $this->db->prepare($sql_update);
                //                  $masterstmt1->execute();
                                 
                //     foreach($podet_data as $k=>$v){
                        
                //         foreach($indentdet_data as $kk=>$value){
                            
                //             if(($value['rawmaterial_ID']==$v['rawmaterial_ID'])){
                                
                //                 $sql_updates="Update $indent_det_tab SET $indent_det_tab.ItemStatus=1,$indent_det_tab.RaisedPOQty=(RaisedPOQty-$v[POQuantity]) WHERE $indent_det_tab.ID=$value[ID]";
                //                 $detstmt1 = $this->db->prepare($sql_updates);
                //               $detstmt1->execute();
                             
                //             }
                            
                //         }
                        
                //     }
                                 
                      
                //  }
           $sql_update="UPDATE ycias_stock
                LEFT JOIN ycias_inventory_transfer_detail
                ON ycias_inventory_transfer_detail.item_name=ycias_stock.rawmaterial_id
                SET ycias_stock.available_qty=(ycias_stock.available_qty+ycias_inventory_transfer_detail.transfer_quantity)
                WHERE ycias_stock.rawmaterial_id=ycias_inventory_transfer_detail.item_name AND ycias_inventory_transfer_detail.inventory_transfer_ID=$data";   
                 
                $sqldetdelete="DELETE $inventory_transfer_tab,$inventory_transfer_detail_tab FROM $inventory_transfer_tab
                               LEFT JOIN  $inventory_transfer_detail_tab ON $inventory_transfer_tab.ID=$inventory_transfer_detail_tab.inventory_transfer_ID 
                               WHERE $inventory_transfer_tab.ID=$data";  

               
                               
                $stmt = $this->db->prepare($sqldetdelete);            
                    
                    if($stmt->execute()){
                    $this->tpl->set('message', 'Stock Return deleted successfully');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/inventorytransfer');
                    // $this->tpl->set('label', 'List');
                    // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    } 
                    
        break;
            case 'view':                    
                $data = trim($_POST['ycs_ID']);
             
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to view!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', 'view');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         
                            
                // $sqlsrr="SELECT $inventory_transfer_tab.*,$inventory_transfer_detail_tab.*,$unit_table.UnitName FROM $inventory_transfer_tab,$inventory_transfer_detail_tab, $unit_table
                // WHERE  $unit_table.ID = $inventory_transfer_detail_tab.unit_id 
                // AND $inventory_transfer_tab.ID = $data";      
                
                $sqlsrr="SELECT $inventory_transfer_tab.*,
                $inventory_transfer_detail_tab.*,
                $unit_table.UnitName 
        FROM   $inventory_transfer_tab,
                $inventory_transfer_detail_tab, 
                $unit_table
        WHERE   $inventory_transfer_tab.ID = $inventory_transfer_detail_tab.inventory_transfer_ID
        AND     $inventory_transfer_detail_tab.unit_id = $unit_table.ID
        AND     $inventory_transfer_tab.ID = $data"; 

                // $sqlsrr="SELECT $inventory_transfer_tab.from_warehouse, $inventory_transfer_tab.from_warehouse, $inventory_transfer_detail_tab.item_name ,$material_inward_detail_tab.ID,$material_inward_detail_tab.batch_no,$unit_table.Description FROM $material_inward_detail_tab,$unit_table, $inventory_transfer_tab, $inventory_transfer_detail_tab  WHERE $unit_table.ID = $material_inward_detail_tab.unit AND $inventory_transfer_tab.ID =  $inventory_transfer_detail_tab.inventory_transfer_ID  and $inventory_transfer_tab.ID= $data";       
                
                $indent_data = $dbutil->getSqlData($sqlsrr);               
              
                
                        
               
                
                
                //edit option     
                $this->tpl->set('message', 'You can view Stock Return form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/inventory_transfer_form.php'));                    
                break;
            
            case 'edit':                    
                $data = trim($_POST['ycs_ID']);
                $mode='edit';
                if(isset($_SESSION['ycs_ID']))
                {
                    $data = trim($_SESSION['ycs_ID']);
                    unset($_SESSION['ycs_ID']);
                    $mode='Confirm';
                }
           // var_dump($data);
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to edit!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', $mode);
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         

                // $sqlsrr="SELECT *,$unit_table.Description FROM $inventory_transfer_tab,$inventory_transfer_detail_tab, $unit_table
                // WHERE  $inventory_transfer_tab.ID = $inventory_transfer_detail_tab.inventory_transfer_ID AND $unit_table.ID = $inventory_transfer_detail_tab.unit
                // AND $inventory_transfer_tab.ID = $data"; 

                // $sqlsrr="SELECT * FROM $inventory_transfer_tab,$inventory_transfer_detail_tab, $unit_table
                //  WHERE  $inventory_transfer_tab.ID = $inventory_transfer_detail_tab.inventory_transfer_ID 
                //     AND $inventory_transfer_tab.ID = $data";

                $sqlsrr="SELECT $inventory_transfer_tab.*, $inventory_transfer_detail_tab.*,$unit_table.UnitName FROM $inventory_transfer_tab,$inventory_transfer_detail_tab, $unit_table
                  WHERE  $unit_table.ID = $inventory_transfer_detail_tab.unit_id 
                    AND $inventory_transfer_tab.ID = $data";   



                $indent_data = $dbutil->getSqlData($sqlsrr);               
                $indentsel_data  = $stmt->fetchAll();	
                $this->tpl->set('indent_data', $indentsel_data);
            
                //edit option     
                $this->tpl->set('message', 'You can edit Stock Return form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/inventory_transfer_form.php'));                    
                break;
            
            case 'editsubmit':             
                $data = trim($_POST['ycs_ID']);
                
                //mode of form submit
                $this->tpl->set('mode', 'edit');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);

                //Post data
                include_once 'util/genUtil.php';
                $util = new GenUtil();
                $form_post_data = $util->arrFltr($_POST);

                //        echo '<pre>';
                //    print_r($form_post_data); die;
                
                // $sqldet_del = "DELETE FROM $po_detail_tab WHERE purchaseorder_ID=$data";
                // $stmt = $this->db->prepare($sqldet_del);
                // $stmt->execute();   
                        
                        try{
                            
                                                    
                    
        
                        
                       
                        $Inventory_transferNo = $form_post_data['Inventory_transferNo'];
                        $from_warehous = $form_post_data['from_warehouse'];
                        $to_warehouse = $form_post_data['to_warehouse'];
            
                        $sql2 = "UPDATE $inventory_transfer_tab set

                       
                        Inventory_transferNo='$Inventory_transferNo',
                        from_warehouse='$from_warehous',
                        to_warehouse='$to_warehouse'
                          

                         WHERE ID=$data";	
                        
                        $stmt = $this->db->prepare($sql2);   
                        $stmt->execute();

                        $sql3 = "DELETE FROM $inventory_transfer_detail_tab WHERE inventory_transfer_ID=$data";
					     $stmt3 = $this->db->prepare($sql3);

                    if($stmt3->execute()){
                    
                    FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {                         
                            $item_name =$form_post_data['item_name_' . $entry_count]; 
                            $batch =$form_post_data['batch_no_' . $entry_count]; 
                            $availabel_stock =$form_post_data['availabel_stock_' . $entry_count];
                            $transfer_quantity =$form_post_data['transfer_quantity_' . $entry_count];
                            $acceptedqty =$form_post_data['acceptedqty_' . $entry_count];
                            $unit =$form_post_data['unit_id_' . $entry_count];
                           
                            $sql_update="Update $stock_tab SET available_qty=(available_qty+'$acceptedqty') 
                                         WHERE   
                                         $stock_tab.rawmaterial_id=$item_name ";               
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                                    
                            if(!empty($item_name) && !empty($batch)){
                                
                                 $sql_update="Update $stock_tab SET available_qty=(available_qty-'$transfer_quantity') 
                                              WHERE    
                                              $stock_tab.rawmaterial_id=$item_name ";               
                                              $update_stmt = $this->db->prepare($sql_update);
                                              $update_stmt->execute();
                          
                                    $vals = "'" . $data . "'," .
                                   
                                    "'" . $item_name . "'," .
                                    "'" . $batch . "'," .
                                    "'" . $availabel_stock . "'," .
                                    "'" . $transfer_quantity . "'," .
                                    "'" . $unit . "'" ;
                                    

                            $sql2 = "INSERT INTO $inventory_transfer_detail_tab
                                    ( 

                                        `inventory_transfer_ID`, 
                                        `item_name`,
                                        `batch_no`,
                                        `availabel_stock`,
                                        `transfer_quantity`,
                                        `unit_id`
                                        
                                    ) 
                            VALUES ($vals)";        

                            $stmt = $this->db->prepare($sql2);
                            $stmt->execute();               
                        }
                        }  

                        
                    }
                                    
                        $this->tpl->set('message', 'Inventory Transfer form edited successfully!');   
                        header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/inventorytransfer');
                       
                        } catch (Exception $exc) {
                         //edit failed option
                        $this->tpl->set('message', 'Failed to edit, try again!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/inventory_transfer_form.php'));
                        }

                break;
                
            case 'addsubmit':
                
                if (isset($crud_string)) {
                     
                    $form_post_data = $dbutil->arrFltr($_POST);
                    // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                    //  $completed_on=!empty($form_post_data['completed_on'])?date("Y-m-d", strtotime($form_post_data['completed_on'])):'';
                    // "'" . $dispatch_date . "'," .
                    // $purchase_indentno=$form_post_data['purchaseindent_ID'];
                    // echo '<pre>';
                    // print_r($form_post_data); die;
                // $from=$form_post_data['from_warehouse'];
                //     $to=$form_post_data['to_warehouse'];
                //     $concat = $from." TO ".$to; 
                 $from=$form_post_data['from_warehouse'];
                    $to=$form_post_data['to_warehouse'];
                    if($from==13)
                    {
                    $concat = "puducherry to cuddlaore"; 
                    }
                    elseif($from==14)
                    {
                        $concat = "cuddlaore to puducherry";
                    }
                    
                            $val =   
                            "'" . $form_post_data['Inventory_transferNo'] . "'," .                               
                                    "'" . $form_post_data['from_warehouse'] . "'," .
                                    "'" . $form_post_data['to_warehouse'] . "'," .
                                    // "'" . $form_post_data['workorder_no'] . "'," .
                                    // "'" . $form_post_data['process_name'] . "'," .
                                    // "'" . $form_post_data['product_number'] . "'," .
                                    // "'" . $form_post_data['process'] . "'," .
                                    // "'" . $form_post_data['product_size'] . "'," .
                                    // "'" . $form_post_data['thickness'] . "'," .
                                    // "'" . $form_post_data['color'] . "'," .
                                    // "'" . $form_post_data['design'] . "'," .
                                    // "'" . $form_post_data['quantity'] . "'," .
                                    // "'" . $form_post_data['completed_on'] . "'," .
                                    // "'" . $form_post_data['lay_up_sequence'] . "'," .
                                    // "'" . $form_post_data['details'] . "'," .
                                    // "'" . $form_post_data['remarks'] . "'," .
                                    // "'" . $form_post_data['created_by'] . "'," .
                                    "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                    "'" .  $this->ses->get('user')['ID'] . "'";

                       $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "inventory_transfer`
                                        ( 
                                       
                                        `Inventory_transferNo`,
                                        `from_warehouse`,
                                        `to_warehouse`,                                      
                                        `entity_ID`,
                                        `users_ID`
                                        ) 
                                VALUES ( $val )";
                                $stmt = $this->db->prepare($sql);      
                    if ($stmt->execute()) { 
                    
                    $lastInsertedID = $this->db->lastInsertId();  
                  FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {

                            //  $invoice_date=!empty($form_post_data['completed_on_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['completed_on_'. $entry_count])):'';
                            $item_name =$form_post_data['item_name_' . $entry_count]; 
                            $batch =$form_post_data['batch_no_' . $entry_count]; 
                            $availabel_stock =$form_post_data['availabel_stock_' . $entry_count];
                            $transfer_quantity =$form_post_data['transfer_quantity_' . $entry_count];								
                            $unit =$form_post_data['unit_id_' . $entry_count];
                            // $total_qty_retn =$form_post_data['total_qty_retn_' . $entry_count];
                            // $qty_return =$form_post_data['qty_return_' . $entry_count];
                            // $costunit =$form_post_data['cost_unit_' . $entry_count];
                            // $total =$form_post_data['total_' . $entry_count];
                            // $supplier =$form_post_data['supplier_invoice_no_' . $entry_count];
                            // $invoice =$form_post_data['invoice_date_' . $entry_count];
                            
                            
                        
                            
                           
                            
                            $vals = "'" . $lastInsertedID . "'," .
                                    "'" . $item_name . "'," .
                                    "'" . $batch . "'," .
                                    "'" . $availabel_stock . "'," .
                                    "'" . $transfer_quantity . "',".
                                    "'" . $unit . "'" ;
                                    // "'" . $total_qty_retn . "'," .
                                    // "'" . $qty_return . "'" ;
                                    // "'" . $costunit . "'," .
                                    // "'" . $total . "'," .
                                    // "'" . $supplier . "'," .
                                    // "'" . $invoice_date . "'" ;   
                                         $sql2 = "INSERT INTO $inventory_transfer_detail_tab
                                    ( 
                                        `inventory_transfer_ID`, 
                                        `item_name`,
                                        `batch_no`,
                                        `availabel_stock`,
                                        `transfer_quantity`,
                                        `unit_id`
                                       
                                       
                                    ) 
                            VALUES ($vals)";
                            $stmt = $this->db->prepare($sql2);
                       // $stmt->execute();    
                         if($stmt->execute()){
                        // $sql_update="UPDATE ycias_stock
                        //                      LEFT JOIN ycias_inventory_transfer_detail
                        //                      ON ycias_inventory_transfer_detail.item_name=ycias_stock.rawmaterial_id
                        //                      SET ycias_stock.available_qty=(ycias_stock.available_qty-$transfer_quantity)
                        //                      WHERE ycias_stock.rawmaterial_id=ycias_inventory_transfer_detail.item_name";          
                                         
                        //                  $update_stmt = $this->db->prepare($sql_update);
                        //                  $update_stmt->execute();
                                         
                        $sql_update="UPDATE ycias_stock                                        
                                             SET available_qty=(available_qty-$transfer_quantity)
                                             WHERE rawmaterial_id =  $item_name AND entity_ID = $entityID";                                                   
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                                         
                         $sqlsrr = "SELECT * FROM  ycias_material_inward_detail WHERE ycias_material_inward_detail.item_id = $item_name";
                                 $qc_det_data = $dbutil->getSqlData($sqlsrr);    
                                         // print_r($qc_det_data[0]['item_id']); die;
                                 $item_id=(count($qc_det_data)>0) ? $qc_det_data[0]['cost_unit']:''; 
                                         
                        $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType, raw_material_ID, stockout, unit_name,warehouse_name,stock_value, entity_ID, users_ID)
                                       VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Inventory Transfer', $item_name, $transfer_quantity,$unit,'$concat',$item_id, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                        
                                       $update_stmt = $this->db->prepare($sql_update);
                                       $update_stmt->execute();   
                          }    
                       
                        
                           
                        } 
                        
                        
                      
                    }
                                                       
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('message', '- Success -');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/inventorytransfer');
                 } else {
                        //edit option
                        //if submit failed to insert form
                        $this->tpl->set('message', 'Failed to submit!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/inventory_transfer_form.php'));
                 }
                 
                break;

            case 'add':
                $this->tpl->set('mode', 'add');
                $this->tpl->set('page_header', 'Store');
                $Inventory_transferNo=$dbutil->keyGeneration('inventory_transfer','ITN','','Inventory_transferNo');
                $this->tpl->set('Inventory_transferNo', $Inventory_transferNo);
                $this->tpl->set('content', $this->tpl->fetch('factory/form/inventory_transfer_form.php'));
                break;

            default:
                /*
                 * List form
                 */
                 
                ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
     $colArr = array(
            // "$inventory_transfer_tab.ID",
            // "$inventory_transfer_tab.store",
            // "$inventory_transfer_tab.from_warehouse",
            // "$inventory_transfer_tab.to_warehouse"
            "$inventory_transfer_tab.ID", 
            "$inventory_transfer_tab.Inventory_transferNo",
            // "$inventory_transfer_tab.from_warehouse",
            // "$inventory_transfer_tab.to_warehouse",
            "entity_from.Title AS from_warehouse_title",
            "entity_to.Title AS to_warehouse_title",
            "$inventory_transfer_tab.created_date"
           
            
            // "$po_master_tab.ID"

        );
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

            if (strpos($colNames, 'DATE') !== false) {
                list($colNames,$x) = $dbutil->dateFilterFormat($colNames);                    
            }else {
                $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);        		    
            }

              if ('' != $x) {
               $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
       IF (count($wsarr) >= 1) {
            $whereString = ' AND '. implode(' AND ', $wsarr);
        }
       } else {
         $whereString ="ORDER BY $inventory_transfer_tab.ID DESC";
       }
       
    $sql = "SELECT "
                . implode(',',$colArr)
                . " FROM $inventory_transfer_tab "
                . " LEFT JOIN $entity_tab AS entity_from  ON $inventory_transfer_tab.from_warehouse= entity_from.ID "
                . " LEFT JOIN $entity_tab AS entity_to ON $inventory_transfer_tab.to_warehouse = entity_to.ID "
                . " WHERE "
                . " $inventory_transfer_tab.entity_ID = $entityID"
                . " $whereString";                             
        
     

            $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
        /*
         * SET DATA TO TEMPLATE
                    */
       $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
     
     
       $this->tpl->set('table_columns_label_arr', array('ID','Reference No.','From Warehouse','To Warehouse','Date'));
        // $this->tpl->set('table_columns_label_arr', array('ID','store','stockreturn_no','po_no'));
        
        /*
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
        // $this->tpl->set('dcpdf','Generate Pdf');            
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
               
        //////////////////////close//////////////////////////////////////  
                 
                include_once $this->tpl->path . '/factory/form/inventory_transfer_crud_form.php';
                $cus_form_data = Form_Elements::data($this->crg);
                include_once 'util/crud3_1.php';
                new Crud3($this->crg, $cus_form_data);
                break;
        }

    ///////////////Use different template////////////////////
    // $this->tpl->set('master_layout', 'layout_chart.php'); 
    $this->tpl->set('master_layout', 'layout_datepicker.php');
    ///////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then /////
    ///////////////////////////////////////////////////////////////////
 } else {
         if ($this->ses->get('user')['ID']) {
             $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
         } else {
             header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
         }
     }
}

///////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////
///////////////////////  Material Consumable Request

function materialissueconsume(){
     
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
         
        /////////////////////////////////////////////////////////////////
        //////////////////////////////access condition applied///////////
        /////////////////////////////////////////////////////////////////
        
        include_once 'util/DBUTIL.php';
        $dbutil = new DBUTIL($this->crg);
        
        include_once 'util/genUtil.php';
        $util = new GenUtil();
         
        $entityID = $this->ses->get('user')['entity_ID'];
        $userID = $this->ses->get('user')['ID'];
        $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
        $unit_table = $this->crg->get('table_prefix') . 'unit';
        $po_detail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
        $po_master_tab = $this->crg->get('table_prefix') . 'purchaseorder';
        $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
        $indent_master_tab = $this->crg->get('table_prefix') . 'purchaseindent';
        $indent_det_tab = $this->crg->get('table_prefix') . 'purchaseindentdetail';
        $state_tab = $this->crg->get('table_prefix') . 'state';
        $unit_tab = $this->crg->get('table_prefix') . 'unit';
        $material_inward_tab = $this->crg->get('table_prefix') . 'material_inward';
        $material_inward_detail_tab = $this->crg->get('table_prefix') . 'material_inward_detail';
        $inventory_transfer_tab = $this->crg->get('table_prefix') . 'inventory_transfer';
        $employee_tab = $this->crg->get('table_prefix') . 'employee';
        $materialrequest_tab = $this->crg->get('table_prefix') . 'materialrequest';
        $materialrequest_det_tab = $this->crg->get('table_prefix') . 'materialrequest_detail';
        $materialissue_consumables_tab = $this->crg->get('table_prefix') . 'materialissue_consumables';
        $materialissue_consumable_detalil_tab = $this->crg->get('table_prefix') . 'materialissue_consumable_detalil';
        $stock_tab = $this->crg->get('table_prefix') . 'stock';
        $stock_trans_tab = $this->crg->get('table_prefix') . 'stock_trans';

        $Employeetype_data = array(array("ID"=>"1","Title"=>"Employee"),array("ID"=>"2","Title"=>"Sub Contractor"));
         $this->tpl->set('Employeetype_data', $Employeetype_data);
        
        //indent table data
       
        $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1 and $indent_master_tab.PI_Status=1 ORDER BY $indent_master_tab.ID DESC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $indent_data  = $stmt->fetchAll();	
        $this->tpl->set('indent_data', $indent_data);

        // Inventory Transfer table data
       
        $sql = "SELECT ID,inventory_no FROM $inventory_transfer_tab";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $inventory_transfer_data  = $stmt->fetchAll();	
        $this->tpl->set('inventory_transfer_data', $inventory_transfer_data);

        
        // Matreial Request Number Transfer table data
       
        $sql = "SELECT ID,Reqno FROM $materialrequest_tab  where $materialrequest_tab.mrc_Status=1 AND $materialrequest_tab.Status=1 AND $materialrequest_tab.entity_ID = $entityID ORDER BY ID DESC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $materialrequest_tab_data  = $stmt->fetchAll();	
        $this->tpl->set('materialrequest_tab_data', $materialrequest_tab_data);

         // employee table data
       
         $sql = "SELECT ID,EmpName FROM $employee_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $employee_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('employee_tab_data', $employee_tab_data);
       
       //supplier table data


         //perchase Order table data
       
         $sql = "SELECT ID,PurchaseOrderNo,purchaseindent_ID FROM $po_master_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $po_master_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('po_master_tab', $po_master_tab_data);
        
        //supplier table data
       
        $sql = "SELECT ID,SupplierName FROM $supplier_tab ORDER BY $supplier_tab.SupplierName ASC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $supplier_data  = $stmt->fetchAll();	
        $this->tpl->set('supplier_data', $supplier_data);
        
        //rawmaterial table data
       
        $sql = "SELECT ID,RMName FROM $rawmaterial_table";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $rawmaterial_data  = $stmt->fetchAll();	
        $this->tpl->set('rawmaterial_data', $rawmaterial_data);
        
        //unit table data
       
        $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
        $stmt = $this->db->prepare($pdt_sql);            
        $stmt->execute();
        $unit_data  = $stmt->fetchAll();	
        $this->tpl->set('unit_data', $unit_data);
 
        $this->tpl->set('page_title', 'Material Issue consumables Tools');	          
        $this->tpl->set('page_header', 'Store');
        
        //Add Role when u submit the add role form
        $thisPageURL = $this->crg->get('route')['base_path'] . '/' . $this->crg->get('route')['module'] . '/' . $this->crg->get('route')['controller'] . '/' . $this->crg->get('route')['action'];

        $crud_string = null;

        if (isset($_POST['req_from_list_view'])) {
            $crud_string = strtolower($_POST['req_from_list_view']);
        }              
        
        //Edit submit
        if (!empty($_POST['edit_submit_button']) && $_POST['edit_submit_button'] == 'edit') {
            $crud_string = 'editsubmit';
        }
        
        //Add submit
        if (!empty($_POST['add_submit_button']) && $_POST['add_submit_button'] == 'add') {
            $crud_string = 'addsubmit';
        }
        

        switch ($crud_string) {
             case 'delete':                    
                  $data = trim($_POST['ycs_ID']);
                   
                   
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                   
                }
               
                // $sqlsrr = "SELECT * FROM $po_master_tab WHERE ID=$data";
                // $po_data = $dbutil->getSqlData($sqlsrr); 
                
                // $sqlsrr = "SELECT * FROM  $po_detail_tab WHERE purchaseorder_ID=$data";
                // $podet_data = $dbutil->getSqlData($sqlsrr); 
                
                // $purchase_indentno=(count($po_data)>0)?$po_data[0]['purchaseindent_ID']:'';
                 
                // $sqlsrr = "SELECT  * FROM  $indent_det_tab WHERE $indent_det_tab.purchaseindent_ID = '$purchase_indentno'";
                // $indentdet_data = $dbutil->getSqlData($sqlsrr);
                
                 
                //  if(!empty($purchase_indentno)){
                //      $sql_update="Update $indent_master_tab SET $indent_master_tab.PI_Status=1 WHERE  $indent_master_tab.ID=$purchase_indentno";
                //                  $masterstmt1 = $this->db->prepare($sql_update);
                //                  $masterstmt1->execute();
                                 
                //     foreach($podet_data as $k=>$v){
                        
                //         foreach($indentdet_data as $kk=>$value){
                            
                //             if(($value['rawmaterial_ID']==$v['rawmaterial_ID'])){
                                
                //                 $sql_updates="Update $indent_det_tab SET $indent_det_tab.ItemStatus=1,$indent_det_tab.RaisedPOQty=(RaisedPOQty-$v[POQuantity]) WHERE $indent_det_tab.ID=$value[ID]";
                //                 $detstmt1 = $this->db->prepare($sql_updates);
                //               $detstmt1->execute();
                             
                //             }
                            
                //         }
                        
                //     }
                                 
                      
                //  }
                
                // 
                 $sqlsrr = "SELECT * FROM $materialissue_consumables_tab WHERE ID=$data";
                $material_data = $dbutil->getSqlData($sqlsrr); 
                
                $sqlsrr = "SELECT * FROM  $materialissue_consumable_detalil_tab WHERE Materialrequest_ID=$data";
                $materialdet_data = $dbutil->getSqlData($sqlsrr); 
                
                $con_materialrequest_no=(count($material_data)>0)?$material_data[0]['con_materialrequest_no']:'';
                 
                $sqlsrr = "SELECT  * FROM  $materialrequest_det_tab WHERE $materialrequest_det_tab.Materialrequest_ID = '$con_materialrequest_no'";
                $materialrequest_data = $dbutil->getSqlData($sqlsrr);
                
                
                 
                 if(!empty($con_materialrequest_no)){
                     $sql_update="Update $materialrequest_tab SET $materialrequest_tab.mrc_Status=1 WHERE  $materialrequest_tab.ID=$con_materialrequest_no";
                                 $masterstmt1 = $this->db->prepare($sql_update);
                                 $masterstmt1->execute();
                                 
                    foreach($materialdet_data as $k=>$v){
                        
                        foreach($materialrequest_data as $kk=>$value){
                            
                            if(($value['Rawmaterial_ID']==$v['Rawmaterial_ID'])){
                                
                                $sql_updates="Update $materialrequest_det_tab SET $materialrequest_det_tab.ItemStatus=1,$materialrequest_det_tab.issue_qty=(issue_qty-$v[issued_qty]) WHERE $materialrequest_det_tab.ID=$value[ID]";
                                $detstmt1 = $this->db->prepare($sql_updates);
                              $detstmt1->execute();
                             
                            }
                            
                        }
                        
                    }
                                 
                      
                 }
                // 
                
                $sql_update="UPDATE ycias_stock
                LEFT JOIN ycias_materialissue_consumable_detalil
                ON ycias_materialissue_consumable_detalil.Rawmaterial_ID=ycias_stock.rawmaterial_id
                SET ycias_stock.available_qty=(ycias_stock.available_qty+ycias_materialissue_consumable_detalil.issued_qty)
                WHERE ycias_stock.rawmaterial_id=ycias_materialissue_consumable_detalil.Rawmaterial_ID AND ycias_materialissue_consumable_detalil.materialissue_consumable_id=$data";          
                $update_stmt = $this->db->prepare($sql_update);
                $update_stmt->execute();
                 
                $sqldetdelete="DELETE $materialissue_consumable_detalil_tab,$materialissue_consumables_tab FROM $materialissue_consumables_tab
                               LEFT JOIN  $materialissue_consumable_detalil_tab ON $materialissue_consumables_tab.ID = $materialissue_consumable_detalil_tab.materialissue_consumable_id 
                               WHERE $materialissue_consumables_tab.ID=$data";  
                $stmt = $this->db->prepare($sqldetdelete);            
                    
                    if($stmt->execute()){
                    $this->tpl->set('message', 'Material Issue deleted successfully');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/materialissueconsume');
                    // $this->tpl->set('label', 'List');
                    // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    } 
                    
        break;
            case 'view':                    
                $data = trim($_POST['ycs_ID']);
             
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to view!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', 'view');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         
                            
                //  $sqlsrr = "SELECT  $po_master_tab.*,
                //                     $po_detail_tab.*,
                //                     $rawmaterial_table.RMName,
                //                     $supplier_tab.SupplierName,
                //                     $supplier_tab.AddressLine1,
                //                     $supplier_tab.AddressLine2,
                //                     $supplier_tab.City,
                //                     $state_tab.StateName,
                //                     $unit_table.UnitName
                //             FROM  $po_master_tab
                //             LEFT JOIN $po_detail_tab ON $po_master_tab.ID=$po_detail_tab.purchaseorder_ID
                //             LEFT JOIN $rawmaterial_table ON  $po_detail_tab.rawmaterial_ID=$rawmaterial_table.ID
                //             LEFT JOIN $supplier_tab  ON $supplier_tab.ID=$po_master_tab.supplier_ID 
                //             LEFT JOIN $state_tab  ON $state_tab.ID=$supplier_tab.State_ID 
                //             LEFT JOIN $unit_table  ON $unit_table.ID=$po_detail_tab.unit_ID 
                //             WHERE $po_master_tab.ID = '$data' 
                //             ORDER BY $po_detail_tab.ID ASC";
                // $indent_data = $dbutil->getSqlData($sqlsrr); 
                
                        $sql = "SELECT ID,Reqno FROM $materialrequest_tab  where  $materialrequest_tab.Status=1 ORDER BY ID DESC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $materialrequest_tab_data  = $stmt->fetchAll();	
        $this->tpl->set('materialrequest_tab_data', $materialrequest_tab_data);
        
        
                $sqlsrr ="SELECT 
                $materialissue_consumables_tab.*,
                $materialissue_consumable_detalil_tab.*,
                $materialrequest_tab.Reqno,
                $materialrequest_tab.EmployeeType,
                $employee_tab.EmpName,
                $supplier_tab.SupplierName,
                $materialrequest_tab.Remarks,
                $materialrequest_det_tab.Rawmaterial_ID,
                $rawmaterial_table.RMName,
                $materialrequest_det_tab.Quantity,
               
                
              
                $materialrequest_det_tab.Rawmaterial_ID ,
                
                $unit_table.UnitName
               
      FROM
          $materialissue_consumables_tab
           

      LEFT JOIN $materialissue_consumable_detalil_tab ON $materialissue_consumables_tab.ID = $materialissue_consumable_detalil_tab.materialissue_consumable_id
      LEFT JOIN $materialrequest_tab ON $materialissue_consumables_tab.con_materialrequest_no = $materialrequest_tab.ID
      LEFT JOIN $materialrequest_det_tab ON $materialrequest_tab.ID = $materialrequest_det_tab.Materialrequest_ID
      LEFT JOIN $supplier_tab ON $materialrequest_tab.Subcontractor_ID = $supplier_tab.ID
      LEFT JOIN $employee_tab ON $materialrequest_tab.EmployeeID = $employee_tab.ID
      LEFT JOIN $rawmaterial_table ON $materialrequest_det_tab.Rawmaterial_ID = $rawmaterial_table.ID
      
      LEFT JOIN $stock_tab ON $materialrequest_det_tab.Rawmaterial_ID = $stock_tab.rawmaterial_id 
   
      LEFT JOIN $unit_table ON $materialrequest_det_tab.unit_ID = $unit_table.ID

      WHERE
                $materialissue_consumable_detalil_tab.Rawmaterial_ID = $materialrequest_det_tab.Rawmaterial_ID
      AND      $stock_tab.entity_ID = $materialissue_consumables_tab.entity_ID
      AND      $materialissue_consumables_tab.ID = $data";    
    
    //  $sqlsrr ="SELECT * from $materialissue_consumables_tab,$materialissue_consumable_detalil_tab 
    //  where 
    //       $materialissue_consumables_tab.ID = $materialissue_consumable_detalil_tab.materialissue_consumable_id
    
    //  AND  $materialissue_consumables_tab.ID = $data";      
                
      $indent_data = $dbutil->getSqlData($sqlsrr);              
              
                
                        
               
                
                
                //edit option     
                $this->tpl->set('message', 'You can view Material Issue form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/material_issue_consumable_tools.php'));                    
                break;
            
            case 'edit':                    
                $data = trim($_POST['ycs_ID']);
                $mode='edit';
                if(isset($_SESSION['ycs_ID']))
                {
                    $data = trim($_SESSION['ycs_ID']);
                    unset($_SESSION['ycs_ID']);
                    $mode='Confirm';
                }
           // var_dump($data);
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to edit!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', $mode);
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         


           $sqlsrr ="SELECT 
                $materialissue_consumables_tab.*,
                $materialissue_consumable_detalil_tab.*,
                $materialrequest_tab.Reqno,
                $materialrequest_tab.EmployeeType,
                $employee_tab.EmpName,
                $supplier_tab.SupplierName,
                $materialrequest_tab.Remarks,
                $materialrequest_det_tab.Rawmaterial_ID,
                $rawmaterial_table.RMName,
                $materialrequest_det_tab.Quantity,
               
                 $materialrequest_det_tab.issue_qty,
                $stock_tab.available_qty,
                $materialrequest_det_tab.Rawmaterial_ID ,
                
                $unit_table.UnitName
               
      FROM
           $materialissue_consumables_tab
           

      LEFT JOIN $materialissue_consumable_detalil_tab ON $materialissue_consumables_tab.ID = $materialissue_consumable_detalil_tab.materialissue_consumable_id
      LEFT JOIN $materialrequest_tab ON $materialissue_consumables_tab.con_materialrequest_no = $materialrequest_tab.ID
      LEFT JOIN $materialrequest_det_tab ON $materialrequest_tab.ID = $materialrequest_det_tab.Materialrequest_ID
      LEFT JOIN $supplier_tab ON $materialrequest_tab.Subcontractor_ID = $supplier_tab.ID
      LEFT JOIN $employee_tab ON $materialrequest_tab.EmployeeID = $employee_tab.ID
      LEFT JOIN $rawmaterial_table ON $materialrequest_det_tab.Rawmaterial_ID = $rawmaterial_table.ID
      
      LEFT JOIN $stock_tab ON $materialrequest_det_tab.Rawmaterial_ID = $stock_tab.rawmaterial_id 
      
      LEFT JOIN $unit_table ON $materialrequest_det_tab.unit_ID = $unit_table.ID

      WHERE
                $materialissue_consumable_detalil_tab.Rawmaterial_ID = $materialrequest_det_tab.Rawmaterial_ID
      AND       $materialissue_consumables_tab.ID = $data";   

            


                $indent_data = $dbutil->getSqlData($sqlsrr);               
                $indentsel_data  = $stmt->fetchAll();	
                $this->tpl->set('indent_data', $indentsel_data);
            
                //edit option     
                $this->tpl->set('message', 'You can edit Material Issue form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/material_issue_consumable_tools.php'));                    
                break;
            
            case 'editsubmit':             
                $data = trim($_POST['ycs_ID']);
              

                //mode of form submit
                $this->tpl->set('mode', 'edit');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);

                //Post data
                include_once 'util/genUtil.php';
                $util = new GenUtil();
                $form_post_data = $util->arrFltr($_POST);

                //      echo '<pre>';
                //    print_r($form_post_data); die;
                    
                // $sqldet_del = "DELETE FROM $po_detail_tab WHERE purchaseorder_ID=$data";
                // $stmt = $this->db->prepare($sqldet_del);
                // $stmt->execute();   
                        
                        try{
                            
                                                    
                    
        
                        
                        $con_materialissue_no= $form_post_data['con_materialissue_no'];
                        $con_materialrequest_no= $form_post_data['con_materialrequest_no'];
                         
                        
                        $sql_update="Update $materialissue_consumables_tab SET  

                        con_materialissue_no='$con_materialissue_no',
                        con_materialrequest_no='$con_materialrequest_no'   
                                                                                     
                                                  WHERE  ID=$data"; 

                        $stmt1 = $this->db->prepare($sql_update);
                        $stmt1->execute();

                        
						 $sql3 = "DELETE FROM $materialissue_consumable_detalil_tab WHERE materialissue_consumable_id=$data";
					     $stmt3 = $this->db->prepare($sql3);

                    if($stmt3->execute()){
                        
            //    echo         $form_post_data['maxCount'];    die;

                    FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {

                            
                            $rawmaterialid =$form_post_data['Rawmaterial_ID_' . $entry_count];
                            $issued_qty =$form_post_data['issued_qty_' . $entry_count];
                            $acceptedqty =$form_post_data['acceptedqty_' . $entry_count];
                            $pending_qty =$form_post_data['pending_qty_' . $entry_count];								
                            
                            $sql_update="Update $stock_tab SET available_qty=(available_qty-'$acceptedqty') 
                                         WHERE 
                                         $stock_tab.rawmaterial_id=$rawmaterialid ";               
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();


                            if(!empty($rawmaterialid)){
                                
                                 $sql_update="Update $stock_tab SET available_qty=(available_qty+'$issued_qty') 
                                              WHERE 
                                              $stock_tab.rawmaterial_id=$rawmaterialid ";               
                                              $update_stmt = $this->db->prepare($sql_update);
                                              $update_stmt->execute();

                                    $vals = "'" . $data . "'," .
                                    "'" . $rawmaterialid . "'," .
                                    "'" . $issued_qty . "'," .
                                    "'" . $pending_qty . "'" ;
                                   
                            
                            $sql2 = "INSERT INTO $materialissue_consumable_detalil_tab
                            ( 
                                `materialissue_consumable_id`, 
                                `Rawmaterial_ID`,
                                `issued_qty`,
                                `pending_qty`
                              
                            ) 
                    VALUES ($vals)";
                    $stmt = $this->db->prepare($sql2);
                $stmt->execute();  
                        }
                        }           

                        
                    }
                                    
                        $this->tpl->set('message', 'Material Issue  form edited successfully!');   
                        header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/materialissueconsume');
                       
                        } catch (Exception $exc) {
                         //edit failed option
                        $this->tpl->set('message', 'Failed to edit, try again!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/material_issue_consumable_tools.php'));
                        }

                break;
                
            case 'addsubmit':
                
                if (isset($crud_string)) {
                     
                    $form_post_data = $dbutil->arrFltr($_POST);
                    // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                    // $delivery_date=!empty($form_post_data['DeliveryDate'])?date("Y-m-d", strtotime($form_post_data['DeliveryDate'])):'';
                    // "'" . $dispatch_date . "'," .
                    $materialreqno=$form_post_data['con_materialrequest_no'];
                //     echo '<pre>';
                //    print_r($form_post_data); die;
                    
                            $val =  "'" . $form_post_data['con_materialissue_no'] . "'," .                                  
                                    "'" . $form_post_data['con_materialrequest_no'] . "'," .
                                    // "'" . $form_post_data['emp_type'] . "'," .
                                    // "'" . $form_post_data['emp_name'] . "'," .
                                    // "'" . $form_post_data['remark'] . "'," .
                                    "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                    "'" .  $this->ses->get('user')['ID'] . "'";

                         $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "materialissue_consumables`
                                        ( 
                                        `con_materialissue_no`,
                                        `con_materialrequest_no`,
                                      
                                        `entity_ID`,
                                        `users_ID`
                                        ) 
                                VALUES ( $val )";
                                $stmt = $this->db->prepare($sql);       
                    if ($stmt->execute()) { 
                    
                    $lastInsertedID = $this->db->lastInsertId();
                      
                //  $count=   $form_post_data['maxCount']; die;
                
                  FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {

                            $rawmaterialid =$form_post_data['Rawmaterial_ID_' . $entry_count]; 
                            $available_stock_qty =$form_post_data['available_qty_' . $entry_count];
                            $issued_qty =$form_post_data['issued_qty_' . $entry_count];
                            $pending_qty =$form_post_data['issue_qty_' . $entry_count];
                            $hai =$form_post_data['RMName_' . $entry_count];
                            $unit_name =$form_post_data['UnitName_' . $entry_count];
                            
                            $sql_update="Update $materialrequest_det_tab SET issue_qty=(issue_qty - '$issued_qty') WHERE $materialrequest_det_tab.Materialrequest_ID=$materialreqno AND $materialrequest_det_tab.Rawmaterial_ID=$rawmaterialid AND $materialrequest_det_tab.ItemStatus=1";   
                            $update_stmt = $this->db->prepare($sql_update);
                            $update_stmt->execute();

                            $vals = "'" . $lastInsertedID . "'," .
                                    "'" . $rawmaterialid . "'," .
                                    "'" . $available_stock_qty ."',".
                                    "'" . $issued_qty . "'," .
                                    "'" . $pending_qty . "'" ;
                                   
                                           $sql2 = "INSERT INTO $materialissue_consumable_detalil_tab
                                    ( 
                                        `materialissue_consumable_id`, 
                                       
                                        `Rawmaterial_ID`,
                                        `available_stock_qty`,
                                        `issued_qty`,
                                        `pending_qty`
                                                                                                                                
                                       
                                    ) 
                            VALUES ($vals)";
                            $stmt = $this->db->prepare($sql2);
                      //  $stmt->execute();  
                       if($stmt->execute()){
                    //   $sql_update="UPDATE ycias_stock
                    //                          LEFT JOIN ycias_materialissue_consumable_detalil
                    //                          ON ycias_materialissue_consumable_detalil.Rawmaterial_ID=ycias_stock.rawmaterial_id
                    //                          SET ycias_stock.available_qty=(ycias_stock.available_qty-$issued_qty)
                    //                          WHERE ycias_stock.rawmaterial_id=ycias_materialissue_consumable_detalil.Rawmaterial_ID";          
                                         
                    //                      $update_stmt = $this->db->prepare($sql_update);
                    //                      $update_stmt->execute();
                    if($issued_qty>0)
                    {
                       $sql_update="UPDATE ycias_stock                                        
                                             SET available_qty=(available_qty-$issued_qty)
                                             WHERE rawmaterial_id =  $rawmaterialid AND entity_ID = $entityID";                                                   
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                    }                 
                         $sqlsrr = "SELECT * FROM  ycias_material_inward_detail WHERE ycias_material_inward_detail.item_id = $rawmaterialid";
                                 $qc_det_data = $dbutil->getSqlData($sqlsrr);    
                                         // print_r($qc_det_data[0]['item_id']); die;
                                 $item_id=(count($qc_det_data)>0) ? $qc_det_data[0]['cost_unit']:''; 
                                         
                        $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType, raw_material_ID, raw_material_name, stockout, unit_name,stock_value, entity_ID, users_ID)
                                         VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Material issue consumable', $rawmaterialid, '$hai', $issued_qty, '$unit_name',$item_id, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                         
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                          }
                       
                           
                        }      
                        
                        
                      
                    }
                     $sqlsrr = "SELECT  * FROM  $materialrequest_det_tab WHERE $materialrequest_det_tab.Materialrequest_ID = '$materialreqno'";
                    $materialrequestdet_data = $dbutil->getSqlData($sqlsrr);
                    $checkunique=[];
                    
                         if(!empty($materialrequestdet_data)){
                             
                             foreach($materialrequestdet_data as $k=>$v){
                                 
                                 if($v['issue_qty']== '0'){
                                     $checkunique[]=1;
                                     $sql_update="Update $materialrequest_det_tab SET ItemStatus='2' WHERE  $materialrequest_det_tab.Materialrequest_ID=$materialreqno AND $materialrequest_det_tab.Rawmaterial_ID=$v[Rawmaterial_ID]";
                                     $stmt1 = $this->db->prepare($sql_update);
                                     $stmt1->execute();
                                     
                                 }else{
                                     $checkunique[]=2; 
                                 }
                            
                                 
                             }
                            
                             if(count($checkunique)>0){
                                if(count(array_unique($checkunique))==1 && end($checkunique) == 1){
                                 $sql_update="Update $materialrequest_tab SET mrc_Status='2' WHERE  $materialrequest_tab.ID=$materialreqno";
                                 $stmt1 = $this->db->prepare($sql_update);
                                 $stmt1->execute();
                                } 
                             }
                         }
                                                       
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('message', '- Success -');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/materialissueconsume');
                 } else {
                        //edit option
                        //if submit failed to insert form
                        $this->tpl->set('message', 'Failed to submit!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/material_issue_consumable_tools.php'));
                 }
                 
                break;

            case 'add':
                $this->tpl->set('mode', 'add');
                $this->tpl->set('page_header', 'Store');
                $con_materialissue_no=$dbutil->keyGeneration('materialissue_consumables','MIN','','con_materialissue_no');
                $this->tpl->set('con_materialissue_no', $con_materialissue_no);
                $this->tpl->set('content', $this->tpl->fetch('factory/form/material_issue_consumable_tools.php'));
                break;

            default:
                /*
                 * List form
                 */
                 
                ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
     $colArr = array(
            "$materialissue_consumables_tab.ID",
            "$materialissue_consumables_tab.con_materialissue_no",
           
           
           
            
            // "$po_master_tab.ID"

        );
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

            if (strpos($colNames, 'DATE') !== false) {
                list($colNames,$x) = $dbutil->dateFilterFormat($colNames);                    
            }else {
                $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);        		    
            }

              if ('' != $x) {
               $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
       IF (count($wsarr) >= 1) {
            $whereString = ' AND '. implode(' AND ', $wsarr);
        }
       } else {
         $whereString ="ORDER BY $materialissue_consumables_tab.ID DESC";
       }
       
    $sql = "SELECT "
                . implode(',',$colArr)
                . " FROM $materialissue_consumables_tab "
                . " WHERE "
                . " $materialissue_consumables_tab.entity_ID = $entityID" 
               ." $whereString";
        
     

            $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
        /*
         * SET DATA TO TEMPLATE
                    */
       $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
     
     
        $this->tpl->set('table_columns_label_arr', array('ID','Material Issue No'));
        
        /*
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
        // $this->tpl->set('dcpdf','Generate Pdf');            
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
               
        //////////////////////close//////////////////////////////////////  
                 
                include_once $this->tpl->path . '/factory/form/material_issue_consumable_tools_crud_form.php';
                $cus_form_data = Form_Elements::data($this->crg);
                include_once 'util/crud3_1.php';
                new Crud3($this->crg, $cus_form_data);
                break;
        }

    ///////////////Use different template////////////////////
    $this->tpl->set('master_layout', 'layout_datepicker.php');
//     $this->tpl->set('master_layout', 'layout_chart.php');  
      // $this->tpl->set('master_layout', 'layout_datepicker.php'); 
    ///////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then /////
    ///////////////////////////////////////////////////////////////////
 } else {
         if ($this->ses->get('user')['ID']) {
             $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
         } else {
             header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
         }
     }
} 


///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
//////////////////   Qc Inventory Transfer

function qcinventorytransfer(){
     
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
         
        /////////////////////////////////////////////////////////////////
        //////////////////////////////access condition applied///////////
        /////////////////////////////////////////////////////////////////
        
        include_once 'util/DBUTIL.php';
        $dbutil = new DBUTIL($this->crg);
        
        include_once 'util/genUtil.php';
        $util = new GenUtil();
         
        $entityID = $this->ses->get('user')['entity_ID'];
        $userID = $this->ses->get('user')['ID'];
        $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
        $unit_table = $this->crg->get('table_prefix') . 'unit';
        $po_detail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
        $po_master_tab = $this->crg->get('table_prefix') . 'purchaseorder';
        $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
        $indent_master_tab = $this->crg->get('table_prefix') . 'purchaseindent';
        $indent_det_tab = $this->crg->get('table_prefix') . 'purchaseindentdetail';
        $state_tab = $this->crg->get('table_prefix') . 'state';
        $unit_tab = $this->crg->get('table_prefix') . 'unit';
        $material_inward_tab = $this->crg->get('table_prefix') . 'material_inward';
        $material_inward_detail_tab = $this->crg->get('table_prefix') . 'material_inward_detail';
        $rawmeterial_issue_tab = $this->crg->get('table_prefix') . 'rawmeterial_issue';
        $rawmeterial_issue_det_tab = $this->crg->get('table_prefix') . 'rawmeterial_issue_detail';
        $stockreturn_mast_tab = $this->crg->get('table_prefix') . 'stockreturn_mast';
        $stockreturn_detail_tab = $this->crg->get('table_prefix') . 'stockreturn_detail';
        $inventory_transfer_tab = $this->crg->get('table_prefix') . 'inventory_transfer';
        $inventory_transfer_detail_tab = $this->crg->get('table_prefix') . 'inventory_transfer_detail';
        $qcinventory_transfer_tab = $this->crg->get('table_prefix') . 'qcinventory_transfer';
        $qcinventory_transfer_detail_tab = $this->crg->get('table_prefix') . 'qcinventory_transfer_detail';
        $stock_tab = $this->crg->get('table_prefix') . 'stock';
        $stock_trans_tab = $this->crg->get('table_prefix') . 'stock_trans';
         $entity_tab = $this->crg->get('table_prefix') . 'entity';  
    
        
        //indent table data
       
        $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1 and $indent_master_tab.PI_Status=1 ORDER BY $indent_master_tab.ID DESC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $indent_data  = $stmt->fetchAll();	
        $this->tpl->set('indent_data', $indent_data);

        

        //indent table data
       
        $sql = "SELECT ID,Inventory_transferNo FROM $inventory_transfer_tab WHERE  entity_ID != $entityID AND qcinventory_status=1";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $inventory_transfer_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('inventory_transfer_tab_data', $inventory_transfer_tab_data);


         //perchase Order table data
       
         $sql = "SELECT ID,MaterialNo FROM $material_inward_tab where  entity_ID = $entityID";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $material_inward_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('material_inward_tab_data', $material_inward_tab_data);

         //perchase Order table data
       
         $sql = "SELECT ID,PurchaseOrderNo,purchaseindent_ID FROM $po_master_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $po_master_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('po_master_tab', $po_master_tab_data);
        
        //supplier table data
       
        $sql = "SELECT ID,SupplierName FROM $supplier_tab ORDER BY $supplier_tab.SupplierName ASC";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $supplier_data  = $stmt->fetchAll();	
        $this->tpl->set('supplier_data', $supplier_data);
        
        //rawmaterial table data
       
        $sql = "SELECT ID,RMName FROM $rawmaterial_table";
        $stmt = $this->db->prepare($sql);            
        $stmt->execute();
        $rawmaterial_data  = $stmt->fetchAll();	
        $this->tpl->set('rawmaterial_data', $rawmaterial_data);

         //Material Inward table data
       
         $sql = "SELECT ID,MaterialNo FROM $material_inward_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $material_data  = $stmt->fetchAll();	
         $this->tpl->set('material_data', $material_data);
        
        //unit table data
       
        $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
        $stmt = $this->db->prepare($pdt_sql);            
        $stmt->execute();
        $unit_data  = $stmt->fetchAll();	
        $this->tpl->set('unit_data', $unit_data);
 
        $this->tpl->set('page_title', 'QC Inventory Transfer');	          
        $this->tpl->set('page_header', 'Store');
        
        //Add Role when u submit the add role form
        $thisPageURL = $this->crg->get('route')['base_path'] . '/' . $this->crg->get('route')['module'] . '/' . $this->crg->get('route')['controller'] . '/' . $this->crg->get('route')['action'];

        $crud_string = null;

        if (isset($_POST['req_from_list_view'])) {
            $crud_string = strtolower($_POST['req_from_list_view']);
        }              
        
        //Edit submit
        if (!empty($_POST['edit_submit_button']) && $_POST['edit_submit_button'] == 'edit') {
            $crud_string = 'editsubmit';
        }
        
        //Add submit
        if (!empty($_POST['add_submit_button']) && $_POST['add_submit_button'] == 'add') {
            $crud_string = 'addsubmit';
        }
        

        switch ($crud_string) {
             case 'delete':                    
                  $data = trim($_POST['ycs_ID']);
                   
                   
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                   
                }
                
                $sql_update="UPDATE $inventory_transfer_tab SET qcinventory_status=1 where ID=(SELECT Inventory_transferNo FROM $qcinventory_transfer_tab where $qcinventory_transfer_tab.ID = $data)";
                $stmt1 = $this->db->prepare($sql_update);
                $stmt1->execute();
                
              $sql_update="UPDATE ycias_stock
                LEFT JOIN ycias_qcinventory_transfer_detail
                ON ycias_qcinventory_transfer_detail.item_name=ycias_stock.rawmaterial_id
                SET ycias_stock.available_qty=(ycias_stock.available_qty-ycias_qcinventory_transfer_detail.accepted_qty)
                WHERE ycias_stock.rawmaterial_id=ycias_qcinventory_transfer_detail.item_name AND ycias_qcinventory_transfer_detail.qcinventory_id=$data"; 
                
                $sqldetdelete="DELETE FROM $qcinventory_transfer_tab
                WHERE $qcinventory_transfer_tab.ID=$data";  


               
                               
                $stmt = $this->db->prepare($sqldetdelete);            
                    
                    if($stmt->execute()){
                    $this->tpl->set('message', 'Stock Return deleted successfully');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/qcinventorytransfer');
                    // $this->tpl->set('label', 'List');
                    // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    } 
                    
        break;
            case 'view':                    
                $data = trim($_POST['ycs_ID']);
             
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to view!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', 'view');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);   
                
                 $sql = "SELECT ID,Inventory_transferNo FROM $inventory_transfer_tab";
         $stmt = $this->db->prepare($sql);            
         $stmt->execute();
         $inventory_transfer_tab_data  = $stmt->fetchAll();	
         $this->tpl->set('inventory_transfer_tab_data', $inventory_transfer_tab_data);

            //     $sqlsrr="SELECT 
            //     $qcinventory_transfer_tab.*,
            //     $qcinventory_transfer_detail_tab.*,
            //     $inventory_transfer_tab.from_warehouse,
            //     $inventory_transfer_tab.to_warehouse,
            //     $rawmaterial_table.RMName,
            //     $inventory_transfer_detail_tab.batch_no,
            //     $stock_tab.available_qty,
            //     $inventory_transfer_detail_tab.transfer_quantity,
            //     $inventory_transfer_detail_tab.item_name,
            //     $inventory_transfer_detail_tab.unit_id,
            //     $unit_table.UnitName
            // FROM
            //     $qcinventory_transfer_tab,
            //     $qcinventory_transfer_detail_tab,
            //     $inventory_transfer_tab,
            //     $inventory_transfer_detail_tab,
            //     $rawmaterial_table,
            //     $unit_table,
            //     $stock_tab
            // WHERE
            //     $qcinventory_transfer_tab.ID  = $qcinventory_transfer_detail_tab.qcinventory_id AND 
            //     $qcinventory_transfer_tab.Inventory_transferNo = $inventory_transfer_tab.ID AND 
            //     $inventory_transfer_tab.ID = $inventory_transfer_detail_tab.inventory_transfer_ID AND 
                
            //     $qcinventory_transfer_detail_tab.item_name = $inventory_transfer_detail_tab.item_name AND 
            //     $qcinventory_transfer_detail_tab.item_name = $stock_tab.rawmaterial_id AND
                
            //     $inventory_transfer_detail_tab.item_name = $rawmaterial_table.ID AND 
            //     $inventory_transfer_detail_tab.unit_id = $unit_table.ID AND 
            //     $qcinventory_transfer_tab.ID = $data";
            // $stock_tab.available_qty,
            
            $sqlsrr="SELECT 
                $qcinventory_transfer_tab.*,
                $qcinventory_transfer_detail_tab.*,
                from_entity.Title as from_title,
                to_entity.Title as to_title,
                $rawmaterial_table.RMName,
                $inventory_transfer_detail_tab.batch_no,
                
                $inventory_transfer_detail_tab.transfer_quantity,
                $inventory_transfer_detail_tab.item_name,
                $inventory_transfer_detail_tab.unit_id,
                $unit_table.UnitName
            FROM
                $qcinventory_transfer_tab
               
            
               INNER JOIN $qcinventory_transfer_detail_tab ON $qcinventory_transfer_tab.ID  = $qcinventory_transfer_detail_tab.qcinventory_id 
               INNER JOIN $inventory_transfer_tab ON $qcinventory_transfer_tab.Inventory_transferNo = $inventory_transfer_tab.ID 
               INNER JOIN $inventory_transfer_detail_tab ON  $inventory_transfer_tab.ID = $inventory_transfer_detail_tab.inventory_transfer_ID 
                
               INNER JOIN $stock_tab ON  $qcinventory_transfer_detail_tab.item_name = $stock_tab.rawmaterial_id 
                
                
               INNER JOIN $rawmaterial_table ON $inventory_transfer_detail_tab.item_name = $rawmaterial_table.ID 
               INNER JOIN $unit_table ON $inventory_transfer_detail_tab.unit_id = $unit_table.ID 

               INNER JOIN $entity_tab AS from_entity ON $inventory_transfer_tab.from_warehouse = from_entity.ID 
               INNER JOIN $entity_tab AS to_entity ON $inventory_transfer_tab.to_warehouse = to_entity.ID

                WHERE
                $qcinventory_transfer_detail_tab.item_name = $inventory_transfer_detail_tab.item_name AND
                $qcinventory_transfer_tab.entity_ID = $stock_tab.entity_ID AND 
                $qcinventory_transfer_tab.ID = $data"; 
                
                $indent_data = $dbutil->getSqlData($sqlsrr);               
              
                
                        
               
                
                
                //edit option     
                $this->tpl->set('message', 'You can view QC Inventory Transfer form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/qcinventory_transfer_form.php'));                    
                break;
            
            case 'edit':                    
                $data = trim($_POST['ycs_ID']);
                $mode='edit';
                if(isset($_SESSION['ycs_ID']))
                {
                    $data = trim($_SESSION['ycs_ID']);
                    unset($_SESSION['ycs_ID']);
                    $mode='Confirm';
                }
           // var_dump($data);
                if (!$data) {
                    $this->tpl->set('message', 'Please select any one ID to edit!');
                    $this->tpl->set('label', 'List');
                    $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    break;
                }
                
                //mode of form submit
                $this->tpl->set('mode', $mode);
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);         
               

                $sqlsrr="SELECT 
                $qcinventory_transfer_tab.*,
                $qcinventory_transfer_detail_tab.*,
                $inventory_transfer_tab.from_warehouse,
                $inventory_transfer_tab.to_warehouse,
                $rawmaterial_table.RMName,
                $inventory_transfer_detail_tab.batch_no,
                $stock_tab.available_qty,
                $inventory_transfer_detail_tab.transfer_quantity,
                $inventory_transfer_detail_tab.item_name,
                $inventory_transfer_detail_tab.unit_id,
                $unit_table.UnitName
            FROM
                $qcinventory_transfer_tab,
                $qcinventory_transfer_detail_tab,
                $inventory_transfer_tab,
                $inventory_transfer_detail_tab,
                $rawmaterial_table,
                $unit_table,
                $stock_tab
            WHERE
                $qcinventory_transfer_tab.ID  = $qcinventory_transfer_detail_tab.qcinventory_id AND 
                $qcinventory_transfer_tab.Inventory_transferNo = $inventory_transfer_tab.ID AND 
                $inventory_transfer_tab.ID = $inventory_transfer_detail_tab.inventory_transfer_ID AND 
                
                $qcinventory_transfer_detail_tab.item_name = $inventory_transfer_detail_tab.item_name AND 
                $qcinventory_transfer_detail_tab.item_name = $stock_tab.rawmaterial_id AND
                
                $inventory_transfer_detail_tab.item_name = $rawmaterial_table.ID AND 
                $inventory_transfer_detail_tab.unit_id = $unit_table.ID AND 
                $qcinventory_transfer_tab.ID = $data";     

                $indent_data = $dbutil->getSqlData($sqlsrr);               
                $indentsel_data  = $stmt->fetchAll();	
                $this->tpl->set('indent_data', $indentsel_data);
            
                //edit option     
                $this->tpl->set('message', 'You can edit Stock Return form');
                $this->tpl->set('page_header', 'Store');
                $this->tpl->set('FmData', $indent_data); 
                
                $this->tpl->set('content', $this->tpl->fetch('factory/form/qcinventory_transfer_form.php'));                    
                break;
            
            case 'editsubmit':             
                $data = trim($_POST['ycs_ID']);
                
                //mode of form submit
                $this->tpl->set('mode', 'edit');
                //set id to edit $ycs_ID
                $this->tpl->set('ycs_ID', $data);

                //Post data
                include_once 'util/genUtil.php';
                $util = new GenUtil();
                $form_post_data = $util->arrFltr($_POST);

                //        echo '<pre>';
                //    print_r($form_post_data); die;
                
                // $sqldet_del = "DELETE FROM $po_detail_tab WHERE purchaseorder_ID=$data";
                // $stmt = $this->db->prepare($sqldet_del);
                // $stmt->execute();   
                        
                        try{
                        $Inventory_transferNo = $form_post_data['Inventory_transferNo'];


                        $sql2 = "UPDATE $qcinventory_transfer_tab set

                        Inventory_transferNo='$Inventory_transferNo'

                         WHERE ID=$data";	                        
                        $stmt = $this->db->prepare($sql2);   
                        // $stmt->execute();

                        $sql3 = "DELETE FROM $qcinventory_transfer_detail_tab WHERE qcinventory_id=$data";
					     $stmt3 = $this->db->prepare($sql3);

                    if($stmt3->execute()){
                    
                    FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {                         
                          
                            // $accepted_qty =$form_post_data['accepted_qty_' . $entry_count];	
                               $item_name =$form_post_data['item_name_' . $entry_count];									
                               $accepted_qty =$form_post_data['accepted_qty_' . $entry_count];	
                               $acceptedqty =$form_post_data['acceptedqty_' . $entry_count];
                               $unit_id =$form_post_data['unit_id_' . $entry_count];		
                               
                                $sql_update="Update $stock_tab SET available_qty=(available_qty-'$acceptedqty') 
                                             WHERE   
                                             $stock_tab.rawmaterial_id=$item_name ";               
                                             $update_stmt = $this->db->prepare($sql_update);
                                             $update_stmt->execute();
                           
                            if(!empty($item_name)){
                                
                                 $sql_update="Update $stock_tab SET available_qty=(available_qty+'$accepted_qty') 
                                              WHERE    
                                              $stock_tab.rawmaterial_id=$item_name ";               
                                              $update_stmt = $this->db->prepare($sql_update);
                                              $update_stmt->execute();
                          
                                    $vals = "'" . $data . "'," .    
                                    "'" . $item_name . "',".                               
                                    "'" . $accepted_qty . "',".
                                    "'" . $unit_id . "'";

                            $sql2 = "INSERT INTO $qcinventory_transfer_detail_tab
                                    ( 
                                        `qcinventory_id`,
                                        `item_name`,                                        
                                        `accepted_qty`,
                                        `unit_id`                                       
                                    ) 
                            VALUES ($vals)";        

                            $stmt = $this->db->prepare($sql2);
                            $stmt->execute();              
                        }
                        }  

                        
                    }
                                    
                        $this->tpl->set('message', 'Inventory Transfer form edited successfully!');   
                        header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/qcinventorytransfer');
                       
                        } catch (Exception $exc) {
                         //edit failed option
                        $this->tpl->set('message', 'Failed to edit, try again!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/qcinventory_transfer_form.php'));
                        }

                break;
                
            case 'addsubmit':
                
                if (isset($crud_string)) {
                     
                    $form_post_data = $dbutil->arrFltr($_POST);
                    // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                    //  $completed_on=!empty($form_post_data['completed_on'])?date("Y-m-d", strtotime($form_post_data['completed_on'])):'';
                    // "'" . $dispatch_date . "'," .
            //   echo      $purchase_indentno=$form_post_data['Inventory_transferNo']; die;ycias_qcinventory_transfer
                //     echo '<pre>';
                //    print_r($form_post_data); die;
                $from=$form_post_data['from_warehouse'];
                $to=$form_post_data['to_warehouse'];
                $concat = $from." TO ".$to; 
                $inventory_id= $form_post_data['Inventory_transferNo'] ;
                                            
                $val =         "'" . $form_post_data['Inventory_transferNo'] . "'," .                           
                                  
                                  
                                    "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                    "'" .  $this->ses->get('user')['ID'] . "'";

                       $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "qcinventory_transfer`
                                        ( 
                                        
                                        `Inventory_transferNo`,
                                                                           
                                        `entity_ID`,
                                        `users_ID`
                                        ) 
                                VALUES ( $val )";             
                                $stmt = $this->db->prepare($sql);     
                                
                                    $sql_update = "UPDATE $inventory_transfer_tab SET qcinventory_status = 2 where ID = $inventory_id";
                                    $stmt1 = $this->db->prepare($sql_update);
                                    $stmt1->execute();
                                
                                // $stmt->execute(); 
                    if ($stmt->execute()) { 
                    
                    $lastInsertedID = $this->db->lastInsertId();  

                    $form_post_data['maxCount'];        

                  FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {

                            
                            $item_name =$form_post_data['item_name_' . $entry_count];
                             $available_qty =$form_post_data['available_qty_' . $entry_count];
                            $accepted_qty =$form_post_data['accepted_qty_' . $entry_count];
                            $unit_id =$form_post_data['unit_id_' . $entry_count];   
                            $hai =$form_post_data['RMName_' . $entry_count];
                            $unit_name =$form_post_data['UnitName_' . $entry_count]; 
                           
                            
                        
                            
                           
                            
                            $vals = "'" . $lastInsertedID . "'," .
                                 "'" . $item_name . "'," .
                                  "'" . $available_qty . "'," .
                                    "'" . $accepted_qty. "',".
                                    "'" . $unit_id . "'" ;
                                   
                          $sql2 = "INSERT INTO $qcinventory_transfer_detail_tab
                                    ( 
                                        `qcinventory_id`, 
                                        `item_name`,
                                         `available_qty`,
                                        `accepted_qty`,
                                        `unit_id`
                                       
                                       
                                       
                                    ) 
                            VALUES ($vals)";               
                            $stmt = $this->db->prepare($sql2);
                            
                        
                            
                       // $stmt->execute();     
                        if($stmt->execute()){
                        // $sql_update="UPDATE ycias_stock
                        //                      LEFT JOIN ycias_qcinventory_transfer_detail
                        //                      ON ycias_qcinventory_transfer_detail.item_name=ycias_stock.rawmaterial_id
                        //                      SET ycias_stock.available_qty=(ycias_stock.available_qty+$accepted_qty)
                        //                      WHERE ycias_stock.rawmaterial_id=ycias_qcinventory_transfer_detail.item_name";          
                        //                  $update_stmt = $this->db->prepare($sql_update);
                        //                  $update_stmt->execute();
                                         
                        $sql_update="UPDATE ycias_stock                                        
                                             SET available_qty=(available_qty+$accepted_qty)
                                             WHERE rawmaterial_id =  $item_name AND entity_ID = $entityID";                                                   
                                         $update_stmt = $this->db->prepare($sql_update);
                                         $update_stmt->execute();
                                         
                         $sqlsrr = "SELECT * FROM  ycias_material_inward_detail WHERE ycias_material_inward_detail.item_id = $item_name";
                                 $qc_det_data = $dbutil->getSqlData($sqlsrr);    
                                         // print_r($qc_det_data[0]['item_id']); die;
                                 $item_id=(count($qc_det_data)>0) ? $qc_det_data[0]['cost_unit']:''; 
                                         
                         $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType, raw_material_ID, raw_material_name, stockin, unit_name,warehouse_name,stock_value, entity_ID, users_ID)
                                       VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'QC inventory transfer', $item_name, '$hai', $accepted_qty, '$unit_name','$concat',$item_id, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                     
                                       $update_stmt = $this->db->prepare($sql_update);
                                       $update_stmt->execute();
                          }      
                        } 
                        
                        
                      
                    }
                                                       
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('message', '- Success -');
                    header('Location:' . $this->crg->get('route')['base_path'] . '/store/cst/qcinventorytransfer');
                 } else {
                        //edit option
                        //if submit failed to insert form
                        $this->tpl->set('message', 'Failed to submit!');
                        $this->tpl->set('FmData', $form_post_data);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/qcinventory_transfer_form.php'));
                 }
                 
                break;

            case 'add':
                $this->tpl->set('mode', 'add');
                $this->tpl->set('page_header', 'Store');
                $MaterialNo=$dbutil->keyGeneration('materialinward','MNO','','MaterialNo');
                $this->tpl->set('MaterialNo', $MaterialNo);
                $this->tpl->set('content', $this->tpl->fetch('factory/form/qcinventory_transfer_form.php'));
                break;

            default:
                /*
                 * List form
                 */
                 
                ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
     $colArr = array(
            "$qcinventory_transfer_tab.ID",
            "$inventory_transfer_tab.Inventory_transferNo", 
            // "$inventory_transfer_tab.from_warehouse",
            // "$inventory_transfer_tab.to_warehouse",
            "entity_from.Title AS from_warehouse_title",
            "entity_to.Title AS to_warehouse_title",
            
            // "$po_master_tab.ID"

        );
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

            if (strpos($colNames, 'DATE') !== false) {
                list($colNames,$x) = $dbutil->dateFilterFormat($colNames);                    
            }else {
                $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);        		    
            }

              if ('' != $x) {
               $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
       IF (count($wsarr) >= 1) {
            $whereString = ' AND '. implode(' AND ', $wsarr);
        }
       } else {
         $whereString ="ORDER BY $qcinventory_transfer_tab.ID DESC";
       }
       
    $sql = "SELECT "
                . implode(',',$colArr)
                . " FROM $qcinventory_transfer_tab LEFT JOIN $inventory_transfer_tab ON $qcinventory_transfer_tab.Inventory_transferNo = $inventory_transfer_tab.ID"
                . " LEFT JOIN $entity_tab AS entity_from ON $inventory_transfer_tab.from_warehouse = entity_from.ID"
                . " LEFT JOIN $entity_tab AS entity_to ON $inventory_transfer_tab.to_warehouse = entity_to.ID"
                . " WHERE "
                . " $qcinventory_transfer_tab.entity_ID = $entityID"
                . " $whereString"; 
        
     

            $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
        /*
         * SET DATA TO TEMPLATE
                    */
       $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
     
     
       $this->tpl->set('table_columns_label_arr', array('ID','Inventory Transfer No','From Warehouse','To Warehouse'));
        // $this->tpl->set('table_columns_label_arr', array('ID','store','stockreturn_no','po_no'));
        
        /*
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
        // $this->tpl->set('dcpdf','Generate Pdf');            
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
               
        //////////////////////close//////////////////////////////////////  
                 
                include_once $this->tpl->path . '/factory/form/qcinventory_transfer_crud_form.php';
                $cus_form_data = Form_Elements::data($this->crg);
                include_once 'util/crud3_1.php';
                new Crud3($this->crg, $cus_form_data);
                break;
        }

    ///////////////Use different template////////////////////
    // $this->tpl->set('master_layout', 'layout_chart.php'); 
    $this->tpl->set('master_layout', 'layout_datepicker.php');
    ///////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then /////
    ///////////////////////////////////////////////////////////////////
 } else {
         if ($this->ses->get('user')['ID']) {
             $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
         } else {
             header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
         }
     }
}


///////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
/////////////////////   stock transactin report


// function stacktransreport() {
//     if ($this->crg->get('wp') || $this->crg->get('rp')) {
// //////////////////////////////////////////////////////////////////////////////////
// //////////////////////////////access condition applied///////////////////////////
// //////////////////////////////////////////////////////////////////////////////// 
      
//       include_once 'util/DBUTIL.php';
//       $dbutil = new DBUTIL($this->crg);
         
//          $entityID = $this->ses->get('user')['entity_ID'];
//          $userID = $this->ses->get('user')['ID'];
        
        
//       $stock_trans = $this->crg->get('table_prefix') . 'stock_trans';
              
      

//         ////////////////////start//////////////////////////////////////////////
                
//       //bUILD SQL 
//         $whereString = '';
        
//         $colArr = array(
//             "$stock_trans.ID", 
//             "$stock_trans.date",
//             "$stock_trans.raw_material_typ",
//             "$stock_trans.raw_material_name",
//             "$stock_trans.batch_no",
//             "$stock_trans.stockin",
//             "$stock_trans.stockout",
           
//             "$stock_trans.available_qty"
           
//         );
        
//         $this->tpl->set('FmData', $_POST);
//         foreach($_POST as $k=>$v){
//             if(strpos($k,'^')){
//                 unset($_POST[$k]);
//             }
//             $_POST[str_replace('^','_',$k)] = $v;
//         }
//         $PD=$_POST;
//         if($_POST['list']!=''){
//             $this->tpl->set('FmData', NULL);
//             $PD=NULL;
//         }

//         IF (count($PD) >= 2) {
//             $wsarr = array();
//             foreach ($colArr as $colNames) {

//       if (strpos($colNames, 'Date') !== false) {
//             list($colNames,$x) = $dbutil->dateFilterFormat($colNames);
//         } else {
//             $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);
//         }

//                 if ('' != $x) {
//                     $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
//                 }
//             }
            
//           IF (count($wsarr) >= 1) {
//              $whereString = ' AND '. implode(' AND ', $wsarr);
//           }
//         }
        
//         $orderBy ="ORDER BY $stock_trans.ID DESC";
        
//      $sql = "SELECT "
//              . implode(',',$colArr)
//              . " FROM $stock_trans "
//              . " WHERE "
//              . " $stock_trans.entity_ID=$entityID "
//              . " $whereString "
//              . " $orderBy";
     
//         $results_per_page = 50;     
        
//             if(isset($PD['pageno'])){$page=$PD['pageno'];}
//             else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
//             else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
//             else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
//             else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
//             else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
//             else{$page=1;} 
     
//         /*
//          * SET DATA TO TEMPLATE
//          */
//         $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
//         /*
//          * set table label
//          */
//         $this->tpl->set('table_columns_label_arr', array('ID','date','Raw Material Name','Raw Material Name','Batch No.','Stockin','stockout','availabel Qty'));
        
//         /*,;;
//          * selectColArr for filter form
//          */
        
//         $this->tpl->set('selectColArr',$colArr);
                    
//         /*
//          * set pagination template
//          */
//         $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table_copy.php');
               
//         //////////////////////close//////////////////////////////////////  
      
//          include_once $this->tpl->path .'/factory/form/stocktransferReport_crud_form.php';
        
        
//         $cus_form_data = Form_Elements::data($this->crg);
//         include_once 'util/crud3_1.php';
//         new Crud3($this->crg, $cus_form_data);
//         // $this->tpl->set('master_layout', 'layout_chart.php'); 
//         // $this->tpl->set('master_layout', 'layout_chart.php'); 
//         $this->tpl->set('master_layout', 'layout_datepicker.php');
//          //if crud is delivered at different point a template
//         //Then  call that template and set to content
       
//       ////$this->tpl->set('content', $this->tpl->fetch('modules/customer/manage_customer.php'));
//     //////////////////////////////////////////////////////////////////////////////////
//     //////////////////////////////on access condition failed then ///////////////////////////
//     ////////////////////////////////////////////////////////////////////////////////            
//     } else {
//         if ($this->ses->get('user')['ID']) {
//             $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
//         } else {
//             header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
//         }
//     }
// } 

///////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
///////////////////// Stock Statment

function Stockstatement() {
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////access condition applied///////////////////////////
//////////////////////////////////////////////////////////////////////////////// 
      
       include_once 'util/DBUTIL.php';
       $dbutil = new DBUTIL($this->crg);
         
         $entityID = $this->ses->get('user')['entity_ID'];
         $userID = $this->ses->get('user')['ID'];
        
        
       $rawmaterial = $this->crg->get('table_prefix') . 'rawmaterial';
       $rawmaterialsubtype = $this->crg->get('table_prefix') . 'rawmaterialsubtype';
       $rawmaterialtype = $this->crg->get('table_prefix') . 'rawmaterialtype';
       $material_inward_detail = $this->crg->get('table_prefix') . 'material_inward_detail';
       $qc_materialinward_detail = $this->crg->get('table_prefix') . 'qc_materialinward_detail';
       $purchaseorderdetail = $this->crg->get('table_prefix') . 'purchaseorderdetail';
       $purchaseorder = $this->crg->get('table_prefix') . 'purchaseorder';
       $material_inward = $this->crg->get('table_prefix') . 'material_inward';
       $qc_materialinward = $this->crg->get('table_prefix') . 'qc_materialinward';
       $stock = $this->crg->get('table_prefix') . 'stock';
              
      

        ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
        $colArr = array(
            "$stock.ID", 
            "$rawmaterialtype.RawMaterialType",
            "$rawmaterial.RMName",
            "$stock.batch_no",
            "$stock.available_qty"
        );
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

       if (strpos($colNames, 'Date') !== false) {
            list($colNames,$x) = $dbutil->dateFilterFormat($colNames);
        } else {
            $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);
        }

                if ('' != $x) {
                    $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
          IF (count($wsarr) >= 1) {
             $whereString = ' AND '. implode(' AND ', $wsarr);
          }
        }
        
        $orderBy ="ORDER BY $stock.ID DESC";
    
    $sql = "SELECT "
    . implode(',',$colArr)
    . " FROM $stock, $rawmaterial,$rawmaterialtype WHERE"
    
    . " $stock.rawmaterial_id = $rawmaterial.ID"
    . " AND "
    . " $stock.rawmaterial_type_id = $rawmaterialtype.ID "
    . " AND "
    . " $stock.entity_ID=$entityID "
    . " $whereString "
    . " $orderBy";  
        $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
     
        /*
         * SET DATA TO TEMPLATE
         */
        $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
        /*
         * set table label
         */
        $this->tpl->set('table_columns_label_arr', array('ID','Raw Material Type','Raw Material Name','Batch No.','available Qty'));
        
        /*,;;
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
                    
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table_stocksstatement.php');
               
        //////////////////////close//////////////////////////////////////  
      
         include_once $this->tpl->path .'/factory/form/stocksstatement_crud_form.php';
        
        
        $cus_form_data = Form_Elements::data($this->crg);
        include_once 'util/crud3_1.php';
        new Crud3($this->crg, $cus_form_data);
        // $this->tpl->set('master_layout', 'layout_chart.php'); 
        // $this->tpl->set('master_layout', 'layout_chart.php'); 
        $this->tpl->set('master_layout', 'layout_datepicker.php');
         //if crud is delivered at different point a template
        //Then  call that template and set to content
       
       ////$this->tpl->set('content', $this->tpl->fetch('modules/customer/manage_customer.php'));
    //////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then ///////////////////////////
    ////////////////////////////////////////////////////////////////////////////////            
    } else {
        if ($this->ses->get('user')['ID']) {
            $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
        } else {
            header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
        }
    }
}

function fgstock() {
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////access condition applied///////////////////////////
//////////////////////////////////////////////////////////////////////////////// 
      
       include_once 'util/DBUTIL.php';
       $dbutil = new DBUTIL($this->crg);
         
         $entityID = $this->ses->get('user')['entity_ID'];
         $userID = $this->ses->get('user')['ID'];
        
       $fgstock_tab = $this->crg->get('table_prefix') . 'fgstock';
       $product_tab = $this->crg->get('table_prefix') . 'product';
              
      
        ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
        $colArr = array(
            "$fgstock_tab.ID", 
            "$product_tab.ProductName",
            "$fgstock_tab.available_qty"
        );
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

       if (strpos($colNames, 'Date') !== false) {
            list($colNames,$x) = $dbutil->dateFilterFormat($colNames);
        } else {
            $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);
        }

                if ('' != $x) {
                    $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
          IF (count($wsarr) >= 1) {
             $whereString = ' AND '. implode(' AND ', $wsarr);
          }
        }
        
        $orderBy ="ORDER BY $fgstock_tab.ID DESC";
    
    $sql = "SELECT "
    . implode(',',$colArr)
    . " FROM $fgstock_tab, $product_tab WHERE"
    . " $fgstock_tab.product_ID = $product_tab.ID"
    . " AND "
    . " $fgstock_tab.entity_ID=$entityID "
    . " $whereString "
    . " $orderBy";  
        $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
     
        /*
         * SET DATA TO TEMPLATE
         */
        $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
        /*
         * set table label
         */
        $this->tpl->set('table_columns_label_arr', array('ID','Product Name','available Qty'));
        
        /*,;;
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
                    
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table_stocksstatement.php');
               
        //////////////////////close//////////////////////////////////////  
      
         include_once $this->tpl->path .'/factory/form/stocksstatement_crud_form.php';
        
        
        $cus_form_data = Form_Elements::data($this->crg);
        include_once 'util/crud3_1.php';
        new Crud3($this->crg, $cus_form_data);
        // $this->tpl->set('master_layout', 'layout_chart.php'); 
        // $this->tpl->set('master_layout', 'layout_chart.php'); 
        $this->tpl->set('master_layout', 'layout_datepicker.php');
         //if crud is delivered at different point a template
        //Then  call that template and set to content
       
       ////$this->tpl->set('content', $this->tpl->fetch('modules/customer/manage_customer.php'));
    //////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then ///////////////////////////
    ////////////////////////////////////////////////////////////////////////////////            
    } else {
        if ($this->ses->get('user')['ID']) {
            $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
        } else {
            header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
        }
    }
}


/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
////////////////////////////////
//////////////////////////////////////////
////////   stock Transaction

function stocktransaction() {
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////access condition applied///////////////////////////
//////////////////////////////////////////////////////////////////////////////// 
      
       include_once 'util/DBUTIL.php';
       $dbutil = new DBUTIL($this->crg);
    //    $start_date = $this->ses->get('user')['start_date'];
    //    $end_date = $this->ses->get('user')['end_date'];

         $entityID = $this->ses->get('user')['entity_ID'];
         $userID = $this->ses->get('user')['ID'];
               
       $stock_trans = $this->crg->get('table_prefix') . 'stock_trans';
       $product_table = $this->crg->get('table_prefix') . 'product';
       $rawmaterialtype_table = $this->crg->get('table_prefix') . 'rawmaterialtype';
       $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';

        ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
        $colArr = array(
            "$stock_trans.ID", 
            "$stock_trans.trans_date",
            "$stock_trans.TransactionType",
            "$rawmaterial_table.RMName",
            "$rawmaterialtype_table.RawMaterialType",
            "$product_table.ProductName",
            "$stock_trans.stockin",
            "$stock_trans.stockout",
            "$stock_trans.unit_name",
            "$stock_trans.warehouse_name",
            "$stock_trans.trans_ref",
            "$stock_trans.stock_value",
            "$stock_trans.remarks"          
        );
        
        $this->tpl->set('FmData', $_POST);
        foreach($_POST as $k=>$v){
            if(strpos($k,'^')){
                unset($_POST[$k]);
            }
            $_POST[str_replace('^','_',$k)] = $v;
        }
        $PD=$_POST;
        if($_POST['list']!=''){
            $this->tpl->set('FmData', NULL);
            $PD=NULL;
        }

        IF (count($PD) >= 2) {
            $wsarr = array();
            foreach ($colArr as $colNames) {

       if (strpos($colNames, 'Date') !== false) {
            list($colNames,$x) = $dbutil->dateFilterFormat($colNames);
        } else {
            $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);
        }

                if ('' != $x) {
                    $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                }
            }
            
          IF (count($wsarr) >= 1) {
             $whereString = ' AND '. implode(' AND ', $wsarr);
          }
        }


        
        $orderBy ="ORDER BY $stock_trans.ID DESC";

                    // var_dump($_POST['month_year']); ==> Start Date   

                    // var_dump($_POST['start_date']);
           
                    if(!empty($_POST['start_date'])){
                        $extract_start_date=explode('-',$_POST['start_date']);
                        $month=$util->getMonthNumber{($extract_start_date[0])};// month part from input 
                        $month=$extract_start_date[1];  
                        $date =$extract_start_date[0];
                        $year=$extract_start_date[2]; 
                                               // year part from input 
                        //$month_year=$month.'/'.$year; changed format as year-month wise on 15-11-2022
                        $start_date=$year.'-'.$month.'-'.$date;
                       
                    }else{
                       //$month_year=date('m/Y',strtotime("-1 month"));         // for getting lastmonth data
                      $start_date=date('d-m-y',strtotime("-1 month"));     
                    }

                      //var_dump($_POST['month_year']); ==> End Date
           
                      if(!empty($_POST['end_date'])){
                        $end_date_date=explode('-',$_POST['end_date']);
                        $month=$util->getMonthNumber{($end_date_date[0])};
                        // print_r ($end_date_date); die; // month part from input 
                        $month=$end_date_date[1];   
                        $date =$end_date_date[0];  
                        $year=$end_date_date[2];                       // year part from input 
                        //$month_year=$month.'/'.$year; changed format as year-month wise on 15-11-2022
                        $end_date=$year.'-'.$month.'-'.$date;
                       
                    }else{
                       //$month_year=date('m/Y',strtotime("-1 month"));         // for getting lastmonth data
                      $end_date=date('d-m-y',strtotime("-1 month"));     
                    }

     $sql = "SELECT "
             . implode(',',$colArr)
             . " FROM $stock_trans LEFT JOIN $product_table ON $product_table.ID=$stock_trans.product_ID LEFT JOIN $rawmaterialtype_table ON $rawmaterialtype_table.ID=$stock_trans.rawmaterialtype_ID LEFT JOIN $rawmaterial_table ON $rawmaterial_table.ID=$stock_trans.raw_material_ID"
             . " WHERE "
             . " trans_date BETWEEN '$start_date' AND '$end_date'"
             . " AND "
             . " $stock_trans.entity_ID = $entityID"
             . " $whereString "
             . " $orderBy";    
        $results_per_page = 50;     
        
            if(isset($PD['pageno'])){$page=$PD['pageno'];}
            else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
            else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
            else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
            else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
            else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
            else{$page=1;} 
     
        /*
         * SET DATA TO TEMPLATE
         */
        $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
        /*
         * set table label
         */
        $this->tpl->set('table_columns_label_arr', array('ID','transaction Date','Transaction Type','Rawmaterial Name','Rawmaterial type','Product Name','Stock In ','Stock Out','UOM','Warehouse','Transaction Ref','Stock Value','Remarks/Notes'));
        
        /*,;;
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
                    
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table_stocks_transaction.php');
        
        $this->tpl->set('widget_2', $this->tpl->fetch('factory/template/filterForms/stocktransaction.php'));    // ==> this is start date and end date
               
        //////////////////////close//////////////////////////////////////  
      
         include_once $this->tpl->path .'/factory/form/stocktransferReport_crud_form.php';
        
        
        $cus_form_data = Form_Elements::data($this->crg);
        include_once 'util/crud3_1.php';
        new Crud3($this->crg, $cus_form_data);
        // $this->tpl->set('master_layout', 'layout_chart.php'); 
        // $this->tpl->set('master_layout', 'layout_chart.php'); 
        $this->tpl->set('master_layout', 'layout_datepicker.php');
         //if crud is delivered at different point a template
        //Then  call that template and set to content
       
       ////$this->tpl->set('content', $this->tpl->fetch('modules/customer/manage_customer.php'));
    //////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////on access condition failed then ///////////////////////////
    ////////////////////////////////////////////////////////////////////////////////            
    } else {
        if ($this->ses->get('user')['ID']) {
            $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
        } else {
            header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
        }
    }
} 


}
