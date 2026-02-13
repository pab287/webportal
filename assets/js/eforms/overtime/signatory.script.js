let createSignatoryModal = $("#payroll--create-signatory-modal");
let editSignatoryModal = $("#payroll--edit-signatory-modal");
let dtSignatory, search_val, _companies, selectedEmployee = [];
let tempRowData = {
    company_description: "", company_id: 0, created_at: "0000-00-00 00:00:00", created_by: 0, id: 0,
    meta: [], status: 0, type: 0, updated_at: "0000-00-00 00:00:00", updated_by: 0
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
                    url: siteUrl("eforms/overtime/create_signatory_content"),
                    dataType: "json",
                    global: false,
                    success: function (json) {
                        const portlet = tempPortlet.append(json.html);
                        const temp_portlet = portlet.find(".m-portlet.m-portlet--bordered.m-portlet--head-sm.m-portlet--mobile.m-portlet--sortable");
                        if (typeof temp_portlet !== "undefined" && temp_portlet.length > 0) {
                            let ctr = temp_portlet.length;
                            let tempId = "m--portlet_append_" + ctr;
                            if (tempPortlet.find("#" + tempId).length > 0) {
                                ctr += 1;
                                tempId += ctr;
                            }
                            let lastChild = $(tempPortlet).find("div.m-portlet:last-child");
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
            
            tempSelector.each((_i, select2) => {
                const $select = $(select2);
                
                if ($select.data("select2-initialized")) return;
                
                $select.off(".select2Events");
                
                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.select2("destroy");
                }

                let prevValue = null;
                
                $(select2).select2({
                    tags: true,
                    allowClear: true,
                    placeholder: 'Select an option',
                    width: '100%',
                    dropdownParent: $("#parent"),
                    ajax: {
                        url: baseUrl("eforms/overtime/select_signatory_employee"),
                        type: 'POST',
                        data: function ({ term }) {
                            return {
                                csrf_token: _csrf_hash,
                                q: term,
                                ids: selectedEmployee,
                                company_id: tempModal.find("#company").val()
                            }
                        },
                        dataType: "json",
                        delay: 250,
                        global: false,
                        processResults: function (data) {
                            let tempData = [];
                            $.each(data.results, function (i, v) {
                                const dd = { id: v.text, text: v.text, empId: v.id };
                                tempData.push(dd);
                            });
                            return { results: tempData };
                        }
                    }
                }).on('select2:opening.select2Events', function (e) {
                    prevValue = $(this).val();

                }).on('select2:select.select2Events', function(e) {
                    const self = $(e.target);
                    self.validate();
                    const data = e.params.data;

                    // if (prevValue !== data.id) {
                    //     if (!selectedEmployee.includes(data.id)) {
                    //         const index = selectedEmployee.indexOf(prevValue);
                    //         if (index > -1) {
                    //             selectedEmployee.splice(index, 1);
                    //         }
                    //     } else {
                    //         Swal.fire({
                    //             title: 'Employee already selected!',
                    //             text: 'Overtime Summary Signatory',
                    //             icon: 'warning',
                    //             allowOutsideClick: false,
                    //         }).then((result) => {
                    //             if (result.isConfirmed) {
                    //                 if (prevValue) {
                    //                     self.val(prevValue).trigger('change.select2');
                    //                     prevValue = self.val();
                    //                 } else {
                    //                     const options = new Option('Select an Option', '', true, true);
                    //                     $select.append(options).trigger('change');
                    //                     prevValue = null;
                    //                 }
                    //             }
                    //         });
                    //     }
                    // }

                    // if (!selectedEmployee.includes(data.id)) {
                    //     selectedEmployee.push(data.id);
                    // } else {
                    //     if (prevValue) {
                    //         Swal.fire({
                    //             title: 'Employee already selected!',
                    //             text: 'Overtime Summary Signatory',
                    //             icon: 'warning',
                    //             allowOutsideClick: false,
                    //         }).then((result) => {
                    //             if (result.isConfirmed) {
                    //                 if (prevValue) {
                    //                     self.val(prevValue).trigger('change.select2');
                    //                     prevValue = self.val();
                    //                 } else {
                    //                     const options = new Option('Select an Option', '', true, true);
                    //                     $select.append(options).trigger('change');
                    //                     prevValue = null;
                    //                 }
                    //             }
                    //         });
                    //     } else {
                    //         selectedEmployee.push(data.id);
                    //     }
                    // }
                }).on('select2:unselect.select2Events', function(e) {
                    const data = e.params.data;
                    const index = selectedEmployee.indexOf(data.id);

                    if (index > -1) {
                        selectedEmployee.splice(index, 1);
                    }
                });
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
        },
        global: false,
    },
    columns: [
        { data: "company", title: "Company", width: "18%" },
        {
            data: null, title: "Signatories", className: "custom-signatory", render: function (data, meta, row) {
                const metaFields = row.meta;
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
                // if (_currentActions.includes("archive")) {
                //     tempHtml += `<button type="button"
                //         class="btn btn-sm btn-default m-btn m-btn--hover-danger m-btn--icon m-btn--icon-only m-btn--pill btnArchive btnDeleteSignatory"
                //         data-placement="bottom"
                //         data-toggle="m-tooltip"
                //         title="" data-original-title="Archive Signatory"
                //         data-id="${row.id}"><i class="la la-archive"></i></button>`;
                // }
                return tempHtml;
            }
        },
    ], columnDefs: [{
        targets: "all",
        defaultContent: "",
    }]
});

