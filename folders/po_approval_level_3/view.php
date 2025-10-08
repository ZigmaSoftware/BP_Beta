<?php
// ====== Bootstrap (same style as your reference file) ======
include '../../config/dbconfig.php';
include '../../config/new_db.php';
// include '../../config/Db.class.php';

/**
 * Helpers
 */
function fmtDate($d) {
    if (empty($d) || $d === '0000-00-00' || $d === '0000-00-00 00:00:00') return '-';
    $ts = strtotime($d);
    return $ts ? date('d-m-Y', $ts) : '-';
}
function fmtNum($n, $dec = 2) {
    if ($n === null || $n === '') return '0.00';
    return number_format((float)$n, $dec);
}
function val($row, $key, $fallback='-') {
    return isset($row[$key]) && $row[$key] !== '' ? $row[$key] : $fallback;
}
function get_item_name($pdo, $item_uid) {
    if (empty($item_uid)) return '-';
    $res = $pdo->select(
        ['item_master', ['item_name']],
        ['unique_id' => $item_uid],   // 🔑 not item_code, use unique_id
        "",
        "LIMIT 1"
    );
    if ($res->status && !empty($res->data)) {
        return $res->data[0]['item_name'];
    }
    return $item_uid; // fallback show id if not found
}
function get_uom_name($pdo, $uom_uid) {
    if (empty($uom_uid)) return '-';
    $rows = unit_name($uom_uid);   // this already queries by unique_id
    if (is_array($rows) && !empty($rows)) {
        return $rows[0]['unit_name'];   // because function returns array of rows
    }
    return $uom_uid; // fallback to raw id
}

function getParamUniqueId() {
    // allow ?unique_id=... OR ?id=... OR raw query string like ?68a40f...
    if (!empty($_GET['unique_id'])) return $_GET['unique_id'];
    if (!empty($_GET['id'])) return $_GET['id'];
    if (!empty($_SERVER['QUERY_STRING'])) {
        // if query string has no '=', treat as unique_id
        if (strpos($_SERVER['QUERY_STRING'], '=') === false) {
            return $_SERVER['QUERY_STRING'];
        }
        // else, parse normally
    }
    return null;
}

$uid = getParamUniqueId();
if (!$uid) {
    die('❌ Invalid Request: missing unique id');
}

$po = [];
{
    $table     = 'purchase_order';
    $columns   = ['*'];
    $where     = ['unique_id' => $uid];
    $details   = [$table, $columns];

    $res = $pdo->select($details, $where, "", "LIMIT 1");
    if ($res->status && !empty($res->data)) {
        $po = $res->data[0];
    } else {
        die('❌ Purchase Order not found');
    }
}

/**
 * 2) Lookups (company, project, supplier)
 */
$company_name = 'BLUE PLANET INTEGRATED WASTE SOLUTIONS LIMITED';
$company_address = 'Kohinoor World Towers T3, Office No.306, Opp. Empire Estate,<br>Old Mumbai-Pune Hwy, Pimpri Colony, Pune Maharashtra-411018.';

if (!empty($po['company_id'])) {
    $res = $pdo->select(['company_creation', ['company_name','address']], ['unique_id' => $po['company_id']], "", "LIMIT 1");
    if ($res->status && !empty($res->data)) {
        $r = $res->data[0];
        $company_name = val($r,'company_name',$company_name);
        $company_address = nl2br(val($r,'address',$company_address));
    }
}

$project_name = '-';
if (!empty($po['project_id'])) {
    $res = $pdo->select(['project_creation', ['project_name']], ['unique_id' => $po['project_id']], "", "LIMIT 1");
    if ($res->status && !empty($res->data)) {
        $project_name = val($res->data[0], 'project_name', '-');
    }
}

$supplier_name = '-';
if (!empty($po['supplier_id'])) {
    $res = $pdo->select(['supplier_profile', ['supplier_name']], ['unique_id' => $po['supplier_id']], "", "LIMIT 1");
    if ($res->status && !empty($res->data)) {
        $supplier_name = val($res->data[0], 'supplier_name', '-');
    }
}

