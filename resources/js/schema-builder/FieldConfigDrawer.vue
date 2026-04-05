<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import SlideOverPanel from './SlideOverPanel.vue';
import type { DefinitionOption, FieldRow } from './types';

const props = defineProps<{
    field: FieldRow;
    definitions: DefinitionOption[];
    namespaces: string[];
}>();

const emit = defineEmits<{ close: [] }>();

function t(key: string): string {
    return trans(`messages.registry.versions.${key}`);
}

const inputClasses =
    'block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600';
const checkboxClasses =
    'size-4 cursor-pointer rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-600';

const hasTypeSettings = ['string', 'file'].includes(props.field.type);
const hasValidation = ['string', 'integer', 'number', 'date', 'repeater'].includes(props.field.type);
const hasPlaceholder = ['string', 'integer', 'number', 'date', 'email', 'reference'].includes(props.field.type);
const hasDefault = ['string', 'integer', 'number', 'date', 'boolean'].includes(props.field.type);

function typeLabel(): string {
    return trans(`messages.registry.versions.type_${props.field.type}`);
}
</script>

<template>
    <SlideOverPanel @close="emit('close')">
        <div class="flex h-full flex-col">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div class="flex items-center gap-2">
                    <h2 id="slide-over-title" class="text-base font-semibold text-gray-900">
                        {{ field.name || t('configure_field') }}
                    </h2>
                    <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                        {{ typeLabel() }}
                    </span>
                </div>
                <button
                    type="button"
                    class="cursor-pointer rounded-md p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                    :title="t('close')"
                    @click="emit('close')"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="flex-1 space-y-6 overflow-y-auto px-5 py-5">
                <!-- Type Settings -->
                <section v-if="hasTypeSettings">
                    <h3 class="mb-3 text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ t('section_type_settings') }}</h3>
                    <div class="space-y-3">
                        <!-- String: multiline -->
                        <label v-if="field.type === 'string'" class="flex cursor-pointer items-center gap-2">
                            <input v-model="field.multiline" type="checkbox" :class="checkboxClasses" />
                            <span class="text-sm text-gray-700">{{ t('multiline') }}</span>
                        </label>
                        <!-- File: MIME types -->
                        <div v-if="field.type === 'file'">
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('file_mime_types') }}</label>
                            <input v-model="field.fileMimeTypes" :class="inputClasses" type="text" placeholder="image/jpeg, application/pdf" />
                        </div>
                        <!-- File: max size -->
                        <div v-if="field.type === 'file'">
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('file_max_size') }}</label>
                            <input v-model="field.fileMaxSize" :class="inputClasses" type="number" min="0" placeholder="5242880" />
                        </div>
                    </div>
                </section>

                <!-- Validation -->
                <section v-if="hasValidation">
                    <h3 class="mb-3 text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ t('section_validation') }}</h3>
                    <div class="space-y-3">
                        <!-- String: minLength / maxLength -->
                        <template v-if="field.type === 'string'">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('min_length') }}</label>
                                    <input v-model="field.minLength" :class="inputClasses" type="number" min="0" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('max_length') }}</label>
                                    <input v-model="field.maxLength" :class="inputClasses" type="number" min="0" />
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('pattern') }}</label>
                                <input v-model="field.pattern" :class="inputClasses" type="text" placeholder="^[A-Z].*" />
                            </div>
                        </template>
                        <!-- Integer / Number: min / max / step -->
                        <template v-if="field.type === 'integer' || field.type === 'number'">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('min_value') }}</label>
                                    <input v-model="field.min" :class="inputClasses" type="number" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('max_value') }}</label>
                                    <input v-model="field.max" :class="inputClasses" type="number" />
                                </div>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('step') }}</label>
                                <input v-model="field.step" :class="inputClasses" type="number" min="0" />
                            </div>
                        </template>
                        <!-- Date: min / max -->
                        <template v-if="field.type === 'date'">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('min_date') }}</label>
                                    <input v-model="field.minDate" :class="inputClasses" type="date" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('max_date') }}</label>
                                    <input v-model="field.maxDate" :class="inputClasses" type="date" />
                                </div>
                            </div>
                        </template>
                        <!-- Repeater: minItems / maxItems -->
                        <template v-if="field.type === 'repeater'">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('min_items') }}</label>
                                    <input v-model="field.minItems" :class="inputClasses" type="number" min="0" placeholder="0" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('max_items') }}</label>
                                    <input v-model="field.maxItems" :class="inputClasses" type="number" min="0" />
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <!-- Presentation -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ t('section_presentation') }}</h3>
                    <div class="space-y-3">
                        <div v-if="hasPlaceholder">
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('placeholder') }}</label>
                            <input v-model="field.placeholder" :class="inputClasses" type="text" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('help_text') }}</label>
                            <textarea v-model="field.helpText" :class="inputClasses" rows="2" />
                        </div>
                        <!-- Default value -->
                        <div v-if="hasDefault">
                            <label class="mb-1 block text-sm font-medium text-gray-700">{{ t('default_value') }}</label>
                            <label v-if="field.type === 'boolean'" class="flex cursor-pointer items-center gap-2">
                                <input v-model="field.defaultValue" type="checkbox" :class="checkboxClasses" />
                                <span class="text-sm text-gray-600">{{ t('default_checked') }}</span>
                            </label>
                            <input
                                v-else-if="field.type === 'integer'"
                                v-model="field.defaultValue"
                                :class="inputClasses"
                                type="number"
                                step="1"
                            />
                            <input
                                v-else-if="field.type === 'number'"
                                v-model="field.defaultValue"
                                :class="inputClasses"
                                type="number"
                                step="any"
                            />
                            <input
                                v-else-if="field.type === 'date'"
                                v-model="field.defaultValue"
                                :class="inputClasses"
                                type="date"
                            />
                            <input
                                v-else
                                v-model="field.defaultValue"
                                :class="inputClasses"
                                type="text"
                            />
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </SlideOverPanel>
</template>
