<!-- Modal -->
<div class="modal fade" id="edit_customer_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="menu_label">Update Customer</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="updated_customer_form" enctype="multipart/form-data">
                    @csrf

                    <h2>Add Customer Details</h2>
                    <input type="hidden" name="customer_id" id="customer_id">
                    <!-- First Name -->
                    <div class="mb-3">
                        <label for="f_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="f_name" name="f_name" required>
                        <span id="error-f_name" class="text-danger"></span>
                    </div>

                    <!-- Middle Name -->
                    <div class="mb-3">
                        <label for="m_name" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="m_name" name="m_name">
                        <span id="error-m_name" class="text-danger"></span>

                    </div>

                    <!-- Last Name -->
                    <div class="mb-3">
                        <label for="l_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="l_name" name="l_name" required>
                        <span id="error-l_name" class="text-danger"></span>

                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" readonly class="form-control" id="updated_email" name="updated_email" required>
                        <span id="error-updated_email" class="text-danger"></span>

                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <label for="updated_phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="updated_phone" name="updated_phone" required>
                        <span id="error-updated_phone" class="text-danger"></span>

                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label for="updated_address" class="form-label">Address</label>
                        <textarea class="form-control" id="updated_address" name="updated_address" rows="2" required></textarea>
                        <span id="error-updated_address" class="text-danger"></span>

                    </div>

                    <!-- Date of Birth -->
                    <div class="mb-3">
                        <label for="updated_dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="updated_dob" name="updated_dob" required>
                        <span id="error-updated_dob" class="text-danger"></span>

                    </div>

                    <!-- Gender -->
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <div class="d-flex">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="updated_gender" id="male" value="male" required>
                                <label class="form-check-label" for="male">Male</label>
                            </div>
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="updated_gender" id="female" value="female">
                                <label class="form-check-label" for="female">Female</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="updated_gender" id="other" value="other">
                                <label class="form-check-label" for="other">Other</label>
                            </div>
                        </div>
                        <span id="error-updated_gender" class="text-danger"></span>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="update_customer_btn" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</div>
