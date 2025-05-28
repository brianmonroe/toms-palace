<?php
// Disable browser and proxy cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false); // For IE compatibility
header("Pragma: no-cache");
header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");

// SiteGround-specific cache bypass header
header("X-SG-Cache-Control: no-store");

// Handle form submissions
if ($_POST) {
    // Load existing games from file or initialize empty array
    $games_file = 'games.json';
    if (file_exists($games_file)) {
        $games = json_decode(file_get_contents($games_file), true);
    } else {
        $games = [];
    }
    
    // Add new game
    if (isset($_POST['add_game'])) {
        $new_game = [
            'home_team' => $_POST['home_team'],
            'away_team' => $_POST['away_team'],
            'date' => $_POST['date'],
            'time' => $_POST['time'],
            'home_score' => null,
            'away_score' => null,
            'status' => 'scheduled'
        ];
        $games[] = $new_game;
        file_put_contents($games_file, json_encode($games, JSON_PRETTY_PRINT));
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Update game score
    if (isset($_POST['update_score'])) {
        $game_index = $_POST['game_index'];
        if (isset($games[$game_index])) {
            $games[$game_index]['home_score'] = $_POST['home_score'];
            $games[$game_index]['away_score'] = $_POST['away_score'];
            $games[$game_index]['status'] = 'completed';
            file_put_contents($games_file, json_encode($games, JSON_PRETTY_PRINT));
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Delete game
    if (isset($_POST['delete_game'])) {
        $game_index = $_POST['game_index'];
        if (isset($games[$game_index])) {
            unset($games[$game_index]);
            $games = array_values($games); // Re-index array
            file_put_contents($games_file, json_encode($games, JSON_PRETTY_PRINT));
        }
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Load games from file or use default data
$games_file = 'games.json';
if (file_exists($games_file)) {
    $games = json_decode(file_get_contents($games_file), true);
} else {
    // Default sample data
    $games = [
        [
            'home_team' => 'Pickle Warriors',
            'away_team' => 'Dill Destroyers',
            'date' => '2025-06-01',
            'time' => '14:00',
            'home_score' => 3,
            'away_score' => 2,
            'status' => 'completed'
        ],
        [
            'home_team' => 'Gherkin Giants',
            'away_team' => 'Brine Bombers',
            'date' => '2025-06-05',
            'time' => '18:30',
            'home_score' => null,
            'away_score' => null,
            'status' => 'scheduled'
        ],
        [
            'home_team' => 'Pickle Warriors',
            'away_team' => 'Cucumber Crushers',
            'date' => '2025-06-10',
            'time' => '16:00',
            'home_score' => null,
            'away_score' => null,
            'status' => 'scheduled'
        ]
    ];
    // Save default data to file
    file_put_contents($games_file, json_encode($games, JSON_PRETTY_PRINT));
}

// Function to format date for display
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Function to format time for display
function formatTime($time) {
    return date('g:i A', strtotime($time));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tom's Pickle Palace - Game Schedule</title>
    <style>
        body {
            font-family: System-Ui, Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            margin: 0;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            margin: 10px 0 0 0;
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .main-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
        }
        
        .schedule-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-panel {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .admin-panel h3 {
            margin-top: 0;
            color: #2E7D32;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #45a049;
        }
        
        .btn-secondary {
            background: #f44336;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-secondary:hover {
            background: #da190b;
        }
        
        .btn-update {
            background: #2196F3;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-update:hover {
            background: #0b7dda;
        }
        
        .game-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: white;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .game-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .game-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .matchup {
            font-size: 1.3em;
            font-weight: bold;
            color: #2E7D32;
        }
        
        .vs {
            color: #666;
            margin: 0 10px;
        }
        
        .game-details {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        
        .detail-item {
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .detail-label {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-weight: bold;
            font-size: 1.1em;
        }
        
        .score {
            font-size: 1.5em;
            color: #4CAF50;
        }
        
        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status.completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status.scheduled {
            background: #fff3cd;
            color: #856404;
        }
        
        .game-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .score-form {
            display: inline-flex;
            gap: 5px;
            align-items: center;
        }
        
        .score-form input {
            width: 50px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-align: center;
        }
        
        .no-games {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        
        .toggle-admin {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            z-index: 1000;
        }
        
        .toggle-admin:hover {
            background: #45a049;
        }
        
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .admin-panel {
                order: -1;
            }
        }
        
        @media (max-width: 768px) {
            .game-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .game-details {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .matchup {
                text-align: center;
                margin-bottom: 10px;
            }
            
            .game-actions {
                flex-direction: column;
            }
            
            .score-form {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🥒 Tom's Pickle Palace</h1>
        <p>Home of Championship Pickle Ball</p>
    </div>
    
    <button class="toggle-admin" onclick="toggleAdmin()">Admin Panel</button>
    
    <div class="main-content">
        <div class="schedule-container">
            <h2>Game Schedule</h2>
            
            <?php if (empty($games)): ?>
                <div class="no-games">
                    No games scheduled at this time.
                </div>
            <?php else: ?>
                <?php foreach ($games as $index => $game): ?>
                    <div class="game-card">
                        <div class="game-header">
                            <div class="matchup">
                                <?php echo htmlspecialchars($game['home_team']); ?>
                                <span class="vs">vs</span>
                                <?php echo htmlspecialchars($game['away_team']); ?>
                            </div>
                            <div class="status <?php echo $game['status']; ?>">
                                <?php echo $game['status']; ?>
                            </div>
                        </div>
                        
                        <div class="game-details">
                            <div class="detail-item">
                                <div class="detail-label">Date</div>
                                <div class="detail-value"><?php echo formatDate($game['date']); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Time</div>
                                <div class="detail-value"><?php echo formatTime($game['time']); ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label">Score</div>
                                <div class="detail-value">
                                    <?php if ($game['status'] === 'completed' && $game['home_score'] !== null && $game['away_score'] !== null): ?>
                                        <span class="score"><?php echo $game['home_score']; ?> - <?php echo $game['away_score']; ?></span>
                                    <?php else: ?>
                                        <span style="color: #999;">TBD</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="game-actions admin-only" style="display: none;">
                            <?php if ($game['status'] === 'scheduled'): ?>
                                <form method="POST" class="score-form">
                                    <input type="hidden" name="game_index" value="<?php echo $index; ?>">
                                    <input type="number" name="home_score" placeholder="Home" min="0" required>
                                    <span>-</span>
                                    <input type="number" name="away_score" placeholder="Away" min="0" required>
                                    <button type="submit" name="update_score" class="btn btn-update">Update Score</button>
                                </form>
                            <?php endif; ?>
                            
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="game_index" value="<?php echo $index; ?>">
                                <button type="submit" name="delete_game" class="btn btn-secondary" 
                                        onclick="return confirm('Are you sure you want to delete this game?')">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="admin-panel admin-only" style="display: none;">
            <h3>Add New Game</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="home_team">Home Team:</label>
                    <input type="text" id="home_team" name="home_team" required>
                </div>
                
                <div class="form-group">
                    <label for="away_team">Away Team:</label>
                    <input type="text" id="away_team" name="away_team" required>
                </div>
                
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="time">Time:</label>
                    <input type="time" id="time" name="time" required>
                </div>
                
                <button type="submit" name="add_game" class="btn btn-primary">Add Game</button>
            </form>
        </div>
    </div>

    <script>
        function toggleAdmin() {
            const adminElements = document.querySelectorAll('.admin-only');
            const toggleButton = document.querySelector('.toggle-admin');
            
            adminElements.forEach(element => {
                if (element.style.display === 'none') {
                    element.style.display = 'block';
                    toggleButton.textContent = 'Hide Admin';
                } else {
                    element.style.display = 'none';
                    toggleButton.textContent = 'Admin Panel';
                }
            });
        }
    </script>
</body>
</html>
