// Datatable start    
const init_payment_start_date = moment();
const init_payment_end_date = moment();
let selected_payment_start_date = null;
let selected_payment_end_date = null;

let search_val = "";
const tbl_remittance = $('#tbl-remittance').DataTable({
    dom: 'tlip',
    destroy: true,
    serverSide: true,
    processing: true,
    searching: true,
    paging: true,
    aaSorting: [],
    ajax: {
        url: baseUrl("eforms/billing/remittance_records/"),
        type: "post",
        global: false,
        dataType: "json",
        data: function(d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;

            if (selected_payment_start_date && selected_payment_end_date) {
                d.startDate = moment(selected_payment_start_date).format("YYYY-MM-DD");
                d.endDate = moment(selected_payment_end_date).format("YYYY-MM-DD");
            } else {
                d.startDate = '';
                d.endDate = '';
            }
        },
        error: function (xhr, error, code) {
            console.log(error);
        }
    },
    columns: [
        { data: "ref_no" },
        { 
            data: null, width: "15%", render: function(data, type, row) {
                if (row.date_from === row.date_to) {
                    return moment(row.date_from).format('MMM DD, YYYY');
                } else {
                    return `${moment(row.date_from).format('MMM DD, YYYY')} - ${moment(row.date_to).format('MMM DD, YYYY')}`;
                }
            }
        },
        { 
            data: "payment_collected", render: function(data, type, row) {
                const amount = parseFloat(data).toFixed(2);
                return g_numberWithCommas(amount);
            }
        },
        { 
            data: "deposit", render: function(data, type, row) {
                const amount = parseFloat(data).toFixed(2);
                return g_numberWithCommas(amount);
            }
        },
        { 
            data: "variance", render: function(data, type, row) {
                const amount = parseFloat(data).toFixed(2);
                return g_numberWithCommas(amount);
            }
        },
        { data: "virtual_cashier", width: "20%" },
        { data: "depositor" },
        { 
            data: "deposit_date", render: function(data, type, row) {
                return moment(data).format('MMM DD, YYYY');
            }
        },
        { 
            data: "created_date", render: function(data, type, row) {
                return moment(data).format('MMM DD, YYYY');
            }
        },
        { data: null, width: "5%" },
    ],
    columnDefs: [
        {
            orderable: false,
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8]
        },
        {
            targets: [0, 1, 5, 6, 7, 8, 9],
            className: "text-center",
        },
        {
            targets: [2, 3, 4],
            className: "text-right",
        },
        {
            data: null,
            defaultContent: "",
            targets: -1,
            orderable: false,
            render: function (row) { return itemDatatableActions(row); },
        }, 
        {
            targets: "_all",
            createdCell: function (td) {
                $(td).addClass('v-middle');
            }
        }
    ],
    createdRow: function (row, data, dataIndex) {
        // Check for variance
        const variance = parseFloat(data.variance);
        
        if (variance > 0) {
            $(row).css('background-color', '#ffcd4a').addClass('has-variance short-dep'); // light yellow
        } else if (variance < 0) {
            $(row).css('background-color', '#00e0fb').addClass('has-variance excess-dep'); // light green
        } else {
            $(row).css('background-color', ''); // no background
        }

        // =============================================================

        // Check for archives
        const is_archived = data.is_archive;

        if (is_archived == 1) {
            $(row).css('background-color', '#f4516c').addClass('is_archived');
        }
    },
    "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;
        let variance_total_collected = 0;
        let variance_total_deposit = 0;
        let variance_total = 0;

        // Total Payment
        const total_collection = api
            .column( 2 )
            .data()
            .reduce( function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0 );
        variance_total_collected = total_collection;
        $( api.column( 1 ).footer() ).html('<b class="d-block text-center">Total</b>');
        $( api.column( 2 ).footer() ).html('<b class="d-block text-right">'+g_numberWithCommas(parseFloat(total_collection).toFixed(2))+'</b>');

        // Total Balance Covered
        const total_deposit = api
            .column( 3 )
            .data()
            .reduce( function (a, b) {
                return parseFloat(a) + parseFloat(b);
            }, 0 );
        variance_total_deposit = total_deposit;
        $( api.column( 3 ).footer() ).html('<b class="d-block text-right">'+g_numberWithCommas(parseFloat(total_deposit).toFixed(2))+'</b>');

        variance_total = total_collection - total_deposit;
        $( api.column( 4 ).footer() ).html('<b class="d-block text-right">'+g_numberWithCommas(parseFloat(variance_total).toFixed(2))+'</b>');
    }
});
// Datatable end

