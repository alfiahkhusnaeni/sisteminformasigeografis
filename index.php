<?php
// Include database connection
include 'db_connection.php';

// Fetch data for health services
$sql = "SELECT id, name, latitude, longitude FROM health_services";
$result = $conn->query($sql);

// Collect health services data into an array
$health_services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $health_services[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Kecamatan dan Layanan Kesehatan Banyumas</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
    #map {
        height: 600px;
    }
    </style>
</head>

<body>

    <h1>Peta Kecamatan dan Layanan Kesehatan Kabupaten Banyumas</h1>
    <a href="crud.php" target="_blank">
        <button>Data</button>
    </a>

    <div id="map" style="height: 700px;"></div>

    <script type="Text/javascript" src="data/kecamatan.json"></script>
    <!-- File JSON dengan data polygon kecamatan -->
    <script>
    // Initialize the map centered at a specific latitude and longitude (Banyumas)
    const map = L.map('map').setView([-7.4501619925610265, 109.16218062235065], 11);

    // Tile layer from OpenStreetMap
    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);


    // Load GeoJSON data for Kecamatan (Polygons) from PHP
    //    L.geoJSON('data/kecamatan.json')
    //      .then(response => response.json())
    //     .then(data => {
    //         L.geoJSON(data, {
    //             style: function(feature) {
    //                 return {
    //                      color: 'blue',
    //                      weight: 2
    //                  };
    //               }
    //            }).addTo(map);
    //        })
    //        .catch(error => console.error('Error loading GeoJSON data:', error));

    //Polygon Kecamatan
    L.geoJSON(kecamatan, {
        style: function(feature) {
            return {
                color: 'blue',
                weight: 2
            };
        }
    }).addTo(map);

    // Add markers for Health Services (from PHP)
    const healthServices = <?php echo json_encode($health_services); ?>;
    healthServices.forEach(service => {
        L.marker([service.latitude, service.longitude]).addTo(map)
            .bindPopup('<b>' + service.name + '</b>')
            .openPopup();
    });
    </script>

</body>

</html>