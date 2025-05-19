<script setup>
import { reactive } from "vue"
import IceLayout from '@/Layouts/IceLayout.vue';
import IcseusdEdit from '@/Components/totocsa/Icseusd/Edit.vue';
import ActionMenu from '@/Components/totocsa/Icseusd/ActionMenu/ActionMenu.vue';
import LocalTranslationHeader from '@/Components/totocsa/LocalTranslation/LocalTranslationHeader.vue';

const props = defineProps({
    userRoles: Object,
    routePrefix: String,
    routeController: String,
    routeParameterName: String,
    modelClassName: String,
    item: Object,
    fields: Object,
    orders: Object,
    additionalData: Object,
})

const actionMenuConfig = {
    index: {
        label: 'Index',
        attributes: {
            href: route(`${props.routeController}.index`),
        }
    },
    create: {
        label: 'Create',
        attributes: {
            href: route(`${props.routeController}.create`),
        }
    },
    show: {
        label: 'Show',
        attributes: {
            href: route(`${props.routeController}.show`, { [props.routeParameterName]: props.item.id }),
        }
    },
    destroy: {
        label: 'Delete',
        attributes: {
            href: route(`${props.routeController}.destroy`, { [props.routeParameterName]: props.item.id }),
        }
    }
}

let da = {}
for (let i of props.orders.item.fields) {
    da[i] = props.item[i]
}

const formData = reactive(da)

const titleArray = ['Translation', 'Translations', 'ActionMenu', 'Edit', props.item.id]
</script>

<template>
    <IceLayout :title="titleArray" :authUser="$page.props.auth.user">
        <template #header>
            <LocalTranslationHeader :titleArray="titleArray" />
            <ActionMenu :config="actionMenuConfig" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <IcseusdEdit :config="props" :formData="formData" />
                </div>
            </div>
        </div>
    </IceLayout>
</template>
