<?php

/**
 * Description of Product_Mod
 *
 * @author psmahadevan
 */
class Dispatch_Mod {

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
    
     function dispatchreturnable(){
    
        if ($this->crg->get('wp') || $this->crg->get('rp')) {
                 
                ////////////////////////////////////////////////////////////////////////////////
                //////////////////////////////access condition applied//////////////////////////
                ////////////////////////////////////////////////////////////////////////////////    
                            
                include_once 'util/DBUTIL.php';
                $dbutil = new DBUTIL($this->crg);
                 
                $entityID = $this->ses->get('user')['entity_ID'];
                $userID = $this->ses->get('user')['ID'];
                
                $dispatchreturnable_table = $this->crg->get('table_prefix') . 'dispatchreturnable';
                $dispatchreturnable_detail_table = $this->crg->get('table_prefix') . 'dispatchreturnable_detail';
                $dispatchreturnable1_detail_table = $this->crg->get('table_prefix') . 'dispatchreturnable1_detail';
                $productionorder_table = $this->crg->get('table_prefix') . 'productionorder';
                $workorder_table = $this->crg->get('table_prefix') . 'workorder';
                $employee_table = $this->crg->get('table_prefix') . 'employee';
                $product_table = $this->crg->get('table_prefix') . 'product';
                $supplier_table = $this->crg->get('table_prefix') . 'supplier';
                $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
                $supplier_table = $this->crg->get('table_prefix') . 'supplier';

                $production_sql = "SELECT ID,PdnOrderNo FROM $productionorder_table";
                $stmt = $this->db->prepare($production_sql);            
                $stmt->execute();
                $production_data  = $stmt->fetchAll();	
                $this->tpl->set('production_data', $production_data);
                
                 $rawmaterial_sql = "SELECT ID,RMName FROM $rawmaterial_table";
                 $stmt = $this->db->prepare($rawmaterial_sql);            
                 $stmt->execute();
                 $rawmaterial_data  = $stmt->fetchAll();	
                 $this->tpl->set('rawmaterial_data', $rawmaterial_data);

                $sub_sql = "SELECT $supplier_table.ID,$supplier_table.SupplierName FROM $workorder_table INNER JOIN $supplier_table ON $supplier_table.ID=$workorder_table.Subcontractor_ID WHERE $workorder_table.EmployeeType=2 GROUP BY $supplier_table.ID";
                $stmt = $this->db->prepare($sub_sql);            
                $stmt->execute();
                $sub_data  = $stmt->fetchAll();	
                $this->tpl->set('sub_data', $sub_data);

                $product_sql = "SELECT ID,ProductName FROM $product_table ";
                $stmt = $this->db->prepare($product_sql);            
                $stmt->execute();
                $product_data  = $stmt->fetchAll();	
                $this->tpl->set('product_data', $product_data);
                
                $this->tpl->set('page_title', 'Dispatch Returnable');	          
                $this->tpl->set('page_header', 'Dispatch Returnable');
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

                         $sqldetdelete="Delete from $dispatchreturnable_table
                                            where $dispatchreturnable_table.ID=$data"; 
                            $stmt = $this->db->prepare($sqldetdelete);            
                            
                            if($stmt->execute()){
                            $this->tpl->set('message', 'Dispatch Returnable form deleted successfully');
                                                                                                                          
                            //$this->tpl->set('label', 'List');
                            //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/dispatchreturnable');
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
                                    
                        
                        // $sqlsrr = "SELECT * FROM `$employee_salary_table` WHERE `$employee_salary_table.ID` = '$data'";                    
                        // $employee_salary_data = $dbutil->getSqlData($sqlsrr); 
                        
                          $sqlsrr = "SELECT $dispatchreturnable_table.*,$dispatchreturnable_detail_table.*,$supplier_table.AddressLine1
                            FROM $dispatchreturnable_table 
                            LEFT JOIN $dispatchreturnable_detail_table ON $dispatchreturnable_detail_table.Dispatchreturn_ID=$dispatchreturnable_table.ID 
                            LEFT JOIN $supplier_table ON $supplier_table.ID=$dispatchreturnable_table.SubcontractorID 
                            WHERE  $dispatchreturnable_table.ID= $data";
                            $dispatchreturnable_data = $dbutil->getSqlData($sqlsrr);

                            $sqlsrr = "SELECT * FROM  `$dispatchreturnable1_detail_table` WHERE `$dispatchreturnable1_detail_table`.`Dispatchreturn_ID` = '$data' ORDER BY $dispatchreturnable1_detail_table.ID ASC";                    
                            $tendersub_data = $dbutil->getSqlData($sqlsrr); 
                       
                    
                        //edit option     
                        $this->tpl->set('message', 'You can view Dispatch Returnable form');
                        $this->tpl->set('page_header', 'Dispatch Returnable');
                        $this->tpl->set('FmData', $dispatchreturnable_data); 
                        $this->tpl->set('FmDataSub', $tendersub_data); 
                        
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_returnable_design_form.php'));                    
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
                                     
                             $sqlsrr = "SELECT $dispatchreturnable_table.*,$dispatchreturnable_detail_table.*,$supplier_table.AddressLine1
                            FROM $dispatchreturnable_table 
                            LEFT JOIN $dispatchreturnable_detail_table ON $dispatchreturnable_detail_table.Dispatchreturn_ID=$dispatchreturnable_table.ID 
                            LEFT JOIN $supplier_table ON $supplier_table.ID=$dispatchreturnable_table.SubcontractorID 
                            WHERE  $dispatchreturnable_table.ID= $data";
                            $dispatchreturnable_data = $dbutil->getSqlData($sqlsrr);

                            $sqlsrr = "SELECT * FROM  `$dispatchreturnable1_detail_table` WHERE `$dispatchreturnable1_detail_table`.`Dispatchreturn_ID` = '$data' ORDER BY $dispatchreturnable1_detail_table.ID ASC";                    
                            $tendersub_data = $dbutil->getSqlData($sqlsrr); 
                            
                            //edit option 
        
                            
                            $this->tpl->set('message', 'You can edit Dispatch Returnable form');
                            $this->tpl->set('page_header', 'Dispatch Returnable');
                            $this->tpl->set('FmData', $dispatchreturnable_data); 
                            $this->tpl->set('FmDataSub', $tendersub_data); 
                            
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_returnable_design_form.php'));                    
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

                        $sqldet_del = "DELETE FROM $dispatchreturnable_detail_table WHERE Dispatchreturn_ID=$data";
                        $stmt = $this->db->prepare($sqldet_del);
                        $stmt->execute();   

                        $sqldet_del = "DELETE FROM $dispatchreturnable1_detail_table WHERE Dispatchreturn_ID=$data";
                        $stmt = $this->db->prepare($sqldet_del);
                        $stmt->execute();
                                                   
                        try{
                              $Dispatchno= $form_post_data['Dispatchno'];
                              $ProductionID= $form_post_data['ProductionID'];
                              $SubcontractorID= $form_post_data['SubcontractorID'];
                              $product_ID= $form_post_data['product_ID'];
                              $Vehicleno= $form_post_data['Vehicleno'];
                              $DriverName= $form_post_data['DriverName'];
                              $DriverMobileNumber= $form_post_data['DriverMobileNumber'];
                              $DispatchFrom= $form_post_data['DispatchFrom'];
                              $TransportMode= $form_post_data['TransportMode'];
                              $TransporterName= $form_post_data['TransporterName'];
                              $LRNumber= $form_post_data['LRNumber'];
                              
                                $sql_update="UPDATE $dispatchreturnable_table set Dispatchno='$Dispatchno',
                                                                              ProductionID='$ProductionID',
                                                                              SubcontractorID='$SubcontractorID',
                                                                              product_ID='$product_ID',
                                                                              Vehicleno='$Vehicleno',
                                                                              DriverName='$DriverName',
                                                                              DriverMobileNumber='$DriverMobileNumber',
                                                                              DispatchFrom='$DispatchFrom',
                                                                              TransportMode='$TransportMode',
                                                                              TransporterName='$TransporterName',
                                                                              LRNumber='$LRNumber'
                                                                              WHERE ID=$data";
                                $stmt1 = $this->db->prepare($sql_update);
                                $stmt1->execute();
                                
                               
                                //$is_delete = $stmt3->execute();
                                // var_dump($is_delete);die;
                                  
                               FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                                       
                                       $Rawmaterial_ID = $form_post_data['Field2_'.$entry_count];
                                       $Quantity = $form_post_data['Field3_'.$entry_count];
                                     
                                     if(!empty($RawmaterialName)){
                                       $vals = "'" . $data . "'," .
                                               "'" . $Rawmaterial_ID . "'," .
                                               "'" . $Quantity . "'" ;
         
                                        $sql2 = "INSERT INTO $dispatchreturnable_detail_table
                                               ( 
                                                `Dispatchreturn_ID`, 
                                                `Rawmaterial_ID`, 
                                                `Quantity`
                                               ) 
                                       VALUES ($vals)";
                                       $stmt = $this->db->prepare($sql2);
                                       $stmt->execute();
                                     }
                                       
                                   //increment here
                                   
                                   }

                                   FOR ($remarkentry_count=1; $remarkentry_count <= $form_post_data['maxCountSub'];$remarkentry_count++) {
    
    							    $rawmaterial_ID =$form_post_data['ItemName_' . $remarkentry_count];
    								$Quantity1 =$form_post_data['Field5_' . $remarkentry_count];
    								
    								if(!empty($rawmaterial_ID)){
    								    
                                    $vals = "'" . $data . "'," .
                                            "'" . $rawmaterial_ID . "'," .
                                            "'" . $Quantity1 . "'";
                                     
                                    $sql2 = "INSERT INTO $dispatchreturnable1_detail_table
                                            ( 
                                                `Dispatchreturn_ID`, 
                                                `rawmaterial_ID`,
                                                `Quantity1`
                                            ) 
                                    VALUES ($vals)";
                                    $stmt = $this->db->prepare($sql2);
                                    $stmt->execute();
    						
    								}
                                }

                              
                           
                                $this->tpl->set('message', 'Dispatch Returnable form edited successfully!');   
                                                                                                                              
                                // $this->tpl->set('label', 'List');
                                // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/dispatchreturnable');
                                } catch (Exception $exc) {
                                 //edit failed option
                                $this->tpl->set('message', 'Failed to edit, try again!');
                                $this->tpl->set('FmData', $form_post_data);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_returnable_design_form.php'));
                                }
    
                        break;
    
                    case 'addsubmit':
                         if (isset($crud_string)) {
     
                            $form_post_data = $dbutil->arrFltr($_POST);

                         include_once 'util/genUtil.php';
                         $util = new GenUtil();
                            
                          // var_dump($_POST);
                           
                           
                           if (isset($form_post_data['Dispatchno'])) {
                               
                                            $val = "'" . $form_post_data['Dispatchno'] . "'," .
                                                   "'" . $form_post_data['ProductionID'] . "'," .
                                                   "'" . $form_post_data['SubcontractorID'] . "'," .
                                                   "'" . $form_post_data['product_ID'] . "'," .
                                                   "'" . $form_post_data['Vehicleno'] . "'," .
                                                   "'" . $form_post_data['DriverName'] . "'," .
                                                   "'" . $form_post_data['DriverMobileNumber'] . "'," .
                                                   "'" . $form_post_data['DispatchFrom'] . "'," .
                                                   "'" . $form_post_data['TransportMode'] . "'," .
                                                   "'" . $form_post_data['TransporterName'] . "'," .
                                                   "'" . $form_post_data['LRNumber'] . "'," .
                                                "'" . $form_post_data['Total'] . "'," .
												"'" . $form_post_data['GST'] . "'," .
												"'" . $form_post_data['CGST'] . "'," .
												"'" . $form_post_data['CGSTtotal'] . "'," .
												"'" . $form_post_data['optradio'] . "'," .
												"'" . $form_post_data['SGST'] . "'," .
												"'" . $form_post_data['SGSTtotal'] . "'," .
												"'" . $form_post_data['IGST'] . "'," .
												"'" . $form_post_data['IGSTtotal'] . "'," .
												"'" . $form_post_data['Advance'] . "'," .
												"'" . $form_post_data['Balance'] . "'," .
                                                  "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                             "'" .  $this->ses->get('user')['ID'] . "'";
                 
                                 $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "dispatchreturnable`
                                                (
                                                `Dispatchno`, 
                                                `ProductionID`, 
                                                `SubcontractorID`, 
                                                `product_ID`, 
                                                `Vehicleno`, 
                                                `DriverName`, 
                                                `DriverMobileNumber`, 
                                                `DispatchFrom`, 
                                                `TransportMode`, 
                                                `TransporterName`, 
                                                `LRNumber`, 
                                            `Total`,
											`GST`,
											`CGST`,
											`CGSTtotal`,
											`CGST_&_SGST_&_IGST`,
											`SGST`,
											`SGSTtotal`,
											`IGST`,
											`IGSTtotal`,
											`Advance`,
											`Balance`,
                                                `entity_ID`,
                                                `users_ID`
                                                ) 
                                             VALUES ( $val )";
                                      $stmt = $this->db->prepare($sql); 
                                      
                                      if ($stmt->execute()) { 
                                       //echo '<pre>';
                                       //print_r($_POST);
                                           $lastInsertedID = $this->db->lastInsertId();
                                           $maxCount = $form_post_data['maxCount'];
           
                                           FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                                   
                                                   $Rawmaterial_ID = $form_post_data['Rawstock_'.$entry_count];
                                                   $Availablestock = $form_post_data['Field4_'.$entry_count];
                                                  // print_r($RawmaterialName);die;
                                                   $Quantity = $form_post_data['Field3_'.$entry_count];
                                                   $Price_unit = $form_post_data['price_unitt_'.$entry_count];
                                                   $value = $form_post_data['Value_'.$entry_count];
                                                 
                                                   if(!empty($Rawmaterial_ID)){
                                                   $vals = "'" . $lastInsertedID . "'," .
                                                           "'" . $Rawmaterial_ID . "'," .
                                                           "'" . $Availablestock . "'," .
                                                           "'" . $Quantity . "'," .
                                                           "'" . $Price_unit . "'," .
                                                           "'" . $value . "'" ;
                     
                                                   $sql2 = "INSERT INTO $dispatchreturnable_detail_table
                                                           ( 
                                                               `Dispatchreturn_ID`, 
                                                               `Rawmaterial_ID`, 
                                                               `Available_stock`, 
                                                               `Quantity`,
                                                               `price_unit`,
                                                               `Value`
                                                           ) 
                                                   VALUES ($vals)";
                                                   $stmt = $this->db->prepare($sql2);
                                                 //  $stmt->execute();
                                                 if($stmt->execute()){
                                                      $sql_update="UPDATE ycias_stock                                        
                                                                   SET available_qty=(available_qty-$Quantity)
                                                                   WHERE rawmaterial_id =  $Rawmaterial_ID AND entity_ID=$entityID";                                                   
                                                              $update_stmt = $this->db->prepare($sql_update);
                                                              $update_stmt->execute();
                                                              
                                                     $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType, raw_material_ID, stockout, entity_ID, users_ID)
                                                                    VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Dispatch Returnable', $Rawmaterial_ID, $Quantity, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                     
                                                                    $update_stmt = $this->db->prepare($sql_update);
                                                                    $update_stmt->execute();
                                                 }
                                                 }
                                               
                                               }

                                               //$maxCountSub = $form_post_data['maxCountSub'];
               
                                               FOR ($remarkentry_count=1; $remarkentry_count <= $form_post_data['maxCountSub'];$remarkentry_count++) {
                  
                                                $rawmaterial_ID =$form_post_data['ItemName_' . $remarkentry_count];
                                             //  print_r($form_post_data);
                                                $Quantity1 =$form_post_data['Field5_' . $remarkentry_count];
                                               // $remarkdate=!empty($remark_date)?date("Y-m-d", strtotime($remark_date)):'';
                                                
                                                if(!empty($rawmaterial_ID)){
                                                    
                                                    $vals = "'" . $lastInsertedID . "'," .
                                                            "'" . $rawmaterial_ID. "'," .
                                                            "'" . $Quantity1 . "'" ;
                                                 
                                                    $sql2 = "INSERT INTO $dispatchreturnable1_detail_table
                                                            ( 
                                                                `Dispatchreturn_ID`, 
                                                                `rawmaterial_ID`,
                                                                `Quantity1`
                                                            ) 
                                                    VALUES ($vals)";
                                                    $stmt = $this->db->prepare($sql2);
                                                    $stmt->execute();
                                                }
                                               
                                            }

                            }
                                     
                            }
                            $this->tpl->set('mode', 'add');
                            $this->tpl->set('message', '- Success -');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                            header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/dispatchreturnable');
                                                                                                            
                         } else {
                                //edit option
                                //if submit failed to insert form
                                $this->tpl->set('message', 'Failed to submit!');
                                $this->tpl->set('FmData', $form_post_data);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_returnable_design_form.php'));
                         }
                        break;
                    case 'add':
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('page_header', 'Dispatch Returnable');
                        $Dispatchno=$dbutil->keyGeneration('dispatchreturnable','DRN','','Dispatchno');
                        $this->tpl->set('Dispatchno', $Dispatchno);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_returnable_design_form.php'));
                        break;
    
