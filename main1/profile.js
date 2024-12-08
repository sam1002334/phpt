// Trigger the file input when settings button is clicked
document.querySelector('.settings-button').addEventListener('click', function () {
    document.getElementById('file-input').click();
});

// Handle the selected image file
document.getElementById('file-input').addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const imageUrl = e.target.result;

            // Show image preview and initialize Cropper.js
            const previewContainer = document.getElementById('image-preview-container');
            const previewImage = document.getElementById('image-preview');
            previewImage.src = imageUrl;
            previewContainer.style.display = 'block';

            // Initialize Cropper.js
            const cropper = new Cropper(previewImage, {
                aspectRatio: 1, // Set the aspect ratio (1:1 for square)
                viewMode: 1, // Crop within the image container
                minContainerWidth: 200, // Min width of the cropper container
                minContainerHeight: 200, // Min height of the cropper container
                cropBoxResizable: true, // Enable resizing of the crop box
            });

            // Handle the cropping action
            document.getElementById('upload-cropped-photo').addEventListener('click', function () {
                const croppedCanvas = cropper.getCroppedCanvas();
                croppedCanvas.toBlob(function (blob) {
                    const formData = new FormData();
                    formData.append('file', blob, 'cropped-photo.jpg');

                    // Send the cropped image to the server
                    fetch('upload_profile_photo.php', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('profile-img').src = 'uploads/' + data.fileName;
                            document.getElementById('profile-menu-img').src = 'uploads/' + data.fileName;
                        } else {
                            alert('Failed to upload cropped photo: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error uploading photo:', error);
                    });
                });
            });
        };
        reader.readAsDataURL(file); // Read the file as data URL for preview
    }
});

// Close profile menu when clicking anywhere outside
document.addEventListener('click', function (event) {
    const profileMenu = document.getElementById('profile-menu');
    const profilePhoto = document.getElementById('profile-photo');
    
    if (!profilePhoto.contains(event.target) && !profileMenu.contains(event.target)) {
        profileMenu.style.display = 'none';
    }
});

// Other event listeners for profile settings, logout, etc.
document.querySelector('.logout-button').addEventListener('click', function () {
    window.location.href = 'logout.php';
});

// Toggle details visibility
document.getElementById('details-button').addEventListener('click', function () {
    const details = document.getElementById('profile-details');
    if (details.style.display === 'block') {
        details.style.display = 'none';
        this.textContent = 'Show Details';
    } else {
        details.style.display = 'block';
        this.textContent = 'Hide Details';
    }
});
