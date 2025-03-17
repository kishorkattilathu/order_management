$(document).ready(function(){

    $('#save_product_btn').html('Save');

    console.log('products js');
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


    $('#image_url').change(function (event) {
        let file = event.target.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').attr('src', e.target.result).show(); 
            };
            reader.readAsDataURL(file);
        }else{
            resetPreview();
        }
        
    });

    // console.log("Edit button found:", $(".edit_btn").length);
    // console.log("Delete button found:", $(".delete_btn").length);

});




function products_datatable()
{
    var currentUrl = window.location.href;
    console.log('currentUrl:', currentUrl);
    console.log("Edit button found:", $(".edit_btn").length);
    console.log("Delete button found:", $(".delete_btn").length);
    // Check if the URL contains 'create_orders'
    if (currentUrl.includes("create_orders")) {
       var path = currentUrl;
    }
    var category_id = $('#categories').val();
    var product_status_id = $('#product_status').val();
    
    console.log(product_status_id);
    if ($.fn.DataTable.isDataTable('#products_datatable')){ 
        $('#products_datatable').DataTable().destroy(); 
    }

    $('#products_datatable').DataTable({
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
            "data": {'product_status_id':product_status_id, 'category_id':category_id,'path':path},
            "url" : base_url + '/products_datatable',
            "type" : "POST",
            "dataType" : "JSON",
            "headers" : {'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')},
        },
        "columns" : [
            {"data" : "id"},
            {"data" : "product_name"},
            {"data" : "image_url"},
            {"data" : "category_name"},
            {"data" : "total_quantity"},
            {"data" : "sold_quantity"},
            {"data" : "price"},
            {"data" : "product_status_id"},
            {"data" : "action"},
        ]
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

    var input_id = ['product_name','description','total_quantity','price','product_status_id','category_id','image_url'];
    remove_php_error(input_id);
    $('#menu_label').html('Add Products');
    $('#product_id').val('');
    $('#product_name').val('');
    $('#description').val('');
    $('#total_quantity').val('');
    $('#price').val('');
    $('#product_status_id').val('');
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
                    $('#div_old_image').show();
                    
                    $('#product_name').val(product.product_name);
                    $('#description').val(product.description);
                    $('#total_quantity').val(product.total_quantity);
                    $('#price').val(product.price);
                    $('#product_status_id').val(product.product_status_id).trigger('change');
                    $('#category_id').val(product.category_id).trigger('change');
                    $('#old_image').attr('src',product.image_url ? 'images/categories/'+product.image_url:'images/categories/default.png');

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
