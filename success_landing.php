<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submission Successful</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes scaleIn {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes drawCheck {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        .animate-scale-in {
            animation: scaleIn 0.5s ease-out forwards;
        }
        .check-path {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: drawCheck 0.8s ease-out 0.3s forwards;
        }
        body {
            background-color: #fdf2f2; /* Very light maroon tint */
        }
        .bg-maroon-900 { background-color: #4a0404; }
        .bg-maroon-800 { background-color: #600000; }
        .text-maroon-900 { color: #4a0404; }
        .text-maroon-800 { color: #800000; }
        .border-maroon-800 { border-color: #800000; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

            <?php if (isset($_GET['application_success'])): ?>
                <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden animate-scale-in">
        <!-- Top Decorative Bar -->
        <div class="h-2 bg-maroon-800"></div>

        <div class="p-8 md:p-12 text-center">
            <!-- Animated Success Icon -->
            <div class="flex justify-center mb-6">
                <div class="rounded-full bg-maroon-50 p-4">
                    <svg class="w-20 h-20 text-maroon-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path class="check-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-bold text-maroon-900 mb-2">Success!</h1>
            <p class="text-gray-600 mb-8 leading-relaxed">
                Your form has been submitted successfully. We've received your information and our team will review it shortly.
            </p>

            <div class="space-y-4">
                <button onclick="window.location.href='home.php'" class="w-full py-3 px-6 bg-maroon-800 hover:bg-maroon-900 text-white font-semibold rounded-xl transition duration-300 shadow-lg active:scale-95">
                    Return to Home
                </button>
                
            </div>
        </div>
            <?php endif; ?>

    <!-- Small decorative background elements -->
    <div class="fixed top-0 left-0 w-32 h-32 bg-maroon-800 rounded-br-full opacity-5 -z-10"></div>
    <div class="fixed bottom-0 right-0 w-64 h-64 bg-maroon-800 rounded-tl-full opacity-5 -z-10"></div>

</body>
</html>