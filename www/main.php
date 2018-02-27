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

function plot( data, div_id )
{
	
	div_id = div_id || "plot1"
	
	d3.select("svg#plotsvg_"+div_id).remove()
	
	var margin = {top: 20, right: 20, bottom: 30, left: 50},
		 width = 1000 - margin.left - margin.right,
		 height = 200 - margin.top - margin.bottom;

	var parseDate = d3.time.format("%d-%b-%y").parse;

	var x = d3.time.scale()
		 .range([0, width]);

	var y = d3.scale.linear()
		 .range([height, 0]);

	var xAxis = d3.svg.axis()
		 .scale(x)
		 .orient("bottom");

	var yAxis = d3.svg.axis()
		 .scale(y)
		 .orient("left");

	var line = d3.svg.line()
		 .x(function(d) { return x(d.date); })
		 .y(function(d) { return y(d.rpm); });

	var svg = d3.select("div#"+div_id).append("svg")
		 .attr("width", width + margin.left + margin.right)
		 .attr("height", height + margin.top + margin.bottom)
		 .attr("id", "plotsvg_"+div_id)
	  .append("g")
		 .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


	  x.domain(d3.extent(data, function(d) { return d.date; }));
	  y.domain(d3.extent(data, function(d) { return d.rpm; }));

	  svg.append("g")
		   .attr("class", "x axis")
		   .attr("transform", "translate(0," + height + ")")
		   .call(xAxis);

	  svg.append("g")
		   .attr("class", "y axis")
		   .call(yAxis)
		 .append("text")
		   .attr("transform", "rotate(-90)")
		   .attr("y", 6)
		   .attr("dy", ".71em")
		   .style("text-anchor", "end")
		   .text("RPM's");

	  svg.append("path")
		   .datum(data)
		   .attr("class", "line")
		   .attr("d", line);

}

function getData( deltaT, plotdiv )
{
    
    deltaT = deltaT || "10m";
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
            var data = cleanup(pulses);
	   
	    plot(data, plotdiv);
	    putAvg(data);
            setTimeout( getData, 5000, deltaT, plotdiv );
	    
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
	var sum=0;
	for( var ii=0; ii<all_rpms.length; ii++ )
	{
		sum = sum + all_rpms[ii].rpm;
	}

	d3.select("span#avg_display")
		.text( Math.round( (sum/all_rpms.length)*1000 )/1000 +' ')

}


function parseURLParams( ) 
{
    var url = window.location.search
    var queryStart = url.indexOf("?") + 1,
        queryEnd   = url.indexOf("#") + 1 || url.length + 1,
        query = url.slice(queryStart, queryEnd - 1),
        pairs = query.replace(/\+/g, " ").split("&"),
        parms = {}, i, n, v, nv;

    if (query === url || query === "") {
      	
	return;
    }

    for (i = 0; i < pairs.length; i++) {
        nv = pairs[i].split("=");
        n = decodeURIComponent(nv[0]);
        v = decodeURIComponent(nv[1]);

        if (!parms.hasOwnProperty(n)) {
            parms[n] = [];
        }

        parms[n].push(nv.length === 2 ? v : null);
    }

    return parms;
}

function main()
{
	parms = parseURLParams( );
	if ( parms == undefined )
	{
		var timeStr = "30m";
	}
	else
	{
		if( "time" in parms)	
		{
			var timeStr = parms.time[0]
		}
		else
		{
			var timeStr = "30m";
		}
	}	
	d3.select("span.avg_descr")
		.text( "RPM's over last " +timeStr );
	getData( timeStr, 'plot1' );

}

</script>
</head>
<body onload=main()>


	
<div id="display">
	<span class=avg_val id=avg_display></span><span class="avg_descr"></span>
	<div id="plot1"></div>

	
</div>

</body>
</html>
