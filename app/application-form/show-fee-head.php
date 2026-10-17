<?php if (isset($_POST['fee_head'])) {
    require '../../includes/db-config.php';
    require '../../includes/helpers.php';
    $fee_head_arr = json_decode($_POST['fee_head'], true);
    $student_id = intval($_POST['student_id']);
    $uni_id = intval($_POST['uni_id']);

    ?>
    <div class="row g-3">
        <?php foreach ($fee_head_arr as $key => $value) {
            $get_student_fee = $conn->query("SELECT * FROM Student_Ledgers where Student_ID = $student_id AND University_ID = $uni_id AND fee_head = '" . $value['id'] . "' AND Source = 'Miscellaneous Type Fee' AND Type=1");
            if ($get_student_fee->num_rows > 0) {
                $feeArr = $get_student_fee->fetch_assoc();
                $fee_head_fee = trim($feeArr['Fee']);
                
                $uni_fee = trim($feeArr['University_Fee']);
            } else {
                   $uni_fee='';
                $fee_head_fee = '';
            }
            $disabled = ($get_student_fee->num_rows == 0) ? '' : "disabled";
            ?>
            <div class="col-md-4 text-md-start text-center">
                <span class="fw-bold text-dark"><?= $value['name'] ?></span> <!-- Replace dynamically -->
            </div>
            <div class="col-md-4">
                <input type="number" name="misce_fee[<?= $value['id'] ?>]" value="<?= $fee_head_fee ?>" class="form-control"
                    <?= $disabled ?> placeholder="Enter Fee" step="500" min="0">
            </div>
              <div class="col-md-4">
                <input type="number" name="uni_fee[<?= $value['id'] ?>]" value="<?= $uni_fee?>" class="form-control"
                    <?= $disabled ?> placeholder="Enter University Fee" step="500" min="0">
            </div>
        <?php }
        $studentLedgerfee = $conn->query("SELECT * FROM Student_Ledgers where Student_ID = $student_id AND University_ID = $uni_id AND Source = 'Miscellaneous Type Fee' AND Type=1");
        if ($studentLedgerfee->num_rows > 0) {
            $debit_amount = 0;
            $debit_amount_arr = [];
            while ($row = $studentLedgerfee->fetch_assoc()) {
                $debit_amount_arr[] = $row['Fee'];
            }
            $debit_amount = array_sum($debit_amount_arr);

            $creditedFee = $conn->query("SELECT SUM(Fee) as credit_fee FROM Student_Ledgers where Student_ID = $student_id AND University_ID = $uni_id AND Source = 'Miscellaneous Type Fee' AND Type=2");
            $credit_fee_data = $creditedFee->fetch_assoc();
            $credit_fee = !empty($credit_fee_data['credit_fee']) ? $credit_fee_data['credit_fee'] : 0;
            $debit_amount = (int) $debit_amount - (int) $credit_fee;

            $studentCreditedfee = $conn->query("SELECT * FROM Student_Ledgers where Student_ID = $student_id AND Source = 'Miscellaneous Type Fee' AND University_ID = $uni_id AND Type=2");
            if ($studentCreditedfee->num_rows > 0) {
                ?>
                <!-- start paid fee list -->
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
                        <?php $num = 1;
                        while ($feeData = $studentCreditedfee->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $num++ ?></td>
                                <td><?= $feeData['date_of_transation'] ?></td>
                                <td><?= $feeData['mode_of_payment'] ?></td>
                                <td><?= $feeData['utr_no'] ?></td>
                                <td><?= $feeData['Fee'] ?></td>
                                <td><a href="javascript:void(0)" onclick="editPayment(<?= $feeData['ID'] ?>)"><i
                                            class="ri-edit-box-fill text-primary ms-3"></i></a>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
            <!-- end paid fee list -->
            <!-- pay fee -->
         
            
            <input type="hidden" name="total_fee" value="<?= trim((int) $debit_amount) ?>">
            <?php if ($debit_amount !== 0) { ?>
   <span style="color:red;" class="fw-bold">(Pending Fees- <?= "&#8377;" . number_format($debit_amount, 2); ?>)</span>
                <div class="row mt-1">
                    <div class="col-md-8 mt-2">
                        <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
                            Credit Amount
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group form-group-default">
                            <input type="number" min="0" onblur="checkAmount(this.value)" required placeholder="Credit Amount"
                                name="credit_amount" value="" class="form-control" />
                            <span class="error" id="credit_error"></span>
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
                            Date of transaction
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group form-group-default">
                            <input type="date" placeholder="Date of transaction" required name="date_of_transation"
                                id="date_of_transation" value="" class="form-control" />
                        </div>
                    </div>

                    <div class="col-md-8 mt-2">
                        <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
                            Mode of Payment
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group form-group-default">
                            <select name="mode_of_payment" onchange="showModeType(this.value)" required id="mode_of_payment"
                                class="form-control select2">
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

                <div class="row mt-1 showBankDetailsBox">
                    <div class="col-md-8 mt-2">
                        <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
                            Account Holder Name
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group form-group-default">
                            <select name="holder_name" id="holder_name" class="form-control">
                                <?php echo getBankType($conn, $uni_id); ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-8 mt-2">
                        <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
                            Upload Receipt
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group form-group-default">
                            <input type="file" name="file" id="file" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-8 mt-2">
                        <div class="form-group form-group-default text-black fw-bold" style="border:unset;font-weight:700">
                            Reference or UTR details
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="form-group form-group-default">
                            <input type="text" placeholder="Reference or UTR details" name="utr_no" id="utr_no" required value=""
                                class="form-control" />
                        </div>
                    </div>
                </div>
                <!-- end feee -->
            <?php } else { ?>
                <div id="success-message" class="mt-3">
    <i class="ri-checkbox-circle-fill text-success me-2"></i>
    <span style="color:darkgreen;">Course Fee Submitted!</span>
</div>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>