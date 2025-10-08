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

        // Fetch Data
        // $screen_unique_id = $_POST['screen_unique_id']; 
        $unique_id        = $_POST['unique_id']; 

        if (isset($_GET["unique_id"])) {
		if (!empty($_GET["unique_id"])) {

			$unique_id = $_GET["unique_id"];
			$where = [
				"unique_id" => $unique_id
			];

    $table            = "periodic_creation_main";
    $table_sub        = "periodic_creation_sub";

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
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
        $table_details  = [
            $table_sub." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_active"           => 1,
            "is_delete"           => 0
        ];
        $order_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

               
               switch($value['level']){
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
                
                if($value['category_name'] != 'All'){
                    $value['category_name']      = category_name($value['category_name'])[0]['category_name'];
                }else{
                    $value['category_name'] = "All Categories";
                }

               if($value['site'] != 'All'){
                   $exp_site = explode(',',$value['site']);
                   foreach($exp_site as $site){
                        $site_name = site_name($site);
                        $site_id = $site_name[0]['site_name'];
                        $value['site_id'] .= "site - ".$site_id."<br>";
                    }
                }else{
                    $value['site_id'] = "All sites";
                }
                
                if($value['department'] != 'All'){
                   $exp_department = explode(',',$value['department']);
                   foreach($exp_department as $department){
                        $department_name = department_type($department);
                        
                        $department_id = $department_name[0]['department_type'];
                       
                        $value['department_name'] .= $department_id."<br>";
                    }
                }else{
                    $value['department_name'] = "All sites";
                }
                
              
            }
           $form_type  = "Update";
				$btn_text   = "Update";
				$btn_action = "update";
        } else {
            $btn_text       = "Error";
				$btn_action     = "error";
				$is_btn_disable = "disabled='disabled'";
        }

		}
        }
        
        
        
        $user_name_options = user_name();
$user_name_options =  select_option_user($user_name_options,"Select",$user_name);

$site_options      = site_name();
$site_options      = select_option($site_options,"All Site",$exp_site_name);


$department_type_options = department_type();
$department_type_options = select_option($department_type_options,"All Departments",$exp_department_name);

$category_name_option = category_creation('',$department_name);
$category_name_option = select_option_category($category_name_option, "All Categories",$complaint_category);


$level_options        = ["1" => [
                               "unique_id" => "1",
                               "value"     => "Level 1",
                                   ],
                               "2" => [
                               "unique_id" => "2",
                               "value"     => "Level 2",
                                   ],
                              "3" => [
                               "unique_id" => "3",
                               "value"     => "Level 3",
                                   ],
                               "4" => [
                                 "unique_id" => "4",
                                 "value"     => "Level 4",
                                        ],
                                 "5" => [
                                 "unique_id" => "5",
                                 "value"     => "Level 5",
                                              ],
                                 "6" => [
                                 "unique_id" => "6",
                                 "value"     => "Level 6",
                                                    ],
                                  "7" => [
                                 "unique_id" => "7",
                                 "value"     => "Level 7",
                                           ],
                           
                               ];
   $level_options        = select_option($level_options,"Select",$level_opt);
 
        
        ?>
   
   
<div class="container" style="background-color:#fff;">
    	<div class="compl_print pt-2">
    		 <div class="row">
                <div style="display: flex; align-items: center; justify-content: center;">
                    <img src="../../assets/images/logo1.png" width="80px" style="margin-right: 10px;" />
            <div>
                <h2 style="margin:0px 0px 10px 0px"><b>Zigma Global Environ Solutions Private Limited</b></h2>
                <h5 style="margin: 0;">No. 178, Indu nagar, Palayapalayam, Perundurai Road, Erode 638 011.</h5>
                <h5>04242225157  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;connect@zigma.in  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;www.zigma.in</h5>
            </div>
        </div>
    </div>

<input type="hidden" name="unique_id" id="unique_id" class="form-control" value="<?php echo $unique_id; ?>">
<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            <form class="was-validated" id="periodic_creation_form_main" name="periodic_creation_form_main">
               <input type="hidden" name="unique_id" id="unique_id" value="<?=$_unique_id;?>">
               <div class="row">
                  <div class="col-12">
                     <div class="form-group row ">
                        <input type="hidden" name="user_name" id="user_name" class="form-control" value='<?php echo  $user_name; ?>'>
                        <div class="col-md-3">
                           
                        </div>
                        <div class="col-md-5">
                        </div>
                       
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-1 col-form-label" for="user_name">User Name</label>
                        <div class="col-md-2">
						
                            <select name="user_name_select" <?=$user_name_select;?> id="user_name_select" class="select2 form-control" required onchange="get_username()">
                                <?php echo $user_name_options; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                           <label class="col-md-12 col-form-label" for="user_type_value" id="user_type_value">User Type :
						   <span id="user_type"></span></label>
                        </div>
                        <div class="col-md-2">
                           <label class="col-md-12 col-form-label" for="ph_no" id="ph_no">Phone Number :
						   <span id="mobile_no"></span></label>
                        </div>
						<div class="form-group col-md-4">
                         <label class="col-md-12 col-form-label" for="designation_name"   id="designation_name">Designation:
                          <span id="designation"></span></label> 
                     </div>
                        
                     </div>
                  </div>
               </div>
               </div>
            </form>
            <form class="periodic_sub_form was-validated" id="periodic_creation_form_sub" name="periodic_creation_form_sub">
               
               <input type="hidden" id="periodic_table_count" name="periodic_table_count" value=>
               <!-- </form> 
                  <form class="was-validated sublist-form" id="sublist-form"> -->
               <div class="row">
                  <div class="col-12">
                     <table id="periodic_sub_datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                           <tr>
                              <th>#</th>
                              <th>Department</th>
                              <th>Category</th>
                              <th>Site</th>
                              <th>Level</th>
                              <th>Starting Days</th>
                              <th>Ending Days</th>
                              
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                       
                     </table>
                  </div>
               </div>
            </form>
            
          </div>
         </div>
      </div>
      <!-- end card-body -->
   </div>
   <!-- end card -->
</div>
<!-- end col -->
</div>  
<script>
   function print(file_name)
    {
       onmouseover= window.open('uploads/periodic_creation/'+file_name,'onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }    
</script> 