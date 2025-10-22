<?php
include 'crud.php';
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
        "created_user_id",
        "updated_user_id",
        "remarks",
        "created"
    ];

    $main_result = $pdo->select([$table, $main_columns], ["unique_id" => $unique_id]);
    // print_r($main_result);

    if ($main_result->status && !empty($main_result->data)) {
        $main_data = $main_result->data[0];

        $company_name   = $main_data['company_id'];
        $project_id     = $main_data['project_id'];
        $purchase_requisition_category = $main_data['service_type'];
        $requisition_for = $main_data['requisition_for'];
        $pr_number      = $main_data['pr_number'];
        $requisition_type = $main_data['requisition_type'];
        $requisition_date = $main_data['requisition_date'];
        $requested_by   = $main_data['requested_by'];
        $sales_order    = $main_data['sales_order_id'];
        $remarks        = $main_data['remarks'];
        $approved_by            = $main_data["updated_user_id"];
        $created_user_id        = $main_data["created_user_id"];
    }
}

// Dropdown options
$item_name_list_options = item_name_list();
$item_name_list_options = select_option($item_name_list_options, "Select the Item/Code", $item_name_list);

$sales_order_options = sales_order();
$sales_order_options = select_option($sales_order_options, "Select the Sales Order", $sales_order);

$project_options = get_project_name();
$project_options = select_option($project_options, "Select the Project Name", $project_id);

$purchase_requisition_category_options = purchase_requisition_category();
$purchase_requisition_category_options = select_option($purchase_requisition_category_options, "Select the Purchase Requisition category", $purchase_requisition_category);

$requisition_type_options = [
    1 => ["unique_id" => "1", "value" => "Regular"],
    2 => ["unique_id" => "683568ca2fe8263239", "value" => "Service"],
    3 => ["unique_id" => "683588840086c13657", "value" => "Capital"]
];
$requisition_type_options = select_option($requisition_type_options, "Select", $requisition_type);

$requisition_for_options = [
    1 => ["unique_id" => "1", "value" => "Direct"],
    2 => ["unique_id" => "2", "value" => "SO"],
    3 => ["unique_id" => "3", "value" => "Ordered BOM"]
];
$requisition_for_options = select_option($requisition_for_options, "Select", $requisition_for);


$requisition_type_map = [
    "1"                  => "Regular",
    "683568ca2fe8263239" => "Service",
    "683588840086c13657" => "Capital"
];

$requisition_type_text = $requisition_type_map[$requisition_type] ?? "-";

$created_user_id = user_name($created_user_id)[0]['user_name'];
$approved_by = user_name($approved_by)[0]['user_name'];

$company_name_is       = company_data($company_name);
// print_r($company_id);
// print_r($company_name_is[0]);

$city = city($company_name_is[0]['city'])[0]['city_name'];
$state = state($company_name_is[0]['state'])[0]['state_name'];
$country = country($company_name_is[0]['country'])[0]['country_name'];

$supplier_data = customer_data($customer_id);
// print_r($supplier_data);

$city = city($supplier_data[0]['city_unique_id'])[0]['city_name'];
$state = state($supplier_data[0]['state_unique_id'])[0]['state_name'];
$country = country($supplier_data[0]['country_unique_id'])[0]['country_name'];

$supplier_contacts = customer_contact_data($customer_id);
// print_r($supplier_contacts);

$uom_unique_id      = unit_name();
$uom_unique_id      = select_option($uom_unique_id,"Select", $uom);

$product_unique_id      = product_name();
$product_unique_id      = select_option($product_unique_id, "Select", $group_unique_id);

$project_options  = get_project_name($project_id);
// print_r($project_options);
// $project_options  = select_option($project_options,"Select the Project Name",$project_id);

$purchase_order_no  = get_po_number();
$purchase_order_no = select_option($purchase_order_no, "Select Purchase Order No",$purchase_number);

$gst_paf_options     = select_option(tax(), "Select GST", $gst_paf);
$gst_freight_options = select_option(tax(), "Select GST", $gst_freight);
$gst_other_options   = select_option(tax(), "Select GST", $gst_other);

$supplier_name_options     = po_supplier();

$supplier_name_options     = select_option($supplier_name_options,"Select", $supplier_name);


$sales_order_data = $pdo->select(["sales_order", ["sales_order_no"]], ["unique_id" => $sales_order]);

if ($sales_order_data->status && !empty($sales_order_data->data)) {
    $sales_order_no = $sales_order_data->data[0]['sales_order_no'];
} else {
    $sales_order_no = "-";
}



// PR items fetch
$columns = [
    "@a:=@a+1 as s_no",
    "item_code",
    "item_description",
    "quantity",
    "uom",
    "item_remarks",
    "required_delivery_date",
    "unique_id",
    "from_sales_order"
];

$table_details = [
    "purchase_requisition_items, (SELECT @a:=0) as a",
    $columns
];

$where = [
    "main_unique_id" => $unique_id, // ✅ FIXED
    "is_delete"      => 0
];

$result = $pdo->select($table_details, $where);

$data = [];

