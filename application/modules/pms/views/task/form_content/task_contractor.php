<div id="m_widget2_tab1_content">
<template v-for="(item, index) in contractor">
    <div class="m-widget2">
        <div class="m-widget2__item " :class="contractorClass(item.status)" @click="previewContractor(item.id)">
            <div class="m-widget2__checkbox">&nbsp;</div>
            <div class="m-widget2__desc">
                <p class="m-widget2__text m--marginless" v-text="item.contractor">&nbsp;</p>
                <span class="m-widget2__user-name">
                    <a href="javascript:void(0);"  class="m-widget2__link">
                        {{item.wo_code}}
                    </a>
                </span>
            </div>
            <div class="m-widget2__actions">
                <span class="m-badge m-badge--wide m--bg-warning m--font-light fadeIn animated" v-if="item.status === '1'">ACTIVE</span>
                <span class="m-badge m-badge--wide m--bg-primary m--font-light fadeIn animated" v-if="item.status === '2'">COMPLETED</span>
                <span class="m-badge m-badge--wide m--bg-danger m--font-light fadeIn animated" v-if="item.status === '3'">TERMINATED</span>
            </div>
        </div>
    </div>
</template>
</div>