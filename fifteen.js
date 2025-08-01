/**
 * fifteen.js - Main logic for the Fifteen Puzzle Game
 * Description: Handles tile generation, shuffling, movement, win detection,
 * game timer, cheat functionality, background selection, and music control.
 */

let tiles = [];
let tileOrder = [];
let shuffleMoves = [];
let moveCount = 0;
let timer = 0;
let timerInterval;
let gameEnded = false;
const MAX_TIME = 600;

/**
 * Initializes event listeners on page load (buttons and music toggle).
 */
window.onload = function () {
    document.getElementById("startBtn").addEventListener("click", startGame);
    document.getElementById("cheatBtn").addEventListener("click", cheat);

    document.getElementById("musicToggle").addEventListener("click", () => {
        const music = document.getElementById("bgMusic");
        const btn = document.getElementById("musicToggle");
        if (music.paused) {
            music.play().then(() => {
                btn.textContent = "Pause Music";
            }).catch(() => {});
        } else {
            music.pause();
            btn.textContent = "Play Music";
        }
    });
};

/**
 * Returns the user ID from the hidden HTML input field.
 */
function getUserId() {
    return parseInt(document.getElementById("userId")?.value, 10);
}

/**
 * Returns the selected background image ID from the dropdown.
 */
function getBackgroundId() {
    const selectedOption = document.getElementById("backgroundSelector")?.selectedOptions[0];
    return selectedOption ? parseInt(selectedOption.dataset.id, 10) : 0;
}

/**
 * Creates and initializes the 15 puzzle tiles and the empty slot.
 */
function createTiles() {
    const puzzleArea = document.getElementById("puzzlearea");
    puzzleArea.innerHTML = "";
    tiles = [];
    tileOrder = [];

    for (let i = 0; i < 15; i++) {
        const tile = document.createElement("div");
        tile.className = "tile";
        tile.textContent = i + 1;
        tile.correctX = i % 4;
        tile.correctY = Math.floor(i / 4);

        // Attach event listener using correct index on click
        tile.addEventListener("click", () => {
            const index = tiles.indexOf(tile);
            if (tryMove(index, "player") && checkWin()) {
                endGame(true);
            }
        });

        tiles.push(tile);
        puzzleArea.appendChild(tile);
        tileOrder.push(i);
    }

    tileOrder.push(null); // Represents empty slot
    updateTilePositions();
    updateTileBackgrounds();
}

/**
 * Updates tile element positions based on their order in tileOrder array.
 */
function updateTilePositions() {
    for (let i = 0; i < tileOrder.length; i++) {
        const tileIndex = tileOrder[i];
        if (tileIndex === null) continue;
        const tile = tiles[tileIndex];
        const x = i % 4;
        const y = Math.floor(i / 4);
        tile.style.left = `${x * 100}px`;
        tile.style.top = `${y * 100}px`;
    }
}

/**
 * Sets each tile's background image position to reflect its correct portion.
 */
function updateTileBackgrounds() {
    for (const tile of tiles) {
        const x = tile.correctX * -100;
        const y = tile.correctY * -100;
        tile.style.backgroundImage = `url('${window.backgroundImage}')`;
        tile.style.backgroundPosition = `${x}px ${y}px`;
        tile.style.backgroundSize = `400px 400px`;
    }
}

/**
 * Attempts to move a tile if it is adjacent to the empty space.
 * @param {number} index - Index of the tile in tiles[]
 * @param {string} source - "player", "shuffle", or "cheat"
 * @returns {boolean} Whether the move was successful
 */
