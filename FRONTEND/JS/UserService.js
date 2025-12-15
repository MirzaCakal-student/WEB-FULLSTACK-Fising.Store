// UserService.js - Complete user profile management
var UserService = {
    currentUser: null,

    init: function() {
        this.loadProfile();
        this.initProfileForm();
        this.initPasswordForm();
        this.initEmailCheck();
        this.initPasswordStrength();
    },

    loadProfile: function() {
        const user = Utils.getCurrentUser();
        if (!user) {
            window.location.hash = "#login";
            return;
        }

        this.currentUser = user;

        // Split username into first and last name
        const nameParts = (user.username || '').split(' ');
        const firstName = nameParts[0] || '';
        const lastName = nameParts.slice(1).join(' ') || '';

        // Fill form fields
        $("#userId").val(user.user_id);
        $("#userFirstName").val(firstName);
        $("#userLastName").val(lastName);
        $("#userEmail").val(user.email);

        // Update header
        $("#profileUserName").text(user.username);
        $("#profileUserEmail").text(user.email);
        
        // Update session info
        $("#sessionEmail").text(user.email);
        $("#sessionRole").text(user.role).removeClass().addClass('badge').addClass(
            user.role === Constants.ADMIN_ROLE ? 'bg-danger' : 'bg-info'
        );
    },

    initProfileForm: function() {
        $("#userProfileForm").on("submit", function(e) {
            e.preventDefault();
            UserService.updateProfile();
        });
    },

    initPasswordForm: function() {
        $("#changePasswordForm").on("submit", function(e) {
            e.preventDefault();
            UserService.changePassword();
        });
    },

    initEmailCheck: function() {
        let emailCheckTimeout;
        
        $("#userEmail").on("input", function() {
            const email = $(this).val();
            const currentEmail = UserService.currentUser.email;
            
            // Clear previous timeout
            clearTimeout(emailCheckTimeout);
            
            // Remove previous feedback
            $(this).removeClass("is-valid is-invalid");
            $(".email-feedback").hide();
            
            // Don't check if same as current email
            if (email === currentEmail) {
                $(this).addClass("is-valid");
                return;
            }
            
            // Don't check if invalid format
            if (!email || !email.includes('@')) {
                return;
            }
            
            // Debounce - wait 500ms after typing stops
            emailCheckTimeout = setTimeout(function() {
                UserService.checkEmailAvailability(email);
            }, 500);
        });
    },

    checkEmailAvailability: function(email) {
        // Check if email exists in database
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "users",
            type: "GET",
            headers: {
                "Authentication": localStorage.getItem("user_token")
            },
            success: function(response) {
                if (response.success && response.data) {
                    // Check if any user has this email (excluding current user)
                    const currentUserId = UserService.currentUser.user_id;
                    const emailTaken = response.data.some(u => 
                        u.email === email && u.user_id != currentUserId
                    );
                    
                    if (emailTaken) {
                        $("#userEmail").addClass("is-invalid").removeClass("is-valid");
                        $(".email-feedback").text("❌ Email already taken").show().css("color", "red");
                    } else {
                        $("#userEmail").addClass("is-valid").removeClass("is-invalid");
                        $(".email-feedback").text("✓ Email available").show().css("color", "green");
                    }
                } else {
                    // On error, just mark as valid
                    $("#userEmail").addClass("is-valid").removeClass("is-invalid");
                }
            },
            error: function() {
                // On error, just mark as valid
                $("#userEmail").addClass("is-valid").removeClass("is-invalid");
            }
        });
    },

    initPasswordStrength: function() {
        $("#newPassword").on("input", function() {
            const password = $(this).val();
            UserService.updatePasswordStrength(password);
        });
    },

    updatePasswordStrength: function(password) {
        let strength = 0;
        let strengthText = "None";
        let strengthColor = "bg-secondary";

        if (password.length >= 6) strength += 25;
        if (password.length >= 10) strength += 15;
        if (/[a-z]/.test(password)) strength += 15;
        if (/[A-Z]/.test(password)) strength += 15;
        if (/[0-9]/.test(password)) strength += 15;
        if (/[^a-zA-Z0-9]/.test(password)) strength += 15;

        if (strength === 0) {
            strengthText = "None";
            strengthColor = "bg-secondary";
        } else if (strength < 40) {
            strengthText = "Weak";
            strengthColor = "bg-danger";
        } else if (strength < 70) {
            strengthText = "Medium";
            strengthColor = "bg-warning";
        } else {
            strengthText = "Strong";
            strengthColor = "bg-success";
        }

        $("#passwordStrengthBar")
            .css("width", strength + "%")
            .removeClass("bg-secondary bg-danger bg-warning bg-success")
            .addClass(strengthColor);
        
        $("#passwordStrengthText").text(`Password strength: ${strengthText}`);
    },

    updateProfile: function() {
        const firstName = $("#userFirstName").val().trim();
        const lastName = $("#userLastName").val().trim();
        const email = $("#userEmail").val().trim();
        const userId = $("#userId").val();

        if (!firstName || !lastName || !email) {
            toastr.error("All fields are required");
            return;
        }

        $.blockUI({ message: '<h3>Updating profile...</h3>' });

        const updateData = {
            username: `${firstName} ${lastName}`,
            email: email
        };

        RestClient.put(`users/${userId}`, updateData, function(response) {
            $.unblockUI();
            
            if (response.success) {
                toastr.success("Profile updated successfully!");
                
                // Update local storage
                const user = Utils.getCurrentUser();
                user.username = updateData.username;
                user.email = updateData.email;
                localStorage.setItem("user", JSON.stringify(user));
                
                UserService.loadProfile();
            }
        }, function(error) {
            $.unblockUI();
            const msg = error.responseJSON?.message || "Failed to update profile";
            toastr.error(msg);
        });
    },

    changePassword: function() {
        const currentPassword = $("#currentPassword").val();
        const newPassword = $("#newPassword").val();
        const confirmPassword = $("#confirmPassword").val();
        const userId = $("#userId").val();

        if (!currentPassword || !newPassword || !confirmPassword) {
            toastr.error("All password fields are required");
            return;
        }

        if (newPassword !== confirmPassword) {
            toastr.error("New passwords don't match");
            return;
        }

        if (newPassword.length < 6) {
            toastr.error("Password must be at least 6 characters");
            return;
        }

        $.blockUI({ message: '<h3>Changing password...</h3>' });

        // First verify current password by attempting login
        const user = Utils.getCurrentUser();
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "auth/login",
            type: "POST",
            data: JSON.stringify({
                email: user.email,
                password: currentPassword
            }),
            contentType: "application/json",
            success: function() {
                // Current password is correct, now update
                RestClient.put(`users/${userId}`, {
                    password: newPassword
                }, function(response) {
                    $.unblockUI();
                    toastr.success("Password changed successfully!");
                    
                    // Clear password fields
                    $("#currentPassword, #newPassword, #confirmPassword").val("");
                    $("#passwordStrengthBar").css("width", "0%");
                    $("#passwordStrengthText").text("Password strength: None");
                }, function(error) {
                    $.unblockUI();
                    toastr.error("Failed to change password");
                });
            },
            error: function() {
                $.unblockUI();
                toastr.error("Current password is incorrect");
            }
        });
    },

    confirmDeleteAccount: function() {
        if (!confirm("Are you sure you want to delete your account? This action cannot be undone!")) {
            return;
        }

        // Ask for password confirmation
        const password = prompt("Please enter your password to confirm account deletion:");
        
        if (!password) {
            return;
        }

        this.deleteAccount(password);
    },

    deleteAccount: function(password) {
        const user = Utils.getCurrentUser();
        const userId = user.user_id;

        $.blockUI({ message: '<h3>Deleting account...</h3>' });

        // First verify password
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "auth/login",
            type: "POST",
            data: JSON.stringify({
                email: user.email,
                password: password
            }),
            contentType: "application/json",
            success: function() {
                // Password correct, proceed with deletion
                RestClient.delete(`users/${userId}`, null, function(response) {
                    $.unblockUI();
                    toastr.success("Account deleted successfully");
                    
                    // Logout and redirect
                    AuthService.logout(false);
                }, function(error) {
                    $.unblockUI();
                    toastr.error("Failed to delete account");
                });
            },
            error: function() {
                $.unblockUI();
                toastr.error("Incorrect password");
            }
        });
    }
};