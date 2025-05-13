<?php
http_response_code(404);
echo <<<JSON
    {
        "code": 404,
        "message": "not found"
    }
JSON;
?>