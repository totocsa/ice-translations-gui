<style>
.animate-fade-show {
    animation: fadeShow 750ms ease-in-out
}

.animate-fade-hide {
    animation: fadeHide 750ms ease-in-out
}

@keyframes fadeShow {
    0% {
        opacity: 0
    }

    100% {
        opacity: 1
    }
}

@keyframes fadeHide {
    0% {
        opacity: 1
    }

    100% {
        opacity: 0
    }
}
</style>

<script setup>
import { useIndex } from "./js/useIndex.js"
import { Link } from '@inertiajs/vue3'
import IceLayout from '@IceDatabaseTranslationLocally/Layouts/IceLayout.vue'
import IcseusdIndex from '@IceIcseusd/Components/totocsa/Icseusd/Index.vue'
import ControllerMenu from "@IceIcseusd/Components/totocsa/Icseusd/ControllerMenu.vue"
import ActionMenu from '@IceIcseusd/Components/totocsa/Icseusd/ActionMenu/ActionMenu.vue'
import LocalTranslation from '@IceDatabaseTranslationLocally/Components/totocsa/LocalTranslation/LocalTranslation.vue'

const props = defineProps({
    userRoles: Object,
    routePrefix: String,
    routeController: String,
    routeParameterName: [String, Object],
    modelClassName: String,
    items: Object,
    per_pages: {
        type: [Array, Object],
        default: () => [10, 20, 50, 100],
    },
    itemButtons: Object,
    filters: Object,
    sort: Object,
    fields: Object,
    orders: Object,
    routes: Object,
    additionalData: Object,
    paramNames: Object,
})

const { importExcelFile, triggerClickImportExcelFile, changeImportFileExcel, exportToExcel, loaderRefresh }
    = useIndex()

const titleArray = ['Translation', 'Translations', 'ActionMenu', 'Index']

const controllerMenuLink = ["inline-block", "m-1", "first:ml-0", "last:mr-0", "px-2", "py-1", "rounded"]
const controllerMenuLinkActive = controllerMenuLink.concat(["bg-gray-200"])

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
    loaderrefresh: {
        label: 'Loader refresh',
        attributes: {
            href: route(`${props.routeController}.loaderrefresh`),
            onclick: (event) => {
                event.preventDefault()
                loaderRefresh(props.routeController)
            },
        }
    },
    export: {
        label: 'Export',
        attributes: {
            href: route(`${props.routeController}.loaderrefresh`),
            onclick: (event) => {
                event.preventDefault()
                exportToExcel()
            }
        }
    },
    import: {
        label: 'Import',
        attributes: {
            href: '',
            onclick: (event) => {
                event.preventDefault()
                //showImportModal()
                triggerClickImportExcelFile()
            },
        }
    },
}
</script>

<template>
    <IceLayout :title="titleArray" :authUser="$page.props.auth.user">
        <template #header>
            <ControllerMenu :userRoles="props.userRoles" groupName="administration" active="translations-index">
                <Link v-if="props.userRoles.Administrator" :href="route('users.index')" :class="controllerMenuLink">
                <LocalTranslation category="ControllerMenu-item" subtitle="Users" />
                </Link>

                <Link :href="route('locales.index')" :class="controllerMenuLink">
                <LocalTranslation category="ControllerMenu-item" subtitle="Locales" />
                </Link>

                <Link id="translations-index" :href="route('translations.index')" :class="controllerMenuLinkActive">
                <LocalTranslation category="ControllerMenu-item" subtitle="Translations" />
                </Link>
            </ControllerMenu>

            <div class="text-right w-[100%]">
                <ActionMenu :config="actionMenuConfig" active="index" />
            </div>
        </template>

        <input type="file" ref="importExcelFile" accept=".xlsx, .xls" class="hidden" @change="changeImportFileExcel" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-4 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <IcseusdIndex :config="props" />
                </div>
            </div>
        </div>
    </IceLayout>
</template>