$.validate({
    form: "#frmCreateSignatory",
    lang: 'en',
    scrollToTopOnError: false,
    onSuccess: function (form) {
        var currentForm = form[0];
        var formUrl = currentForm.action;
        var formData = $(currentForm).serialize();

        $.ajax({
            url: formUrl,
            type: "post",
            dataType: "json",
            data: formData,
            success: function (json) {
                if (json.response) {
                    toastr.success("Create Signatory", json.toastr_msg);
                    dtSignatory.ajax.reload(null, false);
                    createSignatoryModal.modal("hide");
                } else {
                    toastr.error("Create Signatory", json.toastr_msg);
                }
            }
        });
        return false;
    }
});

$(document).on("click", ".btnEditSignatory", function (e) {
    const _this = $(this);
    const tempId = _this.data("id");
    $.ajax({
        url: siteUrl("eforms/overtime/get_current_signatory/" + tempId),
        dataType: "json",
        success: function (json) {
            let tempRow = {};
            let ctr = 0;
            if (json.response) {
                tempRow = Object.assign({}, json.data);
                ctr = json.count;
            }
            vmEditSignatory.row = Object.assign({}, tempRow);
            vmEditSignatory.count = ctr;
            vmEditSignatory.$mount();
            companySelect2(editSignatoryModal);
            initEditSelect2Employee(editSignatoryModal, tempRow.meta);
            editSignatoryModal.find("#m_sortable_portlets").sortable();
            editSignatoryModal.modal("show");
        }
    });
});

$("#payroll--edit-signatory-modal").on('hidden.bs.modal', function () {
    vmEditSignatory.row = Object.assign({});
});

