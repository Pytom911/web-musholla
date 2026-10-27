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

  const kegiatanSelect = document.querySelector("[data-kegiatan-select]");
  const donaturInput = document.querySelector("[data-donatur-input]");
  const donaturRequired = document.querySelector("[data-donatur-required]");

  if (kegiatanSelect && donaturInput) {
    const updateDonaturRequirement = () => {
      const isOptional = kegiatanSelect.value !== "";

      donaturInput.required = !isOptional;
      donaturInput.placeholder = isOptional
        ? "Opsional (sudah memilih kegiatan)"
        : "Masukkan nama donatur";

      if (donaturRequired) {
        donaturRequired.style.display = isOptional ? "none" : "";
      }
    };

    kegiatanSelect.addEventListener("change", updateDonaturRequirement);
    updateDonaturRequirement();
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
