<script setup lang="ts">
import Link from '@tiptap/extension-link';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import { trans } from 'laravel-vue-i18n';
import { onBeforeUnmount, ref, watch } from 'vue';
import type { TemplateVariable } from './VariablePanel.vue';
import VariablePanel from './VariablePanel.vue';
import { VariableChipExtension } from './variable-chip-extension';

const DEBOUNCE_MS = 600;
const SUBJECT_VARIABLE_PATTERN = /\{\{\s*\$(\w+)\s*\}\}/g;

function convertVariablesToChips(html: string): string {
    return html.replace(
        /(<[^>]*>)|(\{\{\s*\$(\w+)\s*\}\})/g,
        (_match: string, tag: string | undefined, _variable: string | undefined, name: string | undefined) => {
            if (tag) return tag;
            return `<span data-variable="${name}">{{ $${name} }}</span>`;
        },
    );
}

const props = defineProps<{
    subjectTemplate: string;
    bodyTemplate: string;
    variables: TemplateVariable[];
    previewUrl: string;
    templateType: string;
    locale: string;
}>();

const subject = ref(props.subjectTemplate);
const previewHtml = ref('');
const isLoadingPreview = ref(false);
const showSubjectVariables = ref(false);
const showLinkInput = ref(false);
const linkUrl = ref('');
const showSource = ref(false);
const sourceHtml = ref('');
let previewTimer: ReturnType<typeof setTimeout> | null = null;

const editor = useEditor({
    content: convertVariablesToChips(props.bodyTemplate),
    extensions: [StarterKit, Link.configure({ openOnClick: false, autolink: false }), VariableChipExtension],
});

function getBodyHtml(): string {
    if (showSource.value) return sourceHtml.value;
    return editor.value?.getHTML() ?? '';
}

function syncHiddenFields(): void {
    const subjectInput = document.querySelector<HTMLInputElement>('input[name="subject_template"]');
    const bodyInput = document.querySelector<HTMLInputElement>('input[name="body_template"]');
    if (subjectInput) subjectInput.value = subject.value;
    if (bodyInput) bodyInput.value = getBodyHtml();
}

function insertVariableInSubject(name: string): void {
    subject.value += `{{ $${name} }}`;
    showSubjectVariables.value = false;
}

function insertVariableInBody(name: string): void {
    if (showSource.value) {
        sourceHtml.value += `{{ $${name} }}`;
        syncHiddenFields();
        schedulePreview();
    } else {
        editor.value?.chain().focus().insertVariable(name).run();
    }
}

function fetchHeaders(): Record<string, string> {
    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
    if (csrfMeta) headers['X-CSRF-TOKEN'] = csrfMeta.content;
    return headers;
}

async function loadPreview(): Promise<void> {
    if (!props.previewUrl) return;

    isLoadingPreview.value = true;

    try {
        const response = await fetch(props.previewUrl, {
            method: 'POST',
            headers: fetchHeaders(),
            body: JSON.stringify({
                templateType: props.templateType,
                locale: props.locale,
                subjectTemplate: subject.value,
                bodyTemplate: getBodyHtml(),
            }),
        });

        if (response.ok) {
            const data = await response.json();
            previewHtml.value = data.html ?? '';
        }
    } finally {
        isLoadingPreview.value = false;
    }
}

function schedulePreview(): void {
    if (previewTimer) clearTimeout(previewTimer);
    previewTimer = setTimeout(() => {
        void loadPreview();
    }, DEBOUNCE_MS);
}

function highlightSubjectVariables(text: string): string {
    return text.replace(
        SUBJECT_VARIABLE_PATTERN,
        '<span class="inline-flex items-center rounded-md bg-indigo-50 px-1 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">$&</span>',
    );
}

watch(subject, () => {
    syncHiddenFields();
    schedulePreview();
});

watch(
    () => editor.value?.getHTML(),
    () => {
        syncHiddenFields();
        schedulePreview();
    },
);

onBeforeUnmount(() => {
    if (previewTimer) clearTimeout(previewTimer);
});

function toggleBold(): void {
    editor.value?.chain().focus().toggleBold().run();
}

function toggleItalic(): void {
    editor.value?.chain().focus().toggleItalic().run();
}

function toggleHeading(): void {
    editor.value?.chain().focus().toggleHeading({ level: 3 }).run();
}

function toggleBulletList(): void {
    editor.value?.chain().focus().toggleBulletList().run();
}

function toggleOrderedList(): void {
    editor.value?.chain().focus().toggleOrderedList().run();
}

function toggleLinkInput(): void {
    showLinkInput.value = !showLinkInput.value;
    if (showLinkInput.value && editor.value?.isActive('link')) {
        linkUrl.value = editor.value.getAttributes('link').href ?? '';
    } else if (!showLinkInput.value) {
        linkUrl.value = '';
    }
}

