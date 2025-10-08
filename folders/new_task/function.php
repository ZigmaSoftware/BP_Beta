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
                    if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
                    $image_view .= '<a href="javascript:print_view_1(\'image/'.$file_name.'\')"><img src="https://zigma.in/blue_planet_beta/uploads/'.$folder_name.'/image/'.$file_name.'"  height="110px" width="150px" ></a>';
                    }else{
                        $image_view .= '<a href="javascript:print_view_1(\'image/'.$file_name.'\')"><img src="https://zigma.in/blue_planet_beta/uploads/'.$folder_name.'/image/'.$file_name.'"  height="110px" width="150px" ></a>';
                    }
                }
                if($doc_name == 2){
                    if(($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')) {
                        $image_view .= '<a href="javascript:print_view_1(\'document/'.$file_name.'\')"><img src="https://zigma.in/blue_planet_beta/assets/images/excel.png"  height="50px" width="50px" ></a>';
                    }
                    else {
                        $image_view .= '<a href="javascript:print_view_1(\'document/'.$file_name.'\')"><img src="https://zigma.in/blue_planet_beta/assets/images/pdf.png"  height="50px" width="50px" ></a>';
                    }
                }
                if($doc_name == 3){
                    //if(($cfile_name[1]=='wav')||($cfile_name[1]=='mp3')) {


                    $image_view .= '<audio controls> <source src="https://zigma.in/blue_planet_beta/uploads/'.$folder_name.'/audio/'.$file_name.'" type="audio/ogg"></audio>';

                   
                    //}
                }
                if($doc_name == 4){
                    $image_view .= '<a href="#"><img src="'.$file_name.'"  height="50px" width="50px" ></a>';
                    
                  //   $image_view .= '<a href="javascript:print(\''.$file_name.'\')"><img src="https://thiranmigutiruppur/pgp_admin/assets/images/pdf.png"  height="50px" width="50px" ></a>';
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
                    $image_view .= '<a href="javascript:print_view(\'image/'.$file_name.'\')"><img src="https://zigma.in/blue_planet_beta/uploads/'.$folder_name.'/image/'.$file_name.'"  height="100%" width="100%" ></a>';
                    }else{
                        $image_view .= '<a href="javascript:print_view(\'image/'.$file_name.'\')"><img src="https://zigma.in/blue_planet_beta/uploads/'.$folder_name.'/image/'.$file_name.'"  height="100%" width="100%" ></a>';
                    }
                }
                if($doc_name == 2){
                    if(($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')) {
                        $image_view .= '<a href="javascript:print_view(\'document/'.$file_name.'\')"><img src="https://zigma.in/blue_planet_beta/assets/images/excel.png"  height="50px" width="50px" ></a>';
                    }
                    else {
                        $image_view .= '<a href="javascript:print_view(\''.$file_name.'\')"><img src="https://zigma.in/blue_planet_beta/assets/images/pdf.png"  height="50px" width="50px" ></a>';
                    }
                }
                if($doc_name == 3){
                    //if(($cfile_name[1]=='wav')||($cfile_name[1]=='mp3')) {


                    $image_view .= '<audio controls> <source src="https://zigma.in/blue_planet_beta/uploads/'.$folder_name.'/audio/'.$file_name.'" type="audio/ogg"></audio>';

                   
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




?>