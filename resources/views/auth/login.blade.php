<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin · El-Habib Plumbing Material and Services Ltd</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gradient-to-r from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Login</h2>
        <form id="loginForm">
            @csrf
            <div class="mb-6 relative">
                <label for="identifier" class="block mb-2 text-sm font-medium text-gray-600">Email or Phone Number</label>
                <div class="flex items-center border rounded-md">
                    <span class="px-3 text-gray-500"><i class="fas fa-envelope"></i></span>
                    <input type="text" id="identifier" name="identifier" class="w-full py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your email or phone number">
                </div>
            </div>
            <div class="mb-6 relative">
                <label for="password" class="block mb-2 text-sm font-medium text-gray-600">Password</label>
                <div class="flex items-center border rounded-md">
                    <span class="px-3 text-gray-500"><i class="fas fa-lock"></i></span>
                    <input type="password" id="password" name="password" class="w-full py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your password">
                    <button type="button" class="px-3 text-gray-500 focus:outline-none" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
            <div class="flex items-center mb-6">
                <input type="checkbox" id="remember" name="remember" class="mr-2">
                <label for="remember" class="text-sm text-gray-600">Keep me logged in</label>
            </div>
            <button type="submit" id="loginBtn" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition duration-300">
                <span id="loginBtnText">Login</span>
                <span id="loginBtnSpinner" class="hidden">
                    <i class="fas fa-spinner fa-spin"></i> Loading...
                </span>
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
        </div>
    </div>
    <footer class="absolute bottom-4 text-center w-full text-white text-sm">
        &copy; 2024 El-Habib Plumbing Material and Services Ltd. All rights reserved.
    </footer>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>

<script>
  $(document).ready(function() {
      $('#loginForm').on('submit', function(e) {
          e.preventDefault();
          
          $('#loginBtn').prop('disabled', true);
          $('#loginBtnText').addClass('hidden');
          $('#loginBtnSpinner').removeClass('hidden');

          $.ajax({
              url: '/login',
              type: 'POST',
              data: $(this).serialize(),
              success: function(response) {
                  if (response.success) {
                      Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'You have been logged in successfully.',
                          timer: 1500,
                          showConfirmButton: false
                      }).then(() => {
                          window.location.href = response.redirect;
                      });
                  }
              },
              error: function(xhr) {
                  let errorMessage = 'An error occurred. Please try again.';

                  if (xhr.status === 422) {
                      // Validation errors
                      const errors = xhr.responseJSON.errors;
                      errorMessage = Object.values(errors).flat().join('<br>');
                  } else if (xhr.status === 401) {
                      // Invalid credentials
                      errorMessage = xhr.responseJSON.message;
                  }

                  Swal.fire({
                      icon: 'error',
                      title: 'Error!',
                      html: errorMessage
                  });
              },
              complete: function() {
                  $('#loginBtn').prop('disabled', false);
                  $('#loginBtnText').removeClass('hidden');
                  $('#loginBtnSpinner').addClass('hidden');
              }
          });
      });
  });
</script>
</body>
</html>