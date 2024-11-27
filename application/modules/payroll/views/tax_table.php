<div class="m-content">
    <div class="m-portlet m-portlet--mobile">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        Withholding Tax Table
                        <small>
                            Effective January 1, 2018 to December 31, 2022 
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
                </div>
            <!--::Top dt end::-->
            <!--::dt begin::-->
                <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                    <table class="table table-striped table-bordered" id="tbl-tax" width="100%">
                        <thead>
                            <td>Payroll Schedule</td>
                            <td>Compensation Level </td>
                            <td>Compensation Range </td>
                            <td>Prescribed Withholding Tax</td>
                            <td>Action</td>
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
        <form id="frm-newRange" action="<?php echo site_url("payroll/tax_table/save_range");?>">
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
                        <label for="example-text-input" class="col-form-label">
                           Payment Schedule :
                        </label>
                            <input class="form-control m-input" type="text" name="payroll_sched" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Compensation Level :
                        </label>
                            <input class="form-control m-input" type="text" name="compensation_level" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="ccol-form-label">
                           Compensation From :
                        </label>
                            <input class="form-control m-input" type="text" name="cr_from" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Compensation To :
                        </label>
                            <input class="form-control m-input" type="text" name="cr_to" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Prescribed Withholding Tax Additional Fee :
                        </label>
                            <input class="form-control m-input" type="text" name="pwt_initial" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Prescribed Withholding Tax Percentage :
                        </label>
                            <input class="form-control m-input" type="text" name="pwt_percentage" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Prescribed Withholding Tax Over Excess :
                        </label>
                            <input class="form-control m-input" type="text" name="pwt_over" data-validation="required">
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
        <form id="frm-editRange" method="POST" action="<?php echo site_url("payroll/tax_table/edit_range");?>">
        <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
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
                    <input class="form-control m-input" type="hidden" name="id">
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Payment Schedule :
                        </label>
                            <input class="form-control m-input" type="text" name="payroll_sched" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Compensation Level :
                        </label>
                            <input class="form-control m-input" type="text" name="compensation_level" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="ccol-form-label">
                           Compensation From :
                        </label>
                            <input class="form-control m-input" type="text" name="cr_from" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Compensation To :
                        </label>
                            <input class="form-control m-input" type="text" name="cr_to" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Prescribed Withholding Tax Additional Fee :
                        </label>
                            <input class="form-control m-input" type="text" name="pwt_initial" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Prescribed Withholding Tax Percentage :
                        </label>
                            <input class="form-control m-input" type="text" name="pwt_percentage" data-validation="required">
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="example-text-input" class="col-form-label">
                           Prescribed Withholding Tax Over Excess :
                        </label>
                            <input class="form-control m-input" type="text" name="pwt_over" data-validation="required">
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
    var search_val = "";
    var _csrf_token = "<?php echo $this->security->get_csrf_token_name(); ?>";
    var _csrf_hash = "<?php echo $this->security->get_csrf_hash(); ?>";
    var tblTax = $("#tbl-tax").DataTable({
        dom: '<"toolbar">rtlip',
        serverSide: true,
        processing: true,
        autoWidth: false,
        searching: true,
        ajax: {
            url: "<?php echo site_url("payroll/tax_table/get_range");?>",
            type: "post",
            dataType: "json",
            data: function (d) {
                d.csrf_token = _csrf_hash,
                d.search['value'] = search_val
            }
        },
        columns: [
            {data: "payroll_sched"},
            {data: "compensation_level"},
            {data: "compensation_range"},
            {data: "prescribed_witholding_tax"},
            {data: "action"},
        ]
    });

    $('#generalSearch').donetyping(function (callback) {
        search_val = $(this).val();
        tblTax.ajax.reload();
    });

    $.validate({
        form: '#frm-newRange',
        lang: 'en',
        onSuccess: function (form) {
            $.ajax({
                url: form[0].action,
                type: "POST",
                data: $("#frm-newRange").find("input").serialize(),
                beforeSend: function () {
                    $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
                },
                success: function (data) {
                    if (data.response) {
                        toastr.success(data.toastr_msg, "Notice", 5000);
                        document.getElementById("frm-newRange").reset();
                    } else {
                        toastr.error(data.toastr_msg, "Notice", 5000);
                    }
                    tblTax.ajax.reload();
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");

                }
            });
            return false;
        },
    });

    $("#tbl-tax").on("click",".btnEdit",function(){
       
       var id = $(this).attr("data-id");
       $.ajax({
           url: "<?php echo site_url("payroll/tax_table/get_range_data")?>",
           type: "POST",
           data: {id : id, csrf_token: _csrf_hash},
           success: function(data){
                   $("#frm-editRange input[name=id]").val(data.id);
                   $("#frm-editRange input[name=compensation_level]").val(data.compensation_level);
                   $("#frm-editRange input[name=payroll_sched]").val(data.payroll_sched);
                   $("#frm-editRange input[name=cr_from]").val(data.cr_from);
                   $("#frm-editRange input[name=cr_to]").val(data.cr_to);
                   $("#frm-editRange input[name=pwt_initial]").val(data.pwt_initial);
                   $("#frm-editRange input[name=pwt_percentage]").val(data.pwt_percentage);
                   $("#frm-editRange input[name=pwt_over]").val(data.pwt_over);
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
                data: $("#frm-editRange").find("input").serialize(),
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
                    $(".btn-submit").removeClass("m-btn--custom m-loader m-loader--light m-loader--right");

                }
            });
            return false;
        },
    });

</script>