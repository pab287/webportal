const searchField = $('#search-archived-loans');
let tblArchivedLoans = $('#table-archived-loans')
    .DataTable({
        dom: 'frtlip',
        serverSide: true,
        processing: true,
        searching: false,
        ordering: true,
        order: [[1, 'asc']],
        ajax: {
            url: baseUrl('hris/archive/get_archived_loans'),
            type: 'post',
            dataType: 'json',
            data: function (d) {
                d.csrf_token = _csrf_hash;
                d.search['value'] = searchField.val();
            }
        },
        columns: [
            {
                data: "loan_name",
                width: "*",
                render: function (data, type, row) {
                    const tempDate = moment(row.created_at).format("YYYY-MM-DD");
                    let tempHtml = `<p class="mb-1 m--font-bolder">${data} <small class='m--font-boldest m--font-primary'>[ ${row.employee_name} ]</small></p>
                    <p class='m-0'><small><span class="m--font-bolder">Created By:</span> ${row.created_by_name}</small></p>
                    <p class='m-0'><small><span class="m--font-bolder">Created Date:</span> ${tempDate}</small></p>`;
                    return tempHtml;
                }
            },
            {
                data: "amount",
                className: "text-right",
                width: "10%",
                render: function (data) {
                    return `<span class="m--font-boldest">
                                ${parseFloat(data).toLocaleString('en-US', { maximumFractionDigits: 2 })}
                            </span>`;
                }
            },
            {
                data: "total_amount_paid",
                className: "text-right m--padding-right-30",
                width: "10%",
                render: function (data) {
                    return `<span class="m--font-boldest">
                                ${parseFloat(data).toLocaleString('en-US', { maximumFractionDigits: 2 })}
                            </span>`;
                }
            },
            {
                data: null,
                className: "text-right m--padding-right-30",
                width: "10%",
                render: function (data, type, row) {
                    const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                    return `<span class="m--font-boldest">
                                ${parseFloat(balance).toLocaleString('en-US', { maximumFractionDigits: 2 })}
                            </span>`;
                }
            },
            {
                data: "deduction_type",
                width: "10%",
                render: function (data, type, row) {
                    return parseInt(data) === 0 ? "Percentage" : "Fix Amount";
                }
            },
            {
                data: null,
                width: "6%",
                render: function (data, type, row) {
                    if (parseInt(row.deduction_type) === 0) {
                        return parseFloat(row.percentage).toLocaleString('en-US', { maximumFractionDigits: 2 }) + "" + "%";
                    } else {
                        return parseFloat(row.fixed_deduction_amt).toLocaleString('en-US', { maximumFractionDigits: 2 });
                    }
                }
            },
            {
                data: "archived_by_name",
                width: "12%",
                render: function (data, type, row) {
                    const tempDate = moment(row.archived_at).format("YYYY-MM-DD");
                    let tempHtml = `<p class="mb-1 m--font-bolder">${data}</p>
                    <p class='m-0'><small><span class="m--font-bolder">Date:</span> ${tempDate}</small></p>`;
                    return tempHtml;
                    return data;
                }
            }, {
                data: "active",
                className: "text-center",
                width: "8%",
                render: function (data, type, row) {
                    let tempStatus = parseInt(data);
                    let badgeColor = "m-badge--warning";
                    let badgeText = "Suspended";
                    if (row.paid == 1 && tempStatus !== 2) { tempStatus = 2; }

                    const balance = parseFloat(row.amount) - parseFloat(row.total_amount_paid);
                    if (balance <= 0) { tempStatus = 2; }

                    switch (tempStatus) {
                        case 1:
                            badgeColor = "m-badge--info";
                            badgeText = "Active";
                            break;
                        case 2:
                            badgeColor = "m-badge--success";
                            badgeText = "Paid";
                            break;
                        default:
                            badgeColor = "m-badge--warning";
                            badgeText = "Suspended";
                            break;
                    }
                    return `<span class="m-badge m-badge--wide m--font-bolder ${badgeColor}" style="width: 75%;">${badgeText}</span>`;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    let btnActions = '' +
                        '<button type="button" ' +
                        '   class="btn btn-default btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--hover-primary btnRestoreOrDeleted"' +
                        '   data-id="' + row.id + '"' +
                        '   onclick="restoreLoans(' + row.id + ')"' +
                        '   title="" data-placement="left" data-toggle="m-tooltip" data-original-title="Restore">' +
                        '   <i class="la la-reply"></i>' +
                        '</button>';

                    return btnActions;
                },
                orderable: false,
                className: 'text-center',
                width: '5%'
            },
        ]
    });

searchField.donetyping(function () {
    tblArchivedLoans.ajax.reload();
});

function restoreLoans(loan_id) {
    $.ajax({
        url: baseUrl('hris/masterfile/open_confirm_modal'),
        type: "POST",
        data: {
            csrf_token: _csrf_hash,
            formData: {
                title: 'Confirm Restore',
                message: 'Are you sure to restore this loan history?',
                action: 'hris/archive/restore_loan/' + loan_id,
                color: 'btn-danger'
            },
            path: 'ams/confirmation_dialog',
            function_name: 'passDataToDialog'
        },
        success: function (modal) {
            const _modal = $(".document-modal-container");
            _modal.html(modal);
            _modal.modal("show");
        }
    });
}