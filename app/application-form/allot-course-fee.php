<?php
if (isset($_POST['student_id']) && isset($_POST['course_id']) && isset($_POST['sub_course_id'])) {
  require '../../includes/db-config.php';
  session_start();
  $id = mysqli_real_escape_string($conn, $_POST['student_id']);
  $id = base64_decode($id);
  $student_id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
  $course_id = intval($_POST['course_id']);
  $sub_course_id = intval($_POST['sub_course_id']);

  $subcourse_sql = $conn->query("SELECT Name, ID FROM Sub_Courses WHERE Course_ID = '$course_id' AND ID ='$sub_course_id'");
  $subcourse_arr = $subcourse_sql->fetch_assoc();

  $fee_sql = $conn->query("SELECT Fee,Type,University_ID FROM Student_Ledgers WHERE Student_ID = '$student_id'  AND Type=1");
  $fee_arr = $fee_sql->fetch_assoc();
  $unifee_sql = $conn->query("SELECT Fee FROM Student_Ledgers WHERE Student_ID = '$student_id'  AND Type=3 and Source='University Fee'");
  $unifee_arr = $unifee_sql->fetch_assoc();
//   print_r($unifee_arr);die;
    $universityFee = $unifee_arr['Fee'];
?>

  <link href="/assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
  <style>
    .select2-container--default.select2-container--focus .select2-selection--multiple {
      border: unset !important;
      outline: 0;
    }

    #customers {
      font-family: Arial, Helvetica, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    #customers td,
    #customers th {
      border: 1px solid #ddd;
      padding: 8px;
    }

    #customers tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    #customers tr:hover {
      background-color: #ddd;
    }

    #customers th {
      padding-top: 12px;
      padding-bottom: 12px;
      text-align: left;
      background-color: #04AA6D;
      color: white;
    }
  </style>
  <div class="modal-header clearfix text-left m-0 p-0">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <h5 class="fs-4 text-black fw-bold">Allot<span class="semi-bold"></span>Course Fee</h5>
  </div>
  <!-- <form role="form" id="form-allot-fee" action="/app/application-form/store-fee" method="POST" enctype="multipart/form-data">
    <div class="modal-body">
      <div class="row">
        <div class="col-md-8">
          <div class="form-group form-group-default" style="border:unset;font-weight:700">
            Course Name<span style="color:red">*</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group form-group-default " style="border:unset">
            Fee<span style="color:red">*</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <div class="form-group form-group-default" style="border:unset;font-weight:700">
            <?= $subcourse_arr['Name'] ?>
            <input type="hidden" name="student_id" value="<?= $student_id ?>">
            <input type="hidden" name="university_id" value="<?= $fee_arr['University_ID'] ?>">
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group form-group-default">

            <input type="number" min="0" step="500" placeholder="Fee" name="fee[1]" value="<?= $fee_arr['Fee'] ?>" class="form-control" />
          </div>
        </div>
      </div>
      <?php if ($fee_sql->num_rows > 0) { ?>
        <?php
        $check_credited = $conn->query("SELECT Fee,Type FROM Student_Ledgers WHERE Student_ID = '$student_id' AND University_ID= '" . $fee_arr['University_ID'] . "' AND Type=2");
        $i = 1;
        $pending_fee = [];
        $total_fee = 0;
        while ($row = $check_credited->fetch_assoc()) {
          $pending_fee[] = $row['Fee'];
        ?>
          <div class="row">
            <div class="col-md-8">
              <div class="form-group form-group-default" style="border:unset;font-weight:700">
                Credit Amount <?= $i++; ?>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group form-group-default">
                <?= $row['Fee'] ?>
              </div>
            </div>
          </div>
        <?php } ?>
        <?php $total_fee = $fee_arr['Fee'] - array_sum($pending_fee); ?>
        <?php if ($total_fee != 0) { ?>
          <span style="color:red;">(Pending Fees-  <?= "&#8377;" . number_format($total_fee, 2);  ?>)</span>
          <div class="row">
            <div class="col-md-8">
              <div class="form-group form-group-default" style="border:unset;font-weight:700">
                Credit Amount
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group form-group-default">
                <input type="number" min="0" step="500"  placeholder="Credit Amount" name="fee[2]" value="" class="form-control" />
              </div>
            </div>
          </div>
      <?php } else { ?>
        <span style="color:darkgreen;">Course Fee Submitted !</span>
    <?php }
      } ?>

      <div id="subcourse">

      </div>
    </div>

    <div class="modal-footer clearfix text-end">
      <div class="col-md-4 m-t-10 sm-m-t-10">
        <button aria-label="" type="submit" class="btn btn-primary btn-cons btn-animated from-left">
          <span>Save</span>
          <span class="hidden-block">
            <i class="pg-icon">tick</i>
          </span>
        </button>
      </div>
    </div>
  </form> -->

  <form class="card-body" role="form" id="form-allot-fee" action="/app/application-form/store-fee" method="POST" enctype="multipart/form-data">
    <?php
    $query = "SELECT ID, University_fee FROM Sub_Courses WHERE Course_ID = $course_id";
    $result = mysqli_query($conn, $query);
    if (!$result) {
      die("Query failed: " . mysqli_error($conn));
    }
    if ($university_fee = mysqli_fetch_assoc($result)) {
        $uniid = $fee_arr['University_ID'];
      echo "<h5>University Fee:&emsp;" . $universityFee . "</h5>";
      echo "<input type='text' name='uni_fee' id='unifee' value=0 class='form-control'><button class='btn btn-primary' type='button' onclick='changeunifee($student_id,$uniid)'>change</button>";
    } else {
      echo "No fee found for Course ID $course_id.";
    }
    mysqli_free_result($result)
    ?>
    <div class="row ">
      <label class="col-sm-8 col-form-label fw-bold text-black" for="multicol-full-name">Course Name <span class="text-danger">*</span></label>
      <div class="col-sm-4">
        <label class=" col-form-label fw-bold text-black" for="multicol-full-name">Fee <span class="text-danger">*</span></label>
      </div>
    </div>
    <div class="row ">
      <div class="col-sm-8">
        <label class=" col-form-label text-black fw-bold" for="multicol-full-name"> <?= $subcourse_arr['Name'] ?> <span class="text-danger">*</span></label>
        <input type="hidden" name="student_id" value="<?= $student_id ?>">
        <input type="hidden" name="university_id" value="<?= $fee_arr['University_ID'] ?>">
      </div>
      <div class="col-sm-4">
        <input type="number" min="0" step="500" placeholder="Fee" name="fee[1]" value="<?= $fee_arr['Fee'] ?>" class="form-control" required>
      </div>
    </div>
    <?php if ($fee_sql->num_rows > 0) { ?>
      <table id="customers" class="table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Date of transaction</th>
            <th>Mode of Payment</th>
            <th>UTR No</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $check_credited = $conn->query("SELECT ID,Fee,Type,date_of_transation,mode_of_payment,utr_no FROM Student_Ledgers WHERE Student_ID = '$student_id' AND University_ID= '" . $fee_arr['University_ID'] . "' AND Type=2");
          $i = 1;
          $pending_fee = [];
          $total_fee = 0;
          $j = 0;
          while ($row = $check_credited->fetch_assoc()) {
            $pending_fee[] = $row['Fee'];
            $j++;
          ?>
            <tr>
              <td><?= $j ?></td>
              <td><?= $row['date_of_transation'] ?></td>
              <td><?= $row['mode_of_payment'] ?></td>
              <td><?= $row['utr_no'] ?></td>
              <td><?= $row['Fee'] ?></td>
              <td><a href="javascript:void(0)" onclick="editPayment(<?= $row['ID'] ?>)"><i class="ri-edit-box-fill text-primary ms-3"></i></a><a href="javascript:void(0)" onclick="pdfdownload(<?= $row['ID'] ?>)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                  </svg>
                </a>
              <a href="javascript:void(0)" onclick="deletePayment(<?= $row['ID'] ?>)"><i class="ri-delete-bin-5-line text-danger"></i></a></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <?php $total_fee = $fee_arr['Fee'] - array_sum($pending_fee); ?>
      <?php if ($total_fee != 0) { ?>
        <span style="color:red;" class="fw-bold">(Pending Fees- <?= "&#8377;" . number_format($total_fee, 2);  ?>)</span>
        <div class="row">
          <div class="col-md-8">
            <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
              Credit Amount
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-group-default">
              <input type="number" min="0" step="500" required placeholder="Credit Amount" name="fee[2]" value="" class="form-control" />
            </div>
          </div>
        </div>
        <div class="row mt-1">
          <div class="col-md-8">
            <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
              Date of transaction
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-group-default">
              <input type="date" placeholder="Date of transaction" required name="date_of_transation" id="date_of_transation" value="" class="form-control" />
            </div>
          </div>
        </div>
        <div class="row mt-1">
          <div class="col-md-8">
            <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
              Mode of paymet
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-group-default">
              <select name="mode_of_payment" required id="mode_of_payment" class="form-control">
                <option value="">Choose</option>
                <option value="Debit card">Debit Card</option>
                <option value="Credit card">Credit Card</option>
                <option value="Cash">Cash</option>
                <option value="UPI">UPI</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cheque">Cheque</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row mt-1">
          <div class="col-md-8">
            <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
              Reference or UTR details
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group form-group-default">
              <input type="text" placeholder="Reference or UTR details" name="utr_no" id="utr_no" required value="" class="form-control" />
            </div>
          </div>
        </div>
      <?php } else { ?>
        <span style="color:darkgreen;">Course Fee Submitted !</span>
    <?php }
    } ?>

    <div id="subcourse">

    </div>
    <div class="pt-6">
      <div class="row justify-content-end">
        <div class="col-sm-9">
          <?php
            if($fee_sql->num_rows==0)
            {
              ?>
                <button type="submit" class="btn btn-primary me-4">Add</button>
              <?php
            }
            elseif($fee_sql->num_rows>0 && ($fee_arr['Fee'] - array_sum($pending_fee))>0 )
            {
                ?>
                  <button type="submit" class="btn btn-primary me-4">Add</button>
                <?php
            }
          ?>
          <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-outline-secondary">Cancel</button>
        </div>
      </div>
    </div>
  </form>
  <script type="text/javascript" src="/assets/plugins/select2/js/select2.full.min.js"></script>
  <script type="text/javascript" src="/assets/plugins/bootstrap-tag/bootstrap-tagsinput.min.js"></script>
  <script>
    $(function() {
      $('#form-allot-fee').validate({
        rules: {
          fee: {
            required: true
          },
          date_of_transation: {
            required: true
          },
          mode_of_payment: {
            required: true
          },
          utr_no: {
            required: true
          }
        },
        highlight: function(element) {
          $(element).closest('.form-control').addClass('has-error');
        },
        unhighlight: function(element) {
          $(element).closest('.form-control').removeClass('has-error');
        }
      });
    })

    $("#form-allot-fee").on("submit", function(e) {
      if ($('#form-allot-fee').valid()) {
        var formData = new FormData(this);
        e.preventDefault();
        $.ajax({
          url: $(this).attr('action'),
          type: "POST",
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          dataType: 'json',
          success: function(data) {
            if (data.status == 200) {
              toastr.success(data.message);
              $('.modal').modal('hide');
              $('.table').DataTable().ajax.reload(null, false);
            } else {
              toastr.error(data.message);
            }
          },
          error: function(data) {
            toastr.error('Server is not responding. Please try again later');
          }
        });
      }
    });

    function editPayment(rowId) {
      $.ajax({
        url: "/app/application-form/edit_payment",
        type: 'post',
        data: {
          id: rowId
        },
        success: function(data) {
          $('#sm-modal-content').html(data);
          $('#smmodal').css('z-index', 999999);
          $('#smmodal').modal('show');
        }
      })
    }
    
     function deletePayment(rowId) {
      $.ajax({
        url: "/app/application-form/delete_payment",
        type: 'post',
        data: {
          id: rowId
        },
        success: function(data) {
          data = JSON.parse(data);
          toastr.success(data.message);
          $('a[onclick="deletePayment(' + rowId + ')"]').closest('tr').remove();
          $('.modal').modal('hide');
          $('.table').DataTable().ajax.reload(null, false);
          
        }
      })
    }

    function pdfdownload(rowId) {
      window.location.href = "/app/application-form/pdf_desc?id=" + rowId;
    }
    
    function changeunifee(stuid,uniid){
        var unifee = $('#unifee').val();
        var studentid = stuid;
        var uniid = uniid;
        $.ajax({
            url:"/app/application-form/allotunifee",
            type:"post",
            data:{unifee,studentid,uniid},
            success:function(res){
                data = JSON.parse(res);
                toastr.success(data.message);
            }
        })
    }
  </script>
<?php } ?>