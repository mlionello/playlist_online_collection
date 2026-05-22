<?php

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Session expired or not set. Please restart the experiment.']);
    http_response_code(401); // Unauthorized
    exit;
}
$_SESSION['step'] = 'instruction';

// Ensure the random words are available in the session
if (!isset($_SESSION['random_labels']) || count($_SESSION['random_labels']) < 4) {
    echo json_encode(['error' => 'Required labels are missing. Please restart the experiment.']);
    http_response_code(500); // Internal Server Error
    exit;
}

// Assign the random labels to the sliders
$labels = $_SESSION['random_labels'];

// Ensure the random words are available in the session
$play_button_text = "Start Example";
$stop = "I am happy with the volume";
$wait_loading_button_text = 'Please wait, loading song...';
$wait_start_exp_button_text = 'Please start example audio first';

if (isset($_SESSION['lang']) && $_SESSION['lang'] == "ita") {
    $play_button_text = "Riproduci Esempio";
    $stop = "Ho impostato e sono soddisfatto con il volume dell'audio";
    $wait_loading_button_text = "Caricamento audio, attendere...";
    $wait_start_exp_button_text = 'Per favore prima l\'audio di esempio';
}

// Prepare HTML content to be returned
ob_start(); // Start output buffering
?>
<?php
if (isset($_SESSION['lang']) && $_SESSION['lang'] == "eng") {
echo '
';
} else {
echo '
    <h1>Istruzioni</h1>
    <p>Sei pronto per iniziare l\'esperimento! Ascolterai 8 tracce audio che spaziano in diversi generi musicali. Ognuno e\' di durata fra 90 e 225 secondi.</p>
    <p>Durante l\'ascolto, verranno visualizzati dei cursori. Ogni cursore è associato a un\'etichetta:
    <ul>
    <li>Ti chiediamo di interagire con questi cursori per tenere traccia delle sensazioni che <b>l\'autore voleva trasmettere</b> con il brano;</li>
    <li> Muovi i cursori nella direzione desiderata <b>non appena percepisci un cambiamento nella musica rispetto la sensazione riferita da ciascun cursore</b>.</li>
    </ul>
    Puoi muovere il cursore in un intervallo di valori che vanno da "assenza di sensazioni" (0) a sinistra, a "massima intensità della sensazione" (100), a destra.</p>
    <p>Dopo l\'ascolto di ciascuna traccia, ti verrà chiesto di valutare alcuni aspetti specifici di quanto ascoltato e di fornire alcune risposte.</p>
    <p style="padding-top: 20px">Ecco un esempio: <b>indossa le cuffie ora</b>, fai clic su inizia brano e regola ora il volume al livello desiderato:</p>
';
}
?>
<div style="align-self:center; text-align:center; padding: 0 20px 0 20px">
    <button id="startexample" onclick="playAudio()" style="margin-right: 20px"></button>
    <button id="stopexample" onclick="stopAudio()"></button><br>
    <audio id="audioPlayer" src="./audio/schumann4_IIImvt_short.wav"></audio>
</div>
<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == "eng") ?
    '<p style="padding-top: 20px">These are the sliders you will interact with during the listening:</p>' :
    '<p style="padding-top: 20px">Questi saranno i cursori con cui interagirai durante l\'ascolto. Leggi attentamente le etichette associate. puoi provare a muovere i cursori per prendere confidenza con il compito:</p>';
