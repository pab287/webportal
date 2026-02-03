let createSignatoryModal = $("#payroll--create-signatory-modal");
let editSignatoryModal = $("#payroll--edit-signatory-modal");
let dtSignatory, search_val, _companies, selectedEmployee = [];
let tempRowData = {
    company_description: "", company_id: 0, created_at: "0000-00-00 00:00:00", created_by: 0, id: 0,
    meta_field: [], status: 0, type: 0, updated_at: "0000-00-00 00:00:00", updated_by: 0
};

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.company !== "undefined" && _tempContentData.company.length > 0){
        _companies = _tempContentData.company;
    }
}


var PortletDraggable = function () {
    return {
        //main function to initiate the module
        init: function () {
            $("#m_sortable_portlets").sortable({
                connectWith: ".m-portlet__head",
                items: ".m-portlet",
                opacity: 0.8,
                handle: '.m-portlet__head',
                coneHelperSize: true,
                placeholder: 'm-portlet--sortable-placeholder',
                forcePlaceholderSize: true,
                tolerance: "pointer",
                helper: "clone",
                tolerance: "pointer",
                forcePlaceholderSize: !0,
                helper: "clone",
                cancel: ".m-portlet--sortable-empty", // cancel dragging if portlet is in fullscreen mode
                revert: 250, // animation in milliseconds
                update: function (b, c) {
                    if (c.item.prev().hasClass("m-portlet--sortable-empty")) {
                        c.item.prev().before(c.item);
                    }
                }
            });
        }
    };
}();

jQuery(document).ready(function () {
    PortletDraggable.init();
});

var vmSignatoryFields = new Vue({
    el: "#signatory--container",
    data: { count: 0 },
    methods: {
        addSignatoryField: function (e) {
            const _this = this;
            const _container = $(_this.$el);
            if (typeof _container !== "undefined") {
                const tempPortlet = _container.find("#m_sortable_portlets");
                $.ajax({
                    url: siteUrl("payroll/create_signatory_content"),
                    dataType: "json",
                    global: false,
                    success: function (json) {
                        const portlet = tempPortlet.prepend(json.html);
                        const temp_portlet = portlet.find(".m-portlet.m-portlet--bordered.m-portlet--head-sm.m-portlet--mobile.m-portlet--sortable");
                        if (typeof temp_portlet !== "undefined" && temp_portlet.length > 0) {
                            let ctr = temp_portlet.length;
                            let tempId = "m--portlet_append_" + ctr;
                            if (tempPortlet.find("#" + tempId).length > 0) {
                                ctr += 1;
                                tempId += ctr;
                            }
                            let lastChild = $(tempPortlet).find("div.m-portlet:first-child");
                            lastChild.prop("id", tempId);
                            $(tempPortlet).find("#" + tempId).mPortlet();
                        }
                        initSelect2Employee(createSignatoryModal);
                    }
                });
            }
        }
    }
});

var companySelect2 = function (tempModal) {
    if (typeof tempModal !== "undefined") {
        tempModal.find("#company").select2({
            allowClear: true,
            width: '100%',
            placeholder: "SELECT AN OPTION",
            dropdownParent: tempModal,
            data: _companies, 
            language: { errorLoading: function () { return "Searching..." } }
        });
    }
}
companySelect2(createSignatoryModal);

var initSelect2Employee = function (tempModal, portlet) {
    if (typeof tempModal !== "undefined" && tempModal.length == 1) {
        let tempSelector = tempModal.find("select.select2--value");
        if (typeof portlet !== "undefined") { tempSelector = portlet.find("select.select2--value"); }
        if (typeof tempSelector !== "undefined") {
            tempSelector.select2({
                tags: true,
                allowClear: true,
                placeholder: 'Select an option',
                width: '100%',
                dropdownParent: tempModal,
                ajax: {
                    url: baseUrl("eforms/overtime/select_signatory_employee"),
                    type: 'POST',
                    data: {
                        csrf_token: _csrf_hash,
                        emp_ids: selectedEmployee
                    },
                    dataType: "json",
                    delay: 250,
                    global: false,
                    processResults: function (data) {
                        let tempData = [];
                        $.each(data.results, function (i, v) {
                            const dd = { id: v.text, text: v.text };
                            tempData.push(dd);
                        });
                        return { results: tempData };
                    }
                }
            });
        }
    }
}
initSelect2Employee(createSignatoryModal);

dtSignatory = $("#table-signatory").DataTable({
    dom: '<"toolbar">frtlip',
    serverSide: true,
    processing: true,
    searching: false,
    ordering: false,
    destroy: true,
    ajax: {
        url: baseUrl("eforms/overtime/get_signatory"),
        type: "post",
        dataType: "json",
        data: function (d) {
            d.csrf_token = _csrf_hash;
            d.search['value'] = search_val;
            return d;
        }, error: function (xhr, error, code) {
            if (error == "parsererror") {
                dtSignatory.ajax.reload(null, false);
            }
        },
        global: false,
    },
    columns: [
        { data: "company", title: "Company", width: "18%" },
        {
            data: null, title: "Signatories", className: "custom-signatory", render: function (data, meta, row) {
                const metaFields = row.meta_field;
                let tempHtml = ``;
                $.each(metaFields, function (i, v) {
                    tempHtml += `<span class="m-badge m-badge--metal m-badge--wide m-badge--rounded mr-1">${v.label}: <strong>${v.value}</strong></span>`;
                });
                return tempHtml;
            }
        },
        {
            data: null, title: "Action", width: "8%", className: "text-center", render: function (data, meta, row) {
                let tempHtml = ``;
                if (_currentActions.includes("edit")) {
                    tempHtml += `<button type="button"
                        class="btn btn-sm btn-default m-btn m-btn--hover-accent m-btn--icon m-btn--icon-only m-btn--pill btnEdit btnEditSignatory"
                        data-placement="bottom"
                        data-toggle="m-tooltip"
                        title="" data-original-title="Edit Signatory"
                        data-id="${row.id}"><i class="la la-edit"></i></button>`;
                }
                if (_currentActions.includes("archive")) {
                    tempHtml += `<button type="button"
                        class="btn btn-sm btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnArchive btnDeleteSignatory"
                        data-placement="bottom"
                        data-toggle="m-tooltip"
                        title="" data-original-title="Archive Signatory"
                        data-id="${row.id}"><i class="la la-archive"></i></button>`;
                }
                return tempHtml;
            }
        },
    ], columnDefs: [{
        targets: "all",
        defaultContent: "",
    }]
});