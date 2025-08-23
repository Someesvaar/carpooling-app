<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>CarPool App - Home</title>
    <style>
        /* Reset & base */
        * {
            box-sizing: border-box;
        }
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            color: #333;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background-color: #007bff;
            padding: 15px 30px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
            flex-shrink: 0;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-left img.logo {
            height: 50px;
            width: auto;
        }

        .header-left h1 {
            margin: 0;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .header-links a {
            color: white;
            font-weight: 600;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .header-links a:hover {
            background-color: #0056b3;
            color: #e2e6ea;
        }

        /* Main content */
        .main-content {
            flex: 1 0 auto; /* take remaining vertical space */
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            padding: 40px 30px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            text-align: center;
            width: 100%;
        }

        .main-content h2 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            color: #007bff;
        }

        .main-content p.description {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        /* Carousel styles */
        .carousel {
            position: relative;
            max-width: 900px;
            margin: 0 auto 40px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            height: 300px;
            width: 100%;
        }

        .carousel img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: none;
            transition: opacity 1s ease-in-out;
        }

        .carousel img.active {
            display: block;
        }

        /* Buttons group - horizontal line */
        .btn-group {
            display: flex;
            justify-content: center;
            gap: 25px;
            flex-wrap: nowrap;
            margin-bottom: 30px;
            padding: 0 10px; /* prevent overflow */
            box-sizing: border-box;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 15px 25px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
            flex: 1 1 180px; /* buttons stretch equally */
            max-width: 220px;
            white-space: nowrap;
            box-sizing: border-box;
            min-width: 0; /* fix flexbox overflow on some browsers */
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Footer links */
        .footer-links {
            text-align: center;
            margin-top: 20px;
            padding: 0 10px; /* padding for smaller screens */
            box-sizing: border-box;
        }

        .footer-links a {
            color: #007bff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: 600;
            font-size: 1rem;
            white-space: nowrap;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 700px) {
            .btn-group {
                flex-wrap: wrap;
                gap: 15px;
                padding: 0 5px;
            }
            .btn {
                flex: 1 1 45%;
                max-width: none;
                white-space: normal;
            }
        }

        @media (max-width: 400px) {
            .btn {
                flex: 1 1 100%;
                max-width: none;
            }
            .footer-links a {
                margin: 0 10px;
                display: inline-block;
                margin-bottom: 8px;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <div class="header-left">
        <img src="logo.png" alt="CarPool Logo" class="logo" />
        <h1>CarPool</h1>
    </div>
    <nav class="header-links">
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<main class="main-content">
    <!-- Image carousel -->
    <div class="carousel" id="carousel">
        <img src="https://drawdown.org/sites/default/files/solutions2020/solution_carpooling.jpg" alt="Carpool Ride 1" class="active" />
        <img src="https://latamobility.com/wp-content/uploads/2024/02/HOOP-1.png" alt="Carpool Ride 2" />
        <img src="https://moovl-blog.s3.ap-southeast-2.amazonaws.com/ultimate-guide-to-enjoy-carpooling-and-ridesharing.jpg" alt="Carpool Ride 3" />
    </div>

    <h2>Welcome to CarPool — Your Friendly Ride Sharing App</h2>
    <p class="description">
        CarPool makes it easy and affordable to share rides with others going your way. Whether you're a driver
        offering seats or a passenger looking for a ride, our platform connects you quickly and safely with trusted
        community members.
    </p>

    <div class="btn-group">
        <a href="register.php? user_type=passenger" class="btn">Register as Passenger</a>
        <a href="register.php? user_type=driver" class="btn">Register as Driver</a>
    </div>


</main>

<script>
    // Simple carousel JS
    const slides = document.querySelectorAll('#carousel img');
    let currentIndex = 0;
    const slideInterval = 4000; // 4 seconds

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % slides.length;
        showSlide(currentIndex);
    }

    setInterval(nextSlide, slideInterval);
</script>

</body>
</html>
