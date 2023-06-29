<?php

/*
 * @author psmahadevan
 * Note Entity_id and user id - are filter by default so u cant add to pagination list
 */

class Form_Elements {


    public static function data($reg, $form_data = NULL) {

        /*
         * Option data if applicable
         * example
         * $option_data['State']
         * Require key value pair
         * data can be taken even from database
         */

        $crg = $reg;
        $ses = $reg->get('ses');
        $db = $reg->get('db');
        
        include_once 'util/DBUTIL.php';
        $dbutil = new DBUTIL($crg);

        $status_option_data =  array(
            'Open'=>'Open',
            'In Progress'=> 'In Progress',
            'Closed'=> 'Closed',
        );      
        

	    $actual_table_name = $crg->get('table_prefix') . 'customer';
            $entityID = $ses->get('user')['entity_ID'];
            $sql = "SELECT `ID`, `FirstName` FROM `$actual_table_name` WHERE `entity_ID` = $entityID && `customertype_ID`=1";
            $customer_ID_sel = $dbutil->getSqlData($sql, 12);

	    $actual_table_name_emp = $crg->get('table_prefix') . 'employee';
            //same as above so no need, it is entity id from login user session
            //$entityID = $ses->get('user')['entity_ID'];
            $sqlemp = "SELECT `ID`, `Name` FROM `$actual_table_name_emp` WHERE `entity_ID` = $entityID";
            $employee_ID_sel = $dbutil->getSqlData($sqlemp, 12);                  

        $FormConfig = array(
            /*
             * 
             * Do not use prefix in table name
             * Example in ycs_user
             * enter as user
             * 
             * 'database_table_name' =>'user',
             */

            'table_name' => 'service',
            /*
             * Primary key ID column required in the pagination list
             * 
             */
            'ID_column_required' => TRUE,
            /*
             * If u want uniqe key then set the following 2 variables
             * used in creating unique key
             * 
             * 'unique_key_required'=> FALSE,
             * for primary key Id - is autoincriment - number
             */
            'unique_key_required'=> TRUE,
            /*
             * Based on a colum name first three letters
             */
            
            'unique_key_prefix'=> 'SER',
            
            /*
             * Unique key suffix
             * get unique key sufix from value filled in a field
             * or colun name
             */
            'unique_key_suffix_from_column_name' => 'entity_ID',
            /*
             * Form limit 
             * Number of form elemens per column
             */
            'max_number_form_elements_per_colum' => 14,
            /*
             * Max number of rows in crud2
             * we will do this in next version
             */
            //'List_max_number_rows' => 10,


            /*
             * Max number of columns in list data grid in crud class
             * eventough this class is no called in list data table 
             * but it is available default in the crud class
             * 
             * how to give value to this variable
             * 
             * Number of elements in the form content array ('form_content') is the max limit
             * 
             * Count number of element u use in  
             * form content array used bellow
             * 
             * give equal or less that that
             */
            'Max_number_columns_in_data_grid' => 13,
            /*
             * Title of the Page
             */
            'page_title' => 'Individual Entry Forms',
            /*
             * Can also be the tite of the List data table
             */
            'form_title' => 'Initial Installation',
            /*
             * Set to default FALSE, passed as parameter to this static method
             */
            'filter_by_user_id' => FALSE,
            /*
             * set to TRUE by default
             */
            'filter_by_entity_id' => TRUE,
            /*
             * Do the form have file upload
             * set to TRUE/False by default
             */
            'Form_Need_to_upload_file' => FALSE,
            /*
             * Form content start here
             * 'form_content'
             */
            'form_content' => array(
                'ID' => array(           
                   'filter' => TRUE,        
                    'type' => 'hidden',
                    'value' => $form_data['ID'],
                    'label' => 'Service ID'
                ),
                'entity_ID' => array(
                    'type' => 'hidden',
                    'value' => $ses->get('user')['entity_ID'],
                    'label' => 'Entity ID'
                ),                
		'users_ID' => array(
                    'type' => 'hidden',
                    'value' => $ses->get('user')['ID'],
                    'label' => 'User ID'
                ),
                'customer_ID' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['customer_ID'],
                    'label' => 'Customer Name',
                    'options' => $customer_ID_sel,
                    'status' => 'onchange = "custChg('."'".$crg->get('route')['base_path']."','customer_ID'".')"' 
		),
		'employee_ID' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['employee_ID'],
                    'label' => 'Employee Name',
                    'options' => $employee_ID_sel
		), 
		'ApIp' => array(
                    'type' => 'text',
                    'value' => $form_data['ApIp'],
                    'label' => 'AP IP'
                ),             
                'EquipIp' => array(
                    'filter' => TRUE,
                    'type' => 'text',
                    'value' => $form_data['EquipIp'],
                    'label' => 'Client Equipment IP'
                ),                
                'InstallationDate' => array(
                    'type' => 'date',
                    'value' => $form_data['InstallationDate'],
                    'status' => 'data-provide="datepicker" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" onclick="ycsdate()"',
                    'label' => 'Installation Date'
                ),
                'Status' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['Status'],
                    'label' => 'Status',
                    'status' => '',
                    'options' => $status_option_data
                )
                
            ),
            'form_footer' => array(		
                'Ping' => array(
                    'type' => 'button',
                    'status' => 'onclick = "pingIp('."'".$crg->get('route')['base_path']."','ApIp'".')"',
                    'label' => 'AP IP Check'
                ),
                'Ping2' => array(
                    'type' => 'button',
                    'status' => 'onclick = "pingIp('."'".$crg->get('route')['base_path']."','EquipIp'".')"',
                    'label' => 'Equipment IP Check'
                ),
                'submit_button' => array(
                    'type' => 'submit',
                    'label' => 'Submit'
                ),
                'list' => array(
                    'type' => 'link',
                    'label' => 'List'
                )
            )
        );
        /////////////////////

        return $FormConfig;
    }

}
