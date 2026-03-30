<script setup lang="ts">
import { OrgChart } from 'd3-org-chart';
import { nextTick, onMounted, onUnmounted, ref } from 'vue';

interface MemberRole {
    id: string;
    name: string;
    detailUrl: string;
}

interface TeamMember {
    id: string;
    name: string;
    detailUrl: string;
    roles: MemberRole[];
}

interface TeamNode {
    id: string;
    parentId: string;
    name: string;
    slug: string;
    memberCount: number;
    members: TeamMember[];
}

const MAX_COLLAPSED_MEMBERS = 5;
const NODE_WIDTH = 280;
const NODE_HEADER_HEIGHT = 44;
const NODE_MEMBER_ROW_HEIGHT = 28;
const NODE_TOGGLE_HEIGHT = 32;
const NODE_PADDING = 16;
const CHART_CHILDREN_MARGIN = 60;
const CHART_SIBLINGS_MARGIN = 30;
const CHART_NEIGHBOUR_MARGIN = 80;
const CHART_COMPACT_MARGIN = 25;
const CHART_MIN_HEIGHT = 700;

const props = defineProps<{
    fetchUrl: string;
}>();

const containerRef = ref<HTMLDivElement | null>(null);
const isLoading = ref(true);
const errorMessage = ref('');
const expandedTeams = ref(new Set<string>());
const treeData = ref<TeamNode[]>([]);

let chart: OrgChart<TeamNode> | null = null;
let parentEl: HTMLElement | null = null;

