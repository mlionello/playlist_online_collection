// Define variables and functions as properties of the window object
window.sliderData = [];
window.startTime;
window.intervalId;
window.currentTrack = 0;
window.currentTrack = JSON.parse(document.getElementById('tracks_index').textContent); // load track index to restore session

// Attach tracks to the window object for global access
window.tracks = JSON.parse(document.getElementById('tracksData').textContent); // Load tracks from a hidden element

console.log(window.tracks);

// Define functions as global by attaching them to window
window.loadTrack = function (trackIndex) {
    if (trackIndex < window.tracks.length) {
        waiting_text = document.getElementById('wait_loading_button_text').innerText;
        play_button_text = document.getElementById('play_button_text').innerText;
        progress_track_text = document.getElementById('track_text').innerText;

        const track = window.tracks[trackIndex];
        window.readingDurationStartTime = Date.now(); // Start the timer for reading duration
        const audioPlayer = document.getElementById('audioPlayer');
        console.log(encodeURI(track.file));
        audioPlayer.src = encodeURI(track.file); // Ensure URI encoding
        audioPlayer.preload = 'auto';

        const startButton = document.getElementById('startsong');
        startButton.disabled = true;
        startButton.innerText = waiting_text;

        // Reset sliders to 0 and disable them
        const sliders = document.querySelectorAll('.slider');
        sliders.forEach(slider => {
            slider.value = 0;
            slider.disabled = true;
            slider.style.backgroundSize = "0% 100%";
        });

        audioPlayer.onerror = function () {
            console.error("Failed to load audio: ", track.file);
            alert("Failed to load audio. Please check the file path or try again later.");
        };

        audioPlayer.oncanplaythrough = function () {
            // Enable the start button when the audio is ready to play
            startButton.disabled = false;
            startButton.innerText = play_button_text;
        };

        // Update track counter
        document.getElementById('trackCounter').innerText = `${progress_track_text} ${trackIndex + 1}/${window.tracks.length}`;

        // Show description overlay
        window.readingDuration = Date.now() - window.readingDurationStartTime; // Capture reading duration
        window.startingDurationStartTime = Date.now(); // Start the timer for starting duration
        window.sliderData = []; // Reset slider data for the new track
    } else {
        submitbutton = document.getElementById('finish_experiment');
        submitbutton.click();
    }
};

window.startExperiment = function () {
    const startButton = document.getElementById('startsong');
    window.startingDuration = Date.now() - window.startingDurationStartTime; // Capture reading duration
    window.listeningDurationStartTime = Date.now(); // Start the timer for listening duration
    startButton.style.visibility = "hidden";
    window.startTime = Date.now();
    const audio = document.getElementById('audioPlayer');
    audio.play().catch(error => {
        console.error("Error playing audio:", error);
    });

    // Enable sliders when the song starts
    const sliders = document.querySelectorAll('.slider');
    sliders.forEach(slider => {
        slider.disabled = false;
    });

    // Store the initial "101" value at the start
    let initialValues = Array.from(sliders).map(slider => 101);
    initialValues.unshift(0); // Timestamp 0
    window.sliderData.push(initialValues);

    window.intervalId = setInterval(() => {
        const timestamp = Date.now() - window.startTime;
        let values = Array.from(sliders).map(slider => slider.value);
        values.unshift(timestamp);
        window.sliderData.push(values);
    }, 100); // 10 times per second

    audio.onended = function () {
        window.listeningDuration = Date.now() - window.listeningDurationStartTime; // Capture listening duration
        clearInterval(window.intervalId);
        window.saveData();
        clearQuestionnaireForm(); // Clear the form before showing the overlay
        document.getElementById('questionnaireOverlay').style.display = 'flex';
        document.getElementById('questionnaireOverlay').scrollTo({top: 0});
        window.questionnaireDurationStartTime = Date.now(); // Start the timer for questionnaire duration
        };
};

// Function to clear the questionnaire form
window.clearQuestionnaireForm = function() {
    const form = document.getElementById('questionnaireForm');

    // Reset all radio buttons
    const radioButtons = form.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.checked = false; // Uncheck each radio button
    });

    // Clear the textarea
    const feedbackTextarea = document.getElementById('feedback');
    if (feedbackTextarea) {
        feedbackTextarea.value = ''; // Clear the textarea
    }
}

window.submitQuestionnaire = function (event) {
    window.questionnaireDuration = Date.now() - window.questionnaireDurationStartTime; // Capture questionnaire duration
    event.preventDefault();

    // Serialize the form data and add the current track as 'current_section'
    let formData = $(event.target).serializeArray(); // Serialize form data

    // Append currentTrack as 'current_section' to the formData array
    formData.push({ name: 'current_section', value: 'track_' + (window.currentTrack + 1) });
    formData.push({ name: 'track_index_next', value: (window.currentTrack + 1) });
    formData.push({ name: 'reading_duration', value: window.readingDuration }); // Send reading duration
    formData.push({ name: 'questionnaire_duration', value: window.questionnaireDuration }); // Send questionnaire duration
    formData.push({ name: 'listening_duration', value: window.listeningDuration }); // Send listening duration
    formData.push({ name: 'starting_duration', value: window.startingDuration }); // Send listening duration

    // Send the data to save_response.php using an AJAX POST request
    $.ajax({
        url: '../php/save_response.php',
        type: 'POST',
        data: formData,
        success: function (response) {
            console.log('Form data saved successfully:', response);
        },
        error: function (xhr, status, error) {
            console.error('Error saving form data:', error);
        }
    });

    // Hide the questionnaire overlay and proceed to the next track
    document.getElementById('questionnaireOverlay').style.display = 'none';
    const startButton = document.getElementById('startsong');
    startButton.style.visibility = "visible";
    window.currentTrack++;
    window.loadTrack(window.currentTrack);
};

window.saveData = function () {
    // Create the data object that includes sliderData and currentTrack
    const dataToSend = {
        sliderData: window.sliderData,
        currentTrack: window.currentTrack + 1
    };

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../php/save_experiment_data.php", true);
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log('Data saved successfully.');
        } else {
            console.error('Error saving data.');
        }
    };
    xhr.onerror = function () {
        console.error('Network error.');
    };
    // Send the combined data object as JSON
    xhr.send(JSON.stringify(dataToSend));
};

