document.addEventListener("DOMContentLoaded", () => {
  const table = document.getElementById("dataTable");
  const search = document.getElementById("searchInput");

  // SEARCH DATA LAPORAN
  if (table && search) {
    const rows = table.querySelectorAll("tbody tr");

    search.addEventListener("input", function () {
      const keyword = this.value.toLowerCase().trim();

      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(keyword) ? "" : "none";
      });
    });
  }
});

// PRINT LAPORAN
function printReport() {
  window.print();
}
