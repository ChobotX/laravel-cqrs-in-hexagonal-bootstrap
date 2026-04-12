<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { computed, onMounted, ref } from 'vue';
import { error as logError } from '../../core/logger/logger';
import { isRecord } from '../../shared/type-guards/is-record';

interface FieldSchema {
    type: string;
    format?: string;
    'x-field-label'?: string;
    'x-field-type'?: string;
    'x-multiline'?: boolean;
    'x-reference'?: { namespace: string; slug: string };
    'x-file'?: { allowedMimeTypes?: string[]; maxSizeBytes?: number; namespace?: string };
    minimum?: number;
    maximum?: number;
    minLength?: number;
    maxLength?: number;
    minItems?: number;
    maxItems?: number;
    properties?: Record<string, FieldSchema>;
    required?: string[];
    items?: FieldSchema;
}

interface RootSchema {
    type: string;
    properties?: Record<string, FieldSchema>;
    required?: string[];
}

interface ReferenceOption {
    id: string;
    title: string;
}

const props = defineProps<{
    schema: string;
    values: string;
    errors: string;
    fieldPrefix: string;
}>();

const parsed = computed<RootSchema>(() => JSON.parse(props.schema));
const formValues = ref<Record<string, unknown>>(JSON.parse(props.values || '{}'));
const formErrors = computed<Record<string, string[]>>(() => JSON.parse(props.errors || '{}'));

const referenceOptions = ref<Record<string, ReferenceOption[]>>({});
const referenceLoading = ref<Record<string, boolean>>({});

const SIMPLE_TYPE_MAP: Record<string, string> = { boolean: 'boolean', integer: 'integer', number: 'number' };
const FORMAT_TYPE_MAP: Record<string, string> = { date: 'date', email: 'email' };

function resolveByExtensions(schema: FieldSchema): string | null {
    if (schema['x-field-type'] === 'file' || schema['x-field-type'] === 'repeater') return schema['x-field-type'];
    if (schema['x-reference']) return 'reference';
    if (schema['x-multiline']) return 'textarea';
    return null;
}

function getFieldType(schema: FieldSchema): string {
    const ext = resolveByExtensions(schema);
    if (ext) return ext;
    if (schema.type === 'object' && schema.properties) return 'object';
    if (schema.format) return FORMAT_TYPE_MAP[schema.format] ?? 'string';
    return SIMPLE_TYPE_MAP[schema.type] ?? 'string';
}

function getInputName(fieldName: string): string {
    return `${props.fieldPrefix}[${fieldName}]`;
}

function getFieldValue(fieldName: string): unknown {
    return formValues.value[fieldName] ?? '';
}

function setFieldValue(fieldName: string, value: unknown): void {
    formValues.value[fieldName] = value;
}

function getFieldErrors(fieldName: string): string[] {
    const key = `${props.fieldPrefix}.${fieldName}`;
    return formErrors.value[key] ?? [];
}

function isRequired(fieldName: string): boolean {
    return parsed.value.required?.includes(fieldName) ?? false;
}

function getRepeaterRows(fieldName: string): Record<string, unknown>[] {
    const val = formValues.value[fieldName];
    if (!Array.isArray(val) || val.length === 0) {
        return [{}];
    }
    const rows = val.filter(isRecord);
    return rows.length > 0 ? rows : [{}];
}

function addRepeaterRow(fieldName: string, schema: FieldSchema): void {
    const rows = getRepeaterRows(fieldName);
    const max = schema.maxItems;
    if (max !== undefined && rows.length >= max) return;
    formValues.value[fieldName] = [...rows, {}];
}

function removeRepeaterRow(fieldName: string, index: number, schema: FieldSchema): void {
    const rows = getRepeaterRows(fieldName);
    const min = schema.minItems ?? 0;
    if (rows.length <= min) return;
    formValues.value[fieldName] = rows.filter((_, i) => i !== index);
}

function getObjectValue(fieldName: string): Record<string, unknown> {
    const val = formValues.value[fieldName];
    if (isRecord(val) && !Array.isArray(val)) {
        return val;
    }
    return {};
}

function setObjectFieldValue(objectName: string, subName: string, value: unknown): void {
    const obj = getObjectValue(objectName);
    obj[subName] = value;
    formValues.value[objectName] = { ...obj };
}

