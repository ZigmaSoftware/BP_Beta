
<?php

$btn_text = "Click";
?>
<li class="next list-inline-item float-right mr-0">

                     <?php echo btn_link('staff_detail_creation',$_SESSION['user_name']&$_SESSION['password']);?>
                        <!-- <a href="index.php?file=staff_detail_creation/form?username=<?php echo $_SESSION['user_name'];?>&pass=<?php echo $_SESSION['password'];?>" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn"><?php echo $btn_text; ?> & Continue</a> -->
                     </li>
