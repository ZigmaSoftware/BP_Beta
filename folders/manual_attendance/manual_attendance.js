let stream = null;
let lastFrameSample = null;
let lastFrameTime = 0;
let currentLatitude = null;
let currentLongitude = null;

// ---------- CAMERA INITIALIZATION ----------
async function startCamera() {
  const video = document.getElementById("cameraPreview");
  const message = document.getElementById("cameraMessage");

  try {
    stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    message.style.display = "none";
  } catch (err) {
    message.textContent =
      "Camera access blocked. Please enable camera permission.";
    message.style.display = "block";
  }
}

// ---------- CAMERA DARKNESS / SHUTTER / STATIC DETECTION ----------
async function isCameraBlocked(video) {
  if (!video.videoWidth || !video.videoHeight) return true;

  const canvas = document.createElement("canvas");
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const ctx = canvas.getContext("2d");
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
  const frame = ctx.getImageData(0, 0, canvas.width, canvas.height).data;

  // Brightness and variance
  let brightnessSum = 0;
  const grayVals = [];
  for (let i = 0; i < frame.length; i += 4) {
    const g = (frame[i] + frame[i + 1] + frame[i + 2]) / 3;
    grayVals.push(g);
    brightnessSum += g;
  }

  const mean = brightnessSum / grayVals.length;
  const variance =
    grayVals.reduce((a, b) => a + Math.pow(b - mean, 2), 0) / grayVals.length;

  const isDark = mean < 35;
  const isUniform = variance < 800;

  // Frame difference check (detect frozen or static image)
  let frameDiff = 0;
  if (lastFrameSample) {
    for (let i = 0; i < frame.length; i += 16) {
      frameDiff += Math.abs(frame[i] - lastFrameSample[i]);
    }
    frameDiff /= frame.length / 16;
  }
  const isFrozen = frameDiff < 2;

  // Update last sample periodically
  const now = performance.now();
  if (now - lastFrameTime > 500) {
    lastFrameSample = frame.slice(0);
    lastFrameTime = now;
  }

  // Combined logic
  return (isDark && isUniform) || isFrozen;
}

// ---------- FACE DETECTION (Native + Fallback) ----------
async function detectFace(video) {
  // 1️⃣ Native FaceDetector API
  if ("FaceDetector" in window) {
    try {
      const detector = new FaceDetector({ fastMode: true });
      const faces = await detector.detect(video);
      if (faces.length > 0) return true;
      console.warn("FaceDetector found 0 faces; fallback check engaged.");
    } catch (err) {
      console.warn("FaceDetector error:", err);
    }
  }

  // 2️⃣ Fallback heuristic — brightness and contrast difference
  try {
    const canvas = document.createElement("canvas");
    const w = (canvas.width = 160);
    const h = (canvas.height = 120);
    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0, w, h);
    const data = ctx.getImageData(0, 0, w, h).data;

    let centerSum = 0,
      edgeSum = 0,
      centerCount = 0,
      edgeCount = 0;

    for (let y = 0; y < h; y++) {
      for (let x = 0; x < w; x++) {
        const i = (y * w + x) * 4;
        const g = (data[i] + data[i + 1] + data[i + 2]) / 3;
        const dx = Math.abs(x - w / 2);
        const dy = Math.abs(y - h / 2);
        const dist = Math.sqrt(dx * dx + dy * dy);

        if (dist < w / 4) {
          centerSum += g;
          centerCount++;
        } else if (dist > w / 3) {
          edgeSum += g;
          edgeCount++;
        }
      }
    }

    const centerMean = centerSum / centerCount;
    const edgeMean = edgeSum / edgeCount;
    const contrast = Math.abs(centerMean - edgeMean);

    // Faces usually cause mid brightness + higher center contrast
    return contrast > 15 && centerMean > 40 && centerMean < 200;
  } catch (e) {
    console.warn("Fallback face check failed:", e);
    return true; // Don’t block if something breaks
  }
}

// ---------- LOCATION REQUEST ----------
function requestLocation() {
  const locStatus = document.getElementById("locationStatus");

  if (!navigator.geolocation) {
    locStatus.textContent = "Location: Not supported by your browser.";
    return;
  }

  navigator.geolocation.getCurrentPosition(
    (pos) => {
      currentLatitude = pos.coords.latitude.toFixed(5);
      currentLongitude = pos.coords.longitude.toFixed(5);
      locStatus.textContent = `Location: ${currentLatitude}, ${currentLongitude}`;
    },
    (err) => {
      currentLatitude = null;
      currentLongitude = null;
      if (err.code === err.PERMISSION_DENIED) {
        locStatus.innerHTML =
          "Location permission denied. Please allow access from your browser settings.";
      } else {
        locStatus.innerHTML = "Unable to fetch location. Please enable GPS.";
      }
    }
  );
}

// ---------- ATTENDANCE CAPTURE ----------
async function captureAttendance() {
  const video = document.getElementById("cameraPreview");
  const canvas = document.getElementById("snapshotCanvas");
  const message = document.getElementById("cameraMessage");
  const statusMessage = document.getElementById("statusMessage");
  const locStatus = document.getElementById("locationStatus");

  if (!stream) {
    message.textContent = "Camera not active. Please allow camera access.";
    message.style.display = "block";
    return;
  }

  // 2️⃣ Face presence check
  const faceFound = await detectFace(video);
  if (!faceFound) {
    message.textContent = "No face detected. Please look into the camera.";
    message.style.display = "block";
    return;
  }

  // 3️⃣ Ensure location fetched
  if (!currentLatitude || !currentLongitude) {
    message.textContent =
      "Location not detected. Please enable GPS or move to an open area.";
    message.style.display = "block";
    requestLocation(); // attempt refresh
    return;
  }

  // 4️⃣ Capture snapshot
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  const ctx = canvas.getContext("2d");
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

  const imageData = canvas.toDataURL("image/jpeg");
      console.info(imageData);
    console.info(currentLatitude);
    console.info(currentLongitude);
  message.style.display = "none";
  statusMessage.textContent =
    "Attendance captured successfully! (Ready for upload)";



  // 5️⃣ Submit to backend (AJAX example)

  $.ajax({
    url: "folders/manual_attendance/crud.php",
    type: "POST",
    data: {
      action: "createupdate",
      image: imageData,
      latitude: currentLatitude,
      longitude: currentLongitude,
    },
    success: function (res) {
      console.log(res);
      statusMessage.textContent = "Attendance submitted successfully.";
    },
    error: function () {
      statusMessage.textContent = "Error submitting attendance.";
    },
  });

}

// ---------- PAGE INITIALIZATION ----------
document.addEventListener("DOMContentLoaded", () => {
  startCamera();
  requestLocation();
  document
    .getElementById("captureBtn")
    .addEventListener("click", captureAttendance);
});
