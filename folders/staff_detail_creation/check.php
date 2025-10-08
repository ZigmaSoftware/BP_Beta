<style>
    .row{
        text-align: center;
    margin-top: 269px;
    margin-left: 308px;
    line-height: 10px;
    border: 1px solid gray;
    width: 60%;
    height: 160px;
    }
    h1{
        color:red;
        font-size: 23px;
    font-family: serif;
    }
    h2{
        color:green;
        font-size: 22px;
    font-family: serif;
        margin-top: 4px;
    }
    </style>

<?php


 $date = $_GET['date'];
 $time = $_GET['time'];
$current_date = date('Y-m-d');
if($current_date == $date){
    echo "helo";
        header('Location:form.php');
    }
else if($current_date != $date){
    echo '<div class="row">
<div class="col-12">
<h1>Session Time Out !</h1><br>
<h1>Your Link Has Expired. For Any Queries</h1><br>
<h2>Please Contact HR!</h2>
</div>


</div>';
// echo '<script>alert("Link has Been Expired Please Contact HR!");</script>';
}

?>