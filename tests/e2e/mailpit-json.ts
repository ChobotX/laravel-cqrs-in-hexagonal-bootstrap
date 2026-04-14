export function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === 'object' && value !== null;
}

export interface MailpitMessageSummary {
    ID: string;
    To: Array<{ Address: string }>;
}

export interface MailpitMessagesResponse {
    messages?: MailpitMessageSummary[];
}

export interface MailpitMessageBody {
    HTML?: string;
    Text?: string;
}

function isMailpitToEntry(value: unknown): value is { Address: string } {
    return isRecord(value) && typeof value.Address === 'string';
}

function isMailpitMessageSummary(value: unknown): value is MailpitMessageSummary {
    if (!isRecord(value) || typeof value.ID !== 'string' || !Array.isArray(value.To)) {
        return false;
    }
    return value.To.every(isMailpitToEntry);
}

export function parseMailpitMessagesResponse(value: unknown): MailpitMessagesResponse {
    if (!isRecord(value) || !Array.isArray(value.messages)) {
        return {};
    }
    return {
        messages: value.messages.filter(isMailpitMessageSummary),
    };
}

export function parseMailpitMessageBody(value: unknown): MailpitMessageBody {
    if (!isRecord(value)) {
        return {};
    }
    let html: string | undefined;
    if (typeof value.HTML === 'string') {
        html = value.HTML;
    } else if (typeof value.Html === 'string') {
        html = value.Html;
    }
    let text: string | undefined;
    if (typeof value.Text === 'string') {
        text = value.Text;
    } else if (typeof value.text === 'string') {
        text = value.text;
    }

    return {
        HTML: html,
        Text: text,
    };
}
