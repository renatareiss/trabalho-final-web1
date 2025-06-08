// 1. Global Variables & DOM References
const textToTypeElement = document.getElementById('text-to-type');
const userInputElement = document.getElementById('user-input');
const timeLeftElement = document.getElementById('time-left');
const wpmValueElement = document.getElementById('wpm-value');
const accuracyValueElement = document.getElementById('accuracy-value');
const startButton = document.getElementById('start-button');
const resetButton = document.getElementById('reset-button'); // Assuming it exists from game.php

let timer = null;
let timeLeft = 60; // Default game time
const INITIAL_TIME = 60;
let currentText = "";
let typedChars = 0; // Total characters typed by the user in the current text segment
let correctChars = 0; // Correct characters typed in the current text segment
let mistakes = 0; // Mistakes in the current text segment
let gameStarted = false;
let startTime = null; // To calculate elapsed time for WPM

// const sampleTexts = [ ... ]; // This array is no longer needed

// 2. Initialization Function (initGame)
async function initGame() { // Made async to await loadNewText if it's async
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
    gameStarted = false;
    timeLeft = INITIAL_TIME;
    currentText = "";
    typedChars = 0;
    correctChars = 0;
    mistakes = 0;
    startTime = null;

    await loadNewText(); // Load a new text, await if it involves fetch

    userInputElement.value = "";
    userInputElement.disabled = true;

    timeLeftElement.textContent = timeLeft;
    wpmValueElement.textContent = "0";
    accuracyValueElement.textContent = "100";

    startButton.disabled = false;
    startButton.textContent = "Start Game";
    resetButton.style.display = "none"; // Hide reset button initially
    startButton.style.display = "inline-block";


    // Text display is handled by loadNewText after fetching
    // textToTypeElement.innerHTML = currentText.split('').map(char => `<span>${char}</span>`).join('');
}

// 6. Load New Text Function
async function loadNewText() { // Make async to use await for fetch
    try {
        const response = await fetch('includes/get_text.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        if (data.error) {
            throw new Error(`Server error: ${data.error}`);
        }
        currentText = data.text;

        textToTypeElement.innerHTML = ""; // Clear previous text
        currentText.split('').forEach(char => {
            const charSpan = document.createElement('span');
            charSpan.textContent = char;
            textToTypeElement.appendChild(charSpan);
        });

    } catch (error) {
        console.error("Failed to load new text:", error);
        // Fallback or error display logic
        currentText = "Error loading text. Please try again. The quick brown fox jumps over the lazy dog."; // Fallback text
        textToTypeElement.innerHTML = currentText.split('').map(char => `<span>${char}</span>`).join('');
    } finally {
        userInputElement.value = ""; // Clear input for new text
        // Reset counters for the new text
        typedChars = 0;
        correctChars = 0;
        mistakes = 0;
        // Any other resets specific to new text loading
    }
}


// 3. Start Game Function
async function startGame() { // Made async to await initGame
    if (gameStarted) return;

    gameStarted = true;
    await initGame(); // Reset and load new text before starting timer

    userInputElement.disabled = false;
    userInputElement.focus();

    startButton.disabled = true;
    startButton.style.display = "none"; // Hide start button
    resetButton.style.display = "inline-block"; // Show reset button
    resetButton.disabled = false;


    startTime = new Date().getTime(); // Record start time

    timer = setInterval(() => {
        timeLeft--;
        timeLeftElement.textContent = timeLeft;
        if (timeLeft <= 0) {
            endGame();
        }
        // WPM and Accuracy are updated in handleInput
    }, 1000);
}

