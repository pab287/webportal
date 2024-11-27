<div class="m-content">
    <div class="row">
        <div class="col-lg-3"><!-- log module selection start -->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Modules
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class=" m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" style="min-height: 350px; max-height: 580px; position: relative; overflow: visible;">
                        <div id="modules-container" class="m-widget4">
                            <template v-if = "count > 0">
                                <template v-for = "(item, index) in rows">                   
                                    <div class="m-widget4__item">
                                        <div class="m-widget4__img m-widget4__img--pic">&nbsp;</div>
                                        <div class="m-widget4__info m--font-transform-u">
                                            <p class="m-widget4__title m--marginless">{{ item.name }}</p>
                                            <p class="m-widget4__sub m--marginless m--font-boldest">{{ item.description }}</p>
                                            <p class="m-widget4__sub m--marginless">{{ item.database }}</p>
                                        </div>
                                        <div class="m-widget4__ext">
                                            <button @click="getLogs(item.database,'user')" class="m-btn m-btn--pill m-btn--hover-info btn btn-sm btn-secondary">
                                                <em class="la la-user"> </em>
                                            </button>
                                            <button @click="getLogs(item.database,'system')" class="m-btn m-btn--pill m-btn--hover-accent btn btn-sm btn-secondary">
                                                <em class="la la-sliders"> </em>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- log module selection end -->

        <div class="col-lg-9"><!--event logs list as per modluke selected start-->
            <div class="m-portlet">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Log events
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll">
                        <table class="table table-striped- table-bordered table-hover dataTable no-footer dtr-inline" id="table-logs">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>User</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                    <th>Type</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!--event logs list as per modluke selected end-->
    </div>
</div>


<script>
    let tblLogEvents = $("#table-logs").DataTable({
        ordering: false,
        columns: [
            {data: 'display_image', width: "6%", className: "text-center",
                render: function (data, type, row, meta) {
                return `<img class='m--img-rounded m--marginless m--img-centered user__pic' src='${data}' />`;
                }
            },
            { data: 'display_name', width: "20%" },
            { data: 'log_message', width: "*",
                render: function(data, type, row, meta){
                    return `<p class='m--marginless'>${data}</p>
                    <p class='m--marginless'><small class='m--font-boldest'>${row.created_date}</small></p>`;
                }
            },
            { data: 'user_action', width: "10%" },
            { data: 'type', width: "12%"},
            { data: 'ip_address', width: "10%" },
        ]
    });
    let vmModuleCollection = new Vue({
        el: "#modules-container",
        data: { rows: {}, count: 0 },
        methods: {
            getLogs: function(database,type){
                $.ajax({
                    url: siteUrl("configuration/get_event_logs"),
                    type: "post",
                    data: {database:database, log_type:type,  csrf_token: _csrf_hash },
                    success: function(data){
                        if(data.status){
                            tblLogEvents.clear();
                            tblLogEvents.rows.add(data.data.data);
                            tblLogEvents.draw();
                        }else{
                            toastr.warning("Unable to view logs.","Event Logs");
                            tblLogEvents.clear().draw();
                        }
                    }
                });
            }
        }
    });
    let module_list = [];
    $.ajax({
        url: baseUrl("configuration/get_module_collection"),
			method: 'get',
			contentType: 'application/json',
			success: function(data){
				if(data.length != 0){
					module_list.push("modules-container");
					vmModuleCollection.rows = Object.assign({}, data);
					vmModuleCollection.count = data.length;
				}else{
				}
			}
    });
</script>
