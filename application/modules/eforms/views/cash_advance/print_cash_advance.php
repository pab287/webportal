<div class="m-content">
    <div class="row">
		<div class="col-lg-12">
			<!--begin::Portlet-->
			<div class="m-portlet m-portlet--mobile">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Print Cash Advance
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

					</div>
				</div>
                <div class="m-portlet__body" id="print">
                <form id="print_cash_advance">
                    <div class="row" id="print">
                        <div class="col-md-12" style="border-right: 1px dashed black;">
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td class="text-center"><b><u>APPLICATION FOR CASH ADVANCE</u></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Date</td>
                                    <td width="40%">:&nbsp;<b id="created_dt"></b></td>
                                    <td width="10%">CA #</td>
                                    <td width="30%">:&nbsp;<b v-text="vm_tab1.reference_no"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Name</td>
                                    <td width="80%">:&nbsp;<b v-text="vm_tab1.employee_name"  id="employee"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Position</td>
                                    <td width="80%">:&nbsp;<b  v-text="vm_tab1.position" id="position"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Status</td>
                                    <td width="80%">:&nbsp;<b  v-text="vm_tab1.status" id="emp_status"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Date Hired</td>
                                    <td width="40%">:&nbsp;<b  v-text="vm_tab1.date_start" id="date_employed"></b></td>
                                    <td width="10%">ID #</td>
                                    <td width="30%">:&nbsp;<b v-text="vm_tab1.idno" id="emp_idno"></b></td>
                                </tr>
                            </table>
                            <br>    
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">PAYROLL O/S Balance</td>
                                    <td width="65%">:&nbsp;<b  v-text="vm_tab1.hr_bal_remarks" id="hr_bal_remarks"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Acctg O/S Balance</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.acctg_bal_remarks" id="acctg_bal_remarks"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Last Vale</td>
                                    <td width="65%">:&nbsp;<b id="last_vale"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Amount Applied</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.amt_applied" id="amt_applied"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Purpose</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.purpose" id="purpose"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Allowable</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.amt_applied" id="allowable1"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Amount Deducted</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.amt_to_b_deducted" id="amount_deduct1"></b></td>
                                </tr>
                            </table>
                            <br>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%">Marked Approved By</td>
                                    <td width="50%">&nbsp;</td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                &nbsp;
                                    <td width="50%"><b v-text="vm_tab1.recommend_by" id="marked_approved_by1"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"><b v-text="vm_tab1.recommend_dt"></b></td>
                                    <td width="50%"><b v-text="vm_tab1.amt_approved" id="amount_approved1"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"></td>
                                </tr>
                            </table>

                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%" id="app_date1"></td>
                                    <td width="50%">Amount Approved</td>
                                </tr>
                            </table>
            
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="100%" id="app_rem1">Remarks: <b v-text="vm_tab1.recommend_remarks"></b> </td>
                                </tr>
                            </table>
                   
                            <hr style="margin:15px 0px 0px 0px;border-top: 0px;">
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%"><b v-text="vm_tab1.hr_bal_by" id="hr_bal_by"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"></td>
                                    <td width="50%">&nbsp;</td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%">PAYROLL</td>
                                    <td width="50%">&nbsp;</td>
                                </tr>
                            </table>
                            
                            <hr style="margin:15px 0px 0px 0px;border-top: 0px;">
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%"><b v-text="vm_tab1.acctg_bal_by" id="acctg_bal_by"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"></td>
                                    <td width="50%">&nbsp;<hr style="margin:0px 0px 0px 0px;border-top: 1px solid black;"></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%">Accounting</td>
                                    <td width="50%">Received By</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td class="text-center"><b><u>APPLICATION FOR CASH ADVANCE</u></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Date</td>
                                    <td width="40%">:&nbsp;<b id="created_dt"></b></td>
                                    <td width="10%">CA #</td>
                                    <td width="30%">:&nbsp;<b v-text="vm_tab1.reference_no"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Name</td>
                                    <td width="80%">:&nbsp;<b v-text="vm_tab1.employee_name"  id="employee"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Position</td>
                                    <td width="80%">:&nbsp;<b  v-text="vm_tab1.position" id="position"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Status</td>
                                    <td width="80%">:&nbsp;<b  v-text="vm_tab1.status" id="emp_status"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="20%">Date Hired</td>
                                    <td width="40%">:&nbsp;<b  v-text="vm_tab1.date_start" id="date_employed"></b></td>
                                    <td width="10%">ID #</td>
                                    <td width="30%">:&nbsp;<b v-text="vm_tab1.idno" id="emp_idno"></b></td>
                                </tr>
                            </table>
                            <br>    
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">PAYROLL O/S Balance</td>
                                    <td width="65%">:&nbsp;<b  v-text="vm_tab1.hr_bal_remarks" id="hr_bal_remarks"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Acctg O/S Balance</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.acctg_bal_remarks" id="acctg_bal_remarks"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Last Vale</td>
                                    <td width="65%">:&nbsp;<b id="last_vale"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Amount Applied</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.amt_applied" id="amt_applied"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Purpose</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.purpose" id="purpose"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Allowable</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.amt_applied" id="allowable1"></b></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="35%">Amount Deducted</td>
                                    <td width="65%">:&nbsp;<b v-text="vm_tab1.amt_to_b_deducted" id="amount_deduct1"></b></td>
                                </tr>
                            </table>
                            <br>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%">Marked Approved By</td>
                                    <td width="50%">&nbsp;</td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                &nbsp;
                                    <td width="50%"><b v-text="vm_tab1.approved_by" id="marked_approved_by1"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"><b v-text="vm_tab1.approved_dt"></b></td>
                                    <td width="50%"><b v-text="vm_tab1.amt_approved" id="amount_approved1"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"></td>
                                </tr>
                            </table>

                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%" id="app_date1"></td>
                                    <td width="50%">Amount Approved</td>
                                </tr>
                            </table>
            
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="100%" id="app_rem1">Remarks: <b v-text="vm_tab1.recommend_remarks"></b> </td>
                                </tr>
                            </table>
                   
                            <hr style="margin:15px 0px 0px 0px;border-top: 0px;">
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%"><b v-text="vm_tab1.hr_bal_by" id="hr_bal_by"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"></td>
                                    <td width="50%">&nbsp;</td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%">PAYROLL</td>
                                    <td width="50%">&nbsp;</td>
                                </tr>
                            </table>
                            
                            <hr style="margin:15px 0px 0px 0px;border-top: 0px;">
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%"><b v-text="vm_tab1.acctg_bal_by" id="acctg_bal_by"></b><hr style="margin:0px 20px 0px 0px;border-top: 1px solid black;"></td>
                                    <td width="50%">&nbsp;<hr style="margin:0px 0px 0px 0px;border-top: 1px solid black;"></td>
                                </tr>
                            </table>
                            <table style="font-size:small;" width="100%" border="0">
                                <tr>
                                    <td width="50%">Accounting</td>
                                    <td width="50%">Received By</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
                </div>
        <!-- /.row -->
</div>
</div>
</div>
</div>
    