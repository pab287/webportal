<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        SSS Contribution Table
                        <small>
                            EFFECTIVE APRIL 2019
                        </small>
                    </h3>
                </div>
            </div>
        </div>
        <div class="m-portlet__body">
            <!--::Top dt begin::-->
                <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                    <div class="row align-items-center">
                        <div class="col-xl-8 order-2 order-xl-1">
                            <div class="form-group m-form__group row align-items-center">
                                <div class="col-md-12">
                                    <button class="btn btn-success m-btn m-btn--custom m-btn--icon m-btn--air m-btn--pill btnNew" data-toggle="modal" data-target="#mdl-new_range">
                                        <span>
                                            <i class="la la-plus"></i>
                                            <span>
                                                New
                                            </span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                            <div class="m-input-icon m-input-icon--left" style="border: 1px solid #c3c3c3;">
                                <input type="text" class="form-control m-input m-input--solid" placeholder="Search..." id="generalSearch">
                                <span class="m-input-icon__icon m-input-icon__icon--left">
                                    <span>
                                        <i class="la la-search"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center mt-2">
                        <div class="col-xl-8 order-2 order-xl-1"></div>
                        <div class="col-xl-4 order-1 order-xl-2 m--align-right">
                            <div class="m-input-icon m-input-icon--left">
                                <div class="m-radio-inline">
                                    <label class="m-radio">
                                        <input type="radio" name="filter_classification" value="1" checked="checked">
                                            EMPLOYED
                                        <span></span>
                                    </label>
                                    <label class="m-radio">
                                        <input type="radio" name="filter_classification" value="2">
                                            HOUSEHOLD
                                        <span></span>
                                    </label>
                                </div>
                            </div>  
                        </div>
                    </div>
                </div>
            <!--::Top dt end::-->
            <!--::dt begin::-->
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered" id="tbl-SSS" width="100%">
                        <thead>
                        <tr>
                            <th rowspan="2">Range of Compensation</th>
                            <th colspan="2" class="text-center">Regular</th>
                            <th colspan="2" class="text-center">Employee Compensation</th>
                            <th colspan="2" class="text-center">Mandatory Provident Fund</th>
                            <th></th>
                            <th rowspan="2">Action</th>
                        </tr>
                        <tr>
                            <th>ER</th>
                            <th>EE</th>
                            <th>ER</th>
                            <th>EE</th>
                            <th>ER</th>
                            <th>EE</th>
                            <th>Classification</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            <!--::dt end::-->
        </div>
    </div>
</div>

