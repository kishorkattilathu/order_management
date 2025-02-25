$(document).ready(function(){
    console.log('create_order');

    orders_datatable();

    $('#add_order_btn').on('click',function(){
        add_order_list();
    });
    $('#create_order_btn').on('click',function(){
        create_order();
    });
});

function add_order_list(){
    console.log('add_order_list'); 


   var product_id = $('#product_id').val();
//    var customer_id = $('#customer_id').val();
   var product_text = $('#product_id option:selected').text();



   if (customer_id === "" || product_id === "") {
        alert("Please select both a customer and a product.");
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

$(document).on('click', '.remove-product', function() {
    $(this).closest('.order-item').remove();
});

function productQtyChange(product_id){
    console.log('product_id',product_id);

    var product_qty = $('#product_qty-'+product_id).val();
    var product_price = $('#product_price-'+product_id).val();

    var product_total_amount = parseInt(product_qty) * parseFloat(product_price);

    $('#product_amount-'+product_id).html(product_total_amount);

}

function create_order(){
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
               window.location.href = response.redirect_url;

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