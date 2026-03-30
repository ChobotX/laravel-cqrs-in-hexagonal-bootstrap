const VIEW_PARAM = 'view';
const VIEW_LIST = 'list';
const VIEW_TREE = 'tree';

const listBtn: HTMLElement | null = document.getElementById('teams-view-list-btn');
const treeBtn: HTMLElement | null = document.getElementById('teams-view-tree-btn');
const listView: HTMLElement | null = document.getElementById('teams-list-view');
const treeView: HTMLElement | null = document.getElementById('teams-tree-view');

if (listBtn && treeBtn && listView && treeView) {
    const activeClass: string[] = ['bg-indigo-100', 'text-indigo-700'];
    const inactiveClass: string[] = ['text-gray-400', 'hover:text-gray-600'];

    function activate(btn: HTMLElement, otherBtn: HTMLElement, show: HTMLElement, hide: HTMLElement): void {
        show.classList.remove('hidden');
        hide.classList.add('hidden');

        btn.classList.add(...activeClass);
        btn.classList.remove(...inactiveClass);

        otherBtn.classList.remove(...activeClass);
        otherBtn.classList.add(...inactiveClass);
    }

    function setViewParam(view: string): void {
        const url = new URL(window.location.href);
        url.searchParams.set(VIEW_PARAM, view);
        window.history.replaceState(null, '', url.toString());
    }

    listBtn.addEventListener('click', () => {
        activate(listBtn, treeBtn, listView, treeView);
        setViewParam(VIEW_LIST);
    });

    treeBtn.addEventListener('click', () => {
        activate(treeBtn, listBtn, treeView, listView);
        setViewParam(VIEW_TREE);
        treeView.dispatchEvent(new CustomEvent('tree-view:shown'));
    });

    const initialView: string | null = new URL(window.location.href).searchParams.get(VIEW_PARAM);

    if (initialView === VIEW_TREE) {
        activate(treeBtn, listBtn, treeView, listView);
        treeView.dispatchEvent(new CustomEvent('tree-view:shown'));
    }
}
