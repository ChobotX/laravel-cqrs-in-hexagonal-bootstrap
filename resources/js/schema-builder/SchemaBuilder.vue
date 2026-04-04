<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { onMounted, ref } from 'vue';
import { error as logError } from '../logger/logger';

interface FieldRow {
    name: string;
    label: string;
    type: string;
    required: boolean;
    multiline: boolean;
    referenceNamespace: string;
    referenceSlug: string;
    fileNamespace: string;
    fileMimeTypes: string;
    fileMaxSize: string;
    minItems: string;
    maxItems: string;
    subFields: FieldRow[];
}

interface DefinitionOption {
    namespace: string;
    slug: string;
    name: string;
}

const FIELD_TYPE_VALUES = [
    'string',
    'integer',
    'number',
    'boolean',
    'date',
    'email',
    'reference',
    'file',
    'repeater',
    'object',
] as const;
const COMPOSITE_TYPES = new Set(['repeater', 'object']);

function fieldTypeOptions(): { value: string; label: string }[] {
    return FIELD_TYPE_VALUES.map((v) => ({ value: v, label: trans(`messages.registry.versions.type_${v}`) }));
}

function simpleFieldTypeOptions(): { value: string; label: string }[] {
    return fieldTypeOptions().filter((ft) => !COMPOSITE_TYPES.has(ft.value));
}

const props = defineProps<{
    action: string;
    csrf: string;
}>();

function t(key: string): string {
    return trans(`messages.registry.versions.${key}`);
}

const definitions = ref<DefinitionOption[]>([]);
const namespaces = ref<string[]>([]);

async function loadDefinitions(): Promise<void> {
    const headers = fetchHeaders();
    try {
        const response = await fetch('/internal-api/registry/definitions', { headers });
        const json = await response.json();
        definitions.value = json.data ?? [];
    } catch {
        logError('Failed to load definitions');
    }
}

async function loadNamespaces(): Promise<void> {
    const headers = fetchHeaders();
    try {
        const response = await fetch('/internal-api/registry/namespaces', { headers });
        const json = await response.json();
        namespaces.value = json.data ?? [];
    } catch {
        logError('Failed to load namespaces');
    }
}

function fetchHeaders(): Record<string, string> {
    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const headers: Record<string, string> = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' };
    if (csrfMeta) headers['X-CSRF-TOKEN'] = csrfMeta.content;
    return headers;
}

function referenceValue(field: FieldRow): string {
    if (field.referenceNamespace && field.referenceSlug) return `${field.referenceNamespace}/${field.referenceSlug}`;
    return '';
}

function onReferenceSelected(field: FieldRow, value: string): void {
    const parts = value.split('/');
    field.referenceNamespace = parts[0] ?? '';
    field.referenceSlug = parts[1] ?? '';
}

onMounted(() => {
    void loadDefinitions();
    void loadNamespaces();
});

function createEmptyField(): FieldRow {
    return {
        name: '',
        label: '',
        type: 'string',
        required: false,
        multiline: false,
        referenceNamespace: '',
        referenceSlug: '',
        fileNamespace: '',
        fileMimeTypes: '',
        fileMaxSize: '',
        minItems: '',
        maxItems: '',
        subFields: [],
    };
}

const fields = ref<FieldRow[]>([createEmptyField()]);

const isSubmitting = ref(false);

function addField(): void {
    fields.value.push(createEmptyField());
}

function removeField(index: number): void {
    if (fields.value.length <= 1) return;
    fields.value.splice(index, 1);
}

function addSubField(parent: FieldRow): void {
    parent.subFields.push(createEmptyField());
}

function removeSubField(parent: FieldRow, index: number): void {
    parent.subFields.splice(index, 1);
}

function applyReferenceProps(field: Record<string, unknown>, f: FieldRow): void {
    field.referenceNamespace = f.referenceNamespace;
    field.referenceSlug = f.referenceSlug;
}

function applyFileProps(field: Record<string, unknown>, f: FieldRow): void {
    field.fileNamespace = f.fileNamespace || undefined;
    field.allowedMimeTypes = f.fileMimeTypes || undefined;
    field.maxSizeBytes = f.fileMaxSize ? Number(f.fileMaxSize) : undefined;
}

function applyCompositeProps(field: Record<string, unknown>, f: FieldRow): void {
    field.fields = f.subFields.map(buildFieldPayload).filter(Boolean);
    if (f.type === 'repeater') {
        field.minItems = f.minItems ? Number(f.minItems) : 0;
        field.maxItems = f.maxItems ? Number(f.maxItems) : undefined;
    }
}

const TYPE_ENHANCERS: Record<string, (field: Record<string, unknown>, f: FieldRow) => void> = {
    reference: applyReferenceProps,
    file: applyFileProps,
    repeater: applyCompositeProps,
    object: applyCompositeProps,
};

