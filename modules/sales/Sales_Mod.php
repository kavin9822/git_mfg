<?php

/**
 * Description of Product_Mod
 *
 * @author psmahadevan
 */
class Sales_Mod {

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
    
///////////////////// Enquiry_Form//////////////////
    
function enquiry(){
     
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
            
            $pdt_table = $this->crg->get('table_prefix') . 'product';
            $employee_table = $this->crg->get('table_prefix') . 'employee';
            $pdndept_table = $this->crg->get('table_prefix') . 'pdndepartment';
            $dept_table = $this->crg->get('table_prefix') . 'department';
            $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
            $unit_table = $this->crg->get('table_prefix') . 'unit';
            $enquirydetail_tab = $this->crg->get('table_prefix') . 'enquirydetail';
            $enquirymaster_tab = $this->crg->get('table_prefix') . 'enquiry';
            $customer_table = $this->crg->get('table_prefix') . 'customer';
            $state_table = $this->crg->get('table_prefix') . 'state';
            $country_table = $this->crg->get('table_prefix') . 'country';
			$pdnorder_tab = $this->crg->get('table_prefix') . 'productionorder';
           
           //product table data 
           
            $pdt_sql = "SELECT ID,ProductName FROM $pdt_table";
            $stmt = $this->db->prepare($pdt_sql);            
            $stmt->execute();
            $pdt_data  = $stmt->fetchAll();	
            $this->tpl->set('pdt_data', $pdt_data);
            
            
           //company name from customer table data 
           
            $sql = "SELECT ID,CONCAT(CompanyName ,' || ', PersonName) AS Title FROM $customer_table ORDER BY ID DESC";
            $stmt = $this->db->prepare($sql);             
            $stmt->execute();
            $cmp_data  = $stmt->fetchAll();	
            $this->tpl->set('customer_data', $cmp_data);
            
            //employee table data 
           
            $emp_sql = "SELECT $employee_table.ID,$employee_table.EmpName FROM $employee_table,$dept_table WHERE $employee_table.department_ID=$dept_table.ID AND $dept_table.DeptName='sales'";
            $stmt = $this->db->prepare($emp_sql);            
            $stmt->execute();
            $employee_data  = $stmt->fetchAll();	
            $this->tpl->set('employee_data', $employee_data);
            
            // mode of enquiry array
            
            $enquirymode_data = array(array("ID"=>"1","Title"=>"Walk-In"),array("ID"=>"2","Title"=>"Telephone"),array("ID"=>"3","Title"=>"IndiaMart"),array("ID"=>"4","Title"=>"Others"));
            $this->tpl->set('enquirymode_data', $enquirymode_data);
            
             // costrequired_data array
            
            $costrequired_data = array(array("ID"=>"1","Title"=>"Yes"),array("ID"=>"2","Title"=>"No"));
            $this->tpl->set('costrequired_data', $costrequired_data);
            
            // order received array
            
            $orderreceived_data = array(array("ID"=>"1","Title"=>"Yes"),array("ID"=>"2","Title"=>"No"));
            $this->tpl->set('orderreceived_data', $orderreceived_data);
            
            // enquiry status array
            
            $enquirystatus_data = array(array("ID"=>"1","Title"=>"Quotation Sent"),array("ID"=>"2","Title"=>"Order Placed"),array("ID"=>"3","Title"=>"Dropped"));
            $this->tpl->set('enquirystatus_data', $enquirystatus_data);
            
            // enquiry type  array
            
            $enquirytype_data = array(array("ID"=>"1","Title"=>"Enquiry"),array("ID"=>"2","Title"=>"Tender"));
            $this->tpl->set('enquirytype_data', $enquirytype_data);
            
             //state table data 
           
                    $state_sql = "SELECT ID,StateName FROM $state_table";
                    $stmt = $this->db->prepare($state_sql);            
                    $stmt->execute();
                    $state_data  = $stmt->fetchAll();	
                    $this->tpl->set('ajxstate_data', $state_data);
                    
                    //country table data 
           
                    $country_sql = "SELECT ID,CountryName FROM $country_table";
                    $stmt = $this->db->prepare($country_sql);            
                    $stmt->execute();
                    $country_data  = $stmt->fetchAll();	
                    $this->tpl->set('ajxcountry_data', $country_data);

            //department table data 
           
            $dept_sql = "SELECT ID,DeptName FROM $pdndept_table  ORDER BY ID DESC";
            $stmt = $this->db->prepare($dept_sql);            
            $stmt->execute();
            $department_data  = $stmt->fetchAll();	
            $this->tpl->set('department_data', $department_data);
            
            //process table data
           
            $pdt_sql = "SELECT ID,Process FROM $processdetail_tab";
            $stmt = $this->db->prepare($pdt_sql);            
            $stmt->execute();
            $process_data  = $stmt->fetchAll();	
            $this->tpl->set('process_data', $process_data);
            
            //rawmaterial table data
           
            $pdt_sql = "SELECT ID,RMName FROM $rawmaterial_table";
            $stmt = $this->db->prepare($pdt_sql);            
            $stmt->execute();
            $rawmaterial_data  = $stmt->fetchAll();	
            $this->tpl->set('rawmaterial_data', $rawmaterial_data);
            
            //unit table data
           
            $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
            $stmt = $this->db->prepare($pdt_sql);            
            $stmt->execute();
            $unit_data  = $stmt->fetchAll();	
            $this->tpl->set('unit_data', $unit_data);
            
           
            $this->tpl->set('page_title', 'Enquiry');	          
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
                    $sqlsrr = "SELECT $enquirymaster_tab.EnquiryType FROM  `$enquirymaster_tab` WHERE `$enquirymaster_tab`.`ID` = '$data'";                    
                    $enquiry_data = $dbutil->getSqlData($sqlsrr); 
                    //var_dump($enquiry_data); die;
                      
                      if(!empty($enquiry_data) && $enquiry_data[0]['EnquiryType']==2){
                          $sqldetdelete="Delete from $enquirymaster_tab where $enquirymaster_tab.ID=$data"; 
                      } else{
                         $sqldetdelete="Delete $enquirydetail_tab,$enquirymaster_tab from $enquirymaster_tab
                                        LEFT JOIN  $enquirydetail_tab ON $enquirymaster_tab.ID=$enquirydetail_tab.enquiry_ID 
                                        where $enquirydetail_tab.enquiry_ID=$data";  
                      }
                     
                        $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Enquiry deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiry');
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
                                
                    $sqlsrr = "SELECT *,$pdnorder_tab.PdnOrderNo FROM  `$enquirymaster_tab` LEFT JOIN `$enquirydetail_tab` ON  `$enquirymaster_tab`.`ID`=`$enquirydetail_tab`.`enquiry_ID` LEFT JOIN `$customer_table` ON  `$enquirymaster_tab`.`customer_ID`=`$customer_table`.`ID` LEFT JOIN $pdnorder_tab ON $pdnorder_tab.enquiry_ID=$enquirymaster_tab.ID WHERE `$enquirymaster_tab`.`ID` = '$data' ORDER BY $enquirydetail_tab.ID ASC";                    
                    $enquiry_data = $dbutil->getSqlData($sqlsrr); 
                    
                    //var_dump($enquiry_data[0]);
                    
                    $stateid=$enquiry_data[0]['BillingState_ID'];
                    $countryid=$enquiry_data[0]['BillingCountry_ID'];
                    
                    //state table data 
           
                    $state_sql = "SELECT ID,StateName FROM $state_table where ID=$stateid";
                    $stmt = $this->db->prepare($state_sql);            
                    $stmt->execute();
                    $state_data  = $stmt->fetchAll();	
                    $this->tpl->set('state_data', $state_data);
                    
                    //country table data 
           
                    $country_sql = "SELECT ID,CountryName FROM $country_table where ID=$countryid";
                    $stmt = $this->db->prepare($country_sql);            
                    $stmt->execute();
                    $country_data  = $stmt->fetchAll();	
                    $this->tpl->set('country_data', $country_data);

                    
                    //edit option     
                    $this->tpl->set('message', 'You can view Enquiry  form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('FmData', $enquiry_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_form.php'));                    
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
                                
                    $sqlsrr = "SELECT *,$pdnorder_tab.PdnOrderNo FROM  `$enquirymaster_tab` LEFT JOIN `$enquirydetail_tab` ON  `$enquirymaster_tab`.`ID`=`$enquirydetail_tab`.`enquiry_ID` LEFT JOIN `$customer_table` ON  `$enquirymaster_tab`.`customer_ID`=`$customer_table`.`ID` LEFT JOIN $pdnorder_tab ON $pdnorder_tab.enquiry_ID=$enquirymaster_tab.ID WHERE `$enquirymaster_tab`.`ID` = '$data' ORDER BY $enquirydetail_tab.ID ASC";                    
                    $enquiry_data = $dbutil->getSqlData($sqlsrr); 
                    
                    //var_dump($enquiry_data[0]);
                    
                    $stateid=$enquiry_data[0]['BillingState_ID'];
                    $countryid=$enquiry_data[0]['BillingCountry_ID'];
                    
                    //state table data 
           
                    $state_sql = "SELECT ID,StateName FROM $state_table where ID=$stateid";
                    $stmt = $this->db->prepare($state_sql);            
                    $stmt->execute();
                    $state_data  = $stmt->fetchAll();	
                    $this->tpl->set('state_data', $state_data);
                    
                    //country table data 
           
                    $country_sql = "SELECT ID,CountryName FROM $country_table where ID=$countryid";
                    $stmt = $this->db->prepare($country_sql);            
                    $stmt->execute();
                    $country_data  = $stmt->fetchAll();	
                    $this->tpl->set('country_data', $country_data);

