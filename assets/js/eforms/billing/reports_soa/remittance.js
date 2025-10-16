$(document).ready(function() {
    Inputmask.extendAliases({
        pesos: {
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
            allowMinus: false // default for deposit and deposit
        },
    });

    $("#payment_collected").inputmask("pesos");
});

// Employee & Daterange filter Remittance Filter Vue Instance
const vm_remit_filter = new Vue({
    el: "#remit_filter",
    data: {
        first_selected_employee: null,
        selectedEmployee: null,
        date_range_picked: null,
        date_range_from: null,
        date_range_to: null,
    },
    mounted() {
        this.initializeSelect2('#employee');
        this.initializeDateRangePicker('#date-picker');
    },
    methods: {
        initializeSelect2(el_id) {
            const vm = this;

            $(el_id).select2({
                placeholder: 'SELECT AN OPTION',
                dropdownParent: $("#modal_new_remittance"),
                width: '100%',
                minimumInputLength: 3,
                ajax: {
                    url: baseUrl("eforms/billing/get_employee_collector"),
                    global: false,
                    processResults: function (data) {
                        return data;
                    }
                }
            }).on('change', function () {
                vm.selectedEmployee = $(this).val();

                if (vm.first_selected_employee && vm.first_selected_employee !== vm.selectedEmployee) {
                    vm.clearForm();
                }

                vm.first_selected_employee = vm.selectedEmployee;
            });
        },

        initializeDateRangePicker(el_id) {
            const vm = this;

            $(el_id).daterangepicker({
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

            vm.date_range_picked = null;
        },

        generateReport() {
            const vm = this;

            if (!vm.selectedEmployee || !vm.date_range_picked) {
                toastr.error('Please select Employee and Date Range.', 'Input Required');
                return;
            }

            $.ajax({
                url: baseUrl("eforms/billing/remittance_date_payments_selected/"),
                type: "POST",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    id: vm.selectedEmployee,
                    date: vm.date_range_picked
                },
                success: function(response) {
                    vm_payment_table.loadPayments(response.data);
                },
                error: function (xhr, error, code) {
                    console.log(error);
                }
            });
        },
    }
});

const vm_payment_table = new Vue({
    el: "#payment_table_wrapper",
    data: {
        table: null,
        payments: [], // your main reactive data source
    },
    mounted() {
        this.initializeTable('#tbl-payment_collection');
    },
    methods: {
        initializeTable(el_id) {
            const vm = this;

            vm.table = $(el_id).DataTable({
                dom: 't',
                serverSide: false,
                processing: true,
                deferLoading: 0,
                paging: false,
                scrollY: "300px",
                scrollCollapse: true,
                data: vm.payments, // ← Vue data
                columns: [
                    { data: "account" },
                    { data: "bill_ref" },
                    { data: "acknowledgement_receipt" },
                    { data: "payment_ref" },
                    { data: "type" },
                    {
                        data: "received_amount",
                        render: function (data) {
                            return vm.numberWithCommas(parseFloat(data || 0).toFixed(2));
                        }
                    },
                    {
                        data: "payment_date",
                        render: function (data) {
                            return moment(data).format('MMM DD, YYYY');
                        }
                    },
                    { data: "cashier" },
                ],
                columnDefs: [
                    { orderable: false, targets: '_all' },
                    {
                        targets: [0, 1, 2, 3, 4, 6, 7],
                        createdCell: function (td) {
                            $(td).addClass('text-center');
                        }
                    },
                    {
                        targets: [5],
                        createdCell: function (td) {
                            $(td).addClass('text-right');
                        }
                    }
                ],
                drawCallback: function () {
                    vm.updateFooterTotal();
                }
            });
        },

        numberWithCommas(data) {
            return (+data || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        updateFooterTotal() {
            const api = this.table;

            // Stop if table is not ready or has no data
            if (!api || !api.rows || api.rows().data().length === 0) {
                // Clear footer when no data
                $('#tbl-payment_collection tfoot th').eq(4).html('');
                $('#tbl-payment_collection tfoot th').eq(5).html('');
                $('.payment_collected').val('0.00');
                return;
            }

            const data = api.rows().data().toArray();

            // Filter out rows with deposit_exists === false
            // const filteredData = data.filter(row => row.deposit_exists === false);

            // Calculate total
            const totalPayment = data.reduce(
                (sum, row) => sum + parseFloat(row.received_amount || 0),
                0
            );


            // Update footer totals
            $(api.column(4).footer())
                .removeClass()
                .addClass('text-center')
                .html('<b>Total</b>');

            $(api.column(5).footer())
                .removeClass()
                .addClass('text-right footer-total')
                .html(`<b>${this.numberWithCommas(totalPayment)}</b>`);

            // Update Payment Collected field
            $('.payment_collected').val(totalPayment.toFixed(2));
        },

        // 🚀 Call this method whenever you fetch new data
        loadPayments(newData) {
            this.payments = newData;
            this.table.clear();
            this.table.rows.add(this.payments).draw();
        }
    }
});
