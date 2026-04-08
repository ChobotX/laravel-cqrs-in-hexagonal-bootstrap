import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import TeamTree from './TeamTree.vue';

const HTTP_OK = 200;
const HTTP_SERVER_ERROR = 500;

const { mockRender }: { mockRender: ReturnType<typeof vi.fn> } = vi.hoisted(() => ({
    mockRender: vi.fn(),
}));

vi.mock('d3-org-chart', () => {
    class MockOrgChart {
        container(): this {
            return this;
        }
        data(): this {
            return this;
        }
        svgWidth(): this {
            return this;
        }
        svgHeight(): this {
            return this;
        }
        nodeId(): this {
            return this;
        }
        parentNodeId(): this {
            return this;
        }
        nodeWidth(): this {
            return this;
        }
        nodeHeight(): this {
            return this;
        }
        childrenMargin(): this {
            return this;
        }
        siblingsMargin(): this {
            return this;
        }
        neighbourMargin(): this {
            return this;
        }
        compactMarginBetween(): this {
            return this;
        }
        nodeContent(): this {
            return this;
        }
        onNodeClick(): this {
            return this;
        }
        render(): this {
            mockRender();
            return this;
        }
    }

    return { OrgChart: MockOrgChart };
});

interface TreeMemberRole {
    id: string;
    name: string;
    detailUrl: string;
}

interface TreeMember {
    id: string;
    name: string;
    avatarUrl: string | null;
    detailUrl: string;
    roles: TreeMemberRole[];
}

interface TreeNode {
    id: string;
    parentId: string;
    name: string;
    slug: string;
    memberCount: number;
    members: TreeMember[];
}

interface TreeResponse {
    data: TreeNode[];
}

const mockTreeData: TreeResponse = {
    data: [
        {
            id: 'team-1',
            parentId: '',
            name: 'Company',
            slug: 'company',
            memberCount: 2,
            members: [
                {
                    id: 'user-1',
                    name: 'Alice',
                    avatarUrl: '/files/avatar-1',
                    detailUrl: '/users/user-1/edit',
                    roles: [{ id: 'role-1', name: 'Developer', detailUrl: '/roles/role-1' }],
                },
                {
                    id: 'user-2',
                    name: 'Bob',
                    avatarUrl: null,
                    detailUrl: '/users/user-2/edit',
                    roles: [],
                },
            ],
        },
        {
            id: 'team-2',
            parentId: 'team-1',
            name: 'Engineering',
            slug: 'engineering',
            memberCount: 0,
            members: [],
        },
    ],
};

function createSuccessFetchMock(response: unknown = mockTreeData): void {
    global.fetch = vi.fn().mockResolvedValue({
        ok: true,
        status: HTTP_OK,
        json: () => Promise.resolve(response),
    });
}

function createErrorFetchMock(response: unknown = null): void {
    global.fetch = vi.fn().mockResolvedValue({
        ok: false,
        status: HTTP_SERVER_ERROR,
        json: () => Promise.resolve(response),
    });
}

function mountTeamTree(props: Partial<InstanceType<typeof TeamTree>['$props']> = {}): ReturnType<typeof mount> {
    return mount(TeamTree, {
        props: {
            fetchUrl: '/internal-api/teams/tree',
            ...props,
        },
        attachTo: document.body,
    });
}

describe('TeamTree', () => {
    beforeEach(() => {
        createSuccessFetchMock();
        mockRender.mockClear();
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('shows loading state initially', () => {
        const wrapper = mountTeamTree();

        expect(wrapper.find('[data-testid="team-tree-loading"]').exists()).toBe(true);
    });

    it('fetches data on mount with correct URL', async () => {
        mountTeamTree();
        await flushPromises();

        expect(global.fetch).toHaveBeenCalledWith(
            '/internal-api/teams/tree',
            expect.objectContaining({
                headers: expect.objectContaining({ Accept: 'application/json' }),
                credentials: 'same-origin',
            }),
        );
    });

    it('hides loading after fetch completes', async () => {
        const wrapper = mountTeamTree();
        await flushPromises();

        expect(wrapper.find('[data-testid="team-tree-loading"]').exists()).toBe(false);
    });

    it('shows error on fetch failure', async () => {
        createErrorFetchMock();

        const wrapper = mountTeamTree();
        await flushPromises();

        expect(wrapper.find('[data-testid="team-tree-error"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="team-tree-error"]').text()).toContain('HTTP 500');
    });

    it('shows error on network failure', async () => {
        global.fetch = vi.fn().mockRejectedValue(new Error('Network error'));

        const wrapper = mountTeamTree();
        await flushPromises();

        expect(wrapper.find('[data-testid="team-tree-error"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="team-tree-error"]').text()).toContain('Network error');
    });

    it('shows tree container after successful load', async () => {
        const wrapper = mountTeamTree();
        await flushPromises();

        expect(wrapper.find('[data-testid="team-tree-container"]').exists()).toBe(true);
    });

    it('does not render chart when data is empty', async () => {
        createSuccessFetchMock({ data: [] });

        mountTeamTree();
        await flushPromises();
        await flushPromises();

        expect(mockRender).not.toHaveBeenCalled();
    });
});
