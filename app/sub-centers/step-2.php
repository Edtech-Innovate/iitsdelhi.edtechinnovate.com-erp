<?php
if (isset($_POST['id'])) {
  require '../../includes/db-config.php';
  require '../../includes/helpers.php';

  session_start();
  $id = intval($_POST['id']);


}
?>

<!-- Modal -->
<div class="modal-header clearfix text-left m-0 p-0">
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  <h5 class="fs-4 fw-bold text-black">Allot Specializations <span class="semi-bold"></span></h5>
</div>

<form class="card-body" role="form" id="form-allot-center-master" action="/app/sub-centers/allot-sub-courses"
  method="POST" enctype="multipart/form-data">
  <div class="row mt-3 mb-4">
    <div class="col-md-6">
      <div class="form-group">
        <dt class="pt-1"><b>Sub Course Name</b></dt>
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group">
        <dt class="pt-1"><b>Session</b></dt>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <input type="checkbox" name="select_all" id="select_all">
        <label for="select_all"><b> All</b></label>
      </div>
    </div>
  </div>
  <?php
  $center_id = getCenterIdFunc($conn, $id);
  $get_sub_course_name = $conn->query("SELECT u.Short_Name as uni_name,Courses.Short_Name as course_name, sc.Name as sub_course_name,csc.*  FROM Center_Sub_Courses AS csc RIGHT JOIN Courses on csc.Course_ID = Courses.ID RIGHT JOIN Sub_Courses as sc on csc.Sub_Course_ID = sc.ID RIGHT JOIN Universities as u on csc.University_ID = u.ID WHERE csc.User_ID = $center_id AND csc.Admission_Sessions_ID !=''");

  ?>
  <div id="subcourse">
    <?php if ($get_sub_course_name->num_rows == 0) {
      echo '<div class="col-md-12 text-center">No Sub Course Assigned</div>';
      return;

    } else {
      while ($row = $get_sub_course_name->fetch_assoc()) {

        $adm_session_ids = [];
        $sub_courses_ids = [];
        $getAllotedData = $conn->query("select Admission_Sessions_ID,Course_ID,Sub_Course_ID from Sub_Center_Sub_Courses where User_ID =$id AND University_ID = " . $row['University_ID'] . " AND Sub_Course_ID = " . $row['Sub_Course_ID'] . "  AND Course_ID = " . $row['Course_ID'] . " AND Duration=" . $row['Duration']);
        if ($getAllotedData->num_rows > 0) {
          $session_arr = $getAllotedData->fetch_assoc();
          $adm_session_ids = explode(',', $session_arr['Admission_Sessions_ID']);
          $sub_courses_ids = $session_arr['Sub_Course_ID'];
        }
        ?>
        <div class="row mb-2">

          <div class="col-md-6">
            <div class="form-group">
              <input type="hidden" name="university_id[<?= $row['Sub_Course_ID']; ?>]"
                value="<?php echo $row['University_ID']; ?>">
              <input type="hidden" name="course_id[]"
                value="<?php echo $row['Course_ID'] . '|' . $row['University_ID']; ?>">
              <label
                for="<?php echo $row['sub_course_name']; ?>"><?php echo ucwords(strtolower($row['sub_course_name'])) . '(' . $row['course_name'] . ')'; ?></label>
            </div>
          </div>
          <div class="col-md-4">
            <select class="adm_session full-width" data-init-plugin="select2"
              name="adm_session[<?= $row['Sub_Course_ID'] ?>][]" multiple>
              <option value="">Select Session</option>
              <?php
              $adm_ids = '';
              if (!empty($row['Admission_Sessions_ID'])) {
                $adm_ids = $row['Admission_Sessions_ID'];
              }
              $get_session = $conn->query("SELECT * FROM Admission_Sessions WHERE ID IN ($adm_ids)");
              while ($session = $get_session->fetch_assoc()) {

                echo $selected = in_array($session['ID'], $adm_session_ids) ? 'selected' : '';

                ?>
                <option value="<?php echo $session['ID']; ?>" <?= $selected ?>><?php echo $session['Name']; ?></option>
              <?php }
              ?>
            </select>

          </div>
          <div class="col-md-1">
            <div class="form-group" style="border:unset">
              <input type="checkbox" value="<?= $row['Course_ID']; ?>" <?= ($row['Sub_Course_ID'] == $sub_courses_ids) ? "checked" : '' ?> name="sub_course[<?= $row['Sub_Course_ID']; ?>]" class="sub_course_checkbox">
            </div>
          </div>
        </div>
      <?php
      } ?>
    </div>
    <div class="pt-6">
      <div class="row">
        <div class="col-sm-12 d-flex justify-content-between">
          <span type="submit" class="mt-2 text-start" onclick="removeAllotment()"><i
              class="ms-3 ri-delete-bin-5-line text-danger" title="Allotment remove"></i></span>
          <div class="justify-content-end">
            <button type="submit" class="btn btn-primary me-4">Submit</button>
            <button type="button" data-bs-dismiss="modal" class="btn btn-outline-secondary">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <script>
    $(document).ready(function () {
      $('.adm_session').select2({
        placeholder: "Select an option",
        dropdownParent: $('#form-allot-center-master'),
        width: 'style'
      });
      $('#select_all').click(function (event) {
        if (this.checked) {
          $('.sub_course_checkbox').each(function () {
            this.checked = true;
          });
        } else {
          $('.sub_course_checkbox').each(function () {
            this.checked = false;
          });
        }
      });
    });

    function removeAllotment() {
      const id = '<?= $id ?>';

      $.ajax({
        url: '/app/sub-centers/remove-allotment',
        type: 'POST',
        data: {
          id: id,
        },
        dataType: 'json',
        success: function (data) {
          if (data.status == 200) {
            $('.modal').modal('hide');
            toastr.success(data.message);
          } else {
            toastr.error(data.message);
          }
        }
      });
    }

    $(function () {
      $('#form-allot-center-master').validate({
        rules: {
          counsellor: {
            required: true
          },
          'fee[]': {
            required: true
          }
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
    });

    $("#form-allot-center-master").on("submit", function (e) {
      if ($('#form-allot-center-master').valid()) {
        $(':input[type="submit"]').prop('disabled', true);
        var formData = new FormData(this);
        formData.append('id', '<?= $id ?>');


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
              $('#sub-courses-table').DataTable().ajax.reload(null, false);
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
<?php } ?>