<?php

class Vancouver_Sunrise extends Plugin
{

    private $host;

    function about()
    {
        return array(1.0, "Displays Vancouver sunrise/sunset data in the toolbar", "Antigravity");
    }

    function init($host)
    {
        $this->host = $host;
        $host->add_hook($host::HOOK_MAIN_TOOLBAR_BUTTON, $this);
    }

    function get_js()
    {
        return file_get_contents(__DIR__ . "/init.js");
    }

    function get_css()
    {
        return file_get_contents(__DIR__ . "/init.css");
    }

    function hook_main_toolbar_button()
    {
        echo $this->render_toolbar_html();
    }

    function update()
    {
        echo $this->render_toolbar_html();
    }

    private function render_toolbar_html()
    {
        $data = $this->get_daylight_info(time());
        if (!$data)
            return "";

        $html = "<span class='vancouver-sunrise-container' title='Vancouver Daylight Info'>";
        $html .= "🌅 " . $data['civil_twilight_start'] . " | ";
        $html .= "☀️ " . $data['sunrise'] . " | ";
        $html .= "🌇 " . $data['sunset'] . " | ";
        $html .= "🌃 " . $data['civil_twilight_end'] . " | ";
        $html .= "⏱️ " . $data['total_daylight'] . " | ";
        $html .= ($data['change_direction'] == 'up' ? "📈 " : "📉 ") . $data['daylight_change'];
        $html .= "</span>";
        return $html;
    }

    public function get_daylight_info($timestamp)
    {
        $csv_path = __DIR__ . "/sunrise.csv";
        if (!file_exists($csv_path))
            return null;

        $today_str = date("M j Y", $timestamp);
        $yesterday_str = date("M j Y", $timestamp - 86400);

        $today_data = $this->parse_csv_for_date($csv_path, $today_str);
        $yesterday_data = $this->parse_csv_for_date($csv_path, $yesterday_str);

        if (!$today_data)
            return null;

        $dst_offset = $this->is_dst($timestamp) ? 3600 : 0;

        $result = [
            'civil_twilight_start' => $this->format_csv_time($today_data['Civil Twilight Start'], $dst_offset),
            'sunrise' => $this->format_csv_time($today_data['Sun rise'], $dst_offset),
            'sunset' => $this->format_csv_time($today_data['Sun set'], $dst_offset),
            'civil_twilight_end' => $this->format_csv_time($today_data['Civil Twilight End'], $dst_offset),
        ];

        // Calculate total daylight
        $sunrise_ts = strtotime($today_str . " " . $today_data['Sun rise']);
        $sunset_ts = strtotime($today_str . " " . $today_data['Sun set']);
        $total_today_seconds = $sunset_ts - $sunrise_ts;

        $result['total_daylight'] = $this->format_duration($total_today_seconds);

        // Calculate change
        if ($yesterday_data) {
            $yesterday_sunrise_ts = strtotime($yesterday_str . " " . $yesterday_data['Sun rise']);
            $yesterday_sunset_ts = strtotime($yesterday_str . " " . $yesterday_data['Sun set']);
            $total_yesterday_seconds = $yesterday_sunset_ts - $yesterday_sunrise_ts;

            $diff = $total_today_seconds - $total_yesterday_seconds;
            $sign = ($diff >= 0) ? "+" : "-";
            $result['daylight_change'] = $sign . abs(round($diff / 60)) . " min since yesterday";
            $result['change_direction'] = ($diff >= 0) ? 'up' : 'down';
        } else {
            $result['daylight_change'] = "N/A";
            $result['change_direction'] = 'up';
        }

        return $result;
    }

    private function parse_csv_for_date($file, $date_str)
    {
        $handle = fopen($file, "r");
        if (!$handle)
            return null;

        // Skip header lines
        fgets($handle); // Title
        $header = fgetcsv($handle); // Column names

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2)
                continue;
            // CSV date format: Jan  1 2026 (note the extra space for single-digit days)
            // But PHP's date("M j Y") returns "Jan 1 2026".
            // Let's normalize spaces.
            $csv_date = preg_replace('/\s+/', ' ', trim($row[0]));
            if ($csv_date == $date_str) {
                fclose($handle);
                return array_combine($header, $row);
            }
        }
        fclose($handle);
        return null;
    }

    private function format_csv_time($time_str, $offset)
    {
        // time_str is like "6:50" or "16:25"
        $parts = explode(":", $time_str);
        $seconds = $parts[0] * 3600 + $parts[1] * 60 + $offset;
        return date("H:i", mktime(0, 0, $seconds));
    }

    private function format_duration($seconds)
    {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        return "{$h}h {$m}m";
    }

    public function is_dst($timestamp)
    {
        $date = date("Y-m-d", $timestamp);
        // Vancouver 2026: March 8 to Nov 1
        return ($date >= "2026-03-08" && $date < "2026-11-01");
    }

    function api_version()
    {
        return 2;
    }
}
