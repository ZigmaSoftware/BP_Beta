<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$unit_name          = "";
$decimal_points     = "";
$description        = "";
$unit_id            = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "units";

        $columns    = [
            "unit_name",
            "decimal_points",
            "description",
            "is_active",
            "unit_id"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $unit_name          = $result_values[0]["unit_name"];
            $description        = $result_values[0]["description"];
            $decimal_points     = $result_values[0]["decimal_points"];
            $is_active          = $result_values[0]["is_active"];
            $unit_id            = $result_values[0]["unit_id"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}
$decimal_options        =  [
    0 => [
        "unique_id" => 0,
        "value"     => 0
    ],
    1 => [
        "unique_id" => 1,
        "value"     => 1
    ],
    2 => [
        "unique_id" => 2,
        "value"     => 2
    ],
    3 => [
        "unique_id" => 3,
        "value"     => 3
    ],
    4 => [
        "unique_id" => 4,
        "value"     => 4
    ]
];
$decimal_options        = select_option($decimal_options,"Select",$decimal_points);

$active_status_options = active_status($is_active);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="unit_name"> Unit Name </label>
                                <div class="col-md-3">
                                    <input type="text" id="unit_name" name="unit_name" class="form-control" value="<?php echo $unit_name; ?>" required>
                                </div> </div>
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="decimal_points"> Decimal Points</label>
                                <div class="col-md-3">
                                    <select name="decimal_points" id="decimal_points" class="select2 form-control" required>
                                    <?php echo $decimal_options;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="description"> Description</label>
                                <div class="col-md-3">
                                    <textarea name="description" id="description" rows="5" class="form-control" > <?php echo $description; ?></textarea>
                                </div> </div>
                                  <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                        
                            <input type="hidden" id="unit_unique_id" name="unit_unique_id" value="<?=$unit_id;?>">

                            
                        </div>
                            <div class="form-group row btn-action">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                   
                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                     <?php echo btn_cancel($btn_cancel);?>
                                </div>
                                
                            </div>
                    </div>
                </div>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  