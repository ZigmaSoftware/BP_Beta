    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
    <style>
    	body {
    		background-color: #fff;
    	}
    	.box1 h3 {
			background-color: #f0f0f0;
		    padding: 6px 0px;
		    text-align: center;
		    font-weight: 700;
		    color: #333;
		    font-size: 14px;
		    margin: 4px 0px;
    	}

    	.bd-highlight {
    		font-size: 14px;
    		color: #444;
    	}

    	.contn_info.d-flex h6 {
    		text-align: right;
    		font-size: 12px;
    		margin-bottom: 6px;
    		margin-top: 6px;
    		font-weight:bold;
    	}

    	.contn_info.d-flex h5 {
    		color: #555;
    		font-size: 11.5px;
    margin-bottom: 6px;
    		margin-top: 6px;
    	}

    	.contn_info.d-flex p {
    		margin-bottom: 6px;
    		margin-top: 6px;
    	}

    	.state_boxbor {

    		margin-bottom: 10px;
    		margin-top: 20px;

    	}
    	.col-md-4 {
    		width: 33.33333333%;
    		padding-left: 5px;
    		padding-right: 5px;
    	}

    	.col-md-8 {
    		flex: 0 0 auto;
    		width: 66.66666667%;
    	}

    	.col-md-4.wid1 {
    		width: 45%;
    	}

    	.col-md-8.wid2 {
    		width: 55%;
    	}
    	.prio {
    border: 1px solid #f1f1f1;
    padding: 0px 4px;
    margin: 0px;
    margin-top: 10px;
}
.prio h6 {
   font-size: 16px!important;
    color: #cb8015;
    letter-spacing: 0.04rem;
}
hr {
    margin: 1rem 0!important;
}   
.prio h5 {
    font-size: 14px!important;
}
</style>
<?php  
    include '../../config/dbconfig.php';
    include 'function.php';

    $unique_id = $_GET['unique_id'];
    $table            = "periodic_creation_main";
    $table_sub        = "periodic_creation_sub";

        // Query Variables
        $json_array     = "";
        $columns        = [
            // "'' as s_no",
            "user_id",
            "'' as user_type",
            "'' as mobile_no",
            "unique_id",
            
        ];
        $table_details  = [
            $table,
            $columns
        ];
        
        $where  = " is_active = 1 AND is_delete = 0  and unique_id = '$unique_id' ";
        // $where     .= " AND is_delete = 0  and unique_id = '$unique_id' ";
        
        $order_by       = "";

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where);
        // print_r($result);
        $total_records  = total_records();
       
        if ($result->status) {

            $res_array      = $result->data;
            $i=1;
            foreach ($res_array as $key => $value) {
                // $value['s_no'] = $i++;
                
                $user_details    = user_name($value['user_id']);
                // $staff_name = $user_details[0]['staff_name'];
                // $value['user_id'] = disname($staff_name);
                // $value['user_id']    = disname($user_details[0]['staff_name']);
                $staff_name = disname($user_details[0]['staff_name']); 
                $value['user_id'] = $staff_name;
                
                $value['mobile_no']  = $user_details[0]['mobile_no'];
            $user_type =  user_type($user_details[0]['user_type_unique_id']);


                $value['user_type'] = $user_type[0]['user_type'];
                $designation_id = designation_name($user_details[0]['designation_id']);
                $value['designation_name'] = $designation_id[0]['designation_name'];
                // $value['designation_name']    =designation_name($value['designation_id'])[0]['designation_name'];
                $data[]                 = array_values($value);
            }
            
          
        } else {
            print_r($result);
        }
        

