<?php
    session_start();
?>

<div class="container-fluid py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center">

          <h3 class="fw-bold" style="color: rgb(0,202,202);">
            Manual Attendance Punch
          </h3>
          <p class="text-muted mb-4">
            Capture your image and location to mark attendance.
          </p>

          <!-- Live Camera -->
          <div class="position-relative border rounded mb-3" style="height:480px;">
            <video id="cameraPreview" autoplay playsinline 
                    style="width:100%;height:100%;object-fit:cover;border-radius:6px;"></video>
            <div id="cameraMessage"
                 class="position-absolute top-50 start-50 translate-middle text-danger fw-semibold bg-white rounded px-3 py-2"
                 style="display:none;"></div>
          </div>

          <!-- Buttons -->
          <button id="captureBtn" class="btn text-white w-100"
                  style="background-color:rgb(0,202,202);font-weight:600;">
            Capture Attendance
          </button>

          <canvas id="snapshotCanvas" style="display:none;"></canvas>

          <div class="mt-3 text-muted small">
            <span id="locationStatus">Location: <em>Not captured</em></span>
          </div>

          <div id="statusMessage" class="text-success fw-semibold mt-3"></div>

        </div>
      </div>
    </div>
  </div>
</div>
          <div><?php// print_r($_SESSION) ?></div>
