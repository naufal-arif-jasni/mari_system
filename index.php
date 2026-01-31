<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image" href="images/logo_red.jpeg">
    <title>Welcome - Malaysia Aid Registration Initiative</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/index-styles.css">

</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/logo_red.jpeg" alt="MARI Logo red" height="60">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#about">About MARI</a></li>
                    <li class="nav-item"><a class="nav-link" href="#news">News & Updates</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                    <li class="nav-item ms-2">
                        <a href="login.php" class="btn btn-primary px-4 rounded-pill">Login Portal</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="container">
            <h1 class="hero-title">Malaysia Aid Registration Initiative (MARI)</h1>
            <p class="lead mb-5 fs-4">Ensuring no one is left behind. A unified platform for disability aid.</p>
            <div class="d-flex gap-3 justify-content-center">
                <a href="login.php" class="btn btn-primary btn-lg px-5 rounded-pill">Login to System</a>
                <a href="register.php" class="btn btn-outline-light btn-lg px-5 rounded-pill">Register Account</a>
            </div>
        </div>
    </header>

     <!-- About Section -->
    <section id="about" class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="fw-bold text-primary mb-4">About the System</h2>
                    <p class="text-muted fs-5">
                        The <strong>Malaysia Aid Registration Initiative (MARI)</strong> is a centralized national database designed to streamline the application and distribution of government aid.
                    </p>
                    <ul class="list-unstyled mt-4">
                        <li class="mb-2">✅ Secure Data Handling</li>
                        <li class="mb-2">✅ Easy Online Registration</li>
                        <li class="mb-2">✅ Fast Verification Process</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <img src="images/about.jpg" class="img-fluid rounded shadow" alt="About Photo">
                </div>
            </div>
        </div>
    </section>

    <!-- News / Info Section -->
    <section id="news" class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Latest Announcements</h2>
                <p class="text-muted">Stay updated with the latest aid programs and policy changes.</p>
            </div>
            <div class="row">
                <!-- News Card 1 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">2026 Aid Applications Open</h5>
                            <p class="card-text text-muted">The new cycle for financial assistance applications is now open. Please ensure your medical reports are up to date.</p>
                            <a href="javascript:void(0)" class="text-primary text-decoration-none read-more-btn" 
                               data-title="2026 Aid Applications Open" 
                               data-content="Registration for the 2026 aid cycle is now officially open. Applicants must submit their latest OKU card details and specialized medical reports from government hospitals. Applications close on December 31st.">
                               Read More &rarr;
                            </a>
                        </div>
                    </div>
                </div>
                <!-- News Card 2 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">New Workshop Programs</h5>
                            <p class="card-text text-muted">Join our upcoming skills workshops designed for inclusive career development.</p>
                            <a href="javascript:void(0)" class="text-primary text-decoration-none read-more-btn"
                               data-title="New Workshop Programs" 
                               data-content="Our new inclusive workshop series includes digital literacy, entrepreneurship, and craft-making. These programs are held weekly across various community centers in Malaysia. Register via the portal today!">
                               Read More &rarr;
                            </a>
                        </div>
                    </div>
                </div>
                <!-- News Card 3 -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Medical Report Guidelines</h5>
                            <p class="card-text text-muted">Updated guidelines for submitting specialist confirmation forms are now available in the portal.</p>
                            <a href="javascript:void(0)" class="text-primary text-decoration-none read-more-btn"
                               data-title="Medical Report Guidelines" 
                               data-content="All medical reports must be signed by a registered specialist. Reports are valid for 2 years from the date of issue. Please ensure all scanned copies are clear and in PDF format for faster processing.">
                               Read More &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FLOATING MODAL STRUCTURE -->
    <div class="modal-overlay" id="infoModal">
        <div class="modal-content-custom">
            <button class="btn-close-custom" id="closeModal">&times;</button>
            <h3 id="modalTitle" class="fw-bold">Title</h3>
            <div id="modalBody" class="modal-body-text">
                Content goes here...
            </div>
            <div class="mt-4 text-end">
                <button class="btn btn-primary rounded-pill px-4" id="closeModalBtn">Got it</button>
            </div>
        </div>
    </div>


     <!-- Footer Section -->
<footer id="contact" class="section-padding bg-white text-black-50 pt-5 pb-4" style="margin-top: 50px;">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h3 class="fw-bold">Contact MARI</h3>
                <p class="text-black-50">Have questions? Reach out to our support team.</p>
                <p class="text-black-50">
                    <strong>Email:</strong> support@mari.gov.my<br>
                    <strong>Phone:</strong> +603-5555 1234<br>
                    <strong>Address:</strong> Level 5, Menara Kebajikan, Shah Alam, Selangor
                </p>
            </div>
            <div class="col-md-6">
                <h3 class="fw-bold">Quick Links</h3>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-black-50 text-decoration-none hover-black">Home</a></li>
                    <li><a href="login.php" class="text-black-50 text-decoration-none hover-black">Login Portal</a></li>
                    <li><a href="register.php" class="text-black-50 text-decoration-none hover-black">Register Account</a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const modal = document.getElementById('infoModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const closeButtons = [document.getElementById('closeModal'), document.getElementById('closeModalBtn')];
        const readMoreBtns = document.querySelectorAll('.read-more-btn');

        // Open modal and set content
        readMoreBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const title = btn.getAttribute('data-title');
                const content = btn.getAttribute('data-content');
                
                modalTitle.innerText = title;
                modalBody.innerText = content;
                
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        });

        // Close modal function
        const closeModal = () => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        };

        closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

        // Close on clicking outside the box
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
    </script>
</body>
</html>