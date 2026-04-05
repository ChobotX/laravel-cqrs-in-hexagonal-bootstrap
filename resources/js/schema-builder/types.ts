export interface FieldRow {
    name: string;
    label: string;
    type: string;
    required: boolean;
    // String
    multiline: boolean;
    minLength: string;
    maxLength: string;
    pattern: string;
    // Integer / Number
    min: string;
    max: string;
    step: string;
    // Date
    minDate: string;
    maxDate: string;
    // Reference
    referenceNamespace: string;
    referenceSlug: string;
    // File
    fileNamespace: string;
    fileMimeTypes: string;
    fileMaxSize: string;
    // Repeater
    minItems: string;
    maxItems: string;
    subFields: FieldRow[];
    // Common presentation
    placeholder: string;
    helpText: string;
    defaultValue: string | boolean;
}

export interface DefinitionOption {
    namespace: string;
    slug: string;
    name: string;
}
