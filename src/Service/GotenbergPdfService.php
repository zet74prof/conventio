<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class GotenbergPdfService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $gotenbergUrl
    ) {}

    public function generatePdfFromHtml(string $html): string
    {
        // Gotenberg expects 'index.html' as the file key for the main content
        $formFields = [
            'files' => [
                'index.html' => new DataPart($html, 'index.html', 'text/html'),
            ],
            // Optional: Adjust margins (standard: 1 inch ~ 0.39)
            'marginTop' => '0.4',
            'marginBottom' => '0.4',
            'marginLeft' => '0.4',
            'marginRight' => '0.4',
            'paperWidth' => '8.27',  // A4
            'paperHeight' => '11.7', // A4
        ];

        $formData = new FormDataPart($formFields);
        $headers = $formData->getPreparedHeaders()->toArray();

        $response = $this->client->request(
            'POST',
            $this->gotenbergUrl . '/forms/chromium/convert/html',
            [
                'headers' => $headers,
                'body' => $formData->bodyToIterable(),
            ]
        );

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Error generating PDF: ' . $response->getContent(false));
        }

        return $response->getContent();
    }
}
