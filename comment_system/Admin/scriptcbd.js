document.addEventListener("DOMContentLoaded", function () {
    console.log("Attempting to fetch dashboard data...");
    console.log("Script loaded successfully!");

    fetch('dashboard_data.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            document.querySelector('#total-comments').textContent = data.total_comments;
            document.querySelector('#total-deleted-comments').textContent = data.total_deleted_comments;
        })
        .catch(error => {
            console.error('Error fetching comment data:', error);
        });
});
