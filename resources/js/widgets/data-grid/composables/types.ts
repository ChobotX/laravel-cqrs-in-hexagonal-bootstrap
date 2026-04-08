import type { Ref } from 'vue';

export interface ColumnDef {
    key: string;
    title: string;
    sortable?: boolean;
}

export interface SortItem {
    key: string;
    order: 'asc' | 'desc';
}

export interface FetchParams {
    page: number;
    perPage: number;
    sort: SortItem | null;
    filters: Record<string, string>;
    search: string;
}

export interface FetchResult<T> {
    data: T[];
    meta: {
        current_page: number;
        per_page: number;
        total: number;
        total_pages: number;
    };
}

export interface Preset {
    id: string;
    name: string;
    filters: string;
    sorting: string;
    search: string;
    is_default: boolean;
    position: number;
    scope: 'personal' | 'team' | 'global';
    team_id: string | null;
    is_owner: boolean;
}

export interface ShareableTeam {
    id: string;
    name: string;
}

export interface DataGridOptions<T> {
    fetchUrl: string;
    gridName: string;
    columns: Ref<ColumnDef[]>;
    defaultSort?: SortItem;
    defaultPerPage?: number;
    fetchFn?: (url: string, params: FetchParams) => Promise<FetchResult<T>>;
}
