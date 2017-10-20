<?php
/**
 * Created by PhpStorm.
 * User: mmestre
 * Date: 4/28/17
 * Time: 10:09 AM
 */

/******** CANVAS UTIL FUNCTIONS ********/
/*
 * Small collection of scripts (that don't require curl) to interface with the Canvas LMS REST API in PHP.
 * They could easily be used for other REST API as well as long as you set up the correct authentication.
 *
 * The cGet script has specific logic to deal with the 100 item return limit that the Canvas API sets.
 *
 */

namespace App\Util\CanvasAPIFunctions;

// GET requests
function cGet ($url) {
    $options = [
        "http" => [
            "method" => "GET",
            "header" => [
                "Authorization: Bearer " . TOKEN,
                "Content-Type: application/json"
            ],
            'content' => null
        ]
    ];
    return canvasGet($url, $options, 1, array());
}
    function canvasGet($url, $options, $page, array $items) {
        $url = addQueryParameter($url, 'per_page=100&page=' . $page);

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, 1);

        if ($response && is_array($data) && count($data) === 100) {
            return canvasGet($url, $options, $page + 1, array_merge($data, $items));
        }
        return array_merge($data, $items);
    }
    function addQueryParameter($url, $param) {
        return $url . (strpos($url, '?') ? '&' : '?') . $param;
    }

// POST requests
function cPost($url, array $postdata) {
    $postdata = http_build_query($postdata);

    $options = array('http' =>
        array (
            'method' => 'POST',
            "header" => [
                "Authorization: Bearer " . TOKEN,
                "Content-type: application/x-www-form-urlencoded"
            ],
            'content' => $postdata
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}

// DELETE requests
function cDel($url) {

    $options = array('http' =>
        array (
            'method' => 'DELETE',
            "header" => [
                "Authorization: Bearer " . TOKEN,
                "Content-type: application/json"
            ],
            'content' => null
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return $result;
}


/******** CANVAS UTIL FUNCTIONS ********/

// check users role in a course
function checkRole($user_id, $course_id) {
    $enrollments = $cGet('https://canvas.brown.edu/api/v1/users/' . $user_id . '/enrollments');
    foreach ($enrollments as $key => $val) {
        $course_roles[$val['course_id']] = $val['role'];
    }
    if (array_key_exists($course_id, $course_roles)) {
        return $course_roles[$course_id];
    }
}