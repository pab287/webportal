var attedances = [];

var _biometric = 0, _roleId = 0, _admin_privilege = false, connection = true, redis_message = '';

if(typeof _tempContentData !== "undefined" && Object.keys(_tempContentData).length > 0){
    if(typeof _tempContentData.biometric_id !== "undefined" && _tempContentData.biometric_id){ _biometric = _tempContentData.biometric_id; }
    if(typeof _tempContentData.roleId !== "undefined" && _tempContentData.roleId){ _roleId = _tempContentData.roleId; }
    if(typeof _tempContentData.admin_privilege !== "undefined" && _tempContentData.admin_privilege){ _admin_privilege = _tempContentData.admin_privilege; }
    if(typeof _tempContentData.connection !== 'undefined' ) { 
        connection = _tempContentData.connection;

        if (!connection) {
            if(typeof _tempContentData.redis_message !== "undefined" && _tempContentData.redis_message){ redis_message = _tempContentData.redis_message; }
        }
    }
}

var vmData = new Vue({
    el: '#data-dashboard',
    data: {
        isShowActivity: false,
        activityLogs: [],
        isShowRealTime: false,
        realTimeLogs: [],
        isShowLate: false,
        late_am: [],
        late_pm: [],
        latelist: false,
        isShowAbsent: false,
        absent_am : [],
        absent_pm : [],
        absentlist: false,
        meridian: "AM",
        ndate: null,
        previousDate: null,
        isShowUt: false,
        utRow: [],
        doubleRow: [],
        isShowDouble: false,
        lackingRow: [],
        isShowLacking: false,
        isShowPersonalLate: false,
        personalLate: [],
        isShowPersonalAbsent: false,
        personalAbsent: [],
        isShowPersonalUndertime: false,
        personalUnder: [],
        isShowPersonalLacking: false,
        personalLacking: [],
        isShowPersonalDouble: false,
        personalDouble: []
    },
    created(){
        var instance = this;

        if (!connection) {
            toastr.error(redis_message, "Error", 10000);
        }

        if ((_roleId == 1 || _roleId == 2) && _admin_privilege == true) {
            instance.showActivityLogs();
            instance.showRealTimeLogs();
            instance.showLate();
            instance.showAbsent();
            instance.getUndertime();
            instance.getDoubleEntry();
            instance.getLackingEntry();
        } else {
            instance.getPersonalLate();
            instance.showPersonalAbsent();
            instance.showPersonalUnder();
            instance.showPersonalLacking();
            instance.showPersonalDouble();
        }
    },
    mounted() {
        var instance = this;
        instance.clockUpdate();
        window.setInterval(instance.clockUpdate, 1000);

        $("#search-real-time-attendances").donetyping(function () {
            var search = $(this).val().trim();
            instance.searchArr(search, attendances);
        }, 1000, 3);

        $("#search-real-time-attendances").on("keyup", function(){
            var search = $(this).val().trim().length;
            if(search == 0){
                instance.realTimeLogs = attendances;
            }
        });

        setTimeout( function(){
            mApp.initScroller($(".m-scrollable"), {});
        }, 750);
    },
    methods: {
        showActivityLogs() {
            var instance = this;

            $("#activity-log #mCSB_1_container").css('height', 'unset !important').css('max-height', 'unset !important');
            
            $.ajax({
                url: baseUrl('gcctime/dashboard/get_activity_logs'),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    instance.isShowActivity = true;
                    instance.activityLogs = response;

                    $("#activity-monitor #overlay").css('display', 'none');
                    $("head").append('<style>#activity-log .m-list-timeline .m-list-timeline__items:before { background-color: #ebedf2 !important; }</style>');
                }
            });
        }, showRealTimeLogs(){
            var instance = this;
            $.ajax({
                url: baseUrl('gcctime/dashboard/get_real_time_attendancesv2'),
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    instance.isShowRealTime = true;
                    instance.realTimeLogs = response;
                    attendances = response;

                    $("#real-time-attendances #overlay").css('display', 'none');
                    
                    if (response.length > 0) {
                        $("#real-time-attendances .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#real-time").css('overflow: visible !important');
                    }
                }
            });
        }, showLate() {
            var instance = this;
            instance.isShowLate = true;

            $.ajax({
                url: baseUrl('gcctime/dashboard/get_late_today_dashboard'),
                dataType: 'json',
                success: function(data){
                    instance.isShowLate = true;
                    $("#late-monitor #overlay").css('display', 'none');
                    $("#late-monitor #redirect-late").removeClass('has-overlay-button');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Late', 5000);
                    }
                    
                    if (!instance.isEmpty(data.checklate_am) || !instance.isEmpty(data.checklate_pm)) {
                        instance.latelist = true;
                        instance.late_am = Object.assign({}, data.checklate_am);
                        instance.late_pm = Object.assign({}, data.checklate_pm);
                        $("#late-scrollable .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#late-scrollable").css('overflow: visible !important');
                        
                    };
                }
            });
        }, showAbsent() {
            var instance = this;

            $.ajax({
                url: baseUrl('gcctime/dashboard/get_absent_today_dashboard'),
                type: "post",
                dataType: 'json',
                data: { csrf_token: _csrf_hash },
                success: function(data){
                    instance.isShowAbsent = true;
                    instance.absentlist = true;
                    instance.absent_am = Object.assign({}, data.check_absent.am);
                    instance.absent_pm = Object.assign({}, data.check_absent.pm);
                    
                    instance.ndate = data.ndate;
                    instance.previousDate = data.previousDate;
                    instance.meridian = data.meridian;

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Absent', 5000);
                    }

                    $("#absent-monitor #overlay").css('display', 'none');
                    $("#absent-monitor #redirect-absent").removeClass('has-overlay-button');
                    
                    if (!instance.isEmpty(data.check_absent.am) || !instance.isEmpty(data.check_absent.pm)) {
                        $("#absent-scrollable .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#absent-scrollable").css('overflow: visible !important');
                    }
                }
            });
        }, getUndertime() {
            var instance = this;

            $.ajax({
                url: baseUrl('gcctime/dashboard/get_undertime_today'),
                methods: 'GET',
                dataType: 'json',
                beforeSend: function() {},
                success: function(data) {
                    instance.isShowUt = true;

                    $("#undertime-monitor #overlay").css('display', 'none');
                    $("#undertime-monitor #redirect-absent").removeClass('has-overlay-button');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Undertime', 5000);
                    }

                    if(!instance.isEmpty(data)) {
                        instance.utRow = data;
                        $("#ut-list .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#ut-list").css('overflow: visible !important');
                    }
                }
            });
        }, getDoubleEntry() {
            var instance = this;

            $.ajax({
                url: baseUrl('gcctime/dashboard/get_double_yesterday'),
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {},
                success: function(data) {
                    instance.isShowDouble = true;

                    $("#double-monitor #overlay").css('display', 'none');
                    $("#double-monitor #redirect-absent").removeClass('has-overlay-button');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Double Entries', 5000);
                    }

                    if (!instance.isEmpty(data.double_entry)) {
                        instance.doubleRow = data;
                        $("#double_list .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#double_list").css('overflow: visible !important');
                    }
                }
            });
        }, getLackingEntry() {
            var instance = this;
            $.ajax({
                url: baseUrl('gcctime/dashboard/get_lacking_yesterday'),
                type: 'GET',
                dataType: 'json',
                beforeSend: function(){},
                success: function(data){
                    instance.isShowLacking = true;

                    $("#lacking-monitor #overlay").css('display', 'none');
                    $("#lacking-monitor #redirect-absent").removeClass('has-overlay-button');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Lacking Entries', 5000);
                    }

                    if (!instance.isEmpty(data.lacking_entry)) {
                        instance.lackingRow = data;
                        $("#lacking_list .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#lacking_list").css('overflow: visible !important');
                    }
                }   			
            });
        }, getPersonalLate(){
            var instance = this;

            $.ajax({
				url: baseUrl('gcctime/dashboard/get_personnel_today/'+ _biometric),
				dataType: 'json',
				success: function(data){
                    instance.isShowPersonalLate = true;

                    $("#late-monitor #overlay").css('display', 'none');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Late', 5000);
                    }

					if (!instance.isEmpty(data)) {
						instance.personalLate = data.lates;
                        $("#personal-late-scrollable .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#personal-late-scrollable").css('overflow: visible !important');
					}
				}
			});
        }, showPersonalAbsent(){
            var instance = this;

            $.ajax({
                url: baseUrl('gcctime/dashboard/get_personnel_absent/'+ _biometric),
				dataType: 'json',
				success: function(data){
                    instance.isShowPersonalAbsent = true;

                    $("#absent-monitor #overlay").css('display', 'none');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Absent', 5000);
                    }

					if (!instance.isEmpty(data)) {
						instance.personalAbsent = data;
                        $("#personal-absent-scrollable .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#personal-absent-scrollable").css('overflow: visible !important');
					}
				}
			});
        }, showPersonalUnder(){
            var instance = this;

            $.ajax({
                url: baseUrl('gcctime/dashboard/get_personnel_undertime/'+ _biometric),
				dataType: 'json',
				success: function(data){
                    instance.isShowPersonalUndertime = true;

                    $("#undertime-monitor #overlay").css('display', 'none');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Undertime', 5000);
                    }

					if (!instance.isEmpty(data.undertime)) {
						instance.personalUnder = data.undertime;
                        $("#personal-under-scrollable .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#personal-under-scrollable").css('overflow: visible !important');
					}
				}
			});
        }, showPersonalLacking(){
            var instance = this;

            $.ajax({
                url: baseUrl('gcctime/dashboard/get_personal_lacking_entry/'+ _biometric),
                dataType: "json",
                success: function(data){
                    instance.isShowPersonalLacking = true;
                    
                    $("#lacking-monitor #overlay").css('display', 'none');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Lacking Entries', 5000);
                    }

                    if (!instance.isEmpty(data.lacking_entry)) {
                        instance.personalLacking = data.lacking_entry;
                        $("#personal-lacking-scrollable .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#personal-lacking-scrollable").css('overflow: visible !important');
                    }
                }
            });
        }, showPersonalDouble(){
            var instance = this;

            $.ajax({
                url: baseUrl('gcctime/dashboard/get_personal_double_entry/'+ _biometric),
                dataType: "json",
                success: function(data){
                    instance.isShowPersonalDouble = true;
                    
                    $("#double-monitor #overlay").css('display', 'none');

                    if (typeof data.no_cache !== 'undefined' && data.no_cache) {
                        toastr.error('Data was not Cached as the server is not reachable', 'Double Entries', 5000);
                    }

                    if (!instance.isEmpty(data.double_entry)) {
                        instance.personalDouble = data.double_entry;
                        $("#personal-double-scrollable .mCSB_container").css('height', 'unset').css('max-height', 'unset');
                        $("#personal-double-scrollable").css('overflow: visible !important');
                    }
                }
            });
        }, isEmpty(arr) {
            return $.isEmptyObject(arr);
        }, statusClass(status) {
            var statusClass = '';

            switch (status) {
                case 'success':
                    statusClass = '';
                    break;
                case 'error':
                    statusClass = 'text-danger custom-text-weight';
                    break;
                case 'warning':
                    statusClass = 'text-warning custom-text-weight';
                    break;
                case 'info':
                    statusClass = 'text-info custom-text-weight';
                    break;
            }

            return statusClass;
        }, clockUpdate(){
            var $dOut = $('#date'),
            $hOut = $('#hours'),
            $mOut = $('#minutes'),
            $sOut = $('#seconds'),
            $ampmOut = $('#ampm');
            var months = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            var days = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var date = new Date();
    
            var ampm = date.getHours() < 12 ? 'AM' : 'PM';
            var hours = date.getHours() == 0 ? 12 : date.getHours() > 12 ? date.getHours() - 12 : date.getHours();
            var minutes = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
            var seconds = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
            
            var dayOfWeek = days[date.getDay()];
            var month = months[date.getMonth()];
            var day = date.getDate();
            var year = date.getFullYear();
            
            var dateString = dayOfWeek + ' ' + month + ' ' + day + ', ' + year;
            
            $dOut.text(dateString);
            $hOut.text(hours);
            $mOut.text(minutes);
            $sOut.text(seconds);
            $ampmOut.text(ampm);
        }, dateFormat(date){
            return moment(date).format("hh:mm A");
        }, searchArr(search, arr){
            let inSearch = false;
            let empInSearch = [];
            var instance = this;

            $.grep(arr, function(item, index) {
                if (arr[index].employee_name.toLowerCase().includes(search.toLowerCase()) || arr[index].biometricno.includes(search.toLowerCase())){
                    inSearch = true;
                    var biometric = arr[index].biometricno;

                    empInSearch[biometric] = arr[index];
                }
            });

            if (!inSearch){
                empInSearch = [];
            }

            instance.realTimeLogs = Object.assign({}, empInSearch);
        }, date(date) {
            return moment(date).format("YYYY-MM-DD");
        }, timeFormat(date) {
            return moment(date).format("hh:mm A");
        }, wordDate(date) {
            return moment(date).format("LL");
        }, redirect(type) {
            window.location.href = baseUrl('gcctime/reports/index?type='+ type);
        }
    }
});