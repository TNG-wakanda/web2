<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Contact</title>
    <style>
        body {
            background-color: lightcyan;
            width: 50%;
            margin-left: 200px;
            font-family: 'Courier New', Courier, monospace;
            text-align: center;
            height: auto;
        }

        header {
            color: black;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            position: fixed;
            top: 0;
            background: white;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        header h1 {
            margin: 0;
            display: flex;
            align-items: center;
        }

        header img {
            width: 50px;
            height: 30px;
            margin-right: 10px;
            border-radius: 15px;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 20px;
        }

        nav li {
            position: relative;
        }

        nav a {
            text-decoration: none;
            color: black;
            padding: 7px 15px;
            display: block;
            font-size: large;
            font-weight: bolder;
            font-family: 'Courier New', Courier, monospace;
        }

        nav a:hover {
            color: blue;
        }

        .submenu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: gainsboro;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .submenu a {
            color: black;
            margin: 5px 0;
        }

        nav li:hover .submenu {
            display: block;
        }

        h2 {
            text-align: center;
            color: darkblue;
            font-size: large;
            margin-top: 90px; /* account for fixed header height */
        }

        p {
            text-align: center;
            font-size: medium;
            color: darkblue;
            flex-direction: column;
        }

        .container {
            flex-wrap: wrap;
            justify-content: center;
            background-color: blanchedalmond;
            padding: 20px;
            border: 2px solid darkblue;
            box-shadow: 7px 7px 7px 0 black;
            border-radius: 25px;
            margin: 20px auto;
            width: 60%;
        }
    </style>
</head>
<body>
    <header>
        <h1><img src="img/logo2.jpg" alt="Logo" />TNG</h1>
        <nav>
            <ul>
                <li><a href="webpage.php">Home</a></li>
                <li>
                    <a href="#">Products</a>
                    <div class="submenu">
                        <a href="product1.php">Product 1</a>
                        <a href="product2.php">Product 2</a>
                        <a href="product3.php">Product 3</a>
                    </div>
                </li>
                <li><a href="contact.php">Contacts</a></li>
            </ul>
        </nav>
    </header>

    <h2>CONTACT US</h2>
    <div class="container">
        <p>For any inquiries reach out to us at:</p>
        <p>Email: nshimiyimanagad24@gmail.com</p>
        <p>Phone: +250 793 084 254</p>
        <p>Address: Kigali, Rwanda</p>
        <p>We are here to help with any question or concern you may have.</p>
        <p>Thank you 🥰😍 for visiting our website.</p>
        <p>We are looking forward to hearing from you.</p>
        <p>Have a great and nice day.</p>
        <p>Keep improving.</p>
        <p>Keep learning.</p>
        <p>See you 👌🎉</p>
    </div>
</body>
</html>
