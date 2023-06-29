<?php

/**
 * Description of Purchase_Mod
 *
 * @author psmahadevan
 */
class Purchase_Mod {

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

////////////////////// Purchase_Indent_Form ////////
    
function manage(){
     
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
            $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
            $unit_table = $this->crg->get('table_prefix') . 'unit';
            $indent_detail_tab = $this->crg->get('table_prefix') . 'purchaseindentdetail';
            $indent_master_tab = $this->crg->get('table_prefix') . 'purchaseindent';
            $user_table = $this->crg->get('table_prefix') . 'users';
            $approvaltype_tab = $this->crg->get('table_prefix') . 'approvaltype';
            $approvalprocess_tab = $this->crg->get('table_prefix') . 'approvalprocess';
            
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
            
            $sql = "SELECT approver_ID FROM $approvaltype_tab where $approvaltype_tab.ProcessTypeName='Purchase Indent'"; 
            $stmt = $this->db->prepare($sql);            
            $stmt->execute();
            $approvaltype_data = $stmt->fetchAll();	
            $approve=$approvaltype_data;
           
            $sql = "SELECT $user_table.ID,$user_table.user_nicename FROM $user_table,$approvaltype_tab where $user_table.ID=$approvaltype_tab.approver_ID AND $approvaltype_tab.ProcessTypeName='Purchase Indent'"; 
            $stmt = $this->db->prepare($sql);            
            $stmt->execute();
            $approver_data = $stmt->fetchAll();	
            $this->tpl->set('approver_data', $approver_data);
           
        
            $this->tpl->set('page_title', 'Purchase Indent');	          
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
            //Confirm Submit
             if (!empty($_POST['confirm_submit_button']) && $_POST['confirm_submit_button'] == 'confirm') {
                $crud_string = 'confirm';
            }
            //Add submit
            if (!empty($_POST['add_submit_button']) && $_POST['add_submit_button'] == 'add') {
                $crud_string = 'addsubmit';
            }
            if (isset($_SESSION['req_from_list_view'])) {
                $crud_string = strtolower($_SESSION['req_from_list_view']);
                unset($_SESSION['req_from_list_view']);
            }  

            switch ($crud_string) {
                 case 'delete':                    
                      $data = trim($_POST['ycs_ID']);
                       
                       
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                       
                    }
                          $sqlsrr = "SELECT $indent_master_tab.Status FROM  `$indent_master_tab` WHERE `$indent_master_tab`.`ID` = '$data'";                    
                          $indent_data = $dbutil->getSqlData($sqlsrr); 
                         
                        if(!empty($indent_data) && $indent_data[0]['Status']==0){
                             
                               $sqldetdelete="Delete $indent_detail_tab,$indent_master_tab from $indent_master_tab
                                        LEFT JOIN  $indent_detail_tab ON $indent_master_tab.ID=$indent_detail_tab.purchaseindent_ID 
                                        where $indent_detail_tab.purchaseindent_ID=$data";  
                     
                                $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Purchase Indent deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/purchase/pur/manage');
                        // $this->tpl->set('label', 'List');
                        // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                        } 
                        }else{
                        $this->tpl->set('message', 'This Purchase Indent form cannot be deleted because it is already in approved state.');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
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
                                
                    $sqlsrr = "SELECT $indent_master_tab.*,$indent_detail_tab.*,$rawmaterial_table.Grade FROM  `$indent_master_tab` LEFT JOIN `$indent_detail_tab` ON  `$indent_master_tab`.`ID`=`$indent_detail_tab`.`purchaseindent_ID` LEFT JOIN `$rawmaterial_table` ON  `$indent_detail_tab`.`rawmaterial_ID`=`$rawmaterial_table`.`ID`  WHERE `$indent_master_tab`.`ID` = '$data' ORDER BY $indent_detail_tab.ID ASC";                    
                    $indent_data = $dbutil->getSqlData($sqlsrr); 
                  
                    //edit option     
                    $this->tpl->set('message', 'You can view Purchase Indent form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/purchase_indent_form.php'));                    
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
                                
                    $sqlsrr = "SELECT $indent_master_tab.*,$indent_detail_tab.*,$rawmaterial_table.Grade FROM  `$indent_master_tab` LEFT JOIN `$indent_detail_tab` ON  `$indent_master_tab`.`ID`=`$indent_detail_tab`.`purchaseindent_ID` LEFT JOIN `$rawmaterial_table` ON  `$indent_detail_tab`.`rawmaterial_ID`=`$rawmaterial_table`.`ID`  WHERE `$indent_master_tab`.`ID` = '$data' ORDER BY $indent_detail_tab.ID ASC";                    
                    $indent_data = $dbutil->getSqlData($sqlsrr); 
                
                    //edit option     
                    $this->tpl->set('message', 'You can edit Purchase Indent form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/purchase_indent_form.php'));                    
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

                    
                    $sqldet_del = "DELETE FROM $indent_detail_tab WHERE purchaseindent_ID=$data";
                    $stmt = $this->db->prepare($sqldet_del);
                    $stmt->execute();   
                            
