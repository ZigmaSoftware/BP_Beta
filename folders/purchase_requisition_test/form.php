<?php include 'function.php'; ?>

<?php
// var_dump($_SESSION);
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

// $requisition_number = generate_requisition_number();
$requisition_date   = date('Y-m-d');
$requested_by       = "";
$sales_order_id     = "";
$unit_id            = "";
$requisition_type   = "";
$remarks            = "";
$requisition_id     = "";
if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $unique_id = $_GET['unique_id'];
    $btn_text  = "Update";
    $btn_action = "update";
} else {
    $prefix = "pr";
     $unique_id =  unique_id($prefix);
}
if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $unique_id = $_GET['unique_id'];

    $table = "purchase_requisition";

    $main_columns = [
        "company_id",
        "project_id",
        "pr_number",
        "service_type",
        "requisition_for",
        "requisition_type",
        "requisition_date",
        "requested_by",
        "sales_order_id",
        "remarks"
    ];

    $main_result = $pdo->select([$table, $main_columns], ["unique_id" => $unique_id]);
    // print_r($main_result);

    if ($main_result->status && !empty($main_result->data)) {
        $main_data = $main_result->data[0];

        $company_name         = $main_data['company_id'];
        $project_id         = $main_data['project_id'];
        $purchase_requisition_category       = $main_data['service_type'];
        $requisition_for    = $main_data['requisition_for'];
        $pr_number    = $main_data['pr_number'];
        $requisition_type   = $main_data['requisition_type'];
        $requisition_date   = $main_data['requisition_date'];
        $requested_by       = $main_data['requested_by'];
        $sales_order        = $main_data['sales_order_id'];
        $remarks            = $main_data['remarks'];

        $btn_text  = "Update";
        $btn_action = "update";
    }

    // Optionally fetch sublist (used if you need to preload sublist in PHP)
    // In your structure, sublist is dynamically loaded via DataTable JS, so not strictly required here:
    /*
    $items_result = $pdo->select([
        "purchase_requisition_items",
        [
            "item_code",
            "item_description",
            "quantity",
            "uom",
            "preferred_vendor_id",
            "budgetary_rate",
            "item_remarks",
            "required_delivery_date"
        ]
    ], ["main_unique_id" => $unique_id]);

    if ($items_result->status) {
        $items = $items_result->data;
    }
    */
}


// $items              = [];

// if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
//   $requisition_id = $_GET['unique_id'];

//   $columns = [
//     "requisition_number", "requisition_date", "requested_by", "sales_order_id", "unit_id", "requisition_type", "remarks"
//   ];

//   $where = ["requisition_id" => $requisition_id];
//   $table = "purchase_requisition";

//   $result = $pdo->select([$table, $columns], $where);
//   if ($result->status) {
//     $res = $result->data[0];
//     $requisition_number = $res['requisition_number'];
//     $requisition_date = $res['requisition_date'];
//     $requested_by = $res['requested_by'];
//     $sales_order_id = $res['sales_order_id'];
//     $unit_id = $res['unit_id'];
//     $requisition_type = $res['requisition_type'];
//     $remarks = $res['remarks'];
//     $btn_text = "Update";
//     $btn_action = "update";
//   }

//   $items_result = $pdo->select([
//     "purchase_requisition_items",
//     ["item_code", "item_description", "quantity", "uom", "preferred_vendor_id", "budgetary_rate", "remarks", "required_delivery_date"]
//   ], ["requisition_id" => $requisition_id]);

//   if ($items_result->status) {
//     $items = $items_result->data;
//   }
// }

// item_name_list

$item_name_list_options        = item_name_list();
$item_name_list_options        = select_option($item_name_list_options,"Select the Item/Code",$item_name_list);

$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$company_name);


$sales_order_options        = sales_order();
$sales_order_options        = select_option($sales_order_options,"Select the Sales Order",$sales_order);

        $project_options  = get_project_name();

        $project_options  = select_option($project_options,"Select the Project Name",$project_id);
// $service_options        = sales_order();
// $service_options        = select_option($service_options,"Select the Sales Order",$sales_order);

$purchase_requisition_category_options        = purchase_requisition_category();
$purchase_requisition_category_options        = select_option($purchase_requisition_category_options,"Select the Purchase Requisition category",$purchase_requisition_category);


// $units              = unit_master_options($unit_id);
// $requisition_types  = select_option([
//   ['id' => 'Regular'],
//   ['id' => 'Service'],
//   ['id' => 'Capital']
// ], 'Select', $requisition_type);


$requisition_type_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Regular"
    ],
    2 => [
        "unique_id" => "683568ca2fe8263239",
        "value"     => "Service"
    ],
    3 => [
        "unique_id" => "683588840086c13657",
        "value"     => "Capital"
    ]
];


$requisition_type_options    = select_option($requisition_type_options,"Select",$requisition_type);

$requisition_for_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Direct"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "SO"
    ],
    3 => [
        "unique_id" => "3",
        "value"     => "Ordered BOM"
    ]
];


$requisition_for_options    = select_option($requisition_for_options,"Select",$requisition_for);


// $vendors            = vendor_master_options();

$today = date('Y-m-d');
?>

