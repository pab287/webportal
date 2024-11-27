<form id="frmAdditionalContractItems" method="post" action="<?php echo site_url("pms/contract/do_post_event/set_new_contract_items"); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
    <div class="m-portlet fadeIn animated">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">TASK INFORMATION</h3>
                </div>
            </div>
            <div class="m-portlet__head-tools"></div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-md-12" id="parentTask">
                    <input type="hidden" name="contract_id" v-model="contract_id" />
                    <input type="hidden" name="checklist_id" v-model="checklist_id" />
                    <input type="hidden" name="units" v-model="units" />
                    <input type="hidden" name="item_id" v-model="parent_id" />
                    <div class="m-widget11">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <td class="m-widget11__description">DESCRIPTION</td>
                                        <td class="m-widget11__qty m--align-center">QTY</td>
                                        <td class="m-widget11__unit m--align-center">UNIT</td>
                                        <td class="m-widget11__unit_cost m--align-right">UNIT COST</td>
                                        <td class="m-widget11__total_cost m--align-right">TOTAL COST</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="count > 0">
                                        <template v-for="(vv, kk) in temp_items">
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
                                                    <td class="m--align-center m--custom-font-12">{{vv.qty}}
                                                    <input type="hidden" v-bind:name="'qty['+vv.id+']'" v-bind:value="vv.temp_qty" />
                                                    <input type="hidden" v-bind:name="'lots['+vv.id+']'" v-bind:value="vv.temp_lots" />
                                                    <input type="hidden" v-bind:name="'unit['+vv.id+']'" v-bind:value="vv.unit" />
                                                    <input type="hidden" v-bind:name="'tariff['+vv.id+']'" v-bind:value="vv.temp_tariff" />
                                                    </td>
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
                                    </template>
                                </tbody>
                                <!--end::Tbody-->
                                <tfoot>
                                    <tr><td colspan="5">&nbsp;</td></tr>
                                    <tr>
                                        <th colspan="4" class="text-right">GRAND TOTAL: </th>
                                        <th class="text-right m--font-primary">{{getGrandTotal}}</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <!--end::Table-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group m-form__group">
                        <label for="remarks">Remarks *</label>
                        <textarea id="remarks" name="remarks" rows="5" class="form-control m-input" data-validation="required"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-portlet__foot">
            <div class="row align-items-center">
                <div class="col-lg-12 m--align-right">
                    <button type="submit" class="btn btn-brand m-btn m-btn--pill m-btn--air">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
</form>