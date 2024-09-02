#!/usr/bin/env python3

import os
import shutil
import argparse


# Import YT Music
from ytmusicapi import YTMusic


source_dir = "data";
target_dir = "/tmp/music";


parser = argparse.ArgumentParser(description="Mode");
parser.add_argument('mode', type=str, help="The mode: 'copyflat', 'deleteremote', 'push'...");

args = parser.parse_args()


if (args.mode == 'copyflat'):
    # Create target directory

    if not os.path.exists(target_dir):
        os.makedirs(target_dir)

    # Scan source directories and sub-directories
    i = 0;
    for root, dirs, files in os.walk(source_dir):
        for file in files:
            if file.endswith('.mp3') or file.endswith('.flac') or file.endswith('.mpga'):
                i = i + 1;
                # Chemin complet du fichier source
                source_file = os.path.join(root, file)
                # Chemin complet du fichier cible
                target_file = os.path.join(target_dir, file)
                # Copier le fichier MP3
                shutil.copy2(source_file, target_file)
                print(f"Copy {i} : {source_file} vers {target_file}")



if (args.mode == 'deleteremote'):
    ytmusic = YTMusic("browser.json");


    listmusic = ytmusic.get_library_upload_songs(100, 'recently_added');    # Number -25 of entry to return

    i = 0
    max = len(listmusic);
    while (i < max):
        print(listmusic[i]);
        i += 1;

    print("Found "+str(i)+" record\n");


if (args.mode == 'push'):
    ytmusic = YTMusic("browser.json");


    # Scan source directories and sub-directories
    i = 0;
    for root, dirs, files in os.walk(target_dir):
        for file in files:
            if file.endswith('.mp3') or file.endswith('.flac') or file.endswith('.mpga'):
                # Test if file already present
                notfound = 0;

            
                if (notfound == 0):
                    full_path = os.path.join(root, file);

                    print(f"Upload file {full_path}");
                    ytmusic.upload_song(full_path);
                    print("File uploaded");
                    break;


