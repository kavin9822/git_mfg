<?php

/**
 * Description of Product_Mod
 *
 * @author psmahadevan
 */
class Production_Mod {

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
    
    
    public function productionOrderStatus(){
     
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
                                        
                                        $username=$this->ses->get('user')['user_nicename'];
                                        $this->tpl->set('preparedby', $username);
                                        
                                        $pdndept_table = $this->crg->get('table_prefix') . 'pdndepartment';
                                        $cust_order_tab = $this->crg->get('table_prefix') . 'customerorder';
                                        $cust_order_detail_tab = $this->crg->get('table_prefix') . 'customerorder_detail';
                                        $cust_order_schedule_tab = $this->crg->get('table_prefix') . 'custorder_schedule';
                                        $cust_order_purchase_detail_tab = $this->crg->get('table_prefix') . 'custpurchase_orderdetail';
                                        $enquirymaster_tab = $this->crg->get('table_prefix') . 'enquiry';
                                        $approvalprocess_tab = $this->crg->get('table_prefix') . 'approvalprocess';
                                        $approvaltype_tab = $this->crg->get('table_prefix') . 'approvaltype';
                                        $user_table = $this->crg->get('table_prefix') . 'users';
                                        $pdn_order_tab = $this->crg->get('table_prefix') . 'productionorder';
                                        $unit_tab = $this->crg->get('table_prefix') . 'unit';
                                        $product_tab = $this->crg->get('table_prefix') . 'product';
                                        $workorder_tab = $this->crg->get('table_prefix') . 'workorder';
                                        $qualitycontrol_tab = $this->crg->get('table_prefix') . 'qualitycontrol';

                                        $enquiry_sql = "SELECT ID,EnquiryNo FROM $enquirymaster_tab WHERE $enquirymaster_tab.Productionorder_Status=1";
                                        $stmt = $this->db->prepare($enquiry_sql);            
                                        $stmt->execute();
                                        $enquiry1_data  = $stmt->fetchAll();	
                                        $this->tpl->set('enquiry1_data', $enquiry1_data);

                                        $sql = "SELECT $user_table.ID,$user_table.user_nicename FROM $user_table,$approvaltype_tab where $user_table.ID=$approvaltype_tab.approver_ID"; 
                                        // $stmt = $this->db->prepare($sql);            
                                        // $stmt->execute();
                                        // $approver_data= $stmt->fetchAll();	
                                        $approver_data = $dbutil->getSqlData($sql); 
                                        $this->tpl->set('approver_data', $approver_data);
                                    
                                        $this->tpl->set('page_title', 'Production Order');	          
                                        $this->tpl->set('page_header', 'Production');
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
                                                            
                                                $sqlsrr = "SELECT $pdn_order_tab.* ,$cust_order_tab.ID as custorder_ID,
                                                                      $cust_order_tab.CustOrderNo ,
                                                                      $enquirymaster_tab.EnquiryNo,
                                                                      $pdndept_table.DeptName as pdn_dept,
                                                                      $cust_order_tab.Drawing_Path,
                                                                      $cust_order_tab.Thirdparty_Inspn,
                                                                      $cust_order_tab.Packing_Instn
                                                                FROM  `$pdn_order_tab` 
                                                                LEFT JOIN `$enquirymaster_tab` ON `$pdn_order_tab`.enquiry_ID=`$enquirymaster_tab`.ID 
                                                                LEFT JOIN `$cust_order_tab` ON `$pdn_order_tab`.enquiry_ID=`$cust_order_tab`.enquiry_ID 
                                                                LEFT JOIN `$pdndept_table` ON `$enquirymaster_tab`.`pdndepartment_ID`=`$pdndept_table`.`ID` 
                                                                WHERE `$pdn_order_tab`.`ID` = '$data'";                    
                                                $pdno_data = $dbutil->getSqlData($sqlsrr); 
                                                $this->tpl->set('FmData', $pdno_data); 
                                            
                                                $cust_order_id=$pdno_data[0]['custorder_ID'];
                                                
                                                $sqlsrr = "SELECT * FROM  `$cust_order_schedule_tab` WHERE `$cust_order_schedule_tab`.`custorder_ID` = '$cust_order_id'";                    
                                                $FmData_Schedule = $dbutil->getSqlData($sqlsrr);
                                                $this->tpl->set('ScheduleData', $FmData_Schedule);
                                                
                                                $sqlsrr = "SELECT GROUP_CONCAT($cust_order_purchase_detail_tab.PurchaseorderNo) as PurchaseorderNo FROM  `$cust_order_purchase_detail_tab` WHERE `$cust_order_purchase_detail_tab`.`custorder_ID` = '$cust_order_id'";                    
                                                $FmData_cust_purchase = $dbutil->getSqlData($sqlsrr);
                                                $this->tpl->set('Cust_Purchase_Data', $FmData_cust_purchase);
                                                
                                                $sqlsrr = "SELECT $cust_order_detail_tab.* ,$unit_tab.UnitName,$product_tab.ProductName FROM  $cust_order_detail_tab LEFT JOIN $unit_tab ON $cust_order_detail_tab.unit_ID = $unit_tab.ID
                                                            LEFT JOIN $product_tab ON $cust_order_detail_tab.Product_ID=$product_tab.ID  WHERE $cust_order_detail_tab.custorder_ID = '$cust_order_id'";                    
                                                $FmData_detail = $dbutil->getSqlData($sqlsrr);
                                                $this->tpl->set('FmDetail_Data', $FmData_detail);	
                                                
                                                
                                                //edit option     
                                                $this->tpl->set('message', 'You can view Production Order form');
                                                $this->tpl->set('page_header', 'Production');
                                                
                                                $this->tpl->set('content', $this->tpl->fetch('factory/form/production_order_form.php'));                    
                                                break;
                            
                                            default:
                                                /*
                                                 * List form
                                                 */
                                                 
                                                ////////////////////start//////////////////////////////////////////////
                                                
                                       //bUILD SQL 
                                        $whereString = '';
                                        
                                     $colArr = array(
                                        "$pdn_order_tab.ID", 
                                        "$pdndept_table.DeptName",
                                        "$pdn_order_tab.PdnOrderNo",
                                        "$pdn_order_tab.PdnOrderDate",
                                        "$product_tab.ProductName",
                                        "$cust_order_detail_tab.Quantity",
                                        "$workorder_tab.NoofQuantity",
                                        "$qualitycontrol_tab.Quantity"
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
                                         $whereString ="ORDER BY $pdn_order_tab.ID DESC";
                                       }
                                        
                                       $sql = "SELECT "
                                               . implode(',',$colArr)
                                               . " FROM $enquirymaster_tab 
                                                   LEFT JOIN $pdn_order_tab ON $pdn_order_tab.enquiry_ID=$enquirymaster_tab.ID 
                                                   LEFT JOIN $pdndept_table ON $enquirymaster_tab.pdndepartment_ID=$pdndept_table.ID LEFT JOIN $cust_order_tab ON $cust_order_tab.enquiry_ID=$enquirymaster_tab.ID
                                                   LEFT JOIN $cust_order_detail_tab ON $cust_order_detail_tab.custorder_ID=$cust_order_tab.ID
                                                   LEFT JOIN $product_tab ON $cust_order_detail_tab.Product_ID=$product_tab.ID
                                                   LEFT JOIN $workorder_tab ON $pdn_order_tab.ID=$workorder_tab.PdnOrderID
                                                   LEFT JOIN $qualitycontrol_tab ON $qualitycontrol_tab.ID=$workorder_tab.QualityID "
                                               . " WHERE "
                                               . " $pdn_order_tab.entity_ID=$entityID "
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
                                     
                                     
                                       $this->tpl->set('table_columns_label_arr', array('ID','Division','Production Order No','Production Order Issued Date','Product Name','Quantity','Workorder Issued Quantity','QC Checked Quantity'));
                                        
                                        /*
                                         * selectColArr for filter form
                                         */
                                        
                                        $this->tpl->set('selectColArr',$colArr);
                                                    
                                        /*
                                         * set pagination template
                                         */
                                        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table1.php');
                                               
                                        //////////////////////close//////////////////////////////////////  
                                                 
                                                include_once $this->tpl->path . '/factory/form/crud_form_production_order.php';
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

    function costing(){
    
        if ($this->crg->get('wp') || $this->crg->get('rp')) {
                 
                ////////////////////////////////////////////////////////////////////////////////
                //////////////////////////////access condition applied//////////////////////////
                ////////////////////////////////////////////////////////////////////////////////    
                            
                include_once 'util/DBUTIL.php';
                $dbutil = new DBUTIL($this->crg);
                 
                $entityID = $this->ses->get('user')['entity_ID'];
                $userID = $this->ses->get('user')['ID'];
                
                $costing_table = $this->crg->get('table_prefix') . 'costing';
                $enquiry_table = $this->crg->get('table_prefix') . 'enquiry';
                $tenderdetail_table = $this->crg->get('table_prefix') . 'tenderdetail';
                $tender_table = $this->crg->get('table_prefix') . 'tender';
                $pdndepartment_table = $this->crg->get('table_prefix') . 'pdndepartment';
                $customer_table = $this->crg->get('table_prefix') . 'customer';
                $unit_table = $this->crg->get('table_prefix') . 'unit';

                $tender_sql = "SELECT ID,Title FROM $tenderdetail_table";
                $stmt = $this->db->prepare($tender_sql);            
                $stmt->execute();
                $tenderdetail_data  = $stmt->fetchAll();	
                $this->tpl->set('tenderdetail_data', $tenderdetail_data);
                
                $this->tpl->set('page_title', 'Costing');	          
                $this->tpl->set('page_header', 'Costing');
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

                $data = trim($_POST['ycs_ID']);

                $enquiry_sql = "SELECT $enquiry_table.ID,$enquiry_table.EnquiryNo FROM $enquiry_table
                INNER JOIN $tender_table ON $enquiry_table.ID=$tender_table.enquiry_ID WHERE $enquiry_table.ID=$data ";
                $stmt = $this->db->prepare($enquiry_sql);            
                $stmt->execute();
                $enquiry_data  = $stmt->fetchAll();	
                $this->tpl->set('enquiry_data', $enquiry_data);
    
    
                switch ($crud_string) {
                     case 'delete':                    
                          $data = trim($_POST['ycs_ID']);
                          // var_dump($data); 
                           
                           
                        if (!$data) {
                            $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                            $this->tpl->set('label', 'List');
                            $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                           
                        }

                         $sqldetdelete="Delete from $costing_table
                                            where $costing_table.ID=$data"; 
                            $stmt = $this->db->prepare($sqldetdelete);            
                            
                            if($stmt->execute()){
                            $this->tpl->set('message', 'Costing form deleted successfully');
                                                                                                                          
                            //$this->tpl->set('label', 'List');
                            //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/costing');
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
                        
                        $sqlsrr = "SELECT *,$pdndepartment_table.DeptName,$customer_table.PersonName,$tender_table.TenderSection,$tender_table.ClosingDateTime,$tender_table.InspectionAgency,$tender_table.ApproveAgency,$tender_table.RAEnabledYN,$tender_table.RegularOrDev,$tender_table.ValidityOfferDays
                                   FROM $costing_table 
                                   LEFT JOIN $enquiry_table ON $costing_table.EnquiryID=$enquiry_table.ID 
                                   LEFT JOIN $pdndepartment_table ON $pdndepartment_table.ID=$enquiry_table.pdndepartment_ID 
                                   LEFT JOIN $customer_table ON $customer_table.ID=$enquiry_table.customer_ID 
                                   LEFT JOIN $tender_table ON $tender_table.enquiry_ID=$enquiry_table.ID 
                                   LEFT JOIN $tenderdetail_table ON $tender_table.ID=$tenderdetail_table.tender_ID 
                                   LEFT JOIN $unit_table ON $unit_table.ID=$tenderdetail_table.unit_ID
                                  
                                   WHERE  $costing_table.EnquiryID= $data";
                                   $costing_data = $dbutil->getSqlData($sqlsrr);
                       
                    
                        //edit option     
                        $this->tpl->set('message', 'You can view Costing form');
                        $this->tpl->set('page_header', 'Costing');
                        $this->tpl->set('FmData', $costing_data); 
                        
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/costing_design_form.php'));                    
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
                                     
                        $sqlsrr = "SELECT *,$pdndepartment_table.DeptName,$customer_table.PersonName,$tender_table.TenderSection,$tender_table.ClosingDateTime,$tender_table.InspectionAgency,$tender_table.ApproveAgency,$tender_table.RAEnabledYN,$tender_table.RegularOrDev,$tender_table.ValidityOfferDays
                                   FROM $costing_table 
                                   LEFT JOIN $enquiry_table ON $costing_table.EnquiryID=$enquiry_table.ID 
                                   LEFT JOIN $pdndepartment_table ON $pdndepartment_table.ID=$enquiry_table.pdndepartment_ID 
                                   LEFT JOIN $customer_table ON $customer_table.ID=$enquiry_table.customer_ID 
                                   LEFT JOIN $tender_table ON $tender_table.enquiry_ID=$enquiry_table.ID 
                                   LEFT JOIN $tenderdetail_table ON $tender_table.ID=$tenderdetail_table.tender_ID 
                                   LEFT JOIN $unit_table ON $unit_table.ID=$tenderdetail_table.unit_ID
                                  
                                   WHERE  $costing_table.EnquiryID= $data";
                                   $costing_data = $dbutil->getSqlData($sqlsrr);
                            
                            //edit option 
        
                            
                            $this->tpl->set('message', 'You can edit Costing form');
                            $this->tpl->set('page_header', 'Costing');
                            $this->tpl->set('FmData', $costing_data); 
                            
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/costing_design_form.php'));                    
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

                         //handle file upload and update staff table    
                           // $updatecustomer = array();

                           $lastInsertedID = $this->db->lastInsertId();
                                        //handle file upload and update staff table    
                                          //  $updatecustomer = array();
                                        
                           foreach ($_FILES as $Fvalue => $valueNotUsing) {
                                                
                                                
                           $uploadedFile = $util->handle_file_upload($Fvalue, $ID);
                  
                           if ($uploadedFile) {
                                                   
                           $updatecustomer = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                  }
                              }
                                            
                                          
                           $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "costing` SET " .  $updatecustomer  .
                                        " WHERE ID = '" .$lastInsertedID . "'";
                           $stmt = $this->db->prepare($updateSql);
                           $stmt->execute();
                                                   
                        try{
                            //   $EnquiryID= $form_post_data['EnquiryID'];
                            //   $TitleID= $form_post_data['TitleID'];
                            //   $PricetoQuote= $form_post_data['PricetoQuote'];
                              
                                // $sql_update="UPDATE $costing_table set EnquiryID='$EnquiryID',TitleID='$TitleID',PricetoQuote='$PricetoQuote' WHERE ID=$data";
                                // $stmt1 = $this->db->prepare($sql_update);
                                // $stmt1->execute();

                                $val = "'" . $form_post_data['EnquiryID'] . "'," .
                                "'" . $form_post_data['TitleID'] . "'," .
                                "'" . $form_post_data['FileUpload'] . "'," .
                                "'" . $form_post_data['PricetoQuote'] . "'," .
                               "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                          "'" .  $this->ses->get('user')['ID'] . "'";

                      $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "costing`
                                (
                                `EnquiryID`, 
                                `TitleID`, 
                                `FileUpload`, 
                                `PricetoQuote`, 
                                `entity_ID`,
                                `users_ID`
                                ) 
                             VALUES ( $val )";
                            $stmt = $this->db->prepare($sql); 
                            if($stmt->execute()){
                                $lastInsertedID = $this->db->lastInsertId();
                                //handle file upload and update staff table    
                                  //  $updatecustomer = array();
                                
                                foreach ($_FILES as $Fvalue => $valueNotUsing) {
                                        
                                        
                                $uploadedFile = $util->handle_file_upload($Fvalue, $ID);
          
                                if ($uploadedFile) {
                                           
                                $updatecustomer = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                     }
                                }
                                    
                                  
                                $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "costing` SET " .  $updatecustomer  .
                                             " WHERE ID = '" .$lastInsertedID . "'";
                                $stmt = $this->db->prepare($updateSql);
                                $stmt->execute();
                            }
                           
                                $this->tpl->set('message', 'costing form edited successfully!');   
                                                                                                                              
                                // $this->tpl->set('label', 'List');
                                // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/costing');
                                } catch (Exception $exc) {
                                 //edit failed option
                                $this->tpl->set('message', 'Failed to edit, try again!');
                                $this->tpl->set('FmData', $form_post_data);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/costing_design_form.php'));
                                }
    
                        break;
    
                    case 'addsubmit':
                         if (isset($crud_string)) {
     
                            $form_post_data = $dbutil->arrFltr($_POST);

                         include_once 'util/genUtil.php';
                         $util = new GenUtil();
                            
                          // var_dump($_POST);
                           
                           
                           if (isset($form_post_data['EnquiryID'])) {
                               
                                            $val = "'" . $form_post_data['EnquiryID'] . "'," .
                                                   "'" . $form_post_data['TitleID'] . "'," .
                                                   "'" . $form_post_data['FileUpload'] . "'," .
                                                   "'" . $form_post_data['PricetoQuote'] . "'," .
                                                  "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                             "'" .  $this->ses->get('user')['ID'] . "'";
    
                           $EnquiryID=$form_post_data['EnquiryID'];
                           if($form_post_data['EnquiryID']){	             
                                 $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "costing`
                                                (
                                                `EnquiryID`, 
                                                `TitleID`, 
                                                `FileUpload`, 
                                                `PricetoQuote`, 
                                                `entity_ID`,
                                                `users_ID`
                                                ) 
                                             VALUES ( $val )";
                                      $stmt = $this->db->prepare($sql); 
                                      if ($stmt->execute()) { 
                                        $lastInsertedID = $this->db->lastInsertId();
                                        //handle file upload and update staff table    
                                          //  $updatecustomer = array();
                                        
                                            foreach ($_FILES as $Fvalue => $valueNotUsing) {
                                                
                                                
                                               $uploadedFile = $util->handle_file_upload($Fvalue, $ID);
                  
                                                if ($uploadedFile) {
                                                   
                                                    $updatecustomer = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                                }
                                            }
                                            
                                          
                                             $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "costing` SET " .  $updatecustomer  .
                                                               " WHERE ID = '" .$lastInsertedID . "'";
                                                $stmt = $this->db->prepare($updateSql);
                                                $stmt->execute();
                                                
                                         }
                                        }
                                        
                                     
                            }
                            $this->tpl->set('mode', 'add');
                            $this->tpl->set('message', '- Success -');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                            header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/costing');
                                                                                                            
                         } else {
                                //edit option
                                //if submit failed to insert form
                                $this->tpl->set('message', 'Failed to submit!');
                                $this->tpl->set('FmData', $form_post_data);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/costing_design_form.php'));
                         }
                        break;
                    case 'add':
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('page_header', 'costing');
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/costing_design_form.php'));
                        break;
    
                    default:
                        /*
                         * List form
                         */
                         
                        ////////////////////start//////////////////////////////////////////////
                        
               //bUILD SQL 
                $whereString = '';
             $colArr = array(
                    "$enquiry_table.ID",
                    "$enquiry_table.EnquiryNo"
    
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
                 $whereString ="ORDER BY $enquiry_table.ID DESC";
               }
               
                  
           $sql = "SELECT " 
                        . implode(',',$colArr)
                        . " FROM $enquiry_table "
                        . " WHERE "
                        . " $enquiry_table.EnquiryType = 2 AND" 
                        . " $enquiry_table.CostRequired = 1 AND" 
                        . " $enquiry_table.entity_ID = $entityID " 
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
             
             
             
                $this->tpl->set('table_columns_label_arr', array('ID','Enquiry No'));
                
                /*
                 * selectColArr for filter form
                 */
                
                $this->tpl->set('selectColArr',$colArr);
                            
                /*
                 * set pagination template
                 */
                $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                       
                //////////////////////close//////////////////////////////////////  
                         
                        include_once $this->tpl->path . '/factory/form/crud_costing_form.php';
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


           function workorder(){
    
                if ($this->crg->get('wp') || $this->crg->get('rp')) {
                         
                        ////////////////////////////////////////////////////////////////////////////////
                        //////////////////////////////access condition applied//////////////////////////
                        ////////////////////////////////////////////////////////////////////////////////    
                                    
                        include_once 'util/DBUTIL.php';
                        $dbutil = new DBUTIL($this->crg);
                         
                        $entityID = $this->ses->get('user')['entity_ID'];
                        $userID = $this->ses->get('user')['ID'];
                        
                        $workorder_table = $this->crg->get('table_prefix') . 'workorder';
                        $productionorder_table = $this->crg->get('table_prefix') . 'productionorder';
                        $employee_table = $this->crg->get('table_prefix') . 'employee';
                        $qualitycontrol_table = $this->crg->get('table_prefix') . 'qualitycontrol';
                        $processmaster_table = $this->crg->get('table_prefix') . 'ProcessMaster';
                        $product_table = $this->crg->get('table_prefix') . 'product';

                        $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
                        $bomprocessdetail_table = $this->crg->get('table_prefix') . 'BOMProcessDetail';
                        $bomprocessmaster_table = $this->crg->get('table_prefix') . 'BOMProcessMaster';
                        $unit_table = $this->crg->get('table_prefix') . 'unit';
                        $supplier_table = $this->crg->get('table_prefix') . 'supplier';
                        $workorder_detail_table = $this->crg->get('table_prefix') . 'workorder_detail';
                       
                        $Rework_data = array(array("ID"=>"1","Title"=>"Yes"),array("ID"=>"2","Title"=>"No"));
                        $this->tpl->set('Rework_data', $Rework_data);

                        $Employeetype_data = array(array("ID"=>"1","Title"=>"Employee"),array("ID"=>"2","Title"=>"Sub Contractor"));
                        $this->tpl->set('Employeetype_data', $Employeetype_data);
                        
                        $username=$this->ses->get('user')['user_nicename'];
                        $this->tpl->set('preparedby', $username);

                        $pdn_sql = "SELECT ID,PdnOrderNo FROM $productionorder_table";
                        $stmt = $this->db->prepare($pdn_sql);            
                        $stmt->execute();
                        $pdn_data  = $stmt->fetchAll();	
                        $this->tpl->set('pdn_data', $pdn_data);

                        $pdn_sql = "SELECT ID,ProcessName FROM $processmaster_table";
                        $stmt = $this->db->prepare($pdn_sql);            
                        $stmt->execute();
                        $process_data  = $stmt->fetchAll();	
                        $this->tpl->set('process_data', $process_data);

                        $employee_sql = "SELECT ID,EmpName FROM $employee_table";
                        $stmt = $this->db->prepare($employee_sql);            
                        $stmt->execute();
                        $employee_data  = $stmt->fetchAll();	
                        $this->tpl->set('employee_data', $employee_data);

                        $employee_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE SupplierType='Sub contractor'";
                        $stmt = $this->db->prepare($employee_sql);            
                        $stmt->execute();
                        $subcontractor_data  = $stmt->fetchAll();	
                        $this->tpl->set('subcontractor_data', $subcontractor_data);

                        $employee_sql = "SELECT ID,Qcno FROM $qualitycontrol_table WHERE Qcverified='Rework'";
                        $stmt = $this->db->prepare($employee_sql);            
                        $stmt->execute();
                        $quality_data  = $stmt->fetchAll();	
                        $this->tpl->set('quality_data', $quality_data);

                        $product_sql = "SELECT ID,ProductName FROM $product_table";
                        $stmt = $this->db->prepare($product_sql);            
                        $stmt->execute();
                        $product_data  = $stmt->fetchAll();	
                        $this->tpl->set('product_data', $product_data);
                        
                        $this->tpl->set('page_title', 'WorkOrder');	          
                        $this->tpl->set('page_header', 'WorkOrder');
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
                                 
                                 $sqldetdelete="Delete from $workorder_table
                                                    where $workorder_table.ID=$data"; 
                                    $stmt = $this->db->prepare($sqldetdelete);            
                                    
                                    if($stmt->execute()){
                                    $this->tpl->set('message', 'Work Order form deleted successfully');
                                                                                                                                  
                                    //$this->tpl->set('label', 'List');
                                    //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                    header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/workorder');
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
                                            
                            
                                
                          $sqlsrr = "SELECT $workorder_table.*,$workorder_detail_table.*
                                           FROM $workorder_table
                                           LEFT JOIN $workorder_detail_table ON $workorder_detail_table.Workorder_ID=$workorder_table.ID
                                           WHERE 
                                           $workorder_table.ID = $data";   
                                           $workorder_data = $dbutil->getSqlData($sqlsrr);
                               
                            
                                //edit option     
                                $this->tpl->set('message', 'You can view work order form');
                                $this->tpl->set('page_header', 'Work Order');
                                $this->tpl->set('FmData', $workorder_data); 
                                
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/workorder_design_form.php'));                    
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
                                         
                               
                           $sqlsrr = "SELECT $workorder_table.*,$workorder_detail_table.*
                                           FROM $workorder_table
                                           LEFT JOIN $workorder_detail_table ON $workorder_detail_table.Workorder_ID=$workorder_table.ID
                                           WHERE 
                                           $workorder_table.ID = $data";   
                                           $workorder_data = $dbutil->getSqlData($sqlsrr);

                                //edit option 
            
                                $this->tpl->set('message', 'You can edit Work Order form');
                                $this->tpl->set('page_header', 'Work Order');
                                $this->tpl->set('FmData', $workorder_data); 
                                
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/workorder_design_form.php'));                    
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
                    
                                $CompletedDate=!empty($form_post_data['CompletedDate'])?date("Y-m-d", strtotime($form_post_data['CompletedDate'])):'';
                                                           
                                try{
                                    
                                    // pdf start
                               
                                 require_once('TCPDF/proformaChallen.php');
                                 ob_start(); // at the beggining of your script
                                 
                                 // create new PDF document
                                 
                                 $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                                 
                                 // set document information
                                 
                                 $pdf->SetCreator(PDF_CREATOR);
                                 $pdf->SetAuthor('Sreenidhi Enterprises');
                                 // $report_title = "Delivery Challan";
                                 // $pdf->SetTitle($report_title);
                                 $pdf->SetSubject('Corrective And Preventive Action');
                                 $pdf->SetKeywords('Quotation,Invoice');
                                 
                                 
                                 // set default header data
                                 
                                 
                                 $capa_date = date('d.m.Y',strtotime($capa_data['AuditDateTime']));
                                 $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.'.', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
                                 $pdf->setFooterData(array(0,64,0), array(0,64,128));
                                 
                                 // set header and footer fonts
                                 
                                 $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                                 $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                                 
                                 // set default monospaced font
                                 
                                 $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                                 
                                 
                                 // set margins
                                 
                                 $pdf->SetMargins(10, 5, 10);
                                 $pdf->SetHeaderMargin(10);
                                 $pdf->SetFooterMargin(10);
                                 
                                 // set auto page breaks
                                 
                                 $pdf->SetAutoPageBreak(TRUE, 13);
                                 
                                 // set image scale factor
                                 
                                 
                                 $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                                 
                                 // set some language-dependent strings (optional)
                                 
                                 if (@file_exists(dirname(__FILE__).'/lang/eng.php')) 
                                 
                                 {
                                 
                                 require_once(dirname(__FILE__).'/lang/eng.php');
                                 
                                 $pdf->setLanguageArray($l);
                                 
                                 }
                                 
                                 
                                 
                                 // ---------------------------------------------------------
                                 // set default font subsetting mode
                                 
                                 
                                 $pdf->setFontSubsetting(true);
                                 
                                 
                                 // Set font
                                 // dejavusans is a UTF-8 Unicode font, if you only need to
                                 // print standard ASCII chars, you can use core fonts like
                                 // helvetica or times to reduce file size.
                                 //$pdf->SetFont('times', '', 12, '', true);
                                 
                                 
                                 $pdf->SetFont('times', '', 10, '', true);
                                 
                                 
                                 
                                 // Add a page
                                 // This method has several options, check the source code documentation for more information.
                                 
                                 
                                 $pdf->AddPage();
                                 $pageWidth = 200;
                                 $pageHeight = 283;
                                 $margin = 11;
                                 $date = date_create()->format('M-d-Y');
                                 
                                 
                                 //$pdf->Rect( $margin, $margin , $pageWidth - $margin , $pageHeight - $margin);
                                 // Line break
                                 // Line
                                 
                                 
                                 $pdf->Line(11, 284, 199, 284, '');
                                 
                                 
                                 // $quote_no = $form_post_data['PurchaseOrderNo'];
                                 // $podate=date("Y-m-d", strtotime($form_post_data['PurchaseOrderDate']));
                                 //$html = ob_get_clean();
                                 // $html = <<<EOD
                                 // EOD;
                                 //$pdf->writeHTML($html, true, false, false, false, '');
                                 //$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                                 $trows = ''; 


                                 // pdf end
                                 
                                      $WorkOrderNo= $form_post_data['WorkOrderNo'];
                                      $PdnOrderID= $form_post_data['PdnOrderID'];
                                      $EmployeeID= $form_post_data['EmployeeID'];
                                      $QualityID= $form_post_data['QualityID'];
                                      $NoofQuantity= $form_post_data['NoofQuantity'];
                                      $StartingProductionNo= $form_post_data['StartingProductionNo'];
                                      $EndProdNo= $form_post_data['EndProdNo'];
                                      $Rework= $form_post_data['Rework'];
                                      $DCRequired= $form_post_data['DCRequired'];
                                      $product_ID= $form_post_data['product_ID'];
                                      $ProcessID= $form_post_data['ProcessID'];
                                      $EmployeeType= $form_post_data['EmployeeType'];
                                      $ProductSize= $form_post_data['ProductSize'];
                                      $Thickness= $form_post_data['Thickness'];
                                      $Colour= $form_post_data['Colour'];
                                      $Design= $form_post_data['Design'];
                                      $CompletedDate=$CompletedDate;
                                      $SequenceMaterialIssued= $form_post_data['SequenceMaterialIssued'];
                                      $Details= $form_post_data['Details'];
                                      $Remarks= $form_post_data['Remarks'];
                                      $Status= $form_post_data['Status'];
                                      
                                        $sql_update="UPDATE $workorder_table set WorkOrderNo='$WorkOrderNo',
                                                                                 PdnOrderID='$PdnOrderID',
                                                                                 EmployeeID='$EmployeeID',
                                                                                 QualityID='$QualityID',
                                                                                 NoofQuantity='$NoofQuantity',
                                                                                 StartingProductionNo='$StartingProductionNo',
                                                                                 EndProdNo='$EndProdNo',
                                                                                 Rework='$Rework',
                                                                                 DCRequired='$DCRequired',
                                                                                 product_ID='$product_ID',
                                                                                 ProcessID='$ProcessID',
                                                                                 EmployeeType='$EmployeeType',
                                                                                 ProductSize='$ProductSize',
                                                                                 Thickness='$Thickness',
                                                                                 Colour='$Colour',
                                                                                 Design='$Design',
                                                                                 CompletedDate='$CompletedDate',
                                                                                 SequenceMaterialIssued='$SequenceMaterialIssued',
                                                                                 Details='$Details',
                                                                                 Remarks='$Remarks',
                                                                                 Status='$Status'
                                                                                 WHERE ID=$data";
                                        $stmt1 = $this->db->prepare($sql_update);
                                        $stmt1->execute();
                                        
                                        $workorder_detail_table = "SELECT $workorder_detail_table.ID,$workorder_detail_table.RMName,$workorder_detail_table.Quantity2,$workorder_detail_table.UnitName,$workorder_table.ID FROM $workorder_detail_table,$workorder_table where $workorder_table.ID=$workorder_detail_table.Workorder_ID AND $workorder_detail_table.Workorder_ID = $data ";
                            $stmt =$this->db->prepare( $workorder_detail_table);            
                            $stmt->execute();		
                            $workorder_detail_table_data = $stmt->fetchAll(2);
                            // var_dump($delDcproduct); 
                            $count = 1;
                            foreach($workorder_detail_table_data as $k=>$v){
                                $troww .=  '<tr>
                                <td style="width:50" align="left">'.$count.'</td>
                                <td style="width:251" align="left">'. $v['RMName'].'</td>
                                <td style="width:67" align="center">'. $v['Quantity2'].'  </td>
                                <td style="width:99" align="right">'.$v["UnitName"].'</td>
                                </tr>'; 
                                 $count++;
                            }
							
							for($i=1;$i<3;$i++){
                                $trowss.="<tr> <td></td> <td></td>"      
                                    . "<td></td>"
                                    . "<td></td>"
                                    . "<td></td>"
									. "<td></td>"
                                    . "</tr>";
                            }
							
							$workorder_tab = "SELECT $workorder_table.*,$productionorder_table.PdnOrderNo,$employee_table.EmpName,$supplier_table.SupplierName,$qualitycontrol_table.Qcno,$product_table.ProductName,$processmaster_table.ProcessName
                                              FROM $workorder_table
                                              LEFT JOIN $productionorder_table ON $productionorder_table.ID=$workorder_table.PdnOrderID
                                              LEFT JOIN $employee_table ON $employee_table.ID=$workorder_table.EmployeeID
                                              LEFT JOIN $supplier_table ON $supplier_table.ID=$workorder_table.Subcontractor_ID
                                              LEFT JOIN $qualitycontrol_table ON $qualitycontrol_table.ID=$workorder_table.QualityID
                                              LEFT JOIN $product_table ON $product_table.ID=$workorder_table.product_ID
                                              LEFT JOIN $processmaster_table ON $processmaster_table.ID=$workorder_table.ProcessID
                                              WHERE $workorder_table.ID=$data ";	
                            $stmt =$this->db->prepare($workorder_tab);            
                            $stmt->execute();	
                            $workorder_tab_fetch = $stmt->fetchAll(2);		
							
							$WorkOrderNo = $workorder_tab_fetch[0]['WorkOrderNo'];		
							$PdnOrderNo = $workorder_tab_fetch[0]['PdnOrderNo'];	
							$EmpName = $workorder_tab_fetch[0]['EmpName'];
							$SupplierName = $workorder_tab_fetch[0]['SupplierName'];
							$Qcno = $workorder_tab_fetch[0]['Qcno'];
							$NoofQuantity = $workorder_tab_fetch[0]['NoofQuantity'];
							$StartingProductionNo = $workorder_tab_fetch[0]['StartingProductionNo'];
							$EndProdNo = $workorder_tab_fetch[0]['EndProdNo'];	
                            
                            $Rework = $workorder_tab_fetch[0]['Rework'];		
							$DCRequired = $workorder_tab_fetch[0]['DCRequired'];	
							$ProductName = $workorder_tab_fetch[0]['ProductName'];
							$ProcessName = $workorder_tab_fetch[0]['ProcessName'];
							$EmployeeType = $workorder_tab_fetch[0]['EmployeeType'];
							$ProductSize = $workorder_tab_fetch[0]['ProductSize'];
							$Thickness = $workorder_tab_fetch[0]['Thickness'];
							$Colour = $workorder_tab_fetch[0]['Colour'];
                            $Design = $workorder_tab_fetch[0]['Design'];
							$CompletedDate = $workorder_tab_fetch[0]['CompletedDate'];
							$SequenceMaterialIssued = $workorder_tab_fetch[0]['SequenceMaterialIssued'];
                            $Details = $workorder_tab_fetch[0]['Details'];
							$Remarks = $workorder_tab_fetch[0]['Remarks'];
							$Status = $workorder_tab_fetch[0]['Status'];

                            $html = <<<EOD
				
<table cellspacing="0" cellpadding="4">
<tr>
  <td></td>
  <td align="right">
    <b style="font-size:200%; color:#660066;"> Meena Fiberglas Industries </b><br>
    <b>An IRIS Certified Company</b><br>
    <b>An ISO 9001:2015 Certified Company</b><br>
    R.S.No. 151/3, Cuddalore-Pondy Main Road,<br>
    Kattukuppam, Puducherry-607 402,<br>
    Phone: 0413-2611009 Mobile:7358019212 ,<br>
    e-mail:sales@meenafibres.com,
  </td>
</tr>
<tr>
  <td width="200">Date : <b>$date</b><br></td>
</tr>
</table>
<table cellspacing="0" cellpadding="3" border="1">
    <tr>
        <td width="165" align="left" >WorkOrderNo</td>

        <td width="165" align="left" >$WorkOrderNo</td>
        <td width="165" align="left" >Production Order no</td>

        <td width="165" align="left" >$PdnOrderNo</td>
    </tr>
    <!-- More table rows... -->
    <tr>
        <td width="165" align="left" >Employee Name</td>

        <td width="165" align="left" >$EmpName</td>
        <td width="165" align="left" >Supplier Name</td>

        <td width="165" align="left" >$SupplierName</td>
    </tr>
   <tr>
     <td width="165" align="left" >Qcno</td>
     
     <td width="165" align="left" >$Qcno</td>
     <td width="165" align="left" >No of Quantity</td>
     
     <td width="165" align="left" >$NoofQuantity</td>
  </tr>
<tr>
     <td width="165" align="left" >Starting Production No</td>
     
     <td width="165" align="left" >$StartingProductionNo</td>
     <td width="165" align="left" >Ending Production No</td>
    
     <td width="165" align="left" >$EndProdNo</td>
  </tr>
<tr>
     <td width="165" align="left" >Rework</td>
    
     <td width="165" align="left" >$Rework</td>
     <td width="165" align="left" >DC Required</td>
   
     <td width="165" align="left" >$DCRequired</td>
  </tr>
<tr>
     <td width="165" align="left" >ProductName</td>
    
     <td width="165" align="left" >$ProductName</td>
     <td width="165" align="left" >ProcessName</td>
    
     <td width="165" align="left" >$ProcessName</td>
  </tr>
<tr>
     <td width="165" align="left" >Employee Type</td>
    
     <td width="165" align="left" >$EmployeeType</td>
     <td width="165" align="left" >ProductSize</td>
     
     <td width="165" align="left" >$ProductSize</td>
  </tr>
<tr>
     <td width="165" align="left" >Thickness</td>
     
     <td width="165" align="left" >$Thickness</td>
     <td width="165" align="left" >Colour</td>
   
     <td width="165" align="left" >$Colour</td>
  </tr>
<tr>
     <td width="165" align="left" >Design</td>
     
     <td width="165" align="left" >$Design</td>
     <td width="165" align="left" >Completed Date</td>
    
     <td width="165" align="left" >$CompletedDate</td>
  </tr>
<tr>
     <td width="165" align="left" >Sequence Material Issued</td>
     
     <td width="165" align="left" >$SequenceMaterialIssued</td>
     <td width="165" align="left" >Details</td>
     
     <td width="165" align="left" >$Details</td>
  </tr>
<tr>
     <td width="165" align="left" >Remarks</td>
    
     <td width="165" align="left" >$Remarks</td>
     <td width="165" align="left" >Status</td>
     
     <td width="165" align="left" >$Status</td>
  </tr>
</table>		
<table cellspacing="0" cellpadding="4" border="1">		
       <tr>
       <td width="50" align="center"><b>S.No</b></td>
       <td width="251" align="center"><b>Rawmaterial Name</b></td>
       <td width="67" align="center"><b>Quantity</b></td>
       <td width="99" align="center"><b>Unit Name</b></td>
    
      </tr>
       
	    {$troww}    
		
		
		
       </table> 	
       
<table cellspacing="0" cellpadding="4">
    <tr>
        <td width="620" height="100" align="right">For Meena Fibre Glass</td>
        <td  width="40"></td>
    </tr>
    <tr>
        <td  width="40"></td>
        <td width="290" align="left" > Signature of the Employee </td>
        <td width="290" align="right" > Authorised Signatory </td>
        <td  width="40"></td>
    </tr>
</table>
EOD;


                                  $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                                  $ht = ob_get_clean();
                                  $pdf->writeHTMLCell(0, 0, '', '', $ht, 0, 1, 0, true, '', true);
                                  $htl = ob_get_clean();
                                  $htl='
                                  <table cellspacing="" cellpadding="1">
                                         <tr>
                                             <td>
                                              <p style="text-indent: 50px;">*Since this is an computer generated document, manual signature is not required.</p><p></p>
                                             </td>
                                         </tr> 
                                     </table>';
                                  
                                  $pdf->writeHTMLCell(0, 0, '', '', $htl, 0, 1, 0, true, '', true); 
                                  ob_end_clean();// at the end of your script
                                  
 $pdf->Output($_SERVER['DOCUMENT_ROOT'] ."/resource/workorderpdf/Invoice".$data.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg5/resource/workorderpdf/Invoice".$lastInsertedID.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg5/resource/workorderpdf/Invoice".$lastInsertedID.".pdf", 'I');
$this->ses->set('pdffile', "/resource/workorderpdf/Invoice".$data.".pdf");
								  
								   // TCPDF END
						                       
							// pdf End
                                   
                                        $this->tpl->set('message', 'Workorder form edited successfully!');   
                                                                                                                                      
                                        // $this->tpl->set('label', 'List');
                                        // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                        header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/workorder');
                                        } catch (Exception $exc) {
                                         //edit failed option
                                        $this->tpl->set('message', 'Failed to edit, try again!');
                                        $this->tpl->set('FmData', $form_post_data);
                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/workorder_design_form.php'));
                                        }
            
                                break;
            
                            case 'addsubmit':
                                 if (isset($crud_string)) {
             
                                    $form_post_data = $dbutil->arrFltr($_POST);
                                    
                                  // var_dump($_POST);
                                 
                                  
                                  $CompletedDate=!empty($form_post_data['CompletedDate'])?date("Y-m-d", strtotime($form_post_data['CompletedDate'])):'';
                                   
                                    $entry_count = 1;
                                    $No_of_quantity=$form_post_data['NoofQuantity'];
                                   
                                   if (isset($form_post_data['WorkOrderNo'])) {
                                       
                                                    $val = "'" . $form_post_data['WorkOrderNo'] . "'," .
                                                     "'" . $form_post_data['PdnOrderID'] . "'," .
                                                     "'" . $form_post_data['EmployeeID'] . "'," .
                                                     "'" . $form_post_data['Subcontractor_ID'] . "'," .
                                                     "'" . $form_post_data['QualityID'] . "'," .
                                                     "'" . $form_post_data['NoofQuantity'] . "'," .
                                                     "'" . $form_post_data['StartingProductionNo'] . "'," .
                                                     "'" . $form_post_data['EndProdNo'] . "'," .
                                                     "'" . $form_post_data['Rework'] . "'," .
                                                     "'" . $form_post_data['DCRequired'] . "'," .
                                                     "'" . $form_post_data['product_ID'] . "'," .
                                                     "'" . $form_post_data['ProcessID'] . "'," .
                                                     "'" . $form_post_data['EmployeeType'] . "'," .
                                                     "'" . $form_post_data['ProductSize'] . "'," .
                                                     "'" . $form_post_data['Thickness'] . "'," .
                                                     "'" . $form_post_data['Colour'] . "'," .
                                                     "'" . $form_post_data['Design'] . "'," .
                                                     "'" . $CompletedDate . "'," .
                                                     "'" . $form_post_data['SequenceMaterialIssued'] . "'," .
                                                     "'" . $form_post_data['Details'] . "'," .
                                                     "'" . $form_post_data['Remarks'] . "'," .
                                                     "'" . $form_post_data['Status'] . "'," .

                                                     "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                     "'" .  $this->ses->get('user')['ID'] . "'";
            
                                         $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "workorder`
                                                        (
                                                        `WorkOrderNo`, 
                                                        `PdnOrderID`, 
                                                        `EmployeeID`,
                                                        `Subcontractor_ID`,
                                                        `QualityID`,
                                                        `NoofQuantity`,
                                                        `StartingProductionNo`,
                                                        `EndProdNo`,
                                                        `Rework`,
                                                        `DCRequired`, 
                                                        `product_ID`, 
                                                        `ProcessID`, 
                                                        `EmployeeType`,
                                                        `ProductSize`,
                                                        `Thickness`,
                                                        `Colour`,
                                                        `Design`,
                                                        `CompletedDate`, 
                                                        `SequenceMaterialIssued`,
                                                        `Details`,
                                                        `Remarks`, 
                                                        `Status`,
                                                        `entity_ID`,
                                                        `users_ID`
                                                        ) 
                                                     VALUES ( $val )";
                                              $stmt = $this->db->prepare($sql);
                                            //  $stmt->execute();
                                            if($stmt->execute()){
                                                
                                                $lastInsertedID = $this->db->lastInsertId();
                                                
                                                 // pdf start
                               
                                 require_once('TCPDF/proformaChallen.php');
                                 ob_start(); // at the beggining of your script
                                 
                                 // create new PDF document
                                 
                                 $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                                 
                                 // set document information
                                 
                                 $pdf->SetCreator(PDF_CREATOR);
                                 $pdf->SetAuthor('Sreenidhi Enterprises');
                                 // $report_title = "Delivery Challan";
                                 // $pdf->SetTitle($report_title);
                                 $pdf->SetSubject('Corrective And Preventive Action');
                                 $pdf->SetKeywords('Quotation,Invoice');
                                 
                                 
                                 // set default header data
                                 
                                 
                                 $capa_date = date('d.m.Y',strtotime($capa_data['AuditDateTime']));
                                 $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.'.', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
                                 $pdf->setFooterData(array(0,64,0), array(0,64,128));
                                 
                                 // set header and footer fonts
                                 
                                 $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
                                 $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
                                 
                                 // set default monospaced font
                                 
                                 $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
                                 
                                 
                                 // set margins
                                 
                                 $pdf->SetMargins(10, 5, 10);
                                 $pdf->SetHeaderMargin(10);
                                 $pdf->SetFooterMargin(10);
                                 
                                 // set auto page breaks
                                 
                                 $pdf->SetAutoPageBreak(TRUE, 13);
                                 
                                 // set image scale factor
                                 
                                 
                                 $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
                                 
                                 // set some language-dependent strings (optional)
                                 
                                 if (@file_exists(dirname(__FILE__).'/lang/eng.php')) 
                                 
                                 {
                                 
                                 require_once(dirname(__FILE__).'/lang/eng.php');
                                 
                                 $pdf->setLanguageArray($l);
                                 
                                 }
                                 
                                 
                                 
                                 // ---------------------------------------------------------
                                 // set default font subsetting mode
                                 
                                 
                                 $pdf->setFontSubsetting(true);
                                 
                                 
                                 // Set font
                                 // dejavusans is a UTF-8 Unicode font, if you only need to
                                 // print standard ASCII chars, you can use core fonts like
                                 // helvetica or times to reduce file size.
                                 //$pdf->SetFont('times', '', 12, '', true);
                                 
                                 
                                 $pdf->SetFont('times', '', 10, '', true);
                                 
                                 
                                 
                                 // Add a page
                                 // This method has several options, check the source code documentation for more information.
                                 
                                 
                                 $pdf->AddPage();
                                 $pageWidth = 200;
                                 $pageHeight = 283;
                                 $margin = 11;
                                 $date = date_create()->format('M-d-Y');
                                 
                                 
                                 //$pdf->Rect( $margin, $margin , $pageWidth - $margin , $pageHeight - $margin);
                                 // Line break
                                 // Line
                                 
                                 
                                 $pdf->Line(11, 284, 199, 284, '');
                                 
                                 
                                 // $quote_no = $form_post_data['PurchaseOrderNo'];
                                 // $podate=date("Y-m-d", strtotime($form_post_data['PurchaseOrderDate']));
                                 //$html = ob_get_clean();
                                 // $html = <<<EOD
                                 // EOD;
                                 //$pdf->writeHTML($html, true, false, false, false, '');
                                 //$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                                 $trows = ''; 


                                 // pdf end
                                              
                                               FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
    
                                                    $RMName =$form_post_data['RMName' . $entry_count]; 
                                                    $rawmaterial_ID =$form_post_data['rawmaterial_ID' . $entry_count];
                                                    $Quantity1 =$form_post_data['Quantity1' . $entry_count];
                                                    $UnitName =$form_post_data['UnitName' . $entry_count];
                                                    $unit_ID =$form_post_data['unit_ID' . $entry_count];
                                            
                                             if(!empty($RMName) && !empty($Quantity1)){
                                                 
                                                    $vals = "'" . $lastInsertedID . "'," .
                                                            "'" . $RMName . "'," .
                                                            "'" . $rawmaterial_ID . "'," .
                                                            "'" . $Quantity1 . "'," .
                                                            "'" . $Quantity1*$No_of_quantity . "'," .
                                                            "'" . $UnitName . "'," .
                                                            "'" . $unit_ID . "'";
                                      
                                                $sql2 = "INSERT INTO $workorder_detail_table
                                                         ( 
                                                          `Workorder_ID`, 
                                                          `RMName`,
                                                          `rawmaterial_ID`,
                                                          `Quantity1`,
                                                          `Quantity2`,
                                                          `UnitName`,
                                                          `unit_ID`
                                                          ) 
                                                  VALUES ($vals)";
                                                  $stmt = $this->db->prepare($sql2);
                                                  $stmt->execute();  
                                             }
                                           }
                                           
                                            $workorder_detail_table = "SELECT $workorder_detail_table.ID,$workorder_detail_table.RMName,$workorder_detail_table.Quantity2,$workorder_detail_table.UnitName,$workorder_table.ID FROM $workorder_detail_table,$workorder_table where $workorder_table.ID=$workorder_detail_table.Workorder_ID AND $workorder_detail_table.Workorder_ID = $lastInsertedID ";
                            $stmt =$this->db->prepare( $workorder_detail_table);            
                            $stmt->execute();		
                            $workorder_detail_table_data = $stmt->fetchAll(2);
                            // var_dump($delDcproduct); 
                            $count = 1;
                            foreach($workorder_detail_table_data as $k=>$v){
                                $troww .=  '<tr>
                                <td style="width:50" align="left">'.$count.'</td>
                                <td style="width:251" align="left">'. $v['RMName'].'</td>
                                <td style="width:67" align="center">'. $v['Quantity2'].'  </td>
                                <td style="width:99" align="right">'.$v["UnitName"].'</td>
                                </tr>'; 
                                 $count++;
                            }
							
							for($i=1;$i<3;$i++){
                                $trowss.="<tr> <td></td> <td></td>"      
                                    . "<td></td>"
                                    . "<td></td>"
                                    . "<td></td>"
									. "<td></td>"
                                    . "</tr>";
                            }
							
							$workorder_tab = "SELECT $workorder_table.*,$productionorder_table.PdnOrderNo,$employee_table.EmpName,$supplier_table.SupplierName,$qualitycontrol_table.Qcno,$product_table.ProductName,$processmaster_table.ProcessName
                                              FROM $workorder_table
                                              LEFT JOIN $productionorder_table ON $productionorder_table.ID=$workorder_table.PdnOrderID
                                              LEFT JOIN $employee_table ON $employee_table.ID=$workorder_table.EmployeeID
                                              LEFT JOIN $supplier_table ON $supplier_table.ID=$workorder_table.Subcontractor_ID
                                              LEFT JOIN $qualitycontrol_table ON $qualitycontrol_table.ID=$workorder_table.QualityID
                                              LEFT JOIN $product_table ON $product_table.ID=$workorder_table.product_ID
                                              LEFT JOIN $processmaster_table ON $processmaster_table.ID=$workorder_table.ProcessID
                                              WHERE $workorder_table.ID=$lastInsertedID ";	
                            $stmt =$this->db->prepare($workorder_tab);            
                            $stmt->execute();	
                            $workorder_tab_fetch = $stmt->fetchAll(2);		
							
							$WorkOrderNo = $workorder_tab_fetch[0]['WorkOrderNo'];		
							$PdnOrderNo = $workorder_tab_fetch[0]['PdnOrderNo'];	
							$EmpName = $workorder_tab_fetch[0]['EmpName'];
							$SupplierName = $workorder_tab_fetch[0]['SupplierName'];
							$Qcno = $workorder_tab_fetch[0]['Qcno'];
							$NoofQuantity = $workorder_tab_fetch[0]['NoofQuantity'];
							$StartingProductionNo = $workorder_tab_fetch[0]['StartingProductionNo'];
							$EndProdNo = $workorder_tab_fetch[0]['EndProdNo'];	
                            
                            $Rework = $workorder_tab_fetch[0]['Rework'];		
							$DCRequired = $workorder_tab_fetch[0]['DCRequired'];	
							$ProductName = $workorder_tab_fetch[0]['ProductName'];
							$ProcessName = $workorder_tab_fetch[0]['ProcessName'];
							$EmployeeType = $workorder_tab_fetch[0]['EmployeeType'];
							$ProductSize = $workorder_tab_fetch[0]['ProductSize'];
							$Thickness = $workorder_tab_fetch[0]['Thickness'];
							$Colour = $workorder_tab_fetch[0]['Colour'];
                            $Design = $workorder_tab_fetch[0]['Design'];
							$CompletedDate = $workorder_tab_fetch[0]['CompletedDate'];
							$SequenceMaterialIssued = $workorder_tab_fetch[0]['SequenceMaterialIssued'];
                            $Details = $workorder_tab_fetch[0]['Details'];
							$Remarks = $workorder_tab_fetch[0]['Remarks'];
							$Status = $workorder_tab_fetch[0]['Status'];

                            $html = <<<EOD
				
<table cellspacing="0" cellpadding="4">
<tr>
  <td></td>
  <td align="right">
    <b style="font-size:200%; color:#660066;"> Meena Fiberglas Industries </b><br>
    <b>An IRIS Certified Company</b><br>
    <b>An ISO 9001:2015 Certified Company</b><br>
    R.S.No. 151/3, Cuddalore-Pondy Main Road,<br>
    Kattukuppam, Puducherry-607 402,<br>
    Phone: 0413-2611009 Mobile:7358019212 ,<br>
    e-mail:sales@meenafibres.com,
  </td>
</tr>
<tr>
  <td width="200">Date : <b>$date</b><br></td>
</tr>
</table>
<table cellspacing="0" cellpadding="3" border="1">
    <tr>
        <td width="165" align="left" >WorkOrderNo</td>

        <td width="165" align="left" >$WorkOrderNo</td>
        <td width="165" align="left" >Production Order no</td>

        <td width="165" align="left" >$PdnOrderNo</td>
    </tr>
    <!-- More table rows... -->
    <tr>
        <td width="165" align="left" >Employee Name</td>

        <td width="165" align="left" >$EmpName</td>
        <td width="165" align="left" >Supplier Name</td>

        <td width="165" align="left" >$SupplierName</td>
    </tr>
   <tr>
     <td width="165" align="left" >Qcno</td>
     
     <td width="165" align="left" >$Qcno</td>
     <td width="165" align="left" >No of Quantity</td>
     
     <td width="165" align="left" >$NoofQuantity</td>
  </tr>
<tr>
     <td width="165" align="left" >Starting Production No</td>
     
     <td width="165" align="left" >$StartingProductionNo</td>
     <td width="165" align="left" >Ending Production No</td>
    
     <td width="165" align="left" >$EndProdNo</td>
  </tr>
<tr>
     <td width="165" align="left" >Rework</td>
    
     <td width="165" align="left" >$Rework</td>
     <td width="165" align="left" >DC Required</td>
   
     <td width="165" align="left" >$DCRequired</td>
  </tr>
<tr>
     <td width="165" align="left" >ProductName</td>
    
     <td width="165" align="left" >$ProductName</td>
     <td width="165" align="left" >ProcessName</td>
    
     <td width="165" align="left" >$ProcessName</td>
  </tr>
<tr>
     <td width="165" align="left" >Employee Type</td>
    
     <td width="165" align="left" >$EmployeeType</td>
     <td width="165" align="left" >ProductSize</td>
     
     <td width="165" align="left" >$ProductSize</td>
  </tr>
<tr>
     <td width="165" align="left" >Thickness</td>
     
     <td width="165" align="left" >$Thickness</td>
     <td width="165" align="left" >Colour</td>
   
     <td width="165" align="left" >$Colour</td>
  </tr>
<tr>
     <td width="165" align="left" >Design</td>
     
     <td width="165" align="left" >$Design</td>
     <td width="165" align="left" >Completed Date</td>
    
     <td width="165" align="left" >$CompletedDate</td>
  </tr>
<tr>
     <td width="165" align="left" >Sequence Material Issued</td>
     
     <td width="165" align="left" >$SequenceMaterialIssued</td>
     <td width="165" align="left" >Details</td>
     
     <td width="165" align="left" >$Details</td>
  </tr>
<tr>
     <td width="165" align="left" >Remarks</td>
    
     <td width="165" align="left" >$Remarks</td>
     <td width="165" align="left" >Status</td>
     
     <td width="165" align="left" >$Status</td>
  </tr>
</table>		
<table cellspacing="0" cellpadding="4" border="1">		
       <tr>
       <td width="50" align="center"><b>S.No</b></td>
       <td width="251" align="center"><b>Rawmaterial Name</b></td>
       <td width="67" align="center"><b>Quantity</b></td>
       <td width="99" align="center"><b>Unit Name</b></td>
    
      </tr>
       
	    {$troww}    
		
		
		
       </table> 	
       
<table cellspacing="0" cellpadding="4">
    <tr>
        <td width="620" height="100" align="right">For Meena Fibre Glass</td>
        <td  width="40"></td>
    </tr>
    <tr>
        <td  width="40"></td>
        <td width="290" align="left" > Signature of the Employee </td>
        <td width="290" align="right" > Authorised Signatory </td>
        <td  width="40"></td>
    </tr>
</table>
EOD;


                                  $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
                                  $ht = ob_get_clean();
                                  $pdf->writeHTMLCell(0, 0, '', '', $ht, 0, 1, 0, true, '', true);
                                  $htl = ob_get_clean();
                                  $htl='
                                  <table cellspacing="" cellpadding="1">
                                         <tr>
                                             <td>
                                              <p style="text-indent: 50px;">*Since this is an computer generated document, manual signature is not required.</p><p></p>
                                             </td>
                                         </tr> 
                                     </table>';
                                  
                                  $pdf->writeHTMLCell(0, 0, '', '', $htl, 0, 1, 0, true, '', true); 
                                  ob_end_clean();// at the end of your script
                                  
 $pdf->Output($_SERVER['DOCUMENT_ROOT'] ."/resource/workorderpdf/Invoice".$lastInsertedID.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg5/resource/workorderpdf/Invoice".$lastInsertedID.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg5/resource/workorderpdf/Invoice".$lastInsertedID.".pdf", 'I');
$this->ses->set('pdffile', "/resource/workorderpdf/Invoice".$lastInsertedID.".pdf");
								  
								   // TCPDF END
						                       
							// pdf End
							
                                          }
                                    }
                                    $this->tpl->set('mode', 'add');
                                    $this->tpl->set('message', '- Success -');
                                    // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                                    header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/workorder');
                                                                                                                    
                                 } else {
                                        //edit option
                                        //if submit failed to insert form
                                        $this->tpl->set('message', 'Failed to submit!');
                                        $this->tpl->set('FmData', $form_post_data);
                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/workorder_design_form.php'));
                                 }
                                break;
                            case 'add':
                                $this->tpl->set('mode', 'add');
                                $this->tpl->set('page_header', 'Workorder');
                                $WorkOrderNo=$dbutil->keyGeneration('workorder','WON','','WorkOrderNo');
                                $this->tpl->set('WorkOrderNo', $WorkOrderNo);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/workorder_design_form.php'));
                                break;
            
                            default:
                                /*
                                 * List form
                                 */
                                 
                                ////////////////////start//////////////////////////////////////////////
                                
                       //bUILD SQL 
                        $whereString = '';
                     $colArr = array(
                            "$workorder_table.ID",
                            "$workorder_table.WorkOrderNo",
                            "$productionorder_table.PdnOrderNo",
                            "$product_table.ProductName",
                            "$processmaster_table.ProcessName",
                            "$workorder_table.NoofQuantity",
                            "$workorder_table.Status"
            
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
                         $whereString ="ORDER BY $workorder_table.ID DESC";
                       }
                       
                          
                   $sql = "SELECT " 
                                . implode(',',$colArr)
                                . " FROM $workorder_table,$productionorder_table,$product_table,$processmaster_table"
                                . " WHERE "
                                . " $workorder_table.entity_ID = $entityID AND" 
                                . " $productionorder_table.ID = $workorder_table.PdnOrderID AND" 
                                . " $product_table.ID = $workorder_table.product_ID AND" 
                                . " $processmaster_table.ID = $workorder_table.ProcessID" 
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
                     
                     
                     
                        $this->tpl->set('table_columns_label_arr', array('ID','WorkOrderNo','Production Order No','Product Name','Process Name','No Of Quantity','Status'));
                        
                        /*
                         * selectColArr for filter form
                         */
                        
                        $this->tpl->set('selectColArr',$colArr);
                        $this->tpl->set('dcpdf3','Generate Pdf');
                                    
                        /*
                         * set pagination template
                         */
                        $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                               
                        //////////////////////close//////////////////////////////////////  
                                 
                                include_once $this->tpl->path . '/factory/form/crud_form_workorder_request.php';
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

                 public function cusWorkOrder(){
    
                    if ($this->crg->get('wp') || $this->crg->get('rp')) {
                             
                            ////////////////////////////////////////////////////////////////////////////////
                            //////////////////////////////access condition applied//////////////////////////
                            ////////////////////////////////////////////////////////////////////////////////    
                                        
                            include_once 'util/DBUTIL.php';
                            $dbutil = new DBUTIL($this->crg);
                             
                            $entityID = $this->ses->get('user')['entity_ID'];
                            $userID = $this->ses->get('user')['ID'];
                            
                            $customized_workorder_table = $this->crg->get('table_prefix') . 'customized_workorder';
                            $cus_workorder_detail_table = $this->crg->get('table_prefix') . 'cus_workorder_detail';
                            //$productionorder_table = $this->crg->get('table_prefix') . 'productionorder';
                            $employee_table = $this->crg->get('table_prefix') . 'employee';
                            $supplier_table = $this->crg->get('table_prefix') . 'supplier';
                            $unit_table = $this->crg->get('table_prefix') . 'unit';
                            $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';

                            $username=$this->ses->get('user')['user_nicename'];
                            $this->tpl->set('preparedby', $username);
                            
                            $Employeetype_data = array(array("ID"=>"1","Title"=>"Employee"),array("ID"=>"2","Title"=>"Sub Contractor"));
                            $this->tpl->set('Employeetype_data', $Employeetype_data);
    
                            $employee_sql = "SELECT ID,EmpName FROM $employee_table";
                            $stmt = $this->db->prepare($employee_sql);            
                            $stmt->execute();
                            $employee_data  = $stmt->fetchAll();	
                            $this->tpl->set('employee_data', $employee_data);

                            $employee_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE SupplierType='Sub contractor'";
                            $stmt = $this->db->prepare($employee_sql);            
                            $stmt->execute();
                            $subcontractor_data  = $stmt->fetchAll();	
                            $this->tpl->set('subcontractor_data', $subcontractor_data);

                            $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
                            $stmt = $this->db->prepare($pdt_sql);            
                            $stmt->execute();
                            $unit_data  = $stmt->fetchAll();	
                            $this->tpl->set('unit_data', $unit_data);

                            $rawmaterial_sql = "SELECT ID,RMName FROM $rawmaterial_table";
                            $stmt = $this->db->prepare($rawmaterial_sql);            
                            $stmt->execute();
                            $rawmaterial_data  = $stmt->fetchAll();	
                            $this->tpl->set('rawmaterial_data', $rawmaterial_data);
                            
                            $this->tpl->set('page_title', 'Customized WorkOrder');	          
                            $this->tpl->set('page_header', 'Customized WorkOrder');
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
                                     
                                     $sqldetdelete="Delete from $customized_workorder_table
                                                        where $customized_workorder_table.ID=$data"; 
                                        $stmt = $this->db->prepare($sqldetdelete);            
                                        
                                        if($stmt->execute()){
                                        $this->tpl->set('message', 'Customized Work Order form deleted successfully');
                                                                                                                                      
                                        //$this->tpl->set('label', 'List');
                                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                        header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/cusWorkOrder');
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
                                    
                                    $sqlsrr = "SELECT *
                                    FROM $customized_workorder_table LEFT JOIN $cus_workorder_detail_table ON $cus_workorder_detail_table.Cusworkorder_ID=$customized_workorder_table.ID
                                    WHERE 
                                    $customized_workorder_table.ID = $data";   
                                    $cusworkorder_data = $dbutil->getSqlData($sqlsrr);
                                   
                                
                                    //edit option     
                                    $this->tpl->set('message', 'You can view Customized work order form');
                                    $this->tpl->set('page_header', 'Customized Work Order');
                                    $this->tpl->set('FmData', $cusworkorder_data); 
                                    
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customized_workorder_design_form.php'));                    
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
                                             
                                //   $sqlsrr = "SELECT  * FROM `$employee_salary_table` WHERE `$employee_salary_table.ID`= `$data`";                    
                                //   $employee_salary_data = $dbutil->getSqlData($sqlsrr); 
                                   
                                $sqlsrr = "SELECT *
                                     FROM $customized_workorder_table LEFT JOIN $cus_workorder_detail_table ON $cus_workorder_detail_table.Cusworkorder_ID=$customized_workorder_table.ID
                                     WHERE 
                                     $customized_workorder_table.ID = $data";   
                                     $cusworkorder_data = $dbutil->getSqlData($sqlsrr);
                                   
                                    
                                    //edit option 
                
                                    
                                    $this->tpl->set('message', 'You can edit Customized Work Order form');
                                    $this->tpl->set('page_header', 'Customized Work Order');
                                    $this->tpl->set('FmData', $cusworkorder_data); 
                                    
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customized_workorder_design_form.php'));                    
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
                        
                                    $CompletedDate=!empty($form_post_data['CompletedDate'])?date("Y-m-d", strtotime($form_post_data['CompletedDate'])):'';
                                                               
                                    try{
                                          $WorkOrderNo= $form_post_data['WorkOrderNo'];
                                          $EmployeeID= $form_post_data['EmployeeID'];
                                          $DCRequired= $form_post_data['DCRequired'];
                                          $ProductName= $form_post_data['ProductName'];
                                          $NoOfQuantity= $form_post_data['NoOfQuantity'];
                                          $Process= $form_post_data['Process'];
                                          $EmployeeType= $form_post_data['EmployeeType'];
                                          $ProductSize= $form_post_data['ProductSize'];
                                          $Thickness= $form_post_data['Thickness'];
                                          $Colour= $form_post_data['Colour'];
                                          $Design= $form_post_data['Design'];
                                          $Quantity= $form_post_data['Quantity'];
                                          $CompletedDate=$CompletedDate;
                                          $SequenceMaterialIssued= $form_post_data['SequenceMaterialIssued'];
                                          $Details= $form_post_data['Details'];
                                          $Remarks= $form_post_data['Remarks'];
                                          
                                          $sql_update="UPDATE $customized_workorder_table set WorkOrderNo='$WorkOrderNo',
                                                                                     EmployeeID='$EmployeeID',
                                                                                     DCRequired='$DCRequired',
                                                                                     ProductName='$ProductName',
                                                                                     NoOfQuantity='$NoOfQuantity',
                                                                                     Process='$Process',
                                                                                     EmployeeType='$EmployeeType',
                                                                                     ProductSize='$ProductSize',
                                                                                     Thickness='$Thickness',
                                                                                     Colour='$Colour',
                                                                                     Design='$Design',
                                                                                     Quantity='$Quantity',
                                                                                     CompletedDate='$CompletedDate',
                                                                                     SequenceMaterialIssued='$SequenceMaterialIssued',
                                                                                     Details='$Details',
                                                                                     Remarks='$Remarks'
                                                                                     WHERE ID=$data";
                                            $stmt1 = $this->db->prepare($sql_update);
                                            $stmt1->execute();


                                            $maxCount = $form_post_data['maxCount'];			   
                      
                                            $sql3 = "DELETE FROM $cus_workorder_detail_table WHERE Cusworkorder_ID=$data";
                                            $stmt3 = $this->db->prepare($sql3);
                                            //$is_delete = $stmt3->execute();
                                            // var_dump($is_delete);die;
                                          if($stmt3->execute()){
                                              
                                            FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                                   
                                                $Rawmaterial_ID = $form_post_data['Field2_'.$entry_count];
                                                $Quantity = $form_post_data['Field3_'.$entry_count];
                                                $unit_ID = $form_post_data['unit_'.$entry_count];
                                              
                                              if(!empty($Rawmaterial_ID)){
                                                $vals = "'" . $data . "'," .
                                                        "'" . $Rawmaterial_ID . "'," .
                                                        "'" . $Quantity . "'," .
                                                        "'" . $unit_ID . "'" ;
                  
                                                 $sql2 = "INSERT INTO $cus_workorder_detail_table
                                                        ( 
                                                         `Cusworkorder_ID`, 
                                                         `Rawmaterial_ID`, 
                                                         `Quantity`,
                                                         `unit_ID`
                                                        ) 
                                                VALUES ($vals)";
                                                $stmt = $this->db->prepare($sql2);
                                                $stmt->execute();
                                              }
                                                
                                            //increment here
                                            
                                            }
                                          }
                                       
                                            $this->tpl->set('message', 'Customized Workorder form edited successfully!');   
                                                                                                                                          
                                            // $this->tpl->set('label', 'List');
                                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                            header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/cusWorkOrder');
                                            } catch (Exception $exc) {
                                             //edit failed option
                                            $this->tpl->set('message', 'Failed to edit, try again!');
                                            $this->tpl->set('FmData', $form_post_data);
                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/customized_workorder_design_form.php'));
                                            }
                
                                    break;
                
                                case 'addsubmit':
                                     if (isset($crud_string)) {
                 
                                        $form_post_data = $dbutil->arrFltr($_POST);
                                        
                                      // var_dump($_POST);
                                     
                                      
                                      $CompletedDate=!empty($form_post_data['CompletedDate'])?date("Y-m-d", strtotime($form_post_data['CompletedDate'])):'';
                                       
                                        $entry_count = 1;
                                       
                                       if (isset($form_post_data['WorkOrderNo'])) {
                                           
                                                        $val = "'" . $form_post_data['WorkOrderNo'] . "'," .
                                                         "'" . $form_post_data['EmployeeID'] . "'," .
                                                         "'" . $form_post_data['Subcontractor_ID'] . "'," .
                                                         "'" . $form_post_data['DCRequired'] . "'," .
                                                         "'" . $form_post_data['ProductName'] . "'," .
                                                         "'" . $form_post_data['NoOfQuantity'] . "'," .
                                                         "'" . $form_post_data['Process'] . "'," .
                                                         "'" . $form_post_data['EmployeeType'] . "'," .
                                                         "'" . $form_post_data['ProductSize'] . "'," .
                                                         "'" . $form_post_data['Thickness'] . "'," .
                                                         "'" . $form_post_data['Colour'] . "'," .
                                                         "'" . $form_post_data['Design'] . "'," .
                                                         "'" . $form_post_data['Quantity'] . "'," .
                                                         "'" . $CompletedDate . "'," .
                                                         "'" . $form_post_data['SequenceMaterialIssued'] . "'," .
                                                         "'" . $form_post_data['Details'] . "'," .
                                                         "'" . $form_post_data['Remarks'] . "'," .
    
                                                         "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                         "'" .  $this->ses->get('user')['ID'] . "'";
                
                                             $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "customized_workorder`
                                                            (
                                                            `WorkOrderNo`, 
                                                            `EmployeeID`,
                                                            `Subcontractor_ID`,
                                                            `DCRequired`, 
                                                            `ProductName`, 
                                                            `NoOfQuantity`, 
                                                            `Process`, 
                                                            `EmployeeType`,
                                                            `ProductSize`,
                                                            `Thickness`,
                                                            `Colour`,
                                                            `Design`,
                                                            `Quantity`, 
                                                            `CompletedDate`, 
                                                            `SequenceMaterialIssued`,
                                                            `Details`,
                                                            `Remarks`, 
                                                            `entity_ID`,
                                                            `users_ID`
                                                            ) 
                                                         VALUES ( $val )";
                                                  $stmt = $this->db->prepare($sql);
            
                                                  if ($stmt->execute()) { 
                                                    //echo '<pre>';
                                                   // print_r($_POST);die;
                                                       $lastInsertedID = $this->db->lastInsertId();
                                                       $maxCount = $form_post_data['maxCount'];
                       
                                                       FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                                               
                                                               $Rawmaterial_ID = $form_post_data['Field2_'.$entry_count];
                                                               $Quantity = $form_post_data['Field3_'.$entry_count];
                                                               $unit_ID = $form_post_data['unit_'.$entry_count];
                                                             
                                                               if(!empty($Rawmaterial_ID)){
                                                               $vals = "'" . $lastInsertedID . "'," .
                                                                       "'" . $Rawmaterial_ID . "'," .
                                                                       "'" . $Quantity . "'," .
                                                                       "'" . $unit_ID . "'" ;
                                 
                                                               $sql2 = "INSERT INTO $cus_workorder_detail_table
                                                                       ( 
                                                                           `Cusworkorder_ID`, 
                                                                           `Rawmaterial_ID`, 
                                                                           `Quantity`,
                                                                           `unit_ID`
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
                                        header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/cusWorkOrder');
                                                                                                                        
                                     } else {
                                            //edit option
                                            //if submit failed to insert form
                                            $this->tpl->set('message', 'Failed to submit!');
                                            $this->tpl->set('FmData', $form_post_data);
                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/customized_workorder_design_form.php'));
                                     }
                                    break;
                                case 'add':
                                    $this->tpl->set('mode', 'add');
                                    $this->tpl->set('page_header', 'Workorder');
                                    $WorkOrderNo=$dbutil->keyGeneration('customized_workorder','WON','','WorkOrderNo');
                                    $this->tpl->set('WorkOrderNo', $WorkOrderNo);
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customized_workorder_design_form.php'));
                                    break;
                
                                default:
                                    /*
                                     * List form
                                     */
                                     
                                    ////////////////////start//////////////////////////////////////////////
                                    
                           //bUILD SQL 
                            $whereString = '';
                         $colArr = array(
                                "$customized_workorder_table.ID",
                                "$customized_workorder_table.WorkOrderNo"
                
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
                             $whereString ="ORDER BY $customized_workorder_table.ID DESC";
                           }
                           
                              
                       $sql = "SELECT " 
                                    . implode(',',$colArr)
                                    . " FROM $customized_workorder_table "
                                    . " WHERE "
                                    . " $customized_workorder_table.entity_ID = $entityID" 
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
                         
                         
                         
                            $this->tpl->set('table_columns_label_arr', array('ID','WorkOrderNo'));
                            
                            /*
                             * selectColArr for filter form
                             */
                            
                            $this->tpl->set('selectColArr',$colArr);
                                        
                            /*
                             * set pagination template
                             */
                            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                                   
                            //////////////////////close//////////////////////////////////////  
                                     
                                    include_once $this->tpl->path . '/factory/form/crud_form_customized_workorder.php';
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

                    function qualitycontrol(){
    
                        if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                 
                                ////////////////////////////////////////////////////////////////////////////////
                                //////////////////////////////access condition applied//////////////////////////
                                ////////////////////////////////////////////////////////////////////////////////    
                                            
                                include_once 'util/DBUTIL.php';
                                $dbutil = new DBUTIL($this->crg);
                                 
                                $entityID = $this->ses->get('user')['entity_ID'];
                                $userID = $this->ses->get('user')['ID'];
                                
                                $qualitycontrol_table = $this->crg->get('table_prefix') . 'qualitycontrol';
                                $workorder_table = $this->crg->get('table_prefix') . 'workorder';
                                $employee_table = $this->crg->get('table_prefix') . 'employee';
                                $supplier_table = $this->crg->get('table_prefix') . 'supplier';
                                $fgstock_table = $this->crg->get('table_prefix') . 'fgstock';
                                $product_table = $this->crg->get('table_prefix') . 'product';
                                $stock_trans_tab = $this->crg->get('table_prefix') . 'stock_trans';
                
                                $pdn_sql = "SELECT ID,WorkOrderNo FROM $workorder_table WHERE entity_ID=$entityID";
                                $stmt = $this->db->prepare($pdn_sql);            
                                $stmt->execute();
                                $workorder_data  = $stmt->fetchAll();	
                                $this->tpl->set('workorder_data', $workorder_data);
                                
                                $product_sql = "SELECT ID,ProductName FROM $product_table";
                                $stmt = $this->db->prepare($product_sql);            
                                $stmt->execute();
                                $product_data  = $stmt->fetchAll();	
                                $this->tpl->set('product_data', $product_data);
                                
                                $this->tpl->set('page_title', 'Quality Control');	          
                                $this->tpl->set('page_header', 'Quality Control');
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
                                        
                                 $sql_update="UPDATE ycias_fgstock
                                        LEFT JOIN ycias_product
                                        ON ycias_product.ID=ycias_fgstock.product_ID
                                        LEFT JOIN ycias_qualitycontrol
                                        ON ycias_qualitycontrol.ProductName=ycias_product.ProductName
                                        SET ycias_fgstock.available_qty=(ycias_fgstock.available_qty-ycias_qualitycontrol.Quantity)
                                        WHERE ycias_fgstock.product_name=ycias_product.ProductName AND ycias_qualitycontrol.ID=$data";          
                                        $update_stmt = $this->db->prepare($sql_update);
                                        $update_stmt->execute();
                
                                         $sqldetdelete="Delete from $qualitycontrol_table
                                                            where $qualitycontrol_table.ID=$data"; 
                                            $stmt = $this->db->prepare($sqldetdelete);            
                                            
                                            if($stmt->execute()){
                                            $this->tpl->set('message', 'Quality Control form deleted successfully');
                                                                                                                                          
                                            //$this->tpl->set('label', 'List');
                                            //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                            header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/qualitycontrol');
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
                                        
                                        $sqlsrr = "SELECT * FROM $qualitycontrol_table
                                                   WHERE  $qualitycontrol_table.ID= $data";
                                            $qualitycontrol_data = $dbutil->getSqlData($sqlsrr);
                                       
                                    
                                        //edit option     
                                        $this->tpl->set('message', 'You can view Quality Control form');
                                        $this->tpl->set('page_header', 'Quality Control');
                                        $this->tpl->set('FmData', $qualitycontrol_data); 
                                        
                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/qualitycontrol_design_form.php'));                    
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
                                                     
                                            $sqlsrr = "SELECT * FROM $qualitycontrol_table
                                                       WHERE  $qualitycontrol_table.ID= $data";

                                                       $qualitycontrol_data = $dbutil->getSqlData($sqlsrr);
                                            
                                            //edit option 
                        
                                            
                                            $this->tpl->set('message', 'You can edit Quality Control form');
                                            $this->tpl->set('page_header', 'Quality Control');
                                            $this->tpl->set('FmData', $qualitycontrol_data); 
                                            
                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/qualitycontrol_design_form.php'));                    
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
                
                                         //handle file upload and update staff table    
                                           // $updatecustomer = array();
                                        
                                           foreach ($_FILES as $Fvalue => $valueNotUsing) {
                
                                            $uploadedFile = $util->handle_file_upload($Fvalue, $ID);
                
                                             if ($uploadedFile) {
                                                
                                                 $updatecustomer = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                             }
                                         }
                                       
                                            $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "qualitycontrol` SET " . $updatecustomer .
                                                          " WHERE ID = " .$data . "";
                
                                             $stmt = $this->db->prepare($updateSql);
                                             $stmt->execute(); 
                                                                   
                                        try{
                                              $Qcno= $form_post_data['Qcno'];
                                              $WorkorderID= $form_post_data['WorkorderID'];
                                              $ProductName= $form_post_data['ProductName'];
                                              $ProductNo= $form_post_data['ProductNo'];
                                              $Quantity= $form_post_data['Quantity'];
                                              $acceptedqty =$form_post_data['acceptedqty_'];
                                              $Qcverified= $form_post_data['Qcverified'];
                                              
                                               $sql_update="Update $fgstock_table SET available_qty=(available_qty-'$acceptedqty') 
                                                           WHERE 
                                                           $fgstock_table.product_name='$ProductName' ";               
                                                           $update_stmt = $this->db->prepare($sql_update);
                                                           $update_stmt->execute();
                                              
                                                $sql_update="UPDATE $qualitycontrol_table set Qcno='$Qcno',
                                                                                              WorkorderID='$WorkorderID',
                                                                                              ProductName='$ProductName',
                                                                                              ProductNo='$ProductNo',
                                                                                              Quantity='$Quantity',
                                                                                              Qcverified='$Qcverified'
                                                                                              WHERE ID=$data";
                                                $stmt1 = $this->db->prepare($sql_update);
                                               // $stmt1->execute();
                                                
                                           if($stmt1->execute()) {
                                                $sql_update="Update $fgstock_table SET available_qty=(available_qty+'$Quantity') 
                                                             WHERE 
                                                             $fgstock_table.product_name='$ProductName' ";               
                                                             $update_stmt = $this->db->prepare($sql_update);
                                                             $update_stmt->execute();
                                                }
                                           
                                                $this->tpl->set('message', 'Quality Control form edited successfully!');   
                                                                                                                                              
                                                // $this->tpl->set('label', 'List');
                                                // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                                header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/qualitycontrol');
                                                } catch (Exception $exc) {
                                                 //edit failed option
                                                $this->tpl->set('message', 'Failed to edit, try again!');
                                                $this->tpl->set('FmData', $form_post_data);
                                                $this->tpl->set('content', $this->tpl->fetch('factory/form/qualitycontrol_design_form.php'));
                                                }
                    
                                        break;
                    
                                    case 'addsubmit':
                                         if (isset($crud_string)) {
                     
                                            $form_post_data = $dbutil->arrFltr($_POST);
                
                                         include_once 'util/genUtil.php';
                                         $util = new GenUtil();
                                            
                                          // var_dump($_POST);
                                       $Quantity=$form_post_data['Quantity'];
                                       $product_ID=$form_post_data['product_ID'];
                                       $Qcverified=$form_post_data['Qcverified'];
                                           
                                           if (isset($form_post_data['Qcno'])) {
                                               
                                                            $val = "'" . $form_post_data['Qcno'] . "'," .
                                                                   "'" . $form_post_data['WorkorderID'] . "'," .
                                                                   "'" . $form_post_data['WorkOrderDate'] . "'," .
                                                                   "'" . $form_post_data['EmployeeContractorName'] . "'," .
                                                                   "'" . $form_post_data['product_ID'] . "'," .
                                                                   "'" . $form_post_data['ProductNo'] . "'," .
                                                                   "'" . $form_post_data['Quantity'] . "'," .
                                                                   "'" . $form_post_data['Qcverified'] . "'," .
                                                                   "'" . $form_post_data['FileUpload'] . "'," .
                                                                  "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                             "'" .  $this->ses->get('user')['ID'] . "'";
                                 
                                             $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "qualitycontrol`
                                                                (
                                                                `Qcno`, 
                                                                `WorkorderID`, 
                                                                `CompletedDate`, 
                                                                `EmpName`, 
                                                                `product_ID`, 
                                                                `ProductNo`, 
                                                                `Quantity`, 
                                                                `Qcverified`, 
                                                                `FileUpload`, 
                                                                `entity_ID`,
                                                                `users_ID`
                                                                ) 
                                                             VALUES ( $val )";
                                                      $stmt = $this->db->prepare($sql); 
                                                      if ($stmt->execute()) { 

                                                        $lastInsertedID = $this->db->lastInsertId();
                                                                   //handle file upload and update staff table    
                                                                     //  $updatecustomer = array();
                                                                   
                                                                       foreach ($_FILES as $Fvalue => $valueNotUsing) {
                                                                           
                                                                           
                                                                          $uploadedFile = $util->handle_file_upload($Fvalue, $ID);
                                             
                                                                           if ($uploadedFile) {
                                                                              
                                                                               $updatecustomer = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                                                           }
                                                                       }
                                                                       
                                                                     
                                                                        $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "qualitycontrol` SET " .  $updatecustomer  .
                                                                                          " WHERE ID = '" .$lastInsertedID . "'";
                                                                           $stmt = $this->db->prepare($updateSql);
                                                                           $stmt->execute();
                                                          
                                                  $sql_update="UPDATE ycias_fgstock
                                                               SET ycias_fgstock.available_qty=(ycias_fgstock.available_qty+$Quantity)
                                                               WHERE '$Qcverified'='Accepted' AND ycias_fgstock.product_ID=$product_ID AND entity_ID=$entityID";          
                                                        $update_stmt = $this->db->prepare($sql_update);
                                                        $update_stmt->execute();
                                                        
                                                    $sql_update = "INSERT INTO ycias_stock_trans(trans_date,TransactionType,product_ID, stockin, entity_ID, users_ID)
                                                                   VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d'),'Quality Control',$product_ID, $Quantity, '" . $this->ses->get('user')['entity_ID'] . "', '" . $this->ses->get('user')['ID'] . "')";
                                                                   $update_stmt = $this->db->prepare($sql_update);
                                                                   $update_stmt->execute();
                                                        
                                                        
                                                                   
                
                                                        }
                                                        
                                                     
                                            }
                                            $this->tpl->set('mode', 'add');
                                            $this->tpl->set('message', '- Success -');
                                            // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                                            header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/qualitycontrol');
                                                                                                                            
                                         } else {
                                                //edit option
                                                //if submit failed to insert form
                                                $this->tpl->set('message', 'Failed to submit!');
                                                $this->tpl->set('FmData', $form_post_data);
                                                $this->tpl->set('content', $this->tpl->fetch('factory/form/qualitycontrol_design_form.php'));
                                         }
                                        break;
                                    case 'add':
                                        $this->tpl->set('mode', 'add');
                                        $this->tpl->set('page_header', 'Quality Control');
                                        $Qcno=$dbutil->keyGeneration('qualitycontrol','QCN','','Qcno');
                                        $this->tpl->set('Qcno', $Qcno);
                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/qualitycontrol_design_form.php'));
                                        break;
                    
                                    default:
                                        /*
                                         * List form
                                         */
                                         
                                        ////////////////////start//////////////////////////////////////////////
                                        
                               //bUILD SQL 
                                $whereString = '';
                             $colArr = array(
                                    "$qualitycontrol_table.ID",
                                    "$qualitycontrol_table.Qcno"
                    
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
                                 $whereString ="ORDER BY $qualitycontrol_table.ID DESC";
                               }
                               
                                  
                           $sql = "SELECT " 
                                        . implode(',',$colArr)
                                        . " FROM $qualitycontrol_table "
                                        . " WHERE "
                                        . " $qualitycontrol_table.entity_ID = $entityID" 
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
                             
                             
                             
                                $this->tpl->set('table_columns_label_arr', array('ID','QC No'));
                                
                                /*
                                 * selectColArr for filter form
                                 */
                                
                                $this->tpl->set('selectColArr',$colArr);
                                            
                                /*
                                 * set pagination template
                                 */
                                $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table2.php');
                                       
                                //////////////////////close//////////////////////////////////////  
                                         
                                        include_once $this->tpl->path . '/factory/form/crud_Quality_Control_form.php';
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

                        function materialrequest(){
    
                            if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                     
                                    ////////////////////////////////////////////////////////////////////////////////
                                    //////////////////////////////access condition applied//////////////////////////
                                    ////////////////////////////////////////////////////////////////////////////////    
                                                
                                    include_once 'util/DBUTIL.php';
                                    $dbutil = new DBUTIL($this->crg);
                                     
                                    $entityID = $this->ses->get('user')['entity_ID'];
                                    $userID = $this->ses->get('user')['ID'];
                                    
                                    $materialrequest_table = $this->crg->get('table_prefix') . 'materialrequest';
                                    $materialrequest_detail_table = $this->crg->get('table_prefix') . 'materialrequest_detail';
                                    $employee_table = $this->crg->get('table_prefix') . 'employee';
                                    $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
                                    $unit_table = $this->crg->get('table_prefix') . 'unit';
                                    $supplier_table = $this->crg->get('table_prefix') . 'supplier';
                    
                                    $employee_sql = "SELECT ID,EmpName FROM $employee_table";
                                    $stmt = $this->db->prepare($employee_sql);            
                                    $stmt->execute();
                                    $employee_data  = $stmt->fetchAll();	
                                    $this->tpl->set('employee_data', $employee_data);

                                    $employee_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE SupplierType='Sub contractor'";
                                    $stmt = $this->db->prepare($employee_sql);            
                                    $stmt->execute();
                                    $subcontractor_data  = $stmt->fetchAll();	
                                    $this->tpl->set('subcontractor_data', $subcontractor_data);

                                    $employee_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE SupplierType='Sub contractor'";
                                    $stmt = $this->db->prepare($employee_sql);            
                                    $stmt->execute();
                                    $subcontractor_data  = $stmt->fetchAll();	
                                    $this->tpl->set('subcontractor_data', $subcontractor_data);

                                    $rawmaterial_sql = "SELECT ID,RMName FROM $rawmaterial_table";
                                    $stmt = $this->db->prepare($rawmaterial_sql);            
                                    $stmt->execute();
                                    $rawmaterial_data  = $stmt->fetchAll();	
                                    $this->tpl->set('rawmaterial_data', $rawmaterial_data);

                                    $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
                                    $stmt = $this->db->prepare($pdt_sql);            
                                    $stmt->execute();
                                    $unit_data  = $stmt->fetchAll();	
                                    $this->tpl->set('unit_data', $unit_data);

                                    $Employeetype_data = array(array("ID"=>"1","Title"=>"Employee"),array("ID"=>"2","Title"=>"Sub Contractor"));
                                    $this->tpl->set('Employeetype_data', $Employeetype_data);
                                    
                                    $this->tpl->set('page_title', 'Material Request');	          
                                    $this->tpl->set('page_header', 'Material Request');
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
                    
                                             $sqldetdelete="Delete from $materialrequest_table
                                                                where $materialrequest_table.ID=$data"; 
                                                $stmt = $this->db->prepare($sqldetdelete);            
                                                
                                                if($stmt->execute()){
                                                $this->tpl->set('message', 'Material Request form deleted successfully');
                                                                                                                                              
                                                //$this->tpl->set('label', 'List');
                                                //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                                header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/materialrequest');
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
                                            
                                            $sqlsrr = "SELECT *
                                            FROM $materialrequest_table LEFT JOIN $materialrequest_detail_table ON $materialrequest_detail_table.Materialrequest_ID=$materialrequest_table.ID 
                                            WHERE  $materialrequest_table.ID= $data";
                                            $materialrequest_data = $dbutil->getSqlData($sqlsrr);
                                           
                                        
                                            //edit option     
                                            $this->tpl->set('message', 'You can view Material Request form');
                                            $this->tpl->set('page_header', 'Material Request');
                                            $this->tpl->set('FmData', $materialrequest_data); 
                                            
                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/materialrequest_design_form.php'));                    
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
                                                         
                                                $sqlsrr = "SELECT *
                                                FROM $materialrequest_table LEFT JOIN $materialrequest_detail_table ON $materialrequest_detail_table.Materialrequest_ID=$materialrequest_table.ID 
                                                WHERE  $materialrequest_table.ID= $data";
                                                $materialrequest_data = $dbutil->getSqlData($sqlsrr);
                                                
                                                //edit option 
                            
                                                
                                                $this->tpl->set('message', 'You can edit Material Request form');
                                                $this->tpl->set('page_header', 'Material Request');
                                                $this->tpl->set('FmData', $materialrequest_data); 
                                                
                                                $this->tpl->set('content', $this->tpl->fetch('factory/form/materialrequest_design_form.php'));                    
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
                                                  $Reqno= $form_post_data['Reqno'];
                                                  $EmployeeType= $form_post_data['EmployeeType'];
                                                  $EmployeeID= $form_post_data['EmployeeID'];
                                                  $Remarks= $form_post_data['Remarks'];
                                                  
                                                    $sql_update="UPDATE $materialrequest_table set Reqno='$Reqno',
                                                                                                  EmployeeType='$EmployeeType',
                                                                                                  EmployeeID='$EmployeeID',
                                                                                                  Remarks='$Remarks'
                                                                                                  WHERE ID=$data";
                                                    $stmt1 = $this->db->prepare($sql_update);
                                                    $stmt1->execute();
                                                    
                                            $maxCount = $form_post_data['maxCount'];			   
                      
                                            $sql3 = "DELETE FROM $materialrequest_detail_table WHERE Materialrequest_ID=$data";
                                            $stmt3 = $this->db->prepare($sql3);
                                            //$is_delete = $stmt3->execute();
                                            // var_dump($is_delete);die;
                                          if($stmt3->execute()){
                                              
                                           FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                                   
                                                   $Rawmaterial_ID = $form_post_data['Field2_'.$entry_count];
                                                   $Quantity = $form_post_data['Field3_'.$entry_count];
                                                   $unit_ID = $form_post_data['unit_'.$entry_count];
                                                 
                                                 if(!empty($Rawmaterial_ID)){
                                                   $vals = "'" . $data . "'," .
                                                           "'" . $Rawmaterial_ID . "'," .
                                                           "'" . $Quantity . "'," .
                                                           "'" . $unit_ID . "'," .
                                                           "'" . $Quantity . "'" ;
                     
                                                    $sql2 = "INSERT INTO $materialrequest_detail_table
                                                           ( 
                                                            `Materialrequest_ID`, 
                                                            `Rawmaterial_ID`, 
                                                            `Quantity`,
                                                            `unit_ID`,
                                                            `issue_qty`
                                                           ) 
                                                   VALUES ($vals)";
                                                   $stmt = $this->db->prepare($sql2);
                                                   $stmt->execute();
                                                 }
                                                   
                                               //increment here
                                               
                                               }
                                          }
                                               
                                                    $this->tpl->set('message', 'Material Request form edited successfully!');   
                                                                                                                                                  
                                                    // $this->tpl->set('label', 'List');
                                                    // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                                    header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/materialrequest');
                                                    } catch (Exception $exc) {
                                                     //edit failed option
                                                    $this->tpl->set('message', 'Failed to edit, try again!');
                                                    $this->tpl->set('FmData', $form_post_data);
                                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/materialrequest_design_form.php'));
                                                    }
                        
                                            break;
                        
                                        case 'addsubmit':
                                             if (isset($crud_string)) {
                         
                                                $form_post_data = $dbutil->arrFltr($_POST);
                    
                                             include_once 'util/genUtil.php';
                                             $util = new GenUtil();
                                                
                                              // var_dump($_POST);
                                               
                                               
                                               if (isset($form_post_data['Reqno'])) {
                                                   
                                                                $val = "'" . $form_post_data['Reqno'] . "'," .
                                                                       "'" . $form_post_data['EmployeeType'] . "'," .
                                                                       "'" . $form_post_data['EmployeeID'] . "'," .
                                                                       "'" . $form_post_data['Subcontractor_ID'] . "'," .
                                                                       "'" . $form_post_data['Remarks'] . "'," .
                                                                      "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                                 "'" .  $this->ses->get('user')['ID'] . "'";
                                     
                                                     $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "materialrequest`
                                                                    (
                                                                    `Reqno`, 
                                                                    `EmployeeType`, 
                                                                    `EmployeeID`, 
                                                                    `Subcontractor_ID`, 
                                                                    `Remarks`, 
                                                                    `entity_ID`,
                                                                    `users_ID`
                                                                    ) 
                                                                 VALUES ( $val )";
                                                          $stmt = $this->db->prepare($sql); 
                                                    
                                                          if ($stmt->execute()) { 
                                                            //echo '<pre>';
                                                           // print_r($_POST);die;
                                                               $lastInsertedID = $this->db->lastInsertId();
                                                               $maxCount = $form_post_data['maxCount'];
                               
                                                               FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                                                       
                                                                       $Rawmaterial_ID = $form_post_data['Field2_'.$entry_count];
                                                                       $Quantity = $form_post_data['Field3_'.$entry_count];
                                                                       $unit_ID = $form_post_data['unit_'.$entry_count];
                                                                     
                                                                       if(!empty($Rawmaterial_ID)){
                                                                       $vals = "'" . $lastInsertedID . "'," .
                                                                               "'" . $Rawmaterial_ID . "'," .
                                                                               "'" . $Quantity . "'," .
                                                                               "'" . $unit_ID . "'," .
                                                                               "'" . $Quantity . "'" ;
                                         
                                                                       $sql2 = "INSERT INTO $materialrequest_detail_table
                                                                               ( 
                                                                                   `Materialrequest_ID`, 
                                                                                   `Rawmaterial_ID`, 
                                                                                   `Quantity`,
                                                                                   `unit_ID`,
                                                                                   `issue_qty`
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
                                                header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/materialrequest');
                                                                                                                                
                                             } else {
                                                    //edit option
                                                    //if submit failed to insert form
                                                    $this->tpl->set('message', 'Failed to submit!');
                                                    $this->tpl->set('FmData', $form_post_data);
                                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/materialrequest_design_form.php'));
                                             }
                                            break;
                                        case 'add':
                                            $this->tpl->set('mode', 'add');
                                            $this->tpl->set('page_header', 'Material Request');
                                            $Reqno=$dbutil->keyGeneration('materialrequest','MRN','','Reqno');
                                            $this->tpl->set('Reqno', $Reqno);
                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/materialrequest_design_form.php'));
                                            break;
                        
                                        default:
                                            /*
                                             * List form
                                             */
                                             
                                            ////////////////////start//////////////////////////////////////////////
                                            
                                   //bUILD SQL 
                                    $whereString = '';
                                 $colArr = array(
                                        "$materialrequest_table.ID",
                                        "$materialrequest_table.Reqno"
                        
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
                                     $whereString ="ORDER BY $materialrequest_table.ID DESC";
                                   }
                                   
                                      
                               $sql = "SELECT " 
                                            . implode(',',$colArr)
                                            . " FROM $materialrequest_table "
                                            . " WHERE "
                                            . " $materialrequest_table.entity_ID = $entityID" 
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
                                 
                                 
                                 
                                    $this->tpl->set('table_columns_label_arr', array('ID','Material Req No'));
                                    
                                    /*
                                     * selectColArr for filter form
                                     */
                                    
                                    $this->tpl->set('selectColArr',$colArr);
                                                
                                    /*
                                     * set pagination template
                                     */
                                    $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                                           
                                    //////////////////////close//////////////////////////////////////  
                                             
                                            include_once $this->tpl->path . '/factory/form/crud_material_request_form.php';
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

                function fgr(){
    
                    if ($this->crg->get('wp') || $this->crg->get('rp')) {
                             
                            ////////////////////////////////////////////////////////////////////////////////
                            //////////////////////////////access condition applied//////////////////////////
                            ////////////////////////////////////////////////////////////////////////////////    
                                        
                            include_once 'util/DBUTIL.php';
                            $dbutil = new DBUTIL($this->crg);
                             
                            $entityID = $this->ses->get('user')['entity_ID'];
                            $userID = $this->ses->get('user')['ID'];
                            
                            $fgr_table = $this->crg->get('table_prefix') . 'fgr';
                            $productionorder_table = $this->crg->get('table_prefix') . 'productionorder';
                            $workorder_table = $this->crg->get('table_prefix') . 'workorder';
                           
                            $pdn_sql = "SELECT ID,PdnOrderNo FROM $productionorder_table";
                            $stmt = $this->db->prepare($pdn_sql);            
                            $stmt->execute();
                            $pdn_data  = $stmt->fetchAll();	
                            $this->tpl->set('pdn_data', $pdn_data);

                            $pdn_sql = "SELECT ID,WorkOrderNo FROM $workorder_table";
                            $stmt = $this->db->prepare($pdn_sql);            
                            $stmt->execute();
                            $workorder_data  = $stmt->fetchAll();	
                            $this->tpl->set('workorder_data', $workorder_data);
                            
                            $this->tpl->set('page_title', 'FGR');	          
                            $this->tpl->set('page_header', 'FGR');
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
                                     
                                     $sqldetdelete="Delete from $fgr_table
                                                        where $fgr_table.ID=$data"; 
                                        $stmt = $this->db->prepare($sqldetdelete);            
                                        
                                        if($stmt->execute()){
                                        $this->tpl->set('message', 'FGR form deleted successfully');
                                                                                                                                      
                                        //$this->tpl->set('label', 'List');
                                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/fgr');
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
                                    
                                    $sqlsrr = "SELECT *
                                     FROM $fgr_table 
                                     WHERE 
                                     $fgr_table.ID = $data";   
                                     $fgr_data = $dbutil->getSqlData($sqlsrr);
                                   
                                
                                    //edit option     
                                    $this->tpl->set('message', 'You can view FGR form');
                                    $this->tpl->set('page_header', 'FGR');
                                    $this->tpl->set('FmData', $fgr_data); 
                                    
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/fgr_design_form.php'));                    
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
                                             
                                //   $sqlsrr = "SELECT  * FROM `$employee_salary_table` WHERE `$employee_salary_table.ID`= `$data`";                    
                                //   $employee_salary_data = $dbutil->getSqlData($sqlsrr); 
                                   
                                $sqlsrr = "SELECT *
                                FROM $fgr_table 
                                WHERE 
                                $fgr_table.ID = $data";   
                                $fgr_data = $dbutil->getSqlData($sqlsrr);
                                   
                                    
                                    //edit option 
                
                                    
                                    $this->tpl->set('message', 'You can edit FGR form');
                                    $this->tpl->set('page_header', 'FGR');
                                    $this->tpl->set('FmData', $fgr_data); 
                                    
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/fgr_design_form.php'));                    
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
                                          $Fgrno= $form_post_data['Fgrno'];
                                          $PdnOrderID= $form_post_data['PdnOrderID'];
                                          $WorkorderID= $form_post_data['WorkorderID'];
                                          
                                            $sql_update="UPDATE $fgr_table set Fgrno='$Fgrno',
                                                                                     PdnOrderID='$PdnOrderID',
                                                                                     WorkorderID='$WorkorderID'
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
                                       
                                            $this->tpl->set('message', 'FGR form edited successfully!');   
                                                                                                                                          
                                            // $this->tpl->set('label', 'List');
                                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/fgr');
                                            } catch (Exception $exc) {
                                             //edit failed option
                                            $this->tpl->set('message', 'Failed to edit, try again!');
                                            $this->tpl->set('FmData', $form_post_data);
                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/fgr_design_form.php'));
                                            }
                
                                    break;
                
                                case 'addsubmit':
                                     if (isset($crud_string)) {
                 
                                        $form_post_data = $dbutil->arrFltr($_POST);
                                        
                                      // var_dump($_POST);
                                       
                                        $entry_count = 1;
                                       
                                       if (isset($form_post_data['Fgrno'])) {
                                           
                                                        $val = "'" . $form_post_data['Fgrno'] . "'," .
                                                         "'" . $form_post_data['PdnOrderID'] . "'," .
                                                         "'" . $form_post_data['WorkorderID'] . "'," .
    
                                                         "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                         "'" .  $this->ses->get('user')['ID'] . "'";
                
                                             $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "fgr`
                                                            (
                                                            `Fgrno`, 
                                                            `PdnOrderID`, 
                                                            `WorkorderID`,
                                                            `entity_ID`,
                                                            `users_ID`
                                                            ) 
                                                         VALUES ( $val )";
                                                  $stmt = $this->db->prepare($sql);
                                                  $stmt->execute();
                                                  
                                       /*           
                                    if ($stmt->execute()) { 
                                        $lastInsertedID = $this->db->lastInsertId();
                                        FOR ($entry_count; $entry_count <= $form_post_data['maxCount'];) {
                                               
                                                $rawmaterial_id ='ItemNo_' . $entry_count;
                                                $Qty ='Quantity_' . $entry_count;
                                               
                
                
                                                $vals = "'" . $lastInsertedID . "'," .
                                                        "'" . $form_post_data[$rawmaterial_id] . "'," .
                                                        "'" . $form_post_data[$Qty] . "'" ;
                                                       
                                                         //"'" . $form_post_data[$Temp] . "'";
                                                 
                                              $sql2 = "INSERT INTO $bomdetail_tab
                                                        ( 
                                                `bommaster_ID`, 
                                                `rawmaterial_id`,
                                                `Qty`
                                                 ) 
                                                VALUES ($vals)";
                
                                                 // this need to be changed in to transaction type
                                                
                                                $stmt = $this->db->prepare($sql2);
                                                $stmt->execute();
                                                  //increment here
                                                $entry_count++;
                                                
                                            }
                                    }*/
                                        }
                                        $this->tpl->set('mode', 'add');
                                        $this->tpl->set('message', '- Success -');
                                        // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/fgr');
                                                                                                                        
                                     } else {
                                            //edit option
                                            //if submit failed to insert form
                                            $this->tpl->set('message', 'Failed to submit!');
                                            $this->tpl->set('FmData', $form_post_data);
                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/fgr_design_form.php'));
                                     }
                                    break;
                                case 'add':
                                    $this->tpl->set('mode', 'add');
                                    $this->tpl->set('page_header', 'FGR');
                                    $Fgrno=$dbutil->keyGeneration('fgr','FGR','','Fgrno');
                                    $this->tpl->set('Fgrno', $Fgrno);
                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/fgr_design_form.php'));
                                    break;
                
                                default:
                                    /*
                                     * List form
                                     */
                                     
                                    ////////////////////start//////////////////////////////////////////////
                                    
                           //bUILD SQL 
                            $whereString = '';
                         $colArr = array(
                                "$fgr_table.ID",
                                "$fgr_table.Fgrno"
                
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
                             $whereString ="ORDER BY $fgr_table.ID DESC";
                           }
                           
                              
                       $sql = "SELECT " 
                                    . implode(',',$colArr)
                                    . " FROM $fgr_table "
                                    . " WHERE "
                                    . " $fgr_table.entity_ID = $entityID" 
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
                         
                         
                         
                            $this->tpl->set('table_columns_label_arr', array('ID','FGR No'));
                            
                            /*
                             * selectColArr for filter form
                             */
                            
                            $this->tpl->set('selectColArr',$colArr);
                                        
                            /*
                             * set pagination template
                             */
                            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                                   
                            //////////////////////close//////////////////////////////////////  
                                     
                                    include_once $this->tpl->path . '/factory/form/crud_fgr_form.php';
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
                    
                     function productionplan(){
    
                                    if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                             
                                            ////////////////////////////////////////////////////////////////////////////////
                                            //////////////////////////////access condition applied//////////////////////////
                                            ////////////////////////////////////////////////////////////////////////////////    
                                                        
                                            include_once 'util/DBUTIL.php';
                                            $dbutil = new DBUTIL($this->crg);
                                             
                                            $entityID = $this->ses->get('user')['entity_ID'];
                                            $userID = $this->ses->get('user')['ID'];
                                            
                                            $productionplan_table = $this->crg->get('table_prefix') . 'productionplan';
                                            $employee_table = $this->crg->get('table_prefix') . 'employee';
                                            $supplier_table = $this->crg->get('table_prefix') . 'supplier';
                            
                                            $employee_sql = "SELECT ID,EmpName FROM $employee_table";
                                            $stmt = $this->db->prepare($employee_sql);            
                                            $stmt->execute();
                                            $employee_data  = $stmt->fetchAll();	
                                            $this->tpl->set('employee_data', $employee_data);

                                            $employee_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE SupplierType='Sub contractor'";
                                            $stmt = $this->db->prepare($employee_sql);            
                                            $stmt->execute();
                                            $subcontractor_data  = $stmt->fetchAll();	
                                            $this->tpl->set('subcontractor_data', $subcontractor_data);

                                            $Employeetype_data = array(array("ID"=>"1","Title"=>"Employee"),array("ID"=>"2","Title"=>"Sub Contractor"));
                                            $this->tpl->set('Employeetype_data', $Employeetype_data);
                                            
                                            $this->tpl->set('page_title', 'Production Plan');	          
                                            $this->tpl->set('page_header', 'Production Plan');
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
                            
                                                     $sqldetdelete="Delete from $productionplan_table
                                                                        where $productionplan_table.ID=$data"; 
                                                        $stmt = $this->db->prepare($sqldetdelete);            
                                                        
                                                        if($stmt->execute()){
                                                        $this->tpl->set('message', 'Production Plan form deleted successfully');
                                                                                                                                                      
                                                        //$this->tpl->set('label', 'List');
                                                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                                        header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/productionplan');
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
                                                    
                                                    $sqlsrr = "SELECT *
                                                        FROM $productionplan_table
                                                        WHERE  $productionplan_table.ID= $data";
                                                        $productionplan_data = $dbutil->getSqlData($sqlsrr);
                                                   
                                                
                                                    //edit option     
                                                    $this->tpl->set('message', 'You can view Production Plan form');
                                                    $this->tpl->set('page_header', 'Production Plan');
                                                    $this->tpl->set('FmData', $productionplan_data); 
                                                    
                                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/productionplan_design_form.php'));                    
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
                                                                 
                                                        $sqlsrr = "SELECT *
                                                        FROM $productionplan_table
                                                        WHERE  $productionplan_table.ID= $data";
                                                        $productionplan_data = $dbutil->getSqlData($sqlsrr);
                                                        
                                                        //edit option 
                                    
                                                        
                                                        $this->tpl->set('message', 'You can edit Production Plan form');
                                                        $this->tpl->set('page_header', 'Production Plan');
                                                        $this->tpl->set('FmData', $productionplan_data); 
                                                        
                                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/productionplan_design_form.php'));                    
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
                            
                                                     //handle file upload and update staff table    
                                                       // $updatecustomer = array();
                                                       $Date=!empty($form_post_data['Date'])?date("Y-m-d", strtotime($form_post_data['Date'])):'';                       
                                                    try{
                                                          $PoReference= $form_post_data['PoReference'];
                                                          $Descriptionofpo= $form_post_data['Descriptionofpo'];
                                                          $RefNo= $form_post_data['RefNo'];
                                                          $EmployeeType= $form_post_data['EmployeeType'];
                                                          $EmployeeID= $form_post_data['EmployeeID'];
                                                          $week= $form_post_data['week'];
                                                          $Date= $Date;
                                                          $Unit= $form_post_data['Unit'];
                                                          
                                                            $sql_update="UPDATE $productionplan_table set PoReference='$PoReference',
                                                                                                          Descriptionofpo='$Descriptionofpo',
                                                                                                          RefNo='$RefNo',
                                                                                                          EmployeeType='$EmployeeType',
                                                                                                          EmployeeID='$EmployeeID',
                                                                                                          week='$week',
                                                                                                          Date='$Date',
                                                                                                          Unit='$Unit'

                                                                                                          WHERE ID=$data";
                                                            $stmt1 = $this->db->prepare($sql_update);
                                                            $stmt1->execute();
                                                       
                                                            $this->tpl->set('message', 'Production Plan form edited successfully!');   
                                                                                                                                                          
                                                            // $this->tpl->set('label', 'List');
                                                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                                            header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/productionplan');
                                                            } catch (Exception $exc) {
                                                             //edit failed option
                                                            $this->tpl->set('message', 'Failed to edit, try again!');
                                                            $this->tpl->set('FmData', $form_post_data);
                                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/productionplan_design_form.php'));
                                                            }
                                
                                                    break;
                                
                                                case 'addsubmit':
                                                     if (isset($crud_string)) {
                                 
                                                        $form_post_data = $dbutil->arrFltr($_POST);
                            
                                                     include_once 'util/genUtil.php';
                                                     $util = new GenUtil();
                                                        
                                                      // var_dump($_POST);
                                                      $Date=!empty($form_post_data['Date'])?date("Y-m-d", strtotime($form_post_data['Date'])):'';
                                                       
                                                       if (isset($form_post_data['PoReference'])) {
                                                           
                                                                        $val = "'" . $form_post_data['PoReference'] . "'," .
                                                                               "'" . $form_post_data['Descriptionofpo'] . "'," .
                                                                               "'" . $form_post_data['RefNo'] . "'," .
                                                                               "'" . $form_post_data['EmployeeType'] . "'," .
                                                                               "'" . $form_post_data['EmployeeID'] . "'," .
                                                                               "'" . $form_post_data['Subcontractor_ID'] . "'," .
                                                                               "'" . $form_post_data['week'] . "'," .
                                                                               "'" . $Date . "'," .
                                                                               "'" . $form_post_data['Unit'] . "'," .
                                                                              "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                                         "'" .  $this->ses->get('user')['ID'] . "'";
                                             
                                                             $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "productionplan`
                                                                            (
                                                                            `PoReference`, 
                                                                            `Descriptionofpo`, 
                                                                            `RefNo`, 
                                                                            `EmployeeType`, 
                                                                            `EmployeeID`, 
                                                                            `Subcontractor_ID`, 
                                                                            `week`, 
                                                                            `Date`, 
                                                                            `Unit`, 
                                                                            `entity_ID`,
                                                                            `users_ID`
                                                                            ) 
                                                                         VALUES ( $val )";
                                                                  $stmt = $this->db->prepare($sql); 
                                                                  $stmt->execute();
                                                                 
                                                        }
                                                        $this->tpl->set('mode', 'add');
                                                        $this->tpl->set('message', '- Success -');
                                                        // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                                                        header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/productionplan');
                                                                                                                                        
                                                     } else {
                                                            //edit option
                                                            //if submit failed to insert form
                                                            $this->tpl->set('message', 'Failed to submit!');
                                                            $this->tpl->set('FmData', $form_post_data);
                                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/productionplan_design_form.php'));
                                                     }
                                                    break;
                                                case 'add':
                                                    $this->tpl->set('mode', 'add');
                                                    $this->tpl->set('page_header', 'Production Plan');
                                                   // $Qcno=$dbutil->keyGeneration('qualitycontrol','QCN','','Qcno');
                                                   // $this->tpl->set('Qcno', $Qcno);
                                                    $this->tpl->set('content', $this->tpl->fetch('factory/form/productionplan_design_form.php'));
                                                    break;
                                
                                                default:
                                                    /*
                                                     * List form
                                                     */
                                                     
                                                    ////////////////////start//////////////////////////////////////////////
                                                    
                                           //bUILD SQL 
                                            $whereString = '';
                                         $colArr = array(
                                                "$productionplan_table.ID",
                                                "$productionplan_table.PoReference",
                                                "$productionplan_table.Descriptionofpo",
                                                "$productionplan_table.RefNo",
                                                "$employee_table.EmpName",
                                                "$productionplan_table.Unit"
                                
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
                                             $whereString ="ORDER BY $productionplan_table.ID DESC";
                                           }
                                           
                                              
                                       $sql = "SELECT " 
                                                    . implode(',',$colArr)
                                                    . " FROM $productionplan_table LEFT JOIN $employee_table ON $employee_table.ID=$productionplan_table.EmployeeID "
                                                    . " WHERE "
                                                    . " $productionplan_table.entity_ID = $entityID" 
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
                                         
                                         
                                         
                                            $this->tpl->set('table_columns_label_arr', array('ID','Po Reference','Description of PO','Ref No','Contractor Name','Week'));
                                            
                                            /*
                                             * selectColArr for filter form
                                             */
                                            
                                            $this->tpl->set('selectColArr',$colArr);
                                                        
                                            /*
                                             * set pagination template
                                             */
                                            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                                                   
                                            //////////////////////close//////////////////////////////////////  
                                                     
                                                    include_once $this->tpl->path . '/factory/form/crud_productionplan_form.php';
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

                                    public function inspectionDispatch(){
    
                                        if ($this->crg->get('wp') || $this->crg->get('rp')) {
                                                 
                                                ////////////////////////////////////////////////////////////////////////////////
                                                //////////////////////////////access condition applied//////////////////////////
                                                ////////////////////////////////////////////////////////////////////////////////    
                                                            
                                                include_once 'util/DBUTIL.php';
                                                $dbutil = new DBUTIL($this->crg);
                                                 
                                                $entityID = $this->ses->get('user')['entity_ID'];
                                                $userID = $this->ses->get('user')['ID'];
                                                
                                                $inspectiondispatch_table = $this->crg->get('table_prefix') . 'inspection_dispatch';
                                                
                                                $this->tpl->set('page_title', 'Inspection cum Dispatch Plan');	          
                                                $this->tpl->set('page_header', 'Inspection cum Dispatch Plan');
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
                                
                                                         $sqldetdelete="Delete from $inspectiondispatch_table
                                                                            where $inspectiondispatch_table.ID=$data"; 
                                                            $stmt = $this->db->prepare($sqldetdelete);            
                                                            
                                                            if($stmt->execute()){
                                                            $this->tpl->set('message', 'Inspection cum Dispatch Plan form deleted successfully');
                                                                                                                                                          
                                                            //$this->tpl->set('label', 'List');
                                                            //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                                            header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/inspectionDispatch');
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
                                                        
                                                        $sqlsrr = "SELECT *
                                                            FROM $inspectiondispatch_table
                                                            WHERE  $inspectiondispatch_table.ID= $data";
                                                            $inspectiondispatch_data = $dbutil->getSqlData($sqlsrr);
                                                       
                                                    
                                                        //edit option     
                                                        $this->tpl->set('message', 'You can view Inspection cum Dispatch Plan form');
                                                        $this->tpl->set('page_header', 'Inspection cum Dispatch Plan');
                                                        $this->tpl->set('FmData', $inspectiondispatch_data); 
                                                        
                                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/inspectiondispatch_design_form.php'));                    
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
                                                                     
                                                            $sqlsrr = "SELECT *
                                                            FROM $inspectiondispatch_table
                                                            WHERE  $inspectiondispatch_table.ID= $data";
                                                            $inspectiondispatch_data = $dbutil->getSqlData($sqlsrr);
                                                            
                                                            //edit option 
                                        
                                                            
                                                            $this->tpl->set('message', 'You can edit Inspection cum Dispatch Plan form');
                                                            $this->tpl->set('page_header', 'Inspection cum Dispatch Plan');
                                                            $this->tpl->set('FmData', $inspectiondispatch_data); 
                                                            
                                                            $this->tpl->set('content', $this->tpl->fetch('factory/form/inspectiondispatch_design_form.php'));                    
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
                                
                                                         //handle file upload and update staff table    
                                                           // $updatecustomer = array();
                                                           $Date=!empty($form_post_data['Date'])?date("Y-m-d", strtotime($form_post_data['Date'])):'';                       
                                                        try{
                                                              $PlanType= $form_post_data['PlanType'];
                                                              $PoReference= $form_post_data['PoReference'];
                                                              $Descriptionofpo= $form_post_data['Descriptionofpo'];
                                                              $week= $form_post_data['week'];
                                                              $Date= $Date;
                                                              $Unit= $form_post_data['Unit'];
                                                              
                                                                $sql_update="UPDATE $inspectiondispatch_table set PlanType='$PlanType',
                                                                                                                  PoReference='$PoReference',
                                                                                                                  Descriptionofpo='$Descriptionofpo',
                                                                                                                  week='$week',
                                                                                                                  Date='$Date',
                                                                                                                  Unit='$Unit'
    
                                                                                                                  WHERE ID=$data";
                                                                $stmt1 = $this->db->prepare($sql_update);
                                                                $stmt1->execute();
                                                           
                                                                $this->tpl->set('message', 'Inspection cum Dispatch Plan form edited successfully!');   
                                                                                                                                                              
                                                                // $this->tpl->set('label', 'List');
                                                                // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                                                header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/inspectionDispatch');
                                                                } catch (Exception $exc) {
                                                                 //edit failed option
                                                                $this->tpl->set('message', 'Failed to edit, try again!');
                                                                $this->tpl->set('FmData', $form_post_data);
                                                                $this->tpl->set('content', $this->tpl->fetch('factory/form/inspectiondispatch_design_form.php'));
                                                                }
                                    
                                                        break;
                                    
                                                    case 'addsubmit':
                                                         if (isset($crud_string)) {
                                     
                                                            $form_post_data = $dbutil->arrFltr($_POST);
                                
                                                         include_once 'util/genUtil.php';
                                                         $util = new GenUtil();
                                                            
                                                          // var_dump($_POST);
                                                          $Date=!empty($form_post_data['Date'])?date("Y-m-d", strtotime($form_post_data['Date'])):'';
                                                           
                                                           if (isset($form_post_data['PoReference'])) {
                                                               
                                                                            $val = "'" . $form_post_data['PlanType'] . "'," .
                                                                                   "'" . $form_post_data['PoReference'] . "'," .
                                                                                   "'" . $form_post_data['Descriptionofpo'] . "'," .
                                                                                   "'" . $form_post_data['week'] . "'," .
                                                                                   "'" . $Date . "'," .
                                                                                   "'" . $form_post_data['Unit'] . "'," .
                                                                                  "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                                                             "'" .  $this->ses->get('user')['ID'] . "'";
                                                 
                                                                 $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "inspection_dispatch`
                                                                                (
                                                                                `PlanType`, 
                                                                                `PoReference`, 
                                                                                `Descriptionofpo`, 
                                                                                `week`, 
                                                                                `Date`, 
                                                                                `Unit`, 
                                                                                `entity_ID`,
                                                                                `users_ID`
                                                                                ) 
                                                                             VALUES ( $val )";
                                                                      $stmt = $this->db->prepare($sql); 
                                                                      $stmt->execute();
                                                                     
                                                            }
                                                            $this->tpl->set('mode', 'add');
                                                            $this->tpl->set('message', '- Success -');
                                                            // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
                                                            header('Location:' . $this->crg->get('route')['base_path'] . '/production/cst/inspectionDispatch');
                                                                                                                                            
                                                         } else {
                                                                //edit option
                                                                //if submit failed to insert form
                                                                $this->tpl->set('message', 'Failed to submit!');
                                                                $this->tpl->set('FmData', $form_post_data);
                                                                $this->tpl->set('content', $this->tpl->fetch('factory/form/inspectiondispatch_design_form.php'));
                                                         }
                                                        break;
                                                    case 'add':
                                                        $this->tpl->set('mode', 'add');
                                                        $this->tpl->set('page_header', 'Inspection cum Dispatch Plan');
                                                       // $Qcno=$dbutil->keyGeneration('qualitycontrol','QCN','','Qcno');
                                                       // $this->tpl->set('Qcno', $Qcno);
                                                        $this->tpl->set('content', $this->tpl->fetch('factory/form/inspectiondispatch_design_form.php'));
                                                        break;
                                    
                                                    default:
                                                        /*
                                                         * List form
                                                         */
                                                         
                                                        ////////////////////start//////////////////////////////////////////////
                                                        
                                               //bUILD SQL 
                                                $whereString = '';
                                             $colArr = array(
                                                    "$inspectiondispatch_table.ID",
                                                    "$inspectiondispatch_table.PoReference",
                                                    "$inspectiondispatch_table.Descriptionofpo",
                                                    "$inspectiondispatch_table.Unit"
                                    
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
                                                 $whereString ="ORDER BY $inspectiondispatch_table.ID DESC";
                                               }
                                               
                                                  
                                           $sql = "SELECT " 
                                                        . implode(',',$colArr)
                                                        . " FROM $inspectiondispatch_table "
                                                        . " WHERE "
                                                        . " $inspectiondispatch_table.entity_ID = $entityID" 
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
                                             
                                             
                                             
                                                $this->tpl->set('table_columns_label_arr', array('ID','Po Reference','Description of PO','week'));
                                                
                                                /*
                                                 * selectColArr for filter form
                                                 */
                                                
                                                $this->tpl->set('selectColArr',$colArr);
                                                            
                                                /*
                                                 * set pagination template
                                                 */
                                                $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                                                       
                                                //////////////////////close//////////////////////////////////////  
                                                         
                                                        include_once $this->tpl->path . '/factory/form/crud_inspection_dispatch_form.php';
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
