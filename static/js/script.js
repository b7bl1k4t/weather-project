// Базовые данные на случай недоступности API
const fallbackWeatherData = {
    current: {
        temp: 22,
        feels_like: 24,
        humidity: 65,
        pressure: 1013,
        wind_speed: 3.5,
        description: "Солнечно",
        icon: "☀️",
        updated_at: "Локальная заглушка"
    },
    forecast: [
        { day: "Понедельник", temp: 23, icon: "☀️", description: "Солнечно" },
        { day: "Вторник", temp: 20, icon: "⛅", description: "Облачно" },
        { day: "Среда", temp: 18, icon: "🌧️", description: "Дождь" },
        { day: "Четверг", temp: 21, icon: "☀️", description: "Солнечно" },
        { day: "Пятница", temp: 19, icon: "⛅", description: "Облачно" }
    ],
    cities: [
        { name: "Москва", temp: 18, icon: "⛅" },
        { name: "Санкт-Петербург", temp: 16, icon: "🌧️" },
        { name: "Новосибирск", temp: 12, icon: "☁️" },
        { name: "Екатеринбург", temp: 14, icon: "⛅" },
        { name: "Казань", temp: 19, icon: "☀️" },
        { name: "Сочи", temp: 25, icon: "☀️" }
    ]
};

let weatherData = JSON.parse(JSON.stringify(fallbackWeatherData));

// Обновление данных на странице
function updateWeatherData(data) {
    // Текущая погода
    document.getElementById('current-temp').textContent = data.current.temp + '°C';
    document.getElementById('current-feels-like').textContent = data.current.feels_like + '°C';
    document.getElementById('current-humidity').textContent = data.current.humidity + '%';
    document.getElementById('current-pressure').textContent = data.current.pressure + ' hPa';
    document.getElementById('current-wind').textContent = data.current.wind_speed + ' м/с';
    document.getElementById('current-description').textContent = data.current.description;
    document.getElementById('current-icon').textContent = data.current.icon;

    // Прогноз
    const forecastContainer = document.getElementById('forecast-container');
    forecastContainer.innerHTML = data.forecast.map(day => `
        <div class="forecast-day">
            <div class="day-name">${day.day}</div>
            <div class="weather-icon">${day.icon}</div>
            <div class="forecast-temp">${day.temp}°C</div>
            <div class="forecast-desc">${day.description}</div>
        </div>
    `).join('');

    // Города
    const citiesContainer = document.getElementById('cities-container');
    citiesContainer.innerHTML = data.cities.map(city => `
        <div class="city-card" onclick="showCityWeather('${city.name}')">
            <div class="city-name">${city.name}</div>
            <div class="weather-icon">${city.icon}</div>
            <div class="city-temp">${city.temp}°C</div>
        </div>
    `).join('');
}

function showCityWeather(cityName) {
    const city = weatherData.cities.find(c => c.name === cityName);
    if (city) {
        alert(`Погода в ${cityName}: ${city.temp}°C ${city.icon}`);
    }
}

function formatDateLabel(value) {
    if (!value) {
        return '—';
    }
    const date = new Date(value);
    return date.toLocaleString('ru-RU', {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
}

async function loadWeatherFromApi() {
    try {
        const response = await fetch('/api/weather.php?limit=6', {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            throw new Error(`API error: ${response.status}`);
        }

        const payload = await response.json();
        const rows = Array.isArray(payload.data) ? payload.data : [];

        if (!rows.length) {
            throw new Error('Пустой ответ API');
        }

        const normalized = rows.map(item => ({
            temp: Number(item.temperature),
            feels_like: Number(item.temperature),
            humidity: Number(item.humidity),
            pressure: Number(item.pressure),
            wind_speed: Number(item.wind_speed),
            description: item.description || '—',
            icon: item.icon || '☁️',
            created_at: item.created_at
        }));

        const [current, ...forecast] = normalized;

        weatherData = {
            current: {
                ...current,
                updated_at: formatDateLabel(current.created_at)
            },
            forecast: forecast.map(entry => ({
                day: formatDateLabel(entry.created_at),
                temp: entry.temp,
                icon: entry.icon,
                description: entry.description
            })),
            cities: weatherData.cities
        };

        updateWeatherData(weatherData);
    } catch (error) {
        console.warn('Не удалось загрузить данные из API, использую заглушку:', error);
        weatherData = JSON.parse(JSON.stringify(fallbackWeatherData));
        updateWeatherData(weatherData);
    }
}

// Обновление времени
function updateDateTime() {
    const now = new Date();
    const dateTimeString = now.toLocaleString('ru-RU', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    document.getElementById('current-datetime').textContent = dateTimeString;
}

// Инициализация
document.addEventListener('DOMContentLoaded', function() {
    updateWeatherData(weatherData);
    loadWeatherFromApi();
    updateDateTime();
    setInterval(updateDateTime, 1000);
});
