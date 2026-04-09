@extends('layouts.app')

@section('title', __('messages.email_templates.edit'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.settings'), 'href' => route('settings.index')],
        ['label' => __('messages.email_templates.title'), 'href' => route('settings.email-templates.index')],
        ['label' => $typeConfig['name'] . ' (' . strtoupper($locale) . ')'],
    ]" />

    <div class="max-w-4xl">
        <x-form-card :title="$typeConfig['name']"
                     :subtitle="$typeConfig['description']">
            <form id="email-template-form"
                  method="POST"
                  action="{{ route('settings.email-templates.update', ['type' => $type, 'locale' => $locale]) }}"
                  data-testid="email-template-form">
                @csrf
                @method('PUT')

                <input id="subject_template"
                       name="subject_template"
                       type="hidden"
                       value="{{ old('subject_template', $template->subjectTemplate) }}"
                       data-testid="subject-template-input">
                <input id="body_template"
                       name="body_template"
                       type="hidden"
                       value="{{ old('body_template', $template->bodyTemplate) }}"
                       data-testid="body-template-input">

                @error('subject_template')
                    <p class="mb-4 text-base text-red-600 sm:text-sm"
                       id="subject-template-error"
                       data-testid="subject-template-error">{{ $message }}</p>
                @enderror
                @error('body_template')
                    <p class="mb-4 text-base text-red-600 sm:text-sm"
                       id="body-template-error"
                       data-testid="body-template-error">{{ $message }}</p>
                @enderror

                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div>
                        <span class="mb-1 block text-xs font-medium text-gray-500">
                            {{ __('messages.email_templates.type_label') }}
                        </span>
                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ $type }}</code>
                    </div>
                    <div>
                        <span class="mb-1 block text-xs font-medium text-gray-500">
                            {{ __('messages.email_templates.locale_label') }}
                        </span>
                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-300/50">
                            {{ strtoupper($locale) }}
                        </span>
                    </div>
                </div>

                <div class="mb-6">
                    <span class="mb-2 block text-xs font-medium text-gray-500">
                        {{ __('messages.email_templates.available_variables') }}
                    </span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($typeConfig['variables'] as $varName => $varConfig)
                            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10"
                                  title="{{ $varConfig['description'] }}"
                                  data-testid="variable-chip-{{ $varName }}">
                                {{ '{{ ' . $varName . ' }}' }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div data-email-template-editor
                     data-subject-template="{{ old('subject_template', $template->subjectTemplate) }}"
                     data-body-template="{{ old('body_template', $template->bodyTemplate) }}"
                     data-variables="{{ json_encode($typeConfig['variables']) }}"
                     data-type="{{ $type }}"
                     data-locale="{{ $locale }}"
                     data-preview-url="{{ route('settings.email-templates.preview') }}"
                     data-testid="email-template-editor">
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <x-primary-button skip-permission
                                      :label="__('messages.email_templates.save')"
                                      data-testid="save-template-button" />
                    <x-primary-button skip-permission
                                      variant="secondary"
                                      :href="route('settings.email-templates.index')"
                                      :label="__('messages.email_templates.cancel')"
                                      data-testid="cancel-template-button" />
                    <x-action-button skip-permission
                                     :action="route('settings.email-templates.reset', ['type' => $type, 'locale' => $locale])"
                                     method="POST"
                                     icon="heroicon-o-arrow-uturn-left"
                                     :label="__('messages.email_templates.reset_to_default')"
                                     confirm
                                     :confirm-title="__('messages.email_templates.reset_confirm_title')"
                                     :confirm-message="__('messages.email_templates.reset_confirm_message', ['type' => $typeConfig['name']])"
                                     data-testid="reset-template-button" />
                </div>
            </form>
        </x-form-card>
    </div>
@endsection
