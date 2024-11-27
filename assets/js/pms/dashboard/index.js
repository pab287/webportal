const awaiting = $("#collapseAwaiting > .m-widget4");
const inProgress = $("#collapseInProgress > .m-widget4");
const backlogs = $("#collapseBackLogs > .m-widget4");
const deferred = $("#collapseDeferred > .m-widget4");
const weekly = $("#collapseWeekly > .m-widget4");
const monthly = $("#collapseMonthly > .m-widget4");
const modalContainer = $("#modal-container");

const noRecordTemplate = `<div class="px-4 m-widget4__item" style="width: 100%;">
                               <div class="m-widget4__info">
                                   <span class="m-widget4__text">
                                       NO RECORD TO SHOW.
                                   </span>
                               </div>
                           </div>`;

$(".m-wrapper").css("margin-bottom", 0);

$('body').tooltip({
    selector: '[data-toggle="m-tooltip"]'
});

function toggleCollapse(collapsible, el) {
    $(collapsible)
        .on("show.bs.collapse", function () {
            $(el).find("span").html("Hide Details");
        });


    $(collapsible)
        .on("hide.bs.collapse", function () {
            $(el).find("span").html("Show Details");
        });

    $(collapsible).collapse("toggle");
}

$.ajax({
    url: baseUrl("pms/dashboard/get_unit_task_demographics"),
    type: "GET",
    dataType: "JSON",
    success: function (response) {
        awaiting.empty("");
        inProgress.empty("");
        backlogs.empty("");
        deferred.empty("");

        const status_based = response.status_based;

        status_based.forEach((item) => {
            if (parseInt(item.awaiting) > 0) {
                awaiting
                    .append('' +
                        '   <div class="px-4 m-widget4__item"' +
                        '        onclick="openTodosModal(' + item.id + ')">' +
                        '       <div class="m-widget4__info">' +
                        '           <span class="m-widget4__text">' +
                        '               ' + item.description +
                        '           </span>' +
                        '       </div>' +
                        '       <div class="m-widget4__ext">' +
                        '           <span class="m-widget4__number m--font-dark">' +
                        '               ' + item.awaiting +
                        '           </span>' +
                        '       </div>' +
                        '   </div>');
            }

            if (parseInt(item.inProgress) > 0) {
                inProgress
                    .append('' +
                        '   <div class="px-4 m-widget4__item"' +
                        '        onclick="openTodosModal(' + item.id + ')">' +
                        '       <div class="m-widget4__info">' +
                        '           <span class="m-widget4__text">' +
                        '               ' + item.description +
                        '           </span>' +
                        '       </div>' +
                        '       <div class="m-widget4__ext">' +
                        '           <span class="m-widget4__number m--font-primary">' +
                        '               ' + item.inProgress +
                        '           </span>' +
                        '       </div>' +
                        '   </div>');
            }

            if (parseInt(item.backlogs) > 0) {
                backlogs
                    .append('' +
                        '   <div class="px-4 m-widget4__item"' +
                        '        onclick="openTodosModal(' + item.id + ')">' +
                        '       <div class="m-widget4__info">' +
                        '           <span class="m-widget4__text">' +
                        '               ' + item.description +
                        '           </span>' +
                        '       </div>' +
                        '       <div class="m-widget4__ext">' +
                        '           <span class="m-widget4__number m--font-danger">' +
                        '               ' + item.backlogs +
                        '           </span>' +
                        '       </div>' +
                        '   </div>');
            }

            if (parseInt(item.deferred) > 0) {
                deferred
                    .append('' +
                        '   <div class="px-4 m-widget4__item"' +
                        '        onclick="openTodosModal(' + item.id + ')">' +
                        '       <div class="m-widget4__info">' +
                        '           <span class="m-widget4__text">' +
                        '               ' + item.description +
                        '           </span>' +
                        '       </div>' +
                        '       <div class="m-widget4__ext">' +
                        '           <span class="m-widget4__number m--font-success">' +
                        '               ' + item.deferred +
                        '           </span>' +
                        '       </div>' +
                        '   </div>');
            }
        });

        determineScroll(awaiting, "collapseAwaiting");
        determineScroll(inProgress, "collapseInProgress");
        determineScroll(backlogs, "collapseBackLogs");
        determineScroll(deferred, "collapseDeferred");


        const awaitingTotal = status_based.reduce((acc, item) => {
            return acc + item.awaiting;
        }, 0);

        const inProgressTotal = status_based.reduce((acc, item) => {
            return acc + item.inProgress;
        }, 0);

        const deferredTotal = status_based.reduce((acc, item) => {
            return acc + item.deferred;
        }, 0);

        const backlogsTotal = status_based.reduce((acc, item) => {
            return acc + item.backlogs;
        }, 0);

        const total = awaitingTotal + inProgressTotal + deferredTotal;
        $("#awaiting-total").html(awaitingTotal);
        $("#in-progress-total").html(inProgressTotal);
        $("#deferred-total").html(deferredTotal);
        $("#backlogs-total").html(backlogsTotal);

        const pcgAwaiting = Math.floor((awaitingTotal / total) * 100);
        const pcgInProgress = Math.floor((inProgressTotal / total) * 100);
        const pcgDeferred = Math.floor((deferredTotal / total) * 100);
        const pcgBackLogs = Math.floor((backlogsTotal / total) * 100);

        $("#pbAwaiting").attr("aria-valuenow", pcgAwaiting).css("width", pcgAwaiting + "%");
        $("#pbInProgress").attr("aria-valuenow", pcgInProgress).css("width", pcgInProgress + "%")
        $("#pbDeferred").attr("aria-valuenow", pcgDeferred).css("width", pcgDeferred + "%");
        $("#pbBacklogs").attr("aria-valuenow", pcgBackLogs).css("width", pcgBackLogs + "%");

        if (awaitingTotal <= 0) {
            awaiting.append(noRecordTemplate);
            const list = awaiting.closest('.collapse');
            $(list).css("overflow", "hidden");
        }

        if (inProgressTotal <= 0) {
            inProgress.append(noRecordTemplate);
        }

        if (deferredTotal <= 0) {
            deferred.append(noRecordTemplate);
        }

        if (backlogsTotal <= 0) {
            backlogs.append(noRecordTemplate);
        }

        const goals = response.goals;
        const _weekly = goals.weekly;
        const _monthly = goals.monthly;

        if (_weekly.length >= 1) {
            _weekly.forEach((unit) => {
                weekly
                    .append('' +
                        '   <div class="px-4 m-widget4__item"' +
                        '        onclick="openTodosModal(' + unit.id + ')">' +
                        '       <div class="m-widget4__info">' +
                        '           <div class="m-widget4__text">' +
                        '               ' + unit.description +
                        '           </div>' +
                        '           <div class="m-widget4__text m--regular-font-size-sm1 d-inline m--font-info" ' +
                        '                data-toggle="m-tooltip" data-original-title="Due Date" data-placement="right"' +
                        '                data-delay=\'{"show": 300}\'>' +
                        '               ' + moment(unit.due_date).format('ll').toUpperCase() +
                        '           </div>' +
                        '       </div>' +
                        '       <div class="m-widget4__ext" style="vertical-align: text-top;">' +
                        '           <span class="m-widget4__number m--font-info">' +
                        '               ' + unit.task_count +
                        '           </span>' +
                        '       </div>' +
                        '   </div>');

                const totalWeeklyTask = _weekly.reduce((acc, unit) => {
                    return acc + parseFloat(unit.task_count);
                }, 0);

                const pcgTotalWeeklyTask = Math.floor((totalWeeklyTask / total) * 100);
                $("#weekly-total").html(totalWeeklyTask);
                $("#pbWeekly").attr("aria-valuenow", pcgTotalWeeklyTask).css("width", pcgTotalWeeklyTask + "%");
            });
        } else {
            weekly.append(noRecordTemplate);
        }
        determineScroll(weekly, "collapseWeekly");

        if (_monthly.length >= 1) {
            _monthly.forEach((unit) => {
                monthly
                    .append('' +
                        '   <div class="px-4 m-widget4__item"' +
                        '        onclick="openTodosModal(' + unit.id + ')">' +
                        '       <div class="m-widget4__info">' +
                        '           <div class="m-widget4__text">' +
                        '               ' + unit.description +
                        '           </div>' +
                        '           <div class="m-widget4__text m--regular-font-size-sm1 d-inline m--font-info" ' +
                        '                data-toggle="m-tooltip" data-original-title="Due Date" data-placement="right"' +
                        '                data-delay=\'{"show": 300}\'>' +
                        '               ' + moment(unit.due_date).format('ll').toUpperCase() +
                        '           </div>' +
                        '       </div>' +
                        '       <div class="m-widget4__ext" style="vertical-align: text-top;">' +
                        '           <span class="m-widget4__number m--font-info">' +
                        '               ' + unit.task_count +
                        '           </span>' +
                        '       </div>' +
                        '   </div>');

                const totalMonthlyTask = _monthly.reduce((acc, unit) => {
                    return acc + parseFloat(unit.task_count);
                }, 0);

                const pcgTotalMonthlyTask = Math.floor((totalMonthlyTask / total) * 100);
                $("#monthly-total").html(totalMonthlyTask);
                $("#pbMonthly").attr("aria-valuenow", pcgTotalMonthlyTask).css("width", pcgTotalMonthlyTask + "%");
            });
        } else {
            monthly.append(noRecordTemplate);
        }
        determineScroll(monthly, "collapseMonthly");
    }
});

function determineScroll(div, elementID) {
    const cardContainer = $(div).closest('.pms-status');
    const listContainer = $(`#${elementID} .m-widget4`, cardContainer);

    if (parseFloat(listContainer.height()) <= cardContainer.height()) {
        $(`div#${elementID}`).css('overflow', 'hidden');
    }
}

function openTodosModal(unit_id) {
    $.ajax({
        url: baseUrl("pms/dashboard/open_modal"),
        type: "POST",
        dataType: "JSON",
        data: {
            csrf_token: _csrf_hash,
            path: "pms/dashboard/modal/to_dos_modal",
            function_name: "plotTodoList",
            model: "pms/Dashboard_m",
            formData: {unit_id},
            init_modal_data_function: "",
        },
        success: function (response) {
            const html = response.html;
            modalContainer.empty().append(html);
            modalContainer.modal("show");
        }
    });
}