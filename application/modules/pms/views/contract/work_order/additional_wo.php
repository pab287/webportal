<div class="m-content">
    <div class="row">
        <div id="contract-content" class="col-lg-4 col-md-4">
            <!--begin::Portlet-->
            <div class="m-portlet m-portlet--mobile">
                <div class="m-portlet__head">
                    <div class="m-portlet__head-caption">
                        <div class="m-portlet__head-title">
                            <h3 class="m-portlet__head-text">Additional Work Order</h3>
                        </div>
                    </div>
                    <div class="m-portlet__head-tools"></div>
                </div>
                <form id="frmGenerateTask" method="post" action="<?php echo site_url("pms/contract/do_post_event/generate_additional_task_contract"); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="contract_id" v-model="id" />
                    <div class="m-portlet__body">
                        <div class="m-portlet m-portlet--unair m-portlet--mobile m-portlet--bordered">
                            <div class="m-portlet__body">
                                <div id="checklist_preview" class="form-group m-form__group">NO CHECKLIST PREVIEW!</div>
                                <div id="selectedItems" class="form-group m-form__group m--marginless"></div>
                            </div>
                        </div>
                        <div class="form-group m-form__group">
                            <label for="other_block_lot">
                                Block and Lot
                            </label>
                            <select class="form-control m-input" id="other_block_lot" name="other_units[]" multiple data-validation="required"></select>
                        </div>
                    </div>
                    <div class="m-portlet__foot">
                        <div class="row align-items-center">
                            <div class="col-lg-12 m--align-right">
                                <button type="submit" class="btn btn-brand m-btn m-btn--pill m-btn--air">Generate Task</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div id="contract_preview-content" class="col-lg-8 col-md-8"></div>
    </div>
</div>