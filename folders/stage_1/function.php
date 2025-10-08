<?php
function image_view($folder_name,$unique_id,$file_name,$doc_name) {
  $file_names = explode(',', $file_name);
    //$image_view = '';

    if($file_name){
            foreach ($file_names as $file_key => $file_name) {
      
                if($file_key!=0){
                    if($file_key%4!=0){
                        $image_view .= "&nbsp";
                    } else {
                        $image_view .= "<br><br>"; 
                    }
                }
       
                $cfile_name = explode('.',$file_name);
                if($doc_name == 1){
                    if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')){
                    $image_view .= '<a href="javascript:print_view_1(\'image/'.$file_name.'\')"><img src="https://zigma.in/g_admin/uploads/'.$folder_name.'/image/'.$file_name.'"  height="50px" width="50px" ></a>';
                    }else{
                        $image_view .= '<a href="javascript:print_view_1(\'image/'.$file_name.'\')"><img src="https://zigma.in/g_admin/uploads/'.$folder_name.'/image/'.$file_name.'"  height="50px" width="50px" ></a>';
                    }
                }
                if($doc_name == 2){
                    if(($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')) {
                        $image_view .= '<a href="javascript:print_view_1(\'document/'.$file_name.'\')"><img src="https://zigma.in/g_admin/assets/images/excel.png"  height="50px" width="50px" ></a>';
                    }
                    else {
                        $image_view .= '<a href="javascript:print_view_1(\'document/'.$file_name.'\')"><img src="https://zigma.in/g_admin/assets/images/pdf.png"  height="50px" width="50px" ></a>';
                    }
                }
                
                

// if ($doc_name == 2) {
//     if (($cfile_name[1] == 'xls') || ($cfile_name[1] == 'xlsx')) {
//         $file_path = 'document/'.$file_name;
//     } else {
//         $file_path = $file_name;
//     }
    
//     // Check if the file exists
//     if (file_exists($file_path)) {
//         // Set headers for file download
//         header('Content-Description: File Transfer');
//         header('Content-Type: application/octet-stream');
//         header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
//         header('Expires: 0');
//         header('Cache-Control: must-revalidate');
//         header('Pragma: public');
//         header('Content-Length: ' . filesize($file_path));
//         readfile($file_path); // Output the file
//         exit;
//     } else {
//         // File not found, handle accordingly
//         echo "File not found";
//     }
// }
                if($doc_name == 3){
                    //if(($cfile_name[1]=='wav')||($cfile_name[1]=='mp3')) {


                    $image_view .= '<audio controls> <source src="https://zigma.in/g_admin/uploads/'.$folder_name.'/audio/'.$file_name.'" type="audio/ogg"></audio>';

                   
                    //}
                }
                if($doc_name == 4){
                    $image_view .= '<a href="#"><img src="'.$file_name.'"  height="50px" width="50px" ></a>';
                    
                  //   $image_view .= '<a href="javascript:print(\''.$file_name.'\')"><img src="https://thiranmigutiruppur.com/pgp_admin/assets/images/pdf.png"  height="50px" width="50px" ></a>';
                }
            }
    }
       return $image_view;
}