// Generate Knock off balance Start
function g_numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
// Generate Knock off balance End

// Action in datatable Start
function itemDatatableActions(row) {
    const is_archived = row.is_archive;

    if (is_archived != 1) { // if NOT archived
        	if (row) {
            var tempHtml = "---";

            tempHtml = `<div class="dropdown">
                            <a href="#" class="btn m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill" data-toggle="dropdown"> 
                                <i class="la la-ellipsis-h"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" data-toggle='modal' data-target='#modal_view_remittance' href="javascript:void(0);" id='view_remit_modal' data-id='${row.id}'><i class="la la-eye"></i>View</a>
                                <a class="dropdown-item" style="color: #FF8383;" href="javascript:void(0);" onclick='modalArchive( `+ row.id +`,`+`\"`+ row.ref_no + `\" )'><i class="la la-trash" style="color: #FF8383;"></i> Cancel</a>
                            </div>
                        </div>`;
            return tempHtml;
        } else { 
            return false; 
        }
    }
}
// Action in datatable End

function modalArchive(id, name){
    const temp = `<p>Are you sure you wan't to cancel <strong class='m--font-boldest'>${name}</strong>?</p>`;
    $('#m_archived').modal('show');
    $('#archive_text').empty().html(temp);
    $("#m_archived input[name=id]").val(id);
    $("#m_archived input[name=archive_ref_no]").val(name);
}

function cancel_remit(){
    const remittance_id = document.getElementById('archive_id').value;
    const ref_no = document.getElementById('archive_ref_no').value;

    $.ajax({
        url: baseUrl("eforms/billing/archive_remittance"),
        type: 'post',
        data: { csrf_token: _csrf_hash, id: remittance_id, ref_no:ref_no },
        success: function (data) {
            if(data.status){
                toastr.success('Remittance archived successfully.', 'Success');

                $('#m_archived').modal('hide');
                tbl_remittance.ajax.reload();
            }
        },
        error: function(data){
            toastr.error("Please check your internet connection.", "Connection error");
        }
    });
}

$(document).on('click', '#view_remit_modal', function() {
    const remittance_id = $(this).attr('data-id');

    $.ajax({
        url: baseUrl("eforms/billing/get_remittance_details/"),
        type: "POST",
        dataType: "json",
        data: {
            csrf_token: _csrf_hash,
            remittance_id: remittance_id,
        },
        success: function(data) {
            const d = data;

            Object.assign(vm_remittance_view.$data, {
                ref_no: d.ref_no,
                cashier: d.cashier,
                depositor: d.depositor,
                date_deposit: moment(d.deposit_date).format('MMM DD, YYYY'),
                total_collection: g_numberWithCommas(d.payment_collected),
                deposit: g_numberWithCommas(d.deposit),
                variance: g_numberWithCommas(d.variance),
                date_range: d.date_range_selected,
                remarks_text: d.remarks,
                daily_cash_report: d.collection.daily_cash_report,
                grand_total_per_cashier: d.collection.grand_total_per_cashier.cashier
            });

            switch (Math.sign(Number(d.variance) || 0)) {
                case 1:
                    vm_remittance_view.variance_color = '#ffcd4a';
                    vm_remittance_view.variance_label_text_color = '#fff';
                    vm_remittance_view.variance_value_text_color = '#fff';
                    break;
                case -1:
                    vm_remittance_view.variance_color = '#00e0fb';
                    vm_remittance_view.variance_label_text_color = '#8E8E93';
                    vm_remittance_view.variance_value_text_color = '#7f7f83';
                    break;
                default:
                    vm_remittance_view.variance_color = '';
                    vm_remittance_view.variance_label_text_color = '';
                    vm_remittance_view.variance_value_text_color = '';
            }
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            console.log("XHR:", xhr.responseText);
        }
    });
});

$('#generalSearch').donetyping(function(callback) {
    search_val = $(this).val();
    tbl_remittance.ajax.reload();
});

