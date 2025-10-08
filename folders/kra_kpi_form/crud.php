<?php
// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];
// Database Country Table Name
$table             = "kra_kpi_form";
// Include DB file and Common Functions
include '../../config/dbconfig.php';
// include 'function.php';
$fileUpload         = new Alirdn\SecureUPload\SecureUPload($fileUploadConfig);


$fileUploadPath = $fileUploadConfig->get("upload_folder");

// Create Folder in root->uploads->(this_folder_name) Before using this file upload
$fileUploadConfig->set("upload_folder", $fileUploadPath . $folder_name . DIRECTORY_SEPARATOR);
// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$staff_name          = "";
$unique_id          = "";
$prefix             = "";
$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose
$sess_user_type  = $_SESSION['sess_user_type'];

switch ($action) {
    case 'createupdate':
        $staff_name    = $_POST["staff_name"];
        // $description     = $_POST["description"];
        $unique_id = $_POST["unique_id"];
        // print_R( $unique_id);die();
        $update_where                       = "";

        if (is_array($_FILES["test_file"]['name'])) {
            if ($_FILES["test_file"]['name'][0] != "") {
                // Multi file Upload 
                $confirm_upload     = $fileUpload->uploadFiles("test_file");
                if (is_array($confirm_upload)) {
                    // print_r($_FILES["test_file"]['name']);
                    $_FILES["test_file"]['file_name'] = [];
                    foreach ($confirm_upload as $c_key => $c_value) {
                        if ($c_value->status == 1) {
                            $c_file_name = $c_value->name ? $c_value->name . "." . $c_value->ext : "";
                            array_push($_FILES["test_file"]['file_name'], $c_file_name);
                        } else { // if Any Error Occured in File Upload Stop the loop
                            $status     = $confirm_upload->status;
                            $data       = "file not uploaded";
                            $error      = $confirm_upload->error;
                            $sql        = "file upload error";
                            $msg        = "file_error";
                            break;
                        }
                    }
                } else if (!empty($_FILES["test_file"]['name'])) { // Single File Upload
                    $confirm_upload     = $fileUpload->uploadFile("test_file");
                    if ($confirm_upload->status == 1) {
                        $c_file_name = $confirm_upload->name ? $confirm_upload->name . "." . $confirm_upload->ext : "";
                        $_FILES["test_file"]['file_name']  = $c_file_name;
                    } else { // if Any Error Occured in File Upload Stop the loop
                        $status     = $confirm_upload->status;
                        $data       = "file not uploaded";
                        $error      = $confirm_upload->error;
                        $sql        = "file upload error";
                        $msg        = "file_error";
                    }
                }
            }
        }
        if (is_array($_FILES["test_file"]['name'])) {
            if ($_FILES["test_file"]['name'][0] != "") {
                $file_names     = implode(",", $_FILES["test_file"]['file_name']);
                $file_org_names = implode(",", $_FILES["test_file"]['name']);
            }
        } else if (!empty($_FILES["test_file"]['name'])) {
            $file_names     = $_FILES["test_file"]['file_name'];
            $file_org_names = $_FILES["test_file"]['name'];
        }

        // document2
        if (is_array($_FILES["test_file1"]['name'])) {

            if ($_FILES["test_file1"]['name'][0] != "") {

                // Multi file Upload 
                $confirm_upload     = $fileUpload->uploadFiles("test_file1");

                if (is_array($confirm_upload)) {
                    // print_r($_FILES["test_file"]['name']);
                    $_FILES["test_file1"]['file_name'] = [];
                    foreach ($confirm_upload as $c_key => $c_value) {
                        if ($c_value->status == 1) {
                            $c_file_name = $c_value->name ? $c_value->name . "." . $c_value->ext : "";
                            array_push($_FILES["test_file1"]['file_name'], $c_file_name);
                        } else { // if Any Error Occured in File Upload Stop the loop
                            $status     = $confirm_upload->status;
                            $data       = "file not uploaded";
                            $error      = $confirm_upload->error;
                            $sql        = "file upload error";
                            $msg        = "file_error";
                            break;
                        }
                    }
                } else if (!empty($_FILES["test_file1"]['name'])) { // Single File Upload
                    $confirm_upload     = $fileUpload->uploadFile("test_file1");

                    if ($confirm_upload->status == 1) {
                        $c_file_name = $confirm_upload->name ? $confirm_upload->name . "." . $confirm_upload->ext : "";
                        $_FILES["test_file1"]['file_name']  = $c_file_name;
                    } else { // if Any Error Occured in File Upload Stop the loop
                        $status     = $confirm_upload->status;
                        $data       = "file not uploaded";
                        $error      = $confirm_upload->error;
                        $sql        = "file upload error";
                        $msg        = "file_error";
                    }
                }
            }
        }

        if (is_array($_FILES["test_file1"]['name'])) {
            if ($_FILES["test_file1"]['name'][0] != "") {
                $file_name     = implode(",", $_FILES["test_file1"]['file_name']);
                $file_org_name = implode(",", $_FILES["test_file1"]['name']);
            }
        } else if (!empty($_FILES["test_file1"]['name'])) {
            $file_name    = $_FILES["test_file1"]['file_name'];
            $file_org_name = $_FILES["test_file1"]['name'];
        }


        if (($file_names) && ($file_name)) {
            $columns            = [

                "staff_name"         => $staff_name,
                "doc_name"           => $file_names,
                "document_name"      => $file_name,
                "unique_id"             => unique_id($prefix)
            ];
        } else {
            $columns            = [
                "staff_name"          => $staff_name,
                "unique_id"           => unique_id($prefix)

            ];
        }

        // Update Begins
        if ($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];
            $action_obj     = $pdo->update($table, $columns, $update_where);
            // Update Ends
        } else {
            // Insert Begins            
            $action_obj     = $pdo->insert($table, $columns);
            // Insert Ends

        }
        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

            if ($unique_id) {
                $msg        = "update";
            } else {
                $msg        = "create";
            }
        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }
        // }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);
        break;

    case 'datatable':
        $btn_edit_delete = "kra_kpi_form";
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start         = $_POST['start'];
        $draw         = $_POST['draw'];
        $limit         = $length;
        $data        = [];
        if ($length == '-1') {
            $limit  = "";
        }
        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select staff_name from staff where staff.unique_id = " .$table.".staff_name) AS staff_name",
            // "staff_name",
            "doc_name",
            "document_name",
            "unique_id",
            // "staff_name"
        ];
        // }
        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];
        if ($_SESSION['sess_user_type']  == '5f97fc3257f2525529') {
            $where = " is_delete = '0' ";
        } else if (
            $_SESSION['sess_user_type']
            != '5f97fc3257f2525529'
        ) {
            $where = " is_delete = '0' and staff_name='" . $_SESSION['staff_id'] . "'";
        }
        if ($_POST['staff_name']) {

            $where  .= " AND staff_name = '".$_POST['staff_name']."'";
        }
       
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);

        // Datatable Searching
        $search         = datatable_searching($search, $columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);

        $total_records  = total_records();

        if ($result->status) {
            $res_array      = $result->data;
            foreach ($res_array as $key => $value) {
                // $value['staff_name']           = staff_name($value['staff_name'])[0]['staff_name'];
                $value['doc_name']      = image_view1("kra_kpi_form", $value['unique_id'], $value['doc_name']);
                $value['document_name'] = image_view("kra_kpi_form", $value['unique_id'], $value['document_name']);
                if ($_SESSION['sess_user_type']  == '5f97fc3257f2525529') {
                $btn_edit               = btn_update($folder_name, $value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete, $value['unique_id']);
                }
                $value['unique_id']     = $btn_edit.$btn_delete;
                $data[]                 = array_values($value);
            }
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
        break;

    case 'delete':
        $unique_id      = $_POST['unique_id'];
        $columns        = [
            "is_delete"   => 1
        ];
        $update_where   = [
            "unique_id"     => $unique_id,
        ];
        $action_obj     = $pdo->update($table, $columns, $update_where);
        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";
        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }
        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];
        echo json_encode($json_array);
        break;
    default:
        break;
}
function image_view1($folder_name = "", $unique_id = "", $doc_name = "")
{
    // print_r($doc_name);DIE();
    $file_name = explode(',', $doc_name);

    $image_view = '';

    if ($doc_name) {
        foreach ($file_name as $file_key => $doc_name) {

            if ($file_key != 0) {
                if ($file_key % 4 != 0) {
                    $image_view .= "&nbsp";
                } else {
                    $image_view .= "<br><br>";
                }
            }

            $cfile_name = explode('.', $doc_name);
            if ($doc_name) {
                if (($cfile_name[1] == 'jpg') || ($cfile_name[1] == 'png') || ($cfile_name[1] == 'jpeg')) {
                    $image_view .= '<a href="javascript:print_view(\'/' . $doc_name . '\')"><img src="uploads/' . $folder_name . '/' . $doc_name . '"  height="50px" width="50px" ></a>';
                    // $image_view .= '<img src="uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" >';
                } else if ($cfile_name[1] == 'pdf') {
                    $image_view .= '<a href="javascript:print(\'/' . $doc_name . '\')"><img src="uploads/kra_kpi_form/pdf.png"  height="50px" width="50px" ></a>';
                } else if (($cfile_name[1] == 'pdf') || ($cfile_name[1] == 'xls') || ($cfile_name[1] == 'xlsx')) {
                    $image_view .= '<a href="javascript:print(\'/' . $doc_name . '\')"><img src="uploads/kra_kpi_form/excel.png"  height="50px" width="50px" ></a>';
                } else if (($cfile_name[1] == 'txt') || ($cfile_name[1] == 'docx') || ($cfile_name[1] == 'doc')) {
                    $image_view .= '<a href="javascript:print(\'/' . $doc_name . '\')"><img src="uploads/kra_kpi_form/word.png"  height="50px" width="50px" ></a>';
                }
            }
        }
    }  
    return $image_view;
}

