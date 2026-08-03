// ===============================
// Data CRUD
// Sistem Informasi Musholla
// ===============================

// Search Table
const searchInput = document.getElementById("searchInput");

if (searchInput) {
    searchInput.addEventListener("keyup", function () {
        const keyword = this.value.toLowerCase();
        const rows = document.querySelectorAll("#dataTable tbody tr");

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(keyword) ? "" : "none";
        });
    });
}

// Confirm Delete
document.querySelectorAll(".btn-delete").forEach(button => {
    button.addEventListener("click", function (e) {
        if (!confirm("Yakin ingin menghapus data ini?")) {
            e.preventDefault();
        }
    });
});