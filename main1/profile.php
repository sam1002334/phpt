<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../login_page/login_page.html");
    exit();
}

// Database connection details
$host = "localhost"; // Database host
$dbname = "login"; // Your database name
$username = "root"; // Your database username
$password = ""; // Your database password

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user's email from the session
$email = $_SESSION['email'];

// Fetch user data from the signup_user table using the email
$stmt = $conn->prepare("SELECT firstName, lastName, age, gender, profile_photo FROM signup_user WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

// If user data is found, fetch it
if ($stmt->num_rows > 0) {
    $stmt->bind_result($firstName, $lastName, $age, $gender, $profilePhoto);
    $stmt->fetch();
} else {
    die("User data not found.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Easy Life - Engineer Profiles</title>
  <link rel="icon" href="logo.png" type="image/png">
  <link rel="stylesheet" href="main_style.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <style>
    /* Profile Container */
    .profile {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-top: -5px;
        border-radius: 50%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }

    /* Profile photo styling */
    .profile-photo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #8e24aa;
        cursor: pointer;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile-photo:hover {
        transform: scale(1.1);
        box-shadow: 0 10px 18px rgba(0, 0, 0, 0.2);
    }

    .profile-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Profile Name */
    .profile-name {
        font-size: 20px;
        font-weight: bold;
        color: #8e24aa;
        margin-top: 12px;
        cursor: pointer;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .profile-name:hover {
        color: #f50057;
        transform: scale(1.05);
    }

    .profile-name:active {
        color: #d500f9;
        transform: scale(0.98);
    }

    /* Profile menu styling */
    .profile-menu {
        position: absolute;
        top: 140px;
        right: 20px;
        background: linear-gradient(145deg, #f3f4f7, #e1bee7);
        border: 1px solid #d1c4e9;
        border-radius: 12px;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        padding: 20px;
        width: 350px;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 1000;
        display: none;
        transition: all 0.3s ease;
    }

    .profile-menu:hover {
        background: linear-gradient(145deg, #e1bee7, #f3f4f7);
    }

    .profile-menu a {
        text-decoration: none;
        color: #8e24aa;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    .profile-menu a:hover {
        color: #f50057;
        transform: scale(1.05);
    }

    /* Profile photo small */
    .profile-photo-small {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #8e24aa;
        cursor: pointer;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .profile-photo-small:hover {
        transform: scale(1.1);
    }

    /* Profile details hidden section */
    .profile-details {
        text-align: center;
        color: #333;
        display: none; /* Initially hidden */
        margin-top: 20px;
        padding-top: 20px;
        background-color: #f3f4f7;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-top: 4px solid #8e24aa;
        width: 100%;
        transition: transform 0.3s ease;
    }

    .profile-details p {
        margin: 12px 0;
        font-size: 16px;
        color: #757575;
    }

    .profile-details p.profile-name {
        font-size: 22px;
        font-weight: bold;
        color: #8e24aa;
    }

    /* For visible profile details */
    .profile-details.visible {
        display: block;
    }

    /* Button Styling */
    .settings-button, .logout-button, .details-button {
        margin: 20px 0;
        padding: 16px 40px;
        font-size: 18px;
        border: none;
        background: linear-gradient(145deg, #8e24aa, #9c27b0);
        color: #fff;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }

    .settings-button:hover, .logout-button:hover, .details-button:hover {
        background: linear-gradient(145deg, #9c27b0, #8e24aa);
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .settings-button:active, .logout-button:active, .details-button:active {
        transform: scale(0.98);
        background: linear-gradient(145deg, #7b1fa2, #9c27b0); /* Slightly darker on click */
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .profile {
            padding: 15px;
            margin-top: 10px;
        }

        .profile-photo {
            width: 70px;
            height: 70px;
        }

        .profile-name {
            font-size: 18px;
        }

        .profile-menu {
            top: 120px;
            width: 300px;
        }

        .profile-details {
            width: 100%;
        }

        .settings-button, .logout-button, .details-button {
            padding: 14px 30px;
            font-size: 16px;
        }
    }

  </style>
</head>
<body>

  <!-- Header Section -->
  <header>
    <div class="logo">
        <a href="profile.php">
            <img src="logo1.png" alt="Easy Life Logo">
        </a>
        <a href="profile.php" class="company-name">Easy Life</a>
    </div>
    <nav>
        <ul class="desktop-nav">
            <li><a href="#services">Categories</a></li>
            <li><a href="#how-it-works">How It Works</a></li>
            <li><a href="#about-us">About Us</a></li>
            <li><a href="#contact-us">Contact Us</a></li>
        </ul>
        <div class="mobile-nav">
            <button class="hamburger" id="hamburger-btn">&#9776;</button>
            <ul id="mobile-menu" class="mobile-menu">
                <li><a href="#services">Categories</a></li>
                <li><a href="#how-it-works">How It Works</a></li>
                <li><a href="#about-us">About Us</a></li>
                <li><a href="#contact-us">Contact Us</a></li>
            </ul>
        </div>
    </nav>
     <div class="profile">
        <!-- Profile Photo Section -->
        
        <div class="profile-photo" id="profile-photo">
            <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile Photo" id="profile-img">
        </div>
        <!-- Profile Menu Section -->
        <div id="profile-menu" class="profile-menu" style="display: none;">
            <!-- Always Visible Section -->
            <div class="profile-summary">
                <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile Photo" class="profile-photo-small">
                <p class="profile-name"> <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?> </p>
                <button class="details-button" id="details-button">Show Details</button>
            </div>

            <!-- Toggleable Details Section -->
            <div class="profile-details" id="profile-details">
                <p class="profile-email">Email: <?php echo htmlspecialchars($email); ?></p>
                <p class="profile-age">Age: <?php echo htmlspecialchars($age); ?></p>
                <p class="profile-gender">Gender: <?php echo htmlspecialchars($gender); ?></p>
            </div>

            <!-- Action Buttons -->
            <button class="settings-button">Change Profile Photo</button>
            <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
            <input type="file" id="file-input" accept="image/*" style="display: none;">
        </div>
     </div>
  </header>

 <!-- Hero Section -->
  <section id="hero">
    <div class="carousel">
      <div class="slides">
        <img src="photo1.jpg" alt="Image 1" />
        <img src="photo2.webp" alt="Image 2" />
        <img src="photo3.png" alt="Image 3" />
      </div>
    </div>
    <div class="hero-text">
      <h1>Connect with Professional Engineers Instantly</h1>
      <p>Easy Life is the go-to platform for hiring engineers in any category. Create profiles or find the right professional for your needs today!</p>
      <div class="hero-buttons">
        <a href="../find_engineer/index.html">
          <button>Find Engineer</button>
        </a>
        <a href="../welcome_new_engineer/welcome.html">
          <button>Create Profile</button>
        </a>
      </div>
    </div>
  </section>





  <!-- Services Section -->
  <section id="services">
    <div class="container">
      <h2>Explore Our Engineering Categories</h2>
      <div class="categories">
        <a href="../find_engineer/index.html">
          <div class="category">
            <img src="software_engineer.jpeg" alt="Software Engineers">
            <h3>Software Engineers</h3>
            <p>Build software solutions tailored to your needs.</p>
          </div>
        </a>
        <a href="../find_engineer/index.html">
          <div class="category">
            <img src="mechanical_engineer.jpeg" alt="Mechanical Engineers">
            <h3>Mechanical Engineers</h3>
            <p>Find mechanical experts for your projects.</p>
          </div>
        </a>
        <a href="../find_engineer/index.html">
          <div class="category">
            <img src="civil_engineer.jpeg" alt="Civil Engineers">
            <h3>Civil Engineers</h3>
            <p>Get assistance with construction and planning.</p>
          </div>
        </a>
        <a href="../find_engineer/index.html" class="category-button">
          <div class="category">
            <img src="electrical_engineer.avif" alt="Electrical Engineers">
            <h3>Electrical Engineers</h3>
            <p>Get assistance with circuits and power systems.</p>
          </div>
        </a>
        <a href="../find_engineer/index.html" class="category-button">
          <div class="category">
            <img src="data_science_ai.jpg" alt="Data Science & AI">
            <h3>Data Science & AI</h3>
            <p>Explore solutions for machine learning and analytics.</p>
          </div>
        </a>
        <a href="../find_engineer/index.html" class="category-button">
          <div class="category">
            <img src="project_management.avif" alt="Project Management">
            <h3>Project Management</h3>
            <p>Streamline planning and resource allocation.</p>
          </div>
        </a>
        <a href="../find_engineer/index.html" class="category-button">
          <div class="category">
            <img src="engineer_tools.png" alt="Engineering Tools and Software">
            <h3>Engineering Tools & Software</h3>
            <p>Find tools for design and analysis.</p>
          </div>
        </a>

      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section id="how-it-works">
    <div class="container">
      <h2>How Easy Life Works</h2>
      <button class="toggle-steps-btn" id="toggle-steps-btn"></button>
      <div id="steps" class="steps">
        <div class="step">
          <img src="customer_logo.png" alt="Customer Icon" class="step-icon">
          <h3>For Customers</h3>
          <p>Search for engineers, review profiles, and book instantly.</p>
        </div>
        <div class="step">
          <img src="engineer_icon.png" alt="Engineer Icon" class="step-icon">
          <h3>For Engineers</h3>
          <p>Create a professional profile and start receiving job offers.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- About Us Section -->
  <section id="about-us">
    <div class="container">
      <h2>About Us</h2>
      <p>Easy Life is a platform dedicated to connecting skilled engineers with clients in need of their expertise. Whether you're a company looking for engineers for a project or an individual seeking specialized services, we offer a seamless experience to help you find the right professional. Our mission is to make engineering services more accessible and efficient, empowering both engineers and clients to connect effortlessly.</p>
    </div>
  </section>

  <!-- Blog Section -->
  <section id="blog">
    <div class="container">
      <h2>Our Blogs</h2>
      
      <!-- First Blog: About Our Company -->
      <div class="blog-post">
        <h3>About Easy Life</h3>
        <p>Easy Life is revolutionizing how businesses and individuals connect with engineers. By leveraging the power of technology, we provide a simple platform where engineers can create detailed profiles showcasing their skills, while clients can easily find the right talent for their projects. Our platform covers multiple engineering categories, ensuring that you can always find the perfect match.</p>
        <a href="../blogs/blogs.html" class="read-more">Read More</a>
      </div>
      
      <!-- Second Blog: How It Works -->
      <div class="blog-post">
        <h3>How Easy Life Works</h3>
        <p>Easy Life connects customers and engineers through a seamless platform. Customers can browse profiles, request specific services, and instantly book the engineer that fits their needs. On the other hand, engineers can create detailed professional profiles, receive project requests, and grow their career by working on a variety of exciting projects. Here's how it works in detail...</p>
        <a href="../blogs/blogs.html" class="read-more">Read More</a>
      </div>
    </div>
  </section>


  <!-- Contact Us Section -->
  <section id="contact-us">
    <div class="container">
        <h2>Contact Us</h2>
        <p>We'd love to hear from you! If you have any questions or need assistance, feel free to reach out to us.</p>
        <form action="contact.php" method="POST">
            <!-- Web3Forms access key -->
            <input type="hidden" name="access_key" value="b47f4da1-cee0-47c9-9907-9c706eed0f5d">
            <div class="contact_input_area">
                <div id="success_fail_info"></div>
                <div class="row">
                    <!-- Form Group -->
                    <div class="col-12">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Name" required="required">
                        </div>
                    </div>
                    <!-- Form Group -->
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email" required="required">
                        </div>
                    </div>
                    <!-- Form Group -->
                    <div class="col-12 col-lg-6">
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control" placeholder="Subject" required="required">
                        </div>
                    </div>
                    <!-- Form Group -->
                    <div class="col-12">
                        <div class="form-group">
                            <textarea rows="6" name="message" class="form-control" placeholder="Your Message" required="required"></textarea>
                        </div>
                    </div>
                    <!-- Button -->
                    <div class="col-12 text-center">
                        <button type="submit" value="Send message" name="submit" id="submitButton" class="btn btn-secondary ct_btn" title="Submit Your Message!">Send Message</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p>&copy; 2024 Easy Life. All Rights Reserved.</p>
      <div class="social-media">
        <a class="social-icon" href="#"><img src="986944_facebook_icon.png" alt="Facebook"></a>
        <a class="social-icon" href="#"><img src="2951729_chat_social network_tweet_twitter_icon.png" alt="Twitter"></a>
        <a class="social-icon" href="#"><img src="2609558_instagram_social media_social_icon.png" alt="Instagram"></a>
      </div>
    </div>
  </footer>
  
  <script>
    // Toggle mobile menu visibility
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
  
    // Toggle menu on button click
    hamburgerBtn.addEventListener('click', function (e) {
      e.stopPropagation(); // Prevent the click from propagating to the document
      mobileMenu.classList.toggle('active');
    });
  
    // Hide menu on scroll
    window.addEventListener('scroll', function () {
      if (mobileMenu.classList.contains('active')) {
        mobileMenu.classList.remove('active');
      }
    });
  
    // Hide menu when clicking anywhere outside
    document.addEventListener('click', function (e) {
      // Check if the click is outside the menu and the hamburger button
      if (
        !mobileMenu.contains(e.target) &&
        !hamburgerBtn.contains(e.target) &&
        mobileMenu.classList.contains('active')
      ) {
        mobileMenu.classList.remove('active');
      }
    });
    
  
    // Toggle How It Works section visibility
    document.getElementById('toggle-steps-btn').addEventListener('click', function () {
      const steps = document.getElementById('steps');
      steps.classList.toggle('active');
    });
  </script>

  <script>
    const slides = document.querySelector('.slides');
    const slideImages = document.querySelectorAll('.slides img');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');

    let currentIndex = 0; // Track the current slide index
    const slideCount = slideImages.length;
    const interval = 7000; // Slow down the auto-slide interval (7 seconds)

    // Function to go to the next slide
    function nextSlide() {
      currentIndex = (currentIndex + 1) % slideCount;
      updateSlidePosition();
    }

    // Function to go to the previous slide
    function prevSlide() {
      currentIndex = (currentIndex - 1 + slideCount) % slideCount;
      updateSlidePosition();
    }

    // Function to update the slide position
    function updateSlidePosition() {
      // Ensure only one image is visible at a time
      const offset = -currentIndex * 100; // Calculate the correct offset
      slides.style.transform = `translateX(${offset}%)`;
    }

    // Auto-slide functionality
    let slideInterval = setInterval(nextSlide, interval);

    // Event listeners for navigation buttons
    nextButton.addEventListener('click', () => {
      clearInterval(slideInterval); // Stop auto-slide on user interaction
      nextSlide();
      slideInterval = setInterval(nextSlide, interval); // Restart auto-slide
    });

    prevButton.addEventListener('click', () => {
      clearInterval(slideInterval); // Stop auto-slide on user interaction
      prevSlide();
      slideInterval = setInterval(nextSlide, interval); // Restart auto-slide
    });

    // Initialize the first slide to ensure it shows immediately
    updateSlidePosition();



  </script>

  <script>
    // Smooth scroll behavior for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default behavior of anchor link

        // Get the target element's position
        const targetId = this.getAttribute('href').substring(1); // Remove '#' from href
        const targetElement = document.getElementById(targetId);

        // Scroll smoothly to the target element
        targetElement.scrollIntoView({
          behavior: 'smooth', // Smooth scroll effect
          block: 'start' // Align to the top of the element
        });
      });
    });

  </script>

  <script>
    // Function to detect if an element is in view
    function isElementInView(el) {
      const rect = el.getBoundingClientRect();
      return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
      );
    }

    // Add 'visible' class when section is in view
    function animateOnScroll() {
      const sections = document.querySelectorAll('section');
      sections.forEach(section => {
        if (isElementInView(section)) {
          section.classList.add('visible');
        }
      });
    }

    // Check visibility on scroll
    window.addEventListener('scroll', animateOnScroll);

    // Check visibility on page load
    document.addEventListener('DOMContentLoaded', animateOnScroll);

  </script>
  <script>
    const syncPointer = ({ x: pointerX, y: pointerY }) => {
      const x = pointerX.toFixed(2)
      const y = pointerY.toFixed(2)
      const xp = (pointerX / window.innerWidth).toFixed(2)
      const yp = (pointerY / window.innerHeight).toFixed(2)
      document.documentElement.style.setProperty('--x', x)
      document.documentElement.style.setProperty('--xp', xp)
      document.documentElement.style.setProperty('--y', y)
      document.documentElement.style.setProperty('--yp', yp)
    }
    document.body.addEventListener('pointermove', syncPointer)
      
  </script>
  <script>
    // Function to adjust hero section height based on viewport size
    function adjustHeroHeight() {
      const heroSection = document.getElementById('hero');
      const viewportHeight = window.innerHeight;
      heroSection.style.height = `${viewportHeight}px`;
    }

    // Adjust on initial load and when the window is resized
    window.addEventListener('load', adjustHeroHeight);
    window.addEventListener('resize', adjustHeroHeight);

  </script>
  <script>
    const header = document.querySelector('header');
    const heroSection = document.getElementById('hero');
    let lastScrollY = window.scrollY;
    let hideHeaderTimeout;
  
    // Show the header
    const showHeader = () => {
      header.style.top = '0';
      clearTimeout(hideHeaderTimeout); // Clear any existing hide timeout
    };
  
    // Hide the header
    const hideHeader = () => {
      if (!isAtStartingPosition() && !isInHeroSection()) {
        header.style.top = '-100px';
      }
    };
  
    // Check if user is at the starting position (top of the page)
    const isAtStartingPosition = () => {
      return window.scrollY === 0;
    };
  
    // Check if user is in the hero section
    const isInHeroSection = () => {
      const heroRect = heroSection.getBoundingClientRect();
      return heroRect.top <= 0 && heroRect.bottom > 0; // Fully or partially visible in the viewport
    };
  
    // Automatically hide header after inactivity
    const startHideHeaderTimer = () => {
      clearTimeout(hideHeaderTimeout); // Clear any existing timeout
      if (!isInHeroSection() && !isAtStartingPosition()) {
        hideHeaderTimeout = setTimeout(() => {
          hideHeader();
        }, 3000); // Hide after 3 seconds of inactivity
      }
    };
  
    // Handle scroll events
    window.addEventListener('scroll', () => {
      if (isInHeroSection() || isAtStartingPosition()) {
        showHeader(); // Always show header in hero section or at the start
      } else if (window.scrollY < lastScrollY) {
        showHeader(); // Show header when scrolling up
      } else {
        hideHeader(); // Hide header when scrolling down
      }
      lastScrollY = window.scrollY;
      startHideHeaderTimer(); // Restart hide timer
    });
  
    // Handle mouse movement near the top
    window.addEventListener('mousemove', (e) => {
      if (e.clientY < 50) {
        showHeader(); // Show header when mouse is near the top
      }
      startHideHeaderTimer(); // Restart hide timer
    });
  
    // Initial check to always show the header at starting position
    if (isAtStartingPosition() || isInHeroSection()) {
      showHeader();
    }
  </script>
  <script src="profile.js"></script>
</body>
</html>
