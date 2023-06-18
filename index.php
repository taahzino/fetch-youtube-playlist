<?php
require_once 'vendor/autoload.php'; // Path to your autoload file

function fetchPlaylistVideos($playlistId)
{
    $API_KEY = 'YOUR_API_KEY'; // replace with your API key
    $client = new Google_Client();
    $client->setDeveloperKey($API_KEY);

    // Define service object for making API requests
    $service = new Google_Service_YouTube($client);

    // Define the $queryParams
    $queryParams = [
        'maxResults' => 50,
        'playlistId' => $playlistId,
    ];

    $response = $service->playlistItems->listPlaylistItems('snippet,contentDetails', $queryParams);
    $videoDetails = [];

    foreach ($response['items'] as $item) {
        $videoDetail = [];
        $videoDetail['id'] = $item['snippet']['resourceId']['videoId'];
        $videoDetail['title'] = $item['snippet']['title'];
        $videoDetail['thumbnailUrl'] = $item['snippet']['thumbnails']['maxres']['url'];
        $videoDetail['description'] = $item['snippet']['description'];

        // Duration needs another API call
        $videoResponse = $service->videos->listVideos('contentDetails', ['id' => $videoDetail['id']]);
        $videoDetail['duration'] = $videoResponse['items'][0]['contentDetails']['duration'];

        $videoDetails[] = $videoDetail;
    }

    return $videoDetails;
}

$playlistId = 'YOUR_PLAYLIST_ID'; // replace with your playlist ID
$videos = fetchPlaylistVideos($playlistId);

print_r(json_encode($videos));
