let page = 1;
const userList = document.getElementById('user-list');
const errorMessages = document.getElementById('error-messages');
const showMoreButton = document.getElementById('show-more');
const loader = document.getElementById('loader');

// Fetch and display users
function fetchUsers() {
    axios.get(`/api/users?page=${page}`)
        .then(response => {
            if (response.data && response.data.data) {
                response.data.data.forEach(user => {
                    const userItem = document.createElement('div');
                    userItem.classList.add('col-md-4');
                    userItem.innerHTML = `
                        <div class="card">
                            <img src="/images/optimized/${user.image}" class="card-img-top img-fluid" alt="${user.name}" style="max-height: 200px; object-fit: cover;">
                            <div class="card-body text-center">
                                <h5 class="card-title">${user.name}</h5>
                                <p class="card-text">${user.email}</p>
                            </div>
                        </div>`;
                    userList.appendChild(userItem);
                });

                // Hide "Show More" button if there are no more pages
                if (!response.data.next_page_url) {
                    showMoreButton.style.display = 'none';
                }
            } else {
                console.error('Unexpected response structure:', response.data);
            }
        })
        .catch(error => {
            console.error('Error fetching users:', error);
        });
}

// Show more users
showMoreButton.addEventListener('click', function () {
    page++;
    fetchUsers();
});

// Add new user
document.getElementById('add-user-form').addEventListener('submit', function (e) {
    e.preventDefault();
    errorMessages.innerHTML = '';  // Clear previous errors
    loader.style.display = 'block';  // Show loader

    const formData = new FormData();
    formData.append('name', document.getElementById('name').value);
    formData.append('email', document.getElementById('email').value);
    formData.append('password', document.getElementById('password').value);
    formData.append('image', document.getElementById('image').files[0]);

    axios.post('/api/users', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // CSRF token
        }
    })
        .then(response => {
            alert('User added successfully');
            document.getElementById('add-user-form').reset(); // Clear form fields
            fetchUsers(); // Refresh the user list
        })
        .catch(error => {
            if (error.response && error.response.data && error.response.data.errors) {
                const errors = error.response.data.errors;
                Object.keys(errors).forEach(function (key) {
                    errorMessages.innerHTML += `<p>${errors[key][0]}</p>`;
                });
            } else {
                console.error('Error adding user:', error);
                errorMessages.innerHTML = 'Failed to add user. Please try again.';
            }
        })
        .finally(() => {
            loader.style.display = 'none';  // Hide loader
        });
});

// Initial load
fetchUsers();