function tryMove(index, source = "player") {
    if (gameEnded) return false;

    const tilePos = tileOrder.indexOf(index);
    const emptyPos = tileOrder.indexOf(null);
    const x1 = tilePos % 4, y1 = Math.floor(tilePos / 4);
    const x2 = emptyPos % 4, y2 = Math.floor(emptyPos / 4);

    if ((x1 === x2 && Math.abs(y1 - y2) === 1) || (y1 === y2 && Math.abs(x1 - x2) === 1)) {
        [tileOrder[tilePos], tileOrder[emptyPos]] = [tileOrder[emptyPos], tileOrder[tilePos]];
        updateTilePositions();

        if (source === "player") {
            moveCount++;
            document.getElementById("moveCount").textContent = moveCount;
            shuffleMoves.push(index);
        } else if (source === "shuffle") {
            shuffleMoves.push(index);
        }

        return true;
    }

    return false;
}

/**
 * Returns a list of tile indexes that can currently be moved into the empty space.
 */
function getMovableIndexes() {
    const emptyPos = tileOrder.indexOf(null);
    const x = emptyPos % 4;
    const y = Math.floor(emptyPos / 4);
    const moves = [];

    // Check up, down, left, right positions
    [[x, y - 1], [x, y + 1], [x - 1, y], [x + 1, y]].forEach(([nx, ny]) => {
        if (nx >= 0 && nx < 4 && ny >= 0 && ny < 4) {
            const index = ny * 4 + nx;
            moves.push(tileOrder[index]);
        }
    });

    return moves;
}

/**
 * Performs 100 random valid moves to shuffle the puzzle.
 */
function shufflePuzzle() {
    shuffleMoves = [];
    let lastTile = null;
    for (let i = 0; i < 100; i++) {
        const movable = getMovableIndexes();
        const filtered = movable.filter(tile => tile !== lastTile);
        const candidates = filtered.length > 0 ? filtered : movable;
        const pick = candidates[Math.floor(Math.random() * candidates.length)];
        tryMove(pick, "shuffle");
        lastTile = pick;
    }
}

/**
 * Starts a new game by resetting state, shuffling, and starting the timer.
 */
function startGame() {
    gameEnded = false;
    moveCount = 0;
    shuffleMoves = [];
    timer = 0;
    document.getElementById("moveCount").textContent = "0";
    document.getElementById("timer").textContent = "0";
    clearInterval(timerInterval);
    createTiles();
    shufflePuzzle();
    timerInterval = setInterval(() => {
        timer++;
        document.getElementById("timer").textContent = `${timer}`;
        if (timer >= MAX_TIME) {
            endGame(false);
        }
    }, 1000);
}

/**
 * Reverses the shuffleMoves and replays them to solve the puzzle step-by-step.
 */
async function cheat() {
    if (gameEnded) return;
    const reversed = [...shuffleMoves].reverse();
    for (let i = 0; i < reversed.length; i++) {
        await new Promise(res => setTimeout(res, 700));
        tryMove(reversed[i], false);
    }

    // Check if it’s truly solved after cheat animation
    if (checkWin()) {
        endGame(true);
    } else {
        endGame(false);
    }
}

/**
 * Checks if the current tile order matches the solved state.
 * @returns {boolean} Whether the puzzle is solved
 */
function checkWin() {
    for (let i = 0; i < 15; i++) {
        if (tileOrder[i] !== i) return false;
    }
    return true;
}

/**
 * Ends the game and sends stats to save_stats.php via POST.
 * Redirects to congrats.php if solved successfully.
 * @param {boolean} didWin - Whether the player won
 */
function endGame(didWin) {
    if (gameEnded) return;
    clearInterval(timerInterval);
    gameEnded = true;

    const stats = {
        user_id: getUserId(),
        moves: moveCount,
        time: timer,
        status: didWin ? "success" : "fail",
        background_id: getBackgroundId() || 0,
        size: 4
    };

    fetch("save_stats.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(stats)
    })
    .then(response => response.json())
    .then(data => {
        if (didWin) {
            window.location.href = "congrats.php";
        } else if (!data.success) {
            alert("Game ended but failed to save stats.");
        }
    })
    .catch(error => {
        alert("Game ended but failed to save stats.");
    });
}
