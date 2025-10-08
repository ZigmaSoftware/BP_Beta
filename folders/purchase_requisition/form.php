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

$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$company_name);


$sales_order_options        = sales_order();
$sales_order_options        = select_option($sales_order_options,"Select the Sales Order",$sales_order);

// $service_options        = sales_order();
// $service_options        = select_option($service_options,"Select the Sales Order",$sales_order);

$purchase_requisition_category_options        = purchase_requisition_category();
$purchase_requisition_category_options        = select_option($purchase_requisition_category_options,"Select the Purchase Requisition category",$purchase_requisition_category);


// $units              = unit_master_options($unit_id);
$requisition_types  = select_option([
  ['id' => 'Regular'],
  ['id' => 'Service'],
  ['id' => 'Capital']
], 'Select', $requisition_type);



// $vendors            = vendor_master_options();
?>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
                <form class="was-validated" id="purchase_requisition_form">
                    <input type="hidden" name="unique_id" value="<?= $unique_id ?>">

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Company Name</label>
                        <div class="col-md-4">
                            <select name="company_id" id="company_id"  class="form-control select2"  onchange="get_project_name(this.value);" required>
                                <?= $company_name_options ?>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label">Project Name</label>
                        <div class="col-md-4">
                            <select name="project_id" id="project_id" class="form-control select2" onchange="get_linked_so(this.value);" required>
                                <?= $project_options ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Type of Service</label>
                        <div class="col-md-4">
                            <select name="service_type" id="service_type" class="form-control select2" required>
                                <?= $purchase_requisition_category_options ?>
                            </select>
                            <!--<input type="text" name="service_type" id="service_type" class="form-control" value="<?= $service_type ?>" required>-->
                        </div>

                        <label class="col-md-2 col-form-label">Requisition For</label>
                        <div class="col-md-4">
                            <select name="requisition_for" id="requisition_for" class="form-control select2" required>
                                <option value="">Select</option>
                                <option value="Direct" <?= $requisition_for == 'Direct' ? 'selected' : '' ?>>Direct</option>
                                <option value="SO" <?= $requisition_for == 'SO' ? 'selected' : '' ?>>Sales Order</option>
                                <option value="Planning WO" <?= $requisition_for == 'Planning WO' ? 'selected' : '' ?>>Planning WO</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Requisition Type</label>
                        <div class="col-md-4">
                            <select name="requisition_type" id="requisition_type" class="form-control select2" required>
                                <option value="">Select</option>
                                <option value="Regular" <?= $requisition_type == 'Regular' ? 'selected' : '' ?>>Regular</option>
                                <option value="Service" <?= $requisition_type == 'Service' ? 'selected' : '' ?>>Service</option>
                                <option value="Capital" <?= $requisition_type == 'Capital' ? 'selected' : '' ?>>Capital</option>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label">Requisition Date</label>
                        <div class="col-md-4">
                            <input type="date" name="requisition_date" id="requisition_date" class="form-control" value="<?= $requisition_date ?>" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Requested By</label>
                        <div class="col-md-4">
                            <input type="text" name="requested_by" id="requested_by" class="form-control" value="<?= $_SESSION['user_name'] ?>" disabled required>
                        </div>

                        <label class="col-md-2 col-form-label">Linked Sales Order</label>
                        <div class="col-md-4">
                            <select name="sales_order_id" id="sales_order_id" class="form-control select2">
                                <?= $sales_order_options ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <!--<label class="col-md-2 col-form-label">Unit / Site</label>-->
                        <!--<div class="col-md-4">-->
                        <!--    <select name="unit_id" id="unit_id" class="form-control select2" required>-->
                        <!--        <?= $unit_options ?>-->
                        <!--    </select>-->
                        <!--</div>-->

                        <label class="col-md-2 col-form-label">Remarks</label>
                        <div class="col-md-4">
                            <textarea name="remarks" id="remarks" class="form-control" rows="2"><?= $remarks ?></textarea>
                        </div>
                    </div>

          <hr>

          <h5>Items</h5>
          <table class="table table-bordered" id="requisition_items_table">
            <thead>
              <tr>
                <th>Item Code</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>UOM</th>
                <th>Preferred Vendor</th>
                <th>Budgetary Rate</th>
                <th>Remarks</th>
                <th>Required Delivery Date</th>
                <th>Action</th>
              </tr>
            </thead>
<tbody>
<?php if (!empty($items)) {
  $row_index = 0;
  foreach ($items as $item) {
?>
  <tr>
    <td><input type="text" id="item_code_<?= $row_index ?>" name="item_code[]" class="form-control" value="<?= $item['item_code'] ?>" required></td>
    <td><input type="text" id="item_description_<?= $row_index ?>" name="item_description[]" class="form-control" value="<?= $item['item_description'] ?>"></td>
    <td><input type="number" id="quantity_<?= $row_index ?>" name="quantity[]" step="0.01" class="form-control" value="<?= $item['quantity'] ?>" required></td>
    <td><input type="text" id="uom_<?= $row_index ?>" name="uom[]" class="form-control" value="<?= $item['uom'] ?>" required></td>
    <td>
      <select id="vendor_<?= $row_index ?>" name="preferred_vendor_id[]" class="form-control select2">
        <?= vendor_master_options($item['preferred_vendor_id']); ?>
      </select>
    </td>
    <td><input type="number" id="budgetary_rate_<?= $row_index ?>" name="budgetary_rate[]" step="0.01" class="form-control" value="<?= $item['budgetary_rate'] ?>"></td>
    <td><input type="text" id="item_remarks_<?= $row_index ?>" name="item_remarks[]" class="form-control" value="<?= $item['remarks'] ?>"></td>
    <td><input type="date" id="required_delivery_date_<?= $row_index ?>" name="required_delivery_date[]" class="form-control" value="<?= $item['required_delivery_date'] ?>" required></td>
    <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
  </tr>
<?php $row_index++; } } ?>
</tbody>

          </table>

          <button type="button" class="btn btn-info" id="add_item_row">Add Item</button>

          <div class="mt-4">
            <?php echo btn_cancel($btn_cancel); ?>
            <?php echo btn_createupdate($folder_name_org, $requisition_id, $btn_text); ?>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
