$(document).ready(function(){
    console.log('create_order');

    orders_datatable();

    $('#add_order_btn').on('click',function(){
        add_order_list();
    });
    $('#create_order_btn').on('click',function(){
        create_order();
    });
    $('#update_order_status_btn').on('click',function(){
        update_order_status();
    });
    var input_array = ['customer_id','product_ids'];
    remove_php_error(input_array);

});

function update_order_status(){
    // console.log('update_order_status');
    var order_id = $('#order_no').val();
    var order_status_id = $('#order_status').val();
    // console.log('order_id',order_id);

    $.ajax({
        url: base_url +'/update_order_status',
        data : {'order_id':order_id, 'order_status_id':order_status_id},
        type : 'POST',
        dataType : 'JSON',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(response){
            if (response.status) {
                console.log(response);
                $("#order_modal").modal("hide");

                orders_datatable();

            }else{
                alert(response.message);
                $("#order_modal").modal("hide");

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

function open_order_modal(order_id) {
    // console.log("Fetching order:", order_id);

    $.ajax({
        url: base_url + "/get_order_detail_by_id",
        type: "POST",
        data: { order_id: order_id },
        dataType: "JSON",
        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
        success: function (response) {
            if (response.status) {
                let orderDetails = response.data;

                if (orderDetails.length > 0) {
                    let firstOrder = orderDetails[0]; 

                    $("#order_no").val(firstOrder.id || "");
                    $("#customer_name").val(firstOrder.customer_name || "");
                    $("#total").val(firstOrder.total_amount || "");
                    $("#order_date").val(firstOrder.order_date || "");
                    $("#order_status").val(firstOrder.order_status_id || "");
                    $("#payment_status").val(firstOrder.payment_status || "");
                    $("#payment_type").val(firstOrder.payment_method || "");

                    $("#product_fields").empty();

                    orderDetails.forEach(function (item) {
                        let row = `
                        <tr>
                            <td><input type="text" class="form-control" value="${item.product_name}" readonly></td>
                            <td><input type="text" class="form-control" value="${item.product_quantity}" readonly></td>
                            <td><input type="text" class="form-control" value="${item.product_amount}" readonly></td>
                        </tr>`;
                        $("#product_fields").append(row);
                    });

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



function add_order_list(){
    $('#grand_total_div').show();

    console.log('add_order_list'); 
    if (customer_id === "" || product_id === "") {
        alert("Please select both a customer and a product.");
        return;
    }

    var product_id = $('#product_id').val();
    var product_text = $('#product_id option:selected').text();

    $.ajax({
        url: base_url +'/get_product_detail',
        data : {'product_id':product_id},
        type : 'POST',
        dataType : 'JSON',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success : function(response){
            if (response.status) {
                // console.log('stored_products',response.stored_products);
                var total_price_sum = response.total_price_sum;
                var product_detail = response.products_detail;
                
                var newRow = `
                    <div class="row g-3 align-items-end order-item">
                        <div class="col-md-3">
                            <input type="hidden" name="product_ids[]" value="${product_id}">
                            <input id="product_price-${product_id}" type="hidden" name="product_price[]" value="${product_detail.price}">
                            <p class="">${product_text}</p>
                        </div>
                        <div class="col-md-3">
                            <input  type="number" onChange="productQtyChange(${product_id})" name="product_qty[]" data-product_id="${product_id}" id="product_qty-${product_id}" value="1" min="1" class="form-control product_qty">

                        </div>

                        <div class="col-md-3">
                            <div id="product_amount-${product_id}"> </div>

                        </div>


                    
                        <div class="col-md-3 d-grid">
                            <button type="button" class="btn btn-danger remove-product">Remove</button>
                        </div>
                    </div>
                `;

                $('#order_list').append(newRow);
                $('#grand_total').html('&#8377;' + total_price_sum);

                productQtyChange(product_id);
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
    

    
}


$(document).on('click', '.remove-product', function () {

    var productElement = $(this).closest('.order-item'); 
    // var product_id = productElement.data('product-id');
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
            } else {
                alert(response.message); 
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
                $('#grand_total').html('&#8377;' + total_price_sum);

                console.log(product_data);
                $.each(product_data,function(index, item){
                    console.log(item)
                    $('#product_amount-'+item.product_id).text(item.total_price);
                });

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
               alert(response.message);
               $('#create_order_btn').html('Create+');

               window.location.href = response.redirect_url;

            }else{
               $('#create_order_btn').html('Create+');

                alert(response.message);
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
            {"data" : "customer_name"},
            {"data" : "customer_email"},
            {"data" : "total_quantity"},
            {"data" : "total_amount"},
            {"data" : "order_date"},
            {"data" : "order_status"},
            {"data" : "action"},
        ]
        // ,
        // "order": [[1, 'asc']], // Order by Name column by default
        // "rowCallback": function (row, data, index) {
        //     $('td:eq(0)', row).html(index + 1);
        // }
    });
}

function cancel_order(order_id){
    // console.log('cancel_order',order_id);
    if (confirm('Are you sure you want to cancel this order?')) {
        $.ajax({
            url: base_url +'/cancel_order',
            data : {'order_id':order_id},
            type : 'POST',
            dataType : 'JSON',
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success : function(response){
                if (response.status) {
                    orders_datatable();
    
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