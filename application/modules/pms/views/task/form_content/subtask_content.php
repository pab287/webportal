<template v-if="task_count > 0">
    <div class="m-widget4">
        <template v-for="(item, index) in task">
            <div class="m-widget4__item">
                <div class="m-widget4__info">
                    <p class="m--margin-bottom-15">
                    <span class="m-widget4__title" v-text="item.task_name"></span><span class="m-badge m-badge--wide m--bg-danger m--font-light m-badge--past_due" v-if="isPastDueItem(item) === true">PAST DUE</span>
                    <span class="m-widget4__sub pull-right" v-text="item.created_at"></span>
                    </p>
                    <p class="m--marginless">
                    <span class="m-widget4__text" v-text="item.contractor"></span>
                    <span class="m-widget4__text pull-right" v-if="item.extension_id !== '0'">EXTENDED DUE DATE</span>
                    </p>
                    <p class="m--margin-bottom-10">
                        <template v-if="item.description">
                            <span class="m-widget4__sub" v-text="item.description"></span>
                        </template>
                        <template v-else>
                            <span class="m-widget4__sub">NO TASK DESCRIPTION</span>
                        </template>
                    </p>
                    <p class="m--margin-bottom-10">
                        <span class="m-widget4__text">TASK IN-CHARGE : {{item.incharge}}</span>
                    </p>
                    <p class="m--marginless text-right">
                        <template v-if="item.activity_count > 0">
                            <span class="m-widget4__title pull-left">COMMENTS: <span class="m-badge m-badge--danger">{{item.activity_count}}</span></span>
                        </template>
                        <template v-if="item.extension_id !== '0'">
                            <span class="m-widget4__sub m-widget4__sub--strong">[ ISSUED DATE : {{item.issued_date}} | <span class="m--font-warning">DUE DATE : {{item.extension_date}}</span> ]</span>
                        </template>
                        <template v-else>
                            <span class="m-widget4__sub m-widget4__sub--strong">[ ISSUED DATE : {{item.issued_date}} | DUE DATE : {{item.due_date}} ]</span>
                        </template>
                    </p>
                </div>
                <div class="m-widget4__ext" v-if="item.is_punchlisted === false">
                    <template v-if="item.task_status === '1'">
                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-brand btn btn-sm btn-brand" @click="getModalSubTaskContent(item.id)">
                            IN PROGRESS
                        </a>
                    </template>
                    <template v-else-if="item.task_status === '2'">
                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-success btn btn-sm btn-success" @click="getModalSubTaskContent(item.id)">
                            DEFERRED
                        </a>
                    </template>
                    <template v-else-if="item.task_status === '3'">
                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-metal btn btn-sm btn-metal" @click="getModalSubTaskContent(item.id)">
                            COMPLETED
                        </a>
                    </template>
                    <template v-else-if="item.task_status === '4'">
                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-danger btn btn-sm btn-danger" @click="getModalSubTaskContent(item.id)">
                            TERMINATED
                        </a>
                    </template>
                    <template v-else-if="item.task_status === '5'">
                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-primary btn btn-sm btn-primary" @click="getModalSubTaskContent(item.id)">
                            PUNCHLIST
                        </a>
                    </template>
                    <template v-else>
                        <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-metal btn btn-sm btn-metal" @click="getModalSubTaskContent(item.id)">
                            AWAITING
                        </a>
                    </template>
                </div>
                <div class="m-widget4__ext" v-else>
                    <a href="javascript:void(0);" class="m-btn m-btn--pill m-btn--hover-metal btn btn-sm btn-primary" @click="getModalSubTaskContent(item.id)">
                        PUNCHLISTED
                    </a>
                </div>
            </div>
        </template>
    </div>
</template>