<style>

    /* Clickable FAB items with sublist */
    .fab-toggle {
        color: #e97027;              /* Bootstrap blue */
        cursor: pointer;             /* Hand cursor */
        transition: color 0.2s ease-in-out;
        user-select: none;           /* Prevent text selection */
    }
    .fab-toggle:hover {
        color: #e97027;              /* Darker blue on hover */
        text-decoration: none;  /* Optional */
    }
    
    /* Non-clickable items */
    .no-sublist {
        color: --inherit;                 /* Greyish normal text */
        cursor: default;             /* Normal arrow cursor */
        user-select: none;           /* Still prevent selection */
    }
    .no-sublist:hover {
        color: --inherit;                 /* No color change on hover */
        text-decoration: none;       /* No underline */
    }

    .requisition_sublist_add_btn.btn-primary a,
    .requisition_sublist_add_btn.btn-primary a span {
        color: #fff !important;
    }


</style>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
                <form class="was-validated" id="purchase_requisition_form">
                    <input type="hidden" id="unique_id" name="unique_id" value="<?= $unique_id ?>">
                    <input type="hidden" name="sublist_unique_id" id="sublist_unique_id" value="">
                        <?php if ($btn_action == "update") : ?>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label labelright">PR Number</label>
                                <div class="col-md-3">
                                    <input type="text" name="pr_number" id="pr_number" class="form-control" value="<?= $pr_number ?>" readonly>
                                </div>
                            </div>
                        <?php endif; ?>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label labelright">Company Name</label>
                        <div class="col-md-3">
                            <select name="company_id" id="company_id"  class="form-control select2"  onchange="get_project_name(this.value);" required>
                                <?= $company_name_options ?>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label labelright">Project Name</label>
                        <div class="col-md-3">
                            <select name="project_id" id="project_id" class="form-control select2" required>
                                <?= $project_options ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                                                <label class="col-md-2 col-form-label labelright">Requisition Type</label>
                        <div class="col-md-3">
                            <select name="requisition_type" id="requisition_type" class="form-control select2" required>
                                <?php echo $requisition_type_options;?>
                            </select>
                        </div>


                        <label class="col-md-2 col-form-label labelright">Requisition For</label>
                        <div class="col-md-3">
                            <select name="requisition_for" id="requisition_for" class="form-control select2" onchange="get_linked_so(this.value);" required>
                                <?= $requisition_for_options; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label labelright">Linked Sales Order</label>
                        <div class="col-md-3">
                            <select name="sales_order_id" id="sales_order_id" class="form-control select2">
                                <?= $sales_order_options ?>
                            </select>
                        </div>
                        <label class="col-md-2 col-form-label labelright">Requisition Date</label>
                        <div class="col-md-3">
                            <input type="date" name="requisition_date" id="requisition_date" class="form-control"  value="<?= $requisition_date ?>" required readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label labelright">Requested By</label>
                        <div class="col-md-3">
                            <input type="text" name="requested_by" id="requested_by" class="form-control" value="<?= $_SESSION['user_name'] ?>" disabled required>
                        </div>
                        
                        <label class="col-md-2 col-form-label labelright">Remarks</label>
                        <div class="col-md-3">
                            <textarea name="remarks" id="remarks" class="form-control" rows="2"><?= $remarks ?></textarea>
                        </div>
                        
                    </div>
          <hr>

<div class="col-12">
    <!-- Table Begins -->
    <div class="table-responsive">
    <table id="purchase_sublist_datatable" class="table table-bordered table-striped w-100">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Item Code</th>
                <th>Description</th>
                <th>Qty</th>
                <th>UOM</th>
                <!--<th>Vendor</th>-->
                <!--<th>Rate</th>-->
                <th>Remarks</th>
                <th>Delivery Date</th>
                <th><button type="button" class="addRow btn btn-success d-none">Add</button>Action</th>
            </tr>

            <!-- Input Form Row -->
            <tr id="requisition_details_form">
                <!--<input type="hidden" name="sublist_unique_id" id="sublist_unique_id" value="">-->
                
                <td>#</td>
                <td>
                    <select id="item_code" name="item_code" class="form-control select2" >
                    </select>
                </td>

                <td>
                    <input type="text" id="item_description" name="item_description" class="form-control" placeholder="Description" disabled>
                </td>
                <td>
                    <input type="number" id="quantity" name="quantity" step="0.01" class="form-control" placeholder="Qty">
                </td>
                <td>
                    <input type="text" id="uom" name="uom" class="form-control" placeholder="UOM" disabled>
                    <input type="hidden" id="uom_id" name="uom_id">
                </td>
                <!--<th>-->
                <!--    <select id="preferred_vendor_id" name="preferred_vendor_id" class="form-control select2">-->
                       
                <!--    </select>-->
                <!--</th>-->
                <!--<th>-->
                <!--    <input type="number" id="budgetary_rate" name="budgetary_rate" step="0.01" class="form-control" placeholder="Rate">-->
                <!--</th>-->
                <td>
                    <input type="text" id="item_remarks" name="item_remarks" class="form-control" placeholder="Remarks">
                </td>
                <td>
                    <input type="date" id="required_delivery_date" name="required_delivery_date" class="form-control" min="<?php echo $today; ?>">
                </td>
                <td>
                <button type="button" class="btn btn-success waves-effect waves-light requisition_sublist_add_btn" onclick="requisition_sublist_add_update()">
                    <a href="javascript:void(0);"><span id="sublist_btn_text">Add</span></a>
                </button>
                </td>
            </tr>
        </thead>
        <tbody>
            <!-- Filled by DataTable JS -->
        </tbody>
    </table></div>
    <!-- Table Ends -->
</div>

<div class="col-md-12 text-end">
    <!-- Cancel, Save and Update Buttons -->
    <?php echo btn_cancel($btn_cancel); ?>
    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
</div>


        </form>
      </div>
    </div>
  </div>
</div>
