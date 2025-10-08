<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$form_type          = "Create"; 
$unique_id          = "";
$remark_creation    = "";
$under_remark_creation  = "";
$exp_under_remark_creation  = "";
$is_active          = 1;


// $description          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $uni_dec = str_replace(" ", "+",$_GET['unique_id']);
        $get_uni_id           = openssl_decrypt(base64_decode($uni_dec), $enc_method, $enc_password,OPENSSL_RAW_DATA, $enc_iv);


        $unique_id  = $get_uni_id;
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "remark_creation";

        $columns    = [
            "remark",
            "is_active",
            "description"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $remark              = $result_values[0]["remark"];
            $is_active           = $result_values[0]["is_active"];
            $description         = $result_values[0]["description"];

           

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

$active_status_options= active_status($is_active);

?>
<!-- start page title -->
<div class="row">
<div class="col-12">
<div class="page-title-box">
<h4 class="page-title">Remark Creation <?=$form_type;?></h4>
<div>
<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
<li class="breadcrumb-item active">Remark Creation</li>
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
                            <div class="form-group row ">
                            <label class="col-md-2 col-form-label" for="remark"> Remark </label>
                                <div class="col-md-4">
                                    <input type="text" id="remark" name="remark"  class="form-control" value="<?php echo $remark; ?>" required>
                                </div>
                                <label for="inputEmail3" class="col-md-2 col-form-label">Status</label>
                            <div class="col-md-4">
                                 <select name="is_active" id="is_active" class="select2 form-control" onchange="get_sections(this.value)" required>
                                 <?php echo $active_status_options;?>
                                 </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="description"> Description </label>
                                <div class="col-md-4">
                                    <textarea name="description" id="description"  class="form-control"><?php echo $description;?></textarea>
                                </div>
                            </div>
                            <br>
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