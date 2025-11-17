<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Fishing Planet API Docs</title>
    <link rel="stylesheet"
          href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.onload = () => {
            SwaggerUIBundle({
                url: "swagger.php", // swagger JSON endpoint u istom folderu
                dom_id: "#swagger-ui",
                presets: [
                    SwaggerUIBundle.presets.apis,
                ],
            });
        };
    </script>
</body>
</html>
