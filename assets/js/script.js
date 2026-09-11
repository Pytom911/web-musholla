document.addEventListener("DOMContentLoaded", () => {
  const search = document.getElementById("searchInput");
  const table = document.getElementById("dataTable");

  if (!search || !table) return;

  const rows = table.querySelectorAll("tbody tr");

  search.addEventListener("input", function () {
    const keyword = this.value.toLowerCase().trim();

    rows.forEach((row) => {
      const text = row.textContent.toLowerCase();

      row.style.display = text.includes(keyword) ? "" : "none";
    });
  });
});
