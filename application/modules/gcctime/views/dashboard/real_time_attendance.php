<style type="text/css">
    .custom-search-box {
        border: 1px solid #efefef;
        outline: none;
        border-radius: 3em;
        padding: 4px 14px;
        transition: border-color 250ms;
    }

    .custom-search-box::placeholder {
        color: #b1b1b1;
    }

    .custom-search-box:hover, .custom-search-box:focus {
        border-color: #b4b4b4;
    }
</style>

<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
    <div class="m-portlet m-portlet">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">TODAY'S ATTENDANCE</h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <input type="text" class="custom-search-box" placeholder="Search here..."
                       id="search-real-time-attendances">
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true"
                 data-max-height="400" style="height: 400px; overflow: visible; max-height: 400px; position: relative;">
                <div class="m-list-timeline m-list-timeline--skin-light">
                    <div class="m-list-timeline__items" id="real-time-attendances">
                        <div class="m-list-timeline__item header" style="padding: 0;">
                            <span class="m-list-timeline__text" style="width: 70%;"><strong>EMPLOYEE</strong></span>
                            <span class="m-list-timeline__text text-right"><strong>TIME</strong></span>
                        </div>

                        <?php if (sizeof($logs) >= 1): ?>
                            <?php foreach ($logs as $log): ?>
                                <div class="m-list-timeline__item" style="padding: 0;">
                                    <span class="m-list-timeline__text" style="width: 70%;">
                                        <small><?= $log->employee_name ?></small>
                                    </span>
                                    <span class="m-list-timeline__text text-right">
                                        <small><?= date("h:i A", strtotime($log->date)) ?></small>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $("#search-real-time-attendances")
        .donetyping(function () {
            triggerRealtimeAttendance($(this).val());
        });
    
    var triggerRealtimeAttendance = function($search){
        $.ajax({
            url: baseUrl(`gcctime/dashboard/get_real_time_attendances`),
            dataType: "JSON",
            type: "POST",
            data: {
                csrf_token: _csrf_hash,
                search: $search,
            },
            global: false,
            success: function (response) {
                $("#real-time-attendances .m-list-timeline__item:not(.header)").remove();
                if (response) {
                    response.forEach((item) => {
                        const template = `<div class="m-list-timeline__item" style="padding: 0;">
                            <span class="m-list-timeline__text" style="width: 70%;">
                                <small>${item.employee_name}</small>
                            </span>
                            <span class="m-list-timeline__text text-right">
                                <small>${moment(item.date).format("hh:mm A")}</small>
                            </span>
                            </div>`;
                        $("#real-time-attendances").append(template);
                    });
                }
            }
        });
    }
</script>