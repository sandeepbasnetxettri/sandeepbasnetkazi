$(document).ready(function () {

    $('#menu').click(function () {
        $(this).toggleClass('fa-times');
        $('.navbar').toggleClass('nav-toggle');
    });

    $(window).on('scroll load', function () {
        $('#menu').removeClass('fa-times');
        $('.navbar').removeClass('nav-toggle');

        if (window.scrollY > 60) {
            document.querySelector('#scroll-top').classList.add('active');
        } else {
            document.querySelector('#scroll-top').classList.remove('active');
        }

        // scroll spy
        $('section').each(function () {
            let height = $(this).height();
            let offset = $(this).offset().top - 200;
            let top = $(window).scrollTop();
            let id = $(this).attr('id');

            if (top > offset && top < offset + height) {
                $('.navbar ul li a').removeClass('active');
                $('.navbar').find(`[href="#${id}"]`).addClass('active');
            }
        });
    });

    // smooth scrolling & close mobile menu on nav click
    $('.navbar a').on('click', function () {
        $('#menu').removeClass('fa-times');
        $('.navbar').removeClass('nav-toggle');
    });

    $('a[href*="#"]').on('click', function (e) {
        let target = $(this).attr('href');
        if (target && target !== '#' && $(target).length) {
            e.preventDefault();
            $('#menu').removeClass('fa-times');
            $('.navbar').removeClass('nav-toggle');
            $('html, body').animate({
                scrollTop: $(target).offset().top,
            }, 500, 'linear');
        }
    });

    // Web3Forms AJAX submission handler
    const contactForm = document.getElementById("contact-form");
    const formStatus = document.getElementById("form-status");
    const submitBtn = document.getElementById("submit-btn");

    if (contactForm) {
        // Clear input errors on typing
        contactForm.querySelectorAll("input, textarea").forEach(input => {
            input.addEventListener("input", function () {
                this.classList.remove("input-error");
            });
        });

        contactForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const nameInput = document.getElementById("name");
            const emailInput = document.getElementById("email");
            const messageInput = document.getElementById("message");

            // Client-side validation
            let isValid = true;
            let firstInvalidField = null;

            // Clear previous status
            formStatus.className = "";
            formStatus.innerHTML = "";
            formStatus.style.display = "none";

            const nameVal = nameInput ? nameInput.value.trim() : "";
            const emailVal = emailInput ? emailInput.value.trim() : "";
            const messageVal = messageInput ? messageInput.value.trim() : "";
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!nameVal || nameVal.length < 2) {
                isValid = false;
                if (nameInput) {
                    nameInput.classList.add("input-error");
                    if (!firstInvalidField) firstInvalidField = nameInput;
                }
            }

            if (!emailVal || !emailRegex.test(emailVal)) {
                isValid = false;
                if (emailInput) {
                    emailInput.classList.add("input-error");
                    if (!firstInvalidField) firstInvalidField = emailInput;
                }
            }

            if (!messageVal || messageVal.length < 5) {
                isValid = false;
                if (messageInput) {
                    messageInput.classList.add("input-error");
                    if (!firstInvalidField) firstInvalidField = messageInput;
                }
            }

            if (!isValid) {
                if (firstInvalidField) firstInvalidField.focus();
                formStatus.className = "active status-error";
                formStatus.innerHTML = `<i class="fas fa-exclamation-circle" aria-hidden="true"></i> <span>Please fill in all required fields with valid details.</span>`;
                formStatus.style.display = "flex";
                return;
            }

            // Submit state
            const btnText = submitBtn.querySelector(".btn-text");
            const btnIcon = submitBtn.querySelector(".btn-icon");

            submitBtn.disabled = true;
            if (btnText) btnText.textContent = "Sending...";
            if (btnIcon) btnIcon.className = "fas fa-spinner fa-spin btn-icon";

            try {
                const formData = new FormData(contactForm);
                const response = await fetch("https://api.web3forms.com/submit", {
                    method: "POST",
                    body: formData
                });

                const data = await response.json();

                if (response.status === 200 && data.success) {
                    formStatus.className = "active status-success";
                    formStatus.innerHTML = `<i class="fas fa-check-circle" aria-hidden="true"></i> <span>Message sent successfully! Thanks for reaching out. I'll get back to you soon.</span>`;
                    formStatus.style.display = "flex";
                    contactForm.reset();
                    contactForm.querySelectorAll(".input-error").forEach(el => el.classList.remove("input-error"));
                } else {
                    formStatus.className = "active status-error";
                    formStatus.innerHTML = `<i class="fas fa-exclamation-circle" aria-hidden="true"></i> <span>Something went wrong while sending your message. Please try again or contact me directly via <a href="https://wa.me/9779768827327" target="_blank" rel="noopener noreferrer">WhatsApp</a> or <a href="tel:+9779768827327">Phone</a>.</span>`;
                    formStatus.style.display = "flex";
                }
            } catch (err) {
                console.error("Form submission error:", err);
                formStatus.className = "active status-error";
                formStatus.innerHTML = `<i class="fas fa-exclamation-circle" aria-hidden="true"></i> <span>Unable to send message due to a connection issue. Please check your internet or reach out directly on <a href="https://wa.me/9779768827327" target="_blank" rel="noopener noreferrer">WhatsApp</a>.</span>`;
                formStatus.style.display = "flex";
            } finally {
                submitBtn.disabled = false;
                if (btnText) btnText.textContent = "Submit";
                if (btnIcon) btnIcon.className = "fas fa-paper-plane btn-icon";
            }
        });
    }

});

