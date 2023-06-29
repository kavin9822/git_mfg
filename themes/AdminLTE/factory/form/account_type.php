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

        $DrCr_option_data = array(
            'Dr' => 'Debit',
            'Cr' => 'Credit'
        );

        $PostingReq_option_data = array(
            'Y' => 'Yes',
            'N' => 'No',
        );      
        

        $FormConfig = array(
            /*
             * 
             * Do not use prefix in table name
             * Example in ycs_user
             * enter as user
             * 
             * 'database_table_name' =>'user',
             */

            'table_name' => 'accounttype',
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
            
            'unique_key_prefix'=> 'ACT',
            
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
            'page_title' => 'Static Data',
            /*
             * Can also be the tite of the List data table
             */
            'form_title' => 'Account Type',
            /*
             * Set to default FALSE, passed as parameter to this static method
             */
            'filter_by_user_id' => FALSE,
            /*
             * set to TRUE by default
             */
            'filter_by_entity_id' => FALSE,
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
                    'label' => 'AccountType ID'
                ),
		'users_ID' => array(
                    'type' => 'hidden',
                    'value' => $ses->get('user')['ID'],
                    'label' => 'User ID'
                ),                                       
               'AccountType' => array(
                    'filter' => TRUE,                    
                    'type' => 'text',
                    'value' => $form_data['AccountType'],
                    'label' => 'Account Type'
                ),                             
                'DrCr' => array(
                    'filter' => TRUE,
                    'type' => 'select',
                    'value' => $form_data['DrCr'],
                    'label' => 'Debit Credit',
                    'options' => $DrCr_option_data
                ),     
                'Description' => array(
                    'type' => 'textarea',
                    'value' => $form_data['Description'],
                    'label' => 'Description'
                ),                
                'PostingReq' => array(
                    'filter' => TRUE,
                    'type' => 'select',                    
                    'value' => $form_data['PostingReq'],
                    'label' => 'Posting Required', 
                    'options' => $PostingReq_option_data
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