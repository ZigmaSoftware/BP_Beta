<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";
$work_location      = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];
        //  $where ='unique_id = "'.$unique_id.'"';

        $table      =  "supplier_ratings";

        $columns    = [
            "@a:=@a+1 s_no",
            "supplier_id",
            "from_period" ,
            "to_period",
            "q_rating",
            "d_rating",
            "r_rating",
            "t_rating",
            "c_rating",
            "remarks",
            "is_active",
            "unique_id",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {
    $result_values  = $result_values->data[0];

    // Assign all fields properly for update mode
    $supplier_id    = $result_values["supplier_id"];
   
    
    // Convert to YYYY-MM (for input type="month")
    $from_period = !empty($result_values["from_period"]) ? date("Y-m", strtotime($result_values["from_period"])) : "";
    $to_period   = !empty($result_values["to_period"]) ? date("Y-m", strtotime($result_values["to_period"])) : "";
    $q_rating       = $result_values["q_rating"];
    $d_rating       = $result_values["d_rating"];
    $r_rating       = $result_values["r_rating"];
    $c_rating       = $result_values["c_rating"];
    $t_rating       = $result_values["t_rating"];
    $remarks        = $result_values["remarks"];
    $is_active      = $result_values["is_active"];
    $unique_id      = $result_values["unique_id"];

    $btn_text       = "Update";
    $btn_action     = "update";
} else {
    $btn_text       = "Error";
    $btn_action     = "error";
    $is_btn_disable = "disabled='disabled'";
}

    }
}

$active_status_options= active_status($is_active);


$supplier_name_options     = supplier();
$supplier_name_options     = select_option($supplier_name_options,"Select", $supplier_id);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                                
                                <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="supplier_id">Supplier <span style="color:red">*</span></label>
                        <div class="col-md-3">
                            <select name="supplier_id" id="supplier_id" class="select2 form-control" required>
                                <?php echo $supplier_name_options; ?>
                            </select>
                        </div>
                    </div>

            
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="from_period">
                            From Period <span style="color:red">*</span>
                        </label>
                        <div class="col-md-3">
                            <input type="month" id="from_period" name="from_period" class="form-control no-keyboard" value="<?= $from_period ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="to_period">
                            To Period <span style="color:red">*</span>
                        </label>
                        <div class="col-md-3">
                            <input type="month" id="to_period" name="to_period" class="form-control no-keyboard" value="<?= $to_period ?>" required>
                        </div>
                    </div>



                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="q_rating">Quality (out of 50)</label>
                        <div class="col-md-3">
                            <input type="text" name="q_rating" id="q_rating"  onkeypress='number_only(event);'
                                   class="form-control" 
                                   value="<?php echo $q_rating; ?>" min="0" max="50" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                            <label class="col-md-2 col-form-label textright" for="d_rating">Delivery (out of 20)</label>
                            <div class="col-md-3">
                                <input type="text" name="d_rating" id="d_rating" onkeypress='number_only(event);'
                                       class="form-control"
                                       value="<?php echo $d_rating; ?>" min="0" max="20" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label textright" for="r_rating">Response (out of 10)</label>
                            <div class="col-md-3">
                                <input type="text" name="r_rating" id="r_rating" onkeypress='number_only(event);'
                                       class="form-control"
                                       value="<?php echo $r_rating; ?>" min="0" max="10" required>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label textright" for="c_rating">Compliances (out of 20)</label>
                            <div class="col-md-3">
                                <input type="text" name="c_rating" id="c_rating" onkeypress='number_only(event);'
                                       class="form-control"
                                       value="<?php echo $c_rating; ?>" min="0" max="20" required>
                            </div>
                        </div>

                    
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="t_rating">Total (out of 100)</label>
                        <div class="col-md-3">
                            <input type="text" name="t_rating" id="t_rating" class="form-control"  onkeypress='number_only(event);'
                                   value="<?php echo $t_rating; ?>" min="0" max="100" readonly>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="remarks">Remarks</label>
                        <div class="col-md-3">
                            <textarea name="remarks" id="remarks" class="form-control" required><?php echo $remarks; ?></textarea>
                        </div>
                    </div>

                                 
                                 
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row btn-action">
                                <div class="col-md-12">
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


<script>


document.addEventListener("DOMContentLoaded", function() {
    // disable typing inside all inputs with class no-keyboard
    document.querySelectorAll(".no-keyboard").forEach(function(input) {
        input.addEventListener("keydown", function(e) {
            e.preventDefault(); // block typing
        });
    });
});

</script>

<script>


function calculateTotal() {
    let q = parseInt(document.getElementById("q_rating").value) || 0;
    let d = parseInt(document.getElementById("d_rating").value) || 0;
    let r = parseInt(document.getElementById("r_rating").value) || 0;

    let total = q + d + r;
    document.getElementById("t_rating").value = total;
}

// Trigger calculation on input changes
document.getElementById("q_rating").addEventListener("input", calculateTotal);
document.getElementById("d_rating").addEventListener("input", calculateTotal);
document.getElementById("r_rating").addEventListener("input", calculateTotal);

// Run once on page load (if editing existing record)
window.onload = calculateTotal;

function validateInput(id, max) {
    let el = document.getElementById(id);
    let val = parseInt(el.value) || 0;

    if (val > max) {
        Swal.fire({
            icon: "warning",
            title: "Limit Exceeded",
            text: id.toUpperCase().replace('_RATING','') + " rating cannot exceed " + max,
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK"
        }).then(() => {
            el.value = max;
            calculateTotal();
        });
    } else if (val < 0) {
        el.value = 0;
    }

    calculateTotal();
}

function calculateTotal() {
    let q = parseInt(document.getElementById("q_rating").value) || 0;
    let d = parseInt(document.getElementById("d_rating").value) || 0;
    let r = parseInt(document.getElementById("r_rating").value) || 0;
    let c = parseInt(document.getElementById("c_rating").value) || 0;

    let total = q + d + r + c;
    document.getElementById("t_rating").value = total;
}

// Attach input listeners with validation
document.getElementById("q_rating").addEventListener("input", function() {
    validateInput("q_rating", 50);
});
document.getElementById("d_rating").addEventListener("input", function() {
    validateInput("d_rating", 20);
});
document.getElementById("r_rating").addEventListener("input", function() {
    validateInput("r_rating", 10);
});
document.getElementById("c_rating").addEventListener("input", function() {
    validateInput("c_rating", 20);
});

// Run once on page load (edit mode)
window.onload = calculateTotal;
</script>



<script>
document.addEventListener("DOMContentLoaded", function() {
    const fromPeriod = document.getElementById("from_period");
    const toPeriod = document.getElementById("to_period");

    function setMinToPeriod() {
        if (fromPeriod.value) {
            let [year, month] = fromPeriod.value.split("-");
            year = parseInt(year);
            month = parseInt(month);

            // move to next month
            if (month === 12) {
                year++;
                month = 1;
            } else {
                month++;
            }

            // format yyyy-mm
            let nextMonth = year + "-" + (month < 10 ? "0" + month : month);
            toPeriod.min = nextMonth;

            // reset if invalid
            if (toPeriod.value && toPeriod.value < nextMonth) {
                toPeriod.value = "";
            }
        }
    }

    fromPeriod.addEventListener("change", setMinToPeriod);

    // initialize on page load
    setMinToPeriod();
});
</script>


