<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="info-header">
    <div class="container reveal">
        <span class="sub-heading" style="color: var(--secondary);">Admissions 2026</span>
        <h1>Start Your Journey</h1>
        <p class="fs-lg text-muted" style="max-width: 600px; margin: 0 auto; color: #94a3b8;">Join a community dedicated to excellence, innovation, and global leadership.</p>
    </div>
</div>

<div class="container section-padding reveal">
    <div class="feature-grid" style="gap: 2rem; align-items: start; margin-bottom: 4rem;">
        <!-- Left Column: Info & Form -->
        <div>
            <h2 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 2rem;">Admission Process</h2>
        <p style="color: var(--text-muted); margin-bottom: 2rem; line-height: 1.9; font-size: 1.1rem;">
            We seek students who are curious, motivated, and ready to contribute to our vibrant community. Our holistic evaluation process considers academic potential, character, and extracurricular interests.
        </p>
        
        <div class="glass-card" style="margin-bottom: 4rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 2rem; color: var(--text-main); display: flex; align-items: center; gap: 0.75rem;">
                <i class="fa-solid fa-clipboard-list" style="color: var(--primary);"></i> Essential Documents
            </h3>
            <ul style="list-style: none; color: var(--text-muted);">
                <li style="margin-bottom: 1.25rem; display: flex; gap: 1rem; align-items: flex-start;">
                    <i class="fa-solid fa-check-circle" style="color: var(--accent); margin-top: 0.25rem;"></i>
                    <span>Official Academic Transcripts (Previous 2 years)</span>
                </li>
                <li style="margin-bottom: 1.25rem; display: flex; gap: 1rem; align-items: flex-start;">
                    <i class="fa-solid fa-check-circle" style="color: var(--accent); margin-top: 0.25rem;"></i>
                    <span>Birth Certificate & Government ID</span>
                </li>
                <li style="margin-bottom: 1.25rem; display: flex; gap: 1rem; align-items: flex-start;">
                    <i class="fa-solid fa-check-circle" style="color: var(--accent); margin-top: 0.25rem;"></i>
                    <span>Recommendation Letter from Previous School</span>
                </li>
                <li style="margin-bottom: 1.25rem; display: flex; gap: 1rem; align-items: flex-start;">
                    <i class="fa-solid fa-check-circle" style="color: var(--accent); margin-top: 0.25rem;"></i>
                    <span>4 Recent Passport-sized Photographs</span>
                </li>
            </ul>
        </div>

        <!-- Online Admission Form -->
        <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--text-main); margin-bottom: 2rem; letter-spacing: -0.025em;" id="apply">Application Inquiry</h2>
        <div class="glass-card">
            <?php if(isset($_GET['status'])): ?>
                <?php if($_GET['status'] == 'success'): ?>
                    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid var(--accent); color: var(--accent); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700;">
                        <i class="fa-solid fa-circle-check"></i> Inquiry received! Our admissions team will contact you shortly.
                    </div>
                <?php elseif($_GET['status'] == 'error'): ?>
                    <div style="background: rgba(248, 113, 113, 0.1); border: 1px solid #f87171; color: #f87171; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; font-weight: 700;">
                        <i class="fa-solid fa-circle-xmark"></i> System error. Please try again later.
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <form action="api/submit_inquiry.php" method="POST">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label>Student Full Name</label>
                        <input type="text" name="student_name" class="form-control" placeholder="Enter name" required>
                    </div>
                    <div class="form-group">
                        <label>Guardian Name</label>
                        <input type="text" name="parent_name" class="form-control" placeholder="Enter name" required>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label>Direct Contact Email</label>
                        <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" class="form-control" placeholder="+977" required>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 2.5rem;">
                    <label>Interested Grade/Program</label>
                    <select name="class_applied" class="form-control" required style="cursor: pointer;">
                        <option value="">Select an option</option>
                        <option value="Primary">Primary Wing (Classes 1-5)</option>
                        <option value="Middle">Middle School (Classes 6-8)</option>
                        <option value="Secondary">Secondary Level (SEE Prep)</option>
                        <option value="+2 Science">+2 Computer Science</option>
                        <option value="+2 HM">+2 Hotel Management</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1.25rem;">Process Inquiry <i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    <!-- Right Column: Sidebar -->
    <div class="admission-sidebar">
        <!-- Fee Structure Box Premium -->
        <div class="reveal glass-card" style="margin-bottom: 2rem;">
            <div style="width: 50px; height: 50px; background: rgba(79, 70, 229, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-coins fa-lg" style="color: var(--primary);"></i>
            </div>
            <h3 style="font-weight: 800; color: var(--text-main); margin-bottom: 1rem; font-size: 1.25rem;">Financial Guide</h3>
            <p style="color: var(--text-muted); line-height: 1.8; margin-bottom: 1.5rem;">Access the comprehensive 2026 fee structure, including tuition and maintenance.</p>
            <a href="#" class="btn btn-secondary" style="width: 100%; justify-content: center;"><i class="fa-solid fa-file-pdf"></i> Download Pricing</a>
        </div>
        
        <!-- Scholarship Box Premium -->
        <div class="reveal glass-card" style="background: linear-gradient(135deg, var(--primary-dark) 0%, #1e1b4b 100%); color: white; border: none;">
            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-star fa-lg" style="color: var(--secondary);"></i>
            </div>
            <h3 style="font-weight: 800; margin-bottom: 1.5rem; font-size: 1.25rem; color: white;">Merit Scholarships</h3>
            <p style="color: #cbd5e1; line-height: 1.8; margin-bottom: 1.5rem;">Up to 100% scholarship available for SEE high scorers and national athletes.</p>
            <a href="contact.php" class="btn" style="background: white; color: var(--primary-dark); width: 100%; justify-content: center;">Speak to Advisor</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
