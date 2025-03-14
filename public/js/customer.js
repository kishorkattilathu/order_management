$(document).ready(function(){
    console.log('customers');
    customer_table();
    $('#open_customer_modal').on('click',function(){
        $('#add_customer_modal').modal('show');

    });
   
    $('#add_customer_btn').on('click',function(){
        add_customer();

    });
    $('#update_customer_btn').on('click',function(){
        update_customer();

    });

    $('#customers_selection_by_status').on('change',function(){
        customer_table();
    });



});

function update_customer(){
    var input_id = ['f_name','m_name','l_name','updated_email','updated_phone','updated_address','updated_dob','updated_gender'];

    remove_error(input_id);
    var customer_id = $('#customer_id').val();
    console.log('customer_id',customer_id);
    var formdata = $('#updated_customer_form').serialize();
    if(customer_id){
        $.ajax({
            url: base_url +'/update_customer_by_id',
            data : formdata,
            type : 'POST',
            dataType : 'JSON',
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success : function(response){
                if (response.status) {
                    console.log(response);
                $('#edit_customer_modal').modal('hide');

                    customer_table();

                }else{
                    alert(response.message);
                }
            },
            error : function(xhr,status,error){
                    console.log('xhr',xhr);
                    console.log('status',status);
                    console.log('error',error);
                    var response_error = xhr.responseJSON.errors;
                    display_php_error(response_error);
            }
        });
    }else{
        alert('Customer does not exist');
    }
}

function open_edit_customer_modal(customer_id){
    console.log('customer_id',customer_id);
    var input_id = ['f_name','m_name','l_name','updated_email','updated_phone','updated_address','updated_dob','updated_gender'];

    remove_error(input_id);
    
    if(customer_id){
        $.ajax({
            url: base_url +'/get_customer_data_by_id',
            data : {'customer_id':customer_id},
            type : 'POST',
            dataType : 'JSON',
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success : function(response){
                if (response.status) {
                    console.log(response);
                    var data = response.data;
                    $('#customer_id').val(data.id || '');
                    $('#f_name').val(data.first_name || '');
                    $('#m_name').val(data.middle_name || '');
                    $('#l_name').val(data.last_name || '');
                    $('#updated_email').val(data.email || '');
                    $('#updated_phone').val(data.phone || '');
                    $('#updated_address').val(data.address || '');
                    $('#updated_dob').val(data.date_of_birth || '');
                    // $('#updated_gender').val(data.gender || '');
                    $('#updated_account_status').val(data.account_status || '');
                    
                    var gender = data.gender || '';

                    if (gender) {
                        $("input[name='updated_gender'][value='" + gender + "']").prop("checked", true);
                    }

                    

                    $('#edit_customer_modal').modal('show');

                }else{
                    alert(response.message);
                }
            },
            error : function(xhr,status,error){

            }
        });
    }else{
        alert('Menu does not exist');
    }
}

function customer_table(){
    var customers_status = $('#customers_selection_by_status').val();

    if ($.fn.DataTable.isDataTable('#all_customers_table')){ 
        
        $('#all_customers_table').DataTable().destroy(); 
    }
    $('#all_customers_table').DataTable({
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
            "data" : {'customers_status':customers_status},
            "url" : base_url + '/get_all_customers',
            "type" : "POST",
            "dataType" : "JSON",
            "headers" : {'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')},
        },
        "columns" : [
            {"data" : "id"},
            {"data" : "first_name"},
            {"data" : "email"},
            {"data" : "phone"},
            // {"data" : "address"},
            {"data" : "date_of_birth"},
            {"data" : "gender"},
            {"data" : "account_status"},
            {"data" : "action"},
        ]
    });
}

function add_customer(){
    var input_id = ['first_name','middle_name','last_name','email','phone','address','dob','gender'];
    remove_error(input_id);
    var formdata = $('#add_customer_form').serialize();
    console.log('formdata',formdata);
        $.ajax({
            url: base_url +'/add_customers',
            data : formdata,
            type : 'POST',
            dataType : 'JSON',
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success : function(response){
                if (response.status) {
                $('#add_customer_modal').modal('hide');
                toastr.options = {
                    "positionClass": "toast-center",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "closeButton": true,
                    "progressBar": true
                };
                toastr.success(response.message);
                customer_table();

                }else{
                toastr.options = {
                    "positionClass": "toast-center",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "closeButton": true,
                    "progressBar": true
                };
                toastr.error(response.message);
                }
            },
            error : function(xhr,status,error){
                    console.log('xhr',xhr);
                    console.log('status',status);
                    console.log('error',error);
                    var response_error = xhr.responseJSON.errors;
                    display_php_error(response_error);
            }
        });
  

}
function display_php_error(response_message){
    $.each(response_message,function(input_id,response_message){
        $('#error-'+input_id ).html(response_message);
    });
}

function remove_php_error(input_array){
    $.each(input_array,function(key,input_id){
        $('#error-'+input_id ).html('');
    });
}

function remove_error(input_id){
    $.each(input_id,function(key, input_id){
        $('#error-'+input_id ).html('');
    });
}

function delete_customer(customerId) {
    if (!customerId) {
        alert('Customer ID is missing!');
        return;
    }

    if (confirm('Are you sure you want to delete this customer?')) {
        $.ajax({
            url: base_url + '/delete_customer_by_id',
            type: 'POST', 
            data: { 'customer_id': customerId }, 
            dataType: 'JSON',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.status) {
                    toastr.options = {
                        "positionClass": "toast-center", // Custom class for center alignment
                        "timeOut": "3000", // Auto close after 3 seconds
                        "extendedTimeOut": "1000",
                        "closeButton": true,
                        "progressBar": true
                    };
                    toastr.success(response.message);
                    // alert('Customer deleted successfully!');
                    customer_table();
                } else {
                    // alert(response.message || 'Failed to delete customer.');
                    toastr.options = {
                        "positionClass": "toast-center",
                        "timeOut": "3000",
                        "extendedTimeOut": "1000",
                        "closeButton": true,
                        "progressBar": true
                    };
                    toastr.error(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Response:', xhr.responseText);
                alert('An error occurred while deleting the customer.');
            }
        });
    }
}

// function get_customers_by_status(){
//     var customers_status = $('#customers_selection_by_status').val();
//     // console.log(customers_selection_by_status);

//     if ($.fn.DataTable.isDataTable('#all_customers_table')){ 
        
//         $('#all_customers_table').DataTable().destroy(); 
//     }
//     $('#all_customers_table').DataTable({
//         "processing" : true,
//         "serverSide" : true,
//         "ajax" : {
//             "data" : {'customers_status':customers_status},
//             "url" : base_url + '/get_all_customers',
//             "type" : "POST",
//             "dataType" : "JSON",
//             "headers" : {'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')},
//         },
//         "columns" : [
//             {"data" : "id"},
//             {"data" : "first_name"},
//             {"data" : "email"},
//             {"data" : "phone"},
//             // {"data" : "address"},
//             {"data" : "date_of_birth"},
//             {"data" : "gender"},
//             {"data" : "account_status"},
//             {"data" : "action"},
//         ]
//     });

// }

        



