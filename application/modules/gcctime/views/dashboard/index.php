<style type="text/css">
    /* #latelist:before, #real-time-attendances:before {
        display: none !important;
    } */

    /* clock.php */
    /*** @font-face {
		font-family: "palanquin";
		src: url("<?php echo base_url('assets/fonts/palanquin.ttf')?>") format('truetype');
	}
	@import 'https://fonts.googleapis.com/css?family=Open+Sans'; **/
	*{
	  margin: 0;
	  padding: 0;
	}
	body{
	  background: radial-gradient(#eee, #668);
	  height: 100vh;
	  width: 100vw;
	}
	#date{
	  color: #575962;
	  font-family: "palanquin", sas-serif;
	  font-size: 2em;
	  text-align: center;
	}
	.date-container{
		align-items: center;
	    -webkit-align-items: center;
	    display: flex;
	    display: -webkit-flex;
	    left: calc(50% - 300px);
	    /* top: calc(50% - 65px); */
	    /* width: 700px; */
		margin: 0 auto;
	}
	#clock{
		align-items: center;
	    -webkit-align-items: center;
	    display: flex;
	    display: -webkit-flex;
	    height: 130px;
	    justify-content: space-around;
	    -webkit-justify-content: space-around;
	    left: calc(50% - 300px);
	    top: calc(50% - 0px);
	    width: 700px;
	    margin: 0 auto;
	}
	.unit{
	 	 /*background: linear-gradient(#aaa, #777); */
	    border-radius: 15px;
	    /* box-shadow: 0 2px 2px #444; */
	    color: #575962;
	    font-family: "Open Sans", sans-serif;
	    font-size: 5em;
	    line-height: 110px;
	    margin: 0 10px;
	    text-align: center;
	    /* text-shadow: 0 2px 2px #666; */
	    font-family: "palanquin";
	}
	.clock-separator{
		font-family: "palanquin";
    	font-size: 4em;
    	color: #575962;
	}
	.date-container {
		text-transform: uppercase;
	}
	
	/*** notification ***/
	.m-custom-portlet p.m-list_notification-item {
		font-size: 10px;
		margin: 0;
		line-height: 18px;
	}
	.m-custom-portlet span.m-list-timeline__time.custom-time_item {
		width: 35%;
		font-size: 8px;
	}
	span.m-list-timeline__text.custom-text-weight {
		font-weight: 500;
	}
	/*** notification ***/

	/* #activity-log .mCSB_container{
		height: 100%;
    	max-height: 100%;
	} */

    .mCSB_container{
        height: 100%;
    	max-height: 100%;
    }

    .empty-list {
        position: relative;
    }

    .empty-list .m-list-timeline__items, .empty-list .m-list-timeline__items {
        position: absolute;
        right: 0;
        top: 150px;
        bottom: 0px;
    }

    #admin #double-monitor .empty-list .m-list-timeline__items, #admin #undertime-monitor .empty-list .m-list-timeline__items{
        position: absolute;
        right: 0;
        top: 30px !important;
        bottom: 0px;
    }

    #personal #double-monitor .empty-list .m-list-timeline__items, #personal #undertime-monitor .empty-list .m-list-timeline__items{
        position: absolute;
        right: 0;
        top: 110px !important;
        bottom: 0px;
    }

    #personal .empty-list .m-list-timeline__items, .empty-list .m-list-timeline__items {
        position: absolute;
        right: 0;
        top: 110px;
        bottom: 0px;
    }

	.no-show-list{
		transform: translate(0px, 35%);
	}
	/* #activity-log .m-list-timeline .m-list-timeline__items:before, #latemonitor .m-list-timeline .m-list-timeline__items:before{
		background-color: unset !important;
	} */

    #activity-log .empty-list {
        /* height: 218px !important; */
        position: relative;
    }

    #activity-log .empty-list .m-list-timeline__items {
        position: absolute;
        right: 0;
        top: 80px;
        bottom: 0px;
    }
    /* clock.php */

    /* real-time-attendances */
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

    #real-time .m-list-timeline .m-list-timeline__items:before, #real-time .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #real-time .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before{
        display: none;
    }
    /* real-time-attendances */

    /* latemonitor */
    #late-monitor .m-list-timeline .m-list-timeline__items:before, #late-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #late-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before{
        background-color: unset !important;
    }
    /* latemonitor */

    /* absent */
    #absent-monitor .m-list-timeline .m-list-timeline__items:before, #absent-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #absent-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before{
        background-color: unset !important;
    }
    /* absent */

    /* personal */
    #personal-late-scrollable .m-list-timeline .m-list-timeline__items:before, #personal-absent-scrollable .m-list-timeline .m-list-timeline__items:before, #personal-under-scrollable .m-list-timeline .m-list-timeline__items:before, #personal-lacking-scrollable .m-list-timeline .m-list-timeline__items:before, #personal-double-scrollable .m-list-timeline .m-list-timeline__items:before{
        background-color: unset !important;
    }
    #personal-late-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #personal-late-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before, #personal-absent-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #personal-absent-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before, #personal-under-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #personal-under-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before, #personal-lacking-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #personal-lacking-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before, #personal-double-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #personal-double-scrollable .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before{
        background-color: unset !important;
    }
    /* personal */

    .m-page{
        overflow-x: hidden;
    }

    .m-portlet {
        position: relative;
    }

    #overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, .2);
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .has-overlay-button {
        pointer-events: none;
        opacity: 0.5;
    }


    #undertime-monitor .m-list-timeline .m-list-timeline__items:before, #undertime-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #undertime-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before{
        background-color: unset !important;
    }

    #double-monitor-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #double-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before, #double-monitor .m-list-timeline .m-list-timeline__items:before {
        background-color: unset !important;
    }

    #lacking-monitor-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:first-child:before, #lacking-monitor .m-list-timeline .m-list-timeline__items .m-list-timeline__item:last-child:before, #lacking-monitor .m-list-timeline .m-list-timeline__items:before {
        background-color: unset !important;
    }
