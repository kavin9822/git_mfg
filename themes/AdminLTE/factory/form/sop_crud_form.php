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

        $supplier_table = $crg->get('table_prefix') . 'supplier';
        $sql_supplier = "SELECT `ID`,`Company` FROM `$supplier_table`";
        $supplier_select = $dbutil->getSqlData($sql_supplier, 12);

        $FormConfig = array(
            /*
             * 
             * Do not use prefix in table name
             * Example in ycs_user
             * enter as user
             * 
             * 'database_table_name' =>'user',
             */

            'table_name' => 'SOP',
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
            
           // 'unique_key_prefix'=> 'po',
            
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
            'page_title' => 'SOP',
            /*
             * Can also be the tite of the List data table
             */
            'form_title' => 'SOP Master',
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
             
             'Custom_crud_paginated_table_complete_path'=>'factory/template/application/crud_paginated_po_with_Del_view.php',
             
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
                    'label' => 'Purchase Entry ID'
                ), 
                'entity_ID' => array(
                    'type' => 'hidden',
                    'value' => $ses->get('user')['entity_ID'],
                    'label' => 'Entity'                    
                ),
		        'users_ID' => array(
                    'type' => 'hidden',
                    'value' => $form_data['users_ID'],
                ),
                'product_ID' => array(
                    'type' => 'text',
                    'value' => $form_data['product_ID'],
                    'label' => 'Product ID'
                ),
                'sopmaster_ID' => array(
                    'filter' => TRUE,
                    'type' => 'text',
                    'value' => $form_data['sopmaster_ID'],
                    'label' => 'Batch No'
                ),
                'AuditDateTime' => array(
                    'filter' => TRUE,
                    'type' => 'text',
                    'value' => $form_data['AuditDateTime'],
                    'label' => 'Audit Date Time'
                ),
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
