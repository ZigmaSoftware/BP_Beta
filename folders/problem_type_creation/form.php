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

        $uni_dec = str_replace(" ", "+",$_GET['unique_id']);
        $get_uni_id           = openssl_decrypt(base64_decode($uni_dec), $enc_method, $enc_password,OPENSSL_RAW_DATA, $enc_iv);


        $unique_id  = $get_uni_id;
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "problem_type_creation";

        $columns            = [
            "problem_type",
            "is_active",
            "description",

        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

        $problem_type       = $result_values[0]["problem_type"];
        $description        = $result_values[0]["description"];
        $is_active          = $result_values[0]["is_active"];
       
        $form_type              = "Update";   
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$active_status_options= active_status($is_active);

?>
<!-- start page title -->
<div class="row">
<div class="col-12">
<div class="page-title-box">
<h4 class="page-title">Problem Type Creation <?=$form_type;?></h4>
<div>
<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
<li class="breadcrumb-item active">Problem Type Creation</li>
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
    
    <label for="inputEmail3" class="col-4 col-xl-3 col-form-label">Problem Type</label>
                              <div class="col-8 col-xl-9 mrg-btm">
                                 <input type="text" id="problem_type" name="problem_type" class="form-control" value="<?php echo $problem_type; ?>" required>
                              </div>
                              <label for="inputEmail3" class="col-4 col-xl-3 col-form-label">Status</label>
                              <div class="col-8 col-xl-9 mrg-btm">
                                 <select name="is_active" id="is_active" class="select2 form-control" onchange="get_sections(this.value)" required>
                                 <?php echo $active_status_options;?>
                                 </select>
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
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  