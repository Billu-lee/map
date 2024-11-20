<!-- index.php -->
<!DOCTYPE html>
<html>

<head>
    <title>Databenki GPS</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
        #map {
            height: 100vh;
            width: 100vw;
            position: absolute;
            top: 0;
            left: 0;
            z-index: 0;
        }

        #button-container {
            position: absolute;
            bottom: 10px;
            left: 5%;
            z-index: 1;
            display: flex;
        }

        button{
            box-shadow: 1px 1px 2px black;
        }
    </style>
</head>

<body>
    <div id="map"></div>
    <div id="button-container" class="row text-center">
        <div class="col-3">
            <button id="add-point" class="btn btn-secondary rounded-pill">Pin</button>
        </div>
        <div class="col-3">
            <button id="draw-polygon" class="btn btn-secondary  rounded-pill">Draw</button>
        </div>
        <div class="col-3">
            <button id="reset" class="btn btn-secondary rounded-pill">Reset</button>
        </div>
        <div class="col-3">
            <button id="sendBtn" class="btn btn-secondary rounded-pill">Send</button>
        </div>
    </div>


    <!-- Modal for the Form -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="formModalLabel">Submit Your Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="detailsForm" novalidate>
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                placeholder="Enter your full name"
                                required>
                            <div class="invalid-feedback">Please enter your full name.</div>
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                class="form-control"
                                placeholder="Enter your phone number"
                                pattern="^[0-9]{10,15}$"
                                required>
                            <div class="invalid-feedback">Please enter a valid phone number (10-15 digits).</div>
                        </div>
                        <div class="mb-3">
                            <label for="placeName" class="form-label">Place Name</label>
                            <input
                                type="text"
                                class="form-control"
                                id="place"
                                name="place"
                                placeholder="Enter the place name"
                                required>
                            <div class="invalid-feedback">Please enter the place name.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" id="save-data">Save Data</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        // Initialize the map
        var map = L.map('map').setView([-6.509, 38.08], 20); // Zoom set to a reasonable level

        // Add Esri Satellite tile layer
        const esriTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.esri.com/">Esri</a>, Earthstar Geographics'
        }).addTo(map);

        //==================================================
        //FOR ESRI MAP SERVICE CHANGE THE LINK  https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
        // TO       https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}
        //==================================================


        // Try to locate the user quickly
        let currentLatLng = null;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const {
                    latitude,
                    longitude
                } = position.coords;
                currentLatLng = [latitude, longitude];

                // Center the map to the user's location
                map.setView(currentLatLng, 20); // Close zoom level for accurate view
            },
            (error) => {
                console.error('Geolocation error:', error);
                alert('Unable to retrieve your location. Showing default view.');
            }, {
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
            L.polygon(cornerPoints, {
                color: 'blue'
            }).addTo(map);
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


        // ajax to send data to php 
        document.getElementById('save-data').addEventListener('click', () => {
            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const place = document.getElementById('place').value;

            if (!name || !phone || !place) {
                alert('Please provide client details.');
                return;
            }

            if (cornerPoints.length < 1) {
                alert('At least 1 points are needed to save.');
                return;
            }

            // Kutuma Data kwenda PHP script i.e. save_data.php kwa kutumia JSON
            const data = {
                name,
                phone,
                place,
                cornerPoints,
            };

            fetch('save_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
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


        //code za kuvalidate form
        document.getElementById('detailsForm').addEventListener('submit', function(event) {
            const form = this;

            // Check if form is valid
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            // Add Bootstrap validation class
            form.classList.add('was-validated');
        });
    </script>
</body>
</html>