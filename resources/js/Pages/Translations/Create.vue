<script setup>
import { reactive, ref } from "vue"
import { router, useForm } from "@inertiajs/vue3"
import IceLayout from '@IceDatabaseTranslationLocally/Layouts/IceLayout.vue'
import Form from "./Form.vue"
import ActionMenu from '@IceIcseusd/Components/totocsa/Icseusd/ActionMenu/ActionMenu.vue'
import LocalTranslationHeader from '@IceDatabaseTranslationLocally/Components/totocsa/LocalTranslation/LocalTranslationHeader.vue'

const success = ref(false);

const props = defineProps({
    userRoles: Object,
    routePrefix: String,
    routeController: String,
    model: Object,
    errors: Object,
})

const actionMenuConfig = {
    index: {
        label: 'Index',
        attributes: {
            href: route(`${props.routeController}.index`),
        }
    },
}

let da = {}
da = {
    category: '',
    subtitle: '',
}

const formData = reactive(da)

const form = useForm({ ...{ _method: 'POST' }, ...formData });

const formSubmit = (data) => {
    for (let i in data) {
        form[i] = data[i]
    }

    form.post(route(`${props.routeController}.store`), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            router.visit(route(`${props.routeController}.index`))
            success.value = true;

            setTimeout(() => {
                success.value = false;
            }, 2000);
        }
    });
};

const titleArray = ['Translation', 'Translations', 'ActionMenu', 'Create']
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
                    <Form :formData="formData" :formSubmit="formSubmit" :errors="props.errors" :success="success" />
                </div>
            </div>
        </div>
    </IceLayout>
</template>
