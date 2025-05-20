<script setup>
import { useFilters } from "@IceIcseusd/Components/totocsa/Icseusd/js/useFilters";
import LocalTranslation from "@IceDatabaseTranslationLocally/Components/totocsa/LocalTranslation/LocalTranslation.vue";

const props = defineProps({
    formData: Object,
    formSubmit: Function,
    errors: Object,
    success: Boolean,
})

const { modelIdName } = useFilters()

const categoryModelIdName = modelIdName(props.formData, 'category', 'translationoriginals')
const subtitleModelIdName = modelIdName(props.formData, 'subtitle', 'translationoriginals')
</script>

<template>
    <form @submit.prevent="formSubmit(formData)" class="table m-auto w-auto">
        <div>
            <label :for="categoryModelIdName.id" class="block">
                <LocalTranslation category="App\\Models\\Translationoriginal" subtitle="category" />
            </label>

            <input v-model="categoryModelIdName.model" type="text" :id="categoryModelIdName.id"
                :name="categoryModelIdName.name" class="block" />

            <div class="error text-red-600">
                {{ props.errors.category }}
            </div>
        </div>

        <div class="mt-2">
            <label :for="subtitleModelIdName.id" class="block">
                <LocalTranslation category="App\\Models\\Translationoriginal" subtitle="subtitle" />
            </label>

            <input v-model="subtitleModelIdName.model" type="text" :id="subtitleModelIdName.id"
                :name="subtitleModelIdName.name" class="block" />

            <div class="error text-red-600">
                {{ props.errors.subtitle }}
            </div>
        </div>

        <div class="mt-4 text-center" :class="{ 'animate-success-form': success, }">
            <button class="bg-indigo-600 hover:bg-indigo-500 rounded p-2 text-gray-100">Submit</button>
        </div>
    </form>
</template>
