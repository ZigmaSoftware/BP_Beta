<?php
// ====== Bootstrap ======
include '../../config/dbconfig.php';
include '../../config/new_db.php';

// ---------- Helpers ----------
function fmtDate($d) {
    if (empty($d) || $d === '0000-00-00' || $d === '0000-00-00 00:00:00') return '-';
    $ts = strtotime($d);
    return $ts ? date('d/m/Y', $ts) : '-';
}
function fmtNum($n, $dec = 2) {
    if ($n === null || $n === '') return number_format(0, $dec);
    return number_format((float)$n, $dec);
}
function val($row, $key, $fallback='-') {
    return (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) ? $row[$key] : $fallback;
}
function get_item_name($pdo, $item_uid) {
    if (empty($item_uid)) return '-';
    $res = $pdo->select(['item_master', ['item_name','hsn_code']], ['unique_id' => $item_uid], "", "LIMIT 1");
    if ($res->status && !empty($res->data)) {
        return [$res->data[0]['item_name'] ?: '-', $res->data[0]['hsn_code'] ?: ''];
    }
    return ['-', ''];
}
function get_uom_name($pdo, $uom_uid) {
    if (empty($uom_uid)) return '-';
    $rows = unit_name($uom_uid); // your common function
    if (is_array($rows) && !empty($rows)) return $rows[0]['unit_name'] ?? '-';
    return '-';
}
function get_tax_label($pdo, $tax_uid, $fallback_perc = '') {
    static $cache = null;
    if ($cache === null) {
        $res = $pdo->select(['tax', ['unique_id','tax_name','tax_value']], ['is_active' => 1, 'is_delete' => 0]);
        $cache = [];
        if ($res->status && !empty($res->data)) {
            foreach ($res->data as $r) {
                $cache[$r['unique_id']] = ['name'=>$r['tax_name'], 'perc'=>(float)$r['tax_value']];
            }
        }
    }
    if ($tax_uid && isset($cache[$tax_uid])) return $cache[$tax_uid]['name'];
    if ($fallback_perc !== '') return "GST " . rtrim(rtrim((string)$fallback_perc, '0'), '.') . "%";
    return '-';
}
function discount_type_label($code) {
    $code = (string)$code;
    if ($code === '1') return 'Percentage (%)';
    if ($code === '2') return 'Amount (₹)';
    return '-';
}
function discount_value_display_print($it) {
    $type = (string)val($it, 'discount_type', '');
    $disc = val($it, 'discount', val($it, 'discount_percentage', ''));
    if ($disc === '' || $disc === null) return '0.00';

    if ($type === '1') { // Percentage
        return rtrim(rtrim((string)$disc, '0'), '.'); 
    }
    if ($type === '2') { // Flat amount
        return fmtNum($disc); // ✅ Show amount instead of 0
    }
    return "0.00";
}

function amountToWordsINR($number) {
    // Simple INR converter (crore/lakh/thousand)
    $no = floor($number);
    $point = round($number - $no, 2) * 100;
    $hundred = null;
    $digits_1 = strlen($no);
    $i = 0;
    $str = [];
    $words = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
    ];
    $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
    while ($i < $digits_1) {
        $divider = ($i == 2) ? 10 : 100;
        $numberPart = floor($no % $divider);
        $no = floor($no / $divider);
        $i += ($divider == 10) ? 1 : 2;

        if ($numberPart) {
            $counter = count($str);
            $plural = ($counter && $numberPart > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            if ($numberPart < 21) {
                $str[] = $words[$numberPart] . " " . $digits[$counter] . $plural . " " . $hundred;
            } else {
                $str[] = $words[floor($numberPart / 10) * 10] . " " . $words[$numberPart % 10] . " " . $digits[$counter] . $plural . " " . $hundred;
            }
        } else $str[] = null;
    }
    $str = array_reverse($str);
    $result = trim(implode('', $str));
    $points = ($point) ? " and " . $words[floor($point / 10) * 10] . " " . $words[$point % 10] . " Paise" : "";
    return ($result ? $result . " Rupees" : "Zero") . $points . " Only";
}
function getParamUniqueId() {
    if (!empty($_GET['unique_id'])) return $_GET['unique_id'];
    if (!empty($_GET['id'])) return $_GET['id'];
    if (!empty($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], '=') === false) {
        return $_SERVER['QUERY_STRING'];
    }
    return null;
}

// ---------- Load Header ----------
$uid = getParamUniqueId();
if (!$uid) { die('❌ Invalid Request: missing unique id'); }

$po = [];
$res = $pdo->select(['purchase_order', ['*']], ['unique_id' => $uid], "", "LIMIT 1");
if ($res->status && !empty($res->data)) { $po = $res->data[0]; } else { die('❌ Purchase Order not found'); }

// ---------- Company / Project ----------
$company = [
    'name'   => 'BLUE PLANET INTEGRATED WASTE SOLUTIONS LIMITED',
    'address'=> "Kohinoor World Towers T3, Office No.306, Opp. Empire Estate,\nOld Mumbai-Pune Hwy, Pimpri Colony, Pune Maharashtra-411018.",
    'phone'  => '-',
    'email'  => '-',
    'gst'    => '-',
    'pan'    => '-',
];

if (!empty($po['company_id'])) {
    $res = $pdo->select(
        ['company_creation', ['company_name','address','tel_number','contact_email_id','gst_number','pan_number']],
        ['unique_id' => $po['company_id']],
        "",
        "LIMIT 1"
    );
    if ($res->status && !empty($res->data)) {
        $c = $res->data[0];
        $company['name']    = val($c, 'company_name', $company['name']);
        $company['address'] = val($c, 'address', $company['address']);

        // Normalize blanks/zeros to '-'
        $phone = val($c, 'tel_number', '-');
        $company['phone'] = (trim($phone) === '' || $phone === '0') ? '-' : $phone;

        $email = val($c, 'contact_email_id', '-');
        $company['email'] = (trim($email) === '') ? '-' : $email;

        $gst = val($c, 'gst_number', '-');
        $company['gst'] = (trim($gst) === '') ? '-' : $gst;

        $pan = val($c, 'pan_number', '-');
        $company['pan'] = (trim($pan) === '') ? '-' : $pan;
    }
}


$project_name = '-';
$project_code = '';
if (!empty($po['project_id'])) {
    $res = $pdo->select(['project_creation', ['project_name','project_code']], ['unique_id' => $po['project_id']], "", "LIMIT 1");
    if ($res->status && !empty($res->data)) {
    $row = $res->data[0];
    $project_name = isset($row['project_name']) ? $row['project_name'] : '-';
    $project_code = isset($row['project_code']) ? $row['project_code'] : '';
}

}

// ---------- Supplier (fixed to actual columns) ----------
$supplier_name      = '-';
$supplier_address   = '-';
$contact_person     = '-';   // no column in table, will stay '-' unless you add it later
$vendor_contact_no  = '-';
$supplier_email     = '-';
$gst_no             = '-';
$pan_no             = '-';

if (!empty($po['supplier_id'])) {
    $res = $pdo->select(
        ['supplier_profile', [
            'supplier_name','address',
            'contact_no','email_id',
            'gst_no','pan_no'
        ]],
        ['unique_id' => $po['supplier_id']],
        "",
        "LIMIT 1"
    );

    if ($res->status && !empty($res->data)) {
        $row               = $res->data[0];
        $supplier_name     = val($row, 'supplier_name', '-');
        $supplier_address  = nl2br(val($row, 'address', '-'));
        $vendor_contact_no = val($row, 'contact_no', '-');
        $supplier_email    = val($row, 'email_id', '-');
        $gst_no            = val($row, 'gst_no', '-');
        $pan_no            = val($row, 'pan_no', '-');
    }
}


// ---------- Items ----------
$items = [];
if (!empty($po['screen_unique_id'])) {
    $res = $pdo->select(['purchase_order_items', ['*']], ['screen_unique_id' => $po['screen_unique_id'],]);
    if ($res->status && !empty($res->data)) $items = $res->data;
}

// ---------- PO Type mapping ----------
$po_type_options = [
    1 => ["unique_id" => "1",                  "value" => "Regular"],
    2 => ["unique_id" => "683568ca2fe8263239", "value" => "Service"],
    3 => ["unique_id" => "683588840086c13657", "value" => "Capital"],
];
$po_type_map = [];
foreach ($po_type_options as $opt) { $po_type_map[(string)$opt['unique_id']] = $opt['value']; }
$po_type = $po_type_map[(string)val($po, 'purchase_order_type','')] ?? '-';

