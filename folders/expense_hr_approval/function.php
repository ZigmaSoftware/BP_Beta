<?php 

// function exp_view_function($folder_name = "", $staff_id = "", $call_amount="" , $expense_amount="",$date='', $entry_date="", $follow_id="", $follow_sub_id="",$staff_name="") {

//         $staff               = staff_name($staff_id);
//         $staff_designation   = $staff[0]['designation_unique_id'];
//         $designation_details = work_designation($staff_designation);
//         $designation         = disname($designation_details[0]['designation_type']);
//         $total_amount        = moneyFormatIndia($expense_amount + $call_amount);
//     $click_link = '<button type="button" class="btn btn-success btn-xs waves-effect waves-light mr-1" data-toggle="modal" data-staff_id="'.$staff_id.'"  data-entry_date="'.$entry_date.'" data-date="'.$date.'" data-staff_name="'.$staff_name.'" data-designation="'.$designation.'" data-total_amount="'.$total_amount.'"  data-call_amount ="'.moneyFormatIndia($call_amount).'"  data-expense_amount="'.moneyFormatIndia($expense_amount).'"  data-target="#expense_hr_approval-modal" ><i class="fas fa-money-bill-alt"></i></button>';

//     return $click_link;
// }

// function exp_main_view_function($folder_name = "", $unique_id = "" ) {
//     $click_link = '<button type="button" class="btn btn-success btn-xs waves-effect waves-light mr-1" data-toggle="modal" data-id="'.$unique_id.'"  data-target="#expense_user_approval_main-modal" ><i class="fas fa-money-bill-alt"></i></button>';

//     return $click_link;
// }

// function image_view($folder_name = "",$unique_id = "",$file_name) {
//   $file_names = explode(',', $file_name);
//     $image_view = '';

//     if($file_name){
//             foreach ($file_names as $file_key => $file_name) {
      
//                 if($file_key!=0){
//                     if($file_key%4!=0){
//                         $image_view .= "&nbsp";
//                     } else {
//                         $image_view .= "<br><br>";
//                     }
//                 }
       
//                 $cfile_name = explode('.',$file_name);

//                 if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')) {
//                     $image_view .= '<a href="javascript:print(\''.$file_name.'\')"><img src="uploads/'.$folder_name.'/'.$file_name.'"  height="50px" width="50px" ></a>';
//                 } else {
//                     $image_view .= '<a href="javascript:print(\''.$file_name.'\')"><button type="button" class="btn btn-pdf  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-36px mdi-file-pdf-outline" height="50px" width="50px"></i></button></a>';
//                 }
//             }
//     }
//        return $image_view;
// }


// function image_view_call($folder_name = "",$unique_id = "",$file_name) {
//   $file_names = explode(',', $file_name);
//     $image_view = '';

//     if($file_name){
//             foreach ($file_names as $file_key => $file_name) {
      
//                 if($file_key!=0){
//                     if($file_key%4!=0){
//                         $image_view .= "&nbsp";
//                     } else {
//                         $image_view .= "<br><br>";
//                     }
//                 }
       
//                 $cfile_name = explode('.',$file_name);

//                 if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
//                     $image_view .= '<a href="javascript:print_call(\''.$file_name.'\')"><img src="uploads/'.$folder_name.'/'.$file_name.'"  height="50px" width="50px" ></a>';
//                 } else {
//                     $image_view .= '<a href="javascript:print_call(\''.$file_name.'\')"><button type="button" class="btn btn-pdf  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-36px mdi-file-pdf-outline" height="50px" width="50px"></i></button></a>';
//                 }
//             }
//     }
//        return $image_view;
// }

function image_view($folder_name = "",$unique_id = "",$file_name) {
  $file_names = explode(',', $file_name);
    $image_view = '';

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

                if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
                    $image_view .= '<a href="javascript:print(\''.$file_name.'\')"><img src="uploads/'.$folder_name.'/'.$file_name.'"  height="50px" width="50px" ></a>';
                } else {
                    $image_view .= '<a href="javascript:print(\''.$file_name.'\')"><button type="button" class="btn btn-pdf  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-36px mdi-file-pdf-outline" height="50px" width="50px"></i></button></a>';
                }
            }
    }
       return $image_view;
}

?>