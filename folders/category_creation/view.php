 <?php 
$department_type   = $_POST["department_type"];
$category_name     = $_POST["category_name"];
$main_category_name     = $_POST["main_category_name"];
?>
 
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
    margin: 5px 0px !important;
}   
.prio h5 {
    font-size: 14px!important;
}
hr:last-child {
    height: 0px!important;
}
</style>


<?php 


include '../../config/dbconfig.php';
include 'function.php';

if (isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {

    $unique_id = $_GET["unique_id"];
    $dept_name = $_GET["dept_name"];
    $main_category_name = $_GET["main_category"];

    $where = "(category_name IN ('$unique_id') OR category_name= 'All') AND     (department_name IN ('$dept_name') OR department_name='All') AND  
        user_id != 'NULL' AND is_delete = 0";

    if($value['category_name'] != 'All'){
        $value['category_name'] = category_name($value['category_name'])[0]['category_name'];
    } else {
        $value['category_name'] = "All Categories";
    }
    if($value['department'] != 'All'){
        $department_name = department_type($department);
    }
    
    $table = "periodic_creation_sub";

    $columns = [
        "(SELECT staff_name FROM user WHERE unique_id = " . $table . ".user_id) AS user_id",
        "(SELECT mobile_no FROM user WHERE unique_id = " . $table. ".user_id) AS mobile_no",
        "site_id",
        "level",
        "starting_count",
        "ending_count",
    ];

    $table_details = [
        $table,
        $columns
    ];
    // print_r($table_details);
    
    $group = " group by user_id,level,department_name";
    $order = " order by level ASC";

    $select_result = $pdo->select($table_details, $where.$group.$order, null, null);
// print_r($select_result);die();
    if ($select_result->status) {
        $select_result = $select_result->data;
        
        // Loop through each result
        foreach ($select_result as $row) {
            $user_name = $row["user_id"];
            $mobile_no = $row["mobile_no"];
            if ($mobile_no!=='') {
                $mobile_no = $row["mobile_no"];
            }
            else{
                $mobile_no = " ";
                // echo "NULL";
            }
            $level_options = $row["level"];
            $starting_count = $row["starting_count"];
            $ending_count = $row["ending_count"];
            if($row['site_id'] != 'All'){
                $site_name = site_name($site_id)[0]['site_name'];
                $row['site_id']  = $site_name;
            }
        }
    } else {
        print_r($select_result);
    }
}
?>


<div class="container" style="background-color:#fff;">
    <div class="compl_print pt-4">
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
        <hr>
        <table cellspacing="0" cellpadding="0" class="" width="100%">
			<thead class="colspanHead">
				<tr>
					<h3 style="text-align:center"><b>Category Creation</b></h3> 
				</tr>
				<tr>
					<?php
                    if (isset($_GET['dept_name'])) {
                         $department = $_GET['dept_name'];
                        $department_name = department_type($department);
                    } else {
                        $department_name = "All";
                    }
                    
                    ?>
					<th width="13%"  align="right"  class="main" scope="col"><b>Department Name : </b><?php echo $department_name[0]['department_type'];?></th>
                                  
					</tr>
						
					<tr>
					<?php
                    if (isset($_GET['main_category'])) {
                        $main_category = $_GET['main_category'];
                        //$main_category_name = main_category($main_category);
                    } else {
                        $main_category = "All";
                    }
                    
                    ?>
					<th  align="center" class="main" scope="col" ><b>Main Category Name: </b><?php echo $main_category;?></th>
					
					</tr>
								        
					<tr>
					<?php
                    if (isset($_GET['unique_id'])) {
                         $category = $_GET['unique_id'];
                        $category_name = category_name($category);
                    } else {
                        $category_name = "All";
                    }
                    
                    ?>
					<th  align="center" class="main" scope="col" ><b>Category Name:</b> <?php echo $category_name[0]['category_name'];?></th>
					
					</tr>	
			</thead>
		</table>
       
        <div style="width: 100%;padding: 10px;background: #ffffff;border: 1px solid #cecece;padding-bottom: 0px;font-family: monospace;">
    <div class="state_recom3">
        <table class="table">
            <thead>
                <tr style="text-align:center;padding: 10px;margin: 10px 0;font-family: Poppins, sans-serif;color: #343a40;">
                    <th>User Name</th>
                    <th>Mobile Number</th>
                    <th>Site Id</th>
                    <th>Level</th>
                    <th>Starting Days</th>
                    <th>Ending Days</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($select_result as $row) { ?>
                    <tr style="padding: 10px;margin: 10px 0;font-family: Poppins, sans-serif;color: #343a40;">
                        <td><?=$row["user_id"]; ?></td>
                        <td><?=$row["mobile_no"]; ?></td>
                        <td>
                            <?php 
                            if($row['site_id'] != 'All'){
                                $site_names = explode(",", $row['site_id']);
                                $formatted_site_names = [];
                                foreach ($site_names as $site_name) {
                                    $formatted_site_names[] = site_name(trim($site_name))[0]['site_name'];
                                }
                                echo implode(", ", $formatted_site_names);
                            }else{echo "All";}
                            ?>
                        </td>
                        <td><?=$row["level"]; ?></td>
                        <td><?=$row["starting_count"]; ?></td>
                        <td><?=$row["ending_count"]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<!--<div class="row mt-3">-->
<!--            <div style="width: 100%;padding: 10px;background: #ffffff;border: 1px solid #cecece;padding-bottom: 0px;font-family: monospace;">-->
<!--                <div class="state_recom3">-->
<!--                    <div class="contn_info d-flex">-->
                        <!--<div class="col-md-2 ">-->
                        <!--    <div class="box">-->
                        <!--        <h5 style="font-size: 18px; font-weight: bold; color: #333;">User Name</h5><hr>-->
                        <!--        <?php foreach ($select_result as $row) { ?>-->
                        <!--            <h6 style="text-align: left;"><?=$row["user_id"]; ?></h6><hr/>-->
                        <!--        <?php } ?>-->
                               
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-2 ">-->
                        <!--    <div class="box">-->
                        <!--        <h5 style="font-size: 18px; font-weight: bold; color: #333;">Mobile Number</h5><hr>-->
                        <!--        <?php foreach ($select_result as $row) { ?>-->
                        <!--            <h6 style="text-align: left;"><?=$row["mobile_no"]; ?></h6><hr/>-->
                        <!--        <?php } ?>-->
                               
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-4 ">-->
                        <!--    <div class="box">-->
                        <!--        <h5 style="font-size: 18px; font-weight: bold; color: #333;">Site Id</h5><hr>-->
                          <?php 
                                // foreach ($select_result as $row) {
                                // if($row['site_id'] != 'All'){
                                    
                                //     $site_names = explode(",", $row['site_id']);
                                //     $formatted_site_names = [];
                                //     foreach ($site_names as $site_name) {
                                //         $formatted_site_names[] = site_name(trim($site_name))[0]['site_name'];
                                //     }
                                //     $row['site_id'] = implode(", ", $formatted_site_names);
                                    
                                //     // $site_details = site_type($row['site_id']);
                                //     // $site_name = $site_details[0]['site_name'];
                                //     // $row['site_id']  = $site_name;
                                // }
                              ?>
                                    <!--<h6 style="text-align: left;"><?=$row["site_id"]; ?></h6><hr/>-->
                        <?php 
                                // } 
                              ?>
                               
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-1 wid1">-->
                        <!--    <div class="box">-->
                        <!--        <h5 style="font-size: 16px; text-align: center;font-weight: bold; color: #333;">Level</h5><hr>-->
                        <!--        <?php foreach ($select_result as $row) { ?>-->
                        <!--            <h6 style="text-align: center;"><?=$row["level"]; ?></h6><hr/>-->
                        <!--        <?php } ?>-->
                               
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-2 wid1">-->
                        <!--    <div class="box">-->
                        <!--        <h5 style="font-size: 16px; text-align: center;font-weight: bold; color: #333;">Starting Days</h5><hr>-->
                        <!--        <?php foreach ($select_result as $row) { ?>-->
                        <!--            <h6 style="text-align: center;"><?=$row["starting_count"]; ?></h6><hr/>-->
                        <!--        <?php } ?>-->
                               
                        <!--    </div>-->
                        <!--</div>-->
                        <!--<div class="col-md-2 wid1">-->
                        <!--    <div class="box">-->
                        <!--        <h5 style="font-size: 16px; text-align: center; font-weight: bold; color: #333;">Ending Days</h5><hr>-->
                              
                        <!--        <?php foreach ($select_result as $row) { ?>-->
                        <!--            <h6 style="text-align: center;"><?=$row["ending_count"]; ?></h6><hr/>-->
                        <!--        <?php } ?>-->
                               
                        <!--    </div>-->
                        <!--</div>-->
        <!--                <hr/>-->
                        
        <!--            </div>-->
                    
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
       

        
    </div>
</div>
