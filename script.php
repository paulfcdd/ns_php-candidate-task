<?php

$base_url = "http://api.citybik.es";
$city = $argv[1];

$n_content = file_get_contents($base_url . '/v2/networks');
$response = parse_response($n_content);

$hrefs = array();

foreach ($response["networks"] as $network) {
    if ($network["location"]["city"] == $city)
        $hrefs[] = $network["href"];
}

$station_info = array();
foreach ($hrefs as $href) {
    $b_content = file_get_contents($base_url . $href);
    $response = parse_response($b_content);

    foreach ($response["network"]["stations"] as $stat) {
        array_push($station_info, array(
            "name" => $stat["name"],
            "latitude" => $stat["latitude"],
            "longitude" => $stat["longitude"],
            "free_bikes" => $stat["free_bikes"]
        ));
    }
}
$bikers_data = explode("\n", file_get_contents("bikers.csv"));
$bikers = array();
for ($i = 0; $i < count($bikers_data); $i++) {
    if ($i == 0)
        continue;
    else {
        $biker_info = explode(',', $bikers_data[$i]);
        array_push($bikers, array(
            "count" => $biker_info[0],
            "latitude" => $biker_info[1],
            "longitude" => $biker_info[2],
        ));
    }
}

$shortest_distances = array();

foreach ($bikers as $biker) {
    $shortest_distance = 9999999999999999;
    $closest_station_name = '';
    $free_bike_count = 0;
    $biker_count = 0;
    foreach ($station_info as $station) {
        $distance = getDistance($station["latitude"], $station["longitude"], $biker["latitude"], $biker["longitude"]);
        if ($distance < $shortest_distance) {
            $shortest_distance = $distance;
            $closest_station_name = $station["name"];
            $free_bike_count = $station["free_bikes"];
            $biker_count = $biker["count"];
        }
    }
    $shortest_distances[] = [
        "name" => $closest_station_name,
        "distance" => $shortest_distance,
        "free_bike_count" => $free_bike_count,
        "biker_count" => $biker_count
    ];
}

foreach ($shortest_distances as $shortest_distance) {
    echo "distance: " . $shortest_distance["distance"] . PHP_EOL;
    echo "name: " . $shortest_distance["name"] . PHP_EOL;
    echo "free_bike_count: " . $shortest_distance["free_bike_count"] . PHP_EOL;
    echo "biker_count: " . $shortest_distance["biker_count"] . PHP_EOL;
    echo PHP_EOL;
}

function parse_response($response)
{
    mb_internal_encoding("UTF-8");
    $i = 0;
    $n = strlen($response);
    try {
        $result = response_decode_value($response, $i);
        while ($i < $n && $response[$i] && $response[$i] <= ' ') $i++;
        if ($i < $n) {
            return null;
        }
        return $result;
    } catch (Exception $e) {
        return null;
    }
}

