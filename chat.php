<?php
// chat.php
session_start();

// Connect to the database
$conn = new mysqli("localhost", "root", "", "register");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Create messages table if it doesn't exist
$conn->query("CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    reply TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    replied_at DATETIME DEFAULT NULL
)");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Chat With Us</title>
  <style>
    body { font-family: Arial; margin: 0; padding: 0; background: #f5f5f5; }
    .chat-box {
      width: 80%; margin: 30px auto; padding: 20px;
      background: white; border-radius: 10px;
      max-height: 500px; overflow-y: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .message {
      margin: 10px; padding: 10px; border-radius: 10px;
      max-width: 70%; word-wrap: break-word;
    }
    .you { background: #dcf8c6; text-align: right; margin-left: auto; }
    .bot { background: #eee; text-align: left; margin-right: auto; }
    .input-area {
      width: 80%; margin: auto; display: flex;
      gap: 10px; padding-top: 10px; flex-wrap: wrap;
    }
    input[type="email"], input[type="text"] {
      padding: 10px; border: 1px solid #ccc; border-radius: 5px;
      flex: 1 1 40%; min-width: 200px;
    }
    button {
      padding: 10px 20px; background-color: #007bff;
      border: none; color: white; border-radius: 5px;
      cursor: pointer; flex: 1 1 15%; min-width: 100px;
    }
    button:hover { background-color: #0056b3; }
    label {
      flex: 1 1 100%; margin: 5px 0 0 0;
      font-weight: bold;
    }
    #applause {
      display: none; text-align: center; margin: 10px; color: green; font-weight: bold;
    }

    header {
      background-color: white;
      color: black;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      position: sticky;
      top: 0;
      z-index: 999;
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
  </style>
</head>
<body>
  <header>
    <h1><img src="img/logo2.jpg" alt="Logo">TNG</h1>
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

  <h3 style="text-align:center;">Chat With Us</h3>

  <div class="chat-box" id="chatBox"></div>

  <div id="applause">👏 Message sent successfully!</div>

  <div class="input-area">
    <label for="emailInput">Your Email:</label>
    <input type="email" id="emailInput" placeholder="Enter your email" required />

    <label for="userInput">Message:</label>
    <input type="text" id="userInput" placeholder="Type your message..." required />

    <button onclick="sendMessage()">Send</button>
  </div>

  <script>
    async function loadMessages() {
      const email = document.getElementById('emailInput').value.trim();
      if (!email) return; // only load if email is filled
      const res = await fetch('fetch_messages.php?email=' + encodeURIComponent(email));
      const data = await res.json();
      const chatBox = document.getElementById('chatBox');
      chatBox.innerHTML = '';
      data.forEach(m => {
        const div = document.createElement('div');
        div.className = 'message ' + (m.sender === 'user' ? 'you' : 'bot');
        div.textContent = m.text;
        chatBox.appendChild(div);
      });
      chatBox.scrollTop = chatBox.scrollHeight;
    }

    async function sendMessage() {
      const emailInput = document.getElementById('emailInput');
      const userInput = document.getElementById('userInput');
      const applause = document.getElementById('applause');
      const email = emailInput.value.trim();
      const text = userInput.value.trim();

      if (!email) {
        alert("Please enter your email.");
        emailInput.focus();
        return;
      }
      if (!text) {
        alert("Please enter your message.");
        userInput.focus();
        return;
      }

      await fetch('send_message.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ email: email, message: text })
      });

      applause.style.display = 'block';
      setTimeout(() => applause.style.display = 'none', 3000);

      userInput.value = '';
      await loadMessages();
    }

    // Delay auto-refresh until email is entered
    setInterval(() => {
      const email = document.getElementById('emailInput').value.trim();
      if (email) loadMessages();
    }, 5000);
  </script>
</body>
</html>
