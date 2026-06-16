<?php
session_start();
include "koneksi.php";

$user = $_POST['username'];
$password = md5($_POST['password']);
$level = $_POST['level']; // ambil level dari form
$op = $_GET['op'];

if ($op == "in") {
    // Ambil data user berdasarkan username, password, level
    $query_l = "SELECT * FROM register WHERE username='$user' AND password='$password' AND level='$level'";
    $h_l = $koneksi->query($query_l);

    if (mysqli_num_rows($h_l) == 1) {
        $d_l = $h_l->fetch_array();

        /// Simpan data session umum
        $_SESSION['username'] = $d_l['username'];
        $_SESSION['level'] = $d_l['level'];
        $_SESSION['namalengkap'] = $d_l['namalengkap'];

        // Hanya untuk non-admin: simpan id_peserta
        $level_lower = strtolower($d_l['level']);
        if (in_array($level_lower, ['warga', 'panitia', 'berqurban'])) {
            $_SESSION['id_peserta'] = $d_l['id_peserta'];
        }


        // Redirect berdasarkan level
        if (strtolower($d_l['level']) == "admin") {
            header("location:dashy/admindas.php");
        } else if (strtolower($d_l['level']) == "panitia") {
            header("location:panitiadas.php");
        } else if (strtolower($d_l['level']) == "warga") {
            header("location:wargadas.php");
        } else if (strtolower($d_l['level']) == "berqurban") {
            header("location:berqurbandas.php");
        } else {
            die("Level tidak dikenali. <a href=\"javascript:history.back()\">Kembali</a>");
        }
        exit;
    } else {
        echo "Username / Password / Level salah! <a href='javascript:history.back()'>Kembali</a>";
    }
} elseif ($op == "out") {
    unset($_SESSION['username']);
    unset($_SESSION['level']);
    unset($_SESSION['id_peserta']); // hapus session nik juga saat logout
    unset($_SESSION['namalengkap']);
    header("location:index.php");
}
?>