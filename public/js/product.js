$(document).ready(function(){

    console.log('products js');
    products_datatable();

    $('#open_add_product_modal').on('click',function(){
        $('#add_product_modal').modal('show');
    });
   
    $('#add_product_btn').on('click',function(){
        add_product();
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

function add_product(){
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

                location.reload();
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