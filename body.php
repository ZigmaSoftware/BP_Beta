<!-- ============================================================== -->
<!-- Start Page Content here -->
<!-- ============================================================== -->
    <?php 
                
        if (isset($_GET['file'])) {


        $file_str        = $_GET['file'];
        $file_arr        = explode("/",$file_str);

        $folder_name_org = $file_arr[0];
        $file_name_org   = $file_arr[1];

        $add_btn_link    = "index.php?file=".$folder_name_org."/create";
        $cancel_btn_link = "index.php?file=".$folder_name_org."/list";
        $folder_crud_link= "folders/".$folder_name_org."/crud.php";

        $btn_add         = "index.php?file=".$folder_name_org."/create";
        $btn_cancel      = "index.php?file=".$folder_name_org."/list";
        $folder_crud_link= "folders/".$folder_name_org."/crud.php";

        $folder_name     = disname($folder_name_org);
        $file_name       = disname($file_name_org);
        
        } else {
            // Default page settings if no 'file' parameter is present in the URL
            $file_str        = "home/welcome";
            $folder_name     = "home";
            $file_name       = "welcome";
            
            $folder_name_org = "home";
            $file_name_org   = "welcome";
            
            $btn_add         = "#"; // No need for add/cancel buttons on the home page
            $btn_cancel      = "#";
            $folder_crud_link= "folders/home/crud.php"; // Optional placeholder for later use

        }

    ?>
    <script>
        sessionStorage.setItem("folder_crud_link","<?php echo $folder_crud_link; ?>");
        sessionStorage.setItem("list_link","<?php echo $btn_cancel; ?>");
        sessionStorage.setItem("create_link","<?php echo $btn_add; ?>");
        sessionStorage.setItem("company_name","<?php echo "zigma"; ?>");
    </script>

    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid">
        <!-- <marquee class="text-danger h2 d-print-none" direction="right" scrollamount="7">
            This was testing module.
        </marquee> -->
        <!-- Location -->
        <input type="hidden" name="location" id="location" class="form-control" value="" disabled>
        <input type="hidden" name="latitude" id="latitude" class="form-control" value="" >
        <input type="hidden" name="longitude" id="longitude" class="form-control" value="" >
        <input type="hidden" name="file" id="file" class="form-control" value="<?= $file_str ?>" >
        
        <?php 
            // detect if current page is a form-type (create or update)
            $is_form_page = preg_match('/(create|update|edit)/i', $file_name_org);
        ?>
        
        <?php if ($is_form_page): ?>
          <!-- 🔔 Reusable Unsaved Warning Banner -->
          <div id="unsaved_warning_banner"
               class="alert alert-warning text-center fw-bold py-2"
               style="display:none; position:sticky; top:0; z-index:1050;">
            ⚠ You have unsaved changes — please <b>Save</b> or <b>Cancel</b> before leaving or reloading this page.
          </div>
        <?php endif; ?>



        <!-- Breadcrumbs Begins Here -->
            <?php include 'breadcrumbs.php'; ?>
        <!-- Breadcrumbs Ends Here -->


        <!-- Page content Begins Here -->
            <?php include 'folders/'.$file_str.'.php';?>
        <!-- Page content Ends Here -->
            
        </div> <!-- container -->

    </div> <!-- content -->



<!-- ============================================================== -->
<!-- End Page content -->
<!-- ============================================================== -->