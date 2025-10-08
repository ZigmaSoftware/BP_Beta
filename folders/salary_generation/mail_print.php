<style>
 .table thead th {
    border: 1px solid #ccc;
    background: #dddddd;
    color: #000;
    font-size: 14px;
    text-transform: uppercase;
}
td {
    border: 1px solid #ccc;
    color: #262424;
    font-weight: 700;
    font-size: 13px;
}
.page-title-box .page-title {
    font-size: 1.25rem;
    margin: 0;
    line-height: 75px;
    color: #2f8132;
    font-weight: 600;
}
button#button {
    background: #38414a;
    border: 0px solid #fff;
    outline: 0;
    box-shadow: unset;
    border-radius: 5px;
    padding: 6px 15px 3px;
}
button.new-btn:hover {
    background: #075a0a;
    outline: 0;
    box-shadow: unset;
}
button.new-btn {
    background: #2f8132;
    border: 0px;
    border-radius: 5px;
    padding: 6px 12px 3px;
}
.ch-in input {
    margin-right: 13px;
}
.ch-in {
    display: flex;
}
.i-con {
    padding-right: 6px;
}
</style>





<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables


$unique_id          = "";
$table_name        = "staff";
if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $start =0;

        $unique_id  = $_GET["unique_id"];
       
        $where      = [
            "is_delete" => 0,
            "unique_id" => $unique_id
        ];

        $table = "salary_generation";
        
        $columns    = [
            "salary_date",
            "salary_no",
            "total_net_salary",
            "total_take_home",
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {

            $res_array      = $result->data;

            $entry_date       = $res_array[0]['salary_date'];
            $salary_no        = $res_array[0]['salary_no'];
            $total_net_salary = $res_array[0]['total_net_salary'];
            $total_take_home  = $res_array[0]['total_take_home'];
            
            $date = date('M-Y',strtotime($entry_date));
            
        }
    }
}

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $start =0;

        $unique_id  = $_GET["unique_id"];
       
        // $where      = [
        //     "is_delete" => 0
        // ];

        $where = " is_delete = '0' AND relieve_date='0000-00-00'  AND DATE_FORMAT(date_of_join, '%Y-%m') <= '".$entry_date."'";

        $columns    = [
            "@a:=@a+1 s_no",
            "employee_id",
            "staff_name",
            "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = ".$table_name.".designation_unique_id ) AS designation_type",
            "department",
            "work_location as branch"
        ];

        $table_details   = [
            $table_name.', (SELECT @a:= ".$start.") AS a' ,
            $columns
        ]; 

        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,"",$start,'',$sql_function);

  
        $total_records  = total_records();
        if ($result->status) {

            $res_array      = $result->data;

            $pay_slip_data     = "";
            $total_value     = 0;

            foreach ($res_array as $key => $value) {
                $s_no  = $value['s_no'];

                $pay_slip_data .="<tr>";

                $pay_slip_data .="<td>".$value['s_no']."</td>";

                $pay_slip_data .="<td><input type='checkbox'   class='form-control checkbox checkbox-success all_staff_class staff_id_class".$value['employee_id']."' id='staff_id".$value['employee_id']."' onclick = 'get_ind_staff_check(this.value,\"".$value['employee_id']."\")' name='staff_id".$value['employee_id']."' style='width: 17px;height: 17px; align: center;'><input type = 'hidden' class = 'check_val check_class".$value['s_no']."' name = 'staff_val".$value['employee_id']."' id = 'staff_val".$value['employee_id']."'><input type = 'hidden' class = 'emp_val emp_class".$value['s_no']."' name = 'emp_val".$value['employee_id']."' id = 'emp_val".$value['employee_id']."'value = ".$value['employee_id']."></td>";

                $pay_slip_data .="<td>".$value['employee_id']."</td>";
                $pay_slip_data .="<td>".$value['staff_name']."<br><label style='font-size:13px;'>( ".$value['designation_type']." - ".$value['department']." )</label></td>";
                $pay_slip_data .="<td>".$value['branch']."</td>";
                $pay_slip_data .="<td><button type='button' id ='button".$value['employee_id']."' class=' btn btn-primary  btn-rounded mr-3 new-btn'  onclick='pay_mail_send(\"".$value['employee_id']."\",\"".$_GET['unique_id']."\",\"".$entry_date."\");' ><i class='fa fa-envelope i-con' aria-hidden='true'></i> Send Mail</button></td>";
                $pay_slip_data .="</tr>";
            }
        }
    }
}



?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                    <div class="row">                                    
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" style="font-size: 16px" for="date" ></label>
                                <label class="col-md-1 col-form-label" style="font-size: 16px" for="date" >Salary Date</label>
                                <label class="col-md-2 col-form-label text-primary" style="font-size: 16px" for="salary_date" id="salary_date"><?=$date;?></label>
                                <label class="col-md-1 col-form-label" for="salary_no" style="font-size: 16px">Salary No</label>
                                <label class="col-md-2 col-form-label text-primary " style="font-size: 16px" for="take_home_salary" id="take_home_salary"><?=moneyFormatIndia($total_take_home);?></label>
                                <label class="col-md-1 col-form-label" for="net_salary" style="font-size: 16px">Net Salary</label>
                                <label class="col-md-2 col-form-label text-primary " style="font-size: 16px" for="total_net_salary" id="total_net_salary"><?=moneyFormatIndia($total_net_salary);?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">                                    
                        <div class="col-12">
                            
                            <table id="pay_slip_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="8%"><div class="ch-in"><input type='checkbox'  class='form-control'  id='staff_check' name='staff_check' style='width: 17px;height: 17px; align: center;' onclick='get_staff_check()'>Check All</div></th>
                                        <th width="10%">Employee ID</th>
                                        <th width="45%">Name</th>
                                        <th width="15%">Location</th>
                                        <th width="10%"><button type='button' id ='button' class='btn btn-primary  btn-rounded mr-3' style='align: center;'   onclick="pay_mail_send_all('<?=$s_no;?>','<?=$_GET['unique_id'];?>','<?=$entry_date;?>');" ><i class="fa fa-paper-plane i-con" aria-hidden="true"></i> Send All</button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $pay_slip_data;?>
                                </tbody>                                            
                            </table>
                        </div>
                    </div>
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  