?>
<div class="experiment-container" style="height:auto">
    <div class="sliders-container">
    <div style="align-self:center; padding: 0 20px 0 20px; display:grid; grid-template-columns: 3fr 6fr">
        <label style="margin-bottom:40px"></label>        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span>0</span>
            <span>100</span>
        </div>
        <label class="label" for="slider1"><?php echo $labels[0][0]; ?></label>
        <input type="range" class="slider" id="slider1" min="0" max="100" value="0">

        <label class="label" for="slider2"><?php echo $labels[1][0]; ?></label>
        <input type="range" class="slider" id="slider2" min="0" max="100" value="0">

        <label class="label" for="slider3"><?php echo $labels[2][0]; ?></label>
        <input type="range" class="slider" id="slider3" min="0" max="100" value="0">

        <label class="label" for="slider4"><?php echo $labels[3][0]; ?></label>
        <input type="range" class="slider" id="slider4" min="0" max="100" value="0">

        <label class="label" for="slider5"><?php echo $labels[4][0]; ?></label>
        <input type="range" class="slider" id="slider5" min="0" max="100" value="0">

        <label class="label" for="slider6"><?php echo $labels[5][0]; ?></label>
        <input type="range" class="slider" id="slider6" min="0" max="100" value="0">

        <label class="label" for="slider7"><?php echo $labels[6][0]; ?></label>
        <input type="range" class="slider" id="slider7" min="0" max="100" value="0">

        <label class="label" for="slider8"><?php echo $labels[7][0]; ?></label>
        <input type="range" class="slider" id="slider8" min="0" max="100" value="0">

        <label class="label" for="slider9"><?php echo $labels[8][0]; ?></label>
        <input type="range" class="slider" id="slider9" min="0" max="100" value="0">

        <label class="label" for="slider10"><?php echo $labels[9][0]; ?></label>
        <input type="range" class="slider" id="slider10" min="0" max="100" value="0">

    </div>
    </div>
    </div>
<script>
const ranges_instruction = document.querySelectorAll(".slider");

ranges_instruction.forEach(range => {
    range.addEventListener("input", () => {
        const min = range.min;        // The minimum value of the current slider
        const max = range.max;        // The maximum value of the current slider
        const currentVal = range.value; // The current value of the current slider

        // Calculate the percentage of how much the slider has been filled
        const percentage = ((currentVal - min) / (max - min)) * 100;

        // Update the current slider's background size based on the percentage
        range.style.backgroundSize = percentage + "% 100%";
    });
});
</script>
<?php echo (isset($_SESSION['lang']) && $_SESSION['lang'] == "eng") ?
'<p>You are ready to go! Click on the "Start" button to start the experiment.</p>' :
'<p>Sei pronto/a per iniziare l\'esperimento! Clicca su inizia!</p>';
?>
    <form id="form-instruction.php" class="submit-and-proceed" method="post">
        <input type="hidden" name="current_section" value="instruction">
        <button id="startexp" type="submit"></button>
    </form>
    <script>
        const audioPlayerexample = document.getElementById('audioPlayer');
        audioPlayerexample.preload = 'auto';
        const startexampleButton = document.getElementById('startexample');
        const stopexampleButton = document.getElementById('stopexample');
        const startexp = document.getElementById('startexp');

        function playAudio() {
            var audio = document.getElementById("audioPlayer");
            audio.play().catch(error => {
                alert("Unable to play audio. Please ensure your browser allows audio playback.");
                console.error("Audio play error:", error);
            });
            startexp.disabled = false;
            startexp.innerText = "<?php echo ($_SESSION['lang'] == "eng") ? 'Start' : 'Inizia'; ?>";
        }

        function stopAudio() {
            var audio = document.getElementById("audioPlayer");
            audio.pause();
            audio.currentTime = 0;
        }

        [startexampleButton, stopexampleButton, startexp].forEach(item => {
            item.disabled = true;
            item.innerText = "<?php echo $wait_loading_button_text; ?>";
        });
        startexp.innerText = "<?php echo $wait_start_exp_button_text; ?>";

        audioPlayerexample.oncanplaythrough = function () {
            // Enable the start button when the audio is ready to play
            startexampleButton.disabled = false;
            stopexampleButton.disabled = false;
            startexampleButton.innerText = "<?php echo $play_button_text; ?>";
            stopexampleButton.innerText = "<?php echo $stop; ?>";
        };
    </script>
<?php
$htmlContent = ob_get_clean(); // Capture the HTML content
echo json_encode(['success' => true, 'content' => $htmlContent]); // Return the content as JSON
?>

