### Oven Rotation Counter for the Mirror lab

## Description

There are two parts to this software, a web grapher/displayer in php and d3 and a logger based in python. The logging is a postgresql database that is populated. The actual counting is done with an inductive sensor that detects  metal supports as they pass by the stationary sensor. This happens three times per rotation. So our resolution is effectively one third of a revolution. 