function image_view($folder_name = "", $unique_id = "", $document_name = "")
{
    $file_names = explode(',', $document_name);
    $image_view1 = '';

    if ($document_name) {
        foreach ($file_names as $file_key => $document_name) {
            if ($file_key != 0) {
                if ($file_key % 4 != 0) {
                    $image_view1 .= "&nbsp";
                } else {
                    $image_view1 .= "<br><br>";
                }
            }

            $cfile_name = explode('.', $document_name);

            if ($document_name) {
                // if (($cfile_name[1] == 'jpg') || ($cfile_name[1] == 'png') || ($cfile_name[1] == 'jpeg')) {
                //     $image_view1 .= '<a href="javascript:print_view(\'/' . $document_name . '\')"><img src="uploads/' . $folder_name . '/' . $document_name . '"  height="50px" width="50px" ></a>';
                // } else if ($cfile_name[1] == 'pdf') {
                //     $image_view1 .= '<a href="javascript:print(\'/' . $document_name . '\')"><img src="uploads/kra_kpi_form/pdf.png"  height="50px" width="50px" ></a>';
                // } else if (($cfile_name[1] == 'pdf') || ($cfile_name[1] == 'xls') || ($cfile_name[1] == 'xlsx')) {
                //     $image_view1 .= '<a onclick="print(\'/' . $document_name . '\')"><img src="uploads/kra_kpi_form/excel.png"  height="50px" width="50px" ></p>';
                // } else if (($cfile_name[1] == 'txt') || ($cfile_name[1] == 'docx') || ($cfile_name[1] == 'doc')) {
                //     $image_view1 .= '<a  print(\'/' . $document_name . '\')"><img src="uploads/kra_kpi_form/word.png"  height="50px" width="50px" ></p>';
                // }
                if (($cfile_name[1] == 'jpg') || ($cfile_name[1] == 'png') || ($cfile_name[1] == 'jpeg')) {
                    $image_view1 .= '<a href="javascript:print_view(\'/' . $document_name . '\')"><img src="uploads/' . $folder_name . '/' . $document_name . '"  height="50px" width="50px" ></a>';
                    // $image_view .= '<img src="uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" >';
                } else if ($cfile_name[1] == 'pdf') {
                    $image_view1 .= '<a href="javascript:print(\'/' . $document_name . '\')"><img src="uploads/kra_kpi_form/pdf.png"  height="50px" width="50px" ></a>';
                } else if (($cfile_name[1] == 'pdf') || ($cfile_name[1] == 'xls') || ($cfile_name[1] == 'xlsx')) {
                    $image_view1 .= '<a href="javascript:print(\'/' . $document_name . '\')"><img src="uploads/kra_kpi_form/excel.png"  height="50px" width="50px" ></a>';
                } else if (($cfile_name[1] == 'txt') || ($cfile_name[1] == 'docx') || ($cfile_name[1] == 'doc')) {
                    $image_view1 .= '<a href="javascript:print(\'/' . $document_name . '\')"><img src="uploads/kra_kpi_form/word.png"  height="50px" width="50px" ></a>';
                }
            }
        }
    }

    return $image_view1;
}
