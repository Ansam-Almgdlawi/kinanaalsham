<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;

class FcmService
{
    protected $client;
    protected $projectId;
    protected $serviceAccountPath;

    public function __construct()
    {
        $this->client = new Client();
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $this->serviceAccountPath = base_path(env('FIREBASE_SERVICE_ACCOUNT_PATH'));

        if (!file_exists($this->serviceAccountPath)) {
            throw new \Exception('Firebase service account file not found at: ' . $this->serviceAccountPath);
        }
    }

    /**
     * Get OAuth2 access token using service account
     */
    protected function getAccessToken(): string
    {
        $guzzleClient = new Client(['verify' => false]);

        $httpHandler = function (RequestInterface $request, array $options = [] ) use ($guzzleClient) {
            return $guzzleClient->send($request, $options);
        };

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        $credentials = new ServiceAccountCredentials($scopes, $this->serviceAccountPath );

        $token = $credentials->fetchAuthToken($httpHandler );

        return $token['access_token'];
    }

    /**
     * Send notification via Firebase
     *
     * @param string $target device token OR topic name
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string $type "token" | "topic"
     */
    public function sendNotification(string $target, string $title, string $body, array $data = [], string $type = 'token'): ?string
    {
        try {
            $accessToken = $this->getAccessToken();
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

            $message = [
                'message' => [
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $data,
                ],
            ];

            if ($type === 'token') {
                $message['message']['token'] = $target;
            } elseif ($type === 'topic') {
                $message['message']['topic'] = $target;
            } else {
                throw new \InvalidArgumentException("Invalid target type. Must be 'token' or 'topic'.");
            }

            $response = $this->client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ],
                'json' => $message,
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            Log::info('FCM Send Response:', $responseBody);

            return $responseBody['name'] ?? null; // FCM message ID
        } catch (RequestException $e) {
            Log::error('FCM Request Error:', [
                'message'  => $e->getMessage(),
                'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response',
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('FCM General Error:', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
