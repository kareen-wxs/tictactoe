<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Крестики-Нолики - Играй и выигрывай промокоды!</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700|inter:400,500,600" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef7f0 0%, #fce7f3 50%, #f3e8ff 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        
        /* Анимированный фон */
        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            overflow: hidden;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.3;
            animation: float 20s infinite ease-in-out;
        }
        
        .shape:nth-child(1) {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #ec4899, #f472b6);
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #a855f7, #c084fc);
            top: 60%;
            right: 15%;
            animation-delay: 5s;
        }
        
        .shape:nth-child(3) {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #f472b6, #fb7185);
            bottom: 20%;
            left: 20%;
            animation-delay: 10s;
        }
        
        .shape:nth-child(4) {
            width: 180px;
            height: 180px;
            background: linear-gradient(135deg, #c084fc, #a78bfa);
            top: 30%;
            right: 30%;
            animation-delay: 15s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }
        
        .container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 40px;
            padding: 60px 40px;
            max-width: 700px;
            width: 100%;
            box-shadow: 0 30px 80px rgba(236, 72, 153, 0.2);
            text-align: center;
            animation: slideUp 0.8s ease-out;
            border: 2px solid rgba(236, 72, 153, 0.1);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            animation: gradient 3s ease infinite;
            background-size: 200% 200%;
        }
        
        @keyframes gradient {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }
        
        .subtitle {
            font-size: 1.5rem;
            color: #6b7280;
            margin-bottom: 40px;
            font-weight: 400;
            line-height: 1.6;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            margin: 50px 0;
        }
        
        .feature {
            padding: 30px 20px;
            background: linear-gradient(135deg, #fff5f7 0%, #fef3f2 100%);
            border-radius: 25px;
            border: 2px solid #fce7f3;
            transition: all 0.3s ease;
        }
        
        .feature:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(236, 72, 153, 0.2);
            border-color: #ec4899;
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .feature-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            color: #ec4899;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .feature-text {
            color: #6b7280;
            font-size: 0.95rem;
            line-height: 1.5;
        }
        
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%);
            color: white;
            text-decoration: none;
            padding: 20px 50px;
            border-radius: 50px;
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(236, 72, 153, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .cta-button:hover::before {
            left: 100%;
        }
        
        .cta-button:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(236, 72, 153, 0.5);
        }
        
        .cta-button:active {
            transform: translateY(-2px) scale(1.02);
        }
        
        .sparkles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 2;
        }
        
        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #ec4899;
            border-radius: 50%;
            animation: sparkle 3s infinite;
        }
        
        @keyframes sparkle {
            0%, 100% {
                opacity: 0;
                transform: scale(0);
            }
            50% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .sparkle:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
        .sparkle:nth-child(2) { top: 40%; right: 15%; animation-delay: 1s; }
        .sparkle:nth-child(3) { bottom: 30%; left: 20%; animation-delay: 2s; }
        .sparkle:nth-child(4) { top: 60%; left: 50%; animation-delay: 0.5s; }
        .sparkle:nth-child(5) { bottom: 20%; right: 30%; animation-delay: 1.5s; }
        
        @media (max-width: 768px) {
            .logo {
                font-size: 2.5rem;
            }
            
            .subtitle {
                font-size: 1.2rem;
            }
            
            .welcome-card {
                padding: 40px 25px;
            }
            
            .features {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .cta-button {
                padding: 18px 40px;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="sparkles">
        <div class="sparkle"></div>
        <div class="sparkle"></div>
        <div class="sparkle"></div>
        <div class="sparkle"></div>
        <div class="sparkle"></div>
    </div>
    
    <div class="container">
        <div class="welcome-card">
            <h1 class="logo">Крестики-Нолики</h1>
            <p class="subtitle">
                Играй, побеждай и получай промокоды на скидки!<br>
                Красивая игра для настоящих победительниц 💖
            </p>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🎮</div>
                    <div class="feature-title">Увлекательная игра</div>
                    <div class="feature-text">Классические крестики-нолики с современным дизайном</div>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">🎁</div>
                    <div class="feature-title">Промокоды</div>
                    <div class="feature-text">Выигрывай промокоды на скидки при каждой победе</div>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">📱</div>
                    <div class="feature-title">Telegram уведомления</div>
                    <div class="feature-text">Получай промокоды прямо в Telegram</div>
                </div>
            </div>
            
            <div style="background: linear-gradient(135deg, #fff5f7 0%, #fef3f2 100%); border: 2px solid #ec4899; border-radius: 20px; padding: 25px; margin: 30px 0; text-align: left;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <div style="font-size: 2rem;">⚠️</div>
                    <div style="font-family: 'Playfair Display', serif; font-size: 1.3rem; color: #ec4899; font-weight: 600;">Важно перед игрой!</div>
                </div>
                <div style="color: #6b7280; line-height: 1.8; font-size: 1rem;">
                    <p style="margin-bottom: 10px;">Чтобы получать промокоды в Telegram, нужно:</p>
                    <ol style="margin-left: 20px; margin-bottom: 10px;">
                        <li>Найти свой Chat ID через бота <a href="https://t.me/userinfobot" target="_blank" style="color: #ec4899; text-decoration: underline;">@userinfobot</a> в Telegram</li>
                        <li>Написать боту команду <strong style="color: #ec4899;">/start</strong></li>
                        <li>Только после этого начинать играть</li>
                    </ol>
                    <p style="font-size: 0.9rem; color: #9ca3af; margin-top: 15px;">
                        💡 Бот не сможет отправить вам промокод, если вы ему не написали первым!
                    </p>
                </div>
            </div>
            
            <a href="{{ route('game') }}" class="cta-button">
                Начать игру ✨
            </a>
        </div>
    </div>
    
    <script>
        // Добавляем дополнительные эффекты при загрузке
        document.addEventListener('DOMContentLoaded', function() {
            // Анимация появления элементов
            const features = document.querySelectorAll('.feature');
            features.forEach((feature, index) => {
                setTimeout(() => {
                    feature.style.opacity = '0';
                    feature.style.animation = 'slideUp 0.6s ease-out forwards';
                }, index * 100);
            });
            
            // Эффект при наведении на кнопку
            const ctaButton = document.querySelector('.cta-button');
            ctaButton.addEventListener('mouseenter', function() {
                this.style.background = 'linear-gradient(135deg, #f472b6 0%, #c084fc 100%)';
            });
            ctaButton.addEventListener('mouseleave', function() {
                this.style.background = 'linear-gradient(135deg, #ec4899 0%, #a855f7 100%)';
            });
        });
    </script>
</body>
</html>