async function loadReferenceOptions(reference: { namespace: string; slug: string }): Promise<void> {
    const key = `${reference.namespace}/${reference.slug}`;
    if (referenceOptions.value[key] || referenceLoading.value[key]) return;

    referenceLoading.value[key] = true;
    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
    if (csrfMeta) headers['X-CSRF-TOKEN'] = csrfMeta.content;

    try {
        const response = await fetch(`/internal-api/registry/${reference.namespace}/${reference.slug}/entries`, {
            headers,
        });
        const json = await response.json();
        referenceOptions.value[key] = json.data ?? [];
    } catch {
        logError('Failed to load reference options');
        referenceOptions.value[key] = [];
    } finally {
        referenceLoading.value[key] = false;
    }
}

function getReferenceOptions(reference: { namespace: string; slug: string }): ReferenceOption[] {
    return referenceOptions.value[`${reference.namespace}/${reference.slug}`] ?? [];
}

function getFileAccept(schema: FieldSchema): string | undefined {
    const mimes = schema['x-file']?.allowedMimeTypes;
    if (mimes && mimes.length > 0) return mimes.join(',');
    return undefined;
}

onMounted(() => {
    if (!parsed.value.properties) return;
    for (const [, schema] of Object.entries(parsed.value.properties)) {
        if (schema['x-reference']) {
            void loadReferenceOptions(schema['x-reference']);
        }
    }
});

const inputClasses =
    'block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-base shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600 sm:text-sm';
const labelClasses = 'mb-1.5 block text-base font-medium text-gray-700 sm:text-sm';
const errorClasses = 'mt-1 text-base text-red-600 sm:text-sm';
const checkboxClasses =
    'size-4 cursor-pointer rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-600';
</script>

