// Имитация данных о погоде
const weatherData = {
    current: {
        temp: 22,
        feels_like: 24,
        humidity: 65,
        pressure: 1013,
        wind_speed: 3.5,
        description: "Солнечно",
        icon: "☀️"
    },
    forecast: [
        { day: "Понедельник", temp: 23, icon: "☀️" },
        { day: "Вторник", temp: 20, icon: "⛅" },
        { day: "Среда", temp: 18, icon: "🌧️" },
        { day: "Четверг", temp: 21, icon: "☀️" },
        { day: "Пятница", temp: 19, icon: "⛅" }
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

// Обновление данных на странице
function updateWeatherData() {
    // Текущая погода
    document.getElementById('current-temp').textContent = weatherData.current.temp + '°C';
    document.getElementById('current-feels-like').textContent = weatherData.current.feels_like + '°C';
    document.getElementById('current-humidity').textContent = weatherData.current.humidity + '%';
    document.getElementById('current-pressure').textContent = weatherData.current.pressure + ' hPa';
    document.getElementById('current-wind').textContent = weatherData.current.wind_speed + ' м/с';
    document.getElementById('current-description').textContent = weatherData.current.description;
    document.getElementById('current-icon').textContent = weatherData.current.icon;

    // Прогноз
    const forecastContainer = document.getElementById('forecast-container');
    forecastContainer.innerHTML = weatherData.forecast.map(day => `
        <div class="forecast-day">
            <div class="day-name">${day.day}</div>
            <div class="weather-icon">${day.icon}</div>
            <div class="forecast-temp">${day.temp}°C</div>
        </div>
    `).join('');

    // Города
    const citiesContainer = document.getElementById('cities-container');
    citiesContainer.innerHTML = weatherData.cities.map(city => `
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
    updateWeatherData();
    updateDateTime();
    setInterval(updateDateTime, 1000);
});