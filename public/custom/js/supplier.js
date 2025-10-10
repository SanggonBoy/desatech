$(document).ready(function () {
    $("#supplierTable").DataTable({
        responsive: true,
        columnDefs: [
            {
                targets: [4], // Kolom alamat
                width: "30%",
            },
            {
                targets: [-1], // Kolom aksi
                orderable: false,
                searchable: false,
            },
        ],
    });

    $('[data-bs-toggle="tooltip"]').tooltip();

    $(document).on('click', '.whatsapp-btn', function(e) {
        e.preventDefault();
        
        const supplierNama = $(this).data('supplier');
        const phoneNumber = $(this).data('phone');
        const originalHref = $(this).attr('href');

        Swal.fire({
            title: `Chat WhatsApp dengan ${supplierNama}`,
            html: `
                <div class="text-start">
                    <label for="wa-message" class="form-label">Pesan (opsional):</label>
                    <textarea id="wa-message" class="form-control" rows="3" placeholder="Ketik pesan Anda atau biarkan kosong untuk pesan default...">Halo ${supplierNama}, .</textarea>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Buka WhatsApp',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#25D366',
            preConfirm: () => {
                const message = document.getElementById('wa-message').value;
                return { message: message };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const message = result.value.message;
                let waUrl = originalHref;
                
                if (message.trim()) {
                    waUrl += `?text=${encodeURIComponent(message)}`;
                }
                
                window.open(waUrl, '_blank');
                
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Membuka WhatsApp untuk ${supplierNama}`,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        });
    });

    $(document).on("click", ".delete-btn", function () {
        const id = $(this).data("id");
        const nama = $(this).data("nama");

        Swal.fire({
            title: "Konfirmasi Hapus",
            text: `Apakah Anda yakin ingin menghapus supplier "${nama}"?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit
                const form = $("<form>", {
                    method: "POST",
                    action: `/supplier/${id}`,
                });

                form.append(
                    $("<input>", {
                        type: "hidden",
                        name: "_token",
                        value: $('meta[name="csrf-token"]').attr("content"),
                    })
                );

                form.append(
                    $("<input>", {
                        type: "hidden",
                        name: "_method",
                        value: "DELETE",
                    })
                );

                $("body").append(form);
                form.submit();
            }
        });
    });
});
