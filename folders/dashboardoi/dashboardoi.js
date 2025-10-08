$(document).ready(function () {
   
    get_task_details();
    get_tagged_details()
    get_site_details();
    get_action_taken();
    get_top_most_complaints();
    get_overall_complaint_status();
    state_wise_map();
    registered_complaints();
    action_taken();
    overall_complaint_status();
    sourcewise_complaints();
});


function get_month_details(){
   
    get_site_details();
    get_action_taken();
    get_top_most_complaints();
    state_wise_map();
    registered_complaints();
    action_taken();
   
}

function submitForm() {
    var priorityName = document.getElementById('priority_name').value;
    document.getElementById('priority_name_post').value = priorityName;
    priority_entry_filter(priorityName);
    document.getElementById('priorityForm').submit();
}

// function priority_entry_filter(priority_type){
//     var priority_id = priority_type;
//     if(priority_id == '664ad1a16448664824'){
//         alert('Operation Impact');
//         window.location.href = 'indexoi.php';
//     }else if(priority_id == '664ad1b10b59c64207'){
//         alert('Operation Not Impact');
//     }else{
//         window.location.href = 'index.php';
//     }
// }


function priority_entry_filter(priority_type){
    var priority_id = priority_type;
    if(priority_id == '664ad1a16448664824'){
        //alert('Operation Impact');
        document.getElementById('priorityForm').action = 'indexoi.php';
    } else if (priority_id == '664ad1b10b59c64207'){
       // alert('Operation Not Impact');
         document.getElementById('priorityForm').action = 'indexoni.php';
    } else {
        document.getElementById('priorityForm').action = 'index.php';
    }
}

function get_overall_complaint_status(){
    // alert("hii");
    var month = $("#month_filter").val();
    var data = 
        {
            "action"           : "overall_complaint_status",
            "month"            : month
        };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
        url: ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
            var obj   = JSON.parse(data);
            var data     = obj.data;
            var total_comp      = obj.total_comp;
                    var progressing_comp      = obj.progressing_comp;
                    var completed_comp          = obj.completed_comp;
                    var cancel_comp    = obj.cancel_comp;
var pending_comp    = obj.pending_comp;

            $('#overall_complaint_status').html(data);
             $('#total_comp').html(total_comp);
                    $('#progressing_comp').html(progressing_comp);
                    $('#completed_comp').html(completed_comp);
                    $('#cancel_comp').html(cancel_comp);
                    $('#pending_comp').html(pending_comp);
        }
   });
}


function get_site_details(){
    var month = $("#month_filter").val();
    var data = 
        {
            "action"           : "site_details",
            "month"            : month
        };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
        url: ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
            var obj   = JSON.parse(data);
            var data     = obj.data;
            $('#site_details_div').html(data);
        }
   });
}

function get_action_taken(){
    var month = $("#month_filter").val();
    var data = 
        {
            "action"           : "action_taken",
            "month"            : month
        };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
        url: ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
            var obj   = JSON.parse(data);
            var data     = obj.data;
            $('#action_taken').html(data);
        }
   });
}

function get_top_most_complaints(){
//     // alert("hiii");
//     // var month = $("#month_filter").val();
    var data = 
        {
            "action"           : "age_wise_complaints",
        };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
        url: ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
//             // alert(data);
            var obj   = JSON.parse(data);
            $('#less_1').text(obj.less_1);
            $('#less_5').text(obj.less_5);
            $('#less_10').text(obj.less_10);
            $('#less_15').text(obj.less_15);
            $('#less_30').text(obj.less_30);
            $('#greater_30').text(obj.greater_30);
            $('#total_count').text(obj.total_count); // Set the total count
        }
  });
}

// function get_top_most_complaints(){
//     var data = {
//         "action": "top_most_complaints"
//     };
//     var ajax_url = sessionStorage.getItem("folder_crud_link");
//     $.ajax({
//         url: ajax_url,
//         type: 'POST',
//         data: data,
//         success: function(data) {
//             var obj = JSON.parse(data);
//             $('#tot_comp').text(obj.tot_comp);
//             $('#pending').text(obj.pending);
//             $('#progressing').text(obj.progressing);
//             $('#registered').text(obj.registered);
//             $('#cancelled').text(obj.cancelled);
//         }
//     });
// }



function get_task_details()  
{
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");
    
    
    var data = {
                "action"    : "task_details"
            }

            $.ajax({
                type    : "POST",
                url     : ajax_url,
                data    : data,
                success : function(data) 
                {
                    var obj     = JSON.parse(data);
                    var pending_complaints      = obj.pending_complaints;
                    var pending_calls           = obj.pending;
                    var opening_complaints      = obj.opening_complaints;
                    var new_complaints          = obj.new_complaints;
                    var completed_complaints    = obj.completed_complaints;

                    $('#opening_complaints').html(opening_complaints);
                    $('#new_complaints').html(new_complaints);
                    $('#completed_complaints').html(completed_complaints);
                    $('#pending_complaints').html(pending_complaints);
                    $('#pending_calls').html(pending_calls);
                }
            });
}

function get_tagged_details()  
{
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");
    
    
    var data = {
        
                "action"    : "tagged_details"
            }
            

            $.ajax({
                type    : "POST",
                url     : ajax_url,
                data    : data,
                success : function(data) 
                {
                    
                    var obj     = JSON.parse(data);
                    var tagged_calls      = obj.tagged_calls;

                    $('#tagged_calls').html(tagged_calls);
                }
            });
}

