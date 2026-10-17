<?php
if (isset($_POST['student_id']) && isset($_POST['course_id']) && isset($_POST['sub_course_id'])) {
    require '../../includes/db-config.php';
    require '../../includes/helpers.php';

    $id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $id = base64_decode($id);
    $student_id = intval(str_replace('W1Ebt1IhGN3ZOLplom9I', '', $id));
    $course_id = intval($_POST['course_id']);
    $sub_course_id = intval($_POST['sub_course_id']);
    $uni_id = intval($_POST['uni_id']);
    $get_student_fee = $conn->query("SELECT * FROM Student_Ledgers where Student_ID = $student_id AND University_ID = $uni_id AND Source = 'Miscellaneous Type Fee' AND Type=1");
    $fee_head = [];
    if ($get_student_fee->num_rows > 0) {
        $stuFeeDataArr = $get_student_fee->fetch_all(MYSQLI_ASSOC);
        $fee_head = array_column($stuFeeDataArr, 'fee_head');
    }

    ?>
    <style>
        ::-webkit-scrollbar {
            width: 2px;
        }

        .select2-selection__rendered {
            overflow-x: auto !important;
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 4px;
        }
        div#lg-modal-content {
    overflow: UNSET;
}
    </style>
    <style>
        /* .select2-container--default.select2-container--focus .select2-selection--multiple {
                border: unset !important;
                outline: 0;
            } */

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
    <!-- Modal Header -->
    <div class="modal-header  border-0">
        <h5 class="modal-title fw-bold">Allot <span class="text-primary">Miscellaneous</span> Fee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <!-- Form Start -->
    <form id="form-add-miscellaneous" class="p-4" action="/app/application-form/store-miscellaneous-fee" method="POST"
        enctype="multipart/form-data" role="form">
        <!--<div style='height: 219px;'>-->
        <input type="hidden" name="student_id" value="<?= $student_id ?>">
        <input type="hidden" name="university_id" value="<?= $uni_id ?>">
        <!-- Fee Head Dropdown -->
        <div class="mb-4">
            <label for="fee_head" class="form-label fw-semibold">Fee Head <span class="text-danger">*</span></label>
            <select id="fee_head" class="select2 form-select" name="fee_head[]" multiple required
                onchange="showFeeSection(this);" data-dropdown-parent="#form-add-miscellaneous">
                <?php
                // $feeHeadArr = ['1' => 'Migration', '2' => 'Transcript', '3' => 'Provisional', '4' => 'Convocation Fee', '5'=>'Bonafide', '6'=>'LOR/MOI'];
                // $feeHeadArr = ['1' => 'Migration', '2' => 'Transcript', '3' => 'Provisional', '4' => 'Convocation Fee', '5'=>'Bonafide', '6'=>'LOR/MOI', '7'=>'WES', '8'=>'QUADRABAY', '9'=>'DATAFLOW'];
                // Fetch all active fee heads
                 $feeHeadsResult = $conn->query("
                   SELECT ID, Name 
                   FROM Fee_Heads 
                   WHERE Status = 1 
                   ORDER BY ID ASC
                   ");

                $feeHeadArr = [];
                 if ($feeHeadsResult->num_rows > 0) {
                     while ($row = $feeHeadsResult->fetch_assoc()) {
                $feeHeadArr[$row['ID']] = $row['Name'];
                   }
                 }

                foreach ($feeHeadArr as $key => $value) {
                    $selected = in_array($key, $fee_head) ? 'selected' : '';
                    ?>
                    <option value="<?= $key ?>" <?= $selected ?>><?= $value ?></option>
                <?php }
                ?>
            </select>
        </div>

        <!-- Dynamic Fee Section -->
        <div class="fee_section">

        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end gap-3 mt-4">
            <button type="submit" class="btn btn-primary px-4">Add</button>
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
        <!--</div>-->
    </form>

    <!-- JavaScript -->
    <script>
        $(document).ready(function () {
            showFeeSection(document.getElementById('fee_head'));
        })
        function showFeeSection(selectedElement) {
            const selectedOption = Array.from(selectedElement.selectedOptions);
            const feeHeadData = selectedOption.map(option => ({
                id: option.value,
                name: option.text
            }));
            var student_id = '<?= $student_id ?>';
            var uni_id = '<?= $uni_id ?>';
            $.ajax({
                url: '/app/application-form/show-fee-head',
                type: 'POST',
                dataType: 'text',
                data: {
                    fee_head: JSON.stringify(feeHeadData),
                    student_id: student_id,
                    uni_id: uni_id
                },
                success: function (result) {
                    $('.fee_section').html(result);
                }
            });
        }

        function showModeType(mode) {
            if (mode !== 'Cash') {
                $('#holder_name, #file').attr('required', 'required'); // Add required
            } else {
                $('#holder_name, #file').removeAttr('required'); // Remove required
            }
        }

        function checkAmount(credit_amount) {
            let total_amount = $("input[name ='total_fee']").val();
            $("#credit_error").empty();
            $(':input[type="submit"]').prop('disabled', false);
            if (credit_amount > total_amount) {
                $("#credit_error").html('Please enter an amount less than or equal to ' + total_amount);
                $(':input[type="submit"]').prop('disabled', true);
            }
        }

        function editPayment(rowId) {
            $.ajax({
                url: "/app/application-form/edit_payment",
                type: 'post',
                data: {
                    id: rowId
                },
                success: function (data) {
                    $('#sm-modal-content').html(data);
                    $('#smmodal').css('z-index', 999999);
                    $('#smmodal').modal('show');
                }
            })
        }

        $(document).ready(function () {
            $('.select2').select2({
                placeholder: "Select Fee Head(s)",
                dropdownParent: $('#form-add-miscellaneous'),
                width: '100%'
            }).on('select2:select', function () {
                $(this).select2('close');
            });

            $('#form-add-miscellaneous').validate({
                rules: {
                    fee_head: { required: true }
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            });

            $('#form-add-miscellaneous').on('submit', function (e) {
                if ($(this).valid()) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    $(':input[type="submit"]').prop('disabled', true);

                    $.ajax({
                        url: this.action,
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (data) {
                            $('.modal').modal('hide');
                            if (data.status === 200) {
                                toastr.success(data.message);
                                $('#DataTables_Table_3').DataTable().ajax.reload(null, false);
                            } else {
                                $(':input[type="submit"]').prop('disabled', false);
                                toastr.error(data.message);
                            }
                        }
                    });
                }
            });
        });
    </script>
<?php } ?>