<?php
// Include koneksi ke database
include 'db_connection.php';

// Ambil ID dari URL dan validasi
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID tidak valid.");
}

// Ambil data untuk ID tersebut menggunakan prepared statement
$query = $conn->prepare("SELECT * FROM health_services WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();

// Periksa apakah data ditemukan
if ($result->num_rows == 0) {
    die("Data tidak ditemukan.");
}
$data = $result->fetch_assoc();
$query->close();

// Proses form untuk update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    // Ambil dan validasi inputan form
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $latitude = filter_input(INPUT_POST, 'latitude', FILTER_VALIDATE_FLOAT);
    $longitude = filter_input(INPUT_POST, 'longitude', FILTER_VALIDATE_FLOAT);

    // Validasi input
    if ($name && $address && $latitude !== false && $longitude !== false) {
        // Update data ke database menggunakan prepared statement
        $update_query = $conn->prepare("UPDATE health_services SET name = ?, address = ?, latitude = ?, longitude = ? WHERE id = ?");
        $update_query->bind_param("ssddi", $name, $address, $latitude, $longitude, $id);
        if ($update_query->execute()) {
            // Redirect ke halaman daftar setelah berhasil update
            header("Location: crud.php");
            exit();
        } else {
            echo "<p style='color: red;'>Terjadi kesalahan saat memperbarui data: " . $update_query->error . "</p>";
        }
        $update_query->close();
    } else {
        echo "<p style='color: red;'>Form input tidak valid. Harap periksa kembali data yang dimasukkan.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Layanan Kesehatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="text"], input[type="number"], button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            box-sizing: border-box;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        a button {
            background-color: #f44336;
        }
        a button:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>

<h1>Edit Data Layanan Kesehatan</h1>

<form action="" method="post">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id']); ?>">

    <label for="name">Nama:</label>
    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required><br>

    <label for="address">Alamat:</label>
    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($data['address']); ?>" required><br>

    <label for="latitude">Latitude:</label>
    <input type="number" step="any" id="latitude" name="latitude" value="<?php echo htmlspecialchars($data['latitude']); ?>" required><br>

    <label for="longitude">Longitude:</label>
    <input type="number" step="any" id="longitude" name="longitude" value="<?php echo htmlspecialchars($data['longitude']); ?>" required><br>

    <button type="submit" name="update">Simpan</button>
</form>

<a href="crud.php"><button>Kembali</button></a>

</body>
</html>

<?php
// Tutup koneksi database setelah selesai
$conn->close();
?>
