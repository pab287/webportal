var modalSequenceForm = $("#modal-sequence_form-list");
var currentNodes = [];

var generateGridData = function(){
    $.ajax({
        url: baseUrl("pms/task/get_sequence_form_grid"),
        dataType: "json",
        success: function(json){
            vmSequenceFormGrid.rows = Object.assign({}, json.rows);
            vmSequenceFormGrid.rowCount = json.row_count;
        }
    });
}

var vmSequenceFormGrid = new Vue({
    el: "#sequenceFormGrid",
    data: {
        perPage: 3,
        currentPage: 1,
        rows : {},
        rowCount: 0,
    },
    methods: {
        renderForm: function(id){
            getSequenceFormData(id);
        },
        mouseLeave: function(event){
            var allDiv = $("div.m-portlet.m-portlet--bordered-semi");
            if(typeof allDiv !== "undefined" && allDiv.length > 0){
                allDiv.removeClass("m-portlet--skin-dark");
            }
        },
        mouseOver: function(event){
            var currentTarget = $(event.target);
            var currentDiv = currentTarget.closest("div.m-portlet.m-portlet--bordered-semi");
            if(typeof currentDiv !== "undefined"){
                currentDiv.addClass("m-portlet--skin-dark");
            }
        }
    }, computed: {
        updateRow: function(){
            var tempObjects = {};
            $.each(this.rows, function(i,v){
                var tempBgColor = "m-portlet--rounded m--bg-light";
                var tempTextColor = "";
                var tempStatus = "PENDING";
                switch(v.form_status){
                    case "1": tempBgColor = "m-portlet--rounded m--bg-success"; tempTextColor = "text-light"; tempStatus = "ONGOING"; break;
                    case "2": tempBgColor = "m-portlet--rounded m--bg-warning"; tempTextColor = "text-light"; tempStatus = "ON HOLD"; break;
                    case "3": tempBgColor = "m-portlet--rounded m--bg-primary"; tempTextColor = "text-light"; tempStatus = "COMPLETE"; break;
                    default: tempBgColor = "m-portlet--rounded m--bg-light"; tempTextColor = ""; tempStatus = "PENDING"; break;
                }
                tempObjects[i] = Object.assign({}, v, { bg_color: tempBgColor, text_color: tempTextColor, temp_status: tempStatus });
            });

            tempObjects = Object.assign({}, this.rows, tempObjects);
            return tempObjects;
        }
    }
});

var vmSequenceFormModal = new Vue({
    el: "#sequenceFormModalContent",
    data: {
        row: {}, 
        node_count: 0, 
        nodes: {},
    },methods: {
        updateRowData: function(e){
            var currentValue = e.target.value;
            var currentName = e.target.name;
            this.$set(this.row, currentName, currentValue);
        },
        switchContractor: function(id, e){
            var _self = this;
            var isChecked = e.target.checked;
            if(isChecked){
                var arrField = ["date_", "due_", "contractor_", "contract_"];
                $.each(arrField, function(i, v){
                    var tempField = v+id;
                    _self.$set(_self.row, tempField, "");
                });

            }
        }
    }
});

var getSequenceFormData = function(id){
    $.ajax({
        url: baseUrl("pms/task/get_sequence_form_data/"+id),
        dataType: "json",
        success: function(json){
            if(json.response){
                var _arrNodeIds = [];
                var modalContent = modalSequenceForm.find("#sequenceFormModalContent");
                if(typeof modalContent !== "undefined"){
                    vmSequenceFormModal.row = Object.assign({}, json.row);

                    var treeSequenceFormAction = modalContent.find("#tree_sequence_form-action");
                    $(treeSequenceFormAction)
                        .jstree("destroy")
                        .jstree({
                            core: {
                                data: json.data,
                                dblclick_toggle: false,
                                check_callback: true,
                                themes: { icons: false }
                            },
                            checkbox: {
                                /*** cascade: "up, down", ***/
                                three_state: true
                            },
                            plugins: ["checkbox", "wholerow"]
                        }).on("ready.jstree", function (e, data) {
                            $(this).jstree("open_all");

                            vmSequenceFormModal.nodes = Object.assign({});
                            vmSequenceFormModal.node_count = 0;

                            var _current = $(this);
                            var _treeItem = _current.find("li[role=treeitem]");
                            if (typeof _treeItem !== "undefined") {
                                _treeItem.each(function (i, v) {
                                    var _id = $(v).attr("id");
                                    _id = parseInt(_id);
                                    if (jQuery.inArray(_id, json.checklist_id) !== -1) {
                                        _arrNodeIds.push(_id);
                                    }
                                });

                                data.instance.select_node( _arrNodeIds, true);
                                _triggerSelectedNodes(_arrNodeIds);
                            }

                            var jsTreeCheckbox = $(this).find(
                                "i.jstree-icon.jstree-checkbox"
                            );
                            var jsTreeOcl = $(this).find("i.jstree-icon.jstree-ocl");
                            if (typeof jsTreeOcl !== "undefined" && jsTreeOcl.length > 0) {
                                jsTreeOcl.remove();
                            }
                            if (
                                typeof jsTreeCheckbox !== "undefined" &&
                                jsTreeCheckbox.length > 0
                            ) {
                                jsTreeCheckbox.css("margin-right", "15px");
                            }
                        }).on('changed.jstree', function (e, data) {
                            var selectedItems = data.selected;
                            _triggerSelectedNodes(selectedItems);
                        });
                    
                    modalSequenceForm.modal("show");
                }
            }
        }
    });
}

var _triggerSelectedNodes = function(selectedItems = []){
    if(selectedItems.length > 0){
        $.ajax({
            global: false,
            url: baseUrl("pms/task/generate_parent_node"),
            type: "post",
            dataType: "json",
            data: { csrf_token: _csrf_hash, selected_nodes: selectedItems },
            success: function(json){
                vmSequenceFormModal.nodes = Object.assign({}, json.parent_nodes);
                vmSequenceFormModal.node_count = json.node_count; 
                
                setTimeout(function(){
                    modalSequenceForm.find(".form-datepicker").datepicker({
                        todayHighlight: true,
                        orientation: "bottom left",
                        templates: {
                            leftArrow: '<i class="la la-angle-left"></i>',
                            rightArrow: '<i class="la la-angle-right"></i>'
                        },
                        format: "yyyy-mm-dd",
                        autoclose: true
                    }).on("changeDate", function (e) {
                        vmSequenceFormModal.updateRowData(e);
                    });                           
                }, 200);
            }
        });
        return this;
    }else{
        return false;
    }
}

var validateSequenceFormData = function(){
    $.validate({
        form: "#frmSequenceFormData",
        lang: "en",
        scrollToTopOnError : false,
        onSuccess: function (form) {
            var currentForm = form[0];
            var formUrl = currentForm.action;
            var formData = $(currentForm).serialize();

            var treeSequence = $(currentForm).find("#tree_sequence_form-action");
            var jsonData = jQuery(treeSequence)
                .jstree(true)
                .get_json("#", { flat: true });
            var _string = JSON.stringify(jsonData);
            formData += "&nodes="+_string;

            $.ajax({
                url: formUrl,
                type: "post",
                dataType: "json",
                data: formData,
                success: function(json){
                    if(json.response){
                        var currentData = json.data;
                        vmSequenceFormGrid.rows = Object.assign({}, currentData.rows);
                        vmSequenceFormGrid.rowCount = currentData.row_count;
                        modalSequenceForm.modal("hide");
                    }
                }
            });

            return false;
        }
    });
}
jQuery(document).ready(function(){
    generateGridData();
    validateSequenceFormData();
});