// https://observablehq.com/@john-guerra/geojson-colombia@40
function _1(md){return(
md
)}

function _map(d3,colombiaGeoJSON)
{
  const svg = d3.create("svg");

  var width = 960,
      height = 750,
      centered;

  // Define color scale
  var color = d3.scaleLinear()
  .domain([0, 1])
  .clamp(true)
  .range(['#fff', 'green']);

  var projection = d3.geoMercator()
  .scale(1800)
  // Center the Map in Colombia
  .center([-70, 2.3])
  .translate([width / 2, height / 2]);

  var path = d3.geoPath()
  .projection(projection);

  // Set svg width & height
  svg
    .attr('width', width)
    .attr('height', height);

  // Add background
  svg.append('rect')
    .attr('class', 'background')
    .attr('width', width)
    .attr('height', height)
    .on('click', clicked);

  var g = svg.append('g');

  var effectLayer = g.append('g')
    .classed('effect-layer', true);

  var mapLayer = g.append('g')
    .classed('map-layer', true);

  // var dummyText = g.append('text')
  //   .classed('dummy-text', true)
  //   .attr('x', 10)
  //   .attr('y', 30)
  //   .style('opacity', 0);

  var bigText = g.append('text')
    .classed('big-text', true)
    .attr('x', '30%')
    .attr('y', '50%');


  // Get province name
  function nameFn(d){
    return d && d.properties ? d.properties.NOMBRE_DPT : null;
  }
  function adressFn(d){
    return d && d.properties ? d.properties.DIRECCION : null;
  }
  function celFn(d){
    return d && d.properties ? d.properties.CELULAR : null;
  }
  function pbxFn(d){
    return d && d.properties ? d.properties.PBX : null;
  }
  function imgFn(d){
    return d && d.properties ? d.properties.IMG : null;
  }
  function colorFn(d){
    return d && d.properties ? d.properties.COLOR : null;
  }


  // Get province name length
  function nameLength(d){
    var n = nameFn(d);
    return n ? n.length : 0;
  }

  // Get province color
  function fillFn(d){
    return d && d.properties ? (colorFn(d) === "verde" ? "green" : "white") : "white";
    }
  function borderFn(d){
      return d && d.properties ? (colorFn(d) === "verde" ? "white" : "green") : "green";
      }
  // When clicked, zoom in
  function clicked(d) {
    var x, y, k;
    // Get information about the clicked department from its properties
   const departmentInfo = d.properties.NOMBRE_DPT;  // Assuming department information is in properties
   const direccionInfo = d.properties.DIRECCION;  // Assuming department information is in properties
   const celInfo = d.properties.CELULAR;  // Assuming department information is in properties
   const imgInfo = d.properties.IMG; // Assuming department information is in properties
   const pbxInfo = d.properties.PBX; // Assuming department information is in properties
   const colorInfo = d.properties.COLOR; // Assuming department information is in properties
  // Update a DOM element (outside this function) to display the information
   document.getElementById('department-info').innerHTML = 
   `<img style="width: 3%;
   position: fixed;
   left: 54.4%;
   top: 34.5%;"src="img/map.gif">
    <h2 style="font-size:100%;width:90%;margin-left:10%;padding-top:3%;line-height:1;"> ${departmentInfo}</h2>
    <h3 style="font-size:80%;width:80%;margin-left:10%;"> ${direccionInfo}</h3>
    <h3 style="font-size:90%;width:80%;margin-left:10%;"> DUWEST Colombia, S.A.S.</h3>
    <img src="img/celmap.png" 
    style="
    width: 1.5%;
    position: fixed;
    left: 55%;
    top: 46.5%;
    
    @media (max-width: 1400px) {
      img {
    width: 1.5%;
    position: fixed;
    left: 55%;
    top: 58%;
    }
  }">
    <h3 style="font-size:80%;width:80%;margin-left:10%;padding-top:0%;padding-bottom:0%;line-height:0;"> Cel. ${celInfo}</h3>
    <h3 style="font-size:80%;width:80%;margin-left:10%;padding-top:0%;padding-bottom:2%;line-height:0;"> PBX: ${pbxInfo}</h3>
    <img src="img/${imgInfo}"
  style="
  width: 27%;
  position: fixed;
   left: 55.5%;
   top: 55.7%;
   height: 31%;
   @media (max-width: 1400px) {
    img{width: 27%;
    position: fixed;
    left: 55.5%;
    top: 62.7%;
    height:25%;
  }
  }">
    `;

    const markerImage = d3.select('.marker-image');
    const pathBBox = path.centroid(d);
    markerImage.attr('transform', `translate(${pathBBox[0]}, ${pathBBox[1]})`);

    // Compute centroid of the selected path
    if (d && centered !== d) {
      var centroid = path.centroid(d);
      x = centroid[0];
      y = centroid[1];
      k = 4; 
      centered = d;
    } else {
      x = width / 2;
      y = height / 2;
      k = 1;
      centered = null;
    }

    // Highlight the clicked province
    mapLayer.selectAll('path')
      .style('fill', function(d){return centered && d===centered ? '#008F39' : fillFn(d);});

    // Zoom
   // g.transition()
     // .duration(750)
      //.attr('transform', 'translate(' + width / 2 + ',' + height / 2 + ')scale(' + k + ')translate(' + -x + ',' + -y + ')');
  }

  function mouseover(d){
    // Highlight hovered province
    d3.select(this).style('fill', 'green');

    // Draw effects
    textArt(nameFn(d));
  }

  function mouseout(d){
    // Reset province color
    mapLayer.selectAll('path')
      .style('fill', function(d){return centered && d===centered ? '#008F39' : fillFn(d);});

    // Remove effect text
    effectLayer.selectAll('text').transition()
      .style('opacity', 0)
      .remove();

    // Clear province name
    bigText.text('');
  }

  // Gimmick
  // Just me playing around.
  // You won't need this for a regular map.

  var BASE_FONT = "'Helvetica Neue', Helvetica, Arial, sans-serif";

  var FONTS = [
    "Century Gothic Bold",
   
  ];

  function textArt(text){
    // Use random font
    var fontIndex = Math.round(Math.random() * FONTS.length);
    var fontFamily = FONTS[fontIndex] + ', ' + BASE_FONT;

    bigText
      .style('font-family', fontFamily)
      .text(text);

    // Use dummy text to compute actual width of the text
    // getBBox() will return bounding box
    dummyText
      .style('font-family', fontFamily)
      .text(text);
    var bbox = dummyText.node().getBBox();

    var textWidth = bbox.width;
    var textHeight = bbox.height;
    var xGap = 3;
    var yGap = 1;

    // Generate the positions of the text in the background
    var xPtr = 0;
    var yPtr = 0;
    var positions = [];
    var rowCount = 0;
    while(yPtr < height){
      while(xPtr < width){
        var point = {
          text: text,
          index: positions.length,
          x: xPtr,
          y: yPtr
        };
        var dx = point.x - width/2 + textWidth/2;
        var dy = point.y - height/2;
        point.distance = dx*dx + dy*dy;

        positions.push(point);
        xPtr += textWidth + xGap;
      }
      rowCount++;
      xPtr = rowCount%2===0 ? 0 : -textWidth/2;
      xPtr += Math.random() * 10;
      yPtr += textHeight + yGap;
    }

    var selection = effectLayer.selectAll('text')
    .data(positions, function(d){return d.text+'/'+d.index;});

    // Clear old ones
    selection.exit().transition()
      .style('opacity', 0)
      .remove();

    // Create text but set opacity to 0
    const textEnter = selection.enter().append('text')
      .text(function(d){return d.text;})
      .attr('x', function(d){return d.x;})
      .attr('y', function(d){return d.y;})
      .style('font-family', fontFamily)
      .style('fill', '#777')
      .style('opacity', 0);

    selection.merge(textEnter)
      .style('font-family', fontFamily)
      .attr('x', function(d){return d.x;})
      .attr('y', function(d){return d.y;});

    // Create transtion to increase opacity from 0 to 0.1-0.5
    // Add delay based on distance from the center of the <svg> and a bit more randomness.
    selection.merge(textEnter).transition()
      .delay(function(d){
      return d.distance * 0.01 + Math.random()*1000;
    })
      .style('opacity', function(d){
      return 0.1 + Math.random()*0.4;
    });
  }
  
    var features = colombiaGeoJSON.features;

  // Update color scale domain based on data
  color.domain([0, d3.max(features, colorFn)]);

  // Draw each province as a path
  mapLayer.selectAll('path')
    .data(features)
    .enter().append('path')
    .attr('d', path)
    .attr('vector-effect', 'non-scaling-stroke')
    .style('fill', fillFn)
    .style('stroke', borderFn)
    .on('mouseover', mouseover)
    .on('mouseout', mouseout)
    .on('click', clicked);

  return svg.node()
}


