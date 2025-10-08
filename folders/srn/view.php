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
    <div class="company-name"><?= htmlspecialchars($company_name) ?></div>
        <div class="company-address"><?= $company_address ?></div>

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
            <tr>
                 <th class="fw-semibold">MSME No :</th>
                 <td><?= ($msme_no === '' || $msme_no === '-' ? 'Not Applicable' : htmlspecialchars($msme_no)) ?></td>
            </tr>
            <tr>
              <th class="fw-semibold">Supplier Email :</th>
              <td><?= htmlspecialchars($supplier_email) ?></td>
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