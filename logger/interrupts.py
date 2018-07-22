#!/usr/bin/env python2.7


import RPi.GPIO as GPIO

import time
import psycopg2
import datetime
maxContactTime = 0.5
minTimeInterval = 3.0

GPIO.setmode(GPIO.BOARD)

lasttime = 0
GPIO.setup(3, GPIO.IN, pull_up_down=GPIO.PUD_DOWN)

lasttime = time.time()


def logPulse( channel ):
	state= 1
	#print "logging", state
	global lasttime
	now = time.time()
	deltaT = now - lasttime
	
	if deltaT < 0.1:
		print deltaT
		return 
	lasttime = now
	now = datetime.datetime.now().strftime( "%Y-%m-%d %H:%M:%S.%f"  )
	pulseTime = "'{0}'".format( now )
	
	conn = psycopg2.connect( "dbname='counter' user='counter' host='localhost' password='count'" )
	cur = conn.cursor()
	
	
	comStr = "INSERT INTO pulses ( time, state ) VALUES ( {0}, {1}::BOOLEAN )".format(pulseTime, state)
	cur.execute( comStr )
	conn.commit()

GPIO.add_event_detect(3, GPIO.RISING, callback=logPulse)


while 1:
	time.sleep(0.5)
	
