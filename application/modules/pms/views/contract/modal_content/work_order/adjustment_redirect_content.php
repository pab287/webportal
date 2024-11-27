<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Adjustment Work Order</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <template v-if="count > 0">
        <div class="m-widget4">
            <div class="m-widget4__item" v-for="(item, index) in rows">
                <div class="m-widget4__info">
                    <p class="m-widget4__title m--marginless">WORK ORDER {{woIndexCount(index)}}</p>
                    <p class="m-widget4__sub m--marginless">{{item.wo_code}}</p>
                </div>
                <div class="m-widget4__ext">
                    <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-brand btn btn-sm btn-secondary" @click="redirectAdjustmentContract(item.contract_id, item.id)" data-dismiss="modal">Adjust</a>
                </div>
            </div>
        </div>
    </template>
    <template v-else>
        Nothing to edit!
    </template>
</div>