$('#billing-date-picker').daterangepicker({
    buttonClasses: 'm-btn btn',
    applyClass: 'btn-primary',
    cancelClass: 'btn-secondary',
    startDate: init_payment_start_date,
    endDate: init_payment_end_date,
    format: "MMM. DD, YYYY"
}, function (start, end, label) {
    selected_payment_start_date = start;
    selected_payment_end_date = end;

    let _label = "<strong>" + start.format("MMM. DD, YYYY") + "</strong> to <strong>" + end.format("MMM. DD, YYYY") + "</strong>";

    $(".selected-filter", $('#billing-date-picker')).html(_label);
    tbl_remittance.ajax.reload();
}).on('cancel.daterangepicker', function(ev, picker) {
    $(".selected-filter", $('#billing-date-picker')).text('Date Filter');

    selected_payment_start_date = null;
    selected_payment_end_date = null;

    tbl_remittance.ajax.reload();
});

// Reset new remittance form vue instances
// ===============================================================================
$('#modal_new_remittance').on('hidden.bs.modal', function () {
    vm_remit.clear_form_instances();
});


// Reset view remittance form vue instances
// ===============================================================================
$('#modal_view_remittance').on('hidden.bs.modal', function () {
    Object.assign(vm_remittance_view.$data, {
        ref_no: '##############',
        cashier: '',
        depositor: '*************',
        date_deposit: '0000-00-00',
        total_collection: 0.00,
        deposit: 0.00,
        variance: 0.00,
        variance_color: '',
        variance_label_text_color: '#8E8E93',
        variance_value_text_color: '#7f7f83',
        date_range: '0000-00-00 to 0000-00-00',
        remarks_text: '',
        daily_cash_report: [],
        grand_total_per_cashier: [],
    });

    $('#remarks_wrap').hide();
});