                            try{
                            
                            $indent_no= $form_post_data['PurchaseIndentNo'];
                            
                            $sql_update="Update $indent_master_tab SET PurchaseIndentNo='$indent_no' WHERE ID=$data";
                            $stmt1 = $this->db->prepare($sql_update);
                            
                        if($stmt1->execute()){
                        
                        FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                                
                                $rawmaterialname =$form_post_data['rawmaterial_' . $entry_count];
                                $quantity =$form_post_data['Quantity_' . $entry_count];
                                $unit_id =$form_post_data['unit_' . $entry_count];
                                $req_for_pono =$form_post_data['Note_' . $entry_count];
                                $req_on=!empty($form_post_data['Emp_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['Emp_'. $entry_count])):'';
                                $remarks =$form_post_data['Water_' . $entry_count];
                                
                               
                            if(!empty($rawmaterialname) && !empty($quantity) && !empty($unit_id) ){
                               
                                $vals = "'" . $data . "'," .
                                        "'" . $rawmaterialname . "'," .
                                        "'" . $quantity . "'," .
                                        "'" . $unit_id . "'," .
                                        "'" . $req_for_pono . "'," .
                                        "'" . $req_on . "'," .
                                        "'" . $remarks . "'" ;
  
                                $sql2 = "INSERT INTO $indent_detail_tab
                                        ( 
                                            `purchaseindent_ID`, 
                                            `rawmaterial_ID`,
                                            `Quantity`,
                                            `unit_ID`,
                                            `PONo`,
                                            `RequiredOn`,
                                            `Remarks`
                                        ) 
                                VALUES ($vals)";

                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                            }
                        }
                            $this->tpl->set('message', 'Purchase Indent form edited successfully!');   
                            header('Location:' . $this->crg->get('route')['base_path'] . '/purchase/pur/manage');
                           
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/purchase_indent_form.php'));
                            }

                    break;
                 case 'confirm':
                    if (isset($crud_string)) {
                            $form_post_data = $dbutil->arrFltr($_POST);
                                               
                            
                            $data=$form_post_data['ycs_ID'];
                            $approved_by= $form_post_data['ApprovedBy'];
                           
                            $sql_update="Update $approvalprocess_tab set ApprovalStatus=1 WHERE process_ID=$data and ProcessType='Purchase Indent'";
                            $stmt1 = $this->db->prepare($sql_update);
                            $stmt1->execute();
                            
                            $sql_update="Update $indent_master_tab set Status=1,ApprovedBy='$approved_by' WHERE ID=$data";
                            $stmt = $this->db->prepare($sql_update);
                            $stmt->execute();
                            
                            
                            $this->tpl->set('message', 'Purchase Indent form Confirmed successfully!');   
                            // $this->tpl->set('label', 'List');
                            header('Location:' . $this->crg->get('route')['base_path'] . '/purchase/pur/manage');
                            $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            
                    }
                break;
                case 'addsubmit':
                     if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                    
					
                                $val =  "'" . $form_post_data['PurchaseIndentNo'] . "'," .
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";

                              $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "purchaseindent`
                                            ( 
                                            `PurchaseIndentNo`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                    VALUES ( $val )";
                                    $stmt = $this->db->prepare($sql);
                                    if ($stmt->execute()) { 
                        $lastInsertedID = $this->db->lastInsertId();
                        $dbutil->ApprovalProcess('Purchase Indent',$approve[0][0],$lastInsertedID);
                        FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                               
                                $rawmaterialname =$form_post_data['rawmaterial_' . $entry_count];
                                $quantity =$form_post_data['Quantity_' . $entry_count];
                                $unit_id =$form_post_data['unit_' . $entry_count];
                                $req_for_pono =$form_post_data['Note_' . $entry_count];
                                $req_on=!empty($form_post_data['Emp_'. $entry_count])?date("Y-m-d", strtotime($form_post_data['Emp_'. $entry_count])):'';
                                $remarks =$form_post_data['Water_' . $entry_count];
                                
                            if(!empty($rawmaterialname) && !empty($quantity) && !empty($unit_id) ){
                                $vals = "'" . $lastInsertedID . "'," .
                                        "'" . $rawmaterialname . "'," .
                                        "'" . $quantity . "'," .
                                        "'" . $unit_id . "'," .
                                        "'" . $req_for_pono . "'," .
                                        "'" . $req_on . "'," .
                                        "'" . $remarks . "'" ;
  
                                 
                                $sql2 = "INSERT INTO $indent_detail_tab
                                        ( 
                                            `purchaseindent_ID`, 
                                            `rawmaterial_ID`,
                                            `Quantity`,
                                            `unit_ID`,
                                            `PONo`,
                                            `RequiredOn`,
                                            `Remarks`
                                        ) 
                                VALUES ($vals)";

                                 // this need to be changed in to transaction type
                                
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                               
                            }
                        }
                        
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/purchase/pur/manage');
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/purchase_indent_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Production');
	                $PurchaseIndentNo=$dbutil->keyGeneration('purchaseindent','PUI','','PurchaseIndentNo');
                    $this->tpl->set('PurchaseIndentNo', $PurchaseIndentNo);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/purchase_indent_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$indent_master_tab.ID",
                "$indent_master_tab.PurchaseIndentNo"
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
             $whereString ="ORDER BY $indent_master_tab.ID DESC";
           }
           
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $indent_master_tab  "
                    . " WHERE "
                    . " $indent_master_tab.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Purchase Indent No'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_form_purchase_indent.php';
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
    
///////////////////////////////////////////////////

////////////////////// Purchase_Order_Form ////////
    
 function purchaseorder(){
     
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
            
            //indent table data
           
            $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1 and $indent_master_tab.PI_Status=1 AND entity_ID = $entityID ORDER BY $indent_master_tab.ID DESC";
            $stmt = $this->db->prepare($sql);            
            $stmt->execute();
            $indent_data  = $stmt->fetchAll();	
            $this->tpl->set('indent_data', $indent_data);
            
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
     
            $this->tpl->set('page_title', 'Purchase Order');	          
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
                 case 'delete':                    
                      $data = trim($_POST['ycs_ID']);
                       
                       
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                       
                    }
                   
                    $sqlsrr = "SELECT * FROM $po_master_tab WHERE ID=$data";
                    $po_data = $dbutil->getSqlData($sqlsrr); 
                    
                    $sqlsrr = "SELECT * FROM  $po_detail_tab WHERE purchaseorder_ID=$data";
                    $podet_data = $dbutil->getSqlData($sqlsrr); 
                    
                    $purchase_indentno=(count($po_data)>0)?$po_data[0]['purchaseindent_ID']:'';
                     
                    $sqlsrr = "SELECT  * FROM  $indent_det_tab WHERE $indent_det_tab.purchaseindent_ID = '$purchase_indentno'";
                    $indentdet_data = $dbutil->getSqlData($sqlsrr);
                    
                     
                     if(!empty($purchase_indentno)){
                         $sql_update="Update $indent_master_tab SET $indent_master_tab.PI_Status=1 WHERE  $indent_master_tab.ID=$purchase_indentno";
                                     $masterstmt1 = $this->db->prepare($sql_update);
                                     $masterstmt1->execute();
                                     
                        foreach($podet_data as $k=>$v){
                            
                            foreach($indentdet_data as $kk=>$value){
                                
                                if(($value['rawmaterial_ID']==$v['rawmaterial_ID'])){
                                    
                                    $sql_updates="Update $indent_det_tab SET $indent_det_tab.ItemStatus=1,$indent_det_tab.RaisedPOQty=(RaisedPOQty-$v[POQuantity]) WHERE $indent_det_tab.ID=$value[ID]";
                                    $detstmt1 = $this->db->prepare($sql_updates);
                                  $detstmt1->execute();
                                 
                                }
                                
                            }
                            
                        }
                                     
                          
                     }
                     
					$sqldetdelete="DELETE $po_detail_tab,$po_master_tab FROM $po_master_tab
								   LEFT JOIN  $po_detail_tab ON $po_master_tab.ID=$po_detail_tab.purchaseorder_ID 
								   WHERE $po_master_tab.ID=$data";  
					$stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Purchase Order deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/purchase/pur/purchaseorder');
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
                                
                     $sqlsrr = "SELECT  $po_master_tab.*,
                                	   $po_detail_tab.*,
                                       $rawmaterial_table.RMName,
                                       $supplier_tab.SupplierName,
                                       $supplier_tab.AddressLine1,
                                       $supplier_tab.AddressLine2,
                                       $supplier_tab.City,
                                       $state_tab.StateName,
                                       $unit_table.UnitName
                               FROM  $po_master_tab
                               LEFT JOIN $po_detail_tab ON $po_master_tab.ID=$po_detail_tab.purchaseorder_ID
                               LEFT JOIN $rawmaterial_table ON  $po_detail_tab.rawmaterial_ID=$rawmaterial_table.ID
                               LEFT JOIN $supplier_tab  ON $supplier_tab.ID=$po_master_tab.supplier_ID 
                               LEFT JOIN $state_tab  ON $state_tab.ID=$supplier_tab.State_ID 
                               LEFT JOIN $unit_table  ON $unit_table.ID=$po_detail_tab.unit_ID 
                               WHERE $po_master_tab.ID = '$data' 
                               ORDER BY $po_detail_tab.ID ASC";
                    $indent_data = $dbutil->getSqlData($sqlsrr); 
                    
                            
                    $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1";
                    $stmt = $this->db->prepare($sql);            
                    $stmt->execute();
                    $indentsel_data  = $stmt->fetchAll();	
                    $this->tpl->set('indent_data', $indentsel_data);
                  
                    //edit option     
                    $this->tpl->set('message', 'You can view Purchase Order form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/po_order_form.php'));                    
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
                                
                    $sqlsrr = "SELECT  $po_master_tab.*,
                                	   $po_detail_tab.*,
                                       $rawmaterial_table.RMName,
                                       $supplier_tab.SupplierName,
                                       $supplier_tab.AddressLine1,
                                       $supplier_tab.AddressLine2,
                                       $supplier_tab.City,
                                       $state_tab.StateName,
                                       $unit_table.UnitName
                               FROM  $po_master_tab
                               LEFT JOIN $po_detail_tab ON $po_master_tab.ID=$po_detail_tab.purchaseorder_ID
                               LEFT JOIN $rawmaterial_table ON  $po_detail_tab.rawmaterial_ID=$rawmaterial_table.ID
                               LEFT JOIN $supplier_tab  ON $supplier_tab.ID=$po_master_tab.supplier_ID 
                               LEFT JOIN $state_tab  ON $state_tab.ID=$supplier_tab.State_ID 
                               LEFT JOIN $unit_table  ON $unit_table.ID=$po_detail_tab.unit_ID 
                               WHERE $po_master_tab.ID = '$data' 
                               ORDER BY $po_detail_tab.ID ASC";
                    $indent_data = $dbutil->getSqlData($sqlsrr); 
                    
                     $sql = "SELECT ID,PurchaseIndentNo FROM $indent_master_tab WHERE $indent_master_tab.Status=1";
                    $stmt = $this->db->prepare($sql);            
                    $stmt->execute();
                    $indentsel_data  = $stmt->fetchAll();	
                    $this->tpl->set('indent_data', $indentsel_data);
                
                    //edit option     
                    $this->tpl->set('message', 'You can edit Purchase Order form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('FmData', $indent_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/po_order_form.php'));                    
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

                    
                    $sqldet_del = "DELETE FROM $po_detail_tab WHERE purchaseorder_ID=$data";
                    $stmt = $this->db->prepare($sqldet_del);
                    $stmt->execute();   
                            