<div class="modal fade" id="mdl-new_range" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-newRange" action="<?php echo site_url("payroll/sss_table/save_range");?>">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Range</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label">
                            From
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="from" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label">
                            To
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="to" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-12 col-form-label" >
                            REGULAR
                        </label>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            ER
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="er" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            EE
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="ee" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-12 col-form-label" >
                            EMPLOYEE COMPENSATION
                        </label>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            ER
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="ec_er" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            EE
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="ec_ee" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-12 col-form-label" >
                            PROVIDENT FUND
                        </label>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            ER
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="prov_er" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            EE
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="prov_ee" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <div class="col-4 p-0">
                            <label for="example-text-input" class="col-2 col-form-label" >
                                Classification
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="classification" id="sss_classification" class="form-control m-input" data-validation="required">
                                <option value="">Select an Option</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="mdl-edit_range" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="frm-editRange" action="<?php echo site_url("payroll/sss_table/edit_range");?>">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
        <input type="hidden" name="id" />
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Range</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            ×
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label">
                            From
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="from" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label">
                            To
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="to" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-12 col-form-label" >
                            REGULAR
                        </label>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            ER
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="er" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            EE
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="ee" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-12 col-form-label" >
                            EMPLOYEE COMPENSATION
                        </label>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            ER
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="ec_er" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            EE
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="ec_ee" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-12 col-form-label" >
                            PROVIDENT FUND
                        </label>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            ER
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="prov_er" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-2 col-form-label" >
                            EE
                        </label>
                        <div class="col-10">
                            <input class="form-control m-input" type="text" name="prov_ee" data-validation="required">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <div class="col-4 p-0">
                            <label for="example-text-input" class="col-2 col-form-label" >
                                Classification
                            </label>
                        </div>
                        <div class="col-8">
                            <select name="classification" id="sss_classification_edit" class="form-control m-input" data-validation="required">
                                <option value="">Select an Option</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-submit btnSave">Save</button>
                    <button class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var classificationSource = [
        { id : "-1", text: "Select an Option" },
        { id : "1", text: "Employed" },
        { id : "2", text: "Household" }
    ];

    var search_val = "";
    var filter_classification = '';
    var _csrf_token = "<?php echo $this->security->get_csrf_token_name(); ?>";
    var _csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var tblSss = $("#tbl-SSS").DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        searching: true,
        ordering: false,
        ajax: {
            url: "<?php echo site_url("payroll/sss_table/get_range");?>",
            type: "post",
            dataType: "json",
            global: false,
            data: function (d) {
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
                d.filter_classification = filter_classification;
            }
        },
        columns: [
            {data: "range", width: "*" },
            {data: "er", width: "10%" },
            {data: "ee", width: "10%" },
            {data: "ec_er", width: "10%" },
            {data: "ec_ee", width: "10%" },
            {data: "prov_er", width: "10%" },
            {data: "prov_ee", width: "10%" },
            {data: "emp_classification", width: "10%" },
            {data: "action", width: "6%", className: "text-center" },
        ]
    });

    var classification = $("#sss_classification").select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        data: classificationSource
    });

    var classificationEdit = $("#sss_classification_edit").select2({
        placeholder: 'SELECT AN OPTION',
        width: '100%',
        data: classificationSource
    });

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        tblSss.ajax.reload();
    });
    $.validate({
        form: '#frm-newRange',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: $("#frm-newRange").find("input, select").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.response) {
                        toastr.success(data.toastr_msg, "Notice", 5000);
                        document.getElementById("frm-newRange").reset();
                        $('#sss_classification').val('');
                    } else {
                        toastr.error(data.toastr_msg, "Notice", 5000);
                    }
                    tblSss.ajax.reload();
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");

                }
            });
            return false;
        },
    });

    $("#tbl-SSS").on("click",".btnEdit",function(){

        var id = $(this).attr("data-id");
        $.ajax({
            url: "<?php echo site_url("payroll/sss_table/get_range_data")?>",
            type: "POST",
            data: {id : id, csrf_token: _csrf_hash},
            success: function(data){
                    $("#frm-editRange input[name=id]").val(data.id);
                    $("#frm-editRange input[name=from]").val(data.from);
                    $("#frm-editRange input[name=to]").val(data.to);
                    $("#frm-editRange input[name=ee]").val(data.ee);
                    $("#frm-editRange input[name=er]").val(data.er);
                    $("#frm-editRange input[name=ec_ee]").val(data.ec_ee);
                    $("#frm-editRange input[name=ec_er]").val(data.ec_er);
                    $("#frm-editRange input[name=prov_ee]").val(data.prov_ee);
                    $("#frm-editRange input[name=prov_er]").val(data.prov_er);
                    $("#frm-editRange select#sss_classification_edit").val(data.classification).trigger('change');
            }
        });
        $("#mdl-edit_range").modal("show");
    });

    $.validate({
        form: '#frm-editRange',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: $("#frm-editRange").find("input, select").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    $("#mdl-edit_range").modal("hide");
                    if (data.response) {
                        toastr.success(data.toastr_msg, "Notice", 5000);
                    } else {
                        toastr.error(data.toastr_msg, "Notice", 5000);
                    }
                    tblSss.ajax.reload(null, false);
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");

                }
            });
            return false;
        },
    });

    $("input[name='filter_classification']").change( function(){
        var selected_value = $(this).val();

        filter_classification = selected_value;
        tblSss.ajax.reload();
    });

</script>