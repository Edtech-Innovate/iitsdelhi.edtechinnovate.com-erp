<div class="modal-header clearfix text-left m-0 p-0">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <h5 class="fs-4 text-black fw-bold">Edit Payment</h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <label for="">Amount</label>
            <input type="text" class="form-control" id="new_amount" name="new_amount">
            <input type="hidden" name="id" id="id" value="<?=$_POST['id']?>">
        </div>
      <div class="col-md-12 mt-2">
            <label for="">Reference or UTR details</label>
            <input type="text" class="form-control" id="utr_no" name="utr_no" placeholder="Reference or UTR details">
        </div>
      <div class="col-md-12 mt-2">
            <label for="">Mode of Paymet</label>
            <select name="mode_of_payment"  id="mode_of_payment" name = "mode_of_payment" class="form-control" aria-required="true">
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
    <div class="row mt-2">
        <div class="col-md-4">
        <button class="btn btn-primary" type="button" onclick="saveAmount()">Save</button>
        </div>
    </div>
</div>
<script>
    function saveAmount()
    {
        var dataid = $('#id').val();
        var amount = $('#new_amount').val();
        var utr_no = $('#utr_no').val();
        var mode_of_payment = $('#mode_of_payment').val();
        $.ajax({
            url:'/app/application-form/update_payment',
            type:'post',
            data:{id:dataid,amount:amount,utr_no:utr_no,mode_of_payment:mode_of_payment},
            success:function(res)
            {
                data = JSON.parse(res);
                toastr.success(data.message);
                $('.modal').modal('hide');
                $('.table').DataTable().ajax.reload(null, false);
            }
        })
    }
</script>