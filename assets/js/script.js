document.addEventListener("DOMContentLoaded", () => {
  const search = document.getElementById("searchInput");
  const table = document.getElementById("dataTable");

  if (search && table) {
    const rows = table.querySelectorAll("tbody tr");

    search.addEventListener("input", function () {
      const keyword = this.value.toLowerCase().trim();

      rows.forEach((row) => {
        const text = row.textContent.toLowerCase();

        row.style.display = text.includes(keyword) ? "" : "none";
      });
    });
  }

  const formatter = new Intl.NumberFormat("id-ID");

  document.querySelectorAll("[data-nominal-input]").forEach((input) => {
    const target = document.getElementById(input.dataset.nominalInput);

    if (!target) return;

    const updatePreview = () => {
      const nominal = Number(input.value);

      target.textContent =
        input.value === "" || Number.isNaN(nominal)
          ? "0"
          : formatter.format(nominal);
    };

    input.addEventListener("input", updatePreview);
    updatePreview();
  });
});
