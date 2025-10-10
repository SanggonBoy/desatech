$(document).ready(function () {
    $("#table").DataTable();

    $(".delete-btn").click(function () {
        const id = $(this).data("id");
        const noPenjualan = $(this).data("no");

        Swal.fire({
            title: "Konfirmasi Hapus",
            text: `Apakah Anda yakin ingin menghapus penjualan ${noPenjualan}?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/penjualan/${id}`,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        Swal.fire(
                            "Berhasil!",
                            "Data berhasil dihapus.",
                            "success"
                        ).then(() => location.reload());
                    },
                    error: function (xhr) {
                        const errorMsg =
                            xhr.responseJSON?.message ||
                            "Terjadi kesalahan saat menghapus data.";
                        Swal.fire("Error!", errorMsg, "error");
                    },
                });
            }
        });
    });
});
