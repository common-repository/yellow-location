
/* google map */

var yellowStations = [];
var inactiveIcon = yellowObject.pluginsUrl + "/images/marker-yellow.png";
var activeIcon = yellowObject.pluginsUrl + "/images/marker-red.png";
var markers = [];
var map;
var infoWindow;
var dragendEvent;
var currentMarker;

var submitBtn = document.querySelector("#location--action");

function initMap(locations) {
    var center = {
        lat: 32.0853,
        lng: 34.7818
    };

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 9,
        center: center
    });
    infoWindow = new google.maps.InfoWindow({});
    var marker, count;
    for (count = 0; count < locations.length; count++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[count].long, locations[count].lat),
            optimized: false,
            icon: {
                url: inactiveIcon,
                size: new google.maps.Size(35, 45),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(35, 45),
                scaledSize: new google.maps.Size(25, 35)
            },
            map: map,
            title: locations[count].name,
            id: locations[count].id,
            active: false
        });
        markers.push(marker);
        google.maps.event.addListener(marker, 'click', function (marker, count) {
            return function () {
                // checks if there is a previous active marker
                // if it is, makes it inactive
                currentMarker = marker;
                if (!marker.active) {
                    var prevActiveMarker = markers.find(function (marker) {
                        return marker.active === true;
                    });
                    if (prevActiveMarker) {
                        prevActiveMarker.active = false;
                        prevActiveMarker.setIcon({
                            url: inactiveIcon,
                            size: new google.maps.Size(35, 45),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(35, 45),
                            scaledSize: new google.maps.Size(25, 35)
                        });
                    }
                }
                infoWindow.setContent(locations[count].address);

                marker.active = marker.active ? false : true;
                var currIcon = marker.active ? activeIcon : inactiveIcon;
                marker.setIcon({
                    url: currIcon,
                    size: new google.maps.Size(35, 45),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(35, 45),
                    scaledSize: new google.maps.Size(25, 35)
                });
                var targetAccodion = document.querySelector(".accordion input[class='" + marker.id + "']");
                if (marker.active) {
                    infoWindow.open(map, marker);
                    if (targetAccodion) {
                        var accordions = Array.from(document.querySelectorAll(".accordion input"));
                        accordions.forEach(function (accordion) {
                            accordion.checked = false;
                        });
                        targetAccodion.checked = true;
                        setTimeout(function () {
                            targetAccodion.parentNode.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
                        }, 200);
                        window.mapSelectedId = marker.id;
                        submitBtn.disabled = false;
                    }
                } else {
                    targetAccodion.checked = false;
                    submitBtn.disabled = true;
                    infoWindow.close(map, marker);
                }
            };
        }(marker, count));
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            infoWindow.setPosition(pos);
            infoWindow.setContent('המיקום נמצא');
            infoWindow.open(map);
            map.setCenter(pos);
        }, function () {
            handleLocationError(true, infoWindow, map.getCenter());
        });
    } else {
        // Browser doesn't support Geolocation
        handleLocationError(false, infoWindow, map.getCenter());
    }
}

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(browserHasGeolocation ? 'נראה שיש כרגע בעיה באיתור המיקום שלך' : 'Error: Your browser doesn\'t support geolocation.');
    infoWindow.open(map);
}

/* end google map */

//uses classList, setAttribute, and querySelectorAll
//if you want this to work in IE8/9 youll need to polyfill these
function makeAccordion() {

    var inputs = Array.from(document.querySelectorAll("#accordion input[type='checkbox']"));

    function toggleAccordion(ev) {
        // console.log(ev.target);
        // let attrFor = ev.target.getAttribute("for");
        // let currInput = document.querySelector(`input[id=${attrFor}]`);

        inputs.forEach(function (input) {
            if (ev.target.getAttribute("id") !== input.getAttribute("id")) {
                input.checked = false;
            }
        });
        // debugger;
        var lat = this.getAttribute('lat');
        var long = this.getAttribute('long');
        var yellowid = this.getAttribute('yellowid');
        var targetMarker = markers.find(function (marker) {
            return marker.id === Number(yellowid);
        });

        if (this.checked) {

            window.map.setCenter(new google.maps.LatLng(lat, long));
            // console.log(markers[1]);

            markers.forEach(function (marker) {
                if (marker.id !== yellowid) {
                    marker.setIcon({
                        url: inactiveIcon,
                        size: new google.maps.Size(35, 45),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(35, 45),
                        scaledSize: new google.maps.Size(25, 35)
                    });
                }
            });
            google.maps.event.trigger(targetMarker, 'click');
            window.map.setZoom(15);

            window.mapSelectedId = this.getAttribute('yellowId');
            submitBtn.disabled = false;
        } else {
            // uncheck 
            infoWindow.close(map, targetMarker);
            targetMarker.active = false;
            targetMarker.setIcon({
                url: inactiveIcon,
                size: new google.maps.Size(35, 45),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(35, 45),
                scaledSize: new google.maps.Size(25, 35)
            });
            submitBtn.disabled = true;
        }
    }

    inputs.forEach(function (input) {
        input.addEventListener("change", toggleAccordion, true);
    });
}; // makeAccordion

