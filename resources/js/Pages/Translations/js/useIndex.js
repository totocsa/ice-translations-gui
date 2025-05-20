import { markRaw, ref } from "vue"
import { router } from "@inertiajs/vue3"
import { Workbook } from "exceljs"
import { saveAs } from "file-saver"
import { z } from "zod"
import axios from "axios"
import { useModalLiFoStore } from "@IceModalLiFo/Components/totocsa/ModalLiFo/ModalLiFoStore.js"
import ImportModal from "../ImportModal.vue"

export function useIndex() {
    const importExcelFile = ref(null)
    const importCount = ref(0)
    const importTotal = ref(0)
    const importErrors = ref({})
    const modalLiFoStore = useModalLiFoStore()

    const loaderRefresh = (routeController) => {
        console.log(route().params)
        try {
            router.get(
                route(`${routeController}.loaderrefresh`),
                {
                    route: {
                        current: route().current(),
                        params: route().params ?? [],
                    },
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    preserveUrl: true,
                    onSuccess: (r) => { },
                    error: (r) => {
                        console.log("Fetching: error")
                    },
                }
            )
        } catch (error) {
            console.error("Error fetching Index:", error)
        }
    }

    const exportToExcel = async () => {
        try {
            const response = await axios.get(route("api.translations.export"))
            const data = response.data

            const workbook = new Workbook()
            const worksheet = workbook.addWorksheet("Translations")

            worksheet.columns = [
                { header: "Category", key: "category" },
                { header: "Original", key: "original" },
                { header: "Language", key: "language" },
                { header: "Translation", key: "translation" },
            ]

            worksheet.getRow(1).eachCell((cell) => {
                cell.font = { bold: true, name: "Arial" }
                cell.alignment = { horizontal: "center", vertical: "top" }
            })

            data.forEach((row) => worksheet.addRow(row))

            worksheet.eachRow((row) => {
                row.eachCell((cell) => {
                    cell.alignment = { vertical: "top" }
                })
            })

            const minimalWidth = 10 // Alap minimális szélesség, ha szükséges
            worksheet.columns.forEach((column) => {
                let maxColumnLength = minimalWidth // Először állítsuk be a minimális szélességet

                if (column && typeof column.eachCell === "function") {
                    column.eachCell({ includeEmpty: true }, (cell) => {
                        const cellLength = cell.value
                            ? cell.value.toString().length
                            : 0
                        maxColumnLength = Math.max(maxColumnLength, cellLength)
                    })

                    // Néhány extra karakter a szöveg köré, hogy ne legyen túl szűk
                    column.width = maxColumnLength + 2
                }
            })

            const buffer = await workbook.xlsx.writeBuffer()
            saveAs(new Blob([buffer]), "translations.xlsx")
        } catch (error) {
            console.error("Export error:", error)
        }
    }

    const importRows = async (rows) => {
        try {
            const response = await fetch(route("api.translations.import"), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ rows }),
            })

            return await response.json()
        } catch (error) {
            console.error("Hiba az importálás során:", error)
        }
    }

    const changeImportFileExcel = (event) => {
        const file = event.target.files[0]

        if (file) {
            showImportModal()

            const reader = new FileReader()

            reader.onload = async (e) => {
                const buffer = e.target.result
                const workbook = new Workbook()
                await workbook.xlsx.load(buffer)

                const worksheet = workbook.worksheets[0] // Első munkalap

                const rowSchema = z.object({
                    category: z.string(),
                    original: z.string(),
                    language: z.string(),
                    translation: z.string(),
                })

                const rows = {}
                const errors = {}
                let rowsTotal = 0
                let result
                worksheet.eachRow({ includeEmpty: true }, (row, rowNumber) => {
                    if (rowNumber > 1) {
                        const rowData = {
                            category: row.getCell(1).value ?? "",
                            original: row.getCell(2).value ?? "",
                            language: row.getCell(3).value ?? "",
                            translation: row.getCell(4).value ?? "",
                        }

                        result = rowSchema.safeParse(rowData)

                        if (result.success) {
                            rows[rowNumber] = rowData
                            rowsTotal++
                        } else {
                            errors[rowNumber] = result.error.format()
                        }
                    }
                })

                if (Object.keys(errors).length === 0) {
                    const limit = 100
                    const firstRow = 2
                    let offset = 0
                    let index = 0
                    let batch = {}
                    let item
                    let rowNumber = firstRow
                    let result

                    importTotal.value = rowsTotal

                    item = rows[rowNumber]
                    while (item !== null) {
                        batch[rowNumber] = item

                        index++
                        if (index >= limit) {
                            result = await importRows(batch)
                            if (!Array.isArray(result.errors)) {
                                break
                            }

                            importCount.value += index
                            offset++
                            index = 0
                            batch = {}
                        }

                        rowNumber = offset * limit + index + firstRow
                        item = rows?.[rowNumber] ?? null
                    }

                    if (
                        Array.isArray(result.errors) &&
                        Object.keys(batch).length !== 0
                    ) {
                        result = await importRows(batch)

                        if (Array.isArray(result.errors)) {
                            importCount.value += index
                        }
                    }

                    if (!Array.isArray(result.errors)) {
                        importErrors.value = result.errors
                    }
                } else {
                    console.error(
                        "A fájlban érvénytelen adatok vannak!",
                        errors
                    )
                }
            }

            reader.readAsArrayBuffer(file)
            importExcelFile.value.value = null
        }
    }

    const showImportModal = () => {
        const itemId = location.protocol === 'https:' ? crypto.randomUUID() : 'x' + Date.now()
        importCount.value = 0
        importTotal.value = 0
        importErrors.value = {}

        modalLiFoStore.addToStack(itemId, markRaw(ImportModal), {
            itemId: itemId,
            importCount: importCount,
            importTotal: importTotal,
            importErrors: importErrors,
        })
    }

    const triggerClickImportExcelFile = () => {
        importExcelFile.value.click()
    }

    return {
        importExcelFile,
        importCount,
        importTotal,
        importErrors,
        triggerClickImportExcelFile,
        changeImportFileExcel,
        exportToExcel,
        loaderRefresh,
    }
}
