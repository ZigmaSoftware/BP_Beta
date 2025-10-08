// Select2 Init

$(document).ready(function(){
  // Select 2 Global Initialiation
  $(".select2").select2({
      theme: 'bootstrap4',
  });


  // // DropZone Default Options
  // Dropzone.options.testFile = {
  //     url : "",
  //     maxFilesize: 50, // MB
  //     accept: ""
  // };

  $('.dropify').dropify({
      allowedFileExtensions : "pdf xlsx png jpeg jpg",
      maxFileSize           : "5M",
      errorsPosition        : "outside",
      error: {
          'fileSize': 'The file size is too big ({{ value }} max).',
          'minWidth': 'The image width is too small ({{ value }}}px min).',
          'maxWidth': 'The image width is too big ({{ value }}}px max).',
          'minHeight': 'The image height is too small ({{ value }}}px min).',
          'maxHeight': 'The image height is too big ({{ value }}px max).',
          'imageFormat': 'The image format is not allowed ({{ value }} only).'
      }
  }); 

  // Datatables Bottom Button init
      var a = $("#datatable-buttons").DataTable({
          lengthChange: !1,
          buttons: [{
              extend: "copy",
              className: "btn-light"
          }, {
              extend: "print",
              className: "btn-light"
          }, {
              extend: "pdf",
              className: "btn-light"
          }],
          language: {
              paginate: {
                  previous: "<i class='mdi mdi-chevron-left'>",
                  next: "<i class='mdi mdi-chevron-right'>"
              }
          },
          drawCallback: function () {
              $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
          }
      });

});

$.extend( $.fn.dataTable.defaults, {
  // searching: false,
  destroy	    : true,
  stateSave	: true,
  ordering    : false,
  responsive  : true,
  paging	    : true,
  processing  : true,
  serverSide  : true,
  searching   : false,
  "columnDefs": [
      {"className": "text-center", "targets": [0, -1]}
  ],
  lengthMenu	: [
      [10,25,50,-1],
      [10,25,50,"All"]
  ],
  language    : {
      paginate    : {
          previous    : "<i class='mdi mdi-chevron-left'>",
          next        : "<i class='mdi mdi-chevron-right'>"
      },
      processing  : '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
  },
  // responsive: {
  //   details: {
  //       display: $.fn.dataTable.Responsive.display.modal( {
  //           header: function ( row ) {
  //               var data = row.data();
  //               return '<h3 class="text-primary">Details for  '+data[1] + "</h3>";
  //           }
  //       } ),
  //       renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
  //           tableClass: 'table'
  //       } )
  //   }
  // },
  drawCallback: function () {
      $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
  }
} );

// Form Validity Checking Function
function form_validity_check(class_name = '',form_name = '') {

  var forms         = document.getElementsByClassName(class_name);

  // If ID based Form validation Needs Not Working
  if (form_name) {
      var forms         = document.getElementsByName(form_name);
  }

  console.log(forms);

  var formValidity  = false;
  var validation    = Array.prototype.filter.call(forms, function (form) {

    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
      formValidity = false;
    } else {
      formValidity = true;
    }
  });
  if (formValidity) {
    return true;
  } else {
    return false;
  }
}

// Form Validity Checking Function Ends

// Sweet Alert Function Starts

