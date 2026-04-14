import { isRecord } from '../../shared/type-guards/is-record';

interface MemberRole {
    id: string;
    name: string;
    detailUrl: string;
}

interface TeamMember {
    id: string;
    name: string;
    avatarUrl: string | null;
    detailUrl: string;
    roles: MemberRole[];
}

export interface TeamNode {
    id: string;
    parentId: string;
    name: string;
    slug: string;
    memberCount: number;
    members: TeamMember[];
}

function isMemberRole(value: unknown): value is MemberRole {
    return (
        isRecord(value) &&
        typeof value.id === 'string' &&
        typeof value.name === 'string' &&
        typeof value.detailUrl === 'string'
    );
}

function isTeamMember(value: unknown): value is TeamMember {
    if (!isRecord(value)) {
        return false;
    }
    const avatar = value.avatarUrl;
    if (!Array.isArray(value.roles) || !(avatar === null || typeof avatar === 'string')) {
        return false;
    }
    return (
        typeof value.id === 'string' &&
        typeof value.name === 'string' &&
        typeof value.detailUrl === 'string' &&
        value.roles.every(isMemberRole)
    );
}

function isTeamNode(value: unknown): value is TeamNode {
    if (!isRecord(value)) {
        return false;
    }
    if (!Array.isArray(value.members)) {
        return false;
    }
    return (
        typeof value.id === 'string' &&
        typeof value.parentId === 'string' &&
        typeof value.name === 'string' &&
        typeof value.slug === 'string' &&
        typeof value.memberCount === 'number' &&
        value.members.every(isTeamMember)
    );
}

export function parseTeamTreeResponse(value: unknown): TeamNode[] {
    if (!isRecord(value) || !Array.isArray(value.data)) {
        return [];
    }
    return value.data.filter(isTeamNode);
}
