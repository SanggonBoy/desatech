$(document).ready(function () {
    $("#table").DataTable({
        responsive: true,
    });

    $(document).on("click", ".delete-btn", function () {
        const id = $(this).data("id");
        const nama = $(this).data("nama");

        Swal.fire({
            title: "Konfirmasi Hapus",
            text: `Apakah Anda yakin ingin menghapus barang "${nama}"?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                const form = $("<form>", {
                    method: "POST",
                    action: `/barang/${id}`,
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
