<?php

function isMobile() {
    return preg_match('/(android|iphone|ipad|ipod|mobile|blackberry|iemobile|opera mini|windows phone|webos|palm|symbian)/i', $_SERVER['HTTP_USER_AGENT']);
}

if (isMobile()) {
    echo "<p style='text-align: center; font-size: 1.2em; color: red;'>This site is not supported on mobile devices. Please visit on a desktop for the best experience.</p>";
    exit; // Stops further page rendering if on mobile
}

session_start();

$_SESSION['debug'] = false;
if (isset($_GET['debug'])) {
    $_SESSION['debug'] = filter_var($_GET['debug'], FILTER_VALIDATE_BOOLEAN);
}

if ($_SESSION['debug']) {
session_unset();
$_SESSION['debug'] = true;
}

$_SESSION['current_date'] = date('Y-m-d H:i:s');

// Generate a new user ID and store it in the session if not already generated
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = uniqid('user_', true);
}

if (isset($_GET['group'])) {
$_SESSION['audio_group'] = $_GET['group'];
}
if (!isset($_SESSION['audio_group'])) {
    $file_path = './responses/audio_group.txt';
    $group_counts = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
    
    // Read the file and count occurrences of each group
    if (file_exists($file_path)) {
        $file_contents = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($file_contents as $line) {
            if (preg_match('/Audio Group: (\d)/', $line, $matches)) {
                $group = (int)$matches[1];
                if (isset($group_counts[$group])) {
                    $group_counts[$group]++;
                }
            }
        }
    }

    // Find the group with the least occurrences
    $_SESSION['audio_group'] = array_keys($group_counts, min($group_counts))[0];
    //$_SESSION['audio_group'] = rand(0,3);
}

// assign to the user the descriptors categories
if (!isset($_SESSION['step'])) {
    $_SESSION['step'] = "welcome"; // if >demographics loadNextSection($_SESSION['descriptions_class'])
}

if (isset($_GET['lang'])) {
    // Set the session variable to the value of the 'lang' parameter
    $_SESSION['lang'] = $_GET['lang'];
}

// assign to the user the descriptors categories
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = "ita"; // if >demographics loadNextSection($_SESSION['descriptions_class'])
}