function sweetalert(msg='',url='',callback ='',title='') {

  switch (msg) {
    case "create":
      Swal.fire({
          // icon: 'success',
          title: 'Successfully Saved',
          // text: 'Modal with a custom image.',  
          imageUrl:'img/emoji/success.webp',
          // imageWidth: 250,
          // imageHeight: 200,
          imageAlt: 'Custom image',
          showConfirmButton: true,
          timer: 2500,
          timerProgressBar: true,
          willClose: () => {
              if (url) {
                  window.location = url;
              }
          }
      });
    break;

    case "update":
      Swal.fire({
          // icon: 'success',
          title: 'Successfully Updated',
          imageUrl:'img/emoji/clapping.webp',
          showConfirmButton: true,
          timer: 2000,
          timerProgressBar: true,
          willClose: () => {
              if (url) {
                  window.location = url;
              }
          }
      });
    break;

    case "error":
      Swal.fire({
          icon: 'error',
          title: 'Error Occured',
          showConfirmButton: true,
          timer: 2000,
          timerProgressBar: true,
          willClose: () => {
            // alert("Hi");
          }
      });
    break;

    case "network_err":
      Swal.fire({
          icon: 'error',
          title: 'Network Error Occured',
          showConfirmButton: true,
          timer: 2000,
          timerProgressBar: true,
          willClose: () => {
            // alert("Hi");
          }
      });
    break;

    

    case "already":
      Swal.fire({
          // icon: 'warning',
          title: 'Already Exist',
          imageUrl:'img/emoji/already.webp',
          showConfirmButton: true,
          timer: 2000,
          timerProgressBar: true,
          willClose: () => {
            // alert("Hi");
          }
      });
    break;

    case "no_internet":
      Swal.fire({
          icon: 'warning',
          title: 'Please Check Your Internet Connection!',
          showConfirmButton: true,
          timer: 2000,
          timerProgressBar: true,
          willClose: () => {
            // alert("Hi");
          }
      });
    break;

     case "no_location":
      Swal.fire({
          icon: 'warning',
          title: 'Please Check Your Geo Location!',
          showConfirmButton: true,
          timer: 2000,
          timerProgressBar: true,
          willClose: () => {
            // alert("Hi");
          }
      });
    break;

    case "delete":
      return Swal.fire({
        title: 'Are you sure to Delete?',
        text: "You won't be able to revert this!",
        // icon: 'warning',
        imageUrl:'img/emoji/delete.webp',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        preConfirm: () => {
          return true;
        }
      });
    break;

    case "success_delete":
      Swal.fire({
          // icon: 'success',
          title: 'Deleted!',
          imageUrl:'img/emoji/success_delete.webp',
          showConfirmButton: true,
          timer: 1500,
          timerProgressBar: true
      });
    break;

    case "form_alert":
      Swal.fire({
        // icon: 'info',
        title: 'Fill Out All Mantantory Fields',
        imageUrl:'img/emoji/form_fill.webp',
        showConfirmButton: true,
        timer: 2000,
        timerProgressBar: true
      })
    break;
    
    case "approve":
      Swal.fire({
          icon: 'success',
          title: 'Successfully Approved',
          showConfirmButton: true,
          timer: 2000,
          willClose: () => {
            window.location = url;
          }
      });
    break; 

    case "convert":
      Swal.fire({
          icon: 'success',
          title: 'Successfully Converted',
          showConfirmButton: true,
          timer: 2000,
          willClose: () => {
            window.location = url;
          }
      });
    break; 
    
    case "add":
      Swal.fire({
          //icon: 'success',
          title: 'Successfully Added',
    imageUrl:'img/emoji/success_delete.webp',
          showConfirmButton: true,
          timer: 2000,
          willClose: () => {
          //   window.location = url;
          }
      });
    break;

    case "custom":
      Swal.fire({
          icon: 'info',
          title: title,
          willClose: () => {

            if (url != "") {
              window.location = url;
            }
          }
      });
    break;
  case "password_alert":
      Swal.fire({
        // icon: 'info',
        title: 'Please Update either Password Or Profile Image',
        imageUrl:'img/emoji/form_fill.webp',
        showConfirmButton: true,
        timer: 2000,
        timerProgressBar: true
      })
    break;
  }
}

//   Sweet Alert Delete Confirmation Function
confirm_delete = function(msg) {
return sweetalert(msg); // <--- return the swal call which returns a promise
};

//   Sweet Alert Function Ends

// Online Offline Check Function

function is_online() {
  return true;
  // return(navigator.onLine);
  return false;
}

// GEO Location Check Function

function geo_location() {
  if (!navigator.geolocation) {
     sweetalert("no_location");
      return false;
  }
  return true;
 
}

function is_online1() {
  if (!navigator.onLine) {
      sweetalert("no_internet");
      return false;
  }
}

function form_reset(form_class = "",form_name = "") {
  $('.'+form_class).find('input').val('');
  $('.'+form_class).find('select').val('');
  $('.'+form_class).find('.select2').val(null).trigger('change');
  $('.'+form_class).find('textarea').val('');
  // $("#mySelect option[value='']").attr('selected', true)
}

// From date must be lower than to date
function fromToDateValidity (fromDate = "", toDate = "") {
fromDate  	= new Date(fromDate);
toDate  	= new Date(toDate);

// To calculate the time difference of two dates 
var Difference_In_Time = fromDate.getTime() - toDate.getTime(); 

// To calculate the no. of days between two dates 
var Difference_In_Days = Math.ceil(Difference_In_Time / (1000 * 3600 * 24));

// alert(Difference_In_Days);
if (Difference_In_Days > 0) {
  sweetalert("custom","","","From Date Must be Equal or Lower than To Date");
  return false;
  }
  
  return true;
}


// function Indian Money Format

function indianMoneyFormat(value = "") {

value = Number(value);

if (isNaN(value)) {
  value = 0;
}

return (value).toFixed(2).replace(/(\d)(?=(\d{2})+\d\.)/g, '$1,');

}

function number_only(event) {

 if((event.keyCode < 46)||(event.keyCode > 57)) event.returnValue = false;

}

function same_item_repeat_validation(input_name = "",input_id = "",input_value = ""){

  var input_name = document.getElementsByName(input_name); 
  
  input_name.forEach(insert_arr);

  function insert_arr(data) {

    if((data.value == input_value) && (data.id!= input_id)) {

      sweetalert("custom","","","Already  Exist in List");
      $("#"+input_id).val(null).trigger("change");
      return false;      
    }




  }


}

// Geolocation Enable check
function getLocation() {
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(showPosition);
} else {
  document.getElementById('show_locaiton').innerHTML = "Geolocation is not supported by this browser.";
}
}

function showPosition(position) {

document.getElementById('latitude').value  = position.coords.latitude;
document.getElementById('longitude').value = position.coords.longitude;

}