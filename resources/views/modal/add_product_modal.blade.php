<!-- Modal -->
<div class="modal fade" id="add_product_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="menu_label">Add Products</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="add_product_form" enctype="multipart/form-data">
                    @csrf
                
                    <input type="hidden" name="product_id" id="product_id">
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                        <span id="error-product_name" class="text-danger"></span>
                    </div>
                
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                        <span id="error-description" class="text-danger"></span>
                    </div>
                
                    <div class="mb-3">
                        <label for="total_quantity" class="form-label">Total Quantity</label>
                        <input type="number" class="form-control" id="total_quantity" name="total_quantity" required>
                        <span id="error-total_quantity" class="text-danger"></span>
                    </div>
                
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        <span id="error-price" class="text-danger"></span>
                    </div>
                
                    <div class="mb-3">
                        <label for="product_status_id" class="form-label">Product Status</label>
                        <select class="form-control" name="product_status_id" id="product_status_id" required>
                            <option value="">Select Status</option>
                            @foreach ($products_statuses as $product_status)
                                <option value="{{$product_status->id}}">{{$product_status->title}}</option>
                            @endforeach
                        </select>
                    </div>
                
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-control" name="category_id" id="category_id" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                        <span id="error-category_id" class="text-danger"></span>

                    </div>
                    <div id="div_old_image">
                        <img src="" id="old_image" style="width:100px" alt="Old Image">
                    </div>
                
                    <div class="mb-3">
                        <label for="image_url" class="form-label">Upload Image</label>
                        <input type="file" class="form-control" id="image_url" name="image_url" required accept="image/*">
                        <img id="imagePreview" alt="Image Preview" width="150" style="display: none;">
                        <span id="error-image_url" class="text-danger"></span>
                    </div>
                
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="save_product_btn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
