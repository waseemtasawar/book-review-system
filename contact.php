<?php include('includes/header.php'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="mb-4 text-primary fw-bold"><i class="fas fa-envelope me-2"></i>Contact Us</h3>

                    <div id="responseMessage"></div>

                    <form id="contactForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="fas fa-paper-plane me-1"></i> Send Message
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 small text-muted">
                        We usually respond within 24 hours.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("contactForm").addEventListener("submit", async function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    const response = await fetch("https://formspree.io/f/xqabvbge", {
        method: "POST",
        body: formData,
        headers: { 'Accept': 'application/json' }
    });

    const messageDiv = document.getElementById("responseMessage");

    if (response.ok) {
        messageDiv.innerHTML = `
            <div class="alert alert-success d-flex align-items-center mt-3" role="alert">
                <i class="fas fa-check-circle me-2"></i> ✅ Your message has been sent successfully!
            </div>
        `;
        form.reset();
    } else {
        messageDiv.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center mt-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> ❌ Failed to send message. Please try again.
            </div>
        `;
    }
});
</script>
<?php include('includes/footer.php'); ?>
