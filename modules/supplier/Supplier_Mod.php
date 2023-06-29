<?php

/**
 * Description of Product_Mod
 *
 * @author psmahadevan
 */
class Supplier_Mod {

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
    
///////////////////// Supplier_Form//////////////////
    
function supplier(){
    
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
			 
			////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////access condition applied//////////////////////////
			////////////////////////////////////////////////////////////////////////////////    
						
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $supplier_table = $this->crg->get('table_prefix') . 'supplier';
			$state_table = $this->crg->get('table_prefix') . 'state';

            $state_sql = "SELECT ID,StateName FROM $state_table";
            $stmt = $this->db->prepare($state_sql);            
            $stmt->execute();
            $state_data  = $stmt->fetchAll();	
            $this->tpl->set('state_data', $state_data);
			
            $this->tpl->set('page_title', 'Supplier');	          
            $this->tpl->set('page_header', 'Supplier');
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
                     
                     $sqldetdelete="Delete from $supplier_table
                                        where $supplier_table.ID=$data"; 
                        $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'supplier form deleted successfully');
																													  
                        //$this->tpl->set('label', 'List');
                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/supplier');
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
                     FROM $supplier_table 
					 WHERE 
                     $supplier_table.ID = $data";   
                     $supplier_data = $dbutil->getSqlData($sqlsrr);
                   
                
                    //edit option     
                    $this->tpl->set('message', 'You can view supplier form');
                    $this->tpl->set('page_header', 'supplier');
                    $this->tpl->set('FmData', $supplier_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_design_form.php'));                    
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
                            FROM $supplier_table 
                            WHERE  $supplier_table.ID= $data";
                            $supplier_data = $dbutil->getSqlData($sqlsrr);
                   
                    
                    //edit option 

					
                    $this->tpl->set('message', 'You can edit Supplier form');
                    $this->tpl->set('page_header', 'Supplier');
                    $this->tpl->set('FmData', $supplier_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_design_form.php'));                    
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
		
					$ApprovedDate=date("Y-m-d", strtotime($form_post_data['ApprovedDate']));
					$CertificateValidity=date("Y-m-d", strtotime($form_post_data['CertificateValidity']));
					$RevaluationPeriod=!empty($form_post_data['RevaluationPeriod'])?date("Y-m-d", strtotime($form_post_data['RevaluationPeriod'])):'';
						                       
                    try{
                          $Nature= $form_post_data['Nature'];
                          $SupplierType= $form_post_data['SupplierType'];
						  $SupplierName= $form_post_data['SupplierName'];
						  $AddressLine1= $form_post_data['AddressLine1'];
						  $AddressLine2= $form_post_data['AddressLine2'];
                          $City= $form_post_data['City'];
						  $State_ID= $form_post_data['State_ID'];
						  $ContactPerson= $form_post_data['ContactPerson'];
						  $ContactNumber= $form_post_data['ContactNumber'];
						  $Email= $form_post_data['Email'];
						  $Code= $form_post_data['Code'];
						  $gst= $form_post_data['gst'];
						  $pan= $form_post_data['pan'];
						  $tan= $form_post_data['tan'];
						  $NatureOfWork= $form_post_data['NatureOfWork'];
						  $Items= $form_post_data['Items'];
						  $TypeOfControl= $form_post_data['TypeOfControl'];
						  $MaterialGrade= $form_post_data['MaterialGrade'];
						  $CustomerApproved= $form_post_data['CustomerApproved'];
						  $Applicable_Statutory_Requirements= $form_post_data['Applicable_Statutory_Requirements'];
						  $SupplierIsoCertified= $form_post_data['SupplierIsoCertified'];
						  $CertificateValidity= $CertificateValidity;
						  $AuditFrequency= $form_post_data['AuditFrequency'];
						  $ApprovedDate= $ApprovedDate;
						  $RevaluationPeriod= $RevaluationPeriod;
						  $SupplierDevelopment= $form_post_data['SupplierDevelopment'];
						  $Remarks= $form_post_data['Remarks'];
						  $Qms_status= $form_post_data['Qms_status'];
						  
							$sql_update="UPDATE $supplier_table set Nature='$Nature',SupplierType='$SupplierType',SupplierName='$SupplierName',AddressLine1='$AddressLine1',AddressLine2='$AddressLine2',City='$City',State_ID='$State_ID',ContactPerson='$ContactPerson',ContactNumber='$ContactNumber',Email='$Email',Code='$Code',gst='$gst',pan='$pan',tan='$tan',NatureOfWork='$NatureOfWork',Items='$Items',TypeOfControl='$TypeOfControl',MaterialGrade='$MaterialGrade',CustomerApproved='$CustomerApproved',Applicable_Statutory_Requirements='$Applicable_Statutory_Requirements',SupplierIsoCertified='$SupplierIsoCertified',CertificateValidity='$CertificateValidity',AuditFrequency='$AuditFrequency',ApprovedDate='$ApprovedDate',RevaluationPeriod='$RevaluationPeriod',SupplierDevelopment='$SupplierDevelopment',Remarks='$Remarks',Qms_status='$Qms_status' WHERE ID=$data";
                            $stmt1 = $this->db->prepare($sql_update);
                           // $stmt1->execute();
                            if($stmt1->execute()){
                                 $sql_update="UPDATE ycias_supplier_report  LEFT JOIN ycias_supplier ON ycias_supplier.ID=ycias_supplier_report.supplier_id 
								                  SET ycias_supplier_report.Qms = 5 where ycias_supplier_report.supplier_id = ycias_supplier.ID AND '$Qms_status'='Yes'";
                                     $update_stmt = $this->db->prepare($sql_update);
                                     $update_stmt->execute(); 
                                     
                                 $sql_update="UPDATE ycias_supplier_report  LEFT JOIN ycias_supplier ON ycias_supplier.ID=ycias_supplier_report.supplier_id 
								                  SET ycias_supplier_report.Qms = 0 where ycias_supplier_report.supplier_id = ycias_supplier.ID AND '$Qms_status'='No'";
                                     $update_stmt = $this->db->prepare($sql_update);
                                     $update_stmt->execute(); 
                            }
							
                            $this->tpl->set('message', 'Supplier form edited successfully!');   
																														  
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
							header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/supplier');
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_design_form.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
 
                        $form_post_data = $dbutil->arrFltr($_POST);
                        
                      // var_dump($_POST);
					 
					  
					   $ApprovedDate=date("Y-m-d", strtotime($form_post_data['ApprovedDate']));
					   $CertificateValidity=date("Y-m-d", strtotime($form_post_data['CertificateValidity']));
					   $RevaluationPeriod=!empty($form_post_data['RevaluationPeriod'])?date("Y-m-d", strtotime($form_post_data['RevaluationPeriod'])):'';
					   $Qms_status=$form_post_data['Qms_status'];
					   $SupplierName=$form_post_data['SupplierName'];
                       
					   if (isset($form_post_data['SupplierName'])) {
                           
                                        $val = "'" . $form_post_data['Nature'] . "'," .
                                         "'" . $form_post_data['SupplierType'] . "'," .
										 "'" . $form_post_data['SupplierName'] . "'," .
                                         "'" . $form_post_data['AddressLine1'] . "'," .
										 "'" . $form_post_data['AddressLine2'] . "'," .
                                         "'" . $form_post_data['City'] . "'," .
										 "'" . $form_post_data['State_ID'] . "'," .
                                         "'" . $form_post_data['ContactPerson'] . "'," .
										 "'" . $form_post_data['ContactNumber'] . "'," .
										 "'" . $form_post_data['Email'] . "'," .
										 "'" . $form_post_data['Code'] . "'," .
										 "'" . $form_post_data['gst'] . "'," .
										 "'" . $form_post_data['pan'] . "'," .
										 "'" . $form_post_data['tan'] . "'," .
                                         "'" . $form_post_data['NatureOfWork'] . "'," .
										 "'" . $form_post_data['Items'] . "'," .
                                         "'" . $form_post_data['TypeOfControl'] . "'," .
										 "'" . $form_post_data['MaterialGrade'] . "'," .
                                         "'" . $form_post_data['CustomerApproved'] . "'," .
										 "'" . $form_post_data['Applicable_Statutory_Requirements'] . "'," .
										 "'" . $form_post_data['SupplierIsoCertified'] . "'," .
                                         "'" . $CertificateValidity . "'," .
										 "'" . $form_post_data['AuditFrequency'] . "'," .
                                         "'" . $ApprovedDate . "'," .
										 "'" . $RevaluationPeriod . "'," .
                                         "'" . $form_post_data['SupplierDevelopment'] . "'," .
										 "'" . $form_post_data['Remarks'] . "'," .
								    	 "'" . $form_post_data['Qms_status'] . "'," .
                                              "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                         "'" .  $this->ses->get('user')['ID'] . "'";

                             $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "supplier`
                                            (
                                            `Nature`, 
                                            `SupplierType`, 
											`SupplierName`,
                                            `AddressLine1`,
											`AddressLine2`, 
                                            `City`,
                                            `State_ID`,
											`ContactPerson`,
                                            `ContactNumber`,
											`Email`,
											`Code`, 
										    `gst`,
											`pan`,
											`tan`, 
                                            `NatureOfWork`, 
											`Items`,
                                            `TypeOfControl`,
											`MaterialGrade`, 
                                            `CustomerApproved`,
                                            `Applicable_Statutory_Requirements`,
											`SupplierIsoCertified`,
                                            `CertificateValidity`,
											`AuditFrequency`,
											`ApprovedDate`,
                                            `RevaluationPeriod`,
											`SupplierDevelopment`,
											`Remarks`,
											`Qms_status`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                         VALUES ( $val )";
                                  $stmt = $this->db->prepare($sql);
							//	  $stmt->execute();
								 if($stmt->execute()){
								    $sql_update="UPDATE ycias_supplier_report  LEFT JOIN ycias_supplier ON ycias_supplier.ID=ycias_supplier_report.supplier_id 
								                  SET ycias_supplier_report.Qms = 5 where ycias_supplier_report.SupplierName ='$SupplierName' AND '$Qms_status'='Yes'";
                                     $update_stmt = $this->db->prepare($sql_update);
                                     $update_stmt->execute(); 
                                     
                                     $sql_update="UPDATE ycias_supplier_report  LEFT JOIN ycias_supplier ON ycias_supplier.ID=ycias_supplier_report.supplier_id 
								                  SET ycias_supplier_report.Qms = 0 where ycias_supplier_report.SupplierName ='$SupplierName' AND '$Qms_status'='No'";
                                     $update_stmt = $this->db->prepare($sql_update);
                                     $update_stmt->execute(); 
								 }
                                  
                        }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/supplier');
																										
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_design_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Supplier');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_design_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
         $colArr = array(
                "$supplier_table.ID",
				"$supplier_table.SupplierType",
                "$supplier_table.SupplierName",
				"$state_table.StateName"

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
             $whereString ="ORDER BY $supplier_table.ID DESC";
           }
		   
		      
       $sql = "SELECT " 
                    . implode(',',$colArr)
                    . " FROM $supplier_table LEFT JOIN $state_table ON $state_table.ID = $supplier_table.State_ID "
                    . " WHERE "
                    . " $supplier_table.entity_ID = $entityID" 
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
         
         
		 
            $this->tpl->set('table_columns_label_arr', array('ID','Supplier type','Supplier Name','State Name'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_supplier_form.php';
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
    
////////////////////////////////////////////////////
   
public function supplierEvaluation(){
    
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
			 
			////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////access condition applied//////////////////////////
			////////////////////////////////////////////////////////////////////////////////    
						
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $supplier_evaluation_table = $this->crg->get('table_prefix') . 'supplier_evaluation';
			$supplier_table = $this->crg->get('table_prefix') . 'supplier';
            
            $supplier_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE $supplier_table.SupplierType='Supplier' AND $supplier_table.Status=1";
            $stmt = $this->db->prepare($supplier_sql);            
            $stmt->execute();
            $supplier_data  = $stmt->fetchAll();	
            $this->tpl->set('supplier_data', $supplier_data);
			
			$supplier1_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE $supplier_table.SupplierType='Supplier' AND $supplier_table.Status=2";
            $stmt = $this->db->prepare($supplier1_sql);            
            $stmt->execute();
            $supplier1_data  = $stmt->fetchAll();	
            $this->tpl->set('supplier1_data', $supplier1_data);
			
            $this->tpl->set('page_title', 'Supplier Evaluation And Assessment');	          
            $this->tpl->set('page_header', 'Supplier Evaluation And Assessment');
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
					
					$sql_update="UPDATE $supplier_table SET Status=1 WHERE ID=(SELECT SupplierID FROM $supplier_evaluation_table WHERE $supplier_evaluation_table.ID= $data)";
                            $stmt1 = $this->db->prepare($sql_update);
					 $stmt1->execute();
                     
                     $sqldetdelete="Delete from $supplier_evaluation_table
                                        where $supplier_evaluation_table.ID=$data"; 
                        $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Supplier Evaluation And Assessment form deleted successfully');
																													  
                        //$this->tpl->set('label', 'List');
                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/supplierEvaluation');
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
                          
                    $sqlsrr = "SELECT *,$supplier_table.ContactPerson,$supplier_table.ContactNumber,$supplier_table.AddressLine1 FROM $supplier_evaluation_table 
				               LEFT JOIN $supplier_table ON $supplier_table.ID=$supplier_evaluation_table.SupplierID
                               WHERE  $supplier_evaluation_table.ID= $data";
                               $supplier_evaluation_data = $dbutil->getSqlData($sqlsrr);
                   
                
                    //edit option     
                    $this->tpl->set('message', 'You can view Supplier Evaluation And Assessment form');
                    $this->tpl->set('page_header', 'Supplier Evaluation And Assessment');
                    $this->tpl->set('FmData', $supplier_evaluation_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_evaluation_design_form.php'));                    
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
           			      
                $sqlsrr = "SELECT *,$supplier_table.SupplierName,$supplier_table.ContactPerson,$supplier_table.ContactNumber,$supplier_table.AddressLine1 FROM $supplier_evaluation_table 
				           LEFT JOIN $supplier_table ON $supplier_table.ID=$supplier_evaluation_table.SupplierID
                           WHERE  $supplier_evaluation_table.ID= $data";
                           $supplier_evaluation_data = $dbutil->getSqlData($sqlsrr);
                    
                    //edit option 

					
                    $this->tpl->set('message', 'You can edit Supplier Evaluation And Assessment form');
                    $this->tpl->set('page_header', 'Supplier Evaluation And Assessment');
                    $this->tpl->set('FmData', $supplier_evaluation_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_evaluation_design_form.php'));                    
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
                          $SupplierID= $form_post_data['SupplierID'];
						  $ActualRated= $form_post_data['ActualRated'];
                          $Remarks0= $form_post_data['Remarks0'];
						  $ActualRated1= $form_post_data['ActualRated1'];
						  $Remarks1= $form_post_data['Remarks1'];
						  $ActualRated2= $form_post_data['ActualRated2'];
						  $Remarks2= $form_post_data['Remarks2'];
						  $ActualRated3= $form_post_data['ActualRated3'];
						  $Remarks3= $form_post_data['Remarks3'];
						  $ActualRated4= $form_post_data['ActualRated4'];
						  $Remarks4= $form_post_data['Remarks4'];
						  $ActualRated5= $form_post_data['ActualRated5'];
						  $Remarks5= $form_post_data['Remarks5'];
						  $ActualRated6= $form_post_data['ActualRated6'];
						  $Remarks6= $form_post_data['Remarks6'];
						  $ActualRated7= $form_post_data['ActualRated7'];
						  $Remarks7= $form_post_data['Remarks7'];
						  $ActualRated8= $form_post_data['ActualRated8'];
						  $Remarks8= $form_post_data['Remarks8'];
						  $ActualRated9= $form_post_data['ActualRated9'];
						  $Remarks9= $form_post_data['Remarks9'];
						  $ActualRated10= $form_post_data['ActualRated10'];
						  $Remarks10= $form_post_data['Remarks10'];
						  $ActualRated11= $form_post_data['ActualRated11'];
						  $Remarks11= $form_post_data['Remarks11'];
						  $ActualRated12= $form_post_data['ActualRated12'];
						  $Remarks12= $form_post_data['Remarks12'];
						  $ActualRated13= $form_post_data['ActualRated13'];
						  $Remarks13= $form_post_data['Remarks13'];
						  $ActualRated14= $form_post_data['ActualRated14'];
						  $Remarks14= $form_post_data['Remarks14'];
						  $ActualRated15= $form_post_data['ActualRated15'];
						  $Remarks15= $form_post_data['Remarks15'];
						  $ActualRated16= $form_post_data['ActualRated16'];
						  $Remarks16= $form_post_data['Remarks16'];
						  $ActualRated17= $form_post_data['ActualRated17'];
						  $Remarks17= $form_post_data['Remarks17'];
						  $ActualRated18= $form_post_data['ActualRated18'];
						  $Remarks18= $form_post_data['Remarks18'];
						  $ActualRated19= $form_post_data['ActualRated19'];
						  $Remarks19= $form_post_data['Remarks19'];
						  $ActualRated20= $form_post_data['ActualRated20'];
						  $Remarks20= $form_post_data['Remarks20'];
						  $ActualRated21= $form_post_data['ActualRated21'];
						  $Remarks21= $form_post_data['Remarks21'];
						  $ActualRated22= $form_post_data['ActualRated22'];
						  $Remarks22= $form_post_data['Remarks22'];
						  $ActualRated23= $form_post_data['ActualRated23'];
						  $Remarks23= $form_post_data['Remarks23'];
						  $ActualRated24= $form_post_data['ActualRated24'];
						  $Remarks24= $form_post_data['Remarks24'];
						  $ActualRated25= $form_post_data['ActualRated25'];
						  $Remarks25= $form_post_data['Remarks25'];
						  $Supplier_Status= $form_post_data['Supplier_Status'];
						  $Action= $form_post_data['Action'];
						  $EvaluatedTeam= $form_post_data['EvaluatedTeam'];
						  
						 $sql_update="UPDATE $supplier_evaluation_table set SupplierID='$SupplierID',ActualRated='$ActualRated',Remarks0='$Remarks0',ActualRated1='$ActualRated1',Remarks1='$Remarks1',ActualRated2='$ActualRated2',Remarks2='$Remarks2',ActualRated3='$ActualRated3',Remarks3='$Remarks3',ActualRated4='$ActualRated4',Remarks4='$Remarks4',ActualRated5='$ActualRated5',Remarks5='$Remarks5',ActualRated6='$ActualRated6',Remarks6='$Remarks6',ActualRated7='$ActualRated7',Remarks7='$Remarks7',ActualRated8='$ActualRated8',Remarks8='$Remarks8',ActualRated9='$ActualRated9',Remarks9='$Remarks9',ActualRated10='$ActualRated10',Remarks10='$Remarks10',ActualRated11='$ActualRated11',Remarks11='$Remarks11',ActualRated12='$ActualRated12',Remarks12='$Remarks12',ActualRated13='$ActualRated13',Remarks13='$Remarks13',ActualRated14='$ActualRated14',Remarks14='$Remarks14',ActualRated15='$ActualRated15',Remarks15='$Remarks15',ActualRated16='$ActualRated16',Remarks16='$Remarks16',ActualRated17='$ActualRated17',Remarks17='$Remarks17',ActualRated18='$ActualRated18',Remarks18='$Remarks18',ActualRated19='$ActualRated19',Remarks19='$Remarks19',ActualRated20='$ActualRated20',Remarks20='$Remarks20',ActualRated21='$ActualRated21',Remarks21='$Remarks21',ActualRated22='$ActualRated22',Remarks22='$Remarks22',ActualRated23='$ActualRated23',Remarks23='$Remarks23',ActualRated24='$ActualRated24',Remarks24='$Remarks24',ActualRated25='$ActualRated25',Remarks25='$Remarks25',Supplier_Status='$Supplier_Status',Action='$Action',EvaluatedTeam='$EvaluatedTeam' WHERE ID=$data";
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
                       
                            $this->tpl->set('message', 'Supplier Evaluation And Assessment form edited successfully!');   
																														  
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
							header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/supplierEvaluation');
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_evaluation_design_form.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
 
                        $form_post_data = $dbutil->arrFltr($_POST);
                        
                      // var_dump($_POST);
					   
                        $entry_count = 1;
                       
					   if (isset($form_post_data['SupplierID'])) {
                           
                                        $val = "'" . $form_post_data['SupplierID'] . "'," .
										 "'" . $form_post_data['ActualRated'] . "'," .
                                         "'" . $form_post_data['Remarks0'] . "'," .
                                         "'" . $form_post_data['ActualRated1'] . "'," .
										 "'" . $form_post_data['Remarks1'] . "'," .
                                         "'" . $form_post_data['ActualRated2'] . "'," .
										 "'" . $form_post_data['Remarks2'] . "'," .
										 "'" . $form_post_data['ActualRated3'] . "'," .
                                         "'" . $form_post_data['Remarks3'] . "'," .
                                         "'" . $form_post_data['ActualRated4'] . "'," .
										 "'" . $form_post_data['Remarks4'] . "'," .
                                         "'" . $form_post_data['ActualRated5'] . "'," .
										 "'" . $form_post_data['Remarks5'] . "'," .
										 "'" . $form_post_data['ActualRated6'] . "'," .
                                         "'" . $form_post_data['Remarks6'] . "'," .
                                         "'" . $form_post_data['ActualRated7'] . "'," .
										 "'" . $form_post_data['Remarks7'] . "'," .
                                         "'" . $form_post_data['ActualRated8'] . "'," .
										 "'" . $form_post_data['Remarks8'] . "'," .
										 "'" . $form_post_data['ActualRated9'] . "'," .
                                         "'" . $form_post_data['Remarks9'] . "'," .
                                         "'" . $form_post_data['ActualRated10'] . "'," .
										 "'" . $form_post_data['Remarks10'] . "'," .
                                         "'" . $form_post_data['ActualRated11'] . "'," .
										 "'" . $form_post_data['Remarks11'] . "'," .
										 "'" . $form_post_data['ActualRated12'] . "'," .
                                         "'" . $form_post_data['Remarks12'] . "'," .
                                         "'" . $form_post_data['ActualRated13'] . "'," .
										 "'" . $form_post_data['Remarks13'] . "'," .
                                         "'" . $form_post_data['ActualRated14'] . "'," .
										 "'" . $form_post_data['Remarks14'] . "'," .
										 "'" . $form_post_data['ActualRated15'] . "'," .
                                         "'" . $form_post_data['Remarks15'] . "'," .
                                         "'" . $form_post_data['ActualRated16'] . "'," .
										 "'" . $form_post_data['Remarks16'] . "'," .
                                         "'" . $form_post_data['ActualRated17'] . "'," .
										 "'" . $form_post_data['Remarks17'] . "'," .
										 "'" . $form_post_data['ActualRated18'] . "'," .
                                         "'" . $form_post_data['Remarks18'] . "'," .
                                         "'" . $form_post_data['ActualRated19'] . "'," .
										 "'" . $form_post_data['Remarks19'] . "'," .
                                         "'" . $form_post_data['ActualRated20'] . "'," .
										 "'" . $form_post_data['Remarks20'] . "'," .
										 "'" . $form_post_data['ActualRated21'] . "'," .
                                         "'" . $form_post_data['Remarks21'] . "'," .
                                         "'" . $form_post_data['ActualRated22'] . "'," .
										 "'" . $form_post_data['Remarks22'] . "'," .
                                         "'" . $form_post_data['ActualRated23'] . "'," .
										 "'" . $form_post_data['Remarks23'] . "'," .
										 "'" . $form_post_data['ActualRated24'] . "'," .
                                         "'" . $form_post_data['Remarks24'] . "'," .
                                         "'" . $form_post_data['ActualRated25'] . "'," .
										 "'" . $form_post_data['Remarks25'] . "'," .
										 "'" . $form_post_data['Supplier_Status'] . "'," .
                                         "'" . $form_post_data['Action'] . "'," .
										 "'" . $form_post_data['EvaluatedTeam'] . "'," .
                                              "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                         "'" .  $this->ses->get('user')['ID'] . "'";
                     $Supplier_ID=$form_post_data['SupplierID']; 
					 if($form_post_data['SupplierID']){
                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "supplier_evaluation`
                                            (
                                            `SupplierID`, 
											`ActualRated`,
                                            `Remarks0`,
											`ActualRated1`,
                                            `Remarks1`, 
											`ActualRated2`,
                                            `Remarks2`,
											`ActualRated3`,
                                            `Remarks3`,
											`ActualRated4`,
                                            `Remarks4`,
											`ActualRated5`,
                                            `Remarks5`,
											`ActualRated6`,
                                            `Remarks6`,
											`ActualRated7`,
                                            `Remarks7`,
											`ActualRated8`,
                                            `Remarks8`,
											`ActualRated9`,
                                            `Remarks9`,
											`ActualRated10`,
                                            `Remarks10`,
											`ActualRated11`,
                                            `Remarks11`,
											`ActualRated12`,
                                            `Remarks12`,
											`ActualRated13`,
                                            `Remarks13`,
											`ActualRated14`,
                                            `Remarks14`,
											`ActualRated15`,
                                            `Remarks15`,
											`ActualRated16`,
                                            `Remarks16`,
											`ActualRated17`,
                                            `Remarks17`, 
											`ActualRated18`,
                                            `Remarks18`,
											`ActualRated19`,
                                            `Remarks19`,
											`ActualRated20`,
                                            `Remarks20`,
											`ActualRated21`,
                                            `Remarks21`,
											`ActualRated22`,
                                            `Remarks22`,
											`ActualRated23`,
                                            `Remarks23`,
											`ActualRated24`,
                                            `Remarks24`,
											`ActualRated25`,
                                            `Remarks25`,
											`Supplier_Status`, 
											`Action`,
                                            `EvaluatedTeam`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                         VALUES ( $val )";
                                  $stmt = $this->db->prepare($sql);
								  $stmt->execute();
								  
						 $sql_update="UPDATE $supplier_table SET Status=2 WHERE ID=$Supplier_ID";
                         $stmt1 = $this->db->prepare($sql_update);
						 $stmt1->execute();
						 }
                                  
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
						header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/supplierEvaluation');
																										
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_evaluation_design_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Supplier Evaluation And Assessment');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/supplier_evaluation_design_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
         $colArr = array(
                "$supplier_evaluation_table.ID",
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
             $whereString ="ORDER BY $supplier_evaluation_table.ID DESC";
           }
		   
		      
       $sql = "SELECT " 
                    . implode(',',$colArr)
                    . " FROM $supplier_evaluation_table LEFT JOIN $supplier_table ON $supplier_table.ID = $supplier_evaluation_table.SupplierID "
                    . " WHERE "
                    . " $supplier_evaluation_table.entity_ID = $entityID" 
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
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_supplier_evaluation_form.php';
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
	
  
////////////////////////////////////////////////////

public function subcontractorEvaluation(){
    
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
			 
			////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////access condition applied//////////////////////////
			////////////////////////////////////////////////////////////////////////////////    
						
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $subcontractor_evaluation_table = $this->crg->get('table_prefix') . 'subcontractor_evaluation';
			$supplier_table = $this->crg->get('table_prefix') . 'supplier';
            
            $supplier_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE $supplier_table.SupplierType='Sub contractor' AND $supplier_table.Status=1";
            $stmt = $this->db->prepare($supplier_sql);            
            $stmt->execute();
            $supplier_data  = $stmt->fetchAll();	
            $this->tpl->set('supplier_data', $supplier_data);
			
			$supplier1_sql = "SELECT ID,SupplierName FROM $supplier_table WHERE $supplier_table.SupplierType='Sub contractor' AND $supplier_table.Status=2";
            $stmt = $this->db->prepare($supplier1_sql);            
            $stmt->execute();
            $supplier1_data  = $stmt->fetchAll();	
            $this->tpl->set('supplier1_data', $supplier1_data);
			
            $this->tpl->set('page_title', 'subcontractor Evaluation And Assessment');	          
            $this->tpl->set('page_header', 'subcontractor Evaluation And Assessment');
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
					
					$sql_update="UPDATE $supplier_table SET Status=1 WHERE ID=(SELECT SupplierID FROM $subcontractor_evaluation_table WHERE $subcontractor_evaluation_table.ID= $data)";
                            $stmt1 = $this->db->prepare($sql_update);
					 $stmt1->execute();
                     
                     $sqldetdelete="Delete from $subcontractor_evaluation_table
                                        where $subcontractor_evaluation_table.ID=$data"; 
                        $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'subcontractor Evaluation And Assessment form deleted successfully');
																													  
                        //$this->tpl->set('label', 'List');
                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/subcontractorEvaluation');
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
                          
                    $sqlsrr = "SELECT *,$supplier_table.ContactPerson,$supplier_table.ContactNumber,$supplier_table.AddressLine1 FROM $subcontractor_evaluation_table 
				               LEFT JOIN $supplier_table ON $supplier_table.ID=$subcontractor_evaluation_table.SupplierID
                               WHERE  $subcontractor_evaluation_table.ID= $data";
                               $subcontractor_evaluation_data = $dbutil->getSqlData($sqlsrr);
                   
                
                    //edit option     
                    $this->tpl->set('message', 'You can view subcontractor Evaluation And Assessment form');
                    $this->tpl->set('page_header', 'subcontractor Evaluation And Assessment');
                    $this->tpl->set('FmData', $subcontractor_evaluation_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_evaluation_design_form.php'));                    
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
           			      
                $sqlsrr = "SELECT *,$supplier_table.SupplierName,$supplier_table.ContactPerson,$supplier_table.ContactNumber,$supplier_table.AddressLine1 FROM $subcontractor_evaluation_table 
				           LEFT JOIN $supplier_table ON $supplier_table.ID=$subcontractor_evaluation_table.SupplierID
                           WHERE  $subcontractor_evaluation_table.ID= $data";
                           $subcontractor_evaluation_data = $dbutil->getSqlData($sqlsrr);
                    
                    //edit option 

					
                    $this->tpl->set('message', 'You can edit subcontractor Evaluation And Assessment form');
                    $this->tpl->set('page_header', 'subcontractor Evaluation And Assessment');
                    $this->tpl->set('FmData', $subcontractor_evaluation_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_evaluation_design_form.php'));                    
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
                          $SupplierID= $form_post_data['SupplierID'];
						  $ActualRated= $form_post_data['ActualRated'];
                          $Remarks0= $form_post_data['Remarks0'];
						  $ActualRated1= $form_post_data['ActualRated1'];
						  $Remarks1= $form_post_data['Remarks1'];
						  $ActualRated2= $form_post_data['ActualRated2'];
						  $Remarks2= $form_post_data['Remarks2'];
						  $ActualRated3= $form_post_data['ActualRated3'];
						  $Remarks3= $form_post_data['Remarks3'];
						  $ActualRated4= $form_post_data['ActualRated4'];
						  $Remarks4= $form_post_data['Remarks4'];
						  $ActualRated5= $form_post_data['ActualRated5'];
						  $Remarks5= $form_post_data['Remarks5'];
						  $ActualRated6= $form_post_data['ActualRated6'];
						  $Remarks6= $form_post_data['Remarks6'];
						  $ActualRated7= $form_post_data['ActualRated7'];
						  $Remarks7= $form_post_data['Remarks7'];
						  $ActualRated8= $form_post_data['ActualRated8'];
						  $Remarks8= $form_post_data['Remarks8'];
						  $ActualRated9= $form_post_data['ActualRated9'];
						  $Remarks9= $form_post_data['Remarks9'];
						  $ActualRated10= $form_post_data['ActualRated10'];
						  $Remarks10= $form_post_data['Remarks10'];
						  $ActualRated11= $form_post_data['ActualRated11'];
						  $Remarks11= $form_post_data['Remarks11'];
						  $ActualRated12= $form_post_data['ActualRated12'];
						  $Remarks12= $form_post_data['Remarks12'];
						  $ActualRated13= $form_post_data['ActualRated13'];
						  $Remarks13= $form_post_data['Remarks13'];
						  $ActualRated14= $form_post_data['ActualRated14'];
						  $Remarks14= $form_post_data['Remarks14'];
						  $ActualRated15= $form_post_data['ActualRated15'];
						  $Remarks15= $form_post_data['Remarks15'];
						  $ActualRated16= $form_post_data['ActualRated16'];
						  $Remarks16= $form_post_data['Remarks16'];
						  $ActualRated17= $form_post_data['ActualRated17'];
						  $Remarks17= $form_post_data['Remarks17'];
						  $ActualRated18= $form_post_data['ActualRated18'];
						  $Remarks18= $form_post_data['Remarks18'];
						  $ActualRated19= $form_post_data['ActualRated19'];
						  $Remarks19= $form_post_data['Remarks19'];
						  $ActualRated20= $form_post_data['ActualRated20'];
						  $Remarks20= $form_post_data['Remarks20'];
						  $ActualRated21= $form_post_data['ActualRated21'];
						  $Remarks21= $form_post_data['Remarks21'];
						  $ActualRated22= $form_post_data['ActualRated22'];
						  $Remarks22= $form_post_data['Remarks22'];
						  $Subcontractor_Status= $form_post_data['Subcontractor_Status'];
						  $Action= $form_post_data['Action'];
						  $EvaluatedTeam= $form_post_data['EvaluatedTeam'];
						  
						 $sql_update="UPDATE $subcontractor_evaluation_table set SupplierID='$SupplierID',ActualRated='$ActualRated',Remarks0='$Remarks0',ActualRated1='$ActualRated1',Remarks1='$Remarks1',ActualRated2='$ActualRated2',Remarks2='$Remarks2',ActualRated3='$ActualRated3',Remarks3='$Remarks3',ActualRated4='$ActualRated4',Remarks4='$Remarks4',ActualRated5='$ActualRated5',Remarks5='$Remarks5',ActualRated6='$ActualRated6',Remarks6='$Remarks6',ActualRated7='$ActualRated7',Remarks7='$Remarks7',ActualRated8='$ActualRated8',Remarks8='$Remarks8',ActualRated9='$ActualRated9',Remarks9='$Remarks9',ActualRated10='$ActualRated10',Remarks10='$Remarks10',ActualRated11='$ActualRated11',Remarks11='$Remarks11',ActualRated12='$ActualRated12',Remarks12='$Remarks12',ActualRated13='$ActualRated13',Remarks13='$Remarks13',ActualRated14='$ActualRated14',Remarks14='$Remarks14',ActualRated15='$ActualRated15',Remarks15='$Remarks15',ActualRated16='$ActualRated16',Remarks16='$Remarks16',ActualRated17='$ActualRated17',Remarks17='$Remarks17',ActualRated18='$ActualRated18',Remarks18='$Remarks18',ActualRated19='$ActualRated19',Remarks19='$Remarks19',ActualRated20='$ActualRated20',Remarks20='$Remarks20',ActualRated21='$ActualRated21',Remarks21='$Remarks21',ActualRated22='$ActualRated22',Remarks22='$Remarks22',Subcontractor_Status='$Subcontractor_Status',Action='$Action',EvaluatedTeam='$EvaluatedTeam' WHERE ID=$data";
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
                       
                            $this->tpl->set('message', 'subcontractor Evaluation And Assessment form edited successfully!');   
																														  
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
							header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/subcontractorEvaluation');
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_evaluation_design_form.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
 
                        $form_post_data = $dbutil->arrFltr($_POST);
                        
                      // var_dump($_POST);
					   
                        $entry_count = 1;
                       
					   if (isset($form_post_data['SupplierID'])) {
                           
                                        $val = "'" . $form_post_data['SupplierID'] . "'," .
										 "'" . $form_post_data['ActualRated'] . "'," .
                                         "'" . $form_post_data['Remarks0'] . "'," .
                                         "'" . $form_post_data['ActualRated1'] . "'," .
										 "'" . $form_post_data['Remarks1'] . "'," .
                                         "'" . $form_post_data['ActualRated2'] . "'," .
										 "'" . $form_post_data['Remarks2'] . "'," .
										 "'" . $form_post_data['ActualRated3'] . "'," .
                                         "'" . $form_post_data['Remarks3'] . "'," .
                                         "'" . $form_post_data['ActualRated4'] . "'," .
										 "'" . $form_post_data['Remarks4'] . "'," .
                                         "'" . $form_post_data['ActualRated5'] . "'," .
										 "'" . $form_post_data['Remarks5'] . "'," .
										 "'" . $form_post_data['ActualRated6'] . "'," .
                                         "'" . $form_post_data['Remarks6'] . "'," .
                                         "'" . $form_post_data['ActualRated7'] . "'," .
										 "'" . $form_post_data['Remarks7'] . "'," .
                                         "'" . $form_post_data['ActualRated8'] . "'," .
										 "'" . $form_post_data['Remarks8'] . "'," .
										 "'" . $form_post_data['ActualRated9'] . "'," .
                                         "'" . $form_post_data['Remarks9'] . "'," .
                                         "'" . $form_post_data['ActualRated10'] . "'," .
										 "'" . $form_post_data['Remarks10'] . "'," .
                                         "'" . $form_post_data['ActualRated11'] . "'," .
										 "'" . $form_post_data['Remarks11'] . "'," .
										 "'" . $form_post_data['ActualRated12'] . "'," .
                                         "'" . $form_post_data['Remarks12'] . "'," .
                                         "'" . $form_post_data['ActualRated13'] . "'," .
										 "'" . $form_post_data['Remarks13'] . "'," .
                                         "'" . $form_post_data['ActualRated14'] . "'," .
										 "'" . $form_post_data['Remarks14'] . "'," .
										 "'" . $form_post_data['ActualRated15'] . "'," .
                                         "'" . $form_post_data['Remarks15'] . "'," .
                                         "'" . $form_post_data['ActualRated16'] . "'," .
										 "'" . $form_post_data['Remarks16'] . "'," .
                                         "'" . $form_post_data['ActualRated17'] . "'," .
										 "'" . $form_post_data['Remarks17'] . "'," .
										 "'" . $form_post_data['ActualRated18'] . "'," .
                                         "'" . $form_post_data['Remarks18'] . "'," .
                                         "'" . $form_post_data['ActualRated19'] . "'," .
										 "'" . $form_post_data['Remarks19'] . "'," .
                                         "'" . $form_post_data['ActualRated20'] . "'," .
										 "'" . $form_post_data['Remarks20'] . "'," .
										 "'" . $form_post_data['ActualRated21'] . "'," .
                                         "'" . $form_post_data['Remarks21'] . "'," .
                                         "'" . $form_post_data['ActualRated22'] . "'," .
										 "'" . $form_post_data['Remarks22'] . "'," .
										 "'" . $form_post_data['Subcontractor_Status'] . "'," .
                                         "'" . $form_post_data['Action'] . "'," .
										 "'" . $form_post_data['EvaluatedTeam'] . "'," .
                                              "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                         "'" .  $this->ses->get('user')['ID'] . "'";
                     $Supplier_ID=$form_post_data['SupplierID']; 
					 if($form_post_data['SupplierID']){
                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "subcontractor_evaluation`
                                            (
                                            `SupplierID`, 
											`ActualRated`,
                                            `Remarks0`,
											`ActualRated1`,
                                            `Remarks1`, 
											`ActualRated2`,
                                            `Remarks2`,
											`ActualRated3`,
                                            `Remarks3`,
											`ActualRated4`,
                                            `Remarks4`,
											`ActualRated5`,
                                            `Remarks5`,
											`ActualRated6`,
                                            `Remarks6`,
											`ActualRated7`,
                                            `Remarks7`,
											`ActualRated8`,
                                            `Remarks8`,
											`ActualRated9`,
                                            `Remarks9`,
											`ActualRated10`,
                                            `Remarks10`,
											`ActualRated11`,
                                            `Remarks11`,
											`ActualRated12`,
                                            `Remarks12`,
											`ActualRated13`,
                                            `Remarks13`,
											`ActualRated14`,
                                            `Remarks14`,
											`ActualRated15`,
                                            `Remarks15`,
											`ActualRated16`,
                                            `Remarks16`,
											`ActualRated17`,
                                            `Remarks17`, 
											`ActualRated18`,
                                            `Remarks18`,
											`ActualRated19`,
                                            `Remarks19`,
											`ActualRated20`,
                                            `Remarks20`,
											`ActualRated21`,
                                            `Remarks21`,
											`ActualRated22`,
                                            `Remarks22`,
											`Subcontractor_Status`, 
											`Action`,
                                            `EvaluatedTeam`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                         VALUES ( $val )";
                                  $stmt = $this->db->prepare($sql);
								  $stmt->execute();
								  
						 $sql_update="UPDATE $supplier_table SET Status=2 WHERE ID=$Supplier_ID";
                         $stmt1 = $this->db->prepare($sql_update);
						 $stmt1->execute();
						 }
                                  
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
						header('Location:' . $this->crg->get('route')['base_path'] . '/supplier/cst/subcontractorEvaluation');
																										
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_evaluation_design_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'subcontractor Evaluation And Assessment');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/subcontractor_evaluation_design_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
         $colArr = array(
                "$subcontractor_evaluation_table.ID",
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
             $whereString ="ORDER BY $subcontractor_evaluation_table.ID DESC";
           }
		   
		      
       $sql = "SELECT " 
                    . implode(',',$colArr)
                    . " FROM $subcontractor_evaluation_table LEFT JOIN $supplier_table ON $supplier_table.ID = $subcontractor_evaluation_table.SupplierID "
                    . " WHERE "
                    . " $subcontractor_evaluation_table.entity_ID = $entityID" 
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
         
         
		 
            $this->tpl->set('table_columns_label_arr', array('ID','subcontractor Name'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_subcontractor_evaluation_form.php';
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
    
 

////////////////////////////////////////////////////
}
