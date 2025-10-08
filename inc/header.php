    <?php
        $js_css_file_version = "0.0.7";
        // $js_css_file_version = "1.0";

        $js_css_file_comment = "?v=".$js_css_file_version;

    ?>
<!DOCTYPE html>
<html lang="en" data-layout="topnav">
<head>
        <meta charset="utf-8" />
        <title>Blue Planet</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Zigma Global Environ Solutions" name="description" />
        <meta content="infobytes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="Pragma" content="no-cache" />
        <meta http-equiv="Expires" content="-1" />

        <link rel="shortcut icon" type="image/x-icon" href="img/icon/blue_planet.ico">
		
		
		<!-- DataTables Bootstrap 5
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> -->

		
		   <!-- DataTables Bootstrap 5 (CSS) -->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->
<!--<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->

		   <!-- DataTables Bootstrap 5 (CSS) -->
<link rel="stylesheet" href="../../assets/datatables/dataTables.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">
<link rel="stylesheet" href="../../assets/datatables/responsive.bootstrap.min.css">
<!--<link rel="stylesheet" href="../../assets/datatables/fixedColumns.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->
<!--<link rel="stylesheet" href="../../assets/datatables/fixedHeader.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->
<link rel="stylesheet" href="../../assets/datatables/buttons.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">
<!--<link rel="stylesheet" href="../../assets/datatables/select.bootstrap5.min.css<?php echo $js_css_file_comment; ?>">-->

		
		  <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />-->
    <!--datatable responsive css-->
    <!--<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />-->

    <!--<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">-->
		
    <script src="assets/js/config.js"></script>
	
        <!-- App css 
   <link href="assets/css/bootstrap.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" id="bs-default-stylesheet" /> 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">---->

        
         <!--  <link href="assets/css/app.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" id="app-default-stylesheet" />---->
		  <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
		  
		  <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
		

       <!--  <link href="assets/css/bootstrap-dark.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" />
        <link href="assets/css/app-dark.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" id="app-dark-stylesheet" /> ---->

        <!-- icons 
        <link href="assets/css/icons.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />
        <link href="assets/css/common.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />-->

        <?php if (session_id() AND ($user_id)) { ?>
        <!-- Datatables -->
        
		
		
		
		
		
		
        <!-- Select2 -->
        <link href="assets/libs/select2/css/select2.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />
        <link href="assets/libs/select2-bootstrap4/select2-bootstrap4.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

        <!-- Dropify 
        <link href="assets/libs/dropify/dist/css/dropify.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />-->

        <!-- Auto complete -->
        <link href="assets/libs/autocomplete/css/autocomplete.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

        <!-- jQuery Multiselect 
        <link href="assets/libs/jquery_multiselect/jquery.multiselect.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" /> -->

        <!-- jQuery
        <script src="assets/libs/jquery/jquery-3.5.1.min.js<?php echo $js_css_file_comment; ?>"></script>--> 
        

        <?php } ?>

        <!-- Flatpicker -->
        <link href="assets/libs/flatpickr/flatpickr.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />


        <!-- Sweetalert2 -->
        <link href="assets/libs/sweetalert2/sweetalert2.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />
        
    </head>