<div class="m-content">
    <div class="row">
        <div id="contract_accomplishment-content" class="col-12 col-md-12 col-lg-12">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">New Accomplishment</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <div class="m-portlet__body">
                    <div class="row">
                        <div class="col-2 col-md-2 col-lg-2">
                            <div class="form-group m-form__group">
                                <label for="task_incharge">Week Range *</label>
                                <input type="text" class="form-control" id="week_duration" name="week_duration" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <table class="m-table table table-bordered m-custom_table">
                                <col width="*" />
                                <col width="8%" />
                                <col width="5%" />
                                <col width="5%" />
                                <col width="8%" />
                                <col width="8%" />
                                <col width="8%" />
                                <col width="5%" />
                                <col width="5%" />
                                <col width="5%" />
                                <col width="8%" />
                                <col width="5%" />
                                <col width="8%" />
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center">Lots</th>
                                        <th class="text-center">Unit</th>
                                        <th class="text-center">Unit Cost</th>
                                        <th class="text-center">Total Cost</th>
                                        <th class="text-center">10% Retention</th>
                                        <th class="text-center">Previous Accomplishment</th>
                                        <th class="text-center">Accomplishment for this week</th>
                                        <th class="text-center">Remaining Balance</th>
                                        <th class="text-center">Variance</th>
                                        <th class="text-center">Amount Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="item_count > 0">
                                        <template v-for="(tempItems, tempIndex) in items">
                                            <tr v-bind:class="renderOddEvenClass(tempIndex)">
                                                <th colspan="12">
                                                    <h5 class="m-table--no-padding m--marginless">WORK ORDER # {{item_counter[tempIndex]}}</h5>
                                                </th>
                                            </tr>
                                            <template v-for="(vv, kk) in tempItems">
                                                <template v-if="vv.is_parent === true">
                                                    <tr v-bind:class="renderOddEvenClass(tempIndex)">
                                                        <td colspan="12">
                                                            <p class="m-widget11__title m--marginless m-widget11__task">
                                                                <i class="fa fa-square fa--custom_sm"></i>
                                                                {{vv.label}}
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <template v-else>
                                                    <tr v-bind:class="renderOddEvenClass(tempIndex)">
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
                                                        <td class="m--align-center m--custom-font-12">
                                                            {{vv.tariff}}
                                                        </td>
                                                        <td class="m--align-right m--custom-font-12 m--font-boldest">
                                                            {{vv.total}}
                                                        </td>
                                                        <td class="m--align-right m--custom-font-12">{{vv.retention}}</td>
                                                        <td class="m--align-center m--custom-font-12 m--font-brand m--font-boldest">{{vv.previous_qty}}</td>
                                                        <td class="m--align-center m--custom-font-12">
                                                            <input 
                                                                type="text" 
                                                                class="form-control form-control-table_field maskQty" 
                                                                :name="'accomplishment['+vv.row_id+']'" 
                                                                :data-id="vv.row_id" 
                                                                :data-remaining="vv.remaining"
                                                                :data-accomplishment="vv.previous_qty" />
                                                            <input 
                                                                type="hidden" 
                                                                :id="'previous_accomplishment-'+vv.row_id"
                                                                :name="'previous_accomplishment['+vv.row_id+']'" 
                                                                :value="vv.previous_qty" />
                                                            <input 
                                                                type="hidden" 
                                                                :id="'remaining_balance-'+vv.row_id"
                                                                :name="'remaining_balance['+vv.row_id+']'" 
                                                                :value="vv.remaining" />
                                                            <input 
                                                                type="hidden" 
                                                                :id="'variance-'+vv.row_id"
                                                                :name="'variance['+vv.row_id+']'" 
                                                                :value="vv.variance" />
                                                        </td>
                                                        <td class="m--align-center m--custom-font-12">
                                                            <span>{{vv.remaining}}</span>
                                                        </td>
                                                        <td class="m--align-center m--custom-font-12">
                                                            <span>{{vv.variance}}</span> % 
                                                        </td>
                                                        <td class="m--align-center m--custom-font-12">0.00</td>
                                                    </tr>
                                                </template>
                                            </template>
                                            <template v-if="adjustments[tempIndex] && adjustments[tempIndex].length > 0">
                                                <tr v-bind:class="renderOddEvenClass(tempIndex)">
                                                    <th colspan="12">
                                                        <h5 class="m-table--no-padding m--marginless m--font-brand">Adjustment</h5>
                                                    </th>
                                                </tr>
                                                <template v-for="(vvx, kkx) in adjustments[tempIndex]">
                                                    <template v-if="vvx.is_parent === true">
                                                        <tr v-bind:class="renderOddEvenClass(tempIndex)">
                                                            <td colspan="12">
                                                                <p class="m-widget11__title m--marginless m-widget11__task m--font-brand m--font-boldest">
                                                                    <i class="fa fa-square fa--custom_sm"></i>
                                                                    {{vvx.label}}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </template>
                                                    <template v-else>
                                                        <tr v-bind:class="renderOddEvenClass(tempIndex)">
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
                                                            <td class="m--align-center m--custom-font-12 m--font-primary m--font-brand m--font-boldest">
                                                                {{vvx.tariff}}
                                                            </td>
                                                            <td class="m--align-right m--custom-font-12 m--font-primary m--font-brand m--font-boldest">
                                                                {{vvx.total}}
                                                            </td>
                                                            <td class="m--align-right m--custom-font-12 m--font-brand m--font-boldest">{{vvx.retention}}</td>
                                                            <td class="m--align-center m--custom-font-12 m--font-brand m--font-boldest">{{vvx.previous_qty}}</td>
                                                            <td class="m--align-center m--custom-font-12">
                                                                <input 
                                                                    type="text" 
                                                                    class="form-control form-control-table_field maskQty" 
                                                                    :name="'accomplishment['+vvx.row_id+']'" 
                                                                    :data-id="vvx.row_id"
                                                                    :data-accomplishment="vvx.previous_qty" />
                                                                <input 
                                                                    type="hidden" 
                                                                    :id="'previous_accomplishment-'+vvx.row_id"
                                                                    :name="'previous_accomplishment['+vvx.row_id+']'" 
                                                                    :value="vvx.previous_qty" />
                                                                <input 
                                                                    type="hidden" 
                                                                    :id="'remaining_balance-'+vvx.row_id" 
                                                                    :name="'remaining_balance['+vvx.row_id+']'" 
                                                                    :value="vvx.remaining" />
                                                                <input 
                                                                    type="hidden" 
                                                                    :id="'remaining_balance-'+vvx.row_id" 
                                                                    :name="'variance['+vvx.row_id+']'" 
                                                                    :value="vvx.variance" />
                                                            </td>
                                                            <td class="m--align-center m--custom-font-12">
                                                                <span>{{vvx.remaining}}</span>
                                                            </td>
                                                            <td class="m--align-center m--custom-font-12">
                                                                <span>{{vvx.variance}}</span> % 
                                                            </td>
                                                            <td class="m--align-center m--custom-font-12 m--font-brand m--font-boldest">0.00</td>
                                                        </tr>
                                                    </template>
                                                </template>
                                            </template>
                                            <tr v-bind:class="renderOddEvenClass(tempIndex)" v-if="item_count > 1">
                                                <td colspan="5" class="text-right">&nbsp;</td>
                                                <td class="text-right m--font-boldest" style="border-top: 2px solid #000000 !important;"><h6 v-text="sub_total[tempIndex]" class="m--marginless m--font-danger m--font-boldest">0.00</h6></td>
                                                <td class="text-right m--font-boldest" style="border-top: 2px solid #000000 !important;"><h6 v-text="sub_ret_total[tempIndex]" class="m--marginless m--font-danger m--font-boldest">0.00</h6></td>
                                                <td colspan="5" class="text-right">&nbsp;</td>
                                            </tr>
                                        </template>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
table.m-custom_table tbody tr.odd{
    background-color: #02061B1A;
}
table.m-custom_table tbody tr > td{
    padding: 5px !important;
    vertical-align: middle;
}
table.m-custom_table tfoot tr > th{
    padding: 15px 0px !important;
}
</style>