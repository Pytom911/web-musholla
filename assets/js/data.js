const searchInput = document.getElementById("searchInput");
const filterBulan = document.getElementById("filterBulan");
const tableRows = document.querySelectorAll("#dataTable tbody tr[data-tanggal]");

function filterData() {
    const keyword = searchInput ? searchInput.value.toLowerCase().trim() : "";
    const bulan = filterBulan ? filterBulan.value : "";

    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const tanggal = row.dataset.tanggal;
        const bulanData = tanggal ? tanggal.substring(5, 7) : "";

        const cocokNama = text.includes(keyword);
        const cocokBulan = bulan === "" || bulanData === bulan;

        row.style.display = cocokNama && cocokBulan ? "" : "none";
    });
}

if (searchInput) {
    searchInput.addEventListener("input", filterData);
}

if (filterBulan) {
    filterBulan.addEventListener("change", filterData);
}

document.querySelectorAll(".btn-delete").forEach(button => {
    button.addEventListener("click", function(e) {
        if (!confirm("Yakin ingin menghapus data ini?")) {
            e.preventDefault();
        }
    });
});