function get_tax_label($pdo, $tax_uid, $fallback_perc = '') {
    static $cache = null;
    if ($tax_uid === '' && $fallback_perc === '') return '-';

    // Build cache once
    if ($cache === null) {
        $res = $pdo->select(['tax', ['unique_id','tax_name']], ['is_active' => 1, 'is_delete' => 0]);
        $cache = [];
        if ($res->status && !empty($res->data)) {
            foreach ($res->data as $r) {
                $cache[$r['unique_id']] = $r['tax_name']; // e.g. "GST 18%"
            }
        }
    }

    if ($tax_uid && isset($cache[$tax_uid])) {
        return $cache[$tax_uid];
    }

    // Fallback: if the row only has a percentage
    if ($fallback_perc !== '') {
        return "GST " . rtrim(rtrim((string)$fallback_perc, '0'), '.') . "%";
    }
    return '-';
}

function discount_type_label($code) {
    $code = (string)$code;
    if ($code === '1') return 'Percentage (%)';
    if ($code === '2') return 'Amount (₹)';
    return '-';
}

function discount_value_display($it) {
    // Your rows sometimes have either 'discount' or 'discount_percentage'
    $type = (string)val($it, 'discount_type', '');
    $disc = val($it, 'discount', val($it, 'discount_percentage', ''));

    if ($disc === '' || $disc === null) return '-';

    if ($type === '1') { // Percentage
        // clean trailing zeros (e.g., 5.00 -> 5)
        $txt = rtrim(rtrim((string)$disc, '0'), '.');
        return $txt . '%';
    }
    if ($type === '2') { // Amount
        return number_format((float)$disc, 2);
    }
    return '-';
}


$items = [];
if (!empty($po['screen_unique_id'])) {
    $res = $pdo->select(['purchase_order_items', ['*']], ['screen_unique_id' => $po['screen_unique_id']]);
    if ($res->status && !empty($res->data)) {
        $items = $res->data;
    }
}

// ----- PO Type mapping -----
$po_type_options = [
    1 => ["unique_id" => "1",                  "value" => "Regular"],
    2 => ["unique_id" => "683568ca2fe8263239", "value" => "Service"],
    3 => ["unique_id" => "683588840086c13657", "value" => "Capital"],
];

$po_type_map = [];
foreach ($po_type_options as $opt) {
    $po_type_map[(string)$opt['unique_id']] = $opt['value'];
}

// resolve PO type text from DB value
$po_type_code = val($po, 'purchase_order_type', '');
$po_type      = $po_type_map[(string)$po_type_code] ?? '-';

$entry_date = fmtDate(val($po, 'entry_date', ''));
$gst_no = val($po,'gst_no','-');
$pan_no = val($po,'pan_no','-');
$contact_person = val($po,'contact_person','-');
$vendor_contact_no = val($po,'vendor_contact_no','-');
$quotation_no = val($po,'quotation_no','-');
$quotation_date = fmtDate(val($po,'quotation_date',''));

$billing_address  = nl2br(val($po,'billing_address','-'));
$shipping_address = nl2br(val($po,'shipping_address','-'));
$remarks_header   = nl2br(val($po,'remarks','-'));

$terms_payment_days = val($po,'payment_days','-');
$terms_delivery     = val($po,'delivery','-');
$terms_remarks      = nl2br(val($po,'dealer_reference','-')); // or use $po['remarks'] if you want header remarks duplicated here

$total_basic_value          = fmtNum(val($po,'net_amount',0));
$freight_amount_display     = fmtNum(val($po,'freight_amount',0));
$other_charges_display      = fmtNum(val($po,'other_charges_percentage',0));
$packing_forwarding_display = fmtNum(val($po,'packing_forwarding_amount',0));
$total_gst_amount           = fmtNum(val($po,'total_gst_amount',0));
$gross_amount               = fmtNum(val($po,'gross_amount',0));



