<?php
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