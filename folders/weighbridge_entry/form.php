<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

<?php
date_default_timezone_set("Asia/Kolkata");
// $date = date('Y/m/d H:i:s', time()); 
$entry_date = '';
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";


$unique_id          = "";

$no_of_trip = "";
$slip_no = "";
$vehicle_no               = "";
$gross_weight               = "";
$tare_weight               = "";
$net_weight               = "";

$is_active          = 1;

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "weighbridge_entry";

        $columns    = [
            "no_of_trip",
            "entry_date",
            "slip_no",
            "vehicle_no",
            "material_name",
            "gross_weight",
            "tare_weight",
            "net_weight"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $no_of_trip        = $result_values[0]["no_of_trip"];
            $entry_date        = $result_values[0]["entry_date"];
            $slip_no        = $result_values[0]["slip_no"];
            $vehicle_no        = $result_values[0]["vehicle_no"];
            $material_name        = $result_values[0]["material_name"];
            $gross_weight        = $result_values[0]["gross_weight"];
            $tare_weight        = $result_values[0]["tare_weight"];
            $net_weight        = $result_values[0]["net_weight"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
} else {
    $no_of_trips = '';
    $entry_date = date('Y-m-d');
    $table      =  "weighbridge_entry";
    $where      = [
        "is_delete" => 0
    ];
    $columns    = [
        "MAX(no_of_trip) as no_of_trip"
    ];

    $table_details   = [
        $table,
        $columns
    ];

    $result_values  = $pdo->select($table_details, $where);
    if ($result_values->status) {

        $result_values      = $result_values->data;

        $no_of_trips        = $result_values[0]["no_of_trip"];
    }

    $no_of_trip = $no_of_trips + 1;
}

$vehicle_no_options  = vehicle_no();
$vehicle_no_options  = select_option($vehicle_no_options, "Select the Vehicle No", $vehicle_no);

$source_of_waste_options  = source_of_waste();
$source_of_waste_options  = select_option($source_of_waste_options, "Select the Source of Waste", $material_name);

$active_status_options   = active_status($is_active);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" id = "myForm" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-12 col-form-label" for="entry_date">Date</label>
                                <div class="col-md-11">
                                    <input type="date" id="entry_date" max="<?php echo $entry_date; ?>" name="entry_date" class="form-control" value="<?php echo $entry_date; ?>" onchange="validateMonth()" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-12 col-form-label" for="no_of_trips">No of Ticket</label>
                                <div class="col-md-11">
                                    <input type="hidden" id="no_of_trip" name="no_of_trip" value="<?php echo $no_of_trip; ?>">
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="no_of_trips" name="no_of_trips" class="form-control" value="<?php echo $no_of_trip; ?>" required disabled>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-12 col-form-label" for="slip_no">Slip No</label>
                                <div class="col-md-11">
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="slip_no" name="slip_no" class="form-control" value="<?php echo $slip_no; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-12 col-form-label" for="vehicle_no">Vehicle No</label>
                                <div class="col-md-11">
                                    <select name="vehicle_no" id="vehicle_no" class="select2 form-control" required>
                                        <?php echo $vehicle_no_options; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-12 col-form-label" for="material_name">Source of Waste</label>
                                <div class="col-md-11">
                                    <select name="material_name" id="material_name" class="select2 form-control" required>
                                        <?php echo $source_of_waste_options; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-12 col-form-label" for="gross_weight">Gross Weight (kg)</label>
                                <div class="col-md-11">
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, ''); validateWeightsOnInput();" id="gross_webtn_cancelight" name="gross_weight" class="form-control" value="<?php echo $gross_weight; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-12 col-form-label" for="tare_weight">Tare Weight (kg)</label>
                                <div class="col-md-11">
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, ''); validateWeightsOnInput();" id="tare_weight" name="tare_weight" class="form-control" value="<?php echo $tare_weight; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label class="col-md-12 col-form-label" for="net_weight">Net Weight (kg)</label>
                                <div class="col-md-11">
                                    <input type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '')" id="net_weight" name="net_weight" class="form-control" value="<?php echo $net_weight; ?>" required readonly>
                                </div>
                            </div>
                            <div class="form-group row btn-action">
                                <div class="col-md-11">
                                    <!-- Cancel,save and update Buttons -->
                                 
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                       <?php echo btn_cancel($btn_cancel); ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>