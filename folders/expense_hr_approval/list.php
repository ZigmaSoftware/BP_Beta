<?php

$pending_status  = 0;

        $sess_user_type      = $_SESSION['sess_user_type'];
        $executive_id        = $_SESSION["staff_id"];

            
            if($sess_user_type != $admin_user_type){
                $executive_options = [];
                // $executive_options   = $data;
                 $executive_options   = staff_name($_SESSION["staff_id"])[0];
                 //print_r($executive_options);
                 $executive_options  =[[
                    "unique_id" => $executive_options['unique_id'], 
                    "name"      => $executive_options['staff_name']
                    ]];
                $executive_options   = select_option($executive_options,"Select Staff Name",$_SESSION["staff_id"]); 
                 $staff_check        = " disabled ";
            } else {
                $executive_options  = staff_name();
                $executive_options  = select_option($executive_options,"Select Executive Name");
                 $staff_check        = "  ";

            }
            $status_options      = [   
                "Approved" => [
                    "unique_id" => "1",
                    "value"     => "Approved",
                ],
                "Pending" => [
                    "unique_id" => "2",
                    "value"     => "Pending",
                ],
                "Cancel" => [
                    "unique_id" => "3",
                    "value"     => "Cancel",
                ],
        ];
$status_options     = select_option($status_options,"Select",$pending_status);

        

// $table   = "view_branch_staff_name";
// $pending_status  = 0;

// $columns   =[ 
//             "staff_unique_id",
//             "name"       
//         ];

//         $table_details = [
//             $table,
//             $columns
//         ];
//         $staff_select_where = '';
        
//         $result = $pdo->select($table_details,$staff_select_where);
        
//         if ($result->status) {
//             $status     = $result->status;
//             $data       = $result->data;
//             $error      = "";
//             $sql        = $result->sql;

//             $executive_options   = $data;
//             $executive_options   = select_option($executive_options,"Select Staff Name"); 
//         }


?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated">
                <div class="row">                                    
                    <div class="col-12">
                        <div class="form-group row ">
                            <label class="col-md-1 col-form-label" for="expense_from"> From  </label>
                            <div class="col-md-2">
                                <input type="date" name="expense_from" id="expense_from" class="form-control"  value="" >
                            </div>
                            
                            <label class="col-md-1 col-form-label" for="expense_to"> To </label>
                            <div class="col-md-2">
                                <input type="date" name="expense_to" id="expense_to" class="form-control"  value="" >
                            </div>
                            <label class="col-md-2 col-form-label" for="executive_name"> Executive Name </label>
                            <div class="col-md-2">
                                <select name="executive_name" id="executive_name" class="select2 form-control "  ><?php echo $executive_options;?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row ">

                            <label class="col-md-1 col-form-label" for="pending_status"> Status </label>
                            <div class="col-md-2">
                                <select name="pending_status" id="pending_status" class="select2 form-control " ><?php echo $status_options;?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="expensehrFilter();">Go</button>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <table id="expense_hr_approval_datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Entry Date</th>
                            <th>Expense Bill No</th>
                            <th>Staff Name</th>
                            <th>Total Amount</th>
                            <th>Staus</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>  
                    
                                                             
                </table>
                
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->