if ($result->status) {
    foreach ($result->data as $row) {
        $item_data = item_name_list($row["item_code"]);
        $is_fab = !empty($item_data[0]["item_code"]) && strpos($item_data[0]["item_code"], "-FAB-") !== false;

        $display_code = isset($item_data[0]["item_name"], $item_data[0]["item_code"])
            ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"]
            : "-";

        $display_class = "no-sublist";
        $sublist = [];

        if ($is_fab) {
            $prod_unique_id = $row["item_code"];

            // ✅ FIXED $so_id → $sales_order
            $obom_res = $pdo->select(
                ["obom_list", ["type"]],
                ["so_unique_id" => $sales_order, "is_delete" => 0]
            );

            $prod_type = ($obom_res->status && !empty($obom_res->data))
                ? intval($obom_res->data[0]["type"])
                : 0;

            if ($prod_type != 1) {
                $sublist_res = $pdo->select(
                    ["obom_child_table", ["item_unique_id", "qty", "uom_unique_id", "remarks"]],
                    ["so_unique_id" => $sales_order, "is_delete" => 0]
                );

                if ($sublist_res->status && !empty($sublist_res->data)) {
                    $display_class = "fab-toggle";

                    foreach ($sublist_res->data as $idx => $sub) {
                        $sub_item = $pdo->select(
                            ["item_master", ["item_name", "item_code"]],
                            ["unique_id" => $sub["item_unique_id"], "is_delete" => 0]
                        );

                        $sub_name = ($sub_item->status && !empty($sub_item->data))
                            ? $sub_item->data[0]["item_name"] . " / " . $sub_item->data[0]["item_code"]
                            : $sub["item_unique_id"];

                        $uom = unit_name($sub["uom_unique_id"]);

                        $sublist[] = [
                            "sno"     => $row["s_no"] . "." . ($idx + 1),
                            "item"    => $sub_name,
                            "qty"     => $sub["qty"],
                            "uom"     => $uom[0]['unit_name'],
                            "remarks" => $sub["remarks"]
                        ];
                    }

                    error_log("sublist: " . print_r($sublist, true) . "\n", 3, "sublist1.log"); // ✅ FIXED
                }
            }
        }

        $display_code = "<span class='{$display_class}'>" . $display_code . "</span>";

        $uom_data = unit_name($row["uom"]);
        $row["uom"] = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";
        $row['quantity'] = round($row['quantity']);

        $edit = btn_edit($btn_prefix, $row["unique_id"]);
        $del  = btn_delete($btn_prefix, $row["unique_id"]);

        $data[] = [
            "s_no"      => $row["s_no"],
            "item_code" => $display_code,
            "item_desc" => $row["item_description"],
            "quantity"  => $row["quantity"],
            "uom"       => $row["uom"],
            "remarks"   => $row["item_remarks"],
            "req_date"  => $row["required_delivery_date"],
            "actions"   => $edit . $del,
            "sublist"   => $sublist
        ];
    }
}


$approved_by = "-";
$approved_dt = "-";

$auth_res = $pdo->select(
    ["purchase_requisition_items", ["updated_user_id", "updated", "status"]],
    [
        "main_unique_id" => $unique_id,
        "is_delete"      => 0,
        "lvl_2_status"   => 1 // only approved
    ]
);

if ($auth_res->status && !empty($auth_res->data)) {
    // sort by updated date, latest first
    usort($auth_res->data, function($a, $b) {
        return strtotime($b['updated']) <=> strtotime($a['updated']);
    });

    // take latest row
    $row     = $auth_res->data[0];
    $auth_id = $row["updated_user_id"];
    $auth_dt = $row["updated"];

    if (!empty($auth_id)) {
        $user_data   = user_name($auth_id);
        $approved_by = $user_data[0]['user_name'] ?? "-";
    }

    if (!empty($auth_dt) && $auth_dt !== "0000-00-00") {
        $approved_dt = date("d-m-Y", strtotime($auth_dt));
    }
}

$created_dt = "-";
if (!empty($main_data['created']) && $main_data['created'] !== "0000-00-00") {
    $created_dt = date("d-m-Y", strtotime($main_data['created']));
}
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
        <strong>Xeon Waste Managers Private Limited</strong><br>
        Unit No. 306, T3, Kohinoor World Towers,<br>
        Old Pune-Mumbai Highway,<br>
        Opp. Empire Estate, Chinchwad Village,<br>
        Pimpri Chinchwad, Pune, Maharashtra<br>
        GSTIN/UIN: 27AAACX3223D1ZQ<br>
        State Name: Maharashtra, Code: 27<br>
        CIN: U37100PN2020PTC190265<br>
        E-Mail: shantanu.kulkarni@blue-planet.com, swapnil.kore@blue-planet.com
      </td>
      <td>
        <table class="table table-bordered mb-0">
          <tr>
            <td><strong>Invoice No.</strong></td>
            <td><strong>XWM/2025-26/090</strong></td>
          </tr>
          <tr>
            <td><strong>Dated</strong></td>
            <td><strong>3-Oct-25</strong></td>
          </tr>
          <tr>
            <td>Mode/Terms of Payment</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td>Reference No. & Date</td>
            <td>XWM/2025-26/090 dt. 3-Oct-25</td>
          </tr>
          <tr>
            <td>Buyer's Order No.</td>
            <td>7000050028</td>
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
