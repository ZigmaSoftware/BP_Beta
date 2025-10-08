<?php


// Permission UI Create Function

function user_permission_ui($main_screen_unique_id = "5f6203173b9d035003") {

    $total_ui = '';

    if ($main_screen_unique_id) {
        $screen_sections = section_name('',$main_screen_unique_id);

        // Section Tab UI starts Here

        $total_ui .= '<ul class="nav nav-pills navtab-bg nav-justified">';

        $show                 = 1;
        $show1                 = 1;
        
        foreach ($screen_sections as $section_key => $section_value) {

            $active              = "";

            if ($show++ == 1) {          
                $active          = " active ";
            }

            $section_name        = disname($section_value["section_name"]);
            $section_unique_id   = $section_value["unique_id"];

            $total_ui .= '<li class="nav-item">
                            <a href="#sec'.$section_unique_id.'" data-toggle="tab" aria-expanded="true" class="nav-link '.$active.'">
                                '.$section_name.'
                            </a>
                        </li>';
        }

        $total_ui .= '</ul>';

        // Section Tab UI Ends Here

        $total_ui            .= '<div class="tab-content">';
        // Section Tab Content UI Starts Here
        foreach ($screen_sections as $section_key => $section_value) {

            $active              = "";
            
            if ($show1++ == 1) {          
                $active          = " show active ";
            }

            $section_name        = disname($section_value["section_name"]);
            $section_unique_id   = $section_value["unique_id"];

            // Tab DIV Start
            $total_ui            .= '<div class="tab-pane '.$active.'" id="sec'.$section_unique_id.'">';

            // Get Section Based Screens
            $user_screens       = user_screen('',$section_unique_id);
            $user_actions       = user_actions();

            // UI Table Start
            $total_ui           .= '<div class="row">
                                        <div class="col-12">';
            
            $total_ui           .= '<table class="user_permission_datatable table dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>';

            $total_ui           .= '<th>#</th>';
            $total_ui           .= '<th>Screen</th>';
            $total_ui           .= '<th>';
            // $total_ui           .= '<span class="toggle-button toggle-button--tuuli">
            //                             <input id="all'.$section_unique_id.'" type="checkbox">
            //                             <label for="all'.$section_unique_id.'"></label>
            //                             <div class="toggle-button__icon"></div>
            //                         </span>';
            $total_ui           .= '<button onclick="check_all(\'all'.$section_unique_id.'\')" type="button" data-id = "all'.$section_unique_id.'" class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light">All</button>';
            $total_ui           .= '</th>';

            foreach ($user_actions as $action_key => $action_value) {

                $action_name    = disname($action_value["action_name"]);

                $total_ui       .= '<th><button onclick="check_all(\''.$action_value["action_name"].$section_unique_id.'\')" type="button" data-id = "'.$action_value["action_name"].$section_unique_id.'" class="btn btn-sm btn-soft-info btn-rounded waves-effect waves-light">'.$action_name.'</button></th>';

            }

            $total_ui           .= '    </tr>
                                    </thead>';

            $total_ui           .= '<tbody>';

            $s_no               = 1;

            foreach ($user_screens as $screen_key => $screen_value) {

                $screen_name        = disname($screen_value["screen_name"]);
                $screen_unique_id   = $screen_value["unique_id"];
                $screen_actions     = explode(",",$screen_value["actions"]);

                $total_ui       .= '<tr>';
                $total_ui       .= '<td>'.$s_no.'</td>';
                $total_ui       .= '<td>'.$screen_name.'</td>';
                $total_ui       .= '<td>
                                        <span class="toggle-button toggle-button--tuuli">
                                            <input class = "all'.$section_unique_id.'" onclick="check_all(\'screen'.$screen_unique_id.'\')" id="all'.$screen_unique_id.$s_no.'" type="checkbox">
                                            <label for="all'.$screen_unique_id.$s_no.'"></label>
                                            <div class="toggle-button__icon"></div>
                                        </span>
                                    </td>';

                foreach ($user_actions as $action_key => $action_value) {

                    $action_unique_id   = $action_value["unique_id"];
                    $total_ui       .= '<td>';

                    if (in_array($action_unique_id,$screen_actions)) {

                        $total_ui   .= '<span class="toggle-button toggle-button--tuuli">

                                            <input class="all-checkbox '.$action_value["action_name"].$section_unique_id.' all'.$section_unique_id.' screen'.$screen_unique_id.'" data-main="'.$main_screen_unique_id.'" data-section="'.$section_unique_id.'" data-screen="'.$screen_unique_id.'"  id="'.$screen_unique_id."-".$action_unique_id.'" type="checkbox">

                                            <label for="'.$screen_unique_id.$action_unique_id.'"></label>
                                            <div class="toggle-button__icon"></div>
                                        </span>';

                    } else {
                        $total_ui   .= '<span class="table_empty_dot"></span>';
                    }

                    $total_ui       .= '</td>';
    
                }

                $total_ui       .= '</tr>';
                
                $s_no++;            
            }

            $total_ui           .= '</tbody>';
            $total_ui           .= '</table>';
            // UI Table End
            $total_ui           .= '    </div>
                                    </div>';

            // Tab DIV End
            $total_ui            .= '</div>';
        }
        $total_ui            .= '</div>';

        // Section Tab Content UI Ends Here
    }
    return $total_ui;
}
?>