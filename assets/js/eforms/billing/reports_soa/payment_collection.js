const exportBtns = new Vue({
    el: '#exportButtons',
    data: {
        collection: [],
        totalCollection: 0,
        totalCancelled: 0,
        totalPaymentCount: 0,
    },
    methods: {
        exportExcel(type = 'xlsx') {
            if (!this.collection.length) {
                toastr.error("No data selected.", "Warning");
                return;
            }

            const upper = v =>
                (v === null || v === undefined)
                ? ''
                : typeof v === 'string'
                    ? v.toUpperCase()
                    : v;

            let rows = [];

            this.collection.forEach(group => {
                group.payments.forEach(p => {
                    rows.push({
                        "AR #": upper(p.acknowledgement_receipt),
                        "Applied Payment Date": upper(p.applied_payment_date),
                        CASHIER: upper(group.cashier),
                        ACCOUNT: upper(p.account),
                        "Bill #": upper(p.bill_ref),
                        "Payment #": upper(p.payment_ref),
                        TYPE: upper(p.type),
                        AMOUNT: p.received_amount,          // keep numeric
                        "Balance Covered": p.balance_covered,  // keep numeric
                        "Payment Date": upper(p.payment_date),
                        CANCELLED: p.is_archived == 1 ? "YES" : "NO"
                    });
                });
            });

            const worksheet = XLSX.utils.json_to_sheet(rows);
            const workbook = XLSX.utils.book_new();

            XLSX.utils.book_append_sheet(workbook, worksheet, "COLLECTIONS");
            XLSX.writeFile(workbook, `PAYMENT_COLLECTION.${type}`);

            saveExportLogs(`Accounts - Export ${type.toUpperCase()}`);
        },

        exportPDF() {
            if (!this.collection.length) {
                toastr.error("No data selected.", "Warning");
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            let startY = 10;

            const pageWidth = doc.internal.pageSize.getWidth();

            // TITLE
            doc.setFontSize(14);
            doc.text(
                'PAYMENT COLLECTION REPORT',
                pageWidth / 2,
                startY,
                { align: 'center' }
            );
            startY += 8;

            // GENERATED DATE
            doc.setFontSize(10);
            doc.text(
                `Generated Date: ${new Date().toLocaleString()}`,
                pageWidth / 2,
                startY,
                { align: 'center' }
            );
            startY += 10;

            doc.setFontSize(10);
            doc.text(`Total cancelled: ${this.totalCancelled}`, 14, startY);
            startY += 10;

            doc.setFontSize(10);
            doc.text(`Total payment(s): ${this.totalPaymentCount}`, 14, startY);
            startY += 10;

            doc.setFontSize(10);
            doc.text(`Total collection: ${this.numberWithCommas(this.totalCollection)}`, 14, startY);
            startY += 10;

            this.collection.forEach(group => {
                doc.setFontSize(11);
                doc.text(`CASHIER: ${group.cashier.toUpperCase()} - TOTAL CANCELLED: ${group.archived_count} - TOTAL PAYMENT(S): ${group.payments.length - group.archived_count} - TOTAL COLLECTION: ${this.numberWithCommas(group.total_cash)}`, 14, startY);
                startY += 5;

                const body = group.payments.map(p => ([
                    p.acknowledgement_receipt,
                    p.applied_payment_date,
                    p.account,
                    p.bill_ref,
                    p.payment_ref,
                    p.type,
                    p.received_amount,
                    p.balance_covered,
                    p.payment_date,
                    p.is_archived,
                ]));

                doc.autoTable({
                    startY,
                    head: [[
                        'AR #', 'AP Date', 'Account', 'Bill #', 'Payment #', 'Type', 'Amount', 'Bal Covered', 'Payment Date'
                    ]],
                    body,
                    styles: { 
                        fontSize: 8,
                        halign: 'center'
                    },

                    // CENTER HEADERS
                    headStyles: {
                        halign: 'center'
                    },

                    // COLUMN-SPECIFIC ALIGNMENT
                    columnStyles: {
                        6: { halign: 'right' }, // Amount (received_amount)
                        7: { halign: 'right' }, // Balance Covered
                        9: { cellWidth: 0 }     // hide IS_ARCHIVED column
                    },

                    // This block is for changing row color based on IS_ARCHIVED value
                    didParseCell: function (data) {
                        // Body rows only
                        if (data.section === 'body') {
                            const isArchived = data.row.raw[9] == 1; // index of is_archived

                            if (isArchived) {
                                data.cell.styles.fillColor = [220, 53, 69]; // Bootstrap danger red
                                data.cell.styles.textColor = 255;
                            }
                        }

                        // Hide IS_ARCHIVED column
                        if (data.column.index === 9) {
                            data.cell.text = '';
                        }
                    },
                });

                startY = doc.lastAutoTable.finalY + 10;
            });

            doc.save('payment_collection.pdf');
            saveExportLogs('Accounts - Export PDF');
        },

        numberWithCommas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
    }
});

const payment_collected = new Vue({
    el: '#payment-collected-list',
    data: {
        collection: [],
    }, 
    methods: {
        numberWithCommas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        date_format(data) {
            return moment(data).format('MMM DD, YYYY');
        }
    }
});

const totalCollection = new Vue({
    el: '#total-collection',
    data: {
        totalCollection: 0,
        totalCancelled: 0,
        totalPaymentCount: 0,
    }, 
    methods: {
        numberWithCommas(data) {
            return data.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
    }
});

const payment_collection_app = new Vue({
    el: '#payment-collection-app',
    data: {
        employee_ids: [],
        date_range: null,
        loading: false
    }, 
    mounted() {
        this.fetchPaymentCollectionReport();
    },
    methods: {
        fetchPaymentCollectionReport() {
            this.loading = true;

            $.ajax({
                url: baseUrl("eforms/billing/get_payment_collection_report/"),
                type: "post",
                dataType: "json",
                data: {
                    csrf_token: _csrf_hash,
                    ids: this.employee_ids,
                    date: this.date_range
                },
                success: function(data) {
                    payment_collected.collection = data.data;

                    exportBtns.collection = data.data;

                    totalCollection.totalCollection = data.totalCash;
                    totalCollection.totalCancelled = data.totalArchived;
                    totalCollection.totalPaymentCount = data.recordsTotal - data.totalArchived;

                    exportBtns.totalCollection = data.totalCash;
                    exportBtns.totalCancelled = data.totalArchived;
                    exportBtns.totalPaymentCount = data.recordsTotal - data.totalArchived;
                },
                error: function(xhr, error, code) {
                    console.log(error);
                },
                complete: () => {
                    this.loading = false;
                }
            });
        },

        generateReport() {
            console.log("Generating report for Employee ID:", this.employee_ids, "Date Range:", this.date_range);

            this.fetchPaymentCollectionReport();
        }
    },
    watch: {
        employee_id() {
            this.fetchPaymentCollectionReport();
        },
        date_range() {
            this.fetchPaymentCollectionReport();
        }
    }
});

// Employee select
$("#employee").select2({
    placeholder: 'SELECT A CASHIER(S)',
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
}).on('select2:select', function(e) {
      payment_collection_app.employee_ids = $(this).val() || [];
}).on('select2:unselect', function(e) {
      payment_collection_app.employee_ids = $(this).val() || [];
});

var selected_date;
let tempFormat;

// Date range select
$("#date-picker").daterangepicker({
    buttonClasses: 'm-btn btn',
    applyClass: 'btn-primary',
    cancelClass: 'btn-secondary',
    locale: { format: 'MM/DD/YYYY' },
    endDate: moment(),
    maxDate: moment()
}).on('apply.daterangepicker', function (ev, picker) {
    const tempStartDate = picker.startDate.format('MMM DD, YYYY');
    const tempEndDate = picker.endDate.format('MMM DD, YYYY');
    const tempFormat = tempStartDate + ' - ' + tempEndDate;

    $("#date-range").val(tempFormat);
    payment_collection_app.date_range = tempFormat;
});

function numberWithCommas(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function saveExportLogs(export_){
  $.ajax({
      url: baseUrl("eforms/billing/save_export_logs"),
      type: 'post',
      data: { csrf_token: _csrf_hash, export_: export_ },
      success: function (data) {}
  });
}