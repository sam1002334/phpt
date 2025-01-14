// Handle click event on the camera icon
document.getElementById('camera-icon').addEventListener('click', function () {
    document.getElementById('file-input').click(); // Trigger file input when the camera icon is clicked
});

// Handle file input change
document.getElementById('file-input').addEventListener('change', function (event) {
    const file = event.target.files[0]; // Get the selected file
    if (file) {
        const formData = new FormData();
        formData.append('file', file);

        // Make a POST request to upload.php
        fetch('upload.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the profile image dynamically
                    const profileImg = document.getElementById('profile-img');
                    profileImg.src = data.profile_pic; // Set the new profile picture path

                    // Update the background image for the container
                    const profilePicContainer = document.getElementById('profile-pic');
                    profilePicContainer.style.backgroundImage = `url('${data.profile_pic}')`;
                } else {
                    alert('Failed to upload profile photo: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error uploading photo:', error);
            });
    }
});