function applyLink(): void {
    if (linkUrl.value) {
        editor.value?.chain().focus().setLink({ href: linkUrl.value }).run();
    }
    showLinkInput.value = false;
    linkUrl.value = '';
}

function removeLink(): void {
    editor.value?.chain().focus().unsetLink().run();
    showLinkInput.value = false;
    linkUrl.value = '';
}

function toggleSource(): void {
    if (showSource.value) {
        editor.value?.commands.setContent(convertVariablesToChips(sourceHtml.value));
    } else {
        sourceHtml.value = getBodyHtml();
    }
    showSource.value = !showSource.value;
    syncHiddenFields();
    schedulePreview();
}

function isActive(name: string, attrs?: Record<string, unknown>): boolean {
    return editor.value?.isActive(name, attrs) ?? false;
}
</script>

<template>
    <div
        class="grid grid-cols-1 gap-6 lg:grid-cols-2"
        data-testid="email-template-editor"
    >
      <div class="space-y-4">
        <!-- Subject -->
        <div>
            <label
                for="subject-input"
                class="mb-1 block text-sm font-medium text-gray-700"
            >
                {{ trans('messages.email_templates.subject') }}
            </label>
            <div class="relative">
                <input
                    id="subject-input"
                    v-model="subject"
                    type="text"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 pr-10 text-sm shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600"
                    data-testid="subject-input"
                />
                <button
                    type="button"
                    class="absolute inset-y-0 right-0 flex cursor-pointer items-center px-3 text-gray-400 hover:text-indigo-600"
                    title="Insert variable"
                    data-testid="subject-variable-toggle"
                    @click="showSubjectVariables = !showSubjectVariables"
                >
                    <svg
                        class="size-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"
                        />
                    </svg>
                </button>
            </div>
            <div
                v-if="showSubjectVariables"
                class="mt-2"
            >
                <div class="flex flex-wrap gap-1.5">
                    <button
                        v-for="variable in variables"
                        :key="variable.name"
                        type="button"
                        class="cursor-pointer rounded-md bg-indigo-50 px-2 py-0.5 font-mono text-xs text-indigo-700 ring-1 ring-inset ring-indigo-700/10 transition-colors hover:bg-indigo-100"
                        :data-testid="`subject-variable-${variable.name}`"
                        @click="insertVariableInSubject(variable.name)"
                    >
                        {{ `$${variable.name}` }}
                    </button>
                </div>
            </div>
            <p
                v-if="subject"
                class="mt-1 text-xs text-gray-400"
                v-html="highlightSubjectVariables(subject)"
            />
        </div>

        <!-- Body editor -->
        <div>
            <div class="mb-1 flex items-center justify-between">
                <label class="block text-sm font-medium text-gray-700">
                    {{ trans('messages.email_templates.body') }}
                </label>
                <div class="flex gap-1">
                    <button
                        type="button"
                        class="cursor-pointer rounded px-2 py-0.5 text-xs font-medium transition-colors"
                        :class="!showSource ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'"
                        data-testid="view-visual"
                        @click="showSource && toggleSource()"
                    >
                        {{ trans('messages.email_templates.visual_view') }}
                    </button>
                    <button
                        type="button"
                        class="cursor-pointer rounded px-2 py-0.5 text-xs font-medium transition-colors"
                        :class="showSource ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700'"
                        data-testid="view-source"
                        @click="!showSource && toggleSource()"
                    >
                        {{ trans('messages.email_templates.source_view') }}
                    </button>
                </div>
            </div>

            <!-- Visual mode -->
            <template v-if="!showSource">
                <!-- Toolbar -->
                <div
                    class="flex items-center gap-0.5 rounded-t-lg border border-b-0 border-gray-300 bg-gray-50 px-2 py-1"
                    data-testid="editor-toolbar"
                >
                    <button
                        type="button"
                        class="cursor-pointer rounded p-1.5 text-sm transition-colors"
                        :class="isActive('bold') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700'"
                        title="Bold"
                        data-testid="toolbar-bold"
                        @click="toggleBold"
                    >
                        <svg
                            class="size-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 4h8a4 4 0 0 1 0 8H6V4Zm0 8h9a4 4 0 0 1 0 8H6v-8Z"
                            />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="cursor-pointer rounded p-1.5 text-sm transition-colors"
                        :class="isActive('italic') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700'"
                        title="Italic"
                        data-testid="toolbar-italic"
                        @click="toggleItalic"
                    >
                        <svg
                            class="size-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M10 4h4m-2 0v16m-4 0h4"
                                transform="skewX(-10)"
                            />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="cursor-pointer rounded p-1.5 text-sm transition-colors"
                        :class="isActive('heading', { level: 3 }) ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700'"
                        title="Heading"
                        data-testid="toolbar-heading"
                        @click="toggleHeading"
                    >
                        <span
                            class="text-xs font-bold"
                            aria-hidden="true"
                        >H</span>
                    </button>

                    <div class="mx-1 h-5 w-px bg-gray-300" />

                    <button
                        type="button"
                        class="cursor-pointer rounded p-1.5 text-sm transition-colors"
                        :class="isActive('link') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700'"
                        title="Link"
                        data-testid="toolbar-link"
                        @click="toggleLinkInput"
                    >
                        <svg
                            class="size-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"
                            />
                        </svg>
                    </button>
                    <div
                        v-if="showLinkInput"
                        class="flex items-center gap-1"
                    >
                        <input
                            v-model="linkUrl"
                            type="text"
                            placeholder="URL"
                            class="h-7 w-40 rounded border border-gray-300 px-2 text-xs"
                            data-testid="link-url-input"
                            @keydown.enter="applyLink"
                        />
                        <button
                            type="button"
                            class="cursor-pointer rounded bg-indigo-600 px-2 py-0.5 text-xs text-white hover:bg-indigo-700"
                            data-testid="link-apply-button"
                            @click="applyLink"
                        >
                            OK
                        </button>
                        <button
                            v-if="isActive('link')"
                            type="button"
                            class="cursor-pointer rounded px-2 py-0.5 text-xs text-red-600 hover:bg-red-50 hover:text-red-700"
                            data-testid="link-unlink-button"
                            @click="removeLink"
                        >
                            {{ trans('messages.email_templates.unlink') }}
                        </button>
                    </div>

                    <div class="mx-1 h-5 w-px bg-gray-300" />

                    <button
                        type="button"
                        class="cursor-pointer rounded p-1.5 text-sm transition-colors"
                        :class="isActive('bulletList') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700'"
                        title="Bullet list"
                        data-testid="toolbar-bullet-list"
                        @click="toggleBulletList"
                    >
                        <svg
                            class="size-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                            />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="cursor-pointer rounded p-1.5 text-sm transition-colors"
                        :class="isActive('orderedList') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700'"
                        title="Numbered list"
                        data-testid="toolbar-ordered-list"
                        @click="toggleOrderedList"
                    >
                        <svg
                            class="size-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75V5.25l1.5-.75M3.75 6.75h1.5m-1.5 0v1.5m0 3v-1.5m0 1.5h1.5m-1.5 0v1.5m0 3v-1.5m0 1.5h1.5m-1.5 0v1.5"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Editor content -->
                <div class="rounded-b-lg border border-gray-300">
                    <EditorContent
                        :editor="editor"
                        class="max-w-none px-3 py-2 focus-within:ring-2 focus-within:ring-indigo-600 [&_.ProseMirror]:min-h-[160px] [&_.ProseMirror]:outline-none"
                        data-testid="editor-content"
                    />
                </div>
            </template>

            <!-- Source mode -->
            <div
                v-else
                class="rounded-lg border border-gray-300"
            >
                <textarea
                    v-model="sourceHtml"
                    class="block w-full rounded-lg border-0 bg-gray-50 px-3 py-2 font-mono text-sm text-gray-800 outline-none focus:ring-2 focus:ring-indigo-600"
                    style="min-height: 200px"
                    data-testid="source-textarea"
                    @input="syncHiddenFields(); schedulePreview()"
                />
            </div>
        </div>

        <!-- Variable panel -->
        <VariablePanel
            :variables="variables"
            @insert="insertVariableInBody"
        />

      </div>

        <!-- Preview (right column) -->
        <div
            v-if="previewUrl"
            class="lg:sticky lg:top-4 lg:self-start"
            data-testid="preview-section"
        >
            <label class="mb-1 block text-sm font-medium text-gray-700">
                {{ trans('messages.email_templates.preview') }}
            </label>
            <div class="relative rounded-lg border border-gray-200 bg-white">
                <div
                    v-if="isLoadingPreview || !previewHtml"
                    class="absolute inset-0 z-10 flex items-center justify-center rounded-lg bg-white/70"
                >
                    <svg
                        class="size-6 animate-spin text-indigo-600"
                        fill="none"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        />
                    </svg>
                </div>
                <iframe
                    v-if="previewHtml"
                    :srcdoc="previewHtml"
                    class="h-[600px] w-full rounded-lg"
                    sandbox="allow-same-origin"
                    title="Email template preview"
                    data-testid="preview-iframe"
                />
                <div
                    v-else
                    class="flex h-[600px] items-center justify-center"
                >
                    <p class="text-sm text-gray-400">
                        Start typing to see a preview
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
