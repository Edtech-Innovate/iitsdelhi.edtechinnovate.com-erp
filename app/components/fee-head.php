<div id="accordionFeeHeads" class="accordion mt-2">
  <div class="accordion-item">
    <h2 class="accordion-header">
      <button class="accordion-button" type="button"
        data-bs-toggle="collapse"
        aria-expanded="true"
        data-bs-target="#feeHeads"
        aria-controls="feeHeads">
        Fee Heads
      </button>
    </h2>

    <div id="feeHeads" class="accordion-collapse collapse">
      <div class="accordion-body">

        <div class="row p-b-20 mb-3">
          <div class="col-lg-12 text-end">
            <button type="button" class="btn btn-primary"
              onclick="addComponents('fee-heads', 'md', '<?= $university_id ?>')">
              <i class="ri-apps-2-add-line"></i>
            </button>
          </div>
        </div>

        <div class="card">
          <div class="card-datatable table-responsive">
            <div class="table-responsive">
              <table
                class="dt-multilingual table table-bordered dataTable no-footer dtr-column ctable"
                id="tableFeeHeadsDatatable">
                <thead>
                  <tr>
                    <th style="width:80%;">Fee Head Name</th>
                    <th style="width:20%;">Action</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  var table = $('#tableFeeHeadsDatatable');

  var settings = {
    processing: true,
    serverSide: true,
    serverMethod: 'post',
    ajax: {
      url: '/app/components/fee-heads/server',
      type: 'POST',
      data: function (data) {
        data.university_id = '<?= $university_id ?>';
      }
    },
    columns: [
      { data: "Name" },
      {
        data: "ID",
        className: "text-center",
        render: function (data, type, row) {
          return `
            <div>
              <i class="ri-edit-box-fill text-success"
                 onclick="editComponents('fee-heads','${data}','md');"></i>
              <i class="ms-3 ri-delete-bin-5-line text-danger"
                 onclick="destroyComponents('fee-heads','FeeHeadsDatatable','${data}');"></i>
            </div>
          `;
        }
      }
    ],
    columnDefs: [
      { width: "90%", targets: 0 },
      { width: "10%", targets: 1, className: 'text-center' }
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
