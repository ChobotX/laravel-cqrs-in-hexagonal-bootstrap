declare module 'd3-org-chart' {
    export interface OrgChartDataItem {
        id: string;
        parentId: string;
        [key: string]: unknown;
    }

    export interface D3Node<T = OrgChartDataItem> {
        data: T;
        id: string;
        depth: number;
        children?: D3Node<T>[];
        width: number;
        height: number;
    }

    export class OrgChart<T extends OrgChartDataItem = OrgChartDataItem> {
        constructor();
        container(selector: string | HTMLElement): this;
        data(data: T[]): this;
        nodeWidth(callback: (node: D3Node<T>) => number): this;
        nodeHeight(callback: (node: D3Node<T>) => number): this;
        nodeContent(callback: (node: D3Node<T>, index: number, nodes: D3Node<T>[], state: unknown) => string): this;
        onNodeClick(callback: (node: D3Node<T>) => void): this;
        childrenMargin(callback: (node: D3Node<T>) => number): this;
        siblingsMargin(callback: (node: D3Node<T>) => number): this;
        neighbourMargin(callback: (n1: D3Node<T>, n2: D3Node<T>) => number): this;
        compactMarginBetween(callback: (node: D3Node<T>) => number): this;
        initialExpandLevel(level: number): this;
        nodeId(callback: (item: T) => string): this;
        parentNodeId(callback: (item: T) => string): this;
        svgWidth(width: number): this;
        svgHeight(height: number): this;
        render(): this;
        expandAll(): this;
        collapseAll(): this;
        fit(options?: { animate?: boolean; scale?: boolean }): this;
        setExpanded(id: string, expanded?: boolean): this;
        setCentered(id: string): this;
        getChartState(): Record<string, unknown>;
    }
}
