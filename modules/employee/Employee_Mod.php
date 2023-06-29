<?php

/**
 * Description of Employee_Mod
 *
 * @author psmahadevan
 */
class Employee_Mod {

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
    //////////////////////////////// Employee Starts //////////////////////
    function manage(){
     if ($this->crg->get('wp') || $this->crg->get('rp')) {
             
            ////////////////////////////////////////////////////////////////////////////////
            //////////////////////////////access condition applied//////////////////////////
            ////////////////////////////////////////////////////////////////////////////////    
            
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $employee_table = $this->crg->get('table_prefix') . 'employee';
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
            
            //designation table data
           
            $design_sql = "SELECT ID,DesignationName FROM $designation_tab";
            $stmt = $this->db->prepare($design_sql);            
            $stmt->execute();
            $designation_data  = $stmt->fetchAll();	
            $this->tpl->set('designation_data', $designation_data);
            
            //department table data
           
            $dept_sql = "SELECT ID,DeptName FROM $dept_tab";
            $stmt = $this->db->prepare($dept_sql);            
            $stmt->execute();
            $department_data  = $stmt->fetchAll();	
            $this->tpl->set('department_data', $department_data);
            
           
            $this->tpl->set('page_title', 'Employee Form');	          
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
                     
                     $sqldetdelete="Delete from $employee_table where $employee_table.ID=$data"; 
                     $stmt = $this->db->prepare($sqldetdelete);            
					 
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Employee deleted successfully');
                    	header('Location:' . $this->crg->get('route')['base_path'] . '/employee/emp/manage');
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
                                
                    $sqlsrr = "SELECT * FROM `$employee_table` WHERE `$employee_table`.`ID` = '$data'";                    
                    $employee_data = $dbutil->getSqlData($sqlsrr); 
                    
                    //edit option     
                    $this->tpl->set('message', 'You can view Employee Form');
                    $this->tpl->set('page_header', 'Static Data');
                    $this->tpl->set('FmData', $employee_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/employee_form.php'));                    
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
                                
                    $sqlsrr = "SELECT * FROM `$employee_table` WHERE `$employee_table`.`ID` = '$data'";                    
                    $employee_data = $dbutil->getSqlData($sqlsrr); 

                    //edit option     
                    $this->tpl->set('message', 'You can edit Employee Form');
                    $this->tpl->set('page_header', 'Static Data');
                    $this->tpl->set('FmData', $employee_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/employee_form.php'));                    
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
                            
                            $employee_name= $form_post_data['EmpName'];
							$department_id= $form_post_data['Department_ID'];
							$designation_id= $form_post_data['Designation_ID'];
							$addressline1= $form_post_data['AddressLine1'];
							$addressline2= $form_post_data['AddressLine2'];
							$state_id= $form_post_data['State_ID'];
							$country_id= $form_post_data['Country_ID'];
							$city= $form_post_data['City'];
							$pincode= $form_post_data['Pincode'];
							$mobile_no= $form_post_data['MobileNo'];
							$contact_no= $form_post_data['ContactNo'];
							$dob=!empty($form_post_data['DOB'])?date("Y-m-d", strtotime($form_post_data['DOB'])):'';
							$email= $form_post_data['Email'];
							$active= $form_post_data['Active'];
                           
                            $sql_update="UPDATE $employee_table SET EmpName='$employee_name',
																	Department_ID='$department_id', 
																	Designation_ID='$designation_id',
																	AddressLine1='$addressline1', 
																	AddressLine2='$addressline2',
																	State_ID='$state_id', 
																	Country_ID='$country_id',
																	City='$city', 
																	Country_ID='$country_id',
																	MobileNo='$mobile_no', 
																	ContactNo='$contact_no',
																	DOB='$dob', 
																	Email='$email',
																	Active='$active'
															WHERE   ID=$data";
                            $stmt1 = $this->db->prepare($sql_update);
                            $stmt1->execute(); 

                       
                            $this->tpl->set('message', 'Employee Form edited successfully!'); 
                            header('Location:' . $this->crg->get('route')['base_path'] . '/employee/emp/manage');
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/employee_form.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                        
                       // var_dump($_POST);
                        
                        $entry_count = 1;
                       
                            if (isset($form_post_data['Department_ID'])) {
                                
                                $dob=!empty($form_post_data['DOB'])?date("Y-m-d", strtotime($form_post_data['DOB'])):'';
                                
                                        $val =  "'" . $form_post_data['EmpCode'] . "'," .
												"'" . $form_post_data['EmpName'] . "'," .
												"'" . $form_post_data['Department_ID'] . "'," .
												"'" . $form_post_data['Designation_ID'] . "'," .
												"'" . $form_post_data['AddressLine1'] . "'," .
												"'" . $form_post_data['AddressLine2'] . "'," .
												"'" . $form_post_data['City'] . "'," .
												"'" . $form_post_data['State_ID'] . "'," .
												"'" . $form_post_data['Country_ID'] . "'," .
												"'" . $form_post_data['Pincode'] . "'," .
												"'" . $form_post_data['MobileNo'] . "'," .
												"'" . $form_post_data['ContactNo'] . "'," .
												"'" . $dob . "'," .
												"'" . $form_post_data['Email'] . "'," .
												"'" . $form_post_data['Active'] . "'," .
                                                "'" . $this->ses->get('user')['entity_ID'] . "'," .
                                                "'" . $this->ses->get('user')['ID'] . "'";

                              $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "employee`
                                            ( 
                                            `EmpCode`,
											`EmpName`,
											`Department_ID`,
											`Designation_ID`,
											`AddressLine1`,
											`AddressLine2`,
											`City`,
											`State_ID`,
											`Country_ID`,
											`Pincode`,
											`MobileNo`,
											`ContactNo`,
											`DOB`,
											`Email`,
											`Active`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                        VALUES ( $val )";
                                  $stmt = $this->db->prepare($sql);
                                  
                                  
								if ($stmt->execute()) { 
									$this->tpl->set('message', '- Success -');
									header('Location:' . $this->crg->get('route')['base_path'] . '/employee/emp/manage');
									//$this->tpl->set('content', $this->tpl->fetch('factory/form/employee_form.php'));
								}
							}
							$this->tpl->set('mode', 'add');
                        
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/employee_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Static Data');
					$EmpCode=$dbutil->keyGeneration('employee','MFG','','EmpCode');
                    $this->tpl->set('EmpCode', $EmpCode);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/employee_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$employee_table.ID",
                "$employee_table.EmpCode",
				"$employee_table.EmpName",
				"$dept_tab.DeptName",
				"$designation_tab.DesignationName",
				"$employee_table.MobileNo"
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
             $whereString ="ORDER BY $employee_table.ID DESC";
           }
            
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $employee_table LEFT JOIN $dept_tab ON $employee_table.Department_ID=$dept_tab.ID "
					. " LEFT JOIN $designation_tab ON $employee_table.Designation_ID=$designation_tab.ID "
                    . " WHERE "
                    . " $employee_table.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Employee Code','Employee Name','Department Name','Designation Name','Mobile No'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_form_employee.php';
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
    ////////////////////////////////Employee Ends Here ////////////////////
    /*
     * End of Class
     */
}
