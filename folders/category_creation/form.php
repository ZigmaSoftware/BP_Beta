<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables

$btn_text           = "Save";
$btn_action         = "create";
$form_type          = "Create"; 
$unique_id          = "";
$user_type          = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

         $raw_unique_id = $_GET['unique_id'];
        $get_uni_id = $raw_unique_id;
    
        


        $unique_id  = $get_uni_id;
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "category_creation";
        
        $columns            = [
            "department",
            "main_category_name",
            "category_name",
            "description",

        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

        $department        = $result_values["department"];
        $main_category_name          = $result_values["main_category_name"];
        $sub_category           = $result_values["category_name"];
        $description            = $result_values["description"];
       
            $form_type          = "Update";
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$department_options         = department_wise();
$department_options         = select_option($department_options,"Select ",$department);

$main_category_name_options = category_name_wise('', $department);
$main_category_name_options = select_option($main_category_name_options, 'Select Category Name', $main_category_name);


?>
<!-- start page title -->
<div class="row">
<div class="col-12">
<div class="page-title-box">
<h4 class="page-title">Category Creation <?=$form_type;?></h4>
<div>
<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
<li class="breadcrumb-item active">Category Creation</li>
</ol>
</div>
</div>
</div>
</div>
<br>
<!-- end page title -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                    <div class="row">                                    
                        <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="row mb-3">
                                <div class="col-6"> 
                                    <div class="row">
                                        <label for="inputEmail3" class="col-4 col-xl-3 col-form-label">Department Name</label>
                                        <div class="col-8 col-xl-9 mrg-btm">
                                            <select name="department" id="department" onchange= "get_category_name()"  class="select2 form-control"  required>
                                                <?php echo $department_options;?>
                                            </select>
                                        </div>
                                        <label for="inputEmail3" class="col-4 col-xl-3 col-form-label">Category Name</label>
                                        
                                        <div class="col-8 col-xl-9 mrg-btm">
                                            <select name="main_category_name" id="main_category_name" class="select2 form-control"  required>
                                                <?php echo $main_category_name_options;?>
                                            </select>
                                        </div>
                                        <label for="inputEmail3" class="col-4 col-xl-3 col-form-label">Sub Category</label>
                                        <div class="col-8 col-xl-9 mrg-btm">
                                         <input type="text" id="category_name" name="category_name" class="form-control" value="<?php echo $sub_category; ?>" required>
                                        </div>
                                        
                                        <label for="inputEmail3" class="col-4 col-xl-3 col-form-label">Description</label>
                                        <div class="col-8 col-xl-9 mrg-btm">
                                            <textarea name="description" id="description"  rows="5" class="form-control"><?php echo $description; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row ">
                                    <div class="col-md-12" align="right">
                                        <!-- Cancel,save and update Buttons -->
                                        <?php echo btn_cancel($btn_cancel);?>
                                        <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form> 
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  