function buildFieldPayload(f: FieldRow): Record<string, unknown> | null {
    if (f.name.trim() === '') return null;

    const field: Record<string, unknown> = {
        name: f.name.trim(),
        label: f.label.trim() || f.name.trim(),
        type: f.type,
        required: f.required,
    };

    if (f.type === 'string' && f.multiline) field.multiline = true;
    TYPE_ENHANCERS[f.type]?.(field, f);
    return field;
}

function buildSchema(): Record<string, unknown> {
    return {
        fields: fields.value.map(buildFieldPayload).filter(Boolean),
    };
}

function submit(): void {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = props.action;
    form.style.display = 'none';

    appendHiddenInput(form, '_token', props.csrf);

    const schema = buildSchema();
    addFormFields(form, 'schema', schema);

    document.body.appendChild(form);
    form.submit();
}

function appendHiddenInput(form: HTMLFormElement, name: string, value: string): void {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    form.appendChild(input);
}

function formatValue(data: unknown): string {
    if (data === true) return '1';
    if (data === false) return '0';
    return String(data ?? '');
}

function addFormFields(form: HTMLFormElement, prefix: string, data: unknown): void {
    if (Array.isArray(data)) {
        for (let i = 0; i < data.length; i++) {
            addFormFields(form, `${prefix}[${i}]`, data[i]);
        }
        return;
    }
    if (typeof data === 'object' && data !== null) {
        for (const [key, value] of Object.entries(data as Record<string, unknown>)) {
            addFormFields(form, `${prefix}[${key}]`, value);
        }
        return;
    }
    appendHiddenInput(form, prefix, formatValue(data));
}

const inputClasses =
    'block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600';
const smallSelectClasses =
    'block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600';
const checkboxClasses =
    'size-4 cursor-pointer rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-600';
</script>