function response_decode_value($decode_value, &$i)
{
    $n = strlen($decode_value);
    while ($i < $n && $decode_value[$i] && $decode_value[$i] <= ' ') $i++;

    switch ($decode_value[$i]) {
        // object
        case '{':
            $i++;
            $result = array();
            while ($i < $n && $decode_value[$i] && $decode_value[$i] <= ' ') $i++;
            if ($decode_value[$i] === '}') {
                $i++;
                return $result;
            }
            while ($i < $n) {
                $key = response_decode_string($decode_value, $i);
                while ($i < $n && $decode_value[$i] && $decode_value[$i] <= ' ') $i++;
                if ($decode_value[$i++] != ':') {
                    throw new Exception("Expected ':' on ".($i - 1));
                }
                $result[$key] = response_decode_value($decode_value, $i);
                while ($i < $n && $decode_value[$i] && $decode_value[$i] <= ' ') $i++;
                if ($decode_value[$i] === '}') {
                    $i++;
                    return $result;
                }
                if ($decode_value[$i++] != ',') {
                    throw new Exception("Expected ',' on ".($i - 1));
                }
                while ($i < $n && $decode_value[$i] && $decode_value[$i] <= ' ') $i++;
            }
            throw new Exception("Syntax error");
        // array
        case '[':
            $i++;
            $result = array();
            while ($i < $n && $decode_value[$i] && $decode_value[$i] <= ' ') $i++;
            if ($decode_value[$i] === ']') {
                $i++;
                return array();
            }
            while ($i < $n) {
                $result[] = response_decode_value($decode_value, $i);
                while ($i < $n && $decode_value[$i] && $decode_value[$i] <= ' ') $i++;
                if ($decode_value[$i] === ']') {
                    $i++;
                    return $result;
                }
                if ($decode_value[$i++] != ',') {
                    throw new Exception("Expected ',' on ".($i - 1));
                }
                while ($i < $n && $decode_value[$i] && $decode_value[$i] <= ' ') $i++;
            }
            throw new Exception("Syntax error");
        // string
        case '"':
            return response_decode_string($decode_value, $i);
        // number
        case '-':
            return response_decode_number($decode_value, $i);
        // true
        case 't':
            if ($i + 3 < $n && substr($decode_value, $i, 4) === 'true') {
                $i += 4;
                return true;
            }
        // false
        case 'f':
            if ($i + 4 < $n && substr($decode_value, $i, 5) === 'false') {
                $i += 5;
                return false;
            }
        // null
        case 'n':
            if ($i + 3 < $n && substr($decode_value, $i, 4) === 'null') {
                $i += 4;
                return null;
            }
        default:
            // number
            if ($decode_value[$i] >= '0' && $decode_value[$i] <= '9') {
                return response_decode_number($decode_value, $i);
            } else {
                throw new Exception("Syntax error");
            };
    }
}

function response_decode_string($string, &$i)
{
    $result = '';
    $escape = array('"' => '"', '\\' => '\\', '/' => '/', 'b' => "\b", 'f' => "\f", 'n' => "\n", 'r' => "\r", 't' => "\t");
    $n = strlen($string);
    if ($string[$i] === '"') {
        while (++$i < $n) {
            if ($string[$i] === '"') {
                $i++;
                return $result;
            } elseif ($string[$i] === '\\') {
                $i++;
                if ($string[$i] === 'u') {
                    $code = "&#".hexdec(substr($string, $i + 1, 4)).";";
                    $convmap = array(0x80, 0xFFFF, 0, 0xFFFF);
                    $result .= mb_decode_numericentity($code, $convmap, 'UTF-8');
                    $i += 4;
                } elseif (isset($escape[$string[$i]])) {
                    $result .= $escape[$string[$i]];
                } else {
                    break;
                }
            } else {
                $result .= $string[$i];
            }
        }
    }
    throw new Exception("Syntax error");
}

function response_decode_number($number, &$i)
{
    $result = '';
    if ($number[$i] === '-') {
        $result = '-';
        $i++;
    }
    $n = strlen($number);
    while ($i < $n && $number[$i] >= '0' && $number[$i] <= '9') {
        $result .= $number[$i++];
    }

    if ($i < $n && $number[$i] === '.') {
        $result .= '.';
        $i++;
        while ($i < $n && $number[$i] >= '0' && $number[$i] <= '9') {
            $result .= $number[$i++];
        }
    }
    if ($i < $n && ($number[$i] === 'e' || $number[$i] === 'E')) {
        $result .= $number[$i];
        $i++;
        if ($number[$i] === '-' || $number[$i] === '+') {
            $result .= $number[$i++];
        }
        while ($i < $n && $number[$i] >= '0' && $number[$i] <= '9') {
            $result .= $number[$i++];
        }
    }

    return (0 + $result);
}

function getDistance($latitude1, $longitude1, $latitude2, $longitude2) {
    $earth_radius = 6371;

    $dLat = deg2rad($latitude2 - $latitude1);
    $dLon = deg2rad($longitude2 - $longitude1);

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    $d = $earth_radius * $c;

    return $d;
}