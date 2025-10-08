<?php

function staff_name_loan ($unique_id = "") {
    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name",
        "employee_id",
        "office_contact_no",
        "designation_unique_id"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $staff_name_list = $pdo->select($table_details, $where);

    if ($staff_name_list->status) {
        return $staff_name_list->data;
    } else {
        print_r($staff_name_list);
        return 0;
    }
}

function select_option_staff($options = [],$description = "", $is_selected = [],$is_disabled = []) {
    
    $option_str     = "<option value='' disabled>No Options to Select</option>";

    $data_attribute = "";

    if ($options) {

        $option_str     = "<option value=''>Select</option>";

        if ($description) {
            $option_str     = "<option value='' selected>".$description."</option>";
        }
        foreach ($options as $key => $value) {

            $value      = array_values($value);
            $selected   = "";
            $disabled   = "";

            if (is_array($is_selected) AND in_array($value[0],$is_selected)) {            
                $selected = " selected='selected' ";
            } elseif ($is_selected == $value[0]) {
                
                $selected = " selected='selected' ";
            }
            
            if (is_array($is_disabled) AND in_array($value[0],$is_disabled)) {            
                $disabled = " disabled='disabled' ";
            } elseif ($is_disabled == $value[0]) {
                $disabled = " disabled='disabled' ";
            }

            if (strpos($value[1],"_")) {
                $value[1] = disname($value[1]);
            } else {
                $value[1] = ucfirst($value[1]);
            }

            if (isset($value[2])) {
                $data_attribute = "data-extra='".$value[2]."'";
            } 

            if (isset($value[3])) {
                $data_attribute = "data-extra='".$value[2]."'";
            } 

            $option_str .= "<option value='".$value[0]."'".$data_attribute.$selected.$disabled.">".$value[1]."-".$value[2]."-".$value[3]."</option>";
        }
    }
    
    return $option_str;
}

function get_loan_no_fun($staff_name = ""){

        global $pdo;

    $table_name    = "loan_advance";
    $where         = [];
    $table_columns = [
        "unique_id",
        "loan_no",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where          = "is_active = 1 AND is_delete = 0 AND staff_id = '".$staff_name."' AND loan_type != 3";
    
    $loan_no_list = $pdo->select($table_details, $where);
   
    if ($loan_no_list->status) {
        return $loan_no_list->data;
    } else {
        print_r($loan_no_list);
        return 0;
    }
}
?>