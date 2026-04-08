<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { onMounted, ref } from 'vue';
import { error as logError } from '../../core/logger/logger';
import FieldConfigDrawer from './FieldConfigDrawer.vue';
import type { DefinitionOption, FieldRow } from './types';

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
        minLength: '',
        maxLength: '',
        pattern: '',
        min: '',
        max: '',
        step: '',
        minDate: '',
        maxDate: '',
        referenceNamespace: '',
        referenceSlug: '',
        fileNamespace: '',
        fileMimeTypes: '',
        fileMaxSize: '',
        minItems: '',
        maxItems: '',
        subFields: [],
        placeholder: '',
        helpText: '',
        defaultValue: '',
    };
}

const fields = ref<FieldRow[]>([createEmptyField()]);
const configField = ref<FieldRow | null>(null);
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

const TYPE_CONFIG_CHECKS: Record<string, (f: FieldRow) => boolean> = {
    string: (f) => !!(f.minLength || f.maxLength || f.pattern || f.multiline),
    integer: (f) => !!(f.min || f.max || f.step),
    number: (f) => !!(f.min || f.max || f.step),
    date: (f) => !!(f.minDate || f.maxDate),
    file: (f) => !!(f.fileMimeTypes || f.fileMaxSize),
    repeater: (f) => !!(f.minItems || f.maxItems),
};

function hasCustomConfig(field: FieldRow): boolean {
    if (field.placeholder || field.helpText) return true;
    if (field.defaultValue !== '' && field.defaultValue !== false) return true;

    return TYPE_CONFIG_CHECKS[field.type]?.(field) ?? false;
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

function applyStringProps(field: Record<string, unknown>, f: FieldRow): void {
    if (f.multiline) field.multiline = true;
    if (f.minLength) field.minLength = Number(f.minLength);
    if (f.maxLength) field.maxLength = Number(f.maxLength);
    if (f.pattern) field.pattern = f.pattern;
    if (f.defaultValue) field.defaultValue = f.defaultValue;
}

function applyNumericProps(field: Record<string, unknown>, f: FieldRow): void {
    if (f.min) field.min = Number(f.min);
    if (f.max) field.max = Number(f.max);
    if (f.step) field.step = Number(f.step);
    if (f.defaultValue) field.defaultValue = Number(f.defaultValue);
}

function applyDateProps(field: Record<string, unknown>, f: FieldRow): void {
    if (f.minDate) field.minDate = f.minDate;
    if (f.maxDate) field.maxDate = f.maxDate;
    if (f.defaultValue) field.defaultValue = f.defaultValue;
}

function applyBooleanProps(field: Record<string, unknown>, f: FieldRow): void {
    if (f.defaultValue === true || f.defaultValue === 'true') field.defaultValue = true;
}

const TYPE_ENHANCERS: Record<string, (field: Record<string, unknown>, f: FieldRow) => void> = {
    string: applyStringProps,
    integer: applyNumericProps,
    number: applyNumericProps,
    date: applyDateProps,
    boolean: applyBooleanProps,
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

    TYPE_ENHANCERS[f.type]?.(field, f);

    if (f.placeholder) field.placeholder = f.placeholder;
    if (f.helpText) field.helpText = f.helpText;

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
                <div>
                    <span class="mb-1 block h-4">&nbsp;</span>
                    <label class="inline-flex h-[38px] cursor-pointer items-center gap-1.5">
                        <input v-model="field.required" type="checkbox" :class="checkboxClasses" />
                        <span class="text-xs font-medium text-gray-600">{{ t('field_required') }}</span>
                    </label>
                </div>
                <!-- Reference: definition picker stays inline -->
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
                <!-- File: namespace stays inline -->
                <div v-if="field.type === 'file'" class="w-36">
                    <label class="mb-1 block text-xs font-medium text-gray-600">{{ t('file_namespace') }}</label>
                    <input v-model="field.fileNamespace" :class="inputClasses" type="text" :list="`ns-${index}`" />
                    <datalist :id="`ns-${index}`">
                        <option v-for="ns in namespaces" :key="ns" :value="ns" />
                    </datalist>
                </div>
                <!-- Configure button -->
                <div>
                    <span class="mb-1 block h-4">&nbsp;</span>
                    <div class="flex h-[38px] items-center">
                        <button
                            type="button"
                            class="relative cursor-pointer rounded p-1.5 text-gray-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                            :title="t('configure_field')"
                            @click="configField = field"
                        >
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span
                                v-if="hasCustomConfig(field)"
                                class="absolute -top-0.5 -right-0.5 size-2.5 rounded-full bg-indigo-500"
                            />
                        </button>
                    </div>
                </div>
                <!-- Delete button -->
                <div>
                    <span class="mb-1 block h-4">&nbsp;</span>
                    <div class="flex h-[38px] items-center">
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
                        <!-- Sub-field: reference picker inline -->
                        <div v-if="sub.type === 'reference'" class="w-48">
                            <select
                                class="w-full rounded-lg border border-gray-300 px-2 py-1.5 text-xs shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600"
                                :value="referenceValue(sub)"
                                @change="onReferenceSelected(sub, ($event.target as HTMLSelectElement).value)"
                            >
                                <option value="">— {{ t('select_definition') }} —</option>
                                <option v-for="def in definitions" :key="`${def.namespace}/${def.slug}`" :value="`${def.namespace}/${def.slug}`">
                                    {{ def.name }} ({{ def.namespace }}/{{ def.slug }})
                                </option>
                            </select>
                        </div>
                        <!-- Sub-field: file namespace inline -->
                        <div v-if="sub.type === 'file'" class="w-32">
                            <input v-model="sub.fileNamespace" :class="inputClasses" type="text" :list="`sub-ns-${index}-${subIndex}`" :placeholder="t('file_namespace')" />
                            <datalist :id="`sub-ns-${index}-${subIndex}`">
                                <option v-for="ns in namespaces" :key="ns" :value="ns" />
                            </datalist>
                        </div>
                        <!-- Sub-field: configure -->
                        <button
                            type="button"
                            class="relative mt-1.5 cursor-pointer rounded p-1 text-gray-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                            :title="t('configure_field')"
                            @click="configField = sub"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <span
                                v-if="hasCustomConfig(sub)"
                                class="absolute -top-0.5 -right-0.5 size-2 rounded-full bg-indigo-500"
                            />
                        </button>
                        <!-- Sub-field: delete -->
                        <button
                            type="button"
                            class="mt-1.5 cursor-pointer rounded p-1 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                            :title="t('remove_field')"
                            :aria-label="t('remove_field')"
                            @click="removeSubField(field, subIndex)"
                        >
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
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

        <!-- Config drawer -->
        <FieldConfigDrawer
            v-if="configField"
            :field="configField"
            :definitions="definitions"
            :namespaces="namespaces"
            @close="configField = null"
        />
    </div>
</template>