</style>

<div class="m-content" id="data-dashboard">
    <!-- clock -->
    <?php $colWidth = (($roleId == 1 || $roleId == 2) || $admin_privilege == true)? "8":"12"; ?>
        <div class="row">
            <div class="col-xl-<?=$colWidth; ?> col-md-<?=$colWidth; ?>">
                <div class="m-portlet m-portlet--mobile">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                    <?=strtoupper("Company Standard Time"); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__body m-custom-portlet">
                        <div class="row">
                            <div class="date-container text-center">
                                <p id="date"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-12 text-center">&nbsp;</div>
                            <div class="col-md-2 col-4 text-center" >
                                <h1 class="unit">
                                    <span id="hours" class="custom-unit_data"></span>
                                </h1>
                            </div>
                            <div class="col-md-2 col-4 text-center">
                                <h1 class="unit">
                                    <span id="minutes" class="custom-unit_data"></span>
                                </h1>
                            </div>
                            <div class="col-md-2 col-4 text-center">
                                <h1 class="unit">
                                    <span id="seconds" class="custom-unit_data"></span>
                                </h1> 
                            </div>
                            <div class="col-md-2 col-12 text-center">
                                <h1 class="unit">
                                    <span id="ampm" class="custom-unit_data"></span>
                                </h1>
                            </div>
                            <div class="col-md-2 col-12 text-center">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(($roleId == 1 || $roleId == 2) || $admin_privilege == true): ?>
                <div class="col-xl-4 col-md-4">
                    <div class="m-portlet m-portlet--mobile" id="activity-monitor">
                        <div id="overlay">
                            <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                        </div>
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">ACTIVITY LOG</h3>
                                </div>
                            </div>
                        </div>
                        <div class="m-portlet__body m-custom-portlet" id="activity-log">
                            <div class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="218" style="height: 218px; overflow: visible; max-height: 218px; position: relative;" >
                                <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(activityLogs) ? 'empty-list' : ''">
                                    <div class="m-list-timeline__items">
                                        <!-- <template v-if="!isShowActivity">
                                            <div class="text-center">
                                                <button id="showActivityLogs" class="btn" style="width: 80%; background-color: rgb(86, 78, 192);" @click="showActivityLogs">
                                                    <h3 class="m-widget1__title text-muted" style="font-size: 10px !important;"></h3>
                                                    <h3 class="m-widget1__title" style="font-size: 10px !important; color: white;">
                                                        SHOW DATA
                                                    </h3>
                                                </button>
                                            </div>
                                        </template> -->
                                        <template v-if="isShowActivity">
                                            <template v-if="!isEmpty(activityLogs)">
                                                <template v-for="(item, index) in activityLogs">
                                                    <div class="m-list-timeline__item">
                                                        <span class="m-list-timeline__badge" :class="'m-list-timeline__badge--' + item.status"></span>
                                                        <span class="m-list-timeline__text" :class="statusClass(item.status)">
                                                            <p class="m-list_notification-item">{{ item.notification.toUpperCase() }}</p>
                                                        </span>
                                                        <span class="m-list-timeline__time custom-time_item">{{ item.formatted_created_at.toUpperCase() }}</span>
                                                    </div>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <!-- <div class="m-list-timeline__item" style="padding: 0; transform: translate(0px, 350%);"> -->
                                                <div class="m-list-timeline__item" style="padding: 0;">
                                                    <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                        <h5>NO ACTIVITY LOG FOUND</h5>
                                                    </span>
                                                </div>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <!-- clock -->

    <?php if (($roleId == 1 || $roleId == 2) || $admin_privilege == true): ?>
        <div class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                <div class="m-portlet m-portlet" id="real-time-attendances">
                    <div id="overlay">
                        <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                    </div>
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">TODAY'S ATTENDANCE</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <input type="text" class="custom-search-box" placeholder="Search here..." id="search-real-time-attendances" :disabled="!isShowRealTime ? 'disabled' : false">
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget6">
                            <div class="m-widget6__head">
                                <div class="m-widget6__item" style="margin-bottom: 5px">					 
                                    <span class="m-widget6__caption" style="width: 25%;"><small>EMPLOYEE NAME</small></span>
                                    <span class="m-widget6__caption m--align-right"><small>TIME</small></span>					 
                                </div>
                            </div>
                            <div class="m-widget6__body">
                                <div id="real-time" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="400" style="height: 400px; max-height: 400px; overflow: visible; position: relative;">
                                    <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(realTimeLogs) ? 'empty-list' : ''">
                                        <div class="m-list-timeline__items">
                                            <template v-if="isShowRealTime">
                                                <template v-if="!isEmpty(realTimeLogs)">
                                                    <template v-for="(item, index) in realTimeLogs">
                                                        <div class="m-list-timeline__item" style="padding: 0;">
                                                            <span class="m-list-timeline__text" style="width: 100%;">
                                                                <small>{{ item.employee_name }}</small>
                                                            </span>
                                                            <span class="m-list-timeline__text text-right">
                                                                <small>{{ dateFormat(item.date) }}</small>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                            <h5>NO DATA FOUND</h5>
                                                        </span>
                                                    </div>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <div class="m-list-timeline__item" style="padding: 0;">
                                                    <span class="m-list-timeline__text text-center" style="width: 100%; padding: 20px 0;"></span>
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
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                <div class="m-portlet m-portlet" id="late-monitor">
                    <div id="overlay">
                        <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                    </div>
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">LATE <small>( AS OF <?php echo strtoupper($currentDate); ?> )</small></h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <button id="redirect-late" class="btn btnNew btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill has-overlay-button" style="float: right;" data-skin="dark" data-toggle="m-tooltip" data-placement="left" data-original-title="Go To Late Report" @click="redirect('late')">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget6">
                            <div class="m-widget6__head">
                                <div class="m-widget6__item" style="margin-bottom: 5px">					 
                                    <span class="m-widget6__caption" style="width: 25%;"><small>EMPLOYEE NAME</small></span>
                                    <span class="m-widget6__caption m--align-right"><small>TIME</small></span>							 
                                </div>
                            </div>
                            <div id="latemonitor" class="m-widget6__body">
                                <div id="late-scrollable" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="400" style="height: 400px; max-height: 400px; overflow: visible; position: relative;">
                                    <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(late_am) && isEmpty(late_pm) ? 'empty-list' : ''">
                                        <div class="m-list-timeline__items">
                                            <template v-if="isShowLate">
                                                <template v-if="latelist">
                                                    <template v-if="!isEmpty(late_pm)">
                                                        <template v-for="lates in late_pm">
                                                            <div class='m-list-timeline__item' style='padding: 0 !important;margin: 0 auto;'>
                                                                <span class='m-list-timeline__text'>
                                                                    <small :class="lates.state == 'exceed' ? 'm--font-boldest' : ''">
                                                                        {{ lates.name ? lates.name.toUpperCase() : lates.biometric_id }}
                                                                    </small>
                                                                </span>
                                                                <span class='m-list-timeline__text text-right' style='width: 25%;'>
                                                                    <small :class="lates.state == 'exceed' ? 'm--font-boldest' : ''">
                                                                        {{ lates.time }}
                                                                    </small>
                                                                </span>
                                                                <span class='m-list-timeline__text text-right' style='width: 25%;'>
                                                                    <small :class="lates.state == 'exceed' ? 'm--font-boldest' : ''">
                                                                        {{ date(lates.date) }}
                                                                    </small>
                                                                </span>
                                                            </div>
                                                        </template>
                                                    </template>

                                                    <template v-if="!isEmpty(late_am)">
                                                        <template v-for="lates in late_am">
                                                            <div class='m-list-timeline__item' style='padding: 0 !important;margin: 0 auto;'>
                                                                <span class='m-list-timeline__text'>
                                                                    <small :class="lates.state == 'exceed' ? 'm--font-boldest' : ''">
                                                                        {{ lates.name }}
                                                                    </small>
                                                                </span>
                                                                <span class='m-list-timeline__text text-right' style='width: 25%;'>
                                                                    <small :class="lates.state == 'exceed' ? 'm--font-boldest' : ''">
                                                                        {{ lates.time }}
                                                                    </small>
                                                                </span>
                                                                <span class='m-list-timeline__text text-right' style='width: 20%;'>
                                                                    <small :class="lates.state == 'exceed' ? 'm--font-boldest' : ''">
                                                                        {{ date(lates.date) }}
                                                                    </small>
                                                                </span>
                                                            </div>
                                                        </template>
                                                    </template>

                                                    <template v-if="isEmpty(late_am) && isEmpty(late_pm)">
                                                        <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                            <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                <h5>NO DATA FOUND</h5>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
                                                        <!-- <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                            <h5>NO DATA FOUND</h5>
                                                        </span> -->
                                                    </div>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <div class="m-list-timeline__item" style="padding: 0;">
                                                    <span class="m-list-timeline__text text-center" style="width: 100%; padding: 20px 0;"></span>
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
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                <div class="m-portlet m-portlet" id="absent-monitor">
                    <div id="overlay">
                        <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                    </div>
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                    ABSENT <small>( AS OF <?php echo strtoupper($currentDate); ?> )</small>
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools">
                            <button id="redirect-absent" class="btn btnNew btn-default m-btn m-btn--icon m-btn--icon-only m-btn--pill has-overlay-button" style="float: right;" data-skin="dark" data-toggle="m-tooltip" data-placement="left" data-original-title="Go To Absentees Report" @click="redirect('absent')">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget6">
                            <div class="m-widget6__head">
                                <div class="m-widget6__item" style="margin-bottom: 5px">					 
                                    <span class="m-widget6__caption" style="width: 25%;">
                                        <small>EMPLOYEE NAME</small>
                                    </span>
                                    <span class="m-widget6__caption m--align-right" style="width: 49%;">
                                        <small>LOA/TO REFERENCE #</small>
                                    </span>					 
                                    <span class="m-widget6__caption m--align-right">
                                        <small>TIME</small>
                                    </span>
                                </div>
                            </div>
                            <div id="absentmonitor" class="m-widget6__body">
                                <div id="absent-scrollable" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" max0-height="400" style="height: 400px; max-height: 400px; overflow: visible; position: relative;">
                                    <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(absent_am) && isEmpty(absent_pm) ? 'empty-list' : ''">
                                        <div class="m-list-timeline__items" id="absentslist">
                                            <template v-if="isShowAbsent">
                                                <template v-if="absentlist">
                                                    <template v-if="ndate == previousDate">
                                                        <template v-if="!isEmpty(absent_am)">
                                                            <template v-for="(item, index) in absent_am">
                                                                <div class="m-list-timeline__item" style="padding: 0 !important; margin: 0 auto;">
                                                                    <div style='padding: 0 !important; margin: 0 auto;' class='m-list-timeline__item'>
                                                                        <span class='m-list-timeline__text' style="width: 56%">
                                                                            <small>{{item.name ? item.name.toUpperCase() : item.biometricno }}</small>
                                                                        </span>
                                                                        <span class='m-list-timeline__text' style='width: 100%; text-align: center'>
                                                                            <small>
                                                                                {{ item.content ? item.content : 'N/A' }}
                                                                            </small>
                                                                        </span>
                                                                        <span style='width: 15%;' class='m-list-timeline__time'>{{ item.mrdn }}</span>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </template>
                                                        <template v-if="!isEmpty(absent_pm)">
                                                            <template v-for="(item, index) in absent_pm">
                                                                <div class="m-list-timeline__item" style="padding: 0 !important; margin: 0 auto;">
                                                                    <div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
                                                                        <span class='m-list-timeline__text' style="width: 56%">
                                                                            <small>{{item.name ? item.name.toUpperCase() : item.biometricno }}</small>
                                                                        </span>
                                                                        <span class='m-list-timeline__text' style='width: 100%; text-align: center'>
                                                                            <small>
                                                                                {{ item.content ? item.content : 'N/A' }}
                                                                            </small>
                                                                        </span>
                                                                        <span style='width: 15%;' class='m-list-timeline__time'>{{ item.mrdn }}</span>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </template>
                                                    </template>
                                                    <template v-else>
                                                        <template v-if="meridian == 'AM'">
                                                            <template v-if="!isEmpty(absent_am)">
                                                                <template v-for="(item, index) in absent_am">
                                                                    <div class="m-list-timeline__item" style="padding: 0 !important; margin: 0 auto;">
                                                                        <div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
                                                                            <span class='m-list-timeline__text' style="width: 56%">
                                                                                <small>{{item.name ? item.name.toUpperCase() : item.biometricno }}</small>
                                                                            </span>
                                                                            <span class='m-list-timeline__text' style='width: 100%; text-align: center'>
                                                                                <small>{{ item.content ? item.content : 'N/A' }}</small>
                                                                            </span>
                                                                            <span style='width: 15%;' class='m-list-timeline__time'>{{ item.mrdn }}</span>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </template>

                                                            <template v-if="!isEmpty(absent_pm)">
                                                                <template v-for="(item, index) in absent_pm">
                                                                    <div class="m-list-timeline__item" style="padding: 0 !important; margin: 0 auto;">
                                                                        <div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
                                                                            <span class='m-list-timeline__text' style="width: 56%">
                                                                                <small>{{item.name ? item.name.toUpperCase() : item.biometricno }}</small>
                                                                            </span>
                                                                            <span class='m-list-timeline__text' style='width: 100%; tex-align: center'>
                                                                                <small>{{ item.content ? item.content : 'N/A' }}</small>
                                                                            </span>
                                                                            <span style='width: 15%;' class='m-list-timeline__time'>{{ item.mrdn }}</span>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </template>

                                                            <template v-if="isEmpty(absent_am) && isEmpty(absent_pm)">
                                                                <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                                    <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                        <h5>NO DATA FOUND</h5>
                                                                    </span>
                                                                </div>
                                                            </template>
                                                        </template>

                                                        <template v-if="meridian == 'PM'">
                                                            <template v-if="!isEmpty(absent_pm)">
                                                                <template v-for="(item, index) in absent_pm">
                                                                    <div class="m-list-timeline__item" style="padding: 0 !important; margin: 0 auto;">
                                                                        <div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
                                                                            <span class='m-list-timeline__text' style="width: 56%">
                                                                                <small>{{item.name ? item.name.toUpperCase() : item.biometricno }}</small>
                                                                            </span>
                                                                            <span class='m-list-timeline__text' style='width: 100%; text-align: center'>
                                                                                <small>{{ item.content ? item.content : 'N/A' }}</small>
                                                                            </span>
                                                                            <span style='width: 15%;' class='m-list-timeline__time'>{{ item.mrdn }}</span>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </template>

                                                            <template v-if="!isEmpty(absent_am)">
                                                                <template v-for="(item, index) in absent_am">
                                                                    <div class="m-list-timeline__item" style="padding: 0 !important; margin: 0 auto;">
                                                                        <div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
                                                                            <span class='m-list-timeline__text' style="width: 56%">
                                                                                <small>{{item.name ? item.name.toUpperCase() : item.biometricno }}</small>
                                                                            </span>
                                                                            <span class='m-list-timeline__text' style='width: 100%; text-align: center'>
                                                                                <small>{{ item.content ? item.content : 'N/A' }}</small>
                                                                            </span>
                                                                            <span style='width: 15%;' class='m-list-timeline__time'>{{ item.mrdn }}</span>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </template>

                                                            <template v-if="isEmpty(absent_am) && isEmpty(absent_pm)">
                                                                <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                                    <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                        <h5>NO DATA FOUND</h5>
                                                                    </span>
                                                                </div>
                                                            </template>
                                                        </template>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
                                                    </div>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                    <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
        </div>
        <div id="admin" class="row">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="m-portlet m-portlet" id="undertime-monitor">
                            <div id="overlay">
                                <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                            </div>
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">
                                            UNDERTIME <small>( AS OF <?php echo strtoupper($yesterdayDate); ?> )</small>
                                        </h3>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools"></div>
                            </div>
                            <div class="m-portlet__body">
                                <div class="m-widget6">
                                    <div class="m-widget6__head">
                                        <div class="m-widget6__item">					 
                                            <span class="m-widget6__caption" style="width: 50%;"><small>EMPLOYEE NAME</small></span>
                                            <span class="m-widget6__caption text-center" style="width: 30%;"><small>LOA REFERENCE #</small></span>
                                            <span class="m-widget6__caption m--align-right"><small>AM/PM</small></span>					 
                                        </div>
                                    </div>
                                    <div class="m-widget6__body">
                                        <div id="ut-list" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="100" style="height: 100px; max-height: 100px; overflow: visible; position: relative;">
                                            <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(utRow) ? 'empty-list' : ''">
                                                <div class="m-list-timeline__items ut" id="ut-lists">
                                                    <template v-if="isShowUt">
                                                        <template v-if="!isEmpty(utRow)">
                                                            <template v-for="(item, index) in utRow">
                                                                <div class="m-list-timeline__item" style="padding: 0;">
                                                                    <div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>
                                                                        <span class='m-widget6__text' style='width: 50%;'>
                                                                            <small>{{ item.name ? item.name.toUpperCase() : item.biometric_id }}</small>
                                                                        </span>
                                                                        <span class='m-widget6__text text-center' style='width: 30%;'>
                                                                            <small>{{ item.content }}</small>
                                                                        </span>
                                                                        <span class='m-widget6__text text-right' style='width: 30%;'>
                                                                            <small>{{ item.mrdn }}</small>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </template>
                                                        <template v-else>
                                                            <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                                <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                    <h5>NO DATA FOUND</h5>
                                                                </span>
                                                            </div>
                                                        </template>
                                                    </template>
                                                    <template v-else>
                                                        <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                            <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
                    <div class="col-xl-12">
                        <div class="m-portlet m-portlet" id="double-monitor">
                            <div id="overlay">
                                <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                            </div>
                            <div class="m-portlet__head">
                                <div class="m-portlet__head-caption">
                                    <div class="m-portlet__head-title">
                                        <h3 class="m-portlet__head-text">
                                            DOUBLE ENTRIES <small>( AS OF <?php echo strtoupper($yesterdayDate); ?> )</small>
                                        </h3>
                                    </div>
                                </div>
                                <div class="m-portlet__head-tools"></div>
                            </div>
                            <div class="m-portlet__body">
                                <div class="m-widget6">
                                    <div class="m-widget6__head">
                                        <div class="m-widget6__item">					 
                                            <span class="m-widget6__caption" style="width: 25%;"><small>EMPLOYEE NAME</small></span>
                                            <span class="m-widget6__caption text-center" style="width: 2%;"><small>COUNT</small></span>
                                            <span class="m-widget6__caption m--align-right"><small>TIME IN/OUT</small></span>					 
                                        </div>
                                    </div>
                                    <div class="m-widget6__body">
                                        <div id="double_list" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="100" style="height: 100px; max-height: 100px; overflow: visible; position: relative;">
                                            <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(doubleRow.double_entry) ? 'empty-list' : ''">
                                                <div class="m-list-timeline__items double_entry">
                                                    <template v-if="isShowDouble">
                                                        <template v-if="!isEmpty(doubleRow)">
                                                            <template v-if="!isEmpty(doubleRow.double_entry)">
                                                                <template v-for="(item, index) in doubleRow.double_entry">
                                                                    <div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>
                                                                        <span class='m-widget6__text' style='width: 25%;'>
                                                                            <small>{{ item.employee_name ? item.employee_name : item.biometric_id }}</small>
                                                                        </span>
                                                                        <span class='m-widget6__text text-center' style='width: 2%;'>
                                                                            <small>{{ item.time_count }}</small>
                                                                        </span>
    
                                                                        <template v-if="!isEmpty(item.logged_time_record)">
                                                                            <span class='m-widget6__text m-widget__text-logged-time m--align-right'>
                                                                                <small>
                                                                                    <template v-for="records in item.logged_time_record">
                                                                                        <span class="m-menu__link-badge">
                                                                                            <span class="m-badge m-badge--accent m-badge--wide">{{ timeFormat(records) }}</span>
                                                                                        </span>
                                                                                    </template>
                                                                                </small>
                                                                            </span>
                                                                        </template>
                                                                        <template v-else>
                                                                            <span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>---</small></span>
                                                                        </template>
                                                                    </div>
                                                                </template>
                                                            </template>
                                                            <template v-else>
                                                                <div class="m-list-timeline__item" style="padding: 0;">
                                                                    <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                        <h5>NO DATA FOUND</h5>
                                                                    </span>
                                                                </div>
                                                            </template>
                                                        </template>
                                                        <template v-else>
                                                            <div class="m-list-timeline__item" style="padding: 0;">
                                                                <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                    <h5>NO DATA FOUND</h5>
                                                                </span>
                                                            </div>
                                                        </template>
                                                    </template>
                                                    <template v-else>
                                                        <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                            <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                <div class="m-portlet m-portlet" id="lacking-monitor">
                    <div id="overlay">
                        <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                    </div>
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">
                                    LACKING ENTRIES <small>( AS OF <?php echo strtoupper($yesterdayDate); ?> )</small>
                                </h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools"></div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget6">
                            <div class="m-widget6__head">
                                <div class="m-widget6__item">					 
                                    <span class="m-widget6__caption" style="width: 20%;"><small>EMPLOYEE NAME</small></span>
                                    <span class="m-widget6__caption text-center" style="width: 25%;"><small>TO REFERENCE #</small></span>
                                    <span class="m-widget6__caption text-center" style="width: 2%;"><small>COUNT</small></span>
                                    <span class="m-widget6__caption m--align-right"><small>TIME IN/OUT</small></span>					 
                                </div>
                            </div>
                            <div class="m-widget6__body">
                                <div id="lacking_list" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="400" style="height: 400px; max-height: 400px; overflow: visible; position: relative;">
                                    <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(lackingRow.lacking_entry) ? 'empty-list' : ''">
                                        <div class="m-list-timeline__items lacking_entry">
                                            <template v-if="isShowLacking">
                                                <template v-if="!isEmpty(lackingRow.lacking_entry)">
                                                    <template v-for="(item, index) in lackingRow.lacking_entry">
                                                        <div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>
                                                            <span class='m-widget6__text' style='width: 27%;'>
                                                                <small>{{ item.employee_name ? item.employee_name.toUpperCase() : item.biometric_id }}</small>
                                                            </span>
                                                            <span class='m-widget6__text text-center' style='width: 25%;'>
                                                                <small>{{ item.reference_no ? item.reference_no : 'N/A' }}</small>
                                                            </span>
                                                            <span class='m-widget6__text text-center' style='width: 10%;'>
                                                                <small>{{ item.time_count }}</small>
                                                            </span>
                                                            <template v-if="!isEmpty(item.logged_time_record)">
                                                                <div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto; text-align: right;'>		
                                                                    <small>
                                                                        <template v-for="records in item.logged_time_record">
                                                                            <span class="m-menu__link-badge">
                                                                                <span class="m-badge m-badge--accent m-badge--wide">{{ timeFormat(records) }}</span>
                                                                            </span>
                                                                        </template>
                                                                    </small>
                                                                </span>
                                                            </template>
                                                            <template v-else>
                                                                <span class='m-widget6__text m-widget__text-logged-time m--align-right'><small>---</small></span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                            <h5>NO DATA FOUND</h5>
                                                        </span>
                                                    </div>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                    <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
        </div>
    <?php else: ?>
        <div class="personal">
            <div class="row">
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                    <div class="m-portlet m-portlet" id="late-monitor">
                        <div id="overlay">
                            <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                        </div>
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        LATE <small>( AS OF <?php echo strtoupper($personalDate); ?> )</small>
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-widget6">
                                <div class="m-widget6__head">
                                    <div class="m-widget6__item">					 
                                        <span class="m-widget6__caption text-left"><small>DATE</small></span>
                                        <span class="m-widget6__caption text-center"><small>TIME</small></span>
                                        <span class="m-widget6__caption m--align-right"><small>DURATION</small></span>					 
                                    </div>
                                </div>
                                <div class="m-widget6__body">
                                    <div id="personal-late-scrollable" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="height: 300px; max-height: 300px; overflow: visible; position: relative;">
                                        <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(personalLate) ? 'empty-list' : ''">
                                            <div class="m-list-timeline__items late" id="latelist">
                                                <template v-if="isShowPersonalLate">
                                                    <template v-if="!isEmpty(personalLate)">
                                                        <div class='m-list-items-data_item'>
                                                            <template v-for="late in personalLate">
                                                                <div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
                                                                    <span class='m-list-timeline__text'>
                                                                        <small>{{ date(late.date) }}</small>
                                                                    </span>
                                                                    <span class='m-list-timeline__text text-center'>
                                                                        <small>{{ late.time }}</small>
                                                                    </span>
                                                                    <span class='m-list-timeline__text text-right'>
                                                                        <small>{{ late.minlate }}</small>
                                                                    </span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                            <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                <h5>NO DATA FOUND</h5>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                    <div class="m-portlet m-portlet" id="absent-monitor">
                        <div id="overlay">
                            <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                        </div>
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        ABSENT <small>( AS OF <?php echo strtoupper($personalDate); ?> )</small>
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-widget6">
                                <div class="m-widget6__head">
                                    <div class="m-widget6__item">					 
                                        <span class="m-widget6__caption text-left"><small>DATE</small></span>
                                        <span class="m-widget6__caption text-center"><small>LOA/TO REFERENCE #</small></span>
                                        <span class="m-widget6__caption m--align-right"><small>TIME</small></span>					 
                                    </div>
                                </div>
                                <div class="m-widget6__body">
                                    <div id="personal-absent-scrollable" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="max-height: 300px; height: 300px; overflow: visible; position: relative;">
                                        <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(personalAbsent) ? 'empty-list' : ''">
                                            <div class="m-list-timeline__items" id="absentslist">
                                                <template v-if="isShowPersonalAbsent">
                                                    <template v-if="!isEmpty(personalAbsent)">
                                                        <div class='m-list-items-data_item'>
                                                            <template v-for="absent in personalAbsent">
                                                                <div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
                                                                    <span class='m-list-timeline__text'>
                                                                        <small>{{ date(absent.date) }}</small>
                                                                    </span>
                                                                    <span class='m-list-timeline__text text-right'>
                                                                        <small>{{ absent.content }}</small>
                                                                    </span>
                                                                    <span class='m-list-timeline__text text-right'>
                                                                        <small>{{ absent.mrdn }}</small>
                                                                    </span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                            <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                <h5>NO DATA FOUND</h5>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                    <div class="m-portlet m-portlet" id="undertime-monitor">
                        <div id="overlay">
                            <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                        </div>
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        UNDERTIME <small>( AS OF <?php echo strtoupper($personalDate); ?> )</small>
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-widget6">
                                <div class="m-widget6__head">
                                    <div class="m-widget6__item">					 
                                        <span class="m-widget6__caption text-left"><small>DATE</small></span>
                                        <span class="m-widget6__caption text-center"><small>CONTENT</small></span>
                                        <span class="m-widget6__caption text-right"><small>TIME</small></span>				 
                                    </div>
                                </div>
                                <div class="m-widget6__body">
                                    <div id="personal-under-scrollable" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="height: 300px; max-height: 300px; overflow: visible; position: relative;">
                                        <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(personalUnder) ? 'empty-list' : ''">
                                            <div class="m-list-timeline__items ut" id="ut-lists">
                                                <template v-if="isShowPersonalUndertime">
                                                    <template v-if="!isEmpty(personalUnder)">
                                                        <div class='m-list-items-data_item'>
                                                            <template v-for="under in personalUnder">
                                                                <div style='padding: 0 !important;margin: 0 auto;' class='m-list-timeline__item'>
                                                                    <span class='m-list-timeline__text'>
                                                                        <small>{{ date(under.date) }}</small>
                                                                    </span>
                                                                    <span class='m-list-timeline__text text-center'>
                                                                        <small> {{ under.content }} </small>
                                                                    </span>
                                                                    <span class='m-list-timeline__text text-right'>
                                                                        <small>{{ under.time }}</small>
                                                                    </span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                            <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                <h5>NO DATA FOUND</h5>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
            </div>
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="m-portlet m-portlet" id="lacking-monitor">
                        <div id="overlay">
                            <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                        </div>
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        LACKING ENTRY <small>( AS OF <?php echo strtoupper($personalDate); ?> )</small>
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-widget6">
                                <div class="m-widget6__head">
                                    <div class="m-widget6__item">					 
                                        <span class="m-widget6__caption" style="width: 10%;"><small>DATE</small></span>
                                        <span class="m-widget6__caption text-center" style="width: 2%;"><small>COUNT</small></span>
                                        <span class="m-widget6__caption m--align-right"><small>TIME IN/OUT</small></span>					 
                                    </div>
                                </div>
                                <div class="m-widget6__body">
                                    <div id="personal-lacking-scrollable" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="height: 300px; max-height: 300px; overflow: visible; position: relative;">
                                        <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(personalLacking) ? 'empty-list' : ''">
                                            <div class="m-list-timeline__items" id="lacking-lists">
                                                <template v-if="isShowPersonalLacking">
                                                    <template v-if="!isEmpty(personalLacking)">
                                                        <div class='m-list-items-data_item'>
                                                            <template v-for="(item, index) in personalLacking">
                                                                <div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>
                                                                    <span class='m-widget6__text' style='width: 25%;'><small>{{ wordDate(item.date) }}</small></span>
                                                                    <span class='m-widget6__text text-center' style='width: 2%;'><small>{{ item.time_count }}</small></span>
                                                                    
                                                                    <template v-if="!isEmpty(item.logged_time_record)">
                                                                        <div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto; text-align: right;'>		
                                                                            <small>
                                                                                <template v-for="records in item.logged_time_record">
                                                                                    <span class="m-menu__link-badge">
                                                                                        <span class="m-badge m-badge--accent m-badge--wide">{{ timeFormat(records) }}</span>
                                                                                    </span>
                                                                                </template>
                                                                            </small>
                                                                        </span>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                            <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                <h5>NO DATA FOUND</h5>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <div class="m-portlet m-portlet" id="double-monitor">
                        <div id="overlay">
                            <div class="m-loader m-loader--light m-loader--lg" style="width: 30px; display: inline-block;"></div>
                        </div>
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        DOUBLE ENTRY <small>( AS OF <?php echo strtoupper($personalDate); ?> )</small>
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-widget6">
                                <div class="m-widget6__head">
                                    <div class="m-widget6__item">					 
                                        <span class="m-widget6__caption" style="width: 10%;"><small>DATE</small></span>
                                        <span class="m-widget6__caption text-center" style="width: 2%;"><small>COUNT</small></span>
                                        <span class="m-widget6__caption m--align-right"><small>TIME IN/OUT</small></span>					 
                                    </div>
                                </div>
                                <div class="m-widget6__body">
                                    <div id="personal-double-scrollable" class="m-scrollable mCustomScrollbar _mCS_3 mCS-autoHide" data-scrollable="true" data-max-height="300" style="heigth: 300px; max-height: 300px; overflow: visible; position: relative;">
                                        <div class="m-list-timeline m-list-timeline--skin-light" :class="isEmpty(personalDouble) ? 'empty-list' : ''">
                                            <div class="m-list-timeline__items" id="double-lists">
                                                <template v-if="isShowPersonalDouble">
                                                    <template v-if="!isEmpty(personalDouble)">
                                                        <div class='m-list-items-data_item'>
                                                            <template v-for="(item, index) in personalDouble">
                                                                <div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto;'>
                                                                    <span class='m-widget6__text' style='width: 25%;'><small>{{ wordDate(item.date) }}</small></span>
                                                                    <span class='m-widget6__text text-center' style='width: 2%;'><small>{{ item.time_count }}</small></span>
                                                                    
                                                                    <template v-if="!isEmpty(item.logged_time_record)">
                                                                        <div class='m-widget6__item' style='padding: 0 !important;margin: 0 auto; text-align: right;'>		
                                                                            <small>
                                                                                <template v-for="records in item.logged_time_record">
                                                                                    <span class="m-menu__link-badge">
                                                                                        <span class="m-badge m-badge--accent m-badge--wide">{{ timeFormat(records) }}</span>
                                                                                    </span>
                                                                                </template>
                                                                            </small>
                                                                        </span>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                            <span class="m-list-timeline__text text-center" style="width: 100%;">
                                                                <h5>NO DATA FOUND</h5>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <div class="m-list-timeline__item" style="padding: 20px 0;">
                                                        <span class="m-list-timeline__text text-center" style="width: 100%;"></span>
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
            </div>
        </div>
    <?php endif ?>
</div>