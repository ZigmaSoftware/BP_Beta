<?php 
 include 'function.php';
    include '../../config/dbconfig.php';
?>
                <h3 class="header d-inline" ></h3>
                <!-- <h3 class="header text-right">Achived : 850000000.00</h3> -->

                <input type="hidden" name="team_head_staff_id" id="team_head_staff_id" value="">
                <table class="table table-striped table-bordered w-100 text-center">
                    <thead>
                        <tr>
                            <th rowspan="3">Customers Category</th>
                            <th colspan="6"><?=$_POST['team_head_staff_name'];?>&emsp;Target : <span style="font-weight: bold; font-size: 14px" id="target_amount"><?=number_format($_POST['target_amount'],2);?></span></th>
                            <th rowspan="2" colspan="2">Grand Total</th>
                        </tr>
                        <tr>
                            <?php 
                            $customer_categorys    = customer_category();
                            $item_categorys        = item_category();
                                foreach ($item_categorys as $item_key => $item_value) {
                                    $item_name = $item_value['category_name'];
                                    $item_id   = $item_value['unique_id'];
                            ?>
                                <th colspan="2"><?=$item_name;?></th>
                            <?php }?> 
                        </tr>
                        <tr>
                            <?php 
                                foreach ($item_categorys as $item_key => $item_value) { ?>
                                    <th>Target</th>
                                    <th>Achived</th>
                            <?php } ?>
                                <th>Target</th>
                                <th>Achived</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php 
                            foreach ($customer_categorys as $cat_key => $cat_value) {
                                $total_target = 0;
                                $cat_name = $cat_value['customer_category'];
                                $cat_id   = $cat_value['unique_id'];
                        ?>
                            <tr>
                                <td width="25%" style="text-align: left"><?=$cat_name;?></td>
                                <?php 
                                    foreach ($item_categorys as $item_key => $item_value) {
                                        $item_name = $item_value['category_name'];
                                        $item_id   = $item_value['unique_id'];

                                        $customer_cat_columns = [
                                            "customer_item_percentage",
                                            "sum(amount) as amount"
                                           
                                        ];
                                    
                                        $customer_cat_details = [
                                            "target_customer_item", // Table Name 
                                            $customer_cat_columns
                                        ];

                                        $where = [
                                            "staff_id"             => $_POST['team_head_staff_id'],
                                            "customer_category_id" => $cat_id,
                                            "item_category_id"     => $item_id,
                                            "is_team_head"         => $_POST['team_head'],
                                        ];

                                        $result_values  = $pdo->select($customer_cat_details,$where);
                                        //print_r($result_values);

                                        if ($result_values->status) {
                                            $result_values      = $result_values->data;
                                            $percentage = $result_values[0]["customer_item_percentage"];
                                            $amount     = $result_values[0]["amount"];

                                            $target  = $_POST['target_amount'] * ($percentage / 100);
                                        }
                                ?>
                                        <td><?php echo $target;?></td>
                                        <td></td>

                                <?php  $total_target +=$target; } ?>

                                    <td><?php echo ($total_target);?></td>
                                    <td></td>
                            </tr>
                        <?php } ?>
                       <!--  <tr>
                            <td>BFSI</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Central Govt And Central PSU's</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>State Govt.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Local Bodies.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr> -->
                    </tbody>
                </table>