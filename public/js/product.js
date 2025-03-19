$(document).ready(function(){
    console.log('products js');
    $('#save_product_btn').html('Save');
    
    $('#pre_order_checkbox').on('change', function(){ 

        // products_datatable();
        pre_order_checkbox();
        $('.order-item').remove();
        $('#grand_total').text('₹ 00.00');
    });

    products_datatable();

    $('#open_add_product_modal').on('click',function(){
        open_add_product_modal();
    });
   
    $('#save_product_btn').on('click',function(){
        save_product();
    });
    $('#product_status').on('change',function(){
        products_datatable();
    });

    $('#categories').on('change',function(){
        products_datatable();
    });

   
    document.getElementById('image_url').addEventListener('change', function(event) {
        var file = event.target.files[0]; 
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var preview = document.getElementById('imagePreview');
                preview.src = e.target.result; 
                preview.style.display = 'block'; 
            };
            reader.readAsDataURL(file); 
        }
    });

    // console.log("Edit button found:", $(".edit_btn").length);
    // console.log("Delete button found:", $(".delete_btn").length);

});

function pre_order_checkbox(){

    if($('#pre_order_checkbox').is(':checked')) {
            var pre_order_check = 1;
            // alert('checked');
    }else{
    var pre_order_check = 0;
    //    alert('not checked');
    }
    $.ajax({
        data : {'pre_order_check':pre_order_check},
        type : 'POST',
        datatype : 'JSON',
        url : base_url + '/pre_order_checkbox',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){
            if (response.status) {
                products_datatable();
            }else{
                products_datatable();

            }
        },
    });
}




function products_datatable()
{
    if($('#pre_order_checkbox').is(':checked')) {
                var pre_order_check = 1;
                // alert('checked');
        }else{
           var pre_order_check = 0;
        //    alert('not checked');
        }

    var currentUrl = window.location.href;
    // console.log('currentUrl:', currentUrl);
    // console.log("Edit button found:", $(".edit_btn").length);
    // console.log("Delete button found:", $(".delete_btn").length);
    if (currentUrl.includes("create_orders")) {
       var path = currentUrl;
    }
    var category_id = $('#categories').val();
    var product_status_id = $('#product_status').val();
    
    // console.log(product_status_id);
    if ($.fn.DataTable.isDataTable('#products_datatable')){ 
        $('#products_datatable').DataTable().destroy(); 
    }

    var columns = [
        {"data" : "id"},
        {"data" : "product_name"},
        {"data" : "image_url"},
        {"data" : "category_name"},
        {"data" : "total_quantity"},
    ];
    if (!path) { 
        columns.push({"data": "sold_quantity"});
    }
    
    columns = columns.concat([
        {"data": "price"},
        
    ]);
    if (!path) { 
        columns.push({"data": "product_status_id"});
    }
    columns = columns.concat([
        {"data": "action"}
    ]);

    $('#products_datatable').DataTable({
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
            "data": {'product_status_id':product_status_id, 'category_id':category_id,'path':path,'pre_order_check':pre_order_check},
            "url" : base_url + '/products_datatable',
            "type" : "POST",
            "dataType" : "JSON",
            "headers" : {'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')},
        },
        "columns" : columns
        // [
        //     {"data" : "id"},
        //     {"data" : "product_name"},
        //     {"data" : "image_url"},
        //     {"data" : "category_name"},
        //     {"data" : "total_quantity"},
        //     // {"data" : "sold_quantity"},
        //     {"data" : "price"},
        //     // {"data" : "product_status_id"},
        //     {"data" : "action"},
        // ]
    });
}

function save_product(){
    $('#save_product_btn').html('loading...');
    var input_id = ['product_name','description','total_quantity','price','product_status_id','category_id','image_url'];
    remove_php_error(input_id);
    var form_data = new FormData($('#add_product_form')[0]);
    console.log('form_data',form_data);
    
    $.ajax({
        data : form_data,
        type : 'POST',
        contentType: false,
        processData : false,
        datatype : 'JSON',
        url : base_url + '/add_product',
        headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){
            if (response.status) {
                
            $('#add_product_modal').modal('hide');
            $('#save_product_btn').html('Save');
            toastr.options = {
                "positionClass": "toast-center",
                "timeOut": "3000",
                "extendedTimeOut": "1000",
                "closeButton": true,
                "progressBar": true
            };
            toastr.success(response.message);
                products_datatable();
            }else{
                $('#save_product_btn').html('Save');

                toastr.error(response.message);

            }
        },
        error: function(xhr,status,error){
            console.log('xhr',xhr.responseJSON.errors);
            console.log('status',status);
            console.log('error',error);

            $('#save_product_btn').html('Save');

            var response_message = xhr.responseJSON.errors;
            display_php_error(response_message);
        }
    });
}

