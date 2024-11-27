<div class="m-content">
    <div class="row">
        <div class="col-md-12">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Warehouse Masterfile
                                <small>
                                
                                </small>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded">
                        <table class="m-datatable__table table table-striped table-bordered" id="tbl_warehouse">
                            <thead class="m-datatable__head">
                                <tr>
                                    <th>Name</th>
                                    <th>Database</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="m-datatable__body">
                                <?php if($data): foreach($data as $_data): ?>
                                <tr><td><?php echo $_data['name'];?></td>
                                <td><?php echo $_data['database'];?></td>
                                <td><?php echo $_data['status'] == 1 ? '<span class="m-badge m-badge--success m-badge--wide m-badge--rounded">Active</span>' : '<span class="m-badge m-badge--danger m-badge--wide m-badge--rounded">Inactive</span>';?></td>
                                <td><button class="btn btn-secondary" data-id="<?php echo $_data['id'];?>"><i class="la la-pencil-square-o"></i></button></td></tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="m_addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document" style="min-width: 30%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                   New Warehouse
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
            </div>
            <form class="m-form m-form--fit m-form--label-align-right" id="frm_New" action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <div class="modal-body">
                <div class="form-group m-form__group row">
                    <label for="example-text-input" class="col-2 col-form-label">
                        Name
                    </label>
                    <div class="col-10">
                        <input class="form-control m-input" name="name" type="text" data-validation="required">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="example-text-input" class="col-2 col-form-label" >
                        Database
                    </label>
                    <div class="col-10">
                        <input class="form-control m-input" name="database" type="text" data-validation="required">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-primary btnSave btn-submit">
                    Save
                </button>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
var _table = $("#tbl_warehouse").DataTable({
    dom: '<"toolbar">frtip',
});

$("div.toolbar").html('<a href="javascript:void()" data-toggle="modal" data-target="#m_addNew" class="btn btn-accent m-btn m-btn--custom m-btn--icon m-btn--pill btnNew"> <span><i class="fa fa-plus"></i><span>New</span></span> </a>');


$.validate({
    form: '#frm_New',
    lang: 'en',
    onSuccess: function (form) {
        $.ajax({
            url: baseUrl("inventory/warehouse/create_new/"),
            type: "POST",
            dataType: "json",
            data: $("#frm_New").find("input").serialize(),
            beforeSend: function () {
                $(".btn-submit").addClass("m-btn--custom m-loader m-loader--light m-loader--right");
            },
            success: function (data) {

                if(data.status == true){
                    $('#frm_New')[0].reset();
                    _table.ajax.reload();
                    toastr.success("Warehouse Notification",data.msg, 5000);
                }else{
                    toastr.warning("Warehouse Notification",data.msg, 5000);
                }
                $(".btn-submit").removeClass("m-loader m-loader--light m-loader--right");
            }
        });
        return false;
    },
});


</script>
