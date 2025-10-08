    <!-- Topbar Start -->
    <div class="navbar-custom">
        <div class="container-fluid">
            <ul class="list-unstyled topnav-menu float-right mb-0">

                <!-- <li class="dropdown d-inline-block d-lg-none">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fe-search noti-icon"></i>
                    </a>
                    <div class="dropdown-menu dropdown-lg dropdown-menu-right p-0">
                        <form class="p-3">
                            <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                        </form>
                    </div>
                </li> -->

                <li class="dropdown d-none d-lg-inline-block">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="fullscreen" href="#">
                        <i class="fe-maximize noti-icon"></i>
                    </a>
                </li>

                <!-- <li class="dropdown d-none d-lg-inline-block topbar-dropdown">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fe-grid noti-icon"></i>
                    </a>
                    <div class="dropdown-menu dropdown-lg dropdown-menu-right">

                        <div class="p-lg-1">
                            <div class="row no-gutters">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="img/brands/slack.png" alt="slack">
                                        <span>Slack</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="img/brands/github.png" alt="Github">
                                        <span>GitHub</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="img/brands/dribbble.png" alt="dribbble">
                                        <span>Dribbble</span>
                                    </a>
                                </div>
                            </div>

                            <div class="row no-gutters">
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="img/brands/bitbucket.png" alt="bitbucket">
                                        <span>Bitbucket</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="img/brands/dropbox.png" alt="dropbox">
                                        <span>Dropbox</span>
                                    </a>
                                </div>
                                <div class="col">
                                    <a class="dropdown-icon-item" href="#">
                                        <img src="img/brands/g-suite.png" alt="G Suite">
                                        <span>G Suite</span>
                                    </a>
                                </div>
                    
                            </div>
                        </div>

                    </div>
                </li> -->


                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="<?=$_SESSION['user_image'];?>" alt="user-image" class="rounded-circle">
                        <span class="pro-user-name ml-1">
                            <?=ucfirst($_SESSION['user_name']);?> <i class="mdi mdi-chevron-down"></i> 
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                        <!-- item-->
                        <!-- <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome !</h6>
                        </div> -->

                        <!-- item-->
                        <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-user"></i>
                            <span>My Account</span>
                        </a> -->

                        <!-- item-->
                        <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-settings"></i>
                            <span>Settings</span>
                        </a> -->

                        <!-- item-->
                        <!-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <i class="fe-lock"></i>
                            <span>Lock Screen</span>
                        </a> -->

                        <div class="dropdown-divider"></div>

                        <!-- item-->
                        <a href="logout.php" class="dropdown-item notify-item">
                            <i class="fe-log-out"></i>
                            <span>Logout</span>
                        </a>

                    </div>
                </li>

                <!-- <li class="dropdown notification-list">
                    <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                        <i class="fe-settings noti-icon"></i>
                    </a>
                </li> -->

            </ul>

            <!-- LOGO -->
            <div class="logo-box">
                <a href="index.php" class="logo logo-dark text-center">
                    <span class="logo-sm">
                        <img src="<?=$_SESSION['sess_img_path'];?>logo-sm.png" alt="" height="50">
                        <!-- <span class="logo-lg-text-light">UBold</span> -->
                    </span>
                    <span class="logo-lg">
                        <img src="<?=$_SESSION['sess_img_path'];?>logo-dark.png" alt="" height="20">
                        <!-- <span class="logo-lg-text-light">U</span> -->
                    </span>
                </a>

                <a href="index.php" class="logo logo-light text-center">
                    <span class="logo-sm">
                        <img src="<?=$_SESSION['sess_img_path'];?>logo-sm.png" alt="" height="50">
                    </span>
                    <span class="logo-lg">
                        <img src="<?=$_SESSION['sess_img_path'];?>logo-new1.png" alt="" height="50">
                    </span>
                </a>
            </div>

            <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                <!-- <li>
                    <button class="button-menu-mobile waves-effect waves-light">
                        <i class="fe-menu"></i>
                    </button>
                </li> -->

                <!-- <li> -->
                    <!-- Mobile menu toggle (Horizontal Layout)-->
                    <!-- <a class="navbar-toggle nav-link" data-toggle="collapse" data-target="#topnav-menu-content">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a> -->
                    <!-- End mobile menu toggle-->
                <!-- </li> -->

                <!-- Menu Option with Permission Begins  -->
                <?php
                    // Main Screen Begins
                    $main_screens  = main_screen();

                    // print_r($_SESSION['main_screens']);
                    foreach ($main_screens as $main_key => $main_value) {

                        // Menu Permission based on User type Begin
                        if (in_array($main_value['unique_id'],$_SESSION['main_screens'])) {

                        $menu_main_name    =  disname($main_value["screen_main_name"]);
                        $menu_main_icon    = "";

                        if ($main_value["icon_name"]) {
                            $menu_main_icon    =  '<i class="mdi '.$main_value["icon_name"].'"></i>';
                        }
                ?>
                
                <li class="dropdown dropdown-mega  d-lg-inline-block d-xl-block">
                    
                    <a class="nav-link dropdown-toggle waves-effect waves-light font-weight-bold text-white menu-text" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">

                        <?php echo $menu_main_name; ?>

                        <?php echo $menu_main_icon; ?> 
                        
                    </a>

                    <div class="dropdown-menu dropdown-megamenu">
                        <div class="row">
                            <div class="col-sm-12">
                    
                                <div class="row">
                    <?php 
                        // Screen Section Begins

                        $screen_sections  = section_name('',$main_value['unique_id']);

                        foreach ($screen_sections as $section_key => $section_value) {

                            // Section Permission Based On User Type
                            if (in_array($section_value['unique_id'],$_SESSION['sections'])) {

                            $menu_section_name    = disname($section_value["section_name"]);
                            $menu_section_icon    = "";

                            if ($section_value["icon_name"]) {
                                $menu_section_icon    =  '<i class="mdi '.$section_value["icon_name"].'"></i>';
                            }
                    ?>
                                        <div class="col-md-3">
                                            <h5 class="text-green mt-0"><?php echo $menu_section_name; ?></h5>
                                            <ul class="list-unstyled megamenu-list">
                                            <?php
                                                    // Screen List Begins
                                                    $user_screens   = user_screen('',$section_value['unique_id']);

                                                    foreach ($user_screens as $screens_key => $screens_value) {
                                                        // User Screen Permission Based On User Type
                                                        if (in_array($screens_value['unique_id'],$_SESSION['screens'])) {
                                                        
                                                        $menu_screen_name   = disname($screens_value["screen_name"]);
                                                        $menu_screen_folder = $screens_value["folder_name"];
                                                        $menu_screen_icon   = "";
                                                ?>
                                                    <li>
                                                        <a href="index.php?file=<?php echo $menu_screen_folder; ?>/list" class="menu-text"><?php echo $menu_screen_name; ?></a>
                                                    </li>

                                                <?php
                                                    // User Screen Permission Based On User Type
                                                        }
                                                    // Screen List Ends
                                                    }
                                                ?>
                                            </ul>
                                        </div>                                    

                                        <?php
                                            // Section Permission Based On User Type
                                            }
                                            // Screen Section Ends
                                        }
                                        ?>

                                </div>
                            </div>
                        </div>
                    </div>

                </li>

                <?php
                        // Menu Permission based on User type Begin
                        }
                    // Main Screen Ends
                    }
                ?>
                <!-- Menu Option with Permission Ends  -->


                
            </ul>
            <div class="clearfix"></div>
        </div>
    </div>
    <!-- end Topbar -->