?>

 
   
   
<div class="container" style="background-color:#fff;">
    <div class="compl_print pt-2">
        <div class="row">
            <div style="display: flex; align-items: center; justify-content: center;">
                <img src="../../assets/images/logo1.png" width="80px" style="margin-right: 10px;" />
                <div>
                    <h2 style="margin:0px 0px 10px 0px"><b>Zigma Global Environ Solutions Private Limited</b></h2>
                    <h5 style="margin: 0;">No. 178, Indu nagar, Palayapalayam, Perundurai Road, Erode 638 011.</h5>
                    <h5>04242225157 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;connect@zigma.in &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;www.zigma.in</h5>
                </div>
            </div>
        </div>
        <input type="hidden" name="unique_id" id="unique_id" class="form-control" value="<?php echo $unique_id; ?>">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form class="was-validated" id="periodic_creation_form_main" name="periodic_creation_form_main">
                            <input type="hidden" name="unique_id" id="unique_id" value="<?= $_unique_id; ?>">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row ">
                                        <input type="hidden" name="user_name" id="user_name" class="form-control" value='<?php echo $value['user_id'] ; ?>'>
                                        
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-3 col-form-label" for="user_name"><b>User Name : </b>
                                        <span id="user_name"><?php echo $value['user_id'] ; ?></span></label>
                                        
                                        <div class="col-md-3">
                                            <label class="col-md-12 col-form-label" for="user_type_value" id="user_type_value"><b>User Type :</b>
                                                <span id="user_type"><?php echo $value['user_type']; ?></span>
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="col-md-12 col-form-label" for="ph_no" id="ph_no"><b>Phone Number :</b>
                                               <span id="mobile_no"><?php echo $value['mobile_no']; ?></span>
                                            </label>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="col-md-12 col-form-label" for="designation_name" id="designation_name"><b>Designation:</b>
                                                <span id="designation"><?php echo $value['designation_name']; ?></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!--<form class="periodic_sub_form was-validated" id="periodic_creation_form_sub" name="periodic_creation_form_sub">-->
                        <!--    <input type="hidden" id="periodic_table_count" name="periodic_table_count" value=>-->
                        <!--    <div class="row">-->
                        <!--        <div class="col-12">-->
                        <!--            <table cellspacing="0" cellpadding="0" class="" width="100%">-->
                        <!--                <thead class="colspanHead">-->
                        <!--                    <tr>-->
                        <!--                        <h3 style="text-align:center"><b>Periodic Creation</b></h3>-->
                        <!--                    </tr>-->
                        <!--                    <tr>-->
                        <!--                        <th width="13%" align="right" class="main" scope="col">S.No <?php echo $value['s_no']; ?></th>-->
                        <!--                        <th width="13%" align="right" class="main" scope="col">Department Name <?php echo $department_name[0]['department_type']; ?></th>-->
                                            <!--</tr>-->
                                            <!--<tr>-->
                        <!--                        <th align="center" class="main" scope="col">Category Name <?php echo $category_name[0]['category_name']; ?></th>-->
                                            <!--</tr>-->
                                            <!--<tr>-->
                        <!--                        <th align="center" class="main" scope="col">Site  <?php echo $value['site_id']; ?></th>-->
                                            <!--</tr>-->
                                            <!--<tr>-->
                        <!--                        <th align="center" class="main" scope="col">Level <?php echo $value['level']; ?></th>-->
                                            <!--</tr>-->
                                            <!--<tr>-->
                        <!--                        <th align="center" class="main" scope="col">Starting Days<?php echo $value["starting_count"]; ?></th>-->
                        <!--                        <th align="center" class="main" scope="col">Ending Days<?php echo $value["ending_count"]; ?></th>-->
                        <!--                    </tr>-->
                        <!--                </thead>-->
                        <!--            </table>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</form>-->
 <table cellspacing="0" cellpadding="0" width="100%" class="table table-borered">
    <thead>
        <tr>
            <th>S.No</th>
            <th>Department Name</th>
            <th>Category Name</th>
            <th>Site</th>
            <th>Level</th>
            <th>Starting Days</th>
            <th>Ending Days</th>
        </tr>
    </thead>
    <tbody>
        <?php
        
        // $screen_unique_id = $_POST['screen_unique_id'];
        $unique_id = $_GET['unique_id'];

        if (!empty($unique_id)) {
            // $where = ["unique_id" => $unique_id];

            $table_sub = "periodic_creation_sub";

            // Query Variables
            $json_array = "";
            $columns = [
                "'' as s_no",
                "'' as department_name",
                "category_name",
                "'' as site_id",
                "level",
                "starting_count",
                "ending_count",
                "unique_id",
                "site_id as site",
                "department_name as department",
            ];
            $table_details = [
                $table_sub,
                $columns
            ];
            // $where = "unique_id='$unique_id'";
            $where = "form_unique_id = '$unique_id' AND is_active = 1 AND is_delete = 0 ";
            $order_by = "";

            $sql_function = "SQL_CALC_FOUND_ROWS";

            $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
            // print_r($result);
            $total_records = total_records();

            if ($result->status) {
                $res_array = $result->data;
                $i = 1;
                foreach ($res_array as $key => $value) {
                    $value['s_no'] = $i++;

                    switch ($value['level']) {
                        case 1:
                            $value['level'] = "Level 1";
                            break;
                        case 2:
                            $value['level'] = "Level 2";
                            break;
                        case 3:
                            $value['level'] = "Level 3";
                            break;
                        case 4:
                            $value['level'] = "Level 4";
                            break;
                        case 5:
                            $value['level'] = "Level 5";
                            break;
                        case 6:
                            $value['level'] = "Level 6";
                            break;
                        case 7:
                            $value['level'] = "Level 7";
                            break;
                    }

                    if ($value['category_name'] != 'All') {
                        $value['category_name'] = category_name($value['category_name'])[0]['category_name'];
                    } else {
                        $value['category_name'] = "All Categories";
                    }

                    if ($value['site'] != 'All') {
                        $exp_site = explode(',', $value['site']);
                        foreach ($exp_site as $site) {
                            $site_name = site_name($site);
                            $site_id = $site_name[0]['site_name'];
                            $value['site_id'] .= "site - " . $site_id . "<br>";
                        }
                    } else {
                        $value['site_id'] = "All sites";
                    }

                    if ($value['department'] != 'All') {
                        $exp_department = explode(',', $value['department']);
                        foreach ($exp_department as $department) {
                            $department_name = department_type($department);
                            $department_id = $department_name[0]['department_type'];
                            $value['department_name'] .= $department_id . "<br>";
                        }
                    } else {
                        $value['department_name'] = "All";
                    }

                    $starting_count = $value["starting_count"];
                    $ending_count = $value["ending_count"];

                    echo "<tr>";
                    echo "<td>" . $value['s_no'] . "</td>";
                    echo "<td>" . $value['department_name'] . "</td>";
                    echo "<td>" . $value['category_name'] . "</td>";
                    echo "<td>" . $value['site_id'] . "</td>";
                    echo "<td>" . $value['level'] . "</td>";
                    echo "<td>" . $starting_count . "</td>";
                    echo "<td>" . $ending_count . "</td>";
                    echo "</tr>";

                    
                }
            }
        }
        ?>
    </tbody>
</table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
