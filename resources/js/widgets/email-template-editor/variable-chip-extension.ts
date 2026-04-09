import type { DOMOutputSpec, NodeConfig, NodeType } from '@tiptap/core';
import { mergeAttributes, Node } from '@tiptap/core';
import type { Node as ProsemirrorNode } from '@tiptap/pm/model';
import type { Transaction } from '@tiptap/pm/state';

declare module '@tiptap/core' {
    interface Commands<ReturnType> {
        variableChip: {
            insertVariable: (name: string) => ReturnType;
        };
    }
}

interface RuleHandlerArgs {
    state: { tr: Transaction };
    range: { from: number; to: number };
    match: RegExpMatchArray;
}

const VARIABLE_PATTERN_GLOBAL: RegExp = /\{\{\s*\$(\w+)\s*\}\}/g;
const VARIABLE_PATTERN_TEST: RegExp = /\{\{\s*\$(\w+)\s*\}\}/;
const VARIABLE_INPUT_PATTERN: RegExp = /\{\{\s*\$(\w+)\s*\}\}$/;

export function createVariableRuleHandler(nodeType: NodeType): (args: RuleHandlerArgs) => void {
    return ({ state, range, match }: RuleHandlerArgs): void => {
        const variableName: string = match[1];
        const node: ProsemirrorNode = nodeType.create({ name: variableName });
        state.tr.replaceWith(range.from, range.to, node);
    };
}

export const VariableChipExtension: Node = Node.create({
    name: 'variableChip',
    group: 'inline',
    inline: true,
    atom: true,

    addAttributes(): NodeConfig['addAttributes'] {
        return {
            name: {
                default: '',
                parseHTML: (element: HTMLElement): string => element.getAttribute('data-variable') ?? '',
                renderHTML: (attributes: Record<string, string>): Record<string, string> => ({
                    'data-variable': attributes.name,
                }),
            },
        };
    },

    parseHTML(): NodeConfig['parseHTML'] {
        return [
            { tag: 'span[data-variable]' },
            {
                tag: 'span',
                getAttrs: (node: string | HTMLElement): false | null =>
                    VARIABLE_PATTERN_TEST.test(String((node as HTMLElement).textContent)) ? null : false,
            },
        ];
    },

    renderHTML({ HTMLAttributes }: { HTMLAttributes: Record<string, string> }): DOMOutputSpec {
        return [
            'span',
            mergeAttributes(HTMLAttributes, {
                class: 'inline-flex items-center rounded-md bg-indigo-50 px-1.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10',
            }),
            `{{ $${HTMLAttributes['data-variable']} }}`,
        ];
    },

    renderText({ node }: { node: ProsemirrorNode }): string {
        return `{{ $${node.attrs.name} }}`;
    },

    addCommands(): NodeConfig['addCommands'] {
        return {
            insertVariable:
                (name: string) =>
                ({ commands }: { commands: { insertContent: (content: unknown) => boolean } }) =>
                    commands.insertContent({
                        type: this.name,
                        attrs: { name },
                    }),
        };
    },

    addInputRules(): NodeConfig['addInputRules'] {
        const handler = createVariableRuleHandler(this.type);
        return [{ find: VARIABLE_INPUT_PATTERN, handler }];
    },

    addPasteRules(): NodeConfig['addPasteRules'] {
        const handler = createVariableRuleHandler(this.type);
        return [{ find: VARIABLE_PATTERN_GLOBAL, handler }];
    },
});
