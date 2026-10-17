<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-top.php') ?>

<style>
    .dataTables_length {
        text-align: start !important;
    }

    .custom-tooltip .tooltip-inner {
        max-width: 300px;
        /* Set your desired width */
        white-space: pre-wrap;
        /* Keep line breaks and spaces */
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" />
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/header-bottom.php') ?>
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/side-menu.php') ?>
<?php require($_SERVER['DOCUMENT_ROOT'] . '/includes/db-config.php'); ?>
<?php
unset($_SESSION['current_session']);
unset($_SESSION['current_session']);
unset($_SESSION['filterByDepartment']);
unset($_SESSION['filterByUser']);
unset($_SESSION['filterByDate']);
unset($_SESSION['filterBySubCourses']);
unset($_SESSION['filterByStatus']);
unset($_SESSION['filterByFeeAssigned']);
?>
<div class="layout-page">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/top-menu.php') ?>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <ol class="breadcrumb d-flex flex-wrap justify-content-between align-self-start">
                <?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI']));
                for ($i = 1; $i <= count($breadcrumbs); $i++) {
                    if (count($breadcrumbs) == $i) : $active = "active";
                        $crumb = explode("?", $breadcrumbs[$i]);
                        echo '<li class="breadcrumb-item ' . $active . '">' . strtoupper($crumb[0]) . '</li>';
                    endif;
                }
                ?>
                <div style="display:flex">
                    <?php if ($_SESSION['Role'] == 'Administrator' || $_SESSION['Role'] == 'University Head') { ?>
                        <!-- <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload OA, Enrollment AND Roll No." onclick="uploadOAEnrollRoll()"> <i class="uil uil-upload"></i></button> -->
                        <!-- <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Upload Pendency" onclick="uploadMultiplePendency()"> <i class="uil uil-file-upload-alt"></i></button> -->
                    <?php } ?>
                    <!-- <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Excel" onclick="exportData()"> <i class="uil uil-down-arrow"></i></button> -->
                    <!-- <button class="btn btn-link" aria-label="" title="" data-toggle="tooltip" data-original-title="Download Documents" onclick="exportSelectedDocument()"> <i class="uil uil-file-download-alt"></i></button> -->
                </div>
            </ol>
            <div class="row justify-content-center mb-3">
                <div class="col-sm-2">
                    <select class="select2 form-select" data-allow-clear="true" id="univeristies" onchange="getadmissionsession(this.value)" data-placeholder="Choose Program">
                        <option value="All">All</option>
                        <?php
                        $universities = $conn->query("SELECT ID , Name FROM Universities WHERE Status=1");
                        while ($university = $universities->fetch_assoc()) { ?>
                            <option value="<?= $university['ID'] ?>" <?= !empty($university_array) ? (in_array($university['ID'], $university_array) ? 'selected' : '') : '' ?>><?= $university['Name'] ?></option>
                        <?php } ?>
                    </select>



                </div>
                <div class="col-sm-2">
                    <select class="select2 form-select" data-allow-clear="true" id="sessions" onchange="changeSession(this.value)">
                        <option value="All">All</option>

                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="select2 form-select" data-allow-clear="true" id="sub_courses" onchange="addFilter(this.value, 'sub_courses')" data-placeholder="Choose Program">
                        <option value="All">All</option>
                        <?php
                        $programs = $conn->query("SELECT Sub_Courses.ID, CONCAT(Courses.Short_Name, ' (', Sub_Courses.Name, ')') as Name FROM Students LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID LEFT JOIN Courses ON Sub_Courses.Course_ID = Courses.ID WHERE  Students.University_ID IS NOT NULL $role_querys GROUP BY Students.Sub_Course_ID");
                        while ($program = $programs->fetch_assoc()) {
                            echo '<option value="' . $program['ID'] . '">' . $program['Name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="select2 form-select" data-allow-clear="true" id="search_assign_fee" onchange="addFilter(this.value, 'search_assign_fee');" data-placeholder="Choose Program">
                        <option value="">Choose Fee Type</option>
                        <option value="1">Fee Assigned</option>
                        <option value="2">Fee not Assigned</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <div class="input-daterange input-group d-flex align-items-center" id="datepicker-range">
                        <input type="text" class="input-sm form-control" placeholder="Select Date" id="startDateFilter" name="start" />
                        <div class="input-group-addon mx-2">to</div>
                        <input type="text" class="input-sm form-control" placeholder="Select Date" id="endDateFilter" onchange="addDateFilter()" name="end" />
                    </div>
                </div>
                <?php if ($_SESSION['Role'] != 'Sub-Center' && $_SESSION['Role'] != 'Center') { ?>
                    <div class="col-sm-2">
                        <select class="select2 form-select" data-allow-clear="true" id="users" onchange="addFilter(this.value, 'users')" data-placeholder="Choose User">

                        </select>
                    </div> <?php } ?>
                <?php if ($_SESSION['Role'] == 'Center') { ?>

                    <!-- <div class="col-md-2 m-b-10">
                    <div class="form-group">
                    <select class="form-control sub_center" data-init-plugin="select2" id="center_sub_center" onchange="addSubCenterFilter(this.value, 'users')" data-placeholder="Choose Sub Center">
                      <?php $sub_center_query = $conn->query("SELECT Users.ID, Users.Name, Users.Code FROM Center_SubCenter LEFT JOIN Users ON Users.ID = Center_SubCenter.Sub_Center  WHERE Center_SubCenter.Center='" . $_SESSION['ID'] . "' AND Users.Role='Sub-Center'");
                        while ($subCenterArr = $sub_center_query->fetch_assoc()) { ?>
                        <option value="">Choose Sub Center</option>
                        <option value="<?= $subCenterArr['ID'] ?>"><?= $subCenterArr['Name'] . "(" . $subCenterArr['Code'] . ")"  ?></option>
                      <?php } ?>  
                    </select>
                    </div>
                  </div> -->
                <?php } ?>
            </div>
            <div class="row justify-content-center">
                <div class="col-sm-12">
                    <div class="card text-center mb-4">
                        <div class="card-header">
                            <div class="nav-align-top ">
                                <ul class="nav nav-pills d-flex justify-content-between" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button type="button" class="nav-link d-flex flex-column gap-1 waves-effect waves-light active" role="tab" data-bs-toggle="tab" data-bs-target="#all_applications" aria-controls="navs-pills-within-card-active" aria-selected="true"><i class="tf-icons ri-home-smile-line"></i><span> ALL APPLICATIONS (<span id="application_count">0</span>)</span></button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content p-0">
                                <div class="tab-pane fade active show" id="all_applications" role="tabpanel">
                                    <!-- <div class="row d-flex justify-content-end">
                                        <div class="col-md-2 d-flex justify-content-start">
                                            <input type="text" id="application-search-table" class="form-control pull-right" placeholder="Search">
                                        </div>
                                    </div> -->
                                    <div class="table-responsive">
                                        <table class="table table-hover nowrap " id="application-table">

                                            <thead class="">
                                                <tr>
                                                    <th data-orderable="true"></th>
                                                    <th class="text-center">Student</th>
                                                    <th class="text-center">Enrollment No.</th>
                                                    <th class="text-center">Adm Session</th>
                                                    <th class="text-center">Center(Sub-Center) Name</th>
                                                    <th class="text-center">University</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-top.php') ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('#datepicker-range').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            endDate: '0d'
        });
    </script>

    <?php if ($_SESSION['Role'] == 'Administrator') { ?>
        <script type="text/javascript">
            changeUniversity();
        </script>
    <?php } ?>

    <script type="text/javascript">
        $(function() {
            var role = '<?php echo $_SESSION['Role']; ?>';
            // alert(role);
            var showInhouse = role != 'Center' && role != 'Sub-Center' ? true : false;
            var is_accountant = ['Accountant', 'Administrator'].includes(role) ? true : false;
            var is_university_head = ['University Head', 'Administrator'].includes(role) ? true : false;
            var is_operations = role == 'Operations' ? true : false;
            var hasStudentLogin = '<?php echo isset($_SESSION['has_lms']) && $_SESSION['has_lms'] == 1 ? 'true' : 'false'; ?>';
            var applicationTable = $('#application-table');
            // alert(is_operations);
            var applicationSettings = {
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                'ajax': {
                    'url': '/app/applications/application-server',
                    'type': 'POST',
                    complete: function(xhr, responseText) {
                        $('#application_count').html(xhr.responseJSON.iTotalDisplayRecords);
                    }
                },
                'columns': [{
                        data: "ID",
                        "render": function(data, type, row) {
                            var addPayment = '<a href="/university/payment/create?id='+row.ID+'"><i class="ri-add-box-fill"></i></a>'
                            return addPayment;
                        }
                    },
                    {
                        data: "First_Name",
                        "render": function(data, type, row) {
                            return '<strong>' + data + '</strong>';
                        },
                    },
                    {
                        data: "Enrollment_No",
                        "render": function(data, type, row) {
                            var edit = showInhouse && row.Processed_To_University != 1 ? '<i class="ri-edit-box-fill text-primary ms-3" title="Add Enrollment No." onclick="addEnrollment(&#39;' + row.ID + '&#39;)">' : '';
                            return data + edit;
                        }
                    },

                    {
                        data: "Adm_Session"
                    },
                    {
                        data: "Center_Name",
                        "render": function(data, type, row) {
                            var name = row.Center_Name;
                            var Sub_Center_Name = row.Sub_Center_Name.length > 0 ? '( ' + row.Sub_Center_Name + ' )' : '';
                            return name + Sub_Center_Name;
                        },
                        visible: role == 'Sub-Center' ? false : true
                    },
                    {
                        data: "university_name",
                    }
                ],
              destroy: true,
              scrollCollapse: true,
              oLanguage: {
                  sInfo: "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
              },
              drawCallback: function() {
                  $('[data-toggle="tooltip"]').tooltip({
                      template: '<div class="tooltip custom-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
                  });
              },
         	 };
            applicationTable.dataTable(applicationSettings);

            // search box for table
            $('#application-search-table').keyup(function() {
                applicationTable.fnFilter($(this).val());
            });
        })
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            getadmissionsession('All');
        })

        function getadmissionsession(university = null) {
            $.ajax({
                url: '/app/applications/get-adm-session',
                data: {
                    university: university
                },
                type: 'POST',
                success: function(data) {
                    $('#sessions').html(data);
                }

            })
        }

        function changeSession(value) {
            $('input[type=search]').val('');
            updateSession();
        }

        function updateSession() {
            var session_id = $('#sessions').val();
            $.ajax({
                url: '/app/applications/change-session',
                data: {
                    session_id: session_id
                },
                type: 'POST',
                success: function(data) {
                    $('.table').DataTable().ajax.reload(null, false);
                }
            })
        }
    </script>

    <script type="text/javascript">
        function addEnrollment(id) {
            $.ajax({
                url: '/app/applications/enrollment/create?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#md-modal-content').html(data);
                    $('#mdmodal').modal('show');
                }
            })
        }

        function addOANumber(id) {
            $.ajax({
                url: '/app/applications/oa-number/create?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#md-modal-content').html(data);
                    $('#mdmodal').modal('show');
                }
            })
        }
    </script>

    <script type="text/javascript">
        function exportData() {
            var search = $('#application-search-table').val();
            //console.log(search, "sandip");
            var steps_found = $('.nav-tabs').find('li a.active').attr('data-target');
            var steps_found = steps_found.substring(1, steps_found.length);
            var url = search.length > 0 ? "?steps_found=" + steps_found + "&search=" + search : "?steps_found=" + steps_found;
            //var url = search.length > 0 ? "?search=" + search : "";
            window.open('/app/applications/export' + url);
        }

        function exportDocuments(id) {
            $.ajax({
                url: '/app/applications/document?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#md-modal-content').html(data);
                    $('#mdmodal').modal('show');
                }
            })
        }

        function exportZip(id) {
            window.open('/app/applications/zip?id=' + id);
        }

        function exportPdf(id) {
            window.open('/app/applications/pdf?id=' + id);
        }

        function exportSelectedDocument() {
            var search = $('#application-search-table').val();
            var searchQuery = search.length > 0 ? "?search=" + search : "";
            $.ajax({
                url: '/app/applications/documents/create' + searchQuery,
                type: 'GET',
                success: function(data) {
                    $('#md-modal-content').html(data);
                    $('#mdmodal').modal('show');
                }
            })
        }
    </script>

    <script type="text/javascript">
        function uploadOAEnrollRoll() {
            $.ajax({
                url: '/app/applications/uploads/create_oa_enroll_roll',
                type: 'GET',
                success: function(data) {
                    $('#md-modal-content').html(data);
                    $('#mdmodal').modal('show');
                }
            })
        }
    </script>

    <script type="text/javascript">
        function printForm(id) {
            window.open('/forms/47/index.php?student_id=' + id, '_blank');
            // window.location.href = '/forms/47/index.php?student_id=' + id;
        }
    </script>

    <script type="text/javascript">
        function processByCenter(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Process'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/app/applications/process-by-center",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: id
                        },
                        success: function(data) {
                            if (data.status == 200) {
                                notification('success', data.message);
                                $('.table').DataTable().ajax.reload(null, false);
                            } else {
                                notification('danger', data.message);
                                $('.table').DataTable().ajax.reload(null, false);
                            }
                        }
                    });
                } else {
                    $('.table').DataTable().ajax.reload(null, false);
                }
            })
        }

        function processedToUniversity(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Process.'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/app/applications/processed-to-university",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: id
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.status == 200) {
                                toastr.success(data.message);
                                $('.table').DataTable().ajax.reload(null, false);
                            } else {
                                toastr.error(data.message);
                                $('.table').DataTable().ajax.reload(null, false);
                            }
                        }
                    });
                } else {
                    $('.table').DataTable().ajax.reload(null, false);
                }
            })
        }

        function verifyPayment(id) {
            $.ajax({
                url: '/app/applications/review-payment?id=' + id,
                type: 'GET',
                success: function(data) {
                    $("#lg-modal-content").html(data);
                    $("#lgmodal").modal('show');
                }
            })
        }

        function verifyDocument(id) {
            $.ajax({
                url: '/app/applications/review-documents?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#xl-modal-content').html(data);
                    $('#xlmodal').modal('show');
                }
            })
        }

        function reportPendency(id) {
            $.ajax({
                url: '/app/pendencies/create?id=' + id,
                type: 'GET',
                success: function(data) {
                    $('#report-modal-content').html(data);
                    $('#reportmodal').modal('show');
                }
            })
        }

        function uploadPendency(id) {
            $(".modal").modal('hide');
            $.ajax({
                url: '/app/pendencies/edit?id=' + id,
                type: 'GET',
                success: function(data) {
                    $("#lg-modal-content").html(data);
                    $("#lgmodal").modal('show');
                }
            })
        }

        function uploadMultiplePendency() {
            $(".modal").modal('hide');
            $.ajax({
                url: '/app/pendencies/upload',
                type: 'GET',
                success: function(data) {
                    $("#lg-modal-content").html(data);
                    $("#lgmodal").modal('show');
                }
            })
        }
    </script>

    <script>
        if ($("#users").length > 0) {
            $("#users").select2({
                placeholder: 'Choose Center'
            })
            getCenterList('users');
        }

        $("#departments").select2({
            placeholder: 'Choose Department'
        })

        function addFilter(id, by) {
            $.ajax({
                url: '/app/applications/filter',
                type: 'POST',
                data: {
                    id,
                    by
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        $('.table').DataTable().ajax.reload(null, false);
                        $("#sub_center").html(data.subCenterName);

                        if ('<?= $_SESSION['Role'] ?>' == 'Administrator') {
                            $(".sub_center").html(data.subCenterName);

                        }
                    }
                }
            })
        }

        function addSubCenterFilter(id, by) {
            $.ajax({
                url: '/app/applications/filter',
                type: 'POST',
                data: {
                    id,
                    by
                },
                dataType: 'json',
                success: function(data) {

                    if (data.status) {
                        $('.table').DataTable().ajax.reload(null, false);
                        // $  ("#sub_center").html(data.subCenterName);
                    }

                }
            })
        }

        function addDateFilter() {
            var startDate = $("#startDateFilter").val();
            var endDate = $("#endDateFilter").val();
            if (startDate.length == 0 || endDate == 0) {
                return
            }
            var id = 0;
            var by = 'date';
            $.ajax({
                url: '/app/applications/filter',
                type: 'POST',
                data: {
                    id,
                    by,
                    startDate,
                    endDate
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        $('.table').DataTable().ajax.reload(null, false);
                    }
                }
            })
        }

        function getCourses(id) {
            $.ajax({
                url: '/app/courses/department-courses',
                type: 'POST',
                data: {
                    id
                },
                success: function(data) {
                    $("#sub_courses").html(data);
                }
            })
        }

        function allot_course_fee(url = null, modal = null, student_id = null, course_id = null, sub_course_id = null, center_id = null, duration = null) {
            $.ajax({
                url: '/app/' + url + '/allot-course-fee',
                type: 'POST',
                data: {
                    student_id: student_id,
                    course_id: course_id,
                    sub_course_id: sub_course_id,
                    center_id: center_id,
                    duration: duration
                },
                success: function(data) {
                    $('#' + modal + '-modal-content').html(data);
                    $('#' + modal + 'modal').modal('show');
                }
            })
        }
      function students_data(url = null, modal = null, ID = null) {
        $.ajax({
            url: '/app/' + url + '/students_details',
            type: 'POST',
            data: {
                ID: ID
            },
            success: function(data) {
                $('#' + modal + '-modal-content').html(data);
                $('#' + modal + 'modal').modal('show');
            }
        })
    }
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/footer-bottom.php') ?>