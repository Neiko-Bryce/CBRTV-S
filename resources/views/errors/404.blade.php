<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - CpsuVotewisely</title>
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

        /* 404 Animation CSS */
        .error-graphic-container {
            position: relative;
            width: 220px;
            height: 180px;
            margin-bottom: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            filter: drop-shadow(0 15px 25px rgba(22, 101, 52, 0.15));
        }

        .number-4 {
            font-family: 'Playfair Display', serif;
            font-size: 8rem;
            font-weight: 800;
            color: var(--cpsu-green-dark);
            line-height: 1;
            text-shadow: 4px 4px 0px rgba(250, 204, 21, 0.5);
            animation: floatLeft 6s ease-in-out infinite;
            z-index: 2;
        }

        .number-0 {
            width: 90px;
            height: 90px;
            margin: 0 5px;
            position: relative;
            animation: pulseCircle 4s ease-in-out infinite;
            z-index: 1;
        }
        
        .number-0 svg {
            width: 100%;
            height: 100%;
        }

        .number-4-alt {
            font-family: 'Playfair Display', serif;
            font-size: 8rem;
            font-weight: 800;
            color: var(--cpsu-green);
            line-height: 1;
            text-shadow: -4px 4px 0px rgba(250, 204, 21, 0.3);
            animation: floatRight 7s ease-in-out infinite;
            z-index: 2;
        }

        @keyframes floatLeft {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(-12px) rotate(2deg); }
        }

        @keyframes floatRight {
            0%, 100% { transform: translateY(0) rotate(2deg); }
            50% { transform: translateY(-15px) rotate(-2deg); }
        }

        @keyframes pulseCircle {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 10px rgba(250, 204, 21, 0.4)); }
            50% { transform: scale(1.05) translateY(5px); filter: drop-shadow(0 0 20px rgba(250, 204, 21, 0.8)); }
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

        .home-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-dark) 100%);
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(22, 101, 52, 0.25);
        }

        .home-btn:hover {
            background: var(--cpsu-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 101, 52, 0.35);
        }

        .home-btn:active {
            transform: translateY(0);
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
            
            .left-section .logo-badge {
                margin-left: auto;
                margin-right: auto;
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
            
            .number-4, .number-4-alt {
                font-size: 6rem;
            }
            
            .number-0 {
                width: 70px;
                height: 70px;
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
            <div class="error-graphic-container">
                <div class="number-4">4</div>
                <div class="number-0">
                    <!-- Target / Ballot Icon as the Zero -->
                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="45" fill="#166534" stroke="#facc15" stroke-width="6"/>
                        <circle cx="50" cy="50" r="30" fill="#22c55e" opacity="0.8"/>
                        <circle cx="50" cy="50" r="15" fill="#facc15"/>
                    </svg>
                </div>
                <div class="number-4-alt">4</div>
            </div>

            <div class="maintenance-content">
                <h2>Page Not Found</h2>
                <p class="mb-6">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>

                <a href="{{ url('/') }}" class="home-btn mt-4">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Return to Homepage
                </a>
            </div>
        </div>
    </div>
</body>

</html>
