<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ulang = $_POST['ulang_password'];
    $nama = $_POST['nama'];
    $nik = $_POST['nik'];
    $level = $_POST['level'];

    $password = $_POST['password'];
    $ulang = $_POST['ulang_password'];

    // cek password cocok dulu
    if ($password !== $ulang) {
        echo "<script>alert('Ulangi, Password Anda tidak sama'); window.location.href='form_register.php';</script>";
    } else {
        // hash dengan md5 (tidak disarankan tapi kalau kamu ingin pakai ini)
        $passwordMD5 = md5($password);

        $sql = "INSERT INTO register (username, password, nik, namalengkap, level) VALUES ('$username', '$passwordMD5', '$nik','$nama', '$level')";
        $query = mysqli_query($koneksi, $sql);

        if ($query) {
            echo "<script>alert('Anda Sukses Registrasi'); window.location.href='data_register.php';</script>";
        } else {
            echo "<script>alert('Gagal Registrasi'); window.location.href='form_register.php';</script>";
        }
    }
}
?>