function _colombiaGeoJSON(d3){return(
d3.json("https://raw.githubusercontent.com/scordesign/PageDuwest/main/colombia.geo.json")
)}

function _4(html){return(
html`<style>

@import url(https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300|Josefin+Slab|Arvo|Lato|Vollkorn|Abril+Fatface|Old+Standard+TT|Droid+Sans|Lobster|Inconsolata|Montserrat|Playfair+Display|Karla|Alegreya|Libre+Baskerville|Merriweather|Lora|Archivo+Narrow|Neuton|Signika|Questrial|Fjalla+One|Bitter|Varela+Round);
@import url('https://fonts.googleapis.com/css2?family=Century+Gothic:wght@200;300;400;500;600;700;800;900&display=swap');

.background {
  fill: transparent;
  pointer-events: all;
}

.map-layer {
  fill: green !important;
  stroke: white !important;
}

.effect-layer{
  pointer-events:none;
}

text{
  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
  font-weight: 300;
}

text.big-text{
  position:fixed;
  top: 370%;
  font-size: 30px;
  font-weight: 400;
}

.effect-layer text, text.dummy-text{
  font-size: 12px;
}
.department-info {
  float: left;
  position: fixed;
  width: 30%;
  background-color: rgba(0 , 0, 0, 0.6);
  left: 54%;
  top: 32%;
  z-index: 999;
  height: 57%;
  color: white;
  border-radius:10px;
  font-family: 'Century Gothic', sans-serif;

}
.department-info-title {
  float: left;
  position: fixed;
  width: 30%;
  background-color: rgba(0 , 0, 0, 0.6);
  left: 54%;
  top: 17%;
  z-index: 999;
  height: auto;
  color: white;
  border-radius:10px;
  font-family: 'Century Gothic', sans-serif;
  text-align: center;

}
.department-info-title h2{
font-size: 24px !important;
padding-top: 1px;
padding-bottom: 1px;

}
.marker-image {
  position: absolute;
  /* adjust width and height as needed */
  width: 20px;
  height: 20px;
}
@media (max-width: 1400px) {
  
}

</style>`
)}

function _d3(require){return(
require("d3@5")
)}


export default function define(runtime, observer) {
  const main = runtime.module();
  main.variable(observer()).define(["md"], _1);
  main.variable(observer("map")).define("map", ["d3","colombiaGeoJSON"], _map);
  main.variable(observer("colombiaGeoJSON")).define("colombiaGeoJSON", ["d3"], _colombiaGeoJSON);
  main.variable(observer()).define(["html"], _4);
  main.variable(observer("d3")).define("d3", ["require"], _d3);
  return main;
}
