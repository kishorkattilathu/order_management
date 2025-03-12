<!-- Modal -->
<div class="modal fade" id="order_modal" tabindex="-1" aria-labelledby="menu_label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="menu_label">Order Details</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="order_form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="order_no" class="form-label">Order No</label>
                            <input type="text" class="form-control" id="order_no" name="order_no" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" readonly>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price Per Qty.</th>
                                </tr>
                            </thead>
                            <tbody id="product_fields"></tbody>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="total" class="form-label">Grand Total</label>
                            <input type="text" class="form-control" id="total" name="total" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="order_date" class="form-label">Order Date</label>
                            <input type="text" class="form-control" id="order_date" name="order_date" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="order_status" class="form-label">Order Status</label>
                            <select class="form-control" name="order_status" id="order_status">
                                @foreach ($order_status as $status)
                                    <option value="{{$status->id}}">{{$status->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="payment_status" class="form-label">Payment Status</label>
                            <input type="text" class="form-control" id="payment_status" name="payment_status" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="payment_type" class="form-label">Payment Type</label>
                            <input type="text" class="form-control" id="payment_type" name="payment_type" readonly>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="update_order_status_btn" class="btn btn-primary">Update</button>

            </div>
        </div>
    </div>
</div>
