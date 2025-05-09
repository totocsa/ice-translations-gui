<script setup>
import { XMarkIcon } from '@heroicons/vue/20/solid'
import { useModalLiFoStore } from '@/Components/totocsa/ModalLiFo/ModalLiFoStore.js'
import Modal from "@/Components/totocsa/ModalLiFo/Modal.vue"
import LocalTranslation from "@/Components/totocsa/LocalTranslation/LocalTranslation.vue"
import CountItemsAndTranslationIcon from "@/Components/totocsa/ModalLiFo/CountItemsAndTranslationIcon.vue"

const props = defineProps({
    importCount: Number,
    importTotal: Number,
    importErrors: Object,
})

const modalLiFoStore = useModalLiFoStore()

const closeModal = (event) => {
    modalLiFoStore.removeLast()
}
</script>

<template>
    <Modal>
        <!-- Fejléc -->
        <div class="flex justify-between rounded-t-lg p-3 bg-blue-500 text-gray-100">
            <div class="text-lg">
                <LocalTranslation category="TranslationsImport" subtitle="Translations import" />
            </div>

            <div class="flex items-start ml-2">
                <CountItemsAndTranslationIcon />

                <XMarkIcon @click="closeModal"
                    class="bg-blue-700 cursor-pointer inline-flex rounded hover:bg-blue-800 w-5" />
            </div>
        </div>

        <!-- Tartalom -->
        <div class="p-2 bg-gray-100">
            <div class="pb-2 pt-2">
                <div>
                    <LocalTranslation category="message" subtitle="Total rows: :total."
                        :params="{ ':total': importTotal }" />
                </div>

                <div>
                    <LocalTranslation category="message" subtitle="Number of rows imported: :count."
                        :params="{ ':count': importCount }" />
                </div>
            </div>

            <div v-if="Object.keys(importErrors).length === 0" class="pb-2 pt-2 text-center">&nbsp;</div>

            <div v-if="Object.keys(importErrors).length !== 0"
                class="pb-2 pt-2 table ml-auto mr-auto w-auto text-red-600">
                <LocalTranslation category="message"
                    subtitle="The import failed. The import is stopped at the first incorrect row, so it may be worth checking the other rows in the table as well."
                    class="table-caption" />
                <LocalTranslation v-if="importCount > 0" category="message"
                    subtitle="The first :count rows have been imported." :params="{ ':count': importCount }"
                    class="table-caption" />
                <div v-for="(i1, index1) of importErrors" :key="'importErrors-' + index1" class="table-row">
                    <div class="table-cell p-1">{{ index1 + '.' }}
                        <LocalTranslation category="TranslationsImport" subtitle="row" />
                    </div>

                    <div class="table-cell p-1">
                        <template v-for="(i2, index2) of i1" :key="'importErrorsRow-' + index2">
                            {{ i2[0].message }}<br />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gombok -->
        <div class="flex justify-end space-x-3 bg-gray-100 border-t border-gray-400 p-3 rounded-b-lg">
            <button @click="closeModal" type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                <LocalTranslation category="form" subtitle="Close" />
            </button>
        </div>
    </Modal>
</template>
