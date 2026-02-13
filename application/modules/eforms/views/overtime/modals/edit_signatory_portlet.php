<template v-if="count > 0">
    <div :id="'m--portlet_append_'+index" class="m-portlet m-portlet--bordered m-portlet--head-sm m-portlet--mobile m-portlet--sortable mb-1" data-portlet="true" v-for="(item, index) in row.meta">
        <div class="m-portlet__head ui-sortable-handle">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <span class="m-portlet__head-icon">
                        <i class="la la-pencil m--font-success"></i>
                    </span>
                    <h3 class="m-portlet__head-text m--font-success">Signatory Field</h3>
                </div>
            </div>
            <div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <a href="javascript:void(0);" @click="removePortlet(event, index)" class="m-portlet__nav-link m-portlet__nav-link--icon m--removePortlet">
                            <i class="la la-close"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-5">
                    <div class="form-group m-form__group">
                        <label>Label / Description *</label>
                        <input type="text" class="form-control m-input" name="label[]" data-validation="required" autocomplete="off" style="height: auto;" v-model="item.label" />
                    </div>
                </div>
                <div class="col-7">
                    <div class="form-group m-form__group">
                        <label>Name *</label>
                        <select class="form-control m-input select2--value" name="value[]"  data-validation="required">
                            <option value="">&nbsp;</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>