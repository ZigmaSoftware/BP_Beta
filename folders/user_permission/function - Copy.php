<?php


// Over All Permission UI Function
function user_permission_ui1($main_screens_arr = "", $screen_sections_arr = "", $user_screens_arr = "") {

    $total_ui   = "";

    $total_ui   .= '<li>
                        <input type="checkbox" id="all" >
                        <label for="all">Select All</label>
                    </li><br />';
    
    // Get Main Screens
    $main_screens  = main_screen();

    foreach ($main_screens as $main_key => $main_value) {

        // Main Screen UI Design Starts Here
        $total_ui   .= '<div class="col-md-4">';
        $total_ui   .= '<li>
                            <input type="checkbox" id="'.$main_value["unique_id"].'" name="user_actions" value="'.$main_value["unique_id"].'" class="check_all main_all">
                            <label for="'.$main_value["unique_id"].'" class="main_screen">'.disname($main_value["screen_main_name"]).'</label>';

        // Get Screen Sections
        $screen_sections  = section_name('',$main_value['unique_id']);

        foreach ($screen_sections as $section_key => $section_value) {

            $total_ui   .= '<ul class="section-permission">';

            // Screen Section UI Design Starts Here
            $total_ui   .= '<li>
                                <input type="checkbox" id="'.$section_value["unique_id"].'" name="user_actions" value="'.$section_value["unique_id"].'" class="check_all section_all '.$main_value["unique_id"].'section">
                                <label for="'.$section_value["unique_id"].'" class="section_screen">'.disname($section_value["section_name"]).'</label>';

            // Get Section Based Screens
            $user_screens   = user_screen('',$section_value['unique_id']);
            
            foreach ($user_screens as $screen_key => $screen_value) {
                // print_r($screen_value);
                $total_ui   .= '<ul class="screen-permission">';
                // Screen Section UI Design Starts Here
                $total_ui   .= '<li>
                                    <input type="checkbox" id="'.$screen_value["unique_id"].'" name="user_actions" value="'.$screen_value["unique_id"].'" class="check_all '.$main_value["unique_id"].'section '.$section_value["unique_id"].'screen">
                                    <label for="'.$screen_value["unique_id"].'" class="user_screen">'.disname($screen_value["screen_name"]).'</label>';

                $curr_user_actions = explode(",",$screen_value['actions']);

                $user_actions      = user_actions();

                $total_ui   .= '<ul class="screen-permission">';
                foreach ($user_actions as $action_key => $action_value) {
                    // print_r($action_value);
                    if (in_array($action_value['unique_id'],$curr_user_actions)) {
                        
                        // Screen Action UI Design Starts Here
                        $total_ui   .= '<li>
                                            <input type="checkbox" id="'.$action_value["unique_id"].'" name="user_actions" value="'.$action_value["unique_id"].'" class="check_all '.$main_value["unique_id"].'section '.$section_value["unique_id"].'screen">
                                            <label for="'.$action_value["unique_id"].'">'.disname($action_value["action_name"]).'</label>';
                        // Screen Action UI Design Ends Here
                        $total_ui   .= '</li>';
                        
                    }
                }
                $total_ui   .= '</ul>';

                // Screen Section UI Design Ends Here
                $total_ui   .= '</li>';
                $total_ui   .= '</ul>';
            }

            // Screen Section UI Design Ends Here
            $total_ui   .= '</li>';
            $total_ui   .= '</ul>';

        }

        // Main Screen UI Design Ends Here
        $total_ui   .= '</li>';
        $total_ui   .= '</div>';
    }

    return $total_ui;
}

function user_permission_array () {
    $permission_array   = [];
    $perm_main          = [];
    $perm_section       = [];
    $perm_screen        = [];
    $perm_action       = [];

    // Get Main Screens
    $main_screens  = main_screen();
    
    foreach ($main_screen as $main_key => $main_value) {

        // Get Screen Sections
        $screen_sections  = section_name('',$main_value['unique_id']);

        foreach ($screen_sections as $section_key => $section_value) {
        
            // Get Section Based Screens
            $user_screens   = user_screen('',$section_value['unique_id']);
            
            foreach ($user_screens as $screen_key => $screen_value) {

                $perm_action = explode(",",$screen_value['actions']);

                // $perm_screen['']

            }
        }        
    }
}

// Over All Permission UI Function
function user_permission_ui($main_screens_arr = "", $screen_sections_arr = "", $user_screens_arr = "") {

    $total_ui   = "";
    $total_ui   = '<ul class="nav nav-pills navtab-bg nav-justified">';
    
    // Get Main Screens
    $main_screens  = main_screen();

    foreach ($main_screens as $main_key => $main_value) {
        $total_ui .='<li class="nav-item">
                        <a href="#home1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                            Admin
                        </a>
                    </li>';
    }

    $total_ui      .= '</ul>';
    $total_ui      .= '<div class="tab-content">';
    $total_ui      .= '<div class="tab-pane show active" id="home1">
                        <div class="card-box">            
                            <div class="row">';
                            
        $total_ui   .= '<div class="col-sm-3">
                            <div class="nav flex-column nav-pills nav-pills-tab" id="v-pills-tab" role="tablist" aria-orientation="vertical">';

    foreach ($main_screens as $main_key => $main_value) {

        // Get Screen Sections
        $screen_sections  = section_name('',$main_value['unique_id']);

        foreach ($screen_sections as $section_key => $section_value) {
            $total_ui .= '<a class="nav-link show active mb-1" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
            Main Screen</a>';
        }

    }

    $total_ui       .= '</div></div>';

    foreach ($screen_sections as $screen_key => $screen_value) {
        # code...
    }

    return $total_ui;
}


?>

<?php



// User Action list Array
function user_action_list($user_action_array = "",$selected = "") {
    $all_checked                = "";

    if ($selected) {
        $selected   = explode(",",$selected);
        $user_action_array_count    = count($user_action_array);
        $selected_count             = count($selected);

        if ($user_action_array_count == $selected_count) {
            $all_checked        = " checked ";
        }
    }

    $return_str =   '<li>
                        <input type="checkbox" id="dis_check" disabled>
                        <label for="dis_check">No Options</label>
                    </li>';

    if ($user_action_array) {

        $return_str = '<li>
                            <input type="checkbox" id="all" '.$all_checked.'>
                            <label for="all">All</label>
                        </li>';

        foreach ($user_action_array as $key => $value) {
            $checked    = "";

            if (is_array($selected)) {
                if (in_array($value["unique_id"],$selected)) {
                    $checked = " checked ";
                }
            }
            // print_r($action_name);
            $return_str .= '<li>
                                <input type="checkbox" id="'.$value["unique_id"].'" '.$checked.' name="user_actions" value="'.$value["unique_id"].'" class="check_all">
                                <label for="'.$value["unique_id"].'">'.disname($value["action_name"]).'</label>
                            </li>';
        }
    }

    return $return_str;
}

?>