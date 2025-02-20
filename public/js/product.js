$(document).ready(function(){

    console.log('products js');
    products_datatable();

    $('#open_add_product_modal').on('click',function(){
        open_add_product_modal();
    });
   
   
    $('#save_product_btn').on('click',function(){
        save_product();
    });
});


function products_datatable()
{
    if ($.fn.DataTable.isDataTable('#products_datatable')){ 
        $('#products_datatable').DataTable().destroy(); 
    }

    $('#products_datatable').DataTable({
        "processing" : true,
        "serverSide" : true,
        "ajax" : {
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

                products_datatable();
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
    $('#menu_label').html('Add Products');
    $('#product_id').val('');
    $('#product_name').val('');
    $('#description').val('');
    $('#total_quantity').val('');
    $('#price').val('');
    $('#product_status_id').val('');
    $('#category_id').val('');
    $('#image_url').val('');
    $('#div_old_image').hide();

    $('#add_product_modal').modal('show');
}

function open_edit_product_modal(product_id){
    $('#product_id').val(product_id);
    console.log('product_id',product_id);

    if(product_id){
        $.ajax({
            data : {'product_id':product_id},
            type : 'POST',
            datatype : 'JSON',
            url : base_url + '/get_product_detail',
            headers : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
                if (response.status) {
                    var product = response.products_detail;
                    $('#div_old_image').show();
                    
                    $('#product_name').val(product.product_name);
                    $('#description').val(product.description);
                    $('#total_quantity').val(product.total_quantity);
                    $('#price').val(product.price);
                    $('#product_status_id').val(product.product_status_id).trigger('change');
                    $('#category_id').val(product.category_id).trigger('change');
                    $('#old_image').attr('src','images/categories/'+product.image_url);

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
                    alert('Product deleted successfully!');
                    products_datatable();
                } else {
                    alert(response.message || 'Failed to delete product.');
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
