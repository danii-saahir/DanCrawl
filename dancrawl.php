<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DanCrawl v1.0</title>
    <style>
        body {
            background-color: #111;
            font-family: 'Courier New', monospace;
            color: #0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #111;
            border: 1px solid #0f0;
            border-radius: 5px;
            margin-top: 20px;
        }

        h1 {
            font-size: 36px;
            text-align: center;
            margin-bottom: 20px;
            color: #0f0;
        }

        h2 {
            font-size: 24px;
            text-align: center;
            margin-top: 10px;
            color: #0f0;
        }

        a {
            color: #0f0;
            text-decoration: underline;
            word-wrap: break-word;
        }

        #result {
            background-color: #111;
            border-radius: 5px;
            margin: 20px 0;
            padding: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        .form-container {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        .form-container label {
            flex: 1;
            padding-right: 10px;
        }

        .form-container input {
            flex: 2;
            padding: 5px;
            background-color: #000;
            border: 1px solid #0f0;
            color: #0f0;
        }

        button {
            background-color: #0f0;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
            font-family: 'Courier New', monospace;
        }

        button:hover {
            background-color: #66ff66;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .form-container {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-container label {
                flex: none;
            }

            .form-container input {
                flex: none;
                width: 100%;
            }

            button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST['url'])) {
            $url = $_POST['url'];

            if (empty($url)) {
                echo '<p>Enter a URL or domain</p>';
            } else {
                if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                    $url = 'https://' . $url;
                }

                try {
                    $html = file_get_contents($url);
                    if ($html === false) {
                        throw new Exception('Failed to fetch URL content');
                    }

                    $matches = [];
                    preg_match_all('/href=["\'](https?:\/\/[^"\']+)/', $html, $matches);

                    $links = $matches[1];

                    echo '<div id="result">';
                    echo '<button onclick="exportToTxt()">Export to Text File</button>';
                    echo '<p>Found ' . count($links) . ' links</p>';
                    echo '<ul>';
                    foreach ($links as $link) {
                        echo '<li><a href="' . htmlspecialchars($link) . '">' . htmlspecialchars($link) . '</a></li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                } catch (Exception $e) {
                    echo '<p>Error: ' . $e->getMessage() . '</p>';
                }
            }
        } else {
            echo '<p>Enter URL or domain</p>';
        }
    } else {
        echo '<h1>DanCrawl v1.0</h1>';
        echo '<h2>Author: <a href="https://github.com/danii-saahir">Danii Saahir</a></h2>';
        echo '<form method="post">';
        echo '<div class="form-container">';
        echo '<label for="url">Enter URL or domain:</label>';
        echo '<input type="text" id="url" name="url" required>';
        echo '</div>';
        echo '<button type="submit">Crawl</button>';
        echo '</form>';
        echo '<div id="result"></div>';
        echo '<script>
            function exportToTxt() {
              const links = document.querySelectorAll("#result ul li a");
              const linkArray = Array.from(links).map(link => link.href);
              const linkText = linkArray.join("\\n");
              const blob = new Blob([linkText], { type: "text/plain" });
              const url = URL.createObjectURL(blob);
              const a = document.createElement("a");
              a.style.display = "none";
              a.href = url;
              a.download = "links.txt";
              document.body.appendChild(a);
              a.click();
              window.URL.revokeObjectURL(url);
            }

            document.querySelector("form").addEventListener("submit", function (e) {
              e.preventDefault();
              const url = document.querySelector("#url").value;
              fetch("", {
                method: "POST",
                body: new URLSearchParams({ url })
              })
                .then(response => response.text())
                .then(data => {
                  document.querySelector("#result").innerHTML = data;
                })
                .catch(error => {
                  console.error("Error:", error.message);
                });
            });
            </script>';
    }
    ?>
</div>
</body>
</html>
