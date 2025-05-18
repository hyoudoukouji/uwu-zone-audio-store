<div id="authModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="modalBackdrop"></div>
    
    <!-- Modal -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <!-- Tabs -->
            <div class="flex text-lg font-medium border-b">
                <button id="loginTab" class="flex-1 p-4 text-center transition-colors duration-200 relative overflow-hidden">
                    Login
                    <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 transform transition-transform duration-200"></div>
                </button>
                <button id="registerTab" class="flex-1 p-4 text-center text-gray-500 transition-colors duration-200 relative overflow-hidden">
                    Register
                    <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 transform transition-transform duration-200 translate-x-full"></div>
                </button>
            </div>

            <!-- Login Form -->
            <div id="loginForm" class="p-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
                <p class="text-gray-600 mb-8">Please sign in to your account</p>
                
                <form method="POST" action="login.php" class="space-y-6">
                    <div class="space-y-4">
                        <div class="relative">
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                   placeholder="Email address">
                            <i class="fas fa-envelope absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                   placeholder="Password">
                            <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
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
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 transform hover:scale-[1.02]">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Sign in</span>
                    </button>

                    <div class="relative flex items-center justify-center">
                        <div class="border-t border-gray-200 w-full"></div>
                        <div class="absolute bg-white px-4 text-sm text-gray-500">Or continue with</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" class="flex items-center justify-center py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <i class="fab fa-google text-red-500 mr-2"></i>
                            Google
                        </button>
                        <button type="button" class="flex items-center justify-center py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <i class="fab fa-facebook text-blue-600 mr-2"></i>
                            Facebook
                        </button>
                    </div>
                </form>
            </div>

            <!-- Register Form -->
            <div id="registerForm" class="p-8 hidden">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                <p class="text-gray-600 mb-8">Join our community today</p>
                
                <form method="POST" action="register.php" class="space-y-6">
                    <div class="space-y-4">
                        <div class="relative">
                            <input type="text" name="username" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                   placeholder="Username">
                            <i class="fas fa-user absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="relative">
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                   placeholder="Email address">
                            <i class="fas fa-envelope absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                   placeholder="Password">
                            <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="relative">
                            <input type="password" name="confirm_password" required
                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-200 bg-gray-50"
                                   placeholder="Confirm password">
                            <i class="fas fa-lock absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 transform hover:scale-[1.02]">
                        <i class="fas fa-user-plus"></i>
                        <span>Create Account</span>
                    </button>

                    <div class="relative flex items-center justify-center">
                        <div class="border-t border-gray-200 w-full"></div>
                        <div class="absolute bg-white px-4 text-sm text-gray-500">Or sign up with</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" class="flex items-center justify-center py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <i class="fab fa-google text-red-500 mr-2"></i>
                            Google
                        </button>
                        <button type="button" class="flex items-center justify-center py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <i class="fab fa-facebook text-blue-600 mr-2"></i>
                            Facebook
                        </button>
                    </div>
                </form>
            </div>

            <!-- Close button -->
            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-700" id="closeModal">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
</div>
