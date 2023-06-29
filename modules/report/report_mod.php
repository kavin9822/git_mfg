<?php

/**
 * Description of Purchase_Mod
 *
 * @author psmahadevan
 */
class report_mod {

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
    
public function premiumFreight(){
    
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
			 
			////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////access condition applied//////////////////////////
			////////////////////////////////////////////////////////////////////////////////    
						
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $premium_freight_table = $this->crg->get('table_prefix') . 'premium_freight';
            $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
            $supplier_table = $this->crg->get('table_prefix') . 'supplier';
            
            $rawmaterial_sql = "SELECT ID,RMName FROM $rawmaterial_table";
            $stmt = $this->db->prepare($rawmaterial_sql);            
            $stmt->execute();
            $rawmaterial_data  = $stmt->fetchAll();	
            $this->tpl->set('rawmaterial_data', $rawmaterial_data);
			
			$supplier_sql = "SELECT ID,SupplierName FROM $supplier_table";
            $stmt = $this->db->prepare($supplier_sql);            
            $stmt->execute();
            $supplier_data  = $stmt->fetchAll();	
            $this->tpl->set('supplier_data', $supplier_data);
			
            $this->tpl->set('page_title', 'Premium Freight Register');	          
            $this->tpl->set('page_header', 'Premium Freight Register');
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
                      // var_dump($data); 
                       
                       
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                       
                    }
					 
                     $sqldetdelete="Delete from $premium_freight_table
                                    where $premium_freight_table.ID=$data"; 
                     $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Premium Freight Register form deleted successfully');
																													  
                        //$this->tpl->set('label', 'List');
                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/premiumFreight');
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
                          
                      $sqlsrr = "SELECT * FROM $premium_freight_table 
                                 WHERE $premium_freight_table.ID= $data";
                                 $premium_freight_data = $dbutil->getSqlData($sqlsrr);
                   
                    //edit option     
                    $this->tpl->set('message', 'You can view Premium Freight Register form');
                    $this->tpl->set('page_header', 'Premium Freight Register');
                    $this->tpl->set('FmData', $premium_freight_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/premium_freight_register_design_form.php'));                    
                    break;
                
                case 'edit':                    
                    $data = trim($_POST['ycs_ID']);
               // var_dump($data);
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to edit!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                        break;
                    }
                    
                    //mode of form submit
                    $this->tpl->set('mode', 'edit');
                    //set id to edit $ycs_ID
                    $this->tpl->set('ycs_ID', $data);  
           			  
                    $sqlsrr = "SELECT * FROM $premium_freight_table 
                                 WHERE $premium_freight_table.ID= $data";
                                 $premium_freight_data = $dbutil->getSqlData($sqlsrr);
                    
                    //edit option 

