<?php

function designation_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "designation_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " designation LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}

// function image_view($folder_name = "",$unique_id = "",$doc_name) {
//   $file_names = explode(',', $doc_name);
//     $image_view = '';

//     if($doc_name){
//             foreach ($file_names as $file_key => $doc_name) { 
      
//                 if($file_key!=0){
//                     if($file_key%4!=0){
//                         $image_view .= "&nbsp";
//                     } else {
//                         $image_view .= "<br><br>"; 
//                     }
//                 }
       
//                 $cfile_name = explode('.',$doc_name);
//                 if($doc_name==1){
//                     if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
//                     $image_view .= '<a href="javascript:print(\'uploads/q_doc/'.$doc_name.'\')"><img src="192.168.0.113/aed_erp/uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" ></a>';
//                     }
//                 }
               
//             }
//     }
//        return $image_view;
// }
?>