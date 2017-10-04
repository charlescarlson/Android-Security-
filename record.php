<?php
$cameras = scandir(getcwd() . "/video/");
foreach ($cameras as $camera) {
    if ($camera !== ".." && $camera !== ".") {
?>
<button value="<?= $camera?>" onclick="camera(this)"><?= $camera ?></button>
<?php
	}
}
?>
<script>
	var context,
		streamRecorder,
		webcam,
		selectedCamera;
	var width = 640;
	var height = 480;
	//var motion = 0;
	var sendRecording = false;
	var options = {
		mimeType: 'video/webm;codecs=vp9',
		audioBitsPerSecond : 12000,
		videoBitsPerSecond : 150000  };
	var chunks = [];
	var video = document.createElement('video');
	video.autoplay = true;
	video.muted = true;

	function newContext() {
		var canvas = document.createElement('canvas');
		canvas.width = width;
		canvas.height = height;
		context = canvas.getContext('2d');
	}

	function camera(selection) {
		selectedCamera = selection.value;
	}

	newContext();

	function dataAvailable(event) {
		streamRecorder.ondataavailable = null;
		newRecorder();
		if (!sendRecording || !selectedCamera)
			return;
		sendRecording = false;
		chunks.push(event.data);
		var blob = new Blob(chunks, { 'type' : 'video/webm' });
		var formData = new FormData();
		formData.append("video", blob);
		formData.append("location", selectedCamera);
		var xhr = new XMLHttpRequest();
		xhr.open('POST', '/security.php');
		xhr.send(formData);
		chunks = [];
	}

	function request() {
		streamRecorder.stop();
	}

	function newRecorder() {
		streamRecorder = new MediaRecorder(webcam, options);
		streamRecorder.ondataavailable = dataAvailable;
		streamRecorder.start();
	}

	function capture() {
		context.globalCompositeOperation = 'difference';
		draw(video);
		//var imageData = context.getImageData(0, 0, width, height);
		//var rgba = imageData.data;
		//var count = 0;
		/*for (var i = 0; i < rgba.length; i += 4) {
			if ((rgba[i] + rgba[i + 1] + rgba[i + 2]) > 96) {
				count++;
				if (count > 1600) {
					break;
				}
			}
		}*/
		//newContext();

		//context.globalCompositeOperation = 'source-over';
		draw(video);
		/*if (count > 1600) {
			if (motion < 12)
				motion++;
			else {
				sendRecording = true;
			}
		} else if (motion > 0) {
			motion--;
		}*/
		sendRecording = true;
	}

	function draw(video) {
		context.drawImage(video, 0, 0, width, height);
	}

	function success(stream) {
		video.srcObject = stream;
		webcam = stream;
		newRecorder();
		setInterval(request, 60000);
		setInterval(capture, 400);
	}

	function error(error) {
		console.log(error);
	}

	navigator.mediaDevices.getUserMedia({audio: true, video: { facingMode: "environment" }}).then(success).catch(error);
</script>