#!/usr/bin/env python3


# Import YT Music
from ytmusicapi import YTMusic

ytmusic = YTMusic("browser.json");


listmusic = ytmusic.get_library_upload_songs(5, 'recently_added');

i = 0
max = len(listmusic);
while (i < max):
    print(listmusic[i]);
    i += 1;




#ytmusic.upload_song('//media/ldestailleur/HDDATA1_LD/Mes Musiques/Artistes/Front 242/Front 242 - First In First Out.mp3');