function onItemSelect(event) {

    var div = event.target;
    var input = div.children[0];
    var attr = input.attributes;
    var long = attr.getNamedItem('long').value;
    var lat = attr.getNamedItem('lat').value;
    var city = attr.getNamedItem('city').value;
    window.mapSelectedId = attr.getNamedItem('id').value;

    window.map.setCenter(new google.maps.LatLng(lat, long));
    window.map.setZoom(15);

    // filter paz stations by choosen city

    var filteredStations = yellowStations.filter(function (station) {
        return station.City === city;
    });
    // console.log('filteredStations', filteredStations);


    document.querySelector("#accordion").innerHTML = '';
    // autocomplete(document.getElementById("myInput"), filteredStations);
    buildStationsList(filteredStations);
    makeAccordion();
}

function autocomplete(inp, arr) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function (e) {
        var a,
            b,
            i,
            val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) {
            return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            if (arr[i].Name.indexOf(val) != -1 || arr[i].Address.indexOf(val) != -1 || arr[i].City.indexOf(val) != -1) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                b.addEventListener('click', onItemSelect, false);
                b.addEventListener('keydown', function (e) {
                    if (e.keyCode === 13) {
                        onItemSelect(e);
                    }
                }, false);
                /*make the matching letters bold:*/
                b.innerHTML = (arr[i].Name + " " + arr[i].City + " " + arr[i].Address).substr(0, val.length);
                b.innerHTML += (arr[i].Name + " " + arr[i].City + " " + arr[i].Address).substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "\n            <input type=\"hidden\"\n             id='" + arr[i].Id + "'\n             lat='" + arr[i].Location[1] + "'\n             long='" + arr[i].Location[0] + "'\n             city='" + arr[i].City + "'\n             value=' " + arr[i].Name + " " + arr[i].City + " " + arr[i].Address + " '>\n            ";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function (e) {
                    /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[0].value;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
                    var currName = this.querySelector('input').getAttribute('city');
                    document.querySelector('#yellow-name').innerHTML = currName;
                    // console.log(currName);
                    closeAllLists();
                    dragendEvent = map.addListener('dragend', function (e) {
                        setTimeout(function () {
                            clearInput(false);
                            currentMarker.active = false;
                            google.maps.event.trigger(currentMarker, 'click');
                        }, 500);
                    });
                });
                a.appendChild(b);
            }
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function (e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 38) {
            //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            //   debugger;

            if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = x.length - 1;
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}

function ajax_get(url, callback) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            // console.log('responseText:' + xmlhttp.responseText);
            try {
                var data = JSON.parse(xmlhttp.responseText);
            } catch (err) {
                console.log(err.message + " in " + xmlhttp.responseText);
                return;
            }
            callback(data);
        }
    };

    xmlhttp.open("GET", url, true);
    xmlhttp.send();
}

ajax_get('https://www.yellow.co.il/api/ClickAndPickStationApi/stations?IncludeLocker=true', function (data) {

    // filters data by include locker
    yellowStations = data.filter(function (station) {
        return station.IncludeLocker === true;
    });
    // console.log(yellowStations.length);
    autocomplete(document.getElementById("myInput"), yellowStations);
    buildStationsList(yellowStations);
    makeAccordion();

    locations = yellowStations.map(function (mark) {
        // return [`${mark.City} ${mark.Address}`, mark.Location[1], mark.Location[0], ]
        return {
            address: mark.City + " " + mark.Address,
            name: mark.Name,
            lat: mark.Location[0],
            long: mark.Location[1],
            id: mark.Id
        };
    });
    initMap(locations);
});

