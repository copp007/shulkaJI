@extends('backend.layouts.master')

@section('title')
{{ __('Equipment Map') }}
@endsection

@section('styles')
    <!-- Start datatable css -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">
    <!-- Add custom styles for the map -->
    <style>
        #map {
            height: 500px; /* Adjust the height as needed */
            width: 100%; /* Make map full width */
        }
        .page-title-area, .main-content-inner {
            margin-bottom: 20px; /* Ensure spacing around the map */
        }
    </style>
@endsection

@section('admin-content')
<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">{{ __('Equipment Map') }}</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><span>{{ __('All Equipment on Map') }}</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            @include('backend.layouts.partials.logout')
        </div>
    </div>
</div>
<!-- page title area end -->

<div class="main-content-inner">
    <div class="row">
        <!-- data table start -->
        <div class="col-12 mt-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title float-left">{{ __('Equipment Map') }}</h4>
                    <!-- Map Container -->
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <!-- data table end -->
    </div>
</div>
@endsection

@section('scripts')
    <!-- Start datatable js -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>
    
    <script>
        let map;

        function initMap() {
            console.log('Initializing map...'); // Debugging line
            
            const equipments = @json($equipments);
            const basePlayToneUrl = "{{ route('admin.equipment.playTone') }}";
            const geocoder = new google.maps.Geocoder();
            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: { lat: 20.5937, lng: 78.9629 }, // Default center (India)
            });

            function geocodeAndAddMarker(equipment) {
                if (!equipment.location) {
                    console.error('No location data for equipment:', equipment);
                    return;
                }

                geocoder.geocode({ address: equipment.location }, (results, status) => {
                    if (status === 'OK') {
                        const location = results[0].geometry.location;

                        if (map.getCenter().lat() === 20.5937 && map.getCenter().lng() === 78.9629) {
                            map.setCenter(location);
                        }

                        const marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            title: equipment.equipment_name,
                        });

                        marker.addListener('click', () => {
                            new google.maps.InfoWindow({
                                content: `
                                    <div>
                                        <h3>${equipment.equipment_name}</h3>
                                        <p>District: ${equipment.district}</p>
                                        <p>Taluk: ${equipment.taluk}</p>
                                        <p>Location: ${equipment.location}</p>
                                        <p><a href="${basePlayToneUrl}?id=${equipment.id}" target="_blank">Play Tone</a></p>
                                    </div>
                                `
                            }).open(map, marker);
                        });

                        // Draw boundary of the location
                        drawBoundary(results[0].geometry.bounds);
                    } else {
                        console.error('Geocode was not successful for the following reason: ' + status);
                    }
                });
            }

            function drawBoundary(bounds) {
                if (!bounds) return;
                
                const coordinates = [
                    { lat: bounds.getNorthEast().lat(), lng: bounds.getNorthEast().lng() },
                    { lat: bounds.getNorthEast().lat(), lng: bounds.getSouthWest().lng() },
                    { lat: bounds.getSouthWest().lat(), lng: bounds.getSouthWest().lng() },
                    { lat: bounds.getSouthWest().lat(), lng: bounds.getNorthEast().lng() }
                ];

                const polygon = new google.maps.Polygon({
                    paths: coordinates,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                    map: map
                });

                polygon.addListener('click', () => {
                    // Handle click event if needed
                });
            }

            equipments.forEach(equipment => {
                geocodeAndAddMarker(equipment);
            });
        }

        function loadGoogleMaps() {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCWlCD6WDkKYYnWwdy5WvG3wkqCNmx5y4c&callback=initMap&libraries=places,geometry&v=beta`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        }

        loadGoogleMaps();
    </script>
@endsection