                            try{
                                
                                  // pdf generation start
						
						
require_once('TCPDF/proformaChallen.php');
ob_start(); // at the beggining of your script

// create new PDF document

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// set document information

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Senthamizh Enterprises');
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
 $PurchaseOrderNo= $form_post_data['PurchaseOrderNo']; 
                            
                            $indent_no= $form_post_data['purchaseindent_ID'];
							$supplier_ID= $form_post_data['supplier_ID'];
							$discnt_percent= $form_post_data['DiscountPercent'];
							$discnt_amt= $form_post_data['DiscountAmount'];
							$gsttax= $form_post_data['GSTTax'];
							$igsttax= $form_post_data['IGSTTax'];
							$cgsttax= $form_post_data['CGSTTax'];
							$sgsttax= $form_post_data['SGSTTax'];
							$igstamt= $form_post_data['IGSTAmount'];
							$csgstamt= $form_post_data['CGSTAmount'];
							$sgstamt= $form_post_data['SGSTAmount'];
							$billamt= $form_post_data['BillAmount'];
							$netamt= $form_post_data['NetAmount'];
							 $purchaseorder= $form_post_data['purchaseorder'];
							$paymentterms= $form_post_data['PaymentTerms'];
							$dispatch_date=!empty($form_post_data['DispatchDate'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
						    $delivery_date=!empty($form_post_data['DeliveryDate'])?date("Y-m-d", strtotime($form_post_data['DeliveryDate'])):'';
                            $earlywarningpolicy= $form_post_data['EarlyWarningPolicy'];
							$freightcharges= $form_post_data['FreightCharges'];
							 $inspection= $form_post_data['inspection'];
							$name_of_transport= $form_post_data['NameOfTransport'];
							$test_certificate= $form_post_data['TestCertificate'];
							$failure_or_damage= $form_post_data['Failure_or_Damage'];
							$special_note= $form_post_data['SpecialNote'];                
                                            
                             
                            
                            $sql_update="Update $po_master_tab SET  purchaseindent_ID='$indent_no',
                                                                    supplier_ID='$supplier_ID',
										                            DiscountPercent='$discnt_percent',
										                            DiscountAmount='$discnt_amt',
										                            GSTTax='$gsttax',
										                            IGSTTax='$igsttax',
										                            CGSTTax='$cgsttax',
										                            SGSTTax='$sgsttax',
										                            IGSTAmount='$isgstamt',
                            										CGSTAmount='$csgstamt',
                            										SGSTAmount='$sgstamt',
                            										BillAmount='$billamt',
                            										NetAmount='$netamt',
                            										 purchaseorder='$purchaseorder',
                            										PaymentTerms='$paymentterms',
                            										DispatchDate='$dispatch_date',
                            										DeliveryDate='$delivery_date',
                            										EarlyWarningPolicy='$earlywarningpolicy',
                            										FreightCharges='$freightcharges',
                            										 inspection='$inspection',
                            										NameOfTransport='$name_of_transport',
                            										TestCertificate='$test_certificate',
                            										Failure_or_Damage='$failure_or_damage',
                            										SpecialNote='$special_note',
                            										trans_date= CURDATE()
                                							 WHERE  ID=$data";
                            $stmt1 = $this->db->prepare($sql_update);
                            
                        if($stmt1->execute()){
                        
                        FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                                
                                $itemid =$form_post_data['ItemNo_' . $entry_count]; 
								$packdet =$form_post_data['Packdet_' . $entry_count]; 
								$approved_qty =$form_post_data['Note_' . $entry_count];
								$raisedpo_qty =$form_post_data['RaisedPOQty_' . $entry_count];								
                                $po_qty =$form_post_data['Qty_' . $entry_count];
                                $unit_id =$form_post_data['unit_' . $entry_count];
								$price =$form_post_data['Emp_' . $entry_count];
                                $amount =$form_post_data['Amount_' . $entry_count];
                                
                               
                            if(!empty($itemid) && !empty($po_qty) && !empty($price) ){
                               
                                $vals = "'" . $data . "'," .
                                        "'" . $itemid . "'," .
                                        "'" . $packdet . "'," .
                                        "'" . $approved_qty . "'," .
                                        "'" . $raisedpo_qty . "'," .
                                        "'" . $po_qty . "'," .
                                        "'" . $po_qty . "'," .
                                        "'" . $unit_id . "'," .
										"'" . $price . "'," .
                                        "'" . $amount . "'" ;
  
                                $sql2 = "INSERT INTO $po_detail_tab
                                        ( 
                                            `purchaseorder_ID`, 
                                            `rawmaterial_ID`,
                                            `PackDetails`,
											`ApprovedQty`,
											`RaisedPOQty`,
											`extra_clm`,
											`POQuantity`,
											`unit_ID`,
                                            `Rate`,
                                            `Amount`
                                        ) 
                                VALUES ($vals)";

                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                            }
                        }
                        
                        // pdf start 
							
 $purchaseorder_data_tab = " SELECT $rawmaterial_table.RMName,$po_detail_tab.PackDetails, $po_detail_tab.POQuantity,$po_detail_tab.Amount,$po_detail_tab.Rate,$unit_table.UnitName FROM    $po_detail_tab LEFT JOIN $rawmaterial_table ON $rawmaterial_table.ID = $po_detail_tab.rawmaterial_ID LEFT JOIN $unit_table ON $unit_table.ID = $po_detail_tab.unit_ID where $po_detail_tab .purchaseorder_ID = $data ";
$stmt =$this->db->prepare( $purchaseorder_data_tab);            
$stmt->execute();		
$purchaseorder_data_tab_fetch = $stmt->fetchAll(2);   
// var_dump($delDcproduct); 
$count = 1;
foreach($purchaseorder_data_tab_fetch as $k=>$v){
    $troww .=  '<tr>
    <td style="width:40" align="left">'.$count.'</td>
    <td style="width:210" align="left">'. $v['RMName'].'</td>
    <td style="width:67" align="center">'. $v['PackDetails'].'  </td>
    <td style="width:79" align="right">'.$v["POQuantity"].'</td>
    <td style="width:80" align="right">'. $v["UnitName"].'</td>
    <td style="width:90" align="right">'. $v["Rate"].'</td>
    <td style="width:99" align="right">'. $v["Amount"].'</td>
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
    
// $companydetails = $this->crg->get('table_prefix') . "companydetails" ;
// $companydetails_data = "SELECT $companydetails.ID , $companydetails.Name, $companydetails.Bank, $companydetails.Branch, $companydetails.Account_Number, $companydetails.IFSC, $companydetails.Branch_code,$proforma_invoice_tab .GST_NO from $companydetails, $proforma_invoice_tab where  $proforma_invoice_tab .ID = $companydetails.ID AND $companydetails.ID = $lastInsertedID ";		
// $stmt =$this->db->prepare($companydetails_data);            
// $stmt->execute();	;
// $companydetails_fetch = $stmt->fetchAll(2);		

// $Name = $companydetails_fetch[0]['Name'];		
// $Bank = $companydetails_fetch[0]['Bank'];	
// $Branch = $companydetails_fetch[0]['Branch'];
// $Account_Number = $companydetails_fetch[0]['Account_Number'];
// $IFSC = $companydetails_fetch[0]['IFSC'];
// $Branch_code = $companydetails_fetch[0]['Branch_code'];										

$supplier_ID=$form_post_data['supplier_ID'];
$supplier = $this->crg->get('table_prefix') . "supplier" ;
$state = $this->crg->get('table_prefix') . "state" ;

$supplier_data = "SELECT $supplier.SupplierName, $supplier.AddressLine1, $supplier.AddressLine2, $supplier.City,$state.StateName from $supplier,$state where $supplier.State_ID = $state.ID AND $supplier.ID = $supplier_ID ";		
$stmt =$this->db->prepare($supplier_data);            
$stmt->execute();	
$supplier_fetch = $stmt->fetchAll(2);		

$SupplierName = $supplier_fetch[0]['SupplierName'];		
$AddressLine1 = $supplier_fetch[0]['AddressLine1'];	
$AddressLine2 = $supplier_fetch[0]['AddressLine2'];
$City = $supplier_fetch[0]['City'];
$StateName = $supplier_fetch[0]['StateName'];



$DiscountPercent=strval($form_post_data['DiscountPercent'].'%');
$DiscountAmount = strval((float) $form_post_data['DiscountAmount']);
$CGSTTax=strval($form_post_data['CGSTTax'].'%');
$SGSTTax=strval($form_post_data['SGSTTax'].'%');
$IGSTTax=strval($form_post_data['IGSTTax'].'%');
$CGSTAmount = strval((float) $form_post_data['CGSTAmount']); 
$SGSTAmount = strval((float) $form_post_data['SGSTAmount']);							
$IGSTAmount = strval((float) $form_post_data['IGSTAmount']);
$NetAmount = strval((float) $form_post_data['NetAmount']);     
// print_r($NetAmount);

// $netamountcopy=$netAmount;



$PaymentTerms=$form_post_data['PaymentTerms'];
$DeliveryDate=$form_post_data['DeliveryDate'];
$DispatchDate=$form_post_data['DispatchDate'];
$Company=$form_post_data['Company'];
$NameOfTransport=$form_post_data['NameOfTransport'];
$FreightCharges=$form_post_data['FreightCharges'];
$SpecialNote=$form_post_data['SpecialNote'];
$inspection=$form_post_data['inspection'];
$EarlyWarningPolicy=$form_post_data['EarlyWarningPolicy'];


// $ordernumber=$dbutil->keyGeneration('purchaseorder','PON','','purchaseorder_ID');
            


$html =  <<<EOD

<table cellspacing="0" cellpadding="4">


<tr>
<td>
</td>
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
<tr><td width="665" align="center" style="font-size:150%;">	<b>PURCHASE ORDER</b>		</td></tr>


</table>      
<table cellspacing="0" cellpadding="4" border="1" style="font-size:110%;">                          
<tr>
<td width="333"> Customer Name and Address:<b>$SupplierName </b> <br>
$AddressLine1 ,
$AddressLine2 , 
$City ,
$StateName . 
<br>
GST No:$GSTNO<br>
Person Contact:$Contact</td>
<td width="333">  Purchase Order Number:$PurchaseOrderNo <br>  </td>

</tr>
<tr>
<td width="333" > Invoice and ship to: <br>
M/s Meena Fiberglas Industries,<br> 
R.S.No, 151/3, Cuddalore Pondy Main Road ,<br> 
Kattukuppam ,<br>
Puducherry  607002<br> 					
GST No:34AAEPG3282E1Z4</td>
<td width="332" align="bottom">  Purchase Order Date :$date  <br></td>
</tr>



</table>
<table cellspacing="0" cellpadding="4" style="font-size:120%;"> 

<tr>
<td></td>
</tr>

</table>
<table cellspacing="0" cellpadding="3" border="1">		
<tr>
<td width="40" align="center" ><b>S.No</b></td>
<td width="210" align="center" ><b>Name of the Material</b></td>
<td width="76" align="center" ><b>Pack Details</b></td>
<td width="70" align="center" > <b>Quantity</b></td>
<td width="80" align="center" ><b>UOM</b></td>
<td width="90" align="center" ><b>Price/Unit</b></td>
<td width="99" align="center" ><b>Total</b></td>

</tr>

{$troww}


<tr>
<td width="533" align="right" >Discount: </td>
<td width="66" align="right" > $DiscountPercent</td>
<td width="66" align="right" >$DiscountAmount</td>
</tr>
<tr>
<td width="533" align="right" >CGST: </td>
<td width="66" align="right" > $CGSTTax</td>
<td width="66" align="right">$CGSTAmount</td>
</tr>
<tr>
<td width="533" align="right" >SGST: </td>
<td width="66" align="right" >$SGSTTax</td>
<td width="66" align="right" >$SGSTAmount</td>
</tr>
<tr>
<td width="533" align="right" >IGST: </td>
<td width="66" align="right" >$IGSTTax</td>
<td width="66" align="right" >$IGSTAmount</td>
</tr>

<tr>
<td width="599" align="right" ><b>Grand Total</b></td>
<td width="66" align="right" ><b>$NetAmount</b></td>
</tr>            
</table> 



<table cellspacing="0" cellpadding="4" style="line-height: 0.8; font-size:120%;">	
<tr>
<td width="665" align="left"></td>
</tr>
<tr>
<td width="665" align="left" ><b>TERMS AND CONDITIONS</b></td>
</tr>
<tr>
<td width="120" align="left" >Payment Terms</td>
<td width="30" align="center">:</td>
<td width="515" align="left">$PaymentTerms</td> 
</tr>    
<tr>
<td width="120" align="left">Dispatch Date</td>
<td width="30" align="center">:</td>
<td width="515" align="left" >$DispatchDate </td>
</tr>     
<tr>
<td width="120" align="left" >Delivery Date </td>
<td width="30" align="center"> :</td>
<td width="515" align="left" >$DeliveryDate</td>
</tr>
<tr>
<td width="120" align="left">Name of Transport</td>
<td width="30" align="center">:</td>
<td width="515" align="left">$NameOfTransport</td>
</tr>
<tr>
<td width="120" align="left" >Freight Charges</td>
<td width="30" align="center">:</td>
<td width="515" align="left" >$FreightCharges</td>
</tr>

<tr>
<td width="120" align="left" >Special Condition</td>
<td width="30" align="center">:</td>
<td width="515" align="left" >$SpecialNote</td>
</tr> 
<tr>
<td width="120" align="left" >Warranty/Expiry</td>
<td width="30" align="center">:</td>
<td width="515" align="left" >$EarlyWarningPolicy</td>
</tr> 
<tr>
<td width="120" align="left" >Inspection</td>
<td width="30" align="center">:</td>
<td width="515" align="left" >$inspection</td>
</tr> 
<tr>
<td width="665" align="left" >Note to be considered</td>
</tr>		
<tr>
<td width="40" align="right">1.</td>
<td width="625" align="left" >Please mention the Purchase Order number & Date in the Invoice.</td>     
</tr>
<tr>
<td width="40" align="right">2.</td>
<td width="625" align="left" >Kindly send your order acceptance within 2 working days from the date of
issuance of Purchase Order.</td>     
</tr>
<tr>
<td width="40" align="right"></td>     
</tr>
<tr>
<td width="330" align="left" >Created by</td> 
<td width="335" align="right" >Approved by</td> 
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
 $pdf->Output($_SERVER['DOCUMENT_ROOT'] ."/resource/purchaseorder/Invoice".$data.".pdf", "F");
// $pdf->Output("C:/xampp/htdocs/mfg18/resource/purchaseorder/Invoice".$data.".pdf", "F");
//  $pdf->Output("C:/xampp/htdocs/mfg18/resource/purchaseorder/Invoice".$data.".pdf", 'I');
$this->ses->set('pdffile', "/resource/purchaseorder/Invoice".$data.".pdf");
       
       
       
       
       
       
       // TCPDF END



// pdf End
                        
                            $this->tpl->set('message', 'Purchase Order form edited successfully!');   
                            header('Location:' . $this->crg->get('route')['base_path'] . '/purchase/pur/purchaseorder');
                           
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/po_order_form.php'));
                            }

                    break;
					
                case 'addsubmit':
                    
					if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
						$dispatch_date=!empty($form_post_data['DispatchDate'])?date("Y-m-d", strtotime($form_post_data['DispatchDate'])):'';
						$delivery_date=!empty($form_post_data['DeliveryDate'])?date("Y-m-d", strtotime($form_post_data['DeliveryDate'])):'';
					    $purchase_indentno=$form_post_data['purchaseindent_ID'];
					    $PurchaseOrderNo=$form_post_data['PurchaseOrderNo']; 
					    
                                $val =  "'" . $form_post_data['PurchaseOrderNo'] . "'," .
										"'" . $purchase_indentno . "'," .
										"'" . $form_post_data['supplier_ID'] . "'," .
										"'" . $form_post_data['DiscountPercent'] . "'," .
										"'" . $form_post_data['DiscountAmount'] . "'," .
										"'" . $form_post_data['GSTTax'] . "'," .
										"'" . $form_post_data['IGSTTax'] . "'," .
										"'" . $form_post_data['CGSTTax'] . "'," .
										"'" . $form_post_data['SGSTTax'] . "'," .
										"'" . $form_post_data['IGSTAmount'] . "'," .
										"'" . $form_post_data['CGSTAmount'] . "'," .
										"'" . $form_post_data['SGSTAmount'] . "'," .
										"'" . $form_post_data['BillAmount'] . "'," .
										"'" . $form_post_data['NetAmount'] . "'," .
										 "'" . $form_post_data['purchaseorder'] . "'," .
										"'" . $form_post_data['PaymentTerms'] . "'," .
										"'" . $dispatch_date . "'," .
										"'" . $delivery_date . "'," .
										"'" . $form_post_data['EarlyWarningPolicy'] . "'," .
										"'" . $form_post_data['FreightCharges'] . "'," .
										 "'" . $form_post_data['inspection'] . "'," .
										"'" . $form_post_data['NameOfTransport'] . "'," .
										"'" . $form_post_data['TestCertificate'] . "'," .
										"'" . $form_post_data['Failure_or_Damage'] . "'," .
										"'" . $form_post_data['SpecialNote'] . "'," .
										            "CURDATE()," .
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";

                              $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "purchaseorder`
                                            ( 
                                            `PurchaseOrderNo`,
											`purchaseindent_ID`,
											`supplier_ID`,
											`DiscountPercent`,
											`DiscountAmount`,
                                            `GSTTax`,
                                            `IGSTTax`,
                                            `CGSTTax`,
                                            `SGSTTax`,
                                            `IGSTAmount`,
                                            `CGSTAmount`,
											`SGSTAmount`,
                                            `BillAmount`,
                                            `NetAmount`,
                                             `purchaseorder`,
											`PaymentTerms`,
											`DispatchDate`,
											`DeliveryDate`,
											`EarlyWarningPolicy`,
											`FreightCharges`,
											 `inspection`,
											`NameOfTransport`,
											`TestCertificate`,
											`Failure_or_Damage`,
											`SpecialNote`,
											`trans_date`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                    VALUES ( $val )";
                                    $stmt = $this->db->prepare($sql);
                        
						if ($stmt->execute()) { 
						
						$lastInsertedID = $this->db->lastInsertId();
						
						// pdf generation start
						
						
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


// Pdf End 
                        
                        FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                               
                                $itemid =$form_post_data['ItemNo_' . $entry_count]; 
								$packdet =$form_post_data['Packdet_' . $entry_count]; 
								$approved_qty =$form_post_data['Note_' . $entry_count];
								$raisedpo_qty =$form_post_data['RaisedPOQty_' . $entry_count];								
                                $po_qty =$form_post_data['Qty_' . $entry_count];
                                $unit_id =$form_post_data['unit_' . $entry_count];
								$price =$form_post_data['Emp_' . $entry_count];
                                $amount =$form_post_data['Amount_' . $entry_count];
                                
                            if(!empty($itemid) && !empty($po_qty) && !empty($price) ){
                                
                                $sql_update="Update $indent_det_tab SET RaisedPOQty=(RaisedPOQty+'$po_qty') WHERE $indent_det_tab.purchaseindent_ID=$purchase_indentno AND $indent_det_tab.rawmaterial_ID=$itemid AND $indent_det_tab.ItemStatus=1";   
					            $update_stmt = $this->db->prepare($sql_update);
					            $update_stmt->execute();
                                
								$vals = "'" . $lastInsertedID . "'," .
                                        "'" . $itemid . "'," .
                                        "'" . $packdet . "'," .
                                        "'" . $approved_qty . "'," .
                                        "'" . $raisedpo_qty . "'," .
                                         "'" . $po_qty . "'," .
                                        "'" . $po_qty . "'," .
                                        "'" . $unit_id . "'," .
										"'" . $price . "'," .
                                        "'" . $amount . "'" ;
  
                                 
                                $sql2 = "INSERT INTO $po_detail_tab
                                        ( 
                                            `purchaseorder_ID`, 
                                            `rawmaterial_ID`,
                                            `PackDetails`,
											`ApprovedQty`,
											`RaisedPOQty`,
											`extra_clm`,
											`POQuantity`,
											`unit_ID`,
                                            `Rate`,
                                            `Amount`
                                        ) 
                                VALUES ($vals)";
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                               
                            }
                               
                            }
                            
                             // pdf start 
							
						$purchaseorder_data_tab = " SELECT $rawmaterial_table.RMName,$po_detail_tab.PackDetails, $po_detail_tab.POQuantity,$po_detail_tab.Amount,$po_detail_tab.Rate,$unit_table.UnitName FROM    $po_detail_tab LEFT JOIN $rawmaterial_table ON $rawmaterial_table.ID = $po_detail_tab.rawmaterial_ID LEFT JOIN $unit_table ON $unit_table.ID = $po_detail_tab.unit_ID where $po_detail_tab .purchaseorder_ID  = $lastInsertedID ";
                        $stmt =$this->db->prepare( $purchaseorder_data_tab);            
                        $stmt->execute();		
                        $purchaseorder_data_tab_fetch = $stmt->fetchAll(2);   
                        // var_dump($delDcproduct); 
                        $count = 1;
                        foreach($purchaseorder_data_tab_fetch as $k=>$v){
                            $troww .=  '<tr>
                            <td style="width:40" align="left">'.$count.'</td>
                            <td style="width:210" align="left">'. $v['RMName'].'</td>
                            <td style="width:76" align="center">'. $v['PackDetails'].'  </td>
                            <td style="width:70" align="right">'.$v["POQuantity"].'</td>
                            <td style="width:80" align="right">'. $v["UnitName"].'</td>
                            <td style="width:90" align="right">'. $v["Rate"].'</td>
                            <td style="width:99" align="right">'. $v["Amount"].'</td>
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
                       
                        
                        // $companydetails = $this->crg->get('table_prefix') . "companydetails" ;
                        // $companydetails_data = "SELECT $companydetails.ID , $companydetails.Name, $companydetails.Bank, $companydetails.Branch, $companydetails.Account_Number, $companydetails.IFSC, $companydetails.Branch_code,$proforma_invoice_tab .GST_NO from $companydetails, $proforma_invoice_tab where  $proforma_invoice_tab .ID = $companydetails.ID AND $companydetails.ID = $lastInsertedID ";		
                        // $stmt =$this->db->prepare($companydetails_data);            
                        // $stmt->execute();	;
                        // $companydetails_fetch = $stmt->fetchAll(2);		
                        
                        // $Name = $companydetails_fetch[0]['Name'];		
                        // $Bank = $companydetails_fetch[0]['Bank'];	
                        // $Branch = $companydetails_fetch[0]['Branch'];
                        // $Account_Number = $companydetails_fetch[0]['Account_Number'];
                        // $IFSC = $companydetails_fetch[0]['IFSC'];
                        // $Branch_code = $companydetails_fetch[0]['Branch_code'];	

                        // Supplier Table info

                        $supplier_ID=$form_post_data['supplier_ID'];
                        $supplier = $this->crg->get('table_prefix') . "supplier" ;
                        $state = $this->crg->get('table_prefix') . "state" ;

                        $supplier_data = "SELECT $supplier.SupplierName, $supplier.AddressLine1, $supplier.AddressLine2, $supplier.City,$state.StateName from $supplier,$state where $supplier.State_ID = $state.ID AND $supplier.ID = $supplier_ID ";		
                        $stmt =$this->db->prepare($supplier_data);            
                        $stmt->execute();	
                        $supplier_fetch = $stmt->fetchAll(2);		
                        
                        $SupplierName = $supplier_fetch[0]['SupplierName'];		
                        $AddressLine1 = $supplier_fetch[0]['AddressLine1'];	
                        $AddressLine2 = $supplier_fetch[0]['AddressLine2'];
                        $City = $supplier_fetch[0]['City'];
                        $StateName = $supplier_fetch[0]['StateName'];
                       
                        // Purchase Order Number

                        $state = $this->crg->get('table_prefix') . "state" ;

                        $supplier_data = "SELECT $supplier.SupplierName, $supplier.AddressLine1, $supplier.AddressLine2, $supplier.City,$state.StateName from $supplier,$state where $supplier.State_ID = $state.ID AND $supplier.ID = $supplier_ID ";		
                        $stmt =$this->db->prepare($supplier_data);            
                        $stmt->execute();	
                        $supplier_fetch = $stmt->fetchAll(2);



                        $DiscountPercent=strval($form_post_data['DiscountPercent'].'%');
                         $DiscountAmount = strval((float) $form_post_data['DiscountAmount']);
                        $CGSTTax=strval($form_post_data['CGSTTax'].'%');
                        $SGSTTax=strval($form_post_data['SGSTTax'].'%');
                        $IGSTTax=strval($form_post_data['IGSTTax'].'%');
                        $CGSTAmount=strval($form_post_data['CGSTAmount']); 
                        $SGSTAmount = strval($form_post_data['SGSTAmount']);							
                        $IGSTAmount = strval((float) $form_post_data['IGSTAmount']);
                        $NetAmount = strval((float) $form_post_data['NetAmount']);     
                        
                        // $netamountcopy=$netAmount;
                       

                        //////////

                        $PaymentTerms=$form_post_data['PaymentTerms'];
                        $DeliveryDate=$form_post_data['DeliveryDate'];
                        $DispatchDate=$form_post_data['DispatchDate'];
                        $Company=$form_post_data['Company'];
                        $NameOfTransport=$form_post_data['NameOfTransport'];
                        $FreightCharges=$form_post_data['FreightCharges'];
                        $SpecialNote=$form_post_data['SpecialNote'];
                        $inspection=$form_post_data['inspection'];
                        $EarlyWarningPolicy=$form_post_data['EarlyWarningPolicy'];
                        
                        
                        // $ordernumber=$dbutil->keyGeneration('purchaseorder','PON','','purchaseorder_ID');
                                    


             $html =  <<<EOD
             
<table cellspacing="0" cellpadding="4" >


   <tr>
   <td>
   </td>
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
    <tr><td width="665" align="center" style="font-size:150%;">	<b>PURCHASE ORDER</b>		</td></tr>


</table>      
<table cellspacing="0" cellpadding="4" border="1" style="font-size:120%;">                          
                <tr>
                <td width="333"> Customer Name and Address: <b>  $SupplierName </b> <br>
                  $AddressLine1 ,
                  $AddressLine2 , 
                  $City ,
                  $StateName.
                 <br>
                GST No:$GSTNO<br>
                 Person Contact:$Contact</td>
                <td width="333">  Purchase Order Number:$PurchaseOrderNo <br>  </td>
               
                </tr>
                <tr>
                <td width="333" > Invoice and ship to: <br>
                  M/s Meena Fiberglas Industries,<br> 
                  R.S.No, 151/3, Cuddalore Pondy Main Road ,<br> 
                  Kattukuppam ,<br>
                  Puducherry – 607002<br> 					
                GST No:34AAEPG3282E1Z4</td>
                 <td width="332" align="bottom">  Purchase Order Date :$date  <br></td>
                </tr>
               
               

</table>
 <table cellspacing="0" cellpadding="4"> 

    <tr>
    <td></td>
    </tr>
    
</table>
        <table cellspacing="0" cellpadding="3" border="1" style="font-size:120%;">		
   <tr>
   <td width="40" align="center" ><b>S.No</b></td>
   <td width="210" align="center" ><b>Name of the Material</b></td>
   <td width="76" align="center" ><b>Pack Details</b></td>
   <td width="70" align="center" > <b>Quantity</b></td>
   <td width="80" align="center" ><b>UOM</b></td>
   <td width="90" align="center" ><b>Price/Unit</b></td>
   <td width="99" align="center" ><b>Total</b></td>

  </tr>
   
    {$troww}
    
    
    <tr>
    <td width="533" align="right" >Discount: </td>
    <td width="66" align="right" > $DiscountPercent</td>
    <td width="66" align="right" >$DiscountAmount</td>
    </tr>
    <tr>
    <td width="533" align="right" >CGST: </td>
    <td width="66" align="right" > $CGSTTax</td>
    <td width="66" align="right">$CGSTAmount</td>
    </tr>
    <tr>
    <td width="533" align="right" >SGST: </td>
    <td width="66" align="right" >$SGSTTax</td>
    <td width="66" align="right" >$SGSTAmount</td>
    </tr>
    <tr>
    <td width="533" align="right" >IGST: </td>
    <td width="66" align="right" >$IGSTTax</td>
    <td width="66" align="right" >$IGSTAmount</td>
    </tr>

    <tr>
    <td width="599" align="right" ><b>Grand Total</b></td>
    <td width="66" align="right" ><b>$NetAmount</b></td>
    </tr>            
   </table> 
             
   
     
   <table cellspacing="0" cellpadding="4" style="line-height: 0.8; font-size:120%;">	
   <tr>
   <td width="665" align="left"></td>
    </tr>
    <tr>
   <td width="665" align="left" ><b>TERMS AND CONDITIONS</b></td>
    </tr>
   <tr>
   <td width="120" align="left" >Payment Terms</td>
   <td width="30" align="center">:</td>
   <td width="515" align="left">$PaymentTerms</td> 
  </tr>    
    <tr>
    <td width="120" align="left">Dispatch Date</td>
    <td width="30" align="center">:</td>
    <td width="515" align="left" >$DispatchDate </td>
    </tr>     
    <tr>
    <td width="120" align="left" >Delivery Date </td>
    <td width="30" align="center"> :</td>
    <td width="515" align="left" >$DeliveryDate</td>
    </tr>
    <tr>
    <td width="120" align="left">Name of Transport</td>
    <td width="30" align="center">:</td>
    <td width="515" align="left">$NameOfTransport</td>
    </tr>
    <tr>
    <td width="120" align="left" >Freight Charges</td>
    <td width="30" align="center">:</td>
    <td width="515" align="left" >$FreightCharges</td>
    </tr>

    <tr>
    <td width="120" align="left" >Special Condition</td>
    <td width="30" align="center">:</td>
    <td width="515" align="left" ><b>$SpecialNote</b></td>
    </tr> 
    <tr>
    <td width="120" align="left" >Warranty/Expiry</td>
    <td width="30" align="center">:</td>
    <td width="515" align="left" ><b>$EarlyWarningPolicy</b></td>
    </tr> 
    <tr>
    <td width="120" align="left" >Inspection</td>
    <td width="30" align="center">:</td>
    <td width="515" align="left" ><b>$inspection</b></td>
    </tr> 
    <tr>
        <td width="665" align="left" >Note to be considered</td>
    </tr>		
    <tr>
    <td width="40" align="right">1.</td>
    <td width="625" align="left" >Please mention the Purchase Order number & Date in the Invoice.</td>     
    </tr>
    <tr>
    <td width="40" align="right">2.</td>
    <td width="625" align="left" >Kindly send your order acceptance within 2 working days from the date of
issuance of Purchase Order.</td>     
    </tr>
    <tr>
    <td width="40" align="right"></td>     
    </tr>
    <tr>
    <td width="330" align="left" >Created by</td> 
    <td width="335" align="right" >Approved by</td> 
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
</table>'
;
$pdf->writeHTMLCell(0, 0, '', '', $htl, 0, 1, 0, true, '', true); 
ob_end_clean();// at the end of your script
$pdf->Output($_SERVER['DOCUMENT_ROOT'] ."/resource/purchaseorder/Invoice".$lastInsertedID.".pdf", "F");
// $pdf->Output("C:/xampp/htdocs/mfg18/resource/purchaseorder/Invoice".$lastInsertedID.".pdf", "F");
// $pdf->Output("C:/xampp/htdocs/mfg18/resource/purchaseorder/Invoice".$lastInsertedID.".pdf", 'I');
$this->ses->set('pdffile', "/resource/purchaseorder/Invoice".$lastInsertedID.".pdf");
                               
                               
                               
                               
                               
                               
                               // TCPDF END
                        
                        
                        
                        // pdf End
                            
                        }
                        $sqlsrr = "SELECT  * FROM  $indent_det_tab WHERE $indent_det_tab.purchaseindent_ID = '$purchase_indentno'";
                        $indentdet_data = $dbutil->getSqlData($sqlsrr);
                        $checkunique=[];
                        
                             if(!empty($indentdet_data)){
                                 
                                 foreach($indentdet_data as $k=>$v){
                                     
                                     if($v['RaisedPOQty']>=$v['Quantity']){
                                         $checkunique[]=1;
                                         $sql_update="Update $indent_det_tab SET ItemStatus='2' WHERE  $indent_det_tab.purchaseindent_ID=$purchase_indentno AND $indent_det_tab.rawmaterial_ID=$v[rawmaterial_ID]";
                                         $stmt1 = $this->db->prepare($sql_update);
                                         $stmt1->execute();
                                         
                                     }else{
                                         $checkunique[]=2; 
                                     }
                                
                                     
                                 }
                                
                                 if(count($checkunique)>0){
                                    if(count(array_unique($checkunique))==1 && end($checkunique) == 1){
                                     $sql_update="Update $indent_master_tab SET PI_Status='2' WHERE  $indent_master_tab.ID=$purchase_indentno";
                                     $stmt1 = $this->db->prepare($sql_update);
                                     $stmt1->execute();
                                    } 
                                 }
                             }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/purchase/pur/purchaseorder');
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/po_order_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Production');
	                $PurchaseOrderNo=$dbutil->keyGeneration('purchaseorder','PUO','','PurchaseOrderNo');
                    $this->tpl->set('PurchaseOrderNo', $PurchaseOrderNo);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/po_order_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$po_master_tab.ID",
                "$po_master_tab.PurchaseOrderNo"
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
             $whereString ="ORDER BY $po_master_tab.ID DESC";
           }
           
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $po_master_tab  "
                    . " WHERE "
                    . " $po_master_tab.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Purchase Order No'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
             $this->tpl->set('dcpdf1','Generate Pdf'); 
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_form_purchase_order.php';
                    $cus_form_data = Form_Elements::data($this->crg);
                    include_once 'util/crud3_1.php';
                    new Crud3($this->crg, $cus_form_data);
                    break;
            }

	    ///////////////Use different template////////////////////
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
}
