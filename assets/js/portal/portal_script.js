$(document).on("click", ".module_redirect", function () {
    var self = $(this);
    var dataId = self.data("id");
    $.ajax({
        url:  siteUrl("portal/redirect_to_module"),
        type: "post",
        dataType: "json",
        data: { id: dataId, csrf_token: _csrf_hash },
        success: function (json) {
            if (json.response) {
                window.location.href = json.redirect;
            } else {
                toastr.warning("Nothing to redirect!", "Portal Redirect", 5000);
            }
        }
    });
});

if (typeof idleTimerTrigger !== "undefined" && typeof idleTimerTrigger == "function") {
    idleTimerTrigger();
}

if(typeof getAcctgcount !== "undefined" && typeof getAcctgcount == "function"){
    getAcctgcount();
}

if(window.location == siteUrl("portal/index")){
    var vmTab1 = new Vue({
        el: "#portal_notifications",
        data: { 
            vm_tab1: {show:false,}, 
            vm_travel_order: {show:false,}, 
            vm_acct: {show:false,}, 
            vm_borrowing: {show:false,}, 
            vm_overtime: {show:false,}, 
            vm_transmittal: {show:false,}, 
            vm_shipping : {show:false,},
            vm_ca : {show:false,},
            payslip: {show:false,data:[]},
            deductions: {show:false, data:[]}
        },
        mounted(){
            this.getPayslip();
            this.getDeductions();
        }, methods: {
            styles: {
                width: '50%',
            }, getUnapprovedLoa(){
                $.ajax({
                    url:  siteUrl("portal/get_unapproved_loa"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_tab1 = Object.assign({}, json);
                        vmTab1.vm_tab1.show = true;
                    }
                });
            }, getTravelOrder(){
                $.ajax({
                    url:  siteUrl("portal/get_to_recommendation"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_travel_order = Object.assign({}, json);
                        vmTab1.vm_travel_order.show = true;
                    }
                });
            }, getAccountability(){
                $.ajax({
                    url:  siteUrl("portal/get_accountability"),
                    type: "post",
                    dataType: "json",
                    data: {
                        csrf_token : _csrf_hash,
                        company : $("#companySelect option:selected").val(),
                    },
                    success: function (json) {
                        vmTab1.vm_acct = Object.assign({}, json);
                        vmTab1.vm_acct.show = true;
                    }
                });
            }, getBorrowing(){
                $.ajax({
                    url:  siteUrl("portal/get_borrowing"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_borrowing = Object.assign({}, json);
                        vmTab1.vm_borrowing.show = true;
                    }
                });
            }, getOvertime(){
                $.ajax({
                    url:  siteUrl("portal/get_overtime"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_overtime = Object.assign({}, json);
                        vmTab1.vm_overtime.show = true;
                    }
                });
            }, getTransmittal(){
                $.ajax({
                    url:  siteUrl("portal/get_transmittal"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_transmittal = Object.assign({}, json);
                        vmTab1.vm_transmittal.show=true;
                    }
                });
            }, getShipping(){
                $.ajax({
                    url:  siteUrl("portal/get_shipping"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_shipping = Object.assign({}, json);
                        vmTab1.vm_shipping.show = true;
                    }
                });
            }, getCashadvance(){
                $.ajax({
                    url:  siteUrl("portal/get_cashadvance"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.vm_ca = Object.assign({}, json);
                        vmTab1.vm_ca.show = true;
                    }
                });
            }, getPayslip(){
                $.ajax({
                    url:  siteUrl("portal/get_payslip"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        vmTab1.payslip.data = Object.assign({}, json);
                        vmTab1.payslip.show = false;
                    }
                });
            }, formatDate(date){
                if(!date) return "---";
                return moment(date).format("MMM DD, YYYY");
            }, formatDateCoverage(start, end) {
                if(!start || !end) return "---";
                const startMoment = moment(start);
                const endMoment = moment(end);
            
                if (startMoment.month() === endMoment.month() && startMoment.year() === endMoment.year()) {
                    return `${startMoment.format("MMM DD")} - ${endMoment.format("DD, YYYY")}`;
                } else if (startMoment.year() === endMoment.year()) {
                    return `${startMoment.format("MMM DD")} - ${endMoment.format("MMM DD, YYYY")}`;
                } else {
                    return `${startMoment.format("MMM DD, YYYY")} - ${endMoment.format("MMM DD, YYYY")}`;
                }
            }, formatCurrency(amount){
                if(!amount) return "₱0.00";
                return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(amount);
            }, getDeductions() {
                $.ajax({
                    url:  siteUrl("portal/get_deductions"),
                    type: "get",
                    dataType: "json",
                    success: function (json) {
                        let tempLoan = [];
                        const tempCreatedAdjustments = json.data.created_adjustments;

                        if (json.data.loans.length > 0) {
                            $.each(json.data.loans, function (index, item) {
                                if (item.loan_name.toLowerCase() != 'charges' && item.loan_name.toLowerCase() != 'under deduction' && item.loan_name.toLowerCase() != 'medical loan') {
                                    var temp_amount = parseFloat(item.amount_due.replace(/,/g, ''));
        
                                    // for adding cash advance with loan adjustments
                                    if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                                        const created_adjustments = tempCreatedAdjustments.split(",");
                                        var tempAdj = 0;
                                        created_adjustments.forEach((row, i) => {
                                            const temp_adjustment = row.split("||");
                                            const adj_type = parseInt(temp_adjustment[2]);
                                            const temp_status = parseInt(temp_adjustment[3]);
                                            let _temp = parseFloat(item.amount_due);
                                            if (adj_type == 1) {
                                                _temp = parseFloat(temp_amount) + parseFloat(temp_adjustment[1]);
                                            } else {
                                                _temp = parseFloat(temp_amount) - parseFloat(temp_adjustment[1]);
                                            }
    
                                            tempAdj = _temp;
                                            _temp = _temp;
    
                                            if (typeof item.loan_name !== "undefined" && item.loan_name.toLowerCase() == 'cash advance') {
                                                if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                                    temp_amount = _temp;
                                                }
                                            } else {
                                                // includes loan adjustments when employee has no cash advance
                                                if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                                    if (!tempLoan.some(el => el.loan_name === 'CASH ADVANCE')) {
                                                        tempLoan.push({
                                                            'loan_name' : 'CASH ADVANCE',
                                                            'amount_due' : temp_adjustment[1],
                                                            'loan_type' : adj_type
                                                        });
                                                    }
                                                }
                                            }
                                        });
                                    }
        
                                    tempLoan.push({
                                        'loan_name' : item.loan_name,
                                        'amount_due' : temp_amount,
                                        'loan_type' : item.loan_type
                                    });
                                }
        
                                // for adding the charges to Other Deductions
                                if (item.loan_name.toLowerCase() == 'charges' || item.loan_name.toLowerCase() == 'under deduction' || item.loan_name.toLowerCase() == 'medical loan') {
                                    vmPayslipContent.row.adjustment_deductions.push({
                                        'label' : item.loan_name,
                                        'display_value' : item.amount_due,
                                        'value' : item.amount_due,
                                        'adj_type' : 0
                                    });
        
                                    totalLoan = totalLoan - parseFloat(item.amount_due.replace(/,/g, ''));
                                }
                            });
                        } else {
                            if (typeof tempCreatedAdjustments !== "undefined" && tempCreatedAdjustments) {
                                const created_adjustments = tempCreatedAdjustments.split(",");
                                var tempAdj = 0;
                                created_adjustments.forEach((row, i) => {
                                    const temp_adjustment = row.split("||");
                                    const adj_type = parseInt(temp_adjustment[2]);
                                    const temp_status = parseInt(temp_adjustment[3]);
                                    let _temp = parseFloat(temp_adjustment[1]);
        
                                    tempAdj = _temp;
                                    _temp = _temp;
        
                                    if (temp_adjustment[0] == "LOAN" && temp_status === 1) {
                                        tempLoan.push({
                                            'loan_name' : 'CASH ADVANCE',
                                            'amount_due' : _temp,
                                            'loan_type' : adj_type
                                        });
                                    }
                                });
                            }
                        }

                        console.log(tempLoan);

                        json.data.loans = tempLoan;
                        vmTab1.deductions.data = Object.assign({}, json.data);
                        vmTab1.deductions.show = false;
                    }
                });
            }
        }
    });
}


