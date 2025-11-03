<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disease Tracker - Heat Map System</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: radial-gradient(circle, #f8fff9, #e9f7ee);
            color: #0f5132;
            min-height: 100vh;
        }
        
        header {
            background-color: #0f5132;
            color: #ffffff;
            padding: 1rem 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        header h1 {
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Disease Tracker</h1>
        </div>
    </header>
