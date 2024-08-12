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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
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
                <h4 class="page-title pull-left">{{ __('Equipment Record Audio') }}</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li><span>{{ __('Record Audio') }}</span></li>
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
                    <h4 class="header-title float-left">{{ __('Record Audio') }}</h4>
                    <!-- Map Container -->
                        <button id="start-recording">Start Recording</button>
                        <button id="stop-recording" disabled>Stop Recording</button>
                        <button id="upload-audio" disabled>Upload Audio</button>
                        <audio id="audio-preview" controls></audio>
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
        const uploadUrl = "{{ route('admin.equipment.uploadAudio') }}";
        let mediaRecorder;
        let audioBlob;
        document.getElementById('start-recording').addEventListener('click', () => {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream);
                    mediaRecorder.start();

                    mediaRecorder.ondataavailable = event => {
                        if (event.data.size > 0) {
                            audioBlob = event.data;
                            document.getElementById('audio-preview').src = URL.createObjectURL(audioBlob);
                            document.getElementById('upload-audio').disabled = false;
                        }
                    };

                    document.getElementById('stop-recording').disabled = false;
                    document.getElementById('start-recording').disabled = true;
                })
                .catch(error => console.error('Error accessing media devices.', error));
        });

        document.getElementById('stop-recording').addEventListener('click', () => {
            if (mediaRecorder) {
                mediaRecorder.stop();
                document.getElementById('start-recording').disabled = false;
                document.getElementById('stop-recording').disabled = true;
            }
        });

        document.getElementById('upload-audio').addEventListener('click', () => {
            //console.log(audioBlob);
            if (!audioBlob) {
                alert('No audio to upload');
                return;
            }
            const formData = new FormData();
            formData.append('audio', audioBlob, 'recording.mp3'); // Change the file name and extension as needed

            axios.post(uploadUrl, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                console.log('Audio uploaded successfully:', response.data);
                alert('Audio uploaded successfully');
            })
            .catch(error => {
                console.error('Error uploading audio:', error.response.data);
                alert('Error uploading audio');
            });
        });
    </script>

@endsection

