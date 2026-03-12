<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>System Maintenance - CpsuVotewisely</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700"
        rel="stylesheet" />
    <style>
        :root {
            --cpsu-green: #166534;
            --cpsu-gold: #facc15;
            --cpsu-green-light: #22c55e;
            --cpsu-green-dark: #14532d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        .background-decoration {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .background-decoration::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: linear-gradient(135deg, rgba(22, 101, 52, 0.08) 0%, rgba(250, 204, 21, 0.05) 100%);
            border-radius: 50%;
            filter: blur(80px);
        }

        .background-decoration::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.05) 0%, rgba(22, 101, 52, 0.08) 100%);
            border-radius: 50%;
            filter: blur(80px);
        }

        .maintenance-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1200px;
            width: 100%;
            min-height: 600px;
            position: relative;
            z-index: 1;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .left-section {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            padding: 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .left-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .logo-badge {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .logo-badge svg {
            width: 32px;
            height: 32px;
            color: white;
        }

        .left-section h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .left-section p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .right-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* 3D Animated Gears CSS */
        .gears-container {
            position: relative;
            width: 180px;
            height: 180px;
            margin-bottom: 2.5rem;
            filter: drop-shadow(0 15px 25px rgba(22, 101, 52, 0.15));
        }

        .gear {
            position: absolute;
            transform-origin: center;
        }

        .gear-main {
            width: 120px;
            height: 120px;
            top: 30px;
            left: 10px;
            animation: spinRight 12s linear infinite;
        }

        .gear-small {
            width: 80px;
            height: 80px;
            top: 10px;
            right: 15px;
            animation: spinLeft 8s linear infinite;
        }

        .gear-bottom {
            width: 60px;
            height: 60px;
            bottom: 15px;
            right: 40px;
            animation: spinRight 6s linear infinite;
        }

        .shield-icon {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 90px;
            height: 90px;
            z-index: 10;
            filter: drop-shadow(0 10px 15px rgba(250, 204, 21, 0.3));
            animation: float 4s ease-in-out infinite;
        }

        @keyframes spinRight {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spinLeft {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(-360deg);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        .maintenance-content h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .maintenance-content p {
            font-size: 1rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2rem;
            max-width: 450px;
            margin-left: auto;
            margin-right: auto;
        }

        .admin-login-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--cpsu-green-dark);
            background: rgba(22, 101, 52, 0.1);
            border-radius: 0.75rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .admin-login-btn:hover {
            background: rgba(22, 101, 52, 0.15);
            transform: translateY(-1px);
        }

        @media (max-width: 968px) {
            .maintenance-wrapper {
                grid-template-columns: 1fr;
                max-width: 600px;
                min-height: auto;
            }

            .left-section {
                padding: 3rem 2rem;
                text-align: center;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .right-section {
                padding: 3rem 2rem;
            }

            .maintenance-content h2 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="background-decoration"></div>

    <div class="maintenance-wrapper">
        <div class="left-section">
            <div class="logo-badge">
                <!-- Voting Ballot Box Icon -->
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M18 13h-.68l-2 2h1.91L19 17H5l1.78-2h2.05l-2-2H6l-3 3v4c0 1.1.89 2 1.99 2H19c1.1 0 2-.89 2-2v-4l-3-3zm-1-5.05l-4.95 4.95-3.54-3.54 4.95-4.95 3.54 3.54zm-4.24-5.66L6.39 8.66a.996.996 0 000 1.41l4.95 4.95c.39.39 1.02.39 1.41 0l6.36-6.36a.996.996 0 000-1.41l-4.95-4.95a.996.996 0 00-1.41 0z" />
                </svg>
            </div>
            <h1>CpsuVotewisely</h1>
            <p>Cloud Based Real Time Voting System</p>
        </div>

        <div class="right-section">
            <div class="gears-container">
                <!-- Main Gear -->
                <svg class="gear gear-main" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M50 8C52.2091 8 54 9.79086 54 12V18.1729C57.6521 19.0435 61.0827 20.4704 64.1953 22.3685L68.5583 18.0055C70.1204 16.4434 72.653 16.4434 74.2152 18.0055L81.9944 25.7848C83.5566 27.3469 83.5566 29.8796 81.9944 31.4417L77.6315 35.8047C79.5295 38.9172 80.9564 42.3478 81.8271 46H87.9999C90.2091 46 91.9999 47.7909 91.9999 50V61C91.9999 63.2091 90.2091 65 87.9999 65H81.8271C80.9564 68.6521 79.5295 72.0827 77.6315 75.1952L81.9944 79.5582C83.5566 81.1204 83.5566 83.653 81.9944 85.2152L74.2152 92.9944C72.653 94.5566 70.1204 94.5566 68.5583 92.9944L64.1953 88.6314C61.0827 90.5295 57.6521 91.9564 54 92.8271V98.9999C54 101.209 52.2091 103 50 103H38.9999C36.7908 103 34.9999 101.209 34.9999 98.9999V92.8271C31.3478 91.9564 27.9172 90.5295 24.8047 88.6314L20.4417 92.9944C18.8796 94.5566 16.3469 94.5566 14.7848 92.9944L7.0055 85.2152C5.44339 83.653 5.44339 81.1204 7.0055 79.5582L11.3685 75.1952C9.47043 72.0827 8.04351 68.6521 7.17286 65H0.999939C-1.2092 65 -2.99994 63.2091 -2.99994 61V50C-2.99994 47.7909 -1.2092 46 0.999939 46H7.17286C8.04351 42.3478 9.47043 38.9172 11.3685 35.8047L7.0055 31.4417C5.44339 29.8796 5.44339 27.3469 7.0055 25.7848L14.7848 18.0055C16.3469 16.4434 18.8796 16.4434 20.4417 18.0055L24.8047 22.3685C27.9172 20.4704 31.3478 19.0435 34.9999 18.1729V12C34.9999 9.79086 36.7908 8 38.9999 8H50ZM44.5 35C33.7304 35 25 43.7304 25 54.5C25 65.2695 33.7304 74 44.5 74C55.2695 74 64 65.2695 64 54.5C64 43.7304 55.2695 35 44.5 35Z"
                        fill="#166534" stroke="#facc15" stroke-width="2" />
                </svg>

                <!-- Small Gear -->
                <svg class="gear gear-small" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M50 8C52.2091 8 54 9.79086 54 12V18.1729C57.6521 19.0435 61.0827 20.4704 64.1953 22.3685L68.5583 18.0055C70.1204 16.4434 72.653 16.4434 74.2152 18.0055L81.9944 25.7848C83.5566 27.3469 83.5566 29.8796 81.9944 31.4417L77.6315 35.8047C79.5295 38.9172 80.9564 42.3478 81.8271 46H87.9999C90.2091 46 91.9999 47.7909 91.9999 50V61C91.9999 63.2091 90.2091 65 87.9999 65H81.8271C80.9564 68.6521 79.5295 72.0827 77.6315 75.1952L81.9944 79.5582C83.5566 81.1204 83.5566 83.653 81.9944 85.2152L74.2152 92.9944C72.653 94.5566 70.1204 94.5566 68.5583 92.9944L64.1953 88.6314C61.0827 90.5295 57.6521 91.9564 54 92.8271V98.9999C54 101.209 52.2091 103 50 103H38.9999C36.7908 103 34.9999 101.209 34.9999 98.9999V92.8271C31.3478 91.9564 27.9172 90.5295 24.8047 88.6314L20.4417 92.9944C18.8796 94.5566 16.3469 94.5566 14.7848 92.9944L7.0055 85.2152C5.44339 83.653 5.44339 81.1204 7.0055 79.5582L11.3685 75.1952C9.47043 72.0827 8.04351 68.6521 7.17286 65H0.999939C-1.2092 65 -2.99994 63.2091 -2.99994 61V50C-2.99994 47.7909 -1.2092 46 0.999939 46H7.17286C8.04351 42.3478 9.47043 38.9172 11.3685 35.8047L7.0055 31.4417C5.44339 29.8796 5.44339 27.3469 7.0055 25.7848L14.7848 18.0055C16.3469 16.4434 18.8796 16.4434 20.4417 18.0055L24.8047 22.3685C27.9172 20.4704 31.3478 19.0435 34.9999 18.1729V12C34.9999 9.79086 36.7908 8 38.9999 8H50ZM44.5 35C33.7304 35 25 43.7304 25 54.5C25 65.2695 33.7304 74 44.5 74C55.2695 74 64 65.2695 64 54.5C64 43.7304 55.2695 35 44.5 35Z"
                        fill="#22c55e" opacity="0.8" stroke="#facc15" stroke-width="2" />
                </svg>

                <!-- Bottom Gear -->
                <svg class="gear gear-bottom" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M50 8C52.2091 8 54 9.79086 54 12V18.1729C57.6521 19.0435 61.0827 20.4704 64.1953 22.3685L68.5583 18.0055C70.1204 16.4434 72.653 16.4434 74.2152 18.0055L81.9944 25.7848C83.5566 27.3469 83.5566 29.8796 81.9944 31.4417L77.6315 35.8047C79.5295 38.9172 80.9564 42.3478 81.8271 46H87.9999C90.2091 46 91.9999 47.7909 91.9999 50V61C91.9999 63.2091 90.2091 65 87.9999 65H81.8271C80.9564 68.6521 79.5295 72.0827 77.6315 75.1952L81.9944 79.5582C83.5566 81.1204 83.5566 83.653 81.9944 85.2152L74.2152 92.9944C72.653 94.5566 70.1204 94.5566 68.5583 92.9944L64.1953 88.6314C61.0827 90.5295 57.6521 91.9564 54 92.8271V98.9999C54 101.209 52.2091 103 50 103H38.9999C36.7908 103 34.9999 101.209 34.9999 98.9999V92.8271C31.3478 91.9564 27.9172 90.5295 24.8047 88.6314L20.4417 92.9944C18.8796 94.5566 16.3469 94.5566 14.7848 92.9944L7.0055 85.2152C5.44339 83.653 5.44339 81.1204 7.0055 79.5582L11.3685 75.1952C9.47043 72.0827 8.04351 68.6521 7.17286 65H0.999939C-1.2092 65 -2.99994 63.2091 -2.99994 61V50C-2.99994 47.7909 -1.2092 46 0.999939 46H7.17286C8.04351 42.3478 9.47043 38.9172 11.3685 35.8047L7.0055 31.4417C5.44339 29.8796 5.44339 27.3469 7.0055 25.7848L14.7848 18.0055C16.3469 16.4434 18.8796 16.4434 20.4417 18.0055L24.8047 22.3685C27.9172 20.4704 31.3478 19.0435 34.9999 18.1729V12C34.9999 9.79086 36.7908 8 38.9999 8H50ZM44.5 35C33.7304 35 25 43.7304 25 54.5C25 65.2695 33.7304 74 44.5 74C55.2695 74 64 65.2695 64 54.5C64 43.7304 55.2695 35 44.5 35Z"
                        fill="#14532d" opacity="0.6" stroke="#facc15" stroke-width="2" />
                </svg>

                <!-- Shield Icon to match the photo -->
                <svg class="shield-icon" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 10L15 25V45C15 65 30 85 50 95C70 85 85 65 85 45V25L50 10Z" fill="#166534"
                        stroke="#facc15" stroke-width="6" stroke-linejoin="round" />
                    <path d="M50 10V95C70 85 85 65 85 45V25L50 10Z" fill="#facc15" opacity="0.7" />
                    <path d="M15 45H85" stroke="#facc15" stroke-width="6" />
                </svg>
            </div>

            <div class="maintenance-content">
                <h2>System Maintenance</h2>
                <p class="mb-6">We are currently performing system updates and improvements. The portal is temporarily
                    unavailable during this maintenance period.</p>

                <div class="admin-login-btn text-center mt-2 px-6 py-4"
                    style="display: inline-block; cursor: default; background: rgba(22, 101, 52, 0.08); max-width: 450px;">
                    Please check back shortly. Thank you for your patience and understanding.
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    // Auto-redirect when admin disables maintenance mode
    (function() {
        var interval = setInterval(function() {
            fetch('/api/maintenance-status', { cache: 'no-store' })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (!data.maintenance) {
                        clearInterval(interval);
                        window.location.href = '/';
                    }
                })
                .catch(function() { /* silently retry */ });
        }, 1500);
    })();
</script>

</html>
