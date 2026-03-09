<div id="device_logs" class="m-content">
	<div class="row">
		<div class="col-lg-3">
			<div class="m-portlet">
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								Application Users
							</h3>
						</div>
					</div>
				</div>
                <form class="m-form m-form--fit">
                <div class="m-portlet__body">
                    <div class="form-group m-form__group row pt-0 pb-0">
                        <div class="col-12">
                            <select class="form-control m-input" id="appUsers" multiple></select>
                            <!-- input id="search-app_users" type="text" name="search" class="form-control m-input" placeholder="Search..." / -->
                        </div>
                        <div class="col-12">
                            <div class="d-flex flex-row justify-content-end mt-4">
                                <button type="button" class="btn btn-warning m-btn m-btn--icon m-btn--pill btnAdvance_search m-btn--sm mr-1 text-white" onclick="resetFilter()">
                                <span>
                                    <i class="fa fa-refresh"></i>
                                    <span>Reset Filter</span>
                                </span>
                                </button>
                                <button type="button" class="btn btn-info m-btn m-btn--icon m-btn--pill btnAdvance_search m-btn--sm" onclick="setAppUsers()">
                                <span>
                                    <i class="fa fa-search"></i>
                                    <span>Find</span>
                                </span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="m-form__seperator m-form__seperator--solid m-form__seperator--space-2x"></div>
                    <div id="app-users" class="form-group m-form__group row pb-0 pt-0">
                        <div class="col-12">
                            <template v-if="loading === true">
                                <h6 class="text-center">Loading App Users, Please Wait...</h6>
                            </template>
                            <template v-else>
                                <template v-if="users.length > 0">
                                <div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" style="min-height: 85px; max-height: 420px; position: relative; overflow: visible;">
                                    <div class="m-widget4">
                                        <div class="m-widget4__item" v-for="user in users">
                                            <div class="m-widget4__img m-widget4__img--pic">
                                                <img :src="user.pic_filename" :alt="user.app_user+'--'+user.id">
                                            </div>
                                            <div class="m-widget4__info">
                                                <span class="m-widget4__title">
                                                    {{ user.app_user }}
                                                </span>
                                                <br>
                                                <span class="m-widget4__sub text-uppercase">{{ user.company_code }}</span>
                                            </div>
                                            <div class="m-widget4__ext">
                                                <a href="javascript:void(0);" class="m-widget4__icon btnView btnViewDeviceLogs" :data-raw="JSON.stringify(user)" @click="previewAppUser(user)">
                                                    <i class="la la-file-text"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div class="col-9">
            <div id="device-logs" class="row">
                <div class="col-12">
                    <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        Device Logs
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="m-portlet__body">
                            <div id="device-logs--preview" class="row mt-3">
                                <template v-if="Object.keys(rawData).length > 0">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="m-widget4">
                                                    <div class="m-widget4__item">
                                                        <div class="m-widget4__img m-widget4__img--pic">
                                                            <img :src="rawData.pic_filename" :alt="rawData.app_user+'--'+rawData.id">
                                                        </div>
                                                        <div class="m-widget4__info">
                                                            <p class="m-widget4__title mb-0">{{ rawData.app_user }}</p>
                                                            <p class="m-widget4__sub text-uppercase m--font-boldest mb-0">{{ rawData.company_code }}</p>
                                                            <p class="m-widget4__sub text-uppercase mb-0">{{ dateTimeFormatter(rawData.last_logged_in) }}</p>
                                                        </div>
                                                        <div class="m-widget4__ext">&nbsp;</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group m-form__group row">
                                                    <label for="deviceLogs" class="col-3 col-form-label text-right">
                                                        Device Logs
                                                    </label>
                                                    <div class="col-9">
                                                        <select class="form-control m-input" id="deviceLogs">
                                                            <option>&nbsp;</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="m-alert m-alert--icon m-alert--icon-solid m-alert--outline alert alert-brand fade show" role="alert">
                                        <div class="m-alert__icon">
                                            <i class="flaticon-exclamation-1"></i>
                                            <span></span>
                                        </div>
                                        <div class="m-alert__text">
                                            <strong>
                                                No Record/s Found!
                                            </strong>
                                            Device Logs not available!
                                        </div>
                                    </div>
                                </template>
                                <template v-if="logs.length > 0">
                                    <div class="m-scrollable--logs mCustomScrollbar _mCS_3 mCS-autoHide" style="min-height: 215px; max-height: 580px; position: relative; overflow: visible;">
                                        <div class="row">
                                            <div class="col-4" v-for="log in logs">
                                                <div class="m-portlet m-portlet--bordered m-portlet--bordered-semi m-portlet--rounded m-portlet--head-sm m-portlet--full-height">
                                                    <div class="m-portlet__head">
                                                        <div class="m-portlet__head-caption">
                                                            <div class="m-portlet__head-title">
                                                                <h3 class="m-portlet__head-text">{{ log.app_type }}</h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="m-portlet__body">
                                                        <div class="m-widget3">
                                                            <div class="m-widget3__item">
                                                                <div class="m-widget3__header">
                                                                    <div class="m-widget3__info p-0">
                                                                        <span class="m-widget3__username text-uppercase">
                                                                            {{ log.action }}
                                                                        </span>
                                                                        <br>
                                                                        <span class="m-widget3__time">
                                                                            {{ dateTimeFormatter(log.app_time) }}
                                                                        </span>
                                                                    </div>
                                                                    <span class="m-widget3__status m--font-info">&nbsp;</span>
                                                                </div>
                                                                <div class="m-widget3__body">
                                                                    <p class="m-widget3__text m--font-bolder text-uppercase">{{ log.message }}</p>
                                                                    <p class="m-widget3__sub mb-0 m--font-bolder">{{ log.data?.reference_no ?? '' }}</p>
                                                                    <p class="m-widget3__sub mb-0 text-uppercase">{{ log.data?.destination ?? '' }}</p>
                                                                    <p class="m-widget3__sub mt-1 text-uppercase">{{ log.data?.location ?? '' }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let selectedAppUsers = [];

    const getActiveAppUsers = ($search) => {
        const resp = $.ajax({
            url: "<?php echo base_url('gcctime/app_users/get_active_app_users'); ?>",
            dataType: "json",
            method: "POST",
            data: {
                search: $search,
                csrf_token: "<?php echo $this->security->get_csrf_hash(); ?>"
            },
            success: function (json) {
                appUsers.users = [];
                if (json.response) {
                    appUsers.users = json.data;
                }
                if(appUsers.searching === false){
                    setTimeout(()=>{
                        appUsers.loading = false;
                    }, 750);
                }
                if($search){ appUsers.searching = false; }
                setTimeout(() => { mApp.initScroller($(".m-scrollable"), {}); }, 750)
            }
        });
    }
    
    const appUsers = new Vue({
        el: "#app-users",
        data: {
            users: [], searching: false, loading: false
        }, methods: {
            previewAppUser: function (user) {
                logPreview.logs = [];
                logPreview.rawData = user;
                setTimeout(() => { logPreview.getDeviceLogs(); }, 750);
            }
        }
    });

    const logPreview = new Vue({
        el: "#device-logs--preview",
        data: {
            logs: [], rawData: {},
        }, methods: {
            dateTimeFormatter: function (datetime) {
                return moment(datetime).format("LLLL");
            }, getDeviceLogs : function () {
                const { emp_id } = this.rawData;
                $.ajax({
                    url: "<?php echo base_url('gcctime/app_users/get_device_log_files'); ?>",
                    method: "POST",
                    data: {
                        emp_id: emp_id,
                        csrf_token: "<?php echo $this->security->get_csrf_hash(); ?>"
                    },
                    dataType: "json",
                    success: function (json) {
                        $("#deviceLogs").select2({
                            width: "100%",
                            placeholder: "Select Device Log",
                            allowClear: true,
                        }).on("select2:select", function (e) {
                            const { id } = e.params.data;
                            $.ajax({
                                url: "<?php echo base_url('gcctime/app_users/get_device_log_json'); ?>",
                                method: "POST",
                                data: {
                                    filename: id,
                                    csrf_token: "<?php echo $this->security->get_csrf_hash(); ?>"
                                },
                                dataType: "json",
                                success: function (json) {
                                    logPreview.logs = [];
                                    if (json.response) {
                                        logPreview.logs = json.data;
                                        setTimeout(() => { mApp.initScroller($(".m-scrollable--logs"), {}); }, 750)
                                    }
                                }
                            });
                        }).empty();
                        if (json.response) {
                            json.data.forEach((log) => {
                                $("#deviceLogs").append(new Option(log.text, log.id, true, true));
                            })
                        }

                       $("#deviceLogs").val(null).trigger("change");
                    }
                });
            }
        }
    });

    jQuery(document).ready(function () {
        appUsers.loading = true;
        getActiveAppUsers();
    });

    jQuery("#search-app_users").donetyping(function (e) {
        appUsers.searching = true;
        getActiveAppUsers($(this).val());
    });

    $("#appUsers").select2({
        width: "100%",
        placeholder: "Select App Users",
        ajax: {
            url: "<?php echo base_url('gcctime/app_users/get_select2_app_users'); ?>",
            dataType: "json",
            method: "POST",
            delay: 750,
            global: false,
            data: function (params) {
                return {
                    search: params.term,
                    csrf_token: "<?php echo $this->security->get_csrf_hash(); ?>"
                };
            }
        }
    }).on("select2:select", function (e) {
        const { data } = e.params;
        selectedAppUsers = selectedAppUsers.filter((user) => user.id !== data.id);

        const tempData = { ...data, app_user: data.text };
        selectedAppUsers.push(tempData);
    }).on("select2:unselect", function (e) {
        const { data } = e.params;
        selectedAppUsers = selectedAppUsers.filter((user) => user.id !== data.id);
    });

    const setAppUsers = () => {
        appUsers.users = [];
        appUsers.loading = true;
        appUsers.users = selectedAppUsers;
        setTimeout(() => {
            appUsers.loading = false;
        }, 250);
    }

    const resetFilter = () => {
        appUsers.users = [];
        selectedAppUsers = [];
        appUsers.loading = true;
        getActiveAppUsers();
    }
</script>