					//print_r($_POST); 
                    $this->tpl->set('message', 'You can edit Premium Freight Register form');
                    $this->tpl->set('page_header', 'Premium Freight Register');
                    $this->tpl->set('FmData', $premium_freight_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/premium_freight_register_design_form.php'));                    
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
								
					$FreightDate=date("Y-m-d", strtotime($form_post_data['FreightDate']));
                    $InvoiceDate=date("Y-m-d", strtotime($form_post_data['InvoiceDate']));
						                       
                    try{
                          $FreightDate= $FreightDate;
						  $SupplierID= $form_post_data['SupplierID'];
						  $RawmaterialID    = $form_post_data['RawmaterialID'];
						  $Purchaseorder_no= $form_post_data['Purchaseorder_no'];
						  $DispatchDate= $form_post_data['DispatchDate'];
						  $Transport_mode= $form_post_data['Transport_mode'];
						  $InvoiceDate= $InvoiceDate;
						  $Invoice_no= $form_post_data['Invoice_no'];
						  $Quantity= $form_post_data['Quantity'];
                          $Freight_paid= $form_post_data['Freight_paid'];
                          $Remarks= $form_post_data['Remarks'];
						  
						 $sql_update="UPDATE $premium_freight_table set FreightDate='$FreightDate',SupplierID='$SupplierID',RawmaterialID='$RawmaterialID',Purchaseorder_no='$Purchaseorder_no',DispatchDate='$DispatchDate',Transport_mode='$Transport_mode',InvoiceDate='$InvoiceDate',Invoice_no='$Invoice_no',Quantity='$Quantity',Freight_paid='$Freight_paid',Remarks='$Remarks' WHERE ID=$data";
                         $stmt1 = $this->db->prepare($sql_update);
                         $stmt1->execute();
					       
                            $this->tpl->set('message', 'Premium Freight Register form edited successfully!');   
																														  
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
							header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/premiumFreight');
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/premium_freight_register_design_form.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
 
                        $form_post_data = $dbutil->arrFltr($_POST);
						
						include_once 'util/genUtil.php';
                         $util = new GenUtil();
                        
                      // var_dump($_POST);
                      $FreightDate=date("Y-m-d", strtotime($form_post_data['FreightDate']));
                      $InvoiceDate=date("Y-m-d", strtotime($form_post_data['InvoiceDate']));
                      $supplier_ID=$form_post_data['SupplierID'];
                       
					   if (isset($form_post_data['SupplierID'])) {
                           
                                        $val = "'" . $FreightDate . "'," .
										       "'" . $form_post_data['SupplierID'] . "'," .
											   "'" . $form_post_data['RawmaterialID'] . "'," .
										       "'" . $form_post_data['Purchaseorder_no'] . "'," .
											   "'" . $form_post_data['DispatchDate'] . "'," .
											   "'" . $form_post_data['Transport_mode'] . "'," .
											   "'" . $InvoiceDate . "'," .
										       "'" . $form_post_data['Invoice_no'] . "'," .
											   "'" . $form_post_data['Quantity']. "'," .
											   "'" . $form_post_data['Freight_paid']. "'," .
											   "'" . $form_post_data['Remarks'] . "'," .
                                              "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                         "'" .  $this->ses->get('user')['ID'] . "'";
        
                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "premium_freight`
                                            (
                                            `FreightDate`, 
											`SupplierID`, 
										    `RawmaterialID`, 
											`Purchaseorder_no`, 
											`DispatchDate`, 
											`Transport_mode`, 
										    `InvoiceDate`, 
											`Invoice_no`, 
											`Quantity`, 
											`Freight_paid`, 
											`Remarks`, 
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                         VALUES ( $val )";
                                  $stmt = $this->db->prepare($sql);
                                 // $stmt->execute();
                                 if($stmt->execute()){
                                       $sql_update="UPDATE ycias_supplier_report  SET trans_date=DATE_FORMAT(NOW(), '%Y-%m-%d'),premium_freight = 0 where ycias_supplier_report.supplier_id = $supplier_ID";
                                       $update_stmt = $this->db->prepare($sql_update);
                                       $update_stmt->execute();  
                                 }
						 
                        }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/premiumFreight');
																										
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/premium_freight_register_design_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Premium Freight Register');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/premium_freight_register_design_form.php'));
                    break;
             
                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
         $colArr = array(
                "$premium_freight_table.ID",
				"$supplier_table.SupplierName"
              
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
             $whereString ="ORDER BY $premium_freight_table.ID DESC";
           }
		   
		      
       $sql = "SELECT " 
                    . implode(',',$colArr)
                    . " FROM $premium_freight_table LEFT JOIN $supplier_table ON $supplier_table.ID = $premium_freight_table.SupplierID "
                    . " WHERE "
                    . " $premium_freight_table.entity_ID = $entityID" 
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
         
           $this->tpl->set('table_columns_label_arr', array('ID','Supplier Name'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_premium_freight_register_form.php';
                    $cus_form_data = Form_Elements::data($this->crg);
                    include_once 'util/crud3_1.php';
                    new Crud3($this->crg, $cus_form_data);
                    break;
            }

	    ///////////////Use different template////////////////////
	    $this->tpl->set('master_layout', 'layout_datepicker.php'); 
////////////////////////////////////////////////////////////////////////////////
//////////////////////////////on access condition failed then //////////////////
//////////////////////////////////////////////////////////////////////////////// 
     } else {
             if ($this->ses->get('user')['ID']) {
                 $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
             } else {
                 header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
             }
         }
    }
    
/////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////    FIELD FAILURE

function fieldfailure(){
     
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
            $field_failure_tab = $this->crg->get('table_prefix') . 'field_failure';
            
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
           
             $sql = "SELECT ID,PdnOrderNo FROM $productionorder_tab";  
             $stmt = $this->db->prepare($sql);            
             $stmt->execute();
             $productionorder_tab_data  = $stmt->fetchAll();	
             $this->tpl->set('productionorder_tab_data', $productionorder_tab_data);
    
              //work Order table data
           
              $sql = "SELECT ID,WorkOrderNo FROM $workorder_tab";  
              $stmt = $this->db->prepare($sql);            
              $stmt->execute();
              $workorder_tab_data  = $stmt->fetchAll();	
              $this->tpl->set('workorder_tab_data', $workorder_tab_data);
    
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
     
            $this->tpl->set('page_title', 'FIELD FAILURE');	          
            $this->tpl->set('page_header', 'Report');
            
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
                    
                    $sql_update="UPDATE $stock_table
                    LEFT JOIN $rawmeterial_issue_det_tab
                    ON $rawmeterial_issue_det_tab.rawmaterial_ID=$stock_table.rawmaterial_id
                    SET $stock_table.available_qty=($stock_table.available_qty+$rawmeterial_issue_det_tab.issued_qty)
                    WHERE $stock_table.rawmaterial_id=$rawmeterial_issue_det_tab.rawmaterial_ID AND $rawmeterial_issue_det_tab.rawmaterial_ID=$data";          
                    $update_stmt = $this->db->prepare($sql_update);
                    $update_stmt->execute();
                     
                    $sqldetdelete="DELETE $field_failure_tab FROM $field_failure_tab
                                   WHERE $field_failure_tab.ID=$data";  
                                   
                    $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Raw Material Issue deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/fieldfailure');
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
                  
    
                    $sqlsrr ="select * from $field_failure_tab
                               WHERE
                                            $field_failure_tab.ID= $data";  
                    
                    $indent_data = $dbutil->getSqlData($sqlsrr);               
                  
                    
                            
                   
                    
                    
                    //edit option     
                    $this->tpl->set('message', 'You can view Raw Material Issue form');
                    $this->tpl->set('page_header', 'Report');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/field_failure_form.php'));                    
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
                   
    
                    $sqlsrr ="select * from $field_failure_tab
                    WHERE
                                 $field_failure_tab.ID= $data";  
    
         
                    $indent_data = $dbutil->getSqlData($sqlsrr);               
                    $indentsel_data  = $stmt->fetchAll();	
                    $this->tpl->set('indent_data', $indentsel_data);
                
                    //edit option     
                    $this->tpl->set('message', 'You can edit Raw Material Issue form');
                    $this->tpl->set('page_header', 'Report');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/field_failure_form.php'));                    
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
    
                    // $stmt->execute();   
                            
                            try{
                                
                                                        
                        
                                // $completed_on=!empty($form_post_data['completed_on'])?date("Y-m-d", strtotime($form_post_data['completed_on'])):'';
                                $invoice_date=!empty($form_post_data['invoice_date'])?date("Y-m-d", strtotime($form_post_data['invoice_date'])):'';
                                $complained_on=!empty($form_post_data['complained_on'])?date("Y-m-d", strtotime($form_post_data['complained_on'])):'';
                                $complained_to=!empty($form_post_data['complained_to'])?date("Y-m-d", strtotime($form_post_data['complained_to'])):'';
                            
                            $rawmaterial_id= $form_post_data['rawmaterial_id'];
                            $rawmaterial_no= $form_post_data['rawmaterial_no'];
                            $supplier_id= $form_post_data['supplier_id'];
                            $po_no= $form_post_data['po_no'];
                            $invoice_no= $form_post_data['invoice_no'];
                            $quantity= $form_post_data['quantity'];
                            $issue= $form_post_data['issue'];
                            $podate = $form_post_data['podate'];
                            $reason= $form_post_data['reason'];
                            $EmployeeID= $form_post_data['EmployeeID'];
                            $product_ID= $form_post_data['product_ID'];
                            $ProcessID= $form_post_data['ProcessID'];
    
                            $sql2 = "UPDATE $field_failure_tab set
                                                                      rawmaterial_id='$rawmaterial_id',
                                                                      rawmaterial_no='$rawmaterial_no',
                                                                      supplier_id=$supplier_id,
                                                                      po_no=$po_no,
                                                                      podate=$podate,
                                                                      invoice_date=$invoice_date
                                                                      invoice_no='$invoice_no',
                                                                      quantity='$quantity',
                                                                      issue='$issue',
                                                                      reason='$reason',
                                                                      complained_on=$complained_on,
                                                                      complained_to=$complained_to,
                                                                      remedy=$remedy,
                                                                      remark_sign=$remark_sign
                                                                      
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
    
                            // $sql3 = "DELETE FROM $rawmeterial_issue_det_tab WHERE rawmaterial_issue_id=$data";
                            // $stmt3 = $this->db->prepare($sql3);
    
                        // if($stmt3->execute()){ 
                        //     // if($stmt1->execute()){
                        // FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                               
                        //         $rawmaterial_ID =$form_post_data['rawmaterial_ID_' . $entry_count];  
                        //         $unit_ID =$form_post_data['unit_ID_' . $entry_count];					
                        //         $issued_qty =$form_post_data['issued_qty_' . $entry_count];
                        //         $acceptedqty =$form_post_data['acceptedqty_' . $entry_count];
                        //         $excess_qty =$form_post_data['excess_qty_' . $entry_count];
                               
                        //         $sql_update="Update $stock_tab SET available_qty=(available_qty-'$acceptedqty') 
                        //                      WHERE 
                        //                      $stock_tab.rawmaterial_id=$rawmaterial_ID ";               
                        //                      $update_stmt = $this->db->prepare($sql_update);
                        //                      $update_stmt->execute();
                                        
                        //         if(!empty($rawmaterial_ID) && !empty($unit_ID)){
                                    
                        //               $sql_update="Update $stock_tab SET available_qty=(available_qty+'$issued_qty') 
                        //                            WHERE 
                        //                            $stock_tab.rawmaterial_id=$rawmaterial_ID ";               
                        //                            $update_stmt = $this->db->prepare($sql_update);
                        //                            $update_stmt->execute();
    
                        //                 $vals = "'" . $data . "'," .
                        //                 "'" . $rawmaterial_ID . "'," .
                        //                 "'" . $unit_ID . "'," .
                        //                 "'" . $issued_qty . "',".
                        //                 "'" . $excess_qty . "'";
    
                        //         $sql2 = "INSERT INTO $rawmeterial_issue_det_tab
                        //                 ( 
    
                        //                     `rawmaterial_issue_id`, 
                        //                     `rawmaterial_ID`,
                        //                     `unit_ID`,
                        //                     `issued_qty`,
                        //                     `excess_qty`
                                            
                        //                 ) 
                        //         VALUES ($vals)";        
    
                        //         $stmt = $this->db->prepare($sql2);
                        //         $stmt->execute();              
                        //     }
                        //     }  
    
                            
                        // }
                                        
                            $this->tpl->set('message', 'Raw Material Issue form edited successfully!');   
                            header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/fieldfailure');
                           
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/field_failure_form.php'));
                            }
    
                    break;
                    
                case 'addsubmit':
                    
                    if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                        // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                         $complained_on=!empty($form_post_data['complained_on'])?date("Y-m-d", strtotime($form_post_data['complained_on'])):'';
                         $complained_to=!empty($form_post_data['complained_to'])?date("Y-m-d", strtotime($form_post_data['complained_to'])):'';
                         $invoice_date=!empty($form_post_data['invoice_date'])?date("Y-m-d", strtotime($form_post_data['invoice_date'])):'';
                         $supplier_id=$form_post_data['supplier_id'];
                        // "'" . $dispatch_date . "'," .
                        // $purchase_indentno=$form_post_data['purchaseindent_ID'];
                    //     echo '<pre>';
                    //    print_r($form_post_data); die;
                        
                                $val =                           
                                        "'" . $form_post_data['rawmaterial_id'] . "'," .
                                        "'" . $form_post_data['rawmaterial_no'] . "'," .
                                        "'" . $form_post_data['supplier_id'] . "'," .
                                        "'" . $form_post_data['po_no'] . "'," .     
                                        "'" . $form_post_data['podate'] . "'," .                              
                                        "'" . $invoice_date . "'," .
                                        "'" . $form_post_data['invoice_no'] . "'," .
                                        "'" . $form_post_data['quantity'] . "'," .
                                        "'" . $form_post_data['issue'] . "'," .
                                        "'" . $form_post_data['reason'] . "'," .                                  
                                        "'" . $complained_on . "'," .
                                        "'" . $complained_to . "'," .
                                        "'" . $form_post_data['remedy'] . "'," .
                                        "'" . $form_post_data['remark_sign'] . "'," .
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";
    
                           $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "field_failure`
                                            ( 
                                            
                                            `rawmaterial_id`,
                                            `rawmaterial_no`,
                                            `supplier_id`,         
                                            `po_no`,  
                                             `podate`,
                                            `invoice_date`,
                                            `invoice_no`,
                                            `quantity`,
                                            `issue`,         
                                            `reason`,  
                                            `complained_on`,
                                            `complained_to`,
                                            `remedy`,
                                            `remark_sign`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                    VALUES ( $val )";                       
                                    $stmt = $this->db->prepare($sql);      
                                   // $stmt->execute();
                                    if($stmt->execute()){
                                       $sql_update="UPDATE ycias_supplier_report  SET trans_date=DATE_FORMAT(NOW(), '%Y-%m-%d'),field_failure = 0 where ycias_supplier_report.supplier_id = $supplier_id";
                                       $update_stmt = $this->db->prepare($sql_update);
                                       $update_stmt->execute();  
                                 }
                    //     if ($stmt->execute()) { 
                        
                    //     $lastInsertedID = $this->db->lastInsertId();  
                    //   FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
    
                    //             //  $invoice_date=!empty($form_post_data['completed_on_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['completed_on_'. $entry_count])):'';
                    //             $rawmaterial_ID =$form_post_data['rawmaterial_ID_' . $entry_count]; 
                              
                    //             $unit_ID =$form_post_data['unit_ID_' . $entry_count];
                               
                    //             $issued_qty =$form_post_data['issued_qty_' . $entry_count];
                    //             $excess_qty =$form_post_data['excess_qty_' . $entry_count];	
                    //             $hai =$form_post_data['RMName_' . $entry_count];
                    //             $unit_name =$form_post_data['UnitName_' . $entry_count];
                               
                                
                                
                            
                                
                               
                                
                    //             $vals = "'" . $lastInsertedID . "'," .
                    //                      "'" . $rawmaterial_ID . "'," .
                                      
                    //                     "'" . $unit_ID . "'," .
                                        
                    //                     "'" . $issued_qty . "'," .
                    //                     "'" . $excess_qty . "'";
                    //                     // "'" . $unit . "'," .
                    //                     // "'" . $receivedqt . "'," .
                    //                     // "'" . $batch . "'," .
                    //                     // "'" . $costunit . "'," .
                    //                     // "'" . $total . "'," .
                    //                     // "'" . $supplier . "'," .
                    //                     // "'" . $invoice_date . "'" ;   
                    //       $sql2 = "INSERT INTO $rawmeterial_issue_det_tab
                    //                     ( 
                    //                         `rawmaterial_issue_id`,
                    //                         `rawmaterial_ID`, 
                                          
                    //                         `unit_ID`,
                                            
                    //                         `issued_qty`,
                    //                         `excess_qty`
                                           
                    //                     ) 
                    //             VALUES ($vals)";
                    //             $stmt = $this->db->prepare($sql2);
                    //         //$stmt->execute();    
                            
                    //          if($stmt->execute()){
                    //             $sql_update="UPDATE $stock_table
                    //                              LEFT JOIN $rawmeterial_issue_det_tab
                    //                              ON $rawmeterial_issue_det_tab.rawmaterial_ID=$stock_table.rawmaterial_id
                    //                              SET $stock_table.available_qty=($stock_table.available_qty-$issued_qty)
                    //                              WHERE $stock_table.rawmaterial_id=$rawmeterial_issue_det_tab.rawmaterial_ID";          
                                             
                    //                          $update_stmt = $this->db->prepare($sql_update);
                    //                          $update_stmt->execute();
                                             
                    //              $sql_update = "INSERT INTO ycias_stock_trans(TransactionType, raw_material_ID, raw_material_name, stockout, unit_name, entity_ID, users_ID)
                    //                          VALUES ('Rawmaterial issue', $rawmaterial_ID, '$hai', $issued_qty, '$unit_name', '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                             
                    //                          $update_stmt = $this->db->prepare($sql_update);
                    //                          $update_stmt->execute();
                    //           }
                           
                               
                    //         }  
                            
                            
                          
                    //     }
                                                           
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/fieldfailure');
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/field_failure_form.php'));
                     }
                     
                    break;
    
                case 'add':
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('page_header', 'Report');
                    $MaterialNo=$dbutil->keyGeneration('materialinward','MNO','','MaterialNo');
                    $this->tpl->set('MaterialNo', $MaterialNo);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/field_failure_form.php'));
                    break;
    
                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$field_failure_tab.ID",
               
                "$rawmaterial_table.RMName",
                "$supplier_tab.SupplierName"
               
                
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
             $whereString ="ORDER BY $field_failure_tab.ID DESC";
           }
           $productionorder_tab = $this->crg->get('table_prefix') . 'productionorder';
            $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
           
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $field_failure_tab, $rawmaterial_table,  $supplier_tab
                    "
                    . " WHERE "
                    . " $field_failure_tab.rawmaterial_id = $rawmaterial_table.ID "
                    . " AND "
                    . " $field_failure_tab.supplier_id = $supplier_tab.ID "
                    . " AND "
                    . " $field_failure_tab.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Rawmaterila','Supplier'));
            
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
                     
