<?php
include("database.php");
session_start();
?>
<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magic Bites</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/drawer/3.2.2/css/drawer.min.css">
</head>

<body id="IndexBody">
    <div class="drawer-overlay"></div>
    <header>
        <div class="top-bar">
            <div class="left-section">
                <button type="button" class="menu-icon" id="menu-toggle">☰</button>
                <img width="50px" height="50px" src="images/logo.png">
                <h1 id="magic" style="margin-left: 15px;">Magic Bites</h1>
            </div>
            <div class="right-section">
                <form action="listrecipe.php" method="GET" class="search-bar">
                    <input type="text" name="search" placeholder="Search Recipes">
                    <button type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
                <?php

                if (isset($_SESSION['user_id'])) {
                    $username = $_SESSION['username'];
                    echo "<a href='account.php' class='login-icon'>👤 $username</a>";
                } else {
                    echo "<a href='Login.php' class='login-icon'>👤 Log In</a>";
                } ?>

            </div>
        </div>
    </header>

    <nav class="drawer-nav" role="navigation">
        <ul class="drawer-menu">
            <li>
                <p class="drawer-brand">
                    <img width="50px" height="50px" src="images/logo.png" alt="Magic Bites Logo" style="float:left">
                    Magic Bites
                </p>
            </li>
            <li><a href="listrecipe.php?category=all">All Recipes</a></li>
            <li>Main Meals <i class="fa fa-chevron-up arrow" style="float:right"></i>
                <ul class="sublist">
                    <li><a href="listrecipe.php?type=Breakfast&category=MainCourse">Breakfast</a></li>
                    <li><a href="listrecipe.php?type=Lunch&category=MainCourse">Lunch</a></li>
                    <li><a href="listrecipe.php?type=Dinner&category=MainCourse">Dinner</a></li>
                </ul>
            </li>
            <li><a href="listrecipe.php?type=NULL&category=Sides">Sides</a></li>
            <li>Desserts <i class="fa fa-chevron-up arrow" style="float:right"></i>
                <ul class="sublist">
                    <li><a href="listrecipe.php?type=Hot&category=Desserts">Hot</a></li>
                    <li><a href="listrecipe.php?type=Cold&category=Desserts">Cold</a></li>
                    <li><a href="listrecipe.php?type=Fried&category=Desserts">Fried</a></li>
                </ul>
            </li>

            <li>Drinks <i class="fa fa-chevron-up arrow" style="float:right"></i>
                <ul class="sublist">
                    <li><a href="listrecipe.php?type=Hot&category=Drinks">Hot</a></li>
                    <li><a href="listrecipe.php?type=Iced&category=Drinks">Iced</a></li>
                </ul>
            </li>
            <li><a href="#section">About us</a></li>
        </ul>
    </nav>
    <style>
        .recipes-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
            height: 20rem;
            background-image: url(images/SideHover.png);
            background-repeat: no-repeat;
            background-size: cover;
        }

        .recipe-card {
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
            background-image: url(images/sideBackground2.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .recipe-card img {
            width: 100%;
            height: auto;
        }

        .recipe-card h4 {
            font-size: 18px;
            margin: 15px;
            text-align: center;
        }

        .recipe-card a {
            display: block;
            text-align: center;
            margin: 10px;
            padding: 10px;
            background-color: orange;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .recipe-card:hover {
            transform: scale(1.05);
        }
    </style>
    <main role="main">
        <div>
            <img style="width: 100%; height:42rem" src="images/mainback.jpg" alt="">
        </div>
        <div>
            <h1 style="text-align:center; border-bottom-style:solid; border-bottom-color:orange">Featured Recipes</h1>
        </div>
        <?php
        // Fetch user's recipes
        $recipe_query = "SELECT * FROM recipes
                 WHERE avg_rating>=4.0";
        $recipe_stmt = $conn->prepare($recipe_query);
        $recipe_stmt->execute();
        $recipes_result = $recipe_stmt->get_result();
        ?>
        <div class="recipes-container">

            <?php while ($recipe = $recipes_result->fetch_assoc()): ?>
                <div class="recipe-card">
                    <?php
                    $recipe_image = !empty($recipe['image']) ? "uploads/" . htmlspecialchars($recipe['image']) : "images/default-recipe.jpg";
                    ?>
                    <img src="<?= $recipe_image ?>" style="height:150px" alt="Recipe Image">
                    <h4><?= htmlspecialchars($recipe['title']) ?></h4>
                    <a href="view_recipe.php?id=<?= htmlspecialchars($recipe['recipe_id']) ?>">View Recipe</a>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/drawer/3.2.2/js/drawer.min.js"></script>
    <script>
        $(document).ready(function () {
            const body = $('body');
            const drawerOverlay = $('.drawer-overlay');
            const drawerNav = $('.drawer-nav');
            $('#menu-toggle').on('click', function () {
                body.toggleClass('drawer-open');
            });
            drawerOverlay.on('click', function () {
                body.removeClass('drawer-open');
            });
            drawerNav.on('click', function (event) {
                event.stopPropagation();
            });
            $('.arrow').on('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                const parent = $(this).parent();
                parent.toggleClass('open');
                parent.find('.sublist').slideToggle();
                $(this).toggleClass('fa-chevron-down fa-chevron-up');
            });
        });



    </script>
    <div id="container4">
        <h1 id="section"> About</h1>
        <div id="aboutrow">
            <div class="aboutcol">
                <h1>About Us</h1>
                <p>
                    Welcome to Magic Bites, your ultimate culinary hub where food enthusiasts from all corners of the
                    globe come together to share, discover, and savor delicious recipes. Whether you're a seasoned chef
                    or a kitchen novice, our community-driven platform is designed to inspire your culinary creativity.
                    From time-tested family recipes to innovative gourmet dishes, join us on a gastronomic adventure and
                    connect with fellow food lovers who share your passion for all things delicious. Let's make every
                    meal a celebration!
                </p>
            </div>
            <div class="aboutcol">
                <div id="aboutimg">
                    <img src="images/about.jpg" alt="">
                </div>
            </div>
        </div>
    </div>
    <div id="container5">
        <h1 id="section1">Contact</h1>
        <div id="contact_row">
            <div class="contact_col">
                <div>
                    <p>
                        <i class="fa fa-map-marker"></i>
                        Lebanese University,Hadath
                    </p>
                    <p>
                        <a href="mailto: alisayegh555@gmail.com">
                            <i class="fa fa-envelope"></i>
                            alisayegh555@gmail.com
                        </a>
                    </p>
                    <p>
                        <a href="tel:+96178849025">
                            <i class="fa fa-phone-square"></i>
                            +96178849025
                        </a>
                    </p>
                    <h3>Follow Us</h3>
                    <p id="social">
                        <a id="facebook" href="https://www.facebook.com/ali.sayegh.5473"><i
                                class="fa-brands fa-square-facebook fa-2x"></i></a>
                        <a id="instagram" href="https://www.instagram.com/alisayegh555/?hl=en"><i
                                class="fa-brands fa-square-instagram fa-2x"></i></a>
                        <a id="twitter" href="https://twitter.com/shadowali58"><i
                                class="fa-brands fa-square-twitter fa-2x"></i></a>
                        <a id="youtube" href="https://www.youtube.com/channel/UCRuwV4V9jP0wje6TryylFVQ"><i
                                class="fa-brands fa-square-youtube fa-2x"></i></a>
                    </p>
                </div>
            </div>
            <div class="contact_col">
                <form>
                    <h2>Get in touch</h2>
                    <input type="text" placeholder="Name">
                    <input type="email" placeholder="Email">
                    <input type="text" placeholder="Subject">
                    <textarea rows="6" placeholder="Type Message"></textarea>
                    <button mailto:alisayegh555@gmail.com>Send Message</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>