// ---------- Convenience ----------
$po_no          = val($po,'purchase_order_no','-');
$entry_date     = fmtDate(val($po,'entry_date',''));
$quotation_no   = val($po,'quotation_no','-');
$quotation_dt   = fmtDate(val($po,'quotation_date',''));

// These two come from PO header (keep if your print template expects them):
$contact_person_po = val($po,'contact_person','-');
$contact_no_po     = val($po,'vendor_contact_no','-');

// Addresses and remarks (from PO header)
$billing_addr  = nl2br(val($po,'billing_address',''));
$shipping_addr = nl2br(val($po,'shipping_address',''));
$remarks       = nl2br(val($po,'remarks',''));

// Totals direct from DB (as requested)
$po_basic_value = 0;
foreach ($items as $it) {
    $qty   = (float)val($it,'quantity',0);
    $rate  = (float)val($it,'rate',0);
    $disc  = (float)val($it,'discount',0);
    $dtype = (string)val($it,'discount_type','');

    $line_total = $qty * $rate;
    if ($dtype === '1' && $disc > 0) {
        $line_total -= ($line_total * $disc / 100);
    } elseif ($dtype === '2' && $disc > 0) {
        $line_total -= $disc;
    }

    $po_basic_value += $line_total;
}
$total_gst_value  = (float)val($po,'total_gst_amount',0);
$total_po_value   = (float)val($po,'gross_amount',0);
$amount_words     = amountToWordsINR($total_po_value);

// From here on, render your HTML/PDF using the above variables.

// ---------- Prepared By / Authorised By (names via common user_name()) ----------
function resolve_user_display_name($uid) {
    if (empty($uid)) return '';
    // user_name() returns an array of rows with fields: user_name, staff_name
    $rows = user_name($uid);
    if (is_array($rows) && !empty($rows)) {
        $u = $rows[0];
        $staff = isset($u['staff_name']) ? trim((string)$u['staff_name']) : '';
        $uname = isset($u['user_name']) ? trim((string)$u['user_name']) : '';
        return $staff !== '' ? $staff : $uname;
    }
    return '';
}

$prepared_by_name = '-';
if (!empty($po['created_user_id'])) {
    $n = resolve_user_display_name($po['created_user_id']);
    if ($n !== '') $prepared_by_name = $n;
}

$authorised_by_name = '-';
// Show "Authorised By" only when BOTH status = 1 AND lvl_2_status = 1
if ((string)val($po,'status','0') === '1' && (string)val($po,'lvl_2_status','0') === '1') {
    // Prefer lvl_2_user_id; if empty, fall back to poa_user_id
    $auth_uid = val($po,'lvl_2_user_id','');
    if ($auth_uid === '-' || $auth_uid === '') $auth_uid = val($po,'poa_user_id','');
    if ($auth_uid !== '' && $auth_uid !== '-') {
        $n = resolve_user_display_name($auth_uid);
        if ($n !== '') $authorised_by_name = $n;
    }
}

// ---------- MSME (from PO header) ----------
$msme_type = trim((string)val($po, 'msme_type_display', ''));
$msme_no   = trim((string)val($po, 'msme_no', ''));

// Build a single display line
if ($msme_no === '' || $msme_no === '-' || strtolower($msme_no) === 'na') {
    $msme_display = 'MSME: Not applicable';
} else {
    $msme_display = 'MSME' . ($msme_type !== '' ? ' (' . $msme_type . ')' : '') . ': ' . $msme_no;
}


$company['logo_url'] = '';
if (!empty($po['company_id'])) {
    $res = $pdo->select(
        ['company_creation', [
            'company_name','address','tel_number','contact_email_id','gst_number','pan_number','logo'
        ]],
        ['unique_id' => $po['company_id']],
        "",
        "LIMIT 1"
    );

    if ($res->status && !empty($res->data)) {
        $c = $res->data[0];
        $company['name']    = val($c, 'company_name', $company['name']);
        $company['address'] = val($c, 'address', $company['address']);
        $company['phone']   = (trim(val($c,'tel_number','')) === '' ? '-' : $c['tel_number']);
        $company['email']   = (trim(val($c,'contact_email_id','')) === '' ? '-' : $c['contact_email_id']);
        $company['gst']     = (trim(val($c,'gst_number','')) === '' ? '-' : $c['gst_number']);
        $company['pan']     = (trim(val($c,'pan_number','')) === '' ? '-' : $c['pan_number']);

        // Build logo URL from unique_id and logo filename
        if (!empty($c['logo'])) {
            $company['logo_url'] = "/blue_planet_beta/uploads/company_creation/" . $c['logo'];
        }
    }
}
$discHeader = 'Discount';
if (!empty($items)) {
    $dtype = (string)val($items[0],'discount_type','');
    if ($dtype === '1') {
        $discHeader = 'Disc %';
    } elseif ($dtype === '2') {
        $discHeader = 'Disc ₹';
    }
}

$authorised_by_name = '-';
$authorised_dt = '-';

$gross = (float)val($po,'gross_amount',0);

if ($gross <= 300000) {
    // Level 1
    if ((string)val($po,'status','0') === '1') {
        $auth_uid = val($po,'updated_user_id','');
        $auth_dt  = val($po,'updated','');
        if (!empty($auth_uid)) {
            $n = resolve_user_display_name($auth_uid);
            if ($n !== '') $authorised_by_name = $n;
        }
        if (!empty($auth_dt) && $auth_dt !== '0000-00-00 00:00:00') {
            $authorised_dt = date("d-m-Y", strtotime($auth_dt));
        }
    }

} elseif ($gross > 300000 && $gross <= 1000000) {
    // Level 2
    if ((string)val($po,'lvl_2_status','0') === '1') {
        $auth_uid = val($po,'lvl_2_user_id','');
        $auth_dt  = val($po,'lvl_2_created_dt','');
        if (!empty($auth_uid)) {
            $n = resolve_user_display_name($auth_uid);
            if ($n !== '') $authorised_by_name = $n;
        }
        if (!empty($auth_dt) && $auth_dt !== '0000-00-00 00:00:00') {
            $authorised_dt = date("d-m-Y", strtotime($auth_dt));
        }
    }

} else {
    // More than 10 lakhs → Level 3
    if ((string)val($po,'lvl_3_status','0') === '1') {
        $auth_uid = val($po,'lvl_3_approved_by','');
        $auth_dt  = val($po,'lvl_3_approved_date','');
        if (!empty($auth_uid)) {
            $n = resolve_user_display_name($auth_uid);
            if ($n !== '') $authorised_by_name = $n;
        }
        if (!empty($auth_dt) && $auth_dt !== '0000-00-00 00:00:00') {
            $authorised_dt = date("d-m-Y", strtotime($auth_dt));
        }
    }
}
$prepared_by_name = '-';
$prepared_dt = '-';

if (!empty($po['created_user_id'])) {
    $n = resolve_user_display_name($po['created_user_id']);
    if ($n !== '') $prepared_by_name = $n;
}

if (!empty($po['created']) && $po['created'] !== '0000-00-00 00:00:00') {
    $prepared_dt = date("d-m-Y", strtotime($po['created']));
}

