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

        $states_option_data = array(
            'TN' => 'Tamil Nadu',
            'PY' => 'Pondicherry'
        );

        $cont_option_data = array(
            'IN' => 'India',
        );  
        
        $payment_type_sel = array(
            'E' => 'Employee',
            'NE' => 'Non-Employee'
        );    
        
            $actual_table_name_emp = $crg->get('table_prefix') . 'employee';            
            $entityID = $ses->get('user')['entity_ID'];
            $sqlemp = "SELECT `ID`, `Name` FROM `$actual_table_name_emp` WHERE `entity_ID` = $entityID";
            $employee_ID_sel = $dbutil->getSqlData($sqlemp, 12);
        
            $actual_table_name= $crg->get('table_prefix') . 'expense';
            $sql = "SELECT `ID`, `ExpenseType` FROM `$actual_table_name` WHERE `entity_ID` = $entityID";
            $expense_ID_sel = $dbutil->getSqlData($sql, 12);
 

       //to retrive the payment mode from table and temprovarily stored here
        if (!$ses->get('payment_mode_sel')) {
            $payment_mode_sel = $dbutil->selectKeyVal('paymentmode','ID','Paymode');
            $ses->set('payment_mode_sel', $payment_mode_sel);
        } else {
            $payment_mode_sel = $ses->get('payment_mode_sel');
        }
        

        $FormConfig = array(
            /*
             * 
             * Do not use prefix in table name
             * Example in ycs_user
             * enter as user
             * 
             * 'database_table_name' =>'user',
             */

            'table_name' => 'payment',
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
            'unique_key_required'=> FALSE,
            /*
             * Based on a colum name first three letters
             */
            
            'unique_key_prefix'=> 'PAY',
            
            /*
             * Unique key suffix
             * get unique key sufix from value filled in a field
             * or colun name
             */
            'unique_key_suffix_from_column_name' => 'Country',
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
            'page_title' => 'Entry Forms',
            /*
             * Can also be the tite of the List data table
             */
            'form_title' => 'Payment',
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
                    'label' => 'Voucher No.'
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
                'PaymentType' => array(
                    'type' => 'select',
                    'value' => $form_data['PaymentType'],
                    'label' => 'Payment Type',
                    'options' => $payment_type_sel,
                    'status' => 'onchange = hideEmp()'
                ),                 
                'employee_ID' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['employee_ID'],
                    'label' => 'Employee ID',
                    'options' => $employee_ID_sel
                ),
                'NonEmployee' => array(
                    'filter' => TRUE,
                    'type' => 'text',
                    'value' => $form_data['NonEmployee'],
                    'label' => 'Non Employee'
                ),  
                'ExpenseType' => array(
                    'type' => 'select',
                    'value' => $form_data['ExpenseType'],
                    'label' => 'Expense Type',
                    'options' => $expense_ID_sel
                ),
                'Amount' => array(
                    'type' => 'text',
                    'value' => $form_data['Amount'],
                    'label' => 'Amount'
                ),                
                'PaymentMode' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['PaymentMode'],
                    'label' => 'Payment Mode',
                    'options' => $payment_mode_sel
                ),
                'PaymentDate' => array(
                    'filter' => TRUE,
                    'type' => 'text',
                    'value' => $form_data['PaymentDate'],
                    'status' => 'data-provide="datepicker" data-date-format="dd-mm-yyyy" onclick="ycsdate()"',
                    'label' => 'Payment Date'
                ),
                'Remark' => array(
                    'filter' => TRUE,
                    'type' => 'text',
                    'value' => $form_data['Remark'],
                    'status' => '',
                    'label' => 'Remark'
                )
            ),
            'form_footer'=> array(
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