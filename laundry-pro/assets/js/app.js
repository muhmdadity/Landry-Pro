document.addEventListener('DOMContentLoaded', () => {
    // Toggle Sidebar Mobile
    const sidebar = document.getElementById('appSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    }

    // Select All Checkboxes Helper
    document.querySelectorAll('[data-select-all]').forEach(masterCheckbox => {
        masterCheckbox.addEventListener('change', function () {
            const targetSelector = this.getAttribute('data-select-all');
            document.querySelectorAll(targetSelector).forEach(cb => {
                cb.checked = this.checked;
            });
        });
    });

    // Live Table Search Filter
    document.querySelectorAll('[data-table-search]').forEach(input => {
        input.addEventListener('keyup', function () {
            const keyword = this.value.toLowerCase();
            const tableId = this.getAttribute('data-table-search');
            const rows = document.querySelectorAll(`#${tableId} tbody tr`);
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(keyword) ? '' : 'none';
            });
        });
    });
});
