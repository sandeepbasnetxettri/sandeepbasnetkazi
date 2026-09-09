# Sandeep Basnet - Personal Portfolio & Web Showcase

Official personal portfolio website for **Sandeep Basnet** showcasing web development projects, skills, professional experience, photography gallery, and technical resources.

- **Live URL:** [https://www.sandhyabasnet.com.np](https://www.sandhyabasnet.com.np)
- **GitHub Repository:** [sandeepbasnetxettri/sandeepbasnetkazi](https://github.com/sandeepbasnetxettri/sandeepbasnetkazi)

---

## 📁 Project Structure & File Reference

```text
d:\sandeepbasnet\
├── 404.html                        # Custom 404 error page
├── all cv.pdf                      # Comprehensive CV document
├── CNAME                           # Custom domain configuration for GitHub Pages
├── gallery.html                    # Responsive photography & work gallery page
├── index.html                      # Main landing page and portfolio
├── Sandeep_Basnet_CV.pdf           # Professional resume / Curriculum Vitae
├── skills.json                     # Dynamic JSON dataset of technical skills & icons
├── README.md                       # Project documentation
│
├── assets/                         # Core site styling, scripts, and media
│   ├── css/                        # Stylesheets (style.css, 404.css)
│   ├── js/                         # Logic (script.js, app.js, particles.min.js, 404.js)
│   └── images/                     # UI graphics, logos, and illustrations
│
├── book/                           # "Notes for Professionals" technical reference library
│   ├── AndroidNotesForProfessionals.pdf
│   ├── CSSNotesForProfessionals.pdf
│   ├── HTML5CanvasNotesForProfessionals.pdf
│   ├── HTML5NotesForProfessionals.pdf
│   ├── JavaScriptNotesForProfessionals.pdf
│   ├── MicrosoftSQLServerNotesForProfessionals.pdf
│   ├── PHPNotesForProfessionals.pdf
│   ├── PythonNotesForProfessionals.pdf
│   └── iOSNotesForProfessionals.pdf
│
├── gallery/                        # High-resolution gallery photographs (1.jpeg – 35.jpg)
└── image/                          # Site icons, profile pictures, and badges (1.jpg – 20.jpg)
```

---

## 📄 File & Directory Breakdown

### Core Web Pages
- **`index.html`**  
  The main entry point of the website. Features an animated hero section with `particles.js` and a typed subtitle, an "About Me" section with quick download links to CV files, dynamic skills grid, education timeline, project showcases, work experience, and an interactive contact form.
  
- **`gallery.html`**  
  A dedicated visual gallery displaying photography and media collections. Styled with Tailwind CSS, custom fonts (Pacifico, Poppins), Remix Icons, and integrated with Apache ECharts for interactive visual insights.

- **`404.html`**  
  Custom error fallback page with dedicated styling (`assets/css/404.css`) and interactive scripts (`assets/js/404.js`) to redirect lost visitors back to the home page.

---

### Data & Configuration Files
- **`CNAME`**  
  Contains the custom domain binding (`www.sandhyabasnet.com.np`) for GitHub Pages DNS mapping.

- **`skills.json`**  
  Data file containing an array of technical skill objects (e.g., Firebase, Android, Git, Figma, Node.js, Bootstrap, HTML5, CSS3, JavaScript, Java, Kotlin) along with corresponding Icon8 badges used to dynamically render the skills section.

---

### Documents & Resumes
- **`Sandeep_Basnet_CV.pdf`**  
  The official, concise CV/resume highlighting professional software development and technical expertise.

- **`all cv.pdf`**  
  Extended curriculum vitae document accessible via the main landing page "About Me" button.

---

### Assets & Media Folders
- **`assets/`**  
  - **`css/`**: Contains `style.css` (primary site styling, animations, navbar transitions, responsive media queries) and `404.css`.
  - **`js/`**: Contains `script.js` (skills rendering, scroll triggers, mobile menu toggle, email submission), `particles.min.js` (interactive canvas particle effects), `app.js`, and `404.js`.
  - **`images/`**: Houses project thumbnails, educational institute emblems, and UI icons.

- **`book/`**  
  A curated collection of developer reference guides covering Android, CSS, HTML5, JavaScript, Python, PHP, Microsoft SQL Server, and iOS for quick access and download.

- **`gallery/`**  
  Contains 35 curated gallery images (`1.jpeg` through `35.jpg`) optimized for the responsive grid layout in `gallery.html`.

- **`image/`**  
  Contains main profile pictures, portfolio assets, favicons (`image/19.png`), and social preview graphics.

---

## 🛠️ Built With

- **Markup & Styling:** HTML5, CSS3, Vanilla CSS, Tailwind CSS (in `gallery.html`)
- **JavaScript & Libraries:**
  - [Particles.js](https://vincentgarreau.com/particles.js/) – Interactive particle background
  - [Typed.js](https://mattboldt.com/demos/typed-js/) – Dynamic typing animation in hero banner
  - [ECharts](https://echarts.apache.org/) – Data visualizations
  - [Font Awesome & Remix Icons](https://fontawesome.com/) – Vector iconography
- **Hosting & Deployment:** GitHub Pages with Custom Domain via `CNAME`

---

## 🚀 Local Development

To run and preview the site locally:

1. Clone or open the directory:
   ```bash
   cd d:\sandeepbasnet
   ```
2. Serve using any local HTTP server (such as Python, Live Server, or Node `serve`):
   ```bash
   # Using Python 3
   python -m http.server 8000

   # Or using npx serve
   npx serve .
   ```
3. Open `http://localhost:8000` in your web browser.
