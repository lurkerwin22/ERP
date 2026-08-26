document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector(
        '[data-drawer-toggle="default-sidebar"]'
    );

    const sidebar = document.getElementById('default-sidebar');

    if (!toggleBtn || !sidebar) {
        return;
    }

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
    });

    document.addEventListener('click', (e) => {
        if (
            !sidebar.contains(e.target) &&
            !toggleBtn.contains(e.target)
        ) {
            sidebar.classList.add('-translate-x-full');
        }
    });
});