$tax_title = "Tax";
if (!empty($po['company_id']) && !empty($po['supplier_id'])) {
    // Get company state
    $c = $pdo->select(['company_creation',['state']], ['unique_id'=>$po['company_id']],"","LIMIT 1");
    $s = $pdo->select(['supplier_profile',['state']], ['unique_id'=>$po['supplier_id']],"","LIMIT 1");

    if ($c->status && !empty($c->data) && $s->status && !empty($s->data)) {
        $company_state  = strtolower(trim($c->data[0]['state']));
        $supplier_state = strtolower(trim($s->data[0]['state']));
        if ($company_state !== '' && $supplier_state !== '') {
            if ($company_state === $supplier_state) {
                $tax_title = "CGST + SGST";
            } else {
                $tax_title = "IGST";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Purchase Order</title>
  <link href="../../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
  <link href="../../assets/css/printsstyle.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="invoice-box">
  <!-- HEADER -->
  <div class="row header-box">
    <div class="col-2">
  <?php if (!empty($company['logo_url'])): ?>
    <img src="<?= htmlspecialchars($company['logo_url']) ?>" alt="Company Logo" style="width: 100%;">
  <?php else: ?>
    <img src="/blue_planet_beta/assets/images/default-logo.png" alt="Company Logo" style="width: 100%;">
  <?php endif; ?>
</div>

<?php
        $statusText = "PENDING";
        $statusColor = "rgba(255,165,0,0.15)";   
        if ($approve_status == 1) {
            $statusText = "APPROVED";
            $statusColor = "rgba(40,167,69,0.15)";
        } elseif ($approve_status == 2) {
            $statusText = "REJECTED";
            $statusColor = "rgba(220,53,69,0.15)";
        }
    ?>

    <!--<div class="status-watermark"><?= $statusText ?></div>-->

   <div class="col-5 company-info">
      <h4><?= htmlspecialchars($company['name']) ?></h4>
      <p>
        <?= nl2br(htmlspecialchars($company['address'])) ?><br>
        Phone: <?= htmlspecialchars($company['phone']) ?> | Email: <?= htmlspecialchars($company['email']) ?><br>
        GST No: <?= htmlspecialchars($company['gst']) ?> | PAN No: <?= htmlspecialchars($company['pan']) ?>
      </p>
    </div>
    <div class="col-5 po-details text-wrap text-break">
      <h2>PURCHASE ORDER</h2>
      <p class="mb-0">
        <span style="font-size:18px; font-weight:600; color:#03407f; word-break:break-word; white-space:normal;">
          <?= htmlspecialchars($po_no) ?>
        </span><br>
        <strong>Date:</strong> <?= $entry_date ?>
      </p>
    </div>
  </div>

  <!-- Vendor & Other Info -->
  <div class="row mb-2">
    <div class="col-6">
      <div class="backgrinfo"><h6>Vendor Details</h6></div>
        <p>
          <strong><?= htmlspecialchars($supplier_name) ?></strong><br>
          <?= $supplier_address ?><br>
          Email: <?= ($supplier_email === '' || $supplier_email === '-' ? 'No Email Available' : htmlspecialchars($supplier_email)) ?><br>
          GST No: <?= htmlspecialchars($gst_no) ?><br>
          PAN No: <?= htmlspecialchars($pan_no) ?><br>
          MSME No: <?= ($msme_no === '' || $msme_no === '-' ? 'Not Applicable' : htmlspecialchars($msme_no)) ?>
        </p>
        <div class="backgrinfo"><h6>Ship To:</h6></div>
        <p><?= $shipping_addr ?></p>

    </div>

    <div class="col-6">
      <div class="backgrinfo"><h6>Other Details</h6></div>
      <table class="table table-sm table-borderless">
        <tr><td>DOC No:</td><td>-</td></tr>
        <tr><td>Quotation:</td><td><?= htmlspecialchars($quotation_no) ?></td></tr>
        <tr><td>Quotation Date:</td><td><?= $quotation_dt ?></td></tr>
        <tr><td>Contact Person:</td><td><?= htmlspecialchars($contact_person_po) ?></td></tr>
          <tr><td>Contact No:</td><td><?= htmlspecialchars($vendor_contact_no) ?></td></tr>
        <tr><td>Project:</td><td><?= htmlspecialchars(($project_code !== '' ? $project_code . '/' : '') . ($project_name ?? '-')) ?></td>
        </tr>
      </table>
    </div>
  </div>

  <!-- Items Table -->
<!-- Items Table -->
<table class="table table-bordered mb-2">
  <thead class="thead-light">
    <tr>
      <th style="width:5%;">#</th>
      <th style="width:35%;">Item Description</th>
      <th>HSN Code</th>
      <th>Delivery Date</th>
      <th>Qty</th>
      <th>UOM</th>
      <th><?= $tax_title ?></th> <!-- ✅ Dynamic column -->
      <th>Rate (INR)</th>
      <th><?= $discHeader ?></th>
      <th>Amount (INR)</th>
      <th>Remarks</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($items)): $sn=1; foreach ($items as $it): ?>
      <?php
        $itemUid = val($it,'item_code','');
        [$itemName, $hsn] = get_item_name($pdo, $itemUid);
        $uomName = get_uom_name($pdo, val($it,'uom',''));
        $deliv   = fmtDate(val($it,'delivery_date',''));
        $qty     = (float)val($it,'quantity',0);
        $rate    = (float)val($it,'rate',0);
        $disc    = (float)val($it,'discount',0);
        $dtype   = (string)val($it,'discount_type','');

        // Calculate line total
        $line_total = $qty * $rate;
        if ($dtype === '1' && $disc > 0) { // Percentage
            $line_total -= ($line_total * $disc / 100);
        } elseif ($dtype === '2' && $disc > 0) { // Flat amount
            $line_total -= $disc;
        }

        // Tax label
        // Tax percentage (from tax master)
        $tax_uid = val($it,'tax','');
        $perc = 0;
        if ($tax_uid !== '-' && $tax_uid !== '') {
            // Look up in tax table cache
            $res = $pdo->select(['tax',['tax_value']], ['unique_id'=>$tax_uid], "", "LIMIT 1");
            if ($res->status && !empty($res->data)) {
                $perc = (float)$res->data[0]['tax_value'];
            }
        }
        
        if ($perc > 0) {
            $taxLabel = "GST {$perc}%";
        } else {
            $taxLabel = "-";
        }


        $discVal = discount_value_display_print($it);
        $amount  = fmtNum($line_total);
      ?>
      <tr>
        <td><?= $sn++ ?></td>
        <td><?= htmlspecialchars($itemName) ?></td>
        <td><?= htmlspecialchars($hsn) ?></td>
        <td><?= $deliv ?></td>
        <td><?= htmlspecialchars($qty) ?></td>
        <td><?= htmlspecialchars($uomName) ?></td>
        <td class="text-center"><?= htmlspecialchars($taxLabel) ?></td> <!-- ✅ New -->
        <td class="text-end"><?= $rate ?></td>
        <td class="text-end"><?= htmlspecialchars($discVal) ?></td>
        <td class="text-end"><?= $amount ?></td>
        <td><?= htmlspecialchars(val($it,'item_remarks','')) ?></td>

      </tr>
    <?php endforeach; else: ?>
      <tr><td colspan="10" class="text-center text-muted">No items found.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

  <!-- Totals -->
  <table class="table table-borderless totals mb-2">
    <tr>
      <td class="text-end">PO Basic Value:</td>
      <td style="width: 140px;" class="text-end"><?= fmtNum($po_basic_value) ?></td>
    </tr>
    <tr>
      <td class="text-end">GST:</td>
      <td class="text-end"><?= fmtNum($total_gst_value) ?></td>
    </tr>
    <tr>
      <td class="text-end">Total PO Value (INR):</td>
      <td class="text-end"><?= fmtNum($total_po_value) ?></td>
    </tr>
  </table>

  <p><strong>INR : <?= htmlspecialchars(strtoupper($amount_words)) ?></strong></p>

  <div class="terms-note mt-2">
    <div><h6><b>Terms & Conditions</b></h6></div>
    <div class="row">
      <div class="col-12">
        <div class="d-flex"><div style="width:200px; font-weight:500;">Payment Terms</div><div style="width:20px;">:</div><div><?= htmlspecialchars(val($po,'payment_days','-')) ?></div></div>
        <div class="d-flex"><div style="width:200px; font-weight:500;">Delivery</div><div style="width:20px;">:</div><div><?= htmlspecialchars(val($po,'delivery','-')) ?></div></div>
        <div class="d-flex"><div style="width:200px; font-weight:500;">Packing &amp; Forwarding</div><div style="width:20px;">:</div><div><?= fmtNum(val($po,'packing_forwarding_amount',0)) ?></div></div>
        <div class="d-flex"><div style="width:200px; font-weight:500;">Remarks</div><div style="width:20px;">:</div><div><?= $remarks ?></div></div>
      </div>
    </div>
  </div>

  <!-- Signatures -->
  <div class="row mt-2">
    <div class="col-7"></div>
    <div class="col-5 text-center">
      <p><strong>For, <?= htmlspecialchars($company['name']) ?></strong></p>
      <div class="row">
      <div class="col-6 mt-2">
  <p>
    <strong>Prepared By</strong><br>
    <?= htmlspecialchars($prepared_by_name) ?><br>
    <small><?= $prepared_dt ?></small>
  </p>
</div>

        <div class="col-6 mt-2">
  <p>
    <strong>Authorised By</strong><br>
    <?= htmlspecialchars($authorised_by_name) ?><br>
    <small><?= $authorised_dt ?></small>
  </p>
</div>


      </div>
    </div>
  </div>

</div>

<div class="text-center no-print mt-3">
  <button class="btn btn-primary" onclick="window.print()">Print Purchase Order</button>
</div>

<div class="page-break invoice-box mt-4">
   <div class="invoice-content">
      <div class="">
         <div class="row">
            <div class="col-lg-12 p-2">
               <div class="invoice-inner-2">
                  <div class="invoice-info" id="invoice_wrapper">
                     <div class="invoice-inner">
                        <div class="invoice-top">
                           <div class="row align-items-center">
                              <div class="row align-items-center">
                                 <div class="col-sm-6 invoice-name "></div>
                                 <div class="col-sm-6 text-end p-0">
                                    <div class="logoterm">
                                       <a href="#">
                                       <img src="https://zigma.in/blue_planet_beta/assets/images/logo.png" 
                                          alt="Company Logo" style="height: 50px;">
                                       </a>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-sm-12 termcondi">
                                 <h6 class="text-center mb-1"><u>GENERAL TERMS & CONDITIONS</u></h6>
                              </div>
                              <div class="text-left">
                                 <div class="intro">
                                    <!--<h6><u>IMPORTANT INSTRUCTIONS:</u></h6>-->
                                    <!--<p>The goods/services you will be supplying under this PO will be as per technical specifications submitted by you and approved by Zigma Global Environ Solutions Private Limited. In case of any deviations in the same, you would be rectifying the same without any extra cost</p>-->
                                    <h6><u>INSPECTION:</u></h6>
                                    <p>You shall carry out an inspection at your works before dispatch</p>
                                    <h6><u>FORCE MAJEURE:</u></h6>
                                    <p>If at any time, during the continuance of the Order/Contract,  performance in whole or in part by  Contractor of any obligations under this Order/Contract shall be prevented or delayed by reason beyond the control of parties like war hostilities, acts of the public enemy, restrictions by Govt. of India, civil commotion, sabotage, floods, epidemics, quarantine restrictions, or acts of God (hereinafter referred to as 'event'), then, provided notice of the happening of such event is given by  Vendor to  Purchaser within fifteen (15) days from the date of occurrence thereof, neither party shall, by reason of such event be entitled to terminate this Order/Contract nor shall have any claim for damages against each other in respect of non-performance and delay in performance as referred to herein above. Immediately after the event has come to an end or ceased to exist, the Vendor shall promptly notify the Purchaser of the same and performance under the Order/Contract shall be resumed immediately. The contractual completion date referred to in paragraph (4) above shall stand extended accordingly.   In the event of parties hereto not able to agree that a force majeure event has occurred, parties shall submit the disputes for resolution pursuant to the provisions hereunder, provided that the burden of proof as to whether a force majeure event has occurred shall be upon the party claiming such an event.</p>
                                    <h6><u>ARBITRATION:</u></h6>
                                    <p>Any dispute, controversy or claim arising out of or relating to or in connection with this Purchase Order or the breach or validity hereof shall be settled by arbitration by a Sole Arbitrator. We shall appoint a retired judge of High Court or Supreme court of India to act as sole Arbitrator. By acknowledging a copy of this P.O. you agree to the aforesaid method of appointment of Arbitrator. The Place of Arbitration shall be at Erode, Tamil Nadu. The language of the Arbitration and award shall be English. Courts in Tamil Nadu alone shall have exclusive Jurisdiction.</p>
                                    <h6><u>SAFETY PRECAUTIONS:</u></h6>
                                    <p>Supplier shall observe all necessary safety precautions to safeguard their personnel, plant, machinery, other personnel, equipment and completed works at site. Supplier shall comply with all the requirements of safety norms both as per prevailing laws and as per ISO Manual. Supplier shall remain solely liable for any claims or damages arising out of non-compliance of such safety precautions and would indemnify Purchaser from any such claims or damages. Supplier will indemnify Purchaser from any loss and costs arising out of any accidents, leading to total or partial loss to the persons or property during the execution of work in the scope of Supplier and also due to non-compliance of rules and regulations of any statutory or Government bodies.</p>
                                    <h6><u>LABOUR LAWS:</u></h6>
                                    <p>By acknowledging this P.O., you confirm that you have taken into consideration compliance with all the provisions of labour laws as may be applicable including but not limited to the Minimum Wages Act, Payment of Wages Act, Employees Provident Fund and miscellaneous provisions of Act, Workmen Compensation Act, Employee State Insurance Act, Prohibition of Child Labour Act. You hereby agree and undertake to promptly comply with the aforesaid provisions of law as and when they arise and save Purchaser harmless from any liability under the aforesaid laws. You agree and undertake to indemnify Purchaser form any liability under the aforesaid laws.</p>
                                    <h6><u>COMPLIANCE WITH PURCHASER POLICIES</u></h6>
                                    <p>The Supplier shall comply with the Purchaser's ABC (Anti-Bribery and Corruption) & AML (Anti Money Laundering) Policies annexed as <b>“Annexure-A”</b> to this Purchase Order and POSH (Prevention of Sexual Harassment) Policy. This includes strictly adhering to anti-bribery and corruption guidelines and creating a safe and respectful work environment free from sexual harassment. The Supplier shall provide evidence of compliance upon request. Non-compliance may result in termination of the Purchase Order. The Supplier acknowledges the importance of policy adherence. This clause remains in effect beyond Purchase Order termination. The supplier shall strictly Implement, adhere and follow the Anti-Bribery and Corruption Policy as being perused and followed by the Company as provided to it from time to time and as appended to this Purchase order and subject to which the Supplier shall undertake a periodic bribery and corruption risk assessment across its business to understand the bribery and corruption risks it faces and ensure that it has adequate procedures to address those risks. The said Risk Assessment may be reviewed by the Company from time to time.</p>
                                    <h6><u>VENDOR REPRESENTATION AND WARRANTIES</u></h6>
                                    <p class="mb-0">Vendor Represents and Warrants that: </p>
                                    <ol class="d sublist_or1 ">
                                       <li><b>	Organization and Authority: </b>Vendor is a legally established entity with the authority to enter into agreements. </li>
                                       <li><b>	Compliance with Laws:</b> Vendor complies with all relevant laws and industry standards. </li>
                                       <li> <b>	Title and Ownership:</b> Vendor has clear title to products/services, free from liens.</li>
                                       <li><b>	Quality and Performance:</b> Products/services meet agreed specifications and performance standards. </li>
                                       <li><b>	Intellectual Property:</b> Vendor has rights to use intellectual property without infringing third-party rights. </li>
                                       <li><b>	No Litigation:</b> No pending legal actions that could impact performance. </li>
                                       <li><b>	Confidentiality:</b> Vendor will maintain confidentiality of shared information. </li>
                                       <li><b>	Financial Stability:</b> Vendor is financially stable to meet obligations. </li>
                                       <li><b>	Insurance:</b> Vendor has and will maintain appropriate insurance coverage. </li>
                                       <li><b> Product Liability:</b> Products are not unreasonably dangerous; Vendor will address liability claims.</li>
                                       <li><b>	Non-Bribery and Corruption: </b>It has conducted its business in compliance with applicable anti-bribery and anti-corruption laws, and no notice has been received by the Consultant from any governmental authority alleging any non-compliance in this regard; and  </li>
                                       <li><b>	it has not at any time authorised any payments or benefits:</b> (i) to or for the use or benefit of any government official; (ii) to any other person either for an advance or reimbursement, if it knows or has reason to know that any part of such payment will be directly or indirectly given or paid by such other person, or will reimburse such other person for payments previously made, to any government official; or (iii) to any other person to secure other improper advantages, the payment of which would violate applicable anti-bribery and anti-corruption laws. </li>
                                    </ol>
                                    <h6 class="mt-1"><u>CONFIDENTIALITY AND PROTECTION OF PROPRIETARY INFORMATION</u></h6>
                                    <p>The Supplier acknowledges that during the course of discussions and execution of assignments pursuant to this Purchase Order, it may receive proprietary data and information ("Confidential Information") from the Purchaser, including but not limited to business activities, products, designs, drawings, know-how, plans, price lists, market studies, computer software, database technologies, and financial details.</p>
                                    <p>The Supplier agrees that the Purchaser's Confidential Information shall be kept strictly confidential and shall not be disclosed to any third party without the prior written consent of the Purchaser.</p>
                                    <p>The Supplier will grant access to the Confidential Information only to its directors, officers, employees, affiliates, agents, external advisors, and consultants ("Representatives") who have a clear need to know for the purposes of the Project. The Supplier shall advise such Representatives of the existence and terms of this Purchase Order and of the obligations of confidentiality herein.</p>
                                    <p>Upon the expiry or earlier termination of this Purchase Order or promptly following the written request of the Purchaser, the Supplier will return to the Purchaser or certify in writing the destruction of all Confidential Information (and copies and extracts thereof) furnished to, or created by or on behalf of, the Supplier, without retaining any copies.</p>
                                    <p>The confidentiality obligations of the Supplier shall survive the expiry or termination of this Purchase Order.</p>
                                    <h6 class="mt-1"><u>NATURE OF RELATIONSHIP</u></h6>
                                    <p>The Supplier is engaged by the Purchaser in the capacity of an independent Supplier to render the Services in accordance with the terms of this Agreement. Nothing in this Agreement shall be construed to mean that the Supplier is an employee, worker, agent or partner of the Purchaser. Nothing in this Agreement shall be construed to have created a joint venture between the Parties.</p>
                                    <h6 class="mt-1"><u>INDEMNITY </u></h6>
                                    <p>The Supplier agrees to indemnify, defend, and hold harmless the Purchaser and its affiliates, directors, officers, employees, agents, and representatives (collectively, the "Indemnified Parties") from and against any and all claims, losses, damages, liabilities, costs, and expenses (including reasonable attorneys' fees and costs) arising out of or in connection with any breach by the Supplier or its Representatives of the confidentiality obligations set forth in this Purchase Order.</p>
                                    <p>In the event that any claim or demand is made or any action or proceeding is brought against any of the Indemnified Parties, the Supplier shall, upon written notice from the Purchaser, defend such claim, demand, action, or proceeding at the Supplier's expense with counsel reasonably satisfactory to the Purchaser. The Purchaser shall have the right, at its own expense, to participate in the defense of any claim, demand, action, or proceeding and to be represented by counsel of its own choosing.</p>
                                    <h6 class="mt-1"><u>LIMITATION ON LIABILITY </u></h6>
                                    <p> Notwithstanding anything to the contrary contained in this Purchase Order, the Purchaser's aggregate liability to the Supplier for any and all claims, losses, damages, liabilities, costs, and expenses arising out of or in connection with this Purchase Order, whether in contract, tort (including negligence), or otherwise, shall not exceed the total amount paid or payable to the Supplier under this Purchase Order.</p>
                                    <h6 class="mt-1"><u>MANDATORY DOCUMENTS </u></h6>
                                    <ol class="d sublist_or1">
                                       <li>Supplied Material Specification and Test Certificates. </li>
                                       <li>Guarantee / Warranty certificates Inspection Report must be shared along with packing list. </li>
                                    </ol>
                                    <h6 class="mt-1"></h6>
                                    <p><b>Original Invoice shall be sent to the following address</b></p>
                                    <p><b><?= htmlspecialchars($company['name']).',' ?></b></p>
                                    <p>
                                       <?= nl2br(htmlspecialchars($company['address'])) ?><br>
                                       Email: <?= htmlspecialchars($company['email']) ?><br>
                                    </p>
                                    <!--<p><b>M/s. Blue Planet Environmental Solutions Pte Ltd, -->
                                    <!--178, Indhu Nagar, Palayapalayam, Perundurai Road, Erode, Tamil Nadu – 638011. -->
                                    <!--E-Mail – purchase.bpb@blue-planet.com,</b>-->
                                    <!--</p>-->
                                 </div>
                              </div>
                              <div class="sign_term ">
                                 <h6 class="mt-3"><b>Read, Agreed and Understood</b></h6>
                                 </b></h6> 
                                 <h6 class="mb-5"><b>For Supplier/Vendor</b></h6>
                                 <h6 class="mt-5 pt-5"><b>(Authorized Signatory and Seal)</b></h6>
                                 <h6><b>Dated : </b></h6>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="page-break invoice-box mt-4">
   <div class="invoice-content p-2">
      <div class=" ">
         <div class="row">
            <table>
               <thead>
                  <tr>
                     <td>
                        <div class="invoice-center pb-0">
                           <div class="row ">
                              <div class="col-sm-6 invoice-name "></div>
                              <div class="col-sm-6 text-end pe-2">
                                 <div class="logoterm">
                                    <a href="#">
                                    <img src="https://zigma.in/blue_planet_beta/assets/images/logo.png" 
                                       alt="Company Logo" style="height: 50px;">
                                    </a>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </td>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>
                        <div class="col-lg-12 p-0">
                           <div class="invoice-inner-2" style="">
                              <div class="invoice-info" id="invoice_wrapper">
                                 <div class="invoice-inner" >
                                    <div class="invoice-top">
                                       <div class="row align-items-center">
                                          <div class="col-sm-12 termcondi text-center" >
                                             <p style="font-size:11px;"><b>ANNEXURE-A</b></p>
                                             <p style="font-size:11px"><b>ANTI-BRIBERY AND CORRUPTION AND ANTI-MONEY LAUNDERING POLICY</b></p>
                                          </div>
                                          <div class="text-center">
                                             <!--<p>Zigma Global Environ Solutions Private Limited<b> (“Zigma”) </b> is a part of Blue Planet Group and falls under the category of Blue Planet Group Companies hence, Zigma has adopted Blue Planet’s ABC & AML Policies as being appended below:</p>-->
                                          </div>
                                          <div class="intro">
                                             <h6>Introduction</h6>
                                             <h6>Zero Tolerance Approach : </h6>
                                             <p>Blue Planet Environmental Solutions Pte Ltd and all its subsidiaries, affiliate and group companies (herein after collectively referred to as <b>“Company”</b> or the <b>“Blue Planet Group”)</b> are committed to conduct all of its business in a professional, fair, and ethical manner. The Company adopts a zero-tolerance approach to bribery and corruption of any form and commits to adopt ethical business practices which are reflected in this policy on Anti-Bribery and Corruption, Gifting and Hospitality, and Anti-Money Laundering <b>(“Policy”).</b> </p>
                                             <p>The Company does not offer or pay or accept any bribes for any purpose whether directly or through a third party. This applies to domestic and foreign governments, as well as to all private parties.</p>
                                             <p>Bribery is a serious criminal offence in countries in which the Company operates, including Singapore, India, Malaysia, UK, Vietnam and others. Bribery offences can result in the imposition of severe fines and/or custodial sentences (imprisonment), exclusion from tendering for public contracts, and severe reputational damage. </p>
                                             <p>The Company is committed to undertaking periodic bribery and corruption risk assessments across its business to understand the bribery and corruption risks it faces across jurisdictions and ensure that it has adequate procedures in place to address those risks. </p>
                                             <p>The risk assessment will be documented and periodically reviewed and updated, and the appropriate committee of the Company be updated in accordance with applicable regulations.</p>
                                             <p>
                                                Covered Persons <i>(as defined below)</i> must conduct their activities in full compliance with this Policy, the Chapter 241 of Singapore, Singapore Corruption Act, Drug Trafficking and Other Serious Crimes Act 1992,laws of the India and all applicable anti-corruption laws, including the Prevention of Corruption Act, 1988, Prevention of Money Laundering Act, 2002, Malaysian Anti-Corruption Commission Act 2009, Malaysia Anti-Money Laundering, Anti-Terrorism Financing and Proceeds of Unlawful Activities Act 2001,the United States Foreign Corrupt Practices Act of 1977 or any other applicable anti-bribery or anti-corruption law (including without limitation Part 12 of the United States Anti-Terrorism, Crime and Security Act of 2001, the United States Money Laundering Control Act of 1986; the United States International Money Laundering Abatement and Anti-Terrorist Financing Act of 2001, Prevention of Corruption Act, Vietnam Anti-Corruption Law and Decree 59/2019, UK Bribery Act 2010).    
                                             </p>
                                             <h6>Scope:</h6>
                                             <p>This Policy is applicable to all individuals working at all levels and grades, including directors, senior managers, officers, other employees (whether permanent, fixed-term or temporary), consultants, contractors, trainees, interns, seconded staff, casual workers and agency staff, agents, or any other person associated with the Company <b>(“Covered Persons”).</b> The Company shall also ensure on best efforts basis that any third party including, but not limited to vendors, clients, and customers that it has engaged on its behalf, also adopt this policy or any other policy with similar safeguards.</p>
                                             <p>This Policy is applicable with effect from 28th October 2023 and supersedes all prior policies and communication on this subject.</p>
                                          </div>
                                          <div class="col-md-12">
                                             <table class="term_con" border="0" >
                                                <tr>
                                                   <th><b>1</b></th>
                                                   <td class="space_rt"><b>Bribery / Corruption :</b></td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.1</b></th>
                                                   <td class="space_rt">A bribe or corrupt action includes the receiving, offering, promising, requesting, authorizing, or providing of a financial or non-financial advantage or “anything of value” to any client, customer, business partner, vendor or other third party in order to secure, induce or keep an improper or unfair advantage or misuse an individual’s position.</td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.2</b></th>
                                                   <td class="space_rt">Anything of value is not only cash and includes (but not limited to) cash equivalents like gifts, services, employment offers, loans, travel (except business travel as covered under sub-clause 2.2) and hospitality (except for business purposes as covered under sub-clause 2.2) , charitable donations, sponsorships, business opportunities, favourable contracts or giving anything even if nominal in value. Please note that “hospitality” would mean and include any form of facility extended like, hotel accommodation, food, drinks, entertainment, or any events (participating or watching) such as sporting events, theatrical events, concerts, awards or ceremonies.
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.3</b></th>
                                                   <td class="space_rt">Any and all expenditure incurred by the Company towards cash equivalents like gifts, services, employment offers, loans, travel (except business travel as covered under sub-clause 2.2) and <b>hospitality</b> (except for business purposes as covered under sub-clause 2.2), charitable donations, sponsorships, business opportunities, favourable contracts or giving anything even if nominal in value should be duly accounted and recorded in writing in the books of accounts. Such payments or expenditure should at all times be brought to the notice of the CCO / CO along with receipts.
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.4</b></th>
                                                   <td class="space_rt">There is a presumption of corrupt intent if financial or non-financial favours are made or anything of value is given to employees of, or persons dealing with the government, under relevant laws where the company does business.</td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.5</b></th>
                                                   <td class="space_rt mb-4">
                                                      Bribes could include the following examples:
                                                      <ol class="d sublist_or">
                                                         <li>   Lavish gifts, entertainment or travel expenses, particularly where they are disproportionate, frequent or provided in the context of on-going business negotiations; </li>
                                                         <li>   Cash payments by employees or third persons such as consortium members, introducers or consultants; </li>
                                                         <li>	Uncompensated use of Company services, facilities or property; </li>
                                                         <li>	Loans, loan guarantees or other extensions of credit;  </li>
                                                         <li>	Providing a sub-contract to a person connected to someone involved in awarding the main contract </li>
                                                         <li>	Engaging a local company owned by or offering an educational scholarship to a member of the family of a potential customer/ public or government official;  </li>
                                                         <li> Political or charitable donations made to a third party linked to, or at the request of, someone with whom the Company does business. </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.6</b></th>
                                                   <td class="space_rt">
                                                      Facilitation payments are strictly prohibited. These include any irregular one-time and/or routine payments made to government officials to expedite or secure routine governmental action. Any such payments must immediately be brought to the attention of the Chief Compliance Officer <b>(“CCO”)</b>  or Compliance Officer<b> (“CO”),</b>  as applicable to a Blue Planet Group entity. The details of such persons are as provided below:
                                                      <table class="table table-sm mb-2" border="1" cellspacing="0" cellpadding="0" width="100%">
                                                         <tr>
                                                            <td width="130" valign="top">
                                                               <strong>Name </strong>
                                                            </td>
                                                            <td width="125" valign="top">
                                                              <strong>Designation</strong>
                                                            </td>
                                                            <td width="184" valign="top">
                                                              <strong>Email    Address</strong>
                                                            </td>
                                                            <td width="156" valign="top">
                                                               <strong>Location</strong>
                                                            </td>
                                                         </tr>
                                                         <tr>
                                                            <td width="130" valign="top">
                                                               <p class="mb-0">Mr. Dhananjay Jitesh Pandey</p>
                                                            </td>
                                                            <td width="125" valign="top">
                                                               <p class="mb-0">Group Compliance Officer</p>
                                                            </td>
                                                            <td width="184" valign="top">
                                                               <p class="mb-0"><a href="mailto:dhananjay.pandey@blue-planet.com">dhananjay.pandey@blue-planet.com</a></p>
                                                            </td>
                                                            <td width="156" valign="top">
                                                               <p class="mb-0">New Delhi, India</p>
                                                            </td>
                                                         </tr>
                                                      </table>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.7</b></th>
                                                   <td class="space_rt">
                                                      No person who is subject to this Policy shall: 
                                                      <ol class="d sublist_or">
                                                         <li>Offer, provide, or authorize, a bribe or anything which may be viewed as a bribe either directly or indirectly or otherwise through any third party; and/or  </li>
                                                         <li>Request or receive a bribe or anything which may be viewed as a bribe either directly or indirectly or otherwise through any third party, or perform their job functions improperly in anticipation, or in consequence, of a bribe.  </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.8</b></th>
                                                   <td class="space_rt">The prohibition on accepting a bribe from, or giving a bribe to, any person applies to any person acting in the course of a business, as an employee of a business or otherwise on behalf of others in relation to the performance of their duties and to public officials.
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.9</b></th>
                                                   <td class="space_rt">The Company prohibits the provision of money, gifts, entertainment or anything else of value except the amounts as agreed and stipulated under this Policy, to any government or public officials or private entity for the purpose of influencing such officials or private entity in order to obtain or retain business or a business or commercial advantage, or otherwise in relation to decisions that may be seen as beneficial to the Company’s business interests. </td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.10</b></th>
                                                   <td class="space_rt">
                                                      Examples of government and public officials include: 
                                                      <ol class="d">
                                                         <li>Anyone holding a legislative, administrative or judicial position, including government ministers, elected representatives of national, regional or local assemblies, officials of a political party, civil servants, magistrates or judges; </li>
                                                         <li>	An employee, officer, agent or other person acting in an official capacity for a government, government department, government or public agency, public enterprise, or commercial enterprise owned in whole or in part by a government;  </li>
                                                         <li>	An employee, officer, agent or person acting in an official capacity for a public international organization, such as the World Bank, United Nations or the European Commission.
                                                         </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>1.11</b></th>
                                                   <td class="space_rt">
                                                      <b>Compliance</b> 
                                                      <ol class="d">
                                                         <li>	All Covered Persons and board of directors of the Company, and all third parties who represent the Company, or who are suppliers, vendors, clients, contractors or other business partners are required to comply with this Policy, and not engage in any form of bribery or corruption.</li>
                                                         <li>		The Company runs a risk of being held responsible for the actions of a third party (who represents the Company, or who are the suppliers, vendors, clients, contractors or other business partners) acting on its behalf. Hence, due care must be taken to ensure that those third parties do not engage or attempt to engage in bribery. </li>
                                                         <li>
                                                            The CCO or the CO of the Company shall ensure that: 
                                                            <ol type="I">
                                                               <li>	All records are accurate, complete and accessible for review, including records relating to commissions, travel and entertainment. This Policy prohibits any practice that might conceal or facilitate bribery or any other corrupt action; </li>
                                                               <li>	Covered Persons must ensure all expenses claims relating to hospitality, gifts or expenses incurred to third parties are recorded and specifically record the reason for the expenditure; </li>
                                                               <li> 	All accounts, invoices, memoranda and other documents and records relating to dealings with third parties, such as clients, suppliers, and business contacts, should be prepared and maintained with strict accuracy and completeness;</li>
                                                               <li>	No accounts will be kept “off-book” to facilitate or conceal improper payments and the same is ensured through effective monitoring and auditing mechanisms in place; and </li>
                                                               <li>Due diligence is undertaken before engaging any third-party above prescribed thresholds and that the Vendor Engagement SOP is given effect to.  </li>
                                                            </ol>
                                                         </li>
                                                         <li>
                                                            Specific guidance on common forms of bribery:
                                                            <ol type="I">
                                                               <li> <u>	Gifts and hospitality, travel and entertainment:</u> It is the responsibility of the person extending or receiving such a gift, hospitality or travel and entertainment benefit to ensure that it is not a bribe and is in strict compliance with the Policy.</li>
                                                               <li><u>	Charitable contributions:</u> Covered Persons must not use charitable contributions as a way of concealing a bribe </li>
                                                               <li> <u>Political contributions:</u> Covered Persons must not use the Company’s resources including funds or facilities to provide support for, or contribute to any political organisation or candidate as the Blue Planet Group is strictly apolitical.</li>
                                                            </ol>
                                                         </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th> <b>1.12</b></th>
                                                   <td class="space_rt">
                                                      <b>Reporting</b>
                                                      <ol class="d">
                                                         <li>	The prevention, detection and reporting of bribery and other forms of corruption are the responsibility of all those working for the Company or under the Company’s control. 
                                                         </li>
                                                         <li>	Covered Persons must notify the CCO / CO and the legal team as soon as possible if they believe or suspect that a breach of or conflict with this Policy has occurred or may occur in the future. 
                                                         </li>
                                                         <li>	If a Covered Person is unsure whether a particular act constitutes bribery or corruption or if he/she has any other queries, these should be raised with their respective manager, the HR team, the Legal team and the CCO / CO.   </li>
                                                         <li> 	Employees who report potential misconduct in good faith or who provide information or otherwise assist in any inquiry or investigation of potential misconduct will be protected against retaliation.</li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                             </table>
                                          </div>
                                          <div class="col-md-12 ">
                                             <table class="term_con" border="0" >
                                                <tr>
                                                   <th><b>2</b></th>
                                                   <td class="space_rt"><b>Gifts, Meals, Entertainment and Employment</b></td>
                                                </tr>
                                                <tr>
                                                   <th></th>
                                                   <td class="space_rt">
                                                      This Policy sets forth various rules relating to gifts, entertainment, travel, meals, lodging and employment.  All such expenditures must be recorded accurately in the books and records of the Company, as outlined below. 
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>2.1</b></th>
                                                   <td class="space_rt">
                                                      <b>Gifts </b>
                                                      <ol class="d">
                                                         <li>
                                                            As a general matter, the Company competes for and earns business through the quality of its personnel, products and services, not with gifts or lavish entertainment.  The use of the Company funds or assets for gifts, gratuities, or other favours to government officials or any other individual or entity (in the private or public sector) that has the power to decide or influence the Company’s commercial activities is prohibited, unless all of the following circumstances are met. 
                                                            <ol type="i">
                                                               <li>	the gift does not involve cash or cash equivalent gifts (e.g., gift cards, store cards or gambling chips); </li>
                                                               <li>	the gift is permitted under both applicable law and the guidelines of the recipient’s employer (provided this has been duly shared with the Company by the recipient’s employer); </li>
                                                               <li> 	the gift is presented openly with complete transparency;     </li>
                                                               <li>	the gift is properly recorded in the Company’s books and records;  </li>
                                                               <li> the gift is provided as a token of esteem, courtesy or in return for hospitality and should comport with local custom; and </li>
                                                               <li>the item costs less than INR 8,000 (or its foreign currency equivalent).  </li>
                                                            </ol>
                                                         <li>Gifts that do not fall specifically within the above guidelines require advance consultation and approval by the CCO or CO.</li>
                                                         </li>
                                                         <li>	Note that the provision of gifts, as well as the reporting requirements, in this Policy, apply even if the Company is not seeking reimbursement for the expenses (i.e., paying these expenses out of your own pocket does not avoid these requirements).  </li>
                                                         <li>	The Company must not accept, or permit any member of his/her immediate family to accept any gifts, gratuities or other favours from any customer, supplier or other person doing or seeking to do business with the Company, other than items of up to INR 8,000 or such other amount as may be decided by the Company or edible items that are customarily acceptable (e.g., sweets or chocolates). Any gifts of a value exceeding INR 8,000 should be returned immediately and reported to the manager of the concerned the Company.  If immediate return is not practical, they should be given to the Company for charitable disposition. 
                                                         </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                             </table>
                                          </div>
                                          <div class="col-md-12 ">
                                             <table class="term_con" border="0" >
                                                <tr>
                                                   <th><b>2.2</b></th>
                                                   <td class="space_rt">
                                                      <b>Meals, Entertainment, Travel and Lodging </b>
                                                      <ol class="d">
                                                         <li> Common sense and moderation should prevail in business entertainment and the payment of travel and lodging expenses engaged in on behalf of the Company. Covered Persons should provide business entertainment to or receive business entertainment from anyone doing business with the Company only if the entertainment is infrequent, modest, and intended to serve legitimate business goals.</li>
                                                         <li>	Meals, entertainment, travel or lodging should never be offered as a means of influencing another person’s or entity’s business decision. Meals, entertainment, travel or lodging should only be offered if it is appropriate, reasonable for promotional purposes, offered or accepted in the normal course of a business relationship, and if the primary subject of discussion or purpose is business.  The appropriateness of a particular type of meal, entertainment, travel or lodging depends upon both the reasonableness of the expense and on the type of activity involved.  This is determined based on whether or not the expenditure is sensible and proportionate to the nature of the individual involved.  </li>
                                                         <li>
                                                            Expenses for meals, hospitality, travel or lodging for government officials or any other individual or entity (in the private or public sector) that has the power to decide or influence the Company commercial activities may be incurred without prior written approval by CCO / CO only if all of the following conditions are met:
                                                            <ol type="i">
                                                               <li>	The expenses are bona fide and related to a legitimate business purpose and the events involved are attended by appropriate Company representatives; </li>
                                                               <li> 	The cost of the meal, hospitality, travel or lodging expenses are reasonable and proportional to the business purpose and are not extravagant; and</li>
                                                               <li> 	the meal, hospitality, travel or lodging is permitted by the rules of the recipient’s employer as shared with the Company.</li>
                                                            </ol>
                                                         </li>
                                                         <li> 	All expense reimbursements must be supported by receipts, and expenses and approvals must be recorded in sufficient detail in the Company’s records. In all instances, the Company must ensure that the recording of the expenditure associated with meals, lodging, travel or entertainment clearly reflects the true purpose of the expenditure.   </li>
                                                         <li>	Note that the provision of meals, entertainment, travel or lodging as well as the reporting requirements in this Policy apply even if the Company are not seeking reimbursement for the expenses (i.e., paying these expenses out of their own pocket does not avoid these requirements). </li>
                                                         <li>	When possible, meals, entertainment, travel or lodging payments should be made directly by the Company to the provider of the service and should not be paid directly as a reimbursement.  Per diem allowances may not be paid to a government official or any other individual (in the private or public sector) that has the power to decide or influence the Company’s commercial activities for any reason.   </li>
                                                         <li>	Any meal, hospitality, travel or lodging expense greater than [INR 8,000] (or its foreign currency equivalent) per person, and any expense at all that is incurred for meals, entertainment, travel or lodging unrelated to a legitimate business purpose, or which in any way deviates from the requirements of clause 2.2, must be pre-approved in writing by the CCO or CO.</li>
                                                         <li> 	Please note that in addition to traditional gifts, meals, entertainment, lodging, and travel that are provided to business relationships (individuals and/or entities that the Company interacts with on a professional level or has any business dealings with or transacts any business with) where the Company are not in attendance shall be considered gifts, and subject to the rules and requirements for gifts specified in this Policy.</li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>2.3</b></th>
                                                   <td class="space_rt">
                                                      <b>Relationships with Third Parties</b>
                                                      <ol class="d">
                                                         <li>	Anti-corruption laws prohibit indirect payments made through a third party, including giving anything of value to a third party while knowing that value will be given to a government official for an improper purpose. Therefore, the Company should avoid situations involving third parties that might lead to a violation of this Policy.</li>
                                                         <li>	The Company who deals with third parties are responsible for taking reasonable precautions to ensure that the third parties conduct business ethically and comply with this Policy.  Such precautions may include, for third parties representing the Company before governmental entities, conducting an integrity due diligence review of a third party, inserting appropriate anti-corruption compliance provisions in the third party’s written contract, requiring the third party to certify that it has not violated and will not violate this Policy and any applicable anti-corruption laws during the course of its business with the Company, and monitoring the reasonableness and legitimacy of the services provided by and the compensation paid to the third party during the engagement. Any doubts regarding the scope of appropriate due diligence efforts in this regard should be resolved by contacting the CCO or the CO. </li>
                                                         <li> 	If the Company have reason to suspect that a third party is engaging in potentially improper conduct, they shall report the case to CCO or Co, immediately. The Company shall investigate and stop further payments to the third party if the Company suspicions are verified through the investigation.</li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                             </table>
                                          </div>
                                          <div class="col-md-12">
                                             <table class="term_con" border="0" >
                                                <tr>
                                                   <th><b>3</b></th>
                                                   <td class="space_rt"><b>
                                                      Anti-Money Laundering </b>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>3.1</b></th>
                                                   <td class="space_rt">
                                                      <b>
                                                      Money Laundering </b>
                                                      <ol class="d">
                                                         <li>	The offence of money laundering is directly or indirectly attempting to indulge or knowingly assisting, or if the Company knowingly is a party or is actually involved in any process or activity connected with the proceeds of crime and projecting it as untainted property. In a general sense, money laundering is used to describe the process by which offenders disguise the original ownership and control of the proceeds of criminal conduct by making such proceeds appear to have derived from a legitimate source.
                                                         </li>
                                                         <li>
                                                            Money laundering usually consists of 3 components 
                                                            <ol type="i">
                                                               <li> <u>Placement:</u> This is the initial stage and during this stage, the money generated from illegal / criminal activity such as sale of drugs, illegal firearms, etc. is disposed of. Funds are deposited into financial institutions or converted into negotiable instruments such as money orders or traveller’s cheques. </li>
                                                               <li><u>Layering:</u>	 In this stage, funds are moved into other accounts in an effort to hide their origin and separate illegally obtained assets or funds from their original source. This is achieved by creating layers of transactions, by moving the illicit funds between accounts, between businesses, and by buying and selling assets on a local and international basis until the original source of the money is virtually untraceable. Thus, a trail of unusually complex transactions is created to disguise the original source of funds and thereby make it appear legitimate.  </li>
                                                               <li><u>Integration:</u> Once the illegitimate money is successfully integrated into the financial system, these illicit funds are reintroduced into the economy and financial system and often used to purchase legitimate assets, fund legitimate businesses, or conduct other criminal activity. The transactions are made in such a manner so as to appear as being made out of legitimate funds.  </li>
                                                            </ol>
                                                         </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>3.2</b></th>
                                                   <td class="space_rt">
                                                      <b>
                                                      Potential Red Flags  </b>
                                                      <ol class="a">
                                                         <li>	Where any suspicions arise that criminal conduct may have taken place involving a customer, colleague or third party, an employee should consider whether there is a risk that money laundering or terrorist financing has occurred or may occur, and to report the same to the CCO.  </li>
                                                         <li>
                                                            Some examples of red flags to be reported include: 
                                                            <ol type="i">
                                                               <li>	A customer or partner provides insufficient, false or suspicious information or is reluctant to provide complete Information;  </li>
                                                               <li> 	Methods or volumes of payment that are not consistent with the payment policy or that are not customarily used in the course of business, e.g., payments with money orders, traveller’s checks, and/or multiple instruments, and payments from unrelated third parties; </li>
                                                               <li>	Receipts of multiple negotiable instruments to pay a single invoice;  </li>
                                                               <li> 	Requests by a customer or partner to pay in cash;</li>
                                                               <li> 	Early repayments of a loan, especially if payment is from an unrelated third party or involves another unacceptable form of payment; </li>
                                                               <li>	Orders or purchases that are inconsistent with the customer’s trade or business;  </li>
                                                               <li>Payments to or from third parties that have no apparent or logical connection with the customer or transaction; </li>
                                                               <li>	Payment to or from countries considered high risk for money laundering or terrorist financing; </li>
                                                               <li>	Payments to or from countries considered to be tax havens or offshore jurisdictions which appear on any sanctions lists; </li>
                                                               <li>	A customer’s or partner’s business formation documents are from jurisdictions which appear on any sanctions lists of the United States of America (https://sanctionssearch.ofac.treas.gov/), or a country that poses a high risk for money laundering, terrorism or terrorist financing, or a country that is not logical for the customer;  </li>
                                                               <li>	Overpayments followed by directions to refund a payment, especially if requested to send the payment to a third party; 
                                                               </li>
                                                               <li>Unusually complex business structures, payment patterns that reflect no real business purpose. 
                                                               </li>
                                                            </ol>
                                                         </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>3.3</b></th>
                                                   <td class="space_rt">
                                                      <b>
                                                      Due Diligence and Compliance  </b><br>
                                                      The due diligence obligations under this Policy are as follows: 
                                                      <ol class="d">
                                                         <li>	To on board any vendors / third parties in accordance with the Blue Planet Vendor Engagement SOP.  </li>
                                                         <li>	To identify and discourage money laundering or terrorist financing activities, and immediately communicate any suspicion of such activities to the CCO / CO.  </li>
                                                         <li>	To take adequate and appropriate measures to follow the spirit of applicable anti-money laundering legislation.  </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>3.4</b></th>
                                                   <td class="space_rt">
                                                      <b>
                                                      Reporting Obligation </b>
                                                      <ol class="d">
                                                         <li>	Employees have the obligation to read and follow this Policy, to understand and identify any red flags that may arise in their business activities and to escalate potential compliance concerns related to this Policy to the CCO / CO and Legal team, without notifying anyone involved in the transaction  </li>
                                                         <li>	In the event that any Covered Person or anyone who has a business relationship with the Company, violates this Policy, the contract or business relationship with the said contractor, vendor or business partner shall be terminated.
                                                         </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                             </table>
                                          </div>
                                          <div class="col-md-12">
                                             <table class="term_con" border="0" >
                                                <tr>
                                                   <th><b>4</b></th>
                                                   <td class="space_rt"><b>
                                                      4.	General</b>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>4.1</b></th>
                                                   <td class="space_rt">
                                                      All reports, complaints, doubts, or concerns in relation to this Policy shall be raised to the CCO / CO. Any action required to be undertaken under this Policy shall be taken by the CCO / CO in accordance with this Policy. 
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>4.2</b></th>
                                                   <td class="space_rt">
                                                      The ESG Committee is responsible for monitoring the use and effectiveness of this Policy. The CCO / CO shall report to the ESG Committee 
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>4.3</b></th>
                                                   <td class="space_rt">
                                                      The CCO / CO is responsible for: 
                                                      <ol class="d">
                                                         <li>	Ensuring that this Policy complies with the Company’s legal and ethical obligations and that all Covered Persons under the respective sectors, function or line of business are aware of and comply with the Policy. All Covered Persons must receive regular messages (oral /written) from the line management reminding them to comply.</li>
                                                         <li>	Ensuring that the Company has a culture of compliance and effective controls to comply with ABAC and AML laws and regulations to prevent, detect and respond to bribery, corruption, money laundering and counter-terrorism financing and to communicate the serious consequences of non-compliance to employees and other Covered Persons. </li>
                                                         <li>	Annual trainings (both in English and relevant local vernacular) being conducted for relevant employees regarding this Policy and general compliance with anti-bribery and anti-corruption and anti-money laundering obligations. </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>4.4</b></th>
                                                   <td class="space_rt">
                                                      Covered Persons are encouraged to exercise their right under the Policy to disclose any suspected activity or wrong-doing. All Covered Persons in the sector, function or line of business are referred to the Company’s Whistle-blower hotline to report the same.
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>4.5</b></th>
                                                   <td class="space_rt">
                                                      <u>	Consequences of Violation:</u>
                                                      <ol class="d">
                                                         <li>Any employee who breaches this Policy will face disciplinary action as prescribed under the Employee Handbook Manual, which could result in dismissal. Failure to report a violation of this Policy constitutes an independent violation of this Policy that is subject to disciplinary action, up to and including termination of employment. </li>
                                                         <li> 	Breach by any other Covered Person may result in the Company pursuing legal remedies against such Covered Person and/or immediate termination of contract with such Covered Person. Additionally, the Blue Planet Group may also be exposed to criminal or civil claims and reputational harm arising from the violation.</li>
                                                         <li>	Any breach of this Policy would also result in imposition of large fines / imprisonment on the Covered Person / Company. </li>
                                                      </ol>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <th><b>4.6</b></th>
                                                   <td class="space_rt">
                                                      <u>Monitor and Review:</u> This Policy will be annually reviewed and updated as needed to ensure it continues to be adequate and effective.
                                                   </td>
                                                </tr>
                                             </table>
                                          </div>
                                          <div class="col-md-12">
                                             <p>  <i>
                                                The Company’s policies do not constitute contracts for employment with the Company either express or implied. The Company reserves the right at any time to delete or add to any provisions of this policy at its sole discretion. However, deletions or additions to this policy may only be made in writing by the Human Resources department. Where there is inconsistency between this policy and procedure and applicable local law, local law will prevail.
                                                </i>
                                             </p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </td >
                  </tr>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
</body>
</html>

