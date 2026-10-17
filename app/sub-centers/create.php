<!-- Modal -->
<?php require '../../includes/db-config.php'; ?>
<div class="modal-header clearfix text-left m-0 p-0">
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

  <h5 class="fs-4 fw-bold text-black">Add <span class="semi-bold"></span>Sub-Center</h5>
</div>
<link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
<link href="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />

<style>
  ::-webkit-scrollbar {
    width: 2px;
  }

  .select2-selection__rendered {
    overflow: auto !important;
    display: flex !important;
    flex-direction: row !important;

  }
</style>

<form class="card-body" role="form" id="form-add-center-master" action="/app/sub-centers/store" method="POST"
  enctype="multipart/form-data">
  <div class="row mb-6 justify-content-center">
    <div class="col-sm-12 mb-3">
      <label class="col-form-label m-0 p-0" for="multicol-full-name"> Center <span class="text-danger">*</span></label>
      <select class="form-select" data-allow-clear="true" id="reporting" name="reporting" required>
        <option value="">Select Center</option>
        <?php
        require '../../includes/db-config.php';
        session_start();
        // print $_SESSION['Role'] != 'Center' ? '<option value="">Choose</option>' : '';

        $center_query = "";
        if ($_SESSION['Role'] == 'University Head') {
          $center_query = " AND Alloted_Center_To_Counsellor.University_ID = " . $_SESSION['university_id'];
        } elseif ($_SESSION['Role'] == 'Center') {
          $center_query = " AND Users.ID = " . $_SESSION['ID'];
        }
        $centers = $conn->query("SELECT Users.ID, CONCAT(Users.Name, ' (', Users.Code, ')') as Name FROM Users LEFT JOIN Alloted_Center_To_Counsellor ON Users.ID = Alloted_Center_To_Counsellor.Code WHERE Role = 'Center' AND Users.CanCreateSubCenter = 1 $center_query GROUP BY Users.ID");
        while ($center = $centers->fetch_assoc()) { ?>
          <option value="<?= $center['ID'] ?>"><?= $center['Name'] ?></option>
        <?php } ?>
      </select>

      </div>
     
      <div class="col-sm-6 mb-3">
        <label class=" col-form-label m-0 p-0" for="multicol-full-name">Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control" placeholder="ex: Jhon Doe" required>
      </div>
  
      
      <div class="col-sm-6 mb-3">
        <label class=" col-form-label m-0 p-0" for="multicol-full-name">Email <span class="text-danger">*</span></label>
        <input type="email" id="email" name="email" class="form-control" placeholder="ex: user@example.com" required>

      </div>
      <div class="col-sm-6 mb-3">
        <label class=" col-form-label m-0 p-0" for="multicol-full-name">Contact <span
            class="text-danger">*</span></label>
        <input type="tel" id="contact" name="contact" class="form-control" placeholder="ex: 9998777655"
          onkeypress="return isNumberKey(event)" required>
      </div>
      
      <div class="col-sm-6 mb-3">
        <label class="col-form-label m-0 p-0" for="multicol-full-name">Photo <span class="text-danger">*</span></label>
        <input class="form-control" type="file" id="photo" multiple="" name="photo"
          accept="image/png, image/jpg, image/jpeg, image/svg" required>
      </div>
    </div>
    <div class="pt-6">
      <div class="row justify-content-end">
        <div class="col-sm-12 text-end">
          <button type="submit" class="btn btn-primary me-4">Add</button>
          <button type="button" data-bs-dismiss="modal" aria-label="Close"
            class="btn btn-outline-secondary">Cancel</button>
        </div>
      </div>
    </div>
</form>
<script>
  $(document).ready(function () {
    $('.select2').select2({
      placeholder: "Select an option",
      dropdownParent: $('#form-add-center-master'),
      width: 'style'
    }).on('select2:select', function () {
      $(this).select2('close');
    });
  });
</script>
<script>
  $(function () {
    $(".university_id").select2({
      placeholder: 'Choose University'
    });
    $(".reporting").select2({
      placeholder: 'Choose Center'
    })

    $('#form-add-center-master').validate({
      rules: {
        university_head: {
          required: true
        },
        user_type: {
          required: true
        },
        university_id: {
          required: true
        },
        name: {
          required: true
        },
        short_name: {
          required: true
        },
        contact_person_name: {
          required: true
        },
        email: {
          required: true
        },
        contact: {
          required: true
        },
        address: {
          required: true
        },
        pincode: {
          required: true
        },
        city: {
          required: true
        },
        district: {
          required: true
        },
        state: {
          required: true
        },
      },
      highlight: function (element) {
        $(element).addClass('error');
        $(element).closest('.form-control').addClass('has-error');
      },
      unhighlight: function (element) {
        $(element).removeClass('error');
        $(element).closest('.form-control').removeClass('has-error');
      }
    });
  })

  function getRegion(pincode) {
    if (pincode.length == 6) {
      $.ajax({
        url: '/app/regions/cities?pincode=' + pincode,
        type: 'GET',
        success: function (data) {
          $('#city').html(data);
        }
      });

      $.ajax({
        url: '/app/regions/districts?pincode=' + pincode,
        type: 'GET',
        success: function (data) {
          $('#district').html(data);
        }
      });

      $.ajax({
        url: '/app/regions/state?pincode=' + pincode,
        type: 'GET',
        success: function (data) {
          $('#state').val(data);
        }
      })
    }
  }

  function checkType(value) {
    $('#center_code_manual').html('');
    if (value == 0) {
      $('#user-type-div').removeClass('col-md-12');
      $('#user-type-div').addClass('col-md-6');
      $('#center_code_manual').addClass('col-md-6');
      $('#center_code_manual').html('<div class="form-group form-group-default required">\
          <label>Code</label>\
          <input type="text" name="center_code" class="form-control" placeholder="ex: 0001" required>\
        </div>');
    } else {
      $('#user-type-div').removeClass('col-md-6');
      $('#user-type-div').addClass('col-md-12');
    }
  }

  $("#form-add-center-master").on("submit", function (e) {
    if ($('#form-add-center-master').valid()) {
      $(':input[type="submit"]').prop('disabled', true);
      var formData = new FormData(this);
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
            $('#DataTables_Table_3').DataTable().ajax.reload(null, false);
          } else {
            $(':input[type="submit"]').prop('disabled', false);
            toastr.error(data.message);
          }
        }
      });
      e.preventDefault();
    }
  });

  function get_university(head_id) {

    $.ajax({
      url: '/app/center-master/get-university',
      type: 'POST',
      dataType: 'text',
      data: {
        'head_id': head_id,
      },
      success: function (data) {
        $('.university_id').html(data);
        <?php if (!empty($user['District'])) { ?>
          $('.university_id').val('<?= $user['District'] ?>');
        <?php } ?>
      }
    });
  }
</script>