<?php if (isset($_GET['id'])) {
  require '../../../includes/db-config.php';
  $id = intval(value: $_GET['id']);
  $get_bank_data = $conn->prepare("SELECT * FROM Bank_Accounts WHERE ID = ?");
  $get_bank_data->bind_param('i', $id);
  $get_bank_data->execute();
  $data = $get_bank_data->get_result();
  if ($data->num_rows == 0) {
    echo json_encode(['status' => 400, 'message' => 'No Record found!']);
    exit();
  }
  $row = $data->fetch_assoc();
  ?>
  <!-- Modal -->
  <div class="modal-header clearfix text-left m-0 p-0">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <h6 class="fs-4 text-black fw-bold">Edit <span class="semi-bold">Bank Details</span></h6>
  </div>

  <form class="card-body" role="form" id="form-edit-bank-details" action="/app/components/bank-details/update" method="POST">
    <div class="row mb-4">
      <div class="col-md-6">
        <label class="col-form-label" for="account_holder_name">Account Holder Name <span
            class="text-danger">*</span></label>
        <input type="text" id="account_holder_name" value="<?= $row['Account_Holder_Name']?>" name="account_holder_name" class="form-control"
          placeholder="e.g., John Doe" required>
      </div>
      <div class="col-md-6">
        <label class="col-form-label" for="account_no">Account Number <span class="text-danger">*</span></label>
        <input type="text" id="account_no" name="account_no" value="<?= $row['Account_No']?>" class="form-control" placeholder="e.g., 1234567890" required>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <label class="col-form-label" for="bank_name">Bank Name <span class="text-danger">*</span></label>
        <input type="text" id="bank_name" name="bank_name" value="<?= $row['Bank_Name']?>" class="form-control" placeholder="e.g., State Bank of India"
          required>
      </div>
      <div class="col-md-6">
        <label class="col-form-label" for="branch_name">Branch Name <span class="text-danger">*</span></label>
        <input type="text" id="branch_name" name="branch_name" value="<?= $row['Branch_Name']?>" class="form-control" placeholder="e.g., Mumbai Main Branch"
          required>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <label class="col-form-label" for="ifsc_code">IFSC Code <span class="text-danger">*</span></label>
        <input type="text" id="ifsc_code" name="ifsc_code" value="<?= $row['IFSC_Code']?>" class="form-control" placeholder="e.g., SBIN0001234" required>
      </div>

      <div class="col-md-6">
        <label class="col-form-label" for="account_type">Account Type <span class="text-danger">*</span></label>
        <select id="account_type" name="account_type" class="form-control" required>
          <option value="">Select Account Type</option>
          <option value="savings" <?= ($row['Account_Type'] =="savings")? 'selected' : "" ?> >Savings</option>
          <option value="current" <?= ($row['Account_Type'] =="current")? 'selected' : "" ?>>Current</option>
        </select>
      </div>
    </div>

    <div class="row justify-content-end">
      <div class="col-sm-9">
        <button type="submit" class="btn btn-primary me-4">UPDATE</button>
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-danger">Cancel</button>
      </div>
    </div>
  </form>
  </form>
  <script>
    $(function () {
      $('#form-edit-bank-details').validate({
        rules: {
          account_holder_name: { required: true },
          account_no: { required: true, digits: true, minlength: 8 },
          bank_name: { required: true },
          branch_name: { required: true },
          ifsc_code: { required: true, minlength: 11, maxlength: 11 },
          account_type: { required: true },
        },
        messages: {
          account_holder_name: { required: "Please enter the account holder's name." },
          account_no: { required: "Account number is required.", digits: "Enter only numbers.", minlength: "Minimum 8 digits required." },
          bank_name: { required: "Bank name is required." },
          branch_name: { required: "Branch name is required." },
          ifsc_code: { required: "IFSC code is required.", minlength: "Must be exactly 11 characters.", maxlength: "Must be exactly 11 characters." },
          account_type: { required: "Please select an account type."},
        },
        highlight: function (element) {
          $(element).addClass('is-invalid').closest('.col-md-6').addClass('has-error');
        },
        unhighlight: function (element) {
          $(element).removeClass('is-invalid').closest('.col-md-6').removeClass('has-error');
        },
        errorPlacement: function (error, element) {
          error.addClass('text-danger').appendTo(element.closest('.col-md-6'));
        }
      });
    })

    $("#form-edit-bank-details").on("submit", function (e) {
      if ($('#form-edit-bank-details').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');
        formData.append('university_id', '<?=$row['University_ID']?>');
        $.ajax({
          url: this.action,
          type: 'post',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "json",
          success: function (data) {
            if (data.status == 200) {
              $('.modal').modal('hide');
              toastr.success(data.message);
              $('#tableBankDetailsDatatable').DataTable().ajax.reload(null, false);
            } else {
              $(':input[type="submit"]').prop('disabled', false);
              toastr.error(data.message);
            }
          }
        });
        e.preventDefault();
      }
    });
  </script>
<?php } else
  echo json_encode(['status' => 400, 'message' => 'No Record found!']);
exit();
?>