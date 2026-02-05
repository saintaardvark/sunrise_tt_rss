<?php

// Mock Plugin class for standalone testing
class Plugin
{
}

require_once __DIR__ . "/init.php";

$plugin = new Vancouver_Sunrise();

function test($name, $assertion)
{
    if ($assertion) {
        echo "✅ PASS: $name\n";
    } else {
        echo "❌ FAIL: $name\n";
        exit(1);
    }
}

echo "Running Vancouver Sunrise Plugin Tests...\n";

// Test 1: Standard Time (Feb 4, 2026)
// CSV: Feb  4 2026,6:27,7:05,7:39,12:26,17:14,17:48,18:26
$ts_feb = strtotime("2026-02-04");
test("Is not DST in Feb", !$plugin->is_dst($ts_feb));
$info_feb = $plugin->get_daylight_info($ts_feb);
test("Feb 4 Civil Twilight Start matches", $info_feb['civil_twilight_start'] == "07:05");
test("Feb 4 Sunrise matches", $info_feb['sunrise'] == "07:39");
test("Feb 4 Sunset matches", $info_feb['sunset'] == "17:14");

// Test 2: DST Time (July 1, 2026)
// CSV: Jul  1 2026,2:28,3:29,4:11,12:16,20:21,21:04,22:05
// DST offset: +1h
$ts_jul = strtotime("2026-07-01");
test("Is DST in July", $plugin->is_dst($ts_jul));
$info_jul = $plugin->get_daylight_info($ts_jul);
test("July 1 Civil Twilight Start (DST) matches (+1h)", $info_jul['civil_twilight_start'] == "04:29");
test("July 1 Sunrise (DST) matches (+1h)", $info_jul['sunrise'] == "05:11");
test("July 1 Sunset (DST) matches (+1h)", $info_jul['sunset'] == "21:21");

// Test 3: Total Daylight Calculation
// Feb 4: 17:14 - 07:39 = 9h 35m
test("Feb 4 Total Daylight matches", $info_feb['total_daylight'] == "9h 35m");

// Test 4: Daylight Change
// CSV Feb 3: 7:40 to 17:13 = 9h 33m
// Change: 2 mins
test("Feb 4 Daylight Change matches", $info_feb['daylight_change'] == "+2 min since yesterday");
test("Feb 4 Change direction is up", $info_feb['change_direction'] == "up");

echo "\nAll tests passed!\n";
