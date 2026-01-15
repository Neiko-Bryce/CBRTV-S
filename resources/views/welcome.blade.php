<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Central Philippine State University - Cloud Based Real-Time Voting System. Secure, transparent, and efficient digital voting platform for university elections.">
    <title>Central Philippine State University - Cloud Based Real-Time Voting System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&family=playfair-display:400,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --cpsu-green: #006633;
            --cpsu-gold: #D4AF37;
            --cpsu-green-light: #008844;
            --cpsu-green-dark: #004422;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #1e293b;
            line-height: 1.6;
            scroll-behavior: smooth;
        }
        .heading-font {
            font-family: 'Playfair Display', serif;
        }
        .nav {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: inherit;
        }
        .nav-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nav-brand-text h1 {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--cpsu-green);
            margin: 0;
        }
        .nav-brand-text p {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-link {
            color: #475569;
            text-decoration: none;
            font-size: 0.9375rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: var(--cpsu-green);
        }
        .nav-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.9375rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
        }
        .btn-outline {
            background: white;
            color: var(--cpsu-green);
            border: 1.5px solid var(--cpsu-green);
        }
        .btn-outline:hover {
            background: var(--cpsu-green);
            color: white;
        }
        .btn-primary {
            background: var(--cpsu-green);
            color: white;
        }
        .btn-primary:hover {
            background: var(--cpsu-green-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.2);
        }
        .btn-gold {
            background: linear-gradient(135deg, var(--cpsu-gold) 0%, #E8D08A 100%);
            color: var(--cpsu-green-dark);
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #B8941F 0%, var(--cpsu-gold) 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }
        .hero {
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            color: white;
            padding: 6rem 2rem;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            filter: blur(60px);
        }
        .hero-container {
            max-width: 1280px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .hero-content {
            max-width: 100%;
        }
        .hero-visual {
            position: relative;
            height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1200px;
            width: 100%;
        }
        .hero-image-wrapper {
            position: relative;
            width: 100%;
            height: 100%;
            max-width: 550px;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1200px;
        }
        .hero-main-image {
            position: relative;
            width: 100%;
            height: 450px;
            border-radius: 0;
            overflow: visible;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            transform-style: preserve-3d;
        }
        .unique-layers {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
        }
        .layer-base {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 102, 51, 0.05) 0%, rgba(212, 175, 55, 0.08) 100%);
            border-radius: 3rem;
            transform: translateZ(-50px);
            backdrop-filter: blur(10px);
        }
        .layer-1 {
            position: absolute;
            width: 85%;
            height: 85%;
            top: 7.5%;
            left: 7.5%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(248, 250, 252, 0.98) 100%);
            border-radius: 2.5rem;
            box-shadow: 
                0 25px 80px rgba(0, 102, 51, 0.15),
                0 0 0 1px rgba(0, 102, 51, 0.1);
            transform: translateZ(20px) rotateY(-2deg);
            animation: layerFloat1 12s ease-in-out infinite;
            overflow: hidden;
        }
        .layer-2 {
            position: absolute;
            width: 70%;
            height: 70%;
            top: 15%;
            left: 15%;
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            border-radius: 2rem;
            box-shadow: 
                0 20px 60px rgba(0, 102, 51, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transform: translateZ(40px) rotateY(3deg);
            animation: layerFloat2 14s ease-in-out infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .layer-3 {
            position: absolute;
            width: 55%;
            height: 55%;
            top: 22.5%;
            left: 22.5%;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 1.5rem;
            box-shadow: 
                0 15px 50px rgba(0, 0, 0, 0.2),
                0 0 0 2px rgba(212, 175, 55, 0.4);
            transform: translateZ(60px) rotateY(-1deg);
            animation: layerFloat3 16s ease-in-out infinite;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            z-index: 10;
        }
        @keyframes layerFloat1 {
            0%, 100% { transform: translateZ(20px) rotateY(-2deg) translateY(0px); }
            50% { transform: translateZ(20px) rotateY(-1deg) translateY(-5px); }
        }
        @keyframes layerFloat2 {
            0%, 100% { transform: translateZ(40px) rotateY(3deg) translateY(0px); }
            50% { transform: translateZ(40px) rotateY(4deg) translateY(-6px); }
        }
        @keyframes layerFloat3 {
            0%, 100% { transform: translateZ(60px) rotateY(-1deg) translateY(0px); }
            50% { transform: translateZ(60px) rotateY(0deg) translateY(-3px); }
        }
        .voting-interface {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .interface-header {
            text-align: center;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(0, 102, 51, 0.1);
        }
        .interface-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--cpsu-green);
            margin-bottom: 0.25rem;
        }
        .interface-subtitle {
            font-size: 0.75rem;
            color: #64748b;
        }
        .vote-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            flex: 1;
        }
        .vote-option-card {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .vote-option-card:hover {
            border-color: var(--cpsu-green);
            background: rgba(0, 102, 51, 0.02);
            transform: translateX(4px);
        }
        .vote-option-card.selected {
            border-color: var(--cpsu-green);
            background: rgba(0, 102, 51, 0.05);
        }
        .option-indicator {
            width: 18px;
            height: 18px;
            border: 2px solid var(--cpsu-green);
            border-radius: 50%;
            position: relative;
            flex-shrink: 0;
        }
        .option-indicator.selected::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 8px;
            height: 8px;
            background: var(--cpsu-green);
            border-radius: 50%;
        }
        .option-label {
            flex: 1;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #1e293b;
        }
        .vote-option-card.selected .option-label {
            color: var(--cpsu-green);
            font-weight: 600;
        }
        .submit-vote-btn {
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 50%, var(--cpsu-gold) 100%);
            color: white;
            border: none;
            border-radius: 0.625rem;
            padding: 0.75rem;
            font-weight: 600;
            font-size: 0.8125rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.2);
        }
        .submit-vote-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(212, 175, 55, 0.3);
        }
        .geometric-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
            pointer-events: none;
        }
        .shape {
            position: absolute;
            opacity: 0.6;
            animation: shapeRotate 20s linear infinite;
        }
        .shape-1 {
            top: 10%;
            right: 10%;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            animation-delay: 0s;
        }
        .shape-2 {
            bottom: 15%;
            left: 8%;
            width: 60px;
            height: 60px;
            background: var(--cpsu-gold);
            border-radius: 50%;
            animation-delay: 5s;
        }
        .shape-3 {
            top: 50%;
            right: 5%;
            width: 50px;
            height: 50px;
            background: var(--cpsu-green);
            transform: rotate(45deg);
            animation-delay: 10s;
        }
        @keyframes shapeRotate {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(1.05); }
            100% { transform: rotate(360deg) scale(1); }
        }
        .abstract-people-group {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 5;
            pointer-events: none;
        }
        .person-abstract {
            position: absolute;
            opacity: 0.8;
            animation: personAbstract 12s ease-in-out infinite;
        }
        .person-abstract svg {
            filter: drop-shadow(0 4px 12px rgba(0, 102, 51, 0.25));
        }
        .person-a {
            bottom: 8%;
            left: 3%;
            width: 50px;
            height: 65px;
            animation-delay: 0s;
        }
        .person-b {
            bottom: 12%;
            right: 5%;
            width: 45px;
            height: 60px;
            animation-delay: 3s;
        }
        .person-c {
            top: 18%;
            left: 5%;
            width: 40px;
            height: 55px;
            animation-delay: 6s;
        }
        .person-d {
            top: 12%;
            right: 3%;
            width: 42px;
            height: 58px;
            animation-delay: 9s;
        }
        @keyframes personAbstract {
            0%, 100% {
                transform: translateY(0px) translateX(0px) scale(1);
                opacity: 0.7;
            }
            50% {
                transform: translateY(-10px) translateX(3px) scale(1.02);
                opacity: 0.9;
            }
        }
        .vote-particles {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }
        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: var(--cpsu-gold);
            border-radius: 50%;
            opacity: 0.7;
            animation: particleFloat 8s ease-in-out infinite;
        }
        .particle:nth-child(1) { top: 20%; left: 15%; animation-delay: 0s; }
        .particle:nth-child(2) { top: 60%; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { top: 40%; right: 15%; animation-delay: 4s; }
        .particle:nth-child(4) { bottom: 25%; right: 20%; animation-delay: 6s; }
        @keyframes particleFloat {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
                opacity: 0.5;
            }
            50% {
                transform: translateY(-15px) translateX(8px);
                opacity: 0.9;
            }
        }
        .voting-concept-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            z-index: 3;
        }
        .voting-icon-large {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 
                0 20px 60px rgba(0, 102, 51, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            animation: pulse-glow 3s ease-in-out infinite;
        }
        .voting-icon-large svg {
            width: 60px;
            height: 60px;
            color: var(--cpsu-green);
        }
        @keyframes pulse-glow {
            0%, 100% {
                transform: scale(1);
                box-shadow: 
                    0 20px 60px rgba(0, 102, 51, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.8);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 
                    0 25px 80px rgba(0, 102, 51, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.8);
            }
        }
        .voting-stats-overlay {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .stat-badge {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 0.75rem 1.25rem;
            border-radius: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-shadow: 0 8px 24px rgba(0, 102, 51, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .stat-badge-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--cpsu-green);
            line-height: 1;
        }
        .stat-badge:nth-child(2) .stat-badge-value {
            color: var(--cpsu-gold-dark);
        }
        .stat-badge-label {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 0.25rem;
            text-align: center;
        }
        .floating-vote-badges {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .vote-badge {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 8px 24px rgba(0, 102, 51, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.5);
            animation: floatBadge 6s ease-in-out infinite;
        }
        .vote-badge svg {
            width: 20px;
            height: 20px;
            color: var(--cpsu-green);
        }
        .vote-badge-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: #1e293b;
        }
        .vote-badge:nth-child(1) {
            top: 10%;
            right: -20px;
            animation-delay: 0s;
        }
        .vote-badge:nth-child(2) {
            bottom: 20%;
            left: -30px;
            animation-delay: 2s;
        }
        .vote-badge:nth-child(3) {
            top: 60%;
            right: -15px;
            animation-delay: 4s;
        }
        @keyframes floatBadge {
            0%, 100% {
                transform: translateY(0px) translateX(0px);
                opacity: 0.9;
            }
            50% {
                transform: translateY(-15px) translateX(5px);
                opacity: 1;
            }
        }
        .aero-card {
            position: relative;
            width: 100%;
            max-width: 500px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(30px);
            border-radius: 2rem;
            padding: 2.5rem;
            box-shadow: 
                0 25px 80px rgba(0, 102, 51, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            animation: float3d 8s ease-in-out infinite;
            transform-style: preserve-3d;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        @keyframes float3d {
            0%, 100% {
                transform: translateY(0px) rotateX(0deg) rotateY(0deg);
            }
            25% {
                transform: translateY(-15px) rotateX(2deg) rotateY(-3deg);
            }
            50% {
                transform: translateY(-25px) rotateX(0deg) rotateY(0deg);
            }
            75% {
                transform: translateY(-15px) rotateX(-2deg) rotateY(3deg);
            }
        }
        .aero-card::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-gold) 50%, var(--cpsu-green-light) 100%);
            border-radius: 2rem;
            z-index: -1;
            opacity: 0.4;
            filter: blur(15px);
            animation: glow 3s ease-in-out infinite;
        }
        @keyframes glow {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.5; }
        }
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .floating-icon {
            position: absolute;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 102, 51, 0.15);
            animation: floatIcon 6s ease-in-out infinite;
        }
        .floating-icon svg {
            width: 28px;
            height: 28px;
            color: var(--cpsu-green);
        }
        .floating-icon:nth-child(2) {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(232, 208, 138, 0.1) 100%);
        }
        .floating-icon:nth-child(2) svg {
            color: var(--cpsu-gold-dark);
        }
        .floating-icon:nth-child(1) {
            top: -30px;
            right: 20px;
            animation-delay: 0s;
        }
        .floating-icon:nth-child(2) {
            bottom: -20px;
            left: -20px;
            animation-delay: 2s;
        }
        .floating-icon:nth-child(3) {
            top: 50%;
            right: -30px;
            animation-delay: 4s;
        }
        @keyframes floatIcon {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }
        .aero-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }
        .aero-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--cpsu-green);
        }
        .aero-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: #64748b;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .voting-preview {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .vote-item {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s;
        }
        .vote-item:hover {
            border-color: var(--cpsu-green);
            box-shadow: 0 4px 12px rgba(0, 102, 51, 0.1);
        }
        .vote-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .vote-item:nth-of-type(2) .vote-icon {
            background: linear-gradient(135deg, var(--cpsu-gold) 0%, #E8D08A 100%);
        }
        .vote-icon svg {
            width: 24px;
            height: 24px;
            color: white;
        }
        .vote-info {
            flex: 1;
        }
        .vote-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.25rem;
            font-size: 0.9375rem;
        }
        .vote-desc {
            font-size: 0.75rem;
            color: #64748b;
        }
        .vote-progress {
            width: 60px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
            position: relative;
        }
        .vote-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--cpsu-green) 0%, var(--cpsu-green-light) 100%);
            border-radius: 9999px;
        }
        .vote-item:nth-of-type(2) .vote-progress-bar {
            background: linear-gradient(90deg, var(--cpsu-gold) 0%, #E8D08A 100%);
        }
        .vote-item:nth-of-type(1) .vote-progress-bar {
            animation: progress1 2s ease-in-out infinite;
        }
        .vote-item:nth-of-type(2) .vote-progress-bar {
            animation: progress2 2s ease-in-out infinite;
        }
        .vote-item:nth-of-type(3) .vote-progress-bar {
            animation: progress3 2s ease-in-out infinite;
        }
        @keyframes progress1 {
            0% { width: 0%; }
            50% { width: 85%; }
            100% { width: 85%; }
        }
        @keyframes progress2 {
            0% { width: 0%; }
            50% { width: 65%; }
            100% { width: 65%; }
        }
        @keyframes progress3 {
            0% { width: 0%; }
            50% { width: 72%; }
            100% { width: 72%; }
        }
        .aero-stats {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e2e8f0;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
        .stat-mini {
            text-align: center;
        }
        .stat-mini-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--cpsu-green);
            margin-bottom: 0.25rem;
        }
        .stat-mini-label {
            font-size: 0.75rem;
            color: #64748b;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 9999px;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(212, 175, 55, 0.3);
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.15);
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }
        .text-gold {
            background: linear-gradient(135deg, var(--cpsu-gold) 0%, #E8D08A 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        .hero p {
            font-size: 1.25rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            line-height: 1.7;
        }
        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .btn-hero {
            padding: 0.875rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }
        .btn-hero-primary {
            background: white;
            color: var(--cpsu-green);
        }
        .btn-hero-primary:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1.5px solid rgba(255, 255, 255, 0.3);
        }
        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .btn-hero-gold {
            background: linear-gradient(135deg, var(--cpsu-gold) 0%, #E8D08A 100%);
            color: var(--cpsu-green-dark);
            border: none;
        }
        .btn-hero-gold:hover {
            background: linear-gradient(135deg, #B8941F 0%, var(--cpsu-gold) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
        }
        .section {
            padding: 5rem 2rem;
        }
        .section-container {
            max-width: 1280px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        .section-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(0, 102, 51, 0.1);
            color: var(--cpsu-green);
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        #features .section-badge,
        #about .section-badge {
            background: linear-gradient(135deg, var(--cpsu-gold) 0%, #E8D08A 100%);
            color: var(--cpsu-green-dark);
            border: 1.5px solid rgba(212, 175, 55, 0.5);
            font-weight: 700;
        }
        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1e293b;
        }
        .section-header p {
            font-size: 1.125rem;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s;
        }
        .feature-card:hover {
            border-color: var(--cpsu-green);
            box-shadow: 0 4px 20px rgba(0, 102, 51, 0.1);
            transform: translateY(-4px);
        }
        .feature-icon {
            width: 56px;
            height: 56px;
            background: rgba(0, 102, 51, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .feature-icon svg {
            width: 28px;
            height: 28px;
            color: var(--cpsu-green);
        }
        .feature-card:nth-child(2) .feature-icon,
        .feature-card:nth-child(4) .feature-icon {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(232, 208, 138, 0.1) 100%);
        }
        .feature-card:nth-child(2) .feature-icon svg,
        .feature-card:nth-child(4) .feature-icon svg {
            color: var(--cpsu-gold-dark);
        }
        .feature-card:nth-child(2):hover,
        .feature-card:nth-child(4):hover {
            border-color: var(--cpsu-gold);
            box-shadow: 0 4px 20px rgba(212, 175, 55, 0.15);
        }
        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #1e293b;
        }
        .feature-card p {
            color: #64748b;
            line-height: 1.7;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .stat-card:nth-child(2) {
            border-color: rgba(212, 175, 55, 0.3);
            background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, rgba(248, 250, 252, 1) 100%);
        }
        .stat-value {
            font-size: 3rem;
            font-weight: 700;
            color: var(--cpsu-green);
            margin-bottom: 0.5rem;
        }
        .stat-card:nth-child(2) .stat-value {
            color: var(--cpsu-gold-dark);
        }
        .stat-label {
            color: #64748b;
            font-weight: 500;
        }
        .about-section {
            background: #f8fafc;
        }
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        .about-content h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #1e293b;
        }
        .about-content p {
            font-size: 1.125rem;
            color: #475569;
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }
        .benefits-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .benefit-item {
            display: flex;
            align-items: start;
            gap: 1rem;
        }
        .benefit-icon {
            width: 40px;
            height: 40px;
            background: var(--cpsu-green);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .benefit-icon svg {
            width: 20px;
            height: 20px;
            color: white;
        }
        .benefit-text {
            color: #475569;
            line-height: 1.7;
        }
        .security-section {
            background: linear-gradient(135deg, #1e293b 0%, var(--cpsu-green-dark) 100%);
            color: white;
        }
        .security-section .section-header h2,
        .security-section .section-header p {
            color: white;
        }
        .security-section .section-badge {
            background: rgba(212, 175, 55, 0.2);
            color: var(--cpsu-gold);
        }
        .security-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
        }
        .security-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }
        .security-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .security-icon svg {
            width: 32px;
            height: 32px;
            color: white;
        }
        .security-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .security-card p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.7;
        }
        .cta-section {
            background: var(--cpsu-green);
            color: white;
            text-align: center;
        }
        .cta-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .cta-section p {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .footer {
            background: #1e293b;
            color: #94a3b8;
            padding: 4rem 2rem 2rem;
        }
        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }
        .footer-brand h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1rem;
        }
        .footer-brand p {
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        .footer-section h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: white;
            margin-bottom: 1rem;
        }
        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: var(--cpsu-gold);
        }
        .footer-bottom {
            border-top: 1px solid #334155;
            padding-top: 2rem;
            text-align: center;
            color: #64748b;
            font-size: 0.875rem;
        }
        @media (max-width: 968px) {
        }
        @media (max-width: 968px) {
            .hero-container {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            .hero-visual {
                height: 400px;
            }
            .hero-main-image {
                height: 350px;
            }
        }
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .hero-visual {
                height: 350px;
            }
            .hero-main-image {
                height: 300px;
            }
            .about-grid {
                grid-template-columns: 1fr;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
        /* Preloader Styles */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--cpsu-green-dark) 0%, var(--cpsu-green) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .preloader.active {
            opacity: 1;
            visibility: visible;
        }
        .preloader-content {
            text-align: center;
            color: white;
        }
        .preloader-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: preloaderFloat 3s ease-in-out infinite;
        }
        .preloader-logo svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        @keyframes preloaderFloat {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        .preloader-spinner {
            width: 60px;
            height: 60px;
            margin: 0 auto 1.5rem;
            position: relative;
        }
        .spinner-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        .spinner-ring:nth-child(2) {
            width: 80%;
            height: 80%;
            top: 10%;
            left: 10%;
            border-width: 3px;
            animation-duration: 1.5s;
            animation-direction: reverse;
        }
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }
        .preloader-text {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: 0.5px;
        }
        .preloader-subtext {
            font-size: 0.875rem;
            opacity: 0.8;
            color: rgba(255, 255, 255, 0.9);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-container">
            <a href="/" class="nav-brand">
                <div class="nav-logo">
                    <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="nav-brand-text">
                    <h1 class="heading-font">CPSU</h1>
                    <p>Voting System</p>
                </div>
            </a>
            <div class="nav-links">
                <a href="#features" class="nav-link">Features</a>
                <a href="#about" class="nav-link">About</a>
                <a href="#security" class="nav-link">Security</a>
            </div>
            <div class="nav-actions">
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn-outline">Log in</a>
                @endif
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 .723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Central Philippine State University</span>
                </div>
                <h1 class="heading-font">Cloud Based<br><span class="text-gold">Real-Time Voting</span><br>System</h1>
                <p>Empowering Central Philippine State University with a secure, transparent, and efficient digital voting platform for student elections, faculty decisions, and institutional surveys.</p>
                <div class="hero-actions">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-hero btn-hero-primary">Get Started</a>
                    @endif
                    <a href="#features" class="btn btn-hero btn-hero-gold">Explore Features</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-image-wrapper">
                    <div class="hero-main-image">
                        <div class="unique-layers">
                            <!-- Base Layer -->
                            <div class="layer-base"></div>
                            
                            <!-- Geometric Shapes -->
                            <div class="geometric-shapes">
                                <div class="shape shape-1"></div>
                                <div class="shape shape-2"></div>
                                <div class="shape shape-3"></div>
                            </div>
                            
                            <!-- Vote Particles -->
                            <div class="vote-particles">
                                <div class="particle"></div>
                                <div class="particle"></div>
                                <div class="particle"></div>
                                <div class="particle"></div>
                            </div>
                            
                            <!-- Layer 1 - Outer Frame -->
                            <div class="layer-1"></div>
                            
                            <!-- Layer 2 - Green Background -->
                            <div class="layer-2">
                                <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" opacity="0.3">
                                    <circle cx="50" cy="50" r="40" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                                    <path d="M30 50 L45 60 L70 40" stroke="rgba(212,175,55,0.6)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            
                            <!-- Layer 3 - Voting Interface -->
                            <div class="layer-3">
                                <div class="voting-interface">
                                    <div class="interface-header">
                                        <div class="interface-title">CPSU Voting</div>
                                        <div class="interface-subtitle">Select Your Choice</div>
                                    </div>
                                    <div class="vote-options">
                                        <div class="vote-option-card selected">
                                            <div class="option-indicator selected"></div>
                                            <div class="option-label">Option A</div>
                                        </div>
                                        <div class="vote-option-card">
                                            <div class="option-indicator"></div>
                                            <div class="option-label">Option B</div>
                                        </div>
                                        <div class="vote-option-card">
                                            <div class="option-indicator"></div>
                                            <div class="option-label">Option C</div>
                                        </div>
                                    </div>
                                    <button class="submit-vote-btn">Submit Vote</button>
                                </div>
                            </div>
                            
                            <!-- Abstract People (No Faces) -->
                            <div class="abstract-people-group">
                                <div class="person-abstract person-a">
                                    <svg viewBox="0 0 50 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="25" cy="12" rx="10" ry="12" fill="#006633"/>
                                        <path d="M10 20C10 25 15 30 20 35L25 40V60C25 65 27 70 30 70C33 70 35 65 35 60V40L40 35C45 30 50 25 50 20L30 15L10 20Z" fill="#006633"/>
                                    </svg>
                                </div>
                                <div class="person-abstract person-b">
                                    <svg viewBox="0 0 45 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="22" cy="11" rx="9" ry="11" fill="#008844"/>
                                        <path d="M8 18C8 23 13 28 18 33L23 38V58C23 63 25 68 28 68C31 68 33 63 33 58V38L38 33C43 28 48 23 48 18L28 13L8 18Z" fill="#008844"/>
                                    </svg>
                                </div>
                                <div class="person-abstract person-c">
                                    <svg viewBox="0 0 40 55" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="20" cy="10" rx="8" ry="10" fill="#006633"/>
                                        <path d="M6 17C6 22 11 27 16 32L21 37V57C21 62 23 67 26 67C29 67 31 62 31 57V37L36 32C41 27 46 22 46 17L26 12L6 17Z" fill="#006633"/>
                                    </svg>
                                </div>
                                <div class="person-abstract person-d">
                                    <svg viewBox="0 0 42 58" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <ellipse cx="21" cy="11" rx="8.5" ry="11" fill="#008844"/>
                                        <path d="M7 18C7 23 12 28 17 33L22 38V58C22 63 24 68 27 68C30 68 32 63 32 58V38L37 33C42 28 47 23 47 18L27 13L7 18Z" fill="#008844"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Floating Stats Badges -->
                        <div class="floating-vote-badges">
                            <div class="vote-badge">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span class="vote-badge-text">Students</span>
                            </div>
                            <div class="vote-badge">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="vote-badge-text">Secure</span>
                            </div>
                            <div class="vote-badge">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="vote-badge-text">Real-Time</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="section">
        <div class="section-container">
            <div class="section-header">
                <span class="section-badge">Platform Features</span>
                <h2 class="heading-font">Powerful Features for Modern Elections</h2>
                <p>Comprehensive tools designed specifically for Central Philippine State University's voting needs</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">Enterprise-Grade Security</h3>
                    <p>Advanced encryption protocols and security measures ensure that every vote is protected with industry-leading security standards.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">Real-Time Processing</h3>
                    <p>Instant vote counting and live result updates provide immediate transparency. Watch election results unfold in real-time as votes are cast.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">Smart User Management</h3>
                    <p>Comprehensive role-based access control for students, faculty, staff, and administrators with seamless system integration.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">Advanced Analytics</h3>
                    <p>Detailed analytics dashboard with participation rates, voting patterns, and comprehensive reports for informed decision-making.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">Automated Scheduling</h3>
                    <p>Set precise voting windows with automatic opening and closing. Perfect for synchronized campus-wide elections and time-sensitive decisions.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">Intuitive Dashboard</h3>
                    <p>User-friendly admin interface requires no technical training. Create and manage elections effortlessly with our streamlined design.</p>
                </div>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">100%</div>
                    <div class="stat-label">Secure & Encrypted</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">24/7</div>
                    <div class="stat-label">Real-Time Monitoring</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">∞</div>
                    <div class="stat-label">Infinitely Scalable</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section about-section">
        <div class="section-container">
            <div class="about-grid">
                <div class="about-content">
                    <span class="section-badge">About CPSU Voting System</span>
                    <h2 class="heading-font">Built for Central Philippine State University</h2>
                    <p>Our Cloud-Based Real-Time Voting System is specifically designed to meet the unique needs of Central Philippine State University. We understand the importance of conducting fair, transparent, and efficient elections in an educational setting.</p>
                    <p>Whether you're organizing Student Government elections, Faculty Senate votes, Academic Council decisions, or university-wide surveys, our platform provides the robust tools and security necessary for maintaining the integrity of democratic processes at CPSU.</p>
                </div>
                <div>
                    <ul class="benefits-list">
                        <li class="benefit-item">
                            <div class="benefit-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="benefit-text">Completely eliminates paper ballots and manual vote counting, reducing human error</p>
                        </li>
                        <li class="benefit-item">
                            <div class="benefit-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="benefit-text">Significantly reduces administrative workload, freeing up staff time for other priorities</p>
                        </li>
                        <li class="benefit-item">
                            <div class="benefit-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="benefit-text">Increases student and faculty participation through accessible online voting from any device</p>
                        </li>
                        <li class="benefit-item">
                            <div class="benefit-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <p class="benefit-text">Guarantees tamper-proof vote recording with immutable audit trails and cryptographic verification</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Section -->
    <section id="security" class="section security-section">
        <div class="section-container">
            <div class="section-header">
                <span class="section-badge">Security & Trust</span>
                <h2 class="heading-font">Enterprise-Grade Security</h2>
                <p>Protecting the integrity of every vote at Central Philippine State University with industry-leading security measures</p>
            </div>
            <div class="features-grid">
                <div class="security-card">
                    <div class="security-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">End-to-End Encryption</h3>
                    <p>All voting data is encrypted using AES-256 encryption in transit and at rest, ensuring complete confidentiality of CPSU election processes.</p>
                </div>
                <div class="security-card">
                    <div class="security-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">Vote Integrity</h3>
                    <p>Each vote is cryptographically signed and verified. Advanced fraud detection prevents duplicate voting and tampering attempts.</p>
                </div>
                <div class="security-card">
                    <div class="security-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="heading-font">Complete Audit Trail</h3>
                    <p>Immutable audit logs maintain complete transparency. Every action is recorded with timestamps and user identification for accountability.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
        <div class="section-container">
            <h2 class="heading-font">Ready to Transform CPSU Elections?</h2>
            <p>Join Central Philippine State University in embracing the future of digital democracy. Register today and experience secure, transparent, and efficient voting.</p>
            <div class="hero-actions" style="justify-content: center;">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-hero btn-hero-gold">Create Your Account</a>
                @endif
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn-hero btn-hero-secondary">Sign In to Dashboard</a>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3 class="heading-font">CPSU Voting System</h3>
                    <p>Central Philippine State University's official digital voting platform. Secure, transparent, and designed for the modern educational institution.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#about">About System</a></li>
                        <li><a href="#security">Security</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Account</h4>
                    <ul class="footer-links">
                        @if (Route::has('login'))
                            <li><a href="{{ route('login') }}">Login</a></li>
                        @endif
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @endif
                        @auth
                            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                        @endauth
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Central Philippine State University • Cloud Based Real-Time Voting System</p>
            </div>
        </div>
    </footer>

    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-content">
            
            <div class="preloader-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <div class="preloader-text">Loading...</div>
            <div class="preloader-subtext">CPSU Voting System</div>
        </div>
    </div>

    <script>
        // Preloader functionality
        document.addEventListener('DOMContentLoaded', function() {
            const preloader = document.getElementById('preloader');
            
            // Function to show preloader and redirect
            function showPreloaderAndRedirect(url) {
                preloader.classList.add('active');
                setTimeout(function() {
                    window.location.href = url;
                }, 400);
            }
            
            // Find all login links
            const loginButtons = document.querySelectorAll('a[href*="login"]');
            
            loginButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    // Only intercept if it's a login route
                    if (href && (href.includes('login') || href.includes('/login'))) {
                        e.preventDefault();
                        showPreloaderAndRedirect(href);
                    }
                });
            });
        });
    </script>
</body>
</html>
