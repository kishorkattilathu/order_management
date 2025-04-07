$(document).ready(function(){
    console.log('create_order');
    $('#update_order_status_btn').text('Update');

    orders_datatable();

    $('#create_order_btn').on('click',function(){
        create_order();
    });
    $('#update_order_status_btn').on('click',function(){
        update_order_status();
    });
    var input_array = ['customer_id','product_ids'];
    remove_php_error(input_array);

    $('#customer_id').on('change',function(){
        $('#search').val('');

        var customerId = $(this).val();
        storeCustomerSession(customerId);
        
    });
    
  

});




function storeCustomerSession(customerId){
    console.log('customerId',customerId);

    $.ajax({
        url: base_url +'/storeCustomerSession',
        data : {'customer_id':customerId},
        type : 'POST',
        dataType : 'JSON',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(response){
            if (response.status) {
                toastr.options = {
                    // "positionClass": "toast-center",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "closeButton": true,
                    "progressBar": true
                };
                
                toastr.success(response.message);

            }else{
                toastr.options = {
                    // "positionClass": "toast-center",
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

$(document).on('click', '.add_order_btn', function() {
    var product_id = $(this).data('id');
    
    var product_text = $(this).data('name');
        add_product_list(product_id, product_text);

});


function update_order_status(){
    $('#update_order_status_btn').text('loading...');
    var order_id = $('#order_no').val();
    var order_status_id = $('#order_status').val();
    var formData = $('#order_form').serialize();
    console.log(formData);
    $.ajax({
        url: base_url +'/update_order_status',
        data : {'order_id':order_id, 'order_status_id':order_status_id},
        type : 'POST',
        dataType : 'JSON',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(response){
            if (response.status) {
                toastr.options = {
                    "positionClass": "toast-center",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "closeButton": true,
                    "progressBar": true
                };
                
                toastr.success(response.message);
                $("#order_modal").modal("hide");
                $('#update_order_status_btn').text('Update');

                orders_datatable();

            }else{
                toastr.options = {
                    "positionClass": "toast-center",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "closeButton": true,
                    "progressBar": true
                };
                toastr.error(response.message);
                $('#update_order_status_btn').text('Update');
                $("#order_modal").modal("hide");

            }
        },
        error : function(xhr,status,error){
                console.log('xhr',xhr);
                console.log('status',status);
                console.log('error',error);
                var response_error = xhr.responseJSON.errors;
                display_php_error(response_error);
                $('#update_order_status_btn').text('Update');

        }
    });


}

function open_order_modal(order_id) {

    $.ajax({
        url: base_url + "/get_order_detail_by_id",
        type: "POST",
        data: { order_id: order_id },
        dataType: "JSON",
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        success: function (response) {
            if (response.status) {
                let orderDetails = response.data;
                console.log('response.data',response.data);
                if (orderDetails.length > 0) {
                    let firstOrder = orderDetails[0];

                    var date = firstOrder.order_date;
                    var dateTimeParts = date.split(' ');
                    var order_date = dateTimeParts[0].split('-');
                    var usFormatedDate = order_date[1] + '/' + order_date[2] + '/' + order_date[0];

                    var order_time = dateTimeParts[1];
                    var timeParts = order_time.split(':');
                    var hours = parseInt(timeParts[0],10);
                    var minutes = timeParts[1];
                    // var seconds = timeParts[2];
                    var amPm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12 || 12;
                    var usFormatedTime = hours +':'+ minutes + ' ' + amPm;
                    $("#order_no").val(firstOrder.id || "");
                    $("#order_type").val( firstOrder.order_type == 1 ? 'Regular' : 'Pre-Order' || "");
                    $("#customer_name").val(firstOrder.customer_name || "");
                    $("#total").val(firstOrder.total_amount || "");
                    $("#order_date").val(usFormatedDate || "");
                    $("#order_time").val(usFormatedTime || "");
                    $("#order_status").val(firstOrder.order_status_id || "");
                    $("#payment_status").val(firstOrder.payment_status || "");
                    $("#payment_type").val(firstOrder.payment_method || "");

                    $("#product_fields").empty();

                    orderDetails.forEach(function (item) {
                        console.log('orderDetails',orderDetails);
                        let row = `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.product_quantity}</td>
                            <td>&#8377;${item.product_amount}</td>
                            <td>&#8377;${(item.product_amount * item.product_quantity)}.00</td>
                        </tr>`;
                        $("#product_fields").append(row);
                    });

                    $("#order_fields").empty();
                    let row2 = `
                        <tr>
                            <td> &#8377;${firstOrder.total_amount}  </td>
                            <td>  ${usFormatedDate}  </td>
                            <td>  ${usFormatedTime}  </td>
                            <td>  ${firstOrder.title}  </td>
                            <td>  ${firstOrder.payment_status}  </td>
                            <td>  ${firstOrder.payment_method}  </td>
                        </tr>
                        `
                        $('#order_fields').append(row2);

                    $("#order_modal").modal("show");
                }
            } else {
                alert(response.message);
            }
        },
        error: function (xhr, status, error) {
            console.log("AJAX Error:", error);
        },
    });
}



function add_product_list(product_id, product_text){
    
    $('#grand_total_div').show();
    var customer_id = $('#customer_id').val();

   
    if (customer_id === "" || product_id === "") {
        toastr.options = {
            "positionClass": "toast-center", // Custom class for center alignment
            "timeOut": "3000", // Auto close after 3 seconds
            "extendedTimeOut": "1000",
            "closeButton": true,
            "progressBar": true
        };
        toastr.error("Please select a customer first");
        return;
    }

    $.ajax({
        url: base_url +'/get_product_detail',
        data : {'product_id':product_id},
        type : 'POST',
        dataType : 'JSON',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(response){
            if (response.status) {
                var total_price_sum = response.total_price_sum;
                var product_detail = response.products_detail;
                var stock_quantity = response.stock_quantity;
                
                var newRow = `
                    <div class="row g-3 align-items-end order-item">
                        <div class="col-md-3">
                            <input type="hidden" name="products[][product_id]" class="product-id" value="${product_id}">
                            <input id="product_price-${product_id}" type="hidden" name="product_price[]" value="${product_detail.price}">
                            <p class="">${product_text}</p>
                        </div>
                        <div class="col-md-2">
                        <input id="product_stock-${product_id}" type="text" readonly name="product_stocks[]" value="${stock_quantity !== null ? stock_quantity : product_detail.total_quantity}" class="form-control product_stock">
                           
                        </div>
                        <div class="col-md-2">
                            <input  type="number" onChange="productQtyChange(${product_id})" name="product_qty[]" data-product_id="${product_id}" id="product_qty-${product_id}" value="1" min="1" class="form-control product_qty">

                        </div>

                        <div class="col-md-3">
                            <div id="product_amount-${product_id}">${product_detail.price} </div>

                        </div>

                        <div class="col-md-2 d-grid">
                            <i class="bi bi-trash remove-product text-danger"></i>
                        </div>
                    </div>
                `;

                $('#order_list').append(newRow);
                $('#grand_total').html('&#8377;' + total_price_sum);
                $('#stock_quantity').html( stock_quantity);
                $('#search').val('');
                productQtyChange(product_id);
            }else{

                toastr.options = {
                    "positionClass": "toast-center", // Custom class for center alignment
                    "timeOut": "3000", // Auto close after 3 seconds
                    "extendedTimeOut": "1000",
                    "closeButton": true,
                    "progressBar": true
                };
                toastr.error(response.message);
                $('#search').val('');

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


$(document).on('click', '.remove-product', function () {
    console.log("Remove button clicked!");
    var productElement = $(this).closest('.order-item'); 
    var product_id = productElement.find('.product-id').val();
    console.log(product_id,'product_id');


    $.ajax({
        url : base_url +'/removeProductFromSession',
        type: "POST",
        data: {'product_id': product_id},
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function (response) {
            if (response.success) {
                productElement.remove(); 
                toastr.error(response.message);
                productQtyChange(product_id);

            } else {
                toastr.options = {
                    "positionClass": "toast-center",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "closeButton": true,
                    "progressBar": true
                };
                toastr.error(response.message);
            }
        }
    });
});

function productQtyChange(product_id){
    $('#grand_total_div').show();

    var product_qty = $('#product_qty-'+product_id).val();
    var product_price = $('#product_price-'+product_id).val();
    var product_total_amount = parseInt(product_qty) * parseFloat(product_price);

    $.ajax({
        url: base_url +'/update_quantity_in_session',
        data : {'product_id':product_id, 'product_total_amount':product_total_amount,'product_qty':product_qty},
        type : 'POST',
        dataType : 'JSON',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(response){
            if (response.status) {
               
                var product_data = response.product_data;
                var total_price_sum = response.total_price_sum;
                $('#grand_total').html('&#8377; ' + parseFloat(total_price_sum).toFixed(2));


                console.log(product_data);
                $.each(product_data,function(index, item){
                    console.log(item)
                    $('#product_amount-'+item.product_id).text(item.total_price);
                });

            }else{
                console.log('response',response);
                var product_id = response.product_id;
                var product_qty = response.product_qty;
                console.log('product_id',product_id);
                console.log('product_qty',product_qty);
                $(`#product_qty-${product_id}`).val(product_qty);

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

function create_order(){
    $('#create_order_btn').html('loading...');
    console.log('create_order');
    var create_order_form = $('#create_order_form').serialize();
    
    $.ajax({
        url: base_url +'/create_final_order',
        data : create_order_form,
        type : 'POST',
        dataType : 'JSON',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(response){
            if (response.status) {
                console.log(response.message);
                console.log(response.status);
                toastr.options = {
                    "positionClass": "toast-center",
                    "timeOut": "3000",
                    "extendedTimeOut": "1000",
                    "closeButton": true,
                    "progressBar": true
                };
                toastr.success(response.message);
               $('#create_order_btn').html('Create+');

               window.location.href = response.redirect_url;

            }else{
                console.log(response.message);

               $('#create_order_btn').html('Create+');

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
                $('#create_order_btn').html('Create+');

                console.log('xhr',xhr);
                console.log('status',status);
                console.log('error',error);
                var response_error = xhr.responseJSON.errors;
                display_php_error(response_error);
        }
    });
}

function orders_datatable(){
    if ($.fn.DataTable.isDataTable('#orders_datatable')){ 
        
        $('#orders_datatable').DataTable().destroy(); 
    }
    $('#orders_datatable').DataTable({
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
            "url" : base_url + '/orders_datatable',
            "type" : "POST",
            "dataType" : "JSON",
            "headers" : {'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')},
        },
        "columns" : [
            {"data" : "id"},
            // {"data" : "order_type"},
            {"data" : "customer_name"},
            {"data" : "customer_email"},
            {"data" : "total_quantity"},
            {"data" : "total_amount"},
            {"data" : "order_date"},
            {"data" : "order_time"},
            {"data" : "order_status"},
            {"data" : "action"},
        ],
       
    });
}

function cancel_order(order_id){
    if (confirm('Are you sure you want to cancel this order?')) {
        console.log('cancel_order',order_id);

        $.ajax({
            url: base_url +'/cancel_order',
            data : {'order_id':order_id},
            type : 'POST',
            dataType : 'JSON',
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success : function(response){
                if (response.status) {
                    orders_datatable();
                    toastr.options = {
                        "positionClass": "toast-center", // Custom class for center alignment
                        "timeOut": "3000", // Auto close after 3 seconds
                        "extendedTimeOut": "1000",
                        "closeButton": true,
                        "progressBar": true
                    };
                    toastr.success(response.message);
                }else{
                    toastr.options = {
                        "positionClass": "toast-center", // Custom class for center alignment
                        "timeOut": "3000", // Auto close after 3 seconds
                        "extendedTimeOut": "1000",
                        "closeButton": true,
                        "progressBar": true
                    };
                    toastr.error(response.message);
                    // alert(response.message);
                }
            },
            error : function(xhr,status,error){
                    console.log('xhr',xhr);
                    console.log('status',status);
                    console.log('error',error);
                    var response_error = xhr.responseJSON.errors;
                    display_php_error(response_error);
                    alert(response_error);
                    
            }
        });
    }

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

$(document).ready(function() {
   
    $('#search').on('keyup', function() {
        var query = $(this).val();
        if (query.length > 0) {
            $.ajax({
                url: base_url +'/search',
                type: "GET",
                data: { query: query },
                success: function(data) {
                    let resultHtml = '<div class="result-item add-new form-control p-2" style="max-height: 200px; overflow-y: auto;">Add new +</div>';
                    data.forEach(product => {
                        resultHtml += `<a href="javascript:void(0);" class="add_order_btn" data-name ="${product.product_name}" data-id="${product.id}">
                                <div class="result-item">${product.product_name}</div>
                            </a>`;
                    });
                    $('#result').html(resultHtml);
                }
            });
        } else {
           // $('#result').html(resultHtml);
        }
    });

    $(document).on('click', '.result-item', function() {
        $('#search').val($(this).text());
        $('#result').html('');
    });

    $(document).on('click', '.add-new', function() {
        $('#product_modal').modal('show');
    });
});

