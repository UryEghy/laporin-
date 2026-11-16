document.addEventListener ("DOMContentLoaded", function() {
    console.log("lapor.in siap digunakan!");

document.querySelectorAll('input[type="password"]').forEach(function (input) {
        var tombol = document.createElement("button");
        tombol.type = "button";
        tombol.className = "btn btn-link btn-sm p-0 mt-1 text-decoration-none";
        tombol.textContent = "Tampilkan password";
        input.insertAdjacentElement("afterend", tombol);
 
        tombol.addEventListener("click", function () {
            var tampil = input.type === "password";
            input.type = tampil ? "text" : "password";
            tombol.textContent = tampil ? "Sembunyikan password" : "Tampilkan password";
        });
    });

    document.querySelectorAll('a[href*="logout.php"]').forEach(function (link) {
        link.addEventListener("click", function (e) {
            if (!confirm("Yakin ingin keluar?")) {
                e.preventDefault();
            }
        });
    });

    var passBaru = document.querySelector('input[name="password_baru"]');
    var passKonfirmasi = document.querySelector('input[name="konfirmasi_password"]');
    if (passBaru && passKonfirmasi) {
        passKonfirmasi.form.addEventListener("submit", function (e) {
            if (passBaru.value !== passKonfirmasi.value) {
                e.preventDefault();
                alert("Konfirmasi password tidak sama!");
            }
        });
    }
});