// ---------- Prepared & Approvals ----------
function resolve_user_display_name($uid) {
    if (empty($uid)) return '';
    $rows = user_name($uid); // <- assumes this helper already exists
    if (is_array($rows) && !empty($rows)) {
        $u = $rows[0];
        $staff = isset($u['staff_name']) ? trim((string)$u['staff_name']) : '';
        $uname = isset($u['user_name']) ? trim((string)$u['user_name']) : '';
        return $staff !== '' ? $staff : $uname;
    }
    return '';
}
// ----- Prepared -----
$prepared_by_name = '-';
$prepared_dt = '-';
if (!empty($po['created_user_id'])) {
    $n = resolve_user_display_name($po['created_user_id']);
    if ($n !== '') $prepared_by_name = $n;
}
if (!empty($po['created']) && $po['created'] !== '0000-00-00 00:00:00') {
    $prepared_dt = date("d-m-Y", strtotime($po['created']));
}

// ----- Level 1 -----
$lvl1_name = $lvl1_dt = '-';
if ((string)val($po,'status','0') === '1') {
    $uid = val($po,'poa_user_id','');
    $dt  = val($po,'poa_created_dt','');
    if ($uid !== '') {
        $n = resolve_user_display_name($uid);
        if ($n !== '') $lvl1_name = '<span class="badge bg-success">Approved</span> ' . $n;
    }
    if (!empty($dt) && $dt !== '0000-00-00 00:00:00') {
        $lvl1_dt = date("d-m-Y", strtotime($dt));
    }
} elseif ((string)val($po,'status','0') === '2') {
    $uid = val($po,'poa_user_id','');
    $dt  = val($po,'poa_created_dt','');
    if ($uid !== '') {
        $n = resolve_user_display_name($uid);
        if ($n !== '') $lvl1_name = '<span class="badge bg-danger">Rejected</span> ' . $n;
    }
    if (!empty($dt) && $dt !== '0000-00-00 00:00:00') {
        $lvl1_dt = date("d-m-Y", strtotime($dt));
    }
}

// ----- Level 2 -----
$lvl2_name = $lvl2_dt = '-';
if ((string)val($po,'lvl_2_status','0') === '1') {
    $uid = val($po,'lvl_2_user_id','');
    $dt  = val($po,'lvl_2_created_dt','');
    if ($uid !== '') {
        $n = resolve_user_display_name($uid);
        if ($n !== '') $lvl2_name = '<span class="badge bg-success">Approved</span> ' . $n;
    }
    if (!empty($dt) && $dt !== '0000-00-00 00:00:00') {
        $lvl2_dt = date("d-m-Y", strtotime($dt));
    }
} elseif ((string)val($po,'lvl_2_status','0') === '2') {
    $uid = val($po,'lvl_2_user_id','');
    $dt  = val($po,'lvl_2_created_dt','');
    if ($uid !== '') {
        $n = resolve_user_display_name($uid);
        if ($n !== '') $lvl2_name = '<span class="badge bg-danger">Rejected</span> ' . $n;
    }
    if (!empty($dt) && $dt !== '0000-00-00 00:00:00') {
        $lvl2_dt = date("d-m-Y", strtotime($dt));
    }
}

