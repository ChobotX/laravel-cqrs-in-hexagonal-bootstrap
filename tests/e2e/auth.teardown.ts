import { rmSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { test as teardown } from '@playwright/test';

const authDir = join(dirname(fileURLToPath(import.meta.url)), '.auth');

teardown('remove stored auth state', () => {
    rmSync(authDir, { recursive: true, force: true });
});
