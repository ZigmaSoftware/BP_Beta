<!DOCTYPE html>
<html lang="en">
    <?php include 'config/dbconfig.php'; ?>
    <?php 
    // Temp variables

    
    $company_name = "Blue Planet";
    $user_id      = "";

    if (isset($_SESSION['user_id'])) {
        $user_id      = $_SESSION['user_id'];

        if (isset($_SESSION['LAST_ACTIVITY']) && ((time() - $_SESSION['LAST_ACTIVITY']) > 3600)) {
            // last request was more than 1 hour ago
            //session_unset();     // unset $_SESSION variable for the run-time 
            //session_destroy();   // destroy session data in storage
        ?>
        
        <?php
            header("Location: logout.php");
        }

        $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
    }
    ?>
    
    
    <?php include 'inc/header.php';
      
    ?>
    <?php 
    
    if (session_id() AND ($user_id)) { 
        // permission_check();
    ?>
    <body class="loading">

        <!-- Pre-loader -->
        <div id="preloader">
            <div id="status">
                <!-- <div class="spinner">Loading...</div> -->
                <div class="spinner-grow text-green" role="status"></div>
            </div>
        </div>
        <!-- End Preloader-->

        <!-- Begin page -->
        <div id="wrapper">

            <?php
                // Skip topmenu for standard_bom/view and ordered_bom/view
                // Skip topmenu for specific pages and any /create or /update pages
                if (
                    !(isset($_GET['file']) && (
                        $_GET['file'] === 'standard_bom/view' ||
                        $_GET['file'] === 'ordered_bom/view' ||
                        $_GET['file'] === 'ordered_bom/obom_child' ||
                        preg_match('~/create$~', $_GET['file']) ||
                        preg_match('~/update$~', $_GET['file'])
                    ))
                ) {
                    include 'inc/topmenu1.php';
                }

            ?>

            <?php  
            // include 'inc/leftsidemenu.php'; 
            ?>
            
            <!-- Page Contents Begins Here -->
            <div class="content-page">
                <!-- File Include Function Starts Here -->

                <?php include 'body.php'; ?>

                <?php include 'inc/companyfooter.php'; ?>

            </div>
            <!-- Page Contents Ends Here -->

        </div>
        <!-- END wrapper -->

        <?php include 'inc/rightsidemenu.php';?>

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <?php include 'inc/footer.php';?>
        
    </body>

    <?php
        } else {
        // LOGIN PAGE 
        $folder_name_org = "login";
    ?>
    <body class="loading auth-fluid-pages pb-0">

        <?php include "folders/login/login.php";?>
        <?php include 'inc/footer.php';?>

    </body>
    <?php
        }
    ?>

</html>