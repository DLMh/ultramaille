<?php 


// Fonction pour nettoyer la couleur en supprimant les mots superflus
function normalizeColor($color) {
    // Convertir en minuscule et supprimer tout sauf les lettres et espaces
    $color = strtolower($color);
    $color = preg_replace('/[^a-z\s]+/', '', $color);

    // Diviser en mots
    $words = explode(' ', trim($color));

    // Filtrer les mots courts ou communs qui ne représentent généralement pas une couleur
    $cleanWords = array_filter($words, function($word) {
        // Ignorer les mots de liaison ou très courts (comme "de", "d'", "le", "la", "na1", etc.)
        return strlen($word) > 2;  // Les mots de 2 lettres ou moins sont souvent des mots de liaison ou préfixes
    });

    // Retourner le premier mot significatif (probablement la couleur principale) ou la couleur originale
    return count($cleanWords) > 0 ? reset($cleanWords) : $color;
}

// Fonction pour vérifier si deux couleurs sont similaires
function areColorsSimilar($color1, $color2, $threshold = 70) {
    // Nettoyer les couleurs en supprimant les éléments non pertinents
    $cleanedColor1 = normalizeColor($color1);
    $cleanedColor2 = normalizeColor($color2);

    // Calculer la similarité entre les deux couleurs nettoyées
    similar_text($cleanedColor1, $cleanedColor2, $similarity);
    
    // Retourner vrai si la similarité dépasse le seuil
    return $similarity >= $threshold;
}

// Fonction de normalisation pour les tailles (inchangée)
function normalizeSize($size) {
    return strtolower(trim($size)); // Convertir en minuscule et supprimer les espaces en trop
}

// function normalizeColor($color) {
//     $color = strtolower($color); // Convertir en minuscule
//     $color = preg_replace('/[^a-z\s]+/', '', $color); // Supprimer tout sauf les lettres et espaces
//     $words = explode(' ', trim($color)); // Diviser la couleur en mots
//     return $words[0]; // Retourner le premier mot, souvent la couleur principale
// }

// // Fonction pour vérifier si deux couleurs sont similaires
// function areColorsSimilar($color1, $color2, $threshold = 70) {
//     similar_text($color1, $color2, $similarity);
//     return $similarity >= $threshold;
// }

// // Fonction de normalisation pour les tailles
// function normalizeSize($size) {
//     return strtolower(trim($size)); // Convertir en minuscule et supprimer les espaces en trop
// }
?>