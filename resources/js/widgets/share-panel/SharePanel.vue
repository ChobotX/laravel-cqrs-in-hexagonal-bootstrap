<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { computed, onMounted, ref } from 'vue';
import { error as showErrorToast, success as showSuccessToast } from '../../shared/toast/toast-queue';
import { isRecord } from '../../shared/type-guards/is-record';

interface ShareEntry {
    grantee_user_id: string;
    grantee_name: string | null;
    grantee_email: string | null;
    grantor_user_id: string;
}

interface UserOption {
    id: string;
    name: string;
    email?: string;
}

function isUserOption(value: unknown): value is UserOption {
    if (!isRecord(value) || typeof value['id'] !== 'string' || typeof value['name'] !== 'string') {
        return false;
    }
    const email = value['email'];
    return email === undefined || typeof email === 'string';
}

function buildUserSearchQueryUrl(searchUrl: string, term: string, excludeIds: string[]): string {
    const params = new URLSearchParams();
    if (term !== '') {
        params.set('q', term);
    }
    for (const id of excludeIds) {
        params.append('exclude[]', id);
    }
    const separator = searchUrl.includes('?') ? '&' : '?';
    return `${searchUrl}${separator}${params.toString()}`;
}

function parseUserSearchBody(body: unknown): UserOption[] {
    if (!isRecord(body) || !Array.isArray(body['data'])) {
        return [];
    }
    return body['data'].filter(isUserOption);
}

const props = defineProps<{
    resourceType: string;
    resourceId: string;
    sharesUrl: string;
    searchUrl: string;
}>();

const shares = ref<ShareEntry[]>([]);
const searchTerm = ref('');
const searchResults = ref<UserOption[]>([]);
const isLoadingShares = ref(true);
const isSearching = ref(false);
const isSubmitting = ref(false);

const SEARCH_DEBOUNCE_MS = 250;

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const excludedIds = computed(() => shares.value.map((s) => s.grantee_user_id));

function csrfHeaders(): Record<string, string> {
    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
    if (csrfMeta) {
        headers['X-CSRF-TOKEN'] = csrfMeta.content;
    }
    return headers;
}

async function loadShares(): Promise<void> {
    isLoadingShares.value = true;
    try {
        const response = await fetch(props.sharesUrl, { headers: csrfHeaders() });
        if (!response.ok) {
            throw new Error(`Failed: ${response.status}`);
        }
        const json = await response.json();
        shares.value = json.data ?? [];
    } catch {
        showErrorToast(trans('messages.sharing.load_failed'));
    } finally {
        isLoadingShares.value = false;
    }
}

async function searchUsers(term: string): Promise<void> {
    isSearching.value = true;
    try {
        const url = buildUserSearchQueryUrl(props.searchUrl, term, excludedIds.value);
        const response = await fetch(url, { headers: csrfHeaders() });
        if (!response.ok) {
            throw new Error(`Failed: ${response.status}`);
        }
        searchResults.value = parseUserSearchBody(await response.json());
    } catch {
        searchResults.value = [];
        showErrorToast(trans('messages.sharing.search_failed'));
    } finally {
        isSearching.value = false;
    }
}

function onSearchInput(event: Event): void {
    const t = event.target;
    if (!(t instanceof HTMLInputElement)) {
        return;
    }
    searchTerm.value = t.value;
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
    debounceTimer = setTimeout(() => {
        void searchUsers(searchTerm.value);
    }, SEARCH_DEBOUNCE_MS);
}

async function shareWith(user: UserOption): Promise<void> {
    if (isSubmitting.value) {
        return;
    }
    isSubmitting.value = true;
    try {
        const response = await fetch(props.sharesUrl, {
            method: 'POST',
            headers: csrfHeaders(),
            body: JSON.stringify({ grantee_user_id: user.id }),
        });
        if (!response.ok) {
            throw new Error(`Failed: ${response.status}`);
        }
        showSuccessToast(trans('messages.sharing.shared_success'));
        searchTerm.value = '';
        searchResults.value = [];
        await loadShares();
    } catch {
        showErrorToast(trans('messages.sharing.share_failed'));
    } finally {
        isSubmitting.value = false;
    }
}

async function revoke(entry: ShareEntry): Promise<void> {
    if (isSubmitting.value) {
        return;
    }
    const confirmed = await window.appDialog.confirm({
        title: trans('messages.sharing.revoke'),
        message: trans('messages.sharing.revoke_confirm', { name: entry.grantee_name ?? entry.grantee_user_id }),
    });
    if (!confirmed) {
        return;
    }
    isSubmitting.value = true;
    try {
        const response = await fetch(`${props.sharesUrl}/${entry.grantee_user_id}`, {
            method: 'DELETE',
            headers: csrfHeaders(),
        });
        if (!response.ok) {
            throw new Error(`Failed: ${response.status}`);
        }
        showSuccessToast(trans('messages.sharing.revoked_success'));
        await loadShares();
    } catch {
        showErrorToast(trans('messages.sharing.revoke_failed'));
    } finally {
        isSubmitting.value = false;
    }
}

onMounted(() => {
    void loadShares();
});
</script>

<template>
    <section class="space-y-4">
        <header>
            <h3 class="text-base font-medium text-gray-900 sm:text-sm">{{ trans('messages.sharing.title') }}</h3>
            <p class="text-xs text-gray-400">{{ trans('messages.sharing.subtitle') }}</p>
        </header>

        <div v-if="isLoadingShares" class="text-xs text-gray-400" aria-live="polite">
            {{ trans('messages.sharing.loading') }}
        </div>

        <ul v-else-if="shares.length > 0" class="divide-y divide-gray-100 rounded-lg border border-gray-200">
            <li
                v-for="entry in shares"
                :key="entry.grantee_user_id"
                class="flex items-center justify-between px-3 py-2"
                data-testid="share-panel-entry"
            >
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-gray-900">{{ entry.grantee_name ?? entry.grantee_user_id }}</p>
                    <p v-if="entry.grantee_email" class="truncate text-xs text-gray-500">{{ entry.grantee_email }}</p>
                </div>
                <button
                    type="button"
                    class="cursor-pointer rounded-md px-2 py-1 text-xs font-medium text-red-600 hover:bg-red-50"
                    :disabled="isSubmitting"
                    data-testid="share-panel-revoke"
                    @click="revoke(entry)"
                >
                    {{ trans('messages.sharing.revoke') }}
                </button>
            </li>
        </ul>

        <p v-else class="text-xs text-gray-500">{{ trans('messages.sharing.no_shares') }}</p>

        <div class="relative">
            <input
                type="text"
                :placeholder="trans('messages.sharing.search_placeholder')"
                class="w-full rounded-lg border border-gray-300 px-3.5 py-2 text-sm shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600"
                :value="searchTerm"
                data-testid="share-panel-search"
                @input="onSearchInput"
            />
            <ul
                v-if="searchResults.length > 0"
                class="absolute z-10 mt-1 max-h-64 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg"
            >
                <li
                    v-for="user in searchResults"
                    :key="user.id"
                    class="cursor-pointer px-3 py-2 text-sm hover:bg-indigo-50"
                    data-testid="share-panel-candidate"
                    @click="shareWith(user)"
                >
                    <p class="truncate font-medium text-gray-900">{{ user.name }}</p>
                    <p v-if="user.email" class="truncate text-xs text-gray-500">{{ user.email }}</p>
                </li>
            </ul>
            <span v-if="isSearching" class="absolute right-3 top-3 text-xs text-gray-400" aria-live="polite">...</span>
        </div>
    </section>
</template>
