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

// Test 1: Winter date (Feb 4, 2026)
// CSV (standard time): Feb  4 2026,6:27,7:05,7:39,12:26,17:14,17:48,18:26
// Permanent summer time: +1h year round
$ts_feb = strtotime("2026-02-04");
$info_feb = $plugin->get_daylight_info($ts_feb);
test("Feb 4 Civil Twilight Start matches (+1h)", $info_feb['civil_twilight_start'] == "08:05");
test("Feb 4 Sunrise matches (+1h)", $info_feb['sunrise'] == "08:39");
test("Feb 4 Sunset matches (+1h)", $info_feb['sunset'] == "18:14");

// Test 2: Summer date (July 1, 2026)
// CSV: Jul  1 2026,2:28,3:29,4:11,12:16,20:21,21:04,22:05
$ts_jul = strtotime("2026-07-01");
$info_jul = $plugin->get_daylight_info($ts_jul);
test("July 1 Civil Twilight Start matches (+1h)", $info_jul['civil_twilight_start'] == "04:29");
test("July 1 Sunrise matches (+1h)", $info_jul['sunrise'] == "05:11");
test("July 1 Sunset matches (+1h)", $info_jul['sunset'] == "21:21");

// Test 2b: Same offset applies across the old November switch date
$ts_nov = strtotime("2026-11-02");
$info_nov = $plugin->get_daylight_info($ts_nov);
test("Nov 2 data is returned", $info_nov !== null);

// Test 2c: Jan 1, 2026 (first day of the year, no DST)
// CSV (standard time): Jan  1 2026,6:50,7:30,8:08,12:16,16:25,17:02,17:43,...
$ts_jan = strtotime("2026-01-01");
$info_jan = $plugin->get_daylight_info($ts_jan);
test("Jan 1 Civil Twilight Start matches (+1h)", $info_jan['civil_twilight_start'] == "08:30");
test("Jan 1 Sunrise matches (+1h)", $info_jan['sunrise'] == "09:08");
test("Jan 1 Sunset matches (+1h)", $info_jan['sunset'] == "17:25");
test("Jan 1 Civil Twilight End matches (+1h)", $info_jan['civil_twilight_end'] == "18:02");
// Daylight length is unaffected by the offset: 16:25 - 8:08 = 8h 17m
test("Jan 1 Total Daylight matches", $info_jan['total_daylight'] == "8h 17m");
// Dec 31, 2025 is not in the CSV
test("Jan 1 Daylight Change is N/A", $info_jan['daylight_change'] == "N/A");

// Test 3: Total Daylight Calculation
// Feb 4: 17:14 - 07:39 = 9h 35m
test("Feb 4 Total Daylight matches", $info_feb['total_daylight'] == "9h 35m");

// Test 4: Daylight Change
// CSV Feb 3: 7:40 to 17:13 = 9h 33m
// Change: 2 mins
test("Feb 4 Daylight Change matches", $info_feb['daylight_change'] == "+2 min since yesterday");
test("Feb 4 Change direction is up", $info_feb['change_direction'] == "up");

echo "\nAll tests passed!\n";
