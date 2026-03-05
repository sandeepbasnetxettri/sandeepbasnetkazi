<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="info-header">
    <div class="container reveal">
        <span class="sub-heading" style="color: var(--secondary);">Get Collaborative</span>
        <h1>Connect With Us</h1>
        <p class="fs-lg text-muted" style="max-width: 600px; margin: 0 auto; color: #94a3b8;">We are here to support your educational journey. Reach out to our team today.</p>
    </div>
</div>

<div class="container section-padding reveal">
    <div class="feature-grid" style="gap: 2.5rem; align-items: start;">

    <!-- Contact Information -->
    <div>
        <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 3rem; letter-spacing: -0.025em;">Contact Hub</h2>
        
        <div class="reveal" style="display: flex; gap: 1.5rem; margin-bottom: 3rem;">
            <div style="background: rgba(79, 70, 229, 0.1); width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--primary);">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
                <h3 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.5rem;">Main Campus</h3>
                <p style="color: var(--text-muted); line-height: 1.8;">Kathmandu, Nepal<br>Educational Zone, Sector 4</p>
            </div>
        </div>

        <div class="reveal" style="display: flex; gap: 1.5rem; margin-bottom: 3rem;">
            <div style="background: rgba(251, 191, 36, 0.1); width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--secondary-dark);">
                <i class="fa-solid fa-phone-volume"></i>
            </div>
            <div>
                <h3 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.5rem;">Support Lines</h3>
                <p style="color: var(--text-muted); line-height: 1.8;">Office: +977 976-8827327<br>Hotline: +977 01-445566</p>
            </div>
        </div>

        <div class="reveal" style="display: flex; gap: 1.5rem; margin-bottom: 3rem;">
            <div style="background: rgba(16, 185, 129, 0.1); width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: var(--accent);">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div>
                <h3 style="color: var(--text-main); font-weight: 700; margin-bottom: 0.5rem;">Digital Mail</h3>
                <p style="color: var(--text-muted); line-height: 1.8;">info@everestschool.edu.np<br>admissions@everestschool.edu.np</p>
            </div>
        </div>
        
        <!-- Social Media Links -->
        <div class="reveal" style="margin-top: 5rem;">
            <h3 style="color: var(--text-main); font-weight: 700; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.9rem;">Follow Our Updates</h3>
            <div style="display: flex; gap: 1rem;">
                <a href="#" class="social-link-item"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="social-link-item"><i class="fa-brands fa-twitter"></i></a>
                <a href="#" class="social-link-item"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="social-link-item"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Contact Form & Map -->
    <div>
        <div class="glass-card" style="margin-bottom: 4rem;">
            <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 2rem;">Send a Message</h2>
            <form action="api/submit_contact.php" method="POST">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label>Your Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter name" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="Enter subject" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 2.5rem;">
                    <label>Detailed Message</label>
                    <textarea name="message" rows="6" class="form-control" placeholder="  Write your message here..." required style="resize: vertical;"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem;">Dispatch Message <i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>

        <!-- Google Map embed Modernized -->
        <div style="border-radius: var(--radius-lg); overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow); height: 400px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d113032.64621396825!2d85.2504897120689!3d27.70895425227181!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb198a307baabf%3A0xb5137c1bf18db1ea!2sKathmandu%2044600%2C%20Nepal!5e0!3m2!1sen!2sus!4v1709400000000!5m2!1sen!2sus" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
