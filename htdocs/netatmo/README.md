# NETATMO MODULE FOR <a href="https://www.dolibarr.org">DOLIBARR ERP CRM</a>


## Features

Add features for Netatmo camera.






## Knowing if your Camera is on same network:

This is an extract of tutorial to get a direct Stream is available on https://dev.netatmo.com/apidocumentation/security#video-access

Step 1:

Go on https://dev.netatmo.com/apidocumentation/security 
Login with your NetAtmo account.
Execute the API #gethomedata with no parameters.
You will receive informations on your Cameras.

Step 2:

Get the value for vpn_url and call URL by adding /command/ping at end.
For example: https://prodvpn-eu-4.netatmo.net/restricted/ip.ip.ip.ip/0ca9956b6330aa456fd736335d7e076c/MTU5MzQyODQwMDqVw0xxxxxxxxxxxxxxxxxx,,/command/ping
You will get the local URL. 

Step 3: Run the command /command/ping again against the local_url retrieved

http://ip.ip.ip.ip/0ca995645698ac5b7xxxxxxxxxxxx/command/ping

If you get a JSON file with same content, your camera is on same network.


## Getting the live stream

Call the *vpn_url* by adding /live/index_local.m3u8 or /live/index.m3u8

Or call the *local_url* by adding /live/index.m3u8

http://192.168.0.10/0ca9956b6330ac5xxxxxxxxxxxxxxx/live/index.m3u8 (automatic resolution)
http://192.168.0.10/0ca9956b6330ac5xxxxxxxxxxxxxxx/live/files/high/index.m3u8
http://192.168.0.10/0ca9956b6330ac5xxxxxxxxxxxxxxx/live/files/medium/index.m3u8
http://192.168.0.10/0ca9956b6330ac5xxxxxxxxxxxxxxx/live/files/low/index.m3u8
http://192.168.0.10/0ca9956b6330ac5xxxxxxxxxxxxxxx/live/files/poor/index.m3u8


## Read the live stream

Open the live stream with vlc or mpv



## Creating a app to use the module

TODO


