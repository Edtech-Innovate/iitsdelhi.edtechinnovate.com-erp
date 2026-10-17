<?php

$id = $_POST['id'];
$courseId = $_POST['courseId'];
$payment_type = $_POST['payment_type'];
?>

<div class="modal-header clearfix text-left m-0 p-0">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    <h5 class="fs-4 text-black fw-bold">Allot<span class="semi-bold"></span>University Fee</h5>
</div>
<form action="/university/payment/store" method="post" id="unversity_payment_form">
    <div class="modal-body">
        <div class="row">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="courseId" value="<?= $courseId ?>">
            <input type="hidden" name="payment_type" value="<?= $payment_type ?>">

            <div class="col-md-6">
                <label for="amount">Amount</label>
                <input type="number" name="amount" id="amount" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="date">Date of Transaction</label>
                <input type="date" name="date_of_transaction" id="date_of_transaction" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="urn_no">URN/Reference Number</label>
                <input type="text" name="urn_no" id="urn_no" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="mode_of_payment">Mode of transaction</label>
                <select name="mode_of_payment" id="mode_of_payment" class="form-control">
                    <option value="Cash">Cash</option>
                    <option value="Debit">Debit Card</option>
                    <option value="Credit">Credit Card</option>
                    <option value="UPI">UPI</option>
                    <option value="DD">DD</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
        </div>
    </div>
    <div class="pt-6">
        <div class="row justify-content-end">
            <div class="col-sm-9">
                <button type="submit" class="btn btn-primary me-4">Add</button>
                <button type="button" data-bs-dismiss="modal" aria-label="Close"
                    class="btn btn-outline-secondary">Cancel</button>
            </div>
        </div>
    </div>
</form>

<script>
    $('#unversity_payment_form').submit(function (e) {
        e.preventDefault();
        var formdata = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formdata,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (res) {
                if (res.status == 'success') {
                    $('.modal').modal('hide');
                    Swal.fire({
                        title: "Done",
                        text: res.message,
                        icon: "success"
                    });
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: res.message,
                    });
                    $('.swal2-container').css('z-index', '9999');
                }
            },
            error: function (data) {
                console.log('An error occurred.');
                console.log(data);
            },
        })
    })
</script>