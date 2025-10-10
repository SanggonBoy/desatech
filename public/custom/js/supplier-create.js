let rowCount = 1;

document.getElementById("addRow").addEventListener("click", function () {
    const tableBody = document.getElementById("supplierTableBody");
    const newRow = document.createElement("tr");

    newRow.innerHTML = `
        <td class="text-center">${rowCount + 1}</td>
        <td>
            <input type="text" name="supplier[${rowCount}][nama_supplier]" 
                   class="form-control" 
                   placeholder="Masukkan nama supplier" required>
        </td>
        <td>
            <input type="text" name="supplier[${rowCount}][no_telp]" 
                   class="form-control" 
                   placeholder="Masukkan no. telpon" required>
        </td>
        <td>
            <textarea name="supplier[${rowCount}][alamat]" 
                    class="form-control" 
                    rows="2" 
                    placeholder="Masukkan alamat" required></textarea>
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
    const rows = document.querySelectorAll("#supplierTableBody tr");
    rows.forEach((row, index) => {
        row.querySelector("td:first-child").textContent = index + 1;

        const namaInput = row.querySelector('input[name*="nama_supplier"]');
        const telpInput = row.querySelector('input[name*="no_telp"]');
        const alamatTextarea = row.querySelector('textarea[name*="alamat"]');

        namaInput.name = `supplier[${index}][nama_supplier]`;
        telpInput.name = `supplier[${index}][no_telp]`;
        alamatTextarea.name = `supplier[${index}][alamat]`;
    });
    rowCount = rows.length;
}

function updateRemoveButtons() {
    const removeButtons = document.querySelectorAll(".removeRow");
    removeButtons.forEach((button) => {
        button.disabled = removeButtons.length <= 1;
    });
}

document.addEventListener("input", function (e) {
    if (e.target.name && e.target.name.includes("no_telp")) {
        let value = e.target.value;
        value = value.replace(/[^0-9+\-\s()]/g, "");
        e.target.value = value;
    }
});
