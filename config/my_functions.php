<?php

function sql_generator($type='',array $details=[],$condition='') {
	$result 	= [];
	$columns 	= [];
	$values 	= [];
	$sql 		= '';

	if (($type) && ($details)) {
		$columns 	= array_keys($details);
		$values 	= array_values($details);
		switch ($type) {
			case 'insert':
				$columns = implode($columns, ",");
				$values  = "'".implode($values, "','")."'";
				$sql 	 = " ( ".$columns." ) VALUES ( ".$values." ) ";
				break;

			case 'update':
				# code...
				break;

			case 'value':
				# code...
				break;
			
			default:
				# code...
				break;
		}
	}
}
// Acc Year Function
function acc_year() {
	$acc_year 	= '';
	$curr_year 	= date("Y");
	
	$today 		= strtotime(date("d-m-Y"));	
	$end_date 	= strtotime("31-03-".$curr_year);
	$start_date = strtotime("01-04-".$curr_year);
	
	if ($today>=$start_date) {
		$next_year 		= $curr_year + 1;
		$acc_year  		= $curr_year."-".$next_year;
	}	
	else if ($today<=$end_date) {
		$previous_year 	= $curr_year - 1;
		$acc_year  		= $previous_year."-".$curr_year; 
	}

	return $acc_year;
}


// Any number to 2 decimal number converter
function number2decimal($number='') {
	$result = number_format($number, 2, '.', '');
	return $result;
}

// Any number to 2 decimal number converter
function number3decimal($number='') {
	$result = number_format($number, 3, '.', '');
	return $result;
}


// Indian Money format

?>
<script>
// JS Bootstarp Form Validation Function
function from_validity_check(class_name='') {
	
	var forms = document.getElementsByClassName('was-validated');
	var formValidity = false;
	var validation = Array.prototype.filter.call(forms, function (form) {

		if (form.checkValidity() === false) {
			event.preventDefault();
			event.stopPropagation();
			formValidity = false;
		} else {
			formValidity = true;
		}
	});
	if (formValidity) {} else {
		return false;
	}
}

// Select2 Value Select and empty Functions

// Select function
	// Single Select
	$('#mySelect2').val('1'); // Select the option with a value of '1'
	$('#mySelect2').trigger('change');
	// Multiple Select
	$('#mySelect2').val(['1','2']); // Select the option with a value of '1'
	$('#mySelect2').trigger('change');

// Empty function
$('#mySelect2').val(null).trigger('change');


// Datatables On Event Listner
var table = $('#example').DataTable( {
    ajax: "/data.json"
} );
 
table.on( 'xhr', function ( e, settings, json ) {
    console.log( 'Ajax event occurred. Returned data: ', json );
} );

// Datatable Footer Callbace at Ajax sourced Data
"footerCallback": function ( row, data, start, end, display ) {
	// alert(data);
	var api = this.api(), data;

	// Remove the formatting to get integer data for summation
	var intVal = function ( i ) {
	    return typeof i === 'string' ? i.replace(/[^\d+.]/g, '')*1 : typeof i === 'number' ? i : 0;
	};

	// // Total over all pages Local Supplied data only
	// total = api
	//     .column( 4 )
	//     .data()
	//     .reduce( function (a, b) {
	//         return intVal(a) + intVal(b);
	//     }, 0 );

	// Total over this page
	open_amt = api
	    .column( 3, { page: 'current'} )
	    .data()
	    .reduce( function (a,b) {
	        var b = intVal(b);
	        return intVal(a) + b;
	    }, 0 );



	sale_amt = api
	    .column( 4, { page: 'current'} )
	    .data()
	    .reduce( function (a,b) {
	        var b = intVal(b);
	        return intVal(a) + b;
	    }, 0 );

	receipt_amt = api
	    .column( 5, { page: 'current'} )
	    .data()
	    .reduce( function (a,b) {
	        var b = intVal(b);
	        return intVal(a) + b;
	    }, 0 );

	sale_return = api
	    .column( 6, { page: 'current'} )
	    .data()
	    .reduce( function (a,b) {
	        var b = intVal(b);
	        return intVal(a) + b;
	    }, 0 );

	// Update footer
	$( api.column( 3 ).footer() ).html(open_amt.toFixed(2));
	$( api.column( 4 ).footer() ).html(sale_amt.toFixed(2));
	$( api.column( 5 ).footer() ).html(receipt_amt.toFixed(2));
	$( api.column( 6 ).footer() ).html(sale_return.toFixed(2));
}


$('#reservation').DataTable({
   dom: 'Bfrtip',
   buttons: [
      {
         extend: 'excel',
         text: 'Export Search Results',
         className: 'btn btn-default',
         exportOptions: {
            columns: 'th:not(:last-child)'
         }
      }
   ]
});

"columnDefs": [
    { className: "text-center", "targets": [ 3 ] },
    { className: "text-center", "targets": [ 2 ] }
],

$('#example').dataTable( {
  "columnDefs": [
    { "width": "20%", "targets": 0 }
  ]
} );

$('#example').dataTable( {
  "columns": [
    { "width": "20%" },
    null,
    null,
    null,
    null
  ]
} );

$('#myTable').DataTable( {
    responsive: true
} );

"columnDefs": [
    { className: "text-center", "targets": [ 3 ] },
    { className: "text-center", "targets": [ 2 ] }
  ],


$(window).scroll(function() {
    if($(window).scrollTop() == $(document).height() - $(window).height()) {
           // ajax call get data from server and append to the div
    }
});


// Date Reverse Function
var date = "2016-10-15";
date = date.split("-").reverse().join("-");
console.log(date);


$(document).ready(function() {
    var t = $('#example').DataTable( {
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 1, 'asc' ]]
    } );
 
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
} );

$('#'+table_id+' thead tr').clone(true).appendTo( '#'+table_id+' thead' );
$('#'+table_id+' thead tr:eq(1) th').each( function (i) {
    var title = $(this).text();
    $(this).html( '<input type="text" placeholder="Search '+title+'" />' );

    $( 'input', this ).on( 'keyup change', function () {
        if ( datatable.column(i).search() !== this.value ) {
            datatable
                .column(i)
                .search( this.value )
                .draw();
        }
        init_datatable(table_id,form_name,action);
    } );
} );
</script>
<style>
<!-- Select2 Custom select css styles -->
.was-validated .custom-select:invalid + .select2 .select2-selection{
    border-color: #dc3545!important;
}
.was-validated .custom-select:valid + .select2 .select2-selection{
    border-color: #28a745!important;
}
*:focus{
  outline:0px;
}
</style>
<?php 
function moneyFormatIndia($num) {
    $explrestunits = "";
    $amount 	= explode('.', $num);
    $num 		= $amount[0];
    $decimal 	= 0;
    if (count($amount)==2) {
    	$decimal = $amount[1];
    }
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash.".".$decimal; // writes the final format where $currency is the currency symbol.
}

?>

<script>
Array.prototype.unique = function() {
  return this.filter(function (value, index, self) { 
    return self.indexOf(value) === index;
  });
}

//select data in option (select2)
var tax_value      = $("#tax"+count).select2('data')[0]['tax_value'];

</script>