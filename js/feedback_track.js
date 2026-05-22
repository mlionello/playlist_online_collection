const questionstrackfeedback = {
    ita: {
        questions_track_instruction1 : "Su una scala da 1 (completamente in disaccordo) a 9 (completamente d'accordo):",
        questions_track_instruction2 : "Su una scala da 1 (completamente in disaccordo) a 9 (completamente d'accordo):",
        questions_track1 : [
            "Mi sono sentito immerso nell'ascolto:",
            "È stato facile seguire lo sviluppo della musica:",
            "È stato facile seguire l'intento emotivo della musica:",
        ],
        questions_track2 : [
            "L'intensità delle emozioni positive che l'autore voleva comunicare nel brano musicale era:",
            "L'intensità delle emozioni negative che l'autore voleva comunicare nel brano musicale era:",
        ],
        header1: [
            "Completamente in disaccordo",
            "In disaccordo",
            "Abbastanza in disaccordo",
            "Né d’accordo né in disaccordo",
            "Abbastanza d'accordo",
            "D'accordo",
            "Completamente d'accordo"
        ],
        header2: [
            "Nessuna",
            "Molto lieve",
            "Lieve",
            "Moderata",
            "Abbastanza forte",
            "Forte",
            "Estremamente forte"
        ],
        text_prompt : "Scrivi tutti pensieri, le immagini mentali e i sentimenti che hai provato mentre ascoltavi questo brano:",
    },
    eng: {
        questions_track_instruction1 : "Please rate the following on a scale from 1 (completely disagree) to 9 (completely agree):",
        questions_track_instruction2 : "Please rate the following on a scale from 1 (completely disagree) to 9 (completely agree):",
        questions_track1 : [
            "I felt immersed in the listening:",
            "It was easy to follow the structure of the music:",
            "It was easy to follow the emotional intent of the music:",
        ],
        questions_track2 : [
            "The intensity of positive emotions that the author wanted to communicate was:",
            "The intensity of negative emotions that the author wanted to communicate was:",
        ],
        header1: [
            "Completely Disagree",
            "Strongly Disagree",
            "Disagree",
            "Neither Agree nor Disagree",
            "Agree",
            "Strongly Agree",
            "Completely Agree"
        ],
        header2: [
            "None",
            "Very mild",
            "Mild",
            "Moderate",
            "Fairly Strong",
            "Strong",
            "Extremely strong"
        ],
        text_prompt : "Please write the thoughts, mental images, and feelings that you experienced while listening to this track:"
    }
}

lang = document.getElementById('lang').innerHTML;
var debug = document.getElementById('debugContainer').value;

// Convert the value to a boolean (optional)
debug = (debug === "1");

// Reference to the container
const containerQuestion_track = document.getElementById('likert_question_track');
const questions_track1 = questionstrackfeedback[lang].questions_track1; // Select questions based on the current language
const questions_track2 = questionstrackfeedback[lang].questions_track2; // Select questions based on the current language

containerQuestion_track.innerHTML += generateHeader(questionstrackfeedback[lang].header1); // Add the header
questions_track1.forEach((question, i) => {
    containerQuestion_track.innerHTML += generateQuestionHtml(question, i + 1, "q1_", 7, debug); // Starting from question 9
});
containerQuestion_track.innerHTML += generateHeader(questionstrackfeedback[lang].header2); // Add the header
questions_track2.forEach((question, i) => {
    containerQuestion_track.innerHTML += generateQuestionHtml(question, i + 1, "q2_", 7, debug); // Starting from question 9
});