function display_php_error(response_message){
    $.each(response_message,function(input_id,response_message){
        $('#error-'+input_id ).html(response_message);
        $(input_id ).focus();
    });
}

function remove_php_error(input_array){
    $.each(input_array,function(key,input_id){
        $('#error-'+input_id ).html('');
    });
}

function open_add_product_modal(){

    resetPreview();
    $('#imageUpload').val('');
    $('#image_url').val('');

    var input_id = ['product_name','description','total_quantity','price','product_status_id','category_id','image_url'];
    remove_php_error(input_id);
    $('#menu_label').html('Add Products');
    $('#product_id').val('');
    $('#product_name').val('');
    $('#description').val('');
    $('#total_quantity').val('');
    $('#price').val('');
    // $('#product_status_id').val('');
    
    
    $('#category_id').val('');
    $('#image_url').val('');
    $('#imagePreview').hide('');
    $('#imagePreview').val('');
    $('#div_old_image').hide();

    $('#add_product_modal').modal('show');
}

function resetPreview() {
    $('#imagePreview').attr('src', 'images/categories/default.png').hide(); 
}

function open_edit_product_modal(product_id){
    // console.log('open_edit_product_modal',product_id);
    $('#image_url').val('');
    resetPreview();
    var input_id = ['product_name','description','total_quantity','price','product_status_id','category_id','image_url'];
    remove_php_error(input_id);
    $('#product_id').val(product_id);
    console.log('product_id',product_id);

    if(product_id){
        $.ajax({
            data : {'product_id':product_id},
            type : 'POST',
            datatype : 'JSON',
            url : base_url + '/get_product_detail_by_id',
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
                if (response.status) {
                    var product = response.products_detail;
                    console.log('product.image_url',product.image_url);
                    
                    $('#product_name').val(product.product_name);
                    $('#description').val(product.description);
                    $('#total_quantity').val(product.total_quantity);
                    $('#is_pre_order').val(product.is_pre_order);
                    $('#price').val(product.price);

                    $('#product_status_id').val(product.product_status_id).trigger('change');
                    $('#category_id').val(product.category_id).trigger('change');
                   

                    if (product.image_url) {
                        $('#imagePreview').attr('src', 'images/categories/' + product.image_url).show();
                    } else {
                        $('#imagePreview').attr('src', 'images/categories/default.png').show();
                    }
                    $('#menu_label').html('Update Products');
                    $('#add_product_modal').modal('show');
                }else{
    
                    alert(response.message);
                }
            },
            error: function(xhr,status,error){
                console.log('xhr',xhr.responseJSON.errors);
                console.log('status',status);
                console.log('error',error);
                var response_message = xhr.responseJSON.errors;
                display_php_error(response_message);
            }
        });
    }else{
        alert('Product does not exist');
    }
}   

function delete_product(product_id){
    if (!product_id) {
        alert('Product ID is missing!');
        return;
    }

    if (confirm('Are you sure you want to delete this product?')) {
        $.ajax({
            url: base_url + '/delete_product_by_id',
            type: 'POST',
            data: { 'product_id': product_id }, 
            dataType: 'JSON',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(response) {
                if (response.status) {
                    toastr.options = {
                        "positionClass": "toast-center", 
                        "timeOut": "3000", 
                        "extendedTimeOut": "1000",
                        "closeButton": true,
                        "progressBar": true
                    };
                    
                    toastr.success(response.message);
                    products_datatable();
                } else {
                    toastr.options = {
                        "positionClass": "toast-center",
                        "timeOut": "3000",
                        "extendedTimeOut": "1000",
                        "closeButton": true,
                        "progressBar": true
                    };
                    toastr.success(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Response:', xhr.responseText);
                alert('An error occurred while deleting the product.');
            }
        });
    }
}
