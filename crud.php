<?php
include 'db_connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek jika ada data yang dikirim menggunakan metode POST
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses penambahan data
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // Menggunakan prepared statements untuk mencegah SQL Injection
        $stmt = $conn->prepare("INSERT INTO health_services (name, address, latitude, longitude) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdd", $name, $address, $latitude, $longitude);
        if ($stmt->execute()) {
            $message = "Data berhasil ditambahkan.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } 
    // Proses pembaruan data
    elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $address = $_POST['address'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // Menggunakan prepared statements untuk pembaruan data
        $stmt = $conn->prepare("UPDATE health_services SET name=?, address=?, latitude=?, longitude=? WHERE id=?");
        $stmt->bind_param("ssddi", $name, $address, $latitude, $longitude, $id);
        if ($stmt->execute()) {
            $message = "Data berhasil diperbarui.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } 
    // Proses penghapusan data
    elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Menggunakan prepared statements untuk penghapusan data
        $stmt = $conn->prepare("DELETE FROM health_services WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $message = "Data berhasil dihapus.";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Ambil semua data dari database untuk ditampilkan
$result = $conn->query("SELECT * FROM health_services");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Layanan Kesehatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        button {
            padding: 5px 10px;
            margin: 2px;
            cursor: pointer;
        }
        h1, h2 {
            margin-bottom: 10px;
        }
        form {
            margin-bottom: 20px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm("Anda yakin ingin menghapus data ini?");
        }
    </script>
</head>
<body>

<h1>Data Layanan Kesehatan di Kabupaten Banyumas</h1>

<a href="index.php" target="_blank">
    <button>Halaman Utama</button>
</a>

<?php
// Menampilkan pesan error atau sukses jika ada
if (isset($message)) {
    echo "<p class='" . (strpos($message, 'Error') !== false ? 'error' : 'success') . "'>$message</p>";
}
?>

<table>
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Alamat</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['address']); ?></td>
        <td><?php echo $row['latitude']; ?></td>
        <td><?php echo $row['longitude']; ?></td>
        <td>
            <!-- Formulir untuk mengedit data -->
            <form action="edit.php" method="get" style="display:inline;">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button type="submit">Edit</button>
            </form>
            <!-- Formulir untuk menghapus data dengan konfirmasi -->
            <form action="crud.php" method="post" style="display:inline;" onsubmit="return confirmDelete();">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="delete">Delete</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

<h2>Tambah Data Baru</h2>
<form action="crud.php" method="post">
    <input type="text" name="name" placeholder="Nama" required>
    <input type="text" name="address" placeholder="Alamat" required>
    <input type="number" step="any" name="latitude" placeholder="Latitude" required>
    <input type="number" step="any" name="longitude" placeholder="Longitude" required>
    <button type="submit" name="add">Tambah</button>
</form>

</body>
</html>

<?php $conn->close(); ?>
