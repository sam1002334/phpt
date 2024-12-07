document.getElementById('profile-photo').addEventListener('click', function () {
    const profileMenu = document.getElementById('profile-menu');
    profileMenu.style.display = profileMenu.style.display === 'none' || profileMenu.style.display === '' ? 'block' : 'none';
});

document.querySelector('.settings-button').addEventListener('click', function () {
    document.getElementById('file-input').click();
});


document.getElementById('file-input').addEventListener('change', function (event) {
    const file = event.target.files[0];
    if (file) {
        const formData = new FormData();
        formData.append('file', file);

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
                    alert('Failed to upload profile photo: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error uploading photo:', error);
            });
    }
});

document.querySelector('.logout-button').addEventListener('click', function () {
    window.location.href = 'logout.php';
});