                    include_once $this->tpl->path . '/factory/form/field_failure_crud_form.php';
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
    
    /////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////  QDC
    
function qdc(){
     
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
            $field_failure_tab = $this->crg->get('table_prefix') . 'field_failure';
            $qdc_tab = $this->crg->get('table_prefix') . 'qdc';
            
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
           
             $sql = "SELECT ID,PdnOrderNo FROM $productionorder_tab";  
             $stmt = $this->db->prepare($sql);            
             $stmt->execute();
             $productionorder_tab_data  = $stmt->fetchAll();	
             $this->tpl->set('productionorder_tab_data', $productionorder_tab_data);
    
              //work Order table data
           
              $sql = "SELECT ID,WorkOrderNo FROM $workorder_tab";  
              $stmt = $this->db->prepare($sql);            
              $stmt->execute();
              $workorder_tab_data  = $stmt->fetchAll();	
              $this->tpl->set('workorder_tab_data', $workorder_tab_data);
    
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
     
            $this->tpl->set('page_title', 'QDC');	          
            $this->tpl->set('page_header', 'Report');
            
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
                    
                    // $sql_update="UPDATE $stock_table
                    // LEFT JOIN $rawmeterial_issue_det_tab
                    // ON $rawmeterial_issue_det_tab.rawmaterial_ID=$stock_table.rawmaterial_id
                    // SET $stock_table.available_qty=($stock_table.available_qty+$rawmeterial_issue_det_tab.issued_qty)
                    // WHERE $stock_table.rawmaterial_id=$rawmeterial_issue_det_tab.rawmaterial_ID AND $rawmeterial_issue_det_tab.rawmaterial_ID=$data";          
                    // $update_stmt = $this->db->prepare($sql_update);
                    // $update_stmt->execute();
                     
                    $sqldetdelete="DELETE $qdc_tab FROM $qdc_tab
                                   WHERE $qdc_tab.ID=$data";  
                                   
                    $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'QDC deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/qdc');
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
                  
    
                    $sqlsrr ="select * from $qdc_tab
                               WHERE
                                            $qdc_tab.ID= $data";  
                    
                    $indent_data = $dbutil->getSqlData($sqlsrr);               
                  
                    
                            
                   
                    
                    
                    //edit option     
                    $this->tpl->set('message', 'You can view QDC form');
                    $this->tpl->set('page_header', 'Report');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/qdc_form.php'));                    
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
                   
    
                    $sqlsrr ="select * from $qdc_tab
                    WHERE
                                 $qdc_tab.ID= $data";  
    
         
                    $indent_data = $dbutil->getSqlData($sqlsrr);               
                    $indentsel_data  = $stmt->fetchAll();	
                    $this->tpl->set('indent_data', $indentsel_data);
                
                    //edit option     
                    $this->tpl->set('message', 'You can edit QDC form');
                    $this->tpl->set('page_header', 'Report');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/qdc_form.php'));                    
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
    
                    // $stmt->execute();   
                            
                            try{
                                
                                                        
                        
                                // $completed_on=!empty($form_post_data['completed_on'])?date("Y-m-d", strtotime($form_post_data['completed_on'])):'';
                                $invoice_date=!empty($form_post_data['invoice_date'])?date("Y-m-d", strtotime($form_post_data['invoice_date'])):'';
                                $complained_on=!empty($form_post_data['complained_on'])?date("Y-m-d", strtotime($form_post_data['complained_on'])):'';
                                $complained_to=!empty($form_post_data['complained_to'])?date("Y-m-d", strtotime($form_post_data['complained_to'])):'';
                            
                            $rawmaterial_id= $form_post_data['rawmaterial_id'];
                            $rawmaterial_no= $form_post_data['rawmaterial_no'];
                            $supplier_id= $form_post_data['supplier_id'];
                            $po_no= $form_post_data['po_no'];
                            $invoice_no= $form_post_data['invoice_no'];
                            $quantity= $form_post_data['quantity'];
                            $issue= $form_post_data['issue'];
                            $podate = $form_post_data['podate'];
                            $reason= $form_post_data['reason'];
                            $EmployeeID= $form_post_data['EmployeeID'];
                            $product_ID= $form_post_data['product_ID'];
                            $ProcessID= $form_post_data['ProcessID'];
    
                            $sql2 = "UPDATE $qdc_tab set
                                                                      rawmaterial_id='$rawmaterial_id',
                                                                      rawmaterial_no='$rawmaterial_no',
                                                                      supplier_id=$supplier_id,
                                                                      po_no=$po_no,
                                                                      podate=$podate,
                                                                      invoice_date=$invoice_date
                                                                      invoice_no='$invoice_no',
                                                                      quantity='$quantity',
                                                                      issue='$issue',
                                                                      reason='$reason',
                                                                      complained_on=$complained_on,
                                                                      complained_to=$complained_to,
                                                                      remedy=$remedy,
                                                                      remark_sign=$remark_sign
                                                                      
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
    
                            // $sql3 = "DELETE FROM $rawmeterial_issue_det_tab WHERE rawmaterial_issue_id=$data";
                            // $stmt3 = $this->db->prepare($sql3);
    
                        // if($stmt3->execute()){ 
                        //     // if($stmt1->execute()){
                        // FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                               
                        //         $rawmaterial_ID =$form_post_data['rawmaterial_ID_' . $entry_count];  
                        //         $unit_ID =$form_post_data['unit_ID_' . $entry_count];					
                        //         $issued_qty =$form_post_data['issued_qty_' . $entry_count];
                        //         $acceptedqty =$form_post_data['acceptedqty_' . $entry_count];
                        //         $excess_qty =$form_post_data['excess_qty_' . $entry_count];
                               
                        //         $sql_update="Update $stock_tab SET available_qty=(available_qty-'$acceptedqty') 
                        //                      WHERE 
                        //                      $stock_tab.rawmaterial_id=$rawmaterial_ID ";               
                        //                      $update_stmt = $this->db->prepare($sql_update);
                        //                      $update_stmt->execute();
                                        
                        //         if(!empty($rawmaterial_ID) && !empty($unit_ID)){
                                    
                        //               $sql_update="Update $stock_tab SET available_qty=(available_qty+'$issued_qty') 
                        //                            WHERE 
                        //                            $stock_tab.rawmaterial_id=$rawmaterial_ID ";               
                        //                            $update_stmt = $this->db->prepare($sql_update);
                        //                            $update_stmt->execute();
    
                        //                 $vals = "'" . $data . "'," .
                        //                 "'" . $rawmaterial_ID . "'," .
                        //                 "'" . $unit_ID . "'," .
                        //                 "'" . $issued_qty . "',".
                        //                 "'" . $excess_qty . "'";
    
                        //         $sql2 = "INSERT INTO $rawmeterial_issue_det_tab
                        //                 ( 
    
                        //                     `rawmaterial_issue_id`, 
                        //                     `rawmaterial_ID`,
                        //                     `unit_ID`,
                        //                     `issued_qty`,
                        //                     `excess_qty`
                                            
                        //                 ) 
                        //         VALUES ($vals)";        
    
                        //         $stmt = $this->db->prepare($sql2);
                        //         $stmt->execute();              
                        //     }
                        //     }  
    
                            
                        // }
                                        
                            $this->tpl->set('message', 'QDC form edited successfully!');   
                            header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/qdc');
                           
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/qdc_form.php'));
                            }
    
