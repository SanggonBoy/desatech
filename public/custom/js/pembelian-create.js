$(document).ready(function () {
    $(".select2").select2({
        theme: "bootstrap-5",
    });

    let itemIndex = 0;
    const barangData = window.barangData || [];

    addItem();

    $("#addItemBtn").click(function () {
        addItem();
    });

    function addItem() {
        const itemHtml = `
            <div class="item-row" data-index="${itemIndex}">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Barang</label>
                        <select name="barang_id[]" class="form-select barang-select" required>
                            <option value="">Pilih Barang</option>
                            ${barangData
                                .map(
                                    (b) =>
                                        `<option value="${b.id}" data-satuan="${
                                            b.satuan_barang
                                        }" data-harga="${b.harga_beli || 0}">${
                                            b.kode_barang
                                        } - ${b.nama_barang}</option>`
                                )
                                .join("")}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah[]" class="form-control jumlah-input" 
                            min="1" step="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Harga Satuan</label>
                        <input type="number" name="harga_satuan[]" class="form-control harga-input" 
                            min="0" step="0.01" value="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Subtotal</label>
                        <input type="text" class="form-control subtotal-display" readonly value="Rp 0">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm w-100 remove-item">
                            <i class="feather-trash-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        $("#itemsContainer").append(itemHtml);
        itemIndex++;
        updateTotal();
    }

    $(document).on("click", ".remove-item", function () {
        if ($(".item-row").length > 1) {
            $(this).closest(".item-row").remove();
            updateTotal();
        } else {
            Swal.fire("Perhatian", "Minimal harus ada 1 barang", "warning");
        }
    });

    $(document).on("change", ".barang-select", function () {
        const selectedOption = $(this).find("option:selected");
        const harga = selectedOption.data("harga") || 0;
        const row = $(this).closest(".item-row");

        row.find(".harga-input").val(harga);
        calculateSubtotal(row);
    });

    $(document).on("input", ".jumlah-input, .harga-input", function () {
        const row = $(this).closest(".item-row");
        calculateSubtotal(row);
    });

    function calculateSubtotal(row) {
        const jumlah = parseFloat(row.find(".jumlah-input").val()) || 0;
        const harga = parseFloat(row.find(".harga-input").val()) || 0;
        const subtotal = jumlah * harga;

        row.find(".subtotal-display").val("Rp " + formatNumber(subtotal));
        updateTotal();
    }

    function updateTotal() {
        let total = 0;
        $(".item-row").each(function () {
            const jumlah = parseFloat($(this).find(".jumlah-input").val()) || 0;
            const harga = parseFloat($(this).find(".harga-input").val()) || 0;
            total += jumlah * harga;
        });

        $("#totalPembelian").text("Rp " + formatNumber(total));
    }

    function formatNumber(num) {
        return num.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    $("#formPembelian").submit(function (e) {
        if ($(".item-row").length === 0) {
            e.preventDefault();
            Swal.fire("Error", "Minimal harus ada 1 barang", "error");
            return false;
        }
    });
});
