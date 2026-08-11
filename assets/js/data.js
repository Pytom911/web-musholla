const searchInput = document.getElementById("searchInput");
const filterBulan = document.getElementById("filterBulan");
const tableRows = document.querySelectorAll("#dataTable tbody tr");

function filterData() {

    const keyword = searchInput
        ? searchInput.value.toLowerCase().trim()
        : "";

    const bulan = filterBulan
        ? filterBulan.value
        : "";

    tableRows.forEach(row => {

        const text = row.textContent.toLowerCase();

        let cocokBulan = true;

        if (filterBulan) {

            const tanggal = row.dataset.tanggal || "";
            const bulanData = tanggal.substring(5, 7);

            cocokBulan = bulan === "" || bulanData === bulan;
        }

        const cocokKeyword = text.includes(keyword);

        row.style.display = (cocokKeyword && cocokBulan)
            ? ""
            : "none";

    });

}

   // SEARCH

if (searchInput) {
    searchInput.addEventListener("input", filterData);
}

   // FILTER BULAN

if (filterBulan) {
    filterBulan.addEventListener("change", filterData);
}

   // DELETE CONFIRMATION

document.querySelectorAll(".btn-delete").forEach(button => {

    button.addEventListener("click", function (e) {

        if (!confirm("Yakin ingin menghapus data ini?")) {
            e.preventDefault();
        }

    });

});