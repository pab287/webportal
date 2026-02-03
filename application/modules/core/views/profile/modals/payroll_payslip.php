<div class="modal fade" tabindex="-1" id="view-payroll-payslip-modal">
    <div class="modal-dialog">
        <div class="modal-content" id="temp-payslip_content">
            <div class="modal-header">
                <h5 class="modal-title">Payslip</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php $this->load->view("payroll/payroll/modals/content/payslip_content"); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btnClose" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>