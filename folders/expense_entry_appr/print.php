<?php
include 'crud.php';
// include '../../include/common_function.php';

if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $unique_id = $_GET['unique_id'];

    // ===== FETCH MAIN EXPENSE ENTRY =====
    $main_res = $pdo->select(
        ['expense_entry', [
            'company_id','project_id','category_id','payment_type_id','customer_id',
            'invoice_no','invoice_date','remarks','basic','total_gst','round_off','tot_amount'
        ]],
        ['unique_id' => $unique_id, 'is_delete' => 0]
    );

    if ($main_res->status && !empty($main_res->data)) {
        $main = $main_res->data[0];
        $company_id     = $main['company_id'];
        $project_id     = $main['project_id'];
        $category_id    = $main['category_id'];
        $payment_type   = $main['payment_type_id'];
        $customer_id    = $main['customer_id'];
        $invoice_no     = $main['invoice_no'];
        $invoice_date   = $main['invoice_date'];
        $remarks        = $main['remarks'];
        $tot_amount     = $main['tot_amount'];
    } else {
        die("Expense not found");
    }

    // ===== FETCH ITEMS =====
    $items_res = $pdo->select(
        ['expense_entry_items', ['item_name','unit','quantity','rate','amount']],
        ['main_unique_id' => $unique_id, 'is_delete' => 0]
    );

    $items = [];
    if ($items_res->status && !empty($items_res->data)) {
        foreach ($items_res->data as $row) {
            $item_name = get_item_name($row['item_name']);
            $uom = unit_name($row['unit'])[0]['unit_name'] ?? '';
            $items[] = [
                'item_name' => $item_name,
                'quantity'  => $row['quantity'],
                'rate'      => number_format($row['rate'], 2),
                'amount'    => number_format($row['amount'], 2)
            ];
        }
    }

    // ===== FETCH COMPANY INFO =====
    $company = company_data($company_id)[0];
    $company_name = $company['company_name'];
    $company_addr = $company['address'];
    $company_gst  = $company['gst_number'];
    $company_state = state($company['state'])[0]['state_name'] ?? '-';
    $company_phone = $company['mobile_no'] ?? '';
    $company_email = $company['email'] ?? '';
    $company_logo = $company['logo'];
}

// === Helper: number to words ===
function convert_number_to_words($number) {
    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = [
        0 => 'zero',1 => 'one',2 => 'two',3 => 'three',4 => 'four',
        5 => 'five',6 => 'six',7 => 'seven',8 => 'eight',9 => 'nine',
        10 => 'ten',11 => 'eleven',12 => 'twelve',13 => 'thirteen',
        14 => 'fourteen',15 => 'fifteen',16 => 'sixteen',17 => 'seventeen',
        18 => 'eighteen',19 => 'nineteen',20 => 'twenty',30 => 'thirty',
        40 => 'forty',50 => 'fifty',60 => 'sixty',70 => 'seventy',
        80 => 'eighty',90 => 'ninety',100 => 'hundred',1000 => 'thousand',
        100000 => 'lakh',10000000 => 'crore'
    ];
    if (!is_numeric($number)) return false;
    if ($number < 0) return $negative . convert_number_to_words(abs($number));
    $string = $fraction = null;
    if (strpos($number, '.') !== false) list($number, $fraction) = explode('.', $number);
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) $string .= $hyphen . $dictionary[$units];
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) $string .= $conjunction . convert_number_to_words($remainder);
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) $string .= $remainder < 100 ? $conjunction : $separator;
            $string .= convert_number_to_words($remainder);
            break;
    }
    return $string;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Expense Print</title>
<link href="../../assets/css/app.min.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; font-size: 13px; color: #000; margin: 20px; }
.invoice-box { border: 1px solid #ddd; padding: 20px; }
.header-table td { vertical-align: top; }
h2 { font-size: 18px; text-align: center; margin-bottom: 20px; text-transform: uppercase; }
.table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.table th, .table td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
.table th { background: #f0f0f0; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.signature { margin-top: 50px; text-align: right; }
.signature img { height: 50px; }
</style>
</head>

<body>

<div class="invoice-box">
    <h2>Expense</h2>

    <table width="100%" class="header-table">
        <tr>
            <td width="50%">
                <img src="/blue_planet_beta/uploads/company_creation/<?= $company_logo ?>" style="height:60px;">
                <p><strong><?= strtoupper($company_name) ?></strong><br>
                <?= $company_addr ?><br>
                Phone: <?= $company_phone ?><br>
                Email: <?= $company_email ?><br>
                GSTIN: <strong><?= $company_gst ?></strong><br>
                State: <?= $company_state ?>
                </p>
            </td>
            <td width="50%" align="right">
                <table border="0">
                    <tr><td><strong>Expense For:</strong></td><td><?= expense_category($category_id)[0]['category_name'] ?? '-' ?></td></tr>
                    <tr><td><strong>Expense No:</strong></td><td><?= $invoice_no ?></td></tr>
                    <tr><td><strong>Date:</strong></td><td><?= date('d/m/Y', strtotime($invoice_date)) ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width:5%;">#</th>
                <th>Description of Goods</th>
                <th style="width:15%;" class="text-right">Quantity</th>
                <th style="width:20%;" class="text-right">Price/Unit (₹)</th>
                <th style="width:20%;" class="text-right">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (!empty($items)) {
                $i = 1;
                foreach ($items as $row) {
                    echo "<tr>
                        <td class='text-center'>{$i}</td>
                        <td>{$row['item_name']}</td>
                        <td class='text-right'>{$row['quantity']}</td>
                        <td class='text-right'>{$row['rate']}</td>
                        <td class='text-right'>{$row['amount']}</td>
                    </tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No items found</td></tr>";
            }
            ?>
            <tr>
                <td class="text-right" colspan="4"><strong>Total</strong></td>
                <td class="text-right"><strong>₹ <?= number_format($tot_amount, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <table width="100%" style="margin-top:10px;">
        <tr>
            <td width="70%">
                <strong>Amount in Words:</strong><br>
                <?= ucwords(convert_number_to_words(round($tot_amount))) ?> Rupees only
            </td>
            <td width="30%" align="right">
                <strong>Paid:</strong> ₹ <?= number_format($tot_amount, 2) ?>
            </td>
        </tr>
    </table>

    <div class="signature">
        <p><strong>For <?= strtoupper($company_name) ?>:</strong></p>
        <img src="../../assets/images/signature.png" alt="signature"><br>
        <span>Authorized Signatory</span>
    </div>
</div>

</body>
</html>


