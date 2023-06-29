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
        
        $machmasstat_table = $crg->get('table_prefix') . 'machinemaster';            
        $sql = "SELECT `ID`,`MachineName` FROM `$machmasstat_table`";           
        $machmas_status_sel = $dbutil->getSqlData($sql,12);

        $unistat_table = $crg->get('table_prefix') .'unit';            
        $sql = "SELECT `ID`,`UnitName` FROM `$unistat_table`";           
        $uni_status_sel = $dbutil->getSqlData($sql,12);
       
        $critical_table = $crg->get('table_prefix') .'CriticalSpare';            
        $sql = "SELECT `ID`,`SpareName` FROM `$critical_table`";           
        $critical_data= $dbutil->getSqlData($sql,12);
        
        $emp_table = $crg->get('table_prefix') .'employee';            
        $sql = "SELECT `ID`,`EmpName` FROM `$emp_table`";           
        $emp_data= $dbutil->getSqlData($sql,12);
        
        //  if (isset($form_data['RegisterDate'])) {
        //           $form_data['RegisterDate']=date('d-m-Y',strtotime($form_data['RegisterDate']));
        //       } else {
        //           $form_data['RegisterDate']=date('d-m-Y');
        //       } 

        $FormConfig = array(
            /*
             * 
             * Do not use prefix in table name
             * Example in ycs_user
             * enter as user
             * 
             * 'database_table_name' =>'user',
             */

            'table_name' => 'CriticalSpareStock',
            /*
             * Primary key ID column required in the pagination list
             * 
             */
            'ID_column_required' => FALSE,
            /*
             * If u want uniqe key then set the following 2 variables
             * used in creating unique key
             * 
             * 'unique_key_required'=> FALSE,
             * for primary key Id - is autoincriment - number
             */
            'unique_key_required'=> FALSE,
            
            /*
             * Based on a colum name first three letters
             */
            
            'unique_key_prefix'=> 'PRD',
            
            /*
             * Unique key suffix
             * get unique key sufix from value filled in a field
             * or colun name
             */
            'unique_key_suffix_from_column_name' => '',
            
            /*
             * Unique key column
             */
            'uniq_key_col' => 'ItemCode',
            /*
             * Form limit 
             * Number of form elemens per column
             */
            'max_number_form_elements_per_colum' => 12,
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
            'Max_number_columns_in_data_grid' => 12,
            /*
             * Title of the Page
             */
            'page_title' => 'Static Data',
            /*
             * Can also be the tite of the List data table
             */
            'form_title' => 'CriticalSpare Stock',
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
                    'label' => 'CriticalSpareStock ID'
                ),
                 'machine_ID' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['machine_ID'],
                    'label' => 'Machine',
                    'options' => $machmas_status_sel,
                    'status'=>'required'
                
                ),
                 'criticalparts_ID' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['criticalparts_ID'],
                    'label' => 'CriticalParts',
                   'options'=>$critical_data,
                   'status'=>'required'
                   
                ),
                 'Currentstock' => array(
                    'filter' => TRUE,
                    'type' => 'text',
                    'value' => $form_data['Currentstock'],
                    'label' => 'Current Stock',
                    'status'=>'onkeypress="return currentstock(this)"required'
                ),
                'RegisterDate' => array(
                    'filter' => TRUE,
                    'type' => 'text',
                    'value' => $form_data['RegisterDate'],
                    'label' => 'Date',
                    //'status' =>'data-provide="datetimepicker" data-date-format="YYYY-MM-DD" onclick="ycsdatetime(this.id)"'
                      'status'=>'data-date-format="YYYY-MM-DD" onmouseover="datepicker(this.id)"'
                ),
                'approvedby_ID' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['approvedby_ID'],
                    'label' => 'Approved By',
                   'options'=> $emp_data,
                   'status'=>'required'
                     
                ),
               'entity_ID' => array(
                    'type' => 'hidden',
                    'value' => $ses->get('user')['entity_ID'],
                    'label' => 'Entity'                    
                ),
		        'users_ID' => array(
                    'type' => 'hidden',
                    'value' => $ses->get('user')['ID'],
                    'label' => 'User ID'
                )
                
            ),
            'form_footer' => array(
                'clear' => array(
                    'type' => 'reset',
                    'label' => 'Clear'
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
