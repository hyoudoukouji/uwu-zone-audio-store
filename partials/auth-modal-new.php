<div id="authModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-labelledby="modalTitle" aria-modal="true">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="modalBackdrop"></div>
    
    <!-- Modal -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <!-- Tabs -->
            <div class="flex text-lg font-medium border-b" role="tablist">
                <button id="loginTab" class="flex-1 p-4 text-center transition-colors duration-200 relative" role="tab" aria-controls="loginForm" aria-selected="true">
                    Login
                    <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 transform transition-transform duration-200"></div>
                </button>
                <button id="registerTab" class="flex-1 p-4 text-center text-gray-500 transition-colors duration-200 relative" role="tab" aria-controls="registerForm" aria-selected="false">
                    Register
                    <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 transform transition-transform duration-200 translate-x-full"></div>
                </button>
            </div>

            <!-- Login Form -->
            <div id="loginForm" class="p-8" role="tabpanel" aria-labelledby="loginTab">
                <h2 id="modalTitle" class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
                <p class="text-gray-600 mb-8">Please sign in to your account</p>
                
                <form class="space-y-6" id="loginFormElement" novalidate>
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <div class="relative">
                                <input type="email" name="email" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                       placeholder="Email address"
                                       aria-describedby="email-error">
                                <i class="fas fa-envelope absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <div id="email-error" class="text-red-500 text-sm hidden"></div>
                        </div>
                        <div class="space-y-1">
                            <div class="relative">
                                <input type="password" name="password" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                       placeholder="Password"
                                       aria-describedby="password-error">
                                <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <div id="password-error" class="text-red-500 text-sm hidden"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-blue-600 hover:text-blue-500">Forgot password?</a>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Sign in to your account">
                        <i class="fas fa-sign-in-alt"></i>
                        <span class="button-text">Sign in</span>
                        <span class="loading-text hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Signing in...
                        </span>
                    </button>

                    <div class="relative flex items-center justify-center">
                        <div class="border-t border-gray-200 w-full"></div>
                        <div class="absolute bg-white px-4 text-sm text-gray-500">Or continue with</div>
                    </div>

                    <!-- Google and Discord Sign In Buttons -->
                    <div class="flex flex-col space-y-4">
                        <div id="googleLoginButton" class="w-full"></div>

                        <a href="/api/auth/discord_auth.php" 
                           class="w-full flex items-center justify-center py-2.5 px-4 border border-[#5865F2] bg-[#5865F2] text-white rounded-lg hover:bg-[#4752C4] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#5865F2] focus:ring-offset-2">
                            <i class="fab fa-discord mr-2 text-xl"></i>
                            <span>Continue with Discord</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Register Form -->
            <div id="registerForm" class="p-8 hidden" role="tabpanel" aria-labelledby="registerTab">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                <p class="text-gray-600 mb-8">Join our khilaf today</p>
                
                <form class="space-y-6" id="registerFormElement" novalidate>
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <div class="relative">
                                <input type="text" name="username" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                       placeholder="Username"
                                       aria-describedby="username-error">
                                <i class="fas fa-user absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <div id="username-error" class="text-red-500 text-sm hidden"></div>
                        </div>
                        <div class="space-y-1">
                            <div class="relative">
                                <input type="email" name="email" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                       placeholder="Email address"
                                       aria-describedby="register-email-error">
                                <i class="fas fa-envelope absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <div id="register-email-error" class="text-red-500 text-sm hidden"></div>
                        </div>
                        <div class="space-y-1">
                            <div class="relative">
                                <input type="password" name="password" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                       placeholder="Password"
                                       aria-describedby="register-password-error">
                                <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <div id="register-password-error" class="text-red-500 text-sm hidden"></div>
                        </div>
                        <div class="space-y-1">
                            <div class="relative">
                                <input type="password" name="confirm_password" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                       placeholder="Confirm password"
                                       aria-describedby="confirm-password-error">
                                <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <div id="confirm-password-error" class="text-red-500 text-sm hidden"></div>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Create new account">
                        <i class="fas fa-user-plus"></i>
                        <span class="button-text">Create Account</span>
                        <span class="loading-text hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Creating account...
                        </span>
                    </button>

                    <div class="relative flex items-center justify-center">
                        <div class="border-t border-gray-200 w-full"></div>
                        <div class="absolute bg-white px-4 text-sm text-gray-500">Or sign up with</div>
                    </div>

                    <!-- Google and Discord Sign In Buttons for Register -->
                    <div class="flex flex-col space-y-4">
                        <div id="googleRegisterButton" class="w-full"></div>

                        <a href="/api/auth/discord_auth.php" 
                           class="w-full flex items-center justify-center py-2.5 px-4 border border-[#5865F2] bg-[#5865F2] text-white rounded-lg hover:bg-[#4752C4] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-[#5865F2] focus:ring-offset-2">
                            <i class="fab fa-discord mr-2 text-xl"></i>
                            <span>Sign up with Discord</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Close button -->
            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-700" id="closeModal" aria-label="Close modal">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
</div>

<!-- Notification Component -->
<div id="notification" class="fixed bottom-4 right-4 transform transition-all duration-300 translate-y-full opacity-0 z-50">
    <div class="flex items-center p-4 rounded-lg shadow-lg max-w-sm">
        <div class="flex-shrink-0 mr-3">
            <i class="fas text-2xl notification-icon"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-medium notification-message"></p>
        </div>
        <button class="ml-4 text-gray-400 hover:text-gray-600 transition-colors duration-200" onclick="hideNotification()" aria-label="Close notification">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Google Sign In Script -->
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script src="assets/js/google-auth.js"></script>
