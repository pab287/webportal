<div class="row">
    <div class="col-md-12">
        <div class="form-group m-form__group">
            <label for="project_id">Development Site</label>
            <select class="form-control select2" id="project_id" name="project_id" data-validation="required"></select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group m-form__group">
            <label for="checklist_id">Rate Card</label>
            <select class="form-control select2" id="checklist_id" name="checklist_id" data-validation="required"></select>
        </div>
    </div>
    <div class="col-md-12">
        <div class="m-portlet m-portlet--unair m-portlet--mobile m-portlet--bordered">
            <div class="m-portlet__body">
                <div id="checklist_preview" class="form-group m-form__group">NO CHECKLIST PREVIEW!</div>
                <div id="selectedItems" class="form-group m-form__group"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group m-form__group">
            <label for="block">Block</label>
            <select class="form-control select2" id="block" name="block" data-validation="required"></select>
        </div>
    </div>
</div>
<div class="row">
    <!-- div class="col-md-12">
        <div class="form-group m-form__group">
            <label for="block">Lots</label>
            <select class="form-control select2" id="lots" name="lots[]" multiple="multiple" data-validation="required"></select>
        </div>
    </div -->
    <div class="col-md-12">
        <button type="button" class="btn m-btn btn-sm btn-primary btnNew" onclick="generateBlockLots()">Generate Lot</button>
    </div>
</div>
<div id="block_lot-content" class="m--margin-top-15">
    <template v-if="count > 0">
        <template v-for="(vv, ii) in blocks" v-if="block_count[vv] > 0">
        <div class="m-portlet m-portlet--unair m-portlet--mobile m-portlet--bordered m--margin-bottom-10">
            <div class="m-portlet__body">
                <div class="row">
                <h6 class="col-12">BLOCK {{vv}}</h6>
                    <template v-for="(item, index) in rows" v-if="item.block == vv">
                    <div class="col-2">LOT {{item.lot}} <input type="hidden" name="other_units[]" :value="item.id" /></div>
                    </template>
                </div>
            </div>
        </div>
        </template>
    </template>
</div>