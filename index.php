<!DOCTYPE html>
<html>

<head>
    <title>Fast Esri Satellite Map</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>
    <!-- Link to Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <h1>Mark Your Field Corners....</h1>
    <div id="map" style="height: 500px;"></div>
    <button id="add-point">Pin</button>
    <button id="draw-polygon">Draw</button>
    <button id="reset">Reset</button>
    <button id="sendBtn">Send</button>


    <!-- Modal for the Form -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Submit Your Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="detailsForm">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" required>

                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input type="text" id="phone" name="phone" required>

                        </div>
                        <div class="mb-3">
                            <label for="placeName" class="form-label">Place Name</label>
                            <input type="text" class="form-control" id="placeName" placeholder="Enter your place name"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary" id="save-data">Save Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        // Default fallback location if geolocation fails
        const defaultMapState = {
            lat: -1.2921, // Example: Nairobi, Kenya
            lng: 36.8219,
            zoom: 20
        };

        // Initialize map with fallback view
        const map = L.map('map').setView([defaultMapState.lat, defaultMapState.lng], defaultMapState.zoom);

        // Add Esri Satellite tile layer
        const esriTiles = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; <a href="https://www.esri.com/">Esri</a>, Earthstar Geographics'
        }).addTo(map);

        // Try to locate the user quickly
        let currentLatLng = null;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;
                currentLatLng = [latitude, longitude];

                // Center the map to the user's location
                map.setView(currentLatLng, 20); // Close zoom level for accurate view
            },
            (error) => {
                console.error('Geolocation error:', error);
                alert('Unable to retrieve your location. Showing default view.');
            },
            {
                enableHighAccuracy: true, // High accuracy for better positioning
                timeout: 5000, // Short timeout for faster response
                maximumAge: 0 // Avoid stale data
            }
        );

        // Add Locate Control for manual location recentering
        L.control.locate({
            position: 'topright',
            drawCircle: true,
            follow: false,
            setView: true,
            flyTo: false,
            keepCurrentZoomLevel: true,
            markerStyle: {
                color: 'blue',
                fillColor: '#007bff',
                fillOpacity: 0.6,
                weight: 2
            },
            circleStyle: {
                color: 'blue',
                fillColor: '#007bff',
                fillOpacity: 0.2
            },
            locateOptions: {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        }).addTo(map);

        // Array to store corner points
        let cornerPoints = [];

        // Add point button functionality
        document.getElementById('add-point').addEventListener('click', () => {
            if (!currentLatLng) {
                alert('Unable to get your current location.');
                return;
            }

            cornerPoints.push(currentLatLng);

            // Add marker to the map
            L.marker(currentLatLng).addTo(map).bindPopup(`Corner Point ${cornerPoints.length}`).openPopup();
        });

        // Draw polygon button functionality
        document.getElementById('draw-polygon').addEventListener('click', () => {
            if (cornerPoints.length < 3) {
                alert('You need at least 3 points to create a polygon.');
                return;
            }

            // Draw the polygon
            L.polygon(cornerPoints, { color: 'blue' }).addTo(map);
        });

        // Reset button functionality
        document.getElementById('reset').addEventListener('click', () => {
            cornerPoints = [];
            map.eachLayer((layer) => {
                if (!layer._url) {
                    map.removeLayer(layer);
                }
            });

            // Reload Esri Satellite tiles
            esriTiles.addTo(map);
        });

        // Handle Send Button 
        document.getElementById('sendBtn').addEventListener('click', () => {
            const formModal = new bootstrap.Modal(document.getElementById('formModal'));
            formModal.show();
        });


        // ajax to sendata to php 
        document.getElementById('save-data').addEventListener('click', () => {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;

            if (!name || !phone) {
                alert('Please provide client details.');
                return;
            }

            if (cornerPoints.length < 1) {
                alert('At least 3 points are needed to save.');
                return;
            }

            const data = {
                name,
                phone,
                cornerPoints,
            };

            fetch('save_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            })
                .then((response) => response.json())
                .then((result) => {
                    if (result.success) {
                        alert('Data saved successfully.');
                    } else {
                        alert(`Error: ${result.message}`);
                    }
                })
                .catch((error) => console.error('Error:', error));
        });


    </script>
</body>

</html>