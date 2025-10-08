<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$user_type               = "";
$under_user_type        = "";
$exp_under_user_type     = "";

$date = date("Y-m-d");
$vehicle_type_options      = "<option value='' disabled='disabled' selected>Select the Vehicle</option>";
$fuel_type_options         = "<option value='' disabled='disabled' selected>Select the Fuel</option>";


// $is_active          = 1;

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "fuel_type_cost_creation";

        $columns    = [
            "entry_date",
            "travel_type",
            "fuel_type",
            "vehicle_type",
            "rate",
            "unique_id"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);
        // print_R($result_values);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $entry_date          = $result_values[0]["entry_date"];
            $travel_type          = $result_values[0]["travel_type"];
            $fuel_type           = $result_values[0]["fuel_type"];
            $vehicle_type      = $result_values[0]["vehicle_type"];
            $rate               = $result_values[0]["rate"];
            $is_active          = $result_values[0]["is_active"];

            $vehicle_type_options      = vehicle_type();
            $vehicle_type_options      = select_option($vehicle_type_options,"Select the Vehicle",$vehicle_type); 

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$travel_type_options = travel_type();

$travel_type_options  = select_option($travel_type_options, "Select Vehicle Type", $travel_type);

$fuel_type_option            = [

    'Petrol' => [
        "id"    => "Petrol",
        "text"  => "Petrol"
    ],
    'Disel' => [
        "id"    => "Disel",
        "text"  => "Disel"
    ],
];
$fuel_type_options  = select_option($fuel_type_option, "Select Fuel Type", $fuel_type);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="entry_date"> Entry Date</label>
                                <div class="col-md-4">
                                    <input type="date" id="entry_date" name="entry_date" class="form-control" value="<?php if ($entry_date) {
                                                                                                                            echo $entry_date;
                                                                                                                        } else {
                                                                                                                            echo $date;
                                                                                                                        } ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="travel_type">Travel Type</label>
                                <div class="col-md-4">
                                    <select name="travel_type" id="travel_type" class="select2 form-control" onchange="get_vehicle(this.value);" required><?php echo $travel_type_options; ?>
                                    </select>
                                </div>

                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="vehicle_type"> Vehicle Type</label>
                                <div class="col-md-4">
                                    <select name="vehicle_type" id="vehicle_type" class="select2 form-control" onchange="get_fuel_type(this.value);" required><?php echo $vehicle_type_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="fuel_type">Fuel Type</label>
                                <div class="col-md-4">
                                    <select name="fuel_type" id="fuel_type" class="select2 form-control"><?php echo $fuel_type_options; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="rate">Rate</label>
                                <div class="col-md-4">
                                    <input type="text" id="rate" name="rate" class="form-control" value="<?php echo $rate; ?>" required>
                                </div>

                            </div>
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                    <?php echo btn_cancel($btn_cancel); ?>
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>