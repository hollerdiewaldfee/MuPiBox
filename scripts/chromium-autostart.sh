#!/bin/dash
# Autostart run script for kiosk mode, based on @AYapejian: https://github.com/MichaIng/DietPi/issues/1737#issue-318697621
# - Please see /root/.chromium-browser.init (and /etc/chromium.d/custom_flags) for additional falgs.
# Command line switches: https://peter.sh/experiments/chromium-command-line-switches/
# --test-type gets rid of some of the Chromium warnings that you may or may not care about in kiosk on a LAN
# --pull-to-refresh=1
# --ash-host-window-bounds="400,300"
# Resolution to use for kiosk mode, should ideally match current system resolution

/usr/local/bin/mupibox/./startup.sh &

rm ~/.config/chromium/Singleton*

CONFIG="/etc/mupibox/mupiboxconfig.json"
RES_X=$(/usr/bin/jq -r .chromium.resX ${CONFIG})
RES_Y=$(/usr/bin/jq -r .chromium.resY ${CONFIG})
DEBUG=$(/usr/bin/jq -r .chromium.debug ${CONFIG})

CHROMIUM_OPTS="--fast --fast-start --use-gl=egl --kiosk --test-type --window-size=${RES_X:-1280},${RES_Y:-720} --start-fullscreen --start-maximized --window-position=0,0 --disk-cache-dir=/home/dietpi/.mupibox/chromium_cache --disk-cache-size=268435456 --media-cache-size=268435456 --cast-app-background-color #054b61 --default-background-color #054b61"
if [ "${DEBUG}" = "1" ]; then
 CHROMIUM_OPTS=${CHROMIUM_OPTS}" --enable-logging --v=1 --disable-pinch"
fi


# If you want tablet mode, uncomment the next line.
#CHROMIUM_OPTS+=' --force-tablet-mode --tablet-ui'
# Home page

URL="http://$(/usr/bin/jq -r .mupibox.host ${CONFIG}):8200"

# RPi or Debian Chromium package
FP_CHROMIUM=$(command -v chromium-browser)
[ "$FP_CHROMIUM" ] || FP_CHROMIUM=$(command -v chromium)
#sudo nice -n -19 sudo -u dietpi xinit "$FP_CHROMIUM" $CHROMIUM_OPTS --homepage "${URL:-http://MuPiBox:8200}" -- -nocursor tty2 &
xinit "$FP_CHROMIUM" $CHROMIUM_OPTS --homepage "${URL:-http://MuPiBox:8200}" -- -nocursor tty2 &

# START SOUND
START_SOUND=$(/usr/bin/jq -r .mupibox.startSound ${CONFIG})
START_VOLUME=$(/usr/bin/jq -r .mupibox.startVolume ${CONFIG})
AUDIO_DEVICE=$(/usr/bin/jq -r .mupibox.audioDevice ${CONFIG})
/usr/bin/amixer sset ${AUDIO_DEVICE} ${START_VOLUME}%
/usr/bin/mplayer -volume 100 ${START_SOUND} &

x11vnc -ncache 10 -forever -display :0 &

# WLED
wled_active=$(/usr/bin/jq -r .wled.active ${CONFIG})
if [ "${wled_active}" = "true" ]; then
	wled_main_id=$(/usr/bin/jq -r .wled.main_id ${CONFIG})
	wled_baud_rate=$(/usr/bin/jq -r .wled.baud_rate ${CONFIG})
	wled_com_port=$(/usr/bin/jq -r .wled.com_port ${CONFIG})
	wled_brightness_def=$(/usr/bin/jq -r .wled.brightness_default ${CONFIG})
	wled_data='{"ps":"'${wled_main_id}'"}'
	sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
	wled_data='{"bri":"'${wled_brightness_def}'"}'
	sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
	wled_data='{"on":true}'
	sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
fi

# BLUETOOTH
pactl load-module module-bluetooth-discover