document.addEventListener('visibilitychange',
    function () {
        if (document.visibilityState === "visible") {
            document.title = "Portfolio | Sandeep basnet kazi ";
            $("#favicon").attr("href", "assets/images/favicon.png");
        }
        else {
            document.title = "Come Back To Portfolio";
            $("#favicon").attr("href", "assets/images/favhand.png");
        }
    });


// <!-- typed js effect starts -->
var typed = new Typed(".typing-text", {
    strings: ["frontend development", "backend development", "web designing", "android development", "video editing", "photo editor", "content writing", "ai call center agents", "social media marketing"],
    loop: true,
    typeSpeed: 50,
    backSpeed: 25,
    backDelay: 500,
});
// <!-- typed js effect ends -->

async function fetchData(type = "skills") {
    try {
        const response = type === "skills"
            ? await fetch("skills.json")
            : await fetch("./projects/projects.json");
        if (!response.ok) return null;
        return await response.json();
    } catch (error) {
        console.log("Could not load dynamic data for " + type, error);
        return null;
    }
}

function showSkills(skills) {
    if (!skills || !Array.isArray(skills) || skills.length === 0) return;
    let skillsContainer = document.getElementById("skillsContainer");
    if (!skillsContainer) return;
    let skillHTML = "";
    skills.forEach(skill => {
        skillHTML += `
        <div class="bar">
              <div class="info">
                <img src="${skill.icon}" alt="${skill.name}" />
                <span>${skill.name}</span>
              </div>
            </div>`;
    });
    skillsContainer.innerHTML = skillHTML;
}

function showProjects(projects) {
    let projectsContainer = document.querySelector("#work .box-container");
    if (!projectsContainer || !projects || !Array.isArray(projects)) return;
    let projectHTML = "";
    projects.slice(0, 10).filter(project => project.category != "android").forEach(project => {
        projectHTML += `
        <div class="box tilt">
      <img draggable="false" src="/assets/images/projects/${project.image}.png" alt="project" />
      <div class="content">
        <div class="tag">
        <h3>${project.name}</h3>
        </div>
        <div class="desc">
          <p>${project.desc}</p>
          <div class="btns">
            <a href="${project.links.view}" class="btn" target="_blank"><i class="fas fa-eye"></i> View</a>
            <a href="${project.links.code}" class="btn" target="_blank">Code <i class="fas fa-code"></i></a>
          </div>
        </div>
      </div>
    </div>`;
    });
    projectsContainer.innerHTML = projectHTML;

    // <!-- tilt js effect starts -->
    VanillaTilt.init(document.querySelectorAll(".tilt"), {
        max: 15,
    });
    // <!-- tilt js effect ends -->

    /* ===== SCROLL REVEAL ANIMATION ===== */
    const srtop = ScrollReveal({
        origin: 'top',
        distance: '80px',
        duration: 1000,
        reset: false
    });

    /* SCROLL PROJECTS */
    srtop.reveal('.work .box', { interval: 200 });

}

fetchData().then(data => {
    if (data) showSkills(data);
}).catch(err => console.log(err));

fetchData("projects").then(data => {
    if (data) showProjects(data);
}).catch(err => console.log(err));

// <!-- tilt js effect starts -->
VanillaTilt.init(document.querySelectorAll(".tilt"), {
    max: 15,
});
// <!-- tilt js effect ends -->


// pre loader start
// function loader() {
//     document.querySelector('.loader-container').classList.add('fade-out');
// }
// function fadeOut() {
//     setInterval(loader, 500);
// }
// window.onload = fadeOut;
// pre loader end

// disable developer mode
document.onkeydown = function (e) {
    if (e.keyCode == 123) {
        return false;
    }
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
        return false;
    }
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
        return false;
    }
    if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
        return false;
    }
    if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
        return false;
    }
}




/* ===== SCROLL REVEAL ANIMATION ===== */
const srtop = ScrollReveal({
    origin: 'top',
    distance: '80px',
    duration: 1000,
    reset: false
});

/* SCROLL HOME */
srtop.reveal('.home .content h3', { delay: 200 });
srtop.reveal('.home .content p', { delay: 200 });
srtop.reveal('.home .content .btn', { delay: 200 });

srtop.reveal('.home .image', { delay: 400 });
srtop.reveal('.home .linkedin', { interval: 600 });
srtop.reveal('.home .github', { interval: 800 });
srtop.reveal('.home .twitter', { interval: 1000 });
srtop.reveal('.home .telegram', { interval: 600 });
srtop.reveal('.home .instagram', { interval: 600 });
srtop.reveal('.home .dev', { interval: 600 });

/* SCROLL ABOUT */
srtop.reveal('.about .content h3', { delay: 200 });
srtop.reveal('.about .content .tag', { delay: 200 });
srtop.reveal('.about .content p', { delay: 200 });
srtop.reveal('.about .content .box-container', { delay: 200 });
srtop.reveal('.about .content .resumebtn', { delay: 200 });


/* SCROLL SKILLS */
srtop.reveal('.skills .container', { interval: 200 });
srtop.reveal('.skills .container .bar', { delay: 400 });

/* SCROLL EDUCATION */
srtop.reveal('.education .box', { interval: 200 });

/* SCROLL PROJECTS */
srtop.reveal('.work .box', { interval: 200 });

/* SCROLL EXPERIENCE */
srtop.reveal('.experience .timeline', { delay: 400 });
srtop.reveal('.experience .timeline .container', { interval: 400 });

/* SCROLL CONTACT */
srtop.reveal('.contact .heading', { delay: 200 });
srtop.reveal('.contact .container', { delay: 300 });
srtop.reveal('.contact-illustration-card', { delay: 400, origin: 'left', distance: '40px' });
srtop.reveal('.contact .content form', { delay: 400, origin: 'right', distance: '40px' });
