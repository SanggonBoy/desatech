$(document).ready(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();

    document.addEventListener("input", function (e) {
        if (e.target.name === "no_telp") {
            let value = e.target.value;
            value = value.replace(/[^0-9+\-\s()]/g, "");
            e.target.value = value;

            const cleanNumber = value.replace(/[^0-9]/g, "");
            const whatsappBtn = document.querySelector(".whatsapp-btn-edit");
            if (whatsappBtn && cleanNumber) {
                let waNumber = cleanNumber;
                if (waNumber.startsWith("0")) {
                    waNumber = "62" + waNumber.substring(1);
                }
                whatsappBtn.href = `https://wa.me/${waNumber}`;
            }
        }
    });

    $(document).on("click", ".whatsapp-btn-edit", function (e) {
        const phoneNumber = $(this).attr("href").replace("https://wa.me/", "");

        if (!phoneNumber || phoneNumber === "https://wa.me/") {
            e.preventDefault();
            Swal.fire({
                icon: "warning",
                title: "Nomor Tidak Valid",
                text: "Silakan masukkan nomor telepon yang valid terlebih dahulu.",
            });
            return;
        }

        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Membuka WhatsApp...",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
        });
    });
});
