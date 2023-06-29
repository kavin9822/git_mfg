<?php

/**
 * Description of Customer_Mod
 *
 * @author psmahadevan
 */
class Customer_Mod {

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

  
//////////////////customer start here//////////////////
    
 
////////////////////customer closes here//////////////// 
   function manage(){
     if ($this->crg->get('wp') || $this->crg->get('rp')) {
             
            ////////////////////////////////////////////////////////////////////////////////
            //////////////////////////////access condition applied//////////////////////////
            ////////////////////////////////////////////////////////////////////////////////    
            
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $customer_table = $this->crg->get('table_prefix') . 'customer';
            $state_table = $this->crg->get('table_prefix') . 'state';
            $unit_table = $this->crg->get('table_prefix') . 'unit';
            $country_tab = $this->crg->get('table_prefix') . 'country';
            $dept_tab = $this->crg->get('table_prefix') . 'department';
            $designation_tab = $this->crg->get('table_prefix') . 'designation';
            
           //state table data 
		   
            $state_sql = "SELECT ID,StateName FROM $state_table";
            $stmt = $this->db->prepare($state_sql);            
            $stmt->execute();
            $state_data  = $stmt->fetchAll();	
            $this->tpl->set('state_data', $state_data);
            
            //country table data
           
            $country_sql = "SELECT ID,CountryName FROM $country_tab";
            $stmt = $this->db->prepare($country_sql);            
            $stmt->execute();
            $country_data  = $stmt->fetchAll();	
            $this->tpl->set('country_data', $country_data);
            
            //department table data
           
            $dept_sql = "SELECT ID,DeptName FROM $dept_tab";
            $stmt = $this->db->prepare($dept_sql);            
            $stmt->execute();
            $department_data  = $stmt->fetchAll();	
            $this->tpl->set('department_data', $department_data);
            
           
            $this->tpl->set('page_title', 'Customer');	          
            $this->tpl->set('page_header', 'Static Data');
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
                     
                    $sqldetdelete="Delete from $customer_table where $customer_table.ID=$data"; 
                    $stmt = $this->db->prepare($sqldetdelete);            
					 
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Customer deleted successfully');
						header('Location:' . $this->crg->get('route')['base_path'] . '/customer/cst/manage');
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
                                
                    $sqlsrr = "SELECT * FROM `$customer_table` WHERE `$customer_table`.`ID` = '$data'";                    
                    $customer_data = $dbutil->getSqlData($sqlsrr); 
                    
                    //edit option     
                    $this->tpl->set('message', 'You can view Customer Form');
                    $this->tpl->set('page_header', 'Static Data');
                    $this->tpl->set('FmData', $customer_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_form.php'));                    
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
                                
                    $sqlsrr = "SELECT * FROM `$customer_table` WHERE `$customer_table`.`ID` = '$data'";                    
                    $customer_data = $dbutil->getSqlData($sqlsrr); 

                    //edit option     
                    $this->tpl->set('message', 'You can edit Customer Form');
                    $this->tpl->set('page_header', 'Static Data');
                    $this->tpl->set('FmData', $customer_data); 
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_form.php'));                    
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
                            
                            $customer_name= $form_post_data['PersonName'];
							$DesignationName= $form_post_data['DesignationName'];
							$company_name= $form_post_data['CompanyName'];
							$gstno= $form_post_data['GSTNo'];
							$billing_addressline1= $form_post_data['BillingAddress1'];
							$billing_addressline2= $form_post_data['BillingAddress2'];
							$billing_city= $form_post_data['BillingCity'];
							$billing_state_id= $form_post_data['BillingState_ID'];
							$billing_country_id= $form_post_data['BillingCountry_ID'];
							$billing_pincode= $form_post_data['BillingZip'];
							$permnt_addressline1= $form_post_data['PermntAddress1'];
							$permnt_addressline2= $form_post_data['PermntAddress2'];
							$permnt_city= $form_post_data['PermntCity'];
							$permnt_state_id= $form_post_data['PermntState_ID'];
							$permnt_country_id= $form_post_data['PermntCountry_ID'];
							$permnt_pincode= $form_post_data['PermntZip'];
							$state_code=$form_post_data['StateCode'];
							$addresstype= $form_post_data['Address_type'];
							$mobile_no= $form_post_data['MobileNo'];
							$landline= $form_post_data['Landline'];
							$extno=$form_post_data['ExtNo'];
							$email= $form_post_data['Email'];
							$website= $form_post_data['Website'];
                           
                            $sql_update="UPDATE $customer_table SET PersonName='$customer_name',
																	DesignationName='$DesignationName',
																	CompanyName='$company_name',
																	GSTNo='$gstno',
																	BillingAddress1='$billing_addressline1', 
																	BillingAddress2='$billing_addressline2',
																	BillingCity='$billing_city', 
																	BillingState_ID='$billing_state_id',
																	BillingCountry_ID='$billing_country_id', 
																	BillingZip='$billing_pincode',
																	PermntAddress1='$permnt_addressline1', 
																	PermntAddress2='$permnt_addressline2',
																	PermntCity='$permnt_city', 
																	PermntState_ID='$permnt_state_id',
																	PermntCountry_ID='$permnt_country_id', 
																	PermntZip='$permnt_pincode',
																	Address_type='$addresstype',
																	MobileNo='$mobile_no',
																	Landline='$landline', 
																	ExtNo='$extno',
																	Email='$email',
																	Website='$website'
															WHERE   ID=$data";
                            $stmt1 = $this->db->prepare($sql_update);
                            $stmt1->execute(); 
							
                            $this->tpl->set('message', 'Customer Form edited successfully!');   
							header('Location:' . $this->crg->get('route')['base_path'] . '/customer/cst/manage');
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_form.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                        
                       // var_dump($_POST);
                       
                            if (isset($form_post_data['PersonName'])) {
                           
                                        $val =  "'" . $form_post_data['PersonName'] . "'," .
												"'" . $form_post_data['DesignationName'] . "'," .
												"'" . $form_post_data['CompanyName'] . "'," .
												"'" . $form_post_data['GSTNo'] . "'," .
												"'" . $form_post_data['BillingAddress1'] . "'," .
												"'" . $form_post_data['BillingAddress2'] . "'," .
												"'" . $form_post_data['BillingCity'] . "'," .
												"'" . $form_post_data['BillingState_ID'] . "'," .
												"'" . $form_post_data['BillingCountry_ID'] . "'," .
												"'" . $form_post_data['BillingZip'] . "'," .
												"'" . $form_post_data['PermntAddress1'] . "'," .
												"'" . $form_post_data['PermntAddress2'] . "'," .
												"'" . $form_post_data['PermntCity'] . "'," .
												"'" . $form_post_data['PermntState_ID'] . "'," .
												"'" . $form_post_data['PermntCountry_ID'] . "'," .
												"'" . $form_post_data['PermntZip'] . "'," .
												"'" . $form_post_data['MobileNo'] . "'," .
												"'" . $form_post_data['StateCode'] . "'," .
												"'" . $form_post_data['Address_type'] . "'," .
												"'" . $form_post_data['Landline'] . "'," .
												"'" . $form_post_data['Email'] . "'," .
												"'" . $form_post_data['ExtNo'] . "'," .
												"'" . $form_post_data['Website'] . "'," .
                                                "'" . $this->ses->get('user')['entity_ID'] . "'," .
                                                "'" . $this->ses->get('user')['ID'] . "'";

									$sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "customer`
                                            ( 
                                            `PersonName`,
											`DesignationName`,
											`CompanyName`,
											`GSTNo`,
											`BillingAddress1`,
											`BillingAddress2`,
											`BillingCity`,
											`BillingState_ID`,
											`BillingCountry_ID`,
											`BillingZip`,
											`PermntAddress1`,
											`PermntAddress2`,
											`PermntCity`,
											`PermntState_ID`,
											`PermntCountry_ID`,
											`PermntZip`,
											`MobileNo`,
											`StateCode`,
											`Address_type`,
											`Landline`,
											`Email`,
											`ExtNo`,
											`Website`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                        VALUES ( $val )";
									$stmt = $this->db->prepare($sql);
                                                                    
								if ($stmt->execute()) { 
									$this->tpl->set('message', '- Success -');
									header('Location:' . $this->crg->get('route')['base_path'] . '/customer/cst/manage');
									// $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_form.php'));
								}
							}
							$this->tpl->set('mode', 'add');
                        
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Static Data');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$customer_table.ID",
                "$customer_table.PersonName",
                "$customer_table.CompanyName",
				"$customer_table.MobileNo",
				"$customer_table.Email",
				"$customer_table.Website"
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
             $whereString ="ORDER BY $customer_table.ID DESC";
           }
            
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $customer_table "
                    . " WHERE "
                    . " $customer_table.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Person Name','Company Name','Mobile No','Email','Website'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_form_customer.php';
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
    
    
   
    /*
     * End of Class
     */
}