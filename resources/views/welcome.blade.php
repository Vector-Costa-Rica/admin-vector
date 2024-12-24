<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Vector Costa Rica Manager</title>

    <!-- Incluye los estilos compilados de Mix -->
    <link href="{{ ('css/app.css') }}" rel="stylesheet">
</head>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Figtree, sans-serif;
        background-color: #000;
        overflow: hidden;
        color: #F9FAF5;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
    }

    .container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
        text-align: center;
    }


    h1, p, footer {
        font-size: 2.5rem;
        color: #F9FAF5;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
        animation: fadeInOpacity 0.8s ease-out forwards;
        opacity: 0;
    }

    @keyframes fadeInOpacity {
        0% {
            opacity: 0;
        }
        100% {
            opacity: 1;
        }
    }

    p {
        font-size: 1.2rem;
        color: #F9FAF5;
    }

    footer {
        font-size: 1rem;
        position: absolute;
        bottom: 20px;
        width: 100%;
        text-align: center;
        color: #F9FAF5;
    }

    .login-button {
        display: inline-block;
        padding: 1rem 2rem;
        margin-top: 2rem;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        color: #fff;
        text-decoration: none;
        font-size: 1.2rem;
        border-radius: 10px;
        text-align: center;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .login-button:hover {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .circle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.3), transparent 60%, transparent 100%);
        filter: blur(100px);
        animation: moveCircle 60s infinite ease-in-out, colorChange 120s infinite ease-in-out;
        opacity: 0.9;
        mix-blend-mode: screen;
        z-index: 1;
        will-change: transform, opacity;
    }

    @keyframes moveCircle {
        0% {
            transform: translate(0, 0) scale(1);
        }
        25% {
            transform: translate(50vw, -30vh) scale(1.2);
        }
        50% {
            transform: translate(-40vw, 40vh) scale(0.8);
        }
        75% {
            transform: translate(30vw, -50vh) scale(1.3);
        }
        100% {
            transform: translate(0, 0) scale(1);
        }
    }

    @keyframes colorChange1 {
        0% {
            background: radial-gradient(circle, rgba(255, 92, 104, 0.3), transparent 60%, transparent 100%);
        }
        25% {
            background: radial-gradient(circle, rgba(160, 215, 231, 0.3), transparent 60%, transparent 100%);
        }
        50% {
            background: radial-gradient(circle, rgba(255, 204, 92, 0.3), transparent 60%, transparent 100%);
        }
        75% {
            background: radial-gradient(circle, rgba(186, 255, 104, 0.3), transparent 60%, transparent 100%);
        }
        100% {
            background: radial-gradient(circle, rgba(255, 92, 104, 0.3), transparent 60%, transparent 100%);
        }
    }

    @keyframes colorChange2 {
        0% {
            background: radial-gradient(circle, rgba(186, 255, 104, 0.3), transparent 60%, transparent 100%);
        }
        25% {
            background: radial-gradient(circle, rgba(92, 179, 255, 0.3), transparent 60%, transparent 100%);
        }
        50% {
            background: radial-gradient(circle, rgba(255, 104, 230, 0.3), transparent 60%, transparent 100%);
        }
        75% {
            background: radial-gradient(circle, rgba(104, 255, 196, 0.3), transparent 60%, transparent 100%);
        }
        100% {
            background: radial-gradient(circle, rgba(186, 255, 104, 0.3), transparent 60%, transparent 100%);
        }
    }

    @keyframes colorChange3 {
        0% {
            background: radial-gradient(circle, rgba(104, 179, 255, 0.3), transparent 60%, transparent 100%);
        }
        25% {
            background: radial-gradient(circle, rgba(255, 92, 160, 0.3), transparent 60%, transparent 100%);
        }
        50% {
            background: radial-gradient(circle, rgba(104, 255, 104, 0.3), transparent 60%, transparent 100%);
        }
        75% {
            background: radial-gradient(circle, rgba(255, 104, 92, 0.3), transparent 60%, transparent 100%);
        }
        100% {
            background: radial-gradient(circle, rgba(104, 179, 255, 0.3), transparent 60%, transparent 100%);
        }
    }

    @keyframes colorChange4 {
        0% {
            background: radial-gradient(circle, rgba(255, 104, 230, 0.3), transparent 60%, transparent 100%);
        }
        25% {
            background: radial-gradient(circle, rgba(104, 255, 160, 0.3), transparent 60%, transparent 100%);
        }
        50% {
            background: radial-gradient(circle, rgba(92, 160, 255, 0.3), transparent 60%, transparent 100%);
        }
        75% {
            background: radial-gradient(circle, rgba(255, 160, 92, 0.3), transparent 60%, transparent 100%);
        }
        100% {
            background: radial-gradient(circle, rgba(255, 104, 230, 0.3), transparent 60%, transparent 100%);
        }
    }


    .circle:nth-child(1) {
        width: 500px;
        height: 500px;
        top: 10%;
        left: 15%;
        animation: moveCircle 60s infinite ease-in-out, colorChange1 120s infinite ease-in-out;
    }

    .circle:nth-child(2) {
        width: 700px;
        height: 700px;
        top: 40%;
        left: 60%;
        animation: moveCircle 50s infinite ease-in-out, colorChange2 110s infinite ease-in-out;
    }

    .circle:nth-child(3) {
        width: 600px;
        height: 600px;
        top: 70%;
        left: 30%;
        animation: moveCircle 70s infinite ease-in-out, colorChange3 130s infinite ease-in-out;
    }

    .circle:nth-child(4) {
        width: 800px;
        height: 800px;
        top: 20%;
        left: 70%;
        animation: moveCircle 55s infinite ease-in-out, colorChange4 115s infinite ease-in-out;
    }
</style>
<body>
<!-- Círculos difuminados -->
<div class="circle"></div>
<div class="circle"></div>
<div class="circle"></div>
<div class="circle"></div>

<!-- Contenedor principal -->
<div class="container">
    <h1>Welcome to Vector Costa Rica Manager</h1>
    <p>Taking concepts to the next level, together. We innovate, create, and overcome challenges with every project.</p>


    <!-- Botón de iniciar sesión centrado -->
    <div>
        <a href="{{ route('saml2.login') }}" class="login-button">Login</a>
    </div>
</div>

<!-- Footer centrado -->
<footer>
    Vector Costa Rica Manager v1.0

</footer>
</body>
</html>
