<?php if (isset($_GET['university_id'])) { ?>
  <!-- Modal Header -->
  <div class="modal-header clearfix text-left m-0 p-0">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <h6 class="fs-4 text-black fw-bold">
      Add <span class="semi-bold">Fee Head</span>
    </h6>
  </div>

  <!-- Form -->
  <form class="card-body"
        role="form"
        id="form-add-fee-heads"
        action="/app/components/fee-heads/store"
        method="POST">

    <div class="row mb-4">
      <div class="col-sm-12">
        <label class="col-form-label">
          Fee Head Name <span class="text-danger">*</span>
        </label>
        <input type="text"
               name="name"
               class="form-control"
               placeholder="ex: Migration / Transcript"
               required>
      </div>
    </div>

    <div class="row justify-content-end">
      <div class="col-sm-9">
        <button type="submit" class="btn btn-primary me-4">Add</button>
        <button type="button"
                data-bs-dismiss="modal"
                class="btn btn-outline-secondary">Cancel</button>
      </div>
    </div>

  </form>

  <!-- Validation + AJAX -->
  <script>
    $(function () {
      $('#form-add-fee-heads').validate({
        rules: {
          name: { required: true }
        },
        highlight: function (element) {
          $(element).addClass('error');
        },
        unhighlight: function (element) {
          $(element).removeClass('error');
        }
      });
    });

    $('#form-add-fee-heads').on('submit', function (e) {
      e.preventDefault();

      if (!$(this).valid()) return;

      $(':input[type="submit"]').prop('disabled', true);

      var formData = new FormData(this);
      formData.append('university_id', '<?= $_GET['university_id'] ?>');

      $.ajax({
        url: this.action,
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            toastr.success(data.message);
            $('#tableFeeHeadsDatatable').DataTable().ajax.reload(null, false);
            localStorage.clear();
          } else {
            $(':input[type="submit"]').prop('disabled', false);
            toastr.error(data.message);
          }
        }
      });
    });
  </script>
<?php } ?>