// Vue Instance
// ===============================================================================
const vm_remit = new Vue({
    el: '#remittance_input',
    data: {
        cashiers: [],
        date_range_picked: null,
        date_range_from: null,
        date_range_to: null,

        deposit_amount: null,
        payment_collected: null,
        variance: null,
        deposit_date: null,

        payment_ids: [],
    },
    mounted() {
        this.initialize_select2();
        this.initialize_date_picker();
        this.initialize_date_range_picker();
        this.initialize_input_mask(['deposit', 'payment_collected']);
        this.initialize_input_mask_negative(['variance']);
    },
    watch: {
        deposit_amount() {
            this.calculated_variance();
        },
        payment_collected() {
            this.calculated_variance();
        }
    },
    methods: {
        initialize_select2() {
            const vm = this;

            $('#employee').select2({
                placeholder: 'SELECT AN OPTION',
                dropdownParent: $("#modal_new_remittance"),
                width: '100%',
                minimumInputLength: 3,
                allowClear: true,
                ajax: {
                    url: baseUrl("eforms/billing/get_employee_collector"),
                    global: false,
                    processResults: function (data) {
                        return data;
                    }
                }
            }).on('change', function () {
                vm.cashiers = $(this).val();
            }).on('select2:select', function(e) {
                vm.cashiers = $(this).val() || [];
            }).on('select2:unselect', function(e) {
                vm.cashiers = $(this).val() || [];
            });
        },

        initialize_date_range_picker() {
            const vm = this;

            vm.date_range_picked = null;

            $('#date-picker').daterangepicker({
                autoUpdateInput: false,
                buttonClasses: 'm-btn btn',
                applyClass: 'btn-primary',
                cancelClass: 'btn-secondary',
                maxDate: moment().format('MM/DD/YYYY'),
                locale: {
                    format: 'MM/DD/YYYY'
                }
            }).on('apply.daterangepicker', function (ev, picker) {
                const tempStartDate = picker.startDate.format('MMM DD, YYYY');
                const tempEndDate = picker.endDate.format('MMM DD, YYYY');
                vm.date_range_picked = tempStartDate + ' - ' + tempEndDate;

                vm.date_range_from = picker.startDate.format('YYYY-MM-DD');
                vm.date_range_to = picker.endDate.format('YYYY-MM-DD');
            }).on('cancel.daterangepicker', function (ev, picker) {
                vm.date_range_picked = null;
                vm.date_range_from = null;
                vm.date_range_to = null;            
            });
        },

        initialize_date_picker() {
            const vm = this;
            $('#deposit_date').datepicker({
                todayHighlight: true,
                orientation: "top left",
                templates: {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                },
                format: "mm/dd/yyyy",     // Format for month/day/year
                viewMode: "days",         // Default view to show calendar days
                minViewMode: "days",      // Minimum selectable view is days
                autoclose: true,
                endDate: new Date(),
            }).on('changeDate', function (e) {
                vm.deposit_date = $(this).val(); // ← update Vue data
            });
        },

        initialize_input_mask(ids) {
            Inputmask.extendAliases({
                pesos: {
                    groupSeparator: ".",
                    alias: "numeric",
                    placeholder: "0",
                    autoGroup: true,
                    digits: 2,
                    digitsOptional: false,
                    clearMaskOnLostFocus: false,
                    autoUnmask: true,
                    rightAlign: true,
                    inputmode: "decimal",
                    allowMinus: false,
                    oncomplete: function () {
                        const event = new Event('input', { bubbles: true });
                        this.dispatchEvent(event);
                    },
                    onincomplete: function () {
                        const event = new Event('input', { bubbles: true });
                        this.dispatchEvent(event);
                    },
                    oncleared: function () {
                        const event = new Event('input', { bubbles: true });
                        this.dispatchEvent(event);
                    }
                },
            });

            ids.forEach(id => {
                Inputmask("pesos").mask(document.getElementById(id));
            });
        },

        initialize_input_mask_negative(ids) {
            Inputmask.extendAliases({
                pesos_negative: {
                    groupSeparator: ".",
                    alias: "numeric",
                    placeholder: "0",
                    autoGroup: true,
                    digits: 2,
                    digitsOptional: false,
                    clearMaskOnLostFocus: false,
                    autoUnmask: false,
                    rightAlign: true,
                    inputmode: "decimal",
                    allowMinus: true, // allow negative values for clearing_entry
                    oncomplete: function () {
                        const event = new Event('input', { bubbles: true });
                        this.dispatchEvent(event);
                    },
                    onincomplete: function () {
                        const event = new Event('input', { bubbles: true });
                        this.dispatchEvent(event);
                    },
                    oncleared: function () {
                        const event = new Event('input', { bubbles: true });
                        this.dispatchEvent(event);
                    }
                }
            });

            ids.forEach(id => {
                Inputmask("pesos_negative").mask(document.getElementById(id));
            });
        },

        calculated_variance() {
            const deposit = parseFloat(this.deposit_amount) || 0;
            const collected = parseFloat(this.payment_collected) || 0;
            this.variance = (collected - deposit).toFixed(2);
        },

        clear_form_instances() {
            const vm = this;

            vm.cashiers = [];
            vm.date_range_picked = null;
            vm.date_range_from = null;
            vm.date_range_to = null;
            
            vm.deposit_amount = null;
            vm.payment_collected = null;
            vm.variance = null;
            vm.deposit_date = null;

            vm.payment_ids = [];

            // Clear remarks
            vm_save_remit.remarksApproved = false;
            vm_save_remit.remarksText = '';

            // Clear cash report
            vm_cash_report.daily_cash_report = null;
            vm_cash_report.total_per_cashier = null;

            // Clear select2
            $('#remit_filter #employee').val(null).trigger('change');

            // Set daterangepicker ui to normal
            $('#remit_filter #date-picker').data('daterangepicker').setStartDate(moment());
            $('#remit_filter #date-picker').data('daterangepicker').setEndDate(moment());
        },

        generateReport() {
            const vm = this;

            vm_cash_report.daily_cash_report = null;
            vm_cash_report.total_per_cashier = null;

            if (!vm.cashiers || !vm.date_range_picked) {
                toastr.error('Please select Employee and Date Range.', 'Input Required');
                return;
            }

            $.ajax({
                url: baseUrl("eforms/billing/remittance_date_payments_selected/"),
                type: "POST",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    id: vm.cashiers,
                    date: vm.date_range_picked
                },
                success: function(response) {
                    vm_cash_report.daily_cash_report = response.daily_cash_report || [];
                    vm_cash_report.total_per_cashier = response.grand_total_per_cashier.cashier || [];

                    vm.payment_collected = response.grand_total_per_cashier.totalCash || 0;
                    vm.payment_ids = response.all_payment_ids || [];

                    /**
                     * if no cashier selected, the backend will return all cashier ids from payments within the default or selected date range
                     */
                    if (vm.cashiers.length == 0) {
                        vm.cashier = response.cashier_ids;
                    }
                },
                error: function (xhr, error, code) {
                    console.log(error);
                }
            });
        },
    }
});

