$(document).ready(function () {

    function closeMenu() {
        $('#menu').removeClass('fa-times');
        $('.navbar').removeClass('nav-toggle');
        $('#nav-overlay').removeClass('active');
        $('body').removeClass('no-scroll');
    }

    function openMenu() {
        $('#menu').addClass('fa-times');
        $('.navbar').addClass('nav-toggle');
        $('#nav-overlay').addClass('active');
        $('body').addClass('no-scroll');
    }

    $('#menu').click(function () {
        if ($('.navbar').hasClass('nav-toggle')) {
            closeMenu();
        } else {
            openMenu();
        }
    });

    $('#nav-overlay').click(function () {
        closeMenu();
    });

    $(window).on('scroll load', function () {
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
        closeMenu();
    });

    $('a[href*="#"]').on('click', function (e) {
        let target = $(this).attr('href');
        if (target && target !== '#' && $(target).length) {
            e.preventDefault();
            closeMenu();
            $('html, body').animate({
                scrollTop: $(target).offset().top,
            }, 500, 'linear');
        }
    });

    // <!-- emailjs to mail contact form data -->
    // $("#contact-form").submit(function (event) {
    //     emailjs.init("user_TTDmetQLYgWCLzHTDgqxm");

    //     emailjs.sendForm('contact_service', 'template_contact', '#contact-form')
    //         .then(function (response) {
    //             console.log('SUCCESS!', response.status, response.text);
    //             document.getElementById("contact-form").reset();
    //             alert("Form Submitted Successfully");
    //         }, function (error) {
    //             console.log('FAILED...', error);
    //             alert("Form Submission Failed! Try Again");
    //         });
    //     event.preventDefault();
    // });
    // <!-- emailjs to mail contact form data -->

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
    let response
    type === "skills" ?
        response = await fetch("skills.json")
        :
        response = await fetch("./projects/projects.json")
    const data = await response.json();
    return data;
}

function showSkills(skills) {
    let skillsContainer = document.getElementById("skillsContainer");
    let skillHTML = "";
    skills.forEach(skill => {
        skillHTML += `
        <div class="bar">
              <div class="info">
                <img src=${skill.icon} alt="skill" />
                <span>${skill.name}</span>
              </div>
            </div>`
    });
    skillsContainer.innerHTML = skillHTML;
}

function showProjects(projects) {
    let projectsContainer = document.querySelector("#work .box-container");
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
    </div>`
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
        reset: true
    });

    /* SCROLL PROJECTS */
    srtop.reveal('.work .box', { interval: 200 });

}

fetchData().then(data => {
    showSkills(data);
});

fetchData("projects").then(data => {
    showProjects(data);
}).catch(err => {
    // Projects data is loaded statically in HTML
});

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
    distance: '60px',
    duration: 900,
    reset: false
});

/* SCROLL HOME */
srtop.reveal('.home .content h2', { delay: 150 });
srtop.reveal('.home .content p', { delay: 200 });
srtop.reveal('.home .content .btn', { delay: 250 });
srtop.reveal('.home .social-icons li', { interval: 150 });
srtop.reveal('.home .image', { delay: 250 });

/* SCROLL ABOUT */
srtop.reveal('.about .heading', { delay: 150 });
srtop.reveal('.about .row .image', { delay: 200 });
srtop.reveal('.about .row .content', { delay: 250 });

/* SCROLL SKILLS */
srtop.reveal('.skills .heading', { delay: 150 });
srtop.reveal('.skills .container', { delay: 200 });
srtop.reveal('.skills .container .bar', { interval: 80 });

/* SCROLL EDUCATION */
srtop.reveal('.education .heading', { delay: 150 });
srtop.reveal('.education .qoute', { delay: 200 });
srtop.reveal('.education .box', { interval: 150 });

/* SCROLL PROJECTS */
srtop.reveal('.work .heading', { delay: 150 });
srtop.reveal('.work .work-card', { interval: 150 });

/* SCROLL EXPERIENCE */
srtop.reveal('.experience .heading', { delay: 150 });
srtop.reveal('.experience .timeline .container', { interval: 150 });

/* SCROLL CONTACT */
srtop.reveal('.contact .heading', { delay: 150 });
srtop.reveal('.contact .container', { delay: 200 });
