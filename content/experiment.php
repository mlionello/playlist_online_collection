<?php

header('Content-Type: application/json');
$debug = $_SESSION['debug'];

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Session expired or not set. Please restart the experiment.']);
    http_response_code(401); // Unauthorized
    exit;
}

$_SESSION['step'] = 'experiment';

if (!isset($_SESSION['track_index'])) {
    $_SESSION['track_index']=0;
}

// Ensure the random words are available in the session
if (!isset($_SESSION['random_labels']) || count($_SESSION['random_labels']) < 4) {
    echo json_encode(['error' => 'Required labels are missing. Please restart the experiment.']);
    http_response_code(500); // Internal Server Error
    exit;
}

// Ensure the random words are available in the session
$wait_loading_button_text = 'Please wait, loading song...';
$play_button_text = "Start";
$track_text = "Track";
$I_have_read_it = "I have read it";
$track_feedback = 'Track Feedback:';
$submit_button = 'Next';

if (isset($_SESSION['lang']) && $_SESSION['lang'] == "ita") {
    $wait_loading_button_text = "Caricamento audio, attendere...";
    $play_button_text = "Inizia";
    $track_text = "Traccia";
    $I_have_read_it = "Ho letto";
    $track_feedback = 'Feedback:';
    $submit_button = 'Continua';
}

// Assign the random labels to the sliders
$labels = $_SESSION['random_labels'];

require __DIR__ . '/../php/load_csv.php';
$debug = $_SESSION['debug'];

$start_index = $_SESSION['audio_group'] * 8;
$end_index = $_SESSION['audio_group'] * 8 + 7;
$indices = range($start_index, $end_index);
if ($debug) {
$indices=[32,32];
}
$audioPaths = loadCSV('../data/audio_paths.csv', $indices);

// Combine audio paths and descriptions based on track number
$tracks = [];
foreach ($audioPaths as $pathIndex => $path) {
    error_log($path['path']);
    $tracks[] = [
        'file' => $path['path'],
    ];
}

// Prepare HTML content to be returned
ob_start(); // Start output buffering
?>
<input type="hidden" id="debugContainer" value="<?php echo (bool)$debug; ?>">
<script src="../js/questionnaire_header_radios.js"></script>
<div id="wait_loading_button_text" style="display:none;"><?php echo $wait_loading_button_text; ?></div>
<div id="play_button_text" style="display:none;"><?php echo $play_button_text; ?></div>
<div id="track_text" style="display:none;"><?php echo $track_text; ?></div>

<div id="tracksData" style="display:none;"><?php echo json_encode($tracks); ?></div>
<div id="tracks_index" style="display:none;"><?php echo $_SESSION['track_index']; ?></div>

<div class="overlay" id="questionnaireOverlay" style="display: none;">
    <h2><?php echo $track_feedback; ?></h2>
    <form id="questionnaireForm" onsubmit="submitQuestionnaire(event)">
        <div class="likert-question" id="likert_question_track">
        </div><br>
        <button type="submit"><?php echo $submit_button; ?></button>
    </form>
</div>

<div class="experiment-container">
    <div class="sliders-container">
        <button id="startsong" onclick="startExperiment()" style="margin-bottom: 40px;"><?php echo $play_button_text; ?></button><br>
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
    <script>
    const ranges_exp = document.querySelectorAll(".slider");

    ranges_exp.forEach(range => {
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

    <audio id="audioPlayer" src=""></audio>
    <div id="trackCounter" class="track-counter"><?php echo $track_text; ?> 1/<?php echo count($tracks); ?></div>
</div>
<div style="display:none">
    <form id="form-instruction.php" class="submit-and-proceed" method="post">
        <input type="hidden" name="current_section" value="experiment">
        <button id="finish_experiment" type="submit"></button>
    </form>
</div>
<script>
        var debug = <?php echo json_encode((bool)$debug); ?>;
</script>
<script src="../js/feedback_track.js"></script>
<script src="../js/experiment.js"></script>
<script>
    loadTrack(window.currentTrack);
    </script>

<?php
$htmlContent = ob_get_clean(); // Capture the HTML content
echo json_encode(['success' => true, 'content' => $htmlContent]); // Return the content as JSON
?>
