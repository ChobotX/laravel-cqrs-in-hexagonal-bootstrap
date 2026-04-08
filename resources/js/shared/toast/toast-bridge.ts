export {};

const flashElement: HTMLElement | null = document.getElementById('app-flash-data');
if (flashElement) {
    const successMessage: string | null = flashElement.getAttribute('data-success');
    const errorMessage: string | null = flashElement.getAttribute('data-error');

    if (successMessage) {
        window.appToast.success(successMessage);
    }
    if (errorMessage) {
        window.appToast.error(errorMessage);
    }
}