// ----- Level 3 -----
$lvl3_name = $lvl3_dt = '-';
if ((string)val($po,'lvl_3_status','0') === '1') {
    $uid = val($po,'lvl_3_approved_by','');
    $dt  = val($po,'lvl_3_approved_date','');
    if ($uid !== '') {
        $n = resolve_user_display_name($uid);
        if ($n !== '') $lvl3_name = '<span class="badge bg-success">Approved</span> ' . $n;
    }
    if (!empty($dt) && $dt !== '0000-00-00 00:00:00') {
        $lvl3_dt = date("d-m-Y", strtotime($dt));
    }
} elseif ((string)val($po,'lvl_3_status','0') === '2') {
    $uid = val($po,'lvl_3_approved_by','');
    $dt  = val($po,'lvl_3_approved_date','');
    if ($uid !== '') {
        $n = resolve_user_display_name($uid);
        if ($n !== '') $lvl3_name = '<span class="badge bg-danger">Rejected</span> ' . $n;
    }
    if (!empty($dt) && $dt !== '0000-00-00 00:00:00') {
        $lvl3_dt = date("d-m-Y", strtotime($dt));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Purchase Order</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .label-title {
      font-weight: 600;
      color: #555;
    }
    .page-header {
      font-size: 1.3rem;
      font-weight: bold;
      margin-bottom: 10px;
      /*border-bottom: 2px solid #0d6efd;*/
      padding-bottom: 8px;
    }
    .po-section {
      margin-bottom: 1.5rem;
    }
    .po-box {
      background: #fff;
      border-radius: 6px;
      padding: 15px;
      box-shadow: 0 0 5px rgba(0,0,0,0.05);
    }
    .company-name {
      font-weight: bold;
      font-size: 1.1rem;
      color: #25507d;
    }
    .company-address {
      font-size: 0.9rem;
      color: #555;
    }
    .text-orange{color:#fd850d;}
  </style>
</head>
<body class="bg-light">

<div class="container py-4">
 <div class="po-box">
 <!-- Header with logo & company info -->
<div class="d-flex align-items-center mb-4 pb-3 border-bottom p-3 p-0">
  <div>
    <img src="https://zigma.in/blue_planet_beta/assets/images/logo.png" alt="Company Logo" style="height: 60px;">
  </div>
  <div class="ms-3">
    <div class="company-name">BLUE PLANET INTEGRATED WASTE SOLUTIONS LIMITED</div>
    <div class="company-address">
      Kohinoor World Towers T3, Office No.306, Opp. Empire Estate,<br>
      Old Mumbai-Pune Hwy, Pimpri Colony, Pune Maharashtra-411018.
    </div>
  </div>
</div>


  <div class="page-header text-center"> Purchase Order View</div>

 

<!-- PO & Invoice Details Section -->
 <!-- PO & Address Section -->
    <div class="row po-section">
      <div class="col-md-4">
        <h6 class="text-orange fw-bold mb-3">Billing & Shipping Address</h6>
        <div class="mb-3">
          <span class="label-title">Billing Address:</span><br>
          <?= $billing_address ?>
        </div>
        <div class="mb-3">
          <span class="label-title">Shipping Address:</span><br>
          <?= $shipping_address ?>
        </div>
        <div class="mb-3">
          <span class="label-title">Remarks</span><br>
          <?= $remarks_header ?>
        </div>
      </div>

      <div class="col-md-4">
        <h6 class="text-orange fw-bold mb-3">PO Details</h6>
        <table class="table table-borderless table-sm align-middle mb-0">
          <tbody>
            <tr>
              <th width="35%" class="fw-semibold">Project Name :</th>
              <td><?= htmlspecialchars($project_name) ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">PO Type :</th>
              <td><?= htmlspecialchars($po_type) ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">Supplier Name :</th>
              <td><?= htmlspecialchars($supplier_name) ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">Entry Date :</th>
              <td><?= $entry_date ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">GST No :</th>
              <td><?= htmlspecialchars($gst_no) ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">PAN No :</th>
              <td><?= htmlspecialchars($pan_no) ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="col-md-4">
        <h6 class="text-orange fw-bold mb-3">Contact Details</h6>
        <table class="table table-borderless table-sm align-middle mb-0">
          <tbody>
            <tr>
              <th class="fw-semibold">Contact Person :</th>
              <td><?= htmlspecialchars($contact_person) ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">Contact No :</th>
              <td><?= htmlspecialchars($vendor_contact_no) ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">Quotation No :</th>
              <td><?= htmlspecialchars($quotation_no) ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">Quotation Date :</th>
              <td><?= $quotation_date ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Items -->
    <div class="table-responsive po-section mt-2">
      <table class="table table-bordered table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>UOM</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Discount Type</th>
            <th>Discount(%)</th>
            <th>Tax</th>
            <th>Amount</th>
            <th>Delivery Date</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($items)): $sn=1; foreach ($items as $it): ?>
          <tr>
            <td><?= $sn++ ?></td>
           <td><?php $itemUid = val($it,'item_code',''); echo htmlspecialchars(get_item_name($pdo, $itemUid));?></td>
           <td><?php $uomUid = val($it,'uom','');   echo htmlspecialchars(get_uom_name($pdo, $uomUid));?></td>

            <td><?= htmlspecialchars(val($it,'quantity','0')) ?></td>
            <td><?= fmtNum(val($it,'rate',0)) ?></td>
            <td><?= htmlspecialchars(discount_type_label(val($it,'discount_type',''))) ?></td>
            <td><?= htmlspecialchars(discount_value_display($it)) ?></td>

            <td>
              <?php
                $taxUid  = val($it,'tax','');                 // stores something like 'tax5ff82c...'
                $taxPerc = val($it,'tax_percentage','');      // sometimes stored as plain percent
                echo htmlspecialchars(get_tax_label($pdo, $taxUid, $taxPerc));
              ?>
            </td>

            <td><?= fmtNum(val($it,'amount',0)) ?></td>
            <td><?= fmtDate(val($it,'delivery_date','')) ?></td>
            <td><?= htmlspecialchars(val($it,'remarks','-')) ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="11" class="text-center text-muted">No items found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Terms & Totals -->
    <div class="row">
      <div class="col-md-8">
        <h6 class="text-orange fw-bold mb-3">Terms & Conditions</h6>
        <div class="d-flex mb-2">
          <span class="fw-semibold me-2" style="width:180px;">Payment Days</span>
          <span class="me-2">:</span>
          <span><?= htmlspecialchars($terms_payment_days) ?></span>
        </div>
        <div class="d-flex mb-2">
          <span class="fw-semibold me-2" style="width:180px;">Delivery</span>
          <span class="me-2">:</span>
          <span><?= htmlspecialchars($terms_delivery) ?></span>
        </div>
        <div class="d-flex mb-2">
          <span class="fw-semibold me-2" style="width:180px;">Remarks</span>
          <span class="me-2">:</span>
          <span><?= $terms_remarks ?></span>
        </div>
      </div>
<div class="col-md-4">
  <div class="d-flex mb-2">
    <span class="fw-semibold me-2" style="width:180px;">Total Basic Value</span>
    <span class="me-2">:</span>
    <span><?= $total_basic_value ?></span>
  </div>
  <div class="d-flex mb-2">
    <span class="fw-semibold me-2" style="width:180px;">Freight Charges</span>
    <span class="me-2">:</span>
    <span><?= $freight_amount_display ?></span>
  </div>
  <div class="d-flex mb-2">
    <span class="fw-semibold me-2" style="width:180px;">Other Charges</span>
    <span class="me-2">:</span>
    <span><?= $other_charges_display ?></span>
  </div>
  <div class="d-flex mb-2">
    <span class="fw-semibold me-2" style="width:180px;">Packing &amp; Forwarding</span>
    <span class="me-2">:</span>
    <span><?= $packing_forwarding_display ?></span>
  </div>
  <div class="d-flex mb-2">
    <span class="fw-semibold me-2" style="width:180px;">Total GST Amount</span>
    <span class="me-2">:</span>
    <span><?= $total_gst_amount ?></span>
  </div>
  <div class="d-flex mb-2">
    <span class="fw-semibold me-2" style="width:180px;">Gross Amount</span>
    <span class="me-2">:</span>
    <span><?= $gross_amount ?></span>
  </div>
</div>

<div class="approvals-block">
  <p><strong>Prepared By :</strong> <?= $prepared_by_name ?> (<?= $prepared_dt ?>)</p>
  <p><strong>Level 1 Approval :</strong> <?= $lvl1_name ?> (<?= $lvl1_dt ?>)</p>
  <p><strong>Level 2 Approval :</strong> <?= $lvl2_name ?> (<?= $lvl2_dt ?>)</p>
  <p><strong>Level 3 Approval :</strong> <?= $lvl3_name ?> (<?= $lvl3_dt ?>)</p>
</div>
    </div>

  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>



<div class="page">
    <div class="invoice-2 invoice-content">
    <div class="container ">
        <div class="row">

            <div class="col-lg-12 p-0">
                <div class="invoice-inner-2">
                    <div class="invoice-info" id="invoice_wrapper">
                        <div class="invoice-inner" style="margin-top:-30px;">
                            <div class="invoice-top">
                                <div class="row align-items-center">
                                                <div class="row align-items-center">
<div class="col-sm-6 invoice-name">
   
</div>
<br>

</div>
       
        
                         </div>
                         </div>
                                </div>
    

                           
                        </div>
                    </div>
                    
                </div>

    
    
        </td > </tr>
</tbody>
   </table>
   
   </div>
        </div>
    </div>
    
    </div>


</html>