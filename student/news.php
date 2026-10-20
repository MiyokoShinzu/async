<?php
/* =========================================================
   WEATHER UPDATES - ETS-Async Learning Portal
   Windy forecast + Open-Meteo API confirmation
   ========================================================= */
session_start();
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true || !isset($_SESSION["user"]) || ($_SESSION["user"]["access"] ?? "") !== "student") {
    header("Location: ../login.php");
    exit;
}
$user = $_SESSION["user"];
require_once "../src/connection.php";
$firstName = $user["first_name"] ?? "";
$lastName = $user["last_name"] ?? "";
$middleInitial = $user["middle_initial"] ?? "";
$extensionName = $user["extension_name"] ?? "";
$fullName = trim($firstName . " " . ($middleInitial !== "" ? $middleInitial . ". " : "") . $lastName . ($extensionName !== "" ? " " . $extensionName : ""));
$initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<?php include 'globals/head.php'; ?>

<body>
    <?php include 'globals/sidebar.php'; ?>
    <?php include 'globals/topbar.php'; ?>
    <main class="main-content">
        <div class="content-wrapper">
            <div class="page-header weather-page-header">
                <div>
                    <h2>Weather Updates</h2>
                    <p>Explore Windy forecasts and compare conditions with a free weather API.</p>
                </div><span class="weather-badge"><i class="bi bi-cloud-sun"></i> Live Weather</span>
            </div>

            <section class="weather-card">
                <div class="weather-card-heading">
                    <div>
                        <h4><i class="bi bi-wind me-2"></i>Windy Forecast Map</h4>
                        <p>Explore wind flow and forecast layers for the selected location.</p>
                    </div><a href="https://www.windy.com/" target="_blank" rel="noopener noreferrer" class="source-label">Windy.com <i class="bi bi-box-arrow-up-right"></i></a>
                </div>
                <div class="windy-frame-wrap"><iframe id="windyFrame" title="Windy live weather map for Tuguegarao City, Cagayan" src="https://embed.windy.com/embed2.html?lat=17.6132&lon=121.7269&detailLat=17.6132&detailLon=121.7269&width=650&height=450&zoom=8&level=surface&overlay=wind&product=ecmwf&menu=&message=true&marker=&calendar=now&pressure=true&type=map&location=coordinates&detail=true&metricWind=default&metricTemp=default&metricRain=default" loading="eager" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    <div class="windy-floating-data" aria-label="Current weather summary">
                        <div class="windy-floating-top">
                            <span class="windy-live-dot"></span>
                            <span id="floatingLocation">Tuguegarao City, Cagayan</span>
                            <span class="windy-floating-label">LIVE CONDITIONS</span>
                        </div>
                        <div class="windy-floating-main">
                            <div>
                                <div class="windy-floating-temp"><span id="floatingTemperature">--</span><span>°C</span></div>
                                <div id="floatingCondition" class="windy-floating-condition">Loading weather…</div>
                            </div>
                            <div class="windy-floating-icon"><i class="bi bi-cloud-sun"></i></div>
                        </div>
                        <div class="windy-floating-metrics">
                            <div><i class="bi bi-wind"></i><span>Wind</span><strong><span id="floatingWind">--</span> km/h</strong></div>
                            <div><i class="bi bi-droplet"></i><span>Humidity</span><strong><span id="floatingHumidity">--</span>%</strong></div>
                            <div><i class="bi bi-cloud-rain"></i><span>Rain</span><strong><span id="floatingRain">--</span> mm</strong></div>
                        </div>
                    </div>
                </div>
                <p class="windy-attribution">Weather map provided by <a href="https://www.windy.com/" target="_blank" rel="noopener noreferrer">Windy.com</a>. Keep the attribution visible.</p>
            </section>

            <section class="weather-card">
                <div class="weather-card-heading">
                    <div>
                        <h4><i class="bi bi-geo-alt me-2"></i>Choose a Location</h4>
                        <p>Select a Philippine location to update both sources.</p>
                    </div>
                </div>
                <div class="location-controls">
                    <div class="location-field"><label for="weatherLocation">Location</label><select id="weatherLocation" class="form-select">
                            <option value="7.0731,125.6128" data-name="Davao City">Davao City</option>
                            <option value="14.5995,120.9842" data-name="Manila">Manila</option>
                            <option value="17.6132,121.7269" data-name="Tuguegarao City" selected>Tuguegarao City, Cagayan</option>
                            <option value="16.4023,120.5960" data-name="Baguio City">Baguio City</option>
                            <option value="10.3157,123.8854" data-name="Cebu City">Cebu City</option>
                            <option value="8.4542,124.6319" data-name="Cagayan de Oro">Cagayan de Oro</option>
                            <option value="6.9214,122.0790" data-name="Zamboanga City">Zamboanga City</option>
                        </select></div>
                    <div class="location-actions"><button type="button" id="refreshWeather" class="btn btn-primary"><i class="bi bi-arrow-clockwise me-1"></i> Refresh Weather</button><a id="openWindy" class="btn btn-outline-secondary" href="https://www.windy.com/" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right me-1"></i> Open Windy</a></div>
                </div>
                <div class="update-line"><span><i class="bi bi-clock me-1"></i>Last checked: <strong id="lastUpdated">Not checked yet</strong></span><span id="apiStatus" class="source-status">Connecting…</span></div>
            </section>

            <section class="weather-card">
                <div class="weather-card-heading">
                    <div>
                        <h4><i class="bi bi-cloud-sun me-2"></i>Current Weather Confirmation</h4>
                        <p>Open-Meteo free weather API — no API key required.</p>
                    </div><span class="source-label">Open-Meteo API</span>
                </div>
                <div id="weatherError" class="weather-error" hidden></div>
                <div class="current-weather-grid">
                    <div class="current-weather-main">
                        <div class="weather-condition-icon" id="weatherIcon"><i class="bi bi-cloud-sun"></i></div>
                        <div>
                            <div class="current-temp"><span id="temperature">--</span><span class="degree">°C</span></div>
                            <div class="current-condition" id="condition">Loading…</div>
                            <div class="current-location" id="currentLocation">Tuguegarao City, Cagayan, Philippines</div>
                        </div>
                    </div>
                    <div class="weather-metric"><span><i class="bi bi-wind"></i> Wind speed</span><strong><span id="windSpeed">--</span> km/h</strong></div>
                    <div class="weather-metric"><span><i class="bi bi-droplet"></i> Relative humidity</span><strong><span id="humidity">--</span>%</strong></div>
                    <div class="weather-metric"><span><i class="bi bi-cloud-rain"></i> Precipitation</span><strong><span id="precipitation">--</span> mm</strong></div>
                    <div class="weather-metric"><span><i class="bi bi-compass"></i> Wind direction</span><strong id="windDirection">--</strong></div>
                </div>
                <p class="weather-note">Comparison note: Windy and Open-Meteo may show different values because they can use different forecast models, update times, and map layers. Compare location and timestamp.</p>
            </section>

            <section class="weather-card">
                <div class="weather-card-heading">
                    <div>
                        <h4><i class="bi bi-calendar-week me-2"></i>7-Day Forecast</h4>
                        <p>Daily forecast for the selected location.</p>
                    </div>
                </div>
                <div class="forecast-grid" id="forecastGrid">
                    <div class="forecast-loading">Loading forecast…</div>
                </div>
            </section>



            <section class="weather-card">
                <div class="weather-card-heading">
                    <div>
                        <h4><i class="bi bi-info-circle me-2"></i>Sources and Safety</h4>
                        <p>Use weather information as a guide, not as a substitute for official warnings.</p>
                    </div>
                </div>
                <div class="source-info-grid">
                    <div class="source-info"><strong><i class="bi bi-cloud me-2"></i>Windy</strong>
                        <p>Interactive forecast map and wind visualization.</p><a href="https://www.windy.com/" target="_blank" rel="noopener noreferrer">Visit Windy.com</a>
                    </div>
                    <div class="source-info"><strong><i class="bi bi-database-check me-2"></i>Open-Meteo</strong>
                        <p>Free current conditions and forecast API, with no API key for standard use.</p><a href="https://open-meteo.com/en/docs" target="_blank" rel="noopener noreferrer">API documentation</a>
                    </div>
                    <div class="source-info"><strong><i class="bi bi-shield-exclamation me-2"></i>PAGASA</strong>
                        <p>Check official Philippine typhoon, rainfall, and severe-weather advisories.</p><a href="https://www.pagasa.dost.gov.ph/" target="_blank" rel="noopener noreferrer">Visit PAGASA</a>
                    </div>
                </div>
                <p class="api-attribution">Weather data by Open-Meteo, licensed under CC BY 4.0. Attribution is required.</p>
            </section>
        </div>
    </main>
    <style>
        :root {
            --w-bg: #fff;
            --w-border: #e4e7ec;
            --w-text: #172033;
            --w-muted: #667085;
            --w-secondary: #475467;
            --w-hover: #f8fafc;
            --w-blue: #2563eb
        }

        html[data-theme="dark"] {
            --w-bg: #151922;
            --w-border: #2a3140;
            --w-text: #f1f5f9;
            --w-muted: #a8b0bf;
            --w-secondary: #c2c9d3;
            --w-hover: #1c2230;
            --w-blue: #60a5fa
        }

        .weather-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px
        }

        .weather-page-header h2 {
            margin: 0;
            color: var(--w-text);
            font-size: 1.55rem;
            font-weight: 750
        }

        .weather-page-header p {
            margin: 6px 0 0;
            color: var(--w-muted);
            font-size: .84rem;
            line-height: 1.6
        }

        .weather-badge,
        .source-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 10px;
            border: 1px solid var(--w-border);
            border-radius: 7px;
            background: var(--w-hover);
            color: var(--w-blue);
            font-size: .7rem;
            font-weight: 700;
            white-space: nowrap
        }

        .weather-card {
            margin-bottom: 17px;
            border: 1px solid var(--w-border);
            border-radius: 11px;
            background: var(--w-bg);
            overflow: hidden
        }

        .weather-card-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 17px 19px;
            border-bottom: 1px solid var(--w-border)
        }

        .weather-card-heading h4 {
            margin: 0 0 4px;
            color: var(--w-text);
            font-size: .92rem;
            font-weight: 750
        }

        .weather-card-heading p {
            margin: 0;
            color: var(--w-muted);
            font-size: .73rem;
            line-height: 1.55
        }

        .location-controls {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 18px;
            padding: 18px 19px 10px
        }

        .location-field {
            width: min(100%, 430px)
        }

        .location-field label {
            display: block;
            margin-bottom: 6px;
            color: var(--w-secondary);
            font-size: .76rem;
            font-weight: 700
        }

        .location-field .form-select {
            min-height: 43px;
            border-color: var(--w-border);
            background-color: var(--w-bg);
            color: var(--w-text);
            font-size: .84rem
        }

        .location-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px
        }

        .location-actions .btn {
            min-height: 41px;
            border-radius: 7px;
            font-size: .77rem;
            font-weight: 650
        }

        .update-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            padding: 10px 19px 15px;
            color: var(--w-muted);
            font-size: .7rem
        }

        .source-status {
            padding: 5px 8px;
            border-radius: 20px;
            background: var(--w-hover);
            color: var(--w-muted);
            font-weight: 700
        }

        .status-success {
            background: #dcfce7;
            color: #166534
        }

        .status-error {
            background: #fee2e2;
            color: #991b1b
        }

        .source-link {
            text-decoration: none
        }

        .current-weather-grid {
            display: grid;
            grid-template-columns: 1.4fr repeat(4, minmax(115px, 1fr));
            gap: 12px;
            padding: 20px
        }

        .current-weather-main {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0
        }

        .weather-condition-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: var(--w-hover);
            color: var(--w-blue);
            font-size: 1.8rem
        }

        .current-temp {
            color: var(--w-text);
            font-size: 2.1rem;
            line-height: 1.1;
            font-weight: 800;
            white-space: nowrap
        }

        .degree {
            font-size: 1.1rem;
            vertical-align: top;
            margin-left: 2px
        }

        .current-condition {
            margin-top: 5px;
            color: var(--w-secondary);
            font-size: .78rem;
            font-weight: 650
        }

        .current-location {
            margin-top: 3px;
            color: var(--w-muted);
            font-size: .68rem
        }

        .weather-metric {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
            min-height: 98px;
            padding: 14px;
            border: 1px solid var(--w-border);
            border-radius: 9px;
            background: var(--w-hover)
        }

        .weather-metric span {
            color: var(--w-muted);
            font-size: .68rem;
            line-height: 1.4
        }

        .weather-metric span i {
            margin-right: 5px;
            color: var(--w-blue)
        }

        .weather-metric strong {
            color: var(--w-text);
            font-size: 1.05rem;
            font-weight: 750
        }

        .weather-note,
        .api-attribution {
            margin: 0;
            padding: 0 20px 17px;
            color: var(--w-muted);
            font-size: .7rem;
            line-height: 1.65
        }

        .weather-error {
            margin: 15px 20px 0;
            padding: 11px 13px;
            border-radius: 7px;
            background: #fee2e2;
            color: #991b1b;
            font-size: .78rem
        }

        .forecast-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 9px;
            padding: 18px
        }

        .forecast-day {
            padding: 13px 8px;
            text-align: center;
            border: 1px solid var(--w-border);
            border-radius: 9px;
            background: var(--w-hover)
        }

        .day-name {
            color: var(--w-text);
            font-size: .72rem;
            font-weight: 750
        }

        .day-icon {
            margin: 10px 0;
            color: var(--w-blue);
            font-size: 1.35rem
        }

        .day-condition {
            min-height: 29px;
            color: var(--w-muted);
            font-size: .62rem;
            line-height: 1.4
        }

        .day-temp {
            margin-top: 7px;
            color: var(--w-text);
            font-size: .8rem;
            font-weight: 750
        }

        .day-rain {
            margin-top: 6px;
            color: var(--w-muted);
            font-size: .62rem
        }

        .forecast-loading {
            grid-column: 1/-1;
            padding: 24px;
            color: var(--w-muted);
            text-align: center;
            font-size: .78rem
        }

        .windy-frame-wrap {
            height: 520px;
            background: var(--w-hover)
        }

        .windy-frame-wrap {
            position: relative;
            isolation: isolate;
        }

        .windy-floating-data {
            position: absolute;
            z-index: 5;
            top: 16px;
            left: 16px;
            width: min(330px, calc(100% - 32px));
            padding: 16px;
            border: 1px solid rgba(255, 255, 255, .42);
            border-radius: 16px;
            color: #f8fafc;
            background: rgba(15, 23, 42, .78);
            box-shadow: 0 12px 34px rgba(0, 0, 0, .24);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            pointer-events: none;
        }

        .windy-floating-top {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .78rem;
            font-weight: 750;
        }

        .windy-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 0 4px rgba(74, 222, 128, .16);
        }

        .windy-floating-label {
            margin-left: auto;
            color: #cbd5e1;
            font-size: .58rem;
            letter-spacing: .08em;
        }

        .windy-floating-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0 14px;
        }

        .windy-floating-temp {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.05em;
        }

        .windy-floating-temp span:last-child {
            margin-left: 3px;
            font-size: 1rem;
            vertical-align: top;
        }

        .windy-floating-condition {
            margin-top: 7px;
            color: #e2e8f0;
            font-size: .76rem;
        }

        .windy-floating-icon {
            display: grid;
            place-items: center;
            width: 58px;
            height: 58px;
            border-radius: 15px;
            color: #bfdbfe;
            background: rgba(255, 255, 255, .12);
            font-size: 1.8rem;
        }

        .windy-floating-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            border-top: 1px solid rgba(255, 255, 255, .2);
            padding-top: 12px;
        }

        .windy-floating-metrics>div {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 0;
        }

        .windy-floating-metrics i {
            color: #93c5fd;
            font-size: .85rem;
        }

        .windy-floating-metrics span {
            color: #cbd5e1;
            font-size: .63rem;
        }

        .windy-floating-metrics strong {
            color: #fff;
            font-size: .77rem;
            white-space: nowrap;
        }

        .windy-frame-wrap iframe {
            display: block;
            width: 100%;
            height: 100%;
            border: 0
        }

        .windy-attribution {
            margin: 0;
            padding: 12px 18px;
            color: var(--w-muted);
            font-size: .69rem
        }

        .windy-attribution a,
        .source-info a {
            color: var(--w-blue);
            font-weight: 650
        }

        .source-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            padding: 18px
        }

        .source-info {
            padding: 15px;
            border: 1px solid var(--w-border);
            border-radius: 9px;
            background: var(--w-hover)
        }

        .source-info strong {
            color: var(--w-text);
            font-size: .78rem
        }

        .source-info p {
            min-height: 48px;
            margin: 9px 0;
            color: var(--w-muted);
            font-size: .7rem;
            line-height: 1.6
        }

        .source-info a {
            font-size: .7rem;
            text-decoration: none
        }

        @media(max-width:1100px) {
            .current-weather-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .current-weather-main {
                grid-column: 1/-1;
                padding-bottom: 8px
            }

            .forecast-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr))
            }
        }

        @media(max-width:700px) {

            .weather-page-header,
            .location-controls {
                align-items: stretch;
                flex-direction: column
            }

            .weather-badge {
                align-self: flex-start
            }

            .location-field {
                width: 100%
            }

            .location-actions {
                display: grid;
                grid-template-columns: 1fr 1fr
            }

            .location-actions .btn {
                padding: 8px 7px
            }

            .weather-card-heading {
                align-items: flex-start
            }

            .current-weather-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                padding: 14px
            }

            .weather-metric {
                min-height: 84px;
                padding: 11px
            }

            .forecast-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                padding: 14px
            }

            .forecast-day:last-child {
                grid-column: 1/-1
            }

            .windy-frame-wrap {
                height: 430px
            }

            .windy-floating-data {
                top: 10px;
                left: 10px;
                width: min(300px, calc(100% - 20px));
                padding: 12px;
            }

            .windy-floating-temp {
                font-size: 2.1rem;
            }

            .source-info-grid {
                grid-template-columns: 1fr;
                padding: 14px
            }

            .source-info p {
                min-height: 0
            }
        }
    </style>
    <script>
        (() => {
            const select = document.getElementById('weatherLocation'),
                button = document.getElementById('refreshWeather'),
                status = document.getElementById('apiStatus'),
                error = document.getElementById('weatherError'),
                forecast = document.getElementById('forecastGrid'),
                frame = document.getElementById('windyFrame'),
                open = document.getElementById('openWindy');
            const descriptions = {
                0: 'Clear sky',
                1: 'Mainly clear',
                2: 'Partly cloudy',
                3: 'Overcast',
                45: 'Fog',
                48: 'Depositing rime fog',
                51: 'Light drizzle',
                53: 'Moderate drizzle',
                55: 'Dense drizzle',
                56: 'Light freezing drizzle',
                57: 'Dense freezing drizzle',
                61: 'Slight rain',
                63: 'Moderate rain',
                65: 'Heavy rain',
                66: 'Light freezing rain',
                67: 'Heavy freezing rain',
                71: 'Slight snow',
                73: 'Moderate snow',
                75: 'Heavy snow',
                77: 'Snow grains',
                80: 'Slight rain showers',
                81: 'Moderate rain showers',
                82: 'Violent rain showers',
                85: 'Slight snow showers',
                86: 'Heavy snow showers',
                95: 'Thunderstorm',
                96: 'Thunderstorm with hail',
                99: 'Thunderstorm with heavy hail'
            };

            function icon(code) {
                if ([0, 1].includes(code)) return 'bi-sun';
                if ([2, 3, 45, 48].includes(code)) return 'bi-cloud';
                if ([51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82].includes(code)) return 'bi-cloud-rain';
                if ([95, 96, 99].includes(code)) return 'bi-cloud-lightning-rain';
                if ([71, 73, 75, 77, 85, 86].includes(code)) return 'bi-snow';
                return 'bi-cloud-sun'
            }

            function setStatus(text, kind) {
                status.textContent = text;
                status.className = 'source-status ' + (kind === 'ok' ? 'status-success' : kind === 'bad' ? 'status-error' : '')
            }

            function place() {
                const o = select.options[select.selectedIndex],
                    c = select.value.split(',').map(Number),
                    name = o.dataset.name || o.textContent.trim();
                document.getElementById('currentLocation').textContent = name + ', Philippines';
                document.getElementById('floatingLocation').textContent = name;
                frame.title = 'Windy live weather map for ' + name;
                frame.src = 'https://embed.windy.com/embed2.html?lat=' + c[0] + '&lon=' + c[1] + '&detailLat=' + c[0] + '&detailLon=' + c[1] + '&width=650&height=450&zoom=8&level=surface&overlay=wind&product=ecmwf&menu=&message=true&marker=&calendar=now&pressure=true&type=map&location=coordinates&detail=true&metricWind=default&metricTemp=default&metricRain=default';
                open.href = 'https://www.windy.com/' + c[0] + '/' + c[1] + '?wind,' + c[0] + ',' + c[1] + ',8';
                return {
                    lat: c[0],
                    lon: c[1],
                    name
                }
            }
            async function load() {
                const p = place();
                error.hidden = true;
                setStatus('Connecting to API…', '');
                button.disabled = true;
                button.textContent = 'Loading…';
                const url = 'https://api.open-meteo.com/v1/forecast?latitude=' + p.lat + '&longitude=' + p.lon + '&current=temperature_2m,relative_humidity_2m,precipitation,weather_code,wind_speed_10m,wind_direction_10m&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max&forecast_days=7&timezone=auto';
                try {
                    const r = await fetch(url, {
                        cache: 'no-store'
                    });
                    if (!r.ok) throw Error('HTTP ' + r.status);
                    const d = await r.json();
                    if (!d.current || !d.daily) throw Error('Incomplete weather response');
                    const c = d.current,
                        code = Number(c.weather_code);
                    document.getElementById('temperature').textContent = Math.round(c.temperature_2m);
                    document.getElementById('floatingTemperature').textContent = Math.round(c.temperature_2m);
                    document.getElementById('floatingCondition').textContent = descriptions[code] || 'Condition unavailable';
                    document.getElementById('floatingWind').textContent = Math.round(c.wind_speed_10m);
                    document.getElementById('floatingHumidity').textContent = Math.round(c.relative_humidity_2m);
                    document.getElementById('floatingRain').textContent = Number(c.precipitation).toFixed(1);
                    document.querySelector('.windy-floating-icon').innerHTML = '<i class="bi ' + icon(code) + '"></i>';
                    document.getElementById('condition').textContent = descriptions[code] || 'Condition unavailable';
                    document.getElementById('weatherIcon').innerHTML = '<i class="bi ' + icon(code) + '"></i>';
                    document.getElementById('windSpeed').textContent = Math.round(c.wind_speed_10m);
                    document.getElementById('humidity').textContent = Math.round(c.relative_humidity_2m);
                    document.getElementById('precipitation').textContent = Number(c.precipitation).toFixed(1);
                    const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
                    document.getElementById('windDirection').textContent = dirs[Math.round(Number(c.wind_direction_10m) / 45) % 8] + ' (' + Math.round(c.wind_direction_10m) + '°)';
                    forecast.innerHTML = '';
                    d.daily.time.forEach((date, i) => {
                        const code = Number(d.daily.weather_code[i]),
                            el = document.createElement('div');
                        el.className = 'forecast-day';
                        el.innerHTML = '<div class="day-name"></div><div class="day-icon"><i class="bi ' + icon(code) + '"></i></div><div class="day-condition"></div><div class="day-temp"></div><div class="day-rain"></div>';
                        el.querySelector('.day-name').textContent = i === 0 ? 'Today' : new Date(date + 'T12:00:00').toLocaleDateString(undefined, {
                            weekday: 'short'
                        });
                        el.querySelector('.day-condition').textContent = descriptions[code] || 'Forecast';
                        el.querySelector('.day-temp').textContent = Math.round(d.daily.temperature_2m_max[i]) + '° / ' + Math.round(d.daily.temperature_2m_min[i]) + '°C';
                        const chance = d.daily.precipitation_probability_max[i];
                        el.querySelector('.day-rain').textContent = chance == null ? 'Rain chance: --' : 'Rain chance: ' + chance + '%';
                        forecast.appendChild(el)
                    });
                    document.getElementById('lastUpdated').textContent = new Date().toLocaleString();
                    setStatus('API data loaded successfully', 'ok')
                } catch (e) {
                    error.hidden = false;
                    error.textContent = 'Unable to load weather data. Check your connection and try again. ' + e.message;
                    forecast.innerHTML = '<div class="forecast-loading">Forecast unavailable. Please try refreshing.</div>';
                    setStatus('Weather API unavailable', 'bad')
                } finally {
                    button.disabled = false;
                    button.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Refresh Weather'
                }
            }
            select.addEventListener('change', load);
            button.addEventListener('click', load);
            load();
        })();
    </script>
    <?php include 'globals/scripts.php'; ?>
</body>

</html>