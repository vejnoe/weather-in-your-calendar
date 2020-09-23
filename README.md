# Weather in your Calendar   ⛅️ 26°

This is the code powering the [Weather in your Calendar](https://weather.vejnoe.dk/?from=github.com).

It's a simple PHP script generating a .ical formated calendar with a 16 days weather forecast with data from [OpenWeatherMap](https://openweathermap.org/).

[You can try it out here](https://weather.vejnoe.dk/?from=github.com)

![Calendar preview](https://weather.vejnoe.dk/images/weather-calendar-screenshot.png)

## URL parameters

### Usage
You can upload it to your host and enter the following url like so:

```url
https://yourdomain.com/weather-cal.php?city=London&units=imperial
```

### Options

Key | Values
--- | ------
`city` | `city name` or <br>`city name,state code` or <br>`city name,state code,country code`
`units` | `metric` or `imperial`
`temperature` | `day` or `low-high`
`location` | `show` or `hide`


## Check it out on Product Hunt

[![Featured on Product Hunt](https://api.producthunt.com/widgets/embed-image/v1/featured.svg?post_id=242724&theme=light)](https://www.producthunt.com/posts/weather-in-your-calendar?utm_source=badge-featured&utm_medium=badge&utm_souce=badge-weather-in-your-calendar)