                    default:
                        /*
                         * List form
                         */
                         
                        ////////////////////start//////////////////////////////////////////////
                        
               //bUILD SQL 
                $whereString = '';
             $colArr = array(
                    "$dispatchreturnable_table.ID",
                    "$dispatchreturnable_table.Dispatchno"
    
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
                 $whereString ="ORDER BY $dispatchreturnable_table.ID DESC";
               }
               
                  
           $sql = "SELECT " 
                        . implode(',',$colArr)
                        . " FROM $dispatchreturnable_table "
                        . " WHERE "
                        . " $dispatchreturnable_table.entity_ID = $entityID" 
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
             
             
             
                $this->tpl->set('table_columns_label_arr', array('ID','Dispatch No'));
                
                /*
                 * selectColArr for filter form
                 */
                
                $this->tpl->set('selectColArr',$colArr);
                            
                /*
                 * set pagination template
                 */
                $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
                       
                //////////////////////close//////////////////////////////////////  
                         
                        include_once $this->tpl->path . '/factory/form/crud_dispatch_returnable_form.php';
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

       function dispatchsupply(){
    
            if ($this->crg->get('wp') || $this->crg->get('rp')) {
                     
                    ////////////////////////////////////////////////////////////////////////////////
                    //////////////////////////////access condition applied//////////////////////////
                    ////////////////////////////////////////////////////////////////////////////////    
                                
                    include_once 'util/DBUTIL.php';
                    $dbutil = new DBUTIL($this->crg);
                     
                    $entityID = $this->ses->get('user')['entity_ID'];
                    $userID = $this->ses->get('user')['ID'];
                    
                    $dispatchsupply_table = $this->crg->get('table_prefix') . 'dispatchsupply';
                    $productionorder_table = $this->crg->get('table_prefix') . 'productionorder';
                    $customer_table = $this->crg->get('table_prefix') . 'customer';
                    $enquiry_table = $this->crg->get('table_prefix') . 'enquiry';
                    $customerorder_table = $this->crg->get('table_prefix') . 'customerorder';
                    $customerorder_detail_table = $this->crg->get('table_prefix') . 'customerorder_detail';
                    $product_table = $this->crg->get('table_prefix') . 'product';
                    $fgstock_table = $this->crg->get('table_prefix') . 'fgstock';
                    $dispatch_supply_detail_table = $this->crg->get('table_prefix') . 'dispatch_supply_detail';
    
                    $production_sql = "SELECT ID,PdnOrderNo FROM $productionorder_table WHERE entity_ID=$entityID GROUP BY $productionorder_table.ID";
                    $stmt = $this->db->prepare($production_sql);            
                    $stmt->execute();
                    $production_data  = $stmt->fetchAll();	
                    $this->tpl->set('production_data', $production_data);

                    $customer_sql = "SELECT ID,PersonName FROM $customer_table";
                    $stmt = $this->db->prepare($customer_sql);            
                    $stmt->execute();
                    $customer_data  = $stmt->fetchAll();	
                    $this->tpl->set('customer_data', $customer_data);
                    
                    $this->tpl->set('page_title', 'Dispatch Supply');	          
                    $this->tpl->set('page_header', 'Dispatch Supply');
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
                            
                     $sql_update="UPDATE $fgstock_table
                            LEFT JOIN $product_table ON $fgstock_table.product_ID=$product_table.ID
                            LEFT JOIN $customerorder_detail_table ON $customerorder_detail_table.Product_ID=$product_table.ID
                            SET $fgstock_table.available_qty=($fgstock_table.available_qty+$customerorder_detail_table.Quantity)
                            WHERE $fgstock_table.product_ID=$customerorder_detail_table.Product_ID";          
                            $update_stmt = $this->db->prepare($sql_update);
                            $update_stmt->execute();
    
                             $sqldetdelete="Delete from $dispatchsupply_table
                                                where $dispatchsupply_table.ID=$data"; 
                                $stmt = $this->db->prepare($sqldetdelete);            
                                
                                if($stmt->execute()){
                                $this->tpl->set('message', 'Dispatch Supply form deleted successfully');
                                                                                                                              
                                //$this->tpl->set('label', 'List');
                                //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/dispatchsupply');
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
                                        
                            
                            // $sqlsrr = "SELECT * FROM `$employee_salary_table` WHERE `$employee_salary_table.ID` = '$data'";                    
                            // $employee_salary_data = $dbutil->getSqlData($sqlsrr); 
                            
                            $sqlsrr = "SELECT $dispatchsupply_table.*,$product_table.ProductName,$customerorder_detail_table.Quantity,$customer_table.PersonName,$customer_table.BillingAddress1
                                            FROM $enquiry_table 
                                            LEFT JOIN $customerorder_table ON $customerorder_table.enquiry_ID=$enquiry_table.ID
                                            LEFT JOIN $customerorder_detail_table ON $customerorder_detail_table.custorder_ID=$customerorder_table.ID
                                            LEFT JOIN $product_table ON $customerorder_detail_table.Product_ID=$product_table.ID
                                            LEFT JOIN $productionorder_table ON $productionorder_table.enquiry_ID=$enquiry_table.ID
                                            LEFT JOIN $dispatchsupply_table ON $productionorder_table.ID=$dispatchsupply_table.ProductionID
                                            LEFT JOIN $customer_table ON $customer_table.ID=$dispatchsupply_table.CustomerID
                                            WHERE  $dispatchsupply_table.ID= $data";        
                                            $dispatchsupply_data = $dbutil->getSqlData($sqlsrr);
                                            
                             $sqlsrr = "SELECT $dispatch_supply_detail_table.Field2,$dispatch_supply_detail_table.Available_stock,$dispatch_supply_detail_table.pending_qty
                                    FROM $dispatchsupply_table 
                                    LEFT JOIN $dispatch_supply_detail_table ON $dispatch_supply_detail_table.dispatch_supply_ID=$dispatchsupply_table.ID
                                    WHERE  $dispatchsupply_table.ID= $data";
                                    $subcontractor_materialinward3_data = $dbutil->getSqlData($sqlsrr);
                                    $this->tpl->set('ScheduleDat', $subcontractor_materialinward3_data); 
                           
                        
                            //edit option     
                            $this->tpl->set('message', 'You can view Dispatch Supply form');
                            $this->tpl->set('page_header', 'Dispatch Supply');
                            $this->tpl->set('FmData', $dispatchsupply_data); 
                            
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_supply_design_form.php'));                    
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
                                         
                                $sqlsrr = "SELECT $dispatchsupply_table.*,$product_table.ProductName,$customerorder_detail_table.Quantity,$customer_table.PersonName,$customer_table.BillingAddress1
                                            FROM $enquiry_table 
                                            LEFT JOIN $customerorder_table ON $customerorder_table.enquiry_ID=$enquiry_table.ID
                                            LEFT JOIN $customerorder_detail_table ON $customerorder_detail_table.custorder_ID=$customerorder_table.ID
                                            LEFT JOIN $product_table ON $customerorder_detail_table.Product_ID=$product_table.ID
                                            LEFT JOIN $productionorder_table ON $productionorder_table.enquiry_ID=$enquiry_table.ID
                                            LEFT JOIN $dispatchsupply_table ON $productionorder_table.ID=$dispatchsupply_table.ProductionID
                                            LEFT JOIN $customer_table ON $customer_table.ID=$dispatchsupply_table.CustomerID
                                            WHERE  $dispatchsupply_table.ID= $data";
                                            $dispatchsupply_data = $dbutil->getSqlData($sqlsrr);
                                
                                //edit option 
            
                                
                                $this->tpl->set('message', 'You can edit Dispatch Supply form');
                                $this->tpl->set('page_header', 'Dispatch Supply');
                                $this->tpl->set('FmData', $dispatchsupply_data); 
                                
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_supply_design_form.php'));                    
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
                                                       
                            try{
                                  $Dispatchno= $form_post_data['Dispatchno'];
                                  $ProductionID= $form_post_data['ProductionID'];
                                  $CustomerID= $form_post_data['CustomerID'];
                                  $Vehicleno= $form_post_data['Vehicleno'];
                                  $DriverName= $form_post_data['DriverName'];
                                  $DriverMobileNumber= $form_post_data['DriverMobileNumber'];
                                  $DispatchFrom= $form_post_data['DispatchFrom'];
                                  $TransportMode= $form_post_data['TransportMode'];
                                  $TransporterName= $form_post_data['TransporterName'];
                                  $LRNumber= $form_post_data['LRNumber'];
                                  $Remarks= $form_post_data['Remarks'];
                                  
                                    $sql_update="UPDATE $dispatchsupply_table set Dispatchno='$Dispatchno',
                                                                                  ProductionID='$ProductionID',
                                                                                  CustomerID='$CustomerID',
                                                                                  Vehicleno='$Vehicleno',
                                                                                  DriverName='$DriverName',
                                                                                  DriverMobileNumber='$DriverMobileNumber',
                                                                                  DispatchFrom='$DispatchFrom',
                                                                                  TransportMode='$TransportMode',
                                                                                  TransporterName='$TransporterName',
                                                                                  LRNumber='$LRNumber',
                                                                                  Remarks='$Remarks'
                                                                                  WHERE ID=$data";
                                    $stmt1 = $this->db->prepare($sql_update);
                                    $stmt1->execute();
                                    
                               /* $entry_count = 1;
                                FOR ($entry_count; $entry_count <= $form_post_data['maxCount'];) {
                                        $Designation_id ='Designation_' . $entry_count;
                                        $Organisation ='Organisation_' . $entry_count;
                                        $ProjectSiteCategory ='ProjectSiteCategory_' . $entry_count;
                                        $PeriodFro ='PeriodFro_' . $entry_count;
                                        $PeriodTo ='PeriodTo_' . $entry_count;
                                        $Salary ='Salary_' . $entry_count;
                                       
                                                $vals = "'" . $data . "'," .
                                                "'" . $form_post_data[$Designation_id] . "'," .
                                                "'" . $form_post_data[$Organisation] . "'," .
                                                "'" . $form_post_data[$ProjectSiteCategory] . "'," .
                                                "'" . $form_post_data[$PeriodFro] . "'," .
                                                "'" . $form_post_data[$PeriodTo] . "'," .
                                                "'" . $form_post_data[$Salary] . "'" ;
                                                
                                         
                                        $sql2 = "INSERT INTO $employee_master_table
                                                ( 
                                        `employee_master_ID`, 
                                        `Designation_id`,
                                        `Organisation`,
                                        `ProjectSiteCategory`,
                                        `PeriodFro`,
                                        `PeriodTo`,
                                        `Salary` ) 
                                        VALUES ($vals)";
        
                                        $stmt = $this->db->prepare($sql2);
                                        $stmt->execute();
                                    //increment here
                                    $entry_count++;
                                    }*/
                               
                                    $this->tpl->set('message', 'Dispatch Supply form edited successfully!');   
                                                                                                                                  
                                    // $this->tpl->set('label', 'List');
                                    // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                    header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/dispatchsupply');
                                    } catch (Exception $exc) {
                                     //edit failed option
                                    $this->tpl->set('message', 'Failed to edit, try again!');
                                    $this->tpl->set('FmData', $form_post_data);
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_supply_design_form.php'));
                                    }
        
                            break;
        
                        case 'addsubmit':
                             if (isset($crud_string)) {
         
                                $form_post_data = $dbutil->arrFltr($_POST);
    
                             include_once 'util/genUtil.php';
                             $util = new GenUtil();
                                
                              // var_dump($_POST);
                            //   echo '<pre>';
                            //   print_r($_POST); die;
 
                                     $production_ID=$form_post_data['ProductionID'];
                               if (isset($form_post_data['Dispatchno'])) {
                                   
                                                $val = "'" . $form_post_data['Dispatchno'] . "'," .
                                                       "'" . $form_post_data['ProductionID'] . "'," .
                                                       "'" . $form_post_data['CustomerID'] . "'," .
                                                       "'" . $form_post_data['Vehicleno'] . "'," .
                                                       "'" . $form_post_data['DriverName'] . "'," .
                                                       "'" . $form_post_data['DriverMobileNumber'] . "'," .
                                                       "'" . $form_post_data['DispatchFrom'] . "'," .
                                                       "'" . $form_post_data['TransportMode'] . "'," .
                                                       "'" . $form_post_data['TransporterName'] . "'," .
                                                       "'" . $form_post_data['LRNumber'] . "'," .
                                                       "'" . $form_post_data['Remarks'] . "'," .
                                                      "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                 "'" .  $this->ses->get('user')['ID'] . "'";
                     
                                     $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "dispatchsupply`
                                                    (
                                                    `Dispatchno`, 
                                                    `ProductionID`, 
                                                    `CustomerID`, 
                                                    `Vehicleno`, 
                                                    `DriverName`, 
                                                    `DriverMobileNumber`, 
                                                    `DispatchFrom`, 
                                                    `TransportMode`, 
                                                    `TransporterName`, 
                                                    `LRNumber`, 
                                                    `Remarks`, 
                                                    `entity_ID`,
                                                    `users_ID`
                                                    ) 
                                                 VALUES ( $val )";
                                          $stmt = $this->db->prepare($sql); 
                                        //  $stmt->execute();     
                                      if($stmt->execute()){
                                    // echo       $form_post_data['maxCount']; die;
                                          
                                        $lastInsertedID = $this->db->lastInsertId();
                                        
                                             FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
    
                        
                                $item_description =$form_post_data['ProductName_' . $entry_count];  
                                $product_ID =$form_post_data['product_ID' . $entry_count];  
                                $Quantity =$form_post_data['Quantity_' . $entry_count];  
                                $pending_qty =$form_post_data['pending_qty' . $entry_count];  
                                $del_Quantity = $form_post_data['Field2_'.$entry_count];
                                $Available_stock = $form_post_data['availablestock_'.$entry_count];
                                
                                         if(!empty($del_Quantity)){
                                                      $vals = "'" . $lastInsertedID . "'," .
                                                              "'" . $product_ID . "'," .
                                                              "'" . $production_ID . "'," .
                                                              "'" . $Available_stock . "'," .
                                                              "'" . ($pending_qty-$del_Quantity) . "'," .
                                                              "'" . $del_Quantity . "'" ;
                                                            
                                                       $sql2 = "INSERT INTO $dispatch_supply_detail_table
                                                              ( 
                                                                  `dispatch_supply_ID`, 
                                                                  `product_ID`,
                                                                  `production_ID`,
                                                                  `Available_stock`, 
                                                                  `pending_qty`, 
                                                                  `Field2` 
                                                    
                                                              ) 
                                                      VALUES ($vals)";
                                                      $stmt = $this->db->prepare($sql2);
                                                      $stmt->execute();
                                         }
                                
                                 $sql_update="UPDATE $fgstock_table
                                                                SET available_qty=(available_qty-$del_Quantity)
                                                                WHERE product_name='$item_description'";          
                                                            
                                                               $update_stmt = $this->db->prepare($sql_update);
                                                               $update_stmt->execute();
                                                               
                                 $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType,product_ID, stockout, entity_ID, users_ID)
                                                                   VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Dispatch Supply',$product_ID, $del_Quantity, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                                                   $update_stmt = $this->db->prepare($sql_update);
                                                                   $update_stmt->execute();
                                             }
                                    
                                          
                                          
                                             }
                                         
                                }
                                $this->tpl->set('mode', 'add');
                                $this->tpl->set('message', '- Success -');
                                // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                                header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/dispatchsupply');
                                                                                                                
                             } else {
                                    //edit option
                                    //if submit failed to insert form
                                    $this->tpl->set('message', 'Failed to submit!');
                                    $this->tpl->set('FmData', $form_post_data);
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_supply_design_form.php'));
                             }
                            break;
                        case 'add':
                            $this->tpl->set('mode', 'add');
                            $this->tpl->set('page_header', 'Dispatch Supply');
                            $Dispatchno=$dbutil->keyGeneration('dispatchsupply','DSN','','Dispatchno');
                            $this->tpl->set('Dispatchno', $Dispatchno);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/dispatch_supply_design_form.php'));
                            break;
        
                        default:
                            /*
                             * List form
                             */
                             
                            ////////////////////start//////////////////////////////////////////////
                            
                   //bUILD SQL 
                    $whereString = '';
                 $colArr = array(
                        "$dispatchsupply_table.ID",
                        "$dispatchsupply_table.Dispatchno"
        
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
                     $whereString ="ORDER BY $dispatchsupply_table.ID DESC";
                   }
                   
                      
               $sql = "SELECT " 
                            . implode(',',$colArr)
                            . " FROM $dispatchsupply_table "
                            . " WHERE "
                            . " $dispatchsupply_table.entity_ID = $entityID" 
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
                 
                 
                 
                    $this->tpl->set('table_columns_label_arr', array('ID','Dispatch No'));
                    
                    /*
                     * selectColArr for filter form
                     */
                    
                    $this->tpl->set('selectColArr',$colArr);
                                
                    /*
                     * set pagination template
                     */
                    $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
                           
                    //////////////////////close//////////////////////////////////////  
                             
                            include_once $this->tpl->path . '/factory/form/crud_dispatch_supply_form.php';
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

           public function subcontractorMaterialinward(){

                if ($this->crg->get('wp') || $this->crg->get('rp')) {
                         
                        ////////////////////////////////////////////////////////////////////////////////
                        //////////////////////////////access condition applied//////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////    
                                    
                        include_once 'util/DBUTIL.php';
                        $dbutil = new DBUTIL($this->crg);
                         
                        $entityID = $this->ses->get('user')['entity_ID'];
                        $userID = $this->ses->get('user')['ID'];
                        
                        $subcontractor_materialinward_table = $this->crg->get('table_prefix') . 'subcontractor_materialinward';
                        $productionorder_table = $this->crg->get('table_prefix') . 'productionorder';
                        $dispatchreturnable_table = $this->crg->get('table_prefix') . 'dispatchreturnable';
                        $dispatchreturnable_detail_table = $this->crg->get('table_prefix') . 'dispatchreturnable_detail';
                        $dispatchreturnable1_detail_table = $this->crg->get('table_prefix') . 'dispatchreturnable1_detail';
                        $workorder_table = $this->crg->get('table_prefix') . 'workorder';
                        $employee_table = $this->crg->get('table_prefix') . 'employee';
                        $submat_inward_detail_table = $this->crg->get('table_prefix') . 'submat_inward_detail';
                        $supplier_table = $this->crg->get('table_prefix') . 'supplier';
                        $product_table = $this->crg->get('table_prefix') . 'product';
                        $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
                        $rawmaterialtype_table = $this->crg->get('table_prefix') . 'rawmaterialtype';
                        $fgstock_table = $this->crg->get('table_prefix') . 'fgstock';
        
                        $production_sql = "SELECT $productionorder_table.ID,$productionorder_table.PdnOrderNo FROM $dispatchreturnable_table INNER JOIN $productionorder_table ON $productionorder_table.ID=$dispatchreturnable_table.ProductionID";
                        $stmt = $this->db->prepare($production_sql);            
                        $stmt->execute();
                        $production_data  = $stmt->fetchAll();	
                        $this->tpl->set('production_data', $production_data);

                        $dispatch_sql = "SELECT ID,Dispatchno FROM $dispatchreturnable_table";
                        $stmt = $this->db->prepare($dispatch_sql);            
                        $stmt->execute();
                        $dispatch_data  = $stmt->fetchAll();	
                        $this->tpl->set('dispatch_data', $dispatch_data);

                        $sub_sql = "SELECT $supplier_table.ID,$supplier_table.SupplierName FROM $dispatchreturnable_table INNER JOIN $supplier_table ON $supplier_table.ID=$dispatchreturnable_table.SubcontractorID ";
                        $stmt = $this->db->prepare($sub_sql);            
                        $stmt->execute();
                        $sub_data  = $stmt->fetchAll();	
                        $this->tpl->set('sub_data', $sub_data);
                        
                        $this->tpl->set('page_title', 'Subcontractor Material Inward');	          
                        $this->tpl->set('page_header', 'Subcontractor Material Inward');
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
        
                                 $sqldetdelete="Delete from $subcontractor_materialinward_table
                                                    where $subcontractor_materialinward_table.ID=$data"; 
                                    $stmt = $this->db->prepare($sqldetdelete);            
                                    
                                    if($stmt->execute()){
                                    $this->tpl->set('message', 'Subcontractor Material Inward form deleted successfully');
                                                                                                                                  
                                    //$this->tpl->set('label', 'List');
                                    //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                    header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/subcontractorMaterialinward');
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
                                            
                                
                                // $sqlsrr = "SELECT * FROM `$employee_salary_table` WHERE `$employee_salary_table.ID` = '$data'";                    
                                // $employee_salary_data = $dbutil->getSqlData($sqlsrr); 
                                
                               $sqlsrr = "SELECT $subcontractor_materialinward_table.*,$supplier_table.AddressLine1,$product_table.ProductName
                                    FROM $dispatchreturnable_table 
                                    LEFT JOIN $product_table ON $product_table.ID=$dispatchreturnable_table.product_ID 
                                    LEFT JOIN $supplier_table ON $dispatchreturnable_table.SubcontractorID=$supplier_table.ID 
                                    LEFT JOIN $subcontractor_materialinward_table ON $subcontractor_materialinward_table.DispatchID=$dispatchreturnable_table.ID
                                    WHERE  $subcontractor_materialinward_table.ID= $data";
                                    $subcontractor_materialinward_data = $dbutil->getSqlData($sqlsrr);
                                   $this->tpl->set('FmData', $subcontractor_materialinward_data); 

                                   $sqlsrr = "SELECT $rawmaterial_table.RMName,$dispatchreturnable_detail_table.Quantity
                                    FROM $dispatchreturnable_table 
                                    LEFT JOIN $dispatchreturnable_detail_table ON $dispatchreturnable_table.ID=$dispatchreturnable_detail_table.Dispatchreturn_ID
                                    LEFT JOIN $rawmaterial_table ON $rawmaterial_table.ID=$dispatchreturnable_detail_table.Rawmaterial_ID
                                    LEFT JOIN $subcontractor_materialinward_table ON $subcontractor_materialinward_table.DispatchID=$dispatchreturnable_table.ID
                                    WHERE  $subcontractor_materialinward_table.ID= $data";
                                    $subcontractor_materialinward1_data = $dbutil->getSqlData($sqlsrr);
                                    $this->tpl->set('ScheduleData1', $subcontractor_materialinward1_data); 

                                   $sqlsrr = "SELECT $rawmaterial_table.RMName,$dispatchreturnable1_detail_table.Quantity1
                                    FROM $dispatchreturnable_table 
                                    LEFT JOIN $dispatchreturnable1_detail_table ON $dispatchreturnable_table.ID=$dispatchreturnable1_detail_table.Dispatchreturn_ID
                                    LEFT JOIN $rawmaterial_table ON $rawmaterial_table.ID=$dispatchreturnable1_detail_table.rawmaterial_ID
                                    LEFT JOIN $subcontractor_materialinward_table ON $subcontractor_materialinward_table.DispatchID=$dispatchreturnable_table.ID
                                    WHERE  $subcontractor_materialinward_table.ID= $data";
                                    $subcontractor_materialinward2_data = $dbutil->getSqlData($sqlsrr);
                                    $this->tpl->set('ScheduleData', $subcontractor_materialinward2_data); 

                                    $sqlsrr = "SELECT $submat_inward_detail_table.Field2,$submat_inward_detail_table.pending_qty
                                    FROM $subcontractor_materialinward_table 
                                    LEFT JOIN $submat_inward_detail_table ON $submat_inward_detail_table.Submat_inward_ID=$subcontractor_materialinward_table.ID
                                    WHERE  $subcontractor_materialinward_table.ID= $data";
                                    $subcontractor_materialinward3_data = $dbutil->getSqlData($sqlsrr);
                                   //  echo "<pre>"; print_r($subcontractor_materialinward3_data);
                                    $this->tpl->set('ScheduleDat', $subcontractor_materialinward3_data); 
                               
                            
                                //edit option     
                                $this->tpl->set('message', 'You can view Subcontractor Material Inward form');
                                $this->tpl->set('page_header', 'Subcontractor Material Inward');
                                
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_materialinward_design_form.php'));                    
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
                                             
                                    $sqlsrr = "SELECT $subcontractor_materialinward_table.*,$supplier_table.AddressLine1,$product_table.ProductName
                                    FROM $dispatchreturnable_table 
                                    LEFT JOIN $product_table ON $product_table.ID=$dispatchreturnable_table.product_ID 
                                    LEFT JOIN $supplier_table ON $dispatchreturnable_table.SubcontractorID=$supplier_table.ID 
                                    LEFT JOIN $subcontractor_materialinward_table ON $subcontractor_materialinward_table.DispatchID=$dispatchreturnable_table.ID
                                    WHERE  $subcontractor_materialinward_table.ID= $data";
                                    $subcontractor_materialinward_data = $dbutil->getSqlData($sqlsrr);
                                   $this->tpl->set('FmData', $subcontractor_materialinward_data); 

                                    $sqlsrr = "SELECT $rawmaterial_table.RMName,$dispatchreturnable_detail_table.Quantity
                                    FROM $dispatchreturnable_table 
                                    LEFT JOIN $dispatchreturnable_detail_table ON $dispatchreturnable_table.ID=$dispatchreturnable_detail_table.Dispatchreturn_ID
                                    LEFT JOIN $rawmaterial_table ON $rawmaterial_table.ID=$dispatchreturnable_detail_table.Rawmaterial_ID
                                    LEFT JOIN $subcontractor_materialinward_table ON $subcontractor_materialinward_table.DispatchID=$dispatchreturnable_table.ID
                                    WHERE  $subcontractor_materialinward_table.ID= $data";
                                    $subcontractor_materialinward1_data = $dbutil->getSqlData($sqlsrr);
                                    $this->tpl->set('ScheduleData1', $subcontractor_materialinward1_data); 

                                    $sqlsrr = "SELECT $rawmaterialtype_table.RawMaterialType,$dispatchreturnable1_detail_table.Quantity1
                                    FROM $dispatchreturnable_table 
                                    LEFT JOIN $dispatchreturnable1_detail_table ON $dispatchreturnable_table.ID=$dispatchreturnable1_detail_table.Dispatchreturn_ID
                                    LEFT JOIN $rawmaterialtype_table ON $rawmaterialtype_table.ID=$dispatchreturnable1_detail_table.rawmaterialtype_ID
                                    LEFT JOIN $subcontractor_materialinward_table ON $subcontractor_materialinward_table.DispatchID=$dispatchreturnable_table.ID
                                    WHERE  $subcontractor_materialinward_table.ID= $data";
                                    $subcontractor_materialinward2_data = $dbutil->getSqlData($sqlsrr);
                                    $this->tpl->set('ScheduleData', $subcontractor_materialinward2_data); 

                                    $sqlsrr = "SELECT $submat_inward_detail_table.Field2
                                    FROM $subcontractor_materialinward_table 
                                    LEFT JOIN $submat_inward_detail_table ON $submat_inward_detail_table.Submat_inward_ID=$subcontractor_materialinward_table.ID
                                    WHERE  $subcontractor_materialinward_table.ID= $data";
                                    $subcontractor_materialinward3_data = $dbutil->getSqlData($sqlsrr);
                                   //  echo "<pre>"; print_r($subcontractor_materialinward3_data);
                                    $this->tpl->set('ScheduleDat', $subcontractor_materialinward3_data); 
                                    
                                    $this->tpl->set('message', 'You can edit Subcontractor Material Inward form');
                                    $this->tpl->set('page_header', 'Subcontractor Material Inward');
                                    
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_materialinward_design_form.php'));                    
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
                                                           
                                try{
                                      $DispatchID= $form_post_data['DispatchID'];
                                      $ProductionID= $form_post_data['ProductionID'];
                                      $SubcontractorID= $form_post_data['SubcontractorID'];
                                      $ProductName= $form_post_data['ProductName'];
                                      $Vehicleno= $form_post_data['Vehicleno'];
                                      $DriverName= $form_post_data['DriverName'];
                                      $DriverMobileNumber= $form_post_data['DriverMobileNumber'];
                                      $DispatchFrom= $form_post_data['DispatchFrom'];
                                      $TransportMode= $form_post_data['TransportMode'];
                                      $TransporterName= $form_post_data['TransporterName'];
                                      $LRNumber= $form_post_data['LRNumber'];
                                      
                                        $sql_update="UPDATE $subcontractor_materialinward_table set DispatchID='$DispatchID',
                                                                                                    ProductionID='$ProductionID',
                                                                                                    SubcontractorID='$SubcontractorID',
                                                                                                    ProductName='$ProductName',
                                                                                                    Vehicleno='$Vehicleno',
                                                                                                    DriverName='$DriverName',
                                                                                                    DriverMobileNumber='$DriverMobileNumber',
                                                                                                    DispatchFrom='$DispatchFrom',
                                                                                                    TransportMode='$TransportMode',
                                                                                                    TransporterName='$TransporterName',
                                                                                                    LRNumber='$LRNumber'
                                                                                                    WHERE ID=$data";
                                        $stmt1 = $this->db->prepare($sql_update);
                                        $stmt1->execute();
                                        
                                 
                                       // echo"<pre>";print_r($_POST);die;
                                        $maxCount = $form_post_data['maxCount'];			   
                      
                                        $sql3 = "DELETE FROM $submat_inward_detail_table WHERE Submat_inward_ID=$data";
                                        $stmt3 = $this->db->prepare($sql3);
                                        //$is_delete = $stmt3->execute();
                                        // var_dump($is_delete);die;
                                      if($stmt3->execute()){
                                        FOR ($entry_count=0; $entry_count <= $maxCount; $entry_count++) {
                                              // print_r('---');die;
                                              $Quantity = $form_post_data['Field2_'];
                                             // echo $Quantity;die;
                                             
                                             if(!empty($Quantity[$entry_count])){
                                               $vals = "'" . $data . "'," .
                                                       "'" . $Quantity[$entry_count] . "'" ;
                 
                                               $sql2 = "INSERT INTO $submat_inward_detail_table
                                                       ( 
                                                           `Submat_inward_ID`, 
                                                           `Field2`
                                                       ) 
                                               VALUES ($vals)";
                                               $stmt = $this->db->prepare($sql2);
                                               $stmt->execute();
                                            //  }
                                               
                                           //increment here
                                           
                                           }
                                      }
                                    }
                                        $this->tpl->set('message', 'Subcontractor Material Inward form edited successfully!');   
                                                                                                                                      
                                        // $this->tpl->set('label', 'List');
                                        // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                        header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/subcontractorMaterialinward');
                                        } catch (Exception $exc) {
                                         //edit failed option
                                        $this->tpl->set('message', 'Failed to edit, try again!');
                                        $this->tpl->set('FmData', $form_post_data);
                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_materialinward_design_form.php'));
                                        }
            
                                break;
            
                            case 'addsubmit':
                                 if (isset($crud_string)) {
             
                                    $form_post_data = $dbutil->arrFltr($_POST);
        
                                 include_once 'util/genUtil.php';
                                 $util = new GenUtil();
                                  //  echo '<pre>';
                               //  print_r($_POST); die;
                                   $DispatchID=$form_post_data['DispatchID'];
                                   
                                   if (isset($form_post_data['DispatchID'])) {
                                       
                                                    $val = "'" . $form_post_data['DispatchID'] . "'," .
                                                           "'" . $form_post_data['ProductionID'] . "'," .
                                                           "'" . $form_post_data['SubcontractorID'] . "'," .
                                                           "'" . $form_post_data['ProductName'] . "'," .
                                                           "'" . $form_post_data['Vehicleno'] . "'," .
                                                           "'" . $form_post_data['DriverName'] . "'," .
                                                           "'" . $form_post_data['DriverMobileNumber'] . "'," .
                                                           "'" . $form_post_data['DispatchFrom'] . "'," .
                                                           "'" . $form_post_data['TransportMode'] . "'," .
                                                           "'" . $form_post_data['TransporterName'] . "'," .
                                                           "'" . $form_post_data['LRNumber'] . "'," .
                                                          "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                     "'" .  $this->ses->get('user')['ID'] . "'";
                         
                                         $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "subcontractor_materialinward`
                                                        (
                                                        `DispatchID`, 
                                                        `ProductionID`, 
                                                        `SubcontractorID`, 
                                                        `ProductName`, 
                                                        `Vehicleno`, 
                                                        `DriverName`, 
                                                        `DriverMobileNumber`, 
                                                        `DispatchFrom`, 
                                                        `TransportMode`, 
                                                        `TransporterName`, 
                                                        `LRNumber`, 
                                                        `entity_ID`,
                                                        `users_ID`
                                                        ) 
                                                     VALUES ( $val )";
                                              $stmt = $this->db->prepare($sql); 
                                             // $stmt->execute();  
                                             if($stmt->execute()) {
                                              $lastInsertedID = $this->db->lastInsertId();
                                              $maxCount = $form_post_data['maxCount'];
					                            //  echo $maxCount;die;
                                              FOR ($entry_count=0; $entry_count <= $maxCount; $entry_count++) {
                                                      
                                                         $Quantity = $form_post_data['Field2_'.$entry_count];
                                                         $item_id = $form_post_data['item_id_'.$entry_count];
                                                         $expect_qty = $form_post_data['Quantity1'.$entry_count];
                                                         $pending_qty = $form_post_data['pending_qty'.$entry_count];
                                                         $Item_id = $form_post_data['item_id_' . $entry_count];
                                                    
                                                      if(!empty($Quantity)){
                                                      $vals = "'" . $lastInsertedID . "'," .
                                                              "'" . $DispatchID . "'," .
                                                              "'" . $item_id . "'," .
                                                              "'" . $expect_qty . "'," .
                                                              "'" . $Quantity . "'," .
                                                              "'" . ($pending_qty-$Quantity) . "'" ;
                                                            
                                                       $sql2 = "INSERT INTO $submat_inward_detail_table
                                                              ( 
                                                                  `Submat_inward_ID`, 
                                                                  `DispatchID`, 
                                                                  `Rawmaterial_ID`,
                                                                  `expect_qty`, 
                                                                  `Field2`,
                                                                  `pending_qty`
                                                    
                                                              ) 
                                                      VALUES ($vals)";
                                                      $stmt = $this->db->prepare($sql2);
                                                      //$stmt->execute();
                                                      if($stmt->execute()){
                                                           $sql_update="UPDATE ycias_stock                                        
                                                                        SET available_qty=(available_qty+$Quantity)
                                                                        WHERE rawmaterial_id =  $Item_id";                                                   
                                                                        $update_stmt = $this->db->prepare($sql_update);
                                                                        $update_stmt->execute();
                                                                        
                                                            $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType, raw_material_ID, stockin, entity_ID, users_ID)
                                                                           VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Subcontractor Material Inward', $Item_id, $Quantity, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                     
                                                                           $update_stmt = $this->db->prepare($sql_update);
                                                                           $update_stmt->execute();
                                                      }
                                                    
                                                              }
                                                            }
                                                        }
                                             
                                    }
                                    $this->tpl->set('mode', 'add');
                                    $this->tpl->set('message', '- Success -');
                                    // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                                    header('Location:' . $this->crg->get('route')['base_path'] . '/dispatch/cst/subcontractorMaterialinward');
                                                                                                                    
                                 } else {
                                        //edit option
                                        //if submit failed to insert form
                                        $this->tpl->set('message', 'Failed to submit!');
                                        $this->tpl->set('FmData', $form_post_data);
                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_materialinward_design_form.php'));
                                 }
                                break;
                            case 'add':
                                $this->tpl->set('mode', 'add');
                                $this->tpl->set('page_header', 'Subcontractor Material Inward');
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_materialinward_design_form.php'));
                                break;
            
                            default:
                                /*
                                 * List form
                                 */
                                 
                                ////////////////////start//////////////////////////////////////////////
                                
                       //bUILD SQL 
                        $whereString = '';
                     $colArr = array(
                            "$subcontractor_materialinward_table.ID",
                            "$dispatchreturnable_table.Dispatchno"
            
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
                         $whereString ="ORDER BY $subcontractor_materialinward_table.ID DESC";
                       }
                       
                          
                   $sql = "SELECT " 
                                . implode(',',$colArr)
                                . " FROM $subcontractor_materialinward_table LEFT JOIN $dispatchreturnable_table ON $dispatchreturnable_table.ID=$subcontractor_materialinward_table.DispatchID"
                                . " WHERE "
                                . " $subcontractor_materialinward_table.entity_ID = $entityID" 
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
                     
                     
                     
                        $this->tpl->set('table_columns_label_arr', array('ID','Dispatch No'));
                        
                        /*
                         * selectColArr for filter form
                         */
                        
                        $this->tpl->set('selectColArr',$colArr);
                                    
                        /*
                         * set pagination template
                         */
                        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
                               
                        //////////////////////close//////////////////////////////////////  
                                 
                                include_once $this->tpl->path . '/factory/form/crud_subcontractor_materialinward_form.php';
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

}
