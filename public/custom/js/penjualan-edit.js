$(document).ready(function () {
    $(".select2").select2({
        theme: "bootstrap-5",
    });

    let itemIndex = window.initialItemIndex || 0;
    let availableBarangs = [];

    const initialGudangId = $("#gudangSelect").val();
    if (initialGudangId) {
        loadBarangByGudang(initialGudangId);
    }

    $("#gudangSelect").change(function () {
        const gudangId = $(this).val();

        if (gudangId) {
            loadBarangByGudang(gudangId);
        } else {
            availableBarangs = [];
            $(".barang-select").each(function () {
                $(this).html(
                    '<option value="">Pilih gudang terlebih dahulu</option>'
                );
            });
            $(".stock-info").html(
                '<small class="text-muted">Pilih gudang terlebih dahulu</small>'
            );
        }
    });

    function loadBarangByGudang(gudangId) {
        $.ajax({
            url: `/gudang/${gudangId}/barang`,
            method: "GET",
            success: function (response) {
                availableBarangs = response;

                if (response.length === 0) {
                    Swal.fire({
                        icon: "warning",
                        title: "Stok Kosong",
                        text: "Gudang ini tidak memiliki stok barang yang tersedia.",
                    });
                    $(".barang-select").html(
                        '<option value="">Tidak ada barang tersedia</option>'
                    );
                } else {
                    updateAllBarangDropdowns();
                }
            },
            error: function () {
                Swal.fire("Error", "Gagal memuat data barang", "error");
                $(".barang-select").html(
                    '<option value="">Error memuat data</option>'
                );
            },
        });
    }

    function updateAllBarangDropdowns() {
        $(".barang-select").each(function () {
            const currentValue = $(this).val();
            const row = $(this).closest(".item-row");
            let optionsHtml = '<option value="">Pilih Barang</option>';

            availableBarangs.forEach(function (barang) {
                optionsHtml += `<option value="${barang.id}" 
                    data-satuan="${barang.satuan_barang}" 
                    data-stok="${barang.total_stok}"
                    ${currentValue == barang.id ? "selected" : ""}>
                    ${barang.text}
                </option>`;
            });

            $(this).html(optionsHtml);

            if (currentValue) {
                const selectedBarang = availableBarangs.find(
                    (b) => b.id == currentValue
                );
                if (selectedBarang) {
                    const stockDisplay = row.find(".stock-info");
                    stockDisplay.html(`
                        <small class="text-success">
                            <strong>Stok Tersedia: ${selectedBarang.total_stok} ${selectedBarang.satuan_barang}</strong>
                        </small>
                    `);
                }
            }
        });
    }

    $("#addItemBtn").click(function () {
        const gudangId = $("#gudangSelect").val();
        if (!gudangId) {
            Swal.fire(
                "Perhatian",
                "Pilih gudang terlebih dahulu sebelum menambah barang",
                "warning"
            );
            return;
        }
        addItem();
    });

    function addItem() {
        let barangOptions =
            '<option value="">Pilih gudang terlebih dahulu</option>';

        if (availableBarangs.length > 0) {
            barangOptions = '<option value="">Pilih Barang</option>';
            availableBarangs.forEach(function (barang) {
                barangOptions += `<option value="${barang.id}" 
                    data-satuan="${barang.satuan_barang}" 
                    data-stok="${barang.total_stok}">
                    ${barang.text}
                </option>`;
            });
        }

        const itemHtml = `
            <div class="item-row" data-index="${itemIndex}">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Barang</label>
                        <select name="barang_id[]" class="form-select barang-select" required>
                            ${barangOptions}
                        </select>
                        <div class="stock-info">
                            <small class="text-muted">Pilih barang untuk melihat stok</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Jumlah</label>
                        <input type="number" name="jumlah[]" class="form-control jumlah-input" 
                            min="1" step="1" value="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Jual (Auto)</label>
                        <input type="text" class="form-control subtotal-display" readonly value="Dihitung otomatis (HPP + 30%)">
                        <small class="text-info">
                            <i class="feather-info"></i> Harga otomatis dengan profit 30%
                        </small>
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
        const stok = selectedOption.data("stok") || 0;
        const satuan = selectedOption.data("satuan") || "unit";
        const row = $(this).closest(".item-row");

        const stockDisplay = row.find(".stock-info");
        if (stok > 0) {
            stockDisplay.html(`
                <small class="text-success">
                    <strong>Stok Tersedia: ${stok} ${satuan}</strong>
                </small>
            `);
        } else {
            stockDisplay.html(
                '<small class="text-danger">Stok tidak tersedia</small>'
            );
        }
    });

    function updateTotal() {}

    $("#formPenjualan").submit(function (e) {
        if ($(".item-row").length === 0) {
            e.preventDefault();
            Swal.fire("Error", "Minimal harus ada 1 barang", "error");
            return false;
        }

        const gudangId = $("#gudangSelect").val();
        if (!gudangId) {
            e.preventDefault();
            Swal.fire("Error", "Pilih gudang terlebih dahulu", "error");
            return false;
        }
    });
});
