import { describe, expect, it } from 'vitest';
import { parseTeamTreeResponse } from './team-tree-response-guard';

const role = { id: 'r1', name: 'Admin', detailUrl: '/r/1' };
const member = {
    id: 'm1',
    name: 'Member',
    avatarUrl: null,
    detailUrl: '/m/1',
    roles: [role],
};
const node = {
    id: 't1',
    parentId: '',
    name: 'Team',
    slug: 'team',
    memberCount: 1,
    members: [member],
};

describe('parseTeamTreeResponse', () => {
    it('returns empty array for invalid root', () => {
        expect(parseTeamTreeResponse(null)).toEqual([]);
        expect(parseTeamTreeResponse({})).toEqual([]);
        expect(parseTeamTreeResponse({ data: 'x' })).toEqual([]);
    });

    it('filters out invalid team nodes', () => {
        const res = parseTeamTreeResponse({
            data: [node, { id: 'bad' }],
        });
        expect(res).toHaveLength(1);
        expect(res[0]?.id).toBe('t1');
    });

    it('rejects team member when roles is not an array', () => {
        const badMember = { ...member, roles: 'x' };
        const badNode = { ...node, members: [badMember] };
        expect(parseTeamTreeResponse({ data: [badNode] })).toEqual([]);
    });

    it('rejects team node when members is not an array', () => {
        expect(parseTeamTreeResponse({ data: [{ ...node, members: {} }] })).toEqual([]);
    });

    it('rejects team member entries that are not objects', () => {
        expect(parseTeamTreeResponse({ data: [{ ...node, members: [null] }] })).toEqual([]);
    });

    it('rejects non-object entries in data', () => {
        expect(parseTeamTreeResponse({ data: [null, node] })).toEqual([node]);
    });
});