function escapeHtml(text: string): string {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderNodeContent(node: { data: TeamNode }): string {
    const team = node.data;
    const isExpanded = expandedTeams.value.has(team.id);
    const visibleMembers = isExpanded ? team.members : team.members.slice(0, MAX_COLLAPSED_MEMBERS);
    const hasMore = team.members.length > MAX_COLLAPSED_MEMBERS;

    const memberRows = visibleMembers
        .map((member) => {
            const roleBadges = member.roles
                .map(
                    (role) =>
                        `<a href="${role.detailUrl}" data-testid="role-link-${role.id}" class="org-role-badge" onclick="event.stopPropagation()">${escapeHtml(role.name)}</a>`,
                )
                .join(' ');

            return `<div class="org-member-row">
                <a href="${member.detailUrl}" data-testid="member-link-${member.id}" class="org-member-name" onclick="event.stopPropagation()">${escapeHtml(member.name)}</a>
                <span class="org-role-list">${roleBadges}</span>
            </div>`;
        })
        .join('');

    const toggleButton = hasMore
        ? `<button type="button" data-testid="toggle-members-${team.id}" class="org-toggle-btn" data-action="toggle-members" data-team-id="${team.id}">
            ${isExpanded ? `Show less` : `Show all ${team.memberCount} members`}
        </button>`
        : '';

    return `<div class="org-node" data-testid="tree-node-${team.id}">
        <div class="org-node-header">
            <span class="org-node-name">${escapeHtml(team.name)}</span>
            <span class="org-node-count">${team.memberCount} ${team.memberCount === 1 ? 'member' : 'members'}</span>
        </div>
        <div class="org-node-body">
            ${memberRows}
            ${toggleButton}
        </div>
    </div>`;
}

function calculateNodeHeight(node: { data: TeamNode }): number {
    const team = node.data;
    const isExpanded = expandedTeams.value.has(team.id);
    const visibleCount = isExpanded ? team.members.length : Math.min(team.members.length, MAX_COLLAPSED_MEMBERS);
    const hasToggle = team.members.length > MAX_COLLAPSED_MEMBERS;

    const toggleHeight = hasToggle ? NODE_TOGGLE_HEIGHT : 0;

    return NODE_HEADER_HEIGHT + visibleCount * NODE_MEMBER_ROW_HEIGHT + toggleHeight + NODE_PADDING;
}

function initChart(): void {
    if (!containerRef.value || treeData.value.length === 0) {
        return;
    }

    const containerWidth = containerRef.value.clientWidth;
    const containerHeight = Math.max(containerRef.value.clientHeight, CHART_MIN_HEIGHT);

    chart = new OrgChart<TeamNode>()
        .container(containerRef.value)
        .data(treeData.value)
        .svgWidth(containerWidth)
        .svgHeight(containerHeight)
        .nodeId((d) => d.id)
        .parentNodeId((d) => d.parentId)
        .nodeWidth(() => NODE_WIDTH)
        .nodeHeight((node) => calculateNodeHeight(node))
        .childrenMargin(() => CHART_CHILDREN_MARGIN)
        .siblingsMargin(() => CHART_SIBLINGS_MARGIN)
        .neighbourMargin(() => CHART_NEIGHBOUR_MARGIN)
        .compactMarginBetween(() => CHART_COMPACT_MARGIN)
        .nodeContent((node) => renderNodeContent(node))
        .onNodeClick(() => {
            // node click handled via links inside nodeContent
        })
        .render();
}

function toggleTeamMembers(teamId: string): void {
    if (expandedTeams.value.has(teamId)) {
        expandedTeams.value.delete(teamId);
    } else {
        expandedTeams.value.add(teamId);
    }

    if (chart) {
        chart.render();
    }
}

function handleClick(event: MouseEvent): void {
    const target = (event.target as HTMLElement).closest<HTMLElement>('[data-action="toggle-members"]');

    if (target?.dataset.teamId) {
        toggleTeamMembers(target.dataset.teamId);
    }
}

async function fetchData(): Promise<void> {
    try {
        const response = await fetch(props.fetchUrl, {
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const json = (await response.json()) as { data: TeamNode[] };
        treeData.value = json.data;
        isLoading.value = false;
    } catch (error) {
        isLoading.value = false;
        errorMessage.value = error instanceof Error ? error.message : 'Failed to load team tree';
    }
}

let visible = false;

function onTreeShown(): void {
    visible = true;
    tryInitChart();
}

function tryInitChart(): void {
    if (!chart && visible && treeData.value.length > 0) {
        void nextTick(() => {
            requestAnimationFrame(() => initChart());
        });
    }
}

onMounted(async () => {
    containerRef.value?.addEventListener('click', handleClick);

    parentEl = containerRef.value?.closest<HTMLElement>('[data-team-tree]') ?? null;
    parentEl?.addEventListener('tree-view:shown', onTreeShown);

    await fetchData();
    tryInitChart();
});

onUnmounted(() => {
    containerRef.value?.removeEventListener('click', handleClick);
    parentEl?.removeEventListener('tree-view:shown', onTreeShown);
    parentEl = null;
    chart = null;
});
</script>

<template>
    <div data-testid="team-tree-root">
        <div v-if="isLoading"
             data-testid="team-tree-loading"
             class="flex items-center justify-center py-12 text-gray-500">
            <svg class="mr-2 size-5 animate-spin text-indigo-600"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24">
                <circle class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4" />
                <path class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            Loading team tree...
        </div>

        <div v-else-if="errorMessage"
             data-testid="team-tree-error"
             class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ errorMessage }}
        </div>

        <div v-show="!isLoading && !errorMessage"
             ref="containerRef"
             data-testid="team-tree-container"
             class="team-tree-container" />
    </div>
</template>

<style>
.team-tree-container {
    width: 100%;
    min-height: 700px;
    overflow: hidden;
    border-radius: 0.75rem;
    background: white;
    box-shadow:
        0 1px 2px 0 rgb(0 0 0 / 0.05),
        0 0 0 1px rgb(3 7 18 / 0.05);
}

.org-node {
    background: white;
    border: 1px solid #e5e7eb;
    border-left: 3px solid #6366f1;
    border-radius: 8px;
    font-family:
        ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial,
        sans-serif;
    overflow: hidden;
    width: 100%;
    height: 100%;
}

.org-node-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
}

.org-node-name {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
}

.org-node-count {
    font-size: 11px;
    color: #6b7280;
}

.org-node-body {
    padding: 6px 12px;
}

.org-member-row {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 3px 0;
    font-size: 12px;
    line-height: 1.5;
}

.org-member-name {
    color: #4f46e5;
    text-decoration: none;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
}

.org-member-name:hover {
    text-decoration: underline;
}

.org-role-list {
    display: flex;
    flex-wrap: wrap;
    gap: 3px;
}

.org-role-badge {
    display: inline-block;
    padding: 0 5px;
    font-size: 10px;
    line-height: 18px;
    color: #4338ca;
    background: #eef2ff;
    border-radius: 3px;
    text-decoration: none;
    white-space: nowrap;
}

.org-role-badge:hover {
    background: #e0e7ff;
}

.org-toggle-btn {
    display: block;
    width: 100%;
    padding: 4px 0;
    font-size: 11px;
    color: #6366f1;
    background: none;
    border: none;
    cursor: pointer;
    text-align: left;
}

.org-toggle-btn:hover {
    color: #4338ca;
}
</style>