if (isset($_SESSION['lang']) && $_SESSION['lang'] == "ita") {
$_SESSION['random_labels'] = array_map('str_getcsv', file('./data/slider_labels_ita.csv'));
} else {
$_SESSION['random_labels'] = array_map('str_getcsv', file('./data/slider_labels.csv'));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>A Music Listening Experiment</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <div id="lang" style="display:none;"><?php echo $_SESSION['lang']; ?></div>
    <div id="content-section">
        <?php
	if (isset($_SESSION['debug']) && $_SESSION['debug']) {
	echo "audio group: " . $_SESSION['audio_group'] . "<br>";
	echo "labels: ";
	foreach ($_SESSION['random_labels'] as $label) { echo $label[0] . " "; };
	echo "<br>user_id: " . $_SESSION['user_id']; 
}
        if (isset($_SESSION['lang']) && $_SESSION['lang'] == "ita") {
        echo '<h1>Benvenuto/a!</h1>
        <p>Prima di procedere, ti preghiamo di leggere attentamente le seguenti istruzioni.</p>
        <p>
            In questo esperimento, ti verrà chiesto di ascoltare musica sinfonica e valutare diversi fattori.
            L\'intera procedura richiederà circa 30 minuti del tuo tempo e ti chiediamo di completarla
            in un\'unica sessione ininterrotta evitando possibili distrazioni e interruzioni.
            Le cuffie sono essenziali per questo esperimento; le risposte raccolte utilizzando
            sistemi diversi dalle cuffie non saranno incluse nella nostra analisi.
        </p>
        <p>
            Durante l\'esperimento, ti verranno poste delle domande a scelta multipla relative ad alcune
            informazioni demografiche, al tuo umore e alla tua esperienza in ambito musicale. Una volta terminate queste domande riceverai altre informazioni
            e potrai procedere all\'ascolto dei brani.
        </p>
        <p>
            L\'ascolto consiste in 8 traccie audio che spaziano su diversi generi musicali. Durante l\'ascolto di ciascun movimento
            ti verranno mostrati dei cursori, ciascuno in riferimento a un particolare aspetto dell\'esperienza di ascolto.
            Ti verrà richiesto di interagire con i cursori ogni volta che percepirai un cambiamento nella musica.
            Al termine di ciascun movimento ti verranno poste delle domande su quanto appena ascoltato e sulla tua esperienza.
        </p>
        <p>
            I dati saranno elaborati e pubblicati in forma anonima, in conformità con le normative e non sarà possibile in alcun modo risalire alla tua identità tramite le risposte che fornirai.
        </p>
        ';
        } else {
        echo '<h1>Welcome!</h1>
        <p>Before proceeding, please read the following information carefully.</p>
        <p>
            In this experiment, you will be asked to listen to symphonic music and evaluate several factors.
            The entire experiment will take about 40 minutes. As a participant, you are asked to complete the experiment
            in a single uninterrupted session. Before starting, please make sure you can dedicate the next 40 minutes to
            this task without distractions or possible interruptions. To avoid interruptions, it is recommended that you set your phone
            to airplane mode. Headphones are essential for this experiment; responses collected using
            external speakers or laptops will not be included in our analysis.
        </p>
        <p>
            During the experiment, you will be asked multiple-choice questions regarding demographic sampling,
            your mood, and your musical experience. At the end of these questionnaires,
            the listening instructions will be repeated, and you will be provided with a 10-second audio sample to adjust the volume to your preference.
        </p>
        <p>The listening phase consists of an entire symphonic piece divided into 4 movements. During each movement,
            you will be presented with 4 sliders, each referring to a particular aspect of the listening experience.
            You will be asked to interact with the sliders whenever you perceive a change in your listening experience.
            Each of the 4 movements will be preceded by a brief description that you should read carefully,
            and at the end of each movement, you will be asked questions about to what you just listened.
            The experiment concludes with a short series of questions about the overall listening experience.
        </p>
        <p>
            The data will be processed and published anonymously, in compliance with XXXX regulations.
        </p>
        ';
        }
        ?>
        <form class="submit-and-proceed" method="post">
            <input type="hidden" name="current_section" value="welcome">
            <button type="submit"><?php echo ($_SESSION['lang'] == "eng") ? 'Next' : 'Avanti' ?></button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        function loadNextSection(current_section) {
            $.ajax({
                url: 'php/load_section.php',
                type: 'POST',
                data: {section: current_section},
                success: function(response) {
                    $('#content-section').html(response.content);
                window.scrollTo({
                    top: 0,
                });
                },
                error: function() {
                    alert('Failed to load the next section.');
                }
            });
        }

        $(document).on('submit', 'form.submit-and-proceed', function(event) {
            event.preventDefault();

            let formData = $(this).serializeArray();
            let currentSection = formData.find(item => item.name === 'current_section').value;

            if (currentSection == 'demographics') {
                // Check if either psychiatric_diagnosis or psychiatric_treatment is 'yes'
                let psychiatricDiagnosis = formData.find(item => item.name === 'psychiatric_diagnosis')?.value.toLowerCase();
                let psychiatricTreatment = formData.find(item => item.name === 'psychiatric_treatment')?.value.toLowerCase();

                if (psychiatricDiagnosis === 'yes' || psychiatricTreatment === 'yes') {
                    $.ajax({
                        url: 'php/restore_section.php',
                        type: 'POST',
                        data: {section: 'cannotproceed'},
                        success: function(response) {
                            $('#content-section').html(response.content);
                        },
                        error: function() {
                            alert('Failed to load the next section.');
                        }
                    });
                    return; // Prevent further processing
                }

            }

            console.log('Submitting:', formData); // Debugging line to check the form data being sent
            console.log('Submitting:', currentSection); // Debugging line to check the form data being sent

            $.ajax({
                url: 'php/save_response.php',
                type: 'POST',
                data: formData,
                success: function() {
                    console.log('Data saved successfully.');
                    loadNextSection(currentSection);
                },
                error: function() {
                    console.log('Error saving data.');
                }
            });

        });
        let restore_section = "<?php echo $_SESSION['step']; ?>";
        if ( restore_section !== 'welcome' && restore_section !== 'terms' && restore_section !== 'demographics') {
                $.ajax({
                    url: 'php/restore_section.php',
                    type: 'POST',
                    data: {section: restore_section},
                    success: function(response) {
                        $('#content-section').html(response.content);
                    },
                    error: function() {
                        alert('Failed to load the next section.');
                    }
                });
        }
    });
    </script>
</body>
</html>
