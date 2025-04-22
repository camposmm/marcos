// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const primaryNav = document.getElementById('primaryNav');
  
    menuToggle.addEventListener('click', function() {
      primaryNav.classList.toggle('open');
      menuToggle.textContent = primaryNav.classList.contains('open') ? 'Close' : 'Menu';
    });
  
    // Set Current Year in Footer
    const currentYearElement = document.getElementById('currentYear');
    if (currentYearElement) {
      currentYearElement.textContent = new Date().getFullYear();
    }
  
    // Set Last Modified Date in Footer
    const lastModifiedElement = document.getElementById('lastModified');
    if (lastModifiedElement) {
      lastModifiedElement.textContent = document.lastModified;
    }
  
    // Fetch Weather Data
    fetchWeatherData();
  
    // Load Business Spotlights
    loadSpotlights();
  });
  
  // Weather API Functions
  async function fetchWeatherData() {
    try {
      // Replace with your API key and location
      const apiKey = 'YOUR_API_KEY';
      const city = 'YourCity';
      const weatherUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&units=imperial&appid=${apiKey}`;
      const forecastUrl = `https://api.openweathermap.org/data/2.5/forecast?q=${city}&units=imperial&appid=${apiKey}`;
  
      // For demo purposes, we'll use mock data instead of actual API calls
      // In a real application, you would use:
      // const weatherResponse = await fetch(weatherUrl);
      // const weatherData = await weatherResponse.json();
  
      const mockWeatherData = {
        main: { temp: 29 },
        weather: [{ description: 'partly cloudy', icon: '04d' }]
      };
  
      const mockForecastData = {
        list: [
          { dt: Date.now() / 1000 + 86400, main: { temp: 30 } },
          { dt: Date.now() / 1000 + 172800, main: { temp: 32 } },
          { dt: Date.now() / 1000 + 259200, main: { temp: 28 } }
        ]
      };
  
      displayCurrentWeather(mockWeatherData);
      displayForecast(mockForecastData);
    } catch (error) {
      console.error('Error fetching weather data:', error);
      document.getElementById('temp').textContent = 'Weather data unavailable';
    }
  }
  
  function displayCurrentWeather(data) {
    const tempElement = document.getElementById('temp');
    const descElement = document.getElementById('description');
    const iconElement = document.getElementById('weather-icon');
  
    if (tempElement && descElement && iconElement) {
      tempElement.textContent = `${Math.round(data.main.temp)}°C`;
      
      descElement.textContent = data.weather[0].description;
      
      // Add weather icon
      iconElement.innerHTML = `<img src="https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png" alt="${data.weather[0].description}">`;
    }
  }
  
  function displayForecast(data) {
    const forecastElement = document.getElementById('forecast');
    if (!forecastElement) return;
  
    forecastElement.innerHTML = '';
    
    // Get only one forecast per day (midday)
    const dailyForecasts = data.list.filter((item, index) => index % 8 === 0).slice(0, 3);
    
    dailyForecasts.forEach(day => {
      const date = new Date(day.dt * 1000);
      const dayName = date.toLocaleDateString('en-US', { weekday: 'short' });
      
      const forecastDay = document.createElement('div');
      forecastDay.className = 'forecast-day';
      forecastDay.innerHTML = `
        <h3>${dayName}</h3>
        <p>${Math.round(day.main.temp)}°C</p>
      `;
      
      forecastElement.appendChild(forecastDay);
    });
  }
  
  // Business Spotlights Functions
  function loadSpotlights() {
    // In a real application, this data would come from a JSON file or API
    const members = [
      {
        name: "Americanas",
        logo: "https://brandcenter.americanas.io/wp-content/uploads/2022/02/logo_americanas-1.svg",
        phone: "(555) 123-4567",
        address: "123 Main St",
        website: "https://americanas.com.br",
        membershipLevel: "gold"
      },
      {
        name: "Casas Bahia",
        logo: "https://upload.wikimedia.org/wikipedia/commons/e/e6/Casas_Bahia_logo.svg",
        phone: "(555) 987-6543",
        address: "456 Oak Ave",
        website: "https://casasbahia.com.br",
        membershipLevel: "gold"
      },
      {
        name: "Zara",
        logo: "https://logosmarcas.net/wp-content/uploads/2020/05/Zara-Logo.png",
        phone: "(555) 555-1212",
        address: "789 Pine St",
        website: "https://zara.com.br",
        membershipLevel: "gold"
      }
    ];
    
    // Filter for gold and silver members
    const eligibleMembers = members.filter(member => 
      member.membershipLevel === "gold" || member.membershipLevel === "silver"
    );
    
    // Randomly select businesses for spotlight
    const shuffled = eligibleMembers.sort(() => 0.5 - Math.random());
    const selectedMembers = shuffled.slice(0, 3);
    
    const spotlightContainer = document.getElementById('spotlightContainer');
    if (!spotlightContainer) return;
    
    spotlightContainer.innerHTML = '';
    
    selectedMembers.forEach(member => {
      const spotlightCard = document.createElement('div');
      spotlightCard.className = 'spotlight-card';
      
      spotlightCard.innerHTML = `
        <img src="${member.logo}" alt="${member.name} logo" class="spotlight-logo">
        <h3>${member.name}</h3>
        <p>${member.address}</p>
        <p>${member.phone}</p>
        <a href="${member.website}" target="_blank">Visit Website</a>
        <span class="spotlight-level">${member.membershipLevel.toUpperCase()} Member</span>
      `;
      
      spotlightContainer.appendChild(spotlightCard);
    });
  }