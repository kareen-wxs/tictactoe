<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Крестики-Нолики</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700|inter:400,500,600" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef7f0 0%, #fce7f3 50%, #f3e8ff 100%);
            min-height: 100vh;
        }
        
        .game-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(236, 72, 153, 0.15);
            padding: 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .game-title {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 600;
            background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .game-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 30px 0;
        }
        
        .cell {
            aspect-ratio: 1;
            background: linear-gradient(135deg, #fff5f7 0%, #fef3f2 100%);
            border: 3px solid #fce7f3;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .cell:hover:not(.disabled) {
            transform: translateY(-5px) scale(1.05);
            border-color: #ec4899;
            box-shadow: 0 10px 25px rgba(236, 72, 153, 0.2);
            background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%);
        }
        
        .cell.disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }
        
        .cell.x {
            color: #ec4899;
        }
        
        .cell.o {
            color: #a855f7;
        }
        
        .status-message {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 500;
            color: #6b7280;
            margin: 20px 0;
            min-height: 30px;
        }
        
        .status-message.winner {
            color: #ec4899;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .status-message.loser {
            color: #6b7280;
        }
        
        .promo-code {
            background: linear-gradient(135deg, #fef3f2 0%, #fce7f3 100%);
            border: 2px dashed #ec4899;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        
        .promo-code-text {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 5px;
            color: #ec4899;
            font-family: 'Inter', monospace;
        }
        
        .promo-label {
            font-size: 0.9rem;
            color: #9ca3af;
            margin-bottom: 10px;
        }
        
        .btn {
            background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%);
            color: white;
            border: none;
            border-radius: 15px;
            padding: 15px 40px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(236, 72, 153, 0.3);
            margin: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(236, 72, 153, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #f3e8ff 0%, #fce7f3 100%);
            color: #a855f7;
            box-shadow: 0 4px 15px rgba(168, 85, 247, 0.2);
        }
        
        .btn-secondary:hover {
            box-shadow: 0 6px 20px rgba(168, 85, 247, 0.3);
        }
        
        .button-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
        }
        
        .modal-content {
            background: white;
            border-radius: 25px;
            padding: 40px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalAppear 0.3s ease;
        }
        
        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 600;
            color: #ec4899;
            margin-bottom: 20px;
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #ec4899;
            position: absolute;
            animation: confetti-fall 3s linear infinite;
        }
        
        @keyframes confetti-fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="game-container" style="position: relative;">
            <h1 class="game-title">Крестики-Нолики</h1>
            
            <div style="background: linear-gradient(135deg, #fff5f7 0%, #fef3f2 100%); border: 2px solid #ec4899; border-radius: 20px; padding: 20px; margin-bottom: 25px; text-align: center;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 10px;">
                    <span style="font-size: 1.5rem;">📱</span>
                    <strong style="color: #ec4899; font-size: 1.1rem;">Перед игрой напишите боту /start!</strong>
                </div>
                <p style="color: #6b7280; font-size: 0.9rem; line-height: 1.6;">
                    Найдите вашего бота в Telegram и отправьте команду <strong style="color: #ec4899;">/start</strong>,<br>
                    чтобы получать промокоды при победе!
                </p>
            </div>
            
            <div class="status-message" id="statusMessage">Ваш ход! Вы играете за X</div>
            
            <div class="game-board" id="gameBoard">
                <div class="cell" data-index="0"></div>
                <div class="cell" data-index="1"></div>
                <div class="cell" data-index="2"></div>
                <div class="cell" data-index="3"></div>
                <div class="cell" data-index="4"></div>
                <div class="cell" data-index="5"></div>
                <div class="cell" data-index="6"></div>
                <div class="cell" data-index="7"></div>
                <div class="cell" data-index="8"></div>
            </div>
            
            <div id="promoCodeContainer" style="display: none;">
                <div class="promo-code">
                    <div class="promo-label">Ваш промокод на скидку:</div>
                    <div class="promo-code-text" id="promoCode"></div>
                </div>
            </div>
            
            <div class="button-container">
                <button class="btn" id="resetBtn" style="display: none;">Играть снова</button>
            </div>
        </div>
    </div>
    
    <div class="modal" id="loseModal">
        <div class="modal-content">
            <h2 class="modal-title">Попробуйте ещё раз!</h2>
            <p style="color: #6b7280; margin-bottom: 30px;">Не расстраивайтесь, в следующий раз обязательно получится! 💪</p>
            <button class="btn" onclick="resetGame()">Играть снова</button>
        </div>
    </div>
    
    <script>
        let board = ['', '', '', '', '', '', '', '', ''];
        let currentPlayer = 'X';
        let gameActive = true;
        let playerWins = false;
        
        const cells = document.querySelectorAll('.cell');
        const statusMessage = document.getElementById('statusMessage');
        const resetBtn = document.getElementById('resetBtn');
        const promoCodeContainer = document.getElementById('promoCodeContainer');
        const promoCodeText = document.getElementById('promoCode');
        const loseModal = document.getElementById('loseModal');
        cells.forEach((cell, index) => {
            cell.addEventListener('click', () => handleCellClick(index));
        });
        
        resetBtn.addEventListener('click', resetGame);
        
        function handleCellClick(index) {
            if (board[index] !== '' || !gameActive) return;
            
            board[index] = currentPlayer;
            updateCell(index, currentPlayer);
            
            if (checkWinner()) {
                playerWins = true;
                gameActive = false;
                handleWin();
                return;
            }
            
            if (checkDraw()) {
                gameActive = false;
                handleDraw();
                return;
            }
            
            // Ход компьютера
            setTimeout(() => {
                if (gameActive) {
                    makeComputerMove();
                }
            }, 500);
        }
        
        function makeComputerMove() {
            // Простой AI: сначала пытается выиграть, потом блокирует игрока, иначе случайный ход
            let move = findWinningMove('O');
            if (move === -1) {
                move = findWinningMove('X');
            }
            if (move === -1) {
                move = findBestMove();
            }
            
            if (move !== -1) {
                board[move] = 'O';
                updateCell(move, 'O');
                
                if (checkWinner()) {
                    gameActive = false;
                    handleLose();
                } else if (checkDraw()) {
                    gameActive = false;
                    handleDraw();
                }
            }
        }
        
        function findWinningMove(player) {
            for (let i = 0; i < 9; i++) {
                if (board[i] === '') {
                    board[i] = player;
                    if (checkWinnerForPlayer(player)) {
                        board[i] = '';
                        return i;
                    }
                    board[i] = '';
                }
            }
            return -1;
        }
        
        function findBestMove() {
            const availableMoves = [];
            for (let i = 0; i < 9; i++) {
                if (board[i] === '') {
                    availableMoves.push(i);
                }
            }
            return availableMoves.length > 0 
                ? availableMoves[Math.floor(Math.random() * availableMoves.length)]
                : -1;
        }
        
        function updateCell(index, player) {
            const cell = cells[index];
            cell.textContent = player;
            cell.classList.add(player.toLowerCase());
            cell.classList.add('disabled');
        }
        
        function checkWinner() {
            return checkWinnerForPlayer(currentPlayer);
        }
        
        function checkWinnerForPlayer(player) {
            const winConditions = [
                [0, 1, 2], [3, 4, 5], [6, 7, 8],
                [0, 3, 6], [1, 4, 7], [2, 5, 8],
                [0, 4, 8], [2, 4, 6]
            ];
            
            return winConditions.some(condition => {
                return condition.every(index => board[index] === player);
            });
        }
        
        function checkDraw() {
            return board.every(cell => cell !== '');
        }
        
        async function handleWin() {
            statusMessage.textContent = '🎉 Поздравляем! Вы выиграли!';
            statusMessage.classList.add('winner');
            
            // Генерируем промокод
            try {
                console.log('Генерация промокода...');
                const response = await fetch('/api/promo/generate');
                
                if (!response.ok) {
                    throw new Error('Ошибка при генерации промокода: ' + response.status);
                }
                
                const data = await response.json();
                const promoCode = data.promo_code;
                
                console.log('Промокод сгенерирован:', promoCode);
                
                promoCodeText.textContent = promoCode;
                promoCodeContainer.style.display = 'block';
                
                // Отправляем сообщение в Telegram
                await sendTelegramMessage(`Победа! Промокод выдан: ${promoCode}`);
                
                createConfetti();
            } catch (error) {
                console.error('Error handling win:', error);
                alert('Ошибка при обработке победы: ' + error.message);
            }
            
            resetBtn.style.display = 'block';
            disableAllCells();
        }
        
        async function handleLose() {
            statusMessage.textContent = 'К сожалению, вы проиграли 😔';
            statusMessage.classList.add('loser');
            
            // Отправляем сообщение в Telegram
            try {
                await sendTelegramMessage('Проигрыш');
            } catch (error) {
                console.error('Error handling lose:', error);
            }
            
            loseModal.classList.add('active');
            disableAllCells();
        }
        
        function handleDraw() {
            statusMessage.textContent = 'Ничья! Попробуйте ещё раз!';
            statusMessage.classList.add('loser');
            resetBtn.style.display = 'block';
            disableAllCells();
        }
        
        function disableAllCells() {
            cells.forEach(cell => {
                cell.classList.add('disabled');
            });
        }
        
        function resetGame() {
            board = ['', '', '', '', '', '', '', '', ''];
            currentPlayer = 'X';
            gameActive = true;
            playerWins = false;
            
            cells.forEach(cell => {
                cell.textContent = '';
                cell.classList.remove('x', 'o', 'disabled');
            });
            
            statusMessage.textContent = 'Ваш ход! Вы играете за X';
            statusMessage.classList.remove('winner', 'loser');
            resetBtn.style.display = 'none';
            promoCodeContainer.style.display = 'none';
            loseModal.classList.remove('active');
        }
        
        async function sendTelegramMessage(message) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (!csrfToken) {
                    console.error('CSRF token not found!');
                    alert('Ошибка: CSRF токен не найден. Обновите страницу.');
                    return;
                }
                
                console.log('Отправка сообщения в Telegram:', message);
                
                const response = await fetch('/api/telegram/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message })
                });
                
                const data = await response.json();
                console.log('Ответ от сервера:', data);
                
                if (!data.success) {
                    console.error('Ошибка отправки в Telegram:', data.error);
                    
                    // Показываем понятное сообщение об ошибке
                    if (data.error && (data.error.includes('Chat ID не указан') || data.error.includes('chat not found'))) {
                        alert('⚠️ Не удалось отправить промокод в Telegram.\n\nУбедитесь, что вы написали боту команду /start перед игрой!');
                    } else {
                        alert('Не удалось отправить сообщение в Telegram: ' + (data.error || 'Неизвестная ошибка'));
                    }
                } else {
                    console.log('Сообщение успешно отправлено в Telegram!');
                }
            } catch (error) {
                console.error('Ошибка при отправке в Telegram:', error);
                alert('Ошибка при отправке сообщения: ' + error.message);
            }
        }
        
        function createConfetti() {
            const colors = ['#ec4899', '#a855f7', '#f472b6', '#c084fc', '#fb7185'];
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.animationDelay = Math.random() * 0.5 + 's';
                    confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    document.body.appendChild(confetti);
                    
                    setTimeout(() => {
                        confetti.remove();
                    }, 5000);
                }, i * 50);
            }
        }
    </script>
</body>
</html>