function image_view_1($folder_name,$unique_id,$file_name,$doc_name) {
  $file_names = explode(',', $file_name);
    //$image_view = '';

    if($file_name){
            foreach ($file_names as $file_key => $file_name) {
      
                if($file_key!=0){
                    if($file_key%4!=0){
                        $image_view .= "&nbsp";
                    } else {
                        $image_view .= "<br><br>"; 
                    }
                }
       
                $cfile_name = explode('.',$file_name);
                if($doc_name == 1){
                    if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')){
                    $image_view .= '<a href="javascript:print_view(\'image/'.$file_name.'\')"><img src="https://zigma.in/g_admin/uploads/'.$folder_name.'/image/'.$file_name.'"  height="50px" width="50px" ></a>';
                    }else{
                        $image_view .= '<a href="javascript:print_view(\'image/'.$file_name.'\')"><img src="https://zigma.in/g_admin/uploads/'.$folder_name.'/image/'.$file_name.'"  height="50px" width="50px" ></a>';
                    }
                }
                if($doc_name == 2){
                    if(($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')) {
                        $image_view .= '<a href="javascript:print_view(\'document/'.$file_name.'\')"><img src="https://zigma.in/g_admin/assets/images/excel.png"  height="50px" width="50px" ></a>';
                    }
                    else {
                        $image_view .= '<a href="javascript:print_view(\'document/'.$file_name.'\')"><img src="https://zigma.in/g_admin/assets/images/pdf.png"  height="50px" width="50px" ></a>';
                    }
                }
                
                

// if ($doc_name == 2) {
//     if (($cfile_name[1] == 'xls') || ($cfile_name[1] == 'xlsx')) {
//         $file_path = 'document/'.$file_name;
//     } else {
//         $file_path = $file_name;
//     }
    
//     // Check if the file exists
//     if (file_exists($file_path)) {
//         // Set headers for file download
//         header('Content-Description: File Transfer');
//         header('Content-Type: application/octet-stream');
//         header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
//         header('Expires: 0');
//         header('Cache-Control: must-revalidate');
//         header('Pragma: public');
//         header('Content-Length: ' . filesize($file_path));
//         readfile($file_path); // Output the file
//         exit;
//     } else {
//         // File not found, handle accordingly
//         echo "File not found";
//     }
// }
                if($doc_name == 3){
                    //if(($cfile_name[1]=='wav')||($cfile_name[1]=='mp3')) {


                    $image_view .= '<audio controls> <source src="https://zigma.in/g_admin/uploads/'.$folder_name.'/audio/'.$file_name.'" type="audio/ogg"></audio>';

                   
                    //}
                }
                if($doc_name == 4){
                    $image_view .= '<a href="#"><img src="'.$file_name.'"  height="50px" width="50px" ></a>';
                    
                  //   $image_view .= '<a href="javascript:print(\''.$file_name.'\')"><img src="https://thiranmigutiruppur.com/pgp_admin/assets/images/pdf.png"  height="50px" width="50px" ></a>';
                }
            }
    }
       return $image_view;
}

function image_view_print($folder_name,$unique_id,$file_name,$doc_name) {


  $file_names = explode(',', $file_name);
    //$image_view = '';

    if($file_name){
            foreach ($file_names as $file_key => $file_name) {
      
                if($file_key!=0){
                    if($file_key%4!=0){
                        $image_view .= "&nbsp";
                    } else {
                        $image_view .= "<br><br>"; 
                    }
                }
       
                $cfile_name = explode('.',$file_name);
                if($doc_name == 1){
                    if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
                    $image_view .= '<a href="javascript:print_view(\'image/'.$file_name.'\')"><img src=https://zigma.in/g_admin/uploads/'.$folder_name.'/image/'.$file_name.'"  height="100%" width="100%" ></a>';
                    }else{
                        $image_view .= '<a href="javascript:print_view(\'image/'.$file_name.'\')"><img src="https://zigma.in/g_admin/uploads/'.$folder_name.'/image/'.$file_name.'"  height="100%" width="100%" ></a>';
                    }
                }
                

                if($doc_name == 3){
                    //if(($cfile_name[1]=='wav')||($cfile_name[1]=='mp3')) {


                    $image_view .= '<audio controls> <source src="https://zigma.in/g_admin/uploads/'.$folder_name.'/audio/'.$file_name.'" type="audio/ogg"></audio>';

                   
                   // }
                }
                if($doc_name == 4){
                    $image_view .= '<a href="#"><img src="'.$file_name.'"  height="50px" width="50px" ></a>';
                    
                  //   $image_view .= '<a href="javascript:print(\''.$file_name.'\')"><img src="https://thiranmigutiruppur.com/pgp_admin/assets/images/pdf.png"  height="50px" width="50px" ></a>';
                }
            }
    }
       return $image_view;
}

function get_level_priodic($user_id){
    
    global $pdo;

    $table_name    = "periodic_creation_sub";
    $where         = [];
    $table_columns = [
        "level",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND user_id ='".$user_id."' and form_unique_id != ''";

    $cnt_status = $pdo->select($table_details, $where);
       //print_r($cnt_status); 
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['level'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function get_dept_priodic($user_id){
    
    global $pdo;

    $table_name    = "periodic_creation_sub";
    $where         = [];
    $table_columns = [
        "GROUP_CONCAT(department_name SEPARATOR ', ') as department_name",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND user_id ='".$user_id."' and form_unique_id != ''";

    $cnt_status = $pdo->select($table_details, $where);
      // print_r($cnt_status); 
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['department_name'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function get_site_priodic($user_id){
     
    global $pdo;

    $table_name    = "periodic_creation_sub";
    $where         = [];
    $table_columns = [
        "GROUP_CONCAT(site_id SEPARATOR ', ') as site_id",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND user_id ='".$user_id."' and form_unique_id != ''";

    $cnt_status = $pdo->select($table_details, $where);
        
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['site_id'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


?>