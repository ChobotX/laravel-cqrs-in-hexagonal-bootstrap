import { type ExecSyncOptions, execSync } from 'node:child_process';
import { existsSync } from 'node:fs';

const defaults: ExecSyncOptions = { stdio: 'inherit', timeout: 30_000 };

/**
 * Run a shell command the same way Playwright expects on the host (`./vendor/bin/sail …`)
 * or inside the Laravel app container (`php artisan …` with no nested Sail/Docker).
 */
export function execInLaravelApp(command: string, options: ExecSyncOptions = {}): void {
    const merged: ExecSyncOptions = { ...defaults, ...options };
    if (existsSync('/.dockerenv')) {
        execSync(command, merged);
    } else {
        execSync(`./vendor/bin/sail ${command}`, merged);
    }
}
