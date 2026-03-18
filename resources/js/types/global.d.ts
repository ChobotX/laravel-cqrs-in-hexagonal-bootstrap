declare module '*.vue' {
    import type { DefineComponent } from 'vue';
    const component: DefineComponent;
    export default component;
}

interface AppDialog {
    confirm(options: { title: string; message: string }): Promise<boolean>;
    alert(options: { title: string; message: string }): Promise<void>;
}

interface AppToast {
    success(message: string): void;
    error(message: string): void;
}

interface Window {
    appDialog: AppDialog;
    appToast: AppToast;
}