const vm_cash_report = new Vue({
    el: "#daily_cash_report_app",
    data: {
        daily_cash_report: null,
        total_per_cashier: null,
    },
    methods: {
        numberWithCommas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
    }
});

const vm_save_remit = new Vue({
    el: "#remit_form_btn",
    data: {
        remarksApproved: false,
        remarksText: '',
    },
    computed: {
        can_save() {
            return (
                vm_cash_report.daily_cash_report !== null &&
                vm_cash_report.total_per_cashier !== null
            );
        }
    },
    methods: {
        save_remittance() {
            const data = vm_remit.$data;
            const vm = this;
    
            // Verify everything first
            if (data.deposit_amount === null || data.deposit_date === null || data.cashiers === null || data.date_range_picked === null) {
                toastr.error('Please fill in required fields. *', 'Input Required *');
                return;
            }

            // ALWAYS use this.can_save
            if (!this.can_save) {
                toastr.error('Please generate data first !', 'Generate Report Required ! ! !');
                return;
            }

            // Check for variance if short or over
            const variance = data.variance;

            if (variance > 0 && !vm.remarksApproved) {
                vm.openRemarks();
                return;
            }

            $.ajax({
                url: baseUrl("eforms/billing/save_remit/"),
                type: "POST",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    cashiers: data.cashiers,
                    date_range_selected: data.date_range_picked,
                    date_range_from: data.date_range_from,
                    date_range_to: data.date_range_to,
                    
                    deposit_amount: data.deposit_amount,
                    payment_collected: data.payment_collected,
                    variance: data.variance,
                    deposit_date: data.deposit_date,

                    payment_ids: data.payment_ids,

                    remarks: vm.remarksText,
                },
                success: function(response) {
                    const res = response || [];
                    if (res.status) {
                        toastr.success('Remittance saved successfully.', 'Success');

                        vm_remit.clear_form_instances();

                        // Close Modal
                        $('#modal_new_remittance').modal('hide');
                    } else {
                        toastr.error(res.message || 'Failed to save remittance.', 'Error');
                    }

                    tbl_remittance.ajax.reload();
                },
                error: function (xhr, error, code) {
                    toastr.error(res.message || 'Failed to save remittance. (Ajax Error)', 'Error');
                }
            });
        },

        openRemarks() {
            const minChars = 30;

            Swal.fire({
                title: "Remarks",
                input: "textarea",
                html: `
                    <span class="text-danger">
                        Deposit does not match the total payment collected. Please provide a remarks.
                    </span>
                    <div style="margin-top:8px; font-size:12px; color:#666;">
                        <span id="charCount">0</span> / ${minChars} required
                    </div>
                `,
                inputAttributes: {
                    autocapitalize: "off"
                },
                showCancelButton: false,
                confirmButtonText: "Proceed",
                confirmButtonColor: "#36a3f7",
                allowOutsideClick: true,
                target: document.querySelector('.modal.show') || document.body,
                didOpen: () => {
                    const textarea = Swal.getInput();
                    const charCount = document.getElementById("charCount");
                    const saveBtn = Swal.getConfirmButton();

                    // Disable save initially
                    saveBtn.disabled = true;

                    textarea.addEventListener("input", () => {
                        const len = textarea.value.length;
                        charCount.textContent = len;

                        // Enable save only if min length is reached
                        saveBtn.disabled = len < minChars;
                    });
                },
                preConfirm: (value) => {
                    if (value.length < minChars) {
                        Swal.showValidationMessage(`Remarks must be at least ${minChars} characters long`);
                    } else {
                        this.remarksApproved = true;
                        this.remarksText = value.trim(); // store in variable

                        // Trigger submit when remarks is approved
                        this.save_remittance();
                    }
                }
            });
        },
    }
});

const vm_remittance_view = new Vue({
    el: "#remittance_details",
    data: {
        ref_no: '##############',
        cashier: '',
        depositor: '*************',
        date_deposit: '0000-00-00',
        total_collection: 0.00,
        deposit: 0.00,
        variance: 0.00,
        variance_color: '',
        variance_label_text_color: '',
        variance_value_text_color: '',
        date_range: '0000-00-00 to 0000-00-00',
        remarks_text: '',
        daily_cash_report: [],
        grand_total_per_cashier: [],
    },
    methods: {
        numberWithCommas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
    }
});