function buildStationsList(data) {
    var output = '';

    for (var i = 0; i < data.length; i++) {
        output += "\n       \n\n        <div class=\"accordion-item-" + [i] + "\">\n        <input\n         id=\"ac-" + [i] + "\"\n         name=\"accordion-" + [i] + "\"\n         class=\"" + data[i].Id + "\"\n         type=\"checkbox\"\n         lat='" + data[i].Location[1] + "'\n         long='" + data[i].Location[0] + "'\n         yellowId='" + data[i].Id + "'\n         address='" + data[i].Address + "'\n         city='" + data[i].City + "'\n         yellowName='" + data[i].Name + "'\n         >\n        <label for=\"ac-" + [i] + "\">\n        <span class=\"circle\"></span>\n        <span><strong>" + data[i].Name + "</strong></span> \n        <span>" + data[i].Address + ", " + data[i].City + "</span>\n        <span class=\"arrow\"></span>\n        </label>\n        <article>\n         <div class=\"detail-info\">\n                    <p>\u05E9\u05E2\u05D5\u05EA \u05E4\u05EA\u05D9\u05D7\u05D4:</p>\n                    <div>\n                            <div class=\"yellow-item\">\n                            <span class=\"ltr\">'\u05D0' - \u05D4</span>\n                            <span>" + data[i].OpenHourWorking + "</span>\n                            </div>\n                            <div class=\"yellow-item\">\n                            <span class=\"ltr\">'\u05D5</span>\n                            <span>" + data[i].OpenHourFriday + "</span>\n                            </div>\n                            <div class=\"yellow-item\">\n                            <span class=\"ltr\">\u05E9\u05D1\u05EA</span>\n                            <span>" + data[i].OpenHourSaturday + "</span>\n                            </div>\n                    </div>\n                </div>\n        </article>\n      </div>\n\n        ";
    }

    document.querySelector("#accordion").innerHTML = output;
}

/* modal */

var modalServiceButtonClick = function modalServiceButtonClick(button) {
    var d = document;
    var trigger = button.getAttribute('data-modal-trigger');
    var modal = d.querySelector("[data-modal=" + trigger + "]");
    modal.classList.toggle('is-open');
};

var modalService = function modalService() {
    var d = document;
    var body = d.querySelector('body');
    var buttons = d.querySelectorAll('[data-modal-trigger]');

    // attach click event to all modal triggers

    var _loop = function _loop(i) {
        var button = buttons[i];
        var trigger = button.getAttribute('data-modal-trigger');
        var modal = d.querySelector("[data-modal=" + trigger + "]");
        var modalBody = modal.querySelector('.modal-body');
        var closeBtn = modalBody.querySelector('.close');
        modal.addEventListener('click', function () {
            return modal.classList.remove('is-open');
        });
        modalBody.addEventListener('click', function (e) {
            return e.stopPropagation();
        });

        // Close modal when hitting escape
        body.addEventListener('keydown', function (e) {
            if (e.keyCode === 27) {
                modal.classList.remove('is-open');
            }
        });

        button.addEventListener('click', function () {
            modalServiceButtonClick(button);
        });

        modalBody.addEventListener('click', function (evt) {
            if (evt.target.classList.contains('close')) {
                modal.classList.remove('is-open');
            }
        }, true);
    };

    for (var i = 0; i < buttons.length; i++) {
        _loop(i);
    }
};

modalService();

/* end modal */

var actionBtn = document.querySelector('#location--action');
var yellowAddressId = document.querySelector('#yellowAddressId');
var selectedStationCity = document.querySelector('#selected-station-city');
var selectedStationAddress = document.querySelector('#selected-station-address');
var selectedStationName = document.querySelector('#selected-station-name');
var yellowboxBtn = document.querySelector('#yellowboxBtn');
var YellowboxPopup = document.querySelector('#Yellowbox-popup');

actionBtn.addEventListener('click', function (e) {

    e.preventDefault();
    // console.log('click');

    if (window.mapSelectedId) {
        var locationInput = document.querySelector('#locationInput');
        var elment = document.querySelector('[yellowId="' + window.mapSelectedId + '"]');
        yellowAddressId.value = elment.getAttribute('yellowId');
        selectedStationCity.value = elment.getAttribute('city');
        selectedStationAddress.value = elment.getAttribute('address');
        selectedStationName.value = elment.getAttribute('yellowName');
        locationInput.innerHTML = "\u05D4\u05E1\u05E0\u05D9\u05E3 \u05D4\u05E0\u05D1\u05D7\u05E8: " + elment.getAttribute('yellowName') + " " + elment.getAttribute('address');
        document.querySelector("#place_order").disabled = false;
    }
});

function clearInput() {
    var changeZoom = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;

    var input = document.getElementById('myInput');
    input.value = "";
    // var event = new Event('change');
    var event = new Event('input');
    google.maps.event.removeListener(dragendEvent);
    input.dispatchEvent(event);
    buildStationsList(yellowStations);
    makeAccordion();
    if (changeZoom) {
        window.map.setZoom(9);
    }
}