function showBackupDownloadedAck(card: HTMLElement | null): void {
    if (card === null) {
        return;
    }

    for (const el of card.querySelectorAll<HTMLElement>('[data-own-two-factor-totp-pending-hint]')) {
        el.classList.add('hidden');
    }

    for (const el of card.querySelectorAll<HTMLElement>('[data-own-two-factor-totp-downloaded-ack]')) {
        el.classList.remove('hidden');
    }
}

export function bindOwnTwoFactorTotp(root: Document): void {
    const download = root.querySelector<HTMLAnchorElement>('[data-own-two-factor-totp-backup-download]');
    const panel = root.querySelector<HTMLElement>('[data-own-two-factor-totp-confirm-panel]');

    if (download === null || panel === null) {
        return;
    }

    if (panel.dataset.ownTwoFactorTotpConfirmVisible === '1') {
        return;
    }

    download.addEventListener('click', (event: MouseEvent): void => {
        event.preventDefault();
        const url = download.getAttribute('href');
        if (url === null || url === '') {
            return;
        }

        void (async (): Promise<void> => {
            try {
                const downloadUrl = new URL(url, window.location.origin);
                downloadUrl.searchParams.set('_download', String(Date.now()));
                const response = await fetch(downloadUrl.toString(), {
                    credentials: 'include',
                    cache: 'no-store',
                });
                if (!response.ok) {
                    return;
                }

                const blob = await response.blob();
                const objectUrl = URL.createObjectURL(blob);
                const anchor = document.createElement('a');
                anchor.href = objectUrl;
                anchor.download = 'totp-backup-codes.txt';
                anchor.rel = 'noopener';
                document.body.append(anchor);
                anchor.click();
                anchor.remove();
                URL.revokeObjectURL(objectUrl);

                panel.classList.remove('hidden');
                panel.classList.add('flex', 'flex-col', 'gap-5');
                const card = download.closest<HTMLElement>('[data-own-two-factor-totp-setup-card]');
                showBackupDownloadedAck(card);
            } catch {
                /* @silent — fetch/network failure; user can retry the download link */
            }
        })();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', (): void => {
        bindOwnTwoFactorTotp(document);
    });
} else {
    bindOwnTwoFactorTotp(document);
}