                    //edit option     
                    $this->tpl->set('message', 'You can edit Enquiry form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('FmData', $enquiry_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_form.php'));                    
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

                    //Build SQL now
                     $sqlsel_del = "SELECT RPFIfAny FROM $enquirydetail_tab WHERE enquiry_ID  = '$data'";
                     $stmt = $this->db->prepare($sqlsel_del);
                     $stmt->execute();
                     $resource_data = $stmt->fetchAll();
                     
                   //  echo $resource_data;die;
                     
                     if(!empty($resource_data)){
                          foreach($resource_data as $k=>$v){
                                    if(file_exists($v['RPFIfAny'])){
                                        unlink($v['RPFIfAny']); 
                                    }
                                }
                     }
                                 
                               
                    $sqldet_del = "DELETE FROM $enquirydetail_tab WHERE enquiry_ID=$data";
                    $stmt = $this->db->prepare($sqldet_del);
                    $stmt->execute();   
                            
                            try{
                            
                            $emp_id= $form_post_data['employee_ID'];
                            $enquirymode= $form_post_data['EnquiryMode'];
                            $enq_type= $form_post_data['EnquiryType'];
                            $pdndept_id= $form_post_data['pdndepartment_ID'];
                            $customerid= $form_post_data['customer_ID'];
                            $costreq= $form_post_data['CostRequired'];
                            $orderreceived= $form_post_data['OrderReceived'];
                            $enquirystatus= $form_post_data['EnquiryStatus'];
                            $pono= $form_post_data['PONo'];
                            $pdnstatus= $form_post_data['ProductionStatus'];
                            $remainder_date=date("Y-m-d", strtotime($form_post_data['RemaindDate']));
                            $remainder_time= $form_post_data['RemaindTime'];
                            $remainder_abt= $form_post_data['RemaindAbout'];
                                          
                           
                            $sql_update="Update $enquirymaster_tab SET  employee_ID='$emp_id',
                                                                        EnquiryMode='$enquirymode',
                                                                        EnquiryType='$enq_type',
                                                                        pdndepartment_ID='$pdndept_id',
                                                                        customer_ID='$customerid',
                                                                        CostRequired='$costreq',
                                                                        OrderReceived='$orderreceived',
                                                                        EnquiryStatus='$enquirystatus',
                                                                        PONo='$pono',
                                                                        ProductionStatus='$pdnstatus',
                                                                        RemaindDate='$remainder_date',
                                                                        RemaindTime='$remainder_time',
                                                                        RemaindAbout='$remainder_abt'
                                                                        WHERE ID=$data";
                            $stmt1 = $this->db->prepare($sql_update);
                            $stmt1->execute();
                        if($enq_type==1){
                        
                        FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                                
                                $productname =$form_post_data['ItemName_' . $entry_count];
                                $quantity =$form_post_data['Quantity_' . $entry_count];
                                $unit_id =$form_post_data['unit_' . $entry_count];
                                $remarks =$form_post_data['Note_' . $entry_count];
                                $rpfany ='Water_' . $entry_count;
                           
                                if(!empty($_FILES[$rpfany]['name'])){
                                     $uploadedfile = $util->custom_file_upload_specific($rpfany, $data,'enquiry');
                                    // print_r($uploadedfile);
                                }else{
                                     $uploadedfile ='';
                                }
                                
                            if(!empty($productname) && !empty($quantity) && !empty($unit_id) ){
                                $vals = "'" . $data . "'," .
                                        "'" . $productname . "'," .
                                        "'" . $quantity . "'," .
                                        "'" . $unit_id . "'," .
                                        "'" . $remarks . "'," .
                                        "'" . $uploadedfile . "'" ;
  
                                $sql2 = "INSERT INTO $enquirydetail_tab
                                        ( 
                                            `enquiry_ID`, 
                                            `ProductName`,
                                            `Quantity`,
                                            `unit_ID`,
                                            `Remarks`,
                                            `RPFIfAny`
                                        ) 
                                VALUES ($vals)";

                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                            }
                        }
                            $this->tpl->set('message', 'Enquiry form edited successfully!');   
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiry');
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_form.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                    
                       // var_dump($_POST);
                        
						$customer_ID = $form_post_data['customer_ID'];		    
						
                        if (isset($form_post_data['EnquiryType']) && $form_post_data['EnquiryType']=='1') {
                           
                                $val =  "'" . $form_post_data['EnquiryNo'] . "'," .
                                        "'" . $form_post_data['employee_ID'] . "'," .
                                        "'" . $form_post_data['EnquiryMode'] . "'," .
                                        "'" . $form_post_data['EnquiryType'] . "'," .
                                        "'" . $form_post_data['pdndepartment_ID'] . "'," .
                                        "'" . $customer_ID . "'," .
                                        "'" . $form_post_data['CostRequired'] . "'," .
                                        "'" . $form_post_data['OrderReceived'] . "'," .
                                        "'" . $form_post_data['EnquiryStatus'] . "'," .
                                        "'" . $form_post_data['PONo'] . "'," .
                                        "'" . $form_post_data['ProductionStatus'] . "'," .
                                        "'" . date("Y-m-d", strtotime($form_post_data['RemaindDate'])) . "'," .
                                        "'" . $form_post_data['RemaindTime'] . "'," .
                                        "'" . $form_post_data['RemaindAbout'] . "'," .
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";

                                    $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "enquiry`
                                            ( 
                                            `EnquiryNo`,
                                            `employee_ID`,
                                            `EnquiryMode`,
                                            `EnquiryType`,
                                            `pdndepartment_ID`,
                                            `customer_ID`,
                                            `CostRequired`,
                                            `OrderReceived`,
                                            `EnquiryStatus`,
                                            `PONo`,
                                            `ProductionStatus`,
                                            `RemaindDate`,
                                            `RemaindTime`,
                                            `RemaindAbout`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                        VALUES ( $val )";
                                    $stmt = $this->db->prepare($sql);
                                  
                                  
                        if ($stmt->execute()) { 
                        $lastInsertedID = $this->db->lastInsertId();
                        FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                               
                                $productname =$form_post_data['ItemName_' . $entry_count];
                                $quantity =$form_post_data['Quantity_' . $entry_count];
                                $unit_id =$form_post_data['unit_' . $entry_count];
                                $remarks =$form_post_data['Note_' . $entry_count];
                                $rpfany ='Water_' . $entry_count;
                                
                                if(!empty($_FILES[$rpfany]['name'])){
                                    
                                     $uploadedfile = $util->custom_file_upload_specific($rpfany, $lastInsertedID,'enquiry');
                                }else{
                                     $uploadedfile ='';
                                }
                            if(!empty($productname) && !empty($quantity) && !empty($unit_id) ){
                                $vals = "'" . $lastInsertedID . "'," .
                                        "'" . $productname . "'," .
                                        "'" . $quantity . "'," .
                                        "'" . $unit_id . "'," .
                                        "'" . $remarks . "'," .
                                        "'" . $uploadedfile . "'" ;
                                 
                                $sql2 = "INSERT INTO $enquirydetail_tab
                                        ( 
                                            `enquiry_ID`, 
                                            `ProductName`,
                                            `Quantity`,
                                            `unit_ID`,
                                            `Remarks`,
                                            `RPFIfAny`
                                        ) 
                                VALUES ($vals)";

                                 // this need to be changed in to transaction type
                                
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                               
                            }
                        }
                        }else{
                                $val =  "'" . $form_post_data['EnquiryNo'] . "'," .
                                        "'" . $form_post_data['employee_ID'] . "'," .
                                        "'" . $form_post_data['EnquiryMode'] . "'," .
                                        "'" . $form_post_data['EnquiryType'] . "'," .
                                        "'" . $form_post_data['pdndepartment_ID'] . "'," .
                                        "'" . $form_post_data['customer_ID'] . "'," .
                                        "'" . $form_post_data['CostRequired'] . "'," .
                                        "'" . $form_post_data['OrderReceived'] . "'," .
                                        "'" . $form_post_data['EnquiryStatus'] . "'," .
                                        "'" . $form_post_data['PONo'] . "'," .
                                        "'" . $form_post_data['ProductionStatus'] . "'," .
                                        "'" . date("Y-m-d", strtotime($form_post_data['RemaindDate'])) . "'," .
                                        "'" . $form_post_data['RemaindTime'] . "'," .
                                        "'" . $form_post_data['RemaindAbout'] . "'," .
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";

                              $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "enquiry`
                                            ( 
                                            `EnquiryNo`,
                                            `employee_ID`,
                                            `EnquiryMode`,
                                            `EnquiryType`,
                                            `pdndepartment_ID`,
                                            `customer_ID`,
                                            `CostRequired`,
                                            `OrderReceived`,
                                            `EnquiryStatus`,
                                            `PONo`,
                                            `ProductionStatus`,
                                            `RemaindDate`,
                                            `RemaindTime`,
                                            `RemaindAbout`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                        VALUES ( $val )";
                                  $stmt = $this->db->prepare($sql);
                                  $stmt->execute();
                        }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiry');
                       // $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_form.php'));
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Production');
	                $EnquiryNo=$dbutil->keyGeneration('enquiry','ENQ','','EnquiryNo');
                    $this->tpl->set('EnquiryNo', $EnquiryNo);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$enquirymaster_tab.ID",
                "$enquirymaster_tab.EnquiryNo",
                "$employee_table.EmpName",
                "$customer_table.CompanyName",
                "$customer_table.PersonName",
                "$pdnorder_tab.PdnOrderNo",
                "$pdndept_table.DeptName",
                "$enquirydetail_tab.ProductName",
                "$enquirydetail_tab.Quantity"
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
             $whereString ="ORDER BY $enquirymaster_tab.ID DESC";
           }
           
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $enquirymaster_tab 
                             LEFT JOIN $enquirydetail_tab ON $enquirymaster_tab.ID=$enquirydetail_tab.enquiry_ID 
                             LEFT JOIN $pdndept_table ON $enquirymaster_tab.pdndepartment_ID=$pdndept_table.ID 
                             LEFT JOIN $pdnorder_tab ON $pdnorder_tab.enquiry_ID=$enquirymaster_tab.ID
                             LEFT JOIN $employee_table ON $enquirymaster_tab.employee_ID=$employee_table.ID 
                             LEFT JOIN $customer_table ON $enquirymaster_tab.customer_ID=$customer_table.ID "
                    . " WHERE "
                    . " $enquirymaster_tab.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Enquiry No','Attended By','Company Name','Contact Person','PO Number','Production Department','Product Name','Quantity'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_form_enquiry.php';
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
    
     public function enquiryRemainder(){
    
        if ($this->crg->get('wp') || $this->crg->get('rp')) {
                 
                ////////////////////////////////////////////////////////////////////////////////
                //////////////////////////////access condition applied//////////////////////////
                ////////////////////////////////////////////////////////////////////////////////    
                            
                include_once 'util/DBUTIL.php';
                $dbutil = new DBUTIL($this->crg);
                 
                $entityID = $this->ses->get('user')['entity_ID'];
                $userID = $this->ses->get('user')['ID'];
                
                $enquiry_reminder_table = $this->crg->get('table_prefix') . 'enquiry_reminder';
                $customer_table = $this->crg->get('table_prefix') . 'customer';
                $employee_table = $this->crg->get('table_prefix') . 'employee';
                $enquiry_table = $this->crg->get('table_prefix') . 'enquiry';
    
                $sql = "SELECT ID,CONCAT(CompanyName ,' || ', PersonName) AS Title FROM $customer_table ORDER BY ID DESC";
                $stmt = $this->db->prepare($sql);             
                $stmt->execute();
                $cmp_data  = $stmt->fetchAll();	
                $this->tpl->set('customer_data', $cmp_data);

                $sql = "SELECT ID,EmpName FROM $employee_table";
                $stmt = $this->db->prepare($sql);             
                $stmt->execute();
                $emp_data  = $stmt->fetchAll();	
                $this->tpl->set('emp_data', $emp_data);

                $sql = "SELECT ID,EnquiryNo FROM $enquiry_table";
                $stmt = $this->db->prepare($sql);             
                $stmt->execute();
                $enquiry_data  = $stmt->fetchAll();	
                $this->tpl->set('enquiry_data', $enquiry_data);
                
                $this->tpl->set('page_title', 'Enquiry Remainder');	          
                $this->tpl->set('page_header', 'Enquiry Remainder');
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
                         
                         $sqldetdelete="Delete from $enquiry_reminder_table
                                            where $enquiry_reminder_table.ID=$data"; 
                            $stmt = $this->db->prepare($sqldetdelete);            
                            
                            if($stmt->execute()){
                            $this->tpl->set('message', 'Enquiry Remainder form deleted successfully');
                                                                                                                          
                            //$this->tpl->set('label', 'List');
                            //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiryRemainder');
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
                         FROM $enquiry_reminder_table 
                         WHERE 
                         $enquiry_reminder_table.ID = $data";   
                         $enquiry_reminder_data = $dbutil->getSqlData($sqlsrr);
                       
                    
                        //edit option     
                        $this->tpl->set('message', 'You can view Enquiry Remainder form');
                        $this->tpl->set('page_header', 'Enquiry Remainder');
                        $this->tpl->set('FmData', $enquiry_reminder_data); 
                        
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_reminder_design_form.php'));                    
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
                               FROM $enquiry_reminder_table 
                               WHERE 
                               $enquiry_reminder_table.ID = $data";   
                               $enquiry_reminder_data = $dbutil->getSqlData($sqlsrr);
                       
                        
                        //edit option 
    
                        
                        $this->tpl->set('message', 'You can edit Enquiry Remainder form');
                        $this->tpl->set('page_header', 'Enquiry Remainder');
                        $this->tpl->set('FmData', $enquiry_reminder_data); 
                        
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_reminder_design_form.php'));                    
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
            
                        $due_date=!empty($form_post_data['due_date'])?date("Y-m-d", strtotime($form_post_data['due_date'])):'';
                                                   
                        try{
                              $enquiry_ID= $form_post_data['enquiry_ID'];
                              $customer_ID= $form_post_data['customer_ID'];
                              $due_date= $due_date;
                              $RemaindTime= $form_post_data['RemaindTime'];
                              $assigned_to_ID= $form_post_data['assigned_to_ID'];
                              $task_repeat= $form_post_data['task_repeat'];
                              $reminder_des= $form_post_data['reminder_des'];
                              $status= $form_post_data['status'];
                              
                                $sql_update="UPDATE $enquiry_reminder_table set enquiry_ID='$enquiry_ID',
                                                                                customer_ID='$customer_ID',
                                                                                due_date='$due_date',
                                                                                RemaindTime='$RemaindTime',
                                                                                assigned_to_ID='$assigned_to_ID',
                                                                                task_repeat='$task_repeat',
                                                                                reminder_des='$reminder_des',
                                                                                status ='$status'
                                                                                WHERE ID=$data";
                                $stmt1 = $this->db->prepare($sql_update);
                                $stmt1->execute();
                               
                                
                                $this->tpl->set('message', 'Enquiry Remainder form edited successfully!');   
                                                                                                                              
                                // $this->tpl->set('label', 'List');
                                // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiryRemainder');
                                } catch (Exception $exc) {
                                 //edit failed option
                                $this->tpl->set('message', 'Failed to edit, try again!');
                                $this->tpl->set('FmData', $form_post_data);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_reminder_design_form.php'));
                                }
    
                        break;
    
                    case 'addsubmit':
                         if (isset($crud_string)) {
     
                            $form_post_data = $dbutil->arrFltr($_POST);
                            
                          // var_dump($_POST);
                         
                          $due_date=!empty($form_post_data['due_date'])?date("Y-m-d", strtotime($form_post_data['due_date'])):'';
                           
                           if (isset($form_post_data['enquiry_ID'])) {
                               
                                            $val = "'" . $form_post_data['enquiry_ID'] . "'," .
                                             "'" . $form_post_data['customer_ID'] . "'," .
                                             "'" . $due_date . "'," .
                                             "'" . $form_post_data['RemaindTime'] . "'," .
                                             "'" . $form_post_data['assigned_to_ID'] . "'," .
                                             "'" . $form_post_data['task_repeat'] . "'," .
                                             "'" . $form_post_data['reminder_des'] . "'," .
                                             "'" . $form_post_data['status'] . "'," .
                                                  "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                             "'" .  $this->ses->get('user')['ID'] . "'";
    
                                 $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "enquiry_reminder`
                                                (
                                                `enquiry_ID`, 
                                                `customer_ID`, 
                                                `due_date`,
                                                `RemaindTime`,
                                                `assigned_to_ID`,
                                                `task_repeat`, 
                                                `reminder_des`,
                                                `status`,
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
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiryRemainder');
                                                                                                            
                         } else {
                                //edit option
                                //if submit failed to insert form
                                $this->tpl->set('message', 'Failed to submit!');
                                $this->tpl->set('FmData', $form_post_data);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_reminder_design_form.php'));
                         }
                        break;
                    case 'add':
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('page_header', 'Enquiry Remainder');
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_reminder_design_form.php'));
                        break;
    
                    default:
                        /*
                         * List form
                         */
                         
                        ////////////////////start//////////////////////////////////////////////
                        
               //bUILD SQL 
                $whereString = '';
             $colArr = array(
                    "$enquiry_reminder_table.ID",
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
                 $whereString ="ORDER BY $enquiry_reminder_table.ID DESC";
               }
               
                  
           $sql = "SELECT " 
                        . implode(',',$colArr)
                        . " FROM $enquiry_reminder_table LEFT JOIN $enquiry_table ON $enquiry_table.ID = $enquiry_reminder_table.enquiry_ID "
                        . " WHERE "
                        . " $enquiry_reminder_table.entity_ID = $entityID" 
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
                         
                        include_once $this->tpl->path . '/factory/form/crud_enquiry_reminder_form.php';
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
   

function tender(){
     
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
                
                
                $employee_table = $this->crg->get('table_prefix') . 'employee';
                $pdndept_table = $this->crg->get('table_prefix') . 'pdndepartment';
                $dept_table = $this->crg->get('table_prefix') . 'department';
                $rawmaterial_table = $this->crg->get('table_prefix') . 'rawmaterial';
                $unit_table = $this->crg->get('table_prefix') . 'unit';
                $tenderattachment_tab = $this->crg->get('table_prefix') . 'tender_attachments';
    			$tendersubdetail_tab = $this->crg->get('table_prefix') . 'tenderremarks';
                $tenderdetail_tab = $this->crg->get('table_prefix') . 'tenderdetail';
                $tendermaster_tab = $this->crg->get('table_prefix') . 'tender';
                $customer_table = $this->crg->get('table_prefix') . 'customer';
                $state_table = $this->crg->get('table_prefix') . 'state';
                $country_table = $this->crg->get('table_prefix') . 'country';
                $enquiry_table = $this->crg->get('table_prefix') . 'enquiry';
              
                // mode of bidding type array
                
                $biddingtype_data = array(array("ID"=>"1","Title"=>"Online"),array("ID"=>"2","Title"=>"Offline"));
                $this->tpl->set('biddingtype_data', $biddingtype_data);
                
                // contracttype_data array
                
                $contracttype_data = array(array("ID"=>"1","Title"=>"Goods"),array("ID"=>"2","Title"=>"Service"));
                $this->tpl->set('contracttype_data', $contracttype_data);
                
                // tendertype_data array
                
                $tendertype_data = array(array("ID"=>"1","Title"=>"Open"),array("ID"=>"2","Title"=>"Limited"),array("ID"=>"3","Title"=>"Special Limited"));
                $this->tpl->set('tendertype_data', $tendertype_data);
                
                // tenderstatus_data array
                
                $tenderstatus_data = array(array("ID"=>"1","Title"=>"Bid not Submitted"),array("ID"=>"2","Title"=>"Bid Submitted"));
                $this->tpl->set('tenderstatus_data', $tenderstatus_data);
                
                // Regular / Developmental data array
                
                $reg_or_dev_data = array(array("ID"=>"1","Title"=>"Regular"),array("ID"=>"2","Title"=>"Developmental"));
                $this->tpl->set('reg_or_dev_data', $reg_or_dev_data);
                
                // procure_from_data array
                
                $procure_from_data = array(array("ID"=>"1","Title"=>"Yes"),array("ID"=>"2","Title"=>"No"));
                $this->tpl->set('procure_from_data', $procure_from_data);
                
                // RA_enabled_data array
                
                $ra_enabled_data = array(array("ID"=>"1","Title"=>"Yes"),array("ID"=>"2","Title"=>"No"));
                $this->tpl->set('ra_enabled_data', $ra_enabled_data);
                
                // bidresult_data array
                
                $bidresult_data = array(array("ID"=>"1","Title"=>"Bid Awarded"),array("ID"=>"2","Title"=>"Bid Not Awarded"));
                $this->tpl->set('bidresult_data', $bidresult_data);
                
                // prebidconference_data array
                
                $prebidconference_data = array(array("ID"=>"1","Title"=>"Yes"),array("ID"=>"2","Title"=>"No"));
                $this->tpl->set('prebidconference_data', $prebidconference_data);
                
                // RA_enabled_data array
                
                $requestforprice_data = array(array("ID"=>"1","Title"=>"Yes"),array("ID"=>"2","Title"=>"No"));
                $this->tpl->set('requestforprice_data', $requestforprice_data);
                
                //unit table data
               
                $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
                $stmt = $this->db->prepare($pdt_sql);            
                $stmt->execute();
                $unit_data  = $stmt->fetchAll();	
                $this->tpl->set('unit_data', $unit_data);
                
                //enquiry table data
               
                $enquiry_sql = "SELECT ID,EnquiryNo FROM $enquiry_table WHERE $enquiry_table.EnquiryType=2 AND $enquiry_table.Tender_Status=1";
                $stmt = $this->db->prepare($enquiry_sql);            
                $stmt->execute();
                $enquiry_data  = $stmt->fetchAll();	
                $this->tpl->set('enquiry_data', $enquiry_data);
				
				$enquiry1_sql = "SELECT ID,EnquiryNo FROM $enquiry_table WHERE $enquiry_table.EnquiryType=2 AND $enquiry_table.Tender_Status=2";
                $stmt = $this->db->prepare($enquiry1_sql);            
                $stmt->execute();
                $enquiry1_data  = $stmt->fetchAll();	
                $this->tpl->set('enquiry1_data', $enquiry1_data);
                
               
                $this->tpl->set('page_title', 'Tender');	          
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
                          // var_dump($data); 
                           
                           
                        if (!$data) {
                            $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                            $this->tpl->set('label', 'List');
                            $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                           
                        }
                         $sqlsel_del = "SELECT document_path FROM $tenderattachment_tab WHERE tender_ID  = '$data'";
                         $resource_data = $dbutil->getSqlData($sqlsel_del); 
                         
                         if(!empty($resource_data)){
                              foreach($resource_data as $k=>$v){
                                           unlink(substr($v['document_path'], 2));
                                    }
                         }
                         
						 $sql_update="UPDATE $enquiry_table SET Tender_Status=1 WHERE ID=(SELECT enquiry_ID FROM $tendermaster_tab WHERE $tendermaster_tab.ID= $data)";
                         $stmt1 = $this->db->prepare($sql_update);
					     $stmt1->execute();
                                    
                         $sqldetdelete="Delete $tenderdetail_tab,$tendermaster_tab,$tendersubdetail_tab,$tenderattachment_tab from $tendermaster_tab
                                        LEFT JOIN  $tenderdetail_tab ON $tendermaster_tab.ID=$tenderdetail_tab.tender_ID 
                                        LEFT JOIN  $tendersubdetail_tab ON $tendermaster_tab.ID=$tendersubdetail_tab.tender_ID 
                                        LEFT JOIN  $tenderattachment_tab ON $tendermaster_tab.ID=$tenderattachment_tab.tender_ID 
                                        where $tendermaster_tab.ID=$data"; 
                        $stmt = $this->db->prepare($sqldetdelete);            
                            
                            if($stmt->execute()){
                            
                            $this->tpl->set('message', 'Enquiry deleted successfully');
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/tender');
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
                                    
                       $sqlsrr = "SELECT $tendermaster_tab.* ,$tenderdetail_tab.*,
                                          $enquiry_table.EnquiryNo,
                                          $pdndept_table.DeptName as pdn_dept,
                                          $customer_table.PersonName,
                                          $customer_table.CompanyName,
                                          $customer_table.BillingAddress1,
                                          $customer_table.BillingAddress2,
                                          $customer_table.BillingCity,
                                          $customer_table.BillingZip,
                                          $customer_table.Address_type,
                                          $customer_table.Email,
                                          $customer_table.MobileNo,
                                          $state_table.StateName as BillingState,
                                          $country_table.CountryName as BillingCountry,
                                          $employee_table.EmpName
                                    FROM  `$tendermaster_tab` 
                                    LEFT JOIN `$tenderdetail_tab` ON  `$tendermaster_tab`.`ID`=`$tendermaster_tabdetail`.`tender_ID` 
                                    LEFT JOIN `$enquiry_table` ON `$tendermaster_tab`.enquiry_ID=`$enquiry_table`.ID 
                                    LEFT JOIN `$employee_table` ON `$enquiry_table`.employee_ID=`$employee_table`.ID 
                                    LEFT JOIN `$pdndept_table` ON `$enquiry_table`.`pdndepartment_ID`=`$pdndept_table`.`ID` 
                                    LEFT JOIN `$customer_table` ON  `$enquiry_table`.`customer_ID`=`$customer_table`.`ID` 
                                    LEFT JOIN `$state_table` ON  `$customer_table`.`BillingState_ID`=`$state_table`.`ID` 
                                    LEFT JOIN `$country_table` ON  `$customer_table`.`BillingCountry_ID`=`$country_table`.`ID` 
                                    WHERE `$tendermaster_tab`.`ID` = '$data' ORDER BY $tenderdetail_tab.ID ASC";                    
                        $tender_data = $dbutil->getSqlData($sqlsrr); 
        
                        $sqlsrr = "SELECT * FROM  `$tendersubdetail_tab` WHERE `$tendersubdetail_tab`.`tender_ID` = '$data' ORDER BY $tendersubdetail_tab.ID ASC";                    
                        $tendersub_data = $dbutil->getSqlData($sqlsrr); 
                        
                        $document= "SELECT ID,document_path  From $tenderattachment_tab where (tender_ID  = '$data' AND attribute_name='cost') ORDER BY $tenderattachment_tab.ID ASC";  
                        $cost_data = $dbutil->getSqlData($document);
                        $this->tpl->set('FmDatacost', $cost_data);

                        $sql= "SELECT ID,document_path  From $tenderattachment_tab where (tender_ID  = '$data' AND attribute_name='specification') ORDER BY $tenderattachment_tab.ID ASC";            
                        $specification_data = $dbutil->getSqlData($sql);
                        $this->tpl->set('FmDataspecification', $specification_data);
                        
                        $sql= "SELECT ID,document_path  From $tenderattachment_tab where (tender_ID  = '$data' AND attribute_name='attachment') ORDER BY $tenderattachment_tab.ID ASC";            
                        $attachemnt_data = $dbutil->getSqlData($sql);
                        $this->tpl->set('FmDataattachment', $attachemnt_data);
                        
                       
                        //var_dump($tender_data[0]);
                      
                        //edit option     
                        $this->tpl->set('message', 'You can view Tender form');
                        $this->tpl->set('page_header', 'Production');
                        $this->tpl->set('FmData', $tender_data); 
                        $this->tpl->set('FmDataSub', $tendersub_data); 
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_form.php'));                    
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
                                
                        $sqlsrr = "SELECT $tendermaster_tab.* ,$tenderdetail_tab.*,
                                          $enquiry_table.EnquiryNo,
                                          $pdndept_table.DeptName as pdn_dept,
                                          $customer_table.PersonName,
                                          $customer_table.CompanyName,
                                          $customer_table.BillingAddress1,
                                          $customer_table.BillingAddress2,
                                          $customer_table.BillingCity,
                                          $customer_table.BillingZip,
                                          $customer_table.Address_type,
                                          $customer_table.Email,
                                          $customer_table.MobileNo,
                                          $state_table.StateName as BillingState,
                                          $country_table.CountryName as BillingCountry,
                                          $employee_table.EmpName
                                    FROM  `$tendermaster_tab` 
                                    LEFT JOIN `$tenderdetail_tab` ON  `$tendermaster_tab`.`ID`=`$tendermaster_tabdetail`.`tender_ID` 
                                    LEFT JOIN `$enquiry_table` ON `$tendermaster_tab`.enquiry_ID=`$enquiry_table`.ID 
                                    LEFT JOIN `$employee_table` ON `$enquiry_table`.employee_ID=`$employee_table`.ID 
                                    LEFT JOIN `$pdndept_table` ON `$enquiry_table`.`pdndepartment_ID`=`$pdndept_table`.`ID` 
                                    LEFT JOIN `$customer_table` ON  `$enquiry_table`.`customer_ID`=`$customer_table`.`ID` 
                                    LEFT JOIN `$state_table` ON  `$customer_table`.`BillingState_ID`=`$state_table`.`ID` 
                                    LEFT JOIN `$country_table` ON  `$customer_table`.`BillingCountry_ID`=`$country_table`.`ID` 
                                    WHERE `$tendermaster_tab`.`ID` = '$data' ORDER BY $tenderdetail_tab.ID ASC";                    
                        $tender_data = $dbutil->getSqlData($sqlsrr); 
                        
                        // bidresult_data array
                        if($tender_data[0]['RAEnabledYN']==1){
                            $bidresult_data = array(array("ID"=>"1","Title"=>"Bid Awarded"),array("ID"=>"2","Title"=>"Bid Not Awarded"),array("ID"=>"3","Title"=>"RA Pending"));
                            $this->tpl->set('bidresult_data', $bidresult_data);
        
                        }else{
                             $bidresult_data = array(array("ID"=>"1","Title"=>"Bid Awarded"),array("ID"=>"2","Title"=>"Bid Not Awarded"));
                             $this->tpl->set('bidresult_data', $bidresult_data);
                        }
                        
                        $sqlsrr = "SELECT * FROM  `$tendersubdetail_tab` WHERE `$tendersubdetail_tab`.`tender_ID` = '$data' ORDER BY $tendersubdetail_tab.ID ASC";                    
                        $tendersub_data = $dbutil->getSqlData($sqlsrr); 
                        
                        $document= "SELECT ID,document_path  From $tenderattachment_tab where (tender_ID  = '$data' AND attribute_name='cost') ORDER BY $tenderattachment_tab.ID ASC";  
                        $cost_data = $dbutil->getSqlData($document);
                        $this->tpl->set('FmDatacost', $cost_data);

                        $sql= "SELECT ID,document_path  From $tenderattachment_tab where (tender_ID  = '$data' AND attribute_name='specification') ORDER BY $tenderattachment_tab.ID ASC";            
                        $specification_data = $dbutil->getSqlData($sql);
                        $this->tpl->set('FmDataspecification', $specification_data);
                        
                        $sql= "SELECT ID,document_path  From $tenderattachment_tab where (tender_ID  = '$data' AND attribute_name='attachment') ORDER BY $tenderattachment_tab.ID ASC";            
                        $attachemnt_data = $dbutil->getSqlData($sql);
                        $this->tpl->set('FmDataattachment', $attachemnt_data);
                        
                        
                        //var_dump($tender_data[0]);
                        
                        //edit option     
                        $this->tpl->set('message', 'You can edit Tender form');
                        $this->tpl->set('page_header', 'Production');
                        $this->tpl->set('FmData', $tender_data); 
                        $this->tpl->set('FmDataSub', $tendersub_data); 
                        
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_form.php'));                    
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
    
                        //Build SQL now
                        /* $sqlsel_del = "SELECT RPFIfAny FROM $tenderdetail_tab WHERE tender_ID  = '$data'";
                         $stmt = $this->db->prepare($sqlsel_del);
                         $stmt->execute();
                         $resource_data = $stmt->fetchAll();
                         
                         if(!empty($resource_data)){
                              foreach($resource_data as $k=>$v){
                                        if(file_exists($v['RPFIfAny'])){
                                            unlink($v['RPFIfAny']); 
                                        }
                                    }
                         }
                        */           
                                   
                        $sqldet_del = "DELETE FROM $tenderdetail_tab WHERE tender_ID=$data";
                        $stmt = $this->db->prepare($sqldet_del);
                        $stmt->execute();   
    					
    					$sqldet_del = "DELETE FROM $tendersubdetail_tab WHERE tender_ID=$data";
                        $stmt = $this->db->prepare($sqldet_del);
                        $stmt->execute();
                                
                        try{
    							
    							
                            $opening_date=!empty($form_post_data['OpeningDate'])?date("Y-m-d", strtotime($form_post_data['OpeningDate'])):'';
                            $prebid_conf_date=!empty($form_post_data['PreBidConfDate'])?date("Y-m-d", strtotime($form_post_data['PreBidConfDate'])):'';
                            $publishing_date=!empty($form_post_data['PublishingDate'])?date("Y-m-d", strtotime($form_post_data['PublishingDate'])):'';
                            $biddone_date=!empty($form_post_data['BiddingDoneDate'])?date("Y-m-d", strtotime($form_post_data['BiddingDoneDate'])):'';
                            $closedatetime=!empty($form_post_data['ClosingDateTime'])?$form_post_data['ClosingDateTime']:'';
                            $RA_datetime=!empty($form_post_data['RADateTime'])?$form_post_data['RADateTime']:'';
                            
                                $customer_tenderno= $form_post_data['CustomerTenderNo'];
    							$enq_id= $form_post_data['enquiry_ID'];
                                $tendersection= $form_post_data['TenderSection'];
                                $closing_date_time=$closedatetime;
                                $opening_date= $opening_date;
                                $bidding_type= $form_post_data['BiddingType'];
                                $contract_type= $form_post_data['ContractType'];
    							$tender_type= $form_post_data['TenderType'];
                                $evaln_criteria= $form_post_data['EvaluationCriteria'];
                                $bidding_system= $form_post_data['BiddingSystem'];
                                $prebid_conf_req= $form_post_data['PreBidConfRequiredYN'];
                                $prebid_conf_date=$prebid_conf_date;
                                $inspection_agency=$form_post_data['InspectionAgency'];
    							$publishing_date=$publishing_date;
    							$bid_done_date= $biddone_date;
    							$procure_approve= $form_post_data['ProcureApproveYN'];
    							$approve_agency= $form_post_data['ApproveAgency'];
								$TenderDocCost= $form_post_data['TenderDocCost'];
    							$ra_enabled= $form_post_data['RAEnabledYN'];
                                $ra_date_time=$RA_datetime;
                                $reg_or_dev= $form_post_data['RegularOrDev'];
    							$validity_offer= $form_post_data['ValidityOfferDays'];
    							$bid_result =$form_post_data['BidResult'];
    							$tender_status= $form_post_data['TenderStatus'];
    							$emd= $form_post_data['EMD'];
                                              
                               
                                $sql_update="Update $tendermaster_tab SET   CustomerTenderNo='$customer_tenderno',
                                                                            enquiry_ID='$enq_id',
                                                                            TenderSection='$tendersection',
                                                                            ClosingDateTime='$closing_date_time',
                                                                            OpeningDate='$opening_date',
                                                                            BiddingType='$bidding_type',
                                                                            ContractType='$contract_type',
                                                                            TenderType='$tender_type',
                                                                            EvaluationCriteria='$evaln_criteria',
                                                                            BiddingSystem='$bidding_system',
                                                                            PreBidConfRequiredYN='$prebid_conf_req',
                                                                            PreBidConfDate='$prebid_conf_date',
                                                                            InspectionAgency='$inspection_agency',
    																		PublishingDate='$publishing_date',
    																		BiddingDoneDate='$bid_done_date',
    																		ProcureApproveYN='$procure_approve',
    																		ApproveAgency='$approve_agency',
																			TenderDocCost='$TenderDocCost',
    																		RAEnabledYN='$ra_enabled',
    																		RADateTime='$ra_date_time',
    																		RegularOrDev='$reg_or_dev',
    																		ValidityOfferDays='$validity_offer',
    																		BidResult='$bid_result',
    																		TenderStatus='$tender_status',
    																		EMD='$emd'
                                                                            WHERE ID=$data";
                                $stmt1 = $this->db->prepare($sql_update);
                                $stmt1->execute();
                                if($stmt1){
                                    $updateCustomer = array();
                                    
                                    for($j=1;$j<=3;$j++){
                                    foreach ($_FILES['files'.$j]['name'] as $i => $name) {
                                            $Fvalue='files'.$j;
                                            $uploadedFile = $util->multi_handle_file_upload_backup($Fvalue, $data,$i,'tender');
                                             
                                        if ($uploadedFile) {
                                           
                                            $updateCustomer[] = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                         
                                            $filename='"' . $uploadedFile.'"'  ;
                                            $type="";
                                            
                                            if($j==1){
                                               $type="cost";
                                            }
                                             if($j==2){
                                               $type="attachment";
                                            }
                                             if($j==3){
                                               $type="specification";
                                            }
                                        
                                   
                                            $valStr= "'" .  $type . "'," .
                                                     "" . $filename . "," .
                                                     "'" . $data . "'";
                                                         
                                           if($filename!='' && $filename!=null){    
                                               
                                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "tender_attachments` (". " `attribute_name`, `document_path`,`tender_ID`) VALUES ( $valStr )";
                                            $stmt = $this->db->prepare($sql);
                                            $stmt->execute();
                                            
                                            }
                                           
                                        }
                                                     
                                     }
                                    }
                                }
                                
    
                            
                            FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                                    
                                    $title =$form_post_data['ItemName_' . $entry_count];
                                    $plcode =$form_post_data['ItemNo_' . $entry_count];
    								$description =$form_post_data['Note_' . $entry_count];
    								$consignee =$form_post_data['Water_' . $entry_count];
    								$deliveryloc =$form_post_data['Grade_' . $entry_count];
    								$qty =$form_post_data['Quantity_' . $entry_count];
                                    $unit_id =$form_post_data['unit_' . $entry_count];
    								$requestforprice =$form_post_data['Amount_' . $entry_count];
    								$price_received =$form_post_data['Rat_' . $entry_count];
                                    $price_quoted =$form_post_data['Rate_' . $entry_count];
                                    
                                    /*if(!empty($_FILES[$rpfany]['name'])){
                                         $uploadedfile = $util->custom_file_upload_specific($rpfany, $data,'tender');
                                    }else{
                                         $uploadedfile ='';
                                    }
    								*/
    								if(!empty($title) && !empty($plcode) && !empty($qty) && !empty($unit_id)){
                                    $vals = "'" . $data . "'," .
                                            "'" . $title . "'," .
                                            "'" . $plcode . "'," .
    										"'" . $description . "'," .
    										"'" . $consignee . "'," .
    										"'" . $deliveryloc . "'," .
    										"'" . $qty . "'," .
                                            "'" . $unit_id . "'," .
    										"'" . $requestforprice . "'," .
                                            "'" . $price_received . "'," .
                                            "'" . $price_quoted . "'" ;
      
                                    $sql2 = "INSERT INTO $tenderdetail_tab
                                            ( 
                                                `tender_ID`, 
                                                `Title`,
                                                `PLCod`,
                                                `Description`,
    											`Consigne`,
    											`DeliveryLocation`,
                                                `Qty`,
    											`unit_ID`,
    											`RequestPriceYN`,
    											`PriceReceived`,
                                                `PriceQuoted`
                                            ) 
                                    VALUES ($vals)";
    
                                    $stmt = $this->db->prepare($sql2);
                                    $stmt->execute();
    							   }
                                }
    								
    						FOR ($remarkentry_count=1; $remarkentry_count <= $form_post_data['maxCountSub'];$remarkentry_count++) {
    
    							    $remark_date =$form_post_data['Field7_' . $remarkentry_count];
    								$remarks_description =$form_post_data['Field8_' . $remarkentry_count];
    								$remarkdate=!empty($remark_date)?date("Y-m-d", strtotime($remark_date)):'';
    								
    								if(!empty($remark_date) || !empty($remarks_description)){
    								    
                                    $vals = "'" . $data . "'," .
                                            "'" . $remarkdate . "'," .
                                            "'" . $remarks_description . "'";
                                     
                                    $sql2 = "INSERT INTO $tendersubdetail_tab
                                            ( 
                                                `tender_ID`, 
                                                `RemarkDate`,
                                                `Remarks`
                                            ) 
                                    VALUES ($vals)";
                                    $stmt = $this->db->prepare($sql2);
                                    $stmt->execute();
    						
    								}
                                }
                                $this->tpl->set('message', 'Tender form edited successfully!');   
                                header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/tender');
                                // $this->tpl->set('label', 'List');
                                // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                                } catch (Exception $exc) {
                                 //edit failed option
                                $this->tpl->set('message', 'Failed to edit, try again!');
                                $this->tpl->set('FmData', $form_post_data);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_form.php'));
                                }
    
                        break;
    
                    case 'addsubmit':
                         if (isset($crud_string)) {
                             
                            $form_post_data = $dbutil->arrFltr($_POST);
                            $opening_date=!empty($form_post_data['OpeningDate'])?date("Y-m-d", strtotime($form_post_data['OpeningDate'])):'';
                            $prebid_conf_date=!empty($form_post_data['PreBidConfDate'])?date("Y-m-d", strtotime($form_post_data['PreBidConfDate'])):'';
                            $publishing_date=!empty($form_post_data['PublishingDate'])?date("Y-m-d", strtotime($form_post_data['PublishingDate'])):'';
                            $biddone_date=!empty($form_post_data['BiddingDoneDate'])?date("Y-m-d", strtotime($form_post_data['BiddingDoneDate'])):'';
                            $closedatetime=!empty($form_post_data['ClosingDateTime'])?$form_post_data['ClosingDateTime']:'';
                            $RA_datetime=!empty($form_post_data['RADateTime'])?$form_post_data['RADateTime']:'';
                           // var_dump($_POST);
                          
                            if (isset($form_post_data['enquiry_ID'])) {
                               
                                  $val =    "'" . $form_post_data['TenderNo'] . "'," .
                                            "'" . $form_post_data['CustomerTenderNo'] . "'," .
                                            "'" . $form_post_data['enquiry_ID'] . "'," .
                                            "'" . $form_post_data['TenderSection'] . "'," .
                                            "'" . $closedatetime . "'," .
                                            "'" . $opening_date . "'," .
                                            "'" . $form_post_data['BiddingType'] . "'," .
                                            "'" . $form_post_data['ContractType'] . "'," .
                                            "'" . $form_post_data['TenderType'] . "'," .
                                            "'" . $form_post_data['EvaluationCriteria'] . "'," .
    										"'" . $form_post_data['BiddingSystem'] . "'," .
    										"'" . $form_post_data['PreBidConfRequiredYN'] . "'," .
                                            "'" . $prebid_conf_date . "'," .
                                            "'" . $form_post_data['InspectionAgency'] . "'," .
    										"'" . $publishing_date . "'," .
    										"'" . $biddone_date . "'," .
                                            "'" . $form_post_data['ProcureApproveYN'] . "'," .
    										"'" . $form_post_data['ApproveAgency'] . "'," .
											"'" . $form_post_data['TenderDocCost'] . "'," .
    										"'" . $form_post_data['RAEnabledYN'] . "'," .
    										"'" . $RA_datetime . "'," .
                                            "'" . $form_post_data['RegularOrDev'] . "'," .
    										"'" . $form_post_data['ValidityOfferDays'] . "'," .
    										"'" . $form_post_data['BidResult'] . "'," .
    										"'" . $form_post_data['TenderStatus'] . "'," .
    										"'" . $form_post_data['EMD'] . "'," .
                                            "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                            "'" .  $this->ses->get('user')['ID'] . "'";
                          
					$EnquiryID=$form_post_data['enquiry_ID'];
					if($form_post_data['enquiry_ID']){
                                  $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "tender`
                                                ( 
                                                `TenderNo`,
                                                `CustomerTenderNo`,
                                                `enquiry_ID`,
                                                `TenderSection`,
                                                `ClosingDateTime`,
                                                `OpeningDate`,
                                                `BiddingType`,
                                                `ContractType`,
                                                `TenderType`,
                                                `EvaluationCriteria`,
                                                `BiddingSystem`,
                                                `PreBidConfRequiredYN`,
    											`PreBidConfDate`,
    											`InspectionAgency`,
    											`PublishingDate`,
    											`BiddingDoneDate`,
    											`ProcureApproveYN`,
    											`ApproveAgency`,
												`TenderDocCost`,
                                                `RAEnabledYN`,
    											`RADateTime`,
    											`RegularOrDev`,
    											`ValidityOfferDays`,
    											`BidResult`,
    											`TenderStatus`,
    											`EMD`,
                                                `entity_ID`,
                                                `users_ID`
                                                ) 
                                            VALUES ( $val )";
                                      $stmt = $this->db->prepare($sql);
									  
					     $sql_update="UPDATE $enquiry_table SET Tender_Status=2 WHERE ID=$EnquiryID";
                         $stmt1 = $this->db->prepare($sql_update);
						 $stmt1->execute();
					}
                                if($stmt->execute()){
                                    
                                    $lastInsertedID = $this->db->lastInsertId();
                                    
                                    $updateCustomer = array();
                                    
                                    for($j=1;$j<=3;$j++){
                                    foreach ($_FILES['files'.$j]['name'] as $i => $name) {
                                            $Fvalue='files'.$j;
                                            $uploadedFile = $util->multi_handle_file_upload_backup($Fvalue, $lastInsertedID,$i,'tender');
                                             
                                        if ($uploadedFile) {
                                           
                                            $updateCustomer[] = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                         
                                            $filename='"' . $uploadedFile.'"'  ;
                                            $type="";
                                            
                                            if($j==1){
                                               $type="cost";
                                            }
                                             if($j==2){
                                               $type="attachment";
                                            }
                                             if($j==3){
                                               $type="specification";
                                            }
                                        
                                   
                                            $valStr= "'" .  $type . "'," .
                                                     "" . $filename . "," .
                                                     "'" . $lastInsertedID . "'";
                                                         
                                           if($filename!='' && $filename!=null){    
                                               
                                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "tender_attachments` (". " `attribute_name`, `document_path`,`tender_ID`) VALUES ( $valStr )";
                                            $stmt = $this->db->prepare($sql);
                                            $stmt->execute();
                                            
                                            }
                                           
                                        }
                                                     
                                     }
                                    }
                             
    								FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                                  
                                    $title =$form_post_data['ItemName_' . $entry_count];
                                    $plcode =$form_post_data['ItemNo_' . $entry_count];
    								$description =$form_post_data['Note_' . $entry_count];
    								$consignee =$form_post_data['Water_' . $entry_count];
    								$deliveryloc =$form_post_data['Grade_' . $entry_count];
    								$qty =$form_post_data['Quantity_' . $entry_count];
                                    $unit_id =$form_post_data['unit_' . $entry_count];
    								$requestforprice =$form_post_data['Amount_' . $entry_count];
    								$price_received =$form_post_data['Rat_' . $entry_count];
                                    $price_quoted =$form_post_data['Rate_' . $entry_count];
                                    
                                    if(!empty($title) && !empty($plcode) && !empty($qty) && !empty($unit_id)){
                                        
                                        $vals = "'" . $lastInsertedID . "'," .
                                                "'" . $title . "'," .
                                                "'" . $plcode . "'," .
        										"'" . $description . "'," .
        										"'" . $consignee . "'," .
        										"'" . $deliveryloc . "'," .
        										"'" . $qty . "'," .
                                                "'" . $unit_id . "'," .
        										"'" . $requestforprice . "'," .
                                                "'" . $price_received . "'," .
                                                "'" . $price_quoted . "'" ;
                                         
                                        $sql2 = "INSERT INTO $tenderdetail_tab
                                                ( 
                                                    `tender_ID`, 
                                                    `Title`,
                                                    `PLCod`,
                                                    `Description`,
        											`Consigne`,
        											`DeliveryLocation`,
                                                    `Qty`,
        											`unit_ID`,
        											`RequestPriceYN`,
        											`PriceReceived`,
                                                    `PriceQuoted`
                                                ) 
                                        VALUES ($vals)";
                                        $stmt = $this->db->prepare($sql2);
                                        $stmt->execute();
                                    }
                                    
                                }
    							
    						
    						FOR ($remarkentry_count=1; $remarkentry_count <= $form_post_data['maxCountSub'];$remarkentry_count++) {
    
    								$remark_date =$form_post_data['Field7_' . $remarkentry_count];
    								$remarks_description =$form_post_data['Field8_' . $remarkentry_count];
    								$remarkdate=!empty($remark_date)?date("Y-m-d", strtotime($remark_date)):'';
    								
    								if(!empty($remark_date) || !empty($remarks_description)){
    								    
    								    $vals = "'" . $lastInsertedID . "'," .
                                            "'" . $remarkdate . "'," .
                                            "'" . $remarks_description . "'" ;
                                     
                                        $sql2 = "INSERT INTO $tendersubdetail_tab
                                                ( 
                                                    `tender_ID`, 
                                                    `RemarkDate`,
                                                    `Remarks`
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
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/tender');
                           // $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_form.php'));
                         } else {
                                //edit option
                                //if submit failed to insert form
                                $this->tpl->set('message', 'Failed to submit!');
                                $this->tpl->set('FmData', $form_post_data);
                                $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_form.php'));
                         }
                        break;
                    case 'add':
                        $this->tpl->set('mode', 'add');
    	                $this->tpl->set('page_header', 'Production');
    	                $TenderNo=$dbutil->keyGeneration('tender','TND','','TenderNo');
                        $this->tpl->set('TenderNo', $TenderNo);
                        $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_form.php'));
                        break;
    
                    default:
                        /*
                         * List form
                         */
                         
                        ////////////////////start//////////////////////////////////////////////
                        
               //bUILD SQL 
                $whereString = '';
                
              $colArr = array(
                    "$tendermaster_tab.ID",
                    "$tendermaster_tab.TenderNo",
                    "$enquiry_table.EnquiryNo",
                    "$employee_table.EmpName",
                    "$customer_table.CompanyName",
                    "$customer_table.PersonName",
                    "$pdndept_table.DeptName",
                    "$tendermaster_tab.BiddingDoneDate",
                    "$tendermaster_tab.OpeningDate",
                    "$tendermaster_tab.ClosingDateTime"
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
                 $whereString ="ORDER BY $tendermaster_tab.ClosingDateTime ASC";
               }
               
            $sql = "SELECT "
                        . implode(',',$colArr)
                        . " FROM $tendermaster_tab  LEFT JOIN $enquiry_table ON  $tendermaster_tab.enquiry_ID=$enquiry_table.ID LEFT JOIN $pdndept_table ON $enquiry_table.pdndepartment_ID=$pdndept_table.ID LEFT JOIN $employee_table ON $enquiry_table.employee_ID=$employee_table.ID LEFT JOIN $customer_table ON $enquiry_table.customer_ID=$customer_table.ID "
                        . " WHERE "
                        . " $tendermaster_tab.entity_ID = $entityID"
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
             
             
                $this->tpl->set('table_columns_label_arr', array('ID','Tender No','Enquiry No','Attended By','Company Name','Contact Person','Production Department','Bidding done date','Opening date','Closing date time'));
                
                /*
                 * selectColArr for filter form
                 */
                
                $this->tpl->set('selectColArr',$colArr);
                            
                /*
                 * set pagination template
                 */
                $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                       
                //////////////////////close//////////////////////////////////////  
                         
                        include_once $this->tpl->path . '/factory/form/crud_form_tender.php';
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

public function customerOrder(){
     
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
            $employee_table = $this->crg->get('table_prefix') . 'employee';
            $pdndept_table = $this->crg->get('table_prefix') . 'pdndepartment';
            $unit_table = $this->crg->get('table_prefix') . 'unit';
            $cust_order_tab = $this->crg->get('table_prefix') . 'customerorder';
            $cust_order_detail_tab = $this->crg->get('table_prefix') . 'customerorder_detail';
			$cust_order_schedule_tab = $this->crg->get('table_prefix') . 'custorder_schedule';
			$cust_order_purchase_detail_tab = $this->crg->get('table_prefix') . 'custpurchase_orderdetail';
			$cust_order_amendment_tab = $this->crg->get('table_prefix') . 'custorder_amendment';
			$cust_order_attachment_tab = $this->crg->get('table_prefix') . 'custorder_attachment';
            $enquirymaster_tab = $this->crg->get('table_prefix') . 'enquiry';
            $customer_table = $this->crg->get('table_prefix') . 'customer';
            $state_table = $this->crg->get('table_prefix') . 'state';
            $country_table = $this->crg->get('table_prefix') . 'country';
			$tender_tab = $this->crg->get('table_prefix') . 'tender';
            $approvalprocess_tab = $this->crg->get('table_prefix') . 'approvalprocess';
            $approvaltype_tab = $this->crg->get('table_prefix') . 'approvaltype';
           	$dept_table = $this->crg->get('table_prefix') . 'department';
           	$user_table = $this->crg->get('table_prefix') . 'users';
           	$pdn_order_tab = $this->crg->get('table_prefix') . 'productionorder';
           	$product_table = $this->crg->get('table_prefix') . 'product';
            // //employee table data 
           
            // $emp_sql = "SELECT $employee_table.ID,$employee_table.EmpName FROM $employee_table,$dept_table WHERE $employee_table.department_ID=$dept_table.ID AND $dept_table.DeptName='sales'";
            // $stmt = $this->db->prepare($emp_sql);            
            // $stmt->execute();
            // $employee_data  = $stmt->fetchAll();	
            // $this->tpl->set('employee_data', $employee_data);
            
            $sql = "SELECT $user_table.ID,$user_table.user_nicename FROM $user_table,$approvaltype_tab where $user_table.ID=$approvaltype_tab.approver_ID AND $approvaltype_tab.ProcessTypeName='Customer Order'"; 
            $stmt = $this->db->prepare($sql);            
            $stmt->execute();
            $approver_data = $stmt->fetchAll();	
            $this->tpl->set('approver_data', $approver_data);
           
            
            $sql = "SELECT approver_ID FROM $approvaltype_tab where $approvaltype_tab.ProcessTypeName='Customer Order'"; 
            $stmt = $this->db->prepare($sql);            
            $stmt->execute();
            $approvaltype_data = $stmt->fetchAll();	
            $approve=$approvaltype_data;
            
            //enquiry table data
               
                $enquiry_sql = "SELECT ID,EnquiryNo FROM $enquirymaster_tab WHERE $enquirymaster_tab.Customerorder_Status=1 ORDER BY $enquirymaster_tab.ID DESC";
                $stmt = $this->db->prepare($enquiry_sql);            
                $stmt->execute();
                $enquiry_data  = $stmt->fetchAll();	
                $this->tpl->set('enquiry_data', $enquiry_data);
				
				$enquiry1_sql = "SELECT ID,EnquiryNo FROM $enquirymaster_tab WHERE $enquirymaster_tab.Customerorder_Status=2";
                $stmt = $this->db->prepare($enquiry1_sql);            
                $stmt->execute();
                $enquiry1_data  = $stmt->fetchAll();	
                $this->tpl->set('enquiry1_data', $enquiry1_data);
                
                $product_sql = "SELECT ID,ProductName FROM $product_table ";
                $stmt = $this->db->prepare($product_sql);            
                $stmt->execute();
                $product_data  = $stmt->fetchAll();	
                $this->tpl->set('product_data', $product_data);
            
            // third party inspection array
            
            $thirdpartyinspn_data = array(array("ID"=>"1","Title"=>"Yes"),array("ID"=>"2","Title"=>"No"));
            $this->tpl->set('thirdpartyinspn_data', $thirdpartyinspn_data);
            
            //unit table data
           
            $pdt_sql = "SELECT ID,UnitName FROM $unit_table";
            $stmt = $this->db->prepare($pdt_sql);            
            $stmt->execute();
            $unit_data  = $stmt->fetchAll();	
            $this->tpl->set('unit_data', $unit_data);
            
           
            $this->tpl->set('page_title', 'Customer Order');	          
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
                      // var_dump($data); 
                       
                       
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                       
                    }
					
					$sqlsel_del = "SELECT Othersfile FROM $cust_order_attachment_tab WHERE custorder_ID  = '$data'";
                    $resource_data = $dbutil->getSqlData($sqlsel_del); 
                         
                     if(!empty($resource_data)){
                            foreach($resource_data as $k=>$v){
                                unlink(substr($v['Othersfile'], 2));
                            }
                     }
                         
                     $sqlsel_del = "SELECT Fileupload FROM $cust_order_amendment_tab WHERE custorder_ID  = '$data'";
                     $ammend_resource_data = $dbutil->getSqlData($sqlsel_del); 
                         
                     if(!empty($ammend_resource_data)){
                            foreach($ammend_resource_data as $k=>$v){
                                unlink($v['Fileupload']);
                            }
                     }
                     
                     $sqlsel_del = "SELECT Fileupload FROM $cust_order_purchase_detail_tab WHERE custorder_ID  = '$data'";
                     $pod_resource_data = $dbutil->getSqlData($sqlsel_del); 
                         
                     if(!empty($pod_resource_data)){
                            foreach($pod_resource_data as $k=>$v){
                                unlink($v['Fileupload']);
                            }
                     }
                     
					  $sql_update="UPDATE $enquirymaster_tab SET Customerorder_Status=1 WHERE ID=(SELECT enquiry_ID FROM $cust_order_tab WHERE $cust_order_tab.ID= $data)";
                      $stmt1 = $this->db->prepare($sql_update);
					  $stmt1->execute();
					  
					  $sql_update1="UPDATE $enquirymaster_tab SET Productionorder_Status=1 WHERE ID=(SELECT enquiry_ID FROM $cust_order_tab WHERE $cust_order_tab.ID= $data)";
                      $stmt2 = $this->db->prepare($sql_update1);
					  $stmt2->execute();
					  
					  $sql_update1="UPDATE $enquirymaster_tab SET Productionorder1_Status=2 WHERE ID=(SELECT enquiry_ID FROM $cust_order_tab WHERE $cust_order_tab.ID= $data)";
                      $stmt3 = $this->db->prepare($sql_update1);
                      $stmt3->execute();
						 
                     $sqldetdelete="Delete $cust_order_tab,$cust_order_detail_tab,
										$cust_order_schedule_tab,$cust_order_purchase_detail_tab,
										$cust_order_amendment_tab,$cust_order_attachment_tab 
										from $cust_order_tab
                                        LEFT JOIN  $cust_order_detail_tab ON $cust_order_tab.ID=$cust_order_detail_tab.custorder_ID 
										LEFT JOIN  $cust_order_schedule_tab ON $cust_order_tab.ID=$cust_order_schedule_tab.custorder_ID 
										LEFT JOIN  $cust_order_purchase_detail_tab ON $cust_order_tab.ID=$cust_order_purchase_detail_tab.custorder_ID 
										LEFT JOIN  $cust_order_amendment_tab ON $cust_order_tab.ID=$cust_order_amendment_tab.custorder_ID 
										LEFT JOIN  $cust_order_attachment_tab ON $cust_order_tab.ID=$cust_order_attachment_tab.custorder_ID 
                                        where $cust_order_tab.ID=$data"; 
                        $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Customer Order deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/customerOrder');
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
                                
					$sqlsrr = "SELECT $cust_order_tab.* ,
                                          $enquirymaster_tab.EnquiryNo,
                                          $pdndept_table.DeptName as pdn_dept,
                                          $customer_table.PersonName,
                                          $customer_table.BillingAddress1,
                                          $customer_table.BillingAddress2,
                                          $customer_table.BillingCity,
										  $state_table.StateName as BillingState,
                                          $customer_table.BillingZip,
										  $customer_table.PermntAddress1,
                                          $customer_table.PermntAddress2,
                                          $customer_table.PermntCity,
										  pstate.StateName as PermntState,
                                          $customer_table.PermntZip,
                                          $customer_table.MobileNo, 
										  $tender_tab.TenderNo,
										  $pdn_order_tab.PdnOrderNo,
										  $pdn_order_tab.PdnOrderDate
                                    FROM  `$cust_order_tab` 
                                    LEFT JOIN `$enquirymaster_tab` ON `$cust_order_tab`.enquiry_ID=`$enquirymaster_tab`.ID 
                                    LEFT JOIN `$tender_tab` ON `$tender_tab`.enquiry_ID=`$cust_order_tab`.enquiry_ID 
                                    LEFT JOIN `$pdndept_table` ON `$enquirymaster_tab`.`pdndepartment_ID`=`$pdndept_table`.`ID` 
                                    LEFT JOIN `$customer_table` ON  `$enquirymaster_tab`.`customer_ID`=`$customer_table`.`ID` 
                                    LEFT JOIN `$state_table` ON  `$customer_table`.`BillingState_ID`=`$state_table`.`ID` 
                                    LEFT JOIN `$state_table` as pstate ON  `$customer_table`.`PermntState_ID`=pstate.`ID` 
                                    LEFT JOIN  `$pdn_order_tab` ON `$cust_order_tab`.`enquiry_ID`=`$pdn_order_tab`.`enquiry_ID` 
                                    WHERE `$cust_order_tab`.`ID` = '$data'";                    
                    $cuo_data = $dbutil->getSqlData($sqlsrr); 
					$this->tpl->set('FmData', $cuo_data); 
						
					$document= "SELECT ID,Othersfile  From $cust_order_attachment_tab where custorder_ID  = '$data' ORDER BY $cust_order_attachment_tab.ID ASC";  
					$FmDataothers = $dbutil->getSqlData($document);
					$this->tpl->set('FmDataothers', $FmDataothers);
                    
					$sqlsrr = "SELECT * FROM  `$cust_order_schedule_tab` WHERE `$cust_order_schedule_tab`.`custorder_ID` = '$data' ORDER BY $cust_order_schedule_tab.ID ASC";                    
                    $FmData_Schedule = $dbutil->getSqlData($sqlsrr);
					$this->tpl->set('ScheduleData', $FmData_Schedule);
					
					$sqlsrr = "SELECT * FROM  `$cust_order_purchase_detail_tab` WHERE `$cust_order_purchase_detail_tab`.`custorder_ID` = '$data' ORDER BY $cust_order_purchase_detail_tab.ID ASC";                    
                    $FmData_cust_purchase = $dbutil->getSqlData($sqlsrr);
					$this->tpl->set('Cust_Purchase_Data', $FmData_cust_purchase);
					
					$sqlsrr = "SELECT * FROM  `$cust_order_amendment_tab` WHERE `$cust_order_amendment_tab`.`custorder_ID` = '$data' ORDER BY $cust_order_amendment_tab.ID ASC";                    
                    $FmData_Amendment = $dbutil->getSqlData($sqlsrr);
					$this->tpl->set('Cust_Amendment_Data', $FmData_Amendment);
					
			     	$sqlsrr = "SELECT * FROM  `$cust_order_detail_tab`
                               LEFT JOIN $product_table ON $product_table.ID=$cust_order_detail_tab.Product_ID WHERE `$cust_order_detail_tab`.`custorder_ID` = '$data' ORDER BY $cust_order_detail_tab.ID ASC";                    
                    $FmData_detail = $dbutil->getSqlData($sqlsrr);
					$this->tpl->set('FmDetail_Data', $FmData_detail);
					
                    //edit option     
                    $this->tpl->set('message', 'You can view Customer Order form');
                    $this->tpl->set('page_header', 'Production');
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_order_form.php'));                    
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
                                
                     $sqlsrr = "SELECT $cust_order_tab.* ,
                                          $enquirymaster_tab.EnquiryNo,
                                          $pdndept_table.DeptName as pdn_dept,
                                          $customer_table.PersonName,
                                          $customer_table.BillingAddress1,
                                          $customer_table.BillingAddress2,
                                          $customer_table.BillingCity,
										  $state_table.StateName as BillingState,
                                          $customer_table.BillingZip,
										  $customer_table.PermntAddress1,
                                          $customer_table.PermntAddress2,
                                          $customer_table.PermntCity,
										  pstate.StateName as PermntState,
                                          $customer_table.PermntZip,
                                          $customer_table.MobileNo, 
										  $tender_tab.TenderNo,
										  $pdn_order_tab.PdnOrderNo,
										  $pdn_order_tab.PdnOrderDate
                                    FROM  `$cust_order_tab` 
                                    LEFT JOIN `$enquirymaster_tab` ON `$cust_order_tab`.enquiry_ID=`$enquirymaster_tab`.ID 
                                    LEFT JOIN `$tender_tab` ON `$tender_tab`.enquiry_ID=`$cust_order_tab`.enquiry_ID 
                                    LEFT JOIN `$pdndept_table` ON `$enquirymaster_tab`.`pdndepartment_ID`=`$pdndept_table`.`ID` 
                                    LEFT JOIN `$customer_table` ON  `$enquirymaster_tab`.`customer_ID`=`$customer_table`.`ID` 
                                    LEFT JOIN `$state_table` ON  `$customer_table`.`BillingState_ID`=`$state_table`.`ID` 
                                    LEFT JOIN `$state_table` as pstate ON  `$customer_table`.`PermntState_ID`=pstate.`ID` 
                                    LEFT JOIN  `$pdn_order_tab` ON `$cust_order_tab`.`enquiry_ID`=`$pdn_order_tab`.`enquiry_ID` 
                                    WHERE `$cust_order_tab`.`ID` = '$data'";                    
                    $cuo_data = $dbutil->getSqlData($sqlsrr); 
					$this->tpl->set('FmData', $cuo_data); 
						
					$document= "SELECT ID,Othersfile  From $cust_order_attachment_tab where custorder_ID  = '$data' ORDER BY $cust_order_attachment_tab.ID ASC";  
					$FmDataothers = $dbutil->getSqlData($document);
					$this->tpl->set('FmDataothers', $FmDataothers);
                    
					$sqlsrr = "SELECT * FROM  `$cust_order_schedule_tab` WHERE `$cust_order_schedule_tab`.`custorder_ID` = '$data' ORDER BY $cust_order_schedule_tab.ID ASC";                    
                    $FmData_Schedule = $dbutil->getSqlData($sqlsrr);
					$this->tpl->set('ScheduleData', $FmData_Schedule);
					
					$sqlsrr = "SELECT * FROM  `$cust_order_purchase_detail_tab` WHERE `$cust_order_purchase_detail_tab`.`custorder_ID` = '$data' ORDER BY $cust_order_purchase_detail_tab.ID ASC";                    
                    $FmData_cust_purchase = $dbutil->getSqlData($sqlsrr);
					$this->tpl->set('Cust_Purchase_Data', $FmData_cust_purchase);
					
					$sqlsrr = "SELECT * FROM  `$cust_order_amendment_tab` WHERE `$cust_order_amendment_tab`.`custorder_ID` = '$data' ORDER BY $cust_order_amendment_tab.ID ASC";                    
                    $FmData_Amendment = $dbutil->getSqlData($sqlsrr);
					$this->tpl->set('Cust_Amendment_Data', $FmData_Amendment);
					
			    	$sqlsrr = "SELECT * FROM  `$cust_order_detail_tab`
                               LEFT JOIN $product_table ON $product_table.ID=$cust_order_detail_tab.Product_ID WHERE `$cust_order_detail_tab`.`custorder_ID` = '$data' ORDER BY $cust_order_detail_tab.ID ASC";                    
                    $FmData_detail = $dbutil->getSqlData($sqlsrr);
					$this->tpl->set('FmDetail_Data', $FmData_detail);
					
                    
                     //edit option    
                    if( $mode='Confirm'){
                    $this->tpl->set('message', 'You can edit Customer Order Approval Form');
                    }
                    else{
                      $this->tpl->set('message', 'You can edit Customer Order Form');
                     
                    }
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_order_form.php'));                    
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
                               
                    $sqldet_del = "DELETE FROM $cust_order_detail_tab WHERE custorder_ID=$data";
                    $stmt = $this->db->prepare($sqldet_del);
                    $stmt->execute();   
					
					$sqldet_del = "DELETE FROM $cust_order_amendment_tab WHERE custorder_ID=$data";
                    $stmt = $this->db->prepare($sqldet_del);
                    $stmt->execute();

					$sqldet_del = "DELETE FROM $cust_order_purchase_detail_tab WHERE custorder_ID=$data";
                    $stmt = $this->db->prepare($sqldet_del);
                    $stmt->execute();
					
					$sqldet_del = "DELETE FROM $cust_order_schedule_tab WHERE custorder_ID=$data";
                    $stmt = $this->db->prepare($sqldet_del);
                    $stmt->execute();
                            
                            try{
                            
                            $enquiry_ID=$form_post_data['enquiry_ID'];
                            $order_date=date("Y-m-d", strtotime($form_post_data['OrderDate']));
                            $gst_no=$form_post_data['GSTNo'];
                            $gst_tax=$form_post_data['GSTTax'];
                            $igst_tax=$form_post_data['IGSTTax'];
                            $cgst_tax=$form_post_data['CGSTTax'];
							$sgst_tax=$form_post_data['SGSTTax'];
                            $igst_amount=$form_post_data['IGSTAmount'];
							$cgst_amount=$form_post_data['CGSTAmount'];
							$sgst_amount=$form_post_data['SGSTAmount'];
                            $bill_amount= $form_post_data['BillAmount'];
                            $net_amount= $form_post_data['NetAmount'];
							$note= $form_post_data['Note'];
							$drawing_path= $form_post_data['Drawing_Path'];
							$gfmat_or_roving= $form_post_data['GF_or_Roving'];
                            $resin_type= $form_post_data['ResinType'];
							$packing_instn= $form_post_data['Packing_Instn'];
							$third_party_inspn= $form_post_data['Thirdparty_Inspn'];
                            $remarks=$form_post_data['Remarks'];
                            $approved_by= $form_post_data['ApprovedBy'];
                                          
                           
                            $sql_update="Update $cust_order_tab SET enquiry_ID='$enquiry_ID',
																	OrderDate='$order_date',
																	GSTNo='$gst_no',
																	GSTTax='$gst_tax',
																	IGSTTax='$igst_tax',
																	CGSTTax='$cgst_tax',
																	SGSTTax='$sgst_tax',
																	IGSTAmount='$igst_amount',
																	CGSTAmount='$cgst_amount',
																	SGSTAmount='$sgst_amount',
																	BillAmount='$bill_amount',
																	NetAmount='$net_amount',
																	Note='$note',
																	Drawing_Path='$drawing_path',
																	GF_or_Roving='$gfmat_or_roving',
																	ResinType='$resin_type',
																	Packing_Instn='$packing_instn',
																	Thirdparty_Inspn='$third_party_inspn',
																	Remarks='$remarks',
																	ApprovedBy='$approved_by'
																	WHERE ID=$data";
                            $stmt1 = $this->db->prepare($sql_update);
                            $stmt1->execute();
							if($stmt1){
                                    $updateCustomer = array();
                                    
                                    for($j=1;$j<=1;$j++){
                                    foreach ($_FILES['files'.$j]['name'] as $i => $name) {
                                            $Fvalue='files'.$j;
                                            $uploadedFile = $util->multi_handle_file_upload_backup($Fvalue, $data,$i,'customerorder');
                                             
                                        if ($uploadedFile) {
                                           
                                            $updateCustomer[] = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                         
                                            $filename='"' . $uploadedFile.'"'  ;
                                            
                                   
                                            $valStr= "'" .  $data . "'," .
                                                     "" . $filename . "" ;
                                                         
                                           if($filename!='' && $filename!=null){    
                                               
                                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "custorder_attachment` (". " `custorder_ID`, `Othersfile`) VALUES ( $valStr )";
                                            $stmt = $this->db->prepare($sql);
                                            $stmt->execute();
                                            
                                            }
                                           
                                        }
                                                     
                                     }
                                    }
									
						
                        FOR ($entry_count = 1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                                
								$productno =$form_post_data['ItemNo_' . $entry_count];
                                $Product_ID =$form_post_data['ItemName_' . $entry_count];
								$productdescp=$form_post_data['Note_' . $entry_count];
								$unit_id =$form_post_data['unit_' . $entry_count];
                                $quantity =$form_post_data['Qty_' . $entry_count];
                                $rate =$form_post_data['Emp_' . $entry_count];
								$amount =$form_post_data['Amount_' . $entry_count];
								
                            if(!empty($productno) && !empty($Product_ID) && !empty($unit_id) && !empty($quantity)  && !empty($rate)){  
                                $vals = "'" . $data . "'," .
										"'" . $productno . "'," .
                                        "'" . $Product_ID . "'," .
										"'" . $productdescp . "'," .
										"'" . $unit_id . "'," .
                                        "'" . $quantity . "'," .
                                        "'" . $rate . "'," .
                                        "'" . $amount . "'" ;
                                        
                                $sql2 = "INSERT INTO $cust_order_detail_tab
                                        ( 
                                            `custorder_ID`, 
                                            `ProductNo`,
                                            `Product_ID`,
                                            `PdtDescription`,
											`unit_ID`,
											`Quantity`,
                                            `Price`,
                                            `Amount`
                                        ) 
                                VALUES ($vals)";

                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                           
                            }
						
                        FOR ($entry_count1=1; $entry_count1 <= $form_post_data['maxCountSub'];$entry_count1++) {

                                $quantity ='Field4_' . $entry_count1;
                                $start_date ='Field5_' . $entry_count1;
								$end_date ='Field6_' . $entry_count1;

                                $vals = "'" . $data . "'," .
										"'" . $form_post_data[$quantity] . "'," .
                                        "'" . date("Y-m-d", strtotime($form_post_data[$start_date])) . "'," .
										"'" . date("Y-m-d", strtotime($form_post_data[$end_date])) . "'" ;
										
                            if(!empty($form_post_data[$quantity]) || !empty($form_post_data[$start_date]) || !empty($form_post_data[$end_date])){  
                                
                                $sql2 = "INSERT INTO $cust_order_schedule_tab
                                        ( 
                                            `custorder_ID`, 
                                            `Quantity`,
                                            `StartDate`,
                                            `EndDate`
                                        ) 
                                VALUES ($vals)";

                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                           
                            }
					
                        FOR ($entry_count2=1; $entry_count2 <= $form_post_data['maxCountSub_1']; $entry_count2++) {
                                
								$purchase_date ='field1_' . $entry_count2;
                                $purchase_order_no ='field2_' . $entry_count2;
								$purchase_file='field3_' . $entry_count2;
								
								if(!empty($_FILES[$purchase_file]['name'])){
                                         $uploadedfile = $util->custom_file_upload_specific($purchase_file, $data,'customerorder');
                                    }else{
                                         $uploadedfile ='';
                                }
                                $vals = "'" . $data . "'," .
										"'" . date("Y-m-d", strtotime($form_post_data[$purchase_date])) . "'," .
										"'" . $form_post_data[$purchase_order_no] . "'," .
                                        "'" . $uploadedfile . "'" ;
										
                            if(!empty($form_post_data[$purchase_date]) || !empty($form_post_data[$purchase_order_no])){
    							
                                $sql2 = "INSERT INTO $cust_order_purchase_detail_tab
                                        ( 
                                            `custorder_ID`, 
                                            `PurchaseDate`,
                                            `PurchaseorderNo`,
                                            `Fileupload`
                                        ) 
                                VALUES ($vals)";

                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                            
                            }

                        FOR ($entry_count3=1; $entry_count3 <= $form_post_data['maxCountSub_2'];$entry_count3++) {
                                
								$amend_date ='field7_' . $entry_count3;
                                $exsist_info ='field8_' . $entry_count3;
								$new_info='field9_' . $entry_count3;
								$amend_no='field10_' . $entry_count3;
								$amend_file='field11_' . $entry_count3;
								
								if(!empty($_FILES[$amend_file]['name'])){
                                         $uploadedfile = $util->custom_file_upload_specific($amend_file, $data,'customerorder');
                                    }else{
                                         $uploadedfile ='';
                                }
                                $vals = "'" . $data . "'," .
										"'" . date("Y-m-d", strtotime($form_post_data[$amend_date])) . "'," .
										"'" . $form_post_data[$exsist_info] . "'," .
										"'" . $form_post_data[$new_info] . "'," .
										"'" . $form_post_data[$amend_no] . "'," .
                                        "'" . $uploadedfile . "'" ;
										
                            if(!empty($form_post_data[$amend_date]) || !empty($form_post_data[$exsist_info]) || !empty($form_post_data[$new_info]) || !empty($form_post_data[$amend_no])){
    						
    							
                                $sql2 = "INSERT INTO $cust_order_amendment_tab
                                        ( 
                                            `custorder_ID`, 
                                            `AmendDate`,
                                            `Exsist_Info`,
											`New_Info`,
											`Amendment_No`,
                                            `Fileupload`
                                        ) 
                                VALUES ($vals)";

                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }

                            }
                                }

                            $this->tpl->set('message', 'Customer Order form edited successfully!');   
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/customerOrder');
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_order_form.php'));
                            }

                    break;
                case 'confirm':
                    if (isset($crud_string)) {
                            $form_post_data = $dbutil->arrFltr($_POST);
                                               
                            
                            $data=$form_post_data['ycs_ID'];
                            $approved_by= $form_post_data['ApprovedBy'];
                           
                            $sql_update="Update $approvalprocess_tab set ApprovalStatus=1 WHERE process_ID=$data and ProcessType='Customer Order'";
                            $stmt1 = $this->db->prepare($sql_update);
                            $stmt1->execute();
                            
                            $sql_update="Update $cust_order_tab set Status=1,ApprovedBy='$approved_by' WHERE ID=$data";
                            $stmt = $this->db->prepare($sql_update);
                            $stmt->execute();
                            
                            
                            $this->tpl->set('message', 'Customer Order form Confirmed successfully!');   
                            // $this->tpl->set('label', 'List');
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/customerOrder');
                            $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            
                    }
                break;
                case 'addsubmit':
                     if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                    
                        //var_dump($_POST); die;
                       
                        if (isset($form_post_data['enquiry_ID'])) {
                           
                                $val =  "'" . $form_post_data['CustOrderNo'] . "'," .
										"'" . $form_post_data['enquiry_ID'] . "'," .
										"'" . date("Y-m-d", strtotime($form_post_data['OrderDate'])) . "'," .
                                        "'" . $form_post_data['GSTNo'] . "'," .
                                        "'" . $form_post_data['GSTTax'] . "'," .
                                        "'" . $form_post_data['IGSTTax'] . "'," .
                                        "'" . $form_post_data['CGSTTax'] . "'," .
                                        "'" . $form_post_data['SGSTTax'] . "'," .
                                        "'" . $form_post_data['IGSTAmount'] . "'," .
                                        "'" . $form_post_data['CGSTAmount'] . "'," .
                                        "'" . $form_post_data['SGSTAmount'] . "'," .
                                        "'" . $form_post_data['BillAmount'] . "'," .
                                        "'" . $form_post_data['NetAmount'] . "'," .
                                        "'" . $form_post_data['Note'] . "'," .
                                        "'" . $form_post_data['Drawing_Path'] . "'," .
                                        "'" . $form_post_data['GF_or_Roving'] . "'," .
										"'" . $form_post_data['ResinType'] . "'," .
										"'" . $form_post_data['Packing_Instn'] . "'," .
										"'" . $form_post_data['Thirdparty_Inspn'] . "'," .
										"'" . $form_post_data['Remarks'] . "'," .
										"'" . $form_post_data['ApprovedBy'] . "'," .
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";

                               $Enquiry_ID= $form_post_data['enquiry_ID'];
								 if($form_post_data['enquiry_ID']){
                                    $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "customerorder`
                                            ( 
											`CustOrderNo`,
                                            `enquiry_ID`,
                                            `OrderDate`,
                                            `GSTNo`,
                                            `GSTTax`,
                                            `IGSTTax`,
                                            `CGSTTax`,
                                            `SGSTTax`,
                                            `IGSTAmount`,
                                            `CGSTAmount`,
											`SGSTAmount`,
                                            `BillAmount`,
                                            `NetAmount`,
                                            `Note`,
                                            `Drawing_Path`,
                                            `GF_or_Roving`,
											`ResinType`,
											`Packing_Instn`,
											`Thirdparty_Inspn`,
											`Remarks`,
											`ApprovedBy`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                        VALUES ( $val )";
                                    $stmt = $this->db->prepare($sql);
                                
						 $sql_update="UPDATE $enquirymaster_tab SET Customerorder_Status=2 WHERE ID=$Enquiry_ID";
                         $stmt1 = $this->db->prepare($sql_update);
						 $stmt1->execute();
						 
						 $sql_update1="UPDATE $enquirymaster_tab SET Productionorder_Status=2 WHERE ID=$Enquiry_ID";
                         $stmt2 = $this->db->prepare($sql_update1);
						 $stmt2->execute();
						 
				    	 $sql_update1="UPDATE $enquirymaster_tab SET Productionorder1_Status=1 WHERE ID=$Enquiry_ID";
                         $stmt3 = $this->db->prepare($sql_update1);
						 $stmt3->execute();
					}
                                  
                        if ($stmt->execute()) { 
                        $lastInsertedID = $this->db->lastInsertId();
                        $dbutil->ApprovalProcess('Customer Order',$approve[0][0],$lastInsertedID);
                        $updateCustomer = array();
                                    
                                    for($j=1;$j<=1;$j++){
                                    foreach ($_FILES['files'.$j]['name'] as $i => $name) {
                                            $Fvalue='files'.$j;
                                            $uploadedFile = $util->multi_handle_file_upload_backup($Fvalue, $lastInsertedID,$i,'custorder_attachment');
                                             
                                        if ($uploadedFile) {
                                           
                                            $updateCustomer[] = '`' . $Fvalue . '` =\'' . $uploadedFile . '\'';
                                         
                                            $filename='"' . $uploadedFile.'"'  ;
                                            
                                   
                                            $valStr= "'" . $lastInsertedID . "'," .
                                                     "" . $filename . "" ;
                                                         
                                           if($filename!='' && $filename!=null){    
                                               
                                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "custorder_attachment` (". " `custorder_ID`, `Othersfile`) VALUES ( $valStr )";
                                            $stmt = $this->db->prepare($sql);
                                            $stmt->execute();
                                            
                                            }
                                           
                                        }
                                                     
                                     }
                                    }
					
                        FOR ($entry_count=1; $entry_count <= $form_post_data['maxCount']; $entry_count++) {
                              
								$productno =$form_post_data['ItemNo_' . $entry_count];
                                $Product_ID =$form_post_data['ItemName_' . $entry_count];
								$productdescp=$form_post_data['Note_' . $entry_count];
								$unit_id =$form_post_data['unit_' . $entry_count];
                                $quantity =$form_post_data['Qty_' . $entry_count];
                                $rate =$form_post_data['Emp_' . $entry_count];
								$amount =$form_post_data['Amount_' . $entry_count];
                            
                            if(!empty($productno) && !empty($Product_ID) && !empty($unit_id) && !empty($quantity)  && !empty($rate)){  
                                
                                $vals = "'" . $lastInsertedID . "'," .
										"'" . $productno . "'," .
                                        "'" . $Product_ID . "'," .
										"'" . $productdescp . "'," .
										"'" . $unit_id . "'," .
                                        "'" . $quantity . "'," .
                                        "'" . $rate . "'," .
                                        "'" . $amount . "'" ;
                                 
                                $sql2 = "INSERT INTO $cust_order_detail_tab
                                        ( 
                                            `custorder_ID`, 
                                            `ProductNo`,
                                            `Product_ID`,
                                            `PdtDescription`,
											`unit_ID`,
											`Quantity`,
                                            `Price`,
                                            `Amount`
                                        ) 
                                VALUES ($vals)";

                                 // this need to be changed in to transaction type
                                
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
                            }
						
                        FOR ($entry_count1=1; $entry_count1 <= $form_post_data['maxCountSub'];$entry_count1++) {

                                $quantity ='Field4_' . $entry_count1;
                                $start_date ='Field5_' . $entry_count1;
								$end_date ='Field6_' . $entry_count1;

                                $vals = "'" . $lastInsertedID . "'," .
										"'" . $form_post_data[$quantity] . "'," .
                                        "'" . date("Y-m-d", strtotime($form_post_data[$start_date])) . "'," .
										"'" . date("Y-m-d", strtotime($form_post_data[$end_date])) . "'" ;
										
                                if(!empty($form_post_data[$quantity]) || !empty($form_post_data[$start_date]) || !empty($form_post_data[$end_date])){
                                    
                                    $sql2 = "INSERT INTO $cust_order_schedule_tab
                                        ( 
                                            `custorder_ID`, 
                                            `Quantity`,
                                            `StartDate`,
                                            `EndDate`
                                        ) 
                                        VALUES ($vals)";
                                        $stmt = $this->db->prepare($sql2);
                                        $stmt->execute();
                                }        
                            
                            }

                        FOR ($entry_count2=1; $entry_count2 <= $form_post_data['maxCountSub_1']; $entry_count2++) {
                                
								$purchase_date ='field1_' . $entry_count2;
                                $purchase_order_no ='field2_' . $entry_count2;
								$purchase_file='field3_' . $entry_count2;
								
								if(!empty($_FILES[$purchase_file]['name'])){
                                         $uploadedfile = $util->custom_file_upload_specific($purchase_file, $data,'customerorder');
                                }else{
                                         $uploadedfile ='';
                                }
                                
                                $vals = "'" . $lastInsertedID . "'," .
										"'" . date("Y-m-d", strtotime($form_post_data[$purchase_date])) . "'," .
										"'" . $form_post_data[$purchase_order_no] . "'," .
                                        "'" . $uploadedfile . "'" ;
										
                            if(!empty($form_post_data[$purchase_date]) || !empty($form_post_data[$purchase_order_no])){
                                
                                 $sql2 = "INSERT INTO $cust_order_purchase_detail_tab
                                        ( 
                                            `custorder_ID`, 
                                            `PurchaseDate`,
                                            `PurchaseorderNo`,
                                            `Fileupload`
                                        ) 
                                VALUES ($vals)";
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
                            }
    							
                            }
						
                        FOR ($entry_count3=1; $entry_count3 <= $form_post_data['maxCountSub_2'];$entry_count3++) {
                                
								$amend_date ='field7_' . $entry_count3;
                                $exsist_info ='field8_' . $entry_count3;
								$new_info='field9_' . $entry_count3;
								$amend_no='field10_' . $entry_count3;
								$amend_file='field11_' . $entry_count3;
								
								if(!empty($_FILES[$amend_file]['name'])){
                                         $uploadedfile = $util->custom_file_upload_specific($amend_file, $data,'customerorder');
                                    }else{
                                         $uploadedfile ='';
                                }
                                $vals = "'" . $lastInsertedID . "'," .
										"'" . date("Y-m-d", strtotime($form_post_data[$amend_date])) . "'," .
										"'" . $form_post_data[$exsist_info] . "'," .
										"'" . $form_post_data[$new_info] . "'," .
										"'" . $form_post_data[$amend_no] . "'," .
                                        "'" . $uploadedfile . "'" ;
										
                            if(!empty($form_post_data[$amend_date]) || !empty($form_post_data[$exsist_info]) || !empty($form_post_data[$new_info]) || !empty($form_post_data[$amend_no])){
    							
                                $sql2 = "INSERT INTO $cust_order_amendment_tab
                                        ( 
                                            `custorder_ID`, 
                                            `AmendDate`,
                                            `Exsist_Info`,
											`New_Info`,
											`Amendment_No`,
                                            `Fileupload`
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
                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/customerOrder');
                       // $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_order_form.php'));
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_order_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Production');
	                $CustOrderNo=$dbutil->keyGeneration('customerorder','CON','','CustOrderNo');
                    $this->tpl->set('CustOrderNo', $CustOrderNo);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customer_order_form.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
                "$cust_order_tab.ID",
                "$enquirymaster_tab.EnquiryNo",
                "$cust_order_tab.CustOrderNo",
                "$pdn_order_tab.PdnOrderNo",
                "$user_table.user_nicename",
                "$customer_table.PersonName",
                "$pdndept_table.DeptName"
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
             $whereString ="ORDER BY $cust_order_tab.ID DESC";
           }
           
         $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $cust_order_tab 
                             LEFT JOIN $cust_order_detail_tab ON $cust_order_tab.ID=$cust_order_detail_tab.custorder_ID 
                             LEFT JOIN $enquirymaster_tab ON $cust_order_tab.enquiry_ID=$enquirymaster_tab.ID 
                             LEFT JOIN  `$pdn_order_tab` ON `$cust_order_tab`.`enquiry_ID`=`$pdn_order_tab`.`enquiry_ID` 
                             LEFT JOIN $pdndept_table ON $enquirymaster_tab.pdndepartment_ID=$pdndept_table.ID 
                             LEFT JOIN $user_table ON $cust_order_tab.ApprovedBy=$user_table.ID 
                             LEFT JOIN $customer_table ON $enquirymaster_tab.customer_ID=$customer_table.ID "
                    . " WHERE "
                    . " $cust_order_tab.entity_ID = $entityID"
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
         
         
           $this->tpl->set('table_columns_label_arr', array('ID','Enquiry No','Customer Order No','Production Order No','Prepared By','Contact Person','Division'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_form_customer_order.php';
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


public function productionOrder(){
     
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
           
            $sql = "SELECT $user_table.ID,$user_table.user_nicename FROM $user_table,$approvaltype_tab where $user_table.ID=$approvaltype_tab.approver_ID"; 
            // $stmt = $this->db->prepare($sql);            
            // $stmt->execute();
            // $approver_data= $stmt->fetchAll();	
            $approver_data = $dbutil->getSqlData($sql); 
            $this->tpl->set('approver_data', $approver_data);
            
            //enquiry table data
               
                $enquiry_sql = "SELECT $enquirymaster_tab.ID,$enquirymaster_tab.EnquiryNo FROM $enquirymaster_tab 
                                LEFT JOIN $cust_order_tab ON $cust_order_tab.enquiry_ID=$enquirymaster_tab.ID
                                WHERE $cust_order_tab.Status=1 AND $enquirymaster_tab.Productionorder_Status=2 AND $enquirymaster_tab.Productionorder1_Status=1";
                $stmt = $this->db->prepare($enquiry_sql);            
                $stmt->execute();
                $enquiry_data  = $stmt->fetchAll();	
                $this->tpl->set('enquiry_data', $enquiry_data);
                
                $enquiry_sql = "SELECT ID,EnquiryNo FROM $enquirymaster_tab WHERE $enquirymaster_tab.Productionorder_Status=1";
                $stmt = $this->db->prepare($enquiry_sql);            
                $stmt->execute();
                $enquiry1_data  = $stmt->fetchAll();	
                $this->tpl->set('enquiry1_data', $enquiry1_data);
        
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
                 case 'delete':                    
                     
                     $data = trim($_POST['ycs_ID']);
                      
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                    }

                    $sql_update1="UPDATE $enquirymaster_tab SET Productionorder_Status=2 WHERE ID=(SELECT enquiry_ID FROM $pdn_order_tab WHERE $pdn_order_tab.ID= $data)";
                    $stmt2 = $this->db->prepare($sql_update1);
				    $stmt2->execute();
				    
                     $sqldetdelete="DELETE FROM $pdn_order_tab WHERE $pdn_order_tab.ID=$data"; 
                     $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Production Order deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/productionOrder');
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
                
                case 'edit':                    
                    $data = trim($_POST['ycs_ID']);
                    
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
                    $this->tpl->set('message', 'You can edit Production Order Form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/production_order_form.php'));                    
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
                            
                            $enquiry_ID=$form_post_data['enquiry_ID'];
                            $pdn_date=date("Y-m-d", strtotime($form_post_data['PdnOrderDate'])) ;
							$layup_sequence= $form_post_data['Layup_Sequence'];
							$matrix_detail= $form_post_data['Matrix_Detail'];
                            
                            $sql_update="Update $pdn_order_tab SET  enquiry_ID='$enquiry_ID',PdnOrderDate='$pdn_date',Layup_Sequence='$layup_sequence',Matrix_Detail='$matrix_detail' WHERE ID=$data";
                            $stmt1 = $this->db->prepare($sql_update);
                            $stmt1->execute();
							if($stmt1){
                                $this->tpl->set('message', 'Customer Order form edited successfully!');   
                                header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/productionOrder'); 
                            }
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/production_order_form.php'));
                            }

                    break;
                    
                case 'addsubmit':
                     if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                    
                        //var_dump($_POST); die;
                       
                        if (isset($form_post_data['enquiry_ID'])) {
                           
                                $val =  "'" . $form_post_data['PdnOrderNo'] . "'," .
                                        "'" . $form_post_data['enquiry_ID'] . "'," .
                                        "'" . date("Y-m-d", strtotime($form_post_data['PdnOrderDate'])) . "'," .
										"'" . $form_post_data['Layup_Sequence'] . "'," .
										"'" . $form_post_data['Matrix_Detail'] . "'," .
                                        "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                        "'" .  $this->ses->get('user')['ID'] . "'";

                       $Enquiry_ID= $form_post_data['enquiry_ID'];
                       if($form_post_data['enquiry_ID']){
                                    $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "productionorder`
                                            ( 
                                            `PdnOrderNo`,
                                            `enquiry_ID`,
                                            `PdnOrderDate`,
                                            `Layup_Sequence`,
                                            `Matrix_Detail`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                        VALUES ( $val )";
                                    $stmt = $this->db->prepare($sql);
                                    
                         $sql_update1="UPDATE $enquirymaster_tab SET Productionorder_Status=1 WHERE ID=$Enquiry_ID";
                         $stmt2 = $this->db->prepare($sql_update1);
						 $stmt2->execute();
					}
                                
                                  
                        if ($stmt->execute()) { 
                            $this->tpl->set('message', '- Success -');
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/productionOrder');
                        }
                        }
                        $this->tpl->set('mode', 'add');
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/production_order_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Production');
	                $PdnOrderNo=$dbutil->keyGeneration('productionorder','PDN','','PdnOrderNo');
                    $this->tpl->set('PdnOrderNo', $PdnOrderNo);
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
                "$enquirymaster_tab.EnquiryNo",
                "$pdn_order_tab.PdnOrderNo",
                "$cust_order_tab.CustOrderNo",
                "$pdndept_table.DeptName"
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
                    . " FROM $pdn_order_tab 
                             LEFT JOIN $enquirymaster_tab ON $pdn_order_tab.enquiry_ID=$enquirymaster_tab.ID 
                             LEFT JOIN $pdndept_table ON $enquirymaster_tab.pdndepartment_ID=$pdndept_table.ID 
                             LEFT JOIN $cust_order_tab ON $enquirymaster_tab.ID=$cust_order_tab.enquiry_ID"
                    . " WHERE "
                    . " $pdn_order_tab.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Enquiry No','Production Order No','Customer Order No','Division'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
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
    
////////////////////////////////////////////////////

public function enquiryQuotation(){
    
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
			 
			////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////access condition applied//////////////////////////
			////////////////////////////////////////////////////////////////////////////////    
						
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $enquiry_quotation_table = $this->crg->get('table_prefix') . 'enquiry_quotation';
			$enquiryquotation_detail_table = $this->crg->get('table_prefix') . 'enquiryquotation_detail';
			$enquiry_table = $this->crg->get('table_prefix') . 'enquiry';
			$customer_table = $this->crg->get('table_prefix') . 'customer';
			$unit_table = $this->crg->get('table_prefix') . 'unit';											  
            
            $enquiry_sql = "SELECT ID,EnquiryNo FROM $enquiry_table WHERE $enquiry_table.EnquiryQuotation_Status=1";
            $stmt = $this->db->prepare($enquiry_sql);            
            $stmt->execute();
            $enquiry_data  = $stmt->fetchAll();	
            $this->tpl->set('enquiry_data', $enquiry_data);
			
			$enquiry_sql = "SELECT ID,EnquiryNo FROM $enquiry_table WHERE $enquiry_table.EnquiryQuotation_Status=2";
            $stmt = $this->db->prepare($enquiry_sql);            
            $stmt->execute();
            $enquiry1_data  = $stmt->fetchAll();	
            $this->tpl->set('enquiry1_data', $enquiry1_data);
			
			$pdt_sql = "SELECT ID,UnitName FROM $unit_table";
            $stmt = $this->db->prepare($pdt_sql);            
            $stmt->execute();
            $unit_data  = $stmt->fetchAll();	
            $this->tpl->set('unit_data', $unit_data);										

            $this->tpl->set('page_title', 'Enquiry Quotation');	          
            $this->tpl->set('page_header', 'Enquiry Quotation');
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
                     $sql_update="UPDATE $enquiry_table SET EnquiryQuotation_Status=1 WHERE ID=(SELECT EnquiryID FROM $enquiry_quotation_table WHERE $enquiry_quotation_table.ID= $data)";
                            $stmt1 = $this->db->prepare($sql_update);
					 $stmt1->execute();
					 
                      $sqldetdelete="Delete $enquiryquotation_detail_table,$enquiry_quotation_table from $enquiry_quotation_table
                                     LEFT JOIN  $enquiryquotation_detail_table ON $enquiry_quotation_table.ID=$enquiryquotation_detail_table.EnquiryQuotation_ID 
                                     where $enquiryquotation_detail_table.EnquiryQuotation_ID=$data"; 
                        $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Enquiry Quotation form deleted successfully');
																													  
                        //$this->tpl->set('label', 'List');
                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiryQuotation');
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
                          
                      $sqlsrr = "SELECT *,$enquiry_table.ID,$customer_table.CompanyName,$customer_table.PersonName,$customer_table.PermntAddress1,$customer_table.MobileNo,$customer_table.PermntCity,$customer_table.PermntZip,$enquiryquotation_detail_table.ItemDescription,$enquiryquotation_detail_table.Unit,
		                       $enquiryquotation_detail_table.UnitPrice,$enquiryquotation_detail_table.Amount
		                       FROM $enquiry_table 
				               LEFT JOIN $customer_table ON $customer_table.ID=$enquiry_table.customer_ID 
							   LEFT JOIN $enquiry_quotation_table ON $enquiry_quotation_table.EnquiryID=$enquiry_table.ID
						       LEFT JOIN $enquiryquotation_detail_table ON $enquiryquotation_detail_table.EnquiryQuotation_ID=$enquiry_quotation_table.ID
                               WHERE  $enquiry_quotation_table.ID= $data";
                               $enquiry_quotation_data = $dbutil->getSqlData($sqlsrr);
                   
                    //edit option     
                    $this->tpl->set('message', 'You can view Enquiry Quotation form');
                    $this->tpl->set('page_header', 'Enquiry Quotation');
                    $this->tpl->set('FmData', $enquiry_quotation_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_quotation_design_form.php'));                    
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
           			  
					$sqlsrr = "SELECT *,$enquiry_table.ID,$customer_table.CompanyName,$customer_table.PersonName,$customer_table.PermntAddress1,$customer_table.MobileNo,$customer_table.PermntCity,$customer_table.PermntZip,$enquiryquotation_detail_table.ItemDescription,$enquiryquotation_detail_table.Unit,
		                       $enquiryquotation_detail_table.UnitPrice,$enquiryquotation_detail_table.Amount
		                       FROM $enquiry_table 
				               LEFT JOIN $customer_table ON $customer_table.ID=$enquiry_table.customer_ID 
							   LEFT JOIN $enquiry_quotation_table ON $enquiry_quotation_table.EnquiryID=$enquiry_table.ID
						       LEFT JOIN $enquiryquotation_detail_table ON $enquiryquotation_detail_table.EnquiryQuotation_ID=$enquiry_quotation_table.ID
                               WHERE  $enquiry_quotation_table.ID= $data";
                               $enquiry_quotation_data = $dbutil->getSqlData($sqlsrr);
                    
                    //edit option 

					//print_r($_POST); 
                    $this->tpl->set('message', 'You can edit Enquiry Quotation form');
                    $this->tpl->set('page_header', 'Enquiry Quotation');
                    $this->tpl->set('FmData', $enquiry_quotation_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_quotation_design_form.php'));                    
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
                        
                        // start the PDF				
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
				// FIRSt HALF END 		
				
                          $EnquiryID= $form_post_data['EnquiryID'];
                          $TermsAndConditions= $form_post_data['TermsAndConditions'];
						  $Validity= $form_post_data['Validity'];
						  $Taxes= $form_post_data['Taxes'];
						  $PaymentTerms= $form_post_data['PaymentTerms'];
						  $Freight= $form_post_data['Freight'];
						  $Inspection= $form_post_data['Inspection'];
						  $SpecialCondition= $form_post_data['SpecialCondition'];
						  
						 $sql_update="UPDATE $enquiry_quotation_table set EnquiryID='$EnquiryID',TermsAndConditions='$TermsAndConditions',Validity='$Validity',Taxes='$Taxes',PaymentTerms='$PaymentTerms',Freight='$Freight',Inspection='$Inspection',SpecialCondition='$SpecialCondition' WHERE ID=$data";
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
                            
					        //echo '<pre>';
					        //print_r($_POST);die;
					        //$lastInsertedID = $this->db->lastInsertId();
							
                           $maxCount = $form_post_data['maxCount'];			   
                      
						 $sql3 = "DELETE FROM $enquiryquotation_detail_table WHERE EnquiryQuotation_ID=$data";
					     $stmt3 = $this->db->prepare($sql3);
                         //$is_delete = $stmt3->execute();
						 // var_dump($is_delete);die;
					   if($stmt3->execute()){
						   
                        FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                
                                $ItemDescription = $form_post_data['Field2_'.$entry_count];
							    $ItemDescription1 = $form_post_data['ItemDescription_'.$entry_count];
                                $Unit = $form_post_data['Unit_'.$entry_count];
                                $unit_id = $form_post_data['unitmeasure_'.$entry_count];
                                $UnitPrice = $form_post_data['UnitPrice_'.$entry_count];
                                $Amount = $form_post_data['Field6_'.$entry_count];
                              
							  if(!empty($Unit) && !empty($unit_id)){
                                $vals = "'" . $data . "'," .
        								"'" . $ItemDescription . "'," .
										"'" . $ItemDescription1 . "'," .
                                        "'" . $Unit . "'," .
                                        "'" . $unit_id . "'," .
                                        "'" . $UnitPrice . "'," .
                                        "'" . $Amount . "'" ;
  
                                 $sql2 = "INSERT INTO $enquiryquotation_detail_table
                                        ( 
										    `EnquiryQuotation_ID`, 
                                            `ItemDescription`, 
											`ItemDescription1`, 
                                            `Unit`,
                                            `unit_ID`,
                                            `UnitPrice`,
                                            `Amount`
                                        ) 
                                VALUES ($vals)";
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
							  }
								
                            //increment here
                            
                            }
					   }
					       
					        // pdf start 
							
							$enquiryquotation_detail_table = "SELECT $enquiryquotation_detail_table.ID,$enquiryquotation_detail_table.ItemDescription,$enquiryquotation_detail_table.unit_ID,$enquiryquotation_detail_table.Unit,$enquiryquotation_detail_table.UnitPrice,$enquiryquotation_detail_table.Amount,$enquiry_quotation_table .ID FROM $enquiryquotation_detail_table,$enquiry_quotation_table where $enquiry_quotation_table .ID=$enquiryquotation_detail_table.EnquiryQuotation_ID AND $enquiryquotation_detail_table.EnquiryQuotation_ID = $data ";
                            $stmt =$this->db->prepare( $enquiryquotation_detail_table);            
                            $stmt->execute();		
                            $enquiryquotation_detail_table_data = $stmt->fetchAll(2);
                            // var_dump($delDcproduct); 
                            $count = 1;
                            foreach($enquiryquotation_detail_table_data as $k=>$v){
                                $troww .=  '<tr>
                                <td style="width:50" align="left">'.$count.'</td>
                                <td style="width:251" align="left">'. $v['ItemDescription'].'</td>
                                <td style="width:67" align="center">'. $v['unit_ID'].'  </td>
                                <td style="width:99" align="right">'.$v["Unit"].'</td>
                                <td style="width:99" align="right">'. $v["UnitPrice"].'</td>
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
								
								
							$PersonName = $form_post_data['PersonName'];	
							
							 $customer_tab = "SELECT $customer_table.ID ,$customer_table.PersonName,$customer_table.GSTNo,$customer_table.PermntAddress1,$customer_table.PermntAddress2,$customer_table.PermntCity,$customer_table.PermntState_ID,$customer_table.PermntZip,$customer_table.MobileNo  from $customer_table where $customer_table.PersonName = '$PersonName' ";	
                            $stmt =$this->db->prepare($customer_tab);            
                            $stmt->execute();	
                            $customer_tab_fetch = $stmt->fetchAll(2);		
							
							$PersonName = $customer_tab_fetch[0]['PersonName'];		
							$GSTNo = $customer_tab_fetch[0]['GSTNo'];	
							$PermntAddress1 = $customer_tab_fetch[0]['PermntAddress1'];
							$PermntAddress2 = $customer_tab_fetch[0]['PermntAddress2'];
							$PermntCity = $customer_tab_fetch[0]['PermntCity'];
							$PermntState_ID = $customer_tab_fetch[0]['PermntState_ID'];
							$PermntZip = $customer_tab_fetch[0]['PermntZip'];
							$MobileNo = $customer_tab_fetch[0]['MobileNo'];			




								// $cgstPre=strval($form_post_data['CGST'].'%');
                            // $sgstPre=strval($form_post_data['SGST'].'%');
                            // $igstPre=strval($form_post_data['IGST'].'%');
                            // $cgstAmount = strval((float) $form_post_data['CGSTtotal']);
                            // $sgstAmount = strval((float) $form_post_data['SGSTtotal']);
                            // $igstAmount = strval((float) $form_post_data['IGSTtotal']);
                            // $netAmount = strval((float) $form_post_data['Balance']);
							// $netamountcopy=$netAmount;
                            // $BillAmount = strval((float) $form_post_data['Total']);
							$Validity = $form_post_data['Validity'];
							// $Taxes = strval((float)) $form_post_data['Taxes'];
							$Taxes=strval($form_post_data['Taxes'].'%');
							$PaymentTerms = $form_post_data['PaymentTerms'];
							$Freight = $form_post_data['Freight'];
							$Inspection = $form_post_data['Inspection'];
							$SpecialCondition = $form_post_data['SpecialCondition'];
							// $City=$form_post_data['City'];
							// $StateID=$form_post_data['StateID'];
							// $Pincode=$form_post_data['Pincode'];
							
							$ProformaNumber=$dbutil->keyGeneration('enquiry_quotation','ENQ','','EnquiryQuotation_ID');
						
    $decimal = round($netamountcopy - ($no = floor($netamountcopy)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');
    if($digits_length >= 10){
        echo "Sorry this does not support more than 99 crores";
    }else {
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $netamountcopy = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($netamountcopy) {
            $plural = (($counter = count($str)) && $netamountcopy > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($netamountcopy < 21) ? $words[$netamountcopy].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($netamountcopy / 10) * 10].' '.$words[$netamountcopy % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? "and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' paise' : '';
     $amountwrd=($Rupees ? ucFirst($Rupees) . 'rupee ' : '') . $paise;
    }
						


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
		


       
                            
                    <tr>
                    <td width="465" align="left">Quotation No:<b>$ProformaNumber</b> <br>  </td>
                    <td width="200">Date : <b>$date</b>  <br></td>
                    </tr>
		</table>
		<table cellspacing="0" cellpadding="4">
                    <tr >
                    <td width="40">To:</td>
					<td width="625">
					<b>  $PersonName </b> <br>
					<b>  $PermntAddress1 ,</b> 
					<b>  $PermntAddress2 ,</b> 
					<b>  $PermntCity ,</b>
					<b>  $PermntState_ID ,</b> 
					<b>  $PermntZip ,</b> <br>
					<b>$MobileNo</b><br>
					 
					</td>
                    </tr>
					<tr>
						<td width="60">GST No:</td>
						<td width="605">
						<b>$GSTNo </b></td>
						
					</tr>
					<tr style="line-height:0.8"><td width="665" align="left" style="font-size:110%;">	<b>Dear Sir/Madam</b>		</td></tr>
					<tr style="line-height:0.8"><td width="665" align="left" style="font-size:100%;">	<b>We thank you for your enquiry with Meena Fiberglas Industries. Please find below our offer.</b>		</td></tr>
                   
                   
   
    </table>
	 <table cellspacing="0" cellpadding="4"> 
	
		<tr>
		<td></td>
		</tr>
		
   </table>
			<table cellspacing="0" cellpadding="4" border="1">		
       <tr>
       <td width="50" align="center"><b>S.No</b></td>
       <td width="251" align="center"><b>Item Description</b></td>
       <td width="67" align="center"><b>Unit</b></td>
       <td width="99" align="center"> <b>UOM</b></td>
       <td width="99" align="center"><b>Unit/Price</b></td>
	   <td width="99" align="center"><b>Total Price</b></td>
    
      </tr>
       
	    {$troww}    
		
		
		
       </table> 			
	   <table cellspacing="0" cellpadding="4" style="line-height: 0.8; font-size:120%;">
		<tr>
			<td></td>
        </tr> 
		<tr>
       <td width="665" align="left"><b>TERMS AND CONDITIONS</b></td>
		</tr>
       <tr>
       <td width="120" align="left">Validity</td>
       <td width="30" align="center">:</td>
       <td width="515" align="left">$Validity</td> 
      </tr>  
		 <tr>
       <td width="120" align="left">Taxes</td>
       <td width="30" align="center">:</td>
       <td width="515" align="left">$Taxes</td> 
      </tr>  
        <tr>
        <td width="120" align="left">Payment Terms</td>
        <td width="30" align="center">:</td>
        <td width="515" align="left" >$PaymentTerms </td>
        </tr>     
        <tr>
        <td width="120" align="left">Freight </td>
        <td width="30" align="center"> :</td>
        <td width="515" align="left">$Freight</td>
        </tr>
        <tr>
        <td width="120" align="left">Installation </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left">$Inspection</td>
        </tr>
        <tr>
        <td width="120" align="left">Unloading </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left">$SpecialCondition</td>
        </tr>
 
        <tr>
			<td></td>
        </tr> 
		<tr>
			<td width="665" align="left">Thanking you and assuring you of our best services at all times.</td>
		</tr>
		<tr>
			<td></td>
        </tr> 
		<tr>
			<td width="665" align="left">Yours truly,</td>
		</tr>
		<tr>
			<td width="665" align="left">For Meena Fiberglas Industries</td>
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
 $pdf->Output($_SERVER['DOCUMENT_ROOT'] ."/resource/quatationpdf/Invoice".$data.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg1/resource/quatationpdf/Invoice".$data.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg1/resource/quatationpdf/Invoice".$data.".pdf", 'I');
$this->ses->set('pdffile', "/resource/quatationpdf/Invoice".$data.".pdf");
								   
								   // TCPDF END
							
							// pdf End
							
                            $this->tpl->set('message', 'Enquiry Quotation form edited successfully!');   
																														  
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
							header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiryQuotation');
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_quotation_design_form.php'));
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
										 "'" . $form_post_data['TermsAndConditions'] . "'," .
                                         "'" . $form_post_data['Validity'] . "'," .
                                         "'" . $form_post_data['Taxes'] . "'," .
										 "'" . $form_post_data['PaymentTerms'] . "'," .
                                         "'" . $form_post_data['Freight'] . "'," .
										 "'" . $form_post_data['Inspection'] . "'," .
										 "'" . $form_post_data['SpecialCondition'] . "'," .
                                              "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                         "'" .  $this->ses->get('user')['ID'] . "'";
        
		            $EnquiryID=$form_post_data['EnquiryID'];
					if($form_post_data['EnquiryID']){
                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "enquiry_quotation`
                                            (
                                            `EnquiryID`, 
											`TermsAndConditions`,
                                            `Validity`,
											`Taxes`,
                                            `PaymentTerms`, 
											`Freight`,
                                            `Inspection`,
											`SpecialCondition`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                         VALUES ( $val )";
                                  $stmt = $this->db->prepare($sql);
								  
					     $sql_update="UPDATE $enquiry_table SET EnquiryQuotation_Status=2 WHERE ID=$EnquiryID";
                         $stmt1 = $this->db->prepare($sql_update);
						 $stmt1->execute();
						 }
					if ($stmt->execute()) { 
					//echo '<pre>';
					//print_r($_POST);die;
					   $lastInsertedID = $this->db->lastInsertId();
					   
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
					   $maxCount = $form_post_data['maxCount'];
					   
                        FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                
                                $ItemDescription = $form_post_data['Field2_'.$entry_count];
								$ItemDescription1 = $form_post_data['ItemDescription_'.$entry_count];
                                $Unit = $form_post_data['Unit_'.$entry_count];
                                $unit_id = $form_post_data['unitmeasure_'.$entry_count];
                                $UnitPrice = $form_post_data['UnitPrice_'.$entry_count];
                                $Amount = $form_post_data['Field6_'.$entry_count];
                              
                                if(!empty($Unit) && !empty($unit_id)){
                                $vals = "'" . $lastInsertedID . "'," .
        								"'" . $ItemDescription . "'," .
										"'" . $ItemDescription1 . "'," .
                                        "'" . $Unit . "'," .
                                        "'" . $unit_id . "'," .
                                        "'" . $UnitPrice . "'," .
                                        "'" . $Amount . "'" ;
  
                                 $sql2 = "INSERT INTO $enquiryquotation_detail_table
                                        ( 
										    `EnquiryQuotation_ID`, 
                                            `ItemDescription`, 
											`ItemDescription1`,
                                            `Unit`,
                                            `unit_ID`,
                                            `UnitPrice`,
                                            `Amount`
                                        ) 
                                VALUES ($vals)";
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
							  }
                            
                            }
                            
             // pdf start 
							
							$enquiryquotation_detail_table = "SELECT $enquiryquotation_detail_table.ID,$enquiryquotation_detail_table.ItemDescription,$enquiryquotation_detail_table.unit_ID,$enquiryquotation_detail_table.Unit,$enquiryquotation_detail_table.UnitPrice,$enquiryquotation_detail_table.Amount,$enquiry_quotation_table .ID FROM $enquiryquotation_detail_table,$enquiry_quotation_table where $enquiry_quotation_table .ID=$enquiryquotation_detail_table.EnquiryQuotation_ID AND $enquiryquotation_detail_table.EnquiryQuotation_ID = $lastInsertedID ";
                            $stmt =$this->db->prepare( $enquiryquotation_detail_table);            
                            $stmt->execute();		
                            $enquiryquotation_detail_table_data = $stmt->fetchAll(2);
                            // var_dump($delDcproduct); 
                            $count = 1;
                            foreach($enquiryquotation_detail_table_data as $k=>$v){
                                $troww .=  '<tr>
                                <td style="width:50" align="left">'.$count.'</td>
                                <td style="width:251" align="left">'. $v['ItemDescription'].'</td>
                                <td style="width:67" align="center">'. $v['unit_ID'].'  </td>
                                <td style="width:99" align="right">'.$v["Unit"].'</td>
                                <td style="width:99" align="right">'. $v["UnitPrice"].'</td>
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
								
								
							$PersonName = $form_post_data['PersonName'];	
							
							 $customer_tab = "SELECT $customer_table.ID ,$customer_table.PersonName,$customer_table.GSTNo,$customer_table.PermntAddress1,$customer_table.PermntAddress2,$customer_table.PermntCity,$customer_table.PermntState_ID,$customer_table.PermntZip,$customer_table.MobileNo  from $customer_table where $customer_table.PersonName = '$PersonName' ";	
                            $stmt =$this->db->prepare($customer_tab);            
                            $stmt->execute();	
                            $customer_tab_fetch = $stmt->fetchAll(2);		
							
							$PersonName = $customer_tab_fetch[0]['PersonName'];		
							$GSTNo = $customer_tab_fetch[0]['GSTNo'];	
							$PermntAddress1 = $customer_tab_fetch[0]['PermntAddress1'];
							$PermntAddress2 = $customer_tab_fetch[0]['PermntAddress2'];
							$PermntCity = $customer_tab_fetch[0]['PermntCity'];
							$PermntState_ID = $customer_tab_fetch[0]['PermntState_ID'];
							$PermntZip = $customer_tab_fetch[0]['PermntZip'];
							$MobileNo = $customer_tab_fetch[0]['MobileNo'];			




								// $cgstPre=strval($form_post_data['CGST'].'%');
                            // $sgstPre=strval($form_post_data['SGST'].'%');
                            // $igstPre=strval($form_post_data['IGST'].'%');
                            // $cgstAmount = strval((float) $form_post_data['CGSTtotal']);
                            // $sgstAmount = strval((float) $form_post_data['SGSTtotal']);
                            // $igstAmount = strval((float) $form_post_data['IGSTtotal']);
                            // $netAmount = strval((float) $form_post_data['Balance']);
							// $netamountcopy=$netAmount;
                            // $BillAmount = strval((float) $form_post_data['Total']);
							$Validity = $form_post_data['Validity'];
							// $Taxes = strval((float)) $form_post_data['Taxes'];
							$Taxes=strval($form_post_data['Taxes'].'%');
							$PaymentTerms = $form_post_data['PaymentTerms'];
							$Freight = $form_post_data['Freight'];
							$Inspection = $form_post_data['Inspection'];
							$SpecialCondition = $form_post_data['SpecialCondition'];
							// $City=$form_post_data['City'];
							// $StateID=$form_post_data['StateID'];
							// $Pincode=$form_post_data['Pincode'];
							
							$ProformaNumber=$dbutil->keyGeneration('enquiry_quotation','ENQ','','EnquiryQuotation_ID');
						
    $decimal = round($netamountcopy - ($no = floor($netamountcopy)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');
    if($digits_length >= 10){
        echo "Sorry this does not support more than 99 crores";
    }else {
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $netamountcopy = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($netamountcopy) {
            $plural = (($counter = count($str)) && $netamountcopy > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($netamountcopy < 21) ? $words[$netamountcopy].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($netamountcopy / 10) * 10].' '.$words[$netamountcopy % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? "and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' paise' : '';
     $amountwrd=($Rupees ? ucFirst($Rupees) . 'rupee ' : '') . $paise;
    }
						


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
		


       
                            
                    <tr>
                    <td width="465" align="left">Quotation No:<b>$ProformaNumber</b> <br>  </td>
                    <td width="200">Date : <b>$date</b>  <br></td>
                    </tr>
		</table>
		<table cellspacing="0" cellpadding="4">
                    <tr >
                    <td width="40">To:</td>
					<td width="625">
					<b>  $PersonName </b> <br>
					<b>  $PermntAddress1 ,</b> 
					<b>  $PermntAddress2 ,</b> 
					<b>  $PermntCity ,</b>
					<b>  $PermntState_ID ,</b> 
					<b>  $PermntZip ,</b> <br>
					<b>$MobileNo</b><br>
					 
					</td>
                    </tr>
					<tr>
						<td width="60">GST No:</td>
						<td width="605">
						<b>$GSTNo </b></td>
						
					</tr>
					<tr style="line-height:0.8"><td width="665" align="left" style="font-size:110%;">	<b>Dear Sir/Madam</b>		</td></tr>
					<tr style="line-height:0.8"><td width="665" align="left" style="font-size:100%;">	<b>We thank you for your enquiry with Meena Fiberglas Industries. Please find below our offer.</b>		</td></tr>
                   
                   
   
    </table>
	 <table cellspacing="0" cellpadding="4"> 
	
		<tr>
		<td></td>
		</tr>
		
   </table>
			<table cellspacing="0" cellpadding="4" border="1">		
       <tr>
       <td width="50" align="center"><b>S.No</b></td>
       <td width="251" align="center"><b>Item Description</b></td>
       <td width="67" align="center"><b>Unit</b></td>
       <td width="99" align="center"> <b>UOM</b></td>
       <td width="99" align="center"><b>Unit/Price</b></td>
	   <td width="99" align="center"><b>Total Price</b></td>
    
      </tr>
       
	    {$troww}    
		
		
		
       </table> 			
	   <table cellspacing="0" cellpadding="4" style="line-height: 0.8; font-size:120%;">
		<tr>
			<td></td>
        </tr> 
		<tr>
       <td width="665" align="left"><b>TERMS AND CONDITIONS</b></td>
		</tr>
       <tr>
       <td width="120" align="left">Validity</td>
       <td width="30" align="center">:</td>
       <td width="515" align="left">$Validity</td> 
      </tr>  
		 <tr>
       <td width="120" align="left">Taxes</td>
       <td width="30" align="center">:</td>
       <td width="515" align="left">$Taxes</td> 
      </tr>  
        <tr>
        <td width="120" align="left">Payment Terms</td>
        <td width="30" align="center">:</td>
        <td width="515" align="left" >$PaymentTerms </td>
        </tr>     
        <tr>
        <td width="120" align="left">Freight </td>
        <td width="30" align="center"> :</td>
        <td width="515" align="left">$Freight</td>
        </tr>
        <tr>
        <td width="120" align="left">Installation </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left">$Inspection</td>
        </tr>
        <tr>
        <td width="120" align="left">Unloading </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left">$SpecialCondition</td>
        </tr>
 
        <tr>
			<td></td>
        </tr> 
		<tr>
			<td width="665" align="left">Thanking you and assuring you of our best services at all times.</td>
		</tr>
		<tr>
			<td></td>
        </tr> 
		<tr>
			<td width="665" align="left">Yours truly,</td>
		</tr>
		<tr>
			<td width="665" align="left">For Meena Fiberglas Industries</td>
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
 $pdf->Output($_SERVER['DOCUMENT_ROOT'] ."/resource/quatationpdf/Invoice".$lastInsertedID.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg1/resource/quatationpdf/Invoice".$lastInsertedID.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg1/resource/quatationpdf/Invoice".$lastInsertedID.".pdf", 'I');
$this->ses->set('pdffile', "/resource/quatationpdf/Invoice".$lastInsertedID.".pdf");
								  
								   // TCPDF END
						
							// pdf End
					       }
                        }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/enquiryQuotation');
																										
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_quotation_design_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Enquiry Quotation');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/enquiry_quotation_design_form.php'));
                    break;
             
                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
         $colArr = array(
                "$enquiry_quotation_table.ID",
				"$enquiry_table.EnquiryNo",
                "$customer_table.CompanyName"

              
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
             $whereString ="ORDER BY $enquiry_quotation_table.ID DESC";
           }
		   
		      
      $sql = "SELECT " 
                    . implode(',',$colArr)
                    . " FROM $enquiry_quotation_table LEFT JOIN $enquiry_table ON $enquiry_table.ID = $enquiry_quotation_table.EnquiryID LEFT JOIN $customer_table ON $customer_table.ID=$enquiry_table.customer_ID  "
                    . " WHERE "
                    . " $enquiry_quotation_table.entity_ID = $entityID" 
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
         
         
		 
            $this->tpl->set('table_columns_label_arr', array('ID','Enquiry No','Company Name'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
            $this->tpl->set('dcpdf','Generate Pdf');
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_enquiry_quotation_form.php';
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

public function feasibilityReviewReport(){
if ($this->crg->get('wp') || $this->crg->get('rp')) {
 ////////////////////////////////////////////////////////////////////////////////
 //////////////////////////////access condition applied//////////////////////////
 ////////////////////////////////////////////////////////////////////////////////    
            
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID']; 
			
            $feasibility_review_report_table=$this->crg->get('table_prefix') . 'feasibility_review_report';			
            $tender_table=$this->crg->get('table_prefix') . 'tender';
			$tenderdetail_table=$this->crg->get('table_prefix') . 'tenderdetail';
			$users_table=$this->crg->get('table_prefix') . 'users';
			
            $tender_sql = "SELECT ID,TenderNo FROM $tender_table WHERE $tender_table.Feasibility_Status=1 ORDER BY $tender_table.ID DESC";
            $stmt = $this->db->prepare($tender_sql);            
            $stmt->execute();
            $tender_data  = $stmt->fetchAll();	
            $this->tpl->set('tender_data', $tender_data);
			
			$tender1_sql = "SELECT ID,TenderNo FROM $tender_table WHERE $tender_table.Feasibility_Status=2 ORDER BY $tender_table.ID DESC";
            $stmt = $this->db->prepare($tender1_sql);            
            $stmt->execute();
            $tender1_data  = $stmt->fetchAll();	
            $this->tpl->set('tender1_data', $tender1_data);
			
            $this->tpl->set('page_title', 'FEASIBILITY REVIEW REPORT');	          
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
					$sql_update="UPDATE $tender_table SET Feasibility_Status=1 WHERE ID=(SELECT Tender_ID FROM $feasibility_review_report_table WHERE $feasibility_review_report_table.ID= $data)";
                    $stmt1 = $this->db->prepare($sql_update);
					$stmt1->execute();
					
                    $sqldetdelete="Delete from $feasibility_review_report_table  where $feasibility_review_report_table.ID=$data";  
                    $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Feasibility Review Report deleted successfully');
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
           					
					$sqlsrr = "SELECT *,$tenderdetail_table.Title,$tenderdetail_table.Qty,$users_table.user_nicename FROM `$tender_table` LEFT JOIN $feasibility_review_report_table ON $feasibility_review_report_table.Tender_ID=$tender_table.ID LEFT JOIN $tenderdetail_table ON $tenderdetail_table.tender_ID=$tender_table.ID LEFT JOIN $users_table ON $users_table.ID=$tender_table.users_ID WHERE `$feasibility_review_report_table`.`ID` = '$data'"; 	 
                    $salarydetail_data = $dbutil->getSqlData($sqlsrr); 
                   
                    //view option     
                    $this->tpl->set('message', 'You can view Feasibility Review Report form');
                    $this->tpl->set('page_header', 'Static Data');
                    $this->tpl->set('FmData', $salarydetail_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/feasibility_review_report.php'));                    
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
					
					 $sqlsrr = "SELECT *,$tenderdetail_table.Title,$tenderdetail_table.Qty,$users_table.user_nicename FROM `$tender_table` LEFT JOIN $feasibility_review_report_table ON $feasibility_review_report_table.Tender_ID=$tender_table.ID LEFT JOIN $tenderdetail_table ON $tenderdetail_table.tender_ID=$tender_table.ID LEFT JOIN $users_table ON $users_table.ID=$tender_table.users_ID WHERE `$feasibility_review_report_table`.`ID` = '$data'"; 	 
                     $salarydetail_data = $dbutil->getSqlData($sqlsrr); 
                     
                    //edit option     
                    $this->tpl->set('message', 'You can edit ReasibilityReview Report form');
                    $this->tpl->set('page_header', 'Static Data');
                    $this->tpl->set('FmData', $salarydetail_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/feasibility_review_report.php'));                    
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
                    // Profile_image,ID_Proof
                   // var_dump($form_post_data['ID_Proof']);die;
                
                    //Build SQL now                           
                        try{
                            
                            $Tender_ID=$form_post_data['Tender_ID'];  
                            $Status=$form_post_data['Status'];
							$Status1=$form_post_data['Status1'];
							$Status2=$form_post_data['Status2'];
							$Status3=$form_post_data['Status3'];
							$Status4=$form_post_data['Status4'];
							$Status5=$form_post_data['Status5'];
							$Status6=$form_post_data['Status6'];
							$Status7=$form_post_data['Status7'];
							$Status8=$form_post_data['Status8'];
							$Status9=$form_post_data['Status9'];
							$Status10=$form_post_data['Status10'];
							$Status11=$form_post_data['Status11'];
							$Status12=$form_post_data['Status12'];
							$Status13=$form_post_data['Status13'];
							$Status14=$form_post_data['Status14'];
							$Status15=$form_post_data['Status15'];
							$Status16=$form_post_data['Status16'];
							$Status17=$form_post_data['Status17'];
							$Status18=$form_post_data['Status18'];
							$Status19=$form_post_data['Status19'];
							$Status20=$form_post_data['Status20'];
							$Status21=$form_post_data['Status21'];
							$Status22=$form_post_data['Status22'];
							$Status23=$form_post_data['Status23'];
							$Status24=$form_post_data['Status24'];
							$Status25=$form_post_data['Status25'];
							$Status26=$form_post_data['Status26'];
							$Status27=$form_post_data['Status27'];
							$Status28=$form_post_data['Status28'];
							$Status29=$form_post_data['Status29'];
							$Status30=$form_post_data['Status30'];
							$Remark=$form_post_data['remark'];
							$Remark1=$form_post_data['remark1'];
							$Remark2=$form_post_data['remark2'];
							$Remark3=$form_post_data['remark3'];
							$Remark4=$form_post_data['remark4'];
							$Remark5=$form_post_data['remark5'];
							$Remark6=$form_post_data['remark6'];
							$Remark7=$form_post_data['remark7'];
							$Remark8=$form_post_data['remark8'];
							$Remark9=$form_post_data['remark9'];
							$Remark10=$form_post_data['remark10'];
							$Remark11=$form_post_data['remark11'];
							$Remark12=$form_post_data['remark12'];
							$Remark13=$form_post_data['remark13'];
							$Remark14=$form_post_data['remark14'];
							$Remark15=$form_post_data['remark15'];
							$Remark16=$form_post_data['remark16'];
							$Remark17=$form_post_data['remark17'];
							$Remark18=$form_post_data['remark18'];
							$Remark19=$form_post_data['remark19'];
							$Remark20=$form_post_data['remark20'];
							$Remark21=$form_post_data['remark21'];
							$Remark22=$form_post_data['remark22'];
							$Remark23=$form_post_data['remark23'];
							$Remark24=$form_post_data['remark24'];
							$Remark25=$form_post_data['remark25'];
							$Remark26=$form_post_data['remark26'];
							$Remark27=$form_post_data['remark27'];
							$Remark28=$form_post_data['remark28'];
							$Remark29=$form_post_data['remark29'];
							$Remark30=$form_post_data['remark30'];
							$check1=$form_post_data['check1'];
							$check2=$form_post_data['check2'];
							$check3=$form_post_data['check3'];
							$check4=$form_post_data['check4'];
							$check5=$form_post_data['check5'];
							$check6=$form_post_data['check6'];
							$check7=$form_post_data['check7'];
							$check8=$form_post_data['check8'];
							$Reason=$form_post_data['Reason'];
							$Reviewed_By=$form_post_data['Reviewed'];
							$Approved_By=$form_post_data['Approved'];

							               
                         $sql_update="Update $feasibility_review_report_table set Tender_ID='$Tender_ID',
																   Status='$Status',
																   Status1='$Status1',
																   Status2='$Status2',
																   Status3='$Status3',
																   Status4='$Status4',
																   Status5='$Status5',
																   Status6='$Status6',
																   Status7='$Status7',
																   Status8='$Status8',
																   Status9='$Status9',
																   Status10='$Status10',
																   Status11='$Status11',
																   Status12='$Status12',
																   Status13='$Status13',
																   Status14='$Status14',
																   Status15='$Status15',
																   Status16='$Status16',
																   Status17='$Status17',
																   Status18='$Status18',
																   Status19='$Status19',
																   Status20='$Status20',
																   Status21='$Status21',
																   Status22='$Status22',
																   Status23='$Status23',
																   Status24='$Status24',
																   Status25='$Status25',
																   Status26='$Status26',
																   Status27='$Status27',
																   Status28='$Status28',
																   Status29='$Status29',
																   Status30='$Status30',
																   Remark='$Remark',
																   Remark1='$Remark1',
																   Remark2='$Remark2',
																   Remark3='$Remark3',
																   Remark4='$Remark4',
																   Remark5='$Remark5',
																   Remark6='$Remark6',
																   Remark7='$Remark7',
																   Remark8='$Remark8',
																   Remark9='$Remark9',
																   Remark10='$Remark10',
																   Remark11='$Remark11',
																   Remark12='$Remark12',
																   Remark13='$Remark13',
																   Remark14='$Remark14',
																   Remark15='$Remark15',
																   Remark16='$Remark16',
																   Remark17='$Remark17',
																   Remark18='$Remark18',
																   Remark19='$Remark19',
																   Remark20='$Remark20',
																   Remark21='$Remark21',
																   Remark22='$Remark22',
																   Remark23='$Remark23',
																   Remark24='$Remark24',
																   Remark25='$Remark25',
																   Remark26='$Remark26',
																   Remark27='$Remark27',
																   Remark28='$Remark28',
																   Remark29='$Remark29',
																   Remark30='$Remark30',
																   check1='$check1',
																   check2='$check2',
																   check3='$check3',
																   check4='$check4',
																   check5='$check5',
																   check6='$check6',
																   check7='$check7',
																   check8='$check8',
																   Reason='$Reason',
																   Reviewed_By='$Reviewed_By',
																   Approved_By='$Approved_By'
																   
																   WHERE ID=$data";   
								
						$stmt1 = $this->db->prepare($sql_update);
                        $stmt1->execute(); 
                            $this->tpl->set('message', 'Feasibility Review Report form edited successfully!');   
                            $this->tpl->set('label', 'List');
                            $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/feasibility_review_report.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
                        
                         include_once 'util/genUtil.php';
                         $util = new GenUtil();
                        
                       // var_dump($_POST);
                       //var_dump($_FILES);die;
                        
                        $entry_count = 1;
                       
                            if (isset($form_post_data['Tender_ID'])) {
                           
                                    $val =  "'" . $form_post_data['Tender_ID'] . "'," .
                                            "'" . $form_post_data['Status'] . "'," .
											"'" . $form_post_data['Status1'] . "'," .
											"'" . $form_post_data['Status2'] . "'," .
											"'" . $form_post_data['Status3'] . "'," .
											"'" . $form_post_data['Status4'] . "'," .
											"'" . $form_post_data['Status5'] . "'," .
											"'" . $form_post_data['Status6'] . "'," .
											"'" . $form_post_data['Status7'] . "'," .
											"'" . $form_post_data['Status8'] . "'," .
											"'" . $form_post_data['Status9'] . "'," .
											"'" . $form_post_data['Status10'] . "'," .
											"'" . $form_post_data['Status11'] . "'," .
											"'" . $form_post_data['Status12'] . "'," .
											"'" . $form_post_data['Status13'] . "'," .
											"'" . $form_post_data['Status14'] . "'," .
											"'" . $form_post_data['Status15'] . "'," .
											"'" . $form_post_data['Status16'] . "'," .
											"'" . $form_post_data['Status17'] . "'," .
											"'" . $form_post_data['Status18'] . "'," .
											"'" . $form_post_data['Status19'] . "'," .
											"'" . $form_post_data['Status20'] . "'," .
											"'" . $form_post_data['Status21'] . "'," .
											"'" . $form_post_data['Status22'] . "'," .
											"'" . $form_post_data['Status23'] . "'," .
											"'" . $form_post_data['Status24'] . "'," .
											"'" . $form_post_data['Status25'] . "'," .
											"'" . $form_post_data['Status26'] . "'," .
											"'" . $form_post_data['Status27'] . "'," .
											"'" . $form_post_data['Status28'] . "'," .
											"'" . $form_post_data['Status29'] . "'," .
											"'" . $form_post_data['Status30'] . "'," .
                                            "'" . $form_post_data['remark'] . "'," .
											"'" . $form_post_data['remark1'] . "'," .
											"'" . $form_post_data['remark2'] . "'," .
											"'" . $form_post_data['remark3'] . "'," .
											"'" . $form_post_data['remark4'] . "'," .
											"'" . $form_post_data['remark5'] . "'," .
											"'" . $form_post_data['remark6'] . "'," .
											"'" . $form_post_data['remark7'] . "'," .
											"'" . $form_post_data['remark8'] . "'," .
											"'" . $form_post_data['remark9'] . "'," .
											"'" . $form_post_data['remark10'] . "'," .
											"'" . $form_post_data['remark11'] . "'," .
											"'" . $form_post_data['remark12'] . "'," .
											"'" . $form_post_data['remark13'] . "'," .
											"'" . $form_post_data['remark14'] . "'," .
											"'" . $form_post_data['remark15'] . "'," .
											"'" . $form_post_data['remark16'] . "'," .
											"'" . $form_post_data['remark17'] . "'," .
											"'" . $form_post_data['remark18'] . "'," .
											"'" . $form_post_data['remark19'] . "'," .
											"'" . $form_post_data['remark20'] . "'," .
											"'" . $form_post_data['remark21'] . "'," .
											"'" . $form_post_data['remark22'] . "'," .
											"'" . $form_post_data['remark23'] . "'," .
											"'" . $form_post_data['remark24'] . "'," .
											"'" . $form_post_data['remark25'] . "'," .
											"'" . $form_post_data['remark26'] . "'," .
											"'" . $form_post_data['remark27'] . "'," .
											"'" . $form_post_data['remark28'] . "'," .
											"'" . $form_post_data['remark29'] . "'," .
											"'" . $form_post_data['remark30'] . "'," .
											"'" . $form_post_data['check1'] . "'," .
											"'" . $form_post_data['check2'] . "'," .
											"'" . $form_post_data['check3'] . "'," .
											"'" . $form_post_data['check4'] . "'," .
											"'" . $form_post_data['check5'] . "'," .
											"'" . $form_post_data['check6'] . "'," .
											"'" . $form_post_data['check7'] . "'," .
											"'" . $form_post_data['check8'] . "'," .
											"'" . $form_post_data['Reason'] . "'," .
											"'" . $form_post_data['Reviewed'] . "'," .
											"'" . $form_post_data['Approved'] . "'," .
                                            "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                            "'" .  $this->ses->get('user')['ID'] . "'";

                         $Tender_ID=$form_post_data['Tender_ID'];
						 if($form_post_data['Tender_ID']){
                         $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "feasibility_review_report`
                                            ( 
                                            `Tender_ID`,
											`Status`,
											`Status1`,
											`Status2`,
											`Status3`,
											`Status4`,
											`Status5`,
											`Status6`,
											`Status7`,
											`Status8`,
											`Status9`,
											`Status10`,
											`Status11`,
											`Status12`,
											`Status13`,
											`Status14`,
											`Status15`,
											`Status16`,
											`Status17`,
											`Status18`,
											`Status19`,
											`Status20`,
											`Status21`,
											`Status22`,
											`Status23`,
											`Status24`,
											`Status25`,
											`Status26`,
											`Status27`,
											`Status28`,
											`Status29`,
											`Status30`,
											`Remark`,
											`Remark1`,
											`Remark2`,
											`Remark3`,
											`Remark4`,
											`Remark5`,
											`Remark6`,
											`Remark7`,
											`Remark8`,
											`Remark9`,
											`Remark10`,
											`Remark11`,
											`Remark12`,
											`Remark13`,
											`Remark14`,
											`Remark15`,
											`Remark16`,
											`Remark17`,
											`Remark18`,
											`Remark19`,
											`Remark20`,
											`Remark21`,
											`Remark22`,
											`Remark23`,
											`Remark24`,
											`Remark25`,
											`Remark26`,
											`Remark27`,
											`Remark28`,
											`Remark29`,
											`Remark30`,
											`check1`,
											`check2`,
											`check3`,
											`check4`,
											`check5`,
											`check6`,
											`check7`,
											`check8`,
											`Reason`,
											`Reviewed_By`,
											`Approved_By`,
                                            `entity_ID`,
                                            `users_ID`
                                            ) 
                                        VALUES ( $val )";   		
                                  $stmt = $this->db->prepare($sql);
								  $stmt->execute();
								  
					     $sql_update="UPDATE $tender_table SET Feasibility_Status=2 WHERE ID=$Tender_ID";
                         $stmt1 = $this->db->prepare($sql_update);
						 $stmt1->execute();
						 }
                        }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/feasibilityReviewReport');
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/feasibility_review_report.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Static Data');
                    $staffcode=$dbutil->keyGeneration('staff_info','GMH','','StaffCode');
                    $this->tpl->set('staffcode', $staffcode);
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/feasibility_review_report.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
				"$feasibility_review_report_table.ID",
                "$tender_table.TenderNo",
                "$feasibility_review_report_table.Status",
                "$feasibility_review_report_table.Remark",
                //"$feasibility_review_report_table.check1",
				//"$feasibility_review_report_table.check2",
				"$feasibility_review_report_table.Reason",
				"$feasibility_review_report_table.Reviewed_By",
				"$feasibility_review_report_table.Approved_By"
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
             $whereString ="ORDER BY $feasibility_review_report_table.ID DESC";
           }
            
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $feasibility_review_report_table LEFT JOIN $tender_table ON $tender_table.ID=$feasibility_review_report_table.Tender_ID "
                    . " WHERE "
                    . " $feasibility_review_report_table.entity_ID = $entityID"
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
         
         
            $this->tpl->set('table_columns_label_arr', array('ID','Enquiry No','Status','Remark','Reason','Reviewed_By','Approved_By'));
			
			// $this->tpl->set('table_columns_label_arr', array('ID','Enquiry No','Title','Quantity','Status','Remark','Design','Tender','Reason','Reviewed_By','Approved_By'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_feasibility_review_report.php';
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
    
//////////////////////////////////////////////////

public function tenderEvaluation(){
    
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
			 
			////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////access condition applied//////////////////////////
			////////////////////////////////////////////////////////////////////////////////    
						
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $tender_evaluation_table = $this->crg->get('table_prefix') . 'tender_evaluation';
			$enquiry_table = $this->crg->get('table_prefix') . 'enquiry';
			$tender_table = $this->crg->get('table_prefix') . 'tender';
			$tenderdetail_table = $this->crg->get('table_prefix') . 'tenderdetail';
            
            $enquiry_sql = "SELECT ID,EnquiryNo FROM $enquiry_table WHERE $enquiry_table.TenderEvaluation_Status=1 AND EnquiryType=2";
            $stmt = $this->db->prepare($enquiry_sql);            
            $stmt->execute();
            $enquiry_data  = $stmt->fetchAll();	
            $this->tpl->set('enquiry_data', $enquiry_data);
			
			$enquiry_sql = "SELECT ID,EnquiryNo FROM $enquiry_table WHERE $enquiry_table.TenderEvaluation_Status=2";
            $stmt = $this->db->prepare($enquiry_sql);            
            $stmt->execute();
            $enquiry1_data  = $stmt->fetchAll();	
            $this->tpl->set('enquiry1_data', $enquiry1_data);
			
            $this->tpl->set('page_title', 'Tender Evaluation');	          
            $this->tpl->set('page_header', 'Tender Evaluation');
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
                     $sql_update="UPDATE $enquiry_table SET TenderEvaluation_Status=1 WHERE ID=(SELECT EnquiryID FROM $tender_evaluation_table WHERE $tender_evaluation_table.ID= $data)";
                     $stmt1 = $this->db->prepare($sql_update);
					 $stmt1->execute();
					 
                     $sqldetdelete="Delete from $tender_evaluation_table
                                    where $tender_evaluation_table.ID=$data"; 
                     $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Tender Evaluation form deleted successfully');
																													  
                        //$this->tpl->set('label', 'List');
                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/tenderEvaluation');
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
                          
                    $sqlsrr = "SELECT *,$tender_table.TenderNo,$tender_table.ClosingDateTime,$tenderdetail_table.Title,$tenderdetail_table.PLCod,$tenderdetail_table.Description,$tenderdetail_table.Qty
		                       FROM $enquiry_table 
				               LEFT JOIN $tender_table ON $tender_table.enquiry_ID=$enquiry_table.ID 
							   LEFT JOIN $tenderdetail_table ON $tenderdetail_table.tender_ID=$tender_table.ID
						       LEFT JOIN $tender_evaluation_table ON $tender_evaluation_table.EnquiryID=$enquiry_table.ID
                               WHERE  $tender_evaluation_table.ID= $data";
                               $tender_evaluation_data = $dbutil->getSqlData($sqlsrr);
                   
                    //edit option     
                    $this->tpl->set('message', 'You can view Tender Evaluation form');
                    $this->tpl->set('page_header', 'Tender Evaluation');
                    $this->tpl->set('FmData', $tender_evaluation_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_evaluation_design_form.php'));                    
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
           			  
                   $sqlsrr = "SELECT *,$tender_table.TenderNo,$tender_table.ClosingDateTime,$tenderdetail_table.Title,$tenderdetail_table.PLCod,$tenderdetail_table.Description,$tenderdetail_table.Qty
		                       FROM $enquiry_table 
				               LEFT JOIN $tender_table ON $tender_table.enquiry_ID=$enquiry_table.ID 
							   LEFT JOIN $tenderdetail_table ON $tenderdetail_table.tender_ID=$tender_table.ID
						       LEFT JOIN $tender_evaluation_table ON $tender_evaluation_table.EnquiryID=$enquiry_table.ID
                               WHERE  $tender_evaluation_table.ID= $data";
                               $tender_evaluation_data = $dbutil->getSqlData($sqlsrr);
                    
                    //edit option 

					//print_r($_POST); 
                    $this->tpl->set('message', 'You can edit Tender Evaluation form');
                    $this->tpl->set('page_header', 'Tender Evaluation');
                    $this->tpl->set('FmData', $tender_evaluation_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_evaluation_design_form.php'));                    
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
                          
                               $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "tender_evaluation` SET " . $updatecustomer .
                                             " WHERE ID = " .$data . "";

                                $stmt = $this->db->prepare($updateSql);
                                $stmt->execute(); 
						                       
                    try{
                          $EnquiryID= $form_post_data['EnquiryID'];
						  
						 $sql_update="UPDATE $tender_evaluation_table set EnquiryID='$EnquiryID' WHERE ID=$data";
                         $stmt1 = $this->db->prepare($sql_update);
                         $stmt1->execute();
					       
                            $this->tpl->set('message', 'Tender Evaluation form edited successfully!');   
																														  
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
							header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/tenderEvaluation');
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_evaluation_design_form.php'));
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
										      // "'" . $_FILES['Description_Proof']['name']. "'," .
											    "'" . $form_post_data['Description_Proof']. "'," .
                                              "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                         "'" .  $this->ses->get('user')['ID'] . "'";
        
		            $EnquiryID=$form_post_data['EnquiryID'];
					
					if($form_post_data['EnquiryID']){
                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "tender_evaluation`
                                            (
                                            `EnquiryID`, 
											`Description_Proof`, 
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
							
						  
                             $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "tender_evaluation` SET " .  $updatecustomer  .
                                               " WHERE ID = '" .$lastInsertedID . "'";
                                $stmt = $this->db->prepare($updateSql);
                                $stmt->execute();
								
					     $sql_update="UPDATE $enquiry_table SET TenderEvaluation_Status=2 WHERE ID=$EnquiryID";
                         $stmt1 = $this->db->prepare($sql_update);
						 $stmt1->execute();
						 }
						 }
					
                        }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/tenderEvaluation');
																										
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_evaluation_design_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Tender Evaluation');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/tender_evaluation_design_form.php'));
                    break;
             
                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
         $colArr = array(
                "$tender_evaluation_table.ID",
				"$enquiry_table.EnquiryNo",
                "$tender_table.TenderNo",
                "$tender_table.ClosingDateTime"
              
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
             $whereString ="ORDER BY $tender_evaluation_table.ID DESC";
           }
		   
		      
       $sql = "SELECT " 
                    . implode(',',$colArr)
                    . " FROM $tender_evaluation_table LEFT JOIN $enquiry_table ON $enquiry_table.ID = $tender_evaluation_table.EnquiryID LEFT JOIN $tender_table ON $tender_table.enquiry_ID=$enquiry_table.ID "
                    . " WHERE "
                    . " $tender_evaluation_table.entity_ID = $entityID" 
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
         
         
		 
             $this->tpl->set('table_columns_label_arr', array('ID','Enquiry No','Tender No','Closing date and time'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_tender_evaluation_form.php';
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

//////////////////////////////////////////////////

public function CustomerComplaint(){
    
    if ($this->crg->get('wp') || $this->crg->get('rp')) {
			 
			////////////////////////////////////////////////////////////////////////////////
			//////////////////////////////access condition applied//////////////////////////
			////////////////////////////////////////////////////////////////////////////////    
						
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
            
            $customercomplaint_register_table = $this->crg->get('table_prefix') . 'customercomplaint_register';
			$customer_table = $this->crg->get('table_prefix') . 'customer';
			$customerorder_table = $this->crg->get('table_prefix') . 'customerorder';
			$customerorder_detail_table = $this->crg->get('table_prefix') . 'customerorder_detail';
			$enquiry_table = $this->crg->get('table_prefix') . 'enquiry';
			$users_table = $this->crg->get('table_prefix') . 'users';
            
            $customer_sql = "SELECT ID,PersonName FROM $customer_table";
            $stmt = $this->db->prepare($customer_sql);            
            $stmt->execute();
            $customer_data  = $stmt->fetchAll();	
            $this->tpl->set('customer_data', $customer_data);
			
			$product_sql = "SELECT ID,ProductName FROM $customerorder_detail_table";
            $stmt = $this->db->prepare($product_sql);            
            $stmt->execute();
            $product_data  = $stmt->fetchAll();	
            $this->tpl->set('product_data', $product_data);
			
			$users_sql = "SELECT ID,user_nicename FROM $users_table";
            $stmt = $this->db->prepare($users_sql);            
            $stmt->execute();
            $users_data  = $stmt->fetchAll();	
            $this->tpl->set('users_data', $users_data);
			
			
			
            $this->tpl->set('page_title', 'Customer Complaint Register');	          
            $this->tpl->set('page_header', 'Customer Complaint Register');
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
					 
                     $sqldetdelete="Delete from $customercomplaint_register_table
                                    where $customercomplaint_register_table.ID=$data"; 
                     $stmt = $this->db->prepare($sqldetdelete);            
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Customer Complaint Register form deleted successfully');
																													  
                        //$this->tpl->set('label', 'List');
                        //$this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/CustomerComplaint');
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
                          
                      $sqlsrr = "SELECT * FROM $customer_table 
					            INNER JOIN $enquiry_table ON $enquiry_table.customer_ID=$customer_table.ID 
			                    INNER JOIN $customerorder_table ON $customerorder_table.enquiry_ID=$enquiry_table.ID
				                INNER JOIN $customerorder_detail_table ON $customerorder_detail_table.custorder_ID=$customerorder_table.ID
					            LEFT JOIN $customercomplaint_register_table ON $customercomplaint_register_table.CustomerID=$customer_table.ID
                                WHERE $customercomplaint_register_table.ID= $data";
                                $customer_complaint_data = $dbutil->getSqlData($sqlsrr);
                   
                    //edit option     
                    $this->tpl->set('message', 'You can view Tender Evaluation form');
                    $this->tpl->set('page_header', 'Tender Evaluation');
                    $this->tpl->set('FmData', $customer_complaint_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customercomplaint_register_design_form.php'));                    
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
           			  
                    $sqlsrr = "SELECT * FROM $customer_table 
					            INNER JOIN $enquiry_table ON $enquiry_table.customer_ID=$customer_table.ID 
			                    INNER JOIN $customerorder_table ON $customerorder_table.enquiry_ID=$enquiry_table.ID
				                INNER JOIN $customerorder_detail_table ON $customerorder_detail_table.custorder_ID=$customerorder_table.ID
					            LEFT JOIN $customercomplaint_register_table ON $customercomplaint_register_table.CustomerID=$customer_table.ID
                                WHERE $customercomplaint_register_table.ID= $data";
                                $customer_complaint_data = $dbutil->getSqlData($sqlsrr);
                    
                    //edit option 

					//print_r($_POST); 
                    $this->tpl->set('message', 'You can edit Customer Complaint Register form');
                    $this->tpl->set('page_header', 'Customer Complaint Register');
                    $this->tpl->set('FmData', $customer_complaint_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customercomplaint_register_design_form.php'));                    
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
                          
                               $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "customercomplaint_register` SET " . $updatecustomer .
                                             " WHERE ID = " .$data . "";

                                $stmt = $this->db->prepare($updateSql);
                                $stmt->execute(); 
								
					$ComplaintDate=date("Y-m-d", strtotime($form_post_data['ComplaintDate']));
					$ClosedOn=date("Y-m-d", strtotime($form_post_data['ClosedOn']));
						                       
                    try{
                          $CustomerID= $form_post_data['CustomerID'];
						  $SiteAddress= $form_post_data['SiteAddress'];
						  $Customer_ComplaintRegNo= $form_post_data['Customer_ComplaintRegNo'];
						  $CustomerOrderDetail_ID= $form_post_data['CustomerOrderDetail_ID'];
						  $Quantity= $form_post_data['Quantity'];
						  $Attended_userid= $form_post_data['Attended_userid'];
						  $ComplaintDetails= $form_post_data['ComplaintDetails'];
						  $Complaint= $form_post_data['Complaint'];
						  $Correction= $form_post_data['Correction'];
						  $ComplaintDate= $ComplaintDate;
						  $Location= $form_post_data['Location'];
						  $RootCauseAnalysis= $form_post_data['RootCauseAnalysis'];
						  $RemarksFromCustomer= $form_post_data['RemarksFromCustomer'];
						  $PreventiveAction= $form_post_data['PreventiveAction'];
						  $CorrectiveAction= $form_post_data['CorrectiveAction'];
						  $ClosedOn= $ClosedOn;
						  $Remarks= $form_post_data['Remarks'];
						  $Status= $form_post_data['Status'];
						  
						 $sql_update="UPDATE $customercomplaint_register_table set CustomerID='$CustomerID',SiteAddress='$SiteAddress',Customer_ComplaintRegNo='$Customer_ComplaintRegNo',CustomerOrderDetail_ID='$CustomerOrderDetail_ID',Quantity='$Quantity',Attended_userid='$Attended_userid',ComplaintDetails='$ComplaintDetails',Complaint='$Complaint',Correction='$Correction',ComplaintDate='$ComplaintDate',Location='$Location',RootCauseAnalysis='$RootCauseAnalysis',RemarksFromCustomer='$RemarksFromCustomer',PreventiveAction='$PreventiveAction',CorrectiveAction='$CorrectiveAction',ClosedOn='$ClosedOn',Remarks='$Remarks',Status='$Status' WHERE ID=$data";
                         $stmt1 = $this->db->prepare($sql_update);
                         $stmt1->execute();
					       
                            $this->tpl->set('message', 'Customer Complaint Register form edited successfully!');   
																														  
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
							header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/CustomerComplaint');
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/customercomplaint_register_design_form.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
 
                        $form_post_data = $dbutil->arrFltr($_POST);
						
						include_once 'util/genUtil.php';
                         $util = new GenUtil();
                        
                      // var_dump($_POST);
				    $ComplaintDate=date("Y-m-d", strtotime($form_post_data['ComplaintDate']));
					$ClosedOn=date("Y-m-d", strtotime($form_post_data['ClosedOn']));
                       
					   if (isset($form_post_data['CustomerID'])) {
                           
                                        $val = "'" . $form_post_data['CustomerID'] . "'," .
										       "'" . $form_post_data['SiteAddress'] . "'," .
											   "'" . $form_post_data['Customer_ComplaintRegNo'] . "'," .
										       "'" . $form_post_data['CustomerOrderDetail_ID'] . "'," .
											   "'" . $form_post_data['Quantity']. "'," .
											   "'" . $form_post_data['Attended_userid'] . "'," .
											   "'" . $form_post_data['ComplaintDetails'] . "'," .
										       "'" . $form_post_data['Complaint'] . "'," .
											   "'" . $form_post_data['Correction']. "'," .
											   "'" . $ComplaintDate . "'," .
											   "'" . $form_post_data['Location']. "'," .
											   "'" . $form_post_data['RootCauseAnalysis'] . "'," .
										       "'" . $form_post_data['RemarksFromCustomer'] . "'," .
											   "'" . $form_post_data['PreventiveAction']. "'," .
											   "'" . $form_post_data['CorrectiveAction'] . "'," .
											   "'" . $form_post_data['FileUpload']. "'," .
											   "'" . $ClosedOn . "'," .
											   "'" . $form_post_data['Remarks'] . "'," .
										       "'" . $form_post_data['Status'] . "'," .
                                              "'" .  $this->ses->get('user')['entity_ID'] . "'," .
                                         "'" .  $this->ses->get('user')['ID'] . "'";
        
                            $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "customercomplaint_register`
                                            (
                                            `CustomerID`, 
											`SiteAddress`, 
										    `Customer_ComplaintRegNo`, 
											`CustomerOrderDetail_ID`, 
											`Quantity`, 
											`Attended_userid`, 
										    `ComplaintDetails`, 
											`Complaint`, 
											`Correction`, 
											`ComplaintDate`, 
											`Location`, 
										    `RootCauseAnalysis`, 
											`RemarksFromCustomer`, 
											`PreventiveAction`, 
											`CorrectiveAction`, 
										    `FileUpload`, 
											`ClosedOn`, 
										    `Remarks`, 
											`Status`, 
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
							
						  
                             $updateSql = "UPDATE `" . $this->crg->get('table_prefix') . "customercomplaint_register` SET " .  $updatecustomer  .
                                               " WHERE ID = '" .$lastInsertedID . "'";
                                $stmt = $this->db->prepare($updateSql);
                                $stmt->execute();
								
						 }
						 
					
                        }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        // $this->tpl->set('content', $this->tpl->fetch('factory/form/salary_form.php'));
						header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/CustomerComplaint');
																										
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/customercomplaint_register_design_form.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Customer Complaint Register');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/customercomplaint_register_design_form.php'));
                    break;
             
                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
         $colArr = array(
                "$customercomplaint_register_table.ID",
				"$customercomplaint_register_table.Customer_ComplaintRegNo",
				"$customer_table.PersonName",
                "$users_table.user_nicename",
                "$customercomplaint_register_table.Complaint",
                "$customercomplaint_register_table.ComplaintDate",
                "$customercomplaint_register_table.ClosedOn"
              
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
             $whereString ="ORDER BY $customercomplaint_register_table.ID DESC";
           }
		   
		      
       $sql = "SELECT " 
                    . implode(',',$colArr)
                    . " FROM $customercomplaint_register_table LEFT JOIN $customer_table ON $customer_table.ID = $customercomplaint_register_table.CustomerID 
                        LEFT JOIN $users_table ON $users_table.ID = $customercomplaint_register_table.Attended_userid "
                    . " WHERE "
                    . " $customercomplaint_register_table.entity_ID = $entityID" 
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
         
         
		 
           $this->tpl->set('table_columns_label_arr', array('ID','Customer Complaint Register No','Customer Name','Attended by','Complaint','Complaint date','Closed on'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_customer_complaint_register_form.php';
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
    
    //////////////////////////////////////////////////////////////////////////////////////////
   public function proformaInvoice(){
     if ($this->crg->get('wp') || $this->crg->get('rp')) {
             
            ////////////////////////////////////////////////////////////////////////////////
            //////////////////////////////access condition applied//////////////////////////
            ////////////////////////////////////////////////////////////////////////////////    
            
            include_once 'util/DBUTIL.php';
            $dbutil = new DBUTIL($this->crg);
             
            $entityID = $this->ses->get('user')['entity_ID'];
            $userID = $this->ses->get('user')['ID'];
			
			
            $state_table = $this->crg->get('table_prefix') . 'state';
            $unit_tab = $this->crg->get('table_prefix') . 'unit';		
			$enquiry_tab = $this->crg->get('table_prefix') . 'enquiry';
	/*		$state_tab = $this->crg->get('table_prefix') . 'state';		*/
			$proforma_add_invoice_tab = $this->crg->get('table_prefix') . 'proforma_add_invoice';
			$proforma_invoice_tab = $this->crg->get('table_prefix') . 'proforma_invoice';
			$customer_table = $this->crg->get('table_prefix') . 'customer';
			
			//enquiry table data 
           
            $enquiry_sql = "SELECT ID,EnquiryNo,PONo FROM $enquiry_tab WHERE $enquiry_tab.Proforma_Status=1";
            $stmt = $this->db->prepare($enquiry_sql);            
            $stmt->execute();
            $enquiry_data  = $stmt->fetchAll();	
            $this->tpl->set('enquiry_data', $enquiry_data);
			
			$enquiry1_sql = "SELECT ID,EnquiryNo,PONo FROM $enquiry_tab WHERE $enquiry_tab.Proforma_Status=2";
            $stmt = $this->db->prepare($enquiry1_sql);            
            $stmt->execute();
            $enquiry1_data  = $stmt->fetchAll();	
            $this->tpl->set('enquiry1_data', $enquiry1_data);
			
			//unit table
			
			 //state table data 
           
            $unit_sql = "SELECT ID,UnitName FROM $unit_tab";
            $stmt = $this->db->prepare($unit_sql);            
            $stmt->execute();
            $unit_data  = $stmt->fetchAll();	
            $this->tpl->set('unit_data', $unit_data);
			
			 //state table data 
           
 /*           $state_sql = "SELECT ID,StateName FROM $state_tab";
            $stmt = $this->db->prepare($state_sql);            
            $stmt->execute();
            $state_data  = $stmt->fetchAll();	
            $this->tpl->set('state_data', $state_data);		*/
            
           
            $this->tpl->set('page_title', 'Proforma Invoice Process');	          
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
                     //  var_dump($data);
                       
                       
                    if (!$data) {
                        $this->tpl->set('message', 'Please select any one ID to '.$crud_string.'!');
                        $this->tpl->set('label', 'List');
                        $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                       
                    }
					
					  $sql_update="UPDATE $enquiry_tab SET Proforma_Status=1 WHERE ID=(SELECT EnquiryID FROM $proforma_invoice_tab WHERE $proforma_invoice_tab.ID= $data)";
                      $stmt1 = $this->db->prepare($sql_update);
					  $stmt1->execute();
                     
                      $sqldetdelete="Delete $proforma_add_invoice_tab,$proforma_invoice_tab from          $proforma_invoice_tab
                                        LEFT JOIN  $proforma_add_invoice_tab ON $proforma_add_invoice_tab.proforma_invoice_ID=$proforma_invoice_tab.ID 
                                        where $proforma_invoice_tab.ID=$data"; 	
						$stmt = $this->db->prepare($sqldetdelete);
                        
                        if($stmt->execute()){
                        $this->tpl->set('message', 'Process deleted successfully');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/proformaInvoice');
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

					 $sqlsrr="SELECT *,$enquiry_tab.PONo,$customer_table.PersonName,$customer_table.CompanyName,$customer_table.GSTNo,$customer_table.BillingAddress1,$customer_table.BillingAddress2,$customer_table.BillingCity,$state_table.StateName,$customer_table.BillingZip 
			   	FROM $proforma_invoice_tab 
							  LEFT JOIN $enquiry_tab ON $enquiry_tab.ID = $proforma_invoice_tab.EnquiryID
							  LEFT JOIN $customer_table ON $customer_table.ID = $enquiry_tab.customer_ID 
							  LEFT JOIN $state_table ON $state_table.ID = $customer_table.BillingState_ID
					          LEFT JOIN $proforma_add_invoice_tab ON $proforma_add_invoice_tab.proforma_invoice_ID=$proforma_invoice_tab.ID
					          WHERE $proforma_invoice_tab.ID = $data";		
							  
                                
                    //$sqlsrr = "SELECT * FROM `$proforma_invoice_tab`,`$proforma_add_invoice_tab` //WHERE`$proforma_add_invoice_tab`.`proforma_invoice_ID`=`$proforma_invoice_tab`.`ID` //AND`$proforma_invoice_tab`.`ID` = '$data'";
				//	$sqlsrr = "SELECT  * FROM `$proforma_invoice_tab` WHERE `$proforma_invoice_tab`.`ID` = '$data'";                    
                   $process_data = $dbutil->getSqlData($sqlsrr); 
                    
                    //edit option     
                    $this->tpl->set('message', 'You can view Proforma Invoice form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('FmData', $process_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/proforma_invoice.php'));                    
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
                                
                   // $sqlsrr = "SELECT  * FROM `$bomprocessdetail_tab`,`$bomprocessmaster_tab` WHERE `$bomprocessdetail_tab`.`proforma_invoice_ID`=`$bomprocessmaster_tab`.`ID` AND `$bomprocessdetail_tab`.`BOMProcessMaster_ID` = '$data'"; 
				   
 		/*	<possibility code 1>	*/	
	             $sqlsrr="SELECT *,$enquiry_tab.PONo,$customer_table.PersonName,$customer_table.CompanyName,$customer_table.GSTNo,$customer_table.BillingAddress1,$customer_table.BillingAddress2,$customer_table.BillingCity,$state_table.StateName,$customer_table.BillingZip 
			   	FROM $proforma_invoice_tab 
							  LEFT JOIN $enquiry_tab ON $enquiry_tab.ID = $proforma_invoice_tab.EnquiryID
							  LEFT JOIN $customer_table ON $customer_table.ID = $enquiry_tab.customer_ID 
							  LEFT JOIN $state_table ON $state_table.ID = $customer_table.BillingState_ID
					          LEFT JOIN $proforma_add_invoice_tab ON $proforma_add_invoice_tab.proforma_invoice_ID=$proforma_invoice_tab.ID
					          WHERE $proforma_invoice_tab.ID = $data";		
							  
		/*	<possibility code 2> 			$sqlsrr="SELECT * FROM $proforma_invoice_tab,$proforma_add_invoice_tab
					         WHERE  $proforma_add_invoice_tab.proforma_invoice_ID=$proforma_invoice_tab.ID
					         AND $proforma_invoice_tab.ID = $data";  */
							  
					 // $sqlsrr = "SELECT  * FROM `$proforma_invoice_tab` WHERE `$proforma_invoice_tab`.`ID` = '$data'";				
                    $process_data = $dbutil->getSqlData($sqlsrr); 

                    //edit option     
                    $this->tpl->set('message', 'You can edit Proforma Invoice form');
                    $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('FmData', $process_data); 
                    
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/proforma_invoice.php'));                    
                    break;
                
                case 'editsubmit':             
                    $data = trim($_POST['ycs_ID']);
                  //  print_r($data); die;
                    //mode of form submit
                    $this->tpl->set('mode', 'edit');
                    //set id to edit $ycs_ID
                    $this->tpl->set('ycs_ID', $data);

                    //Post data
                    include_once 'util/genUtil.php';
                    $util = new GenUtil();
                    $form_post_data = $util->arrFltr($_POST);
                  try{
                      
                      //pdf start						
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

$pdf->SetMargins(10, 3, 10);
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
//pdf end

                               
												 $EnquiryID=$form_post_data['EnquiryID']; 	 
												 $GST_NO=$form_post_data['GST_NO'];
												 $Total=$form_post_data['Total'];
												 $GST=$form_post_data['GST'];
												 $CGST=$form_post_data['CGST'];
												$CGSTtotal=$form_post_data['CGSTtotal'];
												 $optradio=$form_post_data['optradio'];
												 $SGST=$form_post_data['SGST'];
												$SGSTtotal=$form_post_data['SGSTtotal'];
												 $IGST=$form_post_data['IGST'];
												$IGSTtotal=$form_post_data['IGSTtotal'];
												$Advance=$form_post_data['Advance'];
												 $Balance=$form_post_data['Balance']; 
  
                                $sql2 = "UPDATE $proforma_invoice_tab set  EnquiryID='$EnquiryID',

											GST_NO='$GST_NO',
											Total='$Total',
											GST='$GST',
											CGST='$CGST',
											CGSTtotal='$CGSTtotal',
										   `CGST_&_SGST_&_IGST`='$optradio',
											SGST='$SGST',
											SGSTtotal='$SGSTtotal',
											IGST='$IGST',
											IGSTtotal='$IGSTtotal',
											Advance='$Advance',
											Balance='$Balance'

                                             WHERE ID=$data";			
                                        
                
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
								
								$maxCount = $form_post_data['maxCount']; 			   
                      
						 $sql3 = "DELETE FROM $proforma_add_invoice_tab WHERE proforma_invoice_ID=$data";
					     $stmt3 = $this->db->prepare($sql3);
						// $stmt3->execute();
                         //$is_delete = $stmt3->execute();
						 // var_dump($is_delete);die;
					   if($stmt3->execute()){
						  // echo '<pre>';
						  // print_r($_POST); 
                        FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                
                             $Material = $form_post_data['Material_'.$entry_count];
                                $Uom_ID = $form_post_data['Uom_'.$entry_count];
                                $Quantity = $form_post_data['Quantitys_'.$entry_count];
                                $price_unit = $form_post_data['price_unit_'.$entry_count];
                                $Value = $form_post_data['Value_'.$entry_count];
                              
							  if(!empty($Quantity) && !empty($price_unit)){
                                $vals = "'" . $data . "'," .
        								"'" . $Material . "'," .
                                        "'" . $Uom_ID . "'," .
                                        "'" . $Quantity . "'," .
                                        "'" . $price_unit . "'," .
                                        "'" . $Value . "'" ;
  
                                 $sql2 = "INSERT INTO $proforma_add_invoice_tab
                                        ( 
										    `proforma_invoice_ID`, 
                                            `Material`, 
                                            `unit_ID`,
                                            `Quantity`,
                                            `price_unit`,
                                            `Value`
                                        ) 
                                VALUES ($vals)";
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
							  }
								
                            //increment here
                            
                            } 
					   }
							
							   // pdf start 
							
							$proforma_add_invoice_tab_tab = "SELECT $proforma_add_invoice_tab.ID,$proforma_add_invoice_tab.proforma_invoice_ID,$proforma_add_invoice_tab.Material,$proforma_add_invoice_tab.unit_ID,$proforma_add_invoice_tab.Quantity,$proforma_add_invoice_tab.price_unit,$proforma_add_invoice_tab.Value  FROM $proforma_add_invoice_tab,$proforma_invoice_tab where $proforma_invoice_tab .ID=$proforma_add_invoice_tab.proforma_invoice_ID AND $proforma_add_invoice_tab.proforma_invoice_ID = $data ";
                            $stmt =$this->db->prepare( $proforma_add_invoice_tab_tab);            
                            $stmt->execute();		
                            $proforma_add_invoice_tab_data = $stmt->fetchAll(2);
                            // var_dump($delDcproduct); 
                            $count = 1;
                            foreach($proforma_add_invoice_tab_data as $k=>$v){
                                $troww .=  '<tr>
                                <td style="width:50" align="left">'.$count.'</td>
                                <td style="width:251" align="left">'. $v['Material'].'</td>
                                <td style="width:67" align="center">'. $v['unit_ID'].'  </td>
                                <td style="width:99" align="right">'.$v["Quantity"].'</td>
                                <td style="width:99" align="right">'. $v["price_unit"].'</td>
								<td style="width:99" align="right">'. $v["Value"].'</td>
                                </tr>'; 
                                 $count++;
                            }
							
							/*for($i=1;$i<3;$i++){
                                $trowss.="<tr> <td></td> <td></td>"      
                                    . "<td></td>"
                                    . "<td></td>"
                                    . "<td></td>"
									. "<td></td>"
                                    . "</tr>";
                            }*/
								
							$companydetails = $this->crg->get('table_prefix') . "companydetails" ;
							$companydetails_data = "SELECT $companydetails.ID , $companydetails.Name, $companydetails.Bank, $companydetails.Branch, $companydetails.Account_Number, $companydetails.IFSC, $companydetails.Branch_code,$proforma_invoice_tab .GST_NO from $companydetails, $proforma_invoice_tab";		
                            $stmt =$this->db->prepare($companydetails_data);            
                            $stmt->execute();	;
                            $companydetails_fetch = $stmt->fetchAll(2);		
							
							$Name = $companydetails_fetch[0]['Name'];		
							$Bank = $companydetails_fetch[0]['Bank'];	
							$Branch = $companydetails_fetch[0]['Branch'];
							$Account_Number = $companydetails_fetch[0]['Account_Number'];
							$IFSC = $companydetails_fetch[0]['IFSC'];
							$Branch_code = $companydetails_fetch[0]['Branch_code'];										




							$cgstPre=strval($form_post_data['CGST']);
                            $sgstPre=strval($form_post_data['SGST']);
                            $igstPre=strval($form_post_data['IGST']);
                            $cgstAmount = strval((float) $form_post_data['CGSTtotal']);
                            $sgstAmount = strval((float) $form_post_data['SGSTtotal']);
                            $igstAmount = strval((float) $form_post_data['IGSTtotal']);
                            $netAmount = strval((float) $form_post_data['Balance']);
							$netamountcopy=$netAmount;
                            $BillAmount = strval((float) $form_post_data['Total']);
							$GST=$form_post_data['GST'];
							$GSTNO=$form_post_data['GSTNO'];
							$pono=$form_post_data['pono'];
							$Company=$form_post_data['Company'];
							$Permanent_address=$form_post_data['Permanent_address'];
							$Address=$form_post_data['Address'];
							$City=$form_post_data['City'];
							$StateID=$form_post_data['StateID'];
							$Pincode=$form_post_data['Pincode'];
							
							$ProformaNumber=$dbutil->keyGeneration('proforma_invoice','PFI','','proforma_invoice_ID');
						
    $decimal = round($netamountcopy - ($no = floor($netamountcopy)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');
    if($digits_length >= 10){
        echo "Sorry this does not support more than 99 crores";
    }else {
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $netamountcopy = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($netamountcopy) {
            $plural = (($counter = count($str)) && $netamountcopy > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($netamountcopy < 21) ? $words[$netamountcopy].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($netamountcopy / 10) * 10].' '.$words[$netamountcopy % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? "and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' paise' : '';
     $amountwrd=($Rupees ? ucFirst($Rupees) . 'rupee ' : '') . $paise;
    }
						


				 $html = <<<EOD
				 
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
		<tr><td width="665" align="center" style="font-size:150%;">	<b>PROFORMA INVOICE</b>		</td></tr>


  </table>      
  <table cellspacing="0" cellpadding="4" border="1">                          
                    <tr>
                    <td width="333">  Proforma Number:<b>$ProformaNumber</b> <br>  </td>
                    <td width="332" align="bottom">  Date : <b>$date</b>  <br></td>
                    </tr>
                    <tr>
                    <td width="333" > Name and Address of the Company: <b>  $Company </b> <br>
					<b>  $Permanent_address ,</b> 
					<b>  $Address ,</b> 
					<b>  $City ,</b>
					<b>  $StateID ,</b> 
					<b>  $Pincode </b> <br>
					GST No.:<b>$GSTNO</b></td>
					<td width="332" > PO Number: <b>  $pono </b> <br>
					PO Date:<b>$podate</b></td>
                    </tr>
    </table>
	
	 <table cellspacing="0" cellpadding="4"> 
		<tr>
		<td></td>
		</tr>
   </table>
   
			<table cellspacing="0" cellpadding="4" border="1">		
       <tr>
       <td width="50" align="center"><b>S.No</b></td>
       <td width="251" align="center"><b>Name of the Item </b></td>
       <td width="67" align="center"><b>UOM</b></td>
       <td width="99" align="center"> <b>Quantity</b></td>
       <td width="99" align="center"><b>Price/Unit</b></td>
	   <td width="99" align="center"><b>Total Price</b></td>
      </tr>
       
	    {$troww}
      
        <tr>
        <td width="467" ></td>
        <td width="99" align="right"><b>Total:</b> </td>
        <td width="99" align="right" ><b>$BillAmount</b> </td>
        </tr>     
        <tr>
        <td width="533" align="right">CGST: </td>
        <td width="66" align="right"> $cgstPre</td>
        <td width="66" align="right">$cgstAmount</td>
        </tr>
        <tr>
        <td width="533" align="right">SGST: </td>
        <td width="66" align="right">$sgstPre</td>
        <td width="66" align="right">$sgstAmount</td>
        </tr>
        <tr>
        <td width="533" align="right">IGST: </td>
        <td width="66" align="right">$igstPre</td>
        <td width="66" align="right">$igstAmount</td>
        </tr>
        <tr>
        <td width="599" align="right"><b>Final Amount</b></td>
        <td width="66" align="right"><b>$netAmount</b></td>
        </tr>            
       </table> 
				 
	   <table cellspacing="0" cellpadding="4" style="line-height: 0.9; font-size:110%;">
	   <tr>
	   <td width="100" align="left">Rupees in words</td>
	   <td width="30" align="right">:</td>
	   <td width="535" align="left">$amountwrd</td>
	   </tr>
	   <tr>
	   <td width="100" align="left">Payment Terms</td>
	   <td width="30" align="right">:</td>
	   <td width="535" align="left">$hai</td>
	   </tr>
	   
	    </table> 
		
		
	   
	   <table cellspacing="0" cellpadding="4" style="line-height: 0.8; font-size:120%;">	
		<tr>
       <td width="665" align="left"><b>Account Details</b></td>
		</tr>
       <tr>
       <td width="120" align="left">Name</td>
       <td width="30" align="center">:</td>
       <td width="515" align="left">$Name</td> 
      </tr>    
        <tr>
        <td width="120" align="left">Bank</td>
        <td width="30" align="center">:</td>
        <td width="515" align="left" >$Bank </td>
        </tr>     
        <tr>
        <td width="120" align="left">Branch </td>
        <td width="30" align="center"> :</td>
        <td width="515" align="left">$Branch</td>
        </tr>
        <tr>
        <td width="120" align="left">Account Number </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left">$Account_Number</td>
        </tr>
        <tr>
        <td width="120" align="left">IFSC </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left">$IFSC</td>
        </tr>
        <tr>
		<td width="120" align="left">Branch Code </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left"><b>$Branch_code</b></td>
        </tr> 
		<tr>
			<td width="665" align="left"><b>Created by</b></td>
		</tr>
       </table> 
       
        <table cellspacing="0" cellpadding="4" style="line-height: 0.8; font-size:110%;">
		 <tr>
	   <td width="120" align="left"><b>Special Conditions</b></td>
	   <td width="30" align="right"></td>
	   <td width="515" align="left">$Special</td>
	   </tr>
	   <tr>
			<td>*Subject to Puducherry Jurisdiction.</td>
	   </tr>
	   <tr>
			<td>*Goods once sold will not be taken back.</td>
	   </tr>
	   <tr>
			<td>*21% interest will be charged on over due bills according to the payment terms.</td>
	   </tr>
	   </table>
	   
	   
EOD;
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
$ht = ob_get_clean();
$pdf->writeHTMLCell(0, 0, '', '', $ht, 0, 1, 0, true, '', true);
$htl = ob_get_clean();
$htl='<table cellspacing="" cellpadding="1">
       <tr>
           <td>
            <p style="text-indent: 50px;">*Since this is an computer generated document, manual signature is not required.</p><p></p>
           </td>
       </tr> 
   </table>'
;
$pdf->writeHTMLCell(0, 0, '', '', $htl, 0, 1, 0, true, '', true); 
ob_end_clean();// at the end of your script
 $pdf->Output($_SERVER['DOCUMENT_ROOT'] ."/resource/proformapdf/Invoice".$data.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg1/resource/proformapdf/Invoice".$data.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg1/resource/proformapdf/Invoice".$lastInsertedID.".pdf", 'I');
$this->ses->set('pdffile', "/resource/proformapdf/Invoice".$data.".pdf");
								   
								   
								   
								   
								   
								   
								   // TCPDF END
							
							
							
							// pdf End					
							
                            //increment here
                          //  $entry_count++;
                         //   }
                       
                            $this->tpl->set('message', 'Proforma Invoice form edited successfully!');   
                            header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/proformaInvoice');
                            // $this->tpl->set('label', 'List');
                            // $this->tpl->set('content', $this->tpl->fetch('factory/template/form_button_link.php'));
                            } catch (Exception $exc) {
                             //edit failed option
                            $this->tpl->set('message', 'Failed to edit, try again!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/proforma_invoice.php'));
                            }

                    break;

                case 'addsubmit':
                     if (isset($crud_string)) {
                         
                        $form_post_data = $dbutil->arrFltr($_POST);
							include_once 'util/genUtil.php';
                         $util = new GenUtil();
                        	//echo '<pre>';
                       // print_r($form_post_data);
                        
                       
                       
                            if (isset($form_post_data['EnquiryID'])) {
								
                           
                                        $val = "'" . $form_post_data['EnquiryID'] . "'," .
												"'" . $form_post_data['GST_NO'] . "'," .
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

					$EnquiryID=$form_post_data['EnquiryID'];
					if($form_post_data['EnquiryID']){			   			   
                             $sql = "INSERT INTO `" . $this->crg->get('table_prefix') . "proforma_invoice`
                                            ( 
                                            `EnquiryID`,
											`GST_NO`,
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

						$sql_update="UPDATE $enquiry_tab SET Proforma_Status=2 WHERE ID=$EnquiryID";
                        $stmt1 = $this->db->prepare($sql_update);
						$stmt1->execute();
					}
                                  
                    if ($stmt->execute()) { 
						$lastInsertedID = $this->db->lastInsertId();
						
//pdf start						
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

$pdf->SetMargins(10, 3, 10);
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
//pdf end

					   $maxCount = $form_post_data['maxCount'];
					 //  echo '<pre>';
					//   print_r($_POST);
                        FOR ($entry_count=1; $entry_count <= $maxCount; $entry_count++) {
                                
                                $Material = $form_post_data['Material_'.$entry_count];
                                $Uom_ID = $form_post_data['Uom_'.$entry_count];
                                $Quantity = $form_post_data['Quantitys_'.$entry_count];
                                $price_unit = $form_post_data['price_unit_'.$entry_count];
                                $Value = $form_post_data['Value_'.$entry_count];
                              
                                if(!empty($Quantity) && !empty($price_unit)){
                                $vals = "'" . $lastInsertedID . "'," .
        								"'" . $Material . "'," .
                                        "'" . $Uom_ID . "'," .
                                        "'" . $Quantity . "'," .
                                        "'" . $price_unit . "'," .
                                        "'" . $Value . "'" ;
  
                                 $sql2 = "INSERT INTO $proforma_add_invoice_tab
                                        ( 
										    `proforma_invoice_ID`, 
                                            `Material`, 
                                            `unit_ID`,
                                            `Quantity`,
                                            `price_unit`,
                                            `Value`
                                        ) 
                                VALUES ($vals)";
                                $stmt = $this->db->prepare($sql2);
                                $stmt->execute();
								
							  }
                            
                            } 
                            
                  // pdf start 
							
							$proforma_add_invoice_tab_tab = "SELECT $proforma_add_invoice_tab.ID,$proforma_add_invoice_tab.proforma_invoice_ID,$proforma_add_invoice_tab.Material,$proforma_add_invoice_tab.unit_ID,$proforma_add_invoice_tab.Quantity,$proforma_add_invoice_tab.price_unit,$proforma_add_invoice_tab.Value  FROM $proforma_add_invoice_tab,$proforma_invoice_tab where $proforma_invoice_tab .ID=$proforma_add_invoice_tab.proforma_invoice_ID AND $proforma_add_invoice_tab.proforma_invoice_ID = $lastInsertedID ";
                            $stmt =$this->db->prepare( $proforma_add_invoice_tab_tab);            
                            $stmt->execute();		
                            $proforma_add_invoice_tab_data = $stmt->fetchAll(2);
                            // var_dump($delDcproduct); 
                            $count = 1;
                            foreach($proforma_add_invoice_tab_data as $k=>$v){
                                $troww .=  '<tr>
                                <td style="width:50" align="left">'.$count.'</td>
                                <td style="width:251" align="left">'. $v['Material'].'</td>
                                <td style="width:67" align="center">'. $v['unit_ID'].'  </td>
                                <td style="width:99" align="right">'.$v["Quantity"].'</td>
                                <td style="width:99" align="right">'. $v["price_unit"].'</td>
								<td style="width:99" align="right">'. $v["Value"].'</td>
                                </tr>'; 
                                 $count++;
                            }
							
							/*for($i=1;$i<3;$i++){
                                $trowss.="<tr> <td></td> <td></td>"      
                                    . "<td></td>"
                                    . "<td></td>"
                                    . "<td></td>"
									. "<td></td>"
                                    . "</tr>";
                            }*/
								
							$companydetails = $this->crg->get('table_prefix') . "companydetails" ;
							$companydetails_data = "SELECT $companydetails.ID , $companydetails.Name, $companydetails.Bank, $companydetails.Branch, $companydetails.Account_Number, $companydetails.IFSC, $companydetails.Branch_code,$proforma_invoice_tab .GST_NO from $companydetails, $proforma_invoice_tab";		
                            $stmt =$this->db->prepare($companydetails_data);            
                            $stmt->execute();	;
                            $companydetails_fetch = $stmt->fetchAll(2);		
							
							$Name = $companydetails_fetch[0]['Name'];		
							$Bank = $companydetails_fetch[0]['Bank'];	
							$Branch = $companydetails_fetch[0]['Branch'];
							$Account_Number = $companydetails_fetch[0]['Account_Number'];
							$IFSC = $companydetails_fetch[0]['IFSC'];
							$Branch_code = $companydetails_fetch[0]['Branch_code'];										




							$cgstPre=strval($form_post_data['CGST']);
                            $sgstPre=strval($form_post_data['SGST']);
                            $igstPre=strval($form_post_data['IGST']);
                            $cgstAmount = strval((float) $form_post_data['CGSTtotal']);
                            $sgstAmount = strval((float) $form_post_data['SGSTtotal']);
                            $igstAmount = strval((float) $form_post_data['IGSTtotal']);
                            $netAmount = strval((float) $form_post_data['Balance']);
							$netamountcopy=$netAmount;
                            $BillAmount = strval((float) $form_post_data['Total']);
							$GST=$form_post_data['GST'];
							$GSTNO=$form_post_data['GSTNO'];
							$pono=$form_post_data['pono'];
							$Company=$form_post_data['Company'];
							$Permanent_address=$form_post_data['Permanent_address'];
							$Address=$form_post_data['Address'];
							$City=$form_post_data['City'];
							$StateID=$form_post_data['StateID'];
							$Pincode=$form_post_data['Pincode'];
							
							$ProformaNumber=$dbutil->keyGeneration('proforma_invoice','PFI','','proforma_invoice_ID');
						
    $decimal = round($netamountcopy - ($no = floor($netamountcopy)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'one', 2 => 'two',
        3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
        7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve',
        13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
        16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
        19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty',
        70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
    $digits = array('', 'hundred','thousand','lakh', 'crore');
    if($digits_length >= 10){
        echo "Sorry this does not support more than 99 crores";
    }else {
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $netamountcopy = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($netamountcopy) {
            $plural = (($counter = count($str)) && $netamountcopy > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($netamountcopy < 21) ? $words[$netamountcopy].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($netamountcopy / 10) * 10].' '.$words[$netamountcopy % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal) ? "and " . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' paise' : '';
     $amountwrd=($Rupees ? ucFirst($Rupees) . 'rupee ' : '') . $paise;
    }
						


				 $html = <<<EOD
				 
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
		<tr><td width="665" align="center" style="font-size:150%;">	<b>PROFORMA INVOICE</b>		</td></tr>
  </table>  
  
  <table cellspacing="0" cellpadding="4" border="1">                          
                    <tr>
                    <td width="333">  Proforma Number:<b>$ProformaNumber</b> <br>  </td>
                    <td width="332" align="bottom">  Date : <b>$date</b>  <br></td>
                    </tr>
                    <tr>
                    <td width="333" > Name and Address of the Company: <b>  $Company </b> <br>
					<b>  $Permanent_address ,</b> 
					<b>  $Address ,</b> 
					<b>  $City ,</b>
					<b>  $StateID ,</b> 
					<b>  $Pincode </b> <br>
					GST No.:<b>$GSTNO</b></td>
					<td width="332" > PO Number: <b>  $pono </b> <br>
					PO Date:<b>$podate</b></td>
                    </tr>
    </table>
	
	 <table cellspacing="0" cellpadding="4"> 
		<tr>
		<td></td>
		</tr>
   </table>
   
	<table cellspacing="0" cellpadding="4" border="1">		
       <tr>
       <td width="50" align="center"><b>S.No</b></td>
       <td width="251" align="center"><b>Name of the Item </b></td>
       <td width="67" align="center"><b>UOM</b></td>
       <td width="99" align="center"> <b>Quantity</b></td>
       <td width="99" align="center"><b>Price/Unit</b></td>
	   <td width="99" align="center"><b>Total Price</b></td>
      </tr>
       
	    {$troww}
      
        <tr>
        <td width="467" ></td>
        <td width="99" align="right"><b>Total:</b> </td>
        <td width="99" align="right" ><b>$BillAmount</b> </td>
        </tr>     
        <tr>
        <td width="533" align="right">CGST: </td>
        <td width="66" align="right"> $cgstPre</td>
        <td width="66" align="right">$cgstAmount</td>
        </tr>
        <tr>
        <td width="533" align="right">SGST: </td>
        <td width="66" align="right">$sgstPre</td>
        <td width="66" align="right">$sgstAmount</td>
        </tr>
        <tr>
        <td width="533" align="right">IGST: </td>
        <td width="66" align="right">$igstPre</td>
        <td width="66" align="right">$igstAmount</td>
        </tr>
        <tr>
        <td width="599" align="right"><b>Final Amount</b></td>
        <td width="66" align="right"><b>$netAmount</b></td>
        </tr>            
       </table> 
				 
	   <table cellspacing="0" cellpadding="4" style="line-height: 0.9; font-size:110%;">
	   <tr>
	   <td width="100" align="left">Rupees in words</td>
	   <td width="30" align="right">:</td>
	   <td width="535" align="left">$amountwrd</td>
	   </tr>
	   <tr>
	   <td width="100" align="left">Payment Terms</td>
	   <td width="30" align="right">:</td>
	   <td width="535" align="left">$hai</td>
	   </tr>
	   
	    </table> 
		
		 
	   
	   <table cellspacing="0" cellpadding="4" style="line-height: 0.8; font-size:120%;">	
		<tr>
       <td width="665" align="left"><b>Account Details</b></td>
		</tr>
       <tr>
       <td width="120" align="left">Name</td>
       <td width="30" align="center">:</td>
       <td width="515" align="left">$Name</td> 
      </tr>    
        <tr>
        <td width="120" align="left">Bank</td>
        <td width="30" align="center">:</td>
        <td width="515" align="left" >$Bank </td>
        </tr>     
        <tr>
        <td width="120" align="left">Branch </td>
        <td width="30" align="center"> :</td>
        <td width="515" align="left">$Branch</td>
        </tr>
        <tr>
        <td width="120" align="left">Account Number </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left">$Account_Number</td>
        </tr>
        <tr>
        <td width="120" align="left">IFSC </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left">$IFSC</td>
        </tr>
        <tr>
		<td width="120" align="left">Branch Code </td>
        <td width="30" align="center">:</td>
        <td width="515" align="left"><b>$Branch_code</b></td>
        </tr> 
		<tr>
			<td width="665" align="left"><b>Created by</b></td>
		</tr>
       </table> 
       
       <table cellspacing="0" cellpadding="4" style="line-height: 0.8; font-size:110%;">
		 <tr>
	   <td width="120" align="left"><b>Special Conditions</b></td>
	   <td width="30" align="right"></td>
	   <td width="515" align="left">$Special</td>
	   </tr>
	   <tr>
			<td>*Subject to Puducherry Jurisdiction.</td>
	   </tr>
	   <tr>
			<td>*Goods once sold will not be taken back.</td>
	   </tr>
	   <tr>
			<td>*21% interest will be charged on over due bills according to the payment terms.</td>
	   </tr>
	   </table>
	   
	   
EOD;
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
$ht = ob_get_clean();
$pdf->writeHTMLCell(0, 0, '', '', $ht, 0, 1, 0, true, '', true);
$htl = ob_get_clean();
$htl='<table cellspacing="" cellpadding="1">
       <tr>
           <td>
            <p style="text-indent: 50px;">*Since this is an computer generated document, manual signature is not required.</p><p></p>
           </td>
       </tr> 
   </table>'
;
$pdf->writeHTMLCell(0, 0, '', '', $htl, 0, 1, 0, true, '', true); 
ob_end_clean();// at the end of your script
 $pdf->Output($_SERVER['DOCUMENT_ROOT'] ."/resource/proformapdf/Invoice".$lastInsertedID.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg1/resource/proformapdf/Invoice".$lastInsertedID.".pdf", "F");
//$pdf->Output("C:/xampp/htdocs/mfg1/resource/proformapdf/Invoice".$lastInsertedID.".pdf", 'I');
$this->ses->set('pdffile', "/resource/proformapdf/Invoice".$lastInsertedID.".pdf");
								   
								   // TCPDF END
							
							// pdf End							
                    }		
                        }
                        $this->tpl->set('mode', 'add');
                        $this->tpl->set('message', '- Success -');
                        header('Location:' . $this->crg->get('route')['base_path'] . '/sales/cst/proformaInvoice');
                       // $this->tpl->set('content', $this->tpl->fetch('factory/form/bomprocess_form.php'));
                     } else {
                            //edit option
                            //if submit failed to insert form
                            $this->tpl->set('message', 'Failed to submit!');
                            $this->tpl->set('FmData', $form_post_data);
                            $this->tpl->set('content', $this->tpl->fetch('factory/form/proforma_invoice.php'));
                     }
                    break;
                case 'add':
                    $this->tpl->set('mode', 'add');
	                $this->tpl->set('page_header', 'Production');
                    $this->tpl->set('content', $this->tpl->fetch('factory/form/proforma_invoice.php'));
                    break;

                default:
                    /*
                     * List form
                     */
                     
                    ////////////////////start//////////////////////////////////////////////
                    
           //bUILD SQL 
            $whereString = '';
            
         $colArr = array(
				"$proforma_invoice_tab.ID",
                "$enquiry_tab.EnquiryNo",
				"$enquiry_tab.PONo",
                "$customer_table.CompanyName",
				"$customer_table.PersonName"
				
            );
            
            $this->tpl->set('FmData', $_POST);
            foreach($_POST as $k=>$v){
                if(strpos($k,'^')){
                    unset($_POST[$k]);
                }
                $POST[str_replace('^','',$k)] = $v;
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
        		    $x = $dbutil->__mdsf($PD[str_replace('.','',$colNames)]);        		    
                }

                  if ('' != $x) {
                   $wsarr[] = $colNames . " LIKE '%" . $x . "%'";
                    }
                }
                
           IF (count($wsarr) >= 1) {
                $whereString = ' AND '. implode(' AND ', $wsarr);
            }
           } else {
             $whereString ="ORDER BY $proforma_invoice_tab.ID DESC";
           }
            
        $sql = "SELECT "
                    . implode(',',$colArr)
                    . " FROM $proforma_invoice_tab LEFT JOIN $enquiry_tab ON $enquiry_tab.ID=$proforma_invoice_tab.EnquiryID LEFT JOIN $customer_table ON $customer_table.ID = $enquiry_tab.customer_ID "
                    . " WHERE "
                    . " $proforma_invoice_tab.entity_ID = $entityID"
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
         
         
           $this->tpl->set('table_columns_label_arr', array('ID','EnquiryNo','Customer PO Number','Company Name','Customer Name'));
            
            /*
             * selectColArr for filter form
             */
            
            $this->tpl->set('selectColArr',$colArr);
            $this->tpl->set('dcpdf2','Generate Pdf');
                        
            /*
             * set pagination template
             */
            $this->crg->set('paginationListTemplate','factory/template/sql_based_crud_paginated_table.php');
                   
            //////////////////////close//////////////////////////////////////  
                     
                    include_once $this->tpl->path . '/factory/form/crud_proforma_invoice .php';
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
