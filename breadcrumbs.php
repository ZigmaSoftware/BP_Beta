    <!-- start page title -->
    <?php
// STEP 1: Get the file from URL
$file_full_path = $_GET['file'] ?? '';

$folder_name = "";
$file_name = "";

// Remove any query string from full path before processing
$clean_path = explode('?', $file_full_path)[0]; // purchase_order_test/view

// Check if path contains "/"
if (strpos($clean_path, '/') !== false) {
    $parts = explode('/', $clean_path);
    $folder_name = $parts[0]; // e.g. purchase_order_test
    $file_name = $parts[1];   // e.g. view
} else {
    $file_name = $clean_path;
}

// Store original
$folder_name_org = $folder_name;

// ✅ Convert _ to space and capitalize
$folder_name_clean = ucwords(str_replace('_', ' ', $folder_name));
$file_name_clean = ucwords(str_replace('_', ' ', $file_name));

// ✅ Breadcrumb logic
if ($file_name == $folder_name) {
    $breadcramb = $file_name_clean;
} else {
    if ($file_name == "List") {
        $folder_name_clean .= "s";
    }
    $breadcramb = $file_name_clean . " " . $folder_name_clean;
}
?>

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?php echo $company_name;?></a></li>
                        <?php if ($folder_name_org != 'dashboard') { ?>
                        <li class="breadcrumb-item"><a href="javascript: void(0);"><?php echo $folder_name;?></a></li>
                        <?php } ?>
                        <li class="breadcrumb-item active"><?php echo $breadcramb;?></li>
                    </ol>
                </div>
                <?php
                    // if ($file_name == "List") {
                    //     $folder_name = $folder_name."s";
                    // }
                ?>
                <h4 class="page-title"><?php echo $breadcramb;?></h4>
            </div>
        </div>
    </div>     
    <!-- end page title -->