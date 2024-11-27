<div class="m-content">
    <div class="row">
        <div class="col-lg-4">
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                            Reference No - Accountability Form Content Fix
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <div class="m-portlet__body">
                    <div id="m--accountability_body_refno-fix">
                        <div class="form-group m-form__group">
                            <label for="search">Search Reference No</label>
                            <input type="text" class="form-control m-input" id="search_refno" autocomplete="off" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">
                                Accountability Form Content Body - Fix
                            </h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="m-portlet__nav">
                            <li class="m-portlet__nav-item">
                                <a href="javascript:void(0);" class="m-portlet__nav-link m-portlet__nav-link--icon" onclick="generateAccountabilityBodyFix()"><i class="la la-refresh"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div id="m--accountability_body-fix">
                        <template v-if="count > 0">
                            <div class="form-group m-form__group">
                                <label for="search">Search</label>
                                <input type="text" class="form-control m-input" id="search" autocomplete="off" />
                            </div>
                            <div class="m-widget2" v-for="(item, index) in rows">
                                <div class="m-widget2__item m-widget2__item--primary" :class="item.accounted_count !== item.asset_count? 'm-widget2__item--danger': 'm-widget2__item--success'">
                                    <div class="m-widget2__checkbox"></div>
                                    <div class="m-widget2__desc">
                                        <p class="m-widget2__text m--marginless m--font-boldest">{{item.reference_no}}</p>
                                        <p class="m-widget2__user-name m--marginless m--font-boldest">
                                            <span class="m-badge m-badge--wide m-badge--info">Accounted Asset: {{item.accounted_count}}</span>
                                            <span class="m-badge m-badge--wide m-badge--danger">Asset Count: {{item.asset_count}}</span>
                                        </p>
                                    </div>
                                    <div class="m-widget2__actions">
                                        <div class="m-widget2__actions-nav">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-danger m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill">
                                                <i class="fa fa-wrench"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <template v-else>
                            <div class="m-alert m-alert--outline alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Generate form content fix!</strong> click on the refresh button.
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div id="tempSearchRefno">
                <template v-if="acct_body !== 0">
                    <div class="m-portlet m-portlet--mobile">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        Current - Accountability Form Content
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-widget11">
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table">
                                        <!--begin::Thead-->
                                        <thead>
                                            <tr>
                                                <td class="m-widget11__sales">
                                                    Asset Code
                                                </td>
                                                <td class="m-widget11__app">
                                                    Name
                                                </td>
                                                <td class="m-widget11__price m--align-center">
                                                    Type
                                                </td>
                                                <td class="m-widget11__price m--align-center">
                                                    Is Returned
                                                </td>
                                                <td class="m-widget11__total m--align-justify">
                                                    Remarks
                                                </td>
                                            </tr>
                                        </thead>
                                        <!--end::Thead-->
                                        <!--begin::Tbody-->
                                        <tbody>
                                            <tr v-for="(item, index) in rows.acct_body">
                                                <td>
                                                    <span class="m-widget11__title">{{item.asset_code}}</span>
                                                </td>
                                                <td>
                                                    <span class="m-widget11__title">{{item.description}}</span>
                                                </td>
                                                <td>
                                                    <span class="m-widget11__title m--align-center">{{item.type}}</span>
                                                </td>
                                                <td>
                                                    <span class="m-widget11__title m--align-center">{{item.is_returned === '1' ? 'YES' : 'NO' }}</span>
                                                </td>
                                                <td class="m--align-right m--font-brand m--align-justify">
                                                    <span class="m-widget11__title">{{item.is_returned === '1' ? item.remarks_returned : item.remarks }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <!--end::Tbody-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                            </div>
                        </div>
                    </div>
                    </template>
                    <template v-if="acct_count !== 0">
                    <div class="m-portlet m-portlet--mobile">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <h3 class="m-portlet__head-text">
                                        To Fix - Accountability Form Content
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools"></div>
                        </div>
                        <div class="m-portlet__body">
                            <div class="m-widget11">
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table">
                                        <!--begin::Thead-->
                                        <thead>
                                            <tr>
                                                <td class="m-widget11__sales">
                                                    Asset Code
                                                </td>
                                                <td class="m-widget11__app">
                                                    Name
                                                </td>
                                                <td class="m-widget11__price m--align-center">
                                                    Type
                                                </td>
                                                <td class="m-widget11__price m--align-center">
                                                    Action
                                                </td>
                                            </tr>
                                        </thead>
                                        <!--end::Thead-->
                                        <!--begin::Tbody-->
                                        <tbody>
                                            <tr v-for="(item, index) in getItems()">
                                                <td>
                                                    <template v-if="item.acct_type == 'Asset'">
                                                        <span class="m-widget11__title">{{item.assetacode}}</span>
                                                    </template>
                                                    <template v-else>
                                                        <span class="m-widget11__title">{{item.gen_code}}</span>
                                                    </template>
                                                    
                                                </td>
                                                <td>
                                                    <span class="m-widget11__title">{{item.name}}</span>
                                                </td>
                                                <td>
                                                    <span class="m-widget11__title m--align-center">{{item.acct_type}}</span>
                                                </td>
                                                <td>
                                                    <a href="#" class="btn btn-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air btnNew" @click="setAcctBody(item.id, item.acct_id, item.acct_type)">
                                                        <i class="la la-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <!--end::Tbody-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>