<template>
    <div v-if="parsed.properties" class="grid grid-cols-1 gap-x-6 gap-y-5 md:grid-cols-2 xl:grid-cols-4">
        <div
            v-for="(fieldSchema, fieldName) in parsed.properties"
            :key="fieldName"
            :class="{
                'col-span-full': getFieldType(fieldSchema) === 'repeater' || getFieldType(fieldSchema) === 'object',
                'md:col-span-2': getFieldType(fieldSchema) === 'textarea',
            }"
        >
            <!-- String -->
            <div v-if="getFieldType(fieldSchema) === 'string'">
                <label :class="labelClasses" :for="`field-${fieldName}`">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <input
                    :id="`field-${fieldName}`"
                    :class="inputClasses"
                    :name="getInputName(String(fieldName))"
                    type="text"
                    :value="getFieldValue(String(fieldName))"
                    :required="isRequired(String(fieldName))"
                    :minlength="fieldSchema.minLength"
                    :maxlength="fieldSchema.maxLength"
                    @input="setFieldValue(String(fieldName), ($event.target as HTMLInputElement).value)"
                />
                <p v-for="err in getFieldErrors(String(fieldName))" :key="err" :class="errorClasses">{{ err }}</p>
            </div>

            <!-- Textarea -->
            <div v-else-if="getFieldType(fieldSchema) === 'textarea'">
                <label :class="labelClasses" :for="`field-${fieldName}`">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <textarea
                    :id="`field-${fieldName}`"
                    :class="inputClasses"
                    :name="getInputName(String(fieldName))"
                    rows="3"
                    :required="isRequired(String(fieldName))"
                    @input="setFieldValue(String(fieldName), ($event.target as HTMLTextAreaElement).value)"
                    >{{ getFieldValue(String(fieldName)) }}</textarea
                >
                <p v-for="err in getFieldErrors(String(fieldName))" :key="err" :class="errorClasses">{{ err }}</p>
            </div>

            <!-- Email -->
            <div v-else-if="getFieldType(fieldSchema) === 'email'">
                <label :class="labelClasses" :for="`field-${fieldName}`">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <input
                    :id="`field-${fieldName}`"
                    :class="inputClasses"
                    :name="getInputName(String(fieldName))"
                    type="email"
                    :value="getFieldValue(String(fieldName))"
                    :required="isRequired(String(fieldName))"
                    @input="setFieldValue(String(fieldName), ($event.target as HTMLInputElement).value)"
                />
                <p v-for="err in getFieldErrors(String(fieldName))" :key="err" :class="errorClasses">{{ err }}</p>
            </div>

            <!-- Date -->
            <div v-else-if="getFieldType(fieldSchema) === 'date'">
                <label :class="labelClasses" :for="`field-${fieldName}`">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <input
                    :id="`field-${fieldName}`"
                    :class="inputClasses"
                    :name="getInputName(String(fieldName))"
                    type="date"
                    :value="getFieldValue(String(fieldName))"
                    :required="isRequired(String(fieldName))"
                    @input="setFieldValue(String(fieldName), ($event.target as HTMLInputElement).value)"
                />
                <p v-for="err in getFieldErrors(String(fieldName))" :key="err" :class="errorClasses">{{ err }}</p>
            </div>

            <!-- Integer -->
            <div v-else-if="getFieldType(fieldSchema) === 'integer'">
                <label :class="labelClasses" :for="`field-${fieldName}`">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <input
                    :id="`field-${fieldName}`"
                    :class="inputClasses"
                    :name="getInputName(String(fieldName))"
                    type="number"
                    step="1"
                    :value="getFieldValue(String(fieldName))"
                    :required="isRequired(String(fieldName))"
                    :min="fieldSchema.minimum"
                    :max="fieldSchema.maximum"
                    @input="setFieldValue(String(fieldName), ($event.target as HTMLInputElement).value)"
                />
                <p v-for="err in getFieldErrors(String(fieldName))" :key="err" :class="errorClasses">{{ err }}</p>
            </div>

            <!-- Number -->
            <div v-else-if="getFieldType(fieldSchema) === 'number'">
                <label :class="labelClasses" :for="`field-${fieldName}`">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <input
                    :id="`field-${fieldName}`"
                    :class="inputClasses"
                    :name="getInputName(String(fieldName))"
                    type="number"
                    step="any"
                    :value="getFieldValue(String(fieldName))"
                    :required="isRequired(String(fieldName))"
                    :min="fieldSchema.minimum"
                    :max="fieldSchema.maximum"
                    @input="setFieldValue(String(fieldName), ($event.target as HTMLInputElement).value)"
                />
                <p v-for="err in getFieldErrors(String(fieldName))" :key="err" :class="errorClasses">{{ err }}</p>
            </div>

            <!-- Boolean -->
            <div v-else-if="getFieldType(fieldSchema) === 'boolean'">
                <label class="inline-flex cursor-pointer items-center gap-2">
                    <input
                        :name="getInputName(String(fieldName))"
                        type="hidden"
                        value="0"
                    />
                    <input
                        :id="`field-${fieldName}`"
                        :class="checkboxClasses"
                        :name="getInputName(String(fieldName))"
                        type="checkbox"
                        value="1"
                        :checked="!!getFieldValue(String(fieldName))"
                        @change="setFieldValue(String(fieldName), ($event.target as HTMLInputElement).checked)"
                    />
                    <span class="text-base font-medium text-gray-700 sm:text-sm">{{ fieldSchema['x-field-label'] || fieldName }}</span>
                </label>
            </div>

            <!-- Reference -->
            <div v-else-if="getFieldType(fieldSchema) === 'reference'">
                <label :class="labelClasses" :for="`field-${fieldName}`">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <select
                    :id="`field-${fieldName}`"
                    :class="inputClasses"
                    :name="getInputName(String(fieldName))"
                    :required="isRequired(String(fieldName))"
                    @change="setFieldValue(String(fieldName), ($event.target as HTMLSelectElement).value)"
                >
                    <option value="">—</option>
                    <option
                        v-for="opt in getReferenceOptions(fieldSchema['x-reference']!)"
                        :key="opt.id"
                        :value="opt.id"
                        :selected="getFieldValue(String(fieldName)) === opt.id"
                    >
                        {{ opt.title }}
                    </option>
                </select>
                <p v-for="err in getFieldErrors(String(fieldName))" :key="err" :class="errorClasses">{{ err }}</p>
            </div>

            <!-- File -->
            <div v-else-if="getFieldType(fieldSchema) === 'file'">
                <label :class="labelClasses" :for="`field-${fieldName}`">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <div v-if="getFieldValue(String(fieldName))" class="mb-2 text-sm text-gray-500">
                    <a
                        :href="`/files/${getFieldValue(String(fieldName))}`"
                        class="cursor-pointer text-indigo-600 transition-colors hover:text-indigo-800"
                        target="_blank"
                    >{{ getFieldValue(String(fieldName)) }}</a>
                </div>
                <input
                    :id="`field-${fieldName}`"
                    class="block w-full cursor-pointer text-sm text-gray-500 file:mr-4 file:cursor-pointer file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                    :name="getInputName(String(fieldName))"
                    type="file"
                    :accept="getFileAccept(fieldSchema)"
                    :required="isRequired(String(fieldName)) && !getFieldValue(String(fieldName))"
                />
                <p v-for="err in getFieldErrors(String(fieldName))" :key="err" :class="errorClasses">{{ err }}</p>
            </div>

            <!-- Repeater -->
            <div v-else-if="getFieldType(fieldSchema) === 'repeater' && fieldSchema.items">
                <label :class="labelClasses">
                    {{ fieldSchema['x-field-label'] || fieldName }}
                    <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <div
                        v-for="(row, rowIndex) in getRepeaterRows(String(fieldName))"
                        :key="rowIndex"
                        class="flex items-start gap-3 rounded-lg border border-gray-200 p-3"
                    >
                        <div class="flex-1 space-y-3">
                            <div v-for="(subSchema, subName) in fieldSchema.items.properties" :key="subName">
                                <label class="mb-1 block text-xs font-medium text-gray-600">
                                    {{ subSchema['x-field-label'] || subName }}
                                </label>
                                <!-- Sub-field: textarea -->
                                <textarea
                                    v-if="getFieldType(subSchema) === 'textarea'"
                                    :class="inputClasses"
                                    :name="`${fieldPrefix}[${String(fieldName)}][${rowIndex}][${String(subName)}]`"
                                    rows="2"
                                    >{{ row[String(subName)] ?? '' }}</textarea
                                >
                                <!-- Sub-field: boolean -->
                                <label v-else-if="getFieldType(subSchema) === 'boolean'" class="inline-flex cursor-pointer items-center gap-2">
                                    <input :name="`${fieldPrefix}[${String(fieldName)}][${rowIndex}][${String(subName)}]`" type="hidden" value="0" />
                                    <input
                                        :class="checkboxClasses"
                                        :name="`${fieldPrefix}[${String(fieldName)}][${rowIndex}][${String(subName)}]`"
                                        type="checkbox"
                                        value="1"
                                        :checked="!!row[String(subName)]"
                                    />
                                </label>
                                <!-- Sub-field: number/integer -->
                                <input
                                    v-else-if="getFieldType(subSchema) === 'integer' || getFieldType(subSchema) === 'number'"
                                    :class="inputClasses"
                                    :name="`${fieldPrefix}[${String(fieldName)}][${rowIndex}][${String(subName)}]`"
                                    type="number"
                                    :step="getFieldType(subSchema) === 'integer' ? '1' : 'any'"
                                    :value="row[String(subName)] ?? ''"
                                    :min="subSchema.minimum"
                                    :max="subSchema.maximum"
                                />
                                <!-- Sub-field: date -->
                                <input
                                    v-else-if="getFieldType(subSchema) === 'date'"
                                    :class="inputClasses"
                                    :name="`${fieldPrefix}[${String(fieldName)}][${rowIndex}][${String(subName)}]`"
                                    type="date"
                                    :value="row[String(subName)] ?? ''"
                                />
                                <!-- Sub-field: email -->
                                <input
                                    v-else-if="getFieldType(subSchema) === 'email'"
                                    :class="inputClasses"
                                    :name="`${fieldPrefix}[${String(fieldName)}][${rowIndex}][${String(subName)}]`"
                                    type="email"
                                    :value="row[String(subName)] ?? ''"
                                />
                                <!-- Sub-field: default (string) -->
                                <input
                                    v-else
                                    :class="inputClasses"
                                    :name="`${fieldPrefix}[${String(fieldName)}][${rowIndex}][${String(subName)}]`"
                                    :value="row[String(subName)] ?? ''"
                                    type="text"
                                />
                            </div>
                        </div>
                        <button
                            v-if="getRepeaterRows(String(fieldName)).length > (fieldSchema.minItems ?? 0)"
                            type="button"
                            class="mt-6 cursor-pointer rounded p-1 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                            :aria-label="trans('messages.registry.versions.remove_field')"
                            @click="removeRepeaterRow(String(fieldName), rowIndex, fieldSchema)"
                        >
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <button
                        type="button"
                        class="cursor-pointer rounded-lg border border-dashed border-gray-300 px-3 py-2 text-sm text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600"
                        :disabled="fieldSchema.maxItems !== undefined && getRepeaterRows(String(fieldName)).length >= fieldSchema.maxItems"
                        @click="addRepeaterRow(String(fieldName), fieldSchema)"
                    >
                        + {{ trans('messages.registry.entries.add_row') }}
                    </button>
                </div>
            </div>

            <!-- Object -->
            <div v-else-if="getFieldType(fieldSchema) === 'object' && fieldSchema.properties">
                <fieldset class="rounded-lg border border-gray-200 p-4">
                    <legend class="px-2 text-base font-medium text-gray-700 sm:text-sm">
                        {{ fieldSchema['x-field-label'] || fieldName }}
                        <span v-if="isRequired(String(fieldName))" class="text-red-500">*</span>
                    </legend>
                    <div class="space-y-4">
                        <div v-for="(subSchema, subName) in fieldSchema.properties" :key="subName">
                            <label :class="labelClasses" :for="`field-${fieldName}-${subName}`">
                                {{ subSchema['x-field-label'] || subName }}
                                <span v-if="fieldSchema.required?.includes(String(subName))" class="text-red-500">*</span>
                            </label>
                            <!-- Object sub-field: textarea -->
                            <textarea
                                v-if="getFieldType(subSchema) === 'textarea'"
                                :id="`field-${fieldName}-${subName}`"
                                :class="inputClasses"
                                :name="`${fieldPrefix}[${String(fieldName)}][${String(subName)}]`"
                                rows="3"
                                @input="setObjectFieldValue(String(fieldName), String(subName), ($event.target as HTMLTextAreaElement).value)"
                                >{{ getObjectValue(String(fieldName))[String(subName)] ?? '' }}</textarea
                            >
                            <!-- Object sub-field: boolean -->
                            <label v-else-if="getFieldType(subSchema) === 'boolean'" class="inline-flex cursor-pointer items-center gap-2">
                                <input :name="`${fieldPrefix}[${String(fieldName)}][${String(subName)}]`" type="hidden" value="0" />
                                <input
                                    :id="`field-${fieldName}-${subName}`"
                                    :class="checkboxClasses"
                                    :name="`${fieldPrefix}[${String(fieldName)}][${String(subName)}]`"
                                    type="checkbox"
                                    value="1"
                                    :checked="!!getObjectValue(String(fieldName))[String(subName)]"
                                    @change="setObjectFieldValue(String(fieldName), String(subName), ($event.target as HTMLInputElement).checked)"
                                />
                            </label>
                            <!-- Object sub-field: number/integer -->
                            <input
                                v-else-if="getFieldType(subSchema) === 'integer' || getFieldType(subSchema) === 'number'"
                                :id="`field-${fieldName}-${subName}`"
                                :class="inputClasses"
                                :name="`${fieldPrefix}[${String(fieldName)}][${String(subName)}]`"
                                type="number"
                                :step="getFieldType(subSchema) === 'integer' ? '1' : 'any'"
                                :value="getObjectValue(String(fieldName))[String(subName)] ?? ''"
                                :min="subSchema.minimum"
                                :max="subSchema.maximum"
                                @input="setObjectFieldValue(String(fieldName), String(subName), ($event.target as HTMLInputElement).value)"
                            />
                            <!-- Object sub-field: date -->
                            <input
                                v-else-if="getFieldType(subSchema) === 'date'"
                                :id="`field-${fieldName}-${subName}`"
                                :class="inputClasses"
                                :name="`${fieldPrefix}[${String(fieldName)}][${String(subName)}]`"
                                type="date"
                                :value="getObjectValue(String(fieldName))[String(subName)] ?? ''"
                                @input="setObjectFieldValue(String(fieldName), String(subName), ($event.target as HTMLInputElement).value)"
                            />
                            <!-- Object sub-field: email -->
                            <input
                                v-else-if="getFieldType(subSchema) === 'email'"
                                :id="`field-${fieldName}-${subName}`"
                                :class="inputClasses"
                                :name="`${fieldPrefix}[${String(fieldName)}][${String(subName)}]`"
                                type="email"
                                :value="getObjectValue(String(fieldName))[String(subName)] ?? ''"
                                @input="setObjectFieldValue(String(fieldName), String(subName), ($event.target as HTMLInputElement).value)"
                            />
                            <!-- Object sub-field: default (string) -->
                            <input
                                v-else
                                :id="`field-${fieldName}-${subName}`"
                                :class="inputClasses"
                                :name="`${fieldPrefix}[${String(fieldName)}][${String(subName)}]`"
                                type="text"
                                :value="getObjectValue(String(fieldName))[String(subName)] ?? ''"
                                @input="setObjectFieldValue(String(fieldName), String(subName), ($event.target as HTMLInputElement).value)"
                            />
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</template>
