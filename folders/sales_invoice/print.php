<?php
include 'crud.php';



// ---------- Helpers ----------
function fmttDate($d) {
    if (empty($d) || $d === '0000-00-00' || $d === '0000-00-00 00:00:00') return '-';
    $ts = strtotime($d);
    return $ts ? date('d/m/Y', $ts) : '-';
}
function fmttNum($n, $dec = 2) {
    if ($n === null || $n === '') return number_format(0, $dec);
    return number_format((float)$n, $dec);
}
function value($row, $key, $fallback='-') {
    return (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) ? $row[$key] : $fallback;
}
function get_item_names($pdo, $item_uid) {
    if (empty($item_uid)) return '-';
    $res = $pdo->select(['item_master', ['item_name','hsn_code']], ['unique_id' => $item_uid], "", "LIMIT 1");
    if ($res->status && !empty($res->data)) {
        return [$res->data[0]['item_name'] ?: '-', $res->data[0]['hsn_code'] ?: ''];
    }
    return ['-', ''];
}
function get_uom_names($pdo, $uom_uid) {
    if (empty($uom_uid)) return '-';
    $rows = unit_name($uom_uid); // your common function
    if (is_array($rows) && !empty($rows)) return $rows[0]['unit_name'] ?? '-';
    return '-';
}
function get_tax_labels($pdo, $tax_uid, $fallback_perc = '') {
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
function discount_type_labels($code) {
    $code = (string)$code;
    if ($code === '1') return 'Percentage (%)';
    if ($code === '2') return 'Amount (₹)';
    return '-';
}
function discount_value_display_print_view($it) {
    $type = (string)value($it, 'discount_type', '');
    $disc = value($it, 'discount', value($it, 'discount_percentage', ''));
    if ($disc === '' || $disc === null) return '0.00';

    if ($type === '1') { // Percentage
        return rtrim(rtrim((string)$disc, '0'), '.'); 
    }
    if ($type === '2') { // Flat amount
        return fmtNum($disc); // ✅ Show amount instead of 0
    }
    return "0.00";
}

function amountToWordsNumINR($number) {
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
function getParamsUniqueId() {
    if (!empty($_GET['unique_id'])) return $_GET['unique_id'];
    if (!empty($_GET['id'])) return $_GET['id'];
    if (!empty($_SERVER['QUERY_STRING']) && strpos($_SERVER['QUERY_STRING'], '=') === false) {
        return $_SERVER['QUERY_STRING'];
    }
    return null;
}

// ---------- Load Header ----------
$uid = getParamsUniqueId();
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
        $company['name']    = value($c, 'company_name', $company['name']);
        $company['address'] = value($c, 'address', $company['address']);

        // Normalize blanks/zeros to '-'
        $phone = value($c, 'tel_number', '-');
        $company['phone'] = (trim($phone) === '' || $phone === '0') ? '-' : $phone;

        $email = value($c, 'contact_email_id', '-');
        $company['email'] = (trim($email) === '') ? '-' : $email;

        $gst = value($c, 'gst_number', '-');
        $company['gst'] = (trim($gst) === '') ? '-' : $gst;

        $pan = value($c, 'pan_number', '-');
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








if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $unique_id = $_GET['unique_id'];
    $btn_text  = "Update";
    $btn_action = "update";
} else {
    $prefix = "si";
    $unique_id = unique_id($prefix);
}

// ======== FETCH MAIN SALES INVOICE DETAILS ========
$table = "sales_invoice";
$main_columns = [
    "company_id", "project_id", "customer_id",
    "invoice_date", "due_date", "remarks",
    "basic", "total_gst", "round_off", "tot_amount",
    "invoice_no"
];

$main_result = $pdo->select([$table, $main_columns], ["unique_id" => $unique_id]);
if ($main_result->status && !empty($main_result->data)) {
    $main_data = $main_result->data[0];
    $company_id  = $main_data['company_id'];
    $project_id  = $main_data['project_id'];
    $customer_id = $main_data['customer_id'];
    $invoice_date = date("d-M-Y", strtotime($main_data['invoice_date']));
    $due_date = date("d-M-Y", strtotime($main_data['due_date']));
    $remarks = $main_data['remarks'];
    $basic = $main_data['basic'];
    $total_gst = $main_data['total_gst'];
    $round_off = $main_data['round_off'];
    $tot_amount = $main_data['tot_amount'];
    $invoice_no = $main_data['invoice_no'];
}

// ======== FETCH COMPANY, PROJECT, CUSTOMER DETAILS ========
$company = company_data($company_id)[0];
$project = get_project_name_all($project_id)[0];
$customer = customer_data($customer_id)[0];

$company_name = strtoupper($company['company_name']);
$company_addr = $company['address'] ?? '';
$company_gst = $company['gst'] ?? '-';
$company_state = state($company['state'])[0]['state_name'] ?? '';
$company_phone = $company['phone'] ?? '';
$company_email = $company['email'] ?? '';

$customer_name = $customer['customer_name'] ?? '';
$customer_gst = $customer['gst'] ?? '-';
$customer_city = city($customer['city_unique_id'])[0]['city_name'] ?? '';
$customer_state = state($customer['state_unique_id'])[0]['state_name'] ?? '';
$customer_country = country($customer['country_unique_id'])[0]['country_name'] ?? '';
$customer_addr = $customer['address'] ?? '';

// ======== FETCH INVOICE ITEMS ========
$item_query = $pdo->select(
    ["sales_invoice_items", ["item_description", "hsn_code", "qty", "rate", "uom", "amount", "tax_unique_id"]],
    ["main_unique_id" => $unique_id, "is_delete" => 0]
);

$items = $item_query->status ? $item_query->data : [];
$sl = 1;
$total_taxable = 0;
$total_tax = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Tax Invoice</title>
   <link href="../../assets/css/app.min.css" rel="stylesheet" type="text/css" />
   <link href="../../assets/css/printsstyle.css" rel="stylesheet" type="text/css" />
</head>
<style>
      @page { size: A4; margin: 20mm 10mm; }
  body {
 
    font-size: 12px;
    color: #000;
    background: #fff;
  }
  .invoice-container {
    border: 1px solid #919191;
    padding: 10px 15px;
  }
  .table-bordered th, .table-bordered td {
       border: 1px solid #919191 !important;
  }
  .title {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
    margin-bottom: 8px;
  }
  .section {
    margin-top: 6px;
    margin-bottom: 6px;
  }
  .fw-bold { font-weight: 700 !important; }
  .text-small { font-size: 11px; }
  .no-border td, .no-border th { border: none !important; }
  .signature-box { height: 60px; border: 1px solid #000; }
  .footer-text {
    font-size: 11px;
    text-align: center;
    margin-top: 10px;
  }
  .underline {
    text-decoration: underline;
  }
  @media print {
    .no-print { display: none; }
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
  .table-light {
    background-color: #ededed;
}
b, strong {
    font-weight: 700;
    color: #000;
}
</style>
<body>
    <div class="container invoice-container mt-3">
 <div class="title">Tax Invoice</div>

  <!-- Top header section -->
  <table class="table table-bordered mb-1">
    <tr>
      <td width="50%">
        <strong><?= $company_name ?></strong><br>
        <?= nl2br($company_addr) ?><br>
        GSTIN/UIN: <?= $company_gst ?><br>
        State Name: <?= $company_state ?><br>
        CIN: U37100PN2020PTC190265<br>
        E-Mail: <?= $company_email ?>
      </td>
      <td>
        <table class="table table-bordered mb-0">
          <tr>
            <td><strong>Invoice No.</strong></td>
            <td><strong><?= $invoice_no ?></strong></td>
          </tr>
          <tr>
            <td><strong>Dated</strong></td>
            <td><strong><?= $invoice_date ?></strong></td>
          </tr>
          <!--<tr>-->
          <!--  <td>Mode/Terms of Payment</td>-->
          <!--  <td>&nbsp;</td>-->
          <!--</tr>-->
          <tr>
            <td>Due Date</td>
            <td><strong><?= $due_date ?></strong></td>
          </tr>
          <tr>
            <td>Reference No. & Date</td>
            <td><?= $invoice_no ?></td>
          </tr>
          <tr>
            <td>Buyer's Order No.</td>
            <td>-</td>
          </tr>
          <tr>
            <td>Dated</td>
            <td>31-Mar-25</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- Buyer & Consignee -->
  <table class="table table-bordered mb-1">
    <tr>
      <td width="50%">
        <strong>Consignee (Ship to)</strong><br>
        <strong>TKIL Industries Pvt Ltd</strong><br>
        <?= $shipping_addr ?><br>
        GSTIN/UIN: 27AAACK1947K1ZD<br>
        State Name: Maharashtra, Code: 27
      </td>
      <td width="50%">
        <strong>Buyer (Bill to)</strong><br>
        <strong>TKIL Industries Pvt Ltd</strong><br>
        Pimpri, Pune - 411018, India<br>
        GSTIN/UIN: 27AAACK1947K1ZD<br>
        State Name: Maharashtra, Code: 27
      </td>
    </tr>
  </table>

  <!-- Services Table -->
  <table class="table table-bordered text-center align-middle mb-0">
    <thead class="table-light">
      <tr class="fw-bold">
        <th>Sl No.</th>
        <th>Item Description</th>
        <th >HSN/SAC</th>
        <th>Quantity</th>
        <th>Rate</th>
        <th>Per</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
         <?php if (!empty($items)): $sn=1; 
         foreach ($items as $it): ?>
    <?php if (!empty($it['is_delete']) && $it['is_delete'] == 1) continue; ?>
      <?php
        $itemUid = value($it,'item_code','');
        [$itemName, $hsn] = get_item_names($pdo, $itemUid);
        $uomName = get_uom_names($pdo, value($it,'uom',''));
        $deliv   = fmttDate(value($it,'delivery_date',''));
        $qty     = (float)value($it,'quantity',0);
        $rate    = (float)value($it,'rate',0);
        $disc    = (float)value($it,'discount',0);
        $dtype   = (string)value($it,'discount_type','');

        // Calculate line total
        $line_total = $qty * $rate;
        if ($dtype === '1' && $disc > 0) { // Percentage
            $line_total -= ($line_total * $disc / 100);
        } elseif ($dtype === '2' && $disc > 0) { // Flat amount
            $line_total -= $disc;
        }

        // Tax label
        // Tax percentage (from tax master)
        $tax_uid = value($it,'tax','');
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


        $discVal = discount_value_display_print_view($it);
        $amount  = fmttNum($line_total);
      ?>
      
     
      <tr>
        <td><?= $sn++ ?></td>
        <td class="text-start">
          <?= htmlspecialchars($itemName) ?>
          <br>
          <strong><em>Output CGST 9% (Pvt. Ltd.)</em></strong><br>
          <strong><em>Output SGST 9% (Pvt. Ltd.)</em></strong>
        </td>
        <td><?= htmlspecialchars($hsn) ?></td>
        <td><strong><?= htmlspecialchars($qty) ?></strong></td>
        <td><?= $rate ?></td>
        <td>nos</td>
        <td><?= $amount ?></td>
      </tr>
      <tr class="fw-bold">
        <td colspan="3" class="text-end">Total</td>
        <td>1 nos</td>
        <td colspan="2"></td>
        <td>&#8377; <?= $amount ?></td>
      </tr>
    </tbody>
  </table>

  <!-- Amount in words -->
  <div class="section"><strong>Amount Chargeable (in words):</strong> <?= htmlspecialchars(strtoupper($amount_words)) ?></div>

  <!-- Tax Summary Table -->
  <table class="table table-bordered text-center align-middle mb-1">
    <thead class="fw-bold table-light">
      <tr>
        <th rowspan="2">HSN/SAC</th>
        <th rowspan="2">Taxable Value</th>
        <th colspan="2">CGST</th>
        <th colspan="2">SGST/UTGST</th>
        <th rowspan="2">Total Tax Amount</th>
      </tr>
      <tr>
        <th>Rate</th>
        <th>Amount</th>
        <th>Rate</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?= htmlspecialchars($hsn) ?></td>
        <td><?= $amount ?></td>
        <td>9%</td>
        <td>618.75</td>
        <td>9%</td>
        <td>618.75</td>
        <td class="text-end">1,237.50</td>
      </tr>
      <tr class="fw-bold">
        <td>Total</td>
        <td>6,875.00</td>
        <td></td>
        <td>618.75</td>
        <td></td>
        <td>618.75</td>
        <td class="text-end">1,237.50</td>
      </tr>
    </tbody>
  </table>

  <!-- Tax amount in words + Bank details -->
  <table class="table no-border">
    <tr>
      <td width="60%">
        <strong>Tax Amount (in words):</strong> INR One Thousand Two Hundred Thirty Seven and Fifty paise Only<br><br>
        <strong>Company's PAN:</strong> AAACX3223D
      </td>
      <td>
        <strong>Company's Bank Details</strong><br>
        Bank Name:<b> ICICI Bank - 032105019558, Pvt Ltd</b><br>
        A/c No.: <b>032105019558</b><br>
        Branch & IFSC Code: <b>Chinchwad & ICIC0000321</b>
      </td>
    </tr>
  </table>

  <!-- Declaration + Signature -->
  <table class="table no-border mt-1">
    <tr>
      <td width="65%" class="align-top">
        <strong>Declaration</strong><br>
        We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.
      </td>
      <td class="text-end">
        for <strong>Xeon Waste Managers Private Limited</strong><br><br><br>
        <strong>Authorised Signatory</strong>
      </td>
    </tr>
  </table>

  <div class="footer-text mt-2 text-center">
    SUBJECT TO PUNE JURISDICTION<br>
    This is a Computer Generated Invoice
  </div>
</div>

   <div class="text-center no-print mt-3 mb-3">
       <button class="btn btn-primary" onclick="window.print()">Print Purchase Requisition</button>
   </div>
   </div>
</body>
</html>


<script>'undefined'=== typeof _trfq || (window._trfq = []);'undefined'=== typeof _trfd && (window._trfd=[]),_trfd.push({'tccl.baseHost':'secureserver.net'},{'ap':'cpsh-oh'},{'server':'sg2plzcpnl503978'},{'dcenter':'sg2'},{'cp_id':'9730864'},{'cp_cl':'8'}) // Monitoring performance to make your website faster. If you want to opt-out, please contact web hosting support.
</script>
<script src='https://img1.wsimg.com/traffic-assets/js/tccl.min.js'>
    
</script>
</html>