var vmEditSignatory = new Vue({
    el: "#editSignatoryContent",
    data: { row: tempRowData, count: 0 },
    methods: {
        appendCurrentCompany: function () {
            const _this = this;
            const currentElement = _this.$el;
            const currentRow = _this.row;
            const select2Company = $(currentElement).find("select#company");
            if (typeof select2Company !== "undefined" && select2Company.length == 1) {
                let tempOption = new Option(currentRow.company_description, currentRow.company_id, true, true);
                select2Company
                    .empty()
                    .html(tempOption);
            }
        }, appendCurrentSignatory: function () {
            const _this = this;
            const currentElement = _this.$el;
            const currentRow = _this.row;
            $.each(currentRow.meta, function (i, v) {
                const cPortlet = $(currentElement).find("#m--portlet_append_" + i);
                if (typeof cPortlet !== "undefined" && cPortlet.length == 1) {
                    cPortlet.mPortlet();
                    const currentSelect2 = cPortlet.find("select.select2--value");
                    if (typeof currentSelect2 !== "undefined" && currentSelect2.length == 1) {
                        let tempOption = new Option(v.value, v.value, true, true);
                        currentSelect2
                            .empty()
                            .html(tempOption);
                    }
                }
            });
        }, addSignatoryField: function (e) {
            const _this = this;
            const _container = $(_this.$el);
            if (typeof _container !== "undefined") {
                const tempPortlet = _container.find("#m_sortable_portlets");
                $.ajax({
                    url: siteUrl("eforms/overtime/create_signatory_content"),
                    dataType: "json",
                    global: false,
                    success: function (json) {
                        const portlet = tempPortlet.append(json.html);
                        const temp_portlet = portlet.find(".m-portlet.m-portlet--bordered.m-portlet--head-sm.m-portlet--mobile.m-portlet--sortable");
                        if (typeof temp_portlet !== "undefined" && temp_portlet.length > 0) {
                            let ctr = temp_portlet.length;
                            let tempId = "m--portlet_append_" + ctr;
                            if (tempPortlet.find("#" + tempId).length > 0) {
                                ctr += 1;
                                tempId += ctr;
                            }
                            let lastChild = $(tempPortlet).find("div.m-portlet:last-child");
                            lastChild.prop("id", tempId);
                            $(tempPortlet).find("#" + tempId).mPortlet();
                        }
                        let meta = vmEditSignatory.row.meta;
                        initEditSelect2Employee(editSignatoryModal, meta);
                    }
                });
            }
        }, validateFields: function () {
            const _this = this;
            const _container = $(_this.$el);
            if (typeof _container !== "undefined") {
                const _currentForm = _container.find("#frmEditSignatory");
                $.validate({
                    form: _currentForm,
                    lang: 'en',
                    scrollToTopOnError: false,
                    onSuccess: function (form) {
                        var currentForm = form[0];
                        var formUrl = currentForm.action;
                        var formData = $(currentForm).serialize();

                        $.ajax({
                            url: formUrl,
                            type: "post",
                            dataType: "json",
                            data: formData,
                            success: function (json) {
                                if (json.response) {
                                    toastr.success("Update Signatory", json.toastr_msg);
                                    dtSignatory.ajax.reload(null, false);
                                    editSignatoryModal.modal("hide");
                                } else {
                                    toastr.error("Update Signatory", json.toastr_msg);
                                }
                            }
                        });
                        return false;
                    }
                });
            }
        }, removePortlet(e, index) {
            const count = $("#m_sortable_portlets .m-portlet").length;
    
            if (count > 1) {
                selectedEmployee.splice(index, 1);
                $(e.target).closest('.m-portlet').remove();
            }
        }
    }, mounted: function () {
        const _this = this;
        _this.appendCurrentCompany();
        // _this.appendCurrentSignatory();
        setTimeout(_this.validateFields(), 500);
    }
});

$("#payroll--create-signatory-modal").on('hidden.bs.modal', function(){
    $('[id^="m--portlet_append_"]').remove();
    $("#frmCreateSignatory").trigger('reset');
    $("#frmCreateSignatory select").val('').trigger('change');
});

