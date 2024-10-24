<?php
namespace App\Service;

class CommentFilterService
{
    public function filterProhibitedContent(string $content): string
    {
        $patterns = [
            '/\b\d{10}\b/', // Numéros de téléphone à 10 chiffres
            '/\b\d{9}\b/',  // Numéros de téléphone à 9 chiffres
            '/\b\d{3}[-\.\s]?\d{3}[-\.\s]?\d{4}\b/', // Formats téléphoniques communs
            '/\b\w{5,}\d{1,5}\b/', // Mot de passe simple (lettres suivies de chiffres)
            '/\b(pd|pute|fils de pute|connard|con)\b/i' // Mots interdits (grossièretés, par exemple)
        ];

        foreach ($patterns as $pattern) {
            $content = preg_replace($pattern, '❤️', $content);
        }

        return $content;
    }
}
