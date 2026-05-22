// Define questions and header options in both Italian and English
const final_questions = {
    ita: {
        questions: [
            "Quanto ti è piaciuta la musica?",
            "Quanto ti sei sentito coinvolto emotivamente durante l'ascolto complessivo?",
            "Quanto e' stato difficile interpretare la scala di valori dei cursori?",
            "Quanto e' stato difficile interpretare il significato delle etichette dei cursori?",
            "Quanto e' stato difficile associare le etichette dei cursori all'ascolto?",
            "Quanto e' stato difficile l'utilizzo di molteplici cursori?",
            "Quanto e' stato difficile in generale gestire i cursori?",
        ],
        header: [
            "Per niente", "Molto poco", "Poco", "Moderatamente", "Abbastanza", "Molto", "Estremamente",
        ]
    },
    eng: {
        questions: [
            "How much did you enjoy the music?",
            "How much did you feel emotionally involved during the entire listening?"
        ],
        header: [
            "Not at all", "Very mild", "Mild", "Moderately", "Fairly enough", "Very much", "Extremely",
        ]
    }
};

lang = document.getElementById('lang').innerHTML;
var debug = document.getElementById('debugContainer').value;

// Convert the value to a boolean (optional)
debug = (debug === "1");
// Reference to the containers
const containerQuestion_final = document.getElementById('post_experiment_setquestion1');
// Reference to the container
const final_questions_items = final_questions[lang].questions; // Select questions based on the current language

containerQuestion_final.innerHTML += generateHeader(final_questions[lang].header); // Add the header
final_questions_items.forEach((question, i) => {
    containerQuestion_final.innerHTML += generateQuestionHtml(question, i + 1, 'q', 7, debug);
});
