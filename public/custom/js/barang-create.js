let rowCount = 1;

document.getElementById("addRow").addEventListener("click", function () {
    const tableBody = document.getElementById("barangTableBody");
    const newRow = document.createElement("tr");

    newRow.innerHTML = `
        <td class="text-center">${rowCount + 1}</td>
        <td>
            <input type="text" name="barang[${rowCount}][nama_barang]" 
                   class="form-control" 
                   placeholder="Masukkan nama barang" required>
        </td>
        <td>
            <select name="barang[${rowCount}][satuan]" class="form-control" required>
                <option value="">Pilih Satuan</option>
                <option value="pcs">Pcs</option>
                <option value="box">Box</option>
                <option value="kg">Kg</option>
                <option value="liter">Liter</option>
                <option value="unit">Unit</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger removeRow">
                <i class="feather-trash-2"></i>
            </button>
        </td>
    `;

    tableBody.appendChild(newRow);
    rowCount++;
    updateRemoveButtons();
});

document.addEventListener("click", function (e) {
    if (
        e.target.classList.contains("removeRow") ||
        e.target.parentElement.classList.contains("removeRow")
    ) {
        const button = e.target.classList.contains("removeRow")
            ? e.target
            : e.target.parentElement;
        const row = button.closest("tr");
        row.remove();
        updateRowNumbers();
        updateRemoveButtons();
    }
});

function updateRowNumbers() {
    const rows = document.querySelectorAll("#barangTableBody tr");
    rows.forEach((row, index) => {
        row.querySelector("td:first-child").textContent = index + 1;

        const namaInput = row.querySelector('input[name*="nama_barang"]');
        const satuanSelect = row.querySelector('select[name*="satuan"]');

        namaInput.name = `barang[${index}][nama_barang]`;
        satuanSelect.name = `barang[${index}][satuan]`;
    });
    rowCount = rows.length;
}

function updateRemoveButtons() {
    const removeButtons = document.querySelectorAll(".removeRow");
    removeButtons.forEach((button) => {
        button.disabled = removeButtons.length <= 1;
    });
}
