<script setup>
import IceLayout from '@IceDatabaseTranslationLocally/Layouts/IceLayout.vue'
import IcseusdShow from '@IceIcseusd/Components/Icseusd/Show.vue';
import ActionMenu from '@IceIcseusd/Components/Icseusd/ActionMenu/ActionMenu.vue';
import LocalTranslationHeader from '@IceDatabaseTranslationLocally/Components/LocalTranslation/LocalTranslationHeader.vue';

const props = defineProps({
    userRoles: Object,
    routePrefix: String,
    routeController: String,
    routeParameterName: String,
    modelClassName: String,
    item: Object,
    orders: Object,
    fields: Object,
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
    edit: {
        label: 'Edit',
        attributes: {
            href: route(`${props.routeController}.edit`, { [props.routeParameterName]: props.item.id }),
        }
    },
    destroy: {
        label: 'Delete',
        attributes: {
            href: route(`${props.routeController}.destroy`, { [props.routeParameterName]: props.item.id }),
        }
    }
}

const titleArray = ['Translation', 'Translations', 'ActionMenu', 'Show', props.item.id]
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
                    <IcseusdShow :config="props" />
                </div>
            </div>
        </div>
    </IceLayout>
</template>
