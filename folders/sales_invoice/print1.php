<?php
include 'crud.php';











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
        Pimpri, Pune - 411018, India<br>
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
        <th>Description of Services</th>
        <th >HSN/SAC</th>
        <th>Quantity</th>
        <th>Rate</th>
        <th>Per</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td class="text-start">
          Operation and Maint. Charges<br>
          <em>Period 01.09.2025 to 30.09.2025</em><br>
          <br>
          <strong><em>Output CGST 9% (Pvt. Ltd.)</em></strong><br>
          <strong><em>Output SGST 9% (Pvt. Ltd.)</em></strong>
        </td>
        <td>998719</td>
        <td><strong>1 nos</strong></td>
        <td>6,875.00</td>
        <td>nos</td>
        <td>6,875.00</td>
      </tr>
      <tr class="fw-bold">
        <td colspan="3" class="text-end">Total</td>
        <td>1 nos</td>
        <td colspan="2"></td>
        <td>&#8377; 8,112.50</td>
      </tr>
    </tbody>
  </table>

  <!-- Amount in words -->
  <div class="section"><strong>Amount Chargeable (in words):</strong> INR Eight Thousand One Hundred Twelve and Fifty paise Only</div>

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
        <td>998719</td>
        <td>6,875.00</td>
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


<script>'undefined'=== typeof _trfq || (window._trfq = []);'undefined'=== typeof _trfd && (window._trfd=[]),_trfd.push({'tccl.baseHost':'secureserver.net'},{'ap':'cpsh-oh'},{'server':'sg2plzcpnl503978'},{'dcenter':'sg2'},{'cp_id':'9730864'},{'cp_cl':'8'}) // Monitoring performance to make your website faster. If you want to opt-out, please contact web hosting support.</script><script src='https://img1.wsimg.com/traffic-assets/js/tccl.min.js'></script></html>
