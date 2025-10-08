// lwf_entry.js

const ajax_url = sessionStorage.getItem("folder_crud_link");
alert(ajax_url);

$(document).ready(function () {
    // Initialize DataTable
    $('#lwf_table').DataTable({
        ajax: {
            url: ajax_url,
            type: 'POST',
            data: { action: 'datatable', table: 'lwf_entry' },
            dataSrc: function (json) {
                console.log(json); // 🔍 Check if 'btns' is inside each row
                return json.data;
            }
        },
        columns: [
            { data: 's_no' },
            { data: 'project_name' },
            { data: 'state' },
            { data: 'amount' },
            {
                data: 'btns',
                orderable: false,
                searchable: false
            }
        ]
    });


    // Get State on Project Change
    window.get_state = function (project_id) {
        if (!project_id) return;
        $.ajax({
            url: ajax_url,
            method: 'POST',
            data: {
                action: 'get_state',
                project_id: project_id
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.status && res.data && res.data.state) {
                    $('#state').val(res.data.state);
                } else {
                    $('#state').val('');
                    console.warn('State not found for this project');
                }
            },
            error: function () {
                console.error('Failed to fetch state');
            }
        });
    };

    // Create/Update Form Submit
    $('#grn_new_form').on('submit', function (e) {
        e.preventDefault();
        lwf_entry_cu();
    });

    // Delete handler
    $(document).on('click', '.delete-btn', function () {
        if (!confirm('Are you sure you want to delete this record?')) return;
        const id = $(this).data('id');

        $.ajax({
            url: ajax_url,
            method: 'POST',
            data: {
                action: 'delete',
                table: 'lwf_entry',
                unique_id: id
            },
            success: function (response) {
                let res = JSON.parse(response);
                if (res.status) {
                    alert('Deleted successfully');
                    $('#lwf_table').DataTable().ajax.reload();
                } else {
                    alert('Delete failed: ' + res.error);
                }
            },
            error: function () {
                alert('Server error during delete');
            }
        });
    });

    // Edit handler
    $(document).on('click', '.edit-btn', function () {
        const id = $(this).data('id');
        window.location.href = `form.php?unique_id=${id}`;
    });
});

function lwf_entry_cu() {
    let unique_id = $("#unique_id").val();
    const formData = $('#grn_new_form').serializeArray();
    formData.push(
        { name: 'action', value: 'createupdate' },
        { name: 'unique_id', value: unique_id } // ✅ push unique_id into the payload
    );

    $.ajax({
        url: ajax_url,
        method: 'POST',
        data: formData,
        success: function (response) {
            let res = JSON.parse(response);
            if (res.status) {
                alert('Saved successfully!');
                const listLink = sessionStorage.getItem("list_link");
                if (listLink) {
                    window.location.href = listLink;
                } else {
                    console.warn('No list link found in sessionStorage. Reloading instead.');
                    location.reload();
                }
            } else {
                alert('Save failed: ' + res.error);
            }
        },
        error: function () {
            alert('Server error');
        }
    });
}