var initEditSelect2Employee = function (tempModal, ids) {
    if (typeof tempModal !== "undefined" && tempModal.length == 1) {
        let tempSelector = tempModal.find("select.select2--value");
        if (typeof portlet !== "undefined") { tempSelector = portlet.find("select.select2--value"); }
        if (typeof tempSelector !== "undefined") {
            
            if (typeof ids != 'undefined') {    
                $.each(ids, function(index, item) {
                    selectedEmployee.push(item.value);
                });
            }

            tempSelector.each((_i, select2) => {
                const $select = $(select2);

                if ($select.data("select2-initialized")) return;
                
                // Remove Select2 container
                $select.next('.select2-container').remove();
                
                // Clear value
                $select.val('');
                
                $select.off(".select2Events");
                
                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.select2("destroy");
                }

                const value = ids?.[_i]?.value;
                if (value !== undefined && value !== null) {
                    let tempOption = new Option(value, value, true, true);
                    $select.append(tempOption).trigger('change');
                }

                let prevValue = null;
                
                $(select2).select2({
                    tags: true,
                    allowClear: true,
                    placeholder: 'Select an option',
                    width: '100%',
                    dropdownParent: $("#edit-parent"),
                    ajax: {
                        url: baseUrl("eforms/overtime/select_signatory_employee"),
                        type: 'POST',
                        data: function ({ term }) {
                            return {
                                csrf_token: _csrf_hash,
                                q: term,
                                ids: selectedEmployee,
                                company_id: tempModal.find("#company").val()
                            }  
                        },
                        dataType: "json",
                        delay: 250,
                        global: false,
                        processResults: function (data) {
                            let tempData = [];
                            $.each(data.results, function (i, v) {
                                const dd = { id: v.text, text: v.text, empId: v.id };
                                tempData.push(dd);
                            });
                            return { results: tempData };
                        }
                    }
                }).on('select2:opening.select2Events', function (e) {
                    prevValue = $(this).val();
                }).on('select2:select.select2Events', function(e) {
                    const self = $(e.target);
                    self.validate();
                    const data = e.params.data;
                    // if (prevValue !== data.id) {
                    //     if (!selectedEmployee.includes(data.id)) {
                    //         const index = selectedEmployee.indexOf(prevValue);
                    //         if (index > -1) {
                    //             selectedEmployee.splice(index, 1);
                    //         }
                    //     } else {
                    //         e.preventDefault();

                    //         Swal.fire({
                    //             title: 'Employee already selected!',
                    //             text: 'Overtime Summary Signatory',
                    //             icon: 'warning',
                    //             allowOutsideClick: false,
                    //         }).then((result) => {
                    //             if (result.isConfirmed) {
                    //                 if (prevValue) {
                    //                     self.val(prevValue).trigger('change.select2');
                    //                     prevValue = self.val();
                    //                 } else {
                    //                     const options = new Option('Select an Option', '', true, false);
                    //                     $select.append(options).trigger('change.select2');
                    //                     prevValue = null;
                    //                 }
                    //             }
                    //         });
                    //         return false;
                    //     }
                    // }

                    // if (!selectedEmployee.includes(data.id)) {
                    //     selectedEmployee.push(data.id);
                    // } else {
                    //     e.preventDefault();

                    //     Swal.fire({
                    //         title: 'Employee already selected!',
                    //         text: 'Overtime Summary Signatory',
                    //         icon: 'warning',
                    //         allowOutsideClick: false,
                    //     }).then((result) => {
                    //         if (result.isConfirmed) {
                    //             console.log(prevValue)
                    //             if (prevValue) {
                    //                 self.val(prevValue).trigger('change.select2');
                    //                 prevValue = self.val();
                    //             } else {
                    //                 const options = new Option('Select an Option', '', true, true);
                    //                 $select.append(options).trigger('change');
                    //                 prevValue = null;
                    //             }
                    //         }
                    //     });
                    //     return false;
                    // }
                }).on('select2:unselect.select2Events', function(e) {
                    const data = e.params.data;

                    const index = selectedEmployee.indexOf(data.id);

                    if (index > -1) {
                        selectedEmployee.splice(index, 1);
                    }
                });

                $select.data("select2-initialized", true);
            });
        }
    }
}

function removePortlet(e) {
    var $portlet = $(e.target).closest('.m-portlet');
    const count = $("#m_sortable_portlets .m-portlet").length;
    var selectValue = $portlet.find('select').val();
    
    if (count > 1) {
        if (selectValue) {
            const index = selectedEmployee.indexOf(selectValue);
            if (index > -1) {
                selectedEmployee.splice(index, 1);
            }
        }

        $(e.target).closest('.m-portlet').remove();
    }
}