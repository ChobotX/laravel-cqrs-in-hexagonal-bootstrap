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

    listBtn.addEventListener('click', () => activate(listBtn, treeBtn, listView, treeView));
    treeBtn.addEventListener('click', () => {
        activate(treeBtn, listBtn, treeView, listView);
        treeView.dispatchEvent(new CustomEvent('tree-view:shown'));
    });
}
