<!-- print_view.php -->
<?php
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Normally fetch from DB based on ID. We'll use static data for demo.
$employee = [
  'id' => $id,
  'name' => 'John Doe',
  'email' => 'john@example.com',
  'site' => 'Erode',
  'status' => 'Active',
];
?>
<!DOCTYPE html>
<html>
<head>
  <title>Print View - Employee #<?= $employee['id'] ?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      margin: 30px;
      font-size: 15px;
    }
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body>

  <!-- Print Button -->
  <div class="text-center mb-4 no-print">
    <button onclick="window.print()" class="btn btn-success">🖨️ Print</button>
  </div>

  <!-- Print Content -->
  <div class="container">
    <h4 class="mb-4">Employee Details</h4>
    <table class="table table-bordered">
      <tr><th>ID</th><td><?= $employee['id'] ?></td></tr>
      <tr><th>Name</th><td><?= $employee['name'] ?></td></tr>
      <tr><th>Email</th><td><?= $employee['email'] ?></td></tr>
      <tr><th>Site</th><td><?= $employee['site'] ?></td></tr>
      <tr><th>Status</th><td><?= $employee['status'] ?></td></tr>
    </table>
  </div>

  <!-- Optional: Auto Trigger Print -->
  <script>
    // Uncomment the line below to trigger print on load
    // window.onload = () => window.print();
  </script>

</body>
</html>
