<div id="accordionDelivery" class="accordion mt-2">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" aria-expanded="true" data-bs-target="#bank-details" aria-controls="schemes">Bank Details</button>
    </h2>
    <div id="bank-details" class="accordion-collapse collapse">
      <div class="accordion-body">
        <div class="card-body">
          <div class="row p-b-20">
            <div class="col-lg-12 text-end">
              <button type="button" class="btn btn-primary mb-3" onclick="addComponents('bank-details', 'md', <?= $university_id ?>)">
                <i class="ri-apps-2-add-line"></i>
              </button>
            </div>
          </div>

          <div class="card">
            <div class="card-datatable table-responsive">
              <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <div class="table-responsive">
                  <table class="dt-multilingual table table-bordered dataTable no-footer dtr-column ctable" id="tableBankDetailsDatatable" aria-describedby="DataTables_Table_3_info">
                    <thead>
                      <tr>
                        <th class="sorting_disabled" style="width: 50%;">Account Holder Name</th>
                        <th class="sorting_disabled text-center" style="width: 30%;">Account No</th>
                        <th class="sorting_disabled text-center" style="width: 30%;">Bank Name</th>
                        <th class="sorting_disabled text-center" style="width: 30%;">Branch Name</th>
                        <th class="sorting_disabled text-center" style="width: 30%;">IFSC Code</th>
                        <th class="sorting_disabled text-center" style="width: 30%;">Account Type</th>
                        <th class="sorting_disabled text-center" style="width: 20%;">Status</th>
                        <th class="sorting_disabled text-center" style="width: 20%;">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                    </tbody>
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

<script type="text/javascript">
  var table = $('#tableBankDetailsDatatable');
  var settings = {
    processing: true,
    serverSide: true,
    serverMethod: 'post',
    ajax: {
      url: '/app/components/bank-details/server',
      type: 'POST',
      data: function(data) {
        data.university_id = '<?= $university_id ?>';
      },
    },
    columns: [
      { data: "Account_Holder_Name" },
      { data: "Account_No" },
      { data: "Bank_Name" },
      { data: "Branch_Name" },
      { data: "IFSC_Code" },  {
         data: "Account_Type" },
      { 
        data: "Status",
        render: function(data, type, row) {
          var active = data == 1 ? 'Active' : 'Inactive';
          var checked = data == 1 ? 'checked' : '';
          return '<div class="form-check form-switch mb-2 d-flex flex-row justify-content-center">\
            <input class="form-check-input me-3" onclick="changeComponentStatus(\'Bank_Accounts\', \'Bank_Accounts\', \'' + row.ID + '\');" type="checkbox" ' + checked + ' id="scheme-status-switch-' + row.ID + '">\
            <label for="scheme-status-switch-' + row.ID + '">' + active + '</label>\
          </div>';
        }
      },
      { 
        data: "ID",
        render: function(data, type, row) {
          return '<div class="text-end">\
            <i class="ri-edit-box-fill text-success" onclick="editComponents(\'bank-details\', \'' + data + '\', \'md\');"></i>\
            <i class="ms-3 ri-delete-bin-5-line text-danger" onclick="destroyComponents(\'bank-details\', \'tableBankDetailsDatatable\', \'' + data + '\');"></i>\
          </div>';
        }
      }
    ],
    columnDefs: [
      { width: "50%", targets: 0 }, // Column 1 width (Name)
      { width: "30%", targets: 1, className: 'text-center' }, // Column 2 width (Status)
      { width: "20%", targets: 2, className: 'text-center' } // Column 3 width (Action)
    ],
    sDom: "<t><'row'<p i>>",
    destroy: true,
    scrollCollapse: true,
    oLanguage: {
      sLengthMenu: "_MENU_ ",
      sInfo: "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
    },
    aaSorting: [],
    iDisplayLength: 5
  };

  table.dataTable(settings);
</script>
