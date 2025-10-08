<?php
    function btn_ho_approve_status_leave($folder_name = "",$unique_id = "",$approved_status = "",$leads_approval = "") {

        $final_str = "";

        if((($approved_status=='0')&&($leads_approval == 0))||(($approved_status=='0')&&($leads_approval == 1))) {
            $final_str = '<button type="button" class="btn btn-warning  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation-thick"></i></button>';
        } else if(($approved_status=='1')||(($approved_status == 0)&&($leads_approval == 2))) {
            $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button>';
        }
        else if($approved_status=='2') {
            $final_str = '<button type="button"  class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check-all"></i></button>';
        }
        else if($approved_status=='3') {
            $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button>';
        }
        
        return $final_str;
    }

    function btn_ceo_approve_status_leave($folder_name = "",$unique_id = "",$approved_status = "",$ho_approved = "") {

        $final_str = "";

        if(($ho_approved == 1)||($ho_approved == 0)){
            if($approved_status=='0') {
                $final_str = '<button type="button" class="btn btn-warning  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation-thick"></i></button>';
            } else if($approved_status=='1') {
                $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button>';
            }
            else if($approved_status=='2') {
                $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button>';
            }
        }else{
            $final_str = "-"; 
        }
        
        return $final_str;
    }

    function btn_hr_approve_status_leave($folder_name = "",$unique_id = "",$approved_status = "",$ceo_approved = "",$ho_approved = "") {

        $final_str = "";

        if($ho_approved != 3){
            if($approved_status=='0') {
                $final_str = '<button type="button" class="btn btn-warning  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation-thick"></i></button>';
            } else if($approved_status=='1') {
                $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button>';
            }
            else if($approved_status=='2') {
                $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button>';
            }
        }else{
            $final_str = "-"; 
        }
        
        return $final_str;
    }
?>