<?php


// function category_name($unique_id = "",$department_name = "",$main_category = "") {
// alert("hii");
//     global $pdo;

//     $table_name    = "category_creation";
//     $where         = [];
//     $table_columns = [
//         "unique_id",
//         "category_name"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

//     $where     =  "is_active= 1 and is_delete = 0";
    

//     if ($unique_id) {

       
//          $where .= " and unique_id = '".$unique_id."' ";
//     }
//     if ($department_name) {
//          $where .= " and department = '".$department_name."' ";
        
//     }
//         if ($main_category) {
//          $where .= " and  main_category_name = '".$main_category."' ";
        
//     }
//     $category_name = $pdo->select($table_details, $where);
//     //print_r($category_name);
//     if ($category_name->status) {
//         return $category_name->data;
//     } else {
//         print_r($category_name);
//         return 0;
//     }
// }


// function department($unique_id = "") {

//     global $pdo;

//     $table_name    = "department_creation";
//     $where         = [];
//     $table_columns = [
//         "unique_id",
//         "department"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

//     $where     = [
//         "is_active" => 1,
//         "is_delete" => 0
//     ];

//     if ($unique_id) {

//         $where              = [];
//         $where["unique_id"] = $unique_id;
//     }

//     $department = $pdo->select($table_details, $where);
//   // print_r($depatment_name);
//     if ($department->status) {
//         return $department->data;
//     } else {
//         print_r($department);
//         return 0;
//     }
// }


?>