function state_wise_map(){
    var month = $("#month_filter").val();
    var data = 
    {
        "month"            : month,
        "action"           : "state_wise_map"
    };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    
    $.ajax({
        url:ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
            $('#fever1').empty();
            var obj   = JSON.parse(data);
        
            state_1         = obj.state_1;
            state_2         = obj.state_2;
            state_3         = obj.state_3;
            state_4         = obj.state_4;
            date          = obj.date;
    
            var optionsLine = {
                chart: {
                    foreColor: '#9ba7b2',
                    height: 360,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    dropShadow: {
                        enabled: true,
                        top: 3,
                        left: 2,
                        blur: 4,
                        opacity: 0.1,
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 5
                },
                colors: ["#004b58", '#00cd3a', '#ffc416', '#6d00cf'],
        
                series: [{
                    name: "State 1",
                    data: state_1
                }, {
                    name: "State 2",
                    data: state_2
                },
                {
                    name: "State 3",
                    data: state_3
                },
                {
                    name: "State 4",
                    data: state_4
                }],
                title: {
                    text: '',
                    align: 'left',
                    offsetY: 25,
                    offsetX: 20
                },
                subtitle: {
                    text: '',
                    offsetY: 55,
                    offsetX: 20
                },
                markers: {
                    size: 4,
                    strokeWidth: 0,
                    hover: {
                        size: 7
                    }
                },
                grid: {
                    show: true,
                    padding: {
                        bottom: 0
                    }
                },
                labels: date,
                xaxis: {
                    tooltip: {
                        enabled: false
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'right',
                    offsetY: 10
                }
            }
            var chartLine = new ApexCharts(document.querySelector('#chart'), optionsLine);
            chartLine.render();
        }
    });

}

function overall_complaint_status()  
{
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");
    
    
    var data = {
                "action"    : "over_complaint_details"
            }

            $.ajax({
                type    : "POST",
                url     : ajax_url,
                data    : data,
                success : function(data) 
                {
                    var obj     = JSON.parse(data);
                    var total_comp          = obj.total_comp;
                    var pending_comp        = obj.pending_comp;
                    var progressing_comp    = obj.progressing_comp;
                    var completed_comp      = obj.completed_comp;
                    var cancel_comp         = obj.cancel_comp;

                    $('#pending_comp').html(pending_comp);
                    $('#progressing_comp').html(progressing_comp);
                    $('#completed_comp').html(completed_comp);
                    $('#total_comp').html(total_comp);
                    $('#cancel_comp').html(cancel_comp);
                }
            });
}


function commends_delete(unique_id = "") {
    // alert("hi");
    var data = {
        "unique_id": unique_id,
        "action": "delete_commends"
    };

    const ajax_url = "crud.php";

    // Perform AJAX request
    $.ajax({
        url: ajax_url,
        type: 'POST',
        data: data,
        success: function(response) {
            // alert(response);
            try {
                var obj = JSON.parse(response); // Parse JSON response
                // alert(obj);
                if (obj.status) { // Check if the delete operation was successful
                    alert("Commend deleted successfully.");
                    location.reload(); // Reload the page
                } else {
                    alert("Failed to delete commend.");
                }
            } catch (error) {
                console.error("Error parsing JSON response:", error);
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX request failed:", status, error);
        }
    });
}



function sourcewise_complaints()  
{
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");
    
    
    var data = {
                "action"    : "sourcewise_complaints"
            }

            $.ajax({
                type    : "POST",
                url     : ajax_url,
                data    : data,
                success : function(data) 
                {
                    var obj     = JSON.parse(data);
                    var app          = obj.app;
                    var web        = obj.web;
                    var admin_portal    = obj.admin_portal;
                    var chatbot      = obj.chatbot;
                   
                    $('#web').html(web);
                    $('#admin').html(admin_portal);
                    $('#chatbot').html(chatbot);
                    $('#app').html(app);
                   
                }
            });
}

// function new_external_window_print(event, url, complaint_type) {

//     var link = url + '?complaint_type=' + complaint_type;

//     onmouseover = window.open(link, 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
//     event.preventDefault();
// }


function new_external_window_print(event, url, complaint_type,priority_type) {

    var link = url + '?complaint_type=' + complaint_type +'&priority_type=' + priority_type ;

    onmouseover = window.open(link, 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    event.preventDefault();
}

// function new_external_window_print1(event, url, days_cnt) {

//     var link = url + '?days_cnt=' + days_cnt;

//     onmouseover = window.open(link, 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
//     event.preventDefault();
// }

function new_external_window_print1(event, url, days_cnt,priority_type) {

    var link = url + '?days_cnt=' + days_cnt +'&priority_type=' + priority_type;

    onmouseover = window.open(link, 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    event.preventDefault();
}

// function new_external_window_print2(event, url, department_name) {

//     var link = url + '?department_name=' + department_name;

//     onmouseover = window.open(link, 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
//     event.preventDefault();
// }
function new_external_window_print2(event, url, department_name,priority_type) {

    var link = url + '?department_name=' + department_name  +'&priority_type=' + priority_type;

    onmouseover = window.open(link, 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    event.preventDefault();
}

    