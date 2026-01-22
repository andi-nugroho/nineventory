<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NINEVENTORY - Smart Inventory Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/landing.css">
    
    <style>
        body { font-family: 'DM Sans', sans-serif; }
    </style>
</head>
<body class="bg-black text-white overflow-x-hidden">
    
    <!-- Navigation - Floating Centered (Independent) -->
    <header class="fixed top-6 left-1/2 -translate-x-1/2 z-[9999] w-full max-w-5xl px-6">
        <div class="mx-auto flex items-center justify-between rounded-full border border-white/10 bg-black/50 backdrop-blur-xl px-6 py-3 shadow-lg">
            <div class="flex items-center gap-3">
                <!-- Vault Logo -->
                <img src="assets/images/logo.svg" alt="Vault" class="h-6 w-auto">
            </div>

            <nav class="hidden items-center gap-8 text-sm/6 text-white/80 md:flex">
                <a class="hover:text-white transition" href="#features">Features</a>
                <a class="hover:text-white transition" href="#customers">Customers</a>
                <a class="hover:text-white transition" href="#about">About</a>
                <a class="hover:text-white transition" href="#contact">Contact</a>
            </nav>

            <div class="hidden items-center gap-3 md:flex">
                <a href="login.php" class="rounded-full px-4 py-2 text-sm text-white/80 hover:text-white transition">Sign in</a>
                <a href="register.php" class="rounded-full bg-gradient-to-r from-red-500 to-orange-500 px-5 py-2 text-sm font-medium text-white shadow-lg transition hover:shadow-red-500/50">Get Started</a>
            </div>

            <button class="md:hidden rounded-full bg-white/10 px-3 py-2 text-sm">Menu</button>
        </div>
    </header>
    
    <!-- Hero Section -->
    <section id="hero" class="relative isolate min-h-screen overflow-hidden bg-black text-white">
        <!-- Background Gradients -->
        <div aria-hidden class="absolute inset-0 -z-30 animated-gradient"></div>
        <div aria-hidden class="absolute inset-0 -z-20 bg-[radial-gradient(140%_120%_at_50%_0%,transparent_60%,rgba(0,0,0,0.85))]"></div>
        
        <!-- Grid Overlay -->
        <div aria-hidden class="pointer-events-none absolute inset-0 -z-10 grid-overlay"></div>

        <!-- Hero Content -->
        <div class="relative z-10 mx-auto grid w-full max-w-5xl place-items-center px-6 py-16 md:py-24 lg:py-28">
            <div class="mx-auto text-center fade-in">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/5 px-3 py-1 text-[11px] uppercase tracking-wider text-white/70 ring-1 ring-white/10 backdrop-blur">
                    <span class="h-1.5 w-1.5 rounded-full bg-red-500 subtle-pulse"></span> Smart Inventory
                </span>
                
                <h1 class="mt-6 text-4xl font-bold tracking-tight md:text-6xl lg:text-7xl fade-in-delay-1">
                    Manage Your Inventory<br/>with <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-orange-500 to-red-600">Intelligence</span>
                </h1>
                
                <p class="mx-auto mt-5 max-w-2xl text-balance text-white/80 md:text-lg fade-in-delay-2">
                    The essential toolkit for managing your inventory—from tracking items to AI-powered insights. Streamline your workflow with NINEVENTORY.
                </p>
                
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row fade-in-delay-3">
                    <a href="register.php" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-red-500 to-orange-500 px-8 py-3 text-sm font-semibold text-white shadow-lg transition hover:shadow-red-500/50">
                        Get Started Free
                    </a>
                    <a href="#features" class="inline-flex items-center justify-center rounded-full border border-white/20 px-8 py-3 text-sm font-semibold text-white/90 backdrop-blur hover:border-white/40 transition">
                        Learn More
                    </a>
                </div>
            </div>
        </div>

        <!-- Animated Pillars -->
        <div class="absolute bottom-0 left-0 right-0 z-0 flex items-end justify-center gap-1 px-4 pb-0 h-32 md:h-40">
            <div class="pillar-1 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
            <div class="pillar-2 w-2 md:w-3 bg-gradient-to-t from-orange-500/40 to-transparent rounded-t"></div>
            <div class="pillar-3 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
            <div class="pillar-4 w-2 md:w-3 bg-gradient-to-t from-orange-500/40 to-transparent rounded-t"></div>
            <div class="pillar-5 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
            <div class="pillar-6 w-2 md:w-3 bg-gradient-to-t from-orange-500/40 to-transparent rounded-t"></div>
            <div class="pillar-7 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
            <div class="pillar-8 w-2 md:w-3 bg-gradient-to-t from-orange-500/40 to-transparent rounded-t"></div>
            <div class="pillar-9 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
            <div class="pillar-8 w-2 md:w-3 bg-gradient-to-t from-orange-500/40 to-transparent rounded-t"></div>
            <div class="pillar-7 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
            <div class="pillar-6 w-2 md:w-3 bg-gradient-to-t from-orange-500/40 to-transparent rounded-t"></div>
            <div class="pillar-5 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
            <div class="pillar-4 w-2 md:w-3 bg-gradient-to-t from-orange-500/40 to-transparent rounded-t"></div>
            <div class="pillar-3 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
            <div class="pillar-2 w-2 md:w-3 bg-gradient-to-t from-orange-500/40 to-transparent rounded-t"></div>
            <div class="pillar-1 w-2 md:w-3 bg-gradient-to-t from-red-500/40 to-transparent rounded-t"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 md:py-32 bg-black">
        <div class="mx-auto max-w-5xl space-y-12 px-6">
            <div class="relative z-10 grid items-center gap-4 md:grid-cols-2 md:gap-12">
                <h2 class="text-4xl font-semibold text-white">The NINEVENTORY ecosystem brings together our models</h2>
                <p class="max-w-sm sm:ml-auto text-white/70">Empower your team with workflows that adapt to your needs, whether you prefer real-time tracking or AI-powered interface.</p>
            </div>
            
            <!-- Feature Image Showcase -->
            <div class="relative rounded-3xl p-3 md:-mx-8 lg:col-span-3 bg-gradient-to-br from-red-500/10 to-orange-500/10 border border-red-500/20">
                <div class="aspect-[88/36] relative bg-gradient-to-br from-gray-900 to-black rounded-2xl overflow-hidden">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center space-y-4">
                            <svg class="w-24 h-24 mx-auto text-red-500/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-white/50 text-sm">Inventory Dashboard Preview</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-t from-black absolute inset-0 to-transparent"></div>
                </div>
            </div>
            
            <!-- Feature Cards -->
            <div class="relative mx-auto grid grid-cols-2 gap-x-3 gap-y-6 sm:gap-8 lg:grid-cols-4">
                <div class="space-y-3 p-4 rounded-xl bg-gradient-to-br from-red-500/5 to-transparent border border-red-500/10 hover:border-red-500/30 transition">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h3 class="text-sm font-medium text-white">Fast</h3>
                    </div>
                    <p class="text-white/60 text-sm">Lightning-fast inventory tracking helping developers and businesses innovate.</p>
                </div>
                
                <div class="space-y-3 p-4 rounded-xl bg-gradient-to-br from-orange-500/5 to-transparent border border-orange-500/10 hover:border-orange-500/30 transition">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                        </svg>
                        <h3 class="text-sm font-medium text-white">Powerful</h3>
                    </div>
                    <p class="text-white/60 text-sm">Robust system supporting entire helping developers and businesses scale.</p>
                </div>
                
                <div class="space-y-3 p-4 rounded-xl bg-gradient-to-br from-red-500/5 to-transparent border border-red-500/10 hover:border-red-500/30 transition">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <h3 class="text-sm font-medium text-white">Security</h3>
                    </div>
                    <p class="text-white/60 text-sm">Enterprise-grade security helping developers businesses innovate safely.</p>
                </div>
                
                <div class="space-y-3 p-4 rounded-xl bg-gradient-to-br from-orange-500/5 to-transparent border border-orange-500/10 hover:border-orange-500/30 transition">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                        <h3 class="text-sm font-medium text-white">AI Powered</h3>
                    </div>
                    <p class="text-white/60 text-sm">Smart AI chatbot helping developers businesses innovate efficiently.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Customers Section -->
    <section id="customers" class="relative py-16 md:py-24 bg-black overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 opacity-30">
            <img src="assets/images/world.png" alt="" class="w-full h-full object-cover">
        </div>
        <div class="relative mx-auto max-w-6xl px-6">
            <h2 class="text-center text-3xl font-bold mb-12 text-white">Trusted by Leading Companies</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 items-center justify-items-center">
                <img src="assets/images/github.svg" alt="GitHub" class="h-8 md:h-10 w-auto opacity-70 hover:opacity-100 transition-all duration-300 filter invert brightness-0 hover:brightness-100">
                <img src="assets/images/laravel.svg" alt="Laravel" class="h-8 md:h-10 w-auto opacity-70 hover:opacity-100 transition-all duration-300 filter invert brightness-0 hover:brightness-100">
                <img src="assets/images/openai.svg" alt="OpenAI" class="h-8 md:h-10 w-auto opacity-70 hover:opacity-100 transition-all duration-300 filter invert brightness-0 hover:brightness-100">
                <img src="assets/images/column.svg" alt="Column" class="h-8 md:h-10 w-auto opacity-70 hover:opacity-100 transition-all duration-300 filter invert brightness-0 hover:brightness-100">
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="relative py-16 md:py-24 bg-gradient-to-b from-black to-gray-900 overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 opacity-20">
            <img src="assets/images/world-light.png" alt="" class="w-full h-full object-cover">
        </div>
        <div class="relative mx-auto max-w-6xl px-6">
            <h2 class="text-center text-3xl font-bold mb-12 text-white">Why Choose NINEVENTORY?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Easy to Use</h3>
                    <p class="text-white/70">Intuitive interface designed for seamless inventory management</p>
                </div>
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">Real-time Updates</h3>
                    <p class="text-white/70">Track your inventory changes instantly with live synchronization</p>
                </div>
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-white">AI Assistant</h3>
                    <p class="text-white/70">Get instant help with our intelligent chatbot powered by AI</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="relative py-16 md:py-24 bg-black flex items-center justify-center min-h-screen overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 opacity-25">
            <img src="assets/images/blob_https___thevault.png" alt="" class="w-full h-full object-cover">
        </div>
        <div class="relative mx-auto max-w-5xl w-full px-6">
            <!-- Contact Card Container -->
            <div class="relative rounded-3xl border border-white/10 bg-gradient-to-br from-gray-900/50 to-black/50 backdrop-blur-xl p-8 md:p-12">
                <!-- Header -->
                <div class="mb-8 text-center">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Get in touch</h2>
                    <p class="text-white/70 max-w-2xl mx-auto">
                        If you have any questions regarding our Services or need help, please fill out the form here. We do our best to respond within 1 business day.
                    </p>
                </div>

                <!-- Contact Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <!-- Email Card -->
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-red-500/10 to-transparent border border-red-500/20 hover:border-red-500/40 transition">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white/50 mb-1">Email</p>
                            <p class="text-sm text-white font-medium truncate">contact@nineventory.com</p>
                        </div>
                    </div>

                    <!-- Phone Card -->
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-red-500/10 to-transparent border border-red-500/20 hover:border-red-500/40 transition">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white/50 mb-1">Phone</p>
                            <p class="text-sm text-white font-medium">+62 812 3456 7890</p>
                        </div>
                    </div>

                    <!-- Address Card -->
                    <div class="flex items-start gap-3 p-4 rounded-xl bg-gradient-to-br from-red-500/10 to-transparent border border-red-500/20 hover:border-red-500/40 transition md:col-span-1">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-white/50 mb-1">Address</p>
                            <p class="text-sm text-white font-medium">Jakarta, Indonesia</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <form class="w-full space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Name Input -->
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-white/80">Name</label>
                            <input 
                                type="text" 
                                class="w-full px-4 py-3 rounded-lg bg-black/50 border border-white/10 text-white placeholder:text-white/30 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 transition"
                                placeholder="Your name"
                            >
                        </div>

                        <!-- Email Input -->
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-medium text-white/80">Email</label>
                            <input 
                                type="email" 
                                class="w-full px-4 py-3 rounded-lg bg-black/50 border border-white/10 text-white placeholder:text-white/30 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 transition"
                                placeholder="your@email.com"
                            >
                        </div>
                    </div>

                    <!-- Phone Input -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-white/80">Phone</label>
                        <input 
                            type="tel" 
                            class="w-full px-4 py-3 rounded-lg bg-black/50 border border-white/10 text-white placeholder:text-white/30 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 transition"
                            placeholder="+62 812 3456 7890"
                        >
                    </div>

                    <!-- Message Textarea -->
                    <div class="flex flex-col gap-2">
                        <label class="text-sm font-medium text-white/80">Message</label>
                        <textarea 
                            rows="5" 
                            class="w-full px-4 py-3 rounded-lg bg-black/50 border border-white/10 text-white placeholder:text-white/30 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500/20 transition resize-none"
                            placeholder="Tell us about your project..."
                        ></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full rounded-full bg-gradient-to-r from-red-500 to-orange-500 px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:shadow-red-500/50 hover:scale-[1.02] active:scale-[0.98]"
                    >
                        Submit
                    </button>
                </form>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="bg-black border-t border-white/10 py-12">
        <div class="mx-auto max-w-6xl px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <img src="assets/images/logo.svg" alt="Vault" class="h-5 w-auto">
                    </div>
                    <p class="text-white/60 text-sm">Smart inventory management with AI-powered insights.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Product</h4>
                    <ul class="space-y-2 text-white/60 text-sm">
                        <li><a href="#" class="hover:text-white transition">Features</a></li>
                        <li><a href="#" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition">Security</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Company</h4>
                    <ul class="space-y-2 text-white/60 text-sm">
                        <li><a href="#" class="hover:text-white transition">About</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Careers</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-4">Legal</h4>
                    <ul class="space-y-2 text-white/60 text-sm">
                        <li><a href="#" class="hover:text-white transition">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms</a></li>
                        <li><a href="#" class="hover:text-white transition">License</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-white/10 text-center text-white/60 text-sm">
                <p>&copy; 2024 NINEVENTORY. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/landing.js"></script>
</body>
</html>
