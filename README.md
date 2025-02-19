# AzuraCast Playing Next WebSocket Plugin

## Description
The **AzuraCast Playing Next WebSocket** plugin allows WordPress users to fetch and display the next playing song from their **AzuraCast** radio station using a WebSocket connection.

This plugin provides a **shortcode** that can be embedded anywhere on your website to show the upcoming song details.

## Features
- Fetches **next playing song** from AzuraCast WebSocket API.
- Displays song title dynamically using JavaScript.
- Includes **WordPress admin settings page** for configuration.
- Supports album artwork display (optional).

## Installation
1. **Download the Plugin:**  
   - Clone the repository or download the plugin ZIP file.
2. **Upload to WordPress:**  
   - Go to `Plugins` > `Add New` > `Upload Plugin` and select the ZIP file.
3. **Activate the Plugin:**  
   - Navigate to `Plugins` in your WordPress dashboard and activate **AzuraCast WebSocket**.
4. **Configure Settings:**  
   - Go to `Settings` > `AzuraCast WebSocket` and enter the WebSocket API details.

## Usage
Add the following **shortcode** anywhere in your WordPress pages or posts:
```
[azura_playing_next]
```
This will display the next playing song fetched from AzuraCast.

## Configuration Options
| Setting               | Description                                    |
|----------------------|--------------------------------|
| **AzuraCast URL**     | Enter the WebSocket API URL.  |
| **Station Name**      | Set the station name to fetch data from. |
| **Show Album Art**    | Enable/disable album artwork display. |

## Contributing
- Fork this repository on GitHub.
- Create a new feature branch (`git checkout -b feature-name`).
- Commit changes and push to the branch.
- Open a **pull request** with a detailed description.

## License
This plugin is released under the **GPL-2.0+** license.

---
**Developed by s0t0na.** 🚀




