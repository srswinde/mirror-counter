<html>
<head>
<title> Oven Rotation Counter </title>
<style>

body {
  font: 10px sans-serif;
}

span.avg_val
{
	font: 60px sans-serif;
	font-weight: bold;
	font-style:	normal;
}
span.avg_descr
{
	font: 40px sans-serif;
	font-style:	italic
}
.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.x.axis path {
  display: none;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script>
pulses = [];


function getData( deltaT, plot1 )
{
    
    deltaT = deltaT || "10m";
    deltaT = deltaT || ""
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {	
            var valsList = JSON.parse( xmlhttp.responseText );
            for(iter in valsList)
            {
            	var d = new Date(valsList[iter][0]);
            	pulses[iter] = d ;
            }
            var data = cleanup( pulses );
	   
	    putAvg( data );
            setTimeout( getData, 2000, deltaT, plot1 );
	    
        }

    }
    xmlhttp.open("GET", "/getpulses.php?deltaT=" + deltaT, true);
    xmlhttp.send();
}

function cleanup( times )
{
	var data = [];
	var count = 0;
	var sum = 0;
	
		for (var iter=3; iter<times.length-3; iter++ )
		{
			var delta = times[iter].getTime() - times[iter-3].getTime();
		
			var rpm = 1/((delta/1000)/60);
		
			var datum = {};
			datum.date = times[iter];
			datum.rpm = rpm;
			sum = sum + rpm;
			count++;
			data[iter - 3] = datum;

		}


	return data;
}


function putAvg( all_rpms )
{
	
	console.log(all_rpms[0].rpm)
	console.log(all_rpms[all_rpms.length-1].rpm)
	d3.select("span#avg_display")
		.text( Math.round( all_rpms[all_rpms.length-1].rpm *1000)/1000)

}



</script>
</head>
<body onload=getData()>


	
<div id="display">
	<span class=avg_val id=avg_display></span><span class="avg_descr">&nbsp RPM's</span>
	<div id="plot1"></div>

	
</div>

</body>
</html>
