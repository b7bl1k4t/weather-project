CREATE TABLE IF NOT EXISTS weather_data (
    id SERIAL PRIMARY KEY,
    temperature DECIMAL(4,2) NOT NULL,
    humidity INTEGER NOT NULL,
    pressure INTEGER NOT NULL,
    wind_speed DECIMAL(4,2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    icon VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO weather_data (temperature, humidity, pressure, wind_speed, description, icon) VALUES
(22.5, 65, 1013, 3.2, 'Солнечно', '☀️'),
(18.3, 78, 1010, 4.1, 'Облачно', '⛅'),
(15.7, 82, 1008, 2.5, 'Дождь', '🌧️'),
(20.1, 70, 1012, 3.8, 'Солнечно', '☀️'),
(16.4, 85, 1009, 3.0, 'Пасмурно', '☁️');

INSERT INTO users (username, password, email) VALUES
('admin', '$2a$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@weather.local');