                    break;
                    
                case 'addsubmit':
                    
                    if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                        // $dispatch_date=!empty($form_post_data['invoice_date_'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
                         $complained_on=!empty($form_post_data['complained_on'])?date("Y-m-d", strtotime($form_post_data['complained_on'])):'';
                         $complained_to=!empty($form_post_data['complained_to'])?date("Y-m-d", strtotime($form_post_data['complained_to'])):'';
                         $invoice_date=!empty($form_post_data['invoice_date'])?date("Y-m-d", strtotime($form_post_data['invoice_date'])):'';
                         $supplier_id=$form_post_data['supplier_id'];
                        // "'" . $dispatch_date . "'," .
                        // $purchase_indentno=$form_post_data['purchaseindent_ID'];
                    //     echo '<pre>';
                    //    print_r($form_post_data); die;
                        
                                $val =                           
                                        "'" . $form_post_data['rawmaterial_id'] . "'," .
                                        "'" . $form_post_data['rawmaterial_no'] . "'," .
                                        "'" . $form_post_data['supplier_id'] . "'," .
                                        "'" . $form_post_data['po_no'] . "'," .     
                                        "'" . $form_post_data['podate'] . "'," .                              
                                        "'" . $invoice_date . "'," .
                                        "'" . $form_post_data['invoice_no'] . "'," .
                                        "'" . $form_post_data['quantity'] . "'," .
                                        "'" . $form_post_data['issue'] . "'," .
                                        "'" . $form_post_data['reason'] . "'," .                                  
                                        "'" . $complained_on . "'," .
                                        "'" . $complained_to . "'," .
                                        "'" . $form_post_data['remedy'] . "'," .
                                        "'" . $form_post_data['remark_sign'] . "'," .
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";
    
                           $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "qdc`
                                            ( 
                                            
                                            `rawmaterial_id`,
                                            `rawmaterial_no`,
                                            `supplier_id`,         
                                            `po_no`,  
                                             `podate`,
                                            `invoice_date`,
                                            `invoice_no`,
                                            `quantity`,
                                            `issue`,         
                                            `reason`,  
                                            `complained_on`,
                                            `complained_to`,
                                            `remedy`,
                                            `remark_sign`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                    VALUES ( $val )";                       
                                    $stmt = $this->db->prepare($sql);      
                                   // $stmt->execute();
                                    if($stmt->execute()){
                                       $sql_update="UPDATE ycias_supplier_report  SET trans_date=DATE_FORMAT(NOW(), '%Y-%m-%d'),Qdc = 0 where ycias_supplier_report.supplier_id = $supplier_id";
                                       $update_stmt = $this->db->prepare($sql_update);
                                       $update_stmt->execute();  
                                 }
                    //     if ($stmt->execute()) { 
                        
                    //     $lastInsertedID = $this->db->lastInsertId();  
                    //   FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
    
                    //             //  $invoice_date=!empty($form_post_data['completed_on_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['completed_on_'. $entry_count])):'';
                    //             $rawmaterial_ID =$form_post_data['rawmaterial_ID_' . $entry_count]; 
                              
                    //             $unit_ID =$form_post_data['unit_ID_' . $entry_count];
                               
                    //             $issued_qty =$form_post_data['issued_qty_' . $entry_count];
                    //             $excess_qty =$form_post_data['excess_qty_' . $entry_count];	
                    //             $hai =$form_post_data['RMName_' . $entry_count];
                    //             $unit_name =$form_post_data['UnitName_' . $entry_count];
                               
                                
                                
                            
                                
                               
                                
                    //             $vals = "'" . $lastInsertedID . "'," .
                    //                      "'" . $rawmaterial_ID . "'," .
                                      
                    //                     "'" . $unit_ID . "'," .
                                        
                    //                     "'" . $issued_qty . "'," .
                    //                     "'" . $excess_qty . "'";
                    //                     // "'" . $unit . "'," .
                    //                     // "'" . $receivedqt . "'," .
                    //                     // "'" . $batch . "'," .
                    //                     // "'" . $costunit . "'," .
                    //                     // "'" . $total . "'," .
                    //                     // "'" . $supplier . "'," .
                    //                     // "'" . $invoice_date . "'" ;   
                    //       $sql2 = "INSERT INTO $rawmeterial_issue_det_tab
                    //                     ( 
                    //                         `rawmaterial_issue_id`,
                    //                         `rawmaterial_ID`, 
                                          
                    //                         `unit_ID`,
                                            
                    //                         `issued_qty`,
                    //                         `excess_qty`
                                           
                    //                     ) 
                    //             VALUES ($vals)";
                    //             $stmt = $this->db->prepare($sql2);
                    //         //$stmt->execute();    
                            
                    //          if($stmt->execute()){
                    //             $sql_update="UPDATE $stock_table
                    //                              LEFT JOIN $rawmeterial_issue_det_tab
                    //                              ON $rawmeterial_issue_det_tab.rawmaterial_ID=$stock_table.rawmaterial_id
                    //                              SET $stock_table.available_qty=($stock_table.available_qty-$issued_qty)
                    //                              WHERE $stock_table.rawmaterial_id=$rawmeterial_issue_det_tab.rawmaterial_ID";          
                                             
                    //                          $update_stmt = $this->db->prepare($sql_update);
                    //                          $update_stmt->execute();
                                             
                    //              $sql_update = "INSERT INTO ycias_stock_trans(TransactionType, raw_material_ID, raw_material_name, stockout, unit_name, entity_ID, users_ID)
                    //                          VALUES ('Rawmaterial issue', $rawmaterial_ID, '$hai', $issued_qty, '$unit_name', '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                             
                    //                          $update_stmt = $this->db->prepare($sql_update);
                    //                          $update_stmt->execute();
                    //           }
                           
                               
                    //         }  
                            
                            
                          
                    //     }
                                                           
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/report/cst/qdc');
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/qdc_form.php'));
                     }
                     
                    break;
    
                case 'add':
                    $this->tpl->set('mode', 'add');
                    $this->tpl->set('page_header', 'Report');
                    $MaterialNo=$dbutil->keyGeneration('materialinward','MNO','','MaterialNo');
                    $this->tpl->set('MaterialNo', $MaterialNo);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/qdc_form.php'));
                    break;
    
                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$qdc_tab.ID",
               
