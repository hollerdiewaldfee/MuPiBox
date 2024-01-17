#!/bin/bash
#

MUPIBOX_CONFIG="/etc/mupibox/mupiboxconfig.json"
TMP_LEDFILE="/tmp/.power_led"

ledPin=$(/usr/bin/jq -r .shim.ledPin ${MUPIBOX_CONFIG})
ledMax=$(/usr/bin/jq -r .shim.ledBrightnessMax ${MUPIBOX_CONFIG})
ledMin=$(/usr/bin/jq -r .shim.ledBrightnessMin ${MUPIBOX_CONFIG})

echo "{}" | tee ${TMP_LEDFILE}
/usr/bin/cat <<< $(/usr/bin/jq --arg v "${ledPin}" '.led_gpio = $v' ${TMP_LEDFILE}) >  ${TMP_LEDFILE}
/usr/bin/cat <<< $(/usr/bin/jq --arg v "${ledMax}" '.led_max_brightness = $v' ${TMP_LEDFILE}) >  ${TMP_LEDFILE}
/usr/bin/cat <<< $(/usr/bin/jq --arg v "${ledMin}" '.led_min_brightness = $v' ${TMP_LEDFILE}) >  ${TMP_LEDFILE}
/usr/bin/cat <<< $(/usr/bin/jq --arg v "0" '.led_current_brightness = $v' ${TMP_LEDFILE}) >  ${TMP_LEDFILE}
/usr/bin/cat <<< $(/usr/bin/jq --arg v "0" '.led_dim_mode = $v' ${TMP_LEDFILE}) >  ${TMP_LEDFILE}
/usr/bin/python3 /usr/local/bin/mupibox/led_control.py &
sleep 30

while true
do
		sleep 1
		ledPin=$(/usr/bin/jq -r .shim.ledPin ${MUPIBOX_CONFIG})
		ledMax=$(/usr/bin/jq -r .shim.ledBrightnessMax ${MUPIBOX_CONFIG})
		ledMin=$(/usr/bin/jq -r .shim.ledBrightnessMin ${MUPIBOX_CONFIG})
		wled_main_id=$(/usr/bin/jq -r .wled.main_id ${CONFIG})
		wled_baud_rate=$(/usr/bin/jq -r .wled.baud_rate ${CONFIG})
		wled_com_port=$(/usr/bin/jq -r .wled.com_port ${CONFIG})
		wled_brightness_def=$(/usr/bin/jq -r .wled.brightness_default ${CONFIG})
		wled_brightness_dim=$(/usr/bin/jq -r .wled.brightness_default ${CONFIG})

		#ledMin=$(echo "scale=2; $ledMin/100" | bc)
		#ledMax=$(echo "scale=2; $ledMax/100" | bc)
		displayState=`vcgencmd display_power | grep -o '.$'`
		if [ ${displayState} -eq 0 ] && [ ${OLD_STATE} != ${displayState} ]
		then
			wled_data='{"ps":"'${wled_main_id}'"}'
			sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
			wled_data='{"bri":"'${wled_brightness_def}'"}'
			sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
			wled_data='{"on":true}'
			sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
			/usr/bin/cat <<< $(/usr/bin/jq --arg v "1" '.led_dim_mode = $v' ${TMP_LEDFILE}) >  ${TMP_LEDFILE}
			OLD_STATE=${displayState}
		else
			wled_data='{"ps":"'${wled_main_id}'"}'
			sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
			wled_data='{"bri":"'${wled_brightness_dim}'"}'
			sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
			wled_data='{"on":true}'
			sudo python3 /usr/local/bin/mupibox/wled_send_data.py -s ${wled_com_port} -b ${wled_baud_rate} -j ${wled_data}
			/usr/bin/cat <<< $(/usr/bin/jq --arg v "0" '.led_dim_mode = $v' ${TMP_LEDFILE}) >  ${TMP_LEDFILE}
			OLD_STATE=${displayState}
		fi
done