<template>
    <div class="space-y-3">
        <div
            v-for="(field, index) in fields"
            :key="index"
            class="rounded-lg border border-gray-200 p-3"
        >
            <div class="flex flex-wrap items-start gap-3">
                <div class="w-36">
                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('field_name') }}</label>
                    <input
                        v-model="field.name"
                        :class="inputClasses"
                        type="text"
                        pattern="[a-z][a-z0-9_]*"
                        required
                        :placeholder="t('field_name_placeholder')"
                    />
                </div>
                <div class="w-40">
                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('field_label') }}</label>
                    <input v-model="field.label" :class="inputClasses" type="text" :placeholder="t('display_label_placeholder')" />
                </div>
                <div class="w-32">
                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('field_type') }}</label>
                    <select v-model="field.type" :class="smallSelectClasses">
                        <option v-for="ft in fieldTypeOptions()" :key="ft.value" :value="ft.value">{{ ft.label }}</option>
                    </select>
                </div>
                <div class="flex items-end pb-2">
                    <label class="inline-flex cursor-pointer items-center gap-1.5">
                        <input v-model="field.required" type="checkbox" :class="checkboxClasses" />
                        <span class="text-xs font-medium text-gray-600">{{ t('field_required') }}</span>
                    </label>
                </div>
                <div v-if="field.type === 'string'" class="flex items-end pb-2">
                    <label class="inline-flex cursor-pointer items-center gap-1.5">
                        <input v-model="field.multiline" type="checkbox" :class="checkboxClasses" />
                        <span class="text-xs font-medium text-gray-600">{{ t('multiline') }}</span>
                    </label>
                </div>
                <!-- Reference: definition picker -->
                <div v-if="field.type === 'reference'" class="w-56">
                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('select_definition') }}</label>
                    <select
                        :class="smallSelectClasses"
                        :value="referenceValue(field)"
                        @change="onReferenceSelected(field, ($event.target as HTMLSelectElement).value)"
                    >
                        <option value="">—</option>
                        <option v-for="def in definitions" :key="`${def.namespace}/${def.slug}`" :value="`${def.namespace}/${def.slug}`">
                            {{ def.name }} ({{ def.namespace }}/{{ def.slug }})
                        </option>
                    </select>
                </div>
                <!-- File: namespace datalist + options -->
                <template v-if="field.type === 'file'">
                    <div class="w-36">
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('file_namespace') }}</label>
                        <input v-model="field.fileNamespace" :class="inputClasses" type="text" :list="`ns-${index}`" />
                        <datalist :id="`ns-${index}`">
                            <option v-for="ns in namespaces" :key="ns" :value="ns" />
                        </datalist>
                    </div>
                    <div class="w-40">
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('file_mime_types') }}</label>
                        <input v-model="field.fileMimeTypes" :class="inputClasses" type="text" placeholder="image/jpeg, application/pdf" />
                    </div>
                    <div class="w-28">
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('file_max_size') }}</label>
                        <input v-model="field.fileMaxSize" :class="inputClasses" type="number" min="0" placeholder="5242880" />
                    </div>
                </template>
                <template v-if="field.type === 'repeater'">
                    <div class="w-20">
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('min_items') }}</label>
                        <input v-model="field.minItems" :class="inputClasses" type="number" min="0" placeholder="0" />
                    </div>
                    <div class="w-20">
                        <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('max_items') }}</label>
                        <input v-model="field.maxItems" :class="inputClasses" type="number" min="0" />
                    </div>
                </template>
                <div class="flex items-end pb-1">
                    <button
                        type="button"
                        class="cursor-pointer rounded p-1.5 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                        :disabled="fields.length <= 1"
                        :title="t('remove_field')"
                        @click="removeField(index)"
                    >
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Sub-fields for repeater/object -->
            <div v-if="field.type === 'repeater' || field.type === 'object'" class="mt-3 ml-4 space-y-2 border-l-2 border-gray-200 pl-4">
                <p class="text-xs font-medium text-gray-500">{{ field.type === 'repeater' ? t('sub_fields') : t('properties') }}</p>
                <div
                    v-for="(sub, subIndex) in field.subFields"
                    :key="subIndex"
                    class="space-y-2 rounded border border-gray-100 p-2"
                >
                    <div class="flex flex-wrap items-start gap-2">
                        <div class="w-32">
                            <input v-model="sub.name" :class="inputClasses" type="text" pattern="[a-z][a-z0-9_]*" :placeholder="t('field_name_placeholder')" />
                        </div>
                        <div class="w-36">
                            <input v-model="sub.label" :class="inputClasses" type="text" :placeholder="t('display_label_placeholder')" />
                        </div>
                        <div class="w-28">
                            <select v-model="sub.type" :class="smallSelectClasses">
                                <option v-for="ft in simpleFieldTypeOptions()" :key="ft.value" :value="ft.value">{{ ft.label }}</option>
                            </select>
                        </div>
                        <label class="inline-flex cursor-pointer items-center gap-1 pt-2">
                            <input v-model="sub.required" type="checkbox" :class="checkboxClasses" />
                            <span class="text-xs text-gray-500">{{ t('field_required') }}</span>
                        </label>
                        <!-- Sub-field: string multiline -->
                        <div v-if="sub.type === 'string'" class="flex items-center gap-1 pt-2">
                            <label class="inline-flex cursor-pointer items-center gap-1">
                                <input v-model="sub.multiline" type="checkbox" :class="checkboxClasses" />
                                <span class="text-xs text-gray-500">{{ t('multiline') }}</span>
                            </label>
                        </div>
                        <button
                            type="button"
                            class="cursor-pointer rounded p-1 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                            :title="t('remove_field')"
                            :aria-label="t('remove_field')"
                            @click="removeSubField(field, subIndex)"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <!-- Sub-field: reference picker -->
                    <div v-if="sub.type === 'reference'" class="flex items-center gap-2 pl-1">
                        <select
                            class="w-56 rounded-lg border border-gray-300 px-2 py-1.5 text-xs shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600"
                            :value="referenceValue(sub)"
                            @change="onReferenceSelected(sub, ($event.target as HTMLSelectElement).value)"
                        >
                            <option value="">— {{ t('select_definition') }} —</option>
                            <option v-for="def in definitions" :key="`${def.namespace}/${def.slug}`" :value="`${def.namespace}/${def.slug}`">
                                {{ def.name }} ({{ def.namespace }}/{{ def.slug }})
                            </option>
                        </select>
                    </div>
                    <!-- Sub-field: file options -->
                    <div v-if="sub.type === 'file'" class="flex flex-wrap items-start gap-2 pl-1">
                        <div class="w-32">
                            <label class="mb-0.5 block text-xs text-gray-400">{{ t('file_namespace') }}</label>
                            <input v-model="sub.fileNamespace" :class="inputClasses" type="text" :list="`sub-ns-${index}-${subIndex}`" />
                            <datalist :id="`sub-ns-${index}-${subIndex}`">
                                <option v-for="ns in namespaces" :key="ns" :value="ns" />
                            </datalist>
                        </div>
                        <div class="w-36">
                            <label class="mb-0.5 block text-xs text-gray-400">{{ t('file_mime_types') }}</label>
                            <input v-model="sub.fileMimeTypes" :class="inputClasses" type="text" placeholder="image/jpeg" />
                        </div>
                        <div class="w-24">
                            <label class="mb-0.5 block text-xs text-gray-400">{{ t('file_max_size') }}</label>
                            <input v-model="sub.fileMaxSize" :class="inputClasses" type="number" min="0" />
                        </div>
                    </div>
                </div>
                <button
                    type="button"
                    class="cursor-pointer text-xs text-indigo-600 transition-colors hover:text-indigo-800"
                    @click="addSubField(field)"
                >
                    + {{ t('add_field') }}
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="button"
                class="cursor-pointer rounded-lg border border-dashed border-gray-300 px-3 py-2 text-sm text-gray-500 transition-colors hover:border-indigo-300 hover:text-indigo-600"
                @click="addField"
            >
                + {{ t('add_field') }}
            </button>
            <button
                type="button"
                class="cursor-pointer rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-500"
                :disabled="isSubmitting"
                @click="submit"
            >
                {{ t('create_action') }}
            </button>
        </div>
    </div>
</template>
