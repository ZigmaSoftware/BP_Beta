<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table_users        = "user";
$table_messages     = "chat_messages";
$table_groups       = "chat_groups";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$user_type          = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {

    case 'message':
        $sender_id   = $_SESSION['staff_id'];
        $receiver_id = $_POST['receiver_id'];
        $message     = $_POST['message'];

        $insert_columns = [
            "sender_id"     => $sender_id,
            "receiver_id"   => $receiver_id,
            "message"       => $message,
            "unique_id"     => unique_id('msg')
        ];

        $insert     = $pdo->insert($table_messages,$insert_columns);
    break;

    case 'contacts':

    // print_r($_POST);

    // Get Active Users From Users Table 

    // Update Last Active Status in User

    $update_columns = [
        "last_active_time" => date("Y-m-d H:i:s")
    ];

    $update_where   = [
        "unique_id" => $_SESSION['user_id']
    ];

    $update_time = $pdo->update($table_users,$update_columns,$update_where);
    
    $contacts     = "";

    $user_columns = [
        "unique_id",
        "user_name",
        "(SELECT a.staff_name FROM staff a WHERE a.unique_id = user.staff_unique_id) AS staff_name",
        "staff_unique_id",
        "profile_image"
    ];

    $user_details = [
        $table_users,
        $user_columns
    ];

    $user_where   = " is_delete = 0 AND is_active = 1 AND unique_id != '".$_SESSION['user_id']."' ";

    $users  = $pdo->select($user_details,$user_where);

    if (($users->status) && (!empty($users->data))) {

        $users_data = $users->data;

        foreach ($users_data as $user_key => $user_value) {

            $user_id    = $user_value["unique_id"];
            $user_name  = $user_value["user_name"];
            $staff_name = $user_value["staff_name"];
            $staff_id   = $user_value["staff_unique_id"];
            $user_img   = $user_value["profile_image"];

            $user_image = "img/user.jpg";
            
            
            // $contacts .= '<a href="javascript:void(0);" class="text-body" onclick="chats(\''.$staff_id.'\',\''.$staff_name.'\')">
            //     <div class="media p-2">
            //         <img src="'.$user_image.'" class="mr-2 rounded-circle" height="42" alt="'.$staff_name.'">
            //         <div class="media-body">
            //             <h5 class="mt-0 mb-0 font-14">
            //                 <span class="float-right text-muted font-weight-normal font-12">4:30am</span>
            //                 '.$staff_name.'
            //             </h5>
            //             <p class="mt-1 mb-0 text-muted font-14">
            //                 <span class="w-25 float-right text-right"><span class="badge badge-soft-danger">3</span></span>
            //                 <span class="w-75">How are you today?</span>
            //             </p>
            //         </div>
            //     </div>
            //     </a>';

            $contacts .= '<a href="javascript:void(0);" class="text-body" onclick="chats(\''.$staff_id.'\',\''.$staff_name.'\')">
                <div class="media p-2">
                    <img src="'.$user_image.'" class="mr-2 rounded-circle" height="42" alt="'.$staff_name.'">
                    <div class="media-body">
                        <h5 class="mt-0 mb-0 font-14">
                            
                            '.$staff_name.'
                        </h5>
                    </div>
                </div>
                </a>';
        }


    } else {
        $contacts = "No user found related to your search term";
    }

    echo $contacts;

    break;

    case 'contacts_search':

    break;

    case 'groups':

    break;

    case 'chats':

    $sender_id      = $_SESSION["staff_id"];
    $receiver_id    = $_POST["receiver_id"];

    $active_status  = "In Active";

    // get Last Activity of Receiver
    $activity_details = [
        "user",  // Table Name
        [
            "last_active_time" // Selected Columns
        ]
    ];

    $activity_where  = " staff_unique_id = '".$receiver_id."' ";

    $activity_select = $pdo->select($activity_details,$activity_where);

    // print_r($activity_select);

    if (($activity_select->status) && (!empty($activity_select->data))) {
        $activity_data  = $activity_select->data[0]['last_active_time'];

        $activity_time  = strtotime($activity_data);

        $timelimit      = time() - 5; // 5 Seconds

        if($activity_time > $timelimit)
        {
          $active_status = "Active";
        } 
    }

    $chat_messages   = "";

    // Get Chat Messages Between Sender and Receiver

    $message_columns = [
        "sender_id",
        "receiver_id",
        "message",
        "(SELECT a.staff_name FROM staff a WHERE a.unique_id = chat_messages.sender_id) AS in_user",
        "(SELECT a.staff_name FROM staff a WHERE a.unique_id = chat_messages.receiver_id) AS out_user"
    ];


    $message_details = [
        $table_messages,
        $message_columns
    ];

    $message_where = " is_active = 1 AND is_delete = 0 AND (sender_id = '".$sender_id."' AND receiver_id = '".$receiver_id."') OR  (sender_id = '".$receiver_id."' AND receiver_id = '".$sender_id."')";

    $messages = $pdo->select($message_details,$message_where);

    if (($messages->status) && (!empty($messages->data))) {

        $message_data = $messages->data;

        foreach ($message_data as $msg_key => $msg_value) {
            
            $add_odd    = "";

            if ($msg_value['sender_id'] == $sender_id) {
                $add_odd    = " odd";
            } 
            // else {
            //     $msg_value['sender_id'] = $msg_value['receiver_id'];
            //     $msg_value['in_user']   = $msg_value['out_user'];
            // }

            
            // Add Class odd for right side Message(Current User)    
            $chat_messages .= '<li class="clearfix '.$add_odd.' chat_message">
                        <div class="chat-avatar">
                            <img src="img/user.jpg" class="rounded" alt="'.$msg_value['in_user'].'">
                            <i>10:00</i>
                        </div>
                        <div class="conversation-text">
                            <div class="ctext-wrap">
                                <i>'.$msg_value['in_user'].'</i>
                                <p>
                                    '.$msg_value['message'].'
                                </p>
                            </div>
                        </div>
                        <div class="conversation-actions dropdown">
                            <button class="btn btn-sm btn-link" data-toggle="dropdown" aria-expanded="false"><i class="mdi mdi-dots-vertical font-16"></i></button>
        
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">Copy Message</a>
                                <a class="dropdown-item" href="#">Edit</a>
                                <a class="dropdown-item" href="#">Delete</a>
                            </div>
                        </div>
                    </li>';
        }
        
    } else {
        $chat_messages = "Start New Conversation...";
    }

    $json_array = [
        "chats"     => $chat_messages,
        "active"    => $active_status
    ];

    echo json_encode($json_array);

    // echo $chat_messages;

    break;

    default:
        echo "You Dont have Permission to Access This Page";
    break;
}

?>