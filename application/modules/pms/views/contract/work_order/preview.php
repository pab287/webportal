<div class="m-content">
    <div class="row">
        <div class="col-lg-9">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Task <small>Information</small></h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul id="task-actions" class="m-portlet__nav">
                            <li class="m-portlet__nav-item m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                                <a href="javascript:void(0);" class="m-portlet__nav-link btn btn-default m-btn m-btn--hover-default m-btn--icon m-btn--icon-only m-btn--pill m-dropdown__toggle btnEdit">
                                    <i class="la la-ellipsis-h"></i>
                                </a>
                                <div class="m-dropdown__wrapper">
                                    <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust" style="left: auto; right: 17px;"></span>
                                    <div class="m-dropdown__inner">
                                        <div class="m-dropdown__body">
                                            <div class="m-dropdown__content">
                                                <ul class="m-nav" id="task-actions">
                                                    <li class="m-nav__section m-nav__section--first">
                                                        <span class="m-nav__section-text">
                                                            Quick Actions
                                                        </span>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnEdit" @click="editContract(row.id)">
                                                            <i class="m-nav__link-icon flaticon-edit"></i>
                                                            <span class="m-nav__link-text">
                                                                Edit Contract
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnEdit" @click="adjustmentContract(row.id)">
                                                            <i class="m-nav__link-icon flaticon-interface-8"></i>
                                                            <span class="m-nav__link-text">
                                                                Contract Adjustment
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__separator m-nav__separator--fit"></li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnEdit" @click="additionalContract(row.id)">
                                                            <i class="m-nav__link-icon flaticon-add"></i>
                                                            <span class="m-nav__link-text">
                                                                Additional Contract
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="m-nav__separator m-nav__separator--fit"></li>
                                                    <li class="m-nav__item">
                                                        <a href="javascript:void(0);" class="m-nav__link btnEdit" @click="updateDueDate(row.id)">
                                                            <i class="m-nav__link-icon flaticon-calendar"></i>
                                                            <span class="m-nav__link-text">
                                                                Due Date Extension
                                                            </span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body" id="task-details">
                    <div class="row m-row--no-padding">
                        <!-- div class="col-8">
                            <div class="m-widget4">
                                <div class="m-widget4__item">
                                    <div class="m-widget4__info">
                                        <p class="m-widget4__text m--marginless">ASSIGNED TASK</p>
                                        <p class="m-widget4__title m--marginless" v-text="row.task_name">&nbsp;</p>
                                    </div>
                                    <div class="m-widget4__ext">
                                        <a href="javascript:void(0);" 
                                        class="m-btn btnEdit m-btn--pill m-btn--hover-danger btn btn-sm btn-danger" 
                                        @click="updateDueDate(row.id)">EXTEND</a>
                                    </div>
                                </div>
                            </div>
                        </div -->
                        <div class="col-2">
                            <div class="m-widget4">
                                <div class="m-widget4__item">
                                    <div class="m-widget4__info">
                                        <p class="m-widget4__text m--marginless">ISSUED DATE</p>
                                        <p class="m-widget4__title m--marginless" v-text="row.issued_date">&nbsp;</p>
                                    </div>
                                    <div class="m-widget4__ext">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="m-widget4">
                                <div class="m-widget4__item">
                                    <div class="m-widget4__info">
                                        <p class="m-widget4__text m--marginless">DUE DATE</p>
                                        <template v-if="row.extension_id !== '0'">
                                            <p class="m-widget4__title m--marginless" v-text="row.extension_date">&nbsp;</p>
                                        </template>
                                        <template v-else>
                                            <p class="m-widget4__title m--marginless" v-text="row.due_date">&nbsp;</p>
                                        </template>
                                    </div>
                                    <div class="m-widget4__ext">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                        <!-- div class="col-2">
                            <div class="m-widget4">
                                <div class="m-widget4__item">
                                    <div class="m-widget4__info text-right">
                                        <p class="m-widget4__text m--marginless">DATE EXTENSION</p>
                                        <p class="m-widget4__title m--marginless" v-text="row.extension_date">&nbsp;</p>
                                    </div>
                                    <div class="m-widget4__ext">&nbsp;</div>
                                </div>
                            </div>
                        </div -->
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="m-widget11">
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table m-custom_table">
                                        <!--begin::Thead-->
                                        <thead>
                                            <tr>
                                                <td class="m-widget11__description">DESCRIPTION</td>
                                                <td class="m-widget11__qty m--align-center">QTY</td>
                                                <td class="m-widget11__qty m--align-center">LOTS</td>
                                                <td class="m-widget11__unit m--align-center">UNIT</td>
                                                <td class="m-widget11__unit_cost m--align-right">UNIT COST</td>
                                                <td class="m-widget11__total_cost m--align-right">TOTAL COST</td>
                                            </tr>
                                        </thead>
                                        <!--end::Thead-->
                                        <!--begin::Tbody-->
                                        <tbody>
                                        <template v-if="item_count > 0">
                                            <template v-for="(tempItems, tempIndex) in items">
                                                <tr>
                                                    <th colspan="6">
                                                        <h5 class="m-table--no-padding m--marginless">WORK ORDER # {{item_counter[tempIndex]}}</h5>
                                                    </th>
                                                </tr>
                                                <template v-for="(vv, kk) in tempItems">
                                                    <template v-if="vv.is_parent === true">
                                                        <tr>
                                                            <td colspan="6">
                                                                <p class="m-widget11__title m--marginless m-widget11__task">
                                                                    <i class="fa fa-square fa--custom_sm"></i>
                                                                    {{vv.label}}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <template v-else>
                                                        <tr>
                                                            <td class="m--custom-font-12">
                                                                <p class="m-widget11__text m--marginless m-widget11__subtask">
                                                                    <i class="fa fa-angle-double-right"></i>
                                                                        {{vv.label}}
                                                                </p>
                                                            </td>
                                                            <td class="m--align-center m--custom-font-12">{{vv.qty}}</td>
                                                            <td class="m--align-center m--custom-font-12">{{vv.lots}}</td>
                                                            <td class="m--align-center m--custom-font-12">
                                                                {{vv.unit}}
                                                            </td>
                                                            <td class="m--align-right m--custom-font-12 m--font-primary">
                                                                {{vv.tariff}}
                                                            </td>
                                                            <td class="m--align-right m--custom-font-12 m--font-primary m--font-boldest">
                                                                {{vv.total}}
                                                            </td>
                                                        </tr>
                                                    </template>
                                                </template>
                                                <template v-if="adjustments[tempIndex] && adjustments[tempIndex].length > 0">
                                                    <tr>
                                                        <th colspan="6">
                                                            <h5 class="m-table--no-padding m--marginless m--font-brand">Adjustment</h5>
                                                        </th>
                                                    </tr>
                                                    <template v-for="(vvx, kkx) in adjustments[tempIndex]">
                                                        <template v-if="vvx.is_parent === true">
                                                            <tr>
                                                                <td colspan="6">
                                                                    <p class="m-widget11__title m--marginless m-widget11__task m--font-brand m--font-boldest">
                                                                        <i class="fa fa-square fa--custom_sm"></i>
                                                                        {{vvx.label}}
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                        <template v-else>
                                                            <tr>
                                                                <td class="m--custom-font-12">
                                                                    <p class="m-widget11__text m--marginless m-widget11__subtask m--font-brand m--font-boldest">
                                                                        <i class="fa fa-angle-double-right"></i>
                                                                            {{vvx.label}}
                                                                    </p>
                                                                </td>
                                                                <td class="m--align-center m--custom-font-12 m--font-brand m--font-boldest">{{vvx.qty}}</td>
                                                                <td class="m--align-center m--custom-font-12 m--font-brand m--font-boldest">{{vvx.lots}}</td>
                                                                <td class="m--align-center m--custom-font-12 m--font-brand m--font-boldest">
                                                                    {{vvx.unit}}
                                                                </td>
                                                                <td class="m--align-right m--custom-font-12 m--font-primary m--font-brand m--font-boldest">
                                                                    {{vvx.tariff}}
                                                                </td>
                                                                <td class="m--align-right m--custom-font-12 m--font-primary m--font-brand m--font-boldest">
                                                                    {{vvx.total}}
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                </template>
                                                <tr v-if="item_count > 1">
                                                    <td colspan="5" class="text-right"><h6 class="m--marginless m--font-danger m--font-boldest">TOTAL</h6></td>
                                                    <td class="text-right m--font-boldest"><h6 v-text="sub_total[tempIndex]" class="m--marginless m--font-danger m--font-boldest">0.00</h6></td>
                                                </tr>
                                            </template>
                                        </template>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="5" class="text-right"><h5 class="m--marginless">GRAND TOTAL</h5></th>
                                                <th class="text-right m--no_padding"><h5 class="m--marginless" v-text="grand_total">0.00</h5></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- div class="col-12">
                            <div class="m_datatable m-datatable m-datatable--default m-datatable--loaded m-datatable--scroll table-responsive-sm">
                                <table class="table table-striped table-bordered" id="table-task_contract" width="100%">
                                    <thead>
                                    <tr>
                                        <th>Task</th>
                                        <th>Qty</th>
                                        <th>Lots</th>
                                        <th>Unit</th>
                                        <th>Unit Cost</th>
                                        <th>Total</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5">
                                            <p class="text-right m--marginless">Grand Total</p>
                                            </th>
                                            <th>0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div -->
                    </div>
                </div>
            </div>
            <!--end::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Other Information</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools">
                        <ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist">
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link active" data-toggle="tab" href="#m_widget-project_content" role="tab" aria-expanded="true">
                                    PROJECTS
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_widget-task_extension_content" role="tab" aria-expanded="false">
                                    Task Extension
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_widget-change_order_content" role="tab" aria-expanded="false">
                                    Change Order
                                </a>
                            </li>
                            <!-- li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_widget-estimates_content" role="tab" aria-expanded="false">
                                    ESTIMATES
                                </a>
                            </li>
                            <li class="nav-item m-tabs__item">
                                <a class="nav-link m-tabs__link" data-toggle="tab" href="#m_widget-design_content" role="tab" aria-expanded="false">
                                    DESIGN
                                </a>
                            </li -->
                        </ul>
                    </div>
                </div>
                <div class="m-portlet__body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="m_widget-project_content">
                            <div id="contract_project-content">
                                <template v-if="records === true">
                                    <div class="m-widget4 m-widget4--progress">
                                        <div class="m-widget4__item" v-for="(item, index) in rows">
                                            <div class="m-widget4__info">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <a href="javascript:void(0)" class="nav-link m-tabs__link m--padding-left-0" @click="redirectToTask(item.id)">
                                                            <p class="m-widget4__title m--marginless" v-text="item.description">&nbsp;</p>
                                                            <p class="m-widget4__sub m--marginless">Block And Lot</p>
                                                        </a>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <p class="m-widget4__title m--marginless">
                                                            <span v-if="item.contract_status === '1'" class="m--font-success">ONGOING</span>
                                                            <span v-if="item.contract_status === '2'" class="m--font-primary">COMPLETED</span>
                                                            <span v-if="item.contract_status === '3'" class="m--font-danger">TERMINATED</span>
                                                        </p>
                                                        <p class="m-widget4__sub m--marginless">Contract Status</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="m-widget4__progress">
                                                <div class="m-widget4__progress-wrapper">
                                                    <span class="m-widget17__progress-number">0%</span>
                                                    <span class="m-widget17__progress-label">Completion</span>
                                                    <div class="progress m-progress--sm">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="m-widget4__ext">
                                                <!--a href="javascript:void(0);" 
                                                class="m-btn btnEdit m-btn--pill m-btn--hover-success btn btn-sm btn-success">PREVIEW</a -->
                                                &nbsp;
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>&nbsp;</template>
                            </div>
                        </div>
                        <div class="tab-pane" id="m_widget-task_extension_content">
                            <?php echo $this->load->view("pms/contract/form_content/work_order/task_extension_content", null, true); ?>
                        </div>
                        <div class="tab-pane" id="m_widget-change_order_content">
                            <?php echo $this->load->view("pms/contract/form_content/work_order/co_history_content", null, true); ?>
                        </div>
                        <div class="tab-pane" id="m_widget-estimates_content">    
                        </div>
                        <div class="tab-pane" id="m_widget-design_content">
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Portlet-->
        </div>
        <div class="col-lg-3">
            <!--begin::Portlet-->
            <div id="contract-content" class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Work Order <small>Information</small></h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <div class="m-portlet__body">
                    <div class="m-widget4">
                        <div class="m-widget4__item">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless">CONTRACT #</p>
                                <p class="m-widget4__title m--marginless" v-text="row.wo_code">&nbsp;</p>
                            </div>
                            <div class="m-widget4__ext">
                                <!--template v-if="row.status === '1'">
                                <a href="javascript:void(0);" 
                                class="m-widget4__icon btnEdit" 
                                @click="changeOrder(row.id)" 
                                data-container="body" 
                                data-toggle="m-tooltip" 
                                data-trigger="focus" 
                                data-placement="left" 
                                title="Contract Change Order">
                                    <i class="la la-pencil"></i>
                                </a>
                                </template>
                                <template v-else>
                                    &nbsp;
                                </template-->
                            </div>
                        </div>
                        <div class="m-widget4__item">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless">REFERENCE CODE AS PER CONTRACT</p>
                                <p class="m-widget4__title m--marginless" v-text="row.reference_code">&nbsp;</p>
                            </div>
                            <div class="m-widget4__ext">&nbsp;</div>
                        </div>
                        <div class="m-widget4__item">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless">CONTRACTOR</p>
                                <p class="m-widget4__title m--marginless" v-text="row.contractor">&nbsp;</p>
                            </div>
                            <div class="m-widget4__ext">&nbsp;</div>
                        </div>
                        <div class="m-widget4__item">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless">SUPERVISOR</p>
                                <p class="m-widget4__title m--marginless" v-text="row.incharge">&nbsp;</p>
                            </div>
                            <div class="m-widget4__ext">
                                <template v-if="row.status === '1'">
                                    <a href="javascript:void(0);" 
                                    class="m-widget4__icon btnEdit" 
                                    @click="updateIncharge(row.id)"
                                    data-container="body" 
                                    data-toggle="m-tooltip" 
                                    data-trigger="focus" 
                                    data-placement="left" 
                                    title="Edit Task Incharge">
                                        <i class="la la-pencil"></i>
                                    </a>
                                </template>
                                <template v-else>&nbsp;</template>
                            </div>
                        </div>
                        <div class="m-widget4__item">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless">LEADMAN</p>
                                <p class="m-widget4__title m--marginless" v-text="row.leadman">&nbsp;</p>
                            </div>
                            <div class="m-widget4__ext">
                                <template v-if="row.status === '1'">
                                    <a href="javascript:void(0);" 
                                    class="m-widget4__icon btnEdit" 
                                    @click="updateLeadman(row.id)"
                                    data-container="body" 
                                    data-toggle="m-tooltip" 
                                    data-trigger="focus" 
                                    data-placement="left" 
                                    title="Edit Leadman">
                                        <i class="la la-pencil"></i>
                                    </a>
                                </template>
                                <template v-else>&nbsp;</template>
                            </div>
                        </div>
                        <div class="m-widget4__item">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless">FOREMAN</p>
                                <p class="m-widget4__title m--marginless" v-text="row.foreman">&nbsp;</p>
                            </div>
                            <div class="m-widget4__ext">
                                <template v-if="row.status === '1'">
                                    <a href="javascript:void(0);" 
                                    class="m-widget4__icon btnEdit" 
                                    @click="updateForeman(row.id)"
                                    data-container="body" 
                                    data-toggle="m-tooltip" 
                                    data-trigger="focus" 
                                    data-placement="left" 
                                    title="Edit Foreman">
                                        <i class="la la-pencil"></i>
                                    </a>
                                </template>
                                <template v-else>&nbsp;</template>
                            </div>
                        </div>
                        <div class="m-widget4__item">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless">STATUS</p>
                                <h3 class="m--marginless m--font-warning " v-if="row.status == 1">ACTIVE</h3>
                                <h3 class="m--marginless m--font-primary" v-if="row.status == 2">COMPLETED</h3>
                                <h3 class="m--marginless m--font-danger flash animated" v-if="row.status == 3">TERMINATED</h3>
                            </div>
                            <div class="m-widget4__ext">
                                <template v-if="row.status === '1'">
                                    <a href="javascript:void(0);" 
                                    class="m-widget4__icon btnEdit" 
                                    @click="updateStatus(row.id)"
                                    data-container="body" 
                                    data-toggle="m-tooltip" 
                                    data-trigger="focus" 
                                    data-placement="left" 
                                    title="Edit Status">
                                        <i class="la la-pencil"></i>
                                    </a>
                                </template>
                                <template v-else>&nbsp;</template>
                            </div>
                        </div>
                        <div class="m-widget4__item">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless">REMARKS</p>
                                <p class="m-widget4__title m--marginless" v-text="row.remarks">&nbsp;</p>
                            </div>
                            <div class="m-widget4__ext">
                                <template v-if="row.status === '1'">
                                    <a href="javascript:void(0);" 
                                    class="m-widget4__icon btnEdit" 
                                    @click="updateRemarks(row.id)" 
                                    data-container="body" 
                                    data-toggle="m-tooltip" 
                                    data-trigger="focus" 
                                    data-placement="left" 
                                    title="Edit Remarks">
                                        <i class="la la-pencil"></i>
                                    </a>
                                </template>
                                <template v-else>&nbsp;</template>
                            </div>
                        </div>
                        <div class="m-widget4__item" v-if="row.status_remarks !== ''">
                            <div class="m-widget4__info">
                                <p class="m-widget4__text m--marginless" v-if="row.status == 1">ACTIVE REMARKS</p>
                                <p class="m-widget4__text m--marginless" v-if="row.status == 2">COMPLETED REMARKS</p>
                                <p class="m-widget4__text m--marginless" v-if="row.status == 3">TERMINATED REMARKS</p>
                                <p class="m-widget4__title m--marginless" v-text="row.status_remarks">&nbsp;</p>
                            </div>
                            <div class="m-widget4__ext">&nbsp;</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Portlet-->
            <!-- div id="contract_extension-content" class="fadeIn animated" v-if="show_extension === true">
                <div class="m-portlet m-portlet--mobile m-portlet--full-height">
                    <div class="m-portlet__head">
                        <div class="m-portlet__head-caption">
                            <div class="m-portlet__head-title">
                                <h3 class="m-portlet__head-text">Task Extension</h3>
                            </div>
                        </div>
                        <div class="m-portlet__head-tools"></div>
                    </div>
                    <div class="m-portlet__body">
                        <div class="m-widget4">
                            <div class="m-widget4__item">
                                <div class="m-widget4__info">
                                    <p class="m-widget4__title m--marginless">EXTENDED DATE : <span v-text="row.extension_date">&nbsp;</span></p>
                                    <span class="m-widget4__text" v-text="row.extension_remarks">&nbsp;</span>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span class="m-widget4__title" v-text="row.extended_name">&nbsp;</span>
                                        </div>
                                        <div class="col-md-12">
                                            <span class="m-widget4__sub"><span v-text="row.extended_at">&nbsp;</span></span>
                                        </div>
                                    </div>
                                </div>
                                <span class="m-widget4__ext">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div -->
        </div>
    </div>
</div>
<style>
     #table-task_contract_items input.form-control-table_field{
        width: 50%;
        display: inline-block;
        padding: 0 10px;
    }
    .m-widget11 table.m-custom_table tbody tr > td{
        padding: 5px 0px !important;
    }
    .m-widget11 table.m-custom_table tfoot tr > th{
        padding: 15px 0px !important;
    }
    /*** #table-task_contract_items .form-group.m-form__group.m--marginless {
        display: inline-block;
    } ***/
</style>