// 4. End Game Function
function endGame() {
    if (!gameStarted && timeLeft > 0) return; // Prevent ending if not started or timer still running

    gameStarted = false;
    clearInterval(timer);
    timer = null;
    userInputElement.disabled = true;

    startButton.disabled = false;
    startButton.textContent = "Play Again?";
    startButton.style.display = "inline-block";
    resetButton.style.display = "none"; // Hide reset button when game ends

    // Final WPM and Accuracy are already calculated in handleInput.
    // If no input, WPM is 0, Accuracy is 100 or 0 depending on how you define.
    if (typedChars === 0) {
        wpmValueElement.textContent = "0";
        accuracyValueElement.textContent = "0"; // Or 100 if no mistakes made on 0 chars
    }

    // TODO: Call function to save score to server
    const finalWPM = parseInt(wpmValueElement.textContent);
    const finalAccuracy = parseFloat(accuracyValueElement.textContent);

    if (typedChars > 0) { // Only save if user typed something
        fetch('score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                wpm: finalWPM,
                accuracy: finalAccuracy,
            }),
        })
        .then(response => response.json())
        .then(data => {
            console.log('Score save response:', data);
            if (data.status === 'success') {
                // Optionally display a success message to the user
                console.log('Score saved successfully!');
            } else {
                // Optionally display an error message
                console.error('Failed to save score:', data.message);
            }
        })
        .catch((error) => {
            console.error('Error saving score:', error);
            // Optionally display a network error message
        });
    } else {
        console.log("No characters typed, score not saved.");
    }
    console.log("Game Ended. WPM:", finalWPM, "Accuracy:", finalAccuracy + "%");
}

// 5. Input Handling Function
function handleInput() {
    if (!gameStarted) return;

    const inputText = userInputElement.value;
    const textLength = currentText.length;
    typedChars = inputText.length;
    correctChars = 0;
    mistakes = 0;

    let htmlText = "";
    for (let i = 0; i < textLength; i++) {
        if (i < inputText.length) {
            if (inputText[i] === currentText[i]) {
                htmlText += `<span class="correct">${currentText[i]}</span>`;
                correctChars++;
            } else {
                htmlText += `<span class="incorrect">${currentText[i]}</span>`;
                mistakes++;
            }
        } else {
            htmlText += `<span>${currentText[i]}</span>`; // Untyped characters
        }
    }
    textToTypeElement.innerHTML = htmlText;

    // Calculate WPM
    const currentTime = new Date().getTime();
    const timeElapsedInSeconds = (currentTime - startTime) / 1000;
    const timeElapsedInMinutes = timeElapsedInSeconds / 60;

    if (timeElapsedInMinutes > 0) {
        // Standard WPM: (characters / 5) / minutes. Or (words / minutes)
        // Using (correctChars / 5) / minutes for a stricter WPM based on correct typing
        const wpm = Math.round(((correctChars / 5) / timeElapsedInMinutes));
        wpmValueElement.textContent = wpm < 0 ? 0 : wpm;
    } else {
        wpmValueElement.textContent = "0";
    }

    // Calculate Accuracy
    if (typedChars > 0) {
        const accuracy = Math.round(((correctChars) / typedChars) * 100);
        accuracyValueElement.textContent = accuracy < 0 ? 0 : accuracy;
    } else {
        accuracyValueElement.textContent = "100"; // Or 0 if you prefer
    }

    // Check if all text is typed correctly
    if (correctChars === textLength && mistakes === 0 && typedChars === textLength) {
        // Optional: Load new text immediately or give bonus
        // For now, let timer run out or user can reset.
        // To load new text immediately:
        // loadNewText();
        // userInputElement.value = ""; // Clear input
        // startTime = new Date().getTime(); // Reset start time for WPM calculation of new text
        // typedChars = 0; correctChars = 0; mistakes = 0; // Reset counters for new text
    }
}


// 7. Event Listeners
startButton.addEventListener('click', startGame); // startGame is now async
resetButton.addEventListener('click', () => {
    if(timer) clearInterval(timer); // Stop any active timer
    initGame(); // initGame is now async, reset to initial screen
});
userInputElement.addEventListener('input', handleInput);

// 8. Initial Call
document.addEventListener('DOMContentLoaded', () => {
    initGame(); // initGame is now async
});
