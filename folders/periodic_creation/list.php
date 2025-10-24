<?php 
include 'function.php';

// ✅ Department dropdown (for potential future filtering)
$department_type_options = department();
$department_type_options = select_option($department_type_options, "Select Department");
?>

<!-- ==========================================================
✅ FILTER & ACTION BAR
========================================================== -->
<div class="row list-pad mb-2">
  <div class="col-12">
    <div class="row align-items-center">
      <!-- Optional filter area -->
      <div class="col-md-5">
        <button class="btn btn-outline-primary btn-sm mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse"
          aria-expanded="false" aria-controls="filterCollapse">
          <i class="fa fa-filter"></i> Filter (Optional)
        </button>
      </div>

      <!-- Add new record -->
      <div class="col-md-7 text-end">
        <?php echo btn_add($btn_add); ?>
      </div>
    </div>

    <!-- Collapsible filter -->
    <div class="collapse mt-3" id="filterCollapse">
      <div class="card card-body mb-0">
        <div class="row align-items-end">
          <div class="col-md-4">
            <label class="form-label">Department</label>
            <select name="department_type" id="department_type" class="select2 form-control">
              <?= $department_type_options; ?>
            </select>
          </div>
          <div class="col-md-2">
            <button type="button" class="btn btn-primary rounded-pill w-100" onclick="filter_periodic_creation()">
              Go
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ==========================================================
✅ DATATABLE SECTION
========================================================== -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <table id="periodic_creation_datatable" class="table table-striped table-bordered nowrap w-100">
          <thead class="table-light">
            <tr>
              <th>S.No</th>
              <th>User Name</th>
              <th>User Type</th>
              <th>Mobile Number</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

