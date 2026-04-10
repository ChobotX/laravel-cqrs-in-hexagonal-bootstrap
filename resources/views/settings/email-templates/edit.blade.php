@extends('layouts.app')

@section('title', __('messages.email_templates.edit'))

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('messages.nav.dashboard'), 'href' => route('dashboard')],
        ['label' => __('messages.nav.settings'), 'href' => route('settings.index')],
        ['label' => __('messages.email_templates.title'), 'href' => route('settings.email-templates.index')],
        ['label' => __("messages.email_templates.types.{$type}.name")],
    ]" />

    <div>
        <div class="mb-4 border-b border-gray-200">
            <nav class="-mb-px flex gap-4" aria-label="{{ __('messages.email_templates.locale_label') }}" data-testid="locale-tabs">
                @foreach (config('app.locales') as $availableLocale)
                    <a href="{{ route('settings.email-templates.edit', ['type' => $type, 'locale' => $availableLocale]) }}"
                       @class([
                           'cursor-pointer whitespace-nowrap border-b-2 px-1 py-3 text-sm font-medium',
                           'border-indigo-500 text-indigo-600' => $availableLocale === $locale,
                           'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => $availableLocale !== $locale,
                       ])
                       data-testid="locale-tab-{{ $availableLocale }}"
                       title="{{ __('messages.email_templates.switch_locale', ['locale' => strtoupper($availableLocale)]) }}"
                       @if($availableLocale === $locale) aria-current="page" @endif>
                        {{ strtoupper($availableLocale) }}
                    </a>
                @endforeach
            </nav>
        </div>

        <x-form-card :title="__('messages.email_templates.types.' . $type . '.name')"
                     :subtitle="__('messages.email_templates.types.' . $type . '.description')">
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
                                     :confirm-message="__('messages.email_templates.reset_confirm_message', ['type' => __('messages.email_templates.types.' . $type . '.name')])"
                                     data-testid="reset-template-button" />
                </div>
            </form>
        </x-form-card>
    </div>
@endsection