                "$rawmaterial_table.RMName",
                "$supplier_tab.SupplierName"
               
                
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
             $whereString ="ORDER BY $qdc_tab.ID DESC";
           }
           $productionorder_tab = $this->crg->get('table_prefix') . 'productionorder';
            $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
           
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $qdc_tab, $rawmaterial_table,  $supplier_tab
                    "
                    . " WHERE "
                    . " $qdc_tab.rawmaterial_id = $rawmaterial_table.ID "
                    . " AND "
                    . " $qdc_tab.supplier_id = $supplier_tab.ID "
                    . " AND "
                    . " $qdc_tab.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Rawmaterila','Supplier'));
            
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
                     
                    include_once $this->tpl->path . '/factory/form/qdc_crud_form.php';
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
    
    public function supplierReport() {
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////access condition applied///////////////////////////
//////////////////////////////////////////////////////////////////////////////// 
      
       include_once 'util/DBUTIL.php';
       $dbutil = new DBUTIL($this->crg);
         
         $entityID = $this->ses->get('user')['entity_ID'];
         $userID = $this->ses->get('user')['ID'];
        
       $supplierreport_tab = $this->crg->get('table_prefix') . 'supplier_report';
       $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
              
      
        ////////////////////start//////////////////////////////////////////////
                
       //bUILD SQL 
        $whereString = '';
        
        $colArr = array(
            "$supplierreport_tab.ID", 
            "$supplierreport_tab.trans_date", 
            "$supplier_tab.SupplierName",
            "$supplierreport_tab.schedule",
            "$supplierreport_tab.achieved", 
            "$supplierreport_tab.percent",
            "$supplierreport_tab.rating",
            "$supplierreport_tab.received",
            "$supplierreport_tab.rejected", 
            "$supplierreport_tab.percent1",
            "$supplierreport_tab.rating1",
            "$supplierreport_tab.premium_freight",
            "$supplierreport_tab.field_failure",
            "$supplierreport_tab.Qdc",
            "$supplierreport_tab.Qms",
            "(rating+rating1+premium_freight+field_failure+Qdc+Qms)"
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
        
        $orderBy ="ORDER BY $supplierreport_tab.ID DESC";
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
    . " FROM $supplierreport_tab, $supplier_tab WHERE"
    . " $supplierreport_tab.supplier_id = $supplier_tab.ID"
    . " AND "
    . " $supplierreport_tab.trans_date BETWEEN '$start_date' AND '$end_date'"
    . " AND "
    . " $supplierreport_tab.entity_ID=$entityID "
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
        $this->tpl->set('table_columns_label_arr', array('ID','Date','Supplier Name','Material inward Schedule qty','Material inward Achieved qty','Material inward Percentage','Material inward Rating','QC Received qty','QC Rejected qty','QC Percentage','QC Rating','Premium Freight Register','Field Failure','Qdc','Qms','Total Percent'));
        
        /*,;;
         * selectColArr for filter form
         */
        
        $this->tpl->set('selectColArr',$colArr);
                    
        /*
         * set pagination template
         */
        $this->crg->set('paginationListTemplate','factory/template/Search_Download_button.php');
        
        $this->tpl->set('widget_2', $this->tpl->fetch('factory/template/filterForms/stocktransaction.php')); 
               
        //////////////////////close//////////////////////////////////////  
      
         include_once $this->tpl->path .'/factory/form/supplier_report_crud_form.php';
        
        
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
    
    public function reworkReport() {
                                            if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                        //////////////////////////////////////////////////////////////////////////////////
                                        //////////////////////////////access condition applied///////////////////////////
                                        //////////////////////////////////////////////////////////////////////////////// 
                                              
                                               include_once 'util/DBUTIL.php';
                                               $dbutil = new DBUTIL($this->crg);
                                                 
                                                 $entityID = $this->ses->get('user')['entity_ID'];
                                                 $userID = $this->ses->get('user')['ID'];
                                                
                                               $product_tab = $this->crg->get('table_prefix') . 'product';
                                               $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
                                               $workorder_detail_tab = $this->crg->get('table_prefix') . 'workorder_detail';
                                               $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
                                               $rawmaterial_tab = $this->crg->get('table_prefix') . 'rawmaterial';
                                               $purchaseorder_tab = $this->crg->get('table_prefix') . 'purchaseorder';
                                               $purchaseorderdetail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
                                               $productionorder_table = $this->crg->get('table_prefix') . 'productionorder';

                                               $product_sql = "SELECT ID,ProductName FROM $product_tab";
                                                               $stmt = $this->db->prepare($product_sql);            
                                                               $stmt->execute();
                                                               $product_data  = $stmt->fetchAll();	
                                                               $this->tpl->set('product_data', $product_data);

                                               $employee_sql = "SELECT ID,SupplierName FROM $supplier_tab WHERE SupplierType='Sub contractor'";
                                               $stmt = $this->db->prepare($employee_sql);            
                                               $stmt->execute();
                                               $subcontractor_data  = $stmt->fetchAll();	
                                               $this->tpl->set('subcontractor_data', $subcontractor_data);
                                                      
                                              
                                                ////////////////////start//////////////////////////////////////////////
                                                        
                                               //bUILD SQL 
                                                $whereString = '';
                                                
                                                $colArr = array(
                                                    "$workorder_tab.ID", 
                                                    "$workorder_tab.WorkOrderNo",
                                                    "$supplier_tab.SupplierName",
                                                    "$product_tab.ProductName",
                                                    "$rawmaterial_tab.RMName",
                                                    "$workorder_detail_tab.Quantity2",
                                                    "SUM($purchaseorderdetail_tab.Rate) as TotalRate"
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

                                                if(!empty($_POST['product_ID'])){
                                                      $product_ID=($_POST['product_ID']);
                                                   
                                                }


                                                if(!empty($_POST['Subcontractor_ID'])){
                                                      $Subcontractor_ID=($_POST['Subcontractor_ID']);
                                                   
                                                }
                                                
                                            $orderBy ="ORDER BY $workorder_tab.WorkOrderNo ASC";
                                            
                                  $sql = "SELECT "
                                            . implode(',',$colArr)
                                            . " FROM $workorder_tab,$supplier_tab,$product_tab,$workorder_detail_tab,$rawmaterial_tab,$purchaseorder_tab,$purchaseorderdetail_tab WHERE "
                                            . " $supplier_tab.ID=$workorder_tab.Subcontractor_ID AND"
                                            . " $workorder_tab.product_ID = $product_tab.ID AND"
                                            . " $workorder_detail_tab.Workorder_ID = $workorder_tab.ID AND"
                                            . " $workorder_detail_tab.rawmaterial_ID = $rawmaterial_tab.ID AND"
                                            . " $purchaseorderdetail_tab.purchaseorder_ID = $purchaseorder_tab.ID AND"
                                            . " $purchaseorderdetail_tab.rawmaterial_ID = $workorder_detail_tab.rawmaterial_ID AND"
                                            . " ycias_workorder.product_ID = $product_ID AND ycias_workorder.Subcontractor_ID = $Subcontractor_ID AND"
                                            . " $workorder_tab.Rework = 1 AND"
                                            . " $workorder_tab.entity_ID=$entityID "
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
                                                $this->tpl->set('table_columns_label_arr', array('ID','WorkOrderNo','Subcontractor Name','Product Name','Rawmaterial Name','Rework Qty','Total Price'));
                                                
                                                /*,;;
                                                 * selectColArr for filter form
                                                 */
                                                
                                                $this->tpl->set('selectColArr',$colArr);
                                                            
                                                /*
                                                 * set pagination template
                                                 */
                                                $this->crg->set('paginationListTemplate','factory/template/Search_Download_button.php');

                                                $this->tpl->set('widget_2', $this->tpl->fetch('factory/template/filterForms/stocktransaction1.php')); 
                                                       
                                                //////////////////////close//////////////////////////////////////  
                                              
                                                 include_once $this->tpl->path .'/factory/form/rework_report_crud_form.php';
                                                
                                                
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
                                        
                      public function rejectionReport() {
                                            if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                        //////////////////////////////////////////////////////////////////////////////////
                                        //////////////////////////////access condition applied///////////////////////////
                                        //////////////////////////////////////////////////////////////////////////////// 
                                              
                                               include_once 'util/DBUTIL.php';
                                               $dbutil = new DBUTIL($this->crg);
                                                 
                                                 $entityID = $this->ses->get('user')['entity_ID'];
                                                 $userID = $this->ses->get('user')['ID'];
                                                
                                               $qualitycontrol_tab = $this->crg->get('table_prefix') . 'qualitycontrol';
                                               $product_tab = $this->crg->get('table_prefix') . 'product';
                                               $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
                                               $product_tab = $this->crg->get('table_prefix') . 'product';
                                               $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
                                               $workorder_detail_tab = $this->crg->get('table_prefix') . 'workorder_detail';
                                               $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
                                               $rawmaterial_tab = $this->crg->get('table_prefix') . 'rawmaterial';
                                               $purchaseorder_tab = $this->crg->get('table_prefix') . 'purchaseorder';
                                               $purchaseorderdetail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
                                                      
                                              
                                                $product_sql = "SELECT ID,ProductName FROM $product_tab";
                                                               $stmt = $this->db->prepare($product_sql);            
                                                               $stmt->execute();
                                                               $product_data  = $stmt->fetchAll();	
                                                               $this->tpl->set('product_data', $product_data);
                                                               
                                                $emp_sql = "SELECT ID,EmpName FROM $qualitycontrol_tab GROUP BY $qualitycontrol_tab.EmpName";
                                                               $stmt = $this->db->prepare($emp_sql);            
                                                               $stmt->execute();
                                                               $emp_data  = $stmt->fetchAll();	
                                                               $this->tpl->set('emp_data', $emp_data);
                                                ////////////////////start//////////////////////////////////////////////
                                                        
                                               //bUILD SQL 
                                                $whereString = '';
                                                
                                                $colArr = array(
                                                    "$qualitycontrol_tab.ID", 
                                                    "$qualitycontrol_tab.CompletedDate",
                                                    "$workorder_tab.WorkOrderNo",
                                                    "$qualitycontrol_tab.EmpName",
                                                    "$product_tab.ProductName",
                                                    "$rawmaterial_tab.RMName",
                                                    "$workorder_detail_tab.Quantity2",
                                                    "SUM($purchaseorderdetail_tab.Rate) as TotalRate"
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
                                                
                                                 if(!empty($_POST['product_ID'])){
                                                      $product_ID=($_POST['product_ID']);
                                                   
                                                }


                                                if(!empty($_POST['Subcontractor_ID'])){
                                                      $Subcontractor_ID=($_POST['Subcontractor_ID']);
                                                   
                                                }
                                                
                                                $orderBy ="ORDER BY $product_tab.ProductName ASC";
                                            
                                    $sql = "SELECT "
                                            . implode(',',$colArr)
                                            . " FROM $qualitycontrol_tab,$product_tab,$workorder_tab,$workorder_detail_tab,$rawmaterial_tab,$purchaseorder_tab,$purchaseorderdetail_tab WHERE"
                                            . " $workorder_tab.ID=$qualitycontrol_tab.WorkorderID AND"
                                            . " $workorder_detail_tab.Workorder_ID = $workorder_tab.ID AND"
                                            . " $workorder_detail_tab.rawmaterial_ID = $rawmaterial_tab.ID AND"
                                            . " $purchaseorderdetail_tab.purchaseorder_ID = $purchaseorder_tab.ID AND"
                                            . " $purchaseorderdetail_tab.rawmaterial_ID = $workorder_detail_tab.rawmaterial_ID AND"
                                            . " $qualitycontrol_tab.product_ID = $product_ID AND"
                                            . " $qualitycontrol_tab.ID = $Subcontractor_ID AND"
                                            . " $qualitycontrol_tab.product_ID = $product_tab.ID"
                                            . " AND "
                                            . " $qualitycontrol_tab.Qcverified = 'Rejected'"
                                            . " AND "
                                            . " $qualitycontrol_tab.entity_ID=$entityID "
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
                                                $this->tpl->set('table_columns_label_arr', array('ID','Workorder Date','WorkOrderNo','Employee/Subcontractor Name','Product Name','Rawmaterial Name','Rejected Qty','Total Price'));
                                                
                                                /*,;;
                                                 * selectColArr for filter form
                                                 */
                                                
                                                $this->tpl->set('selectColArr',$colArr);
                                                            
                                                /*
                                                 * set pagination template
                                                 */
                                                $this->crg->set('paginationListTemplate','factory/template/Search_Download_button.php');
                                                
                                                $this->tpl->set('widget_2', $this->tpl->fetch('factory/template/filterForms/stocktransaction_rejection_report.php')); 
                                                       
                                                //////////////////////close//////////////////////////////////////  
                                              
                                                 include_once $this->tpl->path .'/factory/form/rejection_report_crud_form.php';
                                                
                                                
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
                                        
                                        public function rawmaterialVolume() {
                                            if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                        //////////////////////////////////////////////////////////////////////////////////
                                        //////////////////////////////access condition applied///////////////////////////
                                        //////////////////////////////////////////////////////////////////////////////// 
                                              
                                               include_once 'util/DBUTIL.php';
                                               $dbutil = new DBUTIL($this->crg);
                                                 
                                                 $entityID = $this->ses->get('user')['entity_ID'];
                                                 $userID = $this->ses->get('user')['ID'];
                                                
                                               $purchaseorder_tab = $this->crg->get('table_prefix') . 'purchaseorder';
                                               $purchaseorderdetail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
                                               $rawmaterial_tab = $this->crg->get('table_prefix') . 'rawmaterial';
                                               $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
                                
                                              
                                                ////////////////////start//////////////////////////////////////////////
                                                
                                               $purchase_sql = "SELECT ID,PurchaseOrderNo FROM $purchaseorder_tab";
                                               $stmt = $this->db->prepare($purchase_sql);            
                                               $stmt->execute();
                                               $purchase_data  = $stmt->fetchAll();	
                                               $this->tpl->set('purchase_data', $purchase_data);
                                            
                                               $employee_sql = "SELECT ID,SupplierName FROM $supplier_tab WHERE SupplierType='Sub contractor'";
                                               $stmt = $this->db->prepare($employee_sql);            
                                               $stmt->execute();
                                               $subcontractor_data  = $stmt->fetchAll();	
                                               $this->tpl->set('subcontractor_data', $subcontractor_data);
                                                        
                                               //bUILD SQL 
                                                $whereString = '';
                                                
                                                $colArr = array(
                                                    "@row_number:=@row_number + 1 AS Rownum",
                                                    "PurchaseOrderNo", 
                                                    "SupplierName",
                                                    "RMName",
                                                    "batch_no",
                                                    "po_qty",
                                                    "total"
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
                                                
                                                 if(!empty($_POST['PurchaseOrderID'])){
                                                                   $PurchaseOrderID=($_POST['PurchaseOrderID']);
                                                   
                                                            }


                                                 if(!empty($_POST['Subcontractor_ID'])){
                                                                   $Subcontractor_ID=($_POST['Subcontractor_ID']);
                                                   
                                                            }
                                                
                                            //$orderBy ="ORDER BY $rawmaterial_tab.RMName ASC";
                                            
                                         $sql = "SELECT "
                                            . implode(',',$colArr)
                                            . " FROM (SELECT
                                                           ycias_purchaseorder.PurchaseOrderNo,
                                                           ycias_supplier.SupplierName,
                                                            ycias_material_inward_detail.batch_no,
                                                           ycias_material_inward_detail.po_qty,
                                                           ycias_material_inward_detail.total,
                                                           ycias_rawmaterial.RMName
                                                      FROM
                                                           ycias_material_inward,
                                                           ycias_purchaseorder,
                                                           ycias_supplier,
                                                           ycias_material_inward_detail,
                                                           ycias_rawmaterial
                                                      WHERE
                                                           ycias_material_inward.PurchaseNO = ycias_purchaseorder.ID 
                                                           AND ycias_material_inward.supplier_ID = ycias_supplier.ID
                                                           AND ycias_material_inward_detail.material_inward_id = ycias_material_inward.ID 
                                                           AND ycias_material_inward_detail.item_id = ycias_rawmaterial.ID 
                                                           AND ycias_material_inward.PurchaseNO = $PurchaseOrderID 
                                                           AND ycias_material_inward.supplier_ID = $Subcontractor_ID 
                                                           AND ycias_material_inward.entity_ID = $entityID)subquery, (SELECT @row_number:=0) AS t ";  
                                              
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
                                                $this->tpl->set('table_columns_label_arr', array('ID','Purchase Order No','Supplier Name','Rawmaterial Name','Batch No','Total Volume','Total Cost'));
                                                
                                                /*,;;
                                                 * selectColArr for filter form
                                                 */
                                                
                                                $this->tpl->set('selectColArr',$colArr);
                                                            
                                                /*
                                                 * set pagination template
                                                 */
                                                $this->crg->set('paginationListTemplate','factory/template/Search_Download_button.php');
                                                
                                                 $this->tpl->set('widget_2', $this->tpl->fetch('factory/template/filterForms/stocktransaction_rawmaterial.php')); 
                                                       
                                                //////////////////////close//////////////////////////////////////  
                                              
                                                 include_once $this->tpl->path .'/factory/form/rawmaterial_1_report_crud_form.php';
                                                
                                                
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
                                        
                            public function rawmaterialVolumePeriod() {
                                            if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                        //////////////////////////////////////////////////////////////////////////////////
                                        //////////////////////////////access condition applied///////////////////////////
                                        //////////////////////////////////////////////////////////////////////////////// 
                                              
                                               include_once 'util/DBUTIL.php';
                                               $dbutil = new DBUTIL($this->crg);
                                                 
                                                 $entityID = $this->ses->get('user')['entity_ID'];
                                                 $userID = $this->ses->get('user')['ID'];
                                                
                                            $purchaseorder_tab = $this->crg->get('table_prefix') . 'purchaseorder';
                                            $purchaseorderdetail_tab = $this->crg->get('table_prefix') . 'purchaseorderdetail';
                                            $rawmaterial_tab = $this->crg->get('table_prefix') . 'rawmaterial';
                                            $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
                                                      
                                                ////////////////////start//////////////////////////////////////////////
                                                
                                            $purchase_sql = "SELECT ID,PurchaseOrderNo FROM $purchaseorder_tab";
                                            $stmt = $this->db->prepare($purchase_sql);            
                                            $stmt->execute();
                                            $purchase_data  = $stmt->fetchAll();	
                                            $this->tpl->set('purchase_data', $purchase_data);
                                            
                                            $employee_sql = "SELECT ID,SupplierName FROM $supplier_tab WHERE SupplierType='Sub contractor'";
                                            $stmt = $this->db->prepare($employee_sql);            
                                            $stmt->execute();
                                            $subcontractor_data  = $stmt->fetchAll();	
                                            $this->tpl->set('subcontractor_data', $subcontractor_data);
                                                        
                                               //bUILD SQL 
                                                $whereString = '';
                                                
                                                $colArr = array(
                                                    "@row_number:=@row_number + 1 AS Rownum",
                                                    "trans_date", 
                                                    "PurchaseOrderNo", 
                                                    "SupplierName",
                                                    "RMName",
                                                    "batch_no",
                                                    "po_qty",
                                                    "total"
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
                                                
                                              //  $orderBy ="ORDER BY $rawmaterial_tab.RMName ASC";
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
                                                            
                                                            if(!empty($_POST['PurchaseOrderID'])){
                                                                   $PurchaseOrderID=($_POST['PurchaseOrderID']);
                                                   
                                                            }


                                                            if(!empty($_POST['Subcontractor_ID'])){
                                                                   $Subcontractor_ID=($_POST['Subcontractor_ID']);
                                                   
                                                            }
                                             
                                         $sql = "SELECT "
                                            . implode(',',$colArr)
                                            . " FROM (SELECT
                                                           ycias_material_inward.trans_date,
                                                           ycias_purchaseorder.PurchaseOrderNo,
                                                           ycias_supplier.SupplierName,
                                                            ycias_material_inward_detail.batch_no,
                                                           ycias_material_inward_detail.po_qty,
                                                           ycias_material_inward_detail.total,
                                                           ycias_rawmaterial.RMName
                                                      FROM
                                                           ycias_material_inward,
                                                           ycias_purchaseorder,
                                                           ycias_supplier,
                                                           ycias_material_inward_detail,
                                                           ycias_rawmaterial
                                                      WHERE
                                                           ycias_material_inward.trans_date BETWEEN '$start_date' AND '$end_date'
                                                           AND ycias_material_inward.PurchaseNO = ycias_purchaseorder.ID 
                                                           AND ycias_material_inward.supplier_ID = ycias_supplier.ID
                                                           AND ycias_material_inward_detail.material_inward_id = ycias_material_inward.ID 
                                                           AND ycias_material_inward.PurchaseNO = $PurchaseOrderID 
                                                           AND ycias_material_inward.supplier_ID = $Subcontractor_ID 
                                                           AND ycias_material_inward_detail.item_id = ycias_rawmaterial.ID 
                                                           AND ycias_material_inward.entity_ID = $entityID)subquery, (SELECT @row_number:=0) AS t ";  
                                                           
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
                                                $this->tpl->set('table_columns_label_arr', array('ID','Date','Purchase Order No','Supplier Name','Rawmaterial Name','Batch No','Total Volume','Total Cost'));
                                                
                                                /*,;;
                                                 * selectColArr for filter form
                                                 */
                                                
                                                $this->tpl->set('selectColArr',$colArr);
                                                            
                                                /*
                                                 * set pagination template
                                                 */
                                                $this->crg->set('paginationListTemplate','factory/template/Search_Download_button.php');
                                                
                                                $this->tpl->set('widget_2', $this->tpl->fetch('factory/template/filterForms/stocktransaction_rawmaterial_period.php')); 
                                                       
                                                //////////////////////close//////////////////////////////////////  
                                              
                                                 include_once $this->tpl->path .'/factory/form/rawmaterial_report_crud_form.php';
                                                
                                                
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
                                        
                                         public function quantityDispatched() {
                                            if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                        //////////////////////////////////////////////////////////////////////////////////
                                        //////////////////////////////access condition applied///////////////////////////
                                        //////////////////////////////////////////////////////////////////////////////// 
                                              
                                               include_once 'util/DBUTIL.php';
                                               $dbutil = new DBUTIL($this->crg);
                                                 
                                                 $entityID = $this->ses->get('user')['entity_ID'];
                                                 $userID = $this->ses->get('user')['ID'];
                                                
                                               $dispatchsupply_tab = $this->crg->get('table_prefix') . 'dispatchsupply';
                                               $dispatch_supply_detail_tab = $this->crg->get('table_prefix') . 'dispatch_supply_detail';
                                               $customer_tab = $this->crg->get('table_prefix') . 'customer';
                                               $productionorder_tab = $this->crg->get('table_prefix') . 'productionorder';
                                               $product_tab = $this->crg->get('table_prefix') . 'product';
                                                      
                                              
                                               $customer_sql = "SELECT ID,PersonName FROM $customer_tab";
                                               $stmt = $this->db->prepare($customer_sql);            
                                               $stmt->execute();
                                               $customer_data  = $stmt->fetchAll();	
                                               $this->tpl->set('customer_data', $customer_data);
                                            
                                               $production_sql = "SELECT ID,PdnOrderNo FROM $productionorder_tab";
                                               $stmt = $this->db->prepare($production_sql);            
                                               $stmt->execute();
                                               $production_data  = $stmt->fetchAll();	
                                               $this->tpl->set('production_data', $production_data);
                                                ////////////////////start//////////////////////////////////////////////
                                                        
                                               //bUILD SQL 
                                                $whereString = '';
                                                
                                                $colArr = array(
                                                    "$dispatchsupply_tab.ID", 
                                                    "$customer_tab.PersonName",
                                                    "$productionorder_tab.PdnOrderNo",
                                                    "$product_tab.ProductName",
                                                    "SUM($dispatch_supply_detail_tab.Field2) as TotalQuantity"
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
                                                
                                                if(!empty($_POST['customer_ID'])){
                                                                   $customer_ID=($_POST['customer_ID']);
                                                   
                                                      }


                                                if(!empty($_POST['ProductionOrderID'])){
                                                                   $ProductionOrderID=($_POST['ProductionOrderID']);
                                                   
                                                      }
                                                
                                                $orderBy ="ORDER BY $customer_tab.PersonName ASC";
                                            
                                    $sql = "SELECT "
                                            . implode(',',$colArr)
                                            . " FROM $dispatchsupply_tab,$customer_tab,$productionorder_tab,$dispatch_supply_detail_tab,$product_tab WHERE"
                                            . " $customer_tab.ID=$dispatchsupply_tab.CustomerID AND"
                                            . " $dispatchsupply_tab.ProductionID = $productionorder_tab.ID AND"
                                            . " $dispatch_supply_detail_tab.dispatch_supply_ID = $dispatchsupply_tab.ID AND"
                                            . " $dispatch_supply_detail_tab.product_ID = $product_tab.ID AND"
                                            . " $dispatchsupply_tab.CustomerID = $customer_ID AND"
                                            . " $dispatchsupply_tab.ProductionID = $ProductionOrderID AND"
                                            . " $dispatchsupply_tab.entity_ID=$entityID "
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
                                                $this->tpl->set('table_columns_label_arr', array('ID','Customer Name','Productionorder No','Product Name','Quantity Issued'));
                                                
                                                /*,;;
                                                 * selectColArr for filter form
                                                 */
                                                
                                                $this->tpl->set('selectColArr',$colArr);
                                                            
                                                /*
                                                 * set pagination template
                                                 */
                                                $this->crg->set('paginationListTemplate','factory/template/Search_Download_button.php');
                                                
                                                $this->tpl->set('widget_2', $this->tpl->fetch('factory/template/filterForms/stocktransaction_customer_status.php')); 
                                                       
                                                //////////////////////close//////////////////////////////////////  
                                              
                                                 include_once $this->tpl->path .'/factory/form/report_crud_form.php';
                                                
                                                
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
                                        
                                         public function DisRetReport() {
                                            if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                        //////////////////////////////////////////////////////////////////////////////////
                                        //////////////////////////////access condition applied///////////////////////////
                                        //////////////////////////////////////////////////////////////////////////////// 
                                              
                                               include_once 'util/DBUTIL.php';
                                               $dbutil = new DBUTIL($this->crg);
                                                 
                                                 $entityID = $this->ses->get('user')['entity_ID'];
                                                 $userID = $this->ses->get('user')['ID'];
                                                
                                               $dispatchreturnable_tab = $this->crg->get('table_prefix') . 'dispatchreturnable';
                                               $dispatchreturnable_detail_tab = $this->crg->get('table_prefix') . 'dispatchreturnable_detail';
                                               $productionorder_tab = $this->crg->get('table_prefix') . 'productionorder';
                                               $product_tab = $this->crg->get('table_prefix') . 'product';
                                               $rawmaterial_tab = $this->crg->get('table_prefix') . 'rawmaterial';
                                               $supplier_tab = $this->crg->get('table_prefix') . 'supplier';
                                                      
                                              
                                               $product_sql = "SELECT ID,ProductName FROM $product_tab ";
                                               $stmt = $this->db->prepare($product_sql);            
                                               $stmt->execute();
                                               $product_data  = $stmt->fetchAll();	
                                               $this->tpl->set('product_data', $product_data);
                                            
                                               $production_sql = "SELECT ID,PdnOrderNo FROM $productionorder_tab";
                                               $stmt = $this->db->prepare($production_sql);            
                                               $stmt->execute();
                                               $production_data  = $stmt->fetchAll();	
                                               $this->tpl->set('production_data', $production_data);
                                                ////////////////////start//////////////////////////////////////////////
                                                        
                                               //bUILD SQL 
                                                $whereString = '';
                                                
                                                $colArr = array(
                                                    "$dispatchreturnable_tab.ID", 
                                                    "$dispatchreturnable_tab.Dispatchno",
                                                    "$supplier_tab.SupplierName",
                                                    "$productionorder_tab.PdnOrderNo",
                                                    "$product_tab.ProductName",
                                                    "$rawmaterial_tab.RMName",
                                                    "$dispatchreturnable_detail_tab.Available_stock",
                                                    "$dispatchreturnable_detail_tab.Quantity"
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
                                                
                                                if(!empty($_POST['ProductionID'])){
                                                                   $ProductionID=($_POST['ProductionID']);
                                                   
                                                      }


                                                if(!empty($_POST['product_ID'])){
                                                                   $product_ID=($_POST['product_ID']);
                                                   
                                                      }
                                                
                                                $orderBy ="ORDER BY $rawmaterial_tab.RMName ASC";
                                            
                                    $sql = "SELECT "
                                            . implode(',',$colArr)
                                            . " FROM $dispatchreturnable_tab,$supplier_tab,$rawmaterial_tab,$dispatchreturnable_detail_tab,$productionorder_tab,$product_tab WHERE"
                                            . " $supplier_tab.ID=$dispatchreturnable_tab.SubcontractorID AND"
                                            . " $dispatchreturnable_detail_tab.Dispatchreturn_ID = $dispatchreturnable_tab.ID AND"
                                            . " $rawmaterial_tab.ID=$dispatchreturnable_detail_tab.Rawmaterial_ID AND"
                                            . " $productionorder_tab.ID=$dispatchreturnable_tab.ProductionID AND"
                                            . " $dispatchreturnable_tab.product_ID = $product_tab.ID AND"
                                            . " $dispatchreturnable_tab.ProductionID=$ProductionID AND"
                                            . " $dispatchreturnable_tab.product_ID = $product_ID AND"
                                            . " $dispatchreturnable_tab.entity_ID=$entityID "
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
                                                $this->tpl->set('table_columns_label_arr', array('ID','Dispatch no','Subcontractor Name','Productionorder No','Product Name','Rawmaterial Name','Available Stock','Quantity Dispatched'));
                                                
                                                /*,;;
                                                 * selectColArr for filter form
                                                 */
                                                
                                                $this->tpl->set('selectColArr',$colArr);
                                                            
                                                /*
                                                 * set pagination template
                                                 */
                                                $this->crg->set('paginationListTemplate','factory/template/Search_Download_button.php');
                                                
                                                $this->tpl->set('widget_2', $this->tpl->fetch('factory/template/filterForms/DisRetReport.php')); 
                                                       
                                                //////////////////////close//////////////////////////////////////  
                                              
                                                 include_once $this->tpl->path .'/factory/form/Dispatchreturnable_report_crud_form.php';
                                                
                                                
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
                                        
                            // function rawmaterial_volume_contract() {
                            //                 if ($this->crg->get('wp') || $this->crg->get('rp')) {
                            //             //////////////////////////////////////////////////////////////////////////////////
                            //             //////////////////////////////access condition applied///////////////////////////
                            //             //////////////////////////////////////////////////////////////////////////////// 
                                              
                            //                   include_once 'util/DBUTIL.php';
                            //                   $dbutil = new DBUTIL($this->crg);
                                                 
                            //                      $entityID = $this->ses->get('user')['entity_ID'];
                            //                      $userID = $this->ses->get('user')['ID'];
                                                
                            //                   $dispatchreturnable_tab = $this->crg->get('table_prefix') . 'dispatchreturnable';
                            //                   $dispatchreturnable_detail_tab = $this->crg->get('table_prefix') . 'dispatchreturnable_detail';
                            //                   $rawmaterial_tab = $this->crg->get('table_prefix') . 'rawmaterial';
                                                      
                                              
                            //                     ////////////////////start//////////////////////////////////////////////
                                                        
                            //                   //bUILD SQL 
                            //                     $whereString = '';
                                                
                            //                     $colArr = array(
                            //                         "$dispatchreturnable_tab.ID", 
                            //                         "$rawmaterial_tab.RMName", 
                            //                         "SUM($dispatchreturnable_detail_tab.Quantity) as TotalQuantity"
                            //                     );
                                                
                            //                     $this->tpl->set('FmData', $_POST);
                            //                     foreach($_POST as $k=>$v){
                            //                         if(strpos($k,'^')){
                            //                             unset($_POST[$k]);
                            //                         }
                            //                         $_POST[str_replace('^','_',$k)] = $v;
                            //                     }
                            //                     $PD=$_POST;
                            //                     if($_POST['list']!=''){
                            //                         $this->tpl->set('FmData', NULL);
                            //                         $PD=NULL;
                            //                     }
                                        
                            //                     IF (count($PD) >= 2) {
                            //                         $wsarr = array();
                            //                         foreach ($colArr as $colNames) {
                                        
                            //                   if (strpos($colNames, 'Date') !== false) {
                            //                         list($colNames,$x) = $dbutil->dateFilterFormat($colNames);
                            //                     } else {
                            //                         $x = $dbutil->__mdsf($PD[str_replace('.','_',$colNames)]);
                            //                     }
                                        
                            //                             if ('' != $x) {
                            //                                 $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                            //                             }
                            //                         }
                                                    
                            //                       IF (count($wsarr) >= 1) {
                            //                          $whereString = ' AND '. implode(' AND ', $wsarr);
                            //                       }
                            //                     }
                                                
                            //               $orderBy ="ORDER BY $rawmaterial_tab.RMName ASC";
                                            
                            //           $sql = "SELECT "
                            //                 . implode(',',$colArr)
                            //                 . " FROM $dispatchreturnable_tab,$dispatchreturnable_detail_tab,$rawmaterial_tab WHERE"
                            //                 . " $dispatchreturnable_detail_tab.Dispatchreturn_ID = $dispatchreturnable_tab.ID AND"
                            //                 . " $dispatchreturnable_detail_tab.Rawmaterial_ID = $rawmaterial_tab.ID AND"
                            //                 . " $rawmaterial_tab.entity_ID=$entityID "
                            //                 . " $whereString "
                            //                 . " GROUP BY $rawmaterial_tab.RMName "
                            //                 . " $orderBy";  
                            //                     $results_per_page = 50;     
                                                
                            //                         if(isset($PD['pageno'])){$page=$PD['pageno'];}
                            //                         else if(isset($PD['pagenof'])){$page=$PD['pagenofirst'];}
                            //                         else if(isset($PD['pagenop'])){$page=$PD['pagenoprev'];}
                            //                         else if(isset($PD['pagenon'])){$page=$PD['pagenonext'];}
                            //                         else if(isset($PD['pagenol'])){$page=$PD['pagenolast'];}
                            //                         else if(isset($PD['pagenog'])){$page=$PD['pagenogo'];}
                            //                         else{$page=1;} 
                                             
                            //                     /*
                            //                      * SET DATA TO TEMPLATE
                            //                      */
                            //                     $this->tpl->set('sql_data_rows', $dbutil->setPaginationList($sql,$page,$results_per_page,$wsarr));
                            //                     /*
                            //                      * set table label
                            //                      */
                            //                     $this->tpl->set('table_columns_label_arr', array('ID','Rawmaterial Name','Total Volume'));
                                                
                            //                     /*,;;
                            //                      * selectColArr for filter form
                            //                      */
                                                
                            //                     $this->tpl->set('selectColArr',$colArr);
                                                            
                            //                     /*
                            //                      * set pagination template
                            //                      */
                            //                     $this->crg->set('paginationListTemplate','factory/template/Search_Download_button.php');
                                                       
                            //                     //////////////////////close//////////////////////////////////////  
                                              
                            //                      include_once $this->tpl->path .'/factory/form/report_crud_form.php';
                                                
                                                
                            //                     $cus_form_data = Form_Elements::data($this->crg);
                            //                     include_once 'util/crud3_1.php';
                            //                     new Crud3($this->crg, $cus_form_data);
                            //                     // $this->tpl->set('master_layout', 'layout_chart.php'); 
                            //                     // $this->tpl->set('master_layout', 'layout_chart.php'); 
                            //                     $this->tpl->set('master_layout', 'layout_datepicker.php');
                            //                      //if crud is delivered at different point a template
                            //                     //Then  call that template and set to content
                                               
                            //                   ////$this->tpl->set('content', $this->tpl->fetch('modules/customer/manage_customer.php'));
                            //                 //////////////////////////////////////////////////////////////////////////////////
                            //                 //////////////////////////////on access condition failed then ///////////////////////////
                            //                 ////////////////////////////////////////////////////////////////////////////////            
                            //                 } else {
                            //                     if ($this->ses->get('user')['ID']) {
                            //                         $this->tpl->set('content', $this->tpl->fetch('modules/user/acess_failed_message.php'));
                            //                     } else {
                            //                         header('Location:' . $this->crg->get('route')['base_path'] . '/user/auth/login');
                            //                     